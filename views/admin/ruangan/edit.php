<?php if (!empty($error)): ?>
    <div style="background-color: var(--danger-bg); border: 1px solid var(--danger); color: var(--danger); padding: 15px; border-radius: var(--radius-sm); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h3 class="panel-title">Form Edit Data Ruangan SPA</h3>
        <a href="admin.php?page=ruangan" class="btn-spa btn-spa-outline">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <div class="panel-body">
        <form action="admin.php?page=ruangan&action=edit&id=<?php echo $room['id_ruangan']; ?>" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="namaRuangan">Nama Ruangan</label>
                    <input type="text" id="namaRuangan" name="namaRuangan" class="form-control" value="<?php echo htmlspecialchars($room['nama_ruangan']); ?>" required autocomplete="off">
                </div>
                
                <div class="form-group">
                    <label for="status">Status Keaktifan</label>
                    <select id="status" name="status" class="form-control">
                        <option value="aktif" <?php echo $room['status'] === 'aktif' ? 'selected' : ''; ?>>Aktif (Tersedia untuk Booking)</option>
                        <option value="tidak aktif" <?php echo $room['status'] === 'tidak aktif' ? 'selected' : ''; ?>>Tidak Aktif (Dalam Pemeliharaan / Renovasi)</option>
                    </select>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="admin.php?page=ruangan" class="btn-spa btn-spa-outline">Batal</a>
                <button type="submit" class="btn-spa btn-spa-accent">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
