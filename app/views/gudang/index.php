<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Manajemen Stok Gudang</h2>
        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Pantau persediaan tabung di gudang (Ready &amp; Kosong), riwayat transaksi, dan manajemen jenis tabung gas</p>
    </div>
    <div>
        <a href="index.php?controller=gudang&action=adjust" class="btn-primary">
            <i class="ph-bold ph-sliders-horizontal text-base"></i>
            Penyesuaian Stok Gudang
        </a>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'success_adjust'): ?>
        <div class="flex items-center justify-between p-4 mb-8 rounded-xl badge-success animate-[slideDown_0.4s_ease-out]">
            <p class="font-medium">Data penyesuaian stok berhasil dicatat!</p>
            <button class="hover:opacity-75 transition-opacity alert-close-btn">&times;</button>
        </div>
    <?php elseif ($_GET['msg'] === 'success_cylinder_create'): ?>
        <div class="flex items-center justify-between p-4 mb-8 rounded-xl badge-success animate-[slideDown_0.4s_ease-out]">
            <p class="font-medium">Jenis tabung baru berhasil ditambahkan!</p>
            <button class="hover:opacity-75 transition-opacity alert-close-btn">&times;</button>
        </div>
    <?php elseif ($_GET['msg'] === 'error_cylinder_exists'): ?>
        <div class="flex items-center justify-between p-4 mb-8 rounded-xl badge-danger animate-[slideDown_0.4s_ease-out]">
            <p class="font-medium">Gagal menambahkan jenis tabung. Nama tersebut mungkin sudah ada.</p>
            <button class="hover:opacity-75 transition-opacity alert-close-btn">&times;</button>
        </div>
    <?php elseif ($_GET['msg'] === 'success_cylinder_update'): ?>
        <div class="flex items-center justify-between p-4 mb-8 rounded-xl badge-success animate-[slideDown_0.4s_ease-out]">
            <p class="font-medium">Informasi jenis tabung berhasil diperbarui!</p>
            <button class="hover:opacity-75 transition-opacity alert-close-btn">&times;</button>
        </div>
    <?php elseif ($_GET['msg'] === 'success_cylinder_delete'): ?>
        <div class="flex items-center justify-between p-4 mb-8 rounded-xl badge-info animate-[slideDown_0.4s_ease-out]">
            <p class="font-medium">Jenis tabung berhasil dihapus!</p>
            <button class="hover:opacity-75 transition-opacity alert-close-btn">&times;</button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Tabs -->
