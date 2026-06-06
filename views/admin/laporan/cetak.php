
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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Keuangan SPADMIN - Periode <?php echo htmlspecialchars($startDate); ?> s/d <?php echo htmlspecialchars($endDate); ?></title>
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .kop-brand {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 1px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .kop-subtitle {
            font-size: 11px;
            color: #444;
            margin: 0 0 3px 0;
            line-height: 1.4;
        }
        .kop-meta {
            font-size: 10px;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 5px;
        }
        .laporan-title {
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 25px;
            text-decoration: underline;
        }
        .meta-periode {
            margin-bottom: 20px;
            font-size: 12px;
        }
        .meta-periode table {
            width: 100%;
        }
        .meta-periode td {
            padding: 3px 0;
        }
        
        .rekap-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-collapse: separate;
            border-spacing: 15px 0;
        }
        .rekap-card {
            display: table-cell;
            width: 33.33%;
            border: 1px solid #000;
            padding: 15px;
            background-color: #fcfcfc;
            text-align: center;
            border-radius: 4px;
        }
        .rekap-card span {
            font-size: 10px;
            text-transform: uppercase;
            color: #555;
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .rekap-card h3 {
            font-size: 16px;
            margin: 0;
            font-weight: bold;
        }
        
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .report-table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 10px 8px;
            font-weight: bold;
            text-align: left;
            font-size: 11px;
        }
        .report-table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 11px;
            vertical-align: middle;
        }
        .report-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        .signature-section {
            width: 100%;
            margin-top: 50px;
            font-size: 12px;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature-space {
            height: 70px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    
    <div class="kop-surat">
        <h1 class="kop-brand">SPADMIN SPA</h1>
        <p class="kop-subtitle">SPA Administration and Digitalization Center</p>
        <p class="kop-subtitle">Jl. Sukajadi No. 102, Bandung &bull; Telp: (022) 203-1245 &bull; Email: support@spadmin.com</p>
    </div>
    
    <div class="laporan-title">Laporan Keuangan & Penjualan SPA</div>
    
    
    <div class="meta-periode">
        <table style="width: 100%;">
            <tr>
                <td style="width: 15%; font-weight: bold;">Periode Laporan</td>
                <td style="width: 35%;">: <?php echo date('d F Y', strtotime($startDate)); ?> s/d <?php echo date('d F Y', strtotime($endDate)); ?></td>
                <td style="width: 15%; font-weight: bold; text-align: right;">Dicetak Oleh</td>
                <td style="width: 35%;">: Administrator (SPADMIN)</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Tanggal Cetak</td>
                <td>: <?php echo date('d-m-Y H:i'); ?> WIB</td>
                <td style="font-weight: bold; text-align: right;">Status</td>
                <td>: Laporan Sah & Terverifikasi</td>
            </tr>
        </table>
    </div>
    
    
    <div class="rekap-grid">
        <div class="rekap-card">
            <span>Total Pendapatan Bersih</span>
            <h3><?php echo rupiah($totalPendapatan); ?></h3>
        </div>
        <div class="rekap-card">
            <span>Volume Pembayaran</span>
            <h3><?php echo htmlspecialchars($totalTxCount); ?> Pembayaran Tercatat</h3>
        </div>
        <div class="rekap-card">
            <span>Rata-Rata per Pembayaran</span>
            <h3><?php echo rupiah($rataRataTx); ?></h3>
        </div>
    </div>
    
    
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 18%;">Kode Transaksi</th>
                <th>Nama Pelanggan</th>
                <th style="width: 18%;">Waktu Transaksi</th>
                <th>Layanan SPA</th>
                <th>Terapis</th>
                <th style="width: 12%;">Metode</th>
                <th style="width: 15%; text-align: right;">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($laporanList)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 25px;">
                        Tidak ada transaksi ditemukan pada periode filter ini.
                    </td>
                </tr>
            <?php else: ?>
                <?php 
                $no = 1; 
                foreach ($laporanList as $row): 
                ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $no++; ?></td>
                        <td style="font-family: monospace; font-weight: bold;">TX-<?php echo str_pad($row['idTransaksi'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($row['namaPelanggan'] ?? 'Pelanggan Walk-In'); ?></td>
                        <td><?php echo date('d-m-Y H:i', strtotime($row['transactionDate'])); ?> WIB</td>
                        <td>
                            <div style="font-size: 0.85rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?php echo htmlspecialchars($row['layananNames']); ?>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($row['nama_terapis'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars(metodeLaporan($row)); ?></td>
                        <td style="text-align: right; font-weight: bold;"><?php echo rupiah($row['totalPayment']); ?></td>
                    </tr>
                <?php endforeach; ?>
                
                <tr style="background-color: #f9f9f9; font-weight: bold; font-size: 12px;">
                    <td colspan="7" style="text-align: right; padding: 12px 10px; text-transform: uppercase;">Total Pendapatan Terakumulasi:</td>
                    <td style="text-align: right; padding: 12px 10px; font-size: 13px; border-top: 2px solid #000;"><?php echo rupiah($totalPendapatan); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    
    <div class="signature-section">
        <div class="signature-box">
            <p>Bandung, <?php echo date('d F Y'); ?></p>
            <p>Mengetahui dan Mengesahkan,</p>
            <p style="font-weight: bold; margin-bottom: 0;">Pimpinan SPADMIN SPA</p>
            <div class="signature-space"></div>
            <p class="signature-name">Diah Meutya Affifah</p>
            <p style="margin: 0; font-size: 10px; color: #555;">Pimpinan Operasional</p>
        </div>
        <div style="clear: both;"></div>
    </div>

    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
