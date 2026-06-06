
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPADMIN – SPA Administration System</title>
    
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    
    <link rel="stylesheet" href="assets/admin/css/style.css">
</head>
<body>
    <div class="wrapper">
        
        <?php include_once __DIR__ . '/sidebar.php'; ?>
        
        <div class="main-content">
            
            <header class="top-navbar">
                <div class="navbar-left">
                    <h2 class="navbar-title">
                        <?php 
                        switch ($page) {
                            case 'dashboard': echo 'Dashboard'; break;
                            case 'terapis': echo 'Terapis'; break;
                            case 'layanan': echo 'Layanan'; break;
                            case 'reservasi': echo 'Reservasi'; break;
                            case 'pembayaran': echo 'Pembayaran'; break;
                            case 'transaksi': echo (($action ?? '') === 'create' ? 'POS Walk-In' : 'Transaksi & POS'); break;
                            case 'laporan': echo 'Laporan'; break;
                            case 'ulasan': echo 'Ulasan'; break;
                            default: echo 'SPADMIN';
                        }
                        ?>
                    </h2>
                </div>
                <div class="navbar-right" style="display: flex; align-items: center; gap: 15px;">
                    <?php if (($page ?? '') === 'dashboard'): ?>
                        <!-- Welcome Section (Dashboard Only) -->
                        <div style="display: flex; align-items: center; gap: 20px;">
                            <div style="text-align: right;">
                                <div style="font-size: 1rem; font-weight: 700; color: var(--primary); line-height: 1.2;">
                                    Halo, <?php echo htmlspecialchars(trim(explode('(', $_SESSION['adminNama'] ?? 'Admin')[0])); ?> 👋
                                </div>
                                <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 1px;">
                                    Selamat Datang di SPADMIN
                                </div>
                            </div>
                            <div class="navbar-date" style="flex-shrink: 0;">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span><?php echo date('d M Y'); ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Date Only (Other Pages) -->
                        <div class="navbar-date">
                            <i class="fa-solid fa-calendar-days"></i>
                            <span><?php echo date('d M Y'); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </header>
            
            
            <main class="content-body">
