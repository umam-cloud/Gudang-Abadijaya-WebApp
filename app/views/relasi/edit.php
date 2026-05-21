<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-title">
        <h2>Edit Mitra / Relasi</h2>
        <p>Ubah profil mitra dan sesuaikan saldo awal tabung mereka</p>
    </div>
    <div class="page-actions">
        <a href="index.php?controller=relasi&action=index" class="btn btn-secondary">Kembali</a>
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
    <form action="index.php?controller=relasi&action=edit&id=<?= $relasi['id'] ?>" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="kode_relasi">Kode Relasi</label>
                <input type="text" id="kode_relasi" name="kode_relasi" class="form-control" value="<?= htmlspecialchars($relasi['kode_relasi']) ?>" required>
                <span class="form-help">Kode unik pembeda relasi</span>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="nama_relasi">Nama Relasi / Toko</label>
                <input type="text" id="nama_relasi" name="nama_relasi" class="form-control" value="<?= htmlspecialchars($relasi['nama_relasi']) ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="lokasi">Lokasi / Alamat</label>
            <input type="text" id="lokasi" name="lokasi" class="form-control" value="<?= htmlspecialchars($relasi['lokasi']) ?>">
        </div>

        <!-- Initial Cylinder Stock Inputs -->
        <div class="stock-init-section">
            <h4>Stok Awal Tabung (MP Relasi)</h4>
            <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom:1rem;">
                Sesuaikan saldo awal tabung yang dipinjam saat mendaftar.
            </p>
            
            <div class="form-grid">
                <?php foreach ($barangList as $b): ?>
                    <?php 
                    $val = isset($stokAwal[$b['id']]) ? $stokAwal[$b['id']] : 0;
                    ?>
                    <div class="form-group" style="margin-bottom:0.5rem;">
                        <label class="form-label" for="stok_awal_<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_barang']) ?></label>
                        <input type="number" id="stok_awal_<?= $b['id'] ?>" name="stok_awal_<?= $b['id'] ?>" class="form-control" value="<?= $val ?>" step="1">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:0.75rem; margin-top:2rem;">
            <a href="index.php?controller=relasi&action=index" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
