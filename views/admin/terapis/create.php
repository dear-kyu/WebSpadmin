

<?php if (!empty($error)): ?>
    <div style="background-color: var(--danger-bg); border: 1px solid var(--danger); color: var(--danger); padding: 15px; border-radius: var(--radius-sm); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h3 class="panel-title">Form Tambah Terapis Baru</h3>
        <a href="admin.php?page=terapis" class="btn-spa btn-spa-outline">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <div class="panel-body">
        <form action="admin.php?page=terapis&action=create" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="namaTerapis">Nama Lengkap Terapis</label>
                    <input type="text" id="namaTerapis" name="namaTerapis" class="form-control" placeholder="Contoh: Sri Wahyuni" required autocomplete="off" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">
                </div>
                
                <div class="form-group">
                    <label for="noTelp">Nomor Telepon / WA</label>
                    <input type="text" id="noTelp" name="noTelp" class="form-control" placeholder="Contoh: 0812XXXXXXXX" required autocomplete="off" oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(this.value.length < 10 || this.value.length > 13) { document.getElementById('err_no_telp').style.display = 'block'; } else { document.getElementById('err_no_telp').style.display = 'none'; }">
                    <small id="err_no_telp" style="color: var(--danger); display: none; margin-top: 5px;">*Nomor telepon harus berupa angka dan berjumlah 10-13 digit.</small>
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Spesialisasi & Keahlian</label>
                    <?php 
                    $options = $layananOptions ?? [];
                    ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; padding: 5px 0;">
                        <?php foreach ($options as $opt): ?>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500; color: var(--text-dark); font-size: 0.85rem;">
                                <input type="checkbox" name="spesialisasi[]" value="<?php echo htmlspecialchars($opt); ?>" 
                                       style="accent-color: var(--accent); width: 16px; height: 16px; cursor: pointer;">
                                <?php echo htmlspecialchars($opt); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Jenis Kelamin</label>
                    <div style="display: flex; gap: 20px; align-items: center; padding: 5px 0;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500; color: var(--text-dark);">
                            <input type="radio" name="jenis_kelamin" value="Perempuan" checked style="accent-color: var(--accent); width: 18px; height: 18px; cursor: pointer;"> Perempuan
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500; color: var(--text-dark);">
                            <input type="radio" name="jenis_kelamin" value="Laki-Laki" style="accent-color: var(--accent); width: 18px; height: 18px; cursor: pointer;"> Laki-Laki
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 15px;">
                <label for="status">Status Terapis</label>
                <select id="status" name="status" class="form-control" style="max-width: 50%;">
                    <option value="Aktif" selected>Aktif (Siap Menerima Pelanggan)</option>
                    <option value="Tidak Aktif">Tidak Aktif (Cuti / Berhenti)</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="reset" class="btn-spa btn-spa-outline">Reset Form</button>
                <button type="submit" class="btn-spa btn-spa-accent">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Data Terapis
                </button>
            </div>
        </form>
    </div>
</div>
