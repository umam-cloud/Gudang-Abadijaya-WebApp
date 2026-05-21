-- Database creation script for Gas Cylinder Management System
CREATE DATABASE IF NOT EXISTS joki_tabung;
USE joki_tabung;

-- 0. Table for Users (Admin / Authentication)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1. Table for Cylinder Types (Barang)
CREATE TABLE IF NOT EXISTS barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(100) NOT NULL UNIQUE,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Table for Relations / Clients (Relasi)
CREATE TABLE IF NOT EXISTS relasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_relasi VARCHAR(50) NOT NULL UNIQUE,
    nama_relasi VARCHAR(150) NOT NULL,
    lokasi VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table for Initial Stock per Relation per Cylinder Type
CREATE TABLE IF NOT EXISTS relasi_stok_awal (
    relasi_id INT NOT NULL,
    barang_id INT NOT NULL,
    stok_awal INT DEFAULT 0,
    PRIMARY KEY (relasi_id, barang_id),
    FOREIGN KEY (relasi_id) REFERENCES relasi(id) ON DELETE CASCADE,
    FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table for Daily Delivery Log (Pengiriman)
CREATE TABLE IF NOT EXISTS pengiriman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    relasi_id INT NOT NULL,
    barang_id INT NOT NULL,
    jumlah_masuk INT DEFAULT 0, -- Full cylinders delivered (masuk/kirim/isi)
    jumlah_keluar INT DEFAULT 0, -- Empty cylinders returned (keluar/kosong)
    keterangan VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (relasi_id) REFERENCES relasi(id) ON DELETE CASCADE,
    FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Table for Warehouse Stocks (Stok Gudang)
CREATE TABLE IF NOT EXISTS stok_gudang (
    barang_id INT PRIMARY KEY,
    stok_ready INT DEFAULT 0,  -- Full cylinders ready in warehouse
    stok_kosong INT DEFAULT 0, -- Empty cylinders in warehouse
    FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Table for Warehouse Log Transactions (Adjustments like Refill, Buy, Sell)
CREATE TABLE IF NOT EXISTS gudang_transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    barang_id INT NOT NULL,
    tipe_transaksi ENUM('beli_baru', 'refill', 'jual_rusak', 'koreksi') NOT NULL,
    jumlah INT NOT NULL,
    keterangan VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Table for Repurchasing / Alerts Action Logs (Evaluasi)
CREATE TABLE IF NOT EXISTS evaluasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    relasi_id INT NOT NULL,
    tanggal DATE NOT NULL,
    status_lanjut ENUM('lanjut', 'putus', 'negosiasi', 'tidak_ada_respon') NOT NULL,
    catatan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (relasi_id) REFERENCES relasi(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
