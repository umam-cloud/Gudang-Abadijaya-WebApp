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

        // Build lookup map for warehouse stock per item
        $warehouseStockMap = [];
        foreach ($warehouseStocks as $w) {
            $warehouseStockMap[$w['nama_barang']] = (int)$w['stok'];
        }

        // Calculate Grand Total (Mitra + Gudang) per type with detailed breakdown
        $barangList = $barangModel->getAll();
        $grandTotalDetail = [];
        $grandTotalTabung = [];
        $totalSemuaTabung = 0;

        foreach ($barangList as $b) {
            $nama_barang = $b['nama_barang'];
            $stokGudang = $warehouseStockMap[$nama_barang] ?? 0;
            $stokMitra = $tabungMitraDetail[$nama_barang] ?? 0;
            $totalTabung = $stokGudang + $stokMitra;
            $totalSemuaTabung += $totalTabung;

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
