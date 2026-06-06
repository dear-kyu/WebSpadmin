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



<!-- ===== POPUP VALIDASI GLOBAL ===== -->
<div id="pengaturanPopupOverlay" style="display:none; position:fixed; inset:0; z-index:1500; background:rgba(30,25,22,0.45); align-items:center; justify-content:center; padding:20px;">
    <div style="width:min(420px,100%); background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--radius-md); box-shadow:0 8px 32px rgba(0,0,0,0.18); overflow:hidden; animation: popupFadeIn 0.18s ease;">
        <div style="padding:14px 18px; border-bottom:1px solid var(--border-color); display:flex; align-items:center; gap:10px;">
            <span id="pengaturanPopupIcon" style="width:32px; height:32px; border-radius:8px; background:var(--danger-bg); color:var(--danger); display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fa-solid fa-circle-exclamation"></i>
            </span>
            <strong id="pengaturanPopupTitle" style="color:var(--primary); font-size:0.95rem;">Peringatan</strong>
        </div>
        <div style="padding:16px 18px; color:var(--text-dark); font-size:0.9rem; line-height:1.6;">
            <p id="pengaturanPopupMsg" style="margin:0;"></p>
        </div>
        <div style="padding:0 18px 16px; display:flex; justify-content:flex-end;">
            <button type="button" class="btn-spa btn-spa-accent" id="pengaturanPopupOkBtn" onclick="closePengaturanPopup()" style="padding:8px 20px;">OK</button>
        </div>
    </div>
</div>

<!-- Popup Konfirmasi Hapus -->
<div id="pengaturanConfirmOverlay" style="display:none; position:fixed; inset:0; z-index:1500; background:rgba(30,25,22,0.45); align-items:center; justify-content:center; padding:20px;">
    <div style="width:min(400px,100%); background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--radius-md); box-shadow:0 8px 32px rgba(0,0,0,0.18); overflow:hidden;">
        <div style="padding:14px 18px; border-bottom:1px solid var(--border-color); display:flex; align-items:center; gap:10px;">
            <span style="width:32px; height:32px; border-radius:8px; background:var(--danger-bg); color:var(--danger); display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fa-solid fa-trash-can"></i>
            </span>
            <strong style="color:var(--primary); font-size:0.95rem;">Hapus Metode Transfer?</strong>
        </div>
        <div style="padding:16px 18px; color:var(--text-dark); font-size:0.9rem; line-height:1.6;">
            <p style="margin:0;">Tindakan ini akan menghapus metode transfer secara permanen. Apakah Anda yakin?</p>
        </div>
        <div style="padding:0 18px 16px; display:flex; justify-content:flex-end; gap:8px;">
            <button type="button" class="btn-spa btn-spa-outline" onclick="closePengaturanConfirm()">Batal</button>
            <a id="pengaturanConfirmHref" href="#" class="btn-spa" style="border-color:var(--danger);background:var(--danger);color:#fff;">
                <i class="fa-solid fa-trash-can"></i> Ya, Hapus
            </a>
        </div>
    </div>
</div>

<style>
@keyframes popupFadeIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
</style>

