<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Edit Katalog Tabung Gas</h2>
        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Ubah nama atau deskripsi spesifikasi tabung</p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>gudang?tab=cylinders" class="btn-secondary">Kembali</a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="flex items-center justify-between p-4 mb-8 rounded-xl badge-danger animate-[slideDown_0.4s_ease-out]">
        <div class="flex items-center gap-3">
            <i class="ph-fill ph-warning-circle text-xl"></i>
            <p class="font-medium"><?= htmlspecialchars($error) ?></p>
        </div>
        <button class="hover:opacity-75 transition-opacity alert-close-btn">&times;</button>
    </div>
<?php endif; ?>

<div class="glass-panel p-6 rounded-2xl shadow-sm max-w-3xl">
    <form action="<?= BASE_URL ?>gudang/edit_cylinder/<?= $barang['id'] ?>" method="POST">
        
        <div class="form-group">
            <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="nama_barang">Nama Tabung (Kode Barang)</label>
            <input type="text" id="nama_barang" name="nama_barang" class="form-control" value="<?= htmlspecialchars($barang['nama_barang']) ?>" required>
        </div>
        
        <div class="form-group mt-4">
            <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="deskripsi">Deskripsi Detail</label>
            <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($barang['deskripsi'] ?: '') ?></textarea>
        </div>

        <div class="flex justify-end gap-3 mt-8">
            <a href="<?= BASE_URL ?>gudang?tab=cylinders" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
