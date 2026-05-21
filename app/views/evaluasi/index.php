<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-title">
        <h2>Evaluasi &amp; Alert Repurchasing</h2>
        <p>Monitor relasi yang sudah lebih dari 30 hari tidak ada pergerakan transaksi</p>
    </div>
    <div class="page-actions">
        <a href="index.php?controller=relasi&action=index" class="btn btn-secondary">Semua Relasi</a>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'success_eval'): ?>
        <div class="alert-banner" style="background: var(--success-bg); border-color: rgba(13, 148, 136, 0.2); color: var(--success);">
            <div class="alert-content">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p>Log tindakan evaluasi berhasil disimpan!</p>
            </div>
            <button class="alert-close" style="color: var(--success);">&times;</button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="section-card">
    <div class="section-title">
        <h3 style="display:flex; align-items:center; gap:0.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:24px;height:24px;color:var(--danger);"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            Daftar Mitra Inaktif (Butuh Tindakan)
        </h3>
    </div>

    <div class="table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Kode Relasi</th>
                    <th>Nama Relasi / Mitra</th>
                    <th>Lokasi</th>
                    <th>Tgl Pengiriman Terakhir</th>
                    <th>Inaktif Selama</th>
                    <th style="text-align: center;">Tindakan Evaluasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inactiveClients)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:3rem; color:var(--text-muted);">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:48px;height:48px; margin:0 auto 1rem auto; opacity:0.5;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Hebat! Semua mitra aktif bertransaksi dalam 30 hari terakhir.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($inactiveClients as $c): ?>
                        <tr class="status-highlight-row">
                            <td><span class="badge badge-info"><strong><?= htmlspecialchars($c['kode_relasi']) ?></strong></span></td>
                            <td><strong><?= htmlspecialchars($c['nama_relasi']) ?></strong></td>
                            <td><?= htmlspecialchars($c['lokasi'] ?: '-') ?></td>
                            <td>
                                <?= $c['tanggal_terakhir'] ? date('d M Y', strtotime($c['tanggal_terakhir'])) : '<span style="color:var(--text-muted);">Belum pernah</span>' ?>
                            </td>
                            <td>
                                <?php if ($c['hari_sejak_pengiriman'] === null): ?>
                                    <strong style="color:var(--danger);">Belum ada transaksi</strong>
                                <?php else: ?>
                                    <strong style="color:var(--danger);"><?= $c['hari_sejak_pengiriman'] ?> Hari</strong>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="index.php?controller=relasi&action=detail&id=<?= $c['id'] ?>" class="btn btn-sm" style="background:var(--danger); color:white; border:none;">
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
