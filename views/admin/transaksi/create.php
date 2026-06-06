
<?php

if (!function_exists('rupiah')) {
    function rupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
}
?>

<?php if (!empty($error)): ?>
    <div style="background-color: var(--danger-bg); border: 1px solid var(--danger); color: var(--danger); padding: 15px; border-radius: var(--radius-sm); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="admin-section-tabs">
    <a href="admin.php?page=transaksi" class="admin-section-tab">
        <i class="fa-solid fa-list-check"></i>
        <span>Riwayat Transaksi</span>
    </a>
    <a href="admin.php?page=transaksi&action=create" class="admin-section-tab active">
        <i class="fa-solid fa-cash-register"></i>
        <span>POS Walk-In</span>
    </a>
</div>

<div class="pos-container">
    
    
<style>
.btn-pos-filter:hover {
    border-color: var(--accent) !important;
    color: var(--accent) !important;
    background: white !important;
}
.btn-pos-filter.active {
    background: var(--accent) !important;
    color: white !important;
    border-color: var(--accent) !important;
}
.pos-pagination-btn {
    padding: 6px 12px;
    border: 1px solid var(--border-color);
    background: var(--bg-light);
    border-radius: 50px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-dark);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}
.pos-pagination-btn:hover:not(.disabled) {
    background: var(--accent) !important;
    color: white !important;
    border-color: var(--accent) !important;
}
.pos-pagination-btn.active {
    background: var(--accent) !important;
    color: white !important;
    border-color: var(--accent) !important;
}
.pos-pagination-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>

