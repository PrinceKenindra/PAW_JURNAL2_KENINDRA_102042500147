CREATE DATABASE IF NOT EXISTS db_lms_paw;
USE db_lms_paw;

-- Tabel Mata Kuliah
CREATE TABLE mata_kuliah (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    dosen VARCHAR(100) NOT NULL,
    semester TINYINT UNSIGNED NOT NULL
) ENGINE=InnoDB;

-- Tabel Materi (Relasi 1 ke N dengan Mata Kuliah)
CREATE TABLE materi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mata_kuliah_id INT UNSIGNED NOT NULL,
    judul VARCHAR(150) NOT NULL,
    isi TEXT NOT NULL,
    tautan VARCHAR(255) NOT NULL,
    FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Tabel Tugas (Relasi 1 ke N dengan Mata Kuliah)
CREATE TABLE tugas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mata_kuliah_id INT UNSIGNED NOT NULL,
    judul VARCHAR(150) NOT NULL,
    instruksi TEXT NOT NULL,
    tenggat DATE NOT NULL,
    status VARCHAR(20) NOT NULL,
    FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Data Awal Mata Kuliah
INSERT INTO mata_kuliah (kode, nama, dosen, semester) VALUES
('ALP-101', 'Algoritma dan Pemrograman', 'Dosen ALP', 1),
('PAW-101', 'Pengembangan Aplikasi Website', 'Dosen PAW', 3);

-- Data Awal Materi
INSERT INTO materi (mata_kuliah_id, judul, isi, tautan) VALUES
(2, 'Pengantar PHP', 'Materi membahas dasar sintaks dan operasi dasar pada PHP.', 'https://www.php.net/manual/en/tutorial.php');

-- Data Awal Tugas
INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status) VALUES
(2, 'Latihan CRUD', 'Buatlah aplikasi CRUD sederhana tanpa framework.', '2026-06-30', 'Aktif');