<?php
class PengirimanController {
    public function index() {
        $pengirimanModel = new PengirimanModel();
        
        // Simple pagination
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($page < 1) $page = 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $deliveries = $pengirimanModel->getAll($limit, $offset);
        
        // Total count for basic pagination
        $db = (new Database())->getConnection();
        $total = $db->query("SELECT COUNT(*) FROM pengiriman")->fetchColumn();
        $totalPages = ceil($total / $limit);

        require_once __DIR__ . '/../views/pengiriman/index.php';
    }

    public function export() {
        $pengirimanModel = new PengirimanModel();
        // Fetch all without limit
        $deliveries = $pengirimanModel->getAll(1000000, 0);

        $filename = "Log_Pengiriman_" . date('Y-m-d') . ".xls";

        // Headers for Excel download
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Output HTML table
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta charset="utf-8"></head>';
        echo '<body>';
        echo '<table border="1" cellpadding="5">';
        
        // Header Row with Color
        echo '<tr>';
        $headers = ['Tanggal', 'Nama Relasi', 'Jenis Tabung', 'Kirim (Isi)', 'Kembali (Kosong)', 'Keterangan'];
        foreach ($headers as $head) {
            echo '<th style="background-color: #4f46e5; color: #ffffff; font-weight: bold; text-align: center;">' . $head . '</th>';
        }
        echo '</tr>';

        // Data rows
        foreach ($deliveries as $d) {
            echo '<tr>';
            // mso-number-format:"\@" forces the cell to be treated as Text, preventing the #### date issue
            echo '<td style="mso-number-format:\'\@\';">' . date('d-m-Y', strtotime($d['tanggal'])) . '</td>';
            echo '<td>' . htmlspecialchars($d['nama_relasi']) . '</td>';
            echo '<td>' . htmlspecialchars($d['nama_barang']) . '</td>';
            echo '<td style="text-align: right;">' . $d['jumlah_masuk'] . '</td>';
            echo '<td style="text-align: right;">' . $d['jumlah_keluar'] . '</td>';
            echo '<td>' . htmlspecialchars($d['keterangan']) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        echo '</body></html>';
        exit;
    }

    public function create() {
        $relasiModel = new RelasiModel();
        $barangModel = new BarangModel();
        
        $clients = $relasiModel->getAll();
        $barangList = $barangModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tanggal = $_POST['tanggal'];
            $relasi_id = (int)$_POST['relasi_id'];
            $barang_id = (int)$_POST['barang_id'];
            $jumlah_masuk = (int)$_POST['jumlah_masuk'];
            $jumlah_keluar = (int)$_POST['jumlah_keluar'];
            $keterangan = trim($_POST['keterangan']);

            $pengirimanModel = new PengirimanModel();
            $pengirimanModel = new PengirimanModel();
            
            // Validasi Stok Gudang & Mitra
            $db = (new Database())->getConnection();
            
            if ($jumlah_masuk > 0) {
                $stmt_gudang = $db->prepare("SELECT stok_ready FROM stok_gudang WHERE barang_id = ?");
                $stmt_gudang->execute([$barang_id]);
                $stok_gudang = $stmt_gudang->fetch();
                
                if (!$stok_gudang || $jumlah_masuk > $stok_gudang['stok_ready']) {
                    $error = "Gagal: Jumlah kirim (" . $jumlah_masuk . ") melebihi stok ready di gudang (" . ($stok_gudang ? $stok_gudang['stok_ready'] : 0) . ").";
                }
            }
            
            if (!isset($error) && $jumlah_keluar > 0) {
                $sql_stok = "SELECT (MAX(COALESCE(sa.stok_awal, 0)) + COALESCE(SUM(p.jumlah_masuk), 0) - COALESCE(SUM(p.jumlah_keluar), 0)) as stok_akhir
                             FROM relasi r
                             CROSS JOIN barang b
                             LEFT JOIN relasi_stok_awal sa ON sa.relasi_id = r.id AND sa.barang_id = b.id
                             LEFT JOIN pengiriman p ON p.relasi_id = r.id AND p.barang_id = b.id
                             WHERE r.id = ? AND b.id = ? GROUP BY r.id, b.id";
                $stmt_relasi = $db->prepare($sql_stok);
                $stmt_relasi->execute([$relasi_id, $barang_id]);
                $stok_relasi = $stmt_relasi->fetch();
                $stok_akhir = $stok_relasi ? $stok_relasi['stok_akhir'] : 0;
                
                if ($jumlah_keluar > $stok_akhir) {
                    $error = "Gagal: Jumlah kembali (" . $jumlah_keluar . ") melebihi stok tabung yang sedang dipegang mitra (" . $stok_akhir . ").";
                }
            }

            if (!isset($error)) {
                try {
                    // Perform delivery update
                    $pengirimanModel->create($tanggal, $relasi_id, $barang_id, $jumlah_masuk, $jumlah_keluar, $keterangan);
                    header("Location: index.php?controller=pengiriman&action=index&msg=success_create");
                    exit;
                } catch (Exception $e) {
                    $error = "Gagal mencatat pengiriman: " . $e->getMessage();
                }
            }
        }

        require_once __DIR__ . '/../views/pengiriman/create.php';
    }

    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $pengirimanModel = new PengirimanModel();
        $relasiModel = new RelasiModel();
        $barangModel = new BarangModel();
        
