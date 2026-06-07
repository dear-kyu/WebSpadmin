
<?php

if (!function_exists('rupiah')) {
    function rupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
}
?>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<div class="stats-grid">
    <a href="admin.php?page=transaksi" style="text-decoration: none; color: inherit;">
    <div class="stats-card" style="cursor: pointer;">
        <div class="stats-info">
            <span>Total Penjualan SPA</span>
            <h2><?php echo rupiah($stats['totalPendapatan']); ?></h2>
        </div>
        <div class="stats-icon">
            <i class="fa-solid fa-rupiah-sign"></i>
        </div>
    </div>
    </a>
    
    <a href="admin.php?page=reservasi" style="text-decoration: none; color: inherit;">
    <div class="stats-card" style="cursor: pointer;">
        <div class="stats-info">
            <span>Reservasi Hari Ini</span>
            <h2><?php echo htmlspecialchars($stats['totalReservasiHariIni']); ?></h2>
        </div>
        <div class="stats-icon">
            <i class="fa-solid fa-calendar-day"></i>
        </div>
    </div>
    </a>
    
    <a href="admin.php?page=layanan" style="text-decoration: none; color: inherit;">
    <div class="stats-card" style="cursor: pointer;">
        <div class="stats-info">
            <span>Total Layanan SPA</span>
            <h2><?php echo htmlspecialchars($stats['totalLayanan']); ?></h2>
        </div>
        <div class="stats-icon">
            <i class="fa-solid fa-spa"></i>
        </div>
    </div>
    </a>
    
    <a href="admin.php?page=terapis" style="text-decoration: none; color: inherit;">
    <div class="stats-card" style="cursor: pointer;">
        <div class="stats-info">
            <span>Terapis Aktif</span>
            <h2><?php echo htmlspecialchars($stats['totalTerapisAktif']); ?></h2>
        </div>
        <div class="stats-icon">
            <i class="fa-solid fa-user-doctor"></i>
        </div>
    </div>
    </a>
</div>


