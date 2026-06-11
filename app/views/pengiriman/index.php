<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Catatan Pengiriman Harian</h2>
        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Kelola pencatatan harian untuk pengiriman tabung isi ke klien dan pengembalian tabung kosong</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?= BASE_URL ?>pengiriman/export" class="btn-secondary !text-success border border-success/20 hover:!bg-success/10" target="_blank">
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
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold">Jurnal Log Transaksi Pengiriman</h3>
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
                                    <a href="index.php?controller=pengiriman&action=edit&id=<?= $d['id'] ?>" class="btn-sm bg-indigo-50 text-primary dark:bg-indigo-500/20 hover:bg-indigo-100 transition-colors inline-block no-underline" title="Edit Log">
                                        Edit
                                    </a>
                                    <a href="index.php?controller=pengiriman&action=delete&id=<?= $d['id'] ?>" class="btn-sm bg-red-50 text-danger dark:bg-red-500/20 hover:bg-red-100 transition-colors inline-block no-underline" onclick="return confirmAction(event, 'Apakah Anda yakin ingin menghapus catatan pengiriman ini? Stok di gudang dan relasi akan otomatis dihitung kembali.', this.href);" title="Hapus">
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
        <div class="flex justify-center gap-2 mt-8">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="index.php?controller=pengiriman&action=index&p=<?= $i ?>" class="btn-sm <?= $page == $i ? 'btn-primary' : 'bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
