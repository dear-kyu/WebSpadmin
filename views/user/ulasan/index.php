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

    <section class="container py-4">
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

            <?php if (!empty($ulasanPelanggan)): ?>
                <div style="<?= !empty($reservasi) ? 'margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e8ded5;' : '' ?>">
                    <h2 style="font-size: 1.15rem; margin-bottom: 1rem; color: #4f6246;">Ulasan Kamu</h2>
                    <div style="display: grid; gap: 1rem;">
                        <?php foreach ($ulasanPelanggan as $ulasan): ?>
                            <article style="border: 1px solid #e8ded5; border-radius: 14px; padding: 1rem; background: #fffdf8;">
                                <div style="display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: .65rem;">
                                    <div>
                                        <strong style="display: block; color: #3f372f;"><?= e($ulasan['nama_layanan']) ?></strong>
                                        <small style="color: #8a7d72;"><?= e(date('d M Y', strtotime($ulasan['created_at']))) ?></small>
                                    </div>
                                    <span style="font-weight: 800; color: #4f6246; white-space: nowrap;"><?= (int) $ulasan['rating'] ?>/5</span>
                                </div>
                                <p style="margin: 0; color: #4f4038; line-height: 1.65;">
                                    <strong style="font-style: italic;">Ulasan Anda:</strong>
                                    "<?= e($ulasan['isi_ulasan']) ?>"
                                </p>

                                <?php if (!empty($ulasan['balasan_admin'])): ?>
                                    <div style="margin-top: .9rem; padding: .85rem 1rem; border-left: 3px solid #db83a6; border-radius: 4px 8px 8px 4px; background: #f1ebd9; color: #2e241e;">
                                        <strong style="display: block; margin-bottom: .35rem; color: #d66881; font-size: .78rem; text-transform: uppercase; letter-spacing: .06em;">SPADMIN</strong>
                                        <p style="margin: 0; line-height: 1.6;"><?= e($ulasan['balasan_admin']) ?></p>
                                    </div>
                                <?php else: ?>
                                    <div style="margin-top: .9rem; padding: .85rem; border-radius: 12px; background: #f7f1eb; color: #8a7d72;">
                                        Belum ada balasan admin.
                                    </div>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php elseif (empty($reservasi)): ?>
                <div style="padding: 1rem; border-radius: 14px; background: #f7f1eb; color: #6f6258;">
                    Belum ada reservasi selesai yang bisa diulas.
                </div>
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

        .review-panel {
            max-width: 760px;
            margin: 0 auto;
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
    </script>
<?php include __DIR__ . '/../templates/footer.php'; ?>
