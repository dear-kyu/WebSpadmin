<?php
$judulHalaman = 'Reservasi Saya - SPAdmin Spa Bandung';
include __DIR__ . '/../templates/header.php';
?>

<section class="page-hero kecil">
    <div class="container">
        <p class="eyebrow">Oase Ketenangan Anda</p>
        <h1><?= getSapaan() ?>, <?= e($_SESSION['nama'] ?? 'Pelanggan') ?>.</h1>
        <p class="section-desc mt-2 mb-0">Waktunya menyegarkan kembali jiwa dan raga Anda. Nikmati pelayanan spa premium terbaik dan temukan kedamaian batin sejati hari ini.</p>
    </div>
</section>

<div class="container py-5">

    <div class="dash-stats-grid-premium">
        <div class="dash-stat-card-premium stat-total">
            <div class="dash-stat-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
            <div class="dash-stat-details">
                <span class="dash-stat-number"><?= (int) ($ringkasan['total_reservasi'] ?? 0) ?></span>
                <span class="dash-stat-name">Total</span>
            </div>
        </div>

        <div class="dash-stat-card-premium stat-pending">
            <div class="dash-stat-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div class="dash-stat-details">
                <span class="dash-stat-number"><?= (int) ($ringkasan['menunggu'] ?? 0) ?></span>
                <span class="dash-stat-name">Menunggu</span>
            </div>
        </div>

        <div class="dash-stat-card-premium stat-approved">
            <div class="dash-stat-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div class="dash-stat-details">
                <span class="dash-stat-number"><?= (int) ($ringkasan['diterima'] ?? 0) ?></span>
                <span class="dash-stat-name">Diterima</span>
            </div>
        </div>

        <div class="dash-stat-card-premium stat-completed">
            <div class="dash-stat-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
            </div>
            <div class="dash-stat-details">
                <span class="dash-stat-number"><?= (int) ($ringkasan['selesai'] ?? 0) ?></span>
                <span class="dash-stat-name">Selesai</span>
            </div>
        </div>
    </div>

    <div class="dash-editorial-wrapper">
        <div class="dash-editorial-header">
            <h2>Reservasi Terbaru Anda</h2>
            <a href="index.php?action=riwayat" class="link-editorial">Lihat Semua →</a>
        </div>

        <?php if (!$reservasi): ?>
            <div class="dash-empty-editorial py-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3 text-muted" style="opacity: 0.5;">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <p class="mb-4">Kamu belum memiliki riwayat reservasi aktif saat ini.</p>
                <a class="btn btn-spadmin rounded-pill px-5 py-3" href="index.php?action=reservasi">Mulai Reservasi Pertama</a>
            </div>
        <?php else: ?>
            <div class="dash-list-editorial">
                <?php foreach ($reservasi as $item): ?>
                    <?php
                    $statusClass    = 'status-' . strtolower(str_replace(' ', '-', e($item['status_reservasi'])));
                    $terapisInisial = getInisialTerapis($item['nama_terapis'] ?? '');
                    ?>
                    <div class="dash-ticket-card <?= $statusClass ?>">
                        <div class="ticket-image-wrapper" style="position: relative; overflow: hidden;">
                            <img src="<?= e(mediaLayanan($item['media'], $item['nama_layanan'])) ?>" alt="Gambar Layanan <?= e($item['nama_layanan']) ?>" class="ticket-service-img" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="service-img-fallback" style="display: none; height: 100%; width: 100%; background: var(--cream); align-items: center; justify-content: center; color: var(--brown-dark); font-weight: 700; font-family: 'Playfair Display', Georgia, serif; font-size: 0.8rem; text-align: center; padding: 5px;">
                                <?= e($item['nama_layanan']) ?>
                            </div>
                        </div>

                        <div class="ticket-service-info">
                            <span class="ticket-tag">SPA TREATMENT</span>
                            <h3 class="ticket-title"><?= e($item['nama_layanan']) ?></h3>
                            <div class="ticket-meta-details">
                                <span class="ticket-meta-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    <?= e($item['tanggal']) ?>
                                </span>
                                <span class="ticket-meta-divider">·</span>
                                <span class="ticket-meta-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <?= e(substr($item['jam'], 0, 5)) ?> WIB
                                </span>
                            </div>
                        </div>

                        <div class="ticket-therapist-info">
                            <div class="ticket-therapist-avatar" title="<?= e($item['nama_terapis'] ?? '') ?>">
                                <?= e($terapisInisial) ?>
                            </div>
                            <div class="ticket-therapist-name">
                                <span class="ticket-therapist-label">Terapis Pilihan</span>
                                <span class="ticket-therapist-val"><?= e($item['nama_terapis'] ?? '-') ?></span>
                            </div>
                        </div>

                        <div class="ticket-action-box">
                            <span class="badge-status <?= strtolower(e($item['status_reservasi'])) ?>">
                                <?= e($item['status_reservasi']) ?>
                            </span>

                            <?php if ($item['status_pembayaran'] === 'Belum Upload'): ?>
                                <a href="index.php?action=pembayaran&id=<?= (int)$item['id'] ?>" class="btn-pay-now-ticket">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                    Bayar Sekarang
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="dash-premium-cta-banner">
            <div class="cta-banner-text">
                <span class="cta-banner-eyebrow">Rencana Kunjungan Selanjutnya</span>
                <h3 class="cta-banner-title">Siap untuk memanjakan diri kembali?</h3>
                <p class="cta-banner-desc">Jadwalkan slot perawatan spa favorit Anda lebih awal untuk mendapatkan kepastian jadwal dan terapis pilihan terbaik Anda.</p>
            </div>
            <div class="cta-banner-btn-box">
                <a class="btn-reservasi-baru" href="index.php?action=reservasi">
                    <span>Reservasi Sekarang</span>
                    <span class="arrow">→</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
