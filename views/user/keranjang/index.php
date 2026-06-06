<?php $judulHalaman = 'Keranjang Belanja - SPAdmin Spa Bandung'; ?>
<?php $bodyClass = 'cart-page'; ?>
<?php include __DIR__ . '/../templates/header.php'; ?>

<style>
.cart-page-eyebrow {
    display: inline-block;
    margin-bottom: 8px;
    color: #d66881;
    font-family: 'Inter', sans-serif;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.2em;
    text-transform: uppercase;
}

.cart-page-title {
    margin: 0;
    color: #4f6048;
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(2.2rem, 5vw, 3.5rem);
    font-weight: 700;
    line-height: 1.15;
    letter-spacing: -0.01em;
}

.cart-page-title span {
    color: #d66881;
    font-style: italic;
    font-weight: 700;
}

.cart-summary-card {
    background: rgba(255, 255, 255, 0.72);
    border: 1px solid rgba(212, 185, 150, 0.38);
    border-radius: 16px;
    padding: 24px;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    box-shadow: 0 4px 12px rgba(63, 48, 40, 0.03);
}

.cart-summary-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 1.15rem;
    font-weight: 700;
    color: #3f3028;
    border-bottom: 1px solid rgba(63, 48, 40, 0.1);
    padding-bottom: 14px;
    margin: 0 0 16px 0;
}

.cart-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #6d625c;
    font-family: 'Inter', sans-serif;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 12px;
}

.cart-summary-row strong {
    color: #221d1b;
    font-weight: 700;
}

.cart-summary-row.border-bottom {
    border-bottom: 1px solid rgba(63, 48, 40, 0.06);
    padding-bottom: 12px;
    margin-bottom: 16px;
}

.cart-summary-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-family: 'Inter', sans-serif;
    font-weight: 800;
    color: #221d1b;
    font-size: 13.5px;
    margin: 16px 0 24px 0;
}

.cart-summary-total strong {
    color: #4f6048;
    font-weight: 800;
    font-size: 16px;
}

.cart-summary-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.cart-reservation-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    min-height: 48px;
    border-radius: 999px;
    background: #4f6048;
    color: #ffffff;
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 10px 22px rgba(79, 96, 72, 0.2);
    transition: all 0.3s ease;
}

.cart-clear-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    min-height: 48px;
    border: 1px solid rgba(214, 104, 129, 0.72);
    background: transparent;
    color: #d66881;
    font-family: 'Inter', sans-serif;
    font-size: 12px;
    font-weight: 700;
    border-radius: 999px;
    cursor: pointer;
    transition: all 0.3s ease;
    outline: none;
    box-shadow: none;
    padding: 0;
}

.cart-confirm-modal {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.25rem;
    background: rgba(47, 38, 31, 0.46);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
}

.cart-confirm-modal[hidden] {
    display: none;
}

.cart-confirm-modal.is-open {
    opacity: 1;
    pointer-events: auto;
}

.cart-confirm-dialog {
    width: min(100%, 440px);
    background: #fffdf9;
    border: 1px solid rgba(122, 91, 67, 0.14);
    border-radius: 18px;
    box-shadow: 0 24px 70px rgba(47, 38, 31, 0.22);
    padding: 1.35rem;
    transform: translateY(8px) scale(0.98);
    transition: transform 0.2s ease;
}

.cart-confirm-modal.is-open .cart-confirm-dialog {
    transform: translateY(0) scale(1);
}

.cart-confirm-icon {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #d66881;
    background: #fff0f4;
    border: 1px solid rgba(214, 104, 129, 0.18);
    margin-bottom: 0.95rem;
}

.cart-confirm-title {
    margin: 0 0 0.45rem;
    color: #3f3028;
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 1.45rem;
    font-weight: 700;
    line-height: 1.2;
}

.cart-confirm-message {
    margin: 0;
    color: #76675d;
    font-family: 'Inter', sans-serif;
    font-size: 0.92rem;
    line-height: 1.62;
}

.cart-confirm-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.25rem;
}

.cart-confirm-cancel,
.cart-confirm-accept {
    flex: 1;
    min-height: 44px;
    border-radius: 999px;
    font-family: 'Inter', sans-serif;
    font-size: 0.9rem;
    font-weight: 800;
    cursor: pointer;
    transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
}

.cart-confirm-cancel {
    background: #ffffff;
    color: #5f5047;
    border: 1px solid rgba(122, 91, 67, 0.18);
}

.cart-confirm-accept {
    background: #d66881;
    color: #ffffff;
    border: 1px solid #d66881;
    box-shadow: 0 10px 22px rgba(214, 104, 129, 0.2);
}

