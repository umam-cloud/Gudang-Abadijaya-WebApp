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

    public function export() {
        $relasiModel = new RelasiModel();
        $barangModel = new BarangModel();
        
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        // Fetch all without limit
        $clients = $relasiModel->getAllWithStocks(1000000, 0, $search);
        $barangList = $barangModel->getAll();

        $filename = "Stok_Relasi_" . date('Y-m-d') . ".xls";

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head><meta charset="utf-8"></head><body>';
        echo '<table border="1" cellpadding="5">';
        
        echo '<tr>';
        echo '<th style="background-color: #6366f1; color: #ffffff; font-weight: bold; text-align: center;" rowspan="2">Nama Relasi / Mitra</th>';
        echo '<th style="background-color: #6366f1; color: #ffffff; font-weight: bold; text-align: center;" rowspan="2">Lokasi / Alamat</th>';
        if (!empty($barangList)) {
            echo '<th style="background-color: #4f46e5; color: #ffffff; font-weight: bold; text-align: center;" colspan="' . count($barangList) . '">Stok Tabung (Di Mitra)</th>';
        }
        echo '<th style="background-color: #6366f1; color: #ffffff; font-weight: bold; text-align: center;" rowspan="2">Total Semua Tabung</th>';
        echo '</tr>';
        
        echo '<tr>';
        foreach ($barangList as $b) {
            echo '<th style="background-color: #818cf8; color: #ffffff; font-weight: bold; text-align: center;">' . htmlspecialchars($b['nama_barang']) . '</th>';
        }
        echo '</tr>';

        foreach ($clients as $c) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($c['nama_relasi']) . '</td>';
            echo '<td>' . htmlspecialchars($c['lokasi']) . '</td>';
            
            $total_semua = 0;
            foreach ($barangList as $b) {
                $b_id = $b['id'];
                $stok_awal = isset($c['stok_awal'][$b_id]) ? $c['stok_awal'][$b_id] : 0;
                $masuk = isset($c['masuk'][$b_id]) ? $c['masuk'][$b_id] : 0;
                $keluar = isset($c['keluar'][$b_id]) ? $c['keluar'][$b_id] : 0;
                $sisa = $stok_awal + $masuk - $keluar;
                $total_semua += $sisa;
                
                echo '<td style="text-align: right;">' . $sisa . '</td>';
            }
            
            echo '<td style="text-align: right; font-weight: bold;">' . $total_semua . '</td>';
            echo '</tr>';
        }
        
        echo '</table></body></html>';
        exit;
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

    public function export_detail() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header("Location: " . BASE_URL . "relasi");
            exit;
        }

        $relasiModel = new RelasiModel();
        $barangModel = new BarangModel();
        
        $relasi = $relasiModel->getById($id);
        if (!$relasi) {
            header("Location: " . BASE_URL . "relasi");
            exit;
        }

        $barangList = $barangModel->getAll();
        $stokAwal = $relasiModel->getStokAwal($id);
        
        $db = (new Database())->getConnection();
        
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

        // All deliveries for this client (no pagination for export)
        $stmt_deliv = $db->prepare(
            "SELECT p.*, b.nama_barang 
             FROM pengiriman p
             JOIN barang b ON p.barang_id = b.id
             WHERE p.relasi_id = ?
             ORDER BY p.tanggal DESC, p.id DESC"
        );
        $stmt_deliv->execute([$id]);
        $deliveries = $stmt_deliv->fetchAll();

        // Get last delivery info
        $stmt_last = $db->prepare(
            "SELECT MAX(tanggal) as tanggal_terakhir
             FROM pengiriman 
             WHERE relasi_id = ?"
        );
        $stmt_last->execute([$id]);
        $last_delivery = $stmt_last->fetch();
        $tgl_terakhir = $last_delivery['tanggal_terakhir'] ? date('d-m-Y', strtotime($last_delivery['tanggal_terakhir'])) : 'Belum pernah';

        $filename = "Laporan_Stok_Mitra_" . preg_replace('/[^A-Za-z0-9_\-]/', '_', $relasi['nama_relasi']) . "_" . date('Y-m-d') . ".xls";

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head><meta charset="utf-8"></head><body>';
        
        // 1. Header Info
        echo '<h2>Detail Mitra & Saldo Tabung</h2>';
        echo '<table border="0" cellpadding="3">';
        echo '<tr><td><strong>Nama Mitra/Relasi</strong></td><td>: ' . htmlspecialchars($relasi['nama_relasi']) . '</td></tr>';
        echo '<tr><td><strong>Lokasi/Alamat</strong></td><td>: ' . htmlspecialchars($relasi['lokasi']) . '</td></tr>';
        echo '<tr><td><strong>Pengiriman Terakhir</strong></td><td>: ' . $tgl_terakhir . '</td></tr>';
        echo '</table><br>';

        // 2. Audit Tabung
        echo '<h3>Audit Saldo Tabung</h3>';
        echo '<table border="1" cellpadding="5">';
        echo '<tr>';
        echo '<th style="background-color: #6366f1; color: #ffffff;">Jenis Tabung</th>';
        echo '<th style="background-color: #6366f1; color: #ffffff;">Stok Awal</th>';
        echo '<th style="background-color: #6366f1; color: #ffffff;">Kirim (Isi)</th>';
        echo '<th style="background-color: #6366f1; color: #ffffff;">Kembali (Kosong)</th>';
        echo '<th style="background-color: #4f46e5; color: #ffffff;">Stok Akhir</th>';
        echo '</tr>';

        foreach ($barangList as $b) {
            $init = isset($stokAwal[$b['id']]) ? $stokAwal[$b['id']] : 0;
            $masuk = isset($sums[$b['id']]) ? $sums[$b['id']]['masuk'] : 0;
            $keluar = isset($sums[$b['id']]) ? $sums[$b['id']]['keluar'] : 0;
            $akhir = $init + $masuk - $keluar;
            
            echo '<tr>';
            echo '<td>' . htmlspecialchars($b['nama_barang']) . '</td>';
            echo '<td style="text-align: right;">' . $init . '</td>';
            echo '<td style="text-align: right; color: green;">+' . $masuk . '</td>';
            echo '<td style="text-align: right; color: orange;">-' . $keluar . '</td>';
            echo '<td style="text-align: right; font-weight: bold;">' . $akhir . '</td>';
            echo '</tr>';
        }
        echo '</table><br><br>';

        // 3. Riwayat Transaksi
        echo '<h3>Riwayat Pengiriman</h3>';
        echo '<table border="1" cellpadding="5">';
        echo '<tr>';
        echo '<th style="background-color: #818cf8; color: #ffffff;">Tanggal</th>';
        echo '<th style="background-color: #818cf8; color: #ffffff;">Barang</th>';
        echo '<th style="background-color: #818cf8; color: #ffffff;">Kirim (Isi)</th>';
        echo '<th style="background-color: #818cf8; color: #ffffff;">Kembali (Kosong)</th>';
        echo '<th style="background-color: #818cf8; color: #ffffff;">Keterangan</th>';
        echo '</tr>';

        if (empty($deliveries)) {
            echo '<tr><td colspan="5" style="text-align: center;">Belum ada riwayat pengiriman.</td></tr>';
        } else {
            foreach ($deliveries as $d) {
                echo '<tr>';
                echo '<td>' . date('d-m-Y', strtotime($d['tanggal'])) . '</td>';
                echo '<td>' . htmlspecialchars($d['nama_barang']) . '</td>';
                echo '<td style="text-align: right; color: green;">' . ($d['jumlah_masuk'] > 0 ? '+' . $d['jumlah_masuk'] : '0') . '</td>';
                echo '<td style="text-align: right; color: orange;">' . ($d['jumlah_keluar'] > 0 ? '-' . $d['jumlah_keluar'] : '0') . '</td>';
                echo '<td>' . htmlspecialchars($d['keterangan']) . '</td>';
                echo '</tr>';
            }
        }
        echo '</table></body></html>';
        exit;
    }
}
