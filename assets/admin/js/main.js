// assets/js/main.js
// SPADMIN – Interactive UI Script

document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // 1. CONFIRMATION DIALOGS
    // ==========================================
    const deleteButtons = document.querySelectorAll('.btn-confirm-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-message') || "Apakah Anda yakin ingin menghapus data ini?";
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
    
    // ==========================================
    // 2. IMAGE ZOOM MODAL (BUKTI PEMBAYARAN)
    // ==========================================
    const zoomableImage = document.getElementById('buktiImg');
    const zoomModal = document.getElementById('zoomModal');
    const modalImg = document.getElementById('imgZoomed');
    const closeZoomBtn = document.querySelector('.zoom-close');
    
    if (zoomableImage && zoomModal && modalImg) {
        zoomableImage.addEventListener('click', function() {
            zoomModal.style.display = "block";
            modalImg.src = this.src;
        });
        
        const closeZoom = function() {
            zoomModal.style.display = "none";
        };
        
        if (closeZoomBtn) {
            closeZoomBtn.addEventListener('click', closeZoom);
        }
        
        zoomModal.addEventListener('click', function(e) {
            if (e.target === zoomModal) {
                closeZoom();
            }
        });
        
        // ESC key to close
        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape" && zoomModal.style.display === "block") {
                closeZoom();
            }
        });
    }
    
    // ==========================================
    // 3. POS KASIR SYSTEM (REAL-TIME CART WITH QTY)
    // ==========================================
    const posItems = document.querySelectorAll('.pos-item-card');
    const cartItemsContainer = document.getElementById('cartItems');
    const posSubtotalEl = document.getElementById('posSubtotal');
    const posTaxEl = document.getElementById('posTax');
    const posTotalEl = document.getElementById('posTotal');
    const posInputItems = document.getElementById('posInputItems'); // Hidden input to post item IDs
    
    let cart = []; // Each item: { id, nama, harga, durasi, qty }
    
    if (posItems.length > 0 && cartItemsContainer) {
        
        posItems.forEach(item => {
            item.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                const nama = this.getAttribute('data-nama');
                const harga = parseFloat(this.getAttribute('data-harga'));
                const durasi = this.getAttribute('data-durasi');
                
                // Cek apakah item sudah ada di keranjang → tambah qty
                const existing = cart.find(x => x.id === id);
                if (existing) {
                    existing.qty++;
                } else {
                    cart.push({ id, nama, harga, durasi, qty: 1 });
                }
                renderCart();
            });
        });
        
        function renderCart() {
            cartItemsContainer.innerHTML = '';
            
            if (cart.length === 0) {
                cartItemsContainer.innerHTML = '<div class="text-center py-4 text-muted"><i class="fa-solid fa-cart-shopping mb-2 d-block fs-3"></i>Keranjang Belanja Kosong</div>';
                updateTotals(0);
                return;
            }
            
            cart.forEach((item, index) => {
                const itemSubtotal = item.harga * item.qty;
                const cartItemHtml = `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <h5>${item.nama}</h5>
                            <span>${item.durasi} menit × ${item.qty}</span>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 8px;">
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <button type="button" class="cart-qty-btn" data-action="minus" data-index="${index}" style="width:26px;height:26px;border:1px solid var(--border-color);background:var(--bg-light);border-radius:4px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:0.85rem;color:var(--text-dark);"><i class="fa-solid fa-minus" style="font-size:0.65rem;"></i></button>
                                <span style="min-width:22px;text-align:center;font-weight:700;font-size:0.9rem;">${item.qty}</span>
                                <button type="button" class="cart-qty-btn" data-action="plus" data-index="${index}" style="width:26px;height:26px;border:1px solid var(--border-color);background:var(--bg-light);border-radius:4px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:0.85rem;color:var(--text-dark);"><i class="fa-solid fa-plus" style="font-size:0.65rem;"></i></button>
                            </div>
                            <span class="cart-item-price">Rp ${formatRupiah(itemSubtotal)}</span>
                            <button type="button" class="cart-item-remove" data-index="${index}"><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    </div>
                `;
                cartItemsContainer.insertAdjacentHTML('beforeend', cartItemHtml);
            });
            
            // Hitung subtotal
            const subtotal = cart.reduce((sum, item) => sum + (item.harga * item.qty), 0);
            updateTotals(subtotal);
            
            // Handle qty +/- buttons
            const qtyButtons = cartItemsContainer.querySelectorAll('.cart-qty-btn');
            qtyButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const index = parseInt(this.getAttribute('data-index'));
                    const action = this.getAttribute('data-action');
                    if (action === 'plus') {
                        cart[index].qty++;
                    } else if (action === 'minus') {
                        cart[index].qty--;
                        if (cart[index].qty <= 0) {
                            cart.splice(index, 1);
                        }
                    }
                    renderCart();
                });
            });
            
            // Handle Remove (delete entire item)
            const removeButtons = cartItemsContainer.querySelectorAll('.cart-item-remove');
            removeButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const index = parseInt(this.getAttribute('data-index'));
                    cart.splice(index, 1);
                    renderCart();
                });
            });
        }
        
        function updateTotals(subtotal) {
            const total = subtotal;
            
            if (posSubtotalEl) posSubtotalEl.textContent = `Rp ${formatRupiah(subtotal)}`;
            if (posTaxEl) posTaxEl.textContent = `Rp 0`;
            if (posTotalEl) posTotalEl.textContent = `Rp ${formatRupiah(total)}`;
            
            // Expose globally for change calculator in views/transaksi/create.php
            window.currentPosTotal = total;
            if (typeof calculateChange === 'function') {
                calculateChange();
            }
            
            // Update hidden input: expand cart to flat array of IDs (with duplicates for qty)
            if (posInputItems) {
                const ids = [];
                cart.forEach(item => {
                    for (let i = 0; i < item.qty; i++) {
                        ids.push(item.id);
                    }
                });
                posInputItems.value = JSON.stringify(ids);
            }
        }
        
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(number);
        }
    }
    
    // ==========================================
    // 4. CLIENT-SIDE TABLE & REVIEW PAGINATION (DYNAMIC RECALCULATION ON SEARCH)
    // ==========================================
    const ROWS_PER_PAGE = 6;
    
    // Table Pagination
    document.querySelectorAll('.custom-table').forEach(function(table) {
        if (table.hasAttribute('data-admin-paginate')) return;
        if (table.closest('.cart-box') || table.closest('.pos-container') || window.location.search.includes('action=detail') || window.location.search.includes('action=nota') || window.location.search.includes('action=cetak')) return;
        
        const tbody = table.querySelector('tbody');
        if (!tbody) return;
        
        let paginationWrapper = table.nextElementSibling;
        if (!paginationWrapper || !paginationWrapper.classList.contains('pagination-wrapper')) {
            paginationWrapper = document.createElement('div');
            paginationWrapper.className = 'pagination-wrapper';
            const tableParent = table.closest('.table-responsive') || table.closest('.panel-body');
            if (tableParent && tableParent.parentNode) {
                tableParent.parentNode.appendChild(paginationWrapper);
            } else {
                table.parentNode.appendChild(paginationWrapper);
            }
        }
        
        let currentPage = 1;
        
        function updateTablePagination() {
            const allRows = Array.from(tbody.querySelectorAll('tr:not(.no-results-row)'));
            if (allRows.length === 1 && allRows[0].querySelector('td[colspan]')) {
                paginationWrapper.style.display = 'none';
                return;
            }
            
            const eligibleRows = allRows.filter(function(row) {
                return row.dataset.searchMatch !== 'false';
            });
            
            if (eligibleRows.length <= ROWS_PER_PAGE) {
                allRows.forEach(function(row) {
                    if (eligibleRows.includes(row)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
                paginationWrapper.style.display = 'none';
                return;
            }
            
            paginationWrapper.style.display = 'flex';
            const totalPages = Math.ceil(eligibleRows.length / ROWS_PER_PAGE);
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;
            
            const startIdx = (currentPage - 1) * ROWS_PER_PAGE;
            const endIdx = startIdx + ROWS_PER_PAGE;
            
            allRows.forEach(function(row) {
                if (eligibleRows.includes(row)) {
                    const idx = eligibleRows.indexOf(row);
                    row.style.display = (idx >= startIdx && idx < endIdx) ? '' : 'none';
                } else {
                    row.style.display = 'none';
                }
            });
            
            eligibleRows.forEach(function(row, idx) {
                const firstTd = row.querySelector('td:first-child');
                if (firstTd && /^\d+$/.test(firstTd.textContent.trim())) {
                    firstTd.textContent = idx + 1;
                }
            });
            
            const startDisplay = startIdx + 1;
            const endDisplay = Math.min(endIdx, eligibleRows.length);
            
            let html = '<span class="pagination-info">Menampilkan ' + startDisplay + ' - ' + endDisplay + ' dari ' + eligibleRows.length + ' data</span>';
            html += '<div class="pagination-nav">';
            
            html += '<button type="button" class="pagination-btn ' + (currentPage === 1 ? 'disabled' : '') + '" data-page="' + (currentPage - 1) + '"><i class="fa-solid fa-chevron-left"></i></button>';
            
            const maxVisible = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);
            if (endPage - startPage < maxVisible - 1) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }
            
            if (startPage > 1) {
                html += '<button type="button" class="pagination-btn" data-page="1">1</button>';
                if (startPage > 2) html += '<span class="pagination-info" style="padding: 0 4px;">...</span>';
            }
            
            for (let i = startPage; i <= endPage; i++) {
                html += '<button type="button" class="pagination-btn ' + (i === currentPage ? 'active' : '') + '" data-page="' + i + '">' + i + '</button>';
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) html += '<span class="pagination-info" style="padding: 0 4px;">...</span>';
                html += '<button type="button" class="pagination-btn" data-page="' + totalPages + '">' + totalPages + '</button>';
            }
            
            html += '<button type="button" class="pagination-btn ' + (currentPage === totalPages ? 'disabled' : '') + '" data-page="' + (currentPage + 1) + '"><i class="fa-solid fa-chevron-right"></i></button>';
            html += '</div>';
            
            paginationWrapper.innerHTML = html;
            
            paginationWrapper.querySelectorAll('.pagination-btn:not(.disabled)').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const page = parseInt(this.getAttribute('data-page'));
                    if (page >= 1 && page <= totalPages) {
                        currentPage = page;
                        updateTablePagination();
                    }
                });
            });
        }
        
        updateTablePagination();
        
        table.addEventListener('tableFiltered', function() {
            currentPage = 1;
            updateTablePagination();
        });
    });

    // Ulasan Cards Pagination
    document.querySelectorAll('.ulasan-grid').forEach(function(grid) {
        if (grid.hasAttribute('data-admin-paginate')) return;
        let paginationWrapper = grid.nextElementSibling;
        if (!paginationWrapper || !paginationWrapper.classList.contains('pagination-wrapper')) {
            paginationWrapper = document.createElement('div');
            paginationWrapper.className = 'pagination-wrapper';
            paginationWrapper.style.marginTop = '25px';
            grid.parentNode.appendChild(paginationWrapper);
        }
        
        let currentPage = 1;
        
        function updateUlasanPagination() {
            const allCards = Array.from(grid.querySelectorAll('.ulasan-card'));
            const eligibleCards = allCards.filter(function(card) {
                return card.dataset.searchMatch !== 'false';
            });
            
            if (eligibleCards.length <= ROWS_PER_PAGE) {
                allCards.forEach(function(card) {
                    if (eligibleCards.includes(card)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
                paginationWrapper.style.display = 'none';
                return;
            }
            
            paginationWrapper.style.display = 'flex';
            const totalPages = Math.ceil(eligibleCards.length / ROWS_PER_PAGE);
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;
            
            const startIdx = (currentPage - 1) * ROWS_PER_PAGE;
            const endIdx = startIdx + ROWS_PER_PAGE;
            
            allCards.forEach(function(card) {
                if (eligibleCards.includes(card)) {
                    const idx = eligibleCards.indexOf(card);
                    card.style.display = (idx >= startIdx && idx < endIdx) ? '' : 'none';
                } else {
                    card.style.display = 'none';
                }
            });
            
            const startDisplay = startIdx + 1;
            const endDisplay = Math.min(endIdx, eligibleCards.length);
            
            let html = '<span class="pagination-info">Menampilkan ' + startDisplay + ' - ' + endDisplay + ' dari ' + eligibleCards.length + ' ulasan</span>';
            html += '<div class="pagination-nav">';
            
            html += '<button type="button" class="pagination-btn ' + (currentPage === 1 ? 'disabled' : '') + '" data-page="' + (currentPage - 1) + '"><i class="fa-solid fa-chevron-left"></i></button>';
            
            const maxVisible = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);
            if (endPage - startPage < maxVisible - 1) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }
            
            if (startPage > 1) {
                html += '<button type="button" class="pagination-btn" data-page="1">1</button>';
                if (startPage > 2) html += '<span class="pagination-info" style="padding: 0 4px;">...</span>';
            }
            
            for (let i = startPage; i <= endPage; i++) {
                html += '<button type="button" class="pagination-btn ' + (i === currentPage ? 'active' : '') + '" data-page="' + i + '">' + i + '</button>';
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) html += '<span class="pagination-info" style="padding: 0 4px;">...</span>';
                html += '<button type="button" class="pagination-btn" data-page="' + totalPages + '">' + totalPages + '</button>';
            }
            
            html += '<button type="button" class="pagination-btn ' + (currentPage === totalPages ? 'disabled' : '') + '" data-page="' + (currentPage + 1) + '"><i class="fa-solid fa-chevron-right"></i></button>';
            html += '</div>';
            
            paginationWrapper.innerHTML = html;
            
            paginationWrapper.querySelectorAll('.pagination-btn:not(.disabled)').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const page = parseInt(this.getAttribute('data-page'));
                    if (page >= 1 && page <= totalPages) {
                        currentPage = page;
                        updateUlasanPagination();
                    }
                });
            });
        }
        
        updateUlasanPagination();
        
        grid.addEventListener('ulasanFiltered', function() {
            currentPage = 1;
            updateUlasanPagination();
        });
    });
});
        

    
