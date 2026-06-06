
<?php

if (!function_exists('rupiah')) {
    function rupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('metodePembayaranAdmin')) {
    function metodePembayaranAdmin($p) {
        $method = trim($p['payment_method'] ?? $p['paymentMethod'] ?? 'Transfer');
        $method = trim(preg_replace('/\s*\((DP Hangus|Pembayaran Hangus)\)\s*/i', '', $method));
        return $method === '' ? 'Transfer' : $method;
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


<div class="admin-section-tabs">
    <a href="admin.php?page=pembayaran" class="admin-section-tab active">
        <i class="fa-solid fa-stamp"></i>
        <span>Verifikasi Pembayaran</span>
    </a>
    <a href="admin.php?page=pembayaran&action=rekening" class="admin-section-tab">
        <i class="fa-solid fa-credit-card"></i>
        <span>Metode Transfer</span>
    </a>
</div>

<!-- Search Bar -->
<div class="panel" style="margin-bottom: 20px;">
    <div class="panel-body" style="padding: 14px 20px;">
        <div style="position: relative; max-width: 420px;">
            <i class="fa-solid fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
            <input type="text" id="searchPembayaran" placeholder="Cari nama pelanggan, layanan, atau status..."
                   style="width: 100%; padding: 9px 12px 9px 36px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); outline: none; background: var(--bg-light); color: var(--text-dark); font-size: 0.87rem; box-sizing: border-box;"
                   oninput="filterPembayaran(this.value)">
        </div>
    </div>
</div>


<div class="panel">
    <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="panel-title">Daftar Validasi Pembayaran Transfer Pelanggan</h3>
        <span class="admin-panel-hint">Kelola bukti transfer masuk dari pelanggan.</span>
    </div>
    
    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="custom-table" data-admin-paginate data-per-page="6" data-noun="data">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">No</th>
                        <th>Pelanggan</th>
                        <th>Layanan SPA</th>
                        <th>Tanggal Reservasi</th>
                        <th>Jumlah Dibayar</th>
                        <th>Metode Bayar</th>
                        <th>Bukti</th>
                        <th>Status Validasi</th>
                        <th style="width: 150px; text-align: center;">Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pembayaranList)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fa-solid fa-receipt mb-2 d-block fs-3"></i> Belum ada pengajuan pembayaran transfer masuk.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1; 
                        foreach ($pembayaranList as $p): 
                        ?>
                            <tr>
                                <td style="text-align: center; font-weight: 600; color: var(--text-muted);"><?php echo $no++; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($p['namaPelanggan']); ?></strong>
                                </td>
                                <td>
                                    <div style="font-size: 0.95rem; font-weight: 600; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($p['layananNames']); ?>">
                                        <?php echo htmlspecialchars($p['layananNames']); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php echo date('d M Y', strtotime($p['reservation_date'])); ?><br>
                                    <small style="color: var(--text-muted);"><?php echo date('H:i', strtotime($p['reservation_date'])); ?> WIB</small>
                                </td>
                                <td>
                                    <strong style="color: var(--primary);"><?php echo rupiah($p['nominal_bayar']); ?></strong><br>
                                    <small style="color: var(--accent-hover);"><?php echo htmlspecialchars($p['jenis_pembayaran'] ?? 'DP 50%'); ?></small><br>
                                    <small style="color: var(--text-muted);">Total reservasi: <?php echo rupiah($p['total_price']); ?></small>
                                </td>
                                 <td><?php echo htmlspecialchars(metodePembayaranAdmin($p)); ?></td>
                                <td>
                                    <?php if ($p['payment_proof'] && $p['payment_proof'] !== 'walk-in-payment'): ?>
                                        <div style="width: 44px; height: 44px; border: 1px solid var(--border-color); border-radius: 4px; overflow: hidden; background-color: var(--bg-light); display: flex; align-items: center; justify-content: center;">
                                            <img src="uploads/pembayaran/<?php echo htmlspecialchars($p['payment_proof']); ?>" 
                                                 alt="Bukti Transfer" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak Ada / Walk-in</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $sp = strtolower($p['status_payment'] ?? $p['statusPayment'] ?? '');
                                    if ($sp === 'verified' || $sp === 'diterima' || $sp === 'lunas'): 
                                    ?>
                                        <span class="badge badge-success"><i class="fa-solid fa-circle-check" style="margin-right: 5px;"></i> <?php echo $sp === 'lunas' ? 'Lunas' : 'Diterima'; ?></span>
                                    <?php elseif ($sp === 'pending' || $sp === 'menunggu validasi' || $sp === 'menunggu pembayaran'): ?>
                                        <span class="badge badge-warning"><i class="fa-solid fa-hourglass-half" style="margin-right: 5px;"></i> Menunggu Validasi</span>
                                    <?php elseif ($sp === 'dp hangus' || $sp === 'pembayaran hangus'): ?>
                                        <span class="badge badge-danger"><i class="fa-solid fa-ban" style="margin-right: 5px;"></i> <?php echo $sp === 'pembayaran hangus' ? 'Pembayaran Hangus' : 'DP Hangus'; ?></span>
                                    <?php elseif ($sp === 'rejected' || $sp === 'ditolak'): ?>
                                        <span class="badge badge-danger"><i class="fa-solid fa-circle-xmark" style="margin-right: 5px;"></i> Ditolak</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?php echo htmlspecialchars($p['status_payment'] ?? $p['statusPayment'] ?? ''); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php 
                                    $spVal = strtolower($p['status_payment'] ?? $p['statusPayment'] ?? '');
                                    if ($spVal === 'pending' || $spVal === 'menunggu validasi' || $spVal === 'menunggu pembayaran'): 
                                    ?>
                                        <a href="admin.php?page=pembayaran&action=verifikasi&id=<?php echo $p['id_payment']; ?>" class="btn-spa btn-spa-accent" style="padding: 6px 12px; font-size: 0.8rem; gap: 5px;">
                                            <i class="fa-solid fa-stamp"></i> Verifikasi
                                        </a>
                                    <?php else: ?>
                                        <a href="admin.php?page=pembayaran&action=verifikasi&id=<?php echo $p['id_payment']; ?>" class="btn-spa btn-spa-outline" style="padding: 6px 12px; font-size: 0.8rem; gap: 5px;">
                                            <i class="fa-solid fa-eye"></i> Tinjau
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
