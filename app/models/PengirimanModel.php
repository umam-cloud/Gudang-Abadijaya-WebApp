<?php
require_once __DIR__ . '/../config/Database.php';

class PengirimanModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getAll($limit = 100, $offset = 0, $filters = []) {
        $whereClause = "";
        $params = [];
        
        if (!empty($filters['no_surat_jalan'])) {
            $whereClause .= " AND p.no_surat_jalan = ?";
            $params[] = $filters['no_surat_jalan'];
        }
        if (!empty($filters['tanggal'])) {
            $whereClause .= " AND p.tanggal = ?";
            $params[] = $filters['tanggal'];
        }
        if (!empty($filters['relasi_id'])) {
            $whereClause .= " AND p.relasi_id = ?";
            $params[] = $filters['relasi_id'];
        }

        if ($whereClause !== "") {
            $whereClause = " WHERE 1=1" . $whereClause;
        }

        $sql = "SELECT p.*, r.nama_relasi, b.nama_barang 
                FROM pengiriman p
                JOIN relasi r ON p.relasi_id = r.id
                JOIN barang b ON p.barang_id = b.id
                $whereClause
                ORDER BY p.tanggal DESC, p.id DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $paramIdx = 1;
        foreach ($params as $param) {
            $stmt->bindValue($paramIdx++, $param);
        }
        $stmt->bindValue($paramIdx++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($paramIdx, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll($filters = []) {
        $whereClause = "";
        $params = [];
        
        if (!empty($filters['no_surat_jalan'])) {
            $whereClause .= " AND p.no_surat_jalan = ?";
            $params[] = $filters['no_surat_jalan'];
        }
        if (!empty($filters['tanggal'])) {
            $whereClause .= " AND p.tanggal = ?";
            $params[] = $filters['tanggal'];
        }
        if (!empty($filters['relasi_id'])) {
            $whereClause .= " AND p.relasi_id = ?";
            $params[] = $filters['relasi_id'];
        }

        if ($whereClause !== "") {
            $whereClause = " WHERE 1=1" . $whereClause;
        }
        
        $sql = "SELECT COUNT(*) FROM pengiriman p JOIN relasi r ON p.relasi_id = r.id $whereClause";
        $stmt = $this->db->prepare($sql);
        $paramIdx = 1;
        foreach ($params as $param) {
            $stmt->bindValue($paramIdx++, $param);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM pengiriman WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function checkSuratJalanExists($no_surat_jalan) {
        if (empty($no_surat_jalan)) return false;
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM pengiriman WHERE no_surat_jalan = ?");
        $stmt->execute([$no_surat_jalan]);
        return $stmt->fetchColumn() > 0;
    }

    public function getAllSuratJalan() {
        $stmt = $this->db->query("SELECT DISTINCT no_surat_jalan FROM pengiriman WHERE no_surat_jalan IS NOT NULL AND no_surat_jalan != '' ORDER BY no_surat_jalan ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function create($tanggal, $no_surat_jalan, $relasi_id, $barang_id, $jumlah_masuk, $kondisi_kirim, $jumlah_keluar, $kondisi_kembali, $keterangan = '') {
        $inTransaction = $this->db->inTransaction();
        try {
            if (!$inTransaction) {
                $this->db->beginTransaction();
            }
            
            // Insert pengiriman
            $stmt = $this->db->prepare(
                "INSERT INTO pengiriman (tanggal, no_surat_jalan, relasi_id, barang_id, jumlah_masuk, kondisi_kirim, jumlah_keluar, kondisi_kembali, keterangan) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$tanggal, $no_surat_jalan, $relasi_id, $barang_id, $jumlah_masuk, $kondisi_kirim, $jumlah_keluar, $kondisi_kembali, $keterangan]);
            
            $delta_ready = 0;
            $delta_kosong = 0;

            if ($kondisi_kirim == 'Isi') {
                $delta_ready -= $jumlah_masuk;
            } else if ($kondisi_kirim == 'Kosong') {
                $delta_kosong -= $jumlah_masuk;
            }

            if ($kondisi_kembali == 'Kosong') {
                $delta_kosong += $jumlah_keluar;
            } else if ($kondisi_kembali == 'Isi') {
                $delta_ready += $jumlah_keluar;
            }

            $stmt_stock = $this->db->prepare(
                "UPDATE stok_gudang 
                 SET stok_ready = stok_ready + ?, 
                     stok_kosong = stok_kosong + ? 
                 WHERE barang_id = ?"
            );
            $stmt_stock->execute([$delta_ready, $delta_kosong, $barang_id]);
            
            if (!$inTransaction) {
                $this->db->commit();
            }
            return true;
        } catch (Exception $e) {
            if (!$inTransaction) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function update($id, $tanggal, $no_surat_jalan, $relasi_id, $barang_id, $jumlah_masuk, $kondisi_kirim, $jumlah_keluar, $kondisi_kembali, $keterangan = '') {
        try {
            $this->db->beginTransaction();
            
            // Get original record to calculate differences
            $stmt_orig = $this->db->prepare("SELECT barang_id, jumlah_masuk, kondisi_kirim, jumlah_keluar, kondisi_kembali FROM pengiriman WHERE id = ?");
            $stmt_orig->execute([$id]);
            $orig = $stmt_orig->fetch();
            
            if (!$orig) {
                throw new Exception("Transaction not found.");
            }
            
            // Helper closure to calc deltas
            $calcDeltas = function($jm, $kkirim, $jk, $kkembali) {
                $d_r = 0; $d_k = 0;
                if ($kkirim == 'Isi') $d_r -= $jm;
                elseif ($kkirim == 'Kosong') $d_k -= $jm;
                if ($kkembali == 'Kosong') $d_k += $jk;
                elseif ($kkembali == 'Isi') $d_r += $jk;
                return [$d_r, $d_k];
            };

            // If the cylinder type has changed, we revert stock on the old one, and apply stock on the new one
            if ($orig['barang_id'] == $barang_id) {
                // Same cylinder type
                $old_deltas = $calcDeltas($orig['jumlah_masuk'], $orig['kondisi_kirim'], $orig['jumlah_keluar'], $orig['kondisi_kembali']);
                $new_deltas = $calcDeltas($jumlah_masuk, $kondisi_kirim, $jumlah_keluar, $kondisi_kembali);
                
                $diff_ready = $new_deltas[0] - $old_deltas[0];
                $diff_kosong = $new_deltas[1] - $old_deltas[1];
                
                $stmt_stock = $this->db->prepare(
                    "UPDATE stok_gudang 
                     SET stok_ready = stok_ready + ?, 
                         stok_kosong = stok_kosong + ? 
                     WHERE barang_id = ?"
                );
                $stmt_stock->execute([$diff_ready, $diff_kosong, $barang_id]);
            } else {
                // Revert old cylinder stock
                $old_deltas = $calcDeltas($orig['jumlah_masuk'], $orig['kondisi_kirim'], $orig['jumlah_keluar'], $orig['kondisi_kembali']);
                $stmt_stock_old = $this->db->prepare(
                    "UPDATE stok_gudang 
                     SET stok_ready = stok_ready + ?, 
                         stok_kosong = stok_kosong + ? 
                     WHERE barang_id = ?"
                );
                $stmt_stock_old->execute([-$old_deltas[0], -$old_deltas[1], $orig['barang_id']]);
                
                // Apply new cylinder stock
                $new_deltas = $calcDeltas($jumlah_masuk, $kondisi_kirim, $jumlah_keluar, $kondisi_kembali);
                $stmt_stock_new = $this->db->prepare(
                    "UPDATE stok_gudang 
                     SET stok_ready = stok_ready + ?, 
                         stok_kosong = stok_kosong + ? 
                     WHERE barang_id = ?"
                );
                $stmt_stock_new->execute([$new_deltas[0], $new_deltas[1], $barang_id]);
            }
            
            // Update pengiriman details
            $stmt = $this->db->prepare(
                "UPDATE pengiriman 
                 SET tanggal = ?, no_surat_jalan = ?, relasi_id = ?, barang_id = ?, jumlah_masuk = ?, kondisi_kirim = ?, jumlah_keluar = ?, kondisi_kembali = ?, keterangan = ? 
                 WHERE id = ?"
            );
            $stmt->execute([$tanggal, $no_surat_jalan, $relasi_id, $barang_id, $jumlah_masuk, $kondisi_kirim, $jumlah_keluar, $kondisi_kembali, $keterangan, $id]);
            
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
            $stmt_orig = $this->db->prepare("SELECT barang_id, jumlah_masuk, kondisi_kirim, jumlah_keluar, kondisi_kembali FROM pengiriman WHERE id = ?");
            $stmt_orig->execute([$id]);
            $orig = $stmt_orig->fetch();
            
            if (!$orig) {
                throw new Exception("Transaction not found.");
            }
            
            // Reverse stock: Gain what was sent, lose what was received
            $d_r = 0; $d_k = 0;
            if ($orig['kondisi_kirim'] == 'Isi') $d_r += $orig['jumlah_masuk'];
            elseif ($orig['kondisi_kirim'] == 'Kosong') $d_k += $orig['jumlah_masuk'];
            if ($orig['kondisi_kembali'] == 'Kosong') $d_k -= $orig['jumlah_keluar'];
            elseif ($orig['kondisi_kembali'] == 'Isi') $d_r -= $orig['jumlah_keluar'];

            $stmt_stock = $this->db->prepare(
                "UPDATE stok_gudang 
                 SET stok_ready = stok_ready + ?, 
                     stok_kosong = stok_kosong + ? 
                 WHERE barang_id = ?"
            );
            $stmt_stock->execute([$d_r, $d_k, $orig['barang_id']]);
            
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
