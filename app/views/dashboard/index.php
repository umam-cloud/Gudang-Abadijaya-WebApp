<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Dashboard Ringkasan</h2>
        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Pantau status stok gudang, pinjaman tabung relasi, dan alert repurchasing</p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>pengiriman/create" class="btn-primary">
            <i class="ph-bold ph-plus text-base"></i>
            Catat Pengiriman
        </a>
    </div>
</div>

<!-- Alert Banner if Inactivity warnings exist -->
<?php if ($activeAlertCount > 0): ?>
    <div class="flex items-center justify-between p-4 mb-8 rounded-xl badge-danger animate-[slideDown_0.4s_ease-out]">
        <div class="flex items-center gap-3">
            <i class="ph-fill ph-warning text-xl"></i>
            <p class="font-medium"><strong>Perhatian:</strong> Ada <?= $activeAlertCount ?> mitra/relasi yang sudah lebih dari 30 hari tidak melakukan transaksi atau pengisian ulang tabung!</p>
        </div>
        <a href="<?= BASE_URL ?>evaluasi" class="btn-sm bg-white dark:bg-gray-800 border border-danger/30 text-danger hover:bg-danger-bg transition-colors font-bold">Evaluasi Sekarang</a>
    </div>
<?php endif; ?>

<!-- Metrics Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-10">
    <div class="glass-panel p-6 rounded-2xl flex justify-between items-center relative overflow-hidden group hover:-translate-y-1 transition-all shadow-sm before:absolute before:top-0 before:left-0 before:w-full before:h-1 before:bg-gradient-to-r before:from-primary before:to-success">
        <div>
            <h3 class="text-xs uppercase font-bold text-slate-500 tracking-wider mb-2">Mitra Relasi</h3>
            <div class="text-3xl font-extrabold text-slate-800 dark:text-gray-100"><?= $totalClients ?></div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
            <i class="ph-fill ph-users-three text-2xl"></i>
        </div>
    </div>
    
    <div class="glass-panel p-6 rounded-2xl flex justify-between items-center relative overflow-hidden group hover:-translate-y-1 transition-all shadow-sm before:absolute before:top-0 before:left-0 before:w-full before:h-1 before:bg-gradient-to-r before:from-primary before:to-success">
        <div>
            <h3 class="text-xs uppercase font-bold text-slate-500 tracking-wider mb-2">Jenis Tabung</h3>
            <div class="text-3xl font-extrabold text-slate-800 dark:text-gray-100"><?= $totalCylinderTypes ?></div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
            <i class="ph ph-cylinder text-2xl"></i>
        </div>
    </div>
    
    <div class="glass-panel p-6 rounded-2xl flex justify-between items-center relative overflow-hidden group hover:-translate-y-1 transition-all shadow-sm before:absolute before:top-0 before:left-0 before:w-full before:h-1 before:bg-danger">
        <div>
            <h3 class="text-xs uppercase font-bold text-slate-500 tracking-wider mb-2">Alert Inaktif</h3>
            <div class="text-3xl font-extrabold text-danger"><?= $activeAlertCount ?></div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-danger-bg text-danger flex items-center justify-center animate-[pulse_2s_infinite]">
            <i class="ph-fill ph-warning-circle text-2xl"></i>
        </div>
    </div>

    <div class="glass-panel p-6 rounded-2xl flex justify-between items-center relative z-10 hover:z-50 overflow-visible group hover:-translate-y-1 transition-all shadow-sm before:absolute before:top-0 before:left-0 before:w-full before:h-1 before:rounded-t-2xl before:bg-gradient-to-r before:from-indigo-500 before:to-purple-500 cursor-pointer">
        <div>
            <h3 class="text-xs uppercase font-bold text-slate-500 tracking-wider mb-2 flex items-center gap-1">
                Tabung di Mitra
                <i class="ph-bold ph-info text-indigo-400 text-sm"></i>
            </h3>
            <div class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400"><?= $totalTabungMitra ?></div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center group-hover:scale-110 transition-transform relative z-10">
            <i class="ph-fill ph-storefront text-2xl"></i>
        </div>

        <!-- Hover Dropdown / Tooltip -->
        <div class="absolute z-50 left-0 top-full mt-2 w-full sm:w-64 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 translate-y-2 group-hover:translate-y-0">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-slate-100 dark:border-gray-700 p-4 relative before:absolute before:-top-2 before:left-6 before:w-4 before:h-4 before:bg-white dark:before:bg-gray-800 before:border-l before:border-t before:border-slate-100 dark:before:border-gray-700 before:rotate-45">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 border-b border-slate-100 dark:border-gray-700 pb-2 relative z-10">Rincian per Tabung</h4>
                <div class="space-y-2 relative z-10">
                    <?php if (empty($tabungMitraDetail)): ?>
                        <div class="text-sm text-slate-500 text-center italic">Belum ada data</div>
                    <?php else: ?>
                        <?php foreach ($tabungMitraDetail as $nama => $jumlah): ?>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-600 dark:text-gray-300 font-medium truncate pr-2"><?= htmlspecialchars($nama) ?></span>
                            <span class="font-bold text-indigo-600 dark:text-indigo-400"><?= $jumlah ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="glass-panel p-6 rounded-2xl flex justify-between items-center relative overflow-hidden group hover:-translate-y-1 transition-all shadow-sm before:absolute before:top-0 before:left-0 before:w-full before:h-1 before:bg-success">
        <div>
            <h3 class="text-xs uppercase font-bold text-slate-500 tracking-wider mb-2">Total Ready</h3>
            <div class="text-3xl font-extrabold text-success">
                <?php 
                $sumReady = 0;
                foreach ($warehouseStocks as $w) $sumReady += $w['stok_ready'];
                echo $sumReady;
                ?>
            </div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-success-bg text-success flex items-center justify-center group-hover:scale-110 transition-transform">
            <i class="ph-bold ph-check-circle text-2xl"></i>
        </div>
    </div>

