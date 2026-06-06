
<?php

if (!function_exists('rupiah')) {
    function rupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
}
?>

<?php if (!empty($error)): ?>
    <div style="background-color: var(--danger-bg); border: 1px solid var(--danger); color: var(--danger); padding: 15px; border-radius: var(--radius-sm); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h3 class="panel-title">Verifikasi & Validasi Bukti Pembayaran</h3>
        <a href="admin.php?page=pembayaran" class="btn-spa btn-spa-outline">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <div class="panel-body">
        <div class="verifikasi-container">
            
            <div class="bukti-box">
                <h4 style="margin-bottom: 15px; color: var(--primary);"><i class="fa-solid fa-image"></i> Gambar Bukti Transfer</h4>
                <?php if ($pay['payment_proof'] && $pay['payment_proof'] !== 'walk-in-payment'): ?>
                    <img src="uploads/pembayaran/<?php echo htmlspecialchars($pay['payment_proof']); ?>" 
                         id="buktiImg" class="bukti-img" alt="Bukti Pembayaran Transfer" title="Klik untuk memperbesar gambar" style="cursor: pointer;">
                    <p style="margin-top: 10px; font-size: 0.8rem; color: var(--text-muted);">
                        <i class="fa-solid fa-magnifying-glass-plus"></i> Klik pada gambar untuk memperbesar bukti transfer.
                    </p>
                <?php else: ?>
                    <div style="padding: 50px; text-align: center; color: var(--text-muted);">
                        <i class="fa-solid fa-ban fs-1 mb-2 d-block"></i> Bukti tidak diunggah oleh pelanggan (atau Pembayaran POS langsung).
                    </div>
                <?php endif; ?>
            </div>
            
            
            <div>
                <h4 style="margin-bottom: 15px; color: var(--primary);"><i class="fa-solid fa-circle-info"></i> Detail Pembayaran & Reservasi</h4>
                
                <ul class="detail-pembayaran-list">
                    <li>
                        <strong>Nama Pelanggan:</strong>
                        <span><?php echo htmlspecialchars($pay['namaPelanggan']); ?></span>
                    </li>
                    <li>
                        <strong>Nomor Telepon:</strong>
                        <span><?php echo htmlspecialchars($pay['noHpPelanggan']); ?></span>
                    </li>
                    <li>
                        <strong>Layanan SPA Dipesan:</strong>
                        <span><?php echo htmlspecialchars($pay['layananNames']); ?></span>
                    </li>
                    <li>
                        <strong>Tanggal Booking:</strong>
                        <span><?php echo date('d F Y', strtotime($pay['reservation_date'])); ?> @ <?php echo date('H:i', strtotime($pay['reservation_date'])); ?> WIB</span>
                    </li>
                    <li>
                        <strong>Jumlah Dibayar:</strong>
                        <span style="font-size: 1.1rem; color: var(--accent-hover); font-weight: 800;"><?php echo rupiah($pay['nominal_bayar']); ?> (<?php echo htmlspecialchars($pay['jenis_pembayaran'] ?? 'DP 50%'); ?>)</span>
                    </li>
                    <li>
                        <strong>Metode Pembayaran:</strong>
                        <span><?php echo htmlspecialchars($pay['payment_method'] ?? $pay['paymentMethod'] ?? 'Transfer'); ?></span>
                    </li>
                    <li>
                        <strong>Status Validasi Saat Ini:</strong>
                        <span>
                            <?php 
                            $sp = strtolower($pay['status_payment'] ?? $pay['statusPayment'] ?? '');
                            if ($sp === 'verified' || $sp === 'diterima' || $sp === 'lunas'): 
                            ?>
                                <span class="badge badge-success"><?php echo $sp === 'lunas' ? 'Lunas' : 'Diterima'; ?></span>
                            <?php elseif ($sp === 'pending' || $sp === 'menunggu validasi' || $sp === 'menunggu pembayaran'): ?>
                                <span class="badge badge-warning">Menunggu Validasi</span>
                            <?php elseif ($sp === 'dp hangus' || $sp === 'pembayaran hangus'): ?>
                                <span class="badge badge-danger"><?php echo $sp === 'pembayaran hangus' ? 'Pembayaran Hangus' : 'DP Hangus'; ?> (Tidak Dikembalikan)</span>
                            <?php elseif ($sp === 'rejected' || $sp === 'ditolak'): ?>
                                <span class="badge badge-danger">Ditolak</span>
                            <?php else: ?>
                                <span class="badge badge-secondary"><?php echo htmlspecialchars($pay['status_payment'] ?? $pay['statusPayment'] ?? ''); ?></span>
                            <?php endif; ?>
                        </span>
                    </li>
                    <?php if ($pay['nama_verifier']): ?>
                        <li>
                            <strong>Diverifikasi Oleh:</strong>
                            <span><?php echo htmlspecialchars($pay['nama_verifier']); ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
                
                
                <?php $dpMinimum = $pay['total_price'] * 0.5; ?>
                <div style="background: var(--info-bg); border: 1px solid var(--info); border-radius: var(--radius-sm); padding: 12px 16px; margin-top: 15px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; font-size: 0.88rem;">
                    <i class="fa-solid fa-info-circle" style="color: var(--info); font-size: 1.1rem;"></i>
                    <div>
                        <strong style="color: var(--info);">Kebijakan DP 50%</strong><br>
                        <span style="color: var(--text-dark);">Pelanggan memilih <strong style="color: var(--accent-hover);"><?php echo htmlspecialchars($pay['jenis_pembayaran'] ?? 'DP 50%'); ?></strong> sebesar <strong style="color: var(--accent-hover);"><?php echo rupiah($pay['nominal_bayar']); ?></strong> dari total <?php echo rupiah($pay['total_price']); ?>. Jika pelanggan tidak datang, seluruh pembayaran yang sudah masuk hangus dan tidak dikembalikan.</span>
                    </div>
                </div>
                <form action="admin.php?page=pembayaran&action=verifikasi&id=<?php echo $pay['id_payment']; ?>" method="POST" class="verifikasi-actions">
                    <h5 style="margin-bottom: 15px; color: var(--primary); font-weight: 700;"><i class="fa-solid fa-stamp"></i> Atur & Ubah Keputusan Verifikasi</h5>
                    
                    <?php 
                    $spCheck = strtolower($pay['status_payment'] ?? $pay['statusPayment'] ?? '');
                    if ($spCheck !== 'pending' && $spCheck !== 'menunggu validasi' && $spCheck !== 'menunggu pembayaran'): 
                    ?>
                        <div style="background-color: var(--bg-card); border: 1px solid var(--border-color); padding: 10px 15px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 0.88rem; color: var(--text-dark);">
                            <i class="fa-solid fa-circle-info text-info"></i> Pembayaran ini sudah diproses sebelumnya, namun Anda tetap dapat mengubah status di bawah ini jika diperlukan.
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Tindakan Validasi</label>
                        <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 10px;">
                            <label style="font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="status_pembayaran" value="pending" <?php echo ($spCheck === 'pending' || $spCheck === 'menunggu validasi' || $spCheck === 'menunggu pembayaran') ? 'checked' : ''; ?> style="accent-color: var(--warning); transform: scale(1.15);">
                                <span style="font-weight: 700; color: var(--warning);"><i class="fa-solid fa-clock"></i> Pending (Belum Diverifikasi)</span>
                            </label>
                            <label style="font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="status_pembayaran" value="verified" <?php echo ($spCheck === 'verified' || $spCheck === 'diterima' || $spCheck === 'lunas') ? 'checked' : ''; ?> style="accent-color: var(--success); transform: scale(1.15);">
                                <span style="font-weight: 700; color: var(--success);"><i class="fa-solid fa-circle-check"></i> Setujui Pembayaran (DP 50% Terverifikasi)</span>
                            </label>
                            <label style="font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="status_pembayaran" value="rejected" <?php echo ($spCheck === 'rejected' || $spCheck === 'ditolak') ? 'checked' : ''; ?> style="accent-color: var(--danger); transform: scale(1.15);">
                                <span style="font-weight: 700; color: var(--danger);"><i class="fa-solid fa-circle-xmark"></i> Tolak Pembayaran (Batalkan Reservasi)</span>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-spa btn-spa-accent" style="width: 100%; justify-content: center; margin-top: 25px;">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Keputusan Verifikasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<div id="zoomModal" class="image-zoom-modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.85); align-items: center; justify-content: center;">
    <span class="zoom-close" style="position: absolute; top: 20px; right: 35px; color: #fff; font-size: 40px; font-weight: bold; cursor: pointer;">&times;</span>
    <img class="zoom-content" id="imgZoomed" style="margin: auto; display: block; max-width: 80%; max-height: 80%; object-fit: contain; margin-top: 5%;">
</div>