<div class="panel" style="margin-bottom: 30px;">
    <div class="panel-header">
        <h3 class="panel-title">Pengaturan Tampilan Halaman Utama</h3>
    </div>
    
    <div class="panel-body">
        <form action="admin.php?page=pengaturan&action=update_tampilan" method="POST">
            <div class="form-group">
                <label for="featured_section_eyebrow">Teks Eyebrow Bagian Unggulan <span class="req-star">*</span></label>
                <input type="text" id="featured_section_eyebrow" name="featured_section_eyebrow" class="form-control" 
                       value="<?php echo htmlspecialchars($settings['featured_section_eyebrow'] ?? 'Our Best Value'); ?>" required autocomplete="off">
                <span class="field-hint"><i class="fa-solid fa-circle-info"></i> Teks kecil di atas judul bagian unggulan, misal: "Our Best Value"</span>
            </div>

            <div class="form-group">
                <label for="featured_section_title">Judul Utama Bagian Unggulan <span class="req-star">*</span></label>
                <input type="text" id="featured_section_title" name="featured_section_title" class="form-control" 
                       value="<?php echo htmlspecialchars($settings['featured_section_title'] ?? 'Combo Packages'); ?>" required autocomplete="off">
                <span class="field-hint"><i class="fa-solid fa-circle-info"></i> Judul besar yang tampil di halaman utama pelanggan</span>
            </div>

            <div class="form-group">
                <label for="featured_section_subtitle">Sub-Judul Deskripsi Bagian Unggulan <span class="req-star">*</span></label>
                <textarea id="featured_section_subtitle" name="featured_section_subtitle" class="form-control" style="min-height: 80px;" required><?php echo htmlspecialchars($settings['featured_section_subtitle'] ?? ''); ?></textarea>
                <span class="field-hint"><i class="fa-solid fa-circle-info"></i> Deskripsi singkat di bawah judul bagian unggulan</span>
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
        <form action="admin.php?page=pengaturan&action=update_sesi" method="POST" onsubmit="return validateSesiForm()">
            
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
                                            <button type="button"
                                               class="btn-spa btn-spa-outline"
                                               style="border-color: var(--danger); color: var(--danger); padding: 6px 10px; font-size: 0.8rem;"
                                               onclick="showHapusRekeningConfirm('admin.php?page=pengaturan&action=hapus_rekening&id=<?php echo (int) $r['id_rekening']; ?>')">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
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
                <form action="admin.php?page=pengaturan&action=tambah_rekening" method="POST" onsubmit="return validateRekeningForm()">
                    <div class="form-group">
                        <label for="nama_bank">Nama Bank / E-Wallet <span class="req-star">*</span></label>
                        <input type="text" id="nama_bank" name="nama_bank" class="form-control" placeholder="Contoh: BCA, Mandiri, GoPay" required
                               oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');"
                               title="Nama bank/e-wallet hanya boleh berisi huruf dan spasi">
                        <span class="field-hint"><i class="fa-solid fa-circle-info"></i> Hanya huruf dan spasi — contoh: BCA, GoPay, OVO</span>
                    </div>
                    <div class="form-group">
                        <label for="nomor_rekening">Nomor Rekening / Nomor HP <span class="req-star">*</span></label>
                        <input type="text" id="nomor_rekening" name="nomor_rekening" class="form-control" placeholder="Contoh: 1234567890" required
                               oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                               title="Nomor rekening hanya boleh berisi angka">
                        <span class="field-hint"><i class="fa-solid fa-circle-info"></i> Hanya angka — contoh: 0812345678</span>
                    </div>
                    <div class="form-group">
                        <label for="atas_nama">Atas Nama <span class="req-star">*</span></label>
                        <input type="text" id="atas_nama" name="atas_nama" class="form-control" placeholder="Contoh: SPADMIN SPA" required
                               oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');"
                               title="Atas nama hanya boleh berisi huruf dan spasi">
                        <span class="field-hint"><i class="fa-solid fa-circle-info"></i> Hanya huruf dan spasi sesuai nama pemilik rekening</span>
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

function showPengaturanPopup(title, message, focusId) {
    document.getElementById('pengaturanPopupTitle').textContent = title || 'Peringatan';
    document.getElementById('pengaturanPopupMsg').textContent = message;
    var overlay = document.getElementById('pengaturanPopupOverlay');
    overlay.style.display = 'flex';
    document.getElementById('pengaturanPopupOkBtn').onclick = function() {
        closePengaturanPopup();
        if (focusId) {
            var el = document.getElementById(focusId);
            if (el) el.focus();
        }
    };
}

function closePengaturanPopup() {
    document.getElementById('pengaturanPopupOverlay').style.display = 'none';
}

