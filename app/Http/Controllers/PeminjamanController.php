<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    /** [ADMIN] Daftar seluruh transaksi peminjaman */
    public function index(Request $request)
    {
        $status = $request->get('status');

        $peminjaman = Peminjaman::with(['anggota', 'detail.buku'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('tanggal_pinjam')
            ->paginate(10)
            ->withQueryString();

        return view('peminjaman.index', compact('peminjaman', 'status'));
    }

    /** [ADMIN] Form buat transaksi peminjaman baru secara manual */
    public function create()
    {
        $anggotaList = \App\Models\Anggota::where('role', 'anggota')->orderBy('nama')->get();
        $bukuList    = Buku::where('stok', '>', 0)->orderBy('judul')->get();

        return view('peminjaman.create', compact('anggotaList', 'bukuList'));
    }

    /** [ADMIN] Simpan transaksi peminjaman baru */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_anggota'  => ['required', 'exists:anggota,id_anggota'],
            'id_buku'     => ['required', 'exists:buku,id_buku'],
            'jumlah_buku' => ['required', 'integer', 'min:1'],
        ]);

        $this->prosesPeminjaman($data['id_anggota'], $data['id_buku'], $data['jumlah_buku']);

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil dicatat.');
    }

    /** [ADMIN] Tandai peminjaman sebagai dikembalikan */
    public function kembalikan(Peminjaman $peminjaman)
    {
        DB::transaction(function () use ($peminjaman) {
            $peminjaman->update([
                'status'          => 'Dikembalikan',
                'tanggal_kembali' => now(),
            ]);

            // Kembalikan stok buku
            foreach ($peminjaman->detail as $detail) {
                $detail->buku()->increment('stok', $detail->jumlah_buku);
            }
        });

        return back()->with('success', 'Peminjaman ditandai sudah dikembalikan.');
    }

    /**
     * [ANGGOTA] Meminjam buku langsung dari halaman rekomendasi.
     * Dipanggil oleh tombol "Pinjam" di member_dashboard.
     */
    public function pinjam(Request $request, int $id_buku)
    {
        $buku = Buku::findOrFail($id_buku);

        if ($buku->stok < 1) {
            return response()->json(['status' => 'error', 'message' => 'Stok buku habis.'], 422);
        }

        $this->prosesPeminjaman(Auth::id(), $buku->id_buku, 1);

        return response()->json(['status' => 'success', 'message' => 'Buku berhasil dipinjam!']);
    }

    /** [ANGGOTA] Riwayat peminjaman milik anggota yang sedang login */
    public function riwayat()
    {
        $riwayat = Peminjaman::with('detail.buku')
            ->where('id_anggota', Auth::id())
            ->orderByDesc('tanggal_pinjam')
            ->paginate(10);

        return view('peminjaman.riwayat', compact('riwayat'));
    }

    /**
     * Helper internal: membuat header Peminjaman + detail sekaligus
     * mengurangi stok buku. Dipakai baik oleh admin maupun anggota.
     */
    private function prosesPeminjaman(int $idAnggota, int $idBuku, int $jumlah): void
    {
        DB::transaction(function () use ($idAnggota, $idBuku, $jumlah) {
            $peminjaman = Peminjaman::create([
                'id_anggota'      => $idAnggota,
                'tanggal_pinjam'  => now(),
                'tanggal_kembali' => null,
                'status'          => 'Dipinjam',
            ]);

            DetailPeminjaman::create([
                'id_peminjaman' => $peminjaman->id_peminjaman,
                'id_buku'       => $idBuku,
                'jumlah_buku'   => $jumlah,
            ]);

            Buku::where('id_buku', $idBuku)->decrement('stok', $jumlah);
            Buku::where('id_buku', $idBuku)->increment('jumlah_pinjam', $jumlah);
        });
    }
}