<div class="glass-panel rounded-2xl shadow-sm mb-8">
    <div class="flex border-b border-slate-200 dark:border-gray-700 overflow-x-auto no-scrollbar pt-2 px-6">
        <button class="px-6 py-4 font-semibold text-sm whitespace-nowrap border-b-2 transition-colors tab-btn <?= (!isset($_GET['tab']) || $_GET['tab'] == 'stocks') ? 'text-primary border-primary dark:text-primary' : 'text-slate-500 dark:text-gray-400 border-transparent hover:text-slate-700 dark:hover:text-gray-300' ?>" data-tab="tab-stocks">Ketersediaan Saat Ini</button>
        <button class="px-6 py-4 font-semibold text-sm whitespace-nowrap border-b-2 transition-colors tab-btn <?= (isset($_GET['tab']) && $_GET['tab'] == 'transactions') ? 'text-primary border-primary dark:text-primary' : 'text-slate-500 dark:text-gray-400 border-transparent hover:text-slate-700 dark:hover:text-gray-300' ?>" data-tab="tab-transactions">Riwayat Transaksi Gudang</button>
        <button class="px-6 py-4 font-semibold text-sm whitespace-nowrap border-b-2 transition-colors tab-btn <?= (isset($_GET['tab']) && $_GET['tab'] == 'cylinders') ? 'text-primary border-primary dark:text-primary' : 'text-slate-500 dark:text-gray-400 border-transparent hover:text-slate-700 dark:hover:text-gray-300' ?>" data-tab="tab-cylinders">Daftar Jenis Tabung (Katalog)</button>
    </div>

    <!-- Tab 1: Current Stock Matrix -->
    <div class="tab-content p-6 <?= (!isset($_GET['tab']) || $_GET['tab'] == 'stocks') ? '' : 'hidden' ?>" id="tab-stocks">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-800 dark:text-gray-100">Ketersediaan Saat Ini</h3>
            <a href="index.php?controller=gudang&action=export_stok" class="btn-secondary btn-sm !text-success border border-success/20 hover:!bg-success/10" target="_blank">
                <i class="ph-bold ph-file-csv text-base"></i> Export Excel Stok
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($warehouseStocks)): ?>
                <div class="col-span-full text-center text-slate-500 py-12">
                    Belum ada jenis tabung terdaftar. Silakan tambahkan melalui tab "Daftar Jenis Tabung".
                </div>
            <?php else: ?>
                <?php foreach ($warehouseStocks as $w): ?>
                    <div class="border border-slate-200 dark:border-gray-700 rounded-xl p-5 hover:border-primary dark:hover:border-primary transition-colors bg-white dark:bg-gray-800">
                        <div class="mb-4">
                            <h4 class="font-bold text-lg text-slate-800 dark:text-gray-100"><?= htmlspecialchars($w['nama_barang']) ?></h4>
                            <p class="text-sm text-slate-500 dark:text-gray-400 mt-1"><?= htmlspecialchars($w['deskripsi'] ?: 'Tidak ada deskripsi') ?></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-success-bg/50 dark:bg-success-bg p-3 rounded-lg flex flex-col items-center">
                                <span class="text-[10px] uppercase font-bold text-success/70 tracking-wider mb-1">Ready / Full</span>
                                <span class="text-2xl font-black text-success"><?= $w['stok_ready'] ?></span>
                            </div>
                            <div class="bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg flex flex-col items-center">
                                <span class="text-[10px] uppercase font-bold text-amber-500/70 tracking-wider mb-1">Kosong</span>
                                <span class="text-2xl font-black text-amber-500"><?= $w['stok_kosong'] ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab 2: Transaction History (Refills, Sales, Purchases) -->
    <div class="tab-content p-6 <?= (isset($_GET['tab']) && $_GET['tab'] == 'transactions') ? '' : 'hidden' ?>" id="tab-transactions">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-800 dark:text-gray-100">Riwayat Transaksi Gudang</h3>
            <a href="index.php?controller=gudang&action=export_transaksi" class="btn-secondary btn-sm !text-success border border-success/20 hover:!bg-success/10" target="_blank">
                <i class="ph-bold ph-file-csv text-base"></i> Export Excel Riwayat
            </a>
        </div>
        <div class="overflow-x-auto border border-slate-200 dark:border-gray-700 rounded-xl">
            <table class="w-full text-xs sm:text-sm text-left whitespace-nowrap">
                <thead class="bg-slate-50/50 dark:bg-gray-800/50 text-slate-500">
                    <tr>
                        <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Tanggal</th>
                        <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Jenis Tabung</th>
                        <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Jenis Transaksi</th>
                        <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Perubahan</th>
                        <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-500">Belum ada riwayat transaksi gudang.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $t): ?>
                            <tr class="hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5 transition-colors">
                                <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-800 dark:text-gray-200"><?= date('d-m-Y', strtotime($t['tanggal'])) ?></td>
                                <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 font-bold text-slate-800 dark:text-gray-200"><?= htmlspecialchars($t['nama_barang']) ?></td>
                                <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700">
                                    <?php
                                    if ($t['tipe_transaksi'] == 'pembelian' || $t['tipe_transaksi'] == 'beli_baru') {
                                        echo '<span class="badge badge-success">Baru/Beli</span>';
                                    } elseif ($t['tipe_transaksi'] == 'refill') {
                                        echo '<span class="badge badge-info">Refill / Isi Ulang</span>';
                                    } elseif ($t['tipe_transaksi'] == 'penjualan' || $t['tipe_transaksi'] == 'jual_rusak') {
                                        echo '<span class="badge badge-danger">Penjualan/Pemusnahan</span>';
                                    } else {
                                        echo '<span class="badge bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 border-none">' . htmlspecialchars($t['tipe_transaksi']) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700">
                                    <?php if ($t['jumlah'] > 0): ?>
                                        <span class="text-success font-bold">+<?= $t['jumlah'] ?></span>
                                    <?php else: ?>
                                        <span class="text-danger font-bold"><?= $t['jumlah'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-800 dark:text-gray-200"><?= htmlspecialchars($t['keterangan'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
            <div class="flex justify-center gap-2 mt-8">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="index.php?controller=gudang&action=index&tab=transactions&p=<?= $i ?>" class="btn-sm <?= $page == $i ? 'btn-primary' : 'bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tab 3: Catalog & Cylinder Type Management -->
    <div class="tab-content p-6 <?= (isset($_GET['tab']) && $_GET['tab'] == 'cylinders') ? '' : 'hidden' ?>" id="tab-cylinders">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cylinder List -->
            <div class="lg:col-span-2">
                <div class="overflow-x-auto border border-slate-200 dark:border-gray-700 rounded-xl">
                    <table class="w-full text-xs sm:text-sm text-left whitespace-nowrap">
                        <thead class="bg-slate-50/50 dark:bg-gray-800/50 text-slate-500">
                            <tr>
                                <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700"># ID</th>
                                <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Nama Barang / Tabung</th>
                                <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700">Deskripsi</th>
                                <th class="px-5 py-4 font-semibold border-b border-slate-200 dark:border-gray-700 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($barangList as $b): ?>
                                <tr class="hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5 transition-colors">
                                    <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-400">#<?= $b['id'] ?></td>
                                    <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 font-bold text-slate-800 dark:text-gray-200"><?= htmlspecialchars($b['nama_barang']) ?></td>
                                    <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-slate-600 dark:text-gray-400"><?= htmlspecialchars($b['deskripsi'] ?: '-') ?></td>
                                    <td class="px-5 py-4 border-b border-slate-200 dark:border-gray-700 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="index.php?controller=gudang&action=edit_cylinder&id=<?= $b['id'] ?>" class="btn-sm bg-indigo-50 text-primary dark:bg-indigo-500/20 hover:bg-indigo-100 transition-colors inline-block no-underline">Edit</a>
                                            <a href="index.php?controller=gudang&action=delete_cylinder&id=<?= $b['id'] ?>" class="btn-sm bg-red-50 text-danger dark:bg-red-500/20 hover:bg-red-100 transition-colors inline-block no-underline" onclick="return confirmAction(event, 'Peringatan Ekstrem: Menghapus jenis tabung akan MENGHAPUS SEMUA transaksi, riwayat pengiriman, dan log stok terkait tabung ini! Lanjutkan?', this.href);">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Create Form -->
            <div class="lg:col-span-1">
                <div class="bg-indigo-50/30 dark:bg-indigo-500/5 border border-indigo-100 dark:border-indigo-500/10 rounded-2xl p-6">
                    <h4 class="font-bold text-lg mb-6">Tambah Jenis Tabung Baru</h4>
                    <form action="index.php?controller=gudang&action=create_cylinder" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="nama_barang">Nama Tabung (Kode)</label>
                            <input type="text" id="nama_barang" name="nama_barang" class="form-control" placeholder="Contoh: OXY 6m3" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="deskripsi">Deskripsi</label>
                            <input type="text" id="deskripsi" name="deskripsi" class="form-control" placeholder="Opsional (mis: Oksigen Medis Besar)">
                        </div>
                        <button type="submit" class="btn-primary w-full justify-center mt-2">Simpan Tabung Baru</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
