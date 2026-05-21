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
            try {
                // Perform delivery update
                $pengirimanModel->create($tanggal, $relasi_id, $barang_id, $jumlah_masuk, $jumlah_keluar, $keterangan);
                header("Location: index.php?controller=pengiriman&action=index&msg=success_create");
                exit;
            } catch (Exception $e) {
                $error = "Gagal mencatat pengiriman: " . $e->getMessage();
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

            try {
                $pengirimanModel->update($id, $tanggal, $relasi_id, $barang_id, $jumlah_masuk, $jumlah_keluar, $keterangan);
                header("Location: index.php?controller=pengiriman&action=index&msg=success_update");
                exit;
            } catch (Exception $e) {
                $error = "Gagal memperbarui pengiriman: " . $e->getMessage();
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
