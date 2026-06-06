<style>
.interval-radio-label:hover {
    border-color: var(--accent) !important;
    background-color: var(--accent-light, #f4f7f6) !important;
}
.time-input:focus {
    border-color: var(--accent) !important;
    outline: none;
    box-shadow: 0 0 0 3px rgba(188, 208, 201, 0.25);
}
</style>

<?php if (!empty($success)): ?>
    <div style="background-color: var(--success-bg); border: 1px solid var(--success); color: var(--success); padding: 15px; border-radius: var(--radius-sm); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div style="background-color: var(--danger-bg); border: 1px solid var(--danger); color: var(--danger); padding: 15px; border-radius: var(--radius-sm); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="panel" style="margin-bottom: 30px;">
    <div class="panel-header">
        <h3 class="panel-title">Pengaturan Tampilan Halaman Utama</h3>
    </div>
    
    <div class="panel-body">
        <form action="admin.php?page=pengaturan&action=update_tampilan" method="POST">
            <div class="form-group">
                <label for="featured_section_eyebrow">Teks Eyebrow Bagian Unggulan</label>
                <input type="text" id="featured_section_eyebrow" name="featured_section_eyebrow" class="form-control" 
                       value="<?php echo htmlspecialchars($settings['featured_section_eyebrow'] ?? 'Our Best Value'); ?>" required autocomplete="off">
            </div>

            <div class="form-group">
                <label for="featured_section_title">Judul Utama Bagian Unggulan</label>
                <input type="text" id="featured_section_title" name="featured_section_title" class="form-control" 
                       value="<?php echo htmlspecialchars($settings['featured_section_title'] ?? 'Combo Packages'); ?>" required autocomplete="off">
            </div>

            <div class="form-group">
                <label for="featured_section_subtitle">Sub-Judul Deskripsi Bagian Unggulan</label>
                <textarea id="featured_section_subtitle" name="featured_section_subtitle" class="form-control" style="min-height: 80px;" required><?php echo htmlspecialchars($settings['featured_section_subtitle'] ?? ''); ?></textarea>
            </div>

            <div class="form-group" style="margin-bottom: 25px;">
                <label for="featured_section_category">Kategori Layanan yang Ditampilkan</label>
                <select id="featured_section_category" name="featured_section_category" class="form-control" required>
                    <?php 
                    $selectedCategory = $settings['featured_section_category'] ?? 'Combo Paket';
                    foreach ($categories as $cat): 
                    ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $selectedCategory === $cat ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text" style="color: var(--text-muted); font-size: 0.8rem; margin-top: 4px; display: block;">
                    Pilih kategori layanan SPA yang ingin Anda tampilkan pada bagian unggulan di beranda pelanggan.
                </small>
            </div>
            
            <div class="form-actions" style="border-top: 1px solid var(--border-color); padding-top: 20px;">
                <button type="submit" class="btn-spa btn-spa-accent">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Tampilan
                </button>
            </div>
        </form>
    </div>
</div>

<div class="panel" style="margin-bottom: 30px;">
    <div class="panel-header">
        <h3 class="panel-title">Pengaturan Slot Reservasi</h3>
    </div>
    
    <div class="panel-body">
        <form action="admin.php?page=pengaturan&action=update_sesi" method="POST">
            
            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-weight: 600; font-size: 0.95rem; color: var(--primary); display: block; margin-bottom: 12px; letter-spacing: 0.5px;">Interval Reservasi</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px;">
                    <?php 
                    $currInterval = $settings['interval_reservasi'] ?? '30';
                    foreach (['30', '45', '60', '90'] as $min): 
                    ?>
                        <label class="interval-radio-label" style="display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: 600; cursor: pointer; font-size: 0.9rem; padding: 10px 16px; border: 2px solid <?php echo $currInterval === $min ? 'var(--accent)' : 'var(--border-color)'; ?>; border-radius: var(--radius-sm); background-color: <?php echo $currInterval === $min ? 'var(--accent-light, #f4f7f6)' : '#fff'; ?>; color: <?php echo $currInterval === $min ? 'var(--primary)' : 'var(--text-main)'; ?>; transition: all 0.2s ease; text-align: center;">
                            <input type="radio" name="interval_reservasi" value="<?php echo $min; ?>" <?php echo $currInterval === $min ? 'checked' : ''; ?> style="accent-color: var(--accent); cursor: pointer; width: 16px; height: 16px;">
                            <span><?php echo $min; ?> Menit</span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="text-align: center; color: var(--border-color); font-weight: bold; margin: 8px 0; letter-spacing: 2px; font-size: 1.1rem; user-select: none;">━━━━━━━━━━━━━━━━</div>

            <div style="margin-bottom: 15px;">
                <h4 style="font-family: var(--font-heading); color: var(--primary); font-size: 1.1rem; font-weight: 700; margin: 0 0 10px 0; display: flex; align-items: center; gap: 8px;">
                    ☀️ Sesi Pagi
                </h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 10px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="sesi_pagi_mulai" style="font-size: 0.88rem; color: var(--text-muted); font-weight: 500; display: block; margin-bottom: 6px;">Mulai</label>
                        <input type="time" id="sesi_pagi_mulai" name="sesi_pagi_mulai" class="form-control time-input" 
                               value="<?php echo htmlspecialchars($settings['sesi_pagi_mulai'] ?? '09:00'); ?>" required style="height: 48px; font-size: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); padding: 10px 16px; width: 100%;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="sesi_pagi_selesai" style="font-size: 0.88rem; color: var(--text-muted); font-weight: 500; display: block; margin-bottom: 6px;">Selesai</label>
                        <input type="time" id="sesi_pagi_selesai" name="sesi_pagi_selesai" class="form-control time-input" 
                               value="<?php echo htmlspecialchars($settings['sesi_pagi_selesai'] ?? '11:30'); ?>" required style="height: 48px; font-size: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); padding: 10px 16px; width: 100%;">
                    </div>
                </div>
                
                <div style="background-color: var(--bg-light, #f9fbfb); border-radius: var(--radius-sm); padding: 10px 16px; border: 1px dashed var(--border-color);">
                    <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Preview:</div>
                    <div id="preview_sesi_pagi" style="font-family: monospace; font-size: 0.95rem; color: var(--primary); font-weight: 600; line-height: 1.6; word-break: break-all;"></div>
                </div>
            </div>

            <div style="text-align: center; color: var(--border-color); font-weight: bold; margin: 8px 0; letter-spacing: 2px; font-size: 1.1rem; user-select: none;">━━━━━━━━━━━━━━━━</div>

            <div style="margin-bottom: 15px;">
                <h4 style="font-family: var(--font-heading); color: var(--primary); font-size: 1.1rem; font-weight: 700; margin: 0 0 10px 0; display: flex; align-items: center; gap: 8px;">
                    🌤️ Sesi Siang
                </h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 10px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="sesi_siang_mulai" style="font-size: 0.88rem; color: var(--text-muted); font-weight: 500; display: block; margin-bottom: 6px;">Mulai</label>
                        <input type="time" id="sesi_siang_mulai" name="sesi_siang_mulai" class="form-control time-input" 
                               value="<?php echo htmlspecialchars($settings['sesi_siang_mulai'] ?? '12:00'); ?>" required style="height: 48px; font-size: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); padding: 10px 16px; width: 100%;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="sesi_siang_selesai" style="font-size: 0.88rem; color: var(--text-muted); font-weight: 500; display: block; margin-bottom: 6px;">Selesai</label>
                        <input type="time" id="sesi_siang_selesai" name="sesi_siang_selesai" class="form-control time-input" 
                               value="<?php echo htmlspecialchars($settings['sesi_siang_selesai'] ?? '16:30'); ?>" required style="height: 48px; font-size: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); padding: 10px 16px; width: 100%;">
                    </div>
                </div>
                
                <div style="background-color: var(--bg-light, #f9fbfb); border-radius: var(--radius-sm); padding: 10px 16px; border: 1px dashed var(--border-color);">
                    <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Preview:</div>
                    <div id="preview_sesi_siang" style="font-family: monospace; font-size: 0.95rem; color: var(--primary); font-weight: 600; line-height: 1.6; word-break: break-all;"></div>
                </div>
            </div>

            <div style="text-align: center; color: var(--border-color); font-weight: bold; margin: 8px 0; letter-spacing: 2px; font-size: 1.1rem; user-select: none;">━━━━━━━━━━━━━━━━</div>

            <div style="margin-bottom: 15px;">
                <h4 style="font-family: var(--font-heading); color: var(--primary); font-size: 1.1rem; font-weight: 700; margin: 0 0 10px 0; display: flex; align-items: center; gap: 8px;">
                    🌙 Sesi Sore/Malam
                </h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 10px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="sesi_sore_mulai" style="font-size: 0.88rem; color: var(--text-muted); font-weight: 500; display: block; margin-bottom: 6px;">Mulai</label>
                        <input type="time" id="sesi_sore_mulai" name="sesi_sore_mulai" class="form-control time-input" 
                               value="<?php echo htmlspecialchars($settings['sesi_sore_mulai'] ?? '17:00'); ?>" required style="height: 48px; font-size: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); padding: 10px 16px; width: 100%;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="sesi_sore_selesai" style="font-size: 0.88rem; color: var(--text-muted); font-weight: 500; display: block; margin-bottom: 6px;">Selesai</label>
                        <input type="time" id="sesi_sore_selesai" name="sesi_sore_selesai" class="form-control time-input" 
                               value="<?php echo htmlspecialchars($settings['sesi_sore_selesai'] ?? '20:00'); ?>" required style="height: 48px; font-size: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); padding: 10px 16px; width: 100%;">
                    </div>
                </div>
                
                <div style="background-color: var(--bg-light, #f9fbfb); border-radius: var(--radius-sm); padding: 10px 16px; border: 1px dashed var(--border-color);">
                    <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Preview:</div>
                    <div id="preview_sesi_sore" style="font-family: monospace; font-size: 0.95rem; color: var(--primary); font-weight: 600; line-height: 1.6; word-break: break-all;"></div>
                </div>
            </div>
            
            <div class="form-actions" style="border-top: 1px solid var(--border-color); padding-top: 20px;">
                <button type="submit" class="btn-spa btn-spa-accent">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Konfigurasi Sesi
                </button>
            </div>
        </form>
    </div>
</div>

<div class="panel" id="metode-transfer" style="margin-bottom: 30px;">
    <div class="panel-header">
        <h3 class="panel-title">Metode Transfer Pembayaran</h3>
        <span class="admin-panel-hint">Rekening ini tampil di halaman pembayaran pelanggan.</span>
    </div>
    <div class="panel-body">
        <div class="settings-payment-grid">
            <div>
                <h4 class="settings-subtitle">Metode Aktif</h4>
                <div class="table-responsive">
                    <table class="custom-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 60px; text-align: center;">No</th>
                                <th>Bank / E-Wallet</th>
                                <th>Nomor Rekening / HP</th>
                                <th>Atas Nama</th>
                                <th style="width: 90px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rekeningList)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-muted);">
                                        <i class="fa-solid fa-credit-card mb-2 d-block fs-3"></i> Belum ada metode transfer.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($rekeningList as $r): ?>
                                    <tr>
                                        <td style="text-align: center; font-weight: 600; color: var(--text-muted);"><?php echo $no++; ?></td>
                                        <td><strong style="color: var(--primary);"><?php echo htmlspecialchars($r['nama_bank']); ?></strong></td>
                                        <td><strong style="font-family: monospace; color: var(--text-dark);"><?php echo htmlspecialchars($r['nomor_rekening']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($r['atas_nama']); ?></td>
                                        <td style="text-align: center;">
                                            <a href="admin.php?page=pengaturan&action=hapus_rekening&id=<?php echo (int) $r['id_rekening']; ?>"
                                               class="btn-spa btn-spa-outline"
                                               style="border-color: var(--danger); color: var(--danger); padding: 6px 10px; font-size: 0.8rem;"
                                               onclick="return confirm('Hapus metode transfer ini?');">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="settings-payment-form">
                <h4 class="settings-subtitle">Tambah Metode Baru</h4>
                <form action="admin.php?page=pengaturan&action=tambah_rekening" method="POST">
                    <div class="form-group">
                        <label for="nama_bank">Nama Bank / E-Wallet</label>
                        <input type="text" id="nama_bank" name="nama_bank" class="form-control" placeholder="Contoh: BCA, MANDIRI, GoPay" required>
                    </div>
                    <div class="form-group">
                        <label for="nomor_rekening">Nomor Rekening / Nomor HP</label>
                        <input type="text" id="nomor_rekening" name="nomor_rekening" class="form-control" placeholder="Contoh: 1234567890" required>
                    </div>
                    <div class="form-group">
                        <label for="atas_nama">Atas Nama</label>
                        <input type="text" id="atas_nama" name="atas_nama" class="form-control" placeholder="Contoh: A.N. SPADMIN SPA" required>
                    </div>
                    <button type="submit" class="btn-spa btn-spa-accent" style="width: 100%; justify-content: center;">
                        <i class="fa-solid fa-plus"></i> Simpan Metode Transfer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const radioIntervals = document.querySelectorAll('input[name="interval_reservasi"]');
    const pagiMulai = document.getElementById('sesi_pagi_mulai');
    const pagiSelesai = document.getElementById('sesi_pagi_selesai');
    const siangMulai = document.getElementById('sesi_siang_mulai');
    const siangSelesai = document.getElementById('sesi_siang_selesai');
    const soreMulai = document.getElementById('sesi_sore_mulai');
    const soreSelesai = document.getElementById('sesi_sore_selesai');

    const previewPagi = document.getElementById('preview_sesi_pagi');
    const previewSiang = document.getElementById('preview_sesi_siang');
    const previewSore = document.getElementById('preview_sesi_sore');

    function getSelectedInterval() {
        let val = 30;
        radioIntervals.forEach(radio => {
            if (radio.checked) {
                val = parseInt(radio.value);
            }
        });
        return val;
    }

    function timeToMinutes(timeStr) {
        if (!timeStr) return null;
        const parts = timeStr.split(':');
        if (parts.length < 2) return null;
        return parseInt(parts[0]) * 60 + parseInt(parts[1]);
    }

    function minutesToTime(totalMin) {
        const h = Math.floor(totalMin / 60);
        const m = totalMin % 60;
        const hStr = h.toString().padStart(2, '0');
        const mStr = m.toString().padStart(2, '0');
        return `${hStr}:${mStr}`;
    }

    function generatePreview(mulaiVal, selesaiVal, interval) {
        const startMin = timeToMinutes(mulaiVal);
        const endMin = timeToMinutes(selesaiVal);
        
        if (startMin === null || endMin === null || startMin > endMin || interval <= 0) {
            return '<span style="color: var(--danger); font-size: 0.85rem;">Format jam atau rentang waktu tidak valid</span>';
        }

        const times = [];
        let curr = startMin;
        while (curr <= endMin) {
            times.push(minutesToTime(curr));
            curr += interval;
        }

        if (times.length === 0) {
            return '-';
        }

        return times.join(' | ');
    }

    function updateAllPreviews() {
        const interval = getSelectedInterval();
        
        previewPagi.innerHTML = generatePreview(pagiMulai.value, pagiSelesai.value, interval);
        previewSiang.innerHTML = generatePreview(siangMulai.value, siangSelesai.value, interval);
        previewSore.innerHTML = generatePreview(soreMulai.value, soreSelesai.value, interval);
    }

    [pagiMulai, pagiSelesai, siangMulai, siangSelesai, soreMulai, soreSelesai].forEach(input => {
        input.addEventListener('input', updateAllPreviews);
    });

    radioIntervals.forEach(radio => {
        radio.addEventListener('change', function() {
            radioIntervals.forEach(r => {
                const label = r.closest('label');
                if (label) {
                    if (r.checked) {
                        label.style.borderColor = 'var(--accent)';
                        label.style.backgroundColor = 'var(--accent-light, #f4f7f6)';
                        label.style.color = 'var(--primary)';
                    } else {
                        label.style.borderColor = 'var(--border-color)';
                        label.style.backgroundColor = '#fff';
                        label.style.color = 'var(--text-main)';
                    }
                }
            });
            updateAllPreviews();
        });
    });

    updateAllPreviews();
});
</script>
