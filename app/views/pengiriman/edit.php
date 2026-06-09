<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Edit Catatan Pengiriman</h2>
        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Koreksi data pengiriman – stok gudang &amp; relasi akan disesuaikan otomatis</p>
    </div>
    <div>
        <a href="index.php?controller=pengiriman&action=index" class="btn-secondary">Kembali</a>
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
    <form action="index.php?controller=pengiriman&action=edit&id=<?= $pengiriman['id'] ?>" method="POST">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="form-group">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="tanggal">Tanggal Pengiriman</label>
                <input type="date" id="tanggal" name="tanggal" class="form-control" value="<?= $pengiriman['tanggal'] ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="no_surat_jalan">No. Surat Jalan / Nota</label>
                <input type="text" id="no_surat_jalan" name="no_surat_jalan" class="form-control" value="<?= htmlspecialchars($pengiriman['no_surat_jalan'] ?? '') ?>" placeholder="Contoh: SJ-20260609-001">
            </div>

            <div class="form-group">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="relasi_id">Mitra / Relasi Pelanggan</label>
                <select id="relasi_id" name="relasi_id" class="form-control choices-select" required>
                    <option value="" disabled>-- Pilih Mitra --</option>
                    <?php foreach ($clients as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $pengiriman['relasi_id'] == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nama_relasi']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="form-group md:col-span-1">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="barang_id">Jenis Tabung Gas</label>
                <select id="barang_id" name="barang_id" class="form-control choices-select" required>
                    <?php foreach ($barangList as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= $b['id'] == $pengiriman['barang_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['nama_barang']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group md:col-span-1">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="jumlah_masuk">KIRIM / KELUAR</label>
                <div class="flex gap-2">
                    <input type="number" id="jumlah_masuk" name="jumlah_masuk" class="form-control border-success focus:border-success focus:ring-success/20 text-success font-bold" min="0" value="<?= $pengiriman['jumlah_masuk'] ?>" required>
                    <select name="kondisi_kirim" class="form-control" style="width: 100px;">
                        <option value="Isi" <?= $pengiriman['kondisi_kirim'] == 'Isi' ? 'selected' : '' ?>>Isi</option>
                        <option value="Kosong" <?= $pengiriman['kondisi_kirim'] == 'Kosong' ? 'selected' : '' ?>>Kosong</option>
                    </select>
                </div>
            </div>

            <div class="form-group md:col-span-1">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="jumlah_keluar">KEMBALI / MASUK</label>
                <div class="flex gap-2">
                    <input type="number" id="jumlah_keluar" name="jumlah_keluar" class="form-control border-warning focus:border-warning focus:ring-warning/20 text-warning font-bold" min="0" value="<?= $pengiriman['jumlah_keluar'] ?>" required>
                    <select name="kondisi_kembali" class="form-control" style="width: 100px;">
                        <option value="Kosong" <?= $pengiriman['kondisi_kembali'] == 'Kosong' ? 'selected' : '' ?>>Kosong</option>
                        <option value="Isi" <?= $pengiriman['kondisi_kembali'] == 'Isi' ? 'selected' : '' ?>>Isi</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group mt-6">
            <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="keterangan">Keterangan</label>
            <input type="text" id="keterangan" name="keterangan" class="form-control" value="<?= htmlspecialchars($pengiriman['keterangan'] ?? '') ?>">
        </div>

        <div class="flex justify-end gap-3 mt-8">
            <a href="index.php?controller=pengiriman&action=index" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
