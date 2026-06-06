
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
.laporan-stat-badge-container {
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--bg-light, #f8faf9);
    border: 1px solid var(--border-color);
    padding: 6px 14px;
    border-radius: var(--radius-sm);
    flex-wrap: wrap;
}
.laporan-stat-badge-item {
    display: flex;
    align-items: center;
    gap: 6px;
}
.laporan-stat-badge-label {
    font-size: 0.76rem;
    color: var(--text-muted);
    font-weight: 500;
    text-transform: capitalize;
}
.laporan-stat-badge-value {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--primary);
}
.laporan-badge-divider {
    width: 1px;
    height: 14px;
    background-color: var(--border-color);
}
@media (max-width: 900px) {
    .laporan-top-layout-row {
        flex-direction: column;
        align-items: stretch !important;
        gap: 12px !important;
    }
    .laporan-stat-badge-container {
        justify-content: space-between;
        width: 100%;
    }
}
</style>

<!-- ===== COMPACT HEADER & FILTER BAR ===== -->
<div class="panel" style="margin-bottom: 20px;">
    <div class="panel-body" style="padding: 10px 15px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;" class="laporan-top-layout-row">
        
        <!-- Left side: Filter Form -->
        <form action="admin.php" method="GET" onsubmit="return validateLaporanFilter()" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 0;">
            <input type="hidden" name="page" value="laporan">
            
            <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                <span style="font-size: 0.82rem; font-weight: 600; color: var(--text-dark);">Periode:</span>
                <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($startDate); ?>" required style="font-size: 0.82rem; padding: 4px 8px; height: 30px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: #fff; width: 130px;">
                <span style="font-size: 0.82rem; color: var(--text-muted);">s/d</span>
                <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($endDate); ?>" required style="font-size: 0.82rem; padding: 4px 8px; height: 30px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: #fff; width: 130px;">
            </div>

            <div style="display: flex; gap: 5px;">
                <button type="submit" class="btn-spa" style="padding: 0 12px; height: 30px; font-size: 0.78rem; display: flex; align-items: center; gap: 4px; border-radius: var(--radius-sm);">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                <a href="admin.php?page=laporan&action=cetak&start_date=<?php echo urlencode($startDate); ?>&end_date=<?php echo urlencode($endDate); ?>"
                   target="_blank" class="btn-spa btn-spa-accent" style="padding: 0 12px; height: 30px; font-size: 0.78rem; display: flex; align-items: center; gap: 4px; text-decoration: none; border-radius: var(--radius-sm);">
                    <i class="fa-solid fa-print"></i> Cetak (A4)
                </a>
            </div>
        </form>

        <!-- Right side: Stats Badges -->
        <div class="laporan-stat-badge-container">
            <div class="laporan-stat-badge-item">
                <span class="laporan-stat-badge-label">Pendapatan:</span>
                <strong class="laporan-stat-badge-value"><?php echo rupiah($totalPendapatan); ?></strong>
            </div>
            <div class="laporan-badge-divider"></div>
            <div class="laporan-stat-badge-item">
                <span class="laporan-stat-badge-label">Volume:</span>
                <strong class="laporan-stat-badge-value"><?php echo htmlspecialchars($totalTxCount); ?> Tx</strong>
            </div>
            <div class="laporan-badge-divider"></div>
            <div class="laporan-stat-badge-item">
                <span class="laporan-stat-badge-label">Rata-rata:</span>
                <strong class="laporan-stat-badge-value"><?php echo rupiah($rataRataTx); ?></strong>
            </div>
        </div>

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
    <div class="panel-header">
        <h3 class="panel-title">
            Rincian Transaksi
            <span style="font-weight: 400; font-size: 0.82rem; color: var(--text-muted); margin-left: 6px;">
                (<?php echo date('d M Y', strtotime($startDate)); ?> – <?php echo date('d M Y', strtotime($endDate)); ?>)
            </span>
        </h3>
    </div>

    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="custom-table" data-admin-paginate data-per-page="8" data-noun="data">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th>Kode</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Layanan SPA</th>
                        <th>Terapis</th>
                        <th>Metode</th>
                        <th style="text-align: right;">Total (Rp)</th>
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
                                    <strong style="font-family: monospace; font-size: 0.88rem;">TX-<?php echo str_pad($row['idTransaksi'], 5, '0', STR_PAD_LEFT); ?></strong>
                                </td>
                                <td style="font-size: 0.88rem;"><?php echo htmlspecialchars($row['namaPelanggan'] ?? 'Walk-In'); ?></td>
                                <td style="font-size: 0.83rem; white-space: nowrap;"><?php echo date('d-m-Y H:i', strtotime($row['transactionDate'])); ?></td>
                                <td>
                                    <div style="font-size: 0.83rem; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($row['layananNames']); ?>">
                                        <?php echo htmlspecialchars($row['layananNames']); ?>
                                    </div>
                                </td>
                                <td style="font-size: 0.83rem;">
                                    <?php if ($row['nama_terapis']): ?>
                                        <i class="fa-solid fa-user-doctor" style="color: var(--accent); font-size: 0.75rem;"></i>
                                        <?php echo htmlspecialchars($row['nama_terapis']); ?>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted);">—</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 0.83rem;"><?php echo htmlspecialchars(metodeLaporan($row)); ?></td>
                                <td style="text-align: right; font-weight: 700; color: var(--primary); font-size: 0.9rem;"><?php echo rupiah($row['totalPayment']); ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <tr data-pagination-summary style="background-color: var(--bg-light); font-weight: 800;">
                            <td colspan="7" style="text-align: right; padding: 14px 20px; font-size: 0.9rem; color: var(--primary);">TOTAL AKUMULASI:</td>
                            <td style="text-align: right; padding: 14px 20px; font-size: 1.05rem; color: var(--accent-hover);"><?php echo rupiah($totalPendapatan); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function showLaporanPopup(message) {
    var popup = document.getElementById('laporanPopup');
    var messageEl = document.getElementById('laporanPopupMessage');
    if (!popup || !messageEl) return;
    messageEl.textContent = message;
    popup.style.display = 'flex';
}

function closeLaporanPopup() {
    var popup = document.getElementById('laporanPopup');
    if (popup) popup.style.display = 'none';
    var startDate = document.getElementById('start_date');
    if (startDate) startDate.focus();
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
