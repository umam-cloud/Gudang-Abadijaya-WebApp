<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-title">
        <h2>Profil Mitra &amp; Detail Saldo</h2>
        <p>Lihat detail inventaris tabung, riwayat pengiriman, dan log tindakan evaluasi</p>
    </div>
    <div class="page-actions">
        <a href="index.php?controller=relasi&action=index" class="btn btn-secondary">Kembali</a>
        <a href="index.php?controller=relasi&action=edit&id=<?= $relasi['id'] ?>" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            Edit Profil
        </a>
    </div>
</div>

<div class="profile-grid">
    <!-- Left Column: Profile Card & Summary -->
    <div>
        <div class="profile-card">
            <div class="profile-avatar-large">
                <?= strtoupper(substr($relasi['nama_relasi'], 0, 1)) ?>
            </div>
            
            <div class="profile-details">
                <h3><?= htmlspecialchars($relasi['nama_relasi']) ?></h3>
                <span class="badge badge-info" style="margin-top: 0.25rem;">Kode: <?= htmlspecialchars($relasi['kode_relasi']) ?></span>
                <p style="margin-top: 0.5rem;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25s-7.5-4.108-7.5-11.25gA7.5 7.5 0 1119.5 10.5z" /></svg> <?= htmlspecialchars($relasi['lokasi'] ?: 'Tidak ada alamat') ?></p>
            </div>
            
            <div class="info-list">
                <div class="info-item">
                    <span>Pengiriman Terakhir:</span>
                    <span><?= $last_delivery['tanggal_terakhir'] ? date('d-m-Y', strtotime($last_delivery['tanggal_terakhir'])) : '<span style="color:var(--text-muted);">Belum pernah</span>' ?></span>
                </div>
                <div class="info-item">
                    <span>Waktu Sejak Pengiriman:</span>
                    <span>
                        <?php if ($last_delivery['hari_sejak_pengiriman'] === null): ?>
                            <span class="badge badge-warning">Baru / Inaktif</span>
                        <?php else: ?>
                            <strong><?= $last_delivery['hari_sejak_pengiriman'] ?> Hari</strong>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-item" style="border-bottom:none; padding-bottom:0;">
                    <span>Status Alert:</span>
                    <span>
                        <?php if ($last_delivery['hari_sejak_pengiriman'] === null || $last_delivery['hari_sejak_pengiriman'] > 30): ?>
                            <span class="badge badge-danger" style="animation:pulse 2s infinite;">Peringatan Inaktif (>30 Hari)</span>
                        <?php else: ?>
                            <span class="badge badge-success">Mitra Aktif</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Evaluation Action Box -->
        <?php if ($last_delivery['hari_sejak_pengiriman'] === null || $last_delivery['hari_sejak_pengiriman'] > 30): ?>
            <div class="section-card" style="margin-top: 1.5rem; border-color: rgba(239, 68, 68, 0.2); background: rgba(239, 68, 68, 0.02);">
                <h4 style="margin-bottom: 0.5rem; color: var(--danger); display:flex; align-items:center; gap:0.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:20px;height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                    Evaluasi Repurchase
                </h4>
                <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 1rem;">
                    Klien sudah tidak memesan selama lebih dari 1 bulan. Catat status negosiasi atau keputusan lanjut/putus di sini.
                </p>
                <form action="index.php?controller=evaluasi&action=create" method="POST">
                    <input type="hidden" name="relasi_id" value="<?= $relasi['id'] ?>">
                    <input type="hidden" name="tanggal" value="<?= date('Y-m-d') ?>">
                    
                    <div class="form-group">
                        <label class="form-label" for="status_lanjut" style="font-size:0.8rem;">Status Keputusan</label>
                        <select name="status_lanjut" id="status_lanjut" class="form-control" style="padding:0.5rem 0.75rem; font-size:0.85rem;" required>
                            <option value="lanjut">Lanjut (Mau Refill/Pesan Lagi)</option>
                            <option value="putus">Putus (Tarik Kembali Semua Tabung)</option>
                            <option value="negosiasi">Sedang Dihubungi / Negosiasi</option>
                            <option value="tidak_ada_respon">Tidak Ada Respon / Macet</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="catatan" style="font-size:0.8rem;">Catatan Tindakan</label>
                        <textarea name="catatan" id="catatan" rows="3" class="form-control" style="font-size:0.85rem;" placeholder="Tulis hasil hubungi client..." required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-sm" style="width:100%; justify-content:center;">Simpan Evaluasi</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Audit Stock & Logs -->
    <div style="display:flex; flex-direction:column; gap:1.5rem;">
        <!-- Cylinder Audit Breakdown Card -->
        <div class="section-card">
            <div class="section-title">
                <h3>Audit Saldo Tabung</h3>
            </div>
            <div class="table-wrapper">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Jenis Tabung</th>
                            <th style="text-align: center;">Stok Awal</th>
                            <th style="text-align: center;">Kirim (Isi)</th>
                            <th style="text-align: center;">Kembali (Kosong)</th>
                            <th style="text-align: center; background: rgba(99,102,241,0.05);">Stok Akhir</th>
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
                            <tr>
                                <td><strong><?= htmlspecialchars($b['nama_barang']) ?></strong></td>
                                <td style="text-align: center; color:var(--text-secondary);"><?= $init ?></td>
                                <td style="text-align: center; color:var(--success); font-weight:500;">+<?= $masuk ?></td>
                                <td style="text-align: center; color:var(--warning); font-weight:500;">-<?= $keluar ?></td>
                                <td style="text-align: center; font-weight: 700; font-size: 1rem; background: rgba(99,102,241,0.02);">
                                    <?php if ($akhir > 0): ?>
                                        <span style="color: var(--warning);"><?= $akhir ?></span>
                                    <?php elseif ($akhir < 0): ?>
                                        <span style="color: var(--danger);"><?= $akhir ?></span>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); opacity: 0.5;">0</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Panel: Transactions & Evaluations -->
        <div class="section-card">
            <div class="tab-menu">
                <button class="tab-btn active" data-tab="tab-deliveries">Riwayat Pengiriman</button>
                <button class="tab-btn" data-tab="tab-evaluations">Riwayat Evaluasi</button>
            </div>
            
            <!-- Deliveries Tab -->
            <div class="tab-content active" id="tab-deliveries">
                <div class="table-wrapper">
                    <table class="custom-table" style="font-size:0.85rem;">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Barang</th>
                                <th>Kirim (Isi)</th>
                                <th>Kembali (Kosong)</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($deliveries)): ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; color:var(--text-muted); padding:1.5rem;">Belum ada riwayat pengiriman untuk mitra ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($deliveries as $d): ?>
                                    <tr>
                                        <td><?= date('d-m-Y', strtotime($d['tanggal'])) ?></td>
                                        <td><?= htmlspecialchars($d['nama_barang']) ?></td>
                                        <td style="color:var(--success); font-weight:600;">+<?= $d['jumlah_masuk'] ?></td>
                                        <td style="color:var(--warning); font-weight:600;">-<?= $d['jumlah_keluar'] ?></td>
                                        <td><?= htmlspecialchars($d['keterangan'] ?: '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Evaluations Tab -->
            <div class="tab-content" id="tab-evaluations">
                <div class="table-wrapper">
                    <table class="custom-table" style="font-size:0.85rem;">
                        <thead>
                            <tr>
                                <th>Tanggal Tindakan</th>
                                <th>Status Keputusan</th>
                                <th>Catatan Hasil Hubungi Klien</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($evalHistory)): ?>
                                <tr>
                                    <td colspan="3" style="text-align:center; color:var(--text-muted); padding:1.5rem;">Belum ada riwayat tindakan evaluasi.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($evalHistory as $ev): ?>
                                    <tr>
                                        <td><?= date('d-m-Y', strtotime($ev['tanggal'])) ?></td>
                                        <td>
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
                                        <td><?= htmlspecialchars($ev['catatan'] ?: '-') ?></td>
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
