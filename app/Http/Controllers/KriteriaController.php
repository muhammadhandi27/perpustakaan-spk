<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;

/**
 * Controller ini menangani halaman "Kelola SPK" di sidebar admin —
 * tempat admin bisa melihat & mengubah bobot tiap kriteria (C1-C4)
 * sebelum menjalankan perhitungan SAW.
 */
class KriteriaController extends Controller
{
    /** Tampilkan daftar kriteria beserta bobotnya */
    public function index()
    {
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();
        $totalBobot = $kriteria->sum('bobot');

        return view('kriteria.index', compact('kriteria', 'totalBobot'));
    }

    /** Update bobot satu kriteria */
    public function update(Request $request, Kriteria $kriteria)
    {
        $data = $request->validate([
            'bobot' => ['required', 'numeric', 'min:0', 'max:1'],
        ]);

        $kriteria->update($data);

        return back()->with('success', "Bobot {$kriteria->nama_kriteria} berhasil diperbarui menjadi {$data['bobot']}.");
    }
}