<?php
$metodePembayaranAktif = $metodePembayaranAktif ?? [];
$categories = ['Semua'];
foreach ($layananList as $srv) {
    $cat = isset($srv['kategori']) ? trim($srv['kategori']) : '';
    if ($cat !== '') {
        $catFormatted = ucwords(strtolower($cat));
        if (!in_array($catFormatted, $categories)) {
            $categories[] = $catFormatted;
        }
    }
}
?>

    <div>
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Pilih Perawatan SPA</h3>
            </div>
            <div class="panel-body">
                
                <!-- Search & Kategori Panel -->
                <div style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 20px;">
                    <!-- Search Input -->
                    <div style="position: relative; width: 100%;">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
                        <input type="text" id="posSearchLayanan" placeholder="Cari nama perawatan SPA..." 
                               style="width: 100%; padding: 10px 12px 10px 38px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); outline: none; background: var(--bg-light); color: var(--text-dark); font-size: 0.9rem; box-sizing: border-box;"
                               oninput="filterPOSLayanan()">
                    </div>
                    
                    <!-- Kategori Tabs -->
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;" id="posCategoryTabs">
                        <?php foreach ($categories as $cat): ?>
                            <button type="button" class="btn-pos-filter <?php echo $cat === 'Semua' ? 'active' : ''; ?>" 
                                    data-category="<?php echo htmlspecialchars($cat); ?>"
                                    style="padding: 6px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; border: 1px solid var(--border-color); background: <?php echo $cat === 'Semua' ? 'var(--accent)' : 'var(--bg-light)'; ?>; color: <?php echo $cat === 'Semua' ? 'white' : 'var(--text-dark)'; ?>; cursor: pointer; transition: all 0.2s ease;"
                                    onclick="selectPOSCategory(this)">
                                <?php echo htmlspecialchars($cat); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pos-menu-grid">
                    <?php foreach ($layananList as $srv): 
                        $srvCat = isset($srv['kategori']) ? ucwords(strtolower(trim($srv['kategori']))) : 'Lainnya';
                    ?>
                        <div class="pos-item-card" 
                             data-id="<?php echo $srv['id_layanan']; ?>" 
                             data-nama="<?php echo htmlspecialchars($srv['nama_layanan']); ?>" 
                             data-durasi="<?php echo $srv['durasi']; ?>" 
                             data-harga="<?php echo $srv['harga']; ?>"
                             data-kategori="<?php echo htmlspecialchars($srvCat); ?>">
                            <div>
                                <h4 class="pos-item-title"><?php echo htmlspecialchars($srv['nama_layanan']); ?></h4>
                                <span class="pos-item-duration"><i class="fa-regular fa-clock"></i> <?php echo $srv['durasi']; ?> Menit</span>
                            </div>
                            <span class="pos-item-price"><?php echo rupiah($srv['harga']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination Panel -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; border-top: 1px solid var(--border-color); padding-top: 15px;" id="posPagination">
                    <span id="posPaginationInfo" style="font-size: 0.85rem; color: var(--text-muted);">Menampilkan...</span>
                    <div id="posPaginationNav" style="display: flex; gap: 6px; align-items: center;"></div>
                </div>

            </div>
        </div>
    </div>
    
    
    <div>
        <form action="admin.php?page=transaksi&action=create" method="POST" onsubmit="return validatePOSForm()">
            
            <input type="hidden" name="cart_items" id="posInputItems" value="[]">
            
            <div class="cart-box">
                <div class="cart-header">
                    <h3 style="font-size: 1.15rem; color: var(--primary); font-weight: 700;">
                        <i class="fa-solid fa-cart-shopping" style="color: var(--accent); margin-right: 5px;"></i> Rincian Kasir POS
                    </h3>
                </div>
                
                
                <div class="cart-items" id="cartItems">
                    <div class="text-center py-4 text-muted">
                        <i class="fa-solid fa-cart-shopping mb-2 d-block fs-3" style="color: var(--border-color);"></i>
                        Keranjang Belanja Kosong
                    </div>
                </div>
                
                
                <div class="cart-summary">
                    <span id="posSubtotal" style="display:none;">Rp 0</span>
                    <span id="posTax" style="display:none;">Rp 0</span>
                    <div class="cart-row cart-row-total">
                        <span>Total Bayar:</span>
                        <strong id="posTotal" style="color: var(--accent-hover);">Rp 0</strong>
                    </div>
                </div>
                
                
                <div style="border-top: 1px solid var(--border-color); padding-top: 20px; margin-bottom: 20px;">

                    <!-- ===== PELANGGAN AUTOCOMPLETE ===== -->
                    <div class="form-group" style="position: relative;">
                        <label>Cari &amp; Pilih Pelanggan</label>
                        <!-- Hidden input untuk form submit -->
                        <input type="hidden" id="pelanggan_select" name="pelanggan_id" value="">
                        <!-- Kotak terpilih -->
                        <div id="pelanggan_selected_box" style="display:none; background: var(--bg-light); border: 1.5px solid var(--accent); border-radius: var(--radius-sm); padding: 8px 12px; margin-bottom: 8px; display: none; align-items: center; justify-content: space-between; gap: 8px;">
                            <span style="display:flex; align-items:center; gap:6px;">
                                <i class="fa-solid fa-user-check" style="color: var(--accent);"></i>
                                <strong id="pelanggan_selected_label" style="font-size:0.88rem; color: var(--text-dark);"></strong>
                            </span>
                            <button type="button" onclick="clearPelangganPOS()" style="background:none; border:none; cursor:pointer; color:var(--danger); font-size:0.8rem; padding:2px 6px;" title="Hapus pilihan">
                                <i class="fa-solid fa-xmark"></i> Ganti
                            </button>
                        </div>
                        <!-- Input search -->
                        <div id="pelanggan_search_wrap" style="position: relative;">
                            <i class="fa-solid fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem; pointer-events:none;"></i>
                            <input type="text" id="pelanggan_search_pos" placeholder="Ketik nama, email, atau no telp..."
                                   autocomplete="off"
                                   style="width: 100%; padding: 8px 10px 8px 30px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); outline: none; background: var(--bg-light); color: var(--text-dark); font-size: 0.85rem; box-sizing: border-box;"
                                   oninput="filterPelangganPOS(this.value)"
                                   onfocus="filterPelangganPOS(this.value)">
                            <!-- Dropdown hasil -->
                            <div id="pelanggan_dropdown" style="display:none; position:absolute; top:100%; left:0; right:0; z-index:200; background: white; border: 1px solid var(--border-color); border-radius: var(--radius-sm); box-shadow: 0 4px 16px rgba(0,0,0,0.10); max-height: 220px; overflow-y:auto; margin-top: 2px;"></div>
                        </div>
                        <div id="pelanggan_not_found" style="display:none; font-size:0.82rem; color: var(--danger); margin-top: 5px;">
                            <i class="fa-solid fa-circle-exclamation" style="margin-right: 4px;"></i> Pelanggan tidak ditemukan. Silakan daftarkan sebagai pelanggan baru di bawah.
                        </div>
                        <button type="button" id="new_customer_button" onclick="selectPelangganPOS('new', '+ Pelanggan Baru (Walk-In)')" class="btn-spa btn-spa-outline" style="width: 100%; justify-content: center; margin-top: 10px; padding: 8px 12px; font-size: 0.82rem;">
                            <i class="fa-solid fa-user-plus"></i> Daftarkan Pelanggan Baru (Walk-In)
                        </button>
                    </div>

                    <!-- Form Pelanggan Baru -->
                    <div id="new_customer_form" style="display: none; border: 1px dashed var(--accent); padding: 15px; border-radius: var(--radius-sm); margin-top: 10px; background: var(--bg-light);">
                        <h5 style="margin-bottom: 12px; color: var(--primary); font-weight: 700;"><i class="fa-solid fa-user-plus"></i> Profil Pelanggan Baru</h5>
                        <div class="form-group">
                            <label for="new_nama">Nama Lengkap</label>
                            <input type="text" id="new_nama" name="new_nama" class="form-control" placeholder="Contoh: Sarah Angelina" autocomplete="off" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">
                        </div>
                        <div class="form-group">
                            <label for="new_no_hp">Nomor Telepon / WA</label>
                            <input type="text" id="new_no_hp" name="new_no_hp" class="form-control" placeholder="Contoh: 0812XXXXXXXX" autocomplete="off" oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(this.value.length > 0 && (this.value.length < 10 || this.value.length > 13)) { document.getElementById('err_new_no_hp').style.display = 'block'; } else { document.getElementById('err_new_no_hp').style.display = 'none'; }">
                            <small id="err_new_no_hp" style="color: var(--danger); display: none; margin-top: 5px;">*Nomor telepon harus berupa angka dan berjumlah 10-13 digit.</small>
                        </div>
                        <div class="form-group">
                            <label for="new_email">Email (Opsional)</label>
                            <input type="email" id="new_email" name="new_email" class="form-control" placeholder="Contoh: sarah@email.com">
                        </div>
                    </div>

                    <!-- ===== TERAPIS AUTOCOMPLETE ===== -->
                    <div class="form-group" style="margin-top: 18px; position: relative;">
                        <label>Cari &amp; Pilih Terapis Bertugas</label>
                        <!-- Hidden input untuk form submit -->
                        <input type="hidden" id="terapis_select" name="id_terapis" value="">
                        <!-- Kotak terpilih -->
                        <div id="terapis_selected_box" style="display:none; background: var(--bg-light); border: 1.5px solid var(--accent); border-radius: var(--radius-sm); padding: 8px 12px; margin-bottom: 8px; align-items: center; justify-content: space-between; gap: 8px;">
                            <span style="display:flex; align-items:center; gap:6px;">
                                <i class="fa-solid fa-user-doctor" style="color: var(--accent);"></i>
                                <strong id="terapis_selected_label" style="font-size:0.88rem; color: var(--text-dark);"></strong>
                            </span>
                            <button type="button" onclick="clearTerapisPOS()" style="background:none; border:none; cursor:pointer; color:var(--danger); font-size:0.8rem; padding:2px 6px;" title="Hapus pilihan">
                                <i class="fa-solid fa-xmark"></i> Ganti
                            </button>
                        </div>
                        <!-- Input search -->
                        <div id="terapis_search_wrap" style="position: relative;">
                            <i class="fa-solid fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem; pointer-events:none;"></i>
                            <input type="text" id="terapis_search_pos" placeholder="Ketik nama atau spesialisasi terapis..."
                                   autocomplete="off"
                                   style="width: 100%; padding: 8px 10px 8px 30px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); outline: none; background: var(--bg-light); color: var(--text-dark); font-size: 0.85rem; box-sizing: border-box;"
                                   oninput="filterTerapisPOS(this.value)"
                                   onclick="filterTerapisPOS(this.value)"
                                   onfocus="filterTerapisPOS(this.value)">
                            <!-- Dropdown hasil -->
                            <div id="terapis_dropdown" style="display:none; position:absolute; top:100%; left:0; right:0; z-index:200; background: white; border: 1px solid var(--border-color); border-radius: var(--radius-sm); box-shadow: 0 4px 16px rgba(0,0,0,0.10); max-height: 220px; overflow-y:auto; margin-top: 2px;"></div>
                        </div>
                        <div id="terapis_not_found" style="display:none; font-size:0.82rem; color: var(--danger); margin-top: 5px;">
                            <i class="fa-solid fa-circle-exclamation" style="margin-right: 4px;"></i> Tidak ada terapis aktif yang cocok dengan pencarian.
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 15px;">
                        <label for="metode_pembayaran_display">Metode Pembayaran Kasir</label>
                        <input type="hidden" id="metode_pembayaran" name="metode_pembayaran" value="Tunai">
                        <select id="metode_pembayaran_display" class="form-control" required onchange="toggleCashSection(this.value)">
                            <option value="Tunai" selected>Tunai (Cash)</option>
                            <option value="Transfer">Transfer Bank</option>
                            <option value="E-Wallet">E-Wallet (OVO/Gopay/Qris)</option>
                        </select>
                    </div>

                    <div id="posBankSection" class="form-group" style="display: none; margin-top: 15px;">
                        <label for="pos_bank">Pilih Rekening Bank</label>
                        <select id="pos_bank" class="form-control" onchange="syncPOSBank(this.value)">
                            <option value="">-- Pilih Bank --</option>
                            <?php foreach ($metodePembayaranAktif as $metodeAktif): ?>
                                <option value="<?php echo htmlspecialchars($metodeAktif); ?>"><?php echo htmlspecialchars($metodeAktif); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    
                    <div id="cashInputSection" class="form-grid" style="margin-top: 15px; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="nominal_bayar">Uang Bayar (Rp)</label>
                            <input type="number" id="nominal_bayar" name="nominal_bayar" class="form-control" placeholder="Jumlah uang..." min="0" onkeyup="calculateChange()" onchange="calculateChange()">
                        </div>
                        <div class="form-group">
                            <label for="nominal_kembalian_display">Kembalian</label>
                            <input type="text" id="nominal_kembalian_display" class="form-control" value="Rp 0" readonly style="background-color: var(--bg-light); font-weight: 700; color: var(--accent-hover);">
                            <input type="hidden" id="nominal_kembalian" name="nominal_kembalian" value="0">
                        </div>
                    </div>
                </div>
                
                
                <button type="submit" class="btn-spa btn-spa-accent" style="width: 100%; justify-content: center; padding: 14px; font-size: 1rem;">
                    <i class="fa-solid fa-cash-register"></i> Cetak & Selesaikan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function validatePOSForm() {
    const cartItems = JSON.parse(document.getElementById('posInputItems').value || '[]');
    if (cartItems.length === 0) {
        alert('Keranjang belanja kosong! Silakan pilih layanan SPA.');
        return false;
    }

    const terapis = document.getElementById('terapis_select').value;
    if (!terapis) {
        alert('Pilih terapis terlebih dahulu untuk melakukan check-out POS!');
        return false;
    }

    const pelanggan = document.getElementById('pelanggan_select').value;
    if (!pelanggan) {
        alert('Pelanggan harus dipilih atau didaftarkan!');
        return false;
    }

    const metodeDisplay = document.getElementById('metode_pembayaran_display').value;
    const metode = document.getElementById('metode_pembayaran').value;
    if (metodeDisplay === 'Transfer' && !metode) {
        alert('Pilih bank pembayaran terlebih dahulu!');
        return false;
    }
    if (metode === 'Tunai') {
        const total = window.currentPosTotal || 0;
        const bayar = parseFloat(document.getElementById('nominal_bayar').value) || 0;
        if (bayar < total) {
            alert('Uang bayar kurang! Pembayaran tunai harus lebih besar atau sama dengan total bayar (' + 'Rp ' + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(total) + ').');
            return false;
        }
    }
    return true;
}

function toggleNewCustomerForm(value) {
    const form = document.getElementById('new_customer_form');
    const inputs = form.querySelectorAll('input');
    
    if (value === 'new') {
        form.style.display = 'block';
        inputs.forEach(input => {
            if (input.id !== 'new_email') {
                input.required = true;
            }
        });
    } else {
        form.style.display = 'none';
        inputs.forEach(input => {
            input.required = false;
        });
    }
}

function calculateChange() {
    const total = window.currentPosTotal || 0;
    const bayarInput = document.getElementById('nominal_bayar');
    const kembalianDisplay = document.getElementById('nominal_kembalian_display');
    const kembalianInput = document.getElementById('nominal_kembalian');
    
    if (!bayarInput || !kembalianDisplay) return;
    
    const bayar = parseFloat(bayarInput.value) || 0;
    const kembalian = Math.max(0, bayar - total);
    
    kembalianDisplay.value = 'Rp ' + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(kembalian);
    if (kembalianInput) {
        kembalianInput.value = kembalian;
    }
}

function toggleCashSection(value) {
    const section = document.getElementById('cashInputSection');
    const bankSection = document.getElementById('posBankSection');
    const bankSelect = document.getElementById('pos_bank');
    const metodeInput = document.getElementById('metode_pembayaran');
    const bayarInput = document.getElementById('nominal_bayar');
    const kembalianDisplay = document.getElementById('nominal_kembalian_display');
    const kembalianInput = document.getElementById('nominal_kembalian');
    
    if (metodeInput) {
        metodeInput.value = value === 'Transfer' ? (bankSelect ? bankSelect.value : '') : value;
    }
    
    if (value === 'Tunai') {
        section.style.display = '';
    } else {
        section.style.display = 'none';
        if (bayarInput) bayarInput.value = '';
        if (kembalianDisplay) kembalianDisplay.value = 'Rp 0';
        if (kembalianInput) kembalianInput.value = '0';
    }
    
    if (bankSection) bankSection.style.display = value === 'Transfer' ? 'block' : 'none';
    if (value !== 'Transfer' && bankSelect) {
        bankSelect.value = '';
    }
}

function syncPOSBank(value) {
    const metodeInput = document.getElementById('metode_pembayaran');
    if (metodeInput) metodeInput.value = value;
}

// ===== POS SEARCH, FILTER & PAGINATION JS =====
let currentPOSPage = 1;
const ITEMS_PER_PAGE = 6;
let currentPOSCategory = 'Semua';
let currentPOSSearch = '';

function filterPOSLayanan() {
    const searchInput = document.getElementById('posSearchLayanan');
    currentPOSSearch = searchInput ? searchInput.value.toLowerCase().trim() : '';
    currentPOSPage = 1;
    applyPOSFiltersAndRender();
}

function selectPOSCategory(btn) {
    const buttons = document.querySelectorAll('#posCategoryTabs button');
    buttons.forEach(b => {
        b.classList.remove('active');
        b.style.background = 'var(--bg-light)';
        b.style.color = 'var(--text-dark)';
        b.style.borderColor = 'var(--border-color)';
    });
    
    btn.classList.add('active');
    btn.style.background = 'var(--accent)';
    btn.style.color = 'white';
    btn.style.borderColor = 'var(--accent)';
    
    currentPOSCategory = btn.getAttribute('data-category');
    currentPOSPage = 1;
    applyPOSFiltersAndRender();
}

function applyPOSFiltersAndRender() {
    const cards = Array.from(document.querySelectorAll('.pos-item-card'));
    
    const filteredCards = cards.filter(card => {
        const nama = card.getAttribute('data-nama').toLowerCase();
        const cat = card.getAttribute('data-kategori');
        
        const matchSearch = nama.includes(currentPOSSearch);
        const matchCategory = (currentPOSCategory === 'Semua' || cat === currentPOSCategory);
        
        return matchSearch && matchCategory;
    });
    
    cards.forEach(card => card.style.display = 'none');
    
    const totalItems = filteredCards.length;
    const totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE) || 1;
    
    if (currentPOSPage > totalPages) currentPOSPage = totalPages;
    
    const startIndex = (currentPOSPage - 1) * ITEMS_PER_PAGE;
    const endIndex = Math.min(startIndex + ITEMS_PER_PAGE, totalItems);
    
    const paginatedCards = filteredCards.slice(startIndex, endIndex);
    paginatedCards.forEach(card => card.style.display = 'flex');
    
    const infoEl = document.getElementById('posPaginationInfo');
    const navEl = document.getElementById('posPaginationNav');
    
    if (!infoEl || !navEl) return;
    
    if (totalItems === 0) {
        infoEl.textContent = 'Tidak ada perawatan yang cocok';
        navEl.innerHTML = '';
        return;
    }
    
    infoEl.textContent = `Menampilkan ${startIndex + 1} - ${endIndex} dari ${totalItems} data`;
    
    let navHtml = '';
    navHtml += `<button type="button" class="pos-pagination-btn ${currentPOSPage === 1 ? 'disabled' : ''}" onclick="goToPOSPage(${currentPOSPage - 1})"><i class="fa-solid fa-chevron-left"></i></button>`;
    
    for (let i = 1; i <= totalPages; i++) {
        navHtml += `<button type="button" class="pos-pagination-btn ${i === currentPOSPage ? 'active' : ''}" onclick="goToPOSPage(${i})">${i}</button>`;
    }
    
    navHtml += `<button type="button" class="pos-pagination-btn ${currentPOSPage === totalPages ? 'disabled' : ''}" onclick="goToPOSPage(${currentPOSPage + 1})"><i class="fa-solid fa-chevron-right"></i></button>`;
    
    navEl.innerHTML = navHtml;
}

