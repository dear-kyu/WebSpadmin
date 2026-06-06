<?php 
$judulHalaman = 'Pembayaran Reservasi - SPAdmin Spa Bandung'; 
include __DIR__ . '/../templates/header.php'; 

// Cek waktu kedaluwarsa (30 menit dari created_at)
$createdTime = strtotime($reservasi['created_at']);
$expiryTime = $createdTime + 30 * 60; // 30 menit
$timeRemaining = $expiryTime - time();
if ($timeRemaining < 0) {
    $timeRemaining = 0;
}
$statusReservasi = $reservasi['status_reservasi'] ?? '';
$statusPembayaran = $reservasi['status_pembayaran'] ?? 'Belum Upload';
$isBelumBayar = in_array($statusPembayaran, ['Belum Upload', 'Menunggu Pembayaran', 'pending'], true);
$isUnpaidExpired = (
    $timeRemaining <= 0
    && $isBelumBayar
    && !in_array($statusReservasi, ['Diterima', 'Dikonfirmasi', 'Selesai', 'Menunggu Validasi'], true)
);
$isReservasiHangus = in_array($statusReservasi, ['Hangus', 'Dibatalkan'], true) || $isUnpaidExpired;

// Format tanggal Indonesia
$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$dateObj = strtotime($reservasi['tanggal']);
$tglFormat = date('j', $dateObj) . ' ' . $bulanIndo[(int)date('n', $dateObj)] . ' ' . date('Y', $dateObj);
$jamFormat = date('H:i', strtotime($reservasi['jam'])) . ' WIB';
?>

<style>
    .success-payment-wrap,
    .success-payment-wrap button,
    .success-payment-wrap input,
    .success-payment-wrap select,
    .success-payment-wrap textarea {
        font-family: 'Inter', sans-serif;
    }

    .success-payment-wrap h1,
    .success-payment-wrap h2,
    .success-payment-wrap h3,
    .success-payment-wrap h4,
    .success-payment-wrap .invoice-main-title,
    .success-payment-wrap .invoice-total-value-large,
    .success-payment-wrap .invoice-dp-value-large {
        font-family: 'Playfair Display', Georgia, serif;
    }

    .premium-invoice-card {
        background: #ffffff;
        border: 1px solid #efeae4;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 10px 40px rgba(122, 91, 67, 0.04);
        margin-bottom: 2.5rem;
        position: relative;
        overflow: hidden;
    }

    .invoice-header-icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #fdf8f5;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(122, 91, 67, 0.12);
        color: #7a5b43;
        flex-shrink: 0;
    }

    .invoice-main-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.65rem;
        font-weight: 700;
        color: #221d1b;
    }

    .invoice-main-subtitle {
        font-family: 'Inter', sans-serif;
        font-size: 0.88rem;
        color: #8c827a;
        font-weight: 400;
    }

    .invoice-icon-circle-small {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #fdf8f5;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(122, 91, 67, 0.1);
        color: #7a5b43;
        flex-shrink: 0;
    }

    .invoice-section-title {
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem;
        font-weight: 700;
        color: #221d1b;
    }

    .invoice-section-desc {
        font-family: 'Inter', sans-serif;
        font-size: 0.84rem;
        color: #8c827a;
    }

    .invoice-service-name {
        font-family: 'Inter', sans-serif;
        font-size: 0.92rem;
        font-weight: 600;
        color: #221d1b;
    }

    .invoice-service-duration {
        font-size: 0.88rem;
        color: #8c827a;
        font-weight: 400;
    }

    .invoice-service-price {
        font-family: 'Inter', sans-serif;
        font-size: 0.92rem;
        font-weight: 700;
        color: #221d1b;
    }

    .invoice-row-label {
        font-family: 'Inter', sans-serif;
        font-size: 0.92rem;
        font-weight: 600;
        color: #221d1b;
    }

    .invoice-row-value {
        font-family: 'Inter', sans-serif;
        font-size: 0.92rem;
        font-weight: 700;
        color: #221d1b;
    }

    .invoice-duration-banner {
        background: linear-gradient(135deg, rgba(253, 248, 245, 0.45) 0%, rgba(253, 248, 245, 0.85) 100%);
        border-radius: 12px;
        border: 1px solid rgba(122, 91, 67, 0.08);
        padding: 0.85rem 1.25rem;
        box-shadow: 
            0 4px 15px rgba(122, 91, 67, 0.02), 
            inset 0 1px 0 rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        transition: all 0.3s ease;
    }

    .invoice-duration-banner:hover {
        border-color: rgba(122, 91, 67, 0.15);
        box-shadow: 
            0 6px 20px rgba(122, 91, 67, 0.04), 
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
        transform: translateY(-1px);
    }

    @media (max-width: 767.98px) {
        .invoice-section-row {
            flex-direction: column;
            gap: 1.5rem !important;
        }
        .invoice-left-block {
            width: 100% !important;
        }
        .invoice-vertical-divider {
            display: none;
        }
    }
</style>

