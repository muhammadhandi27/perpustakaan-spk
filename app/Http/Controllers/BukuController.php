<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /** Daftar semua buku */
    public function index(Request $request)
    {
        $keyword = $request->input('q');

        $buku = Buku::when($keyword, function ($query) use ($keyword) {
                $query->where('judul', 'like', "%{$keyword}%")
                      ->orWhere('penulis', 'like', "%{$keyword}%");
            })
            ->orderBy('judul')
            ->paginate(10)
            ->withQueryString();

        return view('admin.buku.index', compact('buku', 'keyword'));
    }

    /** Form tambah buku baru */
    public function create()
    {
        return view('admin.buku.create');
    }

    /** Simpan buku baru */
    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'         => ['required', 'string', 'max:150'],
            'penulis'       => ['required', 'string', 'max:100'],
            'penerbit'      => ['required', 'string', 'max:100'],
            'tahun_terbit'  => ['required', 'integer', 'min:1900', 'max:' . date('Y')],
            'kategori'      => ['required', 'in:Programming,Artificial Intelligent,Sains,Self-Improvement,Sosial,Fiksi'],
            'stok'          => ['required', 'integer', 'min:0'],
            'rating'        => ['nullable', 'numeric', 'min:0', 'max:5'],
            'jumlah_pinjam' => ['nullable', 'integer', 'min:0'],
        ]);

        Buku::create($data);

        return redirect()->route('admin.buku.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    /** Detail satu buku */
    public function show(Buku $buku)
    {
        return view('admin.buku.show', compact('buku'));
    }

    /** Form edit buku */
    public function edit(Buku $buku)
    {
        return view('admin.buku.edit', compact('buku'));
    }

    /** Update data buku */
    public function update(Request $request, Buku $buku)
    {
        $data = $request->validate([
            'judul'         => ['required', 'string', 'max:150'],
            'penulis'       => ['required', 'string', 'max:100'],
            'penerbit'      => ['required', 'string', 'max:100'],
            'tahun_terbit'  => ['required', 'integer', 'min:1900', 'max:' . date('Y')],
            'kategori'      => ['required', 'in:Programming,Artificial Intelligent,Sains,Self-Improvement,Sosial,Fiksi'],
            'stok'          => ['required', 'integer', 'min:0'],
            'rating'        => ['nullable', 'numeric', 'min:0', 'max:5'],
            'jumlah_pinjam' => ['nullable', 'integer', 'min:0'],
        ]);

        $buku->update($data);

        return redirect()->route('admin.buku.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    /** Hapus buku */
    public function destroy(Buku $buku)
    {
        $buku->delete();

        return redirect()->route('admin.buku.index')->with('success', 'Buku berhasil dihapus.');
    }
}
