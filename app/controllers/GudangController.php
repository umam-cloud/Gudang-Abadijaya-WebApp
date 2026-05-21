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
            try {
                $gudangModel->addAdjustment($tanggal, $barang_id, $tipe_transaksi, $jumlah, $target_stok, $keterangan);
                header("Location: index.php?controller=gudang&action=index&msg=success_adjust");
                exit;
            } catch (Exception $e) {
                $error = "Gagal mencatat penyesuaian: " . $e->getMessage();
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
                header("Location: index.php?controller=gudang&action=index&tab=cylinders&msg=success_cylinder_create");
                exit;
            } catch (Exception $e) {
                header("Location: index.php?controller=gudang&action=index&tab=cylinders&msg=error_cylinder_exists");
                exit;
            }
        }
    }

    public function edit_cylinder() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $barangModel = new BarangModel();
        $barang = $barangModel->getById($id);
        
        if (!$barang) {
            header("Location: index.php?controller=gudang&action=index&tab=cylinders");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_barang = trim($_POST['nama_barang']);
            $deskripsi = trim($_POST['deskripsi']);

            try {
                $barangModel->update($id, $nama_barang, $deskripsi);
                header("Location: index.php?controller=gudang&action=index&tab=cylinders&msg=success_cylinder_update");
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
        header("Location: index.php?controller=gudang&action=index&tab=cylinders&msg=success_cylinder_delete");
        exit;
    }
}