function goToPOSPage(page) {
    const cards = document.querySelectorAll('.pos-item-card');
    const filteredCount = Array.from(cards).filter(card => {
        const nama = card.getAttribute('data-nama').toLowerCase();
        const cat = card.getAttribute('data-kategori');
        return nama.includes(currentPOSSearch) && (currentPOSCategory === 'Semua' || cat === currentPOSCategory);
    }).length;
    
    const totalPages = Math.ceil(filteredCount / ITEMS_PER_PAGE) || 1;
    if (page < 1 || page > totalPages) return;
    
    currentPOSPage = page;
    applyPOSFiltersAndRender();
}

// ===== DATA =====
const allPelanggan = <?php echo json_encode(array_values($pelangganList)); ?>;
const allTerapis   = <?php echo json_encode(array_values($terapisList)); ?>;

// ===== HELPER =====
function escapeHtml(text) {
    if (!text) return '';
    return String(text).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}

// ===== PELANGGAN AUTOCOMPLETE =====
var pelangganTimer = null;
var pelangganActiveIndex = -1;

function filterPelangganPOS(keyword) {
    if (keyword === undefined) keyword = '';
    var dropdown   = document.getElementById('pelanggan_dropdown');
    var notFound   = document.getElementById('pelanggan_not_found');
    var hiddenInput = document.getElementById('pelanggan_select');
    if (!dropdown) return;

    pelangganActiveIndex = -1;

    var searchVal = keyword.toLowerCase().trim();
    var filtered = allPelanggan.filter(function(p) {
        return (p.nama||'').toLowerCase().includes(searchVal)
            || (p.email||'').toLowerCase().includes(searchVal)
            || (p.no_telepon||'').toLowerCase().includes(searchVal);
    });
    var maxData = filtered.slice(0, 6);

    // Tampilkan pesan tidak ditemukan
    if (notFound) notFound.style.display = (searchVal !== '' && filtered.length === 0) ? 'block' : 'none';

    var html = '';
    if (maxData.length === 0 && searchVal === '') {
        html += '<div style="padding:10px 14px; font-size:0.82rem; color:var(--text-muted);">Ketik nama untuk mencari pelanggan...</div>';
    }

    maxData.forEach(function(p) {
        var label = escapeHtml(p.nama) + ' <span style="color:var(--text-muted);font-weight:400;">(' + escapeHtml(p.no_telepon) + ')</span>';
        html += '<div class="pelanggan-opt-item" onclick="selectPelangganPOS(\'' + p.id_user + '\', \'' + p.nama.replace(/'/g,"\\'") + ' (' + p.no_telepon.replace(/'/g,"\\'") + ')\')" '
              + 'style="padding:9px 14px; cursor:pointer; font-size:0.85rem; border-bottom:1px solid var(--border-color); display:flex; align-items:center; gap:8px;"'
              + ' onmouseover="this.style.background=\'#f5f5f5\'" onmouseout="this.style.background=\'white\'">'
              + '<i class="fa-solid fa-user" style="color:var(--primary-light); font-size:0.75rem;"></i>' + label + '</div>';
    });

    dropdown.innerHTML = html;
    dropdown.style.display = 'block';
}

