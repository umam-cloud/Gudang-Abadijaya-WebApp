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
        foreach ($clients as $client) {
            foreach ($client['stocks'] as $stock) {
                $totalTabungMitra += $stock['stok_akhir'];
            }
        }
        
        // Fetch latest deliveries for dashboard summary (last 5)
        $pengirimanModel = new PengirimanModel();
        $recentDeliveries = $pengirimanModel->getAll(5, 0);

        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
