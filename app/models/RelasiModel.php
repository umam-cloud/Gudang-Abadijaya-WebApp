<?php
require_once __DIR__ . '/../config/Database.php';

class RelasiModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM relasi ORDER BY nama_relasi ASC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM relasi WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($nama_relasi, $lokasi, $stok_awal_array = []) {
        try {
            $this->db->beginTransaction();
            
            // Validate and deduct from warehouse
            $stmt_check = $this->db->prepare("SELECT b.nama_barang, sg.stok_ready FROM stok_gudang sg JOIN barang b ON sg.barang_id = b.id WHERE sg.barang_id = ?");
            $stmt_update_gudang = $this->db->prepare("UPDATE stok_gudang SET stok_ready = stok_ready - ? WHERE barang_id = ?");
            $stmt_log = $this->db->prepare("INSERT INTO gudang_transaksi (tanggal, barang_id, tipe_transaksi, jumlah, keterangan) VALUES (CURDATE(), ?, 'koreksi', ?, ?)");

            foreach ($stok_awal_array as $barang_id => $stok_awal) {
                $stok = (int)$stok_awal;
                if ($stok > 0) {
                    $stmt_check->execute([$barang_id]);
                    $row = $stmt_check->fetch();
                    if (!$row || $stok > $row['stok_ready']) {
                        throw new Exception("Stok awal (" . $stok . ") untuk " . ($row ? $row['nama_barang'] : 'Barang ID '.$barang_id) . " melebihi ketersediaan di gudang (" . ($row ? $row['stok_ready'] : 0) . ").");
                    }
                    $stmt_update_gudang->execute([$stok, $barang_id]);
                    $stmt_log->execute([$barang_id, $stok, "Pinjaman stok awal untuk mitra baru: " . $nama_relasi]);
                }
            }

            $stmt = $this->db->prepare("INSERT INTO relasi (nama_relasi, lokasi) VALUES (?, ?)");
            $stmt->execute([$nama_relasi, $lokasi]);
            $relasi_id = $this->db->lastInsertId();
            
            // Insert initial stocks
            $stmt_init = $this->db->prepare("INSERT INTO relasi_stok_awal (relasi_id, barang_id, stok_awal) VALUES (?, ?, ?)");
            foreach ($stok_awal_array as $barang_id => $stok_awal) {
                $stok = (int)$stok_awal;
                $stmt_init->execute([$relasi_id, $barang_id, $stok]);
            }
            
            $this->db->commit();
            return $relasi_id;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $nama_relasi, $lokasi, $stok_awal_array = []) {
        try {
            $this->db->beginTransaction();
            
            // Fetch old starting stocks to calculate difference
            $stmt_old_stocks = $this->db->prepare("SELECT barang_id, stok_awal FROM relasi_stok_awal WHERE relasi_id = ?");
            $stmt_old_stocks->execute([$id]);
            $old_rows = $stmt_old_stocks->fetchAll();
            $old_stocks = [];
            foreach ($old_rows as $row) {
                $old_stocks[$row['barang_id']] = (int)$row['stok_awal'];
            }

            $stmt_check = $this->db->prepare("SELECT b.nama_barang, sg.stok_ready FROM stok_gudang sg JOIN barang b ON sg.barang_id = b.id WHERE sg.barang_id = ?");
            $stmt_update_gudang_kurang = $this->db->prepare("UPDATE stok_gudang SET stok_ready = stok_ready - ? WHERE barang_id = ?");
            $stmt_update_gudang_tambah = $this->db->prepare("UPDATE stok_gudang SET stok_ready = stok_ready + ? WHERE barang_id = ?");
            $stmt_log = $this->db->prepare("INSERT INTO gudang_transaksi (tanggal, barang_id, tipe_transaksi, jumlah, keterangan) VALUES (CURDATE(), ?, 'koreksi', ?, ?)");

            // Process differences and validate
            foreach ($stok_awal_array as $barang_id => $new_stok_raw) {
                $new_stok = (int)$new_stok_raw;
                $old_stok = isset($old_stocks[$barang_id]) ? $old_stocks[$barang_id] : 0;
                $diff = $new_stok - $old_stok;

                if ($diff > 0) { // Needs to take MORE from warehouse
                    $stmt_check->execute([$barang_id]);
                    $row = $stmt_check->fetch();
                    if (!$row || $diff > $row['stok_ready']) {
                        throw new Exception("Penambahan stok awal (" . $diff . ") untuk " . ($row ? $row['nama_barang'] : 'Barang ID '.$barang_id) . " melebihi ketersediaan di gudang (" . ($row ? $row['stok_ready'] : 0) . ").");
                    }
                    $stmt_update_gudang_kurang->execute([$diff, $barang_id]);
                    $stmt_log->execute([$barang_id, $diff, "Penyesuaian tambah stok awal mitra: " . $nama_relasi]);
                } else if ($diff < 0) { // Returns SOME to warehouse
                    $abs_diff = abs($diff);
                    $stmt_update_gudang_tambah->execute([$abs_diff, $barang_id]);
                    $stmt_log->execute([$barang_id, $abs_diff, "Penyesuaian kurang stok awal mitra (kembali ke gudang): " . $nama_relasi]);
                }
            }
            
            $stmt = $this->db->prepare("UPDATE relasi SET nama_relasi = ?, lokasi = ? WHERE id = ?");
            $stmt->execute([$nama_relasi, $lokasi, $id]);
            
            // Clear and rewrite initial stocks
            $stmt_del = $this->db->prepare("DELETE FROM relasi_stok_awal WHERE relasi_id = ?");
            $stmt_del->execute([$id]);
            
            $stmt_init = $this->db->prepare("INSERT INTO relasi_stok_awal (relasi_id, barang_id, stok_awal) VALUES (?, ?, ?)");
            foreach ($stok_awal_array as $barang_id => $stok_awal) {
                $stok = (int)$stok_awal;
                $stmt_init->execute([$id, $barang_id, $stok]);
            }
            
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

            // 1. Dapatkan nama relasi untuk log
            $relasi = $this->getById($id);
            if (!$relasi) {
                $this->db->rollBack();
                return false;
            }
            $nama_mitra = $relasi['nama_relasi'];

            // 2. Hitung stok akhir yang masih ada di mitra
            $sql = "SELECT 
                        b.id as barang_id,
                        (MAX(COALESCE(sa.stok_awal, 0)) + COALESCE(SUM(p.jumlah_masuk), 0) - COALESCE(SUM(p.jumlah_keluar), 0)) as stok_akhir
                    FROM relasi r
                    CROSS JOIN barang b
                    LEFT JOIN relasi_stok_awal sa ON sa.relasi_id = r.id AND sa.barang_id = b.id
                    LEFT JOIN pengiriman p ON p.relasi_id = r.id AND p.barang_id = b.id
                    WHERE r.id = ?
                    GROUP BY r.id, b.id";
            $stmt_stocks = $this->db->prepare($sql);
            $stmt_stocks->execute([$id]);
            $stocks = $stmt_stocks->fetchAll();

            // 3. Kembalikan tabung ke gudang (sebagai stok_kosong) dan catat log
            $stmt_update_gudang = $this->db->prepare("UPDATE stok_gudang SET stok_kosong = stok_kosong + ? WHERE barang_id = ?");
            $stmt_log = $this->db->prepare("INSERT INTO gudang_transaksi (tanggal, barang_id, tipe_transaksi, jumlah, keterangan) VALUES (CURDATE(), ?, 'koreksi', ?, ?)");

            foreach ($stocks as $stock) {
                if ($stock['stok_akhir'] > 0) {
                    $jumlah_kembali = $stock['stok_akhir'];
                    $barang_id = $stock['barang_id'];
                    $keterangan = "Pengembalian otomatis dari " . $nama_mitra . " (Mitra Dihapus)";

                    $stmt_update_gudang->execute([$jumlah_kembali, $barang_id]);
                    $stmt_log->execute([$barang_id, $jumlah_kembali, $keterangan]);
                }
            }

            // 4. Hapus mitra (cascade akan menghapus pengiriman, stok awal, evaluasi)
            $stmt = $this->db->prepare("DELETE FROM relasi WHERE id = ?");
            $stmt->execute([$id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
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
    public function countAllRelasi() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM relasi");
        return $stmt->fetchColumn();
    }

    public function getAllWithStocks($limit = 30, $offset = 0) {
        $sql = "SELECT 
                    r.id as relasi_id,
                    r.nama_relasi,
                    r.lokasi,
                    b.id as barang_id,
                    b.nama_barang,
                    COALESCE(sa.stok_awal, 0) as stok_awal,
                    COALESCE(SUM(p.jumlah_masuk), 0) as total_masuk,
                    COALESCE(SUM(p.jumlah_keluar), 0) as total_keluar,
                    (COALESCE(sa.stok_awal, 0) + COALESCE(SUM(p.jumlah_masuk), 0) - COALESCE(SUM(p.jumlah_keluar), 0)) as stok_akhir
                FROM (SELECT * FROM relasi ORDER BY nama_relasi ASC LIMIT ? OFFSET ?) r
                CROSS JOIN barang b
                LEFT JOIN relasi_stok_awal sa ON sa.relasi_id = r.id AND sa.barang_id = b.id
                LEFT JOIN pengiriman p ON p.relasi_id = r.id AND p.barang_id = b.id
                GROUP BY r.id, b.id
                ORDER BY r.nama_relasi ASC, b.nama_barang ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $raw = $stmt->fetchAll();
        
        // Group by relasi_id for easy display
        $grouped = [];
        foreach ($raw as $row) {
            $r_id = $row['relasi_id'];
            if (!isset($grouped[$r_id])) {
                $grouped[$r_id] = [
                    'id' => $r_id,
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
                    r.nama_relasi,
                    r.lokasi,
                    MAX(p.tanggal) as tanggal_terakhir,
                    CASE 
                        WHEN MAX(p.tanggal) IS NULL THEN NULL
                        ELSE DATEDIFF(CURRENT_DATE, MAX(p.tanggal)) 
                    END as hari_sejak_pengiriman,
                    MAX(ev.status_lanjut) as status_lanjut,
                    MAX(ev.catatan) as evaluasi_catatan,
                    MAX(ev.tanggal) as evaluasi_tanggal,
                    (
                        COALESCE((SELECT SUM(stok_awal) FROM relasi_stok_awal WHERE relasi_id = r.id), 0) +
                        COALESCE(SUM(p.jumlah_masuk), 0) - COALESCE(SUM(p.jumlah_keluar), 0)
                    ) as total_tabung_dipinjam
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
                HAVING total_tabung_dipinjam > 0
                ORDER BY hari_sejak_pengiriman DESC, r.nama_relasi ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
