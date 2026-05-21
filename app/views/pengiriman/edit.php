<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div class="page-title">
        <h2>Edit Catatan Pengiriman</h2>
        <p>Koreksi data pengiriman – stok gudang &amp; relasi akan disesuaikan otomatis</p>
    </div>
    <div class="page-actions">
        <a href="index.php?controller=pengiriman&action=index" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert-banner">
        <div class="alert-content">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p><?= htmlspecialchars($error) ?></p>
        </div>
    </div>
<?php endif; ?>

<div class="section-card">
    <form action="index.php?controller=pengiriman&action=edit&id=<?= $pengiriman['id'] ?>" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="tanggal">Tanggal Pengiriman</label>
                <input type="date" id="tanggal" name="tanggal" class="form-control" value="<?= $pengiriman['tanggal'] ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="relasi_id">Mitra / Relasi</label>
                <select id="relasi_id" name="relasi_id" class="form-control" required>
                    <?php foreach ($clients as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $c['id'] == $pengiriman['relasi_id'] ? 'selected' : '' ?>>
                            [<?= htmlspecialchars($c['kode_relasi']) ?>] <?= htmlspecialchars($c['nama_relasi']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-grid" style="margin-top:0.5rem;">
            <div class="form-group">
                <label class="form-label" for="barang_id">Jenis Tabung Gas</label>
                <select id="barang_id" name="barang_id" class="form-control" required>
                    <?php foreach ($barangList as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= $b['id'] == $pengiriman['barang_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['nama_barang']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="jumlah_masuk">KIRIM / ISI</label>
                <input type="number" id="jumlah_masuk" name="jumlah_masuk" class="form-control" min="0" value="<?= $pengiriman['jumlah_masuk'] ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="jumlah_keluar">KEMBALI / KOSONG</label>
                <input type="number" id="jumlah_keluar" name="jumlah_keluar" class="form-control" min="0" value="<?= $pengiriman['jumlah_keluar'] ?>" required>
            </div>
        </div>

        <div class="form-group" style="margin-top:0.5rem;">
            <label class="form-label" for="keterangan">Keterangan</label>
            <input type="text" id="keterangan" name="keterangan" class="form-control" value="<?= htmlspecialchars($pengiriman['keterangan'] ?? '') ?>">
        </div>

        <div style="display:flex; justify-content:flex-end; gap:0.75rem; margin-top:2rem;">
            <a href="index.php?controller=pengiriman&action=index" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
