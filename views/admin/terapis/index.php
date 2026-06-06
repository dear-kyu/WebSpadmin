<!-- Search Bar -->
<div class="panel" style="margin-bottom: 20px;">
    <div class="panel-body" style="padding: 14px 20px;">
        <div style="position: relative; max-width: 420px;">
            <i class="fa-solid fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
            <input type="text" id="searchTerapis" placeholder="Cari nama terapis, spesialisasi, atau no. telp..."
                   style="width: 100%; padding: 9px 12px 9px 36px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); outline: none; background: var(--bg-light); color: var(--text-dark); font-size: 0.87rem; box-sizing: border-box;"
                   oninput="filterTerapis(this.value)">
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <h3 class="panel-title">Daftar Terapis SPA</h3>
        <a href="admin.php?page=terapis&action=create" class="btn-spa btn-spa-accent">
            <i class="fa-solid fa-user-plus"></i> Tambah Terapis Baru
        </a>
    </div>
    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="custom-table" data-admin-paginate data-per-page="6" data-noun="data">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">No</th>
                        <th>Nama Terapis</th>
                        <th style="width: 150px;">Jenis Kelamin</th>
                        <th>Keahlian / Spesialisasi</th>
                        <th>Nomor Telepon</th>
                        <th>Status</th>
                        <th style="width: 150px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($terapisList)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fa-solid fa-user-doctor mb-2 d-block fs-3"></i> Belum ada data terapis terdaftar.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1; 
                        foreach ($terapisList as $t): 
                            $idTerapis = $t['idTerapis'] ?? $t['id_terapis'];
                            $namaTerapis = $t['namaTerapis'] ?? $t['nama_terapis'];
                            $noTelp = $t['noTelp'] ?? $t['no_telp'];
                        ?>
                            <tr>
                                <td style="text-align: center; font-weight: 600; color: var(--text-muted);"><?php echo $no++; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($namaTerapis); ?></strong>
                                </td>
                                <td>
                                    <?php 
                                    $jk = $t['jenisKelamin'] ?? $t['jenis_kelamin'] ?? 'Perempuan';
                                    if ($jk === 'Laki-Laki' || $jk === 'Laki-laki' || $jk === 'Pria'): 
                                    ?>
                                        <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; background-color: #eff6ff; color: #2563eb; border: 1px solid rgba(59, 130, 246, 0.25);">
                                            Laki-Laki
                                        </span>
                                    <?php else: ?>
                                        <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; background-color: #fdf2f8; color: #db2777; border: 1px solid rgba(236, 72, 153, 0.25);">
                                            Perempuan
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="background-color: var(--bg-light); border: 1px solid var(--border-color); padding: 4px 10px; border-radius: 4px; font-size: 0.85rem; color: var(--primary-light);">
                                        <?php echo htmlspecialchars($t['spesialisasi']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="tel:<?php echo htmlspecialchars($noTelp); ?>" style="color: var(--text-dark);">
                                        <i class="fa-solid fa-phone-flip text-muted mr-1" style="font-size: 0.85rem; margin-right: 5px;"></i> <?php echo htmlspecialchars($noTelp); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if (strtolower($t['status'] ?? '') === 'aktif'): ?>
                                        <?php if ((int)($t['is_busy'] ?? 0) > 0): ?>
                                            <span class="badge badge-info"><i class="fa-solid fa-hourglass-start" style="margin-right: 5px;"></i> Sedang Bertugas</span>
                                        <?php else: ?>
                                            <span class="badge badge-success"><i class="fa-solid fa-circle-check" style="margin-right: 5px;"></i> Tersedia</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><i class="fa-solid fa-circle-xmark" style="margin-right: 5px;"></i> Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <div class="btn-actions" style="justify-content: center;">
                                        <a href="admin.php?page=terapis&action=edit&id=<?php echo $idTerapis; ?>" class="btn-icon" title="Ubah Data">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="admin.php?page=terapis&action=delete&id=<?php echo $idTerapis; ?>" 
                                           class="btn-icon btn-icon-danger btn-confirm-delete" 
                                           data-message="Apakah Anda yakin ingin menghapus data terapis: <?php echo htmlspecialchars($namaTerapis); ?>? Hapus terapis dapat mempengaruhi data reservasi."
                                           title="Hapus Data">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
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
