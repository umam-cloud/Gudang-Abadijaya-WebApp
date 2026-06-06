<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Evaluasi &amp; Alert Repurchasing</h2>
        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Monitor relasi yang sudah lebih dari 30 hari tidak ada pergerakan transaksi</p>
    </div>
    <div>
        <a href="index.php?controller=relasi&action=index" class="btn-secondary">Semua Relasi</a>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'success_eval'): ?>
        <div class="flex items-center justify-between p-4 mb-8 rounded-xl badge-success animate-[slideDown_0.4s_ease-out]">
            <div class="flex items-center gap-3">
                <i class="ph-bold ph-check-circle text-xl"></i>
                <p class="font-medium">Log tindakan evaluasi berhasil disimpan!</p>
            </div>
            <button class="hover:opacity-75 transition-opacity alert-close-btn">&times;</button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="glass-panel p-6 rounded-2xl shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h3 class="flex items-center gap-2 text-lg font-bold">
            <i class="ph-fill ph-warning-circle text-danger text-2xl"></i>
            Daftar Mitra Inaktif (Butuh Tindakan)
        </h3>
    </div>

    <div class="overflow-x-auto border border-slate-200 dark:border-gray-700 rounded-xl">
        <table class="w-full text-xs sm:text-sm text-left whitespace-nowrap">
            <thead class="bg-slate-50/50 dark:bg-gray-800/50 text-slate-500">
                <tr>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Nama Relasi / Mitra</th>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Lokasi</th>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Tgl Pengiriman Terakhir</th>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Inaktif Selama</th>
                    <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700 text-center">Tindakan Evaluasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inactiveClients)): ?>
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-slate-500">
                            <i class="ph-bold ph-check-circle text-[48px] mx-auto mb-4 opacity-50 block text-center"></i>
                            Hebat! Semua mitra aktif bertransaksi dalam 30 hari terakhir.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($inactiveClients as $c): ?>
                        <tr class="bg-red-50/50 dark:bg-red-900/10 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-800 dark:text-gray-200 font-bold"><?= htmlspecialchars($c['nama_relasi']) ?></td>
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-800 dark:text-gray-200"><?= htmlspecialchars($c['lokasi'] ?: '-') ?></td>
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-800 dark:text-gray-200">
                                <?= $c['tanggal_terakhir'] ? date('d M Y', strtotime($c['tanggal_terakhir'])) : '<span class="text-slate-400">Belum pernah</span>' ?>
                            </td>
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700">
                                <?php if ($c['hari_sejak_pengiriman'] === null): ?>
                                    <strong class="text-danger">Belum ada transaksi</strong>
                                <?php else: ?>
                                    <strong class="text-danger"><?= $c['hari_sejak_pengiriman'] ?> Hari</strong>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-center">
                                <a href="index.php?controller=relasi&action=detail&id=<?= $c['relasi_id'] ?>" class="btn-sm bg-danger text-white hover:bg-red-600 transition-colors inline-block no-underline">
                                    Catat Evaluasi Klien
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