<div class="container py-5">
<div class="success-payment-wrap">
        
        <?php if (isset($_GET['pesan'])): ?>
            <!-- Premium Error Banner -->
            <?php 
            $pesanError = '';
            if ($_GET['pesan'] === 'upload-gagal') {
                $pesanError = 'Gagal mengunggah bukti pembayaran. Silakan coba kembali atau gunakan file gambar yang valid.';
            } elseif ($_GET['pesan'] === 'format-bukti-salah') {
                $pesanError = 'Format file tidak sesuai atau ukuran terlalu besar. Pastikan file berupa JPG, JPEG, atau PNG dengan ukuran maksimal 2MB.';
            } elseif ($_GET['pesan'] === 'rekening-tidak-valid') {
                $pesanError = 'Silakan pilih rekening tujuan pembayaran terlebih dahulu.';
            } elseif ($_GET['pesan'] === 'jenis-pembayaran-tidak-valid') {
                $pesanError = 'Silakan pilih pembayaran DP 50% atau Lunas 100%.';
            }
            ?>
            <?php if ($pesanError !== ''): ?>
                <div class="alert alert-spadmin-premium alert-danger-premium mb-4" role="alert">
                    <div class="alert-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <div class="alert-content">
                        <strong>Unggahan Gagal</strong>
                        <span><?= e($pesanError) ?></span>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($isReservasiHangus): ?>
            <!-- CASE 0: RESERVASI HANGUS / EXPIRED -->
            <?php
            $expiredDetailLayanan = ambilDetailReservasi($conn, $reservasi['id']);
            $expiredLayananNames = array_map(static function ($dl) {
                return $dl['nama_layanan'];
            }, $expiredDetailLayanan);
            ?>
            <section class="expired-payment-state">
                <div class="expired-status-hero">
                    <div class="expired-status-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <div class="expired-status-copy">
                        <span class="expired-kicker">Status Reservasi</span>
                        <h1>Reservasi Hangus</h1>
                        <p>Reservasi ini tidak lagi berlaku. Silakan buat reservasi baru untuk memilih jadwal dan terapis yang tersedia.</p>
                    </div>
                </div>

                <div class="expired-summary-section">
                    <div class="expired-summary-heading">
                        <div>
                            <span class="expired-kicker">Ringkasan</span>
                            <h2>Detail Reservasi #<?= (int) $reservasi['id'] ?></h2>
                        </div>
                        <span class="expired-status-pill">Hangus</span>
                    </div>

                    <div class="expired-summary-grid">
                        <div class="expired-summary-item">
                            <span>Layanan</span>
                            <strong><?= e(implode(', ', $expiredLayananNames)) ?></strong>
                        </div>
                        <div class="expired-summary-item">
                            <span>Terapis</span>
                            <strong><?= e($reservasi['nama_terapis']) ?></strong>
                        </div>
                        <div class="expired-summary-item">
                            <span>Jadwal</span>
                            <strong><?= $tglFormat ?> - <?= $jamFormat ?></strong>
                        </div>
                        <div class="expired-summary-item">
                            <span>Total</span>
                            <strong><?= rupiah($reservasi['harga']) ?></strong>
                        </div>
                    </div>
                </div>

                <div class="expired-info-line">
                    <span aria-hidden="true"></span>
                    <p>Pembayaran yang telah dikonfirmasi tidak dapat dikembalikan apabila pelanggan tidak hadir sesuai jadwal.</p>
                </div>

                <div class="expired-actions">
                    <a href="index.php?action=layanan" class="expired-primary-action">Buat Reservasi Baru</a>
                    <a href="index.php?action=riwayat" class="expired-secondary-action">Lihat Riwayat</a>
                </div>
            </section>

            <div class="already-paid-card text-center py-5 d-none" aria-hidden="true">
                <div class="success-icon-container">
                    <div class="success-circle-icon" style="background: linear-gradient(135deg, #fce8e6, #f8d7da); border-color: rgba(197, 34, 31, 0.15);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#c5221f" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </div>
                </div>
                <h2 style="color: #c5221f;">Reservasi Hangus</h2>
                <p class="mb-4">Reservasi ini tidak lagi berlaku. Jika DP sudah diverifikasi dan Anda tidak datang, DP hangus dan tidak dapat dikembalikan. Silakan buat reservasi baru untuk melanjutkan.</p>
                
                <!-- Expired Reservation Summary -->
                <?php 
                $detailLayanan = ambilDetailReservasi($conn, $reservasi['id']); 
                $totalDurasi = 0;
                foreach ($detailLayanan as $dl) {
                    $totalDurasi += $dl['durasi'];
                }
                $totalJam = $totalDurasi / 60;
                $jamText = ($totalJam == (int)$totalJam) ? (int)$totalJam : number_format($totalJam, 1, ',', '.');
                ?>
                <div class="premium-invoice-card text-start" style="opacity: 0.65; pointer-events: none; max-width: 700px; margin: 0 auto 2rem;">
                    <!-- Header -->
                    <div class="invoice-header d-flex align-items-center gap-3 mb-4 pb-3" style="border-bottom: 1px solid #efeae4;">
                        <div class="invoice-header-icon-circle" style="color: #999; background: #fafafa;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h2 class="invoice-main-title mb-1" style="color: #666; font-size: 1.45rem;">Detail Reservasi #<?= (int) $reservasi['id'] ?> <span class="badge-status hangus ms-2" style="font-size: 0.65rem; padding: 0.3rem 0.8rem; background-color: #fce8e6; color: #c5221f; font-family: 'Inter', sans-serif; font-weight: 700;">HANGUS</span></h2>
                            <p class="invoice-main-subtitle mb-0" style="color: #999;">Ringkasan layanan dan informasi reservasi Anda yang telah kedaluwarsa.</p>
                        </div>
                    </div>

                    <!-- Layanan Perawatan Section -->
                    <div class="invoice-section-row d-flex align-items-stretch gap-4 py-3" style="border-bottom: 1px solid #efeae4;">
                        <div class="invoice-left-block d-flex align-items-start gap-3" style="width: 200px; flex-shrink: 0;">
                            <div class="invoice-icon-circle-small" style="color: #999; background: #fafafa;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2C12 2 7.5 7.5 7.5 12C7.5 14.4853 9.51472 16.5 12 16.5C14.4853 16.5 16.5 14.4853 16.5 12C16.5 7.5 12 2 12 2Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="invoice-section-title mb-1" style="color: #666;">Layanan Perawatan</h4>
                                <p class="invoice-section-desc mb-0" style="color: #999;"><?= count($detailLayanan) ?> layanan dipilih</p>
                            </div>
                        </div>
                        
                        <div class="invoice-vertical-divider" style="width: 1px; background-color: #efeae4;"></div>
                        
                        <div class="invoice-right-block flex-grow-1">
                            <div class="invoice-services-list d-flex flex-column gap-3 mb-3">
                                <?php foreach ($detailLayanan as $dl): ?>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="invoice-service-name" style="color: #888;"><?= e($dl['nama_layanan']) ?> <span class="invoice-service-duration">(<?= formatDurasi($dl['durasi']) ?>)</span></span>
                                        <span class="invoice-service-price" style="color: #888; text-decoration: line-through;"><?= rupiah($dl['subtotal']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Terapis Pilihan Row -->
                    <div class="invoice-info-row d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid #efeae4;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="invoice-icon-circle-small" style="color: #999; background: #fafafa;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <span class="invoice-row-label" style="color: #888;">Terapis Pilihan</span>
                        </div>
                        <span class="invoice-row-value" style="color: #888;"><?= e($reservasi['nama_terapis']) ?></span>
                    </div>
                    
                    <!-- Hari & Tanggal Row -->
                    <div class="invoice-info-row d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid #efeae4;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="invoice-icon-circle-small" style="color: #999; background: #fafafa;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <span class="invoice-row-label" style="color: #888;">Jadwal</span>
                        </div>
                        <span class="invoice-row-value" style="color: #888;"><?= $tglFormat ?> — <?= $jamFormat ?></span>
                    </div>

                    <!-- Pricing Block -->
                    <div class="invoice-pricing-block py-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="invoice-total-label" style="font-size: 1.1rem; font-weight: 700; color: #999;">Total</span>
                            <span class="invoice-total-value-large" style="font-family: 'Inter', Arial, sans-serif; font-size: 1.95rem; font-weight: 800; color: #999; text-decoration: line-through;"><?= rupiah($reservasi['harga']) ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center gap-3">
                    <a href="index.php?action=layanan" class="btn btn-spadmin rounded-pill px-4">Buat Reservasi Baru</a>
                    <a href="index.php?action=riwayat" class="btn btn-outline-dark rounded-pill px-4">Lihat Riwayat</a>
                </div>
            </div>
        <?php elseif ($reservasi['status_pembayaran'] !== 'Belum Upload'): ?>
            <!-- CASE 1: SUDAH UPLOAD / SUDAH LUNAS / PROSES VALIDASI -->
            <?php
            $paidDetailLayanan = ambilDetailReservasi($conn, $reservasi['id']);
            $paidLayananNames = array_map(static function ($dl) {
                return $dl['nama_layanan'];
            }, $paidDetailLayanan);
            $isConfirmedReservation = in_array($reservasi['status_reservasi'], ['Diterima', 'Dikonfirmasi', 'Selesai'], true);
            $paidStatusTitle = $isConfirmedReservation ? 'Reservasi Terkonfirmasi' : 'Bukti Pembayaran Terkirim';
            $paidStatusText = $isConfirmedReservation
                ? 'Pembayaran Anda telah diverifikasi. Silakan datang sesuai jadwal treatment yang tertera.'
                : 'Bukti pembayaran Anda sedang dalam proses verifikasi admin. Status reservasi dapat dipantau di halaman Riwayat.';
            $paidStatusPill = $isConfirmedReservation ? 'Terkonfirmasi' : 'Menunggu Validasi';
            $paidNominal = (int) ($reservasi['nominal_pembayaran'] ?? $reservasi['nominal_payment'] ?? 0);
            if ($paidNominal <= 0) {
                $paidNominal = ($reservasi['jenis_pembayaran'] ?? '') === 'Lunas 100%' ? (int) $reservasi['harga'] : (int) ($reservasi['harga'] / 2);
            }
            $paidRemaining = max(0, (int) $reservasi['harga'] - $paidNominal);
            ?>
            <section class="paid-payment-state <?= $isConfirmedReservation ? 'is-confirmed' : 'is-pending' ?>">
                <div class="paid-status-hero">
                    <div class="paid-status-icon" aria-hidden="true">
                        <?php if ($isConfirmedReservation): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.7" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.7" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        <?php endif; ?>
                    </div>
                    <div class="paid-status-copy">
                        <span class="paid-kicker">Status Pembayaran</span>
                        <h1><?= $paidStatusTitle ?></h1>
                        <p><?= $paidStatusText ?></p>
                    </div>
                </div>

                <div class="paid-summary-section">
                    <div class="paid-summary-heading">
                        <div>
                            <span class="paid-kicker">Ringkasan</span>
                            <h2>Detail Reservasi #<?= (int) $reservasi['id'] ?></h2>
                        </div>
                        <span class="paid-status-pill"><?= $paidStatusPill ?></span>
                    </div>

                    <div class="paid-summary-grid">
                        <div class="paid-summary-item">
                            <span>Layanan</span>
                            <strong><?= e(implode(', ', $paidLayananNames)) ?></strong>
                        </div>
                        <div class="paid-summary-item">
                            <span>Terapis</span>
                            <strong><?= !empty($reservasi['nama_terapis']) ? e($reservasi['nama_terapis']) : 'Belum Ditugaskan' ?></strong>
                        </div>
                        <div class="paid-summary-item">
                            <span>Jadwal</span>
                            <strong><?= $tglFormat ?> - <?= $jamFormat ?></strong>
                        </div>
                        <div class="paid-summary-item">
                            <span>Total Biaya</span>
                            <strong><?= rupiah($reservasi['harga']) ?></strong>
                        </div>
                        <div class="paid-summary-item">
                            <span><?= e($reservasi['jenis_pembayaran'] ?? 'Pembayaran') ?></span>
                            <strong><?= rupiah($paidNominal) ?></strong>
                        </div>
                        <div class="paid-summary-item">
                            <span>Sisa Dibayar di SPA</span>
                            <strong><?= rupiah($paidRemaining) ?></strong>
                        </div>
                    </div>
                </div>

                <div class="paid-info-line">
                    <span aria-hidden="true"></span>
                    <p>Datang 10 menit sebelum jadwal treatment. Keterlambatan lebih dari 15 menit dapat menyebabkan reservasi dibatalkan.</p>
                </div>

                <div class="paid-actions">
                    <a href="index.php?action=riwayat" class="paid-primary-action">Lihat Riwayat</a>
                    <a href="index.php?action=home" class="paid-secondary-action">Kembali ke Beranda</a>
                </div>
            </section>

            <div class="already-paid-card text-center py-5 d-none" aria-hidden="true">
                <div class="success-icon-container">
                    <div class="success-circle-icon" style="<?php echo ($reservasi['status_reservasi'] === 'Diterima' || $reservasi['status_reservasi'] === 'Dikonfirmasi' || $reservasi['status_reservasi'] === 'Selesai') ? 'background: linear-gradient(135deg, #e6f4e8, #c8e6c9); border-color: rgba(58, 107, 70, 0.15); color: #2e7d32;' : ''; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="checkmark-svg">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                </div>
                
                <?php if ($reservasi['status_reservasi'] === 'Diterima' || $reservasi['status_reservasi'] === 'Dikonfirmasi' || $reservasi['status_reservasi'] === 'Selesai'): ?>
                    <h2 style="color: #2e7d32;">Reservasi Terkonfirmasi!</h2>
                    <p class="mb-4">Pembayaran Anda telah diverifikasi oleh admin. Silakan datang ke Wellness Spa sesuai dengan jadwal reservasi Anda di bawah ini.</p>
                <?php else: ?>
                    <h2>Bukti Pembayaran Terkirim!</h2>
                    <p class="mb-4">Terima kasih. Bukti pembayaran untuk reservasi Anda sedang dalam proses verifikasi oleh admin kami.<br>Status reservasi Anda dapat dipantau di halaman Riwayat.</p>
                <?php endif; ?>
                
                <!-- Invoice Summary Card -->
                <?php 
                $detailLayanan = ambilDetailReservasi($conn, $reservasi['id']); 
                $totalDurasi = 0;
                foreach ($detailLayanan as $dl) {
                    $totalDurasi += $dl['durasi'];
                }
                $totalJam = $totalDurasi / 60;
                $jamText = ($totalJam == (int)$totalJam) ? (int)$totalJam : number_format($totalJam, 1, ',', '.');
                ?>
                <div class="premium-invoice-card text-start" style="max-width: 700px; margin: 2rem auto 2rem; text-align: left;">
                    <!-- Header -->
                    <div class="invoice-header d-flex align-items-center gap-3 mb-4 pb-3" style="border-bottom: 1px solid #efeae4;">
                        <div class="invoice-header-icon-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7a5b43" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h2 class="invoice-main-title mb-1">Detail Reservasi #<?= (int) $reservasi['id'] ?></h2>
                            <p class="invoice-main-subtitle mb-0">Ringkasan layanan dan informasi reservasi Anda.</p>
                        </div>
                    </div>

                    <!-- Layanan Perawatan Section -->
                    <div class="invoice-section-row d-flex align-items-stretch gap-3 py-3" style="border-bottom: 1px solid #efeae4;">
                        <div class="invoice-left-block d-flex align-items-start gap-3" style="width: 170px; flex-shrink: 0;">
                            <div class="invoice-icon-circle-small">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2C12 2 7.5 7.5 7.5 12C7.5 14.4853 9.51472 16.5 12 16.5C14.4853 16.5 16.5 14.4853 16.5 12C16.5 7.5 12 2 12 2Z" stroke="#7a5b43" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 7C12 7 9.5 10.5 9.5 13.5C9.5 14.8807 10.6193 16 12 16C13.3807 16 14.5 14.8807 14.5 13.5C14.5 10.5 12 7 12 7Z" stroke="#7a5b43" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M6 14C3 13 1 16 2 19C3 22 6 23 9 21" stroke="#7a5b43" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18 14C21 13 23 16 22 19C21 22 18 23 15 21" stroke="#7a5b43" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="invoice-section-title mb-1">Layanan Perawatan</h4>
                                <p class="invoice-section-desc mb-0"><?= count($detailLayanan) ?> layanan dipilih</p>
                            </div>
                        </div>
                        
                        <div class="invoice-vertical-divider" style="width: 1px; background-color: #efeae4;"></div>
                        
                        <div class="invoice-right-block flex-grow-1">
                            <div class="invoice-services-list d-flex flex-column gap-3 mb-3">
                                <?php foreach ($detailLayanan as $dl): ?>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="invoice-service-name"><?= e($dl['nama_layanan']) ?> <span class="invoice-service-duration">(<?= formatDurasi($dl['durasi']) ?>)</span></span>
                                        <span class="invoice-service-price"><?= rupiah($dl['subtotal']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="invoice-duration-banner">
                                <div class="d-flex align-items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7a5b43" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    <span class="invoice-duration-label" style="font-size: 0.85rem; font-weight: 500; color: #7a6b5d; white-space: nowrap;">Total Durasi</span>
                                </div>
                                <span class="invoice-duration-value" style="font-size: 0.95rem; font-weight: 700; color: #7a5b43; white-space: nowrap;"><?= $totalDurasi ?> menit (<?= $jamText ?> jam)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Terapis Pilihan Row -->
                    <div class="invoice-info-row d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid #efeae4;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="invoice-icon-circle-small">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7a5b43" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <span class="invoice-row-label">Terapis Pilihan</span>
                        </div>
                        <span class="invoice-row-value"><?= !empty($reservasi['nama_terapis']) ? e($reservasi['nama_terapis']) : 'Belum Ditugaskan' ?></span>
                    </div>
                    
                    <!-- Hari & Tanggal Row -->
                    <div class="invoice-info-row d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid #efeae4;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="invoice-icon-circle-small">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7a5b43" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <span class="invoice-row-label">Hari & Tanggal</span>
                        </div>
                        <span class="invoice-row-value"><?= $tglFormat ?></span>
                    </div>
                    
                    <!-- Jam Treatment Row -->
                    <div class="invoice-info-row d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid #efeae4;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="invoice-icon-circle-small">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7a5b43" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <span class="invoice-row-label">Jam Treatment</span>
                        </div>
                        <span class="invoice-row-value"><?= $jamFormat ?></span>
                    </div>

                    <!-- Pricing Block -->
                    <div class="invoice-pricing-block pt-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="invoice-total-label" style="font-size: 1.1rem; font-weight: 700; color: #221d1b;">Total Biaya</span>
                            <span class="invoice-total-value-large" style="font-family: 'Inter', Arial, sans-serif; font-size: 1.95rem; font-weight: 800; color: #221d1b; line-height: 1;"><?= rupiah($reservasi['harga']) ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center gap-3">
                    <a href="index.php?action=riwayat" class="btn btn-spadmin rounded-pill px-4">Lihat Riwayat</a>
                    <a href="index.php?action=home" class="btn btn-outline-dark rounded-pill px-4">Kembali ke Beranda</a>
                </div>
            </div>
        <?php else: ?>
            <!-- CASE 2: BELUM UPLOAD PEMBAYARAN -->
            <div class="success-header-box">
                <div class="success-icon-container">
                    <div class="success-circle-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="checkmark-svg">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                </div>
                <h1>Reservasi Berhasil Dibuat!</h1>
                <p>Satu langkah lagi untuk mengunci slot terapis Anda. Pilih pembayaran <strong>DP 50%</strong> atau <strong>Lunas 100%</strong>, lalu upload bukti pembayaran di bawah ini.</p>
            </div>

            <!-- Countdown Banner -->
            <div class="payment-countdown-banner">
                <div class="countdown-left">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>Sisa waktu pembayaran Anda:</span>
                </div>
                <div class="countdown-right" id="countdownTimer">30:00</div>
            </div>            <!-- Invoice Summary Card -->
            <?php 
            $detailLayanan = ambilDetailReservasi($conn, $reservasi['id']); 
            $totalDurasi = 0;
            foreach ($detailLayanan as $dl) {
                $totalDurasi += $dl['durasi'];
            }
            $totalJam = $totalDurasi / 60;
            $jamText = ($totalJam == (int)$totalJam) ? (int)$totalJam : number_format($totalJam, 1, ',', '.');
            ?>
            <form action="index.php?action=upload-pembayaran" method="POST" enctype="multipart/form-data" id="uploadForm">
                <input type="hidden" name="reservasi_id" value="<?= (int) $reservasi['id'] ?>">

            <div class="payment-checkout-grid">
            <div class="premium-invoice-card text-start">
                <!-- Header -->
                <div class="invoice-header d-flex align-items-center gap-3 mb-4 pb-3" style="border-bottom: 1px solid #efeae4;">
                    <div class="invoice-header-icon-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7a5b43" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <div>
                        <h2 class="invoice-main-title mb-1">Detail Reservasi #<?= (int) $reservasi['id'] ?></h2>
                        <p class="invoice-main-subtitle mb-0">Ringkasan layanan dan informasi reservasi Anda.</p>
                    </div>
                </div>

                <!-- Layanan Perawatan Section -->
                <div class="invoice-section-row d-flex align-items-stretch gap-3 py-3" style="border-bottom: 1px solid #efeae4;">
                    <div class="invoice-left-block d-flex align-items-start gap-3" style="width: 170px; flex-shrink: 0;">
                        <div class="invoice-icon-circle-small">
                            <!-- Elegant Lotus SVG -->
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C12 2 7.5 7.5 7.5 12C7.5 14.4853 9.51472 16.5 12 16.5C14.4853 16.5 16.5 14.4853 16.5 12C16.5 7.5 12 2 12 2Z" stroke="#7a5b43" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 7C12 7 9.5 10.5 9.5 13.5C9.5 14.8807 10.6193 16 12 16C13.3807 16 14.5 14.8807 14.5 13.5C14.5 10.5 12 7 12 7Z" stroke="#7a5b43" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6 14C3 13 1 16 2 19C3 22 6 23 9 21" stroke="#7a5b43" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18 14C21 13 23 16 22 19C21 22 18 23 15 21" stroke="#7a5b43" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="invoice-section-title mb-1">Layanan Perawatan</h4>
                            <p class="invoice-section-desc mb-0"><?= count($detailLayanan) ?> layanan dipilih</p>
                        </div>
                    </div>
                    
                    <!-- Vertical Divider Line -->
                    <div class="invoice-vertical-divider" style="width: 1px; background-color: #efeae4;"></div>
                    
                    <div class="invoice-right-block flex-grow-1">
                        <div class="invoice-services-list d-flex flex-column gap-3 mb-3">
                            <?php foreach ($detailLayanan as $dl): ?>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="invoice-service-name"><?= e($dl['nama_layanan']) ?> <span class="invoice-service-duration">(<?= formatDurasi($dl['durasi']) ?>)</span></span>
                                    <span class="invoice-service-price"><?= rupiah($dl['subtotal']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Total Duration Banner inside right block -->
                        <div class="invoice-duration-banner">
                            <div class="d-flex align-items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7a5b43" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <span class="invoice-duration-label" style="font-size: 0.85rem; font-weight: 500; color: #7a6b5d; white-space: nowrap;">Total Durasi Layanan</span>
                            </div>
                            <span class="invoice-duration-value" style="font-size: 0.95rem; font-weight: 700; color: #7a5b43; white-space: nowrap;"><?= $totalDurasi ?> menit (<?= $jamText ?> jam)</span>
                        </div>
                    </div>
                </div>

                <!-- Terapis Pilihan Row -->
                <div class="invoice-info-row d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid #efeae4;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="invoice-icon-circle-small">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7a5b43" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <span class="invoice-row-label">Terapis Pilihan</span>
                    </div>
                    <span class="invoice-row-value"><?= e($reservasi['nama_terapis']) ?></span>
                </div>
                
                <!-- Hari & Tanggal Row -->
                <div class="invoice-info-row d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid #efeae4;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="invoice-icon-circle-small">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7a5b43" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <span class="invoice-row-label">Hari & Tanggal</span>
                    </div>
                    <span class="invoice-row-value"><?= $tglFormat ?></span>
                </div>
                
                <!-- Jam Treatment Row -->
                <div class="invoice-info-row d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="invoice-icon-circle-small">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7a5b43" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <span class="invoice-row-label">Jam Treatment</span>
                    </div>
                    <span class="invoice-row-value"><?= $jamFormat ?></span>
                </div>

                <!-- Pricing Block -->
                <div class="invoice-pricing-block pt-4" style="border-top: 1.5px dashed #efeae4; margin-top: 0.5rem; padding-bottom: 0.5rem;">
                    <div class="d-flex justify-content-between align-items-baseline mb-3">
                        <span class="invoice-total-label" style="font-size: 1.1rem; font-weight: 700; color: #221d1b;">Total Biaya</span>
                        <span class="invoice-total-value-large" style="font-family: 'Inter', Arial, sans-serif; font-size: 1.95rem; font-weight: 800; color: #221d1b; line-height: 1;"><?= rupiah($reservasi['harga']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-baseline">
                        <span class="invoice-dp-label" id="paymentChoiceLabel" style="font-size: 1.1rem; font-weight: 700; color: #d66881;">Down Payment (DP 50%)</span>
                        <span class="invoice-dp-value-large" id="paymentChoiceAmount" style="font-family: 'Inter', Arial, sans-serif; font-size: 1.95rem; font-weight: 800; color: #d66881; line-height: 1;"><?= rupiah($reservasi['harga'] / 2) ?></span>
                    </div>
                </div>
            </div>

            <div class="payment-bank-column">
            <!-- Bank Transfer Info Section -->
            <div class="bank-transfer-section">
                <h3>Pilihan Rekening Pembayaran</h3>
                <p class="text-muted small mb-3">Pilih nominal pembayaran dan rekening tujuan transfer.</p>

                <div class="payment-choice-layout">
                <div class="payment-choice-section payment-amount-section">
                    <div class="payment-choice-header">
                        <span class="payment-choice-step">01</span>
                        <div>
                            <h4>Pilih Nominal</h4>
                            <p>Tentukan jumlah yang ingin dibayarkan sekarang.</p>
                        </div>
                    </div>

                    <div class="payment-amount-grid">
                    <label class="bank-account-card payment-amount-card" style="cursor: pointer;">
                        <input type="radio" name="jenis_pembayaran" value="DP 50%" checked onchange="updateReservationPaymentChoice(this.value)">
                        <span class="bank-radio-mark" aria-hidden="true"></span>
                        <span class="bank-card-content">
                            <span class="bank-card-name">Bayar DP 50%</span>
                            <span class="payment-amount-detail">
                                <span class="payment-amount-value"><?= rupiah($reservasi['harga'] / 2) ?></span>
                                <span class="payment-amount-note">Sisanya dibayar di SPA</span>
                            </span>
                        </span>
                    </label>
                    <label class="bank-account-card payment-amount-card" style="cursor: pointer;">
                        <input type="radio" name="jenis_pembayaran" value="Lunas 100%" onchange="updateReservationPaymentChoice(this.value)">
                        <span class="bank-radio-mark" aria-hidden="true"></span>
                        <span class="bank-card-content">
                            <span class="bank-card-name">Bayar Lunas 100%</span>
                            <span class="payment-amount-detail">
                                <span class="payment-amount-value"><?= rupiah($reservasi['harga']) ?></span>
                                <span class="payment-amount-note">Tidak perlu pelunasan di SPA</span>
                            </span>
                        </span>
                    </label>
                    </div>
                </div>
                
                <div class="payment-choice-section payment-method-section">
                    <div class="payment-choice-header">
                        <span class="payment-choice-step">02</span>
                        <div>
                            <h4>Metode Transfer</h4>
                            <p>Pilih rekening tujuan lalu salin nomor rekening.</p>
                        </div>
                    </div>

                <div class="bank-card-grid">
                    <?php 
                    $rekeningList = $conn->query("SELECT * FROM rekening ORDER BY id_rekening ASC")->fetch_all(MYSQLI_ASSOC);
                    foreach ($rekeningList as $index => $r): 
                        $bankSlug = strtolower(str_replace(' ', '_', $r['nama_bank']));
                        $elId = 'no_' . $bankSlug . '_' . $r['id_rekening'];
                    ?>
                        <label class="bank-account-card payment-method-card <?= $bankSlug ?>-card">
                            <input type="radio" name="rekening_id" value="<?= (int) $r['id_rekening'] ?>" <?= $index === 0 ? 'checked' : '' ?> required>
                            <span class="bank-radio-mark" aria-hidden="true"></span>
                            <span class="bank-card-content">
                                <span class="bank-card-name"><?= htmlspecialchars($r['nama_bank']) ?></span>
                                <span class="bank-acc-row">
                                    <span class="bank-acc-number" id="<?= $elId ?>"><?= htmlspecialchars($r['nomor_rekening']) ?></span>
                                    <button type="button" class="btn-copy-icon <?= $bankSlug ?>-copy" onclick="event.stopPropagation(); salinRekening('<?= $elId ?>', '<?= htmlspecialchars($r['nama_bank']) ?>', this)" aria-label="Salin rekening <?= htmlspecialchars($r['nama_bank']) ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                    </button>
                                </span>
                                <span class="bank-acc-name"><?= htmlspecialchars($r['atas_nama']) ?></span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
                </div>
                </div>
                
                <!-- Note Banner -->
                <div class="payment-note-banner">
                    <div class="note-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                    </div>
                    <span class="note-text">Pastikan nominal transfer sesuai dengan jumlah yang tertera.</span>
                </div>
            </div>
            </div>
            </div>

            <section class="payment-important-card" aria-labelledby="paymentImportantTitle">
                <div class="payment-section-eyebrow">Informasi Penting</div>
                <ul class="payment-important-list" id="paymentImportantTitle">
                    <li>Datang 10 menit sebelum jadwal treatment.</li>
                    <li>Keterlambatan lebih dari 15 menit dapat menyebabkan reservasi dibatalkan.</li>
                    <li>Pembayaran yang telah dikonfirmasi tidak dapat dikembalikan apabila pelanggan tidak hadir sesuai jadwal.</li>
                </ul>
            </section>

            <!-- Upload Zone Form -->
            <section class="payment-upload-section" aria-labelledby="paymentUploadTitle">
                <div class="payment-section-eyebrow" id="paymentUploadTitle">Upload Bukti Pembayaran</div>
                <div class="receipt-upload-box" id="uploadBox">
                    <input type="file" id="bukti" name="bukti" accept=".jpg,.jpeg,.png" class="d-none" required>
                    
                    <div class="upload-icon-circle" id="uploadIconWrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" id="uploadIconSvg">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                    </div>
                    
                    <h4 id="uploadTitle">Upload & letakkan bukti pembayaran di sini</h4>
                    <p id="uploadSubtitle">atau klik untuk memilih file dari komputer Anda (JPG, JPEG, atau PNG, maks 2MB)</p>
                    <div class="mt-2 text-success font-weight-bold d-none" id="fileInfo"></div>
                </div>
            </section>
                
                <div class="upload-actions-bar">
                    <button type="submit" class="btn-premium-submit w-100" id="btnKirim" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                        <span>Kirim Bukti Pembayaran</span>
                    </button>
                </div>
            </form>
        <?php endif; ?>

    </div>
</div>

<!-- Dynamic Toast element for copys -->
<div class="copy-toast" id="toastCopy">Nomor rekening berhasil disalin!</div>

<script>
function updateReservationPaymentChoice(value) {
    var total = <?= (int) $reservasi['harga'] ?>;
    var isLunas = value === 'Lunas 100%';
    var nominal = isLunas ? total : total * 0.5;
    document.getElementById('paymentChoiceLabel').textContent = isLunas ? 'Pembayaran Lunas (100%)' : 'Down Payment (DP 50%)';
    document.getElementById('paymentChoiceAmount').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(nominal);
}

document.addEventListener('DOMContentLoaded', function() {
    // 1. COUNTDOWN TIMER
    let remainingSeconds = <?= (int) $timeRemaining ?>;
    let shouldReloadWhenExpired = remainingSeconds > 0;
    const timerElement = document.getElementById('countdownTimer');
    const btnKirim = document.getElementById('btnKirim');
    const uploadBox = document.getElementById('uploadBox');
    
    if (timerElement) {
        function updateTimer() {
            if (remainingSeconds <= 0) {
                timerElement.textContent = "WAKTU HABIS";
                timerElement.style.color = "#d32f2f";
                timerElement.style.backgroundColor = "#ffebee";
                if (btnKirim) btnKirim.disabled = true;
                if (uploadBox) {
                    uploadBox.style.pointerEvents = "none";
                    uploadBox.style.opacity = "0.6";
                    document.getElementById('uploadTitle').textContent = "Batas waktu pembayaran habis";
                    document.getElementById('uploadSubtitle').textContent = "Silakan buat reservasi baru.";
                }
                if (shouldReloadWhenExpired) {
                    shouldReloadWhenExpired = false;
                    setTimeout(() => {
                        window.location.reload();
                    }, 900);
                }
                return;
            }
            
            const minutes = Math.floor(remainingSeconds / 60);
            const seconds = remainingSeconds % 60;
            
            const formattedMinutes = String(minutes).padStart(2, '0');
            const formattedSeconds = String(seconds).padStart(2, '0');
            
            timerElement.textContent = `${formattedMinutes}:${formattedSeconds}`;
            remainingSeconds--;
            
            setTimeout(updateTimer, 1000);
        }
        
        updateTimer();
    }
    
    // 2. DRAG AND DROP / INTERACTION UPLOAD BOX
    const fileInput = document.getElementById('bukti');
    const uploadTitle = document.getElementById('uploadTitle');
    const uploadSubtitle = document.getElementById('uploadSubtitle');
    const fileInfo = document.getElementById('fileInfo');
    const uploadIconWrap = document.getElementById('uploadIconWrap');
    
    if (uploadBox && fileInput) {
        // Trigger file input click when clicking on the box
        uploadBox.addEventListener('click', () => {
            fileInput.click();
        });
        
        // Handle drag and drop states
        uploadBox.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadBox.classList.add('drag-over');
        });
        
        uploadBox.addEventListener('dragleave', () => {
            uploadBox.classList.remove('drag-over');
        });
        
        uploadBox.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadBox.classList.remove('drag-over');
            
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                handleFileSelect();
            }
        });
        
        // Handle input file change
        fileInput.addEventListener('change', handleFileSelect);
        
        function handleFileSelect() {
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                
                // Validate size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran bukti transfer melebihi 2MB. Silakan kompres atau pilih file lain.');
                    fileInput.value = '';
                    resetUploadBox();
                    return;
                }
                
                // Validate extension
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak didukung. Harap pilih gambar JPEG, JPG, atau PNG.');
                    fileInput.value = '';
                    resetUploadBox();
                    return;
                }
                
                // Update upload box UI
                uploadBox.classList.add('has-file');
                uploadTitle.textContent = "Bukti Pembayaran Terpilih";
                uploadSubtitle.textContent = "Klik kembali jika ingin mengubah file.";
                
                fileInfo.textContent = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                fileInfo.classList.remove('d-none');
                
                if (btnKirim && remainingSeconds > 0) {
                    btnKirim.disabled = false;
                }
                
                // Swap icon SVG to checkmark
                uploadIconWrap.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                `;
            } else {
                resetUploadBox();
            }
        }
        
        function resetUploadBox() {
            uploadBox.classList.remove('has-file');
            uploadTitle.textContent = "Upload & letakkan bukti pembayaran di sini";
            uploadSubtitle.textContent = "atau klik untuk memilih file dari komputer Anda (JPG, JPEG, atau PNG, maks 2MB)";
            fileInfo.classList.add('d-none');
            if (btnKirim) btnKirim.disabled = true;
            
            uploadIconWrap.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" id="uploadIconSvg">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
            `;
        }
    }
});

// 3. COPY BANK ACC TO CLIPBOARD FUNCTION
function salinRekening(elementId, bankName, btn) {
    const accNum = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(accNum).then(function() {
        const toast = document.getElementById('toastCopy');
        toast.textContent = `Nomor rekening ${bankName} berhasil disalin!`;
        toast.classList.add('show');
        
        setTimeout(function() {
            toast.classList.remove('show');
        }, 2000);

        if (btn) {
            const originalContent = btn.innerHTML;
            btn.classList.add('copied');
            btn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            `;
            setTimeout(function() {
                btn.classList.remove('copied');
                btn.innerHTML = originalContent;
            }, 2000);
        }
    }, function(err) {
        console.error('Gagal menyalin rekening: ', err);
    });
}
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
