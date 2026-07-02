-- =====================================================================
-- QUERY DIAGNOSTIK: Mengapa beberapa buku punya Vi = 1.0000?
-- =====================================================================

-- 1. Lihat data mentah buku (rating, tahun, jumlah pinjam, kategori)
--    Cek apakah ada beberapa baris yang nilainya identik
SELECT id_buku, judul, rating, tahun_terbit, jumlah_pinjam, kategori, stok
FROM buku
ORDER BY rating DESC, jumlah_pinjam DESC;

-- 2. Lihat matriks keputusan (Penilaian_Buku) dalam bentuk pivot per buku
--    supaya mudah dibandingkan antar baris
SELECT
    b.id_buku,
    b.judul,
    MAX(CASE WHEN k.kode_kriteria = 'C1' THEN p.nilai END) AS C1_rating,
    MAX(CASE WHEN k.kode_kriteria = 'C2' THEN p.nilai END) AS C2_tahun,
    MAX(CASE WHEN k.kode_kriteria = 'C3' THEN p.nilai END) AS C3_pinjam,
    MAX(CASE WHEN k.kode_kriteria = 'C4' THEN p.nilai END) AS C4_kategori
FROM buku b
JOIN penilaian_buku p ON p.id_buku = b.id_buku
JOIN kriteria k ON k.id_kriteria = p.id_kriteria
GROUP BY b.id_buku, b.judul
ORDER BY b.id_buku;

-- 3. Cek nilai MAX tiap kriteria (ini yang dipakai sebagai pembagi normalisasi)
--    Jika banyak buku menyentuh nilai MAX yang sama di semua kolom, itu penyebabnya
SELECT
    k.kode_kriteria,
    k.nama_kriteria,
    MAX(p.nilai) AS nilai_maksimum,
    COUNT(CASE WHEN p.nilai = (SELECT MAX(p2.nilai) FROM penilaian_buku p2 WHERE p2.id_kriteria = p.id_kriteria) THEN 1 END) AS jumlah_buku_yang_menyentuh_max
FROM penilaian_buku p
JOIN kriteria k ON k.id_kriteria = p.id_kriteria
GROUP BY k.kode_kriteria, k.nama_kriteria;