function selectPelangganPOS(id, label) {
    var hiddenInput   = document.getElementById('pelanggan_select');
    var searchInput   = document.getElementById('pelanggan_search_pos');
    var selectedBox   = document.getElementById('pelanggan_selected_box');
    var selectedLabel = document.getElementById('pelanggan_selected_label');
    var searchWrap    = document.getElementById('pelanggan_search_wrap');
    var dropdown      = document.getElementById('pelanggan_dropdown');
    var notFound      = document.getElementById('pelanggan_not_found');
    var newButton     = document.getElementById('new_customer_button');

    hiddenInput.value = id;
    if (searchInput) searchInput.value = '';
    if (dropdown) dropdown.style.display = 'none';
    if (notFound) notFound.style.display = 'none';

    if (selectedLabel) selectedLabel.innerHTML = label;
    if (selectedBox) { selectedBox.style.display = 'flex'; }
    if (searchWrap)  { searchWrap.style.display  = 'none'; }
    if (newButton)   { newButton.style.display   = 'none'; }

    // Tampilkan form pelanggan baru jika pilihan "new"
    toggleNewCustomerForm(id);
}

function clearPelangganPOS() {
    var hiddenInput = document.getElementById('pelanggan_select');
    var selectedBox = document.getElementById('pelanggan_selected_box');
    var searchWrap  = document.getElementById('pelanggan_search_wrap');
    var dropdown    = document.getElementById('pelanggan_dropdown');
    var newButton   = document.getElementById('new_customer_button');
    if (hiddenInput) hiddenInput.value = '';
    if (selectedBox) selectedBox.style.display = 'none';
    if (searchWrap)  searchWrap.style.display   = '';
    if (dropdown)    dropdown.style.display     = 'none';
    if (newButton)   newButton.style.display    = '';
    toggleNewCustomerForm('');
    document.getElementById('pelanggan_search_pos').focus();
}

