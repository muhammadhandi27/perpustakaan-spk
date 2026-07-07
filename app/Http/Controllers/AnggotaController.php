<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AnggotaController extends Controller
{
    /** Daftar semua anggota */
    public function index(Request $request)
    {
        $keyword = $request->get('q');

        $anggota = Anggota::when($keyword, function ($query) use ($keyword) {
                $query->where('nama', 'like', "%{$keyword}%")
                      ->orWhere('username', 'like', "%{$keyword}%");
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.anggota.index', compact('anggota', 'keyword'));
    }

    /** Form tambah anggota baru (dibuat oleh admin) */
    public function create()
    {
        return view('admin.anggota.create');
    }

    /** Simpan anggota baru */
    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:anggota,username'],
            'password' => ['required', 'string', 'min:6'],
            'nama'     => ['required', 'string', 'max:100'],
            'alamat'   => ['nullable', 'string'],
            'no_hp'    => ['nullable', 'string', 'max:20'],
            'role'     => ['required', 'in:admin,anggota'],
        ]);

        $data['password'] = Hash::make($data['password']);

        Anggota::create($data);

        return redirect()->route('admin.anggota.index')->with('success', 'Anggota berhasil ditambahkan.');
    }

    /** Form edit anggota */
    public function edit(Anggota $anggota)
    {
        // Catatan: Laravel route-model-binding otomatis memakai nama variabel
        // sebagai parameter. Karena nama model "Anggota", Laravel akan
        // otomatis mem-bind ke $anggota jika parameter route diberi nama {anggota}.
        return view('admin.anggota.edit', ['anggota' => $anggota]);
    }

    /** Update data anggota */
    public function update(Request $request, Anggota $anggota)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:anggota,username,' . $anggota->id_anggota . ',id_anggota'],
            'password' => ['nullable', 'string', 'min:6'],
            'nama'     => ['required', 'string', 'max:100'],
            'alamat'   => ['nullable', 'string'],
            'no_hp'    => ['nullable', 'string', 'max:20'],
            'role'     => ['required', 'in:admin,anggota'],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']); // jangan timpa password jika dikosongkan
        }

        $anggota->update($data);

        return redirect()->route('admin.anggota.index')->with('success', 'Data anggota berhasil diperbarui.');
    }

    /** Hapus anggota */
    public function destroy(Anggota $anggota)
    {
        $anggota->delete();

        return redirect()->route('admin.anggota.index')->with('success', 'Anggota berhasil dihapus.');
    }
}
