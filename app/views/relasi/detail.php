<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Profil Mitra &amp; Detail Saldo</h2>
        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Lihat detail inventaris tabung, riwayat pengiriman, dan log tindakan evaluasi</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="index.php?controller=relasi&action=index" class="btn-secondary">Kembali</a>
        <a href="index.php?controller=relasi&action=edit&id=<?= $relasi['id'] ?>" class="btn-primary">
            <i class="ph-bold ph-pencil text-base"></i>
            Edit Profil
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column: Profile Card & Summary -->
    <div class="lg:col-span-1 space-y-6">
        <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
            <div class="w-24 h-24 mx-auto mt-8 mb-4 bg-gradient-to-br from-primary to-cyan-500 rounded-full flex items-center justify-center text-4xl font-black text-white shadow-xl shadow-primary/30">
                <?= strtoupper(substr($relasi['nama_relasi'], 0, 1)) ?>
            </div>
            
            <div class="text-center p-6 border-b border-slate-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-slate-800 dark:text-gray-100"><?= htmlspecialchars($relasi['nama_relasi']) ?></h3>
                <div class="mt-2"><span class="badge badge-info">Kode: <?= htmlspecialchars($relasi['kode_relasi']) ?></span></div>
                <p class="text-sm text-slate-500 dark:text-gray-400 mt-3 flex items-center justify-center gap-1">
                    <i class="ph-fill ph-map-pin"></i>
                    <?= htmlspecialchars($relasi['lokasi'] ?: 'Tidak ada alamat') ?>
                </p>
            </div>
            
            <div class="p-6 space-y-4 text-sm">
                <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-gray-700/50">
                    <span class="text-slate-500 dark:text-gray-400">Pengiriman Terakhir:</span>
                    <span class="font-bold text-slate-800 dark:text-gray-200"><?= $last_delivery['tanggal_terakhir'] ? date('d-m-Y', strtotime($last_delivery['tanggal_terakhir'])) : '<span class="text-slate-400 font-normal">Belum pernah</span>' ?></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-gray-700/50">
                    <span class="text-slate-500 dark:text-gray-400">Waktu Sejak Pengiriman:</span>
                    <span>
                        <?php if ($last_delivery['hari_sejak_pengiriman'] === null): ?>
                            <span class="badge badge-warning">Baru / Inaktif</span>
                        <?php else: ?>
                            <strong class="text-slate-800 dark:text-gray-200"><?= $last_delivery['hari_sejak_pengiriman'] ?> Hari</strong>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <span class="text-slate-500 dark:text-gray-400">Status Alert:</span>
                    <span>
                        <?php if ($last_delivery['hari_sejak_pengiriman'] === null || $last_delivery['hari_sejak_pengiriman'] > 30): ?>
                            <span class="badge badge-danger animate-[pulse_2s_infinite]">Peringatan Inaktif (>30 Hari)</span>
                        <?php else: ?>
                            <span class="badge badge-success">Mitra Aktif</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Evaluation Action Box -->
        <?php if ($last_delivery['hari_sejak_pengiriman'] === null || $last_delivery['hari_sejak_pengiriman'] > 30): ?>
            <div class="glass-panel p-6 rounded-2xl shadow-sm border border-danger/20 bg-danger/5 dark:bg-danger/10">
                <h4 class="font-bold text-danger flex items-center gap-2 mb-2">
                    <i class="ph-fill ph-warning text-xl"></i>
                    Evaluasi Repurchase
                </h4>
                <p class="text-xs text-danger/80 mb-4">
                    Klien sudah tidak memesan selama lebih dari 1 bulan. Catat status negosiasi atau keputusan lanjut/putus di sini.
                </p>
                <form action="index.php?controller=evaluasi&action=create" method="POST" class="space-y-4">
                    <input type="hidden" name="relasi_id" value="<?= $relasi['id'] ?>">
                    <input type="hidden" name="tanggal" value="<?= date('Y-m-d') ?>">
                    
                    <div>
                        <label class="block text-xs font-semibold text-danger/90 mb-1" for="status_lanjut">Status Keputusan</label>
                        <select name="status_lanjut" id="status_lanjut" class="form-control bg-white/50 border-danger/30 text-sm" required>
                            <option value="lanjut">Lanjut (Mau Refill/Pesan Lagi)</option>
                            <option value="putus">Putus (Tarik Kembali Semua Tabung)</option>
                            <option value="negosiasi">Sedang Dihubungi / Negosiasi</option>
                            <option value="tidak_ada_respon">Tidak Ada Respon / Macet</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-danger/90 mb-1" for="catatan">Catatan Tindakan</label>
                        <textarea name="catatan" id="catatan" rows="3" class="form-control bg-white/50 border-danger/30 text-sm" placeholder="Tulis hasil hubungi client..." required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-sm bg-danger text-white w-full justify-center hover:bg-red-600">Simpan Evaluasi</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Audit Stock & Logs -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Cylinder Audit Breakdown Card -->
        <div class="glass-panel p-6 rounded-2xl shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold">Audit Saldo Tabung</h3>
            </div>
            <div class="overflow-x-auto border border-slate-200 dark:border-gray-700 rounded-xl">
                <table class="w-full text-xs sm:text-sm text-left whitespace-nowrap">
                    <thead class="bg-slate-50/50 dark:bg-gray-800/50 text-slate-500">
                        <tr>
                            <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Jenis Tabung</th>
                            <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700 text-center">Stok Awal</th>
                            <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700 text-center">Kirim (Isi)</th>
                            <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700 text-center">Kembali (Kosong)</th>
                            <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700 text-center bg-indigo-50/50 dark:bg-indigo-900/10">Stok Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($barangList as $b): ?>
                            <?php 
                            $init = isset($stokAwal[$b['id']]) ? $stokAwal[$b['id']] : 0;
                            $masuk = isset($sums[$b['id']]) ? $sums[$b['id']]['masuk'] : 0;
                            $keluar = isset($sums[$b['id']]) ? $sums[$b['id']]['keluar'] : 0;
                            $akhir = $init + $masuk - $keluar;
                            ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 font-bold text-slate-800 dark:text-gray-200"><?= htmlspecialchars($b['nama_barang']) ?></td>
                                <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-center text-slate-500 dark:text-gray-400"><?= $init ?></td>
                                <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-center text-success font-medium">+<?= $masuk ?></td>
                                <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-center text-warning font-medium">-<?= $keluar ?></td>
                                <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-center font-bold text-base bg-indigo-50/30 dark:bg-indigo-900/5">
                                    <?php if ($akhir > 0): ?>
                                        <span class="text-warning"><?= $akhir ?></span>
                                    <?php elseif ($akhir < 0): ?>
                                        <span class="text-danger"><?= $akhir ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-300 dark:text-slate-600">0</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Panel: Transactions & Evaluations -->
        <div class="glass-panel rounded-2xl shadow-sm mb-8">
            <div class="flex border-b border-slate-200 dark:border-gray-700 overflow-x-auto no-scrollbar pt-2 px-6">
                <button class="px-6 py-4 font-semibold text-sm whitespace-nowrap border-b-2 transition-colors tab-btn text-primary border-primary dark:text-primary" data-tab="tab-deliveries">Riwayat Pengiriman</button>
                <button class="px-6 py-4 font-semibold text-sm whitespace-nowrap border-b-2 transition-colors tab-btn text-slate-500 dark:text-gray-400 border-transparent hover:text-slate-700 dark:hover:text-gray-300" data-tab="tab-evaluations">Riwayat Evaluasi</button>
            </div>
            
            <!-- Deliveries Tab -->
            <div class="tab-content p-6 block" id="tab-deliveries">
                <div class="overflow-x-auto border border-slate-200 dark:border-gray-700 rounded-xl">
                    <table class="w-full text-xs sm:text-sm text-left whitespace-nowrap">
                        <thead class="bg-slate-50/50 dark:bg-gray-800/50 text-slate-500">
                            <tr>
                                <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Tanggal</th>
                                <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Barang</th>
                                <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Kirim (Isi)</th>
                                <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Kembali (Kosong)</th>
                                <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($deliveries)): ?>
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-slate-500">Belum ada riwayat pengiriman untuk mitra ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($deliveries as $d): ?>
                                    <tr class="hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5 transition-colors">
                                        <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-800 dark:text-gray-200"><?= date('d-m-Y', strtotime($d['tanggal'])) ?></td>
                                        <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 font-bold text-slate-800 dark:text-gray-200"><?= htmlspecialchars($d['nama_barang']) ?></td>
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
            
            <!-- Evaluations Tab -->
            <div class="tab-content p-6 hidden" id="tab-evaluations">
                <div class="overflow-x-auto border border-slate-200 dark:border-gray-700 rounded-xl">
                    <table class="w-full text-xs sm:text-sm text-left whitespace-nowrap">
                        <thead class="bg-slate-50/50 dark:bg-gray-800/50 text-slate-500">
                            <tr>
                                <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Tanggal Tindakan</th>
                                <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Status Keputusan</th>
                                <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Catatan Hasil Hubungi Klien</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($evalHistory)): ?>
                                <tr>
                                    <td colspan="3" class="px-5 py-8 text-center text-slate-500">Belum ada riwayat tindakan evaluasi.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($evalHistory as $ev): ?>
                                    <tr class="hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5 transition-colors">
                                        <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-800 dark:text-gray-200"><?= date('d-m-Y', strtotime($ev['tanggal'])) ?></td>
                                        <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700">
                                            <?php if ($ev['status_lanjut'] === 'lanjut'): ?>
                                                <span class="badge badge-success">Lanjut</span>
                                            <?php elseif ($ev['status_lanjut'] === 'putus'): ?>
                                                <span class="badge badge-danger">Putus</span>
                                            <?php elseif ($ev['status_lanjut'] === 'negosiasi'): ?>
                                                <span class="badge badge-warning">Negosiasi</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Tidak Respon</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-800 dark:text-gray-200"><?= htmlspecialchars($ev['catatan'] ?: '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
