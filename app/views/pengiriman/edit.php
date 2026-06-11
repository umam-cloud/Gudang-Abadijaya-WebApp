<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Edit Catatan Pengiriman</h2>
        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Koreksi data pengiriman – stok gudang &amp; relasi akan disesuaikan otomatis</p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>pengiriman" class="btn-secondary">Kembali</a>
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
    <form id="form-pengiriman-edit" action="<?= BASE_URL ?>pengiriman/edit?id=<?= $pengiriman['id'] ?>" method="POST">
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
            <a href="<?= BASE_URL ?>pengiriman" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<!-- Modal Alert Minus Stok -->
<div id="minusAlertModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-3xl shadow-2xl max-w-md w-full p-6 transform scale-95 transition-transform duration-300">
        <div class="flex items-center justify-center w-16 h-16 rounded-full bg-danger/10 text-danger mx-auto mb-6">
            <i class="ph-fill ph-warning text-3xl"></i>
        </div>
        <h3 class="text-xl font-bold text-center text-slate-900 dark:text-gray-100 mb-2">Peringatan Stok Minus</h3>
        <p class="text-slate-600 dark:text-gray-400 text-center mb-6 text-sm" id="minusAlertMessage">
            Jumlah kembali melebihi stok yang tercatat pada mitra.
        </p>
        <div class="bg-danger/5 border border-danger/20 rounded-xl p-4 mb-8">
            <p class="text-sm font-medium text-danger text-center">Apakah Anda yakin ingin melanjutkan dan mencatat stok minus?</p>
        </div>
        <div class="flex gap-3">
            <button type="button" id="btn-cancel-submit" class="btn-secondary w-1/2">Batalkan</button>
            <button type="button" id="btn-confirm-submit" class="btn-danger w-1/2">Tetap Simpan</button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<script>
const stockMatrix = <?= json_encode($stockMatrix ?? []) ?>;
const barangData = <?= json_encode($barangList ?? []) ?>;
const currentPengiriman = {
    relasi_id: <?= (int)$pengiriman['relasi_id'] ?>,
    barang_id: <?= (int)$pengiriman['barang_id'] ?>,
    jumlah_masuk: <?= (int)$pengiriman['jumlah_masuk'] ?>,
    jumlah_keluar: <?= (int)$pengiriman['jumlah_keluar'] ?>
};
let isConfirmed = false;

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-pengiriman-edit');
    const modal = document.getElementById('minusAlertModal');
    const btnCancel = document.getElementById('btn-cancel-submit');
    const btnConfirm = document.getElementById('btn-confirm-submit');
    const alertMessage = document.getElementById('minusAlertMessage');

    form.addEventListener('submit', function(e) {
        if (isConfirmed) return; // Allow submit if already confirmed

        const relasiId = parseInt(document.getElementById('relasi_id').value);
        const barangId = parseInt(document.getElementById('barang_id').value);
        const jumlahKeluar = parseInt(document.getElementById('jumlah_keluar').value) || 0;
        
        if (!relasiId || !barangId || jumlahKeluar <= 0) return;

        let currentStock = 0;
        if (stockMatrix[relasiId] && stockMatrix[relasiId][barangId] !== undefined) {
            currentStock = stockMatrix[relasiId][barangId];
        }
        
        // Sesuaikan currentStock untuk baseline edit
        if (relasiId === currentPengiriman.relasi_id && barangId === currentPengiriman.barang_id) {
            currentStock = currentStock - currentPengiriman.jumlah_masuk + currentPengiriman.jumlah_keluar;
        }

        if (jumlahKeluar > currentStock) {
            e.preventDefault(); // Stop form
            
            const barangName = barangData.find(b => b.id == barangId)?.nama_barang || 'Tabung';
            const minusAmt = currentStock - jumlahKeluar;
            
            alertMessage.innerHTML = `Pengembalian ini akan menyebabkan stok mitra menjadi minus:<br><br><b>${barangName}</b>: kembali ${jumlahKeluar}, sisa di mitra ${currentStock} (Hasil: <span class="text-danger font-bold">${minusAmt}</span>)`;
            
            // Show modal
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('div').classList.remove('scale-95');
            }, 10);
        }
    });

    btnCancel.addEventListener('click', function() {
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    });

    btnConfirm.addEventListener('click', function() {
        isConfirmed = true;
        form.submit();
    });
});
</script>
