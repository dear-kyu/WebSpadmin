<?php $judulHalaman = 'Ulasan Layanan - SPAdmin Spa Bandung'; ?>
<?php include __DIR__ . '/../templates/header.php'; ?>
    <?php if (($_GET['pesan'] ?? '') === 'ulasan-berhasil'): ?>
        <div class="review-toast" id="reviewToast">Ulasan berhasil dikirim. Terima kasih!</div>
    <?php endif; ?>

    <section class="page-hero kecil">
        <div class="container">
            <p class="eyebrow">Feedback</p>
            <h1>Beri Ulasan Layanan</h1>
            <p class="section-desc mt-2 mb-0">Berikan rating dan ulasan untuk layanan yang pernah kamu reservasi.</p>
        </div>
    </section>

    <div class="container review-back-wrap">
        <a href="index.php?action=riwayat" class="review-back-btn">&larr; Kembali</a>
    </div>

    <section class="container review-section">
        <div class="review-layout">
        <div class="kartu form-panel review-panel">
            <?php if (($_GET['pesan'] ?? '') === 'ulasan-tidak-valid'): ?>
                <div class="pesan-error">Reservasi tidak valid, belum selesai, atau ulasan sudah pernah dikirim.</div>
            <?php endif; ?>
            <?php if (isset($pesanError)): ?>
                <div class="pesan-error"><?= e($pesanError) ?></div>
            <?php endif; ?>

            <?php if (!empty($reservasi)): ?>
                <form method="POST" action="index.php?action=simpan-ulasan">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="reservasi_id">Reservasi</label>
                            <select id="reservasi_id" name="reservasi_id" required>
                                <option value="">Pilih reservasi</option>
                                <?php foreach ($reservasi as $item): ?>
                                    <option value="<?= (int) $item['id'] ?>" <?= (isset($_GET['id']) && (int)$_GET['id'] === (int)$item['id']) ? 'selected' : '' ?>>
                                        <?= e($item['nama_layanan']) ?> &mdash; <?= e($item['tanggal']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="rating">Rating</label>
                            <select id="rating" name="rating" required>
                                <option value="">Pilih rating</option>
                                <option value="5">5 - Sangat puas</option>
                                <option value="4">4 - Puas</option>
                                <option value="3">3 - Cukup</option>
                                <option value="2">2 - Kurang</option>
                                <option value="1">1 - Tidak puas</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="isi_ulasan">Ulasan</label>
                        <textarea id="isi_ulasan" name="isi_ulasan" rows="5" placeholder="Ceritakan pengalamanmu..." required></textarea>
                    </div>
                    <button class="tombol-utama" type="submit">Kirim Ulasan</button>
                </form>
            <?php endif; ?>

            <?php if (empty($reservasi) && empty($ulasanPelanggan)): ?>
                <div class="review-empty-state">
                    Belum ada reservasi selesai yang bisa diulas.
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($ulasanPelanggan)): ?>
            <aside class="review-history-panel">
                <div class="review-history-head">
                    <div>
                        <span class="review-history-kicker">Riwayat Feedback</span>
                        <h2>Ulasan Kamu</h2>
                    </div>
                    <span class="review-count-pill"><?= count($ulasanPelanggan) ?> ulasan</span>
                </div>

                <div class="review-history-list">
                        <?php foreach ($ulasanPelanggan as $index => $ulasan): ?>
                            <article class="review-history-card" data-review-item data-review-page="<?= (int) floor($index / 5) ?>">
                                <div class="review-history-top">
                                    <div>
                                        <strong><?= e($ulasan['nama_layanan']) ?></strong>
                                        <small><?= e(date('d M Y', strtotime($ulasan['created_at']))) ?></small>
                                    </div>
                                    <span><?= (int) $ulasan['rating'] ?>/5</span>
                                </div>
                                <p class="review-history-text">
                                    <strong>Ulasan Anda:</strong>
                                    "<?= e($ulasan['isi_ulasan']) ?>"
                                </p>

                                <?php if (!empty($ulasan['balasan_admin'])): ?>
                                    <div class="review-admin-reply">
                                        <strong>SPADMIN</strong>
                                        <p><?= e($ulasan['balasan_admin']) ?></p>
                                    </div>
                                <?php else: ?>
                                    <div class="review-admin-empty">
                                        Belum ada balasan admin.
                                    </div>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                </div>
                <?php if (count($ulasanPelanggan) > 5): ?>
                    <div class="review-history-pagination" data-review-pagination>
                        <button type="button" class="review-page-btn" data-review-prev>Sebelumnya</button>
                        <span class="review-page-info" data-review-page-info>Halaman 1</span>
                        <button type="button" class="review-page-btn" data-review-next>Berikutnya</button>
                    </div>
                <?php endif; ?>
            </aside>
        <?php endif; ?>
        </div>
    </section>

    <style>
        .review-back-btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            color: #4f6246;
            font-weight: 800;
            text-decoration: none;
        }

        .review-back-wrap {
            padding-top: 1.25rem;
            padding-bottom: .25rem;
        }

        .review-section {
            padding-top: 1rem;
            padding-bottom: 2.6rem;
        }

        .review-layout {
            display: grid;
            grid-template-columns: minmax(320px, 0.9fr) minmax(360px, 1.1fr);
            gap: 1.35rem;
            align-items: start;
            max-width: 1120px;
            margin: 0 auto;
        }

        .review-panel {
            max-width: none;
            margin: 0;
        }

        .review-history-panel {
            border: 1px solid rgba(122, 91, 67, 0.12);
            border-radius: 18px;
            background: rgba(255, 253, 248, 0.96);
            box-shadow: 0 18px 42px rgba(63, 48, 40, 0.07);
            padding: 1.15rem;
        }

        .review-history-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding-bottom: .9rem;
            border-bottom: 1px solid rgba(122, 91, 67, 0.12);
            margin-bottom: .9rem;
        }

        .review-history-kicker {
            display: block;
            margin-bottom: .25rem;
            color: #d66881;
            font-size: .68rem;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .review-history-head h2 {
            margin: 0;
            color: #4f6246;
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.35rem;
            line-height: 1.2;
        }

        .review-count-pill {
            flex: 0 0 auto;
            border-radius: 999px;
            padding: .32rem .75rem;
            color: #4f6246;
            background: #f2f6ef;
            border: 1px solid rgba(79, 98, 70, 0.12);
            font-size: .76rem;
            font-weight: 800;
        }

        .review-history-list {
            display: grid;
            gap: .8rem;
            max-height: 560px;
            overflow-y: auto;
            padding-right: .35rem;
            scrollbar-width: thin;
            scrollbar-color: #d8cfc3 transparent;
        }

        .review-history-card {
            border: 1px solid rgba(122, 91, 67, 0.13);
            border-radius: 14px;
            padding: .95rem;
            background: #fffdf8;
        }

        .review-history-top {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: .55rem;
        }

        .review-history-top strong {
            display: block;
            color: #3f372f;
            line-height: 1.35;
        }

        .review-history-top small {
            display: block;
            margin-top: .18rem;
            color: #8a7d72;
        }

        .review-history-top > span {
            color: #4f6246;
            font-weight: 900;
            white-space: nowrap;
        }

        .review-history-text {
            margin: 0;
            color: #4f4038;
            line-height: 1.58;
            font-size: .92rem;
        }

        .review-history-text strong {
            font-style: italic;
        }

        .review-admin-reply,
        .review-admin-empty {
            margin-top: .75rem;
            padding: .72rem .85rem;
            border-radius: 10px;
            font-size: .88rem;
        }

        .review-admin-reply {
            border-left: 3px solid #db83a6;
            background: #f1ebd9;
            color: #2e241e;
        }

        .review-admin-reply strong {
            display: block;
            margin-bottom: .28rem;
            color: #d66881;
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .review-admin-reply p {
            margin: 0;
            line-height: 1.55;
        }

        .review-admin-empty,
        .review-empty-state {
            background: #f7f1eb;
            color: #8a7d72;
        }

        .review-history-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin-top: 1rem;
            padding-top: .9rem;
            border-top: 1px solid rgba(122, 91, 67, 0.12);
        }

        .review-page-btn {
            min-height: 38px;
            padding: .55rem .95rem;
            border-radius: 999px;
            border: 1px solid rgba(79, 98, 70, 0.18);
            background: #f8faf6;
            color: #4f6246;
            font-size: .82rem;
            font-weight: 850;
            cursor: pointer;
            transition: all .2s ease;
        }

        .review-page-btn:hover:not(:disabled) {
            color: #fff;
            background: #4f6246;
            border-color: #4f6246;
            transform: translateY(-1px);
        }

        .review-page-btn:disabled {
            opacity: .42;
            cursor: not-allowed;
        }

        .review-page-info {
            color: #8a7d72;
            font-size: .82rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .review-empty-state {
            padding: 1rem;
            border-radius: 14px;
        }

        .review-toast {
            position: fixed;
            top: 1.25rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            padding: .95rem 1.15rem;
            border-radius: 14px;
            background: #dcebd4;
            border: 1px solid #bdd5b4;
            color: #3e5a38;
            font-weight: 800;
            box-shadow: 0 18px 45px rgba(63, 55, 47, .16);
            animation: reviewToastIn .25s ease-out;
        }

        .review-toast.is-hidden {
            opacity: 0;
            transform: translate(-50%, -8px);
            transition: opacity .35s ease, transform .35s ease;
        }

        @keyframes reviewToastIn {
            from {
                opacity: 0;
                transform: translate(-50%, -10px);
            }
            to {
                opacity: 1;
                transform: translate(-50%, 0);
            }
        }

        @media (max-width: 640px) {
            .review-layout {
                grid-template-columns: 1fr;
            }

            .review-history-list {
                max-height: 440px;
                padding-right: 0;
            }

            .review-toast {
                left: 1rem;
                right: 1rem;
                top: 1rem;
                transform: none;
            }

            .review-toast.is-hidden {
                transform: translateY(-8px);
            }
        }
    </style>

    <script>
        const reviewToast = document.getElementById('reviewToast');
        if (reviewToast) {
            setTimeout(() => {
                reviewToast.classList.add('is-hidden');
                setTimeout(() => reviewToast.remove(), 400);
            }, 3200);
        }

        const reviewItems = Array.from(document.querySelectorAll('[data-review-item]'));
        const reviewPrev = document.querySelector('[data-review-prev]');
        const reviewNext = document.querySelector('[data-review-next]');
        const reviewPageInfo = document.querySelector('[data-review-page-info]');
        const reviewPageSize = 5;
        const reviewTotalPages = Math.max(1, Math.ceil(reviewItems.length / reviewPageSize));
        let reviewPage = 0;

        function renderReviewPage() {
            reviewItems.forEach((item, index) => {
                const itemPage = Math.floor(index / reviewPageSize);
                item.hidden = itemPage !== reviewPage;
            });

            if (reviewPageInfo) {
                reviewPageInfo.textContent = `Halaman ${reviewPage + 1} dari ${reviewTotalPages}`;
            }
            if (reviewPrev) reviewPrev.disabled = reviewPage === 0;
            if (reviewNext) reviewNext.disabled = reviewPage >= reviewTotalPages - 1;
        }

        reviewPrev?.addEventListener('click', () => {
            reviewPage = Math.max(0, reviewPage - 1);
            renderReviewPage();
        });

        reviewNext?.addEventListener('click', () => {
            reviewPage = Math.min(reviewTotalPages - 1, reviewPage + 1);
            renderReviewPage();
        });

        renderReviewPage();
    </script>
<?php include __DIR__ . '/../templates/footer.php'; ?>
