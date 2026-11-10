<?php
class DashboardController {
    public function index() {
        $relasiModel = new RelasiModel();
        $gudangModel = new GudangModel();
        $barangModel = new BarangModel();
        
        // Fetch warehouse stocks
        $warehouseStocks = $gudangModel->getWarehouseStock();
        
        // Fetch inactivity alerts
        $alerts = $relasiModel->getInactivityAlerts();
        
        // Count active alerts (>30 days or never transacted, AND stock > 0)
        $activeAlertCount = 0;
        foreach ($alerts as $a) {
            $stokRelasi = (int)($a['total_tabung_dipinjam'] ?? 0);
            if ($stokRelasi > 0 && ($a['hari_sejak_pengiriman'] === null || $a['hari_sejak_pengiriman'] > 30)) {
                $activeAlertCount++;
            }
        }
        
        // Accurate total count of all relations in the system
        $totalClients = (int)$relasiModel->countAllRelasi();
        $totalCylinderTypes = count($warehouseStocks);
        
        // Fetch aggregate stock held across ALL relations (no pagination limit)
        $mitraStocks = $relasiModel->getTabungDiMitraSummary();
        $totalTabungMitra = 0;
        $tabungMitraDetail = [];
        foreach ($mitraStocks as $ms) {
            $stok = (int)$ms['total_di_mitra'];
            $nama_barang = $ms['nama_barang'];
            $tabungMitraDetail[$nama_barang] = $stok;
            $totalTabungMitra += $stok;
        }

        $totalStokGudang = 0;
        foreach ($warehouseStocks as $w) {
            $totalStokGudang += (int)$w['stok'];
        }
        
        // Fetch latest deliveries for dashboard summary (last 5)
        $pengirimanModel = new PengirimanModel();
        $recentDeliveries = $pengirimanModel->getAll(5, 0);

        // Optional history date filter applied exclusively to the Grand Total Tabung table
        $dateFilter = isset($_GET['date']) ? trim($_GET['date']) : '';
        if ($dateFilter !== '') {
            $dt = DateTime::createFromFormat('Y-m-d', $dateFilter);
            if (!$dt || $dt->format('Y-m-d') !== $dateFilter) {
                $dateFilter = '';
            }
        }

        if ($dateFilter !== '') {
            $grandTotalWarehouseStocks = $gudangModel->getWarehouseStockAtDate($dateFilter);
            $grandTotalMitraStocks = $relasiModel->getTabungDiMitraSummary($dateFilter);
        } else {
            $grandTotalWarehouseStocks = $warehouseStocks;
            $grandTotalMitraStocks = $mitraStocks;
        }

        // Build lookup maps keyed by barang_id for Grand Total breakdown
        $warehouseStockMap = [];
        foreach ($grandTotalWarehouseStocks as $w) {
            $warehouseStockMap[(int)$w['barang_id']] = (int)$w['stok'];
        }

        $mitraStockMap = [];
        foreach ($grandTotalMitraStocks as $ms) {
            $mitraStockMap[(int)$ms['barang_id']] = (int)$ms['total_di_mitra'];
        }

        // Calculate Grand Total (Mitra + Gudang) per type with detailed breakdown
        $barangList = $barangModel->getAll();
        $grandTotalDetail = [];
        $grandTotalTabung = [];
        $grandTotalStokGudang = 0;
        $grandTotalTabungMitra = 0;
        $grandTotalSemuaTabung = 0;

        foreach ($barangList as $b) {
            $barangId = (int)$b['id'];
            $nama_barang = $b['nama_barang'];
            $stokGudang = $warehouseStockMap[$barangId] ?? 0;
            $stokMitra = $mitraStockMap[$barangId] ?? 0;
            $totalTabung = $stokGudang + $stokMitra;

            $grandTotalStokGudang += $stokGudang;
            $grandTotalTabungMitra += $stokMitra;
            $grandTotalSemuaTabung += $totalTabung;

            $grandTotalDetail[] = [
                'nama_barang' => $nama_barang,
                'stok_gudang' => $stokGudang,
                'stok_mitra' => $stokMitra,
                'grand_total' => $totalTabung
            ];
            $grandTotalTabung[$nama_barang] = $totalTabung;
        }

        // Sort warehouse stocks (highest total first)
        usort($warehouseStocks, function($a, $b) {
            return (int)$b['stok'] <=> (int)$a['stok'];
        });

        // Sort grand total breakdown (highest grand total first)
        usort($grandTotalDetail, function($a, $b) {
            return $b['grand_total'] <=> $a['grand_total'];
        });

        arsort($grandTotalTabung);

        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
