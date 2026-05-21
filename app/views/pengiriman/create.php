<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-title">
        <h2>Catat Pengiriman Harian</h2>
        <p>Input pengiriman tabung isi baru dan penerimaan tabung kosong kembali dari mitra</p>
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
    <form action="index.php?controller=pengiriman&action=create" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="tanggal">Tanggal Pengiriman</label>
                <input type="date" id="tanggal" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="relasi_id">Mitra / Relasi Pelanggan</label>
                <select id="relasi_id" name="relasi_id" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Mitra --</option>
                    <?php foreach ($clients as $c): ?>
                        <option value="<?= $c['id'] ?>">[<?= htmlspecialchars($c['kode_relasi']) ?>] <?= htmlspecialchars($c['nama_relasi']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-grid" style="margin-top:0.5rem;">
            <div class="form-group">
                <label class="form-label" for="barang_id">Jenis Tabung Gas</label>
                <select id="barang_id" name="barang_id" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Jenis Tabung --</option>
                    <?php foreach ($barangList as $b): ?>
                        <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_barang']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="jumlah_masuk">KIRIM / ISI (Jumlah Tabung Dikirim)</label>
                <input type="number" id="jumlah_masuk" name="jumlah_masuk" class="form-control" min="0" value="0" required>
                <span class="form-help">Mengurangi stok READY di gudang Anda</span>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="jumlah_keluar">KEMBALI / KOSONG (Jumlah Tabung Diterima)</label>
                <input type="number" id="jumlah_keluar" name="jumlah_keluar" class="form-control" min="0" value="0" required>
                <span class="form-help">Menambah stok KOSONG di gudang Anda</span>
            </div>
        </div>

        <div class="form-group" style="margin-top:0.5rem;">
            <label class="form-label" for="keterangan">Keterangan / Catatan Tambahan</label>
            <input type="text" id="keterangan" name="keterangan" class="form-control" placeholder="Contoh: Refill CO2 5KG, tabung dipinjam, dll.">
        </div>

        <div style="display:flex; justify-content:flex-end; gap:0.75rem; margin-top:2rem;">
            <a href="index.php?controller=pengiriman&action=index" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" <?= (empty($clients) || empty($barangList)) ? 'disabled' : '' ?>>Simpan Catatan</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