.cart-confirm-cancel:hover,
.cart-confirm-accept:hover {
    transform: translateY(-1px);
}

.cart-confirm-accept:hover {
    background: #c4526b;
    box-shadow: 0 14px 28px rgba(214, 104, 129, 0.26);
}

.cart-empty-state {
    position: relative;
    padding: 48px 64px;
    background: #ffffff;
    border: 1px solid rgba(122, 91, 67, 0.12);
    border-radius: 24px;
    box-shadow: 0 15px 45px rgba(63, 48, 40, 0.05);
    max-width: 680px;
    margin: 0 auto;
    z-index: 10;
    text-align: center;
}

.cart-empty-icon-wrap {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 64px;
    height: 64px;
    margin: 0 auto 16px;
    border-radius: 50%;
    background: #f8f1e7;
    border: 1px solid rgba(212, 185, 150, 0.2);
    box-shadow: 0 4px 10px rgba(122, 91, 67, 0.04);
}

.cart-empty-title {
    margin: 0 0 12px 0;
    color: #3f3028;
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 1.85rem;
    font-weight: 700;
    line-height: 1.3;
}

.cart-empty-copy {
    margin: 0 auto 24px;
    color: #7e746e;
    font-family: var(--font-sans);
    font-size: 0.88rem;
    line-height: 1.6;
    max-width: 480px;
}

