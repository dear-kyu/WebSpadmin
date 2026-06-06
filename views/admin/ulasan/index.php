<style>
.ulasan-body, .ulasan-reply-body {
    word-break: break-all !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
}
</style>



<!-- Search Bar -->
<div class="panel" style="margin-bottom: 20px;">
    <div class="panel-body" style="padding: 14px 20px;">
        <div style="position: relative; max-width: 420px;">
            <i class="fa-solid fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
            <input type="text" id="searchUlasan" placeholder="Cari nama pelanggan, layanan, atau ulasan..."
                   style="width: 100%; padding: 9px 12px 9px 36px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); outline: none; background: var(--bg-light); color: var(--text-dark); font-size: 0.87rem; box-sizing: border-box;"
                   oninput="filterUlasan(this.value)">
        </div>
    </div>
</div>


<div class="panel">
    <div class="panel-header">
        <h3 class="panel-title">Kelola Rating &amp; Ulasan Layanan Pengguna</h3>
    </div>
    <div class="panel-body">
        <?php if (empty($ulasanList)): ?>
            <div style="text-align: center; padding: 50px; color: var(--text-muted);">
                <i class="fa-solid fa-star-half-stroke mb-2 d-block fs-1"></i> Belum ada ulasan / rating yang diberikan pelanggan.
            </div>
        <?php else: ?>
            <div class="ulasan-grid" data-admin-paginate data-item-selector=".ulasan-card" data-per-page="6" data-noun="data">
                <?php foreach ($ulasanList as $ul): ?>
                    <?php 
                    $idUlasan = $ul['idUlasan'] ?? $ul['id_ulasan']; 
                    $namaPelanggan = $ul['namaPelanggan'] ?? $ul['namaPelanggan'];
                    $emailPelanggan = $ul['emailPelanggan'] ?? $ul['email_pelanggan'];
                    $balasanAdmin = $ul['balasanAdmin'] ?? $ul['balasan_admin'];
                    $namaLayanan = $ul['namaLayanan'] ?? $ul['nama_layanan'];
                    $createdAt = $ul['createdAt'] ?? $ul['created_at'];
                    ?>
                    <div class="ulasan-card">
                        <div>
                            
                            <div class="ulasan-header">
                                <div class="ulasan-user">
                                    <h4><?php echo htmlspecialchars($namaPelanggan); ?></h4>
                                    <span><?php echo htmlspecialchars($emailPelanggan); ?></span>
                                </div>
                                <div class="ulasan-rating">
                                    <?php 
                                    $ratingVal = intval($ul['rating']);
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $ratingVal) {
                                            echo '<i class="fa-solid fa-star"></i>';
                                        } else {
                                            echo '<i class="fa-regular fa-star"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            
                            
                            <p class="ulasan-body">
                                "<?php echo htmlspecialchars($ul['ulasan']); ?>"
                            </p>

                            
                            <?php if (!empty($balasanAdmin)): ?>
                                <div class="ulasan-reply-box">
                                    <div class="ulasan-reply-header">
                                        <span><i class="fa-solid fa-reply"></i> Balasan Admin</span>
                                    </div>
                                    <div class="ulasan-reply-body">
                                        <?php echo htmlspecialchars($balasanAdmin); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            
                            <div class="ulasan-reply-form-container" id="replyForm-<?php echo $idUlasan; ?>" style="display: none;">
                                <form action="admin.php?page=ulasan&action=balas" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $idUlasan; ?>">
                                    <div class="form-group" style="margin-bottom: 10px;">
                                        <textarea name="balasan" class="form-control" style="min-height: 70px; padding: 8px 12px; font-size: 0.88rem;" placeholder="Tulis balasan ulasan di sini..." required><?php echo htmlspecialchars($balasanAdmin ?? ''); ?></textarea>
                                    </div>
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <button type="button" class="btn-spa btn-spa-outline" style="font-size: 0.75rem; padding: 4px 10px;" onclick="toggleReplyForm(<?php echo $idUlasan; ?>)">Batal</button>
                                        <button type="submit" class="btn-spa btn-spa-accent" style="font-size: 0.75rem; padding: 4px 10px;"><i class="fa-solid fa-paper-plane"></i> Kirim</button>
                                    </div>
                                </form>
                            </div>
                            
                            <div style="margin-top: 12px; margin-bottom: 2px;">
                                <span class="ulasan-service">
                                    <i class="fa-solid fa-spa" style="font-size: 0.75rem; margin-right: 4px;"></i> <?php echo htmlspecialchars($namaLayanan); ?>
                                </span>
                            </div>
                        </div>
                        
                        
                        <div class="ulasan-footer">
                            <span style="font-size: 0.72rem; color: var(--text-muted);"><?php echo date('d-m-Y', strtotime($createdAt)); ?></span>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <button class="btn-icon" 
                                        style="width: 28px; height: 28px; font-size: 0.8rem;" 
                                        onclick="toggleReplyForm(<?php echo $idUlasan; ?>)" 
                                        title="<?php echo empty($balasanAdmin) ? 'Balas Ulasan' : 'Edit Balasan'; ?>">
                                    <i class="fa-solid fa-comment-dots"></i>
                                </button>

                                <a href="admin.php?page=ulasan&action=delete&id=<?php echo $idUlasan; ?>" 
                                   class="btn-icon btn-icon-danger btn-confirm-delete" 
                                   style="width: 28px; height: 28px; font-size: 0.8rem;"
                                   data-message="Apakah Anda yakin ingin menghapus / melakukan moderasi pada ulasan dari pelanggan: <?php echo htmlspecialchars($namaPelanggan); ?>? Tindakan ini tidak dapat dibatalkan."
                                   title="Hapus / Moderasi Ulasan">
                                     <i class="fa-solid fa-trash-can"></i>
                                 </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleReplyForm(id) {
    var formContainer = document.getElementById('replyForm-' + id);
    if (formContainer.style.display === 'none' || formContainer.style.display === '') {
        formContainer.style.display = 'block';
    } else {
        formContainer.style.display = 'none';
    }
}
</script>
