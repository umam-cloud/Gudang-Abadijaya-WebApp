<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-title">
        <h2>Dashboard Ringkasan</h2>
        <p>Pantau status stok gudang, pinjaman tabung relasi, dan alert repurchasing</p>
    </div>
    <div class="page-actions">
        <a href="index.php?controller=pengiriman&action=create" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:18px;height:18px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Catat Pengiriman
        </a>
    </div>
</div>

<!-- Alert Banner if Inactivity warnings exist -->
<?php if ($activeAlertCount > 0): ?>
    <div class="alert-banner">
        <div class="alert-content">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <p><strong>Perhatian:</strong> Ada <?= $activeAlertCount ?> mitra/relasi yang sudah lebih dari 30 hari tidak melakukan transaksi atau pengisian ulang tabung!</p>
        </div>
        <a href="index.php?controller=evaluasi&action=index" class="btn btn-secondary btn-sm" style="color:var(--danger); border-color:var(--danger);">Evaluasi Sekarang</a>
    </div>
<?php endif; ?>

<!-- Metrics Grid -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-info">
            <h3>Mitra Relasi</h3>
            <div class="value"><?= $totalClients ?></div>
        </div>
        <div class="metric-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.005 9.005 0 00-6-6.197V8.5a3.5 3.5 0 117 0v4.023a9.005 9.005 0 00-1 6.197zm-6-6.197a9.005 9.005 0 00-6 6.197V12.523A9.003 9.003 0 0012 8.5v4.023zm-6 6.197a9.003 9.003 0 001 6.197V18.72zm6 0v2.28c0 .248-.202.45-.45.45H10.45a.45.45 0 01-.45-.45v-2.28m6 0v2.28c0 .248-.202.45-.45.45h-1.1c-.248 0-.45-.202-.45-.45v-2.28M6 18.72v2.28c0 .248.202.45.45.45h1.1a.45.45 0 00.45-.45v-2.28" />
            </svg>
        </div>
    </div>
    
    <div class="metric-card">
        <div class="metric-info">
            <h3>Jenis Tabung</h3>
            <div class="value"><?= $totalCylinderTypes ?></div>
        </div>
        <div class="metric-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.283 8.283 0 013 6.3 8.284 8.284 0 003-6.3 8.287 8.287 0 002.962-2.386z" />
            </svg>
        </div>
    </div>
    
    <div class="metric-card alert-card">
        <div class="metric-info">
            <h3>Alert Inaktif</h3>
            <div class="value" style="color:var(--danger);"><?= $activeAlertCount ?></div>
        </div>
        <div class="metric-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-info">
            <h3>Total Ready Gudang</h3>
            <div class="value" style="color:var(--success);">
                <?php 
                $sumReady = 0;
                foreach ($warehouseStocks as $w) $sumReady += $w['stok_ready'];
                echo $sumReady;
                ?>
            </div>
        </div>
        <div class="metric-icon" style="background:var(--success-bg); color:var(--success);">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
    </div>
</div>

<!-- Charts & Stock Status -->
<div class="dashboard-charts-grid">
    <!-- Stock Gudang Chart Card -->
    <div class="section-card">
        <div class="section-title">
            <h3>Ketersediaan Tabung Gudang</h3>
        </div>
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="warehouseChart"></canvas>
        </div>
    </div>
    
    <!-- Mini Stock Summary List -->
    <div class="section-card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div class="section-title">
                <h3>Detail Gudang</h3>
            </div>
            <div class="table-wrapper">
                <table class="custom-table" style="font-size:0.85rem;">
                    <thead>
                        <tr>
                            <th>Jenis</th>
                            <th>Ready</th>
                            <th>Kosong</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($warehouseStocks as $w): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($w['nama_barang']) ?></strong></td>
                                <td style="color:var(--success); font-weight:700;"><?= $w['stok_ready'] ?></td>
                                <td style="color:var(--warning); font-weight:700;"><?= $w['stok_kosong'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <a href="index.php?controller=gudang&action=index" class="btn btn-secondary btn-sm" style="margin-top: 1rem; width:100%; justify-content:center;">Kelola Gudang</a>
    </div>
</div>

<!-- Recent Deliveries Section -->
<div class="section-card">
    <div class="section-title">
        <h3>Pengiriman Terbaru</h3>
        <a href="index.php?controller=pengiriman&action=index" class="btn btn-secondary btn-sm">Lihat Semua</a>
    </div>
    
    <div class="table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Relasi</th>
                    <th>Jenis Tabung</th>
                    <th>Dikirim (Isi)</th>
                    <th>Kembali (Kosong)</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentDeliveries)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; color:var(--text-muted);">Belum ada riwayat pengiriman.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentDeliveries as $d): ?>
                        <tr>
                            <td><?= date('d-m-Y', strtotime($d['tanggal'])) ?></td>
                            <td>
                                <strong>[<?= htmlspecialchars($d['kode_relasi']) ?>]</strong> 
                                <?= htmlspecialchars($d['nama_relasi']) ?>
                            </td>
                            <td><?= htmlspecialchars($d['nama_barang']) ?></td>
                            <td style="color:var(--success); font-weight:700;">+<?= $d['jumlah_masuk'] ?></td>
                            <td style="color:var(--warning); font-weight:700;">-<?= $d['jumlah_keluar'] ?></td>
                            <td><?= htmlspecialchars($d['keterangan'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Load Chart.js for premium graphics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('warehouseChart').getContext('2d');
    
    // PHP variables mapped to JSON
    const labels = <?= json_encode(array_column($warehouseStocks, 'nama_barang')) ?>;
    const readyData = <?= json_encode(array_map('intval', array_column($warehouseStocks, 'stok_ready'))) ?>;
    const emptyData = <?= json_encode(array_map('intval', array_column($warehouseStocks, 'stok_kosong'))) ?>;
    
    // Style settings for dark/light mode integration
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#f3f4f6' : '#1f2937';
    const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.05)';

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Ready / Full',
                    data: readyData,
                    backgroundColor: 'rgba(13, 148, 136, 0.7)',
                    borderColor: 'rgb(13, 148, 136)',
                    borderWidth: 1,
                    borderRadius: 8
                },
                {
                    label: 'Kosong (Refill Queue)',
                    data: emptyData,
                    backgroundColor: 'rgba(245, 158, 11, 0.7)',
                    borderColor: 'rgb(245, 158, 11)',
                    borderWidth: 1,
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: textColor,
                        font: {
                            family: 'Outfit'
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: gridColor
                    },
                    ticks: {
                        color: textColor,
                        font: {
                            family: 'Outfit'
                        }
                    }
                },
                y: {
                    grid: {
                        color: gridColor
                    },
                    ticks: {
                        color: textColor,
                        font: {
                            family: 'Outfit'
                        }
                    },
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
