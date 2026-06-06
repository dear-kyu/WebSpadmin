
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SPADMIN SPA Administration System</title>
    
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    
    <link rel="stylesheet" href="assets/admin/css/style.css">
</head>
<body class="login-body">

    <div class="login-card">
        <div style="display: flex; justify-content: center; margin-bottom: 15px;">
            <img src="assets/images/logo_spadmin.png" alt="Logo SPAdmin" style="width: 64px; height: 64px; object-fit: contain; border-radius: 50%;">
        </div>
        <div class="login-logo">SPADMIN</div>
        <div class="login-subtitle">SPA Administration System</div>
        
        
        <?php if (!empty($error)): ?>
            <div class="error-alert">
                <i class="fa-solid fa-triangle-exclamation mr-2"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form action="admin.php?page=login" method="POST">
            <div class="form-group-glass">
                <label for="username">Email / Username Admin</label>
                <input type="text" id="username" name="username" class="form-control-glass" placeholder="Masukkan email atau username..." required autocomplete="off">
            </div>
            
            <div class="form-group-glass">
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" class="form-control-glass" placeholder="Masukkan password..." required>
            </div>
            
            <button type="submit" class="btn-glass">
                <i class="fa-solid fa-right-to-bracket mr-2"></i> Masuk Sistem
            </button>
        </form>
        
        <div style="margin-top: 30px; font-size: 0.75rem; color: rgba(255, 255, 255, 0.4); line-height: 1.4;">
            &copy; <?php echo date('Y'); ?> SPADMIN &bull; SPA Administration System
        </div>
    </div>

</body>
</html>
