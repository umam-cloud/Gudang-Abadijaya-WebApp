<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-title">
        <h2>Manajemen Stok Gudang</h2>
        <p>Pantau persediaan tabung di gudang (Ready &amp; Kosong), riwayat transaksi, dan manajemen jenis tabung gas</p>
    </div>
    <div class="page-actions">
        <a href="index.php?controller=gudang&action=adjust" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:18px;height:18px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Penyesuaian Stok Gudang
        </a>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'success_adjust'): ?>
        <div class="alert-banner" style="background: var(--success-bg); border-color: rgba(13, 148, 136, 0.2); color: var(--success);">
            <div class="alert-content">
                <p>Data penyesuaian stok berhasil dicatat!</p>
            </div>
            <button class="alert-close" style="color: var(--success);">&times;</button>
        </div>
    <?php elseif ($_GET['msg'] === 'success_cylinder_create'): ?>
        <div class="alert-banner" style="background: var(--success-bg); border-color: rgba(13, 148, 136, 0.2); color: var(--success);">
            <div class="alert-content">
                <p>Jenis tabung baru berhasil ditambahkan!</p>
            </div>
            <button class="alert-close" style="color: var(--success);">&times;</button>
        </div>
    <?php elseif ($_GET['msg'] === 'error_cylinder_exists'): ?>
        <div class="alert-banner">
            <div class="alert-content">
                <p>Gagal menambahkan jenis tabung. Nama tersebut mungkin sudah ada.</p>
            </div>
            <button class="alert-close">&times;</button>
        </div>
    <?php elseif ($_GET['msg'] === 'success_cylinder_update'): ?>
        <div class="alert-banner" style="background: var(--success-bg); border-color: rgba(13, 148, 136, 0.2); color: var(--success);">
            <div class="alert-content">
                <p>Informasi jenis tabung berhasil diperbarui!</p>
            </div>
            <button class="alert-close" style="color: var(--success);">&times;</button>
        </div>
    <?php elseif ($_GET['msg'] === 'success_cylinder_delete'): ?>
        <div class="alert-banner" style="background: rgba(99, 102, 241, 0.08); border-color: rgba(99, 102, 241, 0.2); color: var(--primary);">
            <div class="alert-content">
                <p>Jenis tabung berhasil dihapus!</p>
            </div>
            <button class="alert-close" style="color: var(--primary);">&times;</button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Tabs -->