<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 40px;">
    

    <div>

        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Tren Pendapatan (7 Hari Terakhir)</h3>
            </div>
            <div class="panel-body">
                <div style="height: 300px; position: relative;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        

        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Reservasi Terbaru</h3>
                <a href="admin.php?page=reservasi" class="btn-spa btn-spa-outline" style="padding: 6px 12px; font-size: 0.8rem;">Lihat Semua</a>
            </div>
            <div class="panel-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Pelanggan</th>
                                <th style="width: 25%;">Layanan</th>
                                <th style="width: 25%;">Tanggal &amp; Waktu</th>
                                <th style="width: 15%;">Terapis</th>
                                <th style="width: 110px; text-align: center;">Status</th>
                                <th style="width: 80px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentReservations)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-muted);">
                                        Belum ada data reservasi masuk.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentReservations as $res): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($res['namaPelanggan'] ?? $res['namaPelanggan']); ?></strong>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.88rem; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 600; display: inline-block; vertical-align: middle;" title="<?php echo htmlspecialchars($res['layananNames'] ?? $res['layananNames']); ?>">
                                                <?php echo htmlspecialchars($res['layananNames'] ?? $res['layananNames']); ?>
                                            </div>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <?php $resDate = $res['reservationDate'] ?? $res['reservation_date']; ?>
                                            <strong><?php echo date('d M Y', strtotime($resDate)); ?></strong><br>
                                            <span style="color: var(--text-muted); font-size: 0.8rem; display: inline-block; margin-top: 4px;"><i class="fa-regular fa-clock" style="margin-right: 3px;"></i> <?php echo date('H:i', strtotime($resDate)); ?> WIB</span>
                                        </td>
                                        <td>
                                            <?php $tName = $res['namaTerapis'] ?? $res['nama_terapis']; ?>
                                            <?php if ($tName): ?>
                                                <span style="font-weight: 600; color: var(--primary-light); white-space: nowrap;"><i class="fa-solid fa-user-doctor" style="color: var(--accent); margin-right: 4px;"></i> <?php echo htmlspecialchars($tName); ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary" style="font-size: 0.75rem; white-space: nowrap;"><i class="fa-solid fa-circle-question" style="margin-right: 4px;"></i> Belum Ditugaskan</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $statusClass = 'badge-secondary';
                                            $sVal = $res['statusReservation'] ?? $res['status_reservation'];
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
                                            <span class="badge <?php echo $statusClass; ?>" style="white-space: nowrap;"><?php echo htmlspecialchars($sVal); ?></span>
                                        </td>
                                        <td>
                                            <a href="admin.php?page=reservasi&action=detail&id=<?php echo $res['idReservasi'] ?? $res['id_reservasi']; ?>" class="btn-icon" title="Detail Reservasi">
                                                <i class="fa-solid fa-eye"></i>
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
    </div>
    

    <div>

        <div class="panel" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: var(--text-light); border: none;">
            <div class="panel-body" style="padding: 25px;">
                <h4 style="color: var(--accent); margin-bottom: 15px; font-weight: 700; font-family: var(--font-heading); margin-brand: uppercase; font-size: 1.1rem;">POS Walk-In Customer</h4>
                <p style="font-size: 0.88rem; color: rgba(255, 255, 255, 0.7); margin-bottom: 20px;">
                    Catat transaksi langsung pelanggan datang langsung (walk-in) dan cetak struk termal secara praktis.
                </p>
                <a href="admin.php?page=transaksi&action=create" class="btn-spa btn-spa-accent" style="width: 100%; justify-content: center;">
                    <i class="fa-solid fa-cash-register"></i> Buka Layar Kasir
                </a>
            </div>
        </div>
        

        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Terapis Aktif</h3>
            </div>
            <div class="panel-body" style="padding: 20px 25px;">
                <?php if (empty($activeTherapists)): ?>
                    <p class="text-center text-muted py-3">Tidak ada terapis aktif.</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <?php foreach ($activeTherapists as $therapist): ?>
                            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid var(--bg-light); gap: 10px;">
                                <div style="flex: 1; min-width: 0;">
                                    <h5 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 2px; color: var(--primary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo htmlspecialchars($therapist['namaTerapis'] ?? $therapist['nama_terapis']); ?></h5>
                                    <small style="color: var(--text-muted); font-size: 0.8rem; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($therapist['spesialisasi']); ?>">
                                        Keahlian: <?php echo htmlspecialchars($therapist['spesialisasi']); ?>
                                    </small>
                                </div>
                                <?php if ((int)($therapist['is_busy'] ?? 0) > 0): ?>
                                    <span class="badge badge-info" style="font-size: 0.7rem; padding: 4px 8px; flex-shrink: 0;">
                                        <i class="fa-solid fa-circle animate-pulse" style="font-size: 0.5rem; margin-right: 4px;"></i> Sibuk
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-success" style="font-size: 0.7rem; padding: 4px 8px; flex-shrink: 0;">
                                        <i class="fa-solid fa-circle" style="font-size: 0.5rem; margin-right: 4px;"></i> Siap
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    

    <?php
    $chartDates = [];
    $chartValues = [];
    

    $datesMap = [];
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i days"));
        $datesMap[$d] = 0;
    }
    
    $cData = $stats['chartData'] ?? $stats['chart_data'] ?? [];
    foreach ($cData as $row) {
        if (isset($datesMap[$row['tanggal']])) {
            $datesMap[$row['tanggal']] = (float)$row['total'];
        }
    }
    
    foreach ($datesMap as $date => $total) {
        $chartDates[] = date('d M', strtotime($date));
        $chartValues[] = $total;
    }
    ?>
    
    const dates = <?php echo json_encode($chartDates); ?>;
    const revenues = <?php echo json_encode($chartValues); ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Total Pendapatan (Rp)',
                data: revenues,
                borderColor: '#bfa15f',
                backgroundColor: 'rgba(191, 161, 95, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#1b352f',
                pointBorderColor: '#bfa15f',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        },
                        color: '#6e7572',
                        font: {
                            family: 'Inter'
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6e7572',
                        font: {
                            family: 'Inter'
                        }
                    }
                }
            }
        }
    });
});
</script>
