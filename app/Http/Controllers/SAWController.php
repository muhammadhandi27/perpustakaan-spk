<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kriteria;
use App\Models\PenilaianBuku;
use App\Models\RekomendasiBuku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * =====================================================================
 * SAWController
 * ---------------------------------------------------------------------
 * Controller ini menangani seluruh logika Sistem Pendukung Keputusan
 * (SPK) untuk rekomendasi buku menggunakan metode SAW
 * (Simple Additive Weighting).
 *
 * Tahapan algoritma SAW yang diimplementasikan:
 * 1. Membentuk matriks keputusan (X) dari tabel Penilaian_Buku
 * 2. Normalisasi matriks (karena semua kriteria bersifat BENEFIT):
 *      r_ij = x_ij / MAX(x_j)
 * 3. Menghitung nilai preferensi V_i = SUM(bobot_j * r_ij)
 * 4. Mengurutkan (ranking) V_i dari terbesar ke terkecil
 * 5. Menyimpan hasil ke tabel Rekomendasi_Buku & mengembalikan JSON
 * =====================================================================
 */
class SAWController extends Controller
{
    /**
     * Tabel konversi kategori buku (C4) ke nilai numerik.
     * Aturan ini sesuai dengan laporan proyek.
     *
     * @var array<string,int>
     */
    private array $konversiKategori = [
        'Programming'            => 4,
        'Artificial Intelligent' => 3,
        'Sains'                  => 3,
        'Self-Improvement'       => 2,
        'Sosial'                 => 2,
        'Fiksi'                  => 1,
    ];

