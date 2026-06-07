

<div class="panel">
    <div class="panel-header">
        <h3 class="panel-title">Kelola Ruangan & Kapasitas SPA</h3>
    </div>
    
    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="custom-table" data-admin-paginate data-per-page="6" data-noun="data">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">No</th>
                        <th>Nama Ruangan</th>
                        <th>Status Aktivitas</th>
                        <th style="width: 250px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ruanganList)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fa-solid fa-door-closed mb-2 d-block fs-3"></i> Belum ada data ruangan terdaftar.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1;
                        foreach ($ruanganList as $r): 
                        ?>
                            <tr class="ruangan-row">
                                <td class="ruangan-no" style="text-align: center; font-weight: 600; color: var(--text-muted);"><?php echo $no++; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($r['nama_ruangan']); ?></strong>
                                </td>
                                <td>
                                    <?php if ($r['status'] === 'aktif'): ?>
                                        <span class="badge badge-success"><i class="fa-solid fa-circle-check" style="margin-right: 5px;"></i> Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><i class="fa-solid fa-circle-xmark" style="margin-right: 5px;"></i> Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <div class="btn-actions" style="justify-content: center; gap: 10px; flex-wrap: nowrap;">
                                        <a href="admin.php?page=ruangan&action=edit&id=<?php echo $r['id_ruangan']; ?>" class="btn-spa btn-spa-outline" style="padding: 6px 12px; font-size: 0.8rem; gap: 5px; white-space: nowrap;">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit Nama
                                        </a>
                                        <?php if ($r['status'] === 'aktif'): ?>
                                            <a href="admin.php?page=ruangan&action=toggle&id=<?php echo $r['id_ruangan']; ?>" class="btn-spa btn-spa-outline" style="padding: 6px 12px; font-size: 0.8rem; gap: 5px; color: var(--danger); border-color: rgba(211, 47, 47, 0.2); white-space: nowrap;">
                                                <i class="fa-solid fa-power-off"></i> Nonaktifkan
                                            </a>
                                        <?php else: ?>
                                            <a href="admin.php?page=ruangan&action=toggle&id=<?php echo $r['id_ruangan']; ?>" class="btn-spa btn-spa-accent" style="padding: 6px 12px; font-size: 0.8rem; gap: 5px; white-space: nowrap;">
                                                <i class="fa-solid fa-circle-check"></i> Aktifkan
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
