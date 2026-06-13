<?php
class RelasiController {
    public function index() {
        $relasiModel = new RelasiModel();
        $barangModel = new BarangModel();
        
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($page < 1) $page = 1;
        $limit = 30;
        $offset = ($page - 1) * $limit;

        $clients = $relasiModel->getAllWithStocks($limit, $offset, $search);
        $barangList = $barangModel->getAll();
        
        $total = $relasiModel->countAllRelasi($search);
        $totalPages = ceil($total / $limit);
        
        require_once __DIR__ . '/../views/relasi/index.php';
    }

    public function create() {
        $barangModel = new BarangModel();
        $barangList = $barangModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_relasi = trim($_POST['nama_relasi']);
            $lokasi = trim($_POST['lokasi']);
            
            // Collect starting stocks
            $stok_awal = [];
            foreach ($barangList as $b) {
                $b_id = $b['id'];
                $stok_awal[$b_id] = isset($_POST['stok_awal_' . $b_id]) ? $_POST['stok_awal_' . $b_id] : 0;
            }

            $relasiModel = new RelasiModel();
            try {
                $relasiModel->create($nama_relasi, $lokasi, $stok_awal);
                header("Location: " . BASE_URL . "relasi?msg=success_create");
                exit;
            } catch (Exception $e) {
                $error = "Gagal membuat relasi: " . $e->getMessage();
            }
        }

        require_once __DIR__ . '/../views/relasi/create.php';
    }

    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $relasiModel = new RelasiModel();
        $barangModel = new BarangModel();
        
        $relasi = $relasiModel->getById($id);
        if (!$relasi) {
            header("Location: " . BASE_URL . "relasi");
            exit;
        }

        $barangList = $barangModel->getAll();
        $stokAwal = $relasiModel->getStokAwal($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_relasi = trim($_POST['nama_relasi']);
            $lokasi = trim($_POST['lokasi']);
            
            // Collect starting stocks
            $stok_awal = [];
            foreach ($barangList as $b) {
                $b_id = $b['id'];
                $stok_awal[$b_id] = isset($_POST['stok_awal_' . $b_id]) ? $_POST['stok_awal_' . $b_id] : 0;
            }

            try {
                $relasiModel->update($id, $nama_relasi, $lokasi, $stok_awal);
                header("Location: " . BASE_URL . "relasi?msg=success_update");
                exit;
            } catch (Exception $e) {
                $error = "Gagal memperbarui relasi: " . $e->getMessage();
            }
        }

        require_once __DIR__ . '/../views/relasi/edit.php';
    }

    public function delete() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $relasiModel = new RelasiModel();
        
        if ($id > 0) {
            $relasiModel->delete($id);
        }
        header("Location: " . BASE_URL . "relasi?msg=success_delete");
        exit;
    }

    public function detail() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $relasiModel = new RelasiModel();
        $barangModel = new BarangModel();
        $evaluasiModel = new EvaluasiModel();
        
        $relasi = $relasiModel->getById($id);
        if (!$relasi) {
            header("Location: " . BASE_URL . "relasi");
            exit;
        }

        $barangList = $barangModel->getAll();
        $stokAwal = $relasiModel->getStokAwal($id);
        $evalHistory = $evaluasiModel->getHistoryByRelasi($id);
        
        // Calculate current stock and delivery history for this client
        $db = (new Database())->getConnection();
        
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($page < 1) $page = 1;
        $limit = 30;
        $offset = ($page - 1) * $limit;

        // Delivery logs for this client
        $stmt_deliv = $db->prepare(
            "SELECT p.*, b.nama_barang 
             FROM pengiriman p
             JOIN barang b ON p.barang_id = b.id
             WHERE p.relasi_id = ?
             ORDER BY p.tanggal DESC, p.id DESC
             LIMIT ? OFFSET ?"
        );
        $stmt_deliv->bindValue(1, $id, PDO::PARAM_INT);
        $stmt_deliv->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt_deliv->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt_deliv->execute();
        $deliveries = $stmt_deliv->fetchAll();

        // Total count for deliveries pagination
        $stmt_total_deliv = $db->prepare("SELECT COUNT(*) FROM pengiriman WHERE relasi_id = ?");
        $stmt_total_deliv->execute([$id]);
        $total_deliv = $stmt_total_deliv->fetchColumn();
        $totalPages = ceil($total_deliv / $limit);
        
        // Sums of delivered & returned cylinders for calculations
        $stmt_sums = $db->prepare(
            "SELECT barang_id, SUM(jumlah_masuk) as total_masuk, SUM(jumlah_keluar) as total_keluar 
             FROM pengiriman 
             WHERE relasi_id = ? 
             GROUP BY barang_id"
        );
        $stmt_sums->execute([$id]);
        $sums_raw = $stmt_sums->fetchAll();
        
        $sums = [];
        foreach ($sums_raw as $s) {
            $sums[$s['barang_id']] = [
                'masuk' => $s['total_masuk'],
                'keluar' => $s['total_keluar']
            ];
        }

        $total_tabung_dipinjam = 0;
        foreach ($barangList as $b) {
            $init = isset($stokAwal[$b['id']]) ? $stokAwal[$b['id']] : 0;
            $masuk = isset($sums[$b['id']]) ? $sums[$b['id']]['masuk'] : 0;
            $keluar = isset($sums[$b['id']]) ? $sums[$b['id']]['keluar'] : 0;
            $total_tabung_dipinjam += ($init + $masuk - $keluar);
        }

        // Get last delivery info
        $stmt_last = $db->prepare(
            "SELECT MAX(tanggal) as tanggal_terakhir, 
                    DATEDIFF(CURRENT_DATE, MAX(tanggal)) as hari_sejak_pengiriman
             FROM pengiriman 
             WHERE relasi_id = ?"
        );
        $stmt_last->execute([$id]);
        $last_delivery = $stmt_last->fetch();

        require_once __DIR__ . '/../views/relasi/detail.php';
    }
}
