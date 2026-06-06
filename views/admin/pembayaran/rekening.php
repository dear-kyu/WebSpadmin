<?php if (!empty($success)): ?>
    <div style="background-color: var(--success-bg); border: 1px solid var(--success); color: var(--success); padding: 15px; border-radius: var(--radius-sm); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div style="background-color: var(--danger-bg); border: 1px solid var(--danger); color: var(--danger); padding: 15px; border-radius: var(--radius-sm); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="admin-section-tabs">
    <a href="admin.php?page=pembayaran" class="admin-section-tab">
        <i class="fa-solid fa-stamp"></i>
        <span>Verifikasi Pembayaran</span>
    </a>
    <a href="admin.php?page=pembayaran&action=rekening" class="admin-section-tab active">
        <i class="fa-solid fa-credit-card"></i>
        <span>Metode Transfer</span>
    </a>
</div>

<div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 30px; margin-bottom: 40px;">
    <div>
        <div class="panel">
            <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="panel-title">Metode Pembayaran Aktif</h3>
                <span class="admin-panel-hint">Rekening ini tampil di halaman pembayaran user.</span>
            </div>
            
            <div class="panel-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="custom-table" style="width: 100%;" data-admin-paginate data-per-page="6" data-noun="data">
                        <thead>
                            <tr>
                                <th style="width: 60px; text-align: center;">No</th>
                                <th>Nama Bank / E-Wallet</th>
                                <th>Nomor Rekening / Nomor HP</th>
                                <th>Atas Nama</th>
                                <th style="width: 100px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rekeningList)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                        <i class="fa-solid fa-credit-card mb-2 d-block fs-3"></i> Belum ada metode pembayaran dikonfigurasi.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                $no = 1; 
                                foreach ($rekeningList as $r): 
                                ?>
                                    <tr>
                                        <td style="text-align: center; font-weight: 600; color: var(--text-muted);"><?php echo $no++; ?></td>
                                        <td>
                                            <strong style="color: var(--primary);"><?php echo htmlspecialchars($r['nama_bank']); ?></strong>
                                        </td>
                                        <td>
                                            <strong style="font-family: monospace; font-size: 1rem; color: var(--text-dark);"><?php echo htmlspecialchars($r['nomor_rekening']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($r['atas_nama']); ?></td>
                                        <td style="text-align: center;">
                                            <a href="admin.php?page=pembayaran&action=hapus_rekening&id=<?php echo $r['id_rekening']; ?>" 
                                               class="btn-spa btn-spa-outline" 
                                               style="border-color: var(--danger); color: var(--danger); padding: 6px 12px; font-size: 0.8rem; gap: 5px;" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus metode pembayaran ini?');">
                                                <i class="fa-solid fa-trash-can"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div>
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Tambah Metode Pembayaran Baru</h3>
            </div>
            
            <div class="panel-body">
                <form action="admin.php?page=pembayaran&action=tambah_rekening" method="POST">
                    <div class="form-group">
                        <label for="nama_bank">Nama Bank / Metode E-Wallet</label>
                        <input type="text" id="nama_bank" name="nama_bank" class="form-control" placeholder="Contoh: BCA, MANDIRI, GoPay" required>
                    </div>
                    
                    <div class="form-group" style="margin-top: 15px;">
                        <label for="nomor_rekening">Nomor Rekening / Nomor HP E-Wallet</label>
                        <input type="text" id="nomor_rekening" name="nomor_rekening" class="form-control" placeholder="Contoh: 1234567890" required>
                    </div>
                    
                    <div class="form-group" style="margin-top: 15px;">
                        <label for="atas_nama">Nama Pemilik Rekening (Atas Nama)</label>
                        <input type="text" id="atas_nama" name="atas_nama" class="form-control" placeholder="Contoh: A.N. SPADMIN SPA" required>
                    </div>
                    
                    <div style="margin-top: 25px;">
                        <button type="submit" class="btn-spa btn-spa-accent" style="width: 100%; justify-content: center;">
                            <i class="fa-solid fa-plus"></i> Simpan Metode Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
