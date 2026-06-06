<?php $judulHalaman = 'Detail Layanan - SPAdmin Spa Bandung'; ?>
<?php include __DIR__ . '/../templates/header.php'; ?>

<div class="detail-compact-page">

<?php
$rawMedia = $layanan['media'] ?? '';
$namaLayananLower = strtolower($layanan['nama_layanan'] ?? '');

$detailBenefits = [
    ['title' => 'Relaksasi Maksimal', 'icon' => 'leaf'],
    ['title' => 'Teknik Pijat Profesional', 'icon' => 'hands'],
    ['title' => 'Melancarkan Peredaran Darah', 'icon' => 'drop'],
];

if (strpos($namaLayananLower, 'wajah') !== false || strpos($namaLayananLower, 'facial') !== false || strpos($namaLayananLower, 'totok') !== false) {
    $detailBenefits = [
        ['title' => 'Wajah Terasa Segar', 'icon' => 'spark'],
        ['title' => 'Teknik Perawatan Lembut', 'icon' => 'hands'],
        ['title' => 'Membantu Relaksasi Wajah', 'icon' => 'leaf'],
    ];
} elseif (strpos($namaLayananLower, 'bekam') !== false) {
    $detailBenefits = [
        ['title' => 'Terapi Bekam Terarah', 'icon' => 'target'],
        ['title' => 'Praktik Higienis', 'icon' => 'shield'],
        ['title' => 'Tubuh Terasa Lebih Ringan', 'icon' => 'spark'],
    ];
} elseif (strpos($namaLayananLower, 'lulur') !== false) {
    $detailBenefits = [
        ['title' => 'Kulit Terasa Halus', 'icon' => 'spark'],
        ['title' => 'Perawatan Tubuh Menyeluruh', 'icon' => 'leaf'],
        ['title' => 'Rileks dan Segar', 'icon' => 'drop'],
    ];
} elseif (strpos($namaLayananLower, 'refleksi') !== false || strpos($namaLayananLower, 'reflex') !== false) {
    $detailBenefits = [
        ['title' => 'Fokus Titik Refleksi', 'icon' => 'target'],
        ['title' => 'Kaki Terasa Ringan', 'icon' => 'spark'],
        ['title' => 'Membantu Rasa Nyaman', 'icon' => 'leaf'],
    ];
}
?>

    <section class="detail-service-section container pt-4 pb-0">

        <div class="mb-3 reveal-stagger delay-1">
            <a href="index.php?action=layanan" class="detail-back-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="back-arrow-icon"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span>Kembali</span>
            </a>
        </div>

        <?php if (isset($_GET['pesan_sukses'])): ?>
            <div class="floating-alert success" id="floatingAlert" role="status" aria-live="polite">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <div class="alert-message">
                    <?= e($_GET['pesan_sukses']) ?>
                </div>
                <button class="alert-close" type="button" onclick="closeFloatingAlert()" aria-label="Tutup notifikasi">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['pesan_error'])): ?>
            <div class="floating-alert error" id="floatingAlert" role="alert">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                </div>
                <div class="alert-message">
                    <?= e($_GET['pesan_error']) ?>
                </div>
                <button class="alert-close" type="button" onclick="closeFloatingAlert()" aria-label="Tutup notifikasi">&times;</button>
            </div>
        <?php endif; ?>

        <div class="detail-service-hero mb-5">
            <div class="detail-visual-column">
                <div class="detail-media-frame">
                    <span class="detail-duration-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><polyline points="12 7 12 12 15 14"></polyline></svg>
                        <?= e(formatDurasi($layanan['durasi'])) ?>
                    </span>
                    <img class="detail-image" src="<?= e(mediaLayanan($layanan['media'], $layanan['nama_layanan'])) ?>" alt="Gambar Layanan <?= e($layanan['nama_layanan']) ?>" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="service-img-fallback detail-image-fallback">
                        <?= e($layanan['nama_layanan']) ?>
                    </div>
                </div>
            </div>

            <div class="detail-info-column">
                <span class="eyebrow-luxury reveal-stagger delay-1"><?= e(strtoupper($layanan['kategori'])) ?></span>
                
                <h1 class="detail-title-luxury reveal-mask">
                    <span class="reveal-title-text"><?= e($layanan['nama_layanan']) ?></span>
                </h1>

                <div class="detail-hero-divider"></div>

                <p class="detail-description reveal-stagger delay-2">
                    <?= e($layanan['deskripsi']) ?>
                </p>

                <div class="detail-benefit-row reveal-stagger delay-3">
                    <?php foreach ($detailBenefits as $benefit): ?>
                        <div class="detail-benefit-item">
                            <span class="detail-benefit-icon detail-benefit-icon-<?= e($benefit['icon']) ?>">
                                <?php if ($benefit['icon'] === 'hands'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 11V5a1.5 1.5 0 0 1 3 0v5"></path><path d="M10 10V4a1.5 1.5 0 0 1 3 0v6"></path><path d="M13 10V5a1.5 1.5 0 0 1 3 0v7"></path><path d="M16 12.5 18.2 10a1.5 1.5 0 0 1 2.2 2l-3.9 4.7A6 6 0 0 1 11.9 19H10a5 5 0 0 1-5-5v-3a1.5 1.5 0 0 1 3 0v2"></path></svg>
                                <?php elseif ($benefit['icon'] === 'drop'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3c-3 3.3-5 6.3-5 9a5 5 0 0 0 10 0c0-2.7-2-5.7-5-9Z"></path><path d="M9.5 14.5c.7.9 1.5 1.3 2.5 1.3"></path></svg>
                                <?php elseif ($benefit['icon'] === 'spark'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3c1.1 4 2.9 5.8 7 7-4.1 1.2-5.9 3-7 7-1.1-4-2.9-5.8-7-7 4.1-1.2 5.9-3 7-7Z"></path><path d="M19 16v4"></path><path d="M17 18h4"></path></svg>
                                <?php elseif ($benefit['icon'] === 'target'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="8"></circle><circle cx="12" cy="12" r="3"></circle><path d="M12 2v3"></path><path d="M12 19v3"></path><path d="M2 12h3"></path><path d="M19 12h3"></path></svg>
                                <?php elseif ($benefit['icon'] === 'shield'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3 19 6v5c0 4.5-2.8 8-7 10-4.2-2-7-5.5-7-10V6l7-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 20A7 7 0 0 1 4 13c0-5 4-8 9-9 0 4 3 5 3 9a7 7 0 0 1-5 7Z"></path><path d="M8.5 14.5c1.6-.2 3.4-1.5 4.5-4"></path></svg>
                                <?php endif; ?>
                            </span>
                            <span><?= e($benefit['title']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="detail-purchase-panel reveal-stagger delay-3">
                    <div class="detail-spec-box">
                        <span>DURASI</span>
                        <strong>
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 14"/></svg>
                            <?= e(formatDurasi($layanan['durasi'])) ?>
                        </strong>
                    </div>
                    <div class="detail-price-box">
                        <span>MULAI DARI</span>
                        <strong><?= e(rupiah($layanan['harga'])) ?></strong>
                    </div>
                </div>

                <?php $isAddon = ($layanan['kategori'] === 'Tambahan' || $layanan['kategori'] === 'Tambahan Bekam'); ?>
                <div class="detail-action-stack reveal-stagger delay-4">
                    <?php if (isInCart($layanan['id'])): ?>
                        <form method="POST" action="index.php?action=hapus-keranjang" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini dari keranjang?');" class="detail-remove-form">
                            <input type="hidden" name="layanan_id" value="<?= (int) $layanan['id'] ?>">
                            <input type="hidden" name="redirect_to" value="index.php?action=detail-layanan&id=<?= (int) $layanan['id'] ?>">
                            <button type="submit" class="detail-secondary-action">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                Hapus dari Keranjang
                            </button>
                        </form>
                    <?php else: ?>
                        <?php if ($isAddon && !hasMainServiceInCart()): ?>
                            <div class="alert alert-warning border-0 rounded-3 mb-3 p-3" style="background-color: rgba(244, 219, 180, 0.35); color: #7d5018; font-size: 0.9rem; font-weight: 500; display: flex; align-items: center; border: 1px solid rgba(125,80,24,0.12) !important;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2 flex-shrink-0"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                <span>Layanan ini adalah layanan tambahan. Silakan pilih layanan utama terlebih dahulu di katalog.</span>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="index.php?action=tambah-keranjang" class="detail-add-form">
                            <input type="hidden" name="layanan_id" value="<?= (int) $layanan['id'] ?>">
                            <input type="hidden" name="redirect_to" value="index.php?action=detail-layanan&id=<?= (int) $layanan['id'] ?>">
                            <button type="submit" class="detail-cta-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                <span>Tambahkan ke Keranjang</span>
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if (!$isAddon): ?>
                        <form method="POST" action="index.php?action=reservasi-langsung" class="detail-direct-form">
                            <input type="hidden" name="layanan_id" value="<?= (int) $layanan['id'] ?>">
                            <button type="submit" class="detail-direct-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M3 10h18"></path><path d="m9 16 2 2 4-4"></path></svg>
                                <span>Reservasi Langsung</span>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="border-top-new container pt-5 pb-5 mt-4">
        <div class="row g-5">
            <!-- Left Grid: Available Specialists -->
            <div class="col-lg-6 d-flex flex-column">
                <div class="showcase-header-new mb-4 border-bottom-new pb-3">
                    <span class="eyebrow-new">TERAPIS SPESIALIS</span>
                    <h2>Terapis Pilihan Tersedia</h2>
                </div>
                
                <div class="terapis-grid-new flex-grow-1">
                    <?php 
                    $qualifiedTherapists = $terapis;
                    ?>

                    <?php if (empty($qualifiedTherapists)): ?>
                        <div class="terapis-card-new terapis-empty-card">
                            <div class="terapis-avatar-new" aria-hidden="true">SP</div>
                            <h4>Belum Ada Terapis</h4>
                            <p>Spesialis layanan ini belum tersedia.</p>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($qualifiedTherapists as $item): ?>
                        <div class="terapis-card-new">
                            <div class="terapis-avatar-new"><?= e(($item['nama_terapis'][0] ?? 'T') . (explode(' ', $item['nama_terapis'])[1][0] ?? '')) ?></div>
                            <h4><?= e($item['nama_terapis']) ?></h4>
                            <p><?= e($item['jenis_kelamin'] ?? 'Perempuan') ?></p>
                            <span class="specialty-new"><?= e($layanan['nama_layanan']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-6 d-flex flex-column">
                 <?php
                 $premiumReviews = [];
                 if (!empty($ulasan)) {
                     foreach ($ulasan as $u) {
                         $premiumReviews[] = [
                             'nama' => $u['nama'],
                             'rating' => $u['rating'],
                             'isi_ulasan' => $u['isi_ulasan']
                         ];
                     }
                 }
                 ?>
                <div class="showcase-header-new d-flex justify-content-between align-items-end mb-4 border-bottom-new pb-3">
                    <div>
                        <span class="eyebrow-new">KESAN TAMU</span>
                        <h2>Penilaian &amp; Testimoni</h2>
                    </div>
                    <?php if (!empty($premiumReviews)): ?>
                    <div class="review-nav-new d-flex gap-2">
                        <button id="reviewPrev" aria-label="Ulasan sebelumnya">&larr;</button>
                        <button id="reviewNext" aria-label="Ulasan berikutnya">&rarr;</button>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="review-card-new-wrapper flex-grow-1 position-relative overflow-hidden">
                    <?php if (empty($premiumReviews)): ?>
                        <div class="no-reviews-placeholder d-flex flex-column align-items-center justify-content-center text-center p-4 h-100" style="min-height: 220px; color: #a48c71; border: 1.5px dashed rgba(164, 140, 113, 0.28); border-radius: 20px; background: rgba(247, 241, 231, 0.25);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3" style="color: var(--wellness-pink);"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            <p style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.95rem; font-weight: 600; line-height: 1.6; margin: 0; color: var(--wellness-green);">Belum ada ulasan untuk layanan ini.</p>
                            <p style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.85rem; font-weight: 500; line-height: 1.5; margin: 6px 0 0; color: #8e8076; max-width: 280px;">Jadilah yang pertama memberikan ulasan setelah menyelesaikan sesi perawatan Anda!</p>
                        </div>
                    <?php else: ?>
                        <div class="review-quote-mark">“</div>
                        <div class="review-track-new" id="detailReviewTrack">
                            <?php foreach ($premiumReviews as $index => $item): ?>
                                <article class="review-item-new <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>">
                                    <div class="review-stars-new d-flex gap-1 text-pink-blush mb-3">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="<?= ($i <= $item['rating']) ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="review-text-new">"<?= e($item['isi_ulasan']) ?>"</p>
                                    <div class="review-author-new">
                                        <strong><?= e($item['nama']) ?></strong>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($premiumReviews)): ?>
                <div class="review-dots-new d-flex justify-content-center gap-2 mt-3" id="detailReviewDots">
                    <?php foreach ($premiumReviews as $index => $_): ?>
                        <button class="review-dot-new <?= $index === 0 ? 'active' : '' ?>" type="button" data-index="<?= $index ?>" aria-label="Tampilkan ulasan <?= $index + 1 ?>"></button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php
    $semuaLayanan = ambilLayanan($conn);
    $otherServices = [];
    $addedNames = [];
    
    if (!empty($layanan['nama_layanan'])) {
        $addedNames[] = strtolower($layanan['nama_layanan']);
    }
    
    foreach ($semuaLayanan as $other) {
        $otherNameLower = strtolower($other['nama_layanan'] ?? '');
        
        if ((int)$other['id'] === (int)$layanan['id']) {
            continue;
        }
        
        if (in_array($otherNameLower, $addedNames, true)) {
            continue;
        }
        
        $otherServices[] = $other;
        $addedNames[] = $otherNameLower;
        
        if (count($otherServices) >= 4) {
            break;
        }
    }
    ?>
    <?php if (!empty($otherServices)): ?>
        <section class="border-top-new container pt-4 pb-5 mt-4 position-relative z-1">
            <div class="showcase-header-new mb-4 border-bottom-new pb-3">
                <span class="eyebrow-new">ANDA JUGA MUNGKIN MENYUKAI</span>
                <h2>Perawatan Lainnya untuk Anda</h2>
            </div>
            
            <div class="row g-4">
                <?php foreach ($otherServices as $other): ?>
                    <div class="col-sm-6 col-lg-3">
                        <div class="other-card-new h-100 d-flex flex-column">
                            <a class="other-media-new" href="index.php?action=detail-layanan&id=<?= (int) $other['id'] ?>">
                                <img src="<?= e(mediaLayanan($other['media'], $other['nama_layanan'])) ?>" alt="<?= e($other['nama_layanan']) ?>" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="service-img-fallback">
                                    <?= e($other['nama_layanan']) ?>
                                </div>
                            </a>
                            <div class="other-body-new flex-grow-1 d-flex flex-column p-3 text-start">
                                <span class="other-category-new"><?= e($other['kategori']) ?></span>
                                <h3><a href="index.php?action=detail-layanan&id=<?= (int) $other['id'] ?>"><?= e($other['nama_layanan']) ?></a></h3>
                                <p class="other-desc-new"><?= e($other['deskripsi']) ?></p>
                                <p class="other-meta-new mt-auto">
                                    <span><?= formatDurasi($other['durasi']) ?></span>
                                    <span class="other-price-new"><?= rupiah($other['harga']) ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.body.classList.add('js-revealed');
            }, 80);

            const reviewTrack = document.getElementById('detailReviewTrack');
            const reviewCards = reviewTrack ? Array.from(reviewTrack.querySelectorAll('.review-item-new')) : [];
            const reviewDots = Array.from(document.querySelectorAll('.review-dot-new'));
            let activeReview = 0;

            function updateDetailReviews(index) {
                if (!reviewCards.length) return;
                activeReview = (index + reviewCards.length) % reviewCards.length;
                
                reviewCards.forEach((card, cardIndex) => {
                    card.classList.toggle('active', cardIndex === activeReview);
                });
                reviewDots.forEach((dot, dotIndex) => {
                    dot.classList.toggle('active', dotIndex === activeReview);
                });
            }

            reviewDots.forEach((dot, index) => dot.addEventListener('click', () => updateDetailReviews(index)));
            document.getElementById('reviewPrev')?.addEventListener('click', () => updateDetailReviews(activeReview - 1));
            document.getElementById('reviewNext')?.addEventListener('click', () => updateDetailReviews(activeReview + 1));
            updateDetailReviews(0);
        });
    </script>

</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
