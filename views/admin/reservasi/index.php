<?php

if (!function_exists('rupiah')) {
    function rupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
}
$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : 'Semua';
?>




<!-- Search Bar -->
<div class="panel" style="margin-bottom: 20px;">
    <div class="panel-body" style="padding: 14px 20px;">
        <div style="position: relative; max-width: 420px;">
            <i class="fa-solid fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
            <input type="text" id="searchReservasi" placeholder="Cari nama pelanggan, layanan, atau terapis..."
                   style="width: 100%; padding: 9px 12px 9px 36px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); outline: none; background: var(--bg-light); color: var(--text-dark); font-size: 0.87rem; box-sizing: border-box;"
                   oninput="filterReservasi(this.value)">
        </div>
    </div>
</div>
<div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 25px;">
    <a href="admin.php?page=reservasi" class="btn-spa <?php echo $statusFilter === 'Semua' ? 'btn-spa-accent' : 'btn-spa-outline'; ?>" style="font-size: 0.85rem; padding: 8px 16px;">
        Semua Reservasi (<?php echo $counts['Semua']; ?>)
    </a>
    <a href="admin.php?page=reservasi&status=Menunggu" class="btn-spa <?php echo $statusFilter === 'Menunggu' ? 'btn-spa-accent' : 'btn-spa-outline'; ?>" style="font-size: 0.85rem; padding: 8px 16px;">
        <i class="fa-solid fa-hourglass-half"></i> Menunggu (<?php echo $counts['Menunggu']; ?>)
    </a>
    <a href="admin.php?page=reservasi&status=Diterima" class="btn-spa <?php echo $statusFilter === 'Diterima' ? 'btn-spa-accent' : 'btn-spa-outline'; ?>" style="font-size: 0.85rem; padding: 8px 16px;">
        <i class="fa-solid fa-calendar-check"></i> Diterima (<?php echo $counts['Diterima']; ?>)
    </a>
    <a href="admin.php?page=reservasi&status=Ditolak" class="btn-spa <?php echo $statusFilter === 'Ditolak' ? 'btn-spa-accent' : 'btn-spa-outline'; ?>" style="font-size: 0.85rem; padding: 8px 16px;">
        <i class="fa-solid fa-circle-xmark"></i> Ditolak (<?php echo $counts['Ditolak']; ?>)
    </a>
    <a href="admin.php?page=reservasi&status=Dibatalkan" class="btn-spa <?php echo $statusFilter === 'Dibatalkan' ? 'btn-spa-accent' : 'btn-spa-outline'; ?>" style="font-size: 0.85rem; padding: 8px 16px;">
        <i class="fa-solid fa-ban"></i> Dibatalkan (<?php echo $counts['Dibatalkan']; ?>)
    </a>
    <a href="admin.php?page=reservasi&status=Selesai" class="btn-spa <?php echo $statusFilter === 'Selesai' ? 'btn-spa-accent' : 'btn-spa-outline'; ?>" style="font-size: 0.85rem; padding: 8px 16px;">
        <i class="fa-solid fa-circle-check"></i> Selesai (<?php echo $counts['Selesai']; ?>)
    </a>
</div>