function showHapusRekeningConfirm(href) {
    var rows = document.querySelectorAll('#metode-transfer table tbody tr');
    if (rows.length <= 1 && !rows[0].innerText.includes('Belum ada metode transfer')) {
        showPengaturanPopup('Peringatan', 'minimal harus terdapat 1 metode pembayaran yg aktif');
        return;
    }
    document.getElementById('pengaturanConfirmHref').href = href;
    document.getElementById('pengaturanConfirmOverlay').style.display = 'flex';
}

function closePengaturanConfirm() {
    document.getElementById('pengaturanConfirmOverlay').style.display = 'none';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePengaturanPopup();
        closePengaturanConfirm();
    }
});
document.addEventListener('click', function(e) {
    if (e.target === document.getElementById('pengaturanPopupOverlay')) closePengaturanPopup();
    if (e.target === document.getElementById('pengaturanConfirmOverlay')) closePengaturanConfirm();
});

function validateSesiForm() {
    var pMulai   = document.getElementById('sesi_pagi_mulai').value;
    var pSelesai = document.getElementById('sesi_pagi_selesai').value;
    var sMulai   = document.getElementById('sesi_siang_mulai').value;
    var sSelesai = document.getElementById('sesi_siang_selesai').value;
    var soMulai  = document.getElementById('sesi_sore_mulai').value;
    var soSelesai= document.getElementById('sesi_sore_selesai').value;

    if (pMulai >= pSelesai) {
        showPengaturanPopup('Konfigurasi Sesi Tidak Valid', 'Jam mulai Sesi Pagi harus lebih awal dari jam selesai. Contoh: mulai 09:00, selesai 11:30.', 'sesi_pagi_mulai');
        return false;
    }
    if (sMulai >= sSelesai) {
        showPengaturanPopup('Konfigurasi Sesi Tidak Valid', 'Jam mulai Sesi Siang harus lebih awal dari jam selesai. Contoh: mulai 12:00, selesai 16:30.', 'sesi_siang_mulai');
        return false;
    }
    if (soMulai >= soSelesai) {
        showPengaturanPopup('Konfigurasi Sesi Tidak Valid', 'Jam mulai Sesi Sore harus lebih awal dari jam selesai. Contoh: mulai 17:00, selesai 20:00.', 'sesi_sore_mulai');
        return false;
    }
    if (sMulai < pSelesai) {
        showPengaturanPopup('Jadwal Sesi Bertabrakan', 'Sesi Siang tidak boleh dimulai sebelum Sesi Pagi selesai. Sesi Pagi selesai pukul ' + pSelesai + ', maka Sesi Siang harus mulai pukul ' + pSelesai + ' atau lebih.', 'sesi_siang_mulai');
        return false;
    }
    if (soMulai < sSelesai) {
        showPengaturanPopup('Jadwal Sesi Bertabrakan', 'Sesi Sore tidak boleh dimulai sebelum Sesi Siang selesai. Sesi Siang selesai pukul ' + sSelesai + ', maka Sesi Sore harus mulai pukul ' + sSelesai + ' atau lebih.', 'sesi_sore_mulai');
        return false;
    }
    return true;
}

function validateRekeningForm() {
    var namaBank = document.getElementById('nama_bank').value.trim();
    var noRek    = document.getElementById('nomor_rekening').value.trim();
    var anRek    = document.getElementById('atas_nama').value.trim();

    if (!namaBank || !/^[a-zA-Z\s]+$/.test(namaBank)) {
        showPengaturanPopup('Format Nama Bank Tidak Valid', 'Nama Bank / E-Wallet hanya boleh berisi huruf dan spasi', 'nama_bank');
        return false;
    }
    if (!noRek || !/^[0-9]+$/.test(noRek)) {
        showPengaturanPopup('Format Nomor Rekening Tidak Valid', 'wajib diisi dengan nomor yg valid', 'nomor_rekening');
        return false;
    }
    if (!anRek || !/^[a-zA-Z\s]+$/.test(anRek)) {
        showPengaturanPopup('Format Atas Nama Tidak Valid', 'atas nama hanya boleh berisi huruf dan spasi', 'atas_nama');
        return false;
    }
    return true;
}
</script>
