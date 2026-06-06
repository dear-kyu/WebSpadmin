
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

<?php
$isOnline = ($res['reservation_type'] ?? '') === 'online';
$dpAmount = $isOnline ? ($res['total_price'] * 0.5) : 0;
$sp = $pembayaran['status_payment'] ?? $pembayaran['statusPayment'] ?? '';
$spCheck = strtolower($sp);
$jenisPembayaranAwal = $pembayaran['jenis_pembayaran'] ?? $pembayaran['jenisPembayaran'] ?? 'DP 50%';
$jumlahDibayar = $pembayaran['nominal_payment'] ?? $pembayaran['nominalPayment'] ?? $dpAmount;
$dpTerverifikasi = in_array($sp, ['verified', 'Diterima', 'Lunas'], true);
$sudahLunas = $sp === 'Lunas';
$sisaBayar = ($isOnline && !$sudahLunas) ? max(0, $res['total_price'] - $jumlahDibayar) : 0;
$pelunasanBisaDilakukan = $isOnline && in_array($sp, ['verified', 'Diterima'], true);
$pelunasanSudahSelesai  = $isOnline && $sp === 'Lunas';
$pref = $res['gender_terapis'] ?? $res['genderTerapis'] ?? 'Bebas';
$statusClass = 'badge-secondary';
if ($res['status_reservation'] === 'Menunggu') $statusClass = 'badge-warning';
elseif ($res['status_reservation'] === 'Diterima' || $res['status_reservation'] === 'Dikonfirmasi') $statusClass = 'badge-info';
elseif ($res['status_reservation'] === 'Selesai') $statusClass = 'badge-success';
elseif (in_array($res['status_reservation'], ['Dibatalkan', 'Ditolak'], true)) $statusClass = 'badge-danger';
?>

