<?php $judulHalaman = 'Daftar Layanan - SPAdmin Spa Bandung'; ?>
<?php include __DIR__ . '/../templates/header.php'; ?>

    <section class="layanan-showcase-hero">
        <div class="container">
            <div class="layanan-hero-grid">
                <div class="layanan-hero-copy">
                    <h1>
                        Curated treatments<br>
                        <span>for your wellbeing.</span>
                    </h1>
                    <div class="layanan-hero-divider" aria-hidden="true">
                        <span></span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="18" viewBox="0 0 22 18" fill="none">
                            <path d="M7 6.7C7.6 9.7 9.9 12 11 12C12.1 12 14.4 9.7 15 6.7C13.2 7 11.8 8 11 9.4C10.2 8 8.8 7 7 6.7Z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/>
                            <path d="M5.2 13.3H16.8" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                        </svg>
                        <span></span>
                    </div>
                    <p>Perawatan terbaik untuk relaksasi tubuh, menenangkan pikiran, dan memulihkan energi Anda.</p>
                </div>
                <div class="layanan-hero-image" aria-hidden="true">
                    <img src="assets/images/hero_spa_bg.jpg" alt="">
                </div>
            </div>
        </div>
    </section>

    <section class="layanan-catalog-section">
        <div class="container">
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

            <?php
            $kategoriAktif = trim($kategori ?? '');
            $labelKategoriAktif = $kategoriAktif !== '' ? $kategoriAktif : 'Semua Kategori';

            $kategoriIcon = function ($namaKategori) {
                $key = strtolower($namaKategori);

                if ($key === '') {
                    return '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="6" height="6" rx="1.2"></rect><rect x="14" y="4" width="6" height="6" rx="1.2"></rect><rect x="4" y="14" width="6" height="6" rx="1.2"></rect><rect x="14" y="14" width="6" height="6" rx="1.2"></rect></svg>';
                }

                if (strpos($key, 'combo') !== false) {
                    return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 9h10l-1 10H8L7 9Z"></path><path d="M9 9V7a3 3 0 0 1 6 0v2"></path><path d="M8 13h8"></path></svg>';
                }

                if (strpos($key, 'lulur') !== false) {
                    return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5c2.5 2.2 4 4.4 4 6.4a4 4 0 0 1-8 0C8 9.4 9.5 7.2 12 5Z"></path><path d="M7 18c2.6 1.2 7.4 1.2 10 0"></path><path d="M9 15c1.6 1 4.4 1 6 0"></path></svg>';
                }

                if (strpos($key, 'signature') !== false) {
                    return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 3 3 3-6 3 6 3-3-1.5 9h-9L6 9Z"></path><path d="M8 19h8"></path></svg>';
                }

                if (strpos($key, 'pijat') !== false) {
                    return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 14c1.7-2 3.3-3 5-3h5"></path><path d="M6 18c2.6-1.2 4.6-2.1 7-2.1h5"></path><path d="M5 10c1.4 0 2.6-.8 3.4-2.4"></path><path d="M11 7c1.2.6 2.1 1.5 2.7 2.7"></path></svg>';
                }

                if (strpos($key, 'refleksi') !== false) {
                    return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 4c3 1 4.7 3.2 4.7 6.2 0 2.5-1.2 4.5-2.6 6.3"></path><path d="M12.6 18.6c-1 1.6-3.4 1.4-4-.3-.5-1.3.6-2.8 1.5-4.2"></path><path d="M14 6.5h3"></path><path d="M14.5 10h2.6"></path></svg>';
                }

                if (strpos($key, 'spesial') !== false || strpos($key, 'special') !== false) {
                    return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5c1.3 2.8 3 4.5 6 5-2.8 1.2-4.5 3-5.1 6-1.2-2.9-3-4.6-5.9-5.1C9.8 9.7 11.5 8 12 5Z"></path><path d="M6 4v3"></path><path d="M4.5 5.5h3"></path></svg>';
                }

                if (strpos($key, 'bekam') !== false) {
                    return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 7h6l1.5 9.5A2.2 2.2 0 0 1 14.3 19H9.7a2.2 2.2 0 0 1-2.2-2.5L9 7Z"></path><path d="M10 5h4"></path><path d="M9 12h6"></path></svg>';
                }

                return '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="7"></circle><path d="M12 8v8"></path><path d="M8 12h8"></path></svg>';
            };

            $kategoriUrl = function ($nilaiKategori) use ($keyword, $durasi, $sort) {
                return 'index.php?' . http_build_query([
                    'action' => 'layanan',
                    'keyword' => $keyword ?? '',
                    'kategori' => $nilaiKategori,
                    'durasi' => $durasi ?? '',
                    'sort' => $sort ?? '',
                ]);
            };

            $opsiUrutan = [
                '' => 'Urutkan',
                'harga-terendah' => 'Harga terendah',
                'harga-tertinggi' => 'Harga tertinggi',
                'durasi-terpendek' => 'Durasi terpendek',
                'durasi-terpanjang' => 'Durasi terpanjang',
            ];

            $sortAktif = $sort ?? '';
            $labelSortAktif = $opsiUrutan[$sortAktif] ?? 'Urutkan';

            $sortIcon = function ($nilaiSort) {
                if ($nilaiSort === 'harga-terendah') {
                    return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7h7"></path><path d="M7 12h5"></path><path d="M7 17h3"></path><path d="M17 7v10"></path><path d="m14 14 3 3 3-3"></path></svg>';
                }

                if ($nilaiSort === 'harga-tertinggi') {
                    return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7h3"></path><path d="M7 12h5"></path><path d="M7 17h7"></path><path d="M17 17V7"></path><path d="m14 10 3-3 3 3"></path></svg>';
                }

                if ($nilaiSort === 'durasi-terpendek') {
                    return '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="7"></circle><path d="M12 8v4l2 1.5"></path><path d="M4 5l2.2 2.2"></path><path d="M20 5l-2.2 2.2"></path></svg>';
                }

                if ($nilaiSort === 'durasi-terpanjang') {
                    return '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="7"></circle><path d="M12 7v5l3.5 2"></path><path d="M9 2h6"></path><path d="M12 2v3"></path></svg>';
                }

                return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16"></path><path d="M7 12h10"></path><path d="M10 17h4"></path></svg>';
            };

            $sortUrl = function ($nilaiSort) use ($keyword, $kategori, $durasi) {
                return 'index.php?' . http_build_query([
                    'action' => 'layanan',
                    'keyword' => $keyword ?? '',
                    'kategori' => $kategori ?? '',
                    'durasi' => $durasi ?? '',
                    'sort' => $nilaiSort,
                ]);
            };
            ?>

            <div class="layanan-toolbar">
                <form class="layanan-filter-form" method="GET" action="index.php">
                    <input type="hidden" name="action" value="layanan">

                    <label class="layanan-search-control">
                        <span class="visually-hidden">Cari layanan</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" name="keyword" placeholder="Cari massage, facial, reflexology..." value="<?= e($keyword) ?>">
                    </label>

                    <details class="kategori-dropdown">
                        <summary aria-label="Pilih kategori layanan">
                            <span class="kategori-summary-icon"><?= $kategoriIcon($kategoriAktif) ?></span>
                            <span><?= e($labelKategoriAktif) ?></span>
                            <svg class="kategori-summary-chevron" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </summary>

                        <div class="kategori-dropdown-menu">
                            <a class="kategori-dropdown-item <?= $kategoriAktif === '' ? 'active' : '' ?>" href="<?= e($kategoriUrl('')) ?>">
                                <span class="kategori-item-icon"><?= $kategoriIcon('') ?></span>
                                <span>Semua Kategori</span>
                                <svg class="kategori-item-check" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </a>

                            <?php foreach ($kategoriLayanan as $item): ?>
                                <?php $namaKategori = $item['kategori']; ?>
                                <a class="kategori-dropdown-item <?= $kategoriAktif === $namaKategori ? 'active' : '' ?>" href="<?= e($kategoriUrl($namaKategori)) ?>">
                                    <span class="kategori-item-icon"><?= $kategoriIcon($namaKategori) ?></span>
                                    <span><?= e($namaKategori) ?></span>
                                    <svg class="kategori-item-check" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </details>

                    <details class="sort-dropdown">
                        <summary aria-label="Pilih urutan layanan">
                            <span class="sort-summary-icon"><?= $sortIcon($sortAktif) ?></span>
                            <span><?= e($labelSortAktif) ?></span>
                            <svg class="sort-summary-chevron" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </summary>

                        <div class="sort-dropdown-menu">
                            <?php foreach ($opsiUrutan as $nilaiUrutan => $labelUrutan): ?>
                                <a class="sort-dropdown-item <?= $sortAktif === $nilaiUrutan ? 'active' : '' ?>" href="<?= e($sortUrl($nilaiUrutan)) ?>">
                                    <span class="sort-item-icon"><?= $sortIcon($nilaiUrutan) ?></span>
                                    <span><?= e($labelUrutan) ?></span>
                                    <svg class="sort-item-check" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </details>

                    <button class="layanan-filter-submit" type="submit">Terapkan</button>
                </form>
            </div>

            <?php $judulKategori = trim($kategori ?? '') !== '' ? strtoupper($kategori) : 'ALL TREATMENTS'; ?>
            <div class="layanan-section-heading">
                <span><?= e($judulKategori) ?></span>
                <span>Menampilkan <?= count($layanan) ?> Layanan</span>
            </div>

            <?php if (!$layanan): ?>
                <div class="empty-state py-5 text-center">
                    <h2>Layanan tidak ditemukan</h2>
                    <p class="section-desc mx-auto mt-2">Coba gunakan kata kunci, kategori, durasi, atau urutan yang berbeda.</p>
                </div>
            <?php endif; ?>

            <div class="layanan-card-grid">
                <?php foreach ($layanan as $item): ?>
                    <article class="layanan-treatment-card">
                        <a class="layanan-card-media" href="index.php?action=detail-layanan&id=<?= (int) $item['id'] ?>" aria-label="Lihat detail <?= e($item['nama_layanan']) ?>">
                            <img src="<?= e(mediaLayanan($item['media'], $item['nama_layanan'])) ?>" alt="Gambar Layanan <?= e($item['nama_layanan']) ?>" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span class="service-img-fallback">
                                <?= e($item['nama_layanan']) ?>
                            </span>
                        </a>
                        <div class="layanan-card-body">
                            <span class="layanan-card-badge"><?= e($item['kategori']) ?></span>
                            <h2><?= e($item['nama_layanan']) ?></h2>
                            <p><?= e($item['deskripsi']) ?></p>
                            <div class="layanan-card-meta">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <?= formatDurasi($item['durasi']) ?>
                                </span>
                                <strong><?= rupiah($item['harga']) ?></strong>
                            </div>
                        </div>
                        <div class="layanan-card-actions">
                            <a href="index.php?action=detail-layanan&id=<?= (int) $item['id'] ?>">Lihat Detail</a>
                            <?php if (isInCart($item['id'])): ?>
                                <form method="POST" action="index.php?action=hapus-keranjang" data-cart-confirm data-confirm-title="Hapus layanan?" data-confirm-message="Layanan ini akan dihapus dari keranjang Anda." data-confirm-label="Hapus">
                                    <input type="hidden" name="layanan_id" value="<?= (int) $item['id'] ?>">
                                    <input type="hidden" name="redirect_to" value="index.php?action=layanan&keyword=<?= urlencode($keyword ?? '') ?>&kategori=<?= urlencode($kategori ?? '') ?>&durasi=<?= urlencode($durasi ?? '') ?>&sort=<?= urlencode($sort ?? '') ?>&page=<?= $page ?? 1 ?>">
                                    <button type="submit" class="layanan-card-cart is-added">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                        Hapus
                                    </button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="index.php?action=tambah-keranjang">
                                    <input type="hidden" name="layanan_id" value="<?= (int) $item['id'] ?>">
                                    <input type="hidden" name="redirect_to" value="index.php?action=layanan&keyword=<?= urlencode($keyword ?? '') ?>&kategori=<?= urlencode($kategori ?? '') ?>&durasi=<?= urlencode($durasi ?? '') ?>&sort=<?= urlencode($sort ?? '') ?>&page=<?= $page ?? 1 ?>">
                                    <button type="submit" class="layanan-card-cart">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                        Keranjang
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($totalHalaman > 1): ?>
                <style>
                    .spd-pag { display:flex; gap:10px; align-items:center; justify-content:center; margin-top:48px; list-style:none; padding:0; }
                    .spd-pag li a, .spd-pag li span {
                        display:flex !important; align-items:center !important; justify-content:center !important;
                        width:48px !important; height:48px !important; border-radius:50% !important;
                        background:#ffffff !important; border:1.5px solid #e2d9cf !important;
                        color:#7a5b43 !important; font-weight:700 !important; font-size:0.95rem !important;
                        font-family:'Inter',sans-serif !important; text-decoration:none !important;
                        line-height:1 !important; transition:all .25s ease !important; cursor:pointer; box-sizing:border-box !important;
                    }
                    .spd-pag li a:hover { background:#f7f1e7 !important; border-color:#7a5b43 !important; color:#7a5b43 !important; transform:translateY(-2px); text-decoration:none !important; }
                    .spd-pag li a.active { background:#7a5b43 !important; border-color:#7a5b43 !important; color:#ffffff !important; text-decoration:none !important; cursor:default; }
                    .spd-pag li span.disabled-btn { opacity:0.4; cursor:not-allowed; pointer-events:none; border-color:#ede8e2 !important; color:rgba(122,91,67,0.3) !important; }
                </style>
                <div style="display:flex; justify-content:center; margin-top:2.5rem;">
                    <nav aria-label="Navigasi Halaman Layanan">
                        <ul class="spd-pag">
                            <?php if ($page > 1): ?>
                                <li>
                                    <a href="index.php?action=layanan&keyword=<?= urlencode($keyword) ?>&kategori=<?= urlencode($kategori) ?>&durasi=<?= urlencode($durasi ?? '') ?>&sort=<?= urlencode($sort ?? '') ?>&page=<?= $page - 1 ?>" aria-label="Sebelumnya">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="20" y1="12" x2="4" y2="12"/><polyline points="10 18 4 12 10 6"/></svg>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li><span class="disabled-btn"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="20" y1="12" x2="4" y2="12"/><polyline points="10 18 4 12 10 6"/></svg></span></li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalHalaman; $i++): ?>
                                <li>
                                    <a class="<?= $i === $page ? 'active' : '' ?>" href="index.php?action=layanan&keyword=<?= urlencode($keyword) ?>&kategori=<?= urlencode($kategori) ?>&durasi=<?= urlencode($durasi ?? '') ?>&sort=<?= urlencode($sort ?? '') ?>&page=<?= $i ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalHalaman): ?>
                                <li>
                                    <a href="index.php?action=layanan&keyword=<?= urlencode($keyword) ?>&kategori=<?= urlencode($kategori) ?>&durasi=<?= urlencode($durasi ?? '') ?>&sort=<?= urlencode($sort ?? '') ?>&page=<?= $page + 1 ?>" aria-label="Selanjutnya">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="12" x2="20" y2="12"/><polyline points="14 6 20 12 14 18"/></svg>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li><span class="disabled-btn"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="12" x2="20" y2="12"/><polyline points="14 6 20 12 14 18"/></svg></span></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php include __DIR__ . '/../templates/footer.php'; ?>
