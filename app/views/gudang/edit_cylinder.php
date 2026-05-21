<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-title">
        <h2>Edit Katalog Tabung Gas</h2>
        <p>Ubah nama atau deskripsi spesifikasi tabung</p>
    </div>
    <div class="page-actions">
        <a href="index.php?controller=gudang&action=index&tab=cylinders" class="btn btn-secondary">Kembali</a>
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
    <form action="index.php?controller=gudang&action=edit_cylinder&id=<?= $barang['id'] ?>" method="POST">
        
        <div class="form-group">
            <label class="form-label" for="nama_barang">Nama Tabung (Kode Barang)</label>
            <input type="text" id="nama_barang" name="nama_barang" class="form-control" value="<?= htmlspecialchars($barang['nama_barang']) ?>" required>
        </div>
        
        <div class="form-group" style="margin-top: 1rem;">
            <label class="form-label" for="deskripsi">Deskripsi Detail</label>
            <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($barang['deskripsi'] ?: '') ?></textarea>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:0.75rem; margin-top:2rem;">
            <a href="index.php?controller=gudang&action=index&tab=cylinders" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
