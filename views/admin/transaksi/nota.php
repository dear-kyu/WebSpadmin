
<?php

if (!function_exists('rupiah')) {
    function rupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk SPADMIN - TX-<?php echo str_pad($tx['idTransaksi'], 5, '0', STR_PAD_LEFT); ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .nota-box {
            width: 58mm;
            padding: 2mm;
            margin: 0 auto;
            text-align: center;
        }
        .header {
            margin-bottom: 10px;
        }
        .brand {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .subtitle {
            font-size: 9px;
            color: #333;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .details-table {
            width: 100%;
            text-align: left;
            font-size: 9px;
            line-height: 1.3;
            margin-bottom: 10px;
        }
        .details-table td {
            vertical-align: top;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            text-align: left;
            margin-bottom: 10px;
        }
        .items-table th {
            border-bottom: 1px dashed #000;
            padding: 4px 0;
            font-weight: bold;
        }
        .items-table td {
            padding: 4px 0;
        }
        .totals-box {
            width: 100%;
            font-size: 9px;
            text-align: right;
            margin-top: 5px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }
        .total-pay {
            font-weight: bold;
            font-size: 11px;
            border-top: 1px dashed #000;
            padding-top: 4px;
            margin-top: 4px;
        }
        .footer {
            font-size: 10px;
            margin-top: 20px;
            font-style: italic;
        }
        
        @media print {
            body {
                background: none;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="nota-box">
        <div class="header">
            <span class="brand">SPADMIN SPA</span><br>
            <span class="subtitle">Jl. Sukajadi No. 102, Bandung</span><br>
            <span class="subtitle">Telp: (022) 203-1245</span>
        </div>
        
        <div class="divider"></div>
        
        <table class="details-table">
            <tr>
                <td style="width: 45%;">No Struk</td>
                <td>: TX-<?php echo str_pad($tx['idTransaksi'], 5, '0', STR_PAD_LEFT); ?></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: <?php echo date('d-m-Y H:i', strtotime($tx['transactionDate'])); ?> WIB</td>
            </tr>
            <tr>
                <td>Pelanggan</td>
                <td>: <?php echo htmlspecialchars($tx['namaPelanggan'] ?? 'Pelanggan Walk-In'); ?></td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>: <?php echo htmlspecialchars($tx['namaAdmin'] ?? 'Admin SPADMIN'); ?></td>
            </tr>
            <?php if ($tx['nama_terapis']): ?>
                <tr>
                    <td>Terapis</td>
                    <td>: <?php echo htmlspecialchars($tx['nama_terapis']); ?></td>
                </tr>
            <?php endif; ?>
        </table>
        
        <div class="divider"></div>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th>Layanan</th>
                    <th style="text-align: right; width: 35%;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tx['details'] as $detail): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($detail['nama_layanan']); ?><br>
                            <small>(<?php echo $detail['durasi']; ?> Menit)</small>
                        </td>
                        <td style="text-align: right; vertical-align: bottom;"><?php echo rupiah($detail['subtotal']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="divider"></div>
        
        <div class="totals-box">
            <div class="totals-row total-pay">
                <span>TOTAL:</span>
                <span><?php echo rupiah($tx['totalPayment']); ?></span>
            </div>
            
            <?php if (isset($tx['uang_bayar']) && $tx['uang_bayar'] > 0): ?>
                <div class="totals-row" style="margin-top: 5px;">
                    <span>BAYAR (<?php echo strtoupper(htmlspecialchars($tx['paymentMethod'] ?? 'CASH')); ?>):</span>
                    <span><?php echo rupiah($tx['uang_bayar']); ?></span>
                </div>
                <div class="totals-row">
                    <span>KEMBALIAN:</span>
                    <span><?php echo rupiah($tx['kembalian']); ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="divider"></div>
        
        <div style="font-size: 9px; text-align: left; line-height: 1.3;">
            <span>Metode Bayar: <strong><?php echo htmlspecialchars($tx['paymentMethod'] ?? 'Tunai'); ?></strong></span><br>
            <span>Status Bayar: <strong>LUNAS (PAID)</strong></span>
        </div>
        
        <div class="footer">
            "Terima kasih atas kunjungan Anda"<br>
            - SPADMIN SPA -
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
