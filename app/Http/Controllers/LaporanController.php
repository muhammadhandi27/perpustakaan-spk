<?php

namespace App\Http\Controllers;

use App\Models\RekomendasiBuku;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Controller untuk menu "Laporan" di sidebar admin.
 * Menampilkan & mengekspor hasil ranking SAW ke PDF.
 *
 * Membutuhkan package tambahan:
 *   composer require barryvdh/laravel-dompdf
 */
class LaporanController extends Controller
{
    /** Halaman laporan (tampilan web) */
    public function index()
    {
        $rekomendasi = RekomendasiBuku::with('buku')
            ->orderBy('ranking')
            ->get();

        return view('admin.laporan.index', compact('rekomendasi'));
    }

    /** Export laporan hasil SAW ke file PDF */
    public function exportPdf()
    {
        $rekomendasi = RekomendasiBuku::with('buku')
            ->orderBy('ranking')
            ->get();

        $pdf = Pdf::loadView('admin.laporan.pdf', compact('rekomendasi'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-rekomendasi-saw-' . now()->format('Y-m-d') . '.pdf');
    }
}
