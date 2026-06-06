
<?php

if (!function_exists('rupiah')) {
    function rupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
}
?>

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


<!-- Search Bar -->
<div class="panel" style="margin-bottom: 20px;">
    <div class="panel-body" style="padding: 14px 20px;">
        <div style="position: relative; max-width: 420px;">
            <i class="fa-solid fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
            <input type="text" id="searchLayanan" placeholder="Cari nama layanan, durasi, atau harga..."
                   style="width: 100%; padding: 9px 12px 9px 36px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); outline: none; background: var(--bg-light); color: var(--text-dark); font-size: 0.87rem; box-sizing: border-box;"
                   oninput="filterLayanan(this.value)">
        </div>
    </div>
</div>


<div class="panel">
    <div class="panel-header">
        <h3 class="panel-title">Katalog Layanan SPA</h3>
        <a href="admin.php?page=layanan&action=create" class="btn-spa btn-spa-accent">
            <i class="fa-solid fa-plus"></i> Tambah Layanan Baru
        </a>
    </div>
    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="custom-table" data-admin-paginate data-per-page="6" data-noun="data">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">No</th>
                        <th>Nama Layanan</th>
                        <th style="width: 150px;">Durasi</th>
                        <th style="width: 180px;">Tarif / Harga</th>
                        <th>Deskripsi Perawatan</th>
                        <th style="width: 150px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($layananList)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fa-solid fa-spa mb-2 d-block fs-3"></i> Belum ada data layanan SPA tersedia.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1; 
                        foreach ($layananList as $l): 
                        ?>
                            <tr>
                                <td style="text-align: center; font-weight: 600; color: var(--text-muted);"><?php echo $no++; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($l['namaLayanan']); ?></strong>
                                </td>
                                <td>
                                    <span style="font-weight: 500;"><i class="fa-regular fa-clock text-muted mr-1" style="margin-right: 5px;"></i> <?php echo htmlspecialchars($l['durasi']); ?> Menit</span>
                                </td>
                                <td>
                                    <strong style="color: var(--accent-hover); font-size: 1.05rem;"><?php echo rupiah($l['harga']); ?></strong>
                                </td>
                                <td>
                                    <p style="font-size: 0.85rem; color: var(--text-muted); max-width: 450px; line-height: 1.4;">
                                        <?php echo htmlspecialchars($l['deskripsi']); ?>
                                    </p>
                                </td>
                                <td>
                                    <div class="btn-actions" style="justify-content: center;">
                                        <a href="admin.php?page=layanan&action=edit&id=<?php echo $l['idLayanan']; ?>" class="btn-icon" title="Ubah Data">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="admin.php?page=layanan&action=delete&id=<?php echo $l['idLayanan']; ?>" 
                                           class="btn-icon btn-icon-danger btn-confirm-delete" 
                                           data-message="Apakah Anda yakin ingin menghapus layanan: <?php echo htmlspecialchars($l['namaLayanan']); ?>? Hapus layanan dapat berakibat pada hilangnya data reservasi dan transaksi terkait."
                                           title="Hapus Data">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
