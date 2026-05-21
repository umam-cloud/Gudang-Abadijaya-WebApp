<?php
require_once __DIR__ . '/../config/Database.php';

class PengirimanModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getAll($limit = 100, $offset = 0) {
        $sql = "SELECT p.*, r.kode_relasi, r.nama_relasi, b.nama_barang 
                FROM pengiriman p
                JOIN relasi r ON p.relasi_id = r.id
                JOIN barang b ON p.barang_id = b.id
                ORDER BY p.tanggal DESC, p.id DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM pengiriman WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($tanggal, $relasi_id, $barang_id, $jumlah_masuk, $jumlah_keluar, $keterangan = '') {
        try {
            $this->db->beginTransaction();
            
            // Insert pengiriman
            $stmt = $this->db->prepare(
                "INSERT INTO pengiriman (tanggal, relasi_id, barang_id, jumlah_masuk, jumlah_keluar, keterangan) 
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$tanggal, $relasi_id, $barang_id, $jumlah_masuk, $jumlah_keluar, $keterangan]);
            
            // Update warehouse stock
            // stok_ready decreases by jumlah_masuk (full cylinders delivered)
            // stok_kosong increases by jumlah_keluar (empty cylinders returned)
            $stmt_stock = $this->db->prepare(
                "UPDATE stok_gudang 
                 SET stok_ready = stok_ready - ?, 
                     stok_kosong = stok_kosong + ? 
                 WHERE barang_id = ?"
            );
            $stmt_stock->execute([$jumlah_masuk, $jumlah_keluar, $barang_id]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $tanggal, $relasi_id, $barang_id, $jumlah_masuk, $jumlah_keluar, $keterangan = '') {
        try {
            $this->db->beginTransaction();
            
            // Get original record to calculate differences
            $stmt_orig = $this->db->prepare("SELECT barang_id, jumlah_masuk, jumlah_keluar FROM pengiriman WHERE id = ?");
            $stmt_orig->execute([$id]);
            $orig = $stmt_orig->fetch();
            
            if (!$orig) {
                throw new Exception("Transaction not found.");
            }
            
            // If the cylinder type has changed, we revert stock on the old one, and apply stock on the new one
            if ($orig['barang_id'] == $barang_id) {
                // Same cylinder type: calculate diffs
                $diff_masuk = $jumlah_masuk - $orig['jumlah_masuk'];
                $diff_keluar = $jumlah_keluar - $orig['jumlah_keluar'];
                
                $stmt_stock = $this->db->prepare(
                    "UPDATE stok_gudang 
                     SET stok_ready = stok_ready - ?, 
                         stok_kosong = stok_kosong + ? 
                     WHERE barang_id = ?"
                );
                $stmt_stock->execute([$diff_masuk, $diff_keluar, $barang_id]);
            } else {
                // Revert old cylinder stock
                $stmt_stock_old = $this->db->prepare(
                    "UPDATE stok_gudang 
                     SET stok_ready = stok_ready + ?, 
                         stok_kosong = stok_kosong - ? 
                     WHERE barang_id = ?"
                );
                $stmt_stock_old->execute([$orig['jumlah_masuk'], $orig['jumlah_keluar'], $orig['barang_id']]);
                
                // Apply new cylinder stock
                $stmt_stock_new = $this->db->prepare(
                    "UPDATE stok_gudang 
                     SET stok_ready = stok_ready - ?, 
                         stok_kosong = stok_kosong + ? 
                     WHERE barang_id = ?"
                );
                $stmt_stock_new->execute([$jumlah_masuk, $jumlah_keluar, $barang_id]);
            }
            
            // Update pengiriman details
            $stmt = $this->db->prepare(
                "UPDATE pengiriman 
                 SET tanggal = ?, relasi_id = ?, barang_id = ?, jumlah_masuk = ?, jumlah_keluar = ?, keterangan = ? 
                 WHERE id = ?"
            );
            $stmt->execute([$tanggal, $relasi_id, $barang_id, $jumlah_masuk, $jumlah_keluar, $keterangan, $id]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete($id) {
        try {
            $this->db->beginTransaction();
            
            // Get original record to reverse stock updates
            $stmt_orig = $this->db->prepare("SELECT barang_id, jumlah_masuk, jumlah_keluar FROM pengiriman WHERE id = ?");
            $stmt_orig->execute([$id]);
            $orig = $stmt_orig->fetch();
            
            if (!$orig) {
                throw new Exception("Transaction not found.");
            }
            
            // Reverse stock
            $stmt_stock = $this->db->prepare(
                "UPDATE stok_gudang 
                 SET stok_ready = stok_ready + ?, 
                     stok_kosong = stok_kosong - ? 
                 WHERE barang_id = ?"
            );
            $stmt_stock->execute([$orig['jumlah_masuk'], $orig['jumlah_keluar'], $orig['barang_id']]);
            
            // Delete delivery record
            $stmt = $this->db->prepare("DELETE FROM pengiriman WHERE id = ?");
            $stmt->execute([$id]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
