
            </main> 
            
            
            <footer class="footer-credits">
                <p>&copy; <?php echo date('Y'); ?> <strong>SPADMIN (SPA Administration System)</strong>. All rights reserved.</p>
            </footer>
        </div> 
    </div> 
    
    
    <script src="assets/admin/js/main.js"></script>
    <script>
        // Generic table filter function with "Data tidak ditemukan" message
        function filterTable(inputId, tableSelector) {
            const input = document.getElementById(inputId);
            if (!input) return;
            const filter = input.value.toLowerCase();
            const table = document.querySelector(tableSelector || '.custom-table');
            if (!table) return;
            
            const tbody = table.querySelector('tbody');
            if (!tbody) return;
            
            // Hapus baris hasil tidak ditemukan yang ada sebelumnya
            const existingNoResults = tbody.querySelector('.no-results-row');
            if (existingNoResults) {
                existingNoResults.remove();
            }
            
            const thCount = table.querySelectorAll('thead th').length || 10;
            let visibleCount = 0;
            
            const rows = tbody.querySelectorAll('tr:not(.no-results-row)');
            rows.forEach(tr => {
                // Cek baris kosong / placeholder default
                const isPlaceholder = tr.querySelector('td[colspan]');
                if (isPlaceholder) {
                    if (filter !== '') {
                        tr.style.display = 'none';
                        tr.dataset.searchMatch = 'false';
                    } else {
                        tr.style.display = '';
                        tr.dataset.searchMatch = 'true';
                        visibleCount++;
                    }
                    return;
                }
                
                const text = tr.textContent.toLowerCase();
                const matches = text.includes(filter);
                tr.style.display = matches ? '' : 'none';
                tr.dataset.searchMatch = matches ? 'true' : 'false';
                if (matches) {
                    visibleCount++;
                }
            });
            
            // Jika tidak ada data yang cocok dengan filter pencarian
            if (visibleCount === 0 && filter !== '') {
                const noResultsTr = document.createElement('tr');
                noResultsTr.className = 'no-results-row';
                noResultsTr.innerHTML = `
                    <td colspan="${thCount}" style="text-align: center; padding: 30px; color: var(--text-muted); background-color: var(--bg-light);">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; justify-content: center;">
                            <i class="fa-solid fa-magnifying-glass-minus" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                            <span>Data "${input.value}" tidak ditemukan</span>
                        </div>
                    </td>
                `;
                tbody.appendChild(noResultsTr);
            }

            table.dispatchEvent(new CustomEvent('adminListFiltered'));
        }

        function filterTerapis(val)    { filterTable('searchTerapis'); }
        function filterLayanan(val)    { filterTable('searchLayanan'); }
        function filterReservasi(val)  { filterTable('searchReservasi'); }
        function filterPembayaran(val) { filterTable('searchPembayaran'); }
        function filterTransaksi(val)  { filterTable('searchTransaksi'); }
        
        // Ulasan filter function with cards placeholder
        function filterUlasan(val) {
            const input = document.getElementById('searchUlasan');
            if (!input) return;
            const filter = input.value.toLowerCase();
            const grid = document.querySelector('.ulasan-grid');
            if (!grid) return;
            
            const existingNoResults = grid.querySelector('.no-ulasan-results');
            if (existingNoResults) {
                existingNoResults.remove();
            }
            
            let visibleCount = 0;
            const cards = grid.querySelectorAll('.ulasan-card');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                const matches = text.includes(filter);
                card.style.display = matches ? '' : 'none';
                card.dataset.searchMatch = matches ? 'true' : 'false';
                if (matches) {
                    visibleCount++;
                }
            });
            
            if (visibleCount === 0 && filter !== '') {
                const noResultsDiv = document.createElement('div');
                noResultsDiv.className = 'no-ulasan-results';
                noResultsDiv.style.cssText = 'grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-muted); background: var(--bg-light); border-radius: var(--radius-md); border: 1px dashed var(--border-color);';
                noResultsDiv.innerHTML = `
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; justify-content: center;">
                        <i class="fa-solid fa-magnifying-glass-minus" style="font-size: 1.8rem; color: var(--text-muted);"></i>
                        <span style="font-weight: 500;">Ulasan "${input.value}" tidak ditemukan</span>
                    </div>
                `;
                grid.appendChild(noResultsDiv);
            }

            grid.dispatchEvent(new CustomEvent('adminListFiltered'));
        }

        function initAdminListPagination() {
            const lists = document.querySelectorAll('[data-admin-paginate]');
            lists.forEach(list => {
                if (list.dataset.paginationReady === 'true') return;
                list.dataset.paginationReady = 'true';

                const itemSelector = list.dataset.itemSelector || 'tbody tr';
                const perPage = parseInt(list.dataset.perPage || '6', 10);
                const noun = list.dataset.noun || 'data';
                let currentPage = 1;

                const footer = document.createElement('div');
                footer.className = 'admin-list-pagination';
                footer.style.cssText = 'display:flex; justify-content:space-between; align-items:center; gap:12px; padding:15px 20px; border-top:1px solid var(--border-color); background-color:var(--bg-card); flex-wrap:wrap;';

                const info = document.createElement('span');
                info.style.cssText = 'font-size:0.85rem; color:var(--text-muted); font-weight:500;';

                const nav = document.createElement('div');
                nav.style.cssText = 'display:flex; align-items:center; gap:6px; flex-wrap:wrap;';

                footer.appendChild(info);
                footer.appendChild(nav);

                const panelBody = list.closest('.panel-body');
                if (panelBody) {
                    panelBody.appendChild(footer);
                } else {
                    list.insertAdjacentElement('afterend', footer);
                }

                function getItems() {
                    return Array.from(list.querySelectorAll(itemSelector)).filter(item => {
                        if (item.classList.contains('no-results-row') || item.classList.contains('no-ulasan-results')) return false;
                        if (item.hasAttribute('data-pagination-summary')) return false;
                        if (item.querySelector && item.querySelector('td[colspan]')) return false;
                        return true;
                    });
                }

                function getVisibleItems(items) {
                    return items.filter(item => item.dataset.searchMatch !== 'false');
                }

                function renderButton(label, page, isActive, disabled, iconClass) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'pos-pagination-btn' + (isActive ? ' active' : '') + (disabled ? ' disabled' : '');
                    button.style.cssText = 'padding:6px 10px; min-width:30px; height:30px; border:1px solid var(--border-color); background:' + (isActive ? 'var(--accent)' : 'var(--bg-light)') + '; color:' + (isActive ? 'white' : 'var(--text-dark)') + '; border-radius:6px; cursor:' + (disabled ? 'not-allowed' : 'pointer') + '; font-size:0.82rem; font-weight:600; display:inline-flex; align-items:center; justify-content:center;';
                    button.disabled = disabled;
                    button.innerHTML = iconClass ? '<i class="' + iconClass + '"></i>' : label;
                    button.addEventListener('click', function() {
                        if (!disabled) showPage(page);
                    });
                    nav.appendChild(button);
                }

                function showPage(page) {
                    const items = getItems();
                    const visibleItems = getVisibleItems(items);
                    const totalItems = visibleItems.length;
                    const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
                    currentPage = Math.min(Math.max(page, 1), totalPages);

                    const startIdx = (currentPage - 1) * perPage;
                    const endIdx = Math.min(startIdx + perPage, totalItems);

                    items.forEach(item => {
                        const visibleIndex = visibleItems.indexOf(item);
                        item.style.display = (visibleIndex >= startIdx && visibleIndex < endIdx) ? '' : 'none';
                    });

                    if (totalItems > 0) {
                        info.textContent = 'Menampilkan ' + (startIdx + 1) + ' - ' + endIdx + ' dari ' + totalItems + ' ' + noun;
                    } else {
                        info.textContent = 'Menampilkan 0 - 0 dari 0 ' + noun;
                    }

                    nav.innerHTML = '';
                    renderButton('', currentPage - 1, false, currentPage === 1, 'fa-solid fa-chevron-left');
                    for (let i = 1; i <= totalPages; i++) {
                        renderButton(String(i), i, i === currentPage, false, '');
                    }
                    renderButton('', currentPage + 1, false, currentPage === totalPages || totalItems === 0, 'fa-solid fa-chevron-right');

                    footer.style.display = items.length > 0 ? 'flex' : 'none';
                }

                list.addEventListener('adminListFiltered', function() {
                    showPage(1);
                });

                showPage(1);
            });
        }

        document.addEventListener('DOMContentLoaded', initAdminListPagination);
    </script>

    <?php if (!empty($success) || !empty($error)): ?>
        <!-- Global Success/Error Notification Modal -->
        <div id="globalNotificationOverlay" style="position: fixed; inset: 0; z-index: 9999; background: rgba(30, 25, 22, 0.45); display: flex; align-items: center; justify-content: center; padding: 20px;">
            <div style="width: min(420px, 100%); background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); box-shadow: 0 8px 32px rgba(0,0,0,0.18); overflow: hidden; animation: popupFadeIn 0.18s ease;">
                <div style="padding: 14px 18px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
                    <?php if (!empty($success)): ?>
                        <span style="width: 32px; height: 32px; border-radius: 8px; background: var(--success-bg); color: var(--success); display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fa-solid fa-circle-check"></i>
                        </span>
                        <strong style="color: var(--primary); font-size: 0.95rem;">Sukses</strong>
                    <?php else: ?>
                        <span style="width: 32px; height: 32px; border-radius: 8px; background: var(--danger-bg); color: var(--danger); display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fa-solid fa-circle-exclamation"></i>
                        </span>
                        <strong style="color: var(--primary); font-size: 0.95rem;">Peringatan</strong>
                    <?php endif; ?>
                </div>
                <div style="padding: 16px 18px; color: var(--text-dark); font-size: 0.9rem; line-height: 1.6;">
                    <p style="margin: 0;"><?php echo htmlspecialchars($success ?? $error); ?></p>
                </div>
                <div style="padding: 0 18px 16px; display: flex; justify-content: flex-end;">
                    <button type="button" class="btn-spa btn-spa-accent" onclick="closeGlobalNotification()" style="padding: 8px 20px;">OK</button>
                </div>
            </div>
        </div>
        <script>
        function closeGlobalNotification() {
            var overlay = document.getElementById('globalNotificationOverlay');
            if (overlay) overlay.style.display = 'none';
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeGlobalNotification();
        });
        document.addEventListener('click', function(e) {
            var overlay = document.getElementById('globalNotificationOverlay');
            if (overlay && e.target === overlay) closeGlobalNotification();
        });
        </script>
    <?php endif; ?>
</body>
</html>