    /**
     * Endpoint utama: menjalankan seluruh proses SAW dan
     * mengembalikan hasil ranking dalam format JSON.
     *
     * Contoh route (routes/web.php atau routes/api.php):
     * Route::get('/spk/hitung-saw', [SAWController::class, 'hitungSAW']);
     */
    public function hitungSAW(Request $request)
    {
        // -----------------------------------------------------------
        // STEP 0: Sinkronisasi nilai kategori (C4) ke tabel Penilaian_Buku
        // Dipanggil setiap kali sebelum hitung, agar data selalu terbaru
        // jika ada perubahan data buku (rating, tahun, jumlah pinjam, kategori).
        // -----------------------------------------------------------
        $this->sinkronisasiPenilaianBuku();

        // -----------------------------------------------------------
        // STEP 1: Ambil matriks keputusan dari tabel Penilaian_Buku
        // Hasil query di-groupkan per id_buku -> [kode_kriteria => nilai]
        // -----------------------------------------------------------
        $matriks = $this->ambilMatriksKeputusan();

        if (empty($matriks)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data penilaian buku tidak ditemukan.',
            ], 404);
        }

        // -----------------------------------------------------------
        // STEP 2: Cari nilai maksimum tiap kolom kriteria (MAX x_j)
        // Dibutuhkan untuk normalisasi benefit
        // -----------------------------------------------------------
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        $maxPerKriteria = [];

        foreach ($kriteriaList as $kriteria) {
            $kode = $kriteria->kode_kriteria;
            $nilaiKolom = array_column($matriks, $kode);
            // Hindari pembagian dengan nol jika seluruh nilai kolom kosong/0
            $maxPerKriteria[$kode] = !empty($nilaiKolom) ? max($nilaiKolom) : 1;
        }

        // -----------------------------------------------------------
        // STEP 3: Normalisasi matriks (rumus benefit) + hitung V_i
        // r_ij = x_ij / max(x_j)
        // V_i  = SUM( bobot_j * r_ij )
        // -----------------------------------------------------------
        $hasilPreferensi = [];

        foreach ($matriks as $idBuku => $nilaiPerKriteria) {
            $vi = 0.0;
            $detailNormalisasi = [];

            foreach ($kriteriaList as $kriteria) {
                $kode  = $kriteria->kode_kriteria;
                $bobot = (float) $kriteria->bobot;

                $xij = $nilaiPerKriteria[$kode] ?? 0;
                $maxJ = $maxPerKriteria[$kode] ?: 1; // fallback agar tidak bagi 0

                // Rumus normalisasi BENEFIT
                $rij = $xij / $maxJ;

                $detailNormalisasi[$kode] = round($rij, 4);

                // Akumulasi nilai preferensi
                $vi += $bobot * $rij;
            }

            $hasilPreferensi[] = [
                'id_buku'     => $idBuku,
                'normalisasi' => $detailNormalisasi,
                'v_i'         => round($vi, 6),
            ];
        }

        // -----------------------------------------------------------
        // STEP 4: Perankingan — urutkan V_i dari terbesar ke terkecil
        // -----------------------------------------------------------
        usort($hasilPreferensi, function ($a, $b) {
            return $b['v_i'] <=> $a['v_i'];
        });

        // Tambahkan nomor ranking setelah diurutkan
        foreach ($hasilPreferensi as $index => &$item) {
            $item['ranking'] = $index + 1;
        }
        unset($item);

        // -----------------------------------------------------------
        // STEP 5: Simpan hasil ke tabel Rekomendasi_Buku
        // (hapus data lama, lalu insert data baru agar selalu up-to-date)
        // -----------------------------------------------------------
        $this->simpanHasilRekomendasi($hasilPreferensi);

        // -----------------------------------------------------------
        // STEP 6: Gabungkan dengan data detail buku, lalu return JSON
        // -----------------------------------------------------------
        $daftarBuku = Buku::whereIn('id_buku', array_column($hasilPreferensi, 'id_buku'))
            ->get()
            ->keyBy('id_buku');

        $response = array_map(function ($item) use ($daftarBuku) {
            $buku = $daftarBuku[$item['id_buku']] ?? null;

            return [
                'ranking'          => $item['ranking'],
                'id_buku'          => $item['id_buku'],
                'judul'            => $buku->judul ?? '-',
                'penulis'          => $buku->penulis ?? '-',
                'kategori'         => $buku->kategori ?? '-',
                'bobot_preferensi' => $item['v_i'],
                'detail_normalisasi' => $item['normalisasi'],
            ];
        }, $hasilPreferensi);

        return response()->json([
            'status'  => 'success',
            'message' => 'Perhitungan SAW berhasil dilakukan.',
            'data'    => $response,
        ]);
    }

    /**
     * Mengambil dan membentuk matriks keputusan dari tabel Penilaian_Buku.
     * Hasil: [ id_buku => ['C1' => nilai, 'C2' => nilai, ...] ]
     */
    private function ambilMatriksKeputusan(): array
    {
        $rows = DB::table('penilaian_buku')
            ->join('kriteria', 'penilaian_buku.id_kriteria', '=', 'kriteria.id_kriteria')
            ->select('penilaian_buku.id_buku', 'kriteria.kode_kriteria', 'penilaian_buku.nilai')
            ->get();

        $matriks = [];
        foreach ($rows as $row) {
            $matriks[$row->id_buku][$row->kode_kriteria] = (float) $row->nilai;
        }

        return $matriks;
    }

    /**
     * Menyinkronkan data buku (rating, tahun_terbit, jumlah_pinjam, kategori)
     * ke tabel Penilaian_Buku, agar matriks keputusan selalu mencerminkan
     * data buku terkini. Kategori dikonversi menggunakan tabel konversi.
     */
    private function sinkronisasiPenilaianBuku(): void
    {
        $kriteriaMap = Kriteria::pluck('id_kriteria', 'kode_kriteria');
        $bukuList    = Buku::all();

        foreach ($bukuList as $buku) {
            $nilaiC4 = $this->konversiKategori[$buku->kategori] ?? 1;

            $nilaiPerKriteria = [
                'C1' => $buku->rating,        // Rating Buku
                'C2' => $buku->tahun_terbit,  // Tahun Terbit
                'C3' => $buku->jumlah_pinjam, // Jumlah Peminjaman
                'C4' => $nilaiC4,             // Kategori (hasil konversi)
            ];

            foreach ($nilaiPerKriteria as $kode => $nilai) {
                if (!isset($kriteriaMap[$kode])) {
                    continue;
                }

                // updateOrCreate: insert baru jika belum ada, update jika sudah ada
                PenilaianBuku::updateOrCreate(
                    [
                        'id_buku'     => $buku->id_buku,
                        'id_kriteria' => $kriteriaMap[$kode],
                    ],
                    [
                        'nilai' => $nilai,
                    ]
                );
            }
        }
    }

    /**
     * Menyimpan hasil akhir ranking SAW ke tabel Rekomendasi_Buku.
     * Data lama dibersihkan agar tabel selalu berisi hasil perhitungan terbaru.
     */
    private function simpanHasilRekomendasi(array $hasilPreferensi): void
    {
        DB::transaction(function () use ($hasilPreferensi) {
            // PENTING: gunakan delete(), BUKAN truncate().
            // TRUNCATE adalah perintah DDL yang melakukan implicit commit
            // di MySQL, sehingga akan merusak transaksi yang sedang berjalan
            // dan memicu error "There is no active transaction".
            // delete() adalah perintah DML biasa sehingga aman di dalam transaksi.
            RekomendasiBuku::query()->delete();

            foreach ($hasilPreferensi as $item) {
                RekomendasiBuku::create([
                    'id_buku'          => $item['id_buku'],
                    'bobot_preferensi' => $item['v_i'],
                    'ranking'          => $item['ranking'],
                ]);
            }
        });
    }

    /**
     * Endpoint tambahan: menampilkan hasil rekomendasi yang SUDAH tersimpan
     * di database (tanpa menghitung ulang). Cocok dipakai di halaman
     * katalog anggota agar lebih ringan (tidak perlu hitung SAW tiap load).
     *
     * Contoh route:
     * Route::get('/spk/rekomendasi', [SAWController::class, 'tampilkanRekomendasi']);
     */
    public function tampilkanRekomendasi()
    {
        $rekomendasi = RekomendasiBuku::with('buku')
            ->orderBy('ranking', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'ranking'          => $item->ranking,
                    'id_buku'          => $item->id_buku,
                    'judul'            => $item->buku->judul ?? '-',
                    'penulis'          => $item->buku->penulis ?? '-',
                    'kategori'         => $item->buku->kategori ?? '-',
                    'stok'             => $item->buku->stok ?? 0,
                    'bobot_preferensi' => $item->bobot_preferensi,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $rekomendasi,
        ]);
    }
}

/**
 * =====================================================================
 * CATATAN MODEL YANG DIBUTUHKAN (buat file terpisah di app/Models/)
 * =====================================================================
 *
 * // app/Models/Buku.php
 * class Buku extends Model {
 *     protected $table = 'buku';
 *     protected $primaryKey = 'id_buku';
 *     protected $guarded = [];
 * }
 *
 * // app/Models/Kriteria.php
 * class Kriteria extends Model {
 *     protected $table = 'kriteria';
 *     protected $primaryKey = 'id_kriteria';
 *     protected $guarded = [];
 * }
 *
 * // app/Models/PenilaianBuku.php
 * class PenilaianBuku extends Model {
 *     protected $table = 'penilaian_buku';
 *     public $incrementing = false; // composite key, bukan auto increment tunggal
 *     protected $guarded = [];
 * }
 *
 * // app/Models/RekomendasiBuku.php
 * class RekomendasiBuku extends Model {
 *     protected $table = 'rekomendasi_buku';
 *     protected $primaryKey = 'id_hasil';
 *     protected $guarded = [];
 *
 *     public function buku() {
 *         return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
 *     }
 * }
 * =====================================================================
 */