<?php if (false): ?>
<div class="reservation-detail-redesign" style="display: none;" aria-hidden="true">
    <div class="reservation-detail-hero">
        <div>
            <a href="admin.php?page=reservasi" class="detail-back-link">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Reservasi
            </a>
            <h2>Rincian Reservasi #<?php echo (int) $res['id_reservasi']; ?></h2>
            <p>Kelola jadwal, layanan, terapis, pembayaran, dan status reservasi dari satu halaman.</p>
        </div>
        <div class="detail-hero-status">
            <span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($res['status_reservation']); ?></span>
            <strong><?php echo date('d F Y', strtotime($res['reservation_date'])); ?></strong>
            <span><?php echo date('H:i', strtotime($res['reservation_date'])); ?> WIB</span>
        </div>
    </div>

    <section class="admin-detail-section">
        <div class="section-heading-row">
            <div>
                <span class="section-kicker">Ringkasan</span>
                <h3>Data Reservasi</h3>
            </div>
            <span class="detail-pill">
                <i class="fa-solid fa-<?php echo $isOnline ? 'globe' : 'cash-register'; ?>"></i>
                <?php echo $isOnline ? 'Reservasi Online' : 'Walk-In'; ?>
            </span>
        </div>
        <div class="detail-summary-grid">
            <div class="detail-info-item">
                <span>Pelanggan</span>
                <strong><?php echo htmlspecialchars($res['namaPelanggan']); ?></strong>
            </div>
            <div class="detail-info-item">
                <span>Email</span>
                <strong><?php echo htmlspecialchars($res['email_pelanggan']); ?></strong>
            </div>
            <div class="detail-info-item">
                <span>No. Telepon</span>
                <strong><?php echo htmlspecialchars($res['noHpPelanggan']); ?></strong>
            </div>
            <div class="detail-info-item">
                <span>Preferensi Terapis</span>
                <strong><?php echo htmlspecialchars($pref ?: 'Bebas'); ?></strong>
            </div>
            <div class="detail-info-item">
                <span>Ruangan</span>
                <strong><?php echo !empty($res['namaRuangan']) ? htmlspecialchars($res['namaRuangan']) : 'Belum dialokasikan'; ?></strong>
            </div>
            <div class="detail-info-item detail-total-item">
                <span>Total Reservasi</span>
                <strong><?php echo rupiah($res['total_price']); ?></strong>
            </div>
        </div>
    </section>

    <section class="admin-detail-section">
        <div class="section-heading-row">
            <div>
                <span class="section-kicker">Layanan & Terapis</span>
                <h3>Penugasan Per Layanan</h3>
            </div>
        </div>
        <div class="service-assignment-list">
            <?php foreach ($res['details'] as $detail): ?>
                <form action="admin.php?page=reservasi&action=assign" method="POST" class="service-assignment-card">
                    <input type="hidden" name="reservasi_id" value="<?php echo (int) $res['id_reservasi']; ?>">
                    <input type="hidden" name="idDetail" value="<?php echo (int) $detail['id_detail']; ?>">
                    <div class="service-assignment-info">
                        <span class="service-category"><?php echo htmlspecialchars($detail['kategori']); ?></span>
                        <strong><?php echo htmlspecialchars($detail['nama_layanan']); ?></strong>
                        <small><?php echo (int) $detail['durasi']; ?> menit &bull; <?php echo rupiah($detail['harga']); ?> x<?php echo (int) $detail['qty']; ?></small>
                    </div>
                    <div class="service-assignment-control">
                        <select name="terapis_id" class="form-control" required>
                            <option value="">-- Pilih Terapis --</option>
                            <?php foreach ($terapisAktif as $t): ?>
                                <option value="<?php echo (int) $t['id_terapis']; ?>" <?php echo $detail['id_terapis'] == $t['id_terapis'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($t['nama_terapis']); ?> (<?php echo htmlspecialchars($t['spesialisasi']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn-spa btn-spa-accent">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan
                        </button>
                    </div>
                </form>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="admin-detail-section payment-verification-section">
        <div class="section-heading-row">
            <div>
                <span class="section-kicker">Pembayaran</span>
                <h3>Pembayaran & Verifikasi</h3>
            </div>
        </div>

        <?php if (!$pembayaran): ?>
            <div class="empty-detail-state">
                <i class="fa-solid fa-receipt"></i>
                <p>Pelanggan belum melakukan konfirmasi pembayaran transfer untuk reservasi ini.</p>
            </div>
        <?php else: ?>
            <div class="payment-detail-layout">
                <div class="payment-proof-card">
                    <span>Bukti Pembayaran</span>
                    <div class="payment-proof-frame">
                        <?php if ($pembayaran['payment_proof'] && $pembayaran['payment_proof'] !== 'walk-in-payment'): ?>
                            <img src="uploads/pembayaran/<?php echo htmlspecialchars($pembayaran['payment_proof']); ?>" id="buktiImg" alt="Bukti Transfer">
                        <?php else: ?>
                            <i class="fa-solid fa-receipt"></i>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="payment-info-stack">
                    <div class="payment-stat-grid">
                        <div>
                            <span>Metode Awal</span>
                            <strong><?php echo htmlspecialchars($pembayaran['payment_method'] ?? $pembayaran['paymentMethod'] ?? 'Transfer'); ?></strong>
                        </div>
                        <div>
                            <span>Status Pembayaran</span>
                            <strong>
                                <?php if (in_array($sp, ['Diterima', 'verified'], true)): ?>
                                    <span class="badge badge-success">Diterima</span>
                                <?php elseif (in_array($sp, ['Menunggu Validasi', 'pending'], true)): ?>
                                    <span class="badge badge-warning">Menunggu Validasi</span>
                                <?php elseif (in_array($sp, ['Ditolak', 'rejected'], true)): ?>
                                    <span class="badge badge-danger">Ditolak</span>
                                <?php elseif ($sp === 'Lunas'): ?>
                                    <span class="badge badge-success">Lunas</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><?php echo htmlspecialchars($sp ?: '-'); ?></span>
                                <?php endif; ?>
                            </strong>
                        </div>
                        <div>
                            <span>Total Reservasi</span>
                            <strong><?php echo rupiah($res['total_price']); ?></strong>
                        </div>
                        <div>
                            <span>Dibayar (<?php echo htmlspecialchars($jenisPembayaranAwal); ?>)</span>
                            <strong><?php echo rupiah($jumlahDibayar); ?></strong>
                        </div>
                        <div>
                            <span>Sisa Pembayaran</span>
                            <strong><?php echo rupiah($sisaBayar); ?></strong>
                        </div>
                        <div>
                            <span>Diverifikasi Oleh</span>
                            <strong><?php echo !empty($pembayaran['nama_verifier']) ? htmlspecialchars($pembayaran['nama_verifier']) : '-'; ?></strong>
                        </div>
                    </div>

                    <?php if ($sp === 'Lunas'): ?>
                        <div class="payment-complete-note">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Pembayaran reservasi sudah lunas<?php echo !empty($pembayaran['pelunasan_date']) ? ' pada ' . date('d F Y H:i', strtotime($pembayaran['pelunasan_date'])) . ' WIB' : ''; ?>.</span>
                        </div>
                    <?php endif; ?>

                    <form action="admin.php?page=pembayaran&action=verifikasi&id=<?php echo (int) $pembayaran['id_payment']; ?>" method="POST" class="inline-payment-verification detail-verification-form">
                        <div class="inline-payment-verification-title">
                            <i class="fa-solid fa-stamp"></i>
                            <span>Verifikasi Pembayaran</span>
                        </div>
                        <?php if (!in_array($spCheck, ['pending', 'menunggu validasi', 'menunggu pembayaran'], true)): ?>
                            <div class="inline-payment-note">Pembayaran sudah pernah diproses. Status tetap bisa diubah jika diperlukan.</div>
                        <?php endif; ?>
                        <label>
                            <input type="radio" name="status_pembayaran" value="pending" <?php echo in_array($spCheck, ['pending', 'menunggu validasi', 'menunggu pembayaran'], true) ? 'checked' : ''; ?>>
                            <span>Pending</span>
                        </label>
                        <label>
                            <input type="radio" name="status_pembayaran" value="verified" <?php echo in_array($spCheck, ['verified', 'diterima', 'lunas'], true) ? 'checked' : ''; ?>>
                            <span>Setujui</span>
                        </label>
                        <label>
                            <input type="radio" name="status_pembayaran" value="rejected" <?php echo in_array($spCheck, ['rejected', 'ditolak'], true) ? 'checked' : ''; ?>>
                            <span>Tolak</span>
                        </label>
                        <button type="submit" class="btn-spa btn-spa-accent">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <section class="admin-detail-section">
        <div class="section-heading-row">
            <div>
                <span class="section-kicker">Operasional</span>
                <h3>Status Reservasi & Pelunasan</h3>
            </div>
        </div>
        <div class="operational-grid">
            <form action="admin.php?page=reservasi&action=assign" method="POST" class="operation-card">
                <input type="hidden" name="reservasi_id" value="<?php echo (int) $res['id_reservasi']; ?>">
                <label for="status">Ubah Status Reservasi</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="Menunggu" <?php echo $res['status_reservation'] === 'Menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                    <?php if ((!empty($pembayaran) && !empty($pembayaran['payment_proof'])) || in_array($res['status_reservation'], ['Diterima', 'Dikonfirmasi'], true)): ?>
                        <option value="Diterima" <?php echo $res['status_reservation'] === 'Diterima' ? 'selected' : ''; ?>>Diterima</option>
                        <option value="Dikonfirmasi" <?php echo $res['status_reservation'] === 'Dikonfirmasi' ? 'selected' : ''; ?>>Dikonfirmasi (DP Terverifikasi)</option>
                    <?php endif; ?>
                    <option value="Ditolak" <?php echo $res['status_reservation'] === 'Ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                    <option value="Dibatalkan" <?php echo $res['status_reservation'] === 'Dibatalkan' ? 'selected' : ''; ?>>Dibatalkan / Tidak Datang (DP Hangus)</option>
                    <option value="Selesai" <?php echo $res['status_reservation'] === 'Selesai' ? 'selected' : ''; ?>>Selesai (Treatment Selesai)</option>
                </select>
                <button type="submit" class="btn-spa btn-spa-outline">
                    <i class="fa-solid fa-circle-check"></i> Simpan Status
                </button>
            </form>

            <?php if ($pelunasanBisaDilakukan): ?>
                <form action="admin.php?page=reservasi&action=pelunasan" method="POST" class="operation-card">
                    <input type="hidden" name="reservasi_id" value="<?php echo (int) $res['id_reservasi']; ?>">
                    <div class="settlement-summary">
                        <div><span>Total</span><strong><?php echo rupiah($res['total_price']); ?></strong></div>
                        <div><span>Sudah Dibayar</span><strong><?php echo rupiah($jumlahDibayar); ?></strong></div>
                        <div><span>Sisa</span><strong><?php echo rupiah($sisaBayar); ?></strong></div>
                    </div>
                    <label for="metode_pelunasan_display">Metode Pelunasan</label>
                    <input type="hidden" id="metode_pelunasan" name="metode_pelunasan" value="">
                    <select id="metode_pelunasan_display" class="form-control" onchange="togglePelunasanCashSection(this.value)" required>
                        <option value="">-- Pilih Metode Pembayaran --</option>
                        <option value="Cash">Tunai (Cash)</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="E-Wallet">E-Wallet (OVO/GoPay/QRIS)</option>
                    </select>
                    <div id="pelunasanBankSection" class="form-group" style="display: none;">
                        <label for="pelunasan_bank">Pilih Rekening Bank</label>
                        <select id="pelunasan_bank" class="form-control" onchange="syncPelunasanBank(this.value)">
                            <option value="">-- Pilih Bank --</option>
                            <?php foreach ($metodePembayaranAktif as $metodeAktif): ?>
                                <option value="<?php echo htmlspecialchars($metodeAktif); ?>"><?php echo htmlspecialchars($metodeAktif); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="pelunasanCashInputSection" class="cash-grid" style="display: none;">
                        <div class="form-group">
                            <label for="pelunasan_uang_bayar">Uang Bayar (Rp)</label>
                            <input type="number" id="pelunasan_uang_bayar" name="pelunasan_uang_bayar" class="form-control" placeholder="Jumlah uang..." min="0" oninput="calculatePelunasanChange(<?php echo $sisaBayar; ?>)">
                        </div>
                        <div class="form-group">
                            <label for="pelunasan_kembalian_display">Kembalian</label>
                            <input type="text" id="pelunasan_kembalian_display" class="form-control" value="Rp 0" readonly>
                        </div>
                    </div>
                    <button type="submit" onclick="return validatePelunasanCash(<?php echo $sisaBayar; ?>)" class="btn-spa btn-spa-accent">
                        <i class="fa-solid fa-circle-check"></i> Konfirmasi Pelunasan
                    </button>
                </form>
            <?php elseif ($pelunasanSudahSelesai): ?>
                <div class="operation-card settlement-done">
                    <i class="fa-solid fa-circle-check"></i>
                    <strong>Pembayaran Lunas</strong>
                    <p>Seluruh pembayaran reservasi telah diselesaikan langsung di SPA.</p>
                </div>
            <?php else: ?>
                <div class="operation-card settlement-muted">
                    <i class="fa-solid fa-wallet"></i>
                    <strong>Pelunasan belum tersedia</strong>
                    <p>Pelunasan muncul setelah pembayaran awal online diterima.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php endif; ?>

<div>
<div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 30px; margin-bottom: 40px;">
    
    
    <div>
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Rincian Reservasi #<?php echo $res['id_reservasi']; ?></h3>
                <a href="admin.php?page=reservasi" class="btn-spa btn-spa-outline">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>
            
            <div class="panel-body">
                <table class="custom-table" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td style="width: 200px; font-weight: 600; border-bottom: 1px solid var(--border-color); color: var(--primary);">Nama Pelanggan</td>
                            <td style="border-bottom: 1px solid var(--border-color);"><?php echo htmlspecialchars($res['namaPelanggan']); ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; border-bottom: 1px solid var(--border-color); color: var(--primary);">Email / Telepon</td>
                            <td style="border-bottom: 1px solid var(--border-color);">
                                <?php echo htmlspecialchars($res['email_pelanggan']); ?> / <?php echo htmlspecialchars($res['noHpPelanggan']); ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; border-bottom: 1px solid var(--border-color); color: var(--primary);">Layanan SPA Dipesan</td>
                            <td style="border-bottom: 1px solid var(--border-color);">
                                <ul style="margin: 0; padding-left: 15px; font-size: 0.9rem;">
                                    <?php foreach ($res['details'] as $detail): ?>
                                        <li style="margin-bottom: 10px;">
                                            <strong><?php echo htmlspecialchars($detail['nama_layanan']); ?></strong><br>
                                            <span class="text-muted"><?php echo $detail['durasi']; ?> menit &bull; <?php echo rupiah($detail['harga']); ?> (x<?php echo $detail['qty']; ?>)</span><br>
                                            <span style="font-size: 0.8rem; color: var(--primary-light); display: inline-block; margin-top: 3px;">
                                                <i class="fa-solid fa-user-doctor" style="color: var(--accent); margin-right: 4px;"></i> 
                                                Terapis: <strong><?php echo $detail['nama_terapis'] ? htmlspecialchars($detail['nama_terapis']) : 'Belum Ditugaskan'; ?></strong>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; border-bottom: 1px solid var(--border-color); color: var(--primary);">Tanggal Reservasi</td>
                            <td style="border-bottom: 1px solid var(--border-color);">
                                <strong><?php echo date('d F Y', strtotime($res['reservation_date'])); ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; border-bottom: 1px solid var(--border-color); color: var(--primary);">Jam Kedatangan</td>
                            <td style="border-bottom: 1px solid var(--border-color);">
                                <strong><?php echo date('H:i', strtotime($res['reservation_date'])); ?> WIB</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; border-bottom: 1px solid var(--border-color); color: var(--primary);">Preferensi Terapis (Gender)</td>
                            <td style="border-bottom: 1px solid var(--border-color);">
                                <?php 
                                $pref = $res['gender_terapis'] ?? $res['genderTerapis'] ?? 'Bebas'; 
                                if ($pref === 'Laki-Laki' || $pref === 'Laki-laki' || $pref === 'Pria'): 
                                ?>
                                    <span style="font-weight: 600; color: #3b82f6;"><i class="fa-solid fa-mars" style="margin-right: 5px;"></i> Laki-Laki</span>
                                <?php elseif ($pref === 'Perempuan'): ?>
                                    <span style="font-weight: 600; color: #ec4899;"><i class="fa-solid fa-venus" style="margin-right: 5px;"></i> Perempuan</span>
                                <?php else: ?>
                                    <span class="badge badge-info" style="background: var(--primary-light); color: white; border-radius: 4px; padding: 2px 6px; font-size: 0.8rem;"><i class="fa-solid fa-genderless" style="margin-right: 5px;"></i> Bebas (Mana Saja)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; border-bottom: 1px solid var(--border-color); color: var(--primary);">Ruangan SPA</td>
                            <td style="border-bottom: 1px solid var(--border-color);">
                                <?php if (!empty($res['namaRuangan'])): ?>
                                    <span style="font-weight: 600; color: var(--primary-light);">
                                        <i class="fa-solid fa-door-open" style="color: var(--accent); margin-right: 5px;"></i> <?php echo htmlspecialchars($res['namaRuangan']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><i class="fa-solid fa-circle-question" style="margin-right: 5px;"></i> Belum Dialokasikan</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; border-bottom: 1px solid var(--border-color); color: var(--primary);">Total Harga</td>
                            <td style="border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--accent-hover); font-size: 1.1rem;">
                                <?php echo rupiah($res['total_price']); ?>
                            </td>
                        </tr>
                        <?php 
                        $isOnline = ($res['reservation_type'] ?? '') === 'online';
                        $dpAmount = $isOnline ? ($res['total_price'] * 0.5) : 0;
                        $sp = $pembayaran['status_payment'] ?? $pembayaran['statusPayment'] ?? '';
                        $jenisPembayaranAwal = $pembayaran['jenis_pembayaran'] ?? $pembayaran['jenisPembayaran'] ?? 'DP 50%';
                        $jumlahDibayar = $pembayaran['nominal_payment'] ?? $pembayaran['nominalPayment'] ?? $dpAmount;
                        $dpTerverifikasi = in_array($sp, ['verified', 'Diterima', 'Lunas'], true);
                        $sudahLunas = $sp === 'Lunas';
                        $sisaBayar = ($isOnline && !$sudahLunas) ? max(0, $res['total_price'] - $jumlahDibayar) : 0;
                        ?>
                        <tr>
                            <td style="font-weight: 600; border-bottom: 1px solid var(--border-color); color: var(--primary);">Status Reservasi</td>
                            <td style="border-bottom: 1px solid var(--border-color);">
                                <?php 
                                $statusClass = 'badge-secondary';
                                if ($res['status_reservation'] === 'Menunggu') $statusClass = 'badge-warning';
                                elseif ($res['status_reservation'] === 'Diterima') $statusClass = 'badge-info';
                                elseif ($res['status_reservation'] === 'Selesai') $statusClass = 'badge-success';
                                elseif ($res['status_reservation'] === 'Dibatalkan') $statusClass = 'badge-danger';
                                elseif ($res['status_reservation'] === 'Ditolak') $statusClass = 'badge-danger';
                                ?>
                                <span class="badge <?php echo $statusClass; ?>" style="font-size: 0.9rem;"><?php echo $res['status_reservation']; ?></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Pembayaran & Verifikasi</h3>
            </div>
            <div class="panel-body">
                <?php if (!$pembayaran): ?>
                    <div style="text-align: center; padding: 20px; color: var(--text-muted);">
                        <i class="fa-solid fa-receipt mb-2 d-block fs-3"></i> Pelanggan belum melakukan konfirmasi pembayaran transfer untuk reservasi ini.
                    </div>
                <?php else: ?>
                    <?php 
                    $sp = $pembayaran['status_payment'] ?? $pembayaran['statusPayment'] ?? '';
                    $spCheck = strtolower($sp);
                    ?>
                    <div class="admin-payment-summary">
                        <div class="admin-payment-proof">
                            <span class="admin-payment-label">Bukti Bayar</span>
                            <div class="admin-payment-proof-frame">
                                <?php if ($pembayaran['payment_proof'] && $pembayaran['payment_proof'] !== 'walk-in-payment'): ?>
                                    <img src="uploads/pembayaran/<?php echo htmlspecialchars($pembayaran['payment_proof']); ?>" 
                                         id="buktiImg" alt="Bukti Transfer">
                                <?php else: ?>
                                    <i class="fa-solid fa-receipt"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="admin-payment-content">
                            <div class="admin-payment-facts">
                                <div class="admin-payment-fact">
                                    <span>Metode Pembayaran</span>
                                    <strong><?php echo htmlspecialchars($pembayaran['payment_method'] ?? $pembayaran['paymentMethod'] ?? 'Transfer'); ?></strong>
                                </div>
                                <div class="admin-payment-fact">
                                    <span>Status Validasi</span>
                                    <strong>
                                        <?php if ($sp === 'Diterima' || $sp === 'verified'): ?>
                                            <span class="badge badge-success">Diterima (Verified)</span>
                                        <?php elseif ($sp === 'Menunggu Validasi' || $sp === 'pending'): ?>
                                            <span class="badge badge-warning">Menunggu Validasi</span>
                                        <?php elseif ($sp === 'DP Hangus' || $sp === 'Pembayaran Hangus'): ?>
                                            <span class="badge badge-danger"><?php echo htmlspecialchars($sp); ?> (Tidak Dikembalikan)</span>
                                        <?php elseif ($sp === 'Ditolak' || $sp === 'rejected'): ?>
                                            <span class="badge badge-danger">Ditolak (Rejected)</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?php echo htmlspecialchars($sp); ?></span>
                                        <?php endif; ?>
                                    </strong>
                                </div>
                                <?php if (!empty($pembayaran['nama_verifier'])): ?>
                                    <div class="admin-payment-fact">
                                        <span>Diverifikasi Oleh</span>
                                        <strong><?php echo htmlspecialchars($pembayaran['nama_verifier']); ?></strong>
                                    </div>
                                <?php endif; ?>
                                <?php if ($sp === 'Lunas'): ?>
                                    <div class="admin-payment-fact">
                                        <span><?php echo $jenisPembayaranAwal === 'Lunas 100%' ? 'Metode Pembayaran Lunas' : 'Metode Pelunasan'; ?></span>
                                        <strong><?php echo htmlspecialchars($jenisPembayaranAwal === 'Lunas 100%' ? ($pembayaran['payment_method'] ?? '-') : ($pembayaran['pelunasan_method'] ?? '-')); ?></strong>
                                    </div>
                                    <div class="admin-payment-fact">
                                        <span>Tanggal Pelunasan</span>
                                        <strong><?php echo !empty($pembayaran['pelunasan_date']) ? date('d F Y H:i', strtotime($pembayaran['pelunasan_date'])) . ' WIB' : '-'; ?></strong>
                                    </div>
                                    <?php if (($pembayaran['pelunasan_method'] ?? '') === 'Cash'): ?>
                                        <div class="admin-payment-fact">
                                            <span>Uang Bayar</span>
                                            <strong><?php echo rupiah($pembayaran['pelunasan_uang_bayar'] ?? 0); ?></strong>
                                        </div>
                                        <div class="admin-payment-fact">
                                            <span>Kembalian</span>
                                            <strong><?php echo rupiah($pembayaran['pelunasan_kembalian'] ?? 0); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            
                            <form action="admin.php?page=pembayaran&action=verifikasi&id=<?php echo (int) $pembayaran['id_payment']; ?>" method="POST" class="inline-payment-verification admin-payment-verification">
                                <div class="inline-payment-verification-title">
                                    <i class="fa-solid fa-stamp"></i>
                                    <span>Verifikasi Pembayaran</span>
                                </div>
                                <?php if (!in_array($spCheck, ['pending', 'menunggu validasi', 'menunggu pembayaran'], true)): ?>
                                    <div class="inline-payment-note">
                                        <i class="fa-solid fa-circle-info"></i>
                                        <span>Pembayaran sudah pernah diproses. Status tetap bisa diubah jika diperlukan.</span>
                                    </div>
                                <?php endif; ?>
                                <div class="verification-options-group">
                                    <label class="verification-option-card">
                                        <input type="radio" name="status_pembayaran" value="pending" <?php echo in_array($spCheck, ['pending', 'menunggu validasi', 'menunggu pembayaran'], true) ? 'checked' : ''; ?>>
                                        <span>Pending</span>
                                    </label>
                                    <label class="verification-option-card">
                                        <input type="radio" name="status_pembayaran" value="verified" <?php echo in_array($spCheck, ['verified', 'diterima', 'lunas'], true) ? 'checked' : ''; ?>>
                                        <span>Setujui</span>
                                    </label>
                                    <label class="verification-option-card">
                                        <input type="radio" name="status_pembayaran" value="rejected" <?php echo in_array($spCheck, ['rejected', 'ditolak'], true) ? 'checked' : ''; ?>>
                                        <span>Tolak</span>
                                    </label>
                                </div>
                                <button type="submit" class="btn-spa btn-spa-accent">
                                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    
    <div>
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Terapis & Status Operasional</h3>
            </div>
            
            <div class="panel-body">
                <h4 style="font-size: 0.95rem; color: var(--primary); margin-bottom: 15px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                    <i class="fa-solid fa-user-doctor" style="color: var(--accent); margin-right: 5px;"></i> Penugasan Terapis per Layanan
                </h4>
                
                <?php foreach ($res['details'] as $detail): ?>
                    <form action="admin.php?page=reservasi&action=assign" method="POST" style="margin-bottom: 15px; background: var(--bg-light); padding: 10px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                        <input type="hidden" name="reservasi_id" value="<?php echo $res['id_reservasi']; ?>">
                        <input type="hidden" name="idDetail" value="<?php echo $detail['id_detail']; ?>">
                        
                        <div class="form-group" style="margin-bottom: 8px;">
                            <span style="font-weight: 600; font-size: 0.85rem; display: block; color: var(--text-dark);">
                                <?php echo htmlspecialchars($detail['nama_layanan']); ?>
                            </span>
                            <span class="text-muted" style="font-size: 0.75rem;">Kategori: <?php echo htmlspecialchars($detail['kategori']); ?></span>
                        </div>
                        
                        <div style="display: flex; gap: 8px;">
                            <select name="terapis_id" class="form-control" style="font-size: 0.8rem; height: 32px; padding: 4px 8px;" required>
                                <option value="">-- Pilih Terapis --</option>
                                <?php foreach ($terapisAktif as $t): ?>
                                    <?php 
                                    $tJk = $t['jenis_kelamin'] ?? $t['jenisKelamin'] ?? 'Perempuan'; 
                                    if ($tJk === 'Pria') $tJk = 'Laki-Laki';
                                    ?>
                                    <option value="<?php echo $t['id_terapis']; ?>" <?php echo $detail['id_terapis'] == $t['id_terapis'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($t['nama_terapis']); ?> (<?php echo htmlspecialchars($t['spesialisasi']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn-spa btn-spa-accent" style="padding: 4px 10px; font-size: 0.75rem; height: 32px; flex-shrink: 0;">
                                Simpan
                            </button>
                        </div>
                    </form>
                <?php endforeach; ?>

                <form action="admin.php?page=reservasi&action=assign" method="POST" style="margin-top: 25px; border-top: 1px solid var(--border-color); padding-top: 15px;">
                    <input type="hidden" name="reservasi_id" value="<?php echo $res['id_reservasi']; ?>">
                    
                    <div class="form-group">
                        <label for="status" style="font-weight: 600; font-size: 0.85rem; color: var(--primary);">Ubah Status Reservasi Global</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="Menunggu" <?php echo $res['status_reservation'] === 'Menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                            <?php if ((!empty($pembayaran) && !empty($pembayaran['payment_proof'])) || in_array($res['status_reservation'], ['Diterima', 'Dikonfirmasi'])): ?>
                                <option value="Diterima" <?php echo $res['status_reservation'] === 'Diterima' ? 'selected' : ''; ?>>Diterima</option>
                                <option value="Dikonfirmasi" <?php echo $res['status_reservation'] === 'Dikonfirmasi' ? 'selected' : ''; ?>>Dikonfirmasi (DP Terverifikasi)</option>
                            <?php endif; ?>
                            <option value="Ditolak" <?php echo $res['status_reservation'] === 'Ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                            <option value="Dibatalkan" <?php echo $res['status_reservation'] === 'Dibatalkan' ? 'selected' : ''; ?>>Dibatalkan / Tidak Datang (DP Hangus)</option>
                            <option value="Selesai" <?php echo $res['status_reservation'] === 'Selesai' ? 'selected' : ''; ?>>Selesai (Treatment Selesai)</option>
                        </select>
                    </div>
                    
                    <div style="margin-top: 15px;">
                        <button type="submit" class="btn-spa btn-spa-outline" style="width: 100%; justify-content: center; font-size: 0.85rem; padding: 8px;">
                            <i class="fa-solid fa-circle-check"></i> Simpan Status Global
                        </button>
                    </div>
                </form>
                
                <?php 
                $pelunasanBisaDilakukan = $isOnline && in_array($sp, ['verified', 'Diterima'], true);
                $pelunasanSudahSelesai  = $isOnline && $sp === 'Lunas';
                ?>

                <?php if ($pelunasanBisaDilakukan): ?>
                    <div style="margin-top: 15px; border-top: 1px dashed var(--border-color); padding-top: 15px;">
                        <h4 style="font-size: 0.9rem; color: var(--accent); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-cash-register"></i> Proses Pelunasan Sisa Pembayaran (50%)
                        </h4>
                        <form action="admin.php?page=reservasi&action=pelunasan" method="POST">
                            <input type="hidden" name="reservasi_id" value="<?php echo $res['id_reservasi']; ?>">
                            
                            <div style="background: var(--bg-light); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 12px; margin-bottom: 12px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <span style="font-size: 0.82rem; color: var(--text-muted);">Total harga layanan</span>
                                    <span style="font-weight: 600; color: var(--text-dark);"><?php echo rupiah($res['total_price']); ?></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <span style="font-size: 0.82rem; color: var(--text-muted);">Sudah dibayar (<?php echo htmlspecialchars($jenisPembayaranAwal); ?>)</span>
                                    <span style="font-weight: 600; color: var(--success);"><?php echo rupiah($jumlahDibayar); ?></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 6px;">
                                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-dark);">Sisa yang harus dilunasi</span>
                                    <span style="font-size: 1rem; font-weight: 700; color: var(--accent-hover);"><?php echo rupiah($sisaBayar); ?></span>
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom: 12px;">
                                <label for="metode_pelunasan_display" style="font-weight: 600; font-size: 0.82rem; color: var(--primary);">Metode Pelunasan</label>
                                <input type="hidden" id="metode_pelunasan" name="metode_pelunasan" value="">
                                <select id="metode_pelunasan_display" class="form-control" onchange="togglePelunasanCashSection(this.value)" required>
                                    <option value="">-- Pilih Metode Pembayaran --</option>
                                    <option value="Cash">Tunai (Cash)</option>
                                    <option value="Transfer Bank">Transfer Bank</option>
                                    <option value="E-Wallet">E-Wallet (OVO/GoPay/QRIS)</option>
                                </select>
                            </div>

                            <div id="pelunasanBankSection" class="form-group" style="display: none; margin-bottom: 12px;">
                                <label for="pelunasan_bank" style="font-weight: 600; font-size: 0.82rem; color: var(--primary);">Pilih Rekening Bank</label>
                                <select id="pelunasan_bank" class="form-control" onchange="syncPelunasanBank(this.value)">
                                    <option value="">-- Pilih Bank --</option>
                                    <?php foreach ($metodePembayaranAktif as $metodeAktif): ?>
                                        <option value="<?php echo htmlspecialchars($metodeAktif); ?>"><?php echo htmlspecialchars($metodeAktif); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div id="pelunasanCashInputSection" style="display: none; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                                <div class="form-group">
                                    <label for="pelunasan_uang_bayar" style="font-weight: 600; font-size: 0.82rem; color: var(--primary);">Uang Bayar (Rp)</label>
                                    <input type="number" id="pelunasan_uang_bayar" name="pelunasan_uang_bayar" class="form-control" placeholder="Jumlah uang..." min="0" oninput="calculatePelunasanChange(<?php echo $sisaBayar; ?>)">
                                </div>
                                <div class="form-group">
                                    <label for="pelunasan_kembalian_display" style="font-weight: 600; font-size: 0.82rem; color: var(--primary);">Kembalian</label>
                                    <input type="text" id="pelunasan_kembalian_display" class="form-control" value="Rp 0" readonly style="background-color: var(--bg-light); font-weight: 700; color: var(--accent-hover);">
                                </div>
                            </div>
                            
                            <button type="submit" onclick="return validatePelunasanCash(<?php echo $sisaBayar; ?>)" class="btn-spa btn-spa-accent" style="width: 100%; justify-content: center; font-size: 0.85rem; padding: 10px; font-weight: 700; margin-top: 10px;">
                                <i class="fa-solid fa-circle-check"></i> Konfirmasi Pelunasan
                            </button>
                        </form>
                    </div>
                <?php elseif ($pelunasanSudahSelesai): ?>
                    <div style="margin-top: 15px; border-top: 1px dashed var(--border-color); padding-top: 15px; background: var(--success-bg, #e8f8f5); border-radius: var(--radius-sm); padding: 12px; text-align: center;">
                        <i class="fa-solid fa-circle-check" style="color: var(--success); font-size: 1.5rem; display: block; margin-bottom: 6px;"></i>
                        <strong style="color: var(--success); font-size: 0.9rem;">Pembayaran Lunas</strong>
                        <p style="margin: 4px 0 0; font-size: 0.8rem; color: var(--text-muted);">Seluruh pembayaran reservasi telah diselesaikan langsung di SPA.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Panel Rating dihapus sesuai permintaan user -->
    </div>
</div>
</div>



<div id="zoomModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.85); align-items: center; justify-content: center;">
    <span class="zoom-close" style="position: absolute; top: 20px; right: 35px; color: #fff; font-size: 40px; font-weight: bold; cursor: pointer;">&times;</span>
    <img id="imgZoomed" style="margin: auto; display: block; max-width: 80%; max-height: 80%; object-fit: contain; margin-top: 5%;">
</div>

<script>
function togglePelunasanCashSection(value) {
    var section = document.getElementById('pelunasanCashInputSection');
    var bankSection = document.getElementById('pelunasanBankSection');
    var bankSelect = document.getElementById('pelunasan_bank');
    var metodeInput = document.getElementById('metode_pelunasan');
    var uangBayar = document.getElementById('pelunasan_uang_bayar');
    var kembalian = document.getElementById('pelunasan_kembalian_display');
    if (!section) return;

    section.style.display = value === 'Cash' ? 'grid' : 'none';
    if (bankSection) bankSection.style.display = value === 'Transfer Bank' ? 'block' : 'none';
    if (metodeInput) metodeInput.value = value === 'Transfer Bank' ? (bankSelect ? bankSelect.value : '') : value;
    if (value !== 'Cash') {
        if (uangBayar) uangBayar.value = '';
        if (kembalian) kembalian.value = 'Rp 0';
    }
    if (value !== 'Transfer Bank' && bankSelect) {
        bankSelect.value = '';
    }
}

function syncPelunasanBank(value) {
    var metodeInput = document.getElementById('metode_pelunasan');
    if (metodeInput) metodeInput.value = value;
}

function calculatePelunasanChange(sisaPembayaran) {
    var uangBayar = parseFloat(document.getElementById('pelunasan_uang_bayar').value) || 0;
    var kembalian = Math.max(0, uangBayar - sisaPembayaran);
    document.getElementById('pelunasan_kembalian_display').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(kembalian);
}

function validatePelunasanCash(sisaPembayaran) {
    var metodeDisplay = document.getElementById('metode_pelunasan_display').value;
    var metode = document.getElementById('metode_pelunasan').value;
    if (metodeDisplay === 'Transfer Bank' && !metode) {
        alert('Pilih bank tujuan pelunasan terlebih dahulu.');
        return false;
    }
    if (metode === 'Cash') {
        var uangBayar = parseFloat(document.getElementById('pelunasan_uang_bayar').value) || 0;
        if (uangBayar < sisaPembayaran) {
            alert('Uang bayar kurang! Harus >= Rp ' + new Intl.NumberFormat('id-ID').format(sisaPembayaran));
            return false;
        }
    }
    return true;
}

// ===== Zoom Modal Bukti Bayar =====
(function() {
    var buktiImg = document.getElementById('buktiImg');
    var zoomModal = document.getElementById('zoomModal');
    var imgZoomed = document.getElementById('imgZoomed');
    var zoomClose = document.querySelector('.zoom-close');

    if (buktiImg && zoomModal) {
        buktiImg.addEventListener('click', function() {
            imgZoomed.src = this.src;
            zoomModal.style.display = 'flex';
        });
        zoomClose.addEventListener('click', function() {
            zoomModal.style.display = 'none';
        });
        zoomModal.addEventListener('click', function(e) {
            if (e.target === zoomModal) zoomModal.style.display = 'none';
        });
    }
})();
</script>

