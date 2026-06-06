<?php

if (!function_exists('rupiah')) {
    function rupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
}
?>


<div class="admin-section-tabs">
    <a href="admin.php?page=transaksi" class="admin-section-tab active">
        <i class="fa-solid fa-list-check"></i>
        <span>Riwayat Transaksi</span>
    </a>
    <a href="admin.php?page=transaksi&action=create" class="admin-section-tab">
        <i class="fa-solid fa-cash-register"></i>
        <span>POS Walk-In</span>
    </a>
</div>

<!-- Search Bar -->
<div class="panel" style="margin-bottom: 20px;">
    <div class="panel-body" style="padding: 14px 20px;">
        <div style="position: relative; max-width: 420px;">
            <i class="fa-solid fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
            <input type="text" id="searchTransaksi" placeholder="Cari kode transaksi, pelanggan, atau layanan..."
                   style="width: 100%; padding: 9px 12px 9px 36px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); outline: none; background: var(--bg-light); color: var(--text-dark); font-size: 0.87rem; box-sizing: border-box;"
                   oninput="filterTransaksi(this.value)">
        </div>
    </div>
</div>


<div class="panel">
    <div class="panel-header">
        <h3 class="panel-title">Daftar Riwayat Transaksi Kasir</h3>
    </div>

    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="custom-table" data-admin-paginate data-per-page="6" data-noun="data">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">No</th>
                        <th>Kode Transaksi</th>
                        <th>Pelanggan</th>
                        <th>Layanan SPA</th>
                        <th>Tanggal Transaksi</th>
                        <th>Total Transaksi</th>
                        <th>Metode Bayar</th>
                        <th>Status</th>
                        <th>Kasir (Admin)</th>
                        <th style="width: 120px; text-align: center;">Struk</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transaksiList)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fa-solid fa-file-invoice-dollar mb-2 d-block fs-3"></i> Belum ada riwayat transaksi tersimpan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $no = 1;
                        foreach ($transaksiList as $tx):
                        ?>
                            <tr>
                                <td style="text-align: center; font-weight: 600; color: var(--text-muted);"><?php echo $no++; ?></td>
                                <td>
                                    <strong style="font-family: monospace; font-size: 0.95rem; color: var(--primary-light);">TX-<?php echo str_pad($tx['idTransaksi'], 5, '0', STR_PAD_LEFT); ?></strong>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($tx['namaPelanggan'] ?? 'Pelanggan Walk-In'); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($tx['noHpPelanggan'] ?? '-'); ?></small>
                                </td>
                                <td>
                                    <div style="font-size: 0.88rem; max-width: 220px; white-space: normal; word-break: break-word; line-height: 1.45;">
                                        <?php echo htmlspecialchars($tx['layananNames']); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php echo date('d M Y', strtotime($tx['transactionDate'])); ?><br>
                                    <small style="color: var(--text-muted);"><?php echo date('H:i', strtotime($tx['transactionDate'])); ?> WIB</small>
                                </td>
                                <td>
                                    <strong style="color: var(--accent-hover); font-size: 1.05rem;"><?php echo rupiah($tx['totalPayment']); ?></strong>
                                </td>
                                <td>
                                    <span style="font-weight: 500;"><i class="fa-solid fa-wallet text-muted mr-1" style="margin-right: 5px;"></i> <?php echo htmlspecialchars($tx['paymentMethod'] ?? 'Tunai'); ?></span>
                                </td>
                                <td>
                                    <?php if ($tx['statusPayment'] === 'Lunas' || ($tx['statusPayment'] === 'verified' && ($tx['reservation_type'] ?? '') === 'Walk-in') || empty($tx['statusPayment'])): ?>
                                        <span class="badge badge-success"><i class="fa-solid fa-circle-check" style="margin-right: 5px;"></i> Lunas</span>
                                    <?php elseif ($tx['statusPayment'] === 'verified' && ($tx['reservation_type'] ?? '') === 'online'): ?>
                                        <span class="badge badge-warning"><i class="fa-solid fa-hourglass-half" style="margin-right: 5px;"></i> DP Terbayar</span>
                                    <?php elseif ($tx['statusPayment'] === 'pending'): ?>
                                        <span class="badge badge-warning"><i class="fa-solid fa-hourglass-half" style="margin-right: 5px;"></i> Pending</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><i class="fa-solid fa-circle-xmark" style="margin-right: 5px;"></i> Gagal</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small style="color: var(--text-muted);"><i class="fa-solid fa-user-tie"></i> <?php echo htmlspecialchars($tx['namaAdmin'] ?? 'Admin SPADMIN'); ?></small>
                                </td>
                                <td style="text-align: center;">
                                    <a href="admin.php?page=transaksi&action=detail&id=<?php echo $tx['idTransaksi']; ?>" class="btn-spa btn-spa-outline" style="padding: 6px 12px; font-size: 0.8rem; gap: 5px;">
                                        <i class="fa-solid fa-receipt"></i> Detail
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
