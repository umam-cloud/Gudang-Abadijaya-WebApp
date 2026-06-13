<?php
class PengirimanController {
    public function index() {
        $pengirimanModel = new PengirimanModel();
        $relasiModel = new RelasiModel();
        
        $clients = $relasiModel->getAll();

        $filters = [];
        if (!empty($_GET['tanggal'])) {
            $filters['tanggal'] = $_GET['tanggal'];
        }
        if (!empty($_GET['relasi_id'])) {
            $filters['relasi_id'] = $_GET['relasi_id'];
        }
        
        // Simple pagination
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($page < 1) $page = 1;
        $limit = 30;
        $offset = ($page - 1) * $limit;
        
        $deliveries = $pengirimanModel->getAll($limit, $offset, $filters);
        
        // Total count for basic pagination
        $total = $pengirimanModel->countAll($filters);
        $totalPages = ceil($total / $limit);

        require_once __DIR__ . '/../views/pengiriman/index.php';
    }

    public function export() {
        $pengirimanModel = new PengirimanModel();
        
        $filters = [];
        if (!empty($_GET['tanggal'])) {
            $filters['tanggal'] = $_GET['tanggal'];
        }
        if (!empty($_GET['relasi_id'])) {
            $filters['relasi_id'] = $_GET['relasi_id'];
        }
        
        // Fetch all without limit
        $deliveries = $pengirimanModel->getAll(1000000, 0, $filters);

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
        $headers = ['Tanggal', 'No. Surat Jalan', 'Nama Relasi', 'Jenis Tabung', 'Kirim (Isi)', 'Kembali (Kosong)', 'Keterangan'];
        foreach ($headers as $head) {
            echo '<th style="background-color: #4f46e5; color: #ffffff; font-weight: bold; text-align: center;">' . $head . '</th>';
        }
        echo '</tr>';

        // Data rows
        foreach ($deliveries as $d) {
            echo '<tr>';
            // mso-number-format:"\@" forces the cell to be treated as Text, preventing the #### date issue
            echo '<td style="mso-number-format:\'\@\';">' . date('d-m-Y', strtotime($d['tanggal'])) . '</td>';
            echo '<td>' . htmlspecialchars($d['no_surat_jalan'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($d['nama_relasi']) . '</td>';
            echo '<td>' . htmlspecialchars($d['nama_barang']) . '</td>';
            echo '<td style="text-align: right;">' . $d['jumlah_masuk'] . ' (' . htmlspecialchars($d['kondisi_kirim']) . ')</td>';
            echo '<td style="text-align: right;">' . $d['jumlah_keluar'] . ' (' . htmlspecialchars($d['kondisi_kembali']) . ')</td>';
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
            $no_surat_jalan = trim($_POST['no_surat_jalan'] ?? '');
            $relasi_id = (int)$_POST['relasi_id'];
            $keterangan = trim($_POST['keterangan']);
            
            $barang_ids = $_POST['barang_id'] ?? [];
            $jumlah_masuks = $_POST['jumlah_masuk'] ?? [];
            $kondisi_kirims = $_POST['kondisi_kirim'] ?? [];
            $jumlah_keluars = $_POST['jumlah_keluar'] ?? [];
            $kondisi_kembalis = $_POST['kondisi_kembali'] ?? [];

            $db = (new Database())->getConnection();
            
            // 1. Accumulate totals per barang_id to validate stock
            $totals = [];
            $valid_items = [];
            for ($i = 0; $i < count($barang_ids); $i++) {
                $b_id = (int)$barang_ids[$i];
                $j_m = (int)($jumlah_masuks[$i] ?? 0);
                $k_kirim = trim($kondisi_kirims[$i] ?? 'Isi');
                $j_k = (int)($jumlah_keluars[$i] ?? 0);
                $k_kembali = trim($kondisi_kembalis[$i] ?? 'Kosong');
                
                if ($b_id <= 0 || ($j_m == 0 && $j_k == 0)) continue;

                if (!isset($totals[$b_id])) {
                    $totals[$b_id] = [
                        'masuk_isi' => 0, 'masuk_kosong' => 0,
                        'keluar_isi' => 0, 'keluar_kosong' => 0
                    ];
                }
                if ($k_kirim == 'Isi') $totals[$b_id]['masuk_isi'] += $j_m;
                elseif ($k_kirim == 'Kosong') $totals[$b_id]['masuk_kosong'] += $j_m;
                
                if ($k_kembali == 'Kosong') $totals[$b_id]['keluar_kosong'] += $j_k;
                elseif ($k_kembali == 'Isi') $totals[$b_id]['keluar_isi'] += $j_k;
                
                $valid_items[] = [
                    'barang_id' => $b_id,
                    'jumlah_masuk' => $j_m,
                    'kondisi_kirim' => $k_kirim,
                    'jumlah_keluar' => $j_k,
                    'kondisi_kembali' => $k_kembali
                ];
            }

            if (empty($valid_items)) {
                $error = "Gagal: Harus ada minimal 1 tabung dengan jumlah kirim atau kembali.";
            }

            // 2. Validate all totals
            if (!isset($error)) {
                foreach ($totals as $b_id => $qty) {
                    if ($qty['masuk_isi'] > 0 || $qty['masuk_kosong'] > 0) {
                        $stmt_gudang = $db->prepare("SELECT stok_ready, stok_kosong FROM stok_gudang WHERE barang_id = ?");
                        $stmt_gudang->execute([$b_id]);
                        $stok_gudang = $stmt_gudang->fetch();
                        
                        if ($qty['masuk_isi'] > 0 && (!$stok_gudang || $qty['masuk_isi'] > $stok_gudang['stok_ready'])) {
                            $error = "Gagal: Total Kirim Isi (" . $qty['masuk_isi'] . ") melebihi stok ready di gudang (" . ($stok_gudang ? $stok_gudang['stok_ready'] : 0) . ").";
                            break;
                        }
                        if ($qty['masuk_kosong'] > 0 && (!$stok_gudang || $qty['masuk_kosong'] > $stok_gudang['stok_kosong'])) {
                            $error = "Gagal: Total Kirim Kosong (" . $qty['masuk_kosong'] . ") melebihi stok kosong di gudang (" . ($stok_gudang ? $stok_gudang['stok_kosong'] : 0) . ").";
                            break;
                        }
                    }
                    
                    $total_keluar = $qty['keluar_isi'] + $qty['keluar_kosong'];
                    if ($total_keluar > 0) {
                        $sql_stok = "SELECT (MAX(COALESCE(sa.stok_awal, 0)) + COALESCE(SUM(p.jumlah_masuk), 0) - COALESCE(SUM(p.jumlah_keluar), 0)) as stok_akhir
                                     FROM relasi r
                                     CROSS JOIN barang b
                                     LEFT JOIN relasi_stok_awal sa ON sa.relasi_id = r.id AND sa.barang_id = b.id
                                     LEFT JOIN pengiriman p ON p.relasi_id = r.id AND p.barang_id = b.id
                                     WHERE r.id = ? AND b.id = ? GROUP BY r.id, b.id";
                        $stmt_relasi = $db->prepare($sql_stok);
                        $stmt_relasi->execute([$relasi_id, $b_id]);
                        $stok_relasi = $stmt_relasi->fetch();
                        $stok_akhir = $stok_relasi ? $stok_relasi['stok_akhir'] : 0;
                        
                        // Validasi stok akhir dikomentari agar stok minus diperbolehkan
                        // if ($total_keluar > $stok_akhir) {
                        //     $error = "Gagal: Total Kembali (" . $total_keluar . ") melebihi stok fisik tabung yang sedang dipegang mitra (" . $stok_akhir . ").";
                        //     break;
                        // }
                    }
                }
            }

            if (!isset($error)) {
                try {
                    $db->beginTransaction();
                    $pengirimanModel = new PengirimanModel();
                    
                    foreach ($valid_items as $item) {
                        $pengirimanModel->create($tanggal, $no_surat_jalan, $relasi_id, $item['barang_id'], $item['jumlah_masuk'], $item['kondisi_kirim'], $item['jumlah_keluar'], $item['kondisi_kembali'], $keterangan);
                    }
                    
                    $db->commit();
                    header("Location: " . BASE_URL . "pengiriman?msg=success_create");
                    exit;
                } catch (Exception $e) {
                    $db->rollBack();
                    $error = "Gagal mencatat pengiriman: " . $e->getMessage();
                }
            }
        }

        // Get Stock Matrix for JS Modal Validation
        $db = (new Database())->getConnection();
        $sql_matrix = "SELECT r.id as relasi_id, b.id as barang_id,
                      (COALESCE(sa.stok_awal, 0) + COALESCE(p_in.total_in, 0) - COALESCE(p_out.total_out, 0)) as stok_akhir
                      FROM relasi r
                      CROSS JOIN barang b
                      LEFT JOIN relasi_stok_awal sa ON sa.relasi_id = r.id AND sa.barang_id = b.id
                      LEFT JOIN (SELECT relasi_id, barang_id, SUM(jumlah_masuk) as total_in FROM pengiriman GROUP BY relasi_id, barang_id) p_in ON p_in.relasi_id = r.id AND p_in.barang_id = b.id
                      LEFT JOIN (SELECT relasi_id, barang_id, SUM(jumlah_keluar) as total_out FROM pengiriman GROUP BY relasi_id, barang_id) p_out ON p_out.relasi_id = r.id AND p_out.barang_id = b.id";
        $stmt_matrix = $db->query($sql_matrix);
        $stockMatrixData = $stmt_matrix->fetchAll();
        $stockMatrix = [];
        foreach ($stockMatrixData as $row) {
            $stockMatrix[$row['relasi_id']][$row['barang_id']] = (int)$row['stok_akhir'];
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
            header("Location: " . BASE_URL . "pengiriman");
            exit;
        }

        $clients = $relasiModel->getAll();
        $barangList = $barangModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tanggal = $_POST['tanggal'];
            $no_surat_jalan = trim($_POST['no_surat_jalan'] ?? '');
            $relasi_id = (int)$_POST['relasi_id'];
            $barang_id = (int)$_POST['barang_id'];
            $jumlah_masuk = (int)$_POST['jumlah_masuk'];
            $kondisi_kirim = trim($_POST['kondisi_kirim'] ?? 'Isi');
            $jumlah_keluar = (int)$_POST['jumlah_keluar'];
            $kondisi_kembali = trim($_POST['kondisi_kembali'] ?? 'Kosong');
            $keterangan = trim($_POST['keterangan']);

            $db = (new Database())->getConnection();
            
            // Baseline untuk Gudang
            if ($jumlah_masuk > 0) {
                $stmt_gudang = $db->prepare("SELECT stok_ready, stok_kosong FROM stok_gudang WHERE barang_id = ?");
                $stmt_gudang->execute([$barang_id]);
                $stok_gudang = $stmt_gudang->fetch();
                
                if ($kondisi_kirim == 'Isi') {
                    $baseline_ready = $stok_gudang ? $stok_gudang['stok_ready'] : 0;
                    if ($pengiriman['barang_id'] == $barang_id && $pengiriman['kondisi_kirim'] == 'Isi') {
                        $baseline_ready += $pengiriman['jumlah_masuk'];
                    }
                    if ($jumlah_masuk > $baseline_ready) {
                        $error = "Gagal: Jumlah kirim (" . $jumlah_masuk . ") melebihi stok ready di gudang (" . $baseline_ready . ").";
                    }
                } else if ($kondisi_kirim == 'Kosong') {
                    $baseline_kosong = $stok_gudang ? $stok_gudang['stok_kosong'] : 0;
                    if ($pengiriman['barang_id'] == $barang_id && $pengiriman['kondisi_kirim'] == 'Kosong') {
                        $baseline_kosong += $pengiriman['jumlah_masuk'];
                    }
                    if ($jumlah_masuk > $baseline_kosong) {
                        $error = "Gagal: Jumlah kirim kosong (" . $jumlah_masuk . ") melebihi stok kosong di gudang (" . $baseline_kosong . ").";
                    }
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
                
                // Validasi stok dikomentari agar bisa minus
                // if ($jumlah_keluar > $baseline_akhir) {
                //     $error = "Gagal: Jumlah kembali (" . $jumlah_keluar . ") melebihi stok tabung yang dipegang mitra (" . $baseline_akhir . ").";
                // }
            }

            if (!isset($error)) {
                try {
                    $pengirimanModel->update($id, $tanggal, $no_surat_jalan, $relasi_id, $barang_id, $jumlah_masuk, $kondisi_kirim, $jumlah_keluar, $kondisi_kembali, $keterangan);
                    header("Location: " . BASE_URL . "pengiriman?msg=success_update");
                    exit;
                } catch (Exception $e) {
                    $error = "Gagal memperbarui pengiriman: " . $e->getMessage();
                }
            }
        }

        // Get Stock Matrix for JS Modal Validation
        $db = (new Database())->getConnection();
        $sql_matrix = "SELECT r.id as relasi_id, b.id as barang_id,
                      (COALESCE(sa.stok_awal, 0) + COALESCE(p_in.total_in, 0) - COALESCE(p_out.total_out, 0)) as stok_akhir
                      FROM relasi r
                      CROSS JOIN barang b
                      LEFT JOIN relasi_stok_awal sa ON sa.relasi_id = r.id AND sa.barang_id = b.id
                      LEFT JOIN (SELECT relasi_id, barang_id, SUM(jumlah_masuk) as total_in FROM pengiriman GROUP BY relasi_id, barang_id) p_in ON p_in.relasi_id = r.id AND p_in.barang_id = b.id
                      LEFT JOIN (SELECT relasi_id, barang_id, SUM(jumlah_keluar) as total_out FROM pengiriman GROUP BY relasi_id, barang_id) p_out ON p_out.relasi_id = r.id AND p_out.barang_id = b.id";
        $stmt_matrix = $db->query($sql_matrix);
        $stockMatrixData = $stmt_matrix->fetchAll();
        $stockMatrix = [];
        foreach ($stockMatrixData as $row) {
            $stockMatrix[$row['relasi_id']][$row['barang_id']] = (int)$row['stok_akhir'];
        }

        require_once __DIR__ . '/../views/pengiriman/edit.php';
    }

    public function delete() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $pengirimanModel = new PengirimanModel();
        
        if ($id > 0) {
            try {
                $pengirimanModel->delete($id);
                header("Location: " . BASE_URL . "pengiriman?msg=success_delete");
                exit;
            } catch (Exception $e) {
                header("Location: " . BASE_URL . "pengiriman?msg=error_delete");
                exit;
            }
        }
        header("Location: " . BASE_URL . "pengiriman");
        exit;
    }
}
