-- =====================================================================
-- SKEMA DATABASE: SISTEM INFORMASI PERPUSTAKAAN BERBASIS WEB
-- DENGAN SPK REKOMENDASI BUKU (METODE SAW - Simple Additive Weighting)
-- =====================================================================
-- Catatan:
-- - Semua kriteria (C1-C4) bertipe BENEFIT (semakin besar nilai, semakin baik)
-- - Kolom bobot & nilai menggunakan DECIMAL agar presisi perhitungan SAW terjaga
-- - Beberapa tabel relasi (Detail_Peminjaman, Penilaian_Buku, Preferensi_Anggota)
--   menggunakan COMPOSITE PRIMARY KEY (gabungan 2 kolom FK) agar sesuai dengan
--   fungsi bisnisnya (misal: 1 peminjaman bisa berisi banyak buku, 1 buku bisa
--   dinilai oleh banyak kriteria). Ini adalah praktik standar normalisasi
--   database relasional untuk tabel penghubung many-to-many.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS db_perpustakaan_spk
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE db_perpustakaan_spk;

-- ---------------------------------------------------------------------
-- 1. TABEL BUKU
-- Menyimpan data master buku perpustakaan
-- ---------------------------------------------------------------------
CREATE TABLE Buku (
    id_buku       INT AUTO_INCREMENT PRIMARY KEY,
    judul         VARCHAR(150) NOT NULL,
    penulis       VARCHAR(100) NOT NULL,
    penerbit      VARCHAR(100) NOT NULL,
    tahun_terbit  YEAR NOT NULL,
    kategori      ENUM('Programming', 'Artificial Intelligent', 'Sains',
                        'Self-Improvement', 'Sosial', 'Fiksi') NOT NULL,
    stok          INT NOT NULL DEFAULT 0,
    rating        DECIMAL(3,2) DEFAULT 0.00,      -- rata-rata rating buku (C1)
    jumlah_pinjam INT DEFAULT 0,                  -- akumulasi jumlah peminjaman (C3)
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. TABEL ANGGOTA
-- Menyimpan data anggota / pengguna perpustakaan
-- ---------------------------------------------------------------------
CREATE TABLE Anggota (
    id_anggota    INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50) NOT NULL UNIQUE,
    password      VARCHAR(255) NOT NULL,          -- disimpan dalam bentuk hash (bcrypt)
    nama          VARCHAR(100) NOT NULL,
    alamat        TEXT,
    no_hp         VARCHAR(20),
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3. TABEL PEMINJAMAN
-- Menyimpan transaksi peminjaman (header)
-- ---------------------------------------------------------------------
CREATE TABLE Peminjaman (
    id_peminjaman   INT AUTO_INCREMENT PRIMARY KEY,
    id_anggota      INT NOT NULL,
    tanggal_pinjam  DATE NOT NULL,
    tanggal_kembali DATE NULL,
    status          ENUM('Dipinjam', 'Dikembalikan', 'Terlambat') DEFAULT 'Dipinjam',
    CONSTRAINT fk_peminjaman_anggota
        FOREIGN KEY (id_anggota) REFERENCES Anggota(id_anggota)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4. TABEL DETAIL_PEMINJAMAN
-- Menyimpan detail buku apa saja yang dipinjam dalam satu transaksi
-- (relasi many-to-many antara Peminjaman dan Buku)
-- ---------------------------------------------------------------------
CREATE TABLE Detail_Peminjaman (
    id_peminjaman INT NOT NULL,
    id_buku       INT NOT NULL,
    jumlah_buku   INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id_peminjaman, id_buku),          -- composite key
    CONSTRAINT fk_detail_peminjaman
        FOREIGN KEY (id_peminjaman) REFERENCES Peminjaman(id_peminjaman)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_detail_buku
        FOREIGN KEY (id_buku) REFERENCES Buku(id_buku)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 5. TABEL KRITERIA
-- Menyimpan master kriteria SPK beserta bobotnya (C1-C4)
-- ---------------------------------------------------------------------
CREATE TABLE Kriteria (
    id_kriteria   INT AUTO_INCREMENT PRIMARY KEY,
    kode_kriteria VARCHAR(5) NOT NULL UNIQUE,      -- C1, C2, C3, C4
    nama_kriteria VARCHAR(100) NOT NULL,
    jenis         ENUM('Benefit', 'Cost') NOT NULL DEFAULT 'Benefit',
    bobot         DECIMAL(4,2) NOT NULL            -- contoh: 0.30
) ENGINE=InnoDB;

-- Data awal kriteria sesuai laporan proyek
INSERT INTO Kriteria (kode_kriteria, nama_kriteria, jenis, bobot) VALUES
('C1', 'Rating Buku',        'Benefit', 0.30),
('C2', 'Tahun Terbit',       'Benefit', 0.25),
('C3', 'Jumlah Peminjaman',  'Benefit', 0.25),
('C4', 'Kategori Buku',      'Benefit', 0.20);

-- ---------------------------------------------------------------------
-- 6. TABEL PENILAIAN_BUKU
-- Matriks keputusan: nilai tiap buku untuk tiap kriteria (X_ij)
-- ---------------------------------------------------------------------
CREATE TABLE Penilaian_Buku (
    id_buku     INT NOT NULL,
    id_kriteria INT NOT NULL,
    nilai       DECIMAL(10,2) NOT NULL,            -- nilai mentah x_ij
    PRIMARY KEY (id_buku, id_kriteria),             -- composite key
    CONSTRAINT fk_penilaian_buku
        FOREIGN KEY (id_buku) REFERENCES Buku(id_buku)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_penilaian_kriteria
        FOREIGN KEY (id_kriteria) REFERENCES Kriteria(id_kriteria)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 7. TABEL REKOMENDASI_BUKU
-- Menyimpan hasil akhir perhitungan SAW (nilai preferensi & ranking)
-- ---------------------------------------------------------------------
CREATE TABLE Rekomendasi_Buku (
    id_hasil         INT AUTO_INCREMENT PRIMARY KEY,
    id_buku          INT NOT NULL,
    bobot_preferensi DECIMAL(10,6) NOT NULL,        -- nilai V_i hasil SAW
    ranking          INT NOT NULL,
    tanggal_hitung    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rekomendasi_buku
        FOREIGN KEY (id_buku) REFERENCES Buku(id_buku)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 8. TABEL PREFERENSI_ANGGOTA
-- Menyimpan preferensi/bobot personal anggota terhadap tiap kriteria
-- (opsional, untuk personalisasi rekomendasi per anggota)
-- ---------------------------------------------------------------------
CREATE TABLE Preferensi_Anggota (
    id_anggota      INT NOT NULL,
    id_kriteria     INT NOT NULL,
    skor_rekomendasi DECIMAL(10,2) DEFAULT 0.00,
    PRIMARY KEY (id_anggota, id_kriteria),          -- composite key
    CONSTRAINT fk_preferensi_anggota
        FOREIGN KEY (id_anggota) REFERENCES Anggota(id_anggota)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_preferensi_kriteria
        FOREIGN KEY (id_kriteria) REFERENCES Kriteria(id_kriteria)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- CONTOH DATA DUMMY UNTUK TESTING (opsional, hapus jika tidak perlu)
-- =====================================================================
INSERT INTO Buku (judul, penulis, penerbit, tahun_terbit, kategori, stok, rating, jumlah_pinjam) VALUES
('Belajar Laravel Dasar', 'Budi Santoso', 'Informatika', 2023, 'Programming', 5, 4.50, 120),
('Pengantar Kecerdasan Buatan', 'Siti Aminah', 'Andi Publisher', 2022, 'Artificial Intelligent', 3, 4.20, 95),
('Fisika Dasar Modern', 'Andi Wijaya', 'Erlangga', 2021, 'Sains', 4, 3.80, 60),
('7 Kebiasaan Efektif', 'Stephen R.', 'Gramedia', 2020, 'Self-Improvement', 6, 4.60, 150),
('Sosiologi Masyarakat', 'Rina Kurnia', 'Rajawali Pers', 2019, 'Sosial', 2, 3.50, 40),
('Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2018, 'Fiksi', 7, 4.80, 200);
