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
                $totalTabungMitra += $stock['stok_akhir'];
                $nama_barang = $stock['nama_barang'];
                if (!isset($tabungMitraDetail[$nama_barang])) {
                    $tabungMitraDetail[$nama_barang] = 0;
                }
                $tabungMitraDetail[$nama_barang] += $stock['stok_akhir'];
            }
        }
        
        // Fetch latest deliveries for dashboard summary (last 5)
        $pengirimanModel = new PengirimanModel();
        $recentDeliveries = $pengirimanModel->getAll(5, 0);

        // Calculate Grand Total (Mitra + Gudang) per type
        $grandTotalTabung = [];
        $barangList = $barangModel->getAll();
        foreach ($barangList as $b) {
            $grandTotalTabung[$b['nama_barang']] = 0;
        }

        foreach ($tabungMitraDetail as $nama_barang => $jumlah) {
            if (isset($grandTotalTabung[$nama_barang])) {
                $grandTotalTabung[$nama_barang] += $jumlah;
            } else {
                $grandTotalTabung[$nama_barang] = $jumlah;
            }
        }

        foreach ($warehouseStocks as $w) {
            $nama_barang = $w['nama_barang'];
            if (isset($grandTotalTabung[$nama_barang])) {
                $grandTotalTabung[$nama_barang] += ($w['stok_ready'] + $w['stok_kosong']);
            } else {
                $grandTotalTabung[$nama_barang] = ($w['stok_ready'] + $w['stok_kosong']);
            }
        }

        // Sort warehouse stocks for chart (highest total first)
        usort($warehouseStocks, function($a, $b) {
            $totalA = $a['stok_ready'] + $a['stok_kosong'];
            $totalB = $b['stok_ready'] + $b['stok_kosong'];
            return $totalB <=> $totalA;
        });

        // Sort grand total (highest total first)
        arsort($grandTotalTabung);

        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