</div>

<!-- Charts & Stock Status -->
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stock Gudang Chart Card -->
    <div class="lg:col-span-2 glass-panel p-6 rounded-2xl shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold">Ketersediaan Tabung Gudang</h3>
        </div>
        <div class="overflow-x-auto pb-2 custom-scrollbar">
            <div class="relative h-[200px] sm:h-[300px]" style="min-width: <?= max(400, count($warehouseStocks) * 100) ?>px;">
                <canvas id="warehouseChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Mini Stock Summary List -->
    <div class="lg:col-span-1 glass-panel p-6 rounded-2xl shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold">Detail Gudang</h3>
            </div>
            <div class="overflow-x-auto overflow-y-auto max-h-[250px] border border-slate-200 dark:border-gray-700 rounded-xl custom-scrollbar relative">
                <table class="w-full text-xs sm:text-sm text-left">
                    <thead class="bg-slate-50 dark:bg-gray-800 text-slate-500 sticky top-0 z-10 shadow-sm">
                        <tr>
                            <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Jenis</th>
                            <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Ready</th>
                            <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Kosong</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($warehouseStocks as $w): ?>
                            <tr class="hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5">
                                <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 font-bold text-slate-800 dark:text-gray-200"><?= htmlspecialchars($w['nama_barang']) ?></td>
                                <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-success font-bold"><?= $w['stok_ready'] ?></td>
                                <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-warning font-bold"><?= $w['stok_kosong'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <a href="<?= BASE_URL ?>gudang" class="btn-secondary w-full justify-center mt-4">Kelola Gudang</a>
    </div>

    <!-- Grand Total -->
    <div class="lg:col-span-1 glass-panel p-6 rounded-2xl shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-indigo-600 dark:text-indigo-400">Grand Total</h3>
            </div>
            <p class="text-xs text-slate-500 mb-2 italic">*Total keseluruhan tabung (Gudang + Mitra) per jenis</p>
            <div class="overflow-x-auto overflow-y-auto max-h-[250px] border border-slate-200 dark:border-gray-700 rounded-xl custom-scrollbar relative">
                <table class="w-full text-xs sm:text-sm text-left">
                    <thead class="bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-300 sticky top-0 z-10 shadow-sm">
                        <tr>
                            <th class="px-5 py-4 font-semibold border-b border-indigo-100 dark:border-indigo-800">Jenis</th>
                            <th class="px-5 py-4 font-semibold border-b border-indigo-100 dark:border-indigo-800 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grandTotalTabung as $nama => $jumlah): ?>
                            <tr class="hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5">
                                <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 font-bold text-slate-800 dark:text-gray-200"><?= htmlspecialchars($nama) ?></td>
                                <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-indigo-600 dark:text-indigo-400 font-bold text-right"><?= $jumlah ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Recent Deliveries Section -->
<div class="glass-panel p-6 rounded-2xl shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold">Pengiriman Terbaru</h3>
        <a href="<?= BASE_URL ?>pengiriman" class="btn-secondary btn-sm">Lihat Semua</a>
    </div>
    
    <div class="overflow-x-auto border border-slate-200 dark:border-gray-700 rounded-xl">
        <table class="w-full text-xs sm:text-sm text-left whitespace-nowrap">
            <thead class="bg-slate-50/50 dark:bg-gray-800/50 text-slate-500">
                <tr>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Tanggal</th>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Relasi</th>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Jenis Tabung</th>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Dikirim (Isi)</th>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Kembali (Kosong)</th>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentDeliveries)): ?>
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-slate-500">Belum ada riwayat pengiriman.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentDeliveries as $d): ?>
                        <tr class="hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5">
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-800 dark:text-gray-200"><?= date('d-m-Y', strtotime($d['tanggal'])) ?></td>
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-800 dark:text-gray-200">
                                <?= htmlspecialchars($d['nama_relasi']) ?>
                            </td>
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-800 dark:text-gray-200"><?= htmlspecialchars($d['nama_barang']) ?></td>
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-success font-bold">+<?= $d['jumlah_masuk'] ?></td>
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-warning font-bold">-<?= $d['jumlah_keluar'] ?></td>
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-800 dark:text-gray-200"><?= htmlspecialchars($d['keterangan'] ?: '-') ?></td>
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
    
    // Map negative values to red colors
    const readyBgColors = readyData.map(val => val < 0 ? 'rgba(239, 68, 68, 0.7)' : 'rgba(13, 148, 136, 0.7)');
    const readyBorderColors = readyData.map(val => val < 0 ? 'rgb(239, 68, 68)' : 'rgb(13, 148, 136)');
    
    const emptyBgColors = emptyData.map(val => val < 0 ? 'rgba(239, 68, 68, 0.7)' : 'rgba(245, 158, 11, 0.7)');
    const emptyBorderColors = emptyData.map(val => val < 0 ? 'rgb(239, 68, 68)' : 'rgb(245, 158, 11)');
    
    // Style settings for dark/light mode integration
    const isDark = document.documentElement.classList.contains('dark');
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
                    backgroundColor: readyBgColors,
                    borderColor: readyBorderColors,
                    borderWidth: 1,
                    borderRadius: 8
                },
                {
                    label: 'Kosong (Refill Queue)',
                    data: emptyData,
                    backgroundColor: emptyBgColors,
                    borderColor: emptyBorderColors,
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
