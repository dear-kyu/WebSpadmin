<?php
$judulHalaman = 'Profil Pelanggan - SPAdmin Spa Bandung';
$bodyClass = 'profile-soft-page';
$namaPelanggan = trim($user['nama'] ?? ($_SESSION['nama'] ?? 'Pelanggan'));
?>
<?php include __DIR__ . '/../templates/header.php'; ?>

<?php if (($_GET['pesan'] ?? '') === 'profil-berhasil'): ?>
    <div class="floating-alert success" id="floatingAlert" role="status" aria-live="polite">
        <span class="alert-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
        </span>
        <span class="alert-message">Profil berhasil diperbarui.</span>
        <button class="alert-close" type="button" onclick="closeFloatingAlert()" aria-label="Tutup notifikasi">&times;</button>
    </div>
<?php endif; ?>

<style>
    body.profile-soft-page {
        background: #f3e8dc;
    }

    .profile-page {
        color: #3f3028;
        background:
            radial-gradient(circle at 70% 8%, rgba(255, 253, 247, 0.82) 0 11rem, rgba(255, 253, 247, 0) 18rem),
            linear-gradient(180deg, #f3e8dc 0%, #f6ece1 48%, #f3e8dc 100%);
        overflow: hidden;
    }

    /* ── HERO ────────────────────────────────────────────── */
    .profile-hero {
        position: relative;
        min-height: 220px;
        background: #f3e8dc;
        overflow: hidden;
        display: flex;
        align-items: center;
    }

    /* Right-side organic blob with spa photo */
    .profile-hero-blob {
        position: absolute;
        top: 0;
        right: 0;
        width: 52%;
        height: 100%;
        overflow: hidden;
        pointer-events: none;
        z-index: 0;
    }

    .profile-hero-blob::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url('assets/images/profile-hero-new.png') center / cover no-repeat;
        clip-path: ellipse(90% 100% at 100% 50%);
        transform: scaleX(1);
    }

    .profile-hero-inner {
        position: relative;
        z-index: 2;
        width: min(1180px, calc(100% - 2rem));
        margin: 0 auto;
        padding: 2.8rem 0 5.2rem;
    }

    /* Greeting — elegant italic serif */
    .profile-greeting {
        color: #d4698a;
        font-family: 'Cormorant Garamond', 'Cormorant', Georgia, serif;
        font-style: italic;
        font-size: clamp(1.45rem, 2.7vw, 1.95rem);
        line-height: 1.1;
        margin: 0 0 0.45rem;
        font-weight: 500;
        letter-spacing: 0.01em;
    }

    /* Title: bold serif + italic serif script */
    .profile-title {
        display: flex;
        align-items: baseline;
        gap: 0.5rem;
        margin: 0;
        color: #2d4a36;
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(3rem, 5.8vw, 4.9rem);
        font-weight: 700;
        line-height: 1;
        letter-spacing: -0.01em;
    }

    .profile-title-script {
        color: #d4698a;
        font-family: 'Cormorant Garamond', 'Cormorant', Georgia, serif;
        font-style: italic;
        font-size: 1em;
        font-weight: 500;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        gap: 0.22rem;
    }

    .profile-title-script svg {
        width: 0.9em;
        height: 0.9em;
        color: #d4698a;
        flex-shrink: 0;
        margin-bottom: -0.08em;
    }

    /* Subtitle */
    .profile-subtitle {
        max-width: 360px;
        margin: 1.25rem 0 0;
        color: #7a6b64;
        font-family: 'Inter', sans-serif;
        font-size: 1.08rem;
        line-height: 1.68;
        font-weight: 500;
    }

    /* ── SHELL / CARD ────────────────────────────────────── */
    .profile-shell {
        position: relative;
        z-index: 2;
        width: min(1180px, calc(100% - 2rem));
        margin: -1.8rem auto 3.2rem;
    }

    .profile-card {
        position: relative;
        overflow: hidden;
        padding: clamp(1.4rem, 3vw, 2.4rem) clamp(1.6rem, 4vw, 3rem);
        background: rgba(255, 253, 250, 0.98);
        border: 1px solid rgba(190, 166, 145, 0.22);
        border-radius: 18px;
        box-shadow: 0 18px 52px rgba(89, 65, 48, 0.08);
    }

    .profile-card::after {
        content: '';
        position: absolute;
        right: -72px;
        bottom: -98px;
        width: 250px;
        height: 250px;
        background: url("assets/images/riwayat-cta-leaf.png") center / contain no-repeat;
        opacity: 0.06;
        pointer-events: none;
    }

    .profile-card-head {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2.2rem;
        position: relative;
        z-index: 1;
    }

    .profile-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #d4698a;
        background: #fbedf1;
        flex-shrink: 0;
    }

    .profile-card-title {
        margin: 0;
        color: #2d2420;
        font-family: 'Inter', 'Playfair Display', Georgia, serif;
        font-size: clamp(1.2rem, 2.2vw, 1.65rem);
        font-weight: 700;
        flex: 1;
    }

    .profile-card-kicker {
        margin-left: auto;
        color: #d4698a;
        font-family: 'Inter', sans-serif;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid rgba(212, 105, 138, 0.4);
        white-space: nowrap;
    }

    .profile-alert {
        position: relative;
        z-index: 1;
        border-radius: 14px;
        padding: 0.85rem 1rem;
        margin-bottom: 1.25rem;
        font-size: 0.88rem;
        font-weight: 700;
    }

    .profile-alert.success {
        background: #edf6e9;
        color: #2f6040;
        border: 1px solid rgba(47, 96, 64, 0.12);
    }

    .profile-alert.error {
        background: #fff0f3;
        color: #b3405b;
        border: 1px solid rgba(214, 104, 129, 0.16);
    }

    .profile-form {
        position: relative;
        z-index: 1;
    }

    .profile-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 2.1rem 3.6rem;
    }

    .profile-field label {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        color: #67564c;
        font-size: 0.88rem;
        font-weight: 700;
        margin-bottom: 0.7rem;
    }

    .profile-field label::before {
        content: '';
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #d66881;
        box-shadow: 0 0 0 3px rgba(214, 104, 129, 0.08);
    }

    .profile-input-wrap {
        position: relative;
    }

    .profile-input-icon {
        position: absolute;
        top: 50%;
        left: 1.15rem;
        transform: translateY(-50%);
        color: #d66881;
        pointer-events: none;
    }

    .profile-input-wrap .password-toggle {
        position: absolute;
        top: 50%;
        right: 1rem;
        transform: translateY(-50%);
        color: #d66881;
        z-index: 2;
    }

    .profile-input-wrap input.has-password-toggle {
        padding-right: 3.25rem;
    }

    .profile-input-wrap .password-toggle svg {
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .profile-field .password-requirements {
        margin-left: 0;
    }

    .profile-field .password-requirements [data-rule="max"] {
        display: none;
    }

    .profile-field input {
        width: 100%;
        height: 52px;
        border: 1px solid rgba(122, 91, 67, 0.18);
        border-radius: 999px;
        background: rgba(255, 250, 246, 0.78);
        color: #5a4b43;
        padding: 0 1.2rem 0 3.25rem;
        font-size: 0.9rem;
        font-weight: 500;
        outline: none;
        transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
    }

    .profile-field input:focus {
        border-color: rgba(214, 104, 129, 0.55);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(214, 104, 129, 0.08);
    }

    .profile-field input:disabled {
        color: #8f8179;
        background: rgba(255, 250, 246, 0.55);
        cursor: not-allowed;
    }

    .profile-note {
        display: block;
        margin: 0.55rem 0 0 1.15rem;
        color: #9a8378;
        font-size: 0.76rem;
        font-weight: 500;
    }

    .profile-actions {
        display: flex;
        justify-content: center;
        margin-top: 2.35rem;
    }

    .profile-save-button {
        min-width: 280px;
        min-height: 60px;
        border: 0;
        border-radius: 999px;
        color: #fff;
        background:
            linear-gradient(90deg, rgba(43, 76, 63, 0.98), rgba(43, 76, 63, 0.94)),
            url("assets/images/riwayat-cta-leaf.png") right -78px center / 210px auto no-repeat;
        box-shadow: 0 18px 34px rgba(43, 76, 63, 0.22);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.85rem;
        font-size: 1rem;
        font-weight: 800;
        transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
    }

    .profile-save-button:hover,
    .profile-save-button:focus-visible {
        transform: translateY(-1px);
        box-shadow: 0 22px 42px rgba(43, 76, 63, 0.28);
    }

    @media (max-width: 991.98px) {
        .profile-hero-blob {
            width: 46%;
        }

        .profile-card-head {
            flex-wrap: wrap;
        }

        .profile-card-kicker {
            order: 3;
            margin-left: 0;
            width: 100%;
        }

        .profile-form-grid {
            grid-template-columns: 1fr;
            gap: 1.35rem;
        }
    }

    @media (max-width: 575.98px) {
        .profile-hero {
            min-height: 240px;
        }

        .profile-hero-blob {
            width: 55%;
            opacity: 0.5;
        }

        .profile-hero-inner {
            padding: 2.5rem 0 5.5rem;
        }

        .profile-title {
            flex-wrap: wrap;
            gap: 0.1rem;
            font-size: 2.2rem;
        }

        .profile-shell {
            margin-top: -3.5rem;
        }

        .profile-card {
            padding: 1.3rem;
            border-radius: 14px;
        }

        .profile-card-head {
            gap: 0.7rem;
            margin-bottom: 1.4rem;
        }

        .profile-card-icon {
            width: 42px;
            height: 42px;
        }

        .profile-save-button {
            width: 100%;
            min-width: 0;
        }
    }
</style>

<section class="profile-page">
    <div class="profile-hero">
        <!-- Right-side organic blob with spa imagery -->
        <div class="profile-hero-blob" aria-hidden="true"></div>

        <div class="profile-hero-inner">
            <p class="profile-greeting">Halo, <?= e($namaPelanggan) ?></p>
            <h1 class="profile-title">
                Profil&nbsp;
                <span class="profile-title-script">Saya</span>
            </h1>
            <p class="profile-subtitle">Kelola informasi akun Anda<br>dengan mudah dan aman.</p>
        </div>
    </div>

    <section class="profile-shell" aria-labelledby="profile-detail-title">
        <div class="profile-card">
            <div class="profile-card-head">
                <span class="profile-card-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                <h2 class="profile-card-title" id="profile-detail-title">Detail Akun</h2>
                <span class="profile-card-kicker">Personal Profile</span>
            </div>

            <?php if (isset($pesanError)): ?>
                <div class="profile-alert error"><?= e($pesanError) ?></div>
            <?php endif; ?>

            <form class="profile-form" method="POST" action="index.php?action=simpan-profil">
                <div class="profile-form-grid">
                    <div class="profile-field">
                        <label for="nama">Nama Lengkap</label>
                        <div class="profile-input-wrap">
                            <span class="profile-input-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                            <input type="text" id="nama" name="nama" value="<?= e($user['nama'] ?? '') ?>" required maxlength="100" pattern="^[A-Za-z\s']+$" title="Nama hanya boleh berisi huruf, spasi, dan tanda petik" placeholder="Masukkan nama lengkap">
                        </div>
                    </div>

                    <div class="profile-field">
                        <label for="email">Alamat Email</label>
                        <div class="profile-input-wrap">
                            <span class="profile-input-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                            </span>
                            <input type="email" id="email" value="<?= e($user['email'] ?? '') ?>" disabled>
                        </div>
                        <span class="profile-note">Email tidak dapat diubah</span>
                    </div>

                    <div class="profile-field">
                        <label for="telepon">Nomor Telepon</label>
                        <div class="profile-input-wrap">
                            <span class="profile-input-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.6a2 2 0 0 1-.4 2.1L8 9.7a16 16 0 0 0 6.3 6.3l1.3-1.3a2 2 0 0 1 2.1-.4c.8.3 1.7.5 2.6.6a2 2 0 0 1 1.7 2Z"/></svg>
                            </span>
                            <input type="tel" id="telepon" name="telepon" value="<?= e($user['telepon'] ?? '') ?>" required inputmode="numeric" pattern="[0-9]{8,13}" title="Nomor telepon harus berupa angka antara 8 sampai 13 digit" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Contoh: 0812345678">
                        </div>
                    </div>

                    <div class="profile-field">
                        <label for="password_baru">Password Baru</label>
                        <div class="profile-input-wrap">
                            <span class="profile-input-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                            </span>
                            <input class="has-password-toggle" type="password" id="password_baru" name="password_baru" minlength="8" maxlength="255" title="Password baru minimal 8 karakter, memiliki 1 huruf besar, dan 1 karakter khusus" placeholder="Kosongkan jika tidak diganti" data-password-rules>
                            <button class="password-toggle" type="button" data-toggle-password="password_baru" aria-label="Tampilkan password baru">
                                <svg class="eye-open" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <svg class="eye-closed" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3l18 18"></path><path d="M10.6 10.6A3 3 0 0 0 12 15a3 3 0 0 0 2.4-1.2"></path><path d="M7.1 7.1C3.8 8.9 2 12 2 12s3.5 6 10 6c1.7 0 3.2-.4 4.5-1"></path><path d="M14.1 5.2C19.2 6.1 22 12 22 12s-.8 1.4-2.3 2.8"></path></svg>
                            </button>
                        </div>
                        <ul class="password-requirements" data-password-requirements="password_baru">
                            <li data-rule="min">Minimal 8 karakter</li>
                            <li data-rule="uppercase">Minimal 1 huruf besar (A-Z)</li>
                            <li data-rule="special">Minimal 1 karakter khusus</li>
                        </ul>
                    </div>
                </div>

                <div class="profile-actions">
                    <button class="profile-save-button" type="submit">
                        <span>Simpan Perubahan</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </section>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
