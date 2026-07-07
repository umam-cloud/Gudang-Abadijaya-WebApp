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

    public function getWarehouseStockAtDate($date) {
        // 1. Get current stock
        $currentStocks = $this->getWarehouseStock();
        $stockMap = [];
        foreach ($currentStocks as $s) {
            $stockMap[$s['barang_id']] = [
                'nama_barang' => $s['nama_barang'],
                'deskripsi' => $s['deskripsi'],
                'stok_ready' => (int)$s['stok_ready'],
                'stok_kosong' => (int)$s['stok_kosong']
            ];
        }

        // 2. Reverse 'pengiriman' that occurred AFTER the date
        $stmt_pengiriman = $this->db->prepare("SELECT barang_id, jumlah_masuk, kondisi_kirim, jumlah_keluar, kondisi_kembali FROM pengiriman WHERE tanggal > ?");
        $stmt_pengiriman->execute([$date]);
        $pengirimans = $stmt_pengiriman->fetchAll();

        foreach ($pengirimans as $p) {
            $b_id = $p['barang_id'];
            if (!isset($stockMap[$b_id])) continue;

            // Reverse Kirim
            if ($p['kondisi_kirim'] == 'Isi') $stockMap[$b_id]['stok_ready'] += $p['jumlah_masuk'];
            elseif ($p['kondisi_kirim'] == 'Kosong') $stockMap[$b_id]['stok_kosong'] += $p['jumlah_masuk'];

            // Reverse Kembali
            if ($p['kondisi_kembali'] == 'Isi') $stockMap[$b_id]['stok_ready'] -= $p['jumlah_keluar'];
            elseif ($p['kondisi_kembali'] == 'Kosong') $stockMap[$b_id]['stok_kosong'] -= $p['jumlah_keluar'];
        }

        // 3. Reverse 'gudang_transaksi' that occurred AFTER the date
        // Since we don't know the exact target_stok for some, we make best-effort assumptions based on standard use
        $stmt_gt = $this->db->prepare("SELECT barang_id, tipe_transaksi, jumlah, keterangan FROM gudang_transaksi WHERE tanggal > ?");
        $stmt_gt->execute([$date]);
        $gts = $stmt_gt->fetchAll();

        foreach ($gts as $gt) {
            $b_id = $gt['barang_id'];
            if (!isset($stockMap[$b_id])) continue;

            $qty = (int)$gt['jumlah'];
            if ($gt['tipe_transaksi'] == 'refill') {
                $stockMap[$b_id]['stok_ready'] -= $qty;
                $stockMap[$b_id]['stok_kosong'] += $qty;
            } elseif ($gt['tipe_transaksi'] == 'beli_baru') {
                // assume ready
                $stockMap[$b_id]['stok_ready'] -= $qty;
            } elseif ($gt['tipe_transaksi'] == 'jual_rusak') {
                // assume ready
                $stockMap[$b_id]['stok_ready'] += $qty;
            } elseif ($gt['tipe_transaksi'] == 'koreksi') {
                // assume ready unless it's a transfer from kosong
                if (stripos($gt['keterangan'], 'kosong') !== false) {
                    $stockMap[$b_id]['stok_kosong'] -= $qty;
                } else {
                    $stockMap[$b_id]['stok_ready'] -= $qty;
                }
            }
        }

        return array_values($stockMap);
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

    public function transferStock($tanggal, $barang_asal_id, $kondisi_asal, $barang_tujuan_id, $kondisi_tujuan, $jumlah, $keterangan) {
        try {
            $this->db->beginTransaction();
            
            // Check stock availability
            $stmt_check = $this->db->prepare("SELECT stok_ready, stok_kosong FROM stok_gudang WHERE barang_id = ?");
            $stmt_check->execute([$barang_asal_id]);
            $stok_asal = $stmt_check->fetch();
            
            if (!$stok_asal) {
                throw new Exception("Barang asal tidak ditemukan di gudang.");
            }
            
            if ($kondisi_asal === 'ready' && $stok_asal['stok_ready'] < $jumlah) {
                throw new Exception("Stok ready barang asal tidak mencukupi untuk transfer.");
            }
            if ($kondisi_asal === 'kosong' && $stok_asal['stok_kosong'] < $jumlah) {
                throw new Exception("Stok kosong barang asal tidak mencukupi untuk transfer.");
            }

            // 1. Deduct from origin
            $col_asal = $kondisi_asal === 'ready' ? 'stok_ready' : 'stok_kosong';
            $stmt_kurang = $this->db->prepare("UPDATE stok_gudang SET {$col_asal} = {$col_asal} - ? WHERE barang_id = ?");
            $stmt_kurang->execute([$jumlah, $barang_asal_id]);
            
            // 2. Add to destination
            $col_tujuan = $kondisi_tujuan === 'ready' ? 'stok_ready' : 'stok_kosong';
            $stmt_tambah = $this->db->prepare("UPDATE stok_gudang SET {$col_tujuan} = {$col_tujuan} + ? WHERE barang_id = ?");
            $stmt_tambah->execute([$jumlah, $barang_tujuan_id]);
            
            // 3. Log transaction
            // Log as 'koreksi' (transfer_out) for origin
            $stmt_log_out = $this->db->prepare(
                "INSERT INTO gudang_transaksi (tanggal, barang_id, tipe_transaksi, jumlah, keterangan) 
                 VALUES (?, ?, 'koreksi', ?, ?)"
            );
            $ket_out = "Transfer Keluar: " . $keterangan;
            $stmt_log_out->execute([$tanggal, $barang_asal_id, -$jumlah, $ket_out]);
            
            // Log as 'koreksi' (transfer_in) for destination
            if ($barang_asal_id != $barang_tujuan_id || $kondisi_asal != $kondisi_tujuan) {
                $stmt_log_in = $this->db->prepare(
                    "INSERT INTO gudang_transaksi (tanggal, barang_id, tipe_transaksi, jumlah, keterangan) 
                     VALUES (?, ?, 'koreksi', ?, ?)"
                );
                $ket_in = "Transfer Masuk: " . $keterangan;
                $stmt_log_in->execute([$tanggal, $barang_tujuan_id, $jumlah, $ket_in]);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