<div class="section-card" style="padding-top: 1rem;">
    <div class="tab-menu">
        <button class="tab-btn <?= (!isset($_GET['tab']) || $_GET['tab'] == 'stocks') ? 'active' : '' ?>" data-tab="tab-stocks">Ketersediaan Saat Ini</button>
        <button class="tab-btn <?= (isset($_GET['tab']) && $_GET['tab'] == 'transactions') ? 'active' : '' ?>" data-tab="tab-transactions">Riwayat Transaksi Gudang</button>
        <button class="tab-btn <?= (isset($_GET['tab']) && $_GET['tab'] == 'cylinders') ? 'active' : '' ?>" data-tab="tab-cylinders">Daftar Jenis Tabung (Katalog)</button>
    </div>

    <!-- Tab 1: Current Stock Matrix -->
    <div class="tab-content <?= (!isset($_GET['tab']) || $_GET['tab'] == 'stocks') ? 'active' : '' ?>" id="tab-stocks">
        <div class="inventory-grid">
            <?php if (empty($warehouseStocks)): ?>
                <div style="grid-column: 1/-1; color: var(--text-muted); text-align: center; padding: 2rem;">
                    Belum ada jenis tabung terdaftar. Silakan tambahkan melalui tab "Daftar Jenis Tabung".
                </div>
            <?php else: ?>
                <?php foreach ($warehouseStocks as $w): ?>
                    <div class="cylinder-card">
                        <div class="cylinder-info">
                            <h4><?= htmlspecialchars($w['nama_barang']) ?></h4>
                            <p><?= htmlspecialchars($w['deskripsi'] ?: 'Tidak ada deskripsi') ?></p>
                        </div>
                        <div class="cylinder-stats">
                            <div class="sub-stat ready">
                                <span>Ready / Full</span>
                                <span><?= $w['stok_ready'] ?></span>
                            </div>
                            <div class="sub-stat empty">
                                <span>Kosong</span>
                                <span><?= $w['stok_kosong'] ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab 2: Transaction History (Refills, Sales, Purchases) -->
    <div class="tab-content <?= (isset($_GET['tab']) && $_GET['tab'] == 'transactions') ? 'active' : '' ?>" id="tab-transactions">
        <div class="table-wrapper">
            <table class="custom-table" style="font-size:0.85rem;">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis Tabung</th>
                        <th>Jenis Transaksi</th>
                        <th>Perubahan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; color:var(--text-muted); padding:1.5rem;">Belum ada riwayat transaksi gudang.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $t): ?>
                            <tr>
                                <td><?= date('d-m-Y', strtotime($t['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($t['nama_barang']) ?></td>
                                <td>
                                    <?php
                                    if ($t['jenis_transaksi'] == 'pembelian') {
                                        echo '<span class="badge badge-success">Baru/Beli</span>';
                                    } elseif ($t['jenis_transaksi'] == 'refill') {
                                        echo '<span class="badge badge-info">Refill / Isi Ulang</span>';
                                    } elseif ($t['jenis_transaksi'] == 'penjualan') {
                                        echo '<span class="badge badge-danger">Penjualan/Pemusnahan</span>';
                                    } else {
                                        echo '<span class="badge" style="background:#e2e8f0;color:#475569;">' . htmlspecialchars($t['jenis_transaksi']) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($t['jumlah_perubahan'] > 0): ?>
                                        <span style="color:var(--success); font-weight:700;">+<?= $t['jumlah_perubahan'] ?></span>
                                    <?php else: ?>
                                        <span style="color:var(--danger); font-weight:700;"><?= $t['jumlah_perubahan'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($t['keterangan'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
            <div style="display:flex; justify-content:center; gap:0.5rem; margin-top:2rem;">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="index.php?controller=gudang&action=index&tab=transactions&p=<?= $i ?>" class="btn btn-secondary btn-sm <?= $page == $i ? 'btn-primary' : '' ?>" style="padding:0.4rem 0.75rem;">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tab 3: Catalog & Cylinder Type Management -->
    <div class="tab-content <?= (isset($_GET['tab']) && $_GET['tab'] == 'cylinders') ? 'active' : '' ?>" id="tab-cylinders">
        
        <div style="display: flex; gap: 2rem;">
            <!-- Cylinder List -->
            <div style="flex: 2;">
                <div class="table-wrapper">
                    <table class="custom-table" style="font-size:0.85rem;">
                        <thead>
                            <tr>
                                <th># ID</th>
                                <th>Nama Barang / Tabung</th>
                                <th>Deskripsi</th>
                                <th style="text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($barangList as $b): ?>
                                <tr>
                                    <td><span style="color:var(--text-muted);">#<?= $b['id'] ?></span></td>
                                    <td><strong><?= htmlspecialchars($b['nama_barang']) ?></strong></td>
                                    <td><?= htmlspecialchars($b['deskripsi'] ?: '-') ?></td>
                                    <td style="text-align: center;">
                                        <a href="index.php?controller=gudang&action=edit_cylinder&id=<?= $b['id'] ?>" class="btn btn-secondary btn-sm" style="color:var(--primary); margin-right: 0.25rem;">Edit</a>
                                        <a href="index.php?controller=gudang&action=delete_cylinder&id=<?= $b['id'] ?>" class="btn btn-secondary btn-sm" style="color:var(--danger);" onclick="return confirm('Peringatan Ekstrem: Menghapus jenis tabung akan MENGHAPUS SEMUA transaksi, riwayat pengiriman, dan log stok terkait tabung ini! Lanjutkan?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Create Form -->
            <div style="flex: 1;">
                <div style="background: rgba(99,102,241,0.03); border: 1px solid rgba(99,102,241,0.1); border-radius: 12px; padding: 1.5rem;">
                    <h4 style="margin-bottom: 1.25rem;">Tambah Jenis Tabung Baru</h4>
                    <form action="index.php?controller=gudang&action=create_cylinder" method="POST">
                        <div class="form-group">
                            <label class="form-label" for="nama_barang">Nama Tabung (Kode)</label>
                            <input type="text" id="nama_barang" name="nama_barang" class="form-control" placeholder="Contoh: OXY 6m3" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="deskripsi">Deskripsi</label>
                            <input type="text" id="deskripsi" name="deskripsi" class="form-control" placeholder="Opsional (mis: Oksigen Medis Besar)">
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Simpan Tabung Baru</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
