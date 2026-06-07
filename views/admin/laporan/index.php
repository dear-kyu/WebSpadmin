<?php

if (!function_exists('rupiah')) {
    function rupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('metodeLaporan')) {
    function metodeLaporan($row) {
        $method = trim($row['paymentMethod'] ?? $row['payment_method'] ?? 'Tunai');
        $method = trim(preg_replace('/\s*\((DP Hangus|Pembayaran Hangus)\)\s*/i', '', $method));
        if ($method === '') {
            $method = 'Tunai';
        }

        $statusReservasi = strtolower(trim($row['statusReservation'] ?? $row['status_reservation'] ?? ''));
        $statusPembayaran = strtolower(trim($row['statusPayment'] ?? $row['status_payment'] ?? ''));
        $jenisPembayaran = $row['jenisPembayaran'] ?? $row['jenis_pembayaran'] ?? 'DP 50%';
        $reservasiMasihHangus = in_array($statusReservasi, ['dibatalkan', 'hangus'], true);
        $pembayaranMasihHangus = in_array($statusPembayaran, ['dp hangus', 'pembayaran hangus'], true);

        if ($reservasiMasihHangus && $pembayaranMasihHangus) {
            $labelHangus = $statusPembayaran === 'pembayaran hangus' || $jenisPembayaran === 'Lunas 100%' ? 'Pembayaran Hangus' : 'DP Hangus';
            return $method . ' (' . $labelHangus . ')';
        }

        return $method;
    }
}

$rataRataTx = $totalTxCount > 0 ? ($totalPendapatan / $totalTxCount) : 0;
?>

<style>
.top-navbar {
    display: none !important;
}
.custom-table th {
    padding: 14px 16px !important;
}
.custom-table td {
    padding: 16px 16px !important;
}
.custom-table th:first-child,
.custom-table td:first-child {
    padding-left: 24px !important;
}
.custom-table th:last-child,
.custom-table td:last-child {
    padding-right: 24px !important;
}
@media (max-width: 900px) {
    .laporan-top-layout-row {
        flex-direction: column;
        align-items: stretch !important;
        gap: 12px !important;
    }
}
/* Redesigned Laporan Stats Cards */
.laporan-card {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 24px 28px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 4px 20px rgba(164, 140, 113, 0.05);
    transition: all 0.3s ease;
}
.laporan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(164, 140, 113, 0.12);
    border-color: #dcc8b1;
}
.laporan-card-icon {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    background-color: #faf2ea;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.3s ease;
}
.laporan-card:hover .laporan-card-icon {
    background-color: #f3ebe1;
}
.laporan-card-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.laporan-card-title {
    font-size: 0.78rem;
    font-weight: 700;
    color: #8c827a;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    margin: 0;
}
.laporan-card-value {
    font-size: 1.8rem;
    font-weight: 800;
    color: #352f2c;
    margin: 0;
    line-height: 1.2;
    font-family: var(--font-heading), sans-serif;
}
.laporan-card-trend {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.8rem;
    color: #8c827a;
    font-weight: 500;
    margin-top: 2px;
}
.laporan-card-trend-arrow {
    color: #2b8a3e;
    font-weight: 700;
    font-size: 0.95rem;
}
</style>

<!-- ===== TRANSPARENT HEADER BAR ===== -->
<div class="laporan-header-row" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px;">
    <div>
        <h1 style="font-family: var(--font-heading); font-size: 1.8rem; font-weight: 800; color: var(--primary); margin: 0; line-height: 1.2;">Laporan</h1>
        <p style="color: var(--text-muted); font-size: 0.88rem; margin: 6px 0 0 0;">Ringkasan transaksi dan pendapatan SPA</p>
    </div>
    <div style="background: #fff; border: 1px solid var(--border-color); border-radius: 8px; padding: 8px 16px; display: flex; align-items: center; gap: 8px; box-shadow: var(--shadow-sm); font-size: 0.88rem; color: var(--text-muted); font-weight: 600;">
        <i class="fa-regular fa-calendar-days" style="color: var(--accent);"></i>
        <span><?php echo date('d M Y'); ?></span>
    </div>
</div>

