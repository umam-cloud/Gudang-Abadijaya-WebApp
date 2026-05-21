<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-title">
        <h2>Mitra &amp; Relasi Pelanggan</h2>
        <p>Monitor stok tabung yang dipinjam oleh masing-masing mitra di setiap lokasi</p>
    </div>
    <div class="page-actions">
        <a href="index.php?controller=relasi&action=create" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:18px;height:18px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Mitra Baru
        </a>
    </div>
</div>

<!-- Stock Matrix Table Card -->
<div class="section-card">
    <div class="section-title">
        <h3>Matriks Saldo Tabung Relasi</h3>
        <div style="font-size: 0.8rem; color: var(--text-muted);">
            *Angka menunjukkan jumlah tabung yang dipinjam (MP) di lokasi relasi
        </div>
    </div>
    
    <div class="table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Relasi</th>
                    <th>Lokasi</th>
                    <?php foreach ($barangList as $b): ?>
                        <th style="text-align: center;"><?= htmlspecialchars($b['nama_barang']) ?></th>
                    <?php endforeach; ?>
                    <th style="text-align: center; width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clients)): ?>
                    <tr>
                        <td colspan="<?= count($barangList) + 4 ?>" style="text-align:center; color:var(--text-muted); padding: 2rem;">
                            Belum ada data relasi. Klik tombol di atas untuk menambah mitra baru.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($clients as $c): ?>
                        <tr>
                            <td><span class="badge badge-info"><strong><?= htmlspecialchars($c['kode_relasi']) ?></strong></span></td>
                            <td><strong><?= htmlspecialchars($c['nama_relasi']) ?></strong></td>
                            <td><?= htmlspecialchars($c['lokasi'] ?: '-') ?></td>
                            
                            <!-- Dynamic Cylinder Stock columns -->
                            <?php foreach ($barangList as $b): ?>
                                <?php 
                                // Find stock for this specific barang in client stocks
                                $stockVal = 0;
                                foreach ($c['stocks'] as $st) {
                                    if ($st['barang_id'] == $b['id']) {
                                        $stockVal = $st['stok_akhir'];
                                        break;
                                    }
                                }
                                ?>
                                <td style="text-align: center; font-weight: 700; font-size: 1rem;">
                                    <?php if ($stockVal > 0): ?>
                                        <span style="color: var(--warning);"><?= $stockVal ?></span>
                                    <?php elseif ($stockVal < 0): ?>
                                        <span style="color: var(--danger);"><?= $stockVal ?></span>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); opacity: 0.5;">0</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            
                            <!-- Action Links -->
                            <td style="text-align: center;">
                                <div style="display:flex; gap:0.5rem; justify-content:center;">
                                    <a href="index.php?controller=relasi&action=detail&id=<?= $c['id'] ?>" class="btn btn-secondary btn-sm" title="Detail Profil &amp; History">
                                        Detail
                                    </a>
                                    <a href="index.php?controller=relasi&action=edit&id=<?= $c['id'] ?>" class="btn btn-secondary btn-sm" style="color:var(--primary);" title="Edit Mitra">
                                        Edit
                                    </a>
                                    <a href="index.php?controller=relasi&action=delete&id=<?= $c['id'] ?>" class="btn btn-secondary btn-sm" style="color:var(--danger);" onclick="return confirm('Apakah Anda yakin ingin menghapus relasi <?= htmlspecialchars($c['nama_relasi']) ?>? Semua data transaksi dan stok terkait akan terhapus.');" title="Hapus">
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
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
