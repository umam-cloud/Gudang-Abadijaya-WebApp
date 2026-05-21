<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-title">
        <h2>Catatan Pengiriman Harian</h2>
        <p>Kelola pencatatan harian untuk pengiriman tabung isi ke klien dan pengembalian tabung kosong</p>
    </div>
    <div class="page-actions">
        <a href="index.php?controller=pengiriman&action=create" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:18px;height:18px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Catat Pengiriman Baru
        </a>
    </div>
</div>

<!-- Deliveries List Card -->
<div class="section-card">
    <div class="section-title">
        <h3>Jurnal Log Transaksi Pengiriman</h3>
    </div>
    
    <div class="table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kode Relasi</th>
                    <th>Nama Relasi</th>
                    <th>Jenis Tabung</th>
                    <th>Kirim (Isi)</th>
                    <th>Kembali (Kosong)</th>
                    <th>Keterangan</th>
                    <th style="text-align: center; width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($deliveries)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; color:var(--text-muted); padding: 2rem;">
                            Belum ada riwayat transaksi pengiriman. Klik tombol di atas untuk mencatat pengiriman.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($deliveries as $d): ?>
                        <tr>
                            <td><?= date('d-m-Y', strtotime($d['tanggal'])) ?></td>
                            <td><span class="badge badge-info"><strong><?= htmlspecialchars($d['kode_relasi']) ?></strong></span></td>
                            <td><strong><?= htmlspecialchars($d['nama_relasi']) ?></strong></td>
                            <td><?= htmlspecialchars($d['nama_barang']) ?></td>
                            <td style="color:var(--success); font-weight:700;">+<?= $d['jumlah_masuk'] ?></td>
                            <td style="color:var(--warning); font-weight:700;">-<?= $d['jumlah_keluar'] ?></td>
                            <td><?= htmlspecialchars($d['keterangan'] ?: '-') ?></td>
                            <td style="text-align: center;">
                                <div style="display:flex; gap:0.5rem; justify-content:center;">
                                    <a href="index.php?controller=pengiriman&action=edit&id=<?= $d['id'] ?>" class="btn btn-secondary btn-sm" style="color:var(--primary);" title="Edit Log">
                                        Edit
                                    </a>
                                    <a href="index.php?controller=pengiriman&action=delete&id=<?= $d['id'] ?>" class="btn btn-secondary btn-sm" style="color:var(--danger);" onclick="return confirm('Apakah Anda yakin ingin menghapus catatan pengiriman ini? Stok di gudang dan relasi akan otomatis dihitung kembali.');" title="Hapus">
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
        <div style="display:flex; justify-content:center; gap:0.5rem; margin-top:2rem;">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="index.php?controller=pengiriman&action=index&p=<?= $i ?>" class="btn btn-secondary btn-sm <?= $page == $i ? 'btn-primary' : '' ?>" style="padding:0.4rem 0.75rem;">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