// Tutup dropdown pelanggan saat klik di luar
document.addEventListener('click', function(e) {
    var wrap = document.getElementById('pelanggan_search_wrap');
    var dd   = document.getElementById('pelanggan_dropdown');
    if (wrap && dd && !wrap.contains(e.target)) dd.style.display = 'none';
});

// ===== TERAPIS AUTOCOMPLETE =====
var terapisActiveIndex = -1;

function filterTerapisPOS(keyword) {
    if (keyword === undefined) keyword = '';
    var dropdown    = document.getElementById('terapis_dropdown');
    var notFound    = document.getElementById('terapis_not_found');
    if (!dropdown) return;

    terapisActiveIndex = -1;

    var searchVal = keyword.toLowerCase().trim();
    var filtered = allTerapis.filter(function(t) {
        return (t.nama_terapis||'').toLowerCase().includes(searchVal)
            || (t.spesialisasi||'').toLowerCase().includes(searchVal);
    });

    if (notFound) notFound.style.display = (filtered.length === 0) ? 'block' : 'none';

    var html = '';
    if (filtered.length === 0) {
        dropdown.innerHTML = '';
        dropdown.style.display = 'none';
        return;
    }

    filtered.forEach(function(t) {
        var label = escapeHtml(t.nama_terapis) + ' <span style="color:var(--text-muted);font-weight:400;">(' + escapeHtml(t.spesialisasi) + ')</span>';
        html += '<div class="terapis-opt-item" onclick="selectTerapisPOS(\'' + t.id_terapis + '\', \'' + t.nama_terapis.replace(/'/g,"\\'") + ' (' + t.spesialisasi.replace(/'/g,"\\'") + ')\')" '
              + 'style="padding:9px 14px; cursor:pointer; font-size:0.85rem; border-bottom:1px solid var(--border-color); display:flex; align-items:center; gap:8px;"'
              + ' onmouseover="this.style.background=\'#f5f5f5\'" onmouseout="this.style.background=\'white\'">'
              + '<i class="fa-solid fa-user-doctor" style="color:var(--accent); font-size:0.75rem;"></i>' + label + '</div>';
    });

    dropdown.innerHTML = html;
    dropdown.style.display = 'block';
}

