
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


<?php if (!empty($error)): ?>
    <div style="background-color: var(--danger-bg); border: 1px solid var(--danger); color: var(--danger); padding: 15px; border-radius: var(--radius-sm); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h3 class="panel-title">Filter Laporan Penjualan & Transaksi</h3>
    </div>
    
    <div class="panel-body">
        <form action="admin.php" method="GET" onsubmit="return validateLaporanFilter()" style="display: flex; gap: 20px; align-items: flex-end; flex-wrap: wrap;">
            <input type="hidden" name="page" value="laporan">
            
            <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                <label for="start_date">Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($startDate); ?>" required>
            </div>
            
            <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                <label for="end_date">Tanggal Akhir</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($endDate); ?>" required>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-spa">
                    <i class="fa-solid fa-filter"></i> Terapkan Filter
                </button>
                <a href="admin.php?page=laporan&action=cetak&start_date=<?php echo urlencode($startDate); ?>&end_date=<?php echo urlencode($endDate); ?>" 
                   target="_blank" class="btn-spa btn-spa-accent">
                    <i class="fa-solid fa-print"></i> Cetak Laporan (A4)
                </a>
            </div>
        </form>

        <div style="margin-top: 12px; display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; color: var(--text-muted); font-size: 0.82rem;">
            <span>
                <i class="fa-solid fa-rotate" style="color: var(--accent); margin-right: 5px;"></i>
                Laporan otomatis diperbarui dari data transaksi terbaru.
            </span>
            <span id="laporanLastUpdated">
                Terakhir diperbarui: <?php echo date('d-m-Y H:i:s'); ?> WIB
            </span>
        </div>
        <div style="margin-top: 8px; color: var(--accent-hover); font-size: 0.82rem; font-weight: 600;">
            <i class="fa-solid fa-circle-info" style="margin-right: 5px;"></i>
            Seluruh pembayaran yang sudah masuk tercatat sebagai pendapatan kotor. Tidak ada fitur refund / pengembalian dana.
        </div>

        <div id="laporanPopup" style="display: none; position: fixed; inset: 0; z-index: 1200; background: rgba(34, 29, 27, 0.42); align-items: center; justify-content: center; padding: 20px;">
            <div style="width: min(430px, 100%); background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); overflow: hidden;">
                <div style="padding: 15px 18px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
                    <span style="width: 34px; height: 34px; border-radius: 8px; background: var(--danger-bg); color: var(--danger); display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </span>
                    <strong style="color: var(--primary); font-size: 0.98rem;">Filter laporan belum valid</strong>
                </div>
                <div style="padding: 18px; color: var(--text-dark); font-size: 0.9rem; line-height: 1.55;">
                    <p id="laporanPopupMessage" style="margin: 0;"></p>
                </div>
                <div style="padding: 0 18px 18px; display: flex; justify-content: flex-end;">
                    <button type="button" class="btn-spa btn-spa-accent" onclick="closeLaporanPopup()" style="padding: 8px 18px;">
                        OK
                    </button>
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
            if (popup) {
                popup.style.display = 'none';
            }

            var startDate = document.getElementById('start_date');
            if (startDate) {
                startDate.focus();
            }
        }

        function validateLaporanFilter() {
            var startDate = document.getElementById('start_date').value;
            var endDate = document.getElementById('end_date').value;
            if (startDate && endDate && startDate > endDate) {
                showLaporanPopup('Tanggal awal tidak boleh lebih besar dari tanggal akhir. Contoh yang benar: Mei 2026 -> Juni 2026.');
                return false;
            }
            return true;
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLaporanPopup();
            }
        });

        document.addEventListener('click', function(e) {
            var popup = document.getElementById('laporanPopup');
            if (popup && e.target === popup) {
                closeLaporanPopup();
            }
        });

        function updateLaporanClock() {
            var label = document.getElementById('laporanLastUpdated');
            if (!label) return;

            var parts = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hourCycle: 'h23'
            }).formatToParts(new Date());
            var values = {};
            parts.forEach(function(part) {
                values[part.type] = part.value;
            });

            label.textContent = 'Terakhir diperbarui: ' +
                values.day + '-' + values.month + '-' + values.year + ' ' +
                values.hour + ':' + values.minute + ':' + values.second + ' WIB';
        }

        updateLaporanClock();
        setInterval(updateLaporanClock, 1000);

        setInterval(function() {
            var active = document.activeElement;
            var isEditingFilter = active && (active.id === 'start_date' || active.id === 'end_date');
            if (!document.hidden && !isEditingFilter) {
                window.location.reload();
            }
        }, 30000);
        </script>
    </div>
