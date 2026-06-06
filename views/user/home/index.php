<?php $judulHalaman = 'SPAdmin Spa Bandung'; ?>
<?php $bodyClass = 'home-page'; ?>
<?php include __DIR__ . '/../templates/header.php'; ?>

<?php if (isset($_GET['pesan_sukses'])): ?>
    <div class="floating-alert success" id="floatingAlert">
        <div class="alert-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        </div>
        <div class="alert-message">
            <?= e($_GET['pesan_sukses']) ?>
        </div>
        <button class="alert-close" onclick="closeAlert()">&times;</button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['pesan_error'])): ?>
    <div class="floating-alert error" id="floatingAlert">
        <div class="alert-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        </div>
        <div class="alert-message">
            <?= e($_GET['pesan_error']) ?>
        </div>
        <button class="alert-close" onclick="closeAlert()">&times;</button>
    </div>
<?php endif; ?>

<!-- Custom Premium CSS for the New Luxurious Design -->
<style>
    /* Styling variables */
    :root {
        --color-dark: #151210;
        --color-beige: #e8dfd8;
        --color-beige-dark: #d3c4b7;
        --color-light-cream: #fbf9f6;
        --color-brown-gold: #c3a88a;
        --color-text-dark: #221d1b;
        --color-text-muted: #6e645e;
        --wellness-green: #2b4c3f;
        --wellness-pink: #8f6f5b;
        --wellness-bg: #f7f4f0;
    }

    /* Base Reset & Fonts */
    body {
        background-color: var(--cream) !important;
        color: var(--color-text-dark);
    }

    body .logo-icon circle,
    body .footer-logo-icon circle {
        fill: var(--wellness-pink) !important;
    }

    body .footer-logo-sub,
    body .footer-col-heading,
    body .footer-bottom-lotus,
    body .footer-info-icon,
    body .footer-social-btn:hover {
        color: var(--wellness-pink) !important;
    }

    body .footer-heading-divider,
    body .footer-social-btn:hover {
        background: var(--wellness-pink) !important;
    }

    body .footer-info-icon {
        background: rgba(143, 111, 91, 0.12) !important;
    }

    body .footer-floral-bg g,
    body .footer-bottom-lotus g {
        stroke: var(--wellness-pink) !important;
    }

    /* ===== NAVBAR ===== */
    .site-header {
        background-color: var(--cream) !important;
        border-bottom-color: var(--line) !important;
    }

    .navbar {
        background-color: var(--cream) !important;
        border-bottom: 1px solid var(--line) !important;
        padding-top: 0.6rem !important;
        padding-bottom: 0.6rem !important;
    }

    .navbar .logo-name {
        font-family: 'Inter', sans-serif !important;
        font-weight: 700 !important;
        font-size: 1.1rem !important;
        letter-spacing: 0.1em !important;
        color: #2b4c3f !important; /* Dark Green matching main theme */
    }

    .navbar .logo-sub {
        font-family: 'Cormorant Garamond', serif !important;
        font-style: italic !important;
        font-size: 0.9rem !important;
        color: #c87a8b !important; /* Soft Rose */
        letter-spacing: 0.05em !important;
        margin-left: 2px !important;
        font-weight: 500 !important;
    }

    .navbar .nav-link {
        color: #6a5c50 !important;
        font-weight: 500 !important;
        font-size: 0.95rem !important;
        transition: all 0.3s ease;
        letter-spacing: 0.02em;
        position: relative;
        padding: 0.5rem 0.8rem !important;
    }

    .navbar .nav-link:hover,
    .navbar .nav-link.active {
        color: #2b4c3f !important;
        font-weight: 600 !important;
    }

    .navbar .nav-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 50%;
        transform: translateX(-50%) scaleX(0);
        width: 24px;
        height: 2px;
        background-color: #c87a8b;
        transition: transform 0.3s ease;
        border-radius: 2px;
    }

    .navbar .nav-link.active::after {
        transform: translateX(-50%) scaleX(1);
    }

    .navbar .nav-cart-pill {
        color: #2b4c3f !important;
        background: transparent !important;
        border: 1px solid rgba(43, 76, 63, 0.15) !important;
        border-radius: 50px !important;
        padding: 0.45rem 1.1rem !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        transition: all 0.3s ease !important;
    }

    .navbar .nav-cart-pill:hover {
        background: rgba(43, 76, 63, 0.04) !important;
        border-color: rgba(43, 76, 63, 0.25) !important;
        color: #2b4c3f !important;
    }

    .navbar .nav-cart-icon-wrap {
        background: rgba(43, 76, 63, 0.06) !important;
        border: 1px solid rgba(43, 76, 63, 0.12) !important;
        border-radius: 50% !important;
        width: 28px !important;
        height: 28px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        position: relative !important;
    }

    .navbar .nav-cart-icon-img {
        width: 14px !important;
        height: 14px !important;
        object-fit: contain;
    }

    .navbar .nav-separator {
        width: 1px;
        height: 24px;
        background-color: rgba(122, 91, 67, 0.15);
        margin: 0 1.2rem;
    }

    .navbar .nav-user-pill {
        background-color: #f1eae1 !important;
        border: 1px solid rgba(43, 76, 63, 0.1) !important;
        color: #2b4c3f !important;
        font-weight: 500 !important;
        font-size: 0.85rem !important;
        padding: 0.45rem 1.1rem !important;
        border-radius: 50px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        cursor: pointer;
        transition: all 0.3s ease !important;
    }

    .navbar .nav-user-pill:hover {
        background-color: #e8ded2 !important;
        border-color: rgba(43, 76, 63, 0.2) !important;
    }

    .navbar .nav-dropdown-menu {
        background-color: #ffffff !important;
        border: 1px solid rgba(122, 91, 67, 0.12) !important;
        box-shadow: 0 10px 30px rgba(63, 48, 40, 0.08) !important;
        border-radius: 16px !important;
    }

    .navbar .navbar-toggler-icon {
        filter: none;
    }

    /* ===== HERO SECTION ===== */
    .hero-outer-frame {
        background-color: var(--cream);
        padding: 0;
        position: relative;
        overflow: hidden;
    }

    .premium-hero {
        background-color: var(--cream);
        color: var(--color-text-dark);
        position: relative;
        overflow: hidden;
        padding: 0;
        min-height: 380px;
        display: flex;
        align-items: center;
        border: none;
        box-shadow: none;
    }

    .hero-eyebrow {
        display: block;
        font-family: 'Inter', sans-serif;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: #a48c71; /* soft gold/brown eyebrow */
        margin-bottom: 0.8rem;
    }

    .hero-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(2.2rem, 4vw, 3.2rem);
        line-height: 1.25;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: #2b4c3f; /* Dark Green */
        margin-top: 0;
        margin-bottom: 0.5rem;
        position: relative;
        display: inline-block;
    }

    .hero-title-cursive {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-style: italic;
        color: #c87a8b; /* beautiful script pink */
        font-weight: 500;
        display: inline-block;
        margin-top: 0.2rem;
    }

    .hero-leaf-graphic {
        position: absolute;
        right: -80px;
        bottom: 5px;
        width: 90px;
        height: 90px;
        pointer-events: none;
        transform: rotate(15deg);
        z-index: 1;
        opacity: 0.85;
    }

    .hero-title-line {
        width: 100%;
        max-width: 300px;
        height: 1.5px;
        background-color: rgba(200, 122, 139, 0.35); /* soft pink accent line */
        margin-bottom: 1.2rem;
        margin-top: 0.4rem;
    }

    .hero-description {
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem;
        line-height: 1.6;
        color: #6e645e;
        max-width: 480px;
        font-weight: 400;
        margin-bottom: 0.8rem;
    }

    /* Column benefit features */
    .hero-features-grid {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.4rem;
        max-width: 100%;
    }

    .hero-feature-col {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        flex: 1;
    }

    .hero-feature-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background-color: rgba(43, 76, 63, 0.06); /* Soft green background */
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }

    .hero-feature-col:hover .hero-feature-icon-box {
        transform: scale(1.05);
    }

    .hero-feature-icon-box.pink-bg {
        background-color: rgba(200, 122, 139, 0.08); /* Soft pink background */
    }

    .hero-feature-icon-box.gold-bg {
        background-color: rgba(164, 140, 113, 0.08); /* Soft gold background */
    }

    .hero-feature-text {
        display: flex;
        flex-direction: column;
    }

    .hero-feature-title {
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 0.95rem;
        color: #2b4c3f;
        margin: 0 0 4px 0;
    }

    .hero-feature-desc {
        font-family: 'Inter', sans-serif;
        font-size: 0.8rem;
        color: #7e746e;
        margin: 0;
        white-space: normal;
    }

    .hero-feature-divider {
        width: 1px;
        height: 45px;
        background-color: rgba(122, 91, 67, 0.12);
        flex-shrink: 0;
    }

    /* CTA pill button */
    .hero-cta-btn {
        display: inline-flex;
        align-items: center;
        background-color: #2b4c3f; /* Premium dark green background */
        color: #ffffff !important;
        font-family: 'Inter', sans-serif;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 0.75rem 1.8rem;
        border-radius: 50px;
        border: none;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        box-shadow: 0 4px 15px rgba(43, 76, 63, 0.2);
        text-decoration: none;
    }

    .hero-cta-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(43, 76, 63, 0.3);
        background-color: #20392f;
    }

    .hero-cta-btn svg {
        transition: transform 0.3s ease;
    }

    .hero-cta-btn:hover svg {
        transform: translateX(4px);
    }

    /* Hero image column */
    .hero-image-side-wrapper {
        position: relative;
        width: 100%;
        height: 100%;
        min-height: 380px;
        overflow: hidden;
    }

    .hero-image-side-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
    }

    /* Seamless left-to-transparent gradient fade overlay */
    .hero-image-side-wrapper::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: 2;
        pointer-events: none;
        background: linear-gradient(
            to right,
            #f7f1e7 0%,
            rgba(247, 241, 231, 0.85) 12%,
            rgba(247, 241, 231, 0.4) 28%,
            transparent 45%
        );
    }

    /* watercolor blush organic glow in top-left */
    .hero-image-side-wrapper::after {
        content: '';
        position: absolute;
        top: -40px;
        left: -40px;
        width: 250px;
        height: 250px;
        background: radial-gradient(
            circle,
            rgba(247, 241, 231, 0.95) 0%,
            transparent 70%
        );
        filter: blur(15px);
        pointer-events: none;
        z-index: 3;
    }

    /* Mobile/Responsive Styling overrides */
    @media (max-width: 991.98px) {
        .premium-hero {
            min-height: auto;
            padding: 3rem 0;
        }

        .hero-leaf-graphic {
            right: -55px;
            width: 80px;
            height: 80px;
        }

        .hero-features-grid {
            flex-direction: column;
            align-items: flex-start;
            gap: 1.2rem;
            margin-bottom: 2rem;
        }

        .hero-feature-divider {
            width: 100%;
            height: 1px;
        }
    }
        min-width: 170px;
    }
    @keyframes floatCard {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }
    .floating-spot-card .spot-badge {
        font-family: 'Inter', sans-serif;
        font-size: 0.68rem;
        font-weight: 700;
        color: var(--wellness-pink);
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .floating-spot-card .spot-badge span.dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background-color: var(--wellness-pink);
        display: inline-block;
        flex-shrink: 0;
    }
    .floating-spot-card .spot-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 0.95rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 2px;
    }
    .floating-spot-card .spot-time {
        font-family: 'Inter', sans-serif;
        font-size: 0.68rem;
        color: #999;
    }

    .hero-image-frame {
        position: relative;
        width: 100%;
        max-width: 650px;
        height: 520px;
        border-radius: 40px;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
        z-index: 3;
        border: 1px solid rgba(195, 168, 138, 0.25);
    }

    .hero-image-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.8s ease;
    }

    .hero-image-frame:hover img {
        transform: scale(1.03);
    }

    /* 2. SECTION RELINESPAS (SOFT BEIGE - LIGHT) */
    .section-relinespas {
        background-color: var(--color-beige);
        padding: 7.5rem 0;
        position: relative;
    }

    .card-relinespas {
        background: var(--color-light-cream);
        border-radius: 28px;
        overflow: hidden;
        border: 1px solid rgba(195, 168, 138, 0.15);
        box-shadow: 0 15px 45px rgba(34, 29, 27, 0.04);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.4s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .card-relinespas:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 55px rgba(34, 29, 27, 0.08);
    }

    .card-img-wrapper {
        height: 250px;
        overflow: hidden;
    }

    .card-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .card-relinespas:hover .card-img-wrapper img {
        transform: scale(1.05);
    }

    .card-body-relinespas {
        padding: 2.2rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .card-category {
        display: inline-block;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: var(--color-brown-gold);
        margin-bottom: 0.6rem;
    }

    .card-body-relinespas h3 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.45rem;
        font-weight: 700;
        color: var(--color-text-dark);
        margin-bottom: 0.8rem;
    }

    .card-body-relinespas p {
        font-size: 0.9rem;
        line-height: 1.65;
        color: var(--color-text-muted);
        margin-bottom: 1.8rem;
    }

    .card-body-relinespas .duration {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--color-brown-gold);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .card-body-relinespas .price {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--color-text-dark);
    }

    .btn-card-detail {
        display: block;
        width: 100%;
        text-align: center;
        border: 1px solid var(--color-beige-dark);
        color: var(--color-text-dark);
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 0.85rem 0;
        border-radius: 50px;
        background: transparent;
        transition: all 0.3s ease;
    }

    .btn-card-detail:hover {
        background-color: var(--color-dark);
        color: var(--color-beige);
        border-color: var(--color-dark);
    }

    .sub-category {
        display: block;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.25em;
        text-transform: uppercase;
        color: var(--color-text-dark);
        margin-bottom: 1rem;
    }

    .section-title-luxury {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(2.2rem, 4.2vw, 3.8rem);
        line-height: 1.1;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--color-text-dark);
        margin-bottom: 1.8rem;
    }

    .section-desc-luxury {
        font-size: 0.95rem;
        line-height: 1.8;
        color: var(--color-text-muted);
        font-weight: 400;
        max-width: 440px;
    }

    .btn-premium-dark {
        display: inline-block;
        background-color: var(--color-dark);
        color: var(--color-beige);
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 0.82rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 1.05rem 2.8rem;
        border-radius: 50px;
        border: 1px solid var(--color-dark);
        box-shadow: 0 10px 25px rgba(21, 18, 16, 0.15);
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-premium-dark:hover {
        background-color: transparent;
        color: var(--color-dark);
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(21, 18, 16, 0.12);
        text-decoration: none;
    }


    /* ===== SECTION ARCH EXPERIENCE ===== */
    .section-arch-experience {
        background-color: var(--color-light-cream);
        padding: 5rem 0;
        border-bottom: 1px solid rgba(43, 76, 63, 0.05);
    }

    .experience-eyebrow {
        display: block;
        font-family: 'Inter', sans-serif;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.25em;
        text-transform: uppercase;
        color: var(--wellness-pink);
        margin-bottom: 1rem;
    }

    .experience-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(1.8rem, 3.2vw, 2.5rem);
        line-height: 1.25;
        font-weight: 700;
        color: var(--wellness-green);
        margin-bottom: 1.5rem;
    }

    .experience-desc {
        font-family: 'Inter', sans-serif;
        font-size: 0.88rem;
        line-height: 1.6;
        color: #7e746e;
        margin-bottom: 2rem;
        max-width: 320px;
    }

    .experience-divider {
        width: 35px;
        height: 2px;
        background-color: var(--wellness-pink);
    }

    /* Arched cards wrapper and row spacing */
    .arch-card-wrapper {
        background: #fdfcfb;
        border: 1px solid #efeae4;
        border-radius: 220px 220px 28px 28px;
        padding: 0.75rem 0.75rem 1.8rem;
        text-align: center;
        box-shadow: 0 4px 18px rgba(0,0,0,0.018);
        transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .arch-card-wrapper:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 36px rgba(43, 76, 63, 0.09);
        border-color: rgba(43, 76, 63, 0.2);
    }

    .arch-img-frame {
        width: 100%;
        overflow: hidden;
        border-radius: 200px 200px 0 0;
        aspect-ratio: 0.8 / 1;
        flex-shrink: 0;
    }

    .arch-img-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 200px 200px 0 0;
        transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .arch-card-wrapper:hover .arch-img-frame img {
        transform: scale(1.05);
    }

    .arch-card-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--wellness-green);
        margin-top: 1.3rem;
        margin-bottom: 0.5rem;
        line-height: 1.25;
    }

    .arch-card-desc {
        font-family: 'Inter', sans-serif;
        font-size: 0.8rem;
        color: #7e746e;
        line-height: 1.5;
        padding: 0 0.3rem;
    }