function selectTerapisPOS(id, label) {
    var hiddenInput   = document.getElementById('terapis_select');
    var searchInput   = document.getElementById('terapis_search_pos');
    var selectedBox   = document.getElementById('terapis_selected_box');
    var selectedLabel = document.getElementById('terapis_selected_label');
    var searchWrap    = document.getElementById('terapis_search_wrap');
    var dropdown      = document.getElementById('terapis_dropdown');
    var notFound      = document.getElementById('terapis_not_found');

    hiddenInput.value = id;
    if (searchInput) searchInput.value = '';
    if (dropdown)    dropdown.style.display    = 'none';
    if (notFound)    notFound.style.display    = 'none';
    if (selectedLabel) selectedLabel.innerHTML = label;
    if (selectedBox) { selectedBox.style.display = 'flex'; }
    if (searchWrap)  { searchWrap.style.display  = 'none'; }
}

function clearTerapisPOS() {
    var hiddenInput = document.getElementById('terapis_select');
    var selectedBox = document.getElementById('terapis_selected_box');
    var searchWrap  = document.getElementById('terapis_search_wrap');
    var dropdown    = document.getElementById('terapis_dropdown');
    if (hiddenInput) hiddenInput.value = '';
    if (selectedBox) selectedBox.style.display = 'none';
    if (searchWrap)  searchWrap.style.display   = '';
    if (dropdown)    dropdown.style.display     = 'none';
    document.getElementById('terapis_search_pos').focus();
}