        $pengiriman = $pengirimanModel->getById($id);
        if (!$pengiriman) {
            header("Location: index.php?controller=pengiriman&action=index");
            exit;
        }

        $clients = $relasiModel->getAll();
        $barangList = $barangModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tanggal = $_POST['tanggal'];
            $relasi_id = (int)$_POST['relasi_id'];
            $barang_id = (int)$_POST['barang_id'];
            $jumlah_masuk = (int)$_POST['jumlah_masuk'];
            $jumlah_keluar = (int)$_POST['jumlah_keluar'];
            $keterangan = trim($_POST['keterangan']);

            $db = (new Database())->getConnection();
            
            // Baseline untuk Gudang
            if ($jumlah_masuk > 0) {
                $stmt_gudang = $db->prepare("SELECT stok_ready FROM stok_gudang WHERE barang_id = ?");
                $stmt_gudang->execute([$barang_id]);
                $stok_gudang = $stmt_gudang->fetch();
                
                $baseline_ready = $stok_gudang ? $stok_gudang['stok_ready'] : 0;
                if ($pengiriman['barang_id'] == $barang_id) {
                    $baseline_ready += $pengiriman['jumlah_masuk'];
                }
                
                if ($jumlah_masuk > $baseline_ready) {
                    $error = "Gagal: Jumlah kirim (" . $jumlah_masuk . ") melebihi stok ready di gudang (" . $baseline_ready . ").";
                }
            }
            
            // Baseline untuk Mitra
            if (!isset($error) && $jumlah_keluar > 0) {
                $sql_stok = "SELECT (MAX(COALESCE(sa.stok_awal, 0)) + COALESCE(SUM(p.jumlah_masuk), 0) - COALESCE(SUM(p.jumlah_keluar), 0)) as stok_akhir
                             FROM relasi r
                             CROSS JOIN barang b
                             LEFT JOIN relasi_stok_awal sa ON sa.relasi_id = r.id AND sa.barang_id = b.id
                             LEFT JOIN pengiriman p ON p.relasi_id = r.id AND p.barang_id = b.id
                             WHERE r.id = ? AND b.id = ? GROUP BY r.id, b.id";
                $stmt_relasi = $db->prepare($sql_stok);
                $stmt_relasi->execute([$relasi_id, $barang_id]);
                $stok_relasi = $stmt_relasi->fetch();
                
                $current_akhir = $stok_relasi ? $stok_relasi['stok_akhir'] : 0;
                $baseline_akhir = $current_akhir;
                
                if ($pengiriman['relasi_id'] == $relasi_id && $pengiriman['barang_id'] == $barang_id) {
                    $baseline_akhir = $baseline_akhir - $pengiriman['jumlah_masuk'] + $pengiriman['jumlah_keluar'];
                }
                
                if ($jumlah_keluar > $baseline_akhir) {
                    $error = "Gagal: Jumlah kembali (" . $jumlah_keluar . ") melebihi stok tabung yang dipegang mitra (" . $baseline_akhir . ").";
                }
            }

            if (!isset($error)) {
                try {
                    $pengirimanModel->update($id, $tanggal, $relasi_id, $barang_id, $jumlah_masuk, $jumlah_keluar, $keterangan);
                    header("Location: index.php?controller=pengiriman&action=index&msg=success_update");
                    exit;
                } catch (Exception $e) {
                    $error = "Gagal memperbarui pengiriman: " . $e->getMessage();
                }
            }
        }

        require_once __DIR__ . '/../views/pengiriman/edit.php';
    }

    public function delete() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $pengirimanModel = new PengirimanModel();
        
        if ($id > 0) {
            try {
                $pengirimanModel->delete($id);
                header("Location: index.php?controller=pengiriman&action=index&msg=success_delete");
                exit;
            } catch (Exception $e) {
                header("Location: index.php?controller=pengiriman&action=index&msg=error_delete");
                exit;
            }
        }
        header("Location: index.php?controller=pengiriman&action=index");
        exit;
    }
}
