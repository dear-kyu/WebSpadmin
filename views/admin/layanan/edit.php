

<?php if (!empty($error)): ?>
    <div style="background-color: var(--danger-bg); border: 1px solid var(--danger); color: var(--danger); padding: 15px; border-radius: var(--radius-sm); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h3 class="panel-title">Form Ubah Layanan SPA</h3>
        <a href="admin.php?page=layanan" class="btn-spa btn-spa-outline">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <div class="panel-body">
        <form action="admin.php?page=layanan&action=edit&id=<?php echo $layanan['id_layanan']; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nama_layanan">Nama Layanan SPA</label>
                <input type="text" id="nama_layanan" name="nama_layanan" class="form-control" value="<?php echo htmlspecialchars($layanan['nama_layanan']); ?>" required autocomplete="off">
            </div>

            <div class="form-group">
                <label for="kategori">Kategori Layanan</label>
                <select id="kategori" name="kategori" class="form-control" required>
                    <?php
                    $kategoriList = [
                        'Pijat', 'Refleksi', 'Our Signature', 'Combo Paket',
                        'Lulur (+ Plus Pijat Sehat)', 'Spesial Treatment',
                        'Tambahan', 'Tambahan Bekam'
                    ];
                    $kategoriSaatIni = $layanan['kategori'] ?? '';
                    foreach ($kategoriList as $kat): ?>
                        <option value="<?php echo htmlspecialchars($kat); ?>" <?php echo $kat === $kategoriSaatIni ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($kat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="durasi">Durasi Perawatan (Menit)</label>
                    <input type="number" id="durasi" name="durasi" class="form-control" value="<?php echo htmlspecialchars($layanan['durasi']); ?>" min="1" required autocomplete="off">
                </div>
                
                <div class="form-group">
                    <label for="harga">Harga / Tarif Layanan (Rp)</label>
                    <input type="number" id="harga" name="harga" class="form-control" value="<?php echo htmlspecialchars(intval($layanan['harga'])); ?>" min="1000" required autocomplete="off">
                </div>
            </div>
            
            <div class="form-group">
                <label for="deskripsi">Deskripsi Lengkap Perawatan</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control" required><?php echo htmlspecialchars($layanan['deskripsi']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="media">Gambar Layanan SPA</label>
                <?php if (!empty($layanan['media'])): ?>
                    <div style="margin-bottom: 12px; display: flex; align-items: center; gap: 15px; background: var(--bg-light); padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                        <img src="uploads/layanan/<?php echo htmlspecialchars($layanan['media']); ?>" alt="Pratinjau Gambar" style="width: 100px; height: 80px; object-fit: cover; border-radius: var(--radius-xs); border: 1px solid var(--border-color);">
                        <div>
                            <span style="font-weight: 500; font-size: 0.85rem; display: block; color: var(--text-dark);">Gambar Saat Ini:</span>
                            <span style="font-size: 0.8rem; color: var(--text-muted); word-break: break-all;"><?php echo htmlspecialchars($layanan['media']); ?></span>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="margin-bottom: 12px; display: flex; align-items: center; gap: 15px; background: var(--bg-light); padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                        <div style="width: 100px; height: 80px; background: var(--border-color); border-radius: var(--radius-xs); display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 1.5rem;">
                            <i class="fa-solid fa-image"></i>
                        </div>
                        <div>
                            <span style="font-weight: 500; font-size: 0.85rem; display: block; color: var(--text-dark);">Belum Ada Gambar Khusus</span>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">Menggunakan gambar default system.</span>
                        </div>
                    </div>
                <?php endif; ?>
                <input type="file" id="media" name="media" class="form-control" accept="image/*" style="padding: 6px 12px;">
                <small class="form-text" style="color: var(--text-muted); font-size: 0.8rem; margin-top: 4px; display: block;">
                    Pilih gambar baru jika ingin mengganti gambar saat ini. Format yang diperbolehkan: JPG, JPEG, PNG, WEBP, GIF. Ukuran maksimum: 5 MB.
                </small>
            </div>
            
            <div class="form-actions">
                <a href="admin.php?page=layanan" class="btn-spa btn-spa-outline">Batal</a>
                <button type="submit" class="btn-spa btn-spa-accent">
                    <i class="fa-solid fa-floppy-disk"></i> Perbarui Layanan
                </button>
            </div>
        </form>
    </div>
</div>
