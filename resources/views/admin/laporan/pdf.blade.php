<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    /* Catatan: dompdf TIDAK mendukung Tailwind CDN, jadi laporan PDF
       memakai CSS biasa yang ditulis manual di sini. */
    body { font-family: sans-serif; font-size: 12px; color: #1e293b; }
    h1 { font-size: 18px; margin-bottom: 4px; }
    p.subtitle { color: #64748b; margin-top: 0; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #e2e8f0; padding: 8px; text-align: left; }
    th { background-color: #f8fafc; font-size: 11px; text-transform: uppercase; }
    .rank { font-weight: bold; color: #4f46e5; }
    footer { margin-top: 20px; font-size: 10px; color: #94a3b8; }
</style>
</head>
<body>
    <h1>Laporan Rekomendasi Buku — Metode SAW</h1>
    <p class="subtitle">Dicetak pada {{ now()->format('d F Y, H:i') }} WIB</p>

    <table>
        <thead>
            <tr>
                <th>Ranking</th>
                <th>Judul Buku</th>
                <th>Kategori</th>
                <th>Nilai Preferensi (Vi)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rekomendasi as $item)
                <tr>
                    <td class="rank">#{{ $item->ranking }}</td>
                    <td>{{ $item->buku->judul ?? '-' }}</td>
                    <td>{{ $item->buku->kategori ?? '-' }}</td>
                    <td>{{ number_format($item->bobot_preferensi, 4) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <footer>Dihasilkan otomatis oleh Sistem Informasi Perpustakaan SPK — Metode SAW (Simple Additive Weighting)</footer>
</body>
</html>
