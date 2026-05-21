<?php
require_once __DIR__ . '/../config/Database.php';

class EvaluasiModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getHistoryByRelasi($relasi_id) {
        $stmt = $this->db->prepare(
            "SELECT * FROM evaluasi 
             WHERE relasi_id = ? 
             ORDER BY tanggal DESC, created_at DESC"
        );
        $stmt->execute([$relasi_id]);
        return $stmt->fetchAll();
    }

    public function create($relasi_id, $tanggal, $status_lanjut, $catatan = '') {
        $stmt = $this->db->prepare(
            "INSERT INTO evaluasi (relasi_id, tanggal, status_lanjut, catatan) 
             VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([$relasi_id, $tanggal, $status_lanjut, $catatan]);
    }
}
