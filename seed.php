<?php
require_once __DIR__ . '/app/config/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Create new tables from schema first
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    $db->exec($sql);
    
    // Clear existing data for clean seed
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $db->exec("TRUNCATE TABLE users;");
    $db->exec("TRUNCATE TABLE evaluasi;");
    $db->exec("TRUNCATE TABLE pengiriman;");
    $db->exec("TRUNCATE TABLE relasi_stok_awal;");
    $db->exec("TRUNCATE TABLE relasi;");
    $db->exec("TRUNCATE TABLE gudang_transaksi;");
    $db->exec("TRUNCATE TABLE stok_gudang;");
    $db->exec("TRUNCATE TABLE barang;");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // 0. Seed User
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $db->exec("INSERT INTO users (username, password, nama_lengkap) VALUES ('admin', '$password', 'Administrator Joki')");
    echo "Seed Users OK (Username: admin, Pass: admin123)\n";

    // 1. Seed Barang (Cylinder Types)
    $db->exec("INSERT INTO barang (nama_barang, deskripsi) VALUES
        ('OXY 6m3', 'Oksigen Medis Besar'),
        ('OXY 1m3', 'Oksigen Medis Kecil (Troli)'),
        ('N2O', 'Nitrous Oxide'),
        ('LPG 12kg', 'LPG Industri 12kg'),
        ('LPG 50kg', 'LPG Industri 50kg');
    ");
    echo "Seed Barang OK\n";
    
    // Initialize stok_gudang for each barang
    $db->exec("INSERT INTO stok_gudang (barang_id, stok_ready, stok_kosong) VALUES
        (1, 0, 0),
        (2, 0, 0),
        (3, 0, 0),
        (4, 0, 0),
        (5, 0, 0);
    ");

    // 2. Seed Gudang Transaksi (Initial Warehouse Stock)
    $db->exec("INSERT INTO gudang_transaksi (tanggal, barang_id, tipe_transaksi, jumlah, keterangan) VALUES
        (CURDATE(), 1, 'beli_baru', 100, 'Stok awal OXY 6m3'),
        (CURDATE(), 2, 'beli_baru', 50, 'Stok awal OXY 1m3'),
        (CURDATE(), 3, 'beli_baru', 20, 'Stok awal N2O'),
        (CURDATE(), 4, 'beli_baru', 200, 'Stok awal LPG 12kg'),
        (CURDATE(), 5, 'beli_baru', 30, 'Stok awal LPG 50kg'),
        (CURDATE(), 1, 'beli_baru', 15, 'Tabung rusak/kosong OXY 6m3');
    ");
    
    // Update stok gudang based on those transactions
    $db->exec("UPDATE stok_gudang SET stok_ready = stok_ready + 100 WHERE barang_id = 1;");
    $db->exec("UPDATE stok_gudang SET stok_ready = stok_ready + 50 WHERE barang_id = 2;");
    $db->exec("UPDATE stok_gudang SET stok_ready = stok_ready + 20 WHERE barang_id = 3;");
    $db->exec("UPDATE stok_gudang SET stok_ready = stok_ready + 200 WHERE barang_id = 4;");
    $db->exec("UPDATE stok_gudang SET stok_ready = stok_ready + 30 WHERE barang_id = 5;");
    $db->exec("UPDATE stok_gudang SET stok_kosong = stok_kosong + 15 WHERE barang_id = 1;");
    echo "Seed Gudang OK\n";

    // 3. Seed Relasi (Clients)
    $db->exec("INSERT INTO relasi (kode_relasi, nama_relasi, lokasi) VALUES
        ('H-J01', 'RSUD Jombang', 'Jl. KH Wahid Hasyim'),
        ('H-S02', 'Klinik Sehat', 'Jl. Merdeka Raya'),
        ('I-01', 'PT. Pabrik Tahu', 'Kawasan Industri'),
        ('I-02', 'Warung Makan Sederhana', 'Jl. Sudirman');
    ");
    echo "Seed Relasi OK\n";

    // 4. Seed Relasi Stok Awal
    $db->exec("INSERT INTO relasi_stok_awal (relasi_id, barang_id, stok_awal) VALUES
        (1, 1, 10), -- RSUD has 10 OXY 6m3
        (1, 2, 5),  -- RSUD has 5 OXY 1m3
        (2, 2, 2),  -- Klinik has 2 OXY 1m3
        (3, 5, 4),  -- Pabrik has 4 LPG 50kg
        (4, 4, 8);  -- Warung has 8 LPG 12kg
    ");
    echo "Seed Relasi Stok Awal OK\n";

    // 5. Seed Pengiriman (Deliveries)
    $oldDate = date('Y-m-d', strtotime('-40 days'));
    $today = date('Y-m-d');
    
    $db->exec("INSERT INTO pengiriman (tanggal, relasi_id, barang_id, jumlah_masuk, jumlah_keluar, keterangan) VALUES
        ('$oldDate', 2, 2, 2, 0, 'Pengiriman awal Klinik'),
        ('$today', 1, 1, 5, 2, 'Kirim 5 OXY 6m3, tarik 2 kosong'),
        ('$today', 3, 5, 2, 2, 'Tukar 2 LPG 50kg');
    ");
    
    $db->exec("UPDATE stok_gudang SET stok_ready = stok_ready - 2 WHERE barang_id = 2;");
    $db->exec("UPDATE stok_gudang SET stok_ready = stok_ready - 5, stok_kosong = stok_kosong + 2 WHERE barang_id = 1;");
    $db->exec("UPDATE stok_gudang SET stok_ready = stok_ready - 2, stok_kosong = stok_kosong + 2 WHERE barang_id = 5;");
    echo "Seed Pengiriman OK\n";

    // 6. Seed Evaluasi (Evaluations)
    $db->exec("INSERT INTO evaluasi (relasi_id, tanggal, status_lanjut, catatan) VALUES
        (2, '$today', 'negosiasi', 'Sudah dihubungi via WA, klinik sedang renovasi jadi belum butuh refill.')
    ");
    echo "Seed Evaluasi OK\n";

    echo "ALL SEEDING COMPLETED SUCCESSFULLY!\n";

} catch (Exception $e) {
    echo "SEEDING FAILED: " . $e->getMessage() . "\n";
}