// Tutup dropdown terapis saat klik di luar
document.addEventListener('click', function(e) {
    var wrap = document.getElementById('terapis_search_wrap');
    var dd   = document.getElementById('terapis_dropdown');
    if (wrap && dd && !wrap.contains(e.target)) dd.style.display = 'none';
});

// ===== VALIDASI FORM =====
function validatePOSForm() {
    // Override validasi untuk hidden input
    var pelangganId = document.getElementById('pelanggan_select') ? document.getElementById('pelanggan_select').value : '';
    var terapisId   = document.getElementById('terapis_select')   ? document.getElementById('terapis_select').value   : '';
    var cartItems   = JSON.parse(document.getElementById('posInputItems').value || '[]');

    if (cartItems.length === 0) { alert('Keranjang belanja kosong! Silakan pilih layanan SPA.'); return false; }
    if (!terapisId)   { alert('Pilih terapis terlebih dahulu!'); return false; }
    if (!pelangganId) { alert('Pilih atau daftarkan pelanggan terlebih dahulu!'); return false; }

    var metodeDisplay = document.getElementById('metode_pembayaran_display').value;
    var metode = document.getElementById('metode_pembayaran').value;
    if (metodeDisplay === 'Transfer' && !metode) {
        alert('Pilih bank pembayaran terlebih dahulu!');
        return false;
    }
    if (metode === 'Tunai') {
        var total = window.currentPosTotal || 0;
        var bayar = parseFloat(document.getElementById('nominal_bayar').value) || 0;
        if (bayar < total) {
            alert('Uang bayar kurang! Harus >= Rp ' + new Intl.NumberFormat('id-ID').format(total));
            return false;
        }
    }
    return true;
}

