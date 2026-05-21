<?php
require_once __DIR__ . '/../config/Database.php';

class RelasiModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM relasi ORDER BY kode_relasi ASC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM relasi WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($kode_relasi, $nama_relasi, $lokasi, $stok_awal_array = []) {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("INSERT INTO relasi (kode_relasi, nama_relasi, lokasi) VALUES (?, ?, ?)");
            $stmt->execute([$kode_relasi, $nama_relasi, $lokasi]);
            $relasi_id = $this->db->lastInsertId();
            
            // Insert initial stocks
            $stmt_init = $this->db->prepare("INSERT INTO relasi_stok_awal (relasi_id, barang_id, stok_awal) VALUES (?, ?, ?)");
            foreach ($stok_awal_array as $barang_id => $stok_awal) {
                if ($stok_awal !== '' && $stok_awal !== null) {
                    $stmt_init->execute([$relasi_id, $barang_id, (int)$stok_awal]);
                } else {
                    $stmt_init->execute([$relasi_id, $barang_id, 0]);
                }
            }
            
            $this->db->commit();
            return $relasi_id;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $kode_relasi, $nama_relasi, $lokasi, $stok_awal_array = []) {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("UPDATE relasi SET kode_relasi = ?, nama_relasi = ?, lokasi = ? WHERE id = ?");
            $stmt->execute([$kode_relasi, $nama_relasi, $lokasi, $id]);
            
            // Clear and rewrite initial stocks
            $stmt_del = $this->db->prepare("DELETE FROM relasi_stok_awal WHERE relasi_id = ?");
            $stmt_del->execute([$id]);
            
            $stmt_init = $this->db->prepare("INSERT INTO relasi_stok_awal (relasi_id, barang_id, stok_awal) VALUES (?, ?, ?)");
            foreach ($stok_awal_array as $barang_id => $stok_awal) {
                if ($stok_awal !== '' && $stok_awal !== null) {
                    $stmt_init->execute([$id, $barang_id, (int)$stok_awal]);
                } else {
                    $stmt_init->execute([$id, $barang_id, 0]);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM relasi WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getStokAwal($relasi_id) {
        $stmt = $this->db->prepare("SELECT barang_id, stok_awal FROM relasi_stok_awal WHERE relasi_id = ?");
        $stmt->execute([$relasi_id]);
        $rows = $stmt->fetchAll();
        
        $stok_awal = [];
        foreach ($rows as $row) {
            $stok_awal[$row['barang_id']] = $row['stok_awal'];
        }
        return $stok_awal;
    }

    /**
     * Get a cross product of all clients and cylinder types, detailing stocks.
     */
    public function getAllWithStocks() {
        $sql = "SELECT 
                    r.id as relasi_id,
                    r.kode_relasi,
                    r.nama_relasi,
                    r.lokasi,
                    b.id as barang_id,
                    b.nama_barang,
                    COALESCE(sa.stok_awal, 0) as stok_awal,
                    COALESCE(SUM(p.jumlah_masuk), 0) as total_masuk,
                    COALESCE(SUM(p.jumlah_keluar), 0) as total_keluar,
                    (COALESCE(sa.stok_awal, 0) + COALESCE(SUM(p.jumlah_masuk), 0) - COALESCE(SUM(p.jumlah_keluar), 0)) as stok_akhir
                FROM relasi r
                CROSS JOIN barang b
                LEFT JOIN relasi_stok_awal sa ON sa.relasi_id = r.id AND sa.barang_id = b.id
                LEFT JOIN pengiriman p ON p.relasi_id = r.id AND p.barang_id = b.id
                GROUP BY r.id, b.id
                ORDER BY r.kode_relasi ASC, b.nama_barang ASC";
        
        $stmt = $this->db->query($sql);
        $raw = $stmt->fetchAll();
        
        // Group by relasi_id for easy display
        $grouped = [];
        foreach ($raw as $row) {
            $r_id = $row['relasi_id'];
            if (!isset($grouped[$r_id])) {
                $grouped[$r_id] = [
                    'id' => $r_id,
                    'kode_relasi' => $row['kode_relasi'],
                    'nama_relasi' => $row['nama_relasi'],
                    'lokasi' => $row['lokasi'],
                    'stocks' => []
                ];
            }
            $grouped[$r_id]['stocks'][] = [
                'barang_id' => $row['barang_id'],
                'nama_barang' => $row['nama_barang'],
                'stok_awal' => $row['stok_awal'],
                'masuk' => $row['total_masuk'],
                'keluar' => $row['total_keluar'],
                'stok_akhir' => $row['stok_akhir']
            ];
        }
        return $grouped;
    }

    /**
     * Get list of inactivity details (last delivery date, days since, and latest action status).
     */
    public function getInactivityAlerts() {
        $sql = "SELECT 
                    r.id as relasi_id,
                    r.kode_relasi,
                    r.nama_relasi,
                    r.lokasi,
                    MAX(p.tanggal) as tanggal_terakhir,
                    CASE 
                        WHEN MAX(p.tanggal) IS NULL THEN NULL
                        ELSE DATEDIFF(CURRENT_DATE, MAX(p.tanggal)) 
                    END as hari_sejak_pengiriman,
                    ev.status_lanjut,
                    ev.catatan as evaluasi_catatan,
                    ev.tanggal as evaluasi_tanggal
                FROM relasi r
                LEFT JOIN pengiriman p ON p.relasi_id = r.id
                LEFT JOIN (
                    -- Get only the latest evaluation record for each relasi
                    SELECT e1.* 
                    FROM evaluasi e1
                    INNER JOIN (
                        SELECT relasi_id, MAX(created_at) as max_created 
                        FROM evaluasi 
                        GROUP BY relasi_id
                    ) e2 ON e1.relasi_id = e2.relasi_id AND e1.created_at = e2.max_created
                ) ev ON ev.relasi_id = r.id
                GROUP BY r.id
                ORDER BY hari_sejak_pengiriman DESC, r.kode_relasi ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
