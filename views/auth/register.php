<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/auth_helper.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php?action=home");
    exit;
}
$judulHalaman = 'Registrasi Pelanggan - SPAdmin Spa Bandung';
$bodyClass = 'auth-page register-auth-page';
?>
<?php include __DIR__ . '/../user/templates/header.php'; ?>
    <section class="register-auth-wrap">
        <div class="register-auth-shell register-compact-shell">
            <div class="register-form-panel">
                <p class="eyebrow">Daftar akun baru</p>
                <h2>Buat Akun Pelanggan</h2>
                <p class="register-form-intro">Isi data singkat di bawah ini untuk mulai reservasi dan memantau pembayaran layanan Anda.</p>
            <?php if (isset($pesanError)): ?>
                <div class="pesan-error"><?= e($pesanError) ?></div>
            <?php endif; ?>
            <form method="POST" action="index.php?action=register" class="register-form" novalidate onsubmit="return validateRegisterForm(event)">
                <div class="form-group register-field">
                    <label for="nama">Nama Lengkap</label>
                    <div class="register-input-wrap">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <input type="text" id="nama" name="nama" value="<?= e($_POST['nama'] ?? '') ?>" required autofocus maxlength="100" pattern="^[A-Za-z\s']+$" title="Nama hanya boleh berisi huruf, spasi, dan tanda petik tunggal" placeholder="Masukkan nama lengkap">
                    </div>
                </div>
                <div class="register-two-fields">
                    <div class="form-group register-field">
                        <label for="email">Email</label>
                        <div class="register-input-wrap">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>
                            <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required placeholder="nama@email.com">
                        </div>
                    </div>
                    <div class="form-group register-field">
                        <label for="telepon">Nomor Telepon</label>
                        <div class="register-input-wrap">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.79 19.79 0 0 1 11.2 18.8a19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72c.12.9.32 1.77.6 2.61a2 2 0 0 1-.45 2.11L8 9.64a16 16 0 0 0 6.36 6.36l1.2-1.2a2 2 0 0 1 2.11-.45c.84.28 1.71.48 2.61.6A2 2 0 0 1 22 16.92Z"></path></svg>
                            <input type="tel" id="telepon" name="telepon" value="<?= e($_POST['telepon'] ?? '') ?>" required inputmode="numeric" pattern="[0-9]{8,13}" title="Nomor telepon harus berupa angka antara 8 sampai 13 digit" oninput="this.value = this.value.replace(/[^0-9]/g, '');" placeholder="0812345678">
                        </div>
                    </div>
                </div>
                <div class="register-password-stack">
                    <div class="form-group register-field">
                        <label for="password">Password</label>
                        <div class="register-input-wrap">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="11" width="18" height="10" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            <input type="password" id="password" name="password" required placeholder="Minimal 6 karakter">
                        </div>
                    </div>
                    <div class="form-group register-field">
                        <label for="konfirmasi_password">Konfirmasi Password</label>
                        <div class="register-input-wrap">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>
                            <input type="password" id="konfirmasi_password" name="konfirmasi_password" required placeholder="Ulangi password">
                        </div>
                    </div>
                </div>
                <button type="submit" class="register-submit">Daftar Sekarang</button>
            </form>
            <script>
            function validateRegisterForm(e) {
                var nama = document.getElementById('nama').value.trim();
                var email = document.getElementById('email').value.trim();
                var telepon = document.getElementById('telepon').value.trim();
                var password = document.getElementById('password').value.trim();
                var konfirmasi = document.getElementById('konfirmasi_password').value.trim();
                
                var errDiv = document.querySelector('.pesan-error');
                if (!errDiv) {
                    errDiv = document.createElement('div');
                    errDiv.className = 'pesan-error';
                    var form = document.querySelector('.register-form');
                    form.parentNode.insertBefore(errDiv, form);
                }
                
                if (!nama || !email || !telepon || !password || !konfirmasi) {
                    e.preventDefault();
                    errDiv.textContent = "nama,email dan password wajib di isi";
                    errDiv.style.display = 'block';
                    if (!nama) document.getElementById('nama').focus();
                    else if (!email) document.getElementById('email').focus();
                    else if (!telepon) document.getElementById('telepon').focus();
                    else if (!password) document.getElementById('password').focus();
                    else document.getElementById('konfirmasi_password').focus();
                    return false;
                }
                return true;
            }
            </script>
            <p class="teks-auth">Sudah punya akun? <a href="index.php?action=login">Login di sini</a></p>
            </div>

            <div class="register-auth-story">
                <div class="register-brand-mark">
                    <img src="assets/images/logo_spadmin.png" alt="SPAdmin Wellness">
                    <span>SPADMIN <em>Wellness</em></span>
                </div>
                <div class="register-story-copy">
                    <h1>Ruang tenang untuk jadwal Anda.</h1>
                    <p>Akun pelanggan membantu reservasi terasa lebih singkat, rapi, dan mudah dilacak.</p>
                </div>
            </div>
        </div>
    </section>
<?php include __DIR__ . '/../user/templates/footer.php'; ?>