</style>

<!-- 1. PREMIUM WELLNESS HERO SECTION (LIGHT WELLNESS MOCKUP THEME) -->
<section class="hero-outer-frame">
    <div class="premium-hero">
        <div class="container-fluid p-0">
            <div class="row g-0 align-items-stretch">
                <!-- Left text column -->
                <div class="col-lg-6 d-flex align-items-center">
                    <div class="pt-4 pb-4 px-4 px-sm-5 ps-lg-5 scroll-reveal reveal-fade-up" style="max-width: 680px; margin-right: auto; margin-left: 6rem;">

                        <span class="hero-eyebrow">YOUR PERSONAL SANCTUARY</span>
                        <h1 class="hero-title">
                            Find Your<br>
                            Perfect Moment<br>
                            <span class="hero-title-cursive">of Calm</span>
                            <!-- High-fidelity custom inline SVG leaf illustration next to "of Calm" -->
                            <span class="hero-leaf-graphic">
                                <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 100 C 40 90, 70 70, 95 30" stroke="#c87a8b" stroke-width="1.2" stroke-linecap="round"/>
                                    <path d="M95 30 C 90 15, 80 10, 70 15 C 75 25, 85 30, 95 30 Z" stroke="#c87a8b" stroke-width="1.2" stroke-linejoin="round"/>
                                    <path d="M70 55 C 55 45, 45 42, 38 48 C 45 58, 60 62, 70 55 Z" stroke="#c87a8b" stroke-width="1.2" stroke-linejoin="round"/>
                                    <path d="M80 45 C 88 35, 95 32, 98 38 C 92 48, 85 50, 80 45 Z" stroke="#c87a8b" stroke-width="1.2" stroke-linejoin="round"/>
                                    <path d="M50 75 C 35 68, 25 68, 20 75 C 28 82, 42 82, 50 75 Z" stroke="#c87a8b" stroke-width="1.2" stroke-linejoin="round"/>
                                    <path d="M60 65 C 68 55, 75 52, 78 58 C 72 68, 65 70, 60 65 Z" stroke="#c87a8b" stroke-width="1.2" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </h1>
                        <div class="hero-title-line"></div>
                        <p class="hero-description">Nikmati pengalaman spa premium dengan terapis profesional dan suasana relaksasi terbaik.</p>

                        <!-- Three Clean Benefit Items (New Design) -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full max-w-[550px] my-4 pt-2 border-t border-brown-dark/10">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-full bg-olive/5 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-olive"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path></svg>
                                </span>
                                <div class="leading-none font-plus">
                                    <div class="text-[14px] font-bold text-olive tracking-wide">Relaks</div>
                                    <div class="text-[11.5px] text-[#7e746e] font-semibold mt-0.5">Redakan Letih</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-full bg-pink-blush/5 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-pink-blush">
                                        <path d="M12 22V12" />
                                        <path d="M12 12C12 12 8 8 8 5C8 3 10 3 12 6C14 3 16 3 16 5C16 8 12 12 12 12Z" />
                                        <path d="M12 15C12 15 16 14 18 11" />
                                        <path d="M12 18C12 18 8 17 6 14" />
                                    </svg>
                                </span>
                                <div class="leading-none font-plus">
                                    <div class="text-[14px] font-bold text-olive tracking-wide">Rejuvenasi</div>
                                    <div class="text-[11.5px] text-[#7e746e] font-semibold mt-0.5">Aura Alami</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-full bg-[#a48c71]/10 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#a48c71]">
                                        <path d="M12 22a7 7 0 0 0 7-7c0-4.3-7-11-7-11S5 10.7 5 15a7 7 0 0 0 7 7z"/>
                                    </svg>
                                </span>
                                <div class="leading-none font-plus">
                                    <div class="text-[14px] font-bold text-olive tracking-wide">Detoks</div>
                                    <div class="text-[11.5px] text-[#7e746e] font-semibold mt-0.5">Bersihkan Darah</div>
                                </div>
                            </div>
                        </div>

                        <!-- Explore pill button (New Design) -->
                        <a href="index.php?action=layanan" class="mt-2 inline-flex items-center gap-2.5 bg-olive text-white font-sans text-xs font-bold leading-none py-3.5 px-7 rounded-full shadow-md shadow-olive/20 hover:bg-[#203a30] hover:-translate-y-0.5 hover:shadow-lg transition-all cursor-pointer text-decoration-none group">
                            Explore Treatments
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-hover:translate-x-1"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </a>
                    </div>
                </div>
                
                <!-- Right image column with the raw image -->
                <div class="col-lg-6 p-0 position-relative d-none d-lg-block">
                    <div class="hero-image-side-wrapper">
                        <img src="assets/images/hero_spa_bg_new.jpg" alt="Luxurious Wellness Spa chamber with massage table">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 1.2. SECTION ARCH EXPERIENCE (THE SPADMIN EXPERIENCE) -->
<section class="section-arch-experience">
    <div class="container">
        <div class="row align-items-center g-4">
            <!-- Left copy side -->
            <div class="col-lg-4 scroll-reveal reveal-fade-up">
                <span class="experience-eyebrow">The SPAdmin Experience</span>
                <h2 class="experience-title">Step into a calming sanctuary designed for your wellbeing.</h2>
                <p class="experience-desc">Every detail of our space is curated to bring warmth, comfort, and total relaxation for your body and mind.</p>
                <div class="experience-divider"></div>
            </div>
            
            <!-- Right arched cards side -->
            <div class="col-lg-8 scroll-reveal reveal-fade-up" style="transition-delay: 0.1s;">
                <div class="row g-3 row-cols-2 row-cols-md-4">
                    <!-- Card 1: Serenity Room -->
                    <div class="col">
                        <div class="arch-card-wrapper">
                            <div class="arch-img-frame">
                                <img src="assets/images/experience_1.jpg" alt="Serenity Room">
                            </div>
                            <h3 class="arch-card-title">Serenity Room</h3>
                            <p class="arch-card-desc">Private treatment rooms for deep relaxation.</p>
                        </div>
                    </div>
                    
                    <!-- Card 2: Aromatherapy Corner -->
                    <div class="col">
                        <div class="arch-card-wrapper">
                            <div class="arch-img-frame">
                                <img src="assets/images/experience_2.jpg" alt="Aromatherapy Corner">
                            </div>
                            <h3 class="arch-card-title">Aromatherapy Corner</h3>
                            <p class="arch-card-desc">Natural scents to calm your senses.</p>
                        </div>
                    </div>
                    
                    <!-- Card 3: Relaxation Lounge -->
                    <div class="col">
                        <div class="arch-card-wrapper">
                            <div class="arch-img-frame">
                                <img src="assets/images/experience_3.jpg" alt="Relaxation Lounge">
                            </div>
                            <h3 class="arch-card-title">Relaxation Lounge</h3>
                            <p class="arch-card-desc">A peaceful space to rest and unwind.</p>
                        </div>
                    </div>
                    
                    <!-- Card 4: Herbal & Natural Care -->
                    <div class="col">
                        <div class="arch-card-wrapper">
                            <div class="arch-img-frame">
                                <img src="assets/images/experience_4.jpg" alt="Herbal & Natural Care">
                            </div>
                            <h3 class="arch-card-title">Herbal & Natural Care</h3>
                            <p class="arch-card-desc">Traditional touch with natural ingredients.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$featuredEyebrow = ambilPengaturan($conn, 'featured_section_eyebrow', 'Our Best Value');
$featuredTitle = ambilPengaturan($conn, 'featured_section_title', 'Combo Packages');
$featuredSubtitle = ambilPengaturan($conn, 'featured_section_subtitle', 'Dapatkan pengalaman spa terlengkap dengan harga terbaik melalui paket kombinasi eksklusif kami.');
$featuredCategory = ambilPengaturan($conn, 'featured_section_category', 'Combo Paket');
$comboPackages = ambilLayanan($conn, '', $featuredCategory);
?>

<!-- SECTION COMBO PACKAGES -->
<section class="section-combo-packages">
    <!-- Ambient Organic Blur Blobs -->
    <div class="combo-blob-left" aria-hidden="true"></div>
    <div class="combo-blob-right" aria-hidden="true"></div>

    <div class="container">
        <!-- Header -->
        <div class="combo-pkg-header scroll-reveal reveal-fade-up">
            <span class="combo-pkg-eyebrow"><?= e($featuredEyebrow) ?></span>
            <h2 class="combo-pkg-title"><?= e($featuredTitle) ?></h2>
            <p class="combo-pkg-subtitle"><?= e($featuredSubtitle) ?></p>
        </div>

        <!-- Slider Track Wrapper -->
        <div class="combo-slider-outer scroll-reveal reveal-fade-up" style="transition-delay:0.1s;">
            <!-- Prev Arrow -->
            <button class="combo-arrow combo-arrow-prev" id="comboPrev" aria-label="Previous">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>

            <div class="combo-slider-container">
                <!-- Centerpiece radial glow behind middle card -->
                <div class="combo-center-glow" aria-hidden="true"></div>

                <div class="combo-slider-track" id="comboTrack">
                    <?php if (!empty($comboPackages)): ?>
                        <?php foreach ($comboPackages as $index => $pkg): ?>
                        <?php
                            $isActive = ($index === 1 || count($comboPackages) === 1);
                            $imgSrc = mediaLayanan($pkg['media'] ?? '', $pkg['nama_layanan'] ?? '');
                            $formattedHarga = rupiah($pkg['harga']);
                            $durasiLabel = formatDurasi($pkg['durasi']);
                        ?>
                        <div class="combo-pkg-card <?= $isActive ? 'active' : '' ?>" data-id="<?= $pkg['id'] ?>">
                            <!-- Image -->
                            <div class="combo-card-img-wrapper">
                                <img src="<?= e($imgSrc) ?>" alt="<?= e($pkg['nama_layanan']) ?>">
                                <!-- Duration badge -->
                                <span class="combo-duration-badge">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <?= $durasiLabel ?>
                                </span>
                            </div>
                            <!-- Body -->
                            <div class="combo-card-body">
                                <span class="combo-card-category"><?= e($pkg['kategori'] ?? 'Layanan') ?></span>
                                <h3 class="combo-card-name"><?= e($pkg['nama_layanan']) ?></h3>
                                <p class="combo-card-desc"><?= e($pkg['deskripsi']) ?></p>
                                <div class="combo-card-footer">
                                    <span class="combo-card-price"><?= $formattedHarga ?></span>
                                    <form method="POST" action="index.php?action=tambah-keranjang" style="margin: 0;">
                                        <input type="hidden" name="layanan_id" value="<?= (int)$pkg['id'] ?>">
                                        <input type="hidden" name="redirect_to" value="index.php?action=home">
                                        <button type="submit" class="combo-card-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                            Keranjang
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color:#888; text-align:center; width:100%; padding: 2rem;">Belum ada paket combo tersedia.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Next Arrow -->
            <button class="combo-arrow combo-arrow-next" id="comboNext" aria-label="Next">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
        </div>

        <!-- Dots -->
        <div class="combo-dots" id="comboDots">
            <?php foreach ($comboPackages as $i => $p): ?>
                <button class="combo-dot <?= $i === 1 || count($comboPackages) === 1 ? 'active' : '' ?>" data-index="<?= $i ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- SECTION WHY CHOOSE US -->
<section class="section-why-choose">
    <div class="why-choose-card">

        <!-- Leaf watermark LEFT -->
        <div class="why-choose-watermark" aria-hidden="true">
            <svg viewBox="0 0 160 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g stroke="#8f6f5b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" opacity="0.22">
                    <path d="M80 385 C72 340 55 275 65 200 C70 160 80 120 82 80 C84 50 83 25 80 8"/>
                    <path d="M80 8 C68 16 55 14 45 7 C58 13 72 12 80 8 Z"/>
                    <path d="M80 8 C90 12 98 6 106 0 C96 6 88 8 80 8 Z"/>
                    <path d="M76 50 C54 56 30 44 12 33 C34 43 58 50 76 50 Z"/>
                    <path d="M76 50 C57 44 34 37 12 33"/>
                    <path d="M74 82 C94 91 118 106 130 126 C108 113 90 100 74 82 Z"/>
                    <path d="M74 82 C90 92 110 107 130 126"/>
                    <path d="M68 122 C42 130 14 117 -6 102 C18 115 48 122 68 122 Z"/>
                    <path d="M68 122 C46 114 18 106 -6 102"/>
                    <path d="M65 162 C88 174 112 194 124 220 C98 202 78 185 65 162 Z"/>
                    <path d="M65 162 C82 176 104 196 124 220"/>
                    <path d="M65 205 C36 215 6 200 -16 182 C12 198 44 206 65 205 Z"/>
                    <path d="M65 205 C40 196 12 187 -16 182"/>
                    <path d="M67 252 C88 268 106 294 116 322 C92 300 75 278 67 252 Z"/>
                    <path d="M67 252 C82 270 100 296 116 322"/>
                    <path d="M70 305 C40 316 8 300 -14 282 C14 298 46 307 70 305 Z"/>
                    <path d="M70 305 C44 295 14 285 -14 282"/>
                </g>
            </svg>
        </div>

        <!-- Spa stones RIGHT -->
        <div class="why-choose-stones" aria-hidden="true">
            <img src="assets/images/spa_stones.png" alt="Spa Stones">
        </div>

        <!-- Centered content -->
        <div class="container">
            <div class="why-choose-inner">

                <!-- Header -->
                <div class="why-choose-header">
                    <span class="why-choose-eyebrow">WHY CHOOSE SPADMIN</span>
                    <h2 class="why-choose-title">Why Guests Love SPADMIN</h2>
                    <p class="why-choose-subtitle">Komitmen kami untuk memberikan pengalaman relaksasi terbaik untuk Anda.</p>
                </div>

                <!-- 4 Features -->
                <div class="why-choose-grid">

                    <!-- Feature 1 -->
                    <div class="why-choose-item">
                        <div class="why-icon-container">
                            <span class="why-icon-pill">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </span>
                        </div>
                        <h3 class="why-item-title">Therapist Profesional</h3>
                        <p class="why-item-desc">Terapis berpengalaman dan bersertifikasi untuk perawatan yang aman dan berkualitas.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="why-choose-item">
                        <div class="why-icon-container">
                            <span class="why-icon-pill">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="22" height="22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 4 C10.5 8 10.5 17 12 21 C13.5 17 13.5 8 12 4 Z"/>
                                    <path d="M12 7.5 C8 10 8 16.5 12 21 C9.5 17.5 10 13 12 7.5 Z"/>
                                    <path d="M12 7.5 C16 10 16 16.5 12 21 C14.5 17.5 14 13 12 7.5 Z"/>
                                    <path d="M12 11 C5.5 12 6.5 17.5 12 21 C8.5 19.5 9 16 12 11 Z"/>
                                    <path d="M12 11 C18.5 12 17.5 17.5 12 21 C15.5 19.5 15 16 12 11 Z"/>
                                </svg>
                            </span>
                        </div>
                        <h3 class="why-item-title">Private &amp; Hygienic Room</h3>
                        <p class="why-item-desc">Ruang perawatan privat, bersih, dan nyaman untuk relaksasi yang maksimal.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="why-choose-item">
                        <div class="why-icon-container">
                            <span class="why-icon-pill">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2v4m4-4v4M5 8h14M5 8a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2M12 12v5m-2-3 2 2 2-2"/></svg>
                            </span>
                        </div>
                        <h3 class="why-item-title">Natural Essential Oil</h3>
                        <p class="why-item-desc">Menggunakan essential oil premium alami untuk manfaat terapi yang optimal.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="why-choose-item">
                        <div class="why-icon-container">
                            <span class="why-icon-pill">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2s-4 4-4 7c0 2.2 1.8 4 4 4s4-1.8 4-4c0-3-4-7-4-7z"/><path d="M9 18h6"/><path d="M8 21h8"/></svg>
                            </span>
                        </div>
                        <h3 class="why-item-title">Relaxing Atmosphere</h3>
                        <p class="why-item-desc">Suasana tenang, aroma menenangkan, dan desain elegan untuk ketenangan penuh.</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>



<style>
/* ===== SECTION WHY CHOOSE US ===== */
.section-why-choose {
    background: linear-gradient(180deg, #fbf8f5 0%, #fffaf6 58%, #fbf8f5 100%);
    padding: 2.6rem 3.5rem 2.25rem;
    margin-top: -1px;
}

.why-choose-card {
    /* Cream-pink diagonal: top-left warmer, bottom-right creamier — super subtle */
    background:
        linear-gradient(
            128deg,
            hsla(348, 72%, 94%, 0.44) 0%,
            hsla(348, 54%, 96%, 0.28) 28%,
            hsla(30,  42%, 98%, 0.64) 56%,
            hsla(40,  24%, 99%, 0.86) 100%
        ),
        #fff9f6;
    position: relative;
    overflow: hidden;
    padding: 2.2rem 0 2rem;
    border-radius: 24px;
    box-shadow: 0 16px 46px rgba(143, 111, 91, 0.055);
    border: 1px solid rgba(143, 111, 91, 0.075);
}

.why-choose-watermark {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 180px;
    pointer-events: none;
    z-index: 0;
    opacity: 1;
}
.why-choose-watermark svg {
    width: 100%;
    height: 100%;
}

.why-choose-stones {
    position: absolute;
    right: 30px; /* shifted left from the edge */
    bottom: 0;
    width: 300px;
    height: auto;
    pointer-events: none;
    z-index: 1;
}
.why-choose-stones img {
    width: 100%;
    height: auto;
    display: block;
    /* mix-blend-mode: multiply is ideal for transparent-bg PNG */
    mix-blend-mode: normal;
}

.why-choose-inner {
    position: relative;
    z-index: 2;
    padding: 0 220px 0 150px; /* tighter — stones shifted left, watermark narrower */
}

.why-choose-header {
    text-align: center;
    margin-bottom: 2.5rem;
    position: relative; /* needed for ::before glow */
}

/* Soft radial glow behind the heading — dreamy + calming */
.why-choose-header::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 520px;
    height: 200px;
    background: radial-gradient(
        ellipse 60% 55% at 50% 50%,
        rgba(143, 111, 91, 0.14) 0%,
        rgba(232, 160, 176, 0.07) 50%,
        transparent 78%
    );
    filter: blur(52px);
    border-radius: 50%;
    pointer-events: none;
    z-index: -1;
}

.why-choose-eyebrow {
    display: inline-block;
    font-family: 'Inter', sans-serif;
    font-size: 0.9rem;
    font-weight: 700;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    color: var(--wellness-pink);
    margin-bottom: 0.6rem;
}

.why-choose-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--wellness-green);
    letter-spacing: -0.02em;
    margin-bottom: 0.8rem;
    line-height: 1.2;
}

