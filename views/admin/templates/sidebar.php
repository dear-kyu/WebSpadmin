
<?php
$currentPage = isset($_GET['page']) ? trim($_GET['page']) : 'dashboard';
$currentAction = isset($_GET['action']) ? trim($_GET['action']) : '';
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="assets/images/logo_spadmin.png" alt="Logo SPAdmin" class="sidebar-logo-img" style="background: none; object-fit: contain;">
        <div class="sidebar-brand-text">SPADMIN</div>
    </div>
    
    <ul class="sidebar-menu">
        <li class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
            <a href="admin.php?page=dashboard">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="<?php echo $currentPage === 'terapis' ? 'active' : ''; ?>">
            <a href="admin.php?page=terapis">
                <i class="fa-solid fa-user-doctor"></i>
                <span>Terapis</span>
            </a>
        </li>
        <li class="<?php echo $currentPage === 'layanan' ? 'active' : ''; ?>">
            <a href="admin.php?page=layanan">
                <i class="fa-solid fa-spa"></i>
                <span>Layanan</span>
            </a>
        </li>
        <li class="<?php echo $currentPage === 'ruangan' ? 'active' : ''; ?>">
            <a href="admin.php?page=ruangan">
                <i class="fa-solid fa-door-open"></i>
                <span>Ruangan</span>
            </a>
        </li>
        <li class="<?php echo $currentPage === 'reservasi' ? 'active' : ''; ?>">
            <a href="admin.php?page=reservasi">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Reservasi</span>
            </a>
        </li>
        <li class="<?php echo $currentPage === 'transaksi' ? 'active' : ''; ?>">
            <a href="admin.php?page=transaksi">
                <i class="fa-solid fa-list-check"></i>
                <span>Transaksi & POS</span>
            </a>
        </li>
        <li class="<?php echo $currentPage === 'laporan' ? 'active' : ''; ?>">
            <a href="admin.php?page=laporan">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>Laporan</span>
            </a>
        </li>
        <li class="<?php echo $currentPage === 'ulasan' ? 'active' : ''; ?>">
            <a href="admin.php?page=ulasan">
                <i class="fa-solid fa-star-half-stroke"></i>
                <span>Ulasan</span>
            </a>
        </li>
        <li class="<?php echo $currentPage === 'pengaturan' ? 'active' : ''; ?>">
            <a href="admin.php?page=pengaturan">
                <i class="fa-solid fa-sliders"></i>
                <span>Pengaturan</span>
            </a>
        </li>
    </ul>
    
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <div class="sidebar-user-info">
                <h5><?php echo htmlspecialchars($_SESSION['adminNama'] ?? 'Admin SPADMIN'); ?></h5>
                <span>Role: Administrator</span>
            </div>
        </div>
        <a href="admin.php?page=logout" class="btn-logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>