</div>


<div class="stats-grid">
    <div class="stats-card">
        <div class="stats-info">
            <span>Total Pendapatan Kotor</span>
            <h2><?php echo rupiah($totalPendapatan); ?></h2>
        </div>
        <div class="stats-icon">
            <i class="fa-solid fa-sack-dollar"></i>
        </div>
    </div>
    
    <div class="stats-card">
        <div class="stats-info">
            <span>Volume Pembayaran</span>
            <h2><?php echo htmlspecialchars($totalTxCount); ?> Pembayaran Tercatat</h2>
        </div>
        <div class="stats-icon">
            <i class="fa-solid fa-file-signature"></i>
        </div>
    </div>
    
    <div class="stats-card">
        <div class="stats-info">
            <span>Rata-Rata per Pembayaran</span>
            <h2><?php echo rupiah($rataRataTx); ?></h2>
        </div>
        <div class="stats-icon">
            <i class="fa-solid fa-chart-line"></i>
        </div>
    </div>
</div>


<div class="panel">
    <div class="panel-header">
        <h3 class="panel-title">Rincian Pembayaran Terhitung (<?php echo date('d M Y', strtotime($startDate)); ?> s/d <?php echo date('d M Y', strtotime($endDate)); ?>)</h3>
    </div>
    
    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="custom-table" data-admin-paginate data-per-page="6" data-noun="data">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">No</th>
                        <th>Kode Transaksi</th>
                        <th>Pelanggan</th>
                        <th>Tanggal Transaksi</th>
                        <th>Layanan SPA</th>
                        <th>Terapis</th>
                        <th>Metode</th>
                        <th style="text-align: right;">Total Nilai (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($laporanList)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fa-solid fa-calendar-xmark mb-2 d-block fs-3"></i> Tidak ditemukan transaksi pada rentang tanggal tersebut.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1; 
                        foreach ($laporanList as $row): 
                        ?>
                            <tr>
                                <td style="text-align: center; font-weight: 600; color: var(--text-muted);"><?php echo $no++; ?></td>
                                <td>
                                    <strong style="font-family: monospace; font-size: 0.9rem;">TX-<?php echo str_pad($row['idTransaksi'], 5, '0', STR_PAD_LEFT); ?></strong>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['namaPelanggan'] ?? 'Pelanggan Walk-In'); ?></strong>
                                </td>
                                <td>
                                    <?php echo date('d-m-Y H:i', strtotime($row['transactionDate'])); ?> WIB
                                </td>
                                <td>
                                    <div style="font-size: 0.88rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($row['layananNames']); ?>">
                                        <?php echo htmlspecialchars($row['layananNames']); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($row['nama_terapis']): ?>
                                        <small><i class="fa-solid fa-user-doctor"></i> <?php echo htmlspecialchars($row['nama_terapis']); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-weight: 500; font-size: 0.85rem;"><?php echo htmlspecialchars(metodeLaporan($row)); ?></span>
                                </td>
                                <td style="text-align: right; font-weight: 700; color: var(--primary);"><?php echo rupiah($row['totalPayment']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <tr data-pagination-summary style="background-color: var(--bg-light); font-weight: 800;">
                            <td colspan="7" style="text-align: right; padding: 18px 20px; font-size: 1rem; color: var(--primary);">TOTAL AKUMULASI PENDAPATAN:</td>
                            <td style="text-align: right; padding: 18px 20px; font-size: 1.15rem; color: var(--accent-hover);"><?php echo rupiah($totalPendapatan); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