.why-choose-subtitle {
    font-family: 'Inter', sans-serif;
    font-size: 0.92rem;
    color: #7e746e;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.5;
}

/* ---- Grid Features ---- */
.why-choose-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: 0;
    align-items: start;
}

.why-choose-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 0 1.2rem;
    position: relative;
}

/* Elegant vertical divider lines between grid items */
.why-choose-item:not(:first-child)::before {
    content: '';
    position: absolute;
    left: 0;
    top: 15%;
    height: 70%;
    width: 1px;
    background: rgba(143, 111, 91, 0.15);
}

.why-icon-container {
    display: flex;
    justify-content: center;
    margin-bottom: 1.2rem;
}

.why-icon-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #ffffff;
    border: 1.5px solid rgba(143, 111, 91, 0.2);
    color: var(--wellness-pink);
    box-shadow: 0 4px 10px rgba(143, 111, 91, 0.08);
}

.why-item-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--wellness-green);
    margin-bottom: 0.6rem;
}

.why-item-desc {
    font-family: 'Inter', sans-serif;
    font-size: 0.8rem;
    color: #7e746e;
    line-height: 1.5;
    margin: 0;
}

/* ---- Responsive ---- */
@media (max-width: 991.98px) {
    .why-choose-grid {
        grid-template-columns: 1fr 1fr;
        row-gap: 2.5rem;
    }
    .why-choose-item:not(:first-child)::before {
        display: none;
    }
    .why-choose-stones {
        display: none;
    }
    .why-choose-watermark {
        display: none;
    }
    .why-choose-inner {
        padding: 0 1.5rem;
    }
}

