CREATE DATABASE safety_reporting;
USE safety_reporting;

-- 1. TABEL USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    namaLengkap VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    peran ENUM('admin', 'karyawan') NOT NULL DEFAULT 'karyawan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. TABEL LAPORAN
CREATE TABLE laporan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(20) NOT NULL UNIQUE,
    karyawan_id INT NOT NULL, 

    judul VARCHAR(150) NOT NULL,
    deskripsi TEXT NOT NULL,
    kategori ENUM(
        'Kecelakaan Kerja', 
        'Nyaris Celaka',
        'Kondisi Tidak Aman',
        'Tindakan Tidak Aman'
    ) NOT NULL,
    lokasi VARCHAR(150) NOT NULL,
    file_lampiran VARCHAR(255),
    status ENUM(
        'Menunggu Verifikasi',
        'Sedang Ditindaklanjuti',
        'Dalam Perbaikan',
        'Selesai',
        'Ditolak'
    ) NOT NULL DEFAULT 'Menunggu Verifikasi',

    tanggal_lapor DATE NOT NULL DEFAULT (CURRENT_DATE),
    tanggal_tutup DATE,

    FOREIGN KEY (karyawan_id) REFERENCES users(id)
);