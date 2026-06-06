
<?php

if (!function_exists('rupiah')) {
    function rupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
}

$subtotal = $tx['totalPayment'];
$tax = 0;
?>

<div class="panel" style="max-width: 800px; margin: 0 auto 40px auto;">
    <div class="panel-header" style="background-color: var(--bg-light);">
        <h3 class="panel-title">Faktur Transaksi POS Kasir</h3>
        <div style="display: flex; gap: 10px;">
            <a href="admin.php?page=transaksi" class="btn-spa btn-spa-outline">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat
            </a>
            <a href="admin.php?page=transaksi&action=nota&id=<?php echo $tx['idTransaksi']; ?>" target="_blank" class="btn-spa btn-spa-accent">
                <i class="fa-solid fa-print"></i> Cetak Struk
            </a>
        </div>
    </div>
    
    <div class="panel-body" style="padding: 40px;">
        
        <div style="display: flex; justify-content: space-between; border-bottom: 2px solid var(--border-color); padding-bottom: 25px; margin-bottom: 30px;">
            <div>
                <h2 style="font-size: 1.8rem; font-weight: 800; color: var(--primary); font-family: var(--font-heading); margin-bottom: 5px;">SPADMIN SPA</h2>
                <p style="color: var(--text-muted); font-size: 0.85rem; line-height: 1.4;">
                    Spa Administration and Digitalization Center<br>
                    Jl. Sukajadi No. 102, Dago, Bandung<br>
                    Telepon: (022) 203-1245
                </p>
            </div>
            <div style="text-align: right;">
                <h4 style="font-family: var(--font-heading); font-size: 1.1rem; color: var(--accent-hover); font-weight: 700; margin-bottom: 8px;">KODE TRANSAKSI:</h4>
                <div style="font-family: monospace; font-size: 1.25rem; font-weight: 800; background: var(--bg-light); padding: 6px 12px; border-radius: 4px; border: 1px solid var(--border-color); color: var(--primary-light);">
                    TX-<?php echo str_pad($tx['idTransaksi'], 5, '0', STR_PAD_LEFT); ?>
                </div>
            </div>
        </div>
        
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 40px; font-size: 0.92rem;">
            <div>
                <h5 style="color: var(--text-muted); text-transform: uppercase; font-size: 0.78rem; letter-spacing: 1px; margin-bottom: 8px;">Diberikan Kepada:</h5>
                <strong style="color: var(--primary); font-size: 1.05rem;"><?php echo htmlspecialchars($tx['namaPelanggan'] ?? 'Pelanggan Walk-In'); ?></strong>
                <p style="color: var(--text-muted); margin-top: 5px; line-height: 1.4;">
                    No. Hp: <?php echo htmlspecialchars($tx['noHpPelanggan'] ?? '-'); ?><br>
                    Email: <?php echo htmlspecialchars($tx['email_pelanggan'] ?? '-'); ?><br>
                </p>
            </div>
            <div style="text-align: right;">
                <h5 style="color: var(--text-muted); text-transform: uppercase; font-size: 0.78rem; letter-spacing: 1px; margin-bottom: 8px;">Detail Faktur:</h5>
                <p style="color: var(--text-muted); line-height: 1.4;">
                    Tanggal Transaksi: <strong><?php echo date('d M Y H:i', strtotime($tx['transactionDate'])); ?> WIB</strong><br>
                    Kasir (Admin): <strong><?php echo htmlspecialchars($tx['namaAdmin'] ?? 'Admin SPADMIN'); ?></strong><br>
                    Metode Pembayaran: <strong><?php echo htmlspecialchars($tx['paymentMethod'] ?? 'Tunai'); ?></strong><br>
                    Status: <span class="badge badge-success" style="font-size: 0.75rem; padding: 3px 8px;">LUNAS</span>
                </p>
            </div>
        </div>
        
        
        <div style="margin-bottom: 40px;">
            <table class="custom-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="padding: 12px 10px;">Layanan SPA Dipesan</th>
                        <th style="width: 150px; text-align: center; padding: 12px 10px;">Durasi</th>
                        <th style="width: 200px; text-align: right; padding: 12px 10px;">Harga Satuan</th>
                        <th style="width: 200px; text-align: right; padding: 12px 10px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tx['details'] as $detail): ?>
                        <tr>
                            <td style="padding: 16px 10px;">
                                <strong><?php echo htmlspecialchars($detail['nama_layanan']); ?></strong>
                            </td>
                            <td style="text-align: center; padding: 16px 10px; font-weight: 500;"><?php echo $detail['durasi']; ?> Menit</td>
                            <td style="text-align: right; padding: 16px 10px;"><?php echo rupiah($detail['harga']); ?></td>
                            <td style="text-align: right; padding: 16px 10px; font-weight: 600; color: var(--primary);"><?php echo rupiah($detail['subtotal']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        
        <div style="display: flex; justify-content: flex-end;">
            <div style="width: 320px; font-size: 0.95rem;">
                <div style="display: flex; justify-content: space-between; padding: 12px 0; font-size: 1.2rem; color: var(--primary); border-bottom: 2px solid var(--border-color);">
                    <span style="font-weight: 700; font-family: var(--font-heading);">Total Pembayaran:</span>
                    <strong style="color: var(--accent-hover); font-weight: 800;"><?php echo rupiah($tx['totalPayment']); ?></strong>
                </div>
                
                <?php if (isset($tx['uang_bayar']) && $tx['uang_bayar'] > 0): ?>
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border-color);">
                        <span style="color: var(--text-muted);">Uang Bayar (<?php echo htmlspecialchars($tx['paymentMethod'] ?? 'Tunai'); ?>):</span>
                        <strong><?php echo rupiah($tx['uang_bayar']); ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                        <span style="color: var(--text-muted);">Kembalian:</span>
                        <strong style="color: var(--success); font-weight: 700;"><?php echo rupiah($tx['kembalian']); ?></strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        
        <div style="border-top: 1px solid var(--border-color); padding-top: 30px; margin-top: 40px; text-align: center; color: var(--text-muted); font-size: 0.85rem; font-style: italic;">
            "Terima kasih atas kunjungan Anda. Tubuh rileks, pikiran jernih, energi pulih."<br>
            SPADMIN SPA Administration System
        </div>
    </div>
</div>