@media (max-width: 575.98px) {
    .why-choose-grid {
        grid-template-columns: 1fr;
        row-gap: 2rem;
    }
    .why-choose-card {
        padding: 2.5rem 1.5rem 2rem;
    }
    .why-choose-stones {
        display: none;
    }
}
</style>

<?php
$realReviews = ambilSemuaUlasan($conn, 6);
$displayReviews = [];
foreach ($realReviews as $rev) {
    $displayReviews[] = [
        'nama_user' => $rev['nama_user'],
        'nama_layanan' => $rev['nama_layanan'],
        'rating' => (int)$rev['rating'],
        'isi_ulasan' => '"' . $rev['isi_ulasan'] . '"'
    ];
}
?>

<?php if (!empty($displayReviews)): ?>
<section class="section-testimonials">
    <div class="testi-wave-top" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 60" preserveAspectRatio="none">
            <path d="M0,30 C240,60 480,0 720,30 C960,60 1200,0 1440,30 L1440,0 L0,0 Z" fill="#fbf8f5"/>
        </svg>
    </div>
    <div class="container">
        <div class="testi-header scroll-reveal reveal-fade-up">
            <span class="testi-eyebrow">Guest Voices</span>
            <h2 class="testi-title">Moments Remembered</h2>
        </div>

        <div class="testi-glow-wrap">
            <div class="testi-glow-blob" aria-hidden="true"></div>
            <div class="testi-grain" aria-hidden="true"></div>

            <div class="testi-slider-outer scroll-reveal reveal-fade-up" style="transition-delay:0.1s;">
            <button class="testi-arrow testi-arrow-prev" id="testiPrev" aria-label="Previous">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>

            <div class="testi-slider-container">
                <div class="testi-slider-track" id="testiTrack">
                    <?php foreach ($displayReviews as $index => $review): ?>
                    <?php 
                        $isActive = ($index === 1 || count($displayReviews) === 1); 
                    ?>
                    <div class="testi-card <?= $isActive ? 'active' : '' ?>" data-index="<?= $index ?>">
                        <div class="testi-stars">
                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                <?php if ($s <= $review['rating']): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 .587l3.668 7.431 8 1.15-5.792 5.645 1.367 7.962L12 18.896l-7.243 3.847 1.367-7.962L.333 9.168l8-1.15z"/></svg>
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>

                        <p class="testi-text"><?= e($review['isi_ulasan']) ?></p>

                        <div class="testi-service-tag">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <?= e($review['nama_layanan']) ?>
                        </div>

                        <div class="testi-user">
                            <div class="testi-user-info">
                                <h4 class="testi-username"><?= e($review['nama_user']) ?></h4>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button class="testi-arrow testi-arrow-next" id="testiNext" aria-label="Next">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
        </div>
        </div>

        <div class="testi-dots" id="testiDots">
            <?php foreach ($displayReviews as $i => $p): ?>
                <button class="testi-dot <?= $i === 1 || count($displayReviews) === 1 ? 'active' : '' ?>" data-index="<?= $i ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<style>
