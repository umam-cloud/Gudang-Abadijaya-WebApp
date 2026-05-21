<?php
require_once __DIR__ . '/../config/Database.php';

class GudangModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getWarehouseStock() {
        $sql = "SELECT sg.*, b.nama_barang, b.deskripsi 
                FROM stok_gudang sg
                JOIN barang b ON sg.barang_id = b.id
                ORDER BY b.nama_barang ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getTransactions($limit = 100, $offset = 0) {
        $sql = "SELECT gt.*, b.nama_barang 
                FROM gudang_transaksi gt
                JOIN barang b ON gt.barang_id = b.id
                ORDER BY gt.tanggal DESC, gt.id DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function addAdjustment($tanggal, $barang_id, $tipe_transaksi, $jumlah, $target_stok = 'ready', $keterangan = '') {
        try {
            $this->db->beginTransaction();
            
            // Insert log
            $stmt = $this->db->prepare(
                "INSERT INTO gudang_transaksi (tanggal, barang_id, tipe_transaksi, jumlah, keterangan) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$tanggal, $barang_id, $tipe_transaksi, $jumlah, $keterangan]);
            
            // Update stok_gudang based on action
            if ($tipe_transaksi === 'refill') {
                // Refill: decreases empty stock, increases full stock
                $stmt_stock = $this->db->prepare(
                    "UPDATE stok_gudang 
                     SET stok_kosong = stok_kosong - ?, 
                         stok_ready = stok_ready + ? 
                     WHERE barang_id = ?"
                );
                $stmt_stock->execute([$jumlah, $jumlah, $barang_id]);
            } else if ($tipe_transaksi === 'beli_baru') {
                // Buy: increases selected stock type (ready or kosong)
                if ($target_stok === 'kosong') {
                    $stmt_stock = $this->db->prepare("UPDATE stok_gudang SET stok_kosong = stok_kosong + ? WHERE barang_id = ?");
                } else {
                    $stmt_stock = $this->db->prepare("UPDATE stok_gudang SET stok_ready = stok_ready + ? WHERE barang_id = ?");
                }
                $stmt_stock->execute([$jumlah, $barang_id]);
            } else if ($tipe_transaksi === 'jual_rusak') {
                // Sell/Damaged: decreases selected stock type
                if ($target_stok === 'kosong') {
                    $stmt_stock = $this->db->prepare("UPDATE stok_gudang SET stok_kosong = stok_kosong - ? WHERE barang_id = ?");
                } else {
                    $stmt_stock = $this->db->prepare("UPDATE stok_gudang SET stok_ready = stok_ready - ? WHERE barang_id = ?");
                }
                $stmt_stock->execute([$jumlah, $barang_id]);
            } else if ($tipe_transaksi === 'koreksi') {
                // Manual correction (can be positive or negative)
                if ($target_stok === 'kosong') {
                    $stmt_stock = $this->db->prepare("UPDATE stok_gudang SET stok_kosong = stok_kosong + ? WHERE barang_id = ?");
                } else {
                    $stmt_stock = $this->db->prepare("UPDATE stok_gudang SET stok_ready = stok_ready + ? WHERE barang_id = ?");
                }
                $stmt_stock->execute([$jumlah, $barang_id]);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
