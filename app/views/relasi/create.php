<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Tambah Mitra / Relasi</h2>
        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Daftarkan mitra baru beserta saldo awal tabung yang sedang mereka bawa</p>
    </div>
    <div>
        <a href="index.php?controller=relasi&action=index" class="btn-secondary">Kembali</a>
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

<div class="glass-panel p-6 rounded-2xl shadow-sm max-w-4xl">
    <form action="index.php?controller=relasi&action=create" method="POST">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="form-group">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="kode_relasi">Kode Relasi</label>
                <input type="text" id="kode_relasi" name="kode_relasi" class="form-control" placeholder="Contoh: H-J01, H-01MR" required>
                <span class="form-help block text-xs text-slate-500 mt-2">Kode unik pembeda relasi</span>
            </div>
            
            <div class="form-group">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="nama_relasi">Nama Relasi / Toko</label>
                <input type="text" id="nama_relasi" name="nama_relasi" class="form-control" placeholder="Contoh: BUDIMAN ANEKA TEKHNIK" required>
            </div>
        </div>

        <div class="form-group mt-6">
            <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="lokasi">Lokasi / Alamat</label>
            <input type="text" id="lokasi" name="lokasi" class="form-control" placeholder="Contoh: Gedung Sentosa, Gapura Baru">
        </div>

        <!-- Initial Cylinder Stock Inputs -->
        <div class="bg-indigo-50/30 dark:bg-indigo-500/5 border border-indigo-100 dark:border-indigo-500/10 rounded-2xl p-6 mt-6">
            <h4 class="font-bold text-lg mb-2">Stok Awal Tabung (MP Relasi)</h4>
            <p class="text-sm text-slate-500 dark:text-gray-400 mb-6">
                Masukkan jumlah tabung yang saat ini sedang berada di pihak mitra (Outstanding/Pinjaman awal). 
                Isi 0 jika tidak ada pinjaman awal. Gunakan angka negatif (-) jika relasi memiliki kelebihan setor tabung kosong.
            </p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (empty($barangList)): ?>
                    <p class="col-span-full text-danger text-sm font-medium">
                        Belum ada jenis tabung terdaftar. Daftarkan jenis tabung di menu <strong>Stok Gudang</strong> terlebih dahulu!
                    </p>
                <?php else: ?>
                    <?php foreach ($barangList as $b): ?>
                        <div class="form-group">
                            <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="stok_awal_<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_barang']) ?></label>
                            <input type="number" id="stok_awal_<?= $b['id'] ?>" name="stok_awal_<?= $b['id'] ?>" class="form-control" value="0" step="1">
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8">
            <a href="index.php?controller=relasi&action=index" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary" <?= empty($barangList) ? 'disabled' : '' ?>>Simpan Relasi</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
