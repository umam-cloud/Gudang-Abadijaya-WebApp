<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Penyesuaian Stok Gudang</h2>
        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Catat pembelian tabung baru, isi ulang, atau penyesuaian manual</p>
    </div>
    <div>
        <a href="index.php?controller=gudang&action=index" class="btn-secondary">Kembali</a>
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
    <form action="index.php?controller=gudang&action=adjust" method="POST">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="form-group">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="tanggal">Tanggal Penyesuaian</label>
                <input type="date" id="tanggal" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="barang_id">Jenis Tabung Gas</label>
                <select id="barang_id" name="barang_id" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Jenis Tabung --</option>
                    <?php foreach ($barangList as $b): ?>
                        <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_barang']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="form-group md:col-span-1">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="tipe_transaksi">Tipe Penyesuaian</label>
                <select id="tipe_transaksi" name="tipe_transaksi" class="form-control" required onchange="toggleTargetSelect()">
                    <option value="beli_baru">Pembelian / Tambah Tabung Baru</option>
                    <option value="refill">Refill / Isi Ulang Tabung Kosong</option>
                    <option value="jual_rusak">Penjualan / Pemusnahan Tabung (-)</option>
                    <option value="koreksi">Koreksi Manual (Catatan: jumlah dapat berupa -/negatif)</option>
                </select>
                <span class="form-help block text-xs text-slate-500 mt-2">Pilih sifat dari penyesuaian ini.</span>
            </div>
            
            <div class="form-group md:col-span-1">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="jumlah">Jumlah (Tabung)</label>
                <input type="number" id="jumlah" name="jumlah" class="form-control" value="1" required>
            </div>
            
            <div class="form-group md:col-span-1 transition-opacity duration-300" id="target_stok_group">
                <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="target_stok">Status Masuk Ke</label>
                <select id="target_stok" name="target_stok" class="form-control" required>
                    <option value="ready">Stok READY / Full</option>
                    <option value="kosong">Stok KOSONG</option>
                </select>
            </div>
        </div>

        <div class="form-group mt-6">
            <label class="form-label block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2" for="keterangan">Keterangan / Catatan Tambahan</label>
            <input type="text" id="keterangan" name="keterangan" class="form-control" placeholder="Contoh: Beli 10 tabung baru kosong, atau Refill dari supplier X">
        </div>

        <div class="flex justify-end gap-3 mt-8">
            <a href="index.php?controller=gudang&action=index" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary" <?= empty($barangList) ? 'disabled' : '' ?>>Simpan Penyesuaian</button>
        </div>
    </form>
</div>

<script>
function toggleTargetSelect() {
    const tipe = document.getElementById('tipe_transaksi').value;
    const targetGroup = document.getElementById('target_stok_group');
    const targetSelect = document.getElementById('target_stok');
    
    if (tipe === 'refill') {
        targetGroup.classList.add('opacity-50');
        targetSelect.value = 'ready';
        targetSelect.setAttribute('disabled', 'true');
        if (!document.getElementById('hidden_target')) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.id = 'hidden_target';
            hidden.name = 'target_stok';
            hidden.value = 'ready';
            targetGroup.appendChild(hidden);
        }
    } else {
        targetGroup.classList.remove('opacity-50');
        targetSelect.removeAttribute('disabled');
        const hidden = document.getElementById('hidden_target');
        if (hidden) hidden.remove();
    }
}
document.addEventListener('DOMContentLoaded', toggleTargetSelect);
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
