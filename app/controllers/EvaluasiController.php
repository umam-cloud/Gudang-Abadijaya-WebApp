<?php
class EvaluasiController {
    public function index() {
        $relasiModel = new RelasiModel();
        
        // Fetch all client inactivity alerts
        $alerts = $relasiModel->getInactivityAlerts();
        
        // Filter to only active warnings (>30 days since last delivery or never had a delivery)
        $inactiveClients = [];
        foreach ($alerts as $a) {
            // If they had deliveries and days > 30, OR if they never had deliveries
            // Let's count them as inactive if days > 30 or null (so we encourage deliveries)
            if ($a['hari_sejak_pengiriman'] === null || $a['hari_sejak_pengiriman'] > 30) {
                $inactiveClients[] = $a;
            }
        }

        require_once __DIR__ . '/../views/evaluasi/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $relasi_id = (int)$_POST['relasi_id'];
            $tanggal = $_POST['tanggal'];
            $status_lanjut = $_POST['status_lanjut'];
            $catatan = trim($_POST['catatan']);

            $evaluasiModel = new EvaluasiModel();
            try {
                $evaluasiModel->create($relasi_id, $tanggal, $status_lanjut, $catatan);
                header("Location: " . BASE_URL . "evaluasi?msg=success_eval");
                exit;
            } catch (Exception $e) {
                header("Location: " . BASE_URL . "evaluasi?msg=error_eval");
                exit;
            }
        }
        header("Location: " . BASE_URL . "evaluasi");
        exit;
    }
}
