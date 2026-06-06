

<?php if (!empty($error)): ?>
    <div style="background-color: var(--danger-bg); border: 1px solid var(--danger); color: var(--danger); padding: 15px; border-radius: var(--radius-sm); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h3 class="panel-title">Form Tambah Layanan Baru</h3>
        <a href="admin.php?page=layanan" class="btn-spa btn-spa-outline">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <div class="panel-body">
        <form action="admin.php?page=layanan&action=create" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nama_layanan">Nama Layanan SPA <span class="req-star">*</span></label>
                <input type="text" id="nama_layanan" name="nama_layanan" class="form-control" placeholder="Contoh: Aromatherapy Lavender Spa" required autocomplete="off">
                <span class="field-hint"><i class="fa-solid fa-circle-info"></i> Nama layanan yang akan ditampilkan di halaman pelanggan</span>
            </div>

            <div class="form-group">
                <label for="kategori">Kategori Layanan <span class="req-star">*</span></label>
                <select id="kategori" name="kategori" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Kategori --</option>
                    <?php
                    $kategoriList = [
                        'Pijat', 'Refleksi', 'Our Signature', 'Combo Paket',
                        'Lulur (+ Plus Pijat Sehat)', 'Spesial Treatment',
                        'Tambahan', 'Tambahan Bekam'
                    ];
                    foreach ($kategoriList as $kat): ?>
                        <option value="<?php echo htmlspecialchars($kat); ?>"><?php echo htmlspecialchars($kat); ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="field-hint"><i class="fa-solid fa-circle-info"></i> Pilih kategori sesuai jenis layanan yang ditawarkan</span>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="durasi">Durasi Perawatan (Menit) <span class="req-star">*</span></label>
                    <input type="number" id="durasi" name="durasi" class="form-control" placeholder="Contoh: 90" min="1" required autocomplete="off">
                    <span class="field-hint"><i class="fa-solid fa-circle-info"></i> Masukkan angka menit, misal: 60 untuk 1 jam</span>
                </div>
                
                <div class="form-group">
                    <label for="harga">Harga / Tarif Layanan (Rp) <span class="req-star">*</span></label>
                    <input type="number" id="harga" name="harga" class="form-control" placeholder="Contoh: 180000" min="1000" required autocomplete="off">
                    <span class="field-hint"><i class="fa-solid fa-circle-info"></i> Masukkan harga dalam Rupiah tanpa titik/koma</span>
                </div>
            </div>
            
            <div class="form-group">
                <label for="deskripsi">Deskripsi Lengkap Perawatan <span class="req-star">*</span></label>
                <textarea id="deskripsi" name="deskripsi" class="form-control" placeholder="Tuliskan detail perawatan spa, minyak yang digunakan, manfaat, dan tahapan spa lainnya..." required></textarea>
                <span class="field-hint"><i class="fa-solid fa-circle-info"></i> Deskripsi ini ditampilkan kepada pelanggan saat memilih layanan</span>
            </div>

            <div class="form-group">
                <label for="media">Gambar Layanan SPA</label>
                <input type="file" id="media" name="media" class="form-control" accept="image/*" style="padding: 6px 12px;">
                <small class="form-text" style="color: var(--text-muted); font-size: 0.8rem; margin-top: 4px; display: block;">
                    Format yang diperbolehkan: JPG, JPEG, PNG, WEBP, GIF. Ukuran maksimum: 5 MB.
                </small>
            </div>
            
            <div class="form-actions">
                <button type="reset" class="btn-spa btn-spa-outline">Reset Form</button>
                <button type="submit" class="btn-spa btn-spa-accent">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Layanan Baru
                </button>
            </div>
        </form>
    </div>
</div>
