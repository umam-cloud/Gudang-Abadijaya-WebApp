<?php
class GudangController {
    public function index() {
        $gudangModel = new GudangModel();
        $barangModel = new BarangModel();
        
        $warehouseStocks = $gudangModel->getWarehouseStock();
        $barangList = $barangModel->getAll();
        
        // Simple pagination for warehouse transactions
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($page < 1) $page = 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $transactions = $gudangModel->getTransactions($limit, $offset);
        
        $db = (new Database())->getConnection();
        $total = $db->query("SELECT COUNT(*) FROM gudang_transaksi")->fetchColumn();
        $totalPages = ceil($total / $limit);

        require_once __DIR__ . '/../views/gudang/index.php';
    }

    public function export_stok() {
        $gudangModel = new GudangModel();
        $warehouseStocks = $gudangModel->getWarehouseStock();

        $filename = "Stok_Gudang_" . date('Y-m-d') . ".xls";

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head><meta charset="utf-8"></head><body>';
        echo '<table border="1" cellpadding="5">';
        
        echo '<tr>';
        $headers = ['Nama Tabung', 'Deskripsi', 'Stok Ready (Isi)', 'Stok Kosong'];
        foreach ($headers as $head) {
            echo '<th style="background-color: #0d9488; color: #ffffff; font-weight: bold; text-align: center;">' . $head . '</th>';
        }
        echo '</tr>';

        foreach ($warehouseStocks as $s) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($s['nama_barang']) . '</td>';
            echo '<td>' . htmlspecialchars($s['deskripsi']) . '</td>';
            echo '<td style="text-align: right; font-weight: bold;">' . $s['stok_ready'] . '</td>';
            echo '<td style="text-align: right; font-weight: bold;">' . $s['stok_kosong'] . '</td>';
            echo '</tr>';
        }
        
        echo '</table></body></html>';
        exit;
    }

    public function export_transaksi() {
        $gudangModel = new GudangModel();
        // Fetch all without limit
        $transactions = $gudangModel->getTransactions(1000000, 0);

        $filename = "Jurnal_Transaksi_Gudang_" . date('Y-m-d') . ".xls";

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head><meta charset="utf-8"></head><body>';
        echo '<table border="1" cellpadding="5">';
        
        echo '<tr>';
        $headers = ['Tanggal', 'Jenis Tabung', 'Tipe Transaksi', 'Jumlah', 'Keterangan'];
        foreach ($headers as $head) {
            echo '<th style="background-color: #f59e0b; color: #ffffff; font-weight: bold; text-align: center;">' . $head . '</th>';
        }
        echo '</tr>';

        foreach ($transactions as $t) {
            $tipe = '';
            if ($t['tipe_transaksi'] === 'refill') $tipe = 'Refill';
            elseif ($t['tipe_transaksi'] === 'beli_baru') $tipe = 'Pembelian';
            elseif ($t['tipe_transaksi'] === 'rusak') $tipe = 'Penyusutan (Rusak/Hilang)';
            else $tipe = htmlspecialchars($t['tipe_transaksi']);

            echo '<tr>';
            echo '<td style="mso-number-format:\'\@\';">' . date('d-m-Y', strtotime($t['tanggal'])) . '</td>';
            echo '<td>' . htmlspecialchars($t['nama_barang']) . '</td>';
            echo '<td>' . $tipe . '</td>';
            echo '<td style="text-align: right;">' . $t['jumlah'] . '</td>';
            echo '<td>' . htmlspecialchars($t['keterangan']) . '</td>';
            echo '</tr>';
        }
        
        echo '</table></body></html>';
        exit;
    }

    public function adjust() {
        $barangModel = new BarangModel();
        $barangList = $barangModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tanggal = $_POST['tanggal'];
            $barang_id = (int)$_POST['barang_id'];
            $tipe_transaksi = $_POST['tipe_transaksi'];
            $jumlah = (int)$_POST['jumlah'];
            $target_stok = isset($_POST['target_stok']) ? $_POST['target_stok'] : 'ready';
            $keterangan = trim($_POST['keterangan']);

            $gudangModel = new GudangModel();
            
            // Validasi Refill
            if ($tipe_transaksi === 'refill') {
                $db = (new Database())->getConnection();
                $stmt = $db->prepare("SELECT stok_kosong FROM stok_gudang WHERE barang_id = ?");
                $stmt->execute([$barang_id]);
                $stok = $stmt->fetch();
                
                if (!$stok || $jumlah > $stok['stok_kosong']) {
                    $error = "Gagal: Jumlah refill (" . $jumlah . ") melebihi stok tabung kosong di gudang (" . ($stok ? $stok['stok_kosong'] : 0) . ").";
                }
            }
            
            if (!isset($error)) {
                try {
                    $gudangModel->addAdjustment($tanggal, $barang_id, $tipe_transaksi, $jumlah, $target_stok, $keterangan);
                    header("Location: " . BASE_URL . "gudang?msg=success_adjust");
                    exit;
                } catch (Exception $e) {
                    $error = "Gagal mencatat penyesuaian: " . $e->getMessage();
                }
            }
        }

        require_once __DIR__ . '/../views/gudang/adjust.php';
    }

    public function create_cylinder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_barang = trim($_POST['nama_barang']);
            $deskripsi = trim($_POST['deskripsi']);

            $barangModel = new BarangModel();
            try {
                $barangModel->create($nama_barang, $deskripsi);
                header("Location: " . BASE_URL . "gudang?tab=cylinders&msg=success_cylinder_create");
                exit;
            } catch (Exception $e) {
                header("Location: " . BASE_URL . "gudang?tab=cylinders&msg=error_cylinder_exists");
                exit;
            }
        }
    }

    public function edit_cylinder() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $barangModel = new BarangModel();
        $barang = $barangModel->getById($id);
        
        if (!$barang) {
            header("Location: " . BASE_URL . "gudang?tab=cylinders");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_barang = trim($_POST['nama_barang']);
            $deskripsi = trim($_POST['deskripsi']);

            try {
                $barangModel->update($id, $nama_barang, $deskripsi);
                header("Location: " . BASE_URL . "gudang?tab=cylinders&msg=success_cylinder_update");
                exit;
            } catch (Exception $e) {
                $error = "Gagal memperbarui jenis tabung: " . $e->getMessage();
            }
        }

        require_once __DIR__ . '/../views/gudang/edit_cylinder.php';
    }

    public function delete_cylinder() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $barangModel = new BarangModel();
        
        if ($id > 0) {
            $barangModel->delete($id);
        }
        header("Location: " . BASE_URL . "gudang?tab=cylinders&msg=success_cylinder_delete");
        exit;
    }
}
