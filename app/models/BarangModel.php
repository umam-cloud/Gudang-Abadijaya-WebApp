<?php
require_once __DIR__ . '/../config/Database.php';

class BarangModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM barang ORDER BY nama_barang ASC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM barang WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($nama_barang, $deskripsi = '') {
        try {
            $this->db->beginTransaction();
            
            // Insert into barang
            $stmt = $this->db->prepare("INSERT INTO barang (nama_barang, deskripsi) VALUES (?, ?)");
            $stmt->execute([$nama_barang, $deskripsi]);
            $barang_id = $this->db->lastInsertId();
            
            // Initialize warehouse stock to 0
            $stmt_stock = $this->db->prepare("INSERT INTO stok_gudang (barang_id, stok_ready, stok_kosong) VALUES (?, 0, 0)");
            $stmt_stock->execute([$barang_id]);
            
            $this->db->commit();
            return $barang_id;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $nama_barang, $deskripsi = '') {
        $stmt = $this->db->prepare("UPDATE barang SET nama_barang = ?, deskripsi = ? WHERE id = ?");
        return $stmt->execute([$nama_barang, $deskripsi, $id]);
    }

    public function delete($id) {
        // FK cascade deletes entries in stok_gudang, relasi_stok_awal, pengiriman, etc.
        $stmt = $this->db->prepare("DELETE FROM barang WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
