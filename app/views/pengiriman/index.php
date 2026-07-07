<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Catatan Pengiriman Harian</h2>
        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Kelola pencatatan harian untuk pengiriman tabung isi ke klien dan pengembalian tabung kosong</p>
    </div>
<?php
$filterQuery = '';
if (!empty($_GET['search'])) $filterQuery .= '&search=' . urlencode($_GET['search']);
if (!empty($_GET['tanggal'])) $filterQuery .= '&tanggal=' . urlencode($_GET['tanggal']);
$exportUrl = BASE_URL . 'pengiriman/export' . ($filterQuery ? '?' . ltrim($filterQuery, '&') : '');
?>
    <div class="flex items-center gap-3">
        <a href="<?= $exportUrl ?>" class="btn-secondary !text-success border border-success/20 hover:!bg-success/10" target="_blank">
            <i class="ph-bold ph-file-csv text-base"></i>
            Export Excel
        </a>
        <a href="<?= BASE_URL ?>pengiriman/create" class="btn-primary">
            <i class="ph-bold ph-plus text-base"></i>
            Catat Pengiriman Baru
        </a>
    </div>
</div>

<!-- Deliveries List Card -->
<div class="glass-panel p-6 rounded-2xl shadow-sm">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h3 class="text-lg font-bold">Jurnal Log Transaksi Pengiriman</h3>
        
        <style>
            .filter-wrapper .choices {
                margin-bottom: 0;
            }
            .filter-wrapper .choices__inner {
                min-height: 38px !important; /* Menyamakan tinggi dengan tombol dan input text py-1.5 */
                padding-top: 6px !important;
                padding-bottom: 6px !important;
            }
            .filter-wrapper .choices[data-type*="select-one"]::after {
                margin-top: -6px; /* Menyesuaikan posisi panah dropdown */
            }
        </style>
        
        <form action="<?= BASE_URL ?>pengiriman" method="GET" class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
            <input type="date" name="tanggal" value="<?= htmlspecialchars($_GET['tanggal'] ?? '') ?>" class="form-control py-1.5 text-sm w-full sm:w-[150px]" title="Filter Tanggal">
            <div class="w-full sm:w-[300px] relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ph-bold ph-magnifying-glass text-slate-400 dark:text-slate-500"></i>
                </div>
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="w-full pl-10 py-1.5 text-sm form-control" placeholder="Cari ket, no SJ, relasi, tabung...">
            </div>
            <button type="submit" class="btn-primary py-1.5 px-4 text-sm whitespace-nowrap">Cari</button>
            <?php if(!empty($_GET['tanggal']) || !empty($_GET['search'])): ?>
                <a href="<?= BASE_URL ?>pengiriman" class="btn-secondary py-1.5 px-4 text-sm whitespace-nowrap">Reset</a>
            <?php endif; ?>
        </form>
    </div>
    
    <div class="overflow-x-auto border border-slate-200 dark:border-gray-700 rounded-xl">
        <table class="w-full text-xs sm:text-sm text-left whitespace-nowrap">
            <thead class="bg-slate-50/50 dark:bg-gray-800/50 text-slate-500">
                <tr>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Tanggal</th>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">No. SJ / Nota</th>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Nama Relasi</th>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Jenis Tabung</th>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Kirim (Isi)</th>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Kembali (Kosong)</th>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Keterangan</th>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($deliveries)): ?>
                    <tr>
                        <td colspan="8" class="px-5 py-8 text-center text-slate-500">
                            Belum ada riwayat transaksi pengiriman. Klik tombol di atas untuk mencatat pengiriman.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($deliveries as $d): ?>
                        <tr class="hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5 transition-colors">
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-800 dark:text-gray-200"><?= date('d-m-Y', strtotime($d['tanggal'])) ?></td>
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-800 dark:text-gray-200"><?= htmlspecialchars($d['no_surat_jalan'] ?? '-') ?></td>
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 font-bold text-slate-800 dark:text-gray-200"><?= htmlspecialchars($d['nama_relasi']) ?></td>
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-800 dark:text-gray-200"><?= htmlspecialchars($d['nama_barang']) ?></td>
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-success font-bold">+<?= $d['jumlah_masuk'] ?> <span class="text-xs font-normal text-slate-500">(<?= htmlspecialchars($d['kondisi_kirim']) ?>)</span></td>
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-warning font-bold">-<?= $d['jumlah_keluar'] ?> <span class="text-xs font-normal text-slate-500">(<?= htmlspecialchars($d['kondisi_kembali']) ?>)</span></td>
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-800 dark:text-gray-200"><?= htmlspecialchars($d['keterangan'] ?: '-') ?></td>
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?= BASE_URL ?>pengiriman/edit/<?= $d['id'] ?>" class="btn-sm bg-indigo-50 text-primary dark:bg-indigo-500/20 hover:bg-indigo-100 transition-colors inline-block no-underline" title="Edit Log">
                                        Edit
                                    </a>
                                    <a href="<?= BASE_URL ?>pengiriman/delete/<?= $d['id'] ?>" class="btn-sm bg-red-50 text-danger dark:bg-red-500/20 hover:bg-red-100 transition-colors inline-block no-underline" onclick="return confirmAction(event, 'Apakah Anda yakin ingin menghapus catatan pengiriman ini? Stok di gudang dan relasi akan otomatis dihitung kembali.', this.href);" title="Hapus">
                                        Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="flex justify-center items-center gap-1 mt-8">
            <!-- Prev -->
            <?php if ($page > 1): ?>
                <a href="<?= BASE_URL ?>pengiriman/index?p=<?= $page - 1 ?><?= $filterQuery ?>" class="btn-sm bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                    <i class="ph-bold ph-caret-left"></i>
                </a>
            <?php endif; ?>

            <?php 
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);

            if ($startPage > 1) {
                echo '<a href="' . BASE_URL . 'pengiriman/index?p=1' . $filterQuery . '" class="btn-sm bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">1</a>';
                if ($startPage > 2) {
                    echo '<span class="px-2 text-slate-400">...</span>';
                }
            }

            for ($i = $startPage; $i <= $endPage; $i++): 
            ?>
                <a href="<?= BASE_URL ?>pengiriman/index?p=<?= $i ?><?= $filterQuery ?>" class="btn-sm <?= $page == $i ? 'btn-primary' : 'bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php 
            if ($endPage < $totalPages) {
                if ($endPage < $totalPages - 1) {
                    echo '<span class="px-2 text-slate-400">...</span>';
                }
                echo '<a href="' . BASE_URL . 'pengiriman/index?p=' . $totalPages . $filterQuery . '" class="btn-sm bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">' . $totalPages . '</a>';
            }
            ?>

            <!-- Next -->
            <?php if ($page < $totalPages): ?>
                <a href="<?= BASE_URL ?>pengiriman/index?p=<?= $page + 1 ?><?= $filterQuery ?>" class="btn-sm bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                    <i class="ph-bold ph-caret-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
