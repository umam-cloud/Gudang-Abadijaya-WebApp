<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Dashboard Ringkasan</h2>
        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Pantau total stok gudang, pinjaman tabung relasi, dan alert repurchasing</p>
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
            <h3 class="text-xs uppercase font-bold text-slate-500 tracking-wider mb-2">Total Stok Gudang</h3>
            <div class="text-3xl font-extrabold text-success">
                <?= $totalStokGudang ?>
            </div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-success-bg text-success flex items-center justify-center group-hover:scale-110 transition-transform">
            <i class="ph-bold ph-warehouse text-2xl"></i>
        </div>
    </div>

</div>

<!-- Grand Total & Warehouse Stock Status -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Grand Total (Prominent Feature Card) -->
    <div class="lg:col-span-2 glass-panel p-6 rounded-2xl shadow-sm flex flex-col justify-between">
        <div>
            <!-- Card Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-primary flex items-center justify-center">
                            <i class="ph-bold ph-stack text-lg"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-gray-100">Grand Total Tabung</h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">Total keseluruhan aset tabung (Stok Gudang + Sedang Dipinjam Mitra)</p>
                </div>
                
                <div class="flex items-center gap-2.5 w-full sm:w-auto justify-between sm:justify-end flex-wrap">
                    <!-- Dropdown Select Filter -->
                    <select id="selectFilterGrandTotal" class="py-1.5 px-3 text-xs rounded-xl border border-slate-200 dark:border-gray-700 bg-white/70 dark:bg-gray-800/70 text-slate-700 dark:text-gray-200 focus:outline-none focus:border-primary">
                        <option value="">Semua Jenis Tabung</option>
                        <?php foreach ($grandTotalDetail as $g): ?>
                            <option value="<?= htmlspecialchars($g['nama_barang']) ?>"><?= htmlspecialchars($g['nama_barang']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Live Search Input -->
                    <div class="relative flex-grow sm:flex-grow-0">
                        <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" id="filterGrandTotal" placeholder="Cari jenis..." class="w-full sm:w-36 pl-8 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 dark:border-gray-700 bg-white/70 dark:bg-gray-800/70 text-slate-900 dark:text-gray-100 focus:outline-none focus:border-primary">
                    </div>
                    <!-- Total Count Badge -->
                    <span class="badge badge-info text-xs font-bold px-3 py-1.5 whitespace-nowrap">
                        <i class="ph-bold ph-cylinder mr-1"></i> <?= number_format($totalSemuaTabung, 0, ',', '.') ?> Unit
                    </span>
                </div>
            </div>

            <!-- Table Container -->
            <div class="overflow-x-auto overflow-y-auto max-h-[380px] border border-slate-200 dark:border-gray-700 rounded-xl custom-scrollbar relative">
                <table class="w-full text-xs sm:text-sm text-left">
                    <thead class="bg-indigo-50/80 dark:bg-indigo-950/40 text-indigo-900 dark:text-indigo-200 sticky top-0 z-10 shadow-sm backdrop-blur-sm">
                        <tr>
                            <th class="px-5 py-3.5 font-bold border-b border-indigo-100 dark:border-indigo-900/50">Jenis Tabung</th>
                            <th class="px-5 py-3.5 font-bold border-b border-indigo-100 dark:border-indigo-900/50 text-right">Di Gudang</th>
                            <th class="px-5 py-3.5 font-bold border-b border-indigo-100 dark:border-indigo-900/50 text-right">Di Mitra</th>
                            <th class="px-5 py-3.5 font-bold border-b border-indigo-100 dark:border-indigo-900/50 text-right">Grand Total</th>
                        </tr>
                    </thead>
                    <tbody id="grandTotalTableBody" class="divide-y divide-slate-100 dark:divide-gray-800">
                        <?php if (empty($grandTotalDetail)): ?>
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-slate-500 italic">Belum ada data barang.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($grandTotalDetail as $item): 
                                $total = $item['grand_total'];
                                $gudang = $item['stok_gudang'];
                                $mitra = $item['stok_mitra'];
                            ?>
                                <tr class="hover:bg-indigo-50/40 dark:hover:bg-indigo-500/5 transition-colors">
                                    <td class="px-5 py-3.5 font-semibold text-slate-800 dark:text-gray-200 tabung-name">
                                        <?= htmlspecialchars($item['nama_barang']) ?>
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-medium <?= $gudang < 0 ? 'text-danger' : 'text-success' ?>">
                                        <?= number_format($gudang, 0, ',', '.') ?>
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-medium text-indigo-600 dark:text-indigo-400">
                                        <?= number_format($mitra, 0, ',', '.') ?>
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-black text-slate-900 dark:text-gray-100">
                                        <span class="inline-block px-2.5 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300">
                                            <?= number_format($total, 0, ',', '.') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="bg-slate-50 dark:bg-gray-800 text-slate-800 dark:text-gray-200 font-bold border-t-2 border-slate-200 dark:border-gray-700 sticky bottom-0 z-10 shadow-sm">
                        <tr>
                            <td class="px-5 py-3">Total (<?= count($grandTotalDetail) ?> Jenis Tabung)</td>
                            <td class="px-5 py-3 text-right text-success"><?= number_format($totalStokGudang, 0, ',', '.') ?></td>
                            <td class="px-5 py-3 text-right text-indigo-600 dark:text-indigo-400"><?= number_format($totalTabungMitra, 0, ',', '.') ?></td>
                            <td class="px-5 py-3 text-right font-black text-indigo-600 dark:text-indigo-400">
                                <span class="inline-block px-2.5 py-0.5 rounded-md bg-indigo-100 dark:bg-indigo-900/50">
                                    <?= number_format($totalSemuaTabung, 0, ',', '.') ?>
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <!-- Legend notes -->
        <div class="flex flex-wrap items-center justify-between text-xs text-slate-500 dark:text-gray-400 mt-4 pt-3 border-t border-slate-100 dark:border-gray-700/50">
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-success"></span> Stok Gudang</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-primary"></span> Tabung di Mitra</span>
            </div>
            <span class="italic text-[11px]">*Diperbarui otomatis secara real-time dari log transaksi</span>
        </div>
    </div>
    
    <!-- Stok Fisik di Gudang Card -->
    <div class="lg:col-span-1 glass-panel p-6 rounded-2xl shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex justify-between items-center mb-6">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-500/10 text-success flex items-center justify-center">
                            <i class="ph-bold ph-warehouse text-lg"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-gray-100">Stok di Gudang</h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">Stok fisik saat ini di gudang utama</p>
                </div>
                <span class="badge badge-success text-xs font-bold px-2.5 py-1">
                    <?= number_format($totalStokGudang, 0, ',', '.') ?>
                </span>
            </div>
            
            <div class="overflow-x-auto overflow-y-auto max-h-[380px] border border-slate-200 dark:border-gray-700 rounded-xl custom-scrollbar relative">
                <table class="w-full text-xs sm:text-sm text-left">
                    <thead class="bg-slate-50 dark:bg-gray-800 text-slate-500 sticky top-0 z-10 shadow-sm backdrop-blur-sm">
                        <tr>
                            <th class="px-5 py-3.5 font-semibold border-b border-slate-200 dark:border-gray-700">Jenis Tabung</th>
                            <th class="px-5 py-3.5 font-semibold border-b border-slate-200 dark:border-gray-700 text-right">Stok Fisik</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-gray-800">
                        <?php foreach ($warehouseStocks as $w): ?>
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-gray-200"><?= htmlspecialchars($w['nama_barang']) ?></td>
                                <td class="px-5 py-3.5 text-success font-bold text-right"><?= number_format($w['stok'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <a href="<?= BASE_URL ?>gudang" class="btn-secondary w-full justify-center mt-4">
            <i class="ph-bold ph-sliders text-base"></i>
            Kelola Stok Gudang
        </a>
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
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Dikirim</th>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Kembali</th>
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

<!-- Grand Total Table Filter Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const filterInput = document.getElementById('filterGrandTotal');
    const filterSelect = document.getElementById('selectFilterGrandTotal');

    function applyGrandTotalFilter(query) {
        const q = (query || '').toLowerCase().trim();
        const rows = document.querySelectorAll('#grandTotalTableBody tr');
        rows.forEach(row => {
            const nameEl = row.querySelector('.tabung-name');
            if (nameEl) {
                const text = nameEl.textContent.toLowerCase();
                row.style.display = (q === '' || text.includes(q)) ? '' : 'none';
            }
        });
    }

    if (filterInput) {
        filterInput.addEventListener('input', function() {
            if (filterSelect) filterSelect.value = '';
            applyGrandTotalFilter(this.value);
        });
    }

    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            if (filterInput) filterInput.value = this.value;
            applyGrandTotalFilter(this.value);
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
