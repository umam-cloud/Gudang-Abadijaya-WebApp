<?php
class DashboardController {
    public function index() {
        $relasiModel = new RelasiModel();
        $gudangModel = new GudangModel();
        $barangModel = new BarangModel();
        
        // Fetch all relation stock calculations
        $clients = $relasiModel->getAllWithStocks();
        
        // Fetch warehouse stocks
        $warehouseStocks = $gudangModel->getWarehouseStock();
        
        // Fetch inactivity alerts
        $alerts = $relasiModel->getInactivityAlerts();
        
        // Count active alerts (>30 days)
        $activeAlertCount = 0;
        foreach ($alerts as $a) {
            if ($a['hari_sejak_pengiriman'] !== null && $a['hari_sejak_pengiriman'] > 30) {
                $activeAlertCount++;
            }
        }
        
        $totalClients = count($clients);
        $totalCylinderTypes = count($warehouseStocks);
        
        $totalTabungMitra = 0;
        $tabungMitraDetail = [];
        foreach ($clients as $client) {
            foreach ($client['stocks'] as $stock) {
                $totalTabungMitra += (int)$stock['stok_akhir'];
                $nama_barang = $stock['nama_barang'];
                if (!isset($tabungMitraDetail[$nama_barang])) {
                    $tabungMitraDetail[$nama_barang] = 0;
                }
                $tabungMitraDetail[$nama_barang] += (int)$stock['stok_akhir'];
            }
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