/* ===== SECTION TESTIMONIALS ===== */
.section-testimonials {
    background-color: #f7f1e7;
    padding: 0 0 4rem;
    border-top: none;
    position: relative;
    overflow: hidden;
}

.testi-wave-top {
    display: block;
    width: 100%;
    overflow: hidden;
    line-height: 0;
    margin-top: -2px;
}

.testi-wave-top svg {
    display: block;
    width: 100%;
    height: 48px;
}

.testi-wave-top svg path {
    fill: var(--color-light-cream) !important;
}

.testi-glow-wrap {
    position: relative;
}

.testi-glow-blob {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 540px;
    height: 300px;
    background: radial-gradient(
        circle,
        rgba(214, 104, 129, 0.12) 0%,
        rgba(214, 104, 129, 0.04) 40%,
        transparent 70%
    );
    border-radius: 50%;
    filter: blur(48px);
    pointer-events: none;
    z-index: 0;
}

.testi-grain {
    display: none;
}

.testi-header {
    text-align: center;
    margin-top: 0.75rem;
    margin-bottom: 1.35rem;
    position: relative;
    z-index: 10;
}

.testi-eyebrow {
    display: block;
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    font-size: 11.5px;
    font-weight: 800;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    color: #a48c71;
    margin-bottom: 0.5rem;
}

