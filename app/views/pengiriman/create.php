<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Catat Pengiriman Harian</h2>
        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Input pengiriman tabung isi baru dan penerimaan tabung kosong kembali dari mitra</p>
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
    <form action="index.php?controller=pengiriman&action=create" method="POST">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="form-group">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="tanggal">Tanggal Pengiriman</label>
                <input type="date" id="tanggal" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="no_surat_jalan">No. Surat Jalan / Nota (Opsional)</label>
                <input type="text" id="no_surat_jalan" name="no_surat_jalan" class="form-control" placeholder="Contoh: SJ-20260609-001">
            </div>

            <div class="form-group">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="relasi_id">Mitra / Relasi Pelanggan</label>
                <select id="relasi_id" name="relasi_id" class="form-control choices-select" required>
                    <option value="" disabled selected>-- Pilih Mitra --</option>
                    <?php foreach ($clients as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nama_relasi']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div id="items-container">
            <div class="item-row grid grid-cols-1 md:grid-cols-4 gap-6 mt-6 items-end p-4 border border-slate-200 dark:border-gray-700 rounded-xl relative">
                <div class="form-group md:col-span-1">
                    <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2">Jenis Tabung Gas</label>
                    <select name="barang_id[]" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Jenis Tabung --</option>
                        <?php foreach ($barangList as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_barang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group md:col-span-1">
                    <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2">KIRIM / KELUAR</label>
                    <div class="flex gap-2">
                        <input type="number" name="jumlah_masuk[]" class="form-control border-success focus:border-success focus:ring-success/20 text-success font-bold" min="0" value="0" required>
                        <select name="kondisi_kirim[]" class="form-control" style="width: 100px;">
                            <option value="Isi" selected>Isi</option>
                            <option value="Kosong">Kosong</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group md:col-span-1">
                    <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2">KEMBALI / MASUK</label>
                    <div class="flex gap-2">
                        <input type="number" name="jumlah_keluar[]" class="form-control border-warning focus:border-warning focus:ring-warning/20 text-warning font-bold" min="0" value="0" required>
                        <select name="kondisi_kembali[]" class="form-control" style="width: 100px;">
                            <option value="Kosong" selected>Kosong</option>
                            <option value="Isi">Isi</option>
                        </select>
                    </div>
                </div>

                <div class="form-group md:col-span-1 flex items-center h-full pb-1">
                    <button type="button" class="btn-danger w-full btn-remove-item hidden">
                        <i class="ph-bold ph-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="button" id="btn-add-item" class="w-full border-2 border-dashed border-indigo-200 dark:border-indigo-800 text-primary dark:text-indigo-400 font-semibold py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-colors flex items-center justify-center gap-2">
                <i class="ph-bold ph-plus text-lg"></i> Tambah Tabung Lain
            </button>
        </div>

        <div class="form-group mt-6">
            <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="keterangan">Keterangan / Catatan Tambahan</label>
            <input type="text" id="keterangan" name="keterangan" class="form-control" placeholder="Contoh: Refill CO2 5KG, tabung dipinjam, dll.">
        </div>

        <div class="flex justify-end gap-3 mt-8">
            <a href="index.php?controller=pengiriman&action=index" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary" <?= (empty($clients) || empty($barangList)) ? 'disabled' : '' ?>>Simpan Catatan</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsContainer = document.getElementById('items-container');
    const btnAddItem = document.getElementById('btn-add-item');

    btnAddItem.addEventListener('click', function() {
        // Clone the first row
        const firstRow = itemsContainer.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);

        // Reset values in the new row
        const inputs = newRow.querySelectorAll('input');
        inputs.forEach(input => {
            if(input.type === 'number') {
                input.value = '0';
            } else {
                input.value = '';
            }
        });

        const selects = newRow.querySelectorAll('select');
        selects.forEach(select => {
            if (select.name === 'kondisi_kirim[]') {
                select.value = 'Isi';
            } else if (select.name === 'kondisi_kembali[]') {
                select.value = 'Kosong';
            } else {
                select.selectedIndex = 0;
            }
        });

        // Show the remove button on the new row
        const removeBtn = newRow.querySelector('.btn-remove-item');
        if(removeBtn) {
            removeBtn.classList.remove('hidden');
        }

        // Add event listener to the new remove button
        removeBtn.addEventListener('click', function() {
            newRow.remove();
        });

        // Append to container
        itemsContainer.appendChild(newRow);
    });

    // Event delegation for the initial row's remove button (though it's hidden by default)
    // We don't really need it for the first row if we want to force at least one item
});
</script>
