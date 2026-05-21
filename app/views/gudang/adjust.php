<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-title">
        <h2>Penyesuaian Stok Gudang</h2>
        <p>Catat pembelian tabung baru, isi ulang, atau penyesuaian manual</p>
    </div>
    <div class="page-actions">
        <a href="index.php?controller=gudang&action=index" class="btn btn-secondary">Kembali</a>
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
    <form action="index.php?controller=gudang&action=adjust" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="tanggal">Tanggal Penyesuaian</label>
                <input type="date" id="tanggal" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="barang_id">Jenis Tabung Gas</label>
                <select id="barang_id" name="barang_id" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Jenis Tabung --</option>
                    <?php foreach ($barangList as $b): ?>
                        <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_barang']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-grid" style="margin-top:0.5rem;">
            <div class="form-group">
                <label class="form-label" for="tipe_transaksi">Tipe Penyesuaian</label>
                <select id="tipe_transaksi" name="tipe_transaksi" class="form-control" required onchange="toggleTargetSelect()">
                    <option value="pembelian">Pembelian / Tambah Tabung Baru</option>
                    <option value="refill">Refill / Isi Ulang Tabung Kosong</option>
                    <option value="penjualan">Penjualan / Pemusnahan Tabung (-)</option>
                </select>
                <span class="form-help">Pilih sifat dari penyesuaian ini.</span>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="jumlah">Jumlah (Tabung)</label>
                <input type="number" id="jumlah" name="jumlah" class="form-control" min="1" value="1" required>
            </div>
            
            <div class="form-group" id="target_stok_group">
                <label class="form-label" for="target_stok">Status Masuk Ke</label>
                <select id="target_stok" name="target_stok" class="form-control" required>
                    <option value="ready">Stok READY / Full</option>
                    <option value="kosong">Stok KOSONG</option>
                </select>
            </div>
        </div>

        <div class="form-group" style="margin-top:0.5rem;">
            <label class="form-label" for="keterangan">Keterangan / Catatan Tambahan</label>
            <input type="text" id="keterangan" name="keterangan" class="form-control" placeholder="Contoh: Beli 10 tabung baru kosong, atau Refill dari supplier X">
        </div>

        <div style="display:flex; justify-content:flex-end; gap:0.75rem; margin-top:2rem;">
            <a href="index.php?controller=gudang&action=index" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" <?= empty($barangList) ? 'disabled' : '' ?>>Simpan Penyesuaian</button>
        </div>
    </form>
</div>

<script>
function toggleTargetSelect() {
    const tipe = document.getElementById('tipe_transaksi').value;
    const targetGroup = document.getElementById('target_stok_group');
    const targetSelect = document.getElementById('target_stok');
    
    if (tipe === 'refill') {
        // Refill logic handled by model (moves empty to ready)
        // Disable target selection as it's implicit (empty -> ready)
        targetGroup.style.opacity = '0.5';
        targetSelect.value = 'ready';
        targetSelect.setAttribute('disabled', 'true');
        // Add hidden input so POST receives a value
        if (!document.getElementById('hidden_target')) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.id = 'hidden_target';
            hidden.name = 'target_stok';
            hidden.value = 'ready';
            targetGroup.appendChild(hidden);
        }
    } else {
        targetGroup.style.opacity = '1';
        targetSelect.removeAttribute('disabled');
        const hidden = document.getElementById('hidden_target');
        if (hidden) hidden.remove();
    }
}
document.addEventListener('DOMContentLoaded', toggleTargetSelect);
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