<!-- ===== STATS CARDS ===== -->
<div class="stats-grid">
    <!-- Card 1: Total Pendapatan -->
    <div class="laporan-card">
        <div class="laporan-card-icon">
            <div style="width: 26px; height: 26px; border-radius: 50%; border: 1.8px solid #a48c71; display: flex; align-items: center; justify-content: center; color: #a48c71; font-weight: 700; font-size: 0.85rem; font-family: sans-serif; line-height: 1;">Rp</div>
        </div>
        <div class="laporan-card-info">
            <span class="laporan-card-title">Total Pendapatan</span>
            <h2 class="laporan-card-value"><?php echo rupiah($totalPendapatan); ?></h2>
        </div>
    </div>
    
    <!-- Card 2: Volume Transaksi -->
    <div class="laporan-card">
        <div class="laporan-card-icon">
            <i class="fa-solid fa-arrow-trend-up" style="font-size: 1.35rem; color: #a48c71;"></i>
        </div>
        <div class="laporan-card-info">
            <span class="laporan-card-title">Volume Transaksi</span>
            <h2 class="laporan-card-value"><?php echo htmlspecialchars($totalTxCount); ?> Tx</h2>
        </div>
    </div>
    
    <!-- Card 3: Rata-Rata Transaksi -->
    <div class="laporan-card">
        <div class="laporan-card-icon">
            <i class="fa-solid fa-calculator" style="font-size: 1.35rem; color: #a48c71;"></i>
        </div>
        <div class="laporan-card-info">
            <span class="laporan-card-title">Rata-Rata Transaksi</span>
            <h2 class="laporan-card-value"><?php echo rupiah($rataRataTx); ?></h2>
        </div>
    </div>
</div>

<!-- ===== COMPACT FILTER BAR ===== -->
<div class="panel" style="margin-bottom: 25px;">
    <div class="panel-body laporan-top-layout-row" style="padding: 12px 20px; display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
        
        <!-- Filter Form -->
        <form action="admin.php" method="GET" onsubmit="return validateLaporanFilter()" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 0; width: 100%; justify-content: space-between;">
            <input type="hidden" name="page" value="laporan">
            
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-dark);"><i class="fa-solid fa-calendar-days" style="margin-right: 5px; color: var(--accent);"></i> Periode:</span>
                <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($startDate); ?>" required style="font-size: 0.85rem; padding: 6px 12px; height: 36px; border: 1px solid var(--border-color); border-radius: 8px; background: #fff; width: 150px; color: var(--text-dark);">
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">s/d</span>
                <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($endDate); ?>" required style="font-size: 0.85rem; padding: 6px 12px; height: 36px; border: 1px solid var(--border-color); border-radius: 8px; background: #fff; width: 150px; color: var(--text-dark);">
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-spa" style="padding: 0 18px; height: 36px; font-size: 0.85rem; display: flex; align-items: center; gap: 6px; border-radius: 8px; font-weight: 600; cursor: pointer; background: #f3ebe1; border: 1px solid var(--border-color); color: var(--text-dark); transition: var(--transition);">
                    <i class="fa-solid fa-filter" style="color: var(--text-dark);"></i> Filter
                </button>
                <a href="admin.php?page=laporan&action=cetak&start_date=<?php echo urlencode($startDate); ?>&end_date=<?php echo urlencode($endDate); ?>"
                   target="_blank" class="btn-spa btn-spa-accent" style="padding: 0 18px; height: 36px; font-size: 0.85rem; display: flex; align-items: center; gap: 6px; text-decoration: none; border-radius: 8px; font-weight: 600; cursor: pointer; background: var(--primary); color: #fff; transition: var(--transition);">
                    <i class="fa-solid fa-print"></i> Cetak (A4)
                </a>
            </div>
        </form>

    </div>
</div>

<!-- ===== POPUP VALIDASI ===== -->
<div id="laporanPopup" style="display: none; position: fixed; inset: 0; z-index: 1200; background: rgba(34, 29, 27, 0.42); align-items: center; justify-content: center; padding: 20px;">
    <div style="width: min(400px, 100%); background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); overflow: hidden;">
        <div style="padding: 14px 18px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
            <span style="width: 30px; height: 30px; border-radius: 8px; background: var(--danger-bg); color: var(--danger); display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fa-solid fa-circle-exclamation"></i>
            </span>
            <strong style="color: var(--primary); font-size: 0.95rem;">Filter tidak valid</strong>
        </div>
        <div style="padding: 16px 18px; color: var(--text-dark); font-size: 0.88rem; line-height: 1.55;">
            <p id="laporanPopupMessage" style="margin: 0;"></p>
        </div>
        <div style="padding: 0 18px 16px; display: flex; justify-content: flex-end;">
            <button type="button" class="btn-spa btn-spa-accent" onclick="closeLaporanPopup()" style="padding: 7px 16px; font-size: 0.88rem;">OK</button>
        </div>
    </div>
</div>

