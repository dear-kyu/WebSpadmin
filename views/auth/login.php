<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/auth_helper.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php?action=home");
    exit;
}
$judulHalaman = 'Login Pelanggan - SPAdmin Spa Bandung';
$bodyClass = 'auth-page login-auth-page';
?>
<?php include __DIR__ . '/../user/templates/header.php'; ?>
    <section class="login-auth-wrap">
        <div class="login-auth-shell">
            <div class="login-auth-story">
                <div class="register-brand-mark">
                    <img src="assets/images/logo_spadmin.png" alt="SPAdmin Wellness">
                    <span>SPADMIN <em>Wellness</em></span>
                </div>
                <div class="login-story-copy">
                    <h1>Selamat datang kembali.</h1>
                    <p>Lanjutkan reservasi, cek riwayat, dan pantau pembayaran layanan wellness Anda.</p>
                </div>
            </div>

            <div class="login-form-panel">
                <p class="eyebrow">Masuk akun pelanggan</p>
                <h2>Login Pelanggan</h2>
                <p class="login-form-intro">Masuk untuk melanjutkan reservasi dan melihat riwayat layananmu.</p>
            <?php if (isset($pesanSukses)): ?>
                <div class="pesan-sukses"><?= e($pesanSukses) ?></div>
            <?php endif; ?>
            <?php if (isset($pesanError)): ?>
                <div class="pesan-error"><?= e($pesanError) ?></div>
            <?php endif; ?>
            <form method="POST" action="index.php?action=login<?= isset($_GET['next']) ? '&next=' . urlencode($_GET['next']) : '' ?>" class="login-form" novalidate onsubmit="return validateLoginForm(event)">
                <div class="form-group register-field">
                    <label for="email">Email</label>
                    <div class="register-input-wrap">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>
                        <input type="email" id="email" name="email" placeholder="nama@email.com" value="<?= e($_POST['email'] ?? '') ?>" required autofocus>
                    </div>
                </div>
                <div class="form-group register-field">
                    <label for="password">Password</label>
                    <div class="register-input-wrap">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="11" width="18" height="10" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                </div>
                <button type="submit" class="register-submit">Masuk</button>
            </form>
            <script>
            function validateLoginForm(e) {
                var email = document.getElementById('email').value.trim();
                var password = document.getElementById('password').value.trim();
                
                var errDiv = document.querySelector('.pesan-error');
                if (!errDiv) {
                    errDiv = document.createElement('div');
                    errDiv.className = 'pesan-error';
                    var form = document.querySelector('.login-form');
                    form.parentNode.insertBefore(errDiv, form);
                }
                
                if (!email || !password) {
                    e.preventDefault();
                    errDiv.textContent = "nama,email dan password wajib di isi";
                    errDiv.style.display = 'block';
                    if (!email) document.getElementById('email').focus();
                    else document.getElementById('password').focus();
                    return false;
                }
                return true;
            }
            </script>
            <p class="teks-auth">Belum punya akun? <a href="index.php?action=register">Daftar pelanggan</a></p>
            </div>
        </div>
    </section>
<?php include __DIR__ . '/../user/templates/footer.php'; ?>