<div class="panel">
    <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="panel-title">Data Pengajuan Reservasi SPA (<?php echo htmlspecialchars($statusFilter); ?>)</h3>
        <button id="btnLihatRuangan" class="btn-spa btn-spa-outline" style="font-size: 0.85rem; padding: 6px 12px; display: flex; align-items: center; gap: 6px; cursor: pointer;">
            <i class="fa-solid fa-door-closed"></i> Ketersediaan Ruangan
        </button>
    </div>
    
    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="custom-table" data-admin-paginate data-per-page="6" data-noun="data">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">ID</th>
                        <th style="width: 22%;">Profil Pelanggan</th>
                        <th style="width: 22%;">Layanan SPA</th>
                        <th style="width: 23%;">Tanggal &amp; Jam Reservasi</th>
                        <th style="width: 17%;">Terapis Bertugas</th>
                        <th style="width: 160px; text-align: center;">Status</th>
                        <th style="width: 90px; text-align: center;">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reservasiList)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fa-solid fa-calendar-xmark mb-2 d-block fs-3"></i> Tidak ditemukan data reservasi untuk filter ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reservasiList as $r): ?>
                            <tr>
                                <td style="text-align: center; font-weight: 600; color: var(--text-muted);">#<?php echo $r['id_reservasi']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($r['namaPelanggan']); ?></strong>
                                </td>
                                <td>
                                    <div style="font-weight: 600; max-width: 250px; display: inline-block; white-space: normal; line-height: 1.4; word-break: break-word; vertical-align: middle;">
                                        <?php echo htmlspecialchars($r['layananNames']); ?>
                                    </div>
                                </td>
                                <td style="white-space: nowrap;">
                                    <strong><?php echo date('d M Y', strtotime($r['reservationDate'])); ?></strong><br>
                                    <span style="color: var(--text-muted); font-size: 0.8rem; display: inline-block; margin-top: 4px;"><i class="fa-regular fa-clock" style="margin-right: 3px;"></i> <?php echo date('H:i', strtotime($r['reservationDate'])); ?> WIB</span>
                                </td>
                                <td>
                                    <?php if ($r['namaTerapis']): ?>
                                        <span style="font-weight: 600; color: var(--primary-light); white-space: nowrap;">
                                            <i class="fa-solid fa-user-doctor" style="color: var(--accent); margin-right: 4px;"></i> <?php echo htmlspecialchars($r['namaTerapis']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary" style="font-size: 0.75rem; white-space: nowrap;">
                                            <i class="fa-solid fa-circle-question" style="margin-right: 4px;"></i> Belum Ditugaskan
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $statusClass = 'badge-secondary';
                                    $sVal = $r['statusReservation'];
                                    if (in_array($sVal, ['Menunggu', 'Menunggu Pembayaran', 'Menunggu Validasi'])) {
                                        $statusClass = 'badge-warning';
                                    } elseif (in_array($sVal, ['Diterima', 'Dikonfirmasi'])) {
                                        $statusClass = 'badge-info';
                                    } elseif ($sVal === 'Selesai') {
                                        $statusClass = 'badge-success';
                                    } elseif (in_array($sVal, ['Dibatalkan', 'Ditolak', 'Hangus'])) {
                                        $statusClass = 'badge-danger';
                                    }
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($sVal); ?></span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="admin.php?page=reservasi&action=detail&id=<?php echo $r['idReservasi']; ?>" class="btn-spa btn-spa-outline" style="padding: 6px 12px; font-size: 0.8rem; gap: 5px;">
                                        <i class="fa-solid fa-pen-to-square"></i> Kelola
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="ruanganModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div style="background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); max-width: 500px; width: 100%; box-shadow: var(--shadow-lg); overflow: hidden; margin: 10% auto;">
        <div style="padding: 15px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background-color: var(--primary); color: var(--accent);">
            <h4 style="margin: 0; font-weight: 700; font-family: var(--font-heading); color: var(--accent) !important;"><i class="fa-solid fa-door-open" style="margin-right: 8px;"></i> Ketersediaan Ruangan SPA</h4>
            <span class="ruangan-close" style="color: var(--accent); font-size: 24px; font-weight: bold; cursor: pointer;">&times;</span>
        </div>
        <div style="padding: 20px;">
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach ($ruanganStatus as $rm): ?>
                    <div class="ruangan-item" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 15px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background-color: var(--bg-light);">
                        <div>
                            <strong style="font-size: 1rem; color: var(--primary);"><?php echo htmlspecialchars($rm['nama_ruangan']); ?></strong>
                            <?php if ((int)($rm['is_busy'] ?? 0) > 0): ?>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">
                                    Sedang melayani: <strong><?php echo htmlspecialchars($rm['nama_pelanggan']); ?></strong>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if ((int)($rm['is_busy'] ?? 0) > 0): ?>
                            <span class="badge badge-info" style="font-size: 0.75rem; padding: 4px 8px;">
                                <i class="fa-solid fa-circle animate-pulse" style="font-size: 0.5rem; margin-right: 4px;"></i> Sibuk
                            </span>
                        <?php else: ?>
                            <span class="badge badge-success" style="font-size: 0.75rem; padding: 4px 8px;">
                                <i class="fa-solid fa-circle" style="font-size: 0.5rem; margin-right: 4px;"></i> Tersedia
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="ruanganPagination" style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid var(--border-color);">
                <button type="button" id="prevRuanganBtn" class="btn-spa btn-spa-outline" style="padding: 6px 12px; font-size: 0.8rem; display: flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-chevron-left"></i> Prev
                </button>
                <span id="ruanganPageIndicator" style="font-size: 0.85rem; font-weight: 600; color: var(--primary);">Halaman 1 dari 2</span>
                <button type="button" id="nextRuanganBtn" class="btn-spa btn-spa-outline" style="padding: 6px 12px; font-size: 0.8rem; display: flex; align-items: center; gap: 5px; flex-direction: row-reverse;">
                    <i class="fa-solid fa-chevron-right"></i> Next
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var btn = document.getElementById('btnLihatRuangan');
    var modal = document.getElementById('ruanganModal');
    var close = document.querySelector('.ruangan-close');

    if (btn && modal) {
        btn.addEventListener('click', function() {
            modal.style.display = 'flex';
            showPage(1);
        });
        close.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        modal.addEventListener('click', function(e) {
            if (e.target === modal) modal.style.display = 'none';
        });
    }

    var items = document.querySelectorAll('.ruangan-item');
    var itemsPerPage = 5;
    var totalPages = Math.ceil(items.length / itemsPerPage);
    var currentPage = 1;

    var prevBtn = document.getElementById('prevRuanganBtn');
    var nextBtn = document.getElementById('nextRuanganBtn');
    var indicator = document.getElementById('ruanganPageIndicator');

    function showPage(page) {
        if (page < 1) page = 1;
        if (page > totalPages) page = totalPages;
        currentPage = page;

        for (var i = 0; i < items.length; i++) {
            if (i >= (currentPage - 1) * itemsPerPage && i < currentPage * itemsPerPage) {
                items[i].style.display = 'flex';
            } else {
                items[i].style.display = 'none';
            }
        }

        if (indicator) {
            indicator.textContent = 'Halaman ' + currentPage + ' dari ' + (totalPages || 1);
        }

        if (prevBtn) {
            prevBtn.disabled = (currentPage === 1);
            prevBtn.style.opacity = (currentPage === 1) ? '0.5' : '1';
            prevBtn.style.cursor = (currentPage === 1) ? 'default' : 'pointer';
        }
        if (nextBtn) {
            nextBtn.disabled = (currentPage === totalPages || totalPages === 0);
            nextBtn.style.opacity = (currentPage === totalPages || totalPages === 0) ? '0.5' : '1';
            nextBtn.style.cursor = (currentPage === totalPages || totalPages === 0) ? 'default' : 'pointer';
        }
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                showPage(currentPage - 1);
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (currentPage < totalPages) {
                showPage(currentPage + 1);
            }
        });
    }

    showPage(1);
})();
</script>