<!-- ===== TABEL RINCIAN ===== -->
<div class="panel">
    <div class="panel-header" style="border-bottom: 1px solid var(--border-color); padding: 18px 24px;">
        <h3 class="panel-title" style="display: flex; align-items: center; gap: 8px; margin: 0; font-size: 1rem; font-weight: 700;">
            <span style="width: 3px; height: 16px; background-color: var(--accent); display: inline-block; border-radius: 2px;"></span>
            <span>Rincian Transaksi</span>
            <span style="font-weight: 400; font-size: 0.82rem; color: var(--text-muted); margin-left: 4px;">
                (<?php echo date('d M Y', strtotime($startDate)); ?> – <?php echo date('d M Y', strtotime($endDate)); ?>)
            </span>
        </h3>
    </div>

    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="custom-table" data-admin-paginate data-per-page="8" data-noun="data">
                <thead>
                    <tr style="background-color: #faf7f2;">
                        <th style="width: 50px; text-align: center;">No</th>
                        <th>Kode</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Layanan SPA</th>
                        <th>Terapis</th>
                        <th>Metode</th>
                        <th style="text-align: left;">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($laporanList)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fa-solid fa-calendar-xmark mb-2 d-block fs-3"></i>
                                Tidak ditemukan transaksi pada rentang tanggal tersebut.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($laporanList as $row): ?>
                            <tr>
                                <td style="text-align: center; font-weight: 600; color: var(--text-muted); font-size: 0.85rem;"><?php echo $no++; ?></td>
                                <td>
                                    <strong style="font-family: monospace; font-size: 0.88rem; color: var(--accent-hover);">TX-<?php echo str_pad($row['idTransaksi'], 5, '0', STR_PAD_LEFT); ?></strong>
                                </td>
                                <td style="font-size: 0.88rem; font-weight: 500; color: var(--text-dark);"><?php echo htmlspecialchars($row['namaPelanggan'] ?? 'Walk-In'); ?></td>
                                <td style="font-size: 0.85rem; color: var(--text-dark); white-space: nowrap;"><?php echo date('d-m-Y H:i', strtotime($row['transactionDate'])); ?></td>
                                <td>
                                    <div style="font-size: 0.85rem; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($row['layananNames']); ?>">
                                        <?php echo htmlspecialchars($row['layananNames']); ?>
                                    </div>
                                </td>
                                <td style="font-size: 0.85rem; white-space: nowrap; color: var(--text-dark);">
                                    <?php if ($row['nama_terapis']): ?>
                                        <i class="fa-solid fa-user-doctor" style="color: var(--accent); margin-right: 8px; font-size: 0.82rem;"></i>
                                        <span><?php echo htmlspecialchars($row['nama_terapis']); ?></span>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted);">—</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 0.85rem; white-space: nowrap;">
                                    <span style="background: rgba(191, 161, 95, 0.06); color: var(--accent-hover); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; display: inline-block;">
                                        <?php echo htmlspecialchars(metodeLaporan($row)); ?>
                                    </span>
                                </td>
                                <td style="text-align: left; font-weight: 700; color: var(--text-dark); font-size: 0.9rem; white-space: nowrap;">
                                    <?php echo rupiah($row['totalPayment']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <tr data-pagination-summary style="background-color: rgba(191, 161, 95, 0.06); font-weight: 800; border-top: 2px solid var(--border-color);">
                            <td colspan="7" style="text-align: right; padding: 16px 24px !important; font-size: 0.88rem; color: var(--text-dark); letter-spacing: 0.5px; font-weight: 700;">TOTAL AKUMULASI:</td>
                            <td style="text-align: left; padding: 16px 16px !important; font-size: 1.05rem; color: var(--accent-hover); font-weight: 800;"><?php echo rupiah($totalPendapatan); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function showLaporanPopup(msg) {
    var popup = document.getElementById('laporanPopup');
    var label = document.getElementById('laporanPopupMessage');
    if (popup && label) {
        label.textContent = msg;
        popup.style.display = 'flex';
    }
}

function closeLaporanPopup() {
    var popup = document.getElementById('laporanPopup');
    if (popup) popup.style.display = 'none';
}

function validateLaporanFilter() {
    var startDate = document.getElementById('start_date').value;
    var endDate = document.getElementById('end_date').value;
    if (startDate && endDate && startDate > endDate) {
        showLaporanPopup('Tanggal awal tidak boleh lebih besar dari tanggal akhir.');
        return false;
    }
    return true;
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLaporanPopup();
});
document.addEventListener('click', function(e) {
    var popup = document.getElementById('laporanPopup');
    if (popup && e.target === popup) closeLaporanPopup();
});

function updateLaporanClock() {
    var label = document.getElementById('laporanLastUpdated');
    if (!label) return;
    var parts = new Intl.DateTimeFormat('id-ID', {
        timeZone: 'Asia/Jakarta',
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit', hourCycle: 'h23'
    }).formatToParts(new Date());
    var v = {};
    parts.forEach(function(p) { v[p.type] = p.value; });
    label.textContent = v.day + '-' + v.month + '-' + v.year + ' ' + v.hour + ':' + v.minute + ':' + v.second + ' WIB';
}

updateLaporanClock();
setInterval(updateLaporanClock, 1000);

setInterval(function() {
    var active = document.activeElement;
    var isEditing = active && (active.id === 'start_date' || active.id === 'end_date');
    if (!document.hidden && !isEditing) {
        window.location.reload();
    }
}, 30000);
</script>