.testi-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(1.5rem, 3vw, 2.25rem);
    font-weight: 800;
    color: var(--wellness-green);
    line-height: 1.25;
    margin-bottom: 0.5rem;
}

.testi-slider-outer {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    z-index: 2;
    max-width: 672px;
    margin: 0 auto;
}

.testi-arrow {
    position: absolute;
    top: 50%;
    z-index: 6;
    width: 42px;
    height: 42px;
    border-radius: 999px;
    border: 1px solid rgba(95, 80, 71, 0.14);
    background: rgba(255, 255, 255, 0.92);
    color: var(--wellness-green);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 12px 28px rgba(63, 55, 47, 0.12);
    transform: translateY(-50%);
    cursor: pointer;
    transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
}

.testi-arrow:hover {
    background: var(--wellness-green);
    color: #fff;
    transform: translateY(-50%) scale(1.04);
    box-shadow: 0 16px 34px rgba(63, 55, 47, 0.18);
}

.testi-arrow-prev {
    left: -44px;
}

.testi-arrow-next {
    right: -44px;
}

.testi-slider-container {
    flex: 1;
    overflow: hidden;
    padding: 0.6rem 0.5rem 1.5rem;
    position: relative;
    width: 100%;
}

.testi-slider-track {
    display: flex;
    align-items: center;
    gap: 2rem;
    width: max-content;
    transition: transform 0.5s cubic-bezier(0, 0, 0.2, 1);
    will-change: transform;
}

.testi-card {
    background: #fff;
    border-radius: 24px;
    border: 1px solid #efeae4;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    padding: 2.2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 650px;
    min-height: auto;
    flex-shrink: 0;
    transition: all 0.4s ease;
    transform: scale(0.96);
    opacity: 0.4;
    cursor: pointer;
    position: relative;
}

.testi-card::before {
    content: '“';
    position: absolute;
    top: 0.5rem;
    left: 1.5rem;
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 4.5rem;
    font-weight: 700;
    color: rgba(79, 96, 72, 0.2);
    line-height: 1;
    user-select: none;
    opacity: 0.3;
}

.testi-card.active {
    background: #fff;
    border-color: #efeae4;
    color: inherit;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transform: scale(1);
    opacity: 1;
}

.testi-stars {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-bottom: 1.25rem;
    color: var(--wellness-pink);
    z-index: 10;
}

.testi-stars svg {
    width: 18px !important;
    height: 18px !important;
}

.testi-card.active .testi-stars {
    color: var(--wellness-pink);
}

.testi-text {
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    font-size: 1.15rem;
    font-weight: 500;
    font-style: italic;
    letter-spacing: 0.02em;
    line-height: 1.8;
    color: rgba(63, 48, 40, 0.9);
    text-align: center;
    margin-bottom: 1.5rem;
    max-width: 32rem;
    margin-left: auto;
    margin-right: auto;
    transition: color 0.35s ease;
}

.testi-card.active .testi-text {
    color: rgba(63, 48, 40, 0.9);
}

.testi-service-tag {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    font-size: 11px;
    font-weight: 700;
    color: var(--wellness-green);
    background: rgba(79, 96, 72, 0.05);
    border: 1px solid rgba(79, 96, 72, 0.1);
    padding: 0.4rem 1rem;
    border-radius: 9999px;
    margin-bottom: 1.5rem;
    margin-left: auto;
    margin-right: auto;
    transition: all 0.35s ease;
}

.testi-service-tag svg {
    width: 12px !important;
    height: 12px !important;
}

.testi-card.active .testi-service-tag {
    color: var(--wellness-green);
    background: rgba(79, 96, 72, 0.05);
    border-color: rgba(79, 96, 72, 0.1);
}

.testi-user {
    display: flex;
    flex-direction: column;
    align-items: center;
    border-top: none;
    padding-top: 0;
    margin-top: auto;
    width: 100%;
}

.testi-card.active .testi-user {
    border-top-color: transparent;
}

.testi-user-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}

.testi-username {
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--wellness-green);
    margin: 0;
    text-align: center;
    transition: color 0.35s ease;
}

.testi-card.active .testi-username {
    color: var(--wellness-green);
}

.testi-dots {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: -0.5rem;
    margin-bottom: 1.5rem;
    position: relative;
    z-index: 10;
}

.testi-dot {
    width: 1.75rem;
    height: 3px;
    border-radius: 9999px;
    border: none;
    background: rgba(79, 96, 72, 0.15);
    cursor: pointer;
    padding: 0;
    transition: all 0.3s ease;
}

.testi-dot.active {
    background: var(--wellness-green);
}

@media (max-width: 767.98px) {
    .testi-slider-track { gap: 1rem; }
    .testi-card { width: 320px; min-height: auto; padding: 1.5rem; }
    .testi-text { font-size: 0.95rem; line-height: 1.6; max-width: 100%; }
    .testi-dots { margin-top: -0.5rem; margin-bottom: 1.5rem; }
    .testi-arrow { width: 36px; height: 36px; }
    .testi-arrow-prev { left: 2px; }
    .testi-arrow-next { right: 2px; }
}

/* ===== SECTION COMBO PACKAGES ===== */
.section-combo-packages {
    background: linear-gradient(180deg, #fbf8f5 0%, #fff2ec 48%, #fbf8f5 100%);
    padding: 4rem 0 4.8rem;
    border-top: 1px solid rgba(79, 96, 72, 0.05);
    position: relative;
    overflow: hidden;
    isolation: isolate;
}

.section-combo-packages::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    bottom: -1px;
    height: 150px;
    background: linear-gradient(180deg, rgba(251, 248, 245, 0), #fbf8f5 82%);
    pointer-events: none;
    z-index: 1;
}

/* Ambient Organic Blur Blobs */
.combo-blob-left {
    position: absolute;
    bottom: -6rem;
    left: -6rem;
    width: 380px;
    height: 380px;
    border-radius: 50%;
    background: radial-gradient(rgba(122, 91, 67, 0.1), transparent 70%);
    filter: blur(50px);
    pointer-events: none;
    z-index: 0;
    animation: combo-blob-pulse 4s ease-in-out infinite alternate;
}

.combo-blob-right {
    position: absolute;
    top: -4rem;
    right: -4rem;
    width: 320px;
    height: 320px;
    border-radius: 50%;
    background: radial-gradient(rgba(214, 104, 129, 0.08), transparent 70%);
    filter: blur(42px);
    pointer-events: none;
    z-index: 0;
    animation: combo-blob-pulse 3s ease-in-out infinite alternate;
}

.combo-center-glow {
    display: none; /* Removed centerpiece glow */
}

.section-combo-packages .container {
    position: relative;
    z-index: 2;
}

@keyframes combo-blob-pulse {
    0% { transform: scale(1); opacity: 0.8; }
    100% { transform: scale(1.05); opacity: 1; }
}

.combo-pkg-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.combo-pkg-eyebrow {
    display: block;
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    font-size: 11.5px;
    font-weight: 800;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    color: var(--wellness-pink);
    margin-bottom: 0.5rem;
}

.combo-pkg-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(1.65rem, 3.2vw, 2.45rem);
    font-weight: 800;
    color: var(--wellness-green);
    line-height: 1.25;
    margin-bottom: 0.5rem;
}

