<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Transfer & Konversi Tabung</h2>
        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Ubah fungsi/tipe tabung atau pindah status dari isi ke kosong</p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>gudang" class="btn-secondary">Kembali</a>
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

<div class="glass-panel p-6 rounded-2xl shadow-sm max-w-5xl">
    <form action="<?= BASE_URL ?>gudang/transfer" method="POST">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="form-group">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="tanggal">Tanggal Transfer</label>
                <input type="date" id="tanggal" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="jumlah">Jumlah Tabung Ditransfer</label>
                <input type="number" id="jumlah" name="jumlah" class="form-control" value="1" min="1" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-6">
            <!-- ASAL -->
            <div class="p-5 border border-slate-200 dark:border-gray-700 rounded-xl bg-slate-50/50 dark:bg-gray-800/50">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <i class="ph-bold ph-export text-danger"></i>
                    Dari (Asal)
                </h3>
                
                <div class="form-group mb-4">
                    <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="barang_asal_id">Jenis Tabung Asal</label>
                    <select id="barang_asal_id" name="barang_asal_id" class="form-control choices-select" required>
                        <option value="" disabled selected>-- Pilih Tabung Asal --</option>
                        <?php foreach ($barangList as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_barang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="kondisi_asal">Status Asal</label>
                    <select id="kondisi_asal" name="kondisi_asal" class="form-control" required>
                        <option value="ready">Stok READY / Full</option>
                        <option value="kosong">Stok KOSONG</option>
                    </select>
                </div>
            </div>
            
            <!-- TUJUAN -->
            <div class="p-5 border border-indigo-200 dark:border-indigo-500/30 rounded-xl bg-indigo-50/30 dark:bg-indigo-500/5 relative">
                <div class="hidden md:flex absolute top-1/2 -left-6 w-12 h-12 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-full items-center justify-center -mt-6 z-10 shadow-sm text-primary">
                    <i class="ph-bold ph-arrow-right text-xl"></i>
                </div>
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-primary">
                    <i class="ph-bold ph-import"></i>
                    Menjadi (Tujuan)
                </h3>
                
                <div class="form-group mb-4">
                    <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="barang_tujuan_id">Jenis Tabung Tujuan</label>
                    <select id="barang_tujuan_id" name="barang_tujuan_id" class="form-control choices-select" required>
                        <option value="" disabled selected>-- Pilih Tabung Tujuan --</option>
                        <?php foreach ($barangList as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_barang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="kondisi_tujuan">Status Tujuan</label>
                    <select id="kondisi_tujuan" name="kondisi_tujuan" class="form-control" required>
                        <option value="ready">Stok READY / Full</option>
                        <option value="kosong">Stok KOSONG</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group mt-6">
            <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="keterangan">Keterangan / Catatan Tambahan</label>
            <input type="text" id="keterangan" name="keterangan" class="form-control" placeholder="Contoh: Tabung Joewara diubah fungsi jadi Tabung MP">
        </div>

        <div class="flex justify-end gap-3 mt-8">
            <a href="<?= BASE_URL ?>gudang" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary" <?= empty($barangList) ? 'disabled' : '' ?>>Proses Transfer</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