.cart-empty-cta {
    display: inline-block;
    padding: 14px 38px;
    font-family: var(--font-sans);
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    background-color: #4f6048;
    color: #ffffff;
    border-radius: 999px;
    text-decoration: none;
    box-shadow: 0 10px 22px rgba(79, 96, 72, 0.25);
    transition: all 0.3s ease;
}
</style>

    <section class="container py-5">

        <div class="row mt-1">
            <div class="col-12">
                <div class="cart-page-heading" style="margin-bottom: 24px;">
                    <span class="cart-page-eyebrow">YOUR WELLNESS SELECTION</span>
                    <h1 class="cart-page-title">
                        Ready to begin<br>
                        <span>your retreat?</span>
                    </h1>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['pesan_sukses'])): ?>
            <div class="floating-alert success" id="floatingAlert" role="status" aria-live="polite">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <div class="alert-message"><?= e($_GET['pesan_sukses']) ?></div>
                <button class="alert-close" type="button" onclick="closeFloatingAlert()" aria-label="Tutup notifikasi">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['pesan_error'])): ?>
            <div class="floating-alert error" id="floatingAlert" role="alert">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                </div>
                <div class="alert-message"><?= e($_GET['pesan_error']) ?></div>
                <button class="alert-close" type="button" onclick="closeFloatingAlert()" aria-label="Tutup notifikasi">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (empty($cart)): ?>
            <div class="cart-empty-scene row py-4 mt-1 justify-content-center" style="position: relative; min-height: 380px; overflow: visible; isolation: isolate;">
                <div class="col-lg-8 col-xl-7">
                    <div class="cart-empty-state">
                        <div class="cart-empty-icon-wrap" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 26px; height: 26px; color: #3f3028;">
                                <circle cx="9" cy="21" r="1" fill="currentColor"/>
                                <circle cx="20" cy="21" r="1" fill="currentColor"/>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h2 class="cart-empty-title">Keranjang belanja Anda masih kosong</h2>
                        <p class="cart-empty-copy">Jelajahi beragam perawatan spa premium kami dan pilih layanan utama untuk memulai reservasi.</p>
                        <a href="index.php?action=layanan" class="cart-empty-cta">Jelajahi Layanan</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php $totalDurasi = array_sum(array_column($cart, 'durasi')); ?>
            <div class="row g-4 mt-2 align-items-start">
                <div class="col-lg-8">
                    <div class="cart-service-list">
                        <?php foreach ($cart as $id => $item): ?>
                            <article class="cart-service-card">
                                <div class="cart-service-media">
                                    <img src="<?= e(mediaLayanan($item['media'] ?? '', $item['nama'])) ?>" alt="<?= e($item['nama']) ?>">
                                </div>
                                <div class="cart-service-body">
                                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; flex-wrap: wrap; margin-bottom: 6px; width: 100%;">
                                        <h2 style="margin: 0;"><?= e($item['nama']) ?></h2>
                                        <span class="cart-service-badge"><?= e($item['kategori']) ?></span>
                                    </div>
                                    <div class="cart-service-meta">
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                            <?= formatDurasi($item['durasi']) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="cart-service-side">
                                    <strong><?= rupiah($item['harga']) ?></strong>
                                    <form method="POST" action="index.php?action=hapus-keranjang" data-cart-confirm data-confirm-title="Hapus layanan?" data-confirm-message="Layanan ini akan dihapus dari keranjang Anda. Anda masih bisa menambahkannya lagi dari halaman layanan." data-confirm-label="Hapus">
                                        <input type="hidden" name="layanan_id" value="<?= (int)$id ?>">
                                        <button type="submit" class="cart-remove-btn" title="Hapus Layanan" aria-label="Hapus <?= e($item['nama']) ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                        </button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <aside class="cart-summary-sidebar cart-summary-card">
                        <h2 class="cart-summary-title">Ringkasan Pemesanan</h2>

                        <div class="cart-summary-row">
                            <span>Total Item</span>
                            <strong><?= cartCount() ?> Layanan</strong>
                        </div>

                        <div class="cart-summary-row border-bottom">
                            <span>Total Durasi</span>
                            <strong><?= formatDurasiJam($totalDurasi) ?></strong>
                        </div>

                        <div class="cart-summary-total">
                            <span>Total Bayar</span>
                            <strong><?= rupiah(cartTotal()) ?></strong>
                        </div>

                        <div class="cart-summary-actions">
                            <a href="index.php?action=reservasi" class="cart-reservation-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                Lanjut ke Reservasi
                            </a>

                            <form method="POST" action="index.php?action=kosongkan-keranjang" data-cart-confirm data-confirm-title="Kosongkan keranjang?" data-confirm-message="Semua layanan yang sudah Anda pilih akan dihapus dari keranjang." data-confirm-label="Kosongkan">
                                <button type="submit" class="cart-clear-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    Kosongkan Keranjang
                                </button>
                            </form>
                        </div>
                    </aside>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <div class="cart-confirm-modal" id="cartConfirmModal" hidden role="dialog" aria-modal="true" aria-labelledby="cartConfirmTitle" aria-describedby="cartConfirmMessage">
        <div class="cart-confirm-dialog">
            <div class="cart-confirm-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path></svg>
            </div>
            <h2 class="cart-confirm-title" id="cartConfirmTitle">Konfirmasi</h2>
            <p class="cart-confirm-message" id="cartConfirmMessage">Apakah Anda yakin ingin melanjutkan?</p>
            <div class="cart-confirm-actions">
                <button type="button" class="cart-confirm-cancel" id="cartConfirmCancel">Batal</button>
                <button type="button" class="cart-confirm-accept" id="cartConfirmAccept">Ya, lanjutkan</button>
            </div>
        </div>
    </div>

    <script>
    function closeFloatingAlert() {
        const alertEl = document.getElementById('floatingAlert');
        if (alertEl) alertEl.classList.remove('show');
    }

    window.addEventListener('DOMContentLoaded', () => {
        const alertEl = document.getElementById('floatingAlert');
        if (alertEl) {
            setTimeout(() => alertEl.classList.add('show'), 150);
            setTimeout(() => alertEl.classList.remove('show'), 4000);
        }

        const modal = document.getElementById('cartConfirmModal');
        const title = document.getElementById('cartConfirmTitle');
        const message = document.getElementById('cartConfirmMessage');
        const cancelButton = document.getElementById('cartConfirmCancel');
        const acceptButton = document.getElementById('cartConfirmAccept');
        let pendingForm = null;

        function openConfirm(form) {
            pendingForm = form;
            title.textContent = form.dataset.confirmTitle || 'Konfirmasi';
            message.textContent = form.dataset.confirmMessage || 'Apakah Anda yakin ingin melanjutkan?';
            acceptButton.textContent = form.dataset.confirmLabel || 'Ya, lanjutkan';
            modal.hidden = false;
            requestAnimationFrame(() => modal.classList.add('is-open'));
            cancelButton.focus();
        }

        function closeConfirm() {
            modal.classList.remove('is-open');
            pendingForm = null;
            setTimeout(() => {
                if (!modal.classList.contains('is-open')) {
                    modal.hidden = true;
                }
            }, 180);
        }

        document.querySelectorAll('form[data-cart-confirm]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                openConfirm(form);
            });
        });

        cancelButton?.addEventListener('click', closeConfirm);
        modal?.addEventListener('click', (event) => {
            if (event.target === modal) closeConfirm();
        });
        acceptButton?.addEventListener('click', () => {
            if (!pendingForm) return;
            const form = pendingForm;
            closeConfirm();
            form.submit();
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal && !modal.hidden) {
                closeConfirm();
            }
        });
    });
    </script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