.combo-pkg-subtitle {
    font-family: 'Lora', Georgia, serif;
    font-size: 0.95rem;
    font-weight: 500;
    color: rgba(63, 48, 40, 0.8);
    max-width: 500px;
    margin: 0 auto;
    line-height: 1.625;
}

/* ---- Slider Outer ---- */
.combo-slider-outer {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    max-width: 62rem; /* max-w-4xl */
    margin: 0 auto;
}

/* ---- Arrow buttons ---- */
.combo-arrow {
    position: absolute;
    flex-shrink: 0;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 1px solid rgba(79, 96, 72, 0.15);
    background: #fff;
    color: var(--wellness-green);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    z-index: 20;
}
.combo-arrow-prev { left: 0; }
.combo-arrow-next { right: 0; }

@media (min-width: 992px) {
    .combo-arrow-prev { left: -3rem; }
    .combo-arrow-next { right: -3rem; }
}

.combo-arrow:hover {
    background: var(--wellness-green);
    color: #fff;
    border-color: var(--wellness-green);
}

.combo-slider-container {
    width: 100%;
    overflow: hidden;
    padding: 1rem 0.5rem;
    position: relative;
    user-select: none;
}

/* ---- Track ---- */
.combo-slider-track {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    width: max-content;
    transition: transform 0.5s cubic-bezier(0, 0, 0.2, 1);
    will-change: transform;
    position: relative;
}

/* ---- Card ---- */
.combo-pkg-card {
    background: #fff;
    border-radius: 24px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    width: 330px;
    flex-shrink: 0;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
    transform: scale(0.9);
    opacity: 0.6;
    cursor: pointer;
}

.combo-pkg-card:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); /* hover:shadow-xl */
}

.combo-pkg-card.active {
    transform: scale(1);
    opacity: 1;
    background: #fff;
    border-color: rgba(79, 96, 72, 0.3);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 0 0 2px rgba(79, 96, 72, 0.05);
}

/* ---- Card Image ---- */
.combo-card-img-wrapper {
    position: relative;
    width: 100%;
    height: 220px;
    overflow: hidden;
    background: rgba(122, 91, 67, 0.05); /* bg-brown/5 */
}
.combo-card-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.combo-pkg-card:hover .combo-card-img-wrapper img {
    transform: scale(1.05);
}

.combo-duration-badge {
    position: absolute;
    top: 0.75rem;
    left: 0.75rem;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(12px);
    border-radius: 9999px;
    padding: 0.375rem 0.75rem;
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    font-size: 10px;
    font-weight: 700;
    color: var(--wellness-green);
    display: flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.combo-pkg-card.active .combo-duration-badge {
    background: rgba(255, 255, 255, 0.92);
    color: var(--wellness-green);
}

/* ---- Card Body ---- */
.combo-card-body {
    padding: 1.4rem;
    display: flex;
    flex-direction: column;
    height: 245px;
}

.combo-card-category {
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    background: var(--wellness-pink-soft, #fbedf1);
    color: var(--wellness-pink);
    padding: 0.25rem 0.625rem;
    border-radius: 9999px;
    margin-bottom: 0.75rem;
    display: inline-block;
    line-height: 1;
    align-self: flex-start;
}

.combo-card-name {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 1rem;
    font-weight: 700;
    color: var(--wellness-green);
    margin-bottom: 0.5rem;
    line-height: 1.375;
}

.combo-pkg-card.active .combo-card-name {
    color: var(--wellness-green);
}

.combo-card-desc {
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    font-size: 0.82rem;
    line-height: 1.625;
    color: #7e746e;
    flex-grow: 1;
    margin-bottom: 1.1rem;
}

.combo-pkg-card.active .combo-card-desc {
    color: #7e746e;
}

.combo-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.375rem;
    margin-top: auto;
    padding-top: 1rem;
    border-top: 1px solid rgba(63, 48, 40, 0.05);
}

.combo-card-price {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 1rem;
    font-weight: 700;
    color: var(--wellness-green);
}

.combo-pkg-card.active .combo-card-price {
    color: var(--wellness-green);
}

.combo-card-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #e2dfd9;
    color: var(--wellness-green);
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.025em;
    padding: 0.375rem 1rem;
    border-radius: 9999px;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    border: none;
    outline: none;
    cursor: pointer;
}

.combo-card-btn:hover {
    background: var(--wellness-green);
    color: #fff;
    transform: scale(1.03);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    text-decoration: none;
}