// Inisialisasi saat DOM siap
document.addEventListener('DOMContentLoaded', function() {
    applyPOSFiltersAndRender();
    // Keyboard navigation for Pelanggan
    var pelangganSearch = document.getElementById('pelanggan_search_pos');
    if (pelangganSearch) {
        pelangganSearch.addEventListener('keydown', function(e) {
            var dropdown = document.getElementById('pelanggan_dropdown');
            if (!dropdown || dropdown.style.display === 'none') return;

            var items = dropdown.querySelectorAll('.pelanggan-opt-item');
            if (items.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                pelangganActiveIndex++;
                if (pelangganActiveIndex >= items.length) pelangganActiveIndex = 0;
                highlightItem(items, pelangganActiveIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                pelangganActiveIndex--;
                if (pelangganActiveIndex < 0) pelangganActiveIndex = items.length - 1;
                highlightItem(items, pelangganActiveIndex);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (pelangganActiveIndex >= 0 && pelangganActiveIndex < items.length) {
                    items[pelangganActiveIndex].click();
                } else if (items.length > 0) {
                    items[0].click();
                }
            } else if (e.key === 'Escape') {
                dropdown.style.display = 'none';
                pelangganActiveIndex = -1;
            }
        });
    }

    // Keyboard navigation for Terapis
    var terapisSearch = document.getElementById('terapis_search_pos');
    if (terapisSearch) {
        terapisSearch.addEventListener('keydown', function(e) {
            var dropdown = document.getElementById('terapis_dropdown');
            if (!dropdown || dropdown.style.display === 'none') return;

            var items = dropdown.querySelectorAll('.terapis-opt-item');
            if (items.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                terapisActiveIndex++;
                if (terapisActiveIndex >= items.length) terapisActiveIndex = 0;
                highlightItem(items, terapisActiveIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                terapisActiveIndex--;
                if (terapisActiveIndex < 0) terapisActiveIndex = items.length - 1;
                highlightItem(items, terapisActiveIndex);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (terapisActiveIndex >= 0 && terapisActiveIndex < items.length) {
                    items[terapisActiveIndex].click();
                } else if (items.length > 0) {
                    items[0].click();
                }
            } else if (e.key === 'Escape') {
                dropdown.style.display = 'none';
                terapisActiveIndex = -1;
            }
        });
    }

    function highlightItem(items, activeIndex) {
        items.forEach(function(item, idx) {
            if (idx === activeIndex) {
                item.style.background = '#e9ecef';
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.style.background = 'white';
            }
        });
    }
});
</script>