.combo-pkg-card:not(.active) .combo-card-btn {
    background: #e2dfd9;
    color: var(--wellness-green);
    border: none;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.combo-pkg-card:not(.active) .combo-card-btn:hover {
    background: var(--wellness-green);
    color: #fff;
}

/* ---- Dots ---- */
.combo-dots {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1.15rem;
    margin-bottom: -0.2rem;
}
.combo-dot {
    width: 8px;
    height: 8px;
    border-radius: 9999px;
    border: none;
    background: rgba(79, 96, 72, 0.15);
    cursor: pointer;
    padding: 0;
    transition: all 0.3s ease;
}
.combo-dot.active {
    background: var(--wellness-green);
    width: 24px;
}

@media (max-width: 767.98px) {
    .combo-slider-track { gap: 1rem; }
    .combo-pkg-card { width: 286px; }
    .combo-arrow { width: 36px; height: 36px; }
}

/* ===== FLOATING TOAST NOTIFICATION ===== */
.floating-alert {
    position: fixed;
    top: 2rem;
    left: 50%;
    transform: translateX(-50%) translateY(-20px);
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0.9rem 1.4rem;
    border-radius: 50px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
    font-family: 'Inter', sans-serif;
    font-size: 0.85rem;
    font-weight: 500;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    opacity: 0;
    pointer-events: none;
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.floating-alert.show {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
    pointer-events: auto;
}

.floating-alert.success {
    background: rgba(253, 252, 251, 0.94);
    color: var(--wellness-green);
    border-left: 4px solid var(--wellness-green);
}

.floating-alert.error {
    background: rgba(255, 245, 245, 0.94);
    color: #dc3545;
    border-left: 4px solid #dc3545;
}

.floating-alert .alert-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.floating-alert.success .alert-icon {
    color: var(--wellness-green);
}

.floating-alert.error .alert-icon {
    color: #dc3545;
}

.floating-alert .alert-message {
    line-height: 1.4;
}

.floating-alert .alert-close {
    background: none;
    border: none;
    outline: none;
    color: #a0958e;
    font-size: 1.2rem;
    line-height: 1;
    cursor: pointer;
    padding: 0;
    margin-left: 8px;
    transition: color 0.2s ease;
}
.floating-alert .alert-close:hover {
    color: #333;
}

/* Home is tuned so browser zoom 80% feels like the old 100% composition. */
@media (min-width: 992px) {
    .home-page main > section > .container,
    .home-page .section-testimonials > .container,
    .home-page .testi-glow-wrap > .container {
        max-width: min(94vw, 1500px) !important;
    }

    .home-page .hero-outer-frame > .container,
    .home-page .section-arch-experience > .container,
    .home-page .section-combo-packages > .container,
    .home-page .section-why-choose > .container,
    .home-page .section-testimonials > .container,
    .home-page .testi-glow-wrap > .container {
        zoom: 1.18;
    }

    .home-page .premium-hero {
        min-height: 475px;
    }

    .home-page .hero-image-side-wrapper {
        min-height: 475px;
    }

    .home-page .hero-title {
        font-size: clamp(2.75rem, 5vw, 4rem);
    }

    .home-page .hero-description,
    .home-page .section-desc-luxury,
    .home-page .combo-pkg-subtitle,
    .home-page .testi-text {
        font-size: 1.12rem;
    }

    .home-page .hero-eyebrow,
    .home-page .combo-pkg-eyebrow,
    .home-page .testi-eyebrow {
        font-size: 0.95rem;
    }

    .home-page .hero-feature-title {
        font-size: 1.08rem;
    }

    .home-page .hero-feature-desc {
        font-size: 0.95rem;
    }

    .home-page .hero-cta-btn {
        font-size: 1rem;
        padding: 0.95rem 2.25rem;
    }

    .home-page .section-title-luxury,
    .home-page .combo-pkg-title,
    .home-page .testi-title {
        font-size: clamp(2.6rem, 4.6vw, 4.35rem);
    }

    .home-page .section-arch-experience,
    .home-page .section-combo-packages {
        padding-top: 5.5rem;
        padding-bottom: 5.5rem;
    }

    .home-page .section-why-choose {
        padding-top: 3rem;
        padding-bottom: 3rem;
    }

    .home-page .section-testimonials {
        padding-top: 0;
        padding-bottom: 4rem;
    }

    .home-page .testi-wave-top svg {
        height: 48px;
    }

    .home-page .testi-header {
        margin-top: 0.75rem;
    }

    .home-page .section-arch-experience .col-lg-4 {
        padding-left: clamp(2.25rem, 4vw, 4.5rem);
    }

    .home-page .section-arch-experience .col-lg-8 {
        padding-right: clamp(2.25rem, 4vw, 4.5rem);
    }

    .home-page .combo-slider-outer {
        max-width: 72rem;
    }

    .home-page .combo-pkg-card {
        width: 380px;
    }

    .home-page .combo-card-img-wrapper {
        height: 255px;
    }

    .home-page .combo-card-body {
        height: 285px;
    }

    .home-page .combo-card-name,
    .home-page .combo-card-price {
        font-size: 1.16rem;
    }

    .home-page .combo-card-desc {
        font-size: 0.98rem;
    }

    .home-page .combo-card-btn {
        font-size: 0.78rem;
        padding: 0.5rem 1.15rem;
    }
}
</style>

<script>
(function() {
    const track = document.getElementById('comboTrack');
    const container = track ? track.parentElement : null;
    const dots  = document.querySelectorAll('.combo-dot');
    const cards = track ? Array.from(track.querySelectorAll('.combo-pkg-card')) : [];
    let current = cards.findIndex(c => c.classList.contains('active'));
    if (current < 0) current = 0;

    function updateSlider() {
        if (!track || !container || !cards[current]) return;
        
        const containerWidth = container.offsetWidth;
        const activeCard = cards[current];
        const cardCenter = activeCard.offsetLeft + (activeCard.offsetWidth / 2);
        
        let tx = (containerWidth / 2) - cardCenter;
        
        track.style.transform = `translateX(${tx}px)`;
    }

    function goTo(idx) {
        if (cards.length === 0) return;
        
        // Loop infinitely in both directions
        let target = idx;
        if (target < 0) {
            target = cards.length - 1;
        } else if (target >= cards.length) {
            target = 0;
        }
        
        cards[current].classList.remove('active');
        if (dots[current]) dots[current].classList.remove('active');
        
        current = target;
        
        cards[current].classList.add('active');
        if (dots[current]) dots[current].classList.add('active');
        
        updateSlider();
    }

    document.getElementById('comboPrev')?.addEventListener('click', () => goTo(current - 1));
    document.getElementById('comboNext')?.addEventListener('click', () => goTo(current + 1));
    dots.forEach((dot, i) => dot.addEventListener('click', () => goTo(i)));
    cards.forEach((card, i) => card.addEventListener('click', () => goTo(i)));
    
    // Centering on initial load and resize
    updateSlider();
    setTimeout(updateSlider, 150); // safety fallback for dynamic layout adjustments
    window.addEventListener('resize', updateSlider);
})();

(function() {
    const track = document.getElementById('testiTrack');
    const container = track ? track.parentElement : null;
    const dots  = document.querySelectorAll('.testi-dot');
    const cards = track ? Array.from(track.querySelectorAll('.testi-card')) : [];
    let current = cards.findIndex(c => c.classList.contains('active'));
    if (current < 0) current = 0;

    let autoplayTimer = null;
    const autoplayInterval = 5000; // Rotate every 5 seconds for testimonials

    function updateSlider() {
        if (!track || !container || !cards[current]) return;
        
        const containerWidth = container.offsetWidth;
        const activeCard = cards[current];
        const cardCenter = activeCard.offsetLeft + (activeCard.offsetWidth / 2);
        
        let tx = (containerWidth / 2) - cardCenter;
        
        track.style.transform = `translateX(${tx}px)`;
    }

    function goTo(idx) {
        if (cards.length === 0) return;
        
        // Loop infinitely in both directions
        let target = idx;
        if (target < 0) {
            target = cards.length - 1;
        } else if (target >= cards.length) {
            target = 0;
        }
        
        cards[current].classList.remove('active');
        if (dots[current]) dots[current].classList.remove('active');
        
        current = target;
        
        cards[current].classList.add('active');
        if (dots[current]) dots[current].classList.add('active');
        
        updateSlider();
        resetAutoplay(); // Reset timer so a manual click doesn't slide immediately again
    }

    function startAutoplay() {
        if (cards.length <= 1) return;
        stopAutoplay();
        autoplayTimer = setInterval(() => {
            goTo(current + 1);
        }, autoplayInterval);
    }

    function stopAutoplay() {
        if (autoplayTimer) {
            clearInterval(autoplayTimer);
            autoplayTimer = null;
        }
    }

    function resetAutoplay() {
        stopAutoplay();
        startAutoplay();
    }

    document.getElementById('testiPrev')?.addEventListener('click', () => goTo(current - 1));
    document.getElementById('testiNext')?.addEventListener('click', () => goTo(current + 1));
    dots.forEach((dot, i) => dot.addEventListener('click', () => goTo(i)));
    cards.forEach((card, i) => card.addEventListener('click', () => goTo(i)));
    
    // Pause autoplay when hovering to read or interact
    const sliderOuter = document.querySelector('.testi-slider-outer');
    if (sliderOuter) {
        sliderOuter.addEventListener('mouseenter', stopAutoplay);
        sliderOuter.addEventListener('mouseleave', startAutoplay);
    }
    
    // Centering on initial load and resize
    updateSlider();
    setTimeout(updateSlider, 150); // safety fallback for dynamic layout adjustments
    window.addEventListener('resize', updateSlider);
    
    // Start autoplay initially
    startAutoplay();
})();
</script>


<!-- Cinematic Reveal & Scroll-Reveal Activator Scripts -->
<script>
    function activateMood(card) {
        document.querySelectorAll('.mood-card').forEach(el => el.classList.remove('active'));
        card.classList.add('active');
    }

    function closeAlert() {
        const alertEl = document.getElementById('floatingAlert');
        if (alertEl) {
            alertEl.classList.remove('show');
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        // 1. Trigger choreographed entry reveal
        setTimeout(() => {
            document.body.classList.add('js-revealed');
        }, 80);

        // 2. High-Performance Intersection Observer for scroll triggers
        const revealElements = document.querySelectorAll('.scroll-reveal');
        
        if ('IntersectionObserver' in window) {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.08 // Trigger when 8% of the element is in viewport
            };
            
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            
            revealElements.forEach(el => observer.observe(el));
        } else {
            // Fallback for older browsers
            revealElements.forEach(el => el.classList.add('is-visible'));
        }

        // 3. Floating alert toast auto-dismiss
        const alertEl = document.getElementById('floatingAlert');
        if (alertEl) {
            setTimeout(() => {
                alertEl.classList.add('show');
            }, 200);

            setTimeout(() => {
                alertEl.classList.remove('show');
            }, 4500);
        }
    });
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
