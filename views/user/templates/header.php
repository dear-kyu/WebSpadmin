<?php $login = sudahLogin(); ?>
<?php $aksiSaat = $_GET['action'] ?? 'home'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($judulHalaman ?? 'SPAdmin Spa Bandung') ?></title>
    <meta name="description" content="SPAdmin Spa Bandung — Reservasi layanan spa premium secara online. Pilih terapis, tentukan jadwal, dan nikmati relaksasi terbaik.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,400;1,600&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: { preflight: false },
            theme: {
                extend: {
                    colors: {
                        cream: '#f7f1e7',
                        'warm-white': '#fffdf8',
                        sage: '#73836d',
                        olive: '#4f6048',
                        'olive-light': '#637858',
                        brown: '#7a5b43',
                        'brown-dark': '#3f3028',
                        sand: '#e4d2b9',
                        'pink-blush': '#d66881',
                        'pink-soft': '#fbedf1',
                        'brown-soft': '#8b7d74',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        serif: ['Playfair Display', 'Georgia', 'serif'],
                        cursive: ['Cormorant Garamond', 'Georgia', 'serif'],
                        lora: ['Lora', 'Georgia', 'serif'],
                        plus: ['Plus Jakarta Sans', 'Inter', 'system-ui', 'sans-serif'],
                    },
                    backgroundImage: {
                        'radial': 'radial-gradient(var(--tw-gradient-stops))',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fade-in-up 0.3s cubic-bezier(0.16, 1, 0.3, 1) both; }
    </style>
</head>
<body class="<?= e($bodyClass ?? '') ?>">
    <header class="sticky top-0 z-50 bg-cream/95 backdrop-blur-md border-b border-brown-dark/10 transition-shadow hover:shadow-sm">
      <div class="max-w-[1500px] mx-auto px-3 sm:px-5 lg:px-6">
        <div class="flex items-center justify-between h-16">
          
          <a href="index.php?action=home" class="flex items-center gap-2.5 focus:outline-none cursor-pointer group text-decoration-none">
            <img src="assets/images/logo_spadmin.png" alt="Logo SPAdmin" class="w-8 h-8 object-contain transition-transform group-hover:scale-105 rounded-full">
            <div class="flex items-baseline gap-1 select-none">
              <span class="font-sans font-extrabold text-lg tracking-widest text-[#8b7d74]">SPADMIN</span>
              <span class="font-cursive italic text-lg font-semibold" style="color: #db83a6 !important;">Wellness</span>
            </div>
          </a>

          <nav class="hidden md:flex items-center gap-8">
            <a href="index.php?action=home" class="relative py-1 font-sans text-base font-semibold tracking-wider uppercase transition-colors hover:text-olive cursor-pointer text-decoration-none <?= $aksiSaat == 'home' ? 'text-olive font-extrabold' : 'text-brown-dark/70' ?>">
              Beranda
              <?php if ($aksiSaat == 'home'): ?>
                <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-6 h-[2px] bg-pink-blush rounded-full"></span>
              <?php endif; ?>
            </a>
            <a href="index.php?action=layanan" class="relative py-1 font-sans text-base font-semibold tracking-wider uppercase transition-colors hover:text-olive cursor-pointer text-decoration-none <?= in_array($aksiSaat, ['layanan', 'detail-layanan']) ? 'text-olive font-extrabold' : 'text-brown-dark/70' ?>">
              Layanan
              <?php if (in_array($aksiSaat, ['layanan', 'detail-layanan'])): ?>
                <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-6 h-[2px] bg-pink-blush rounded-full"></span>
              <?php endif; ?>
            </a>
          </nav>

          <div class="hidden md:flex items-center gap-1">
            <a href="index.php?action=keranjang" class="flex items-center gap-2 px-3 py-1.5 border border-olive/15 rounded-full hover:bg-olive/4 hover:border-olive/25 transition-all text-olive cursor-pointer group text-decoration-none">
              <span class="relative flex items-center justify-center w-7 h-7 rounded-full bg-olive/5 border border-olive/10 group-hover:scale-105 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5 text-olive"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                <?php 
                $count = cartCount();
                if ($count > 0): 
                ?>
                  <span class="nav-cart-count absolute -top-1 -right-1 min-w-4 h-4 rounded-full bg-brown-dark text-[9px] font-sans font-bold text-white flex items-center justify-center px-1 border border-cream animate-pulse">
                    <?= $count ?>
                  </span>
                <?php endif; ?>
              </span>
              <span class="font-sans text-base font-semibold tracking-wide">Keranjang</span>
            </a>

            <span class="w-[1px] h-6 bg-brown-dark/15 mx-3"></span>

            <?php if ($login): ?>
              <div class="relative">
                <button onclick="toggleNavDropdown()" class="flex items-center gap-2 bg-[#f1eae1] border border-olive/10 hover:bg-[#e8ded2] hover:border-olive/20 transition-all text-olive py-1.5 px-3.5 rounded-full cursor-pointer border-0">
                  <span class="flex items-center justify-center w-5 h-5 rounded-full bg-cream shadow-inner text-olive">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                  </span>
                  <span class="font-sans text-base font-semibold max-w-[140px] truncate"><?= e($_SESSION['nama'] ?? 'Pelanggan') ?></span>
                  <svg id="chevronIcon" class="w-3 h-3 text-olive/60 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                    <polyline points="6 9 12 15 18 9" />
                  </svg>
                </button>

                <div id="navDropdownMenu" class="absolute right-0 mt-2 w-48 bg-white border border-brown-dark/12 rounded-xl shadow-lg py-1.5 z-55 animate-fade-in-up d-none">
                  <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="admin/index.php" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-base font-bold text-olive hover:bg-cream/40 hover:text-olive transition-colors cursor-pointer text-decoration-none">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                      Halaman Admin
                    </a>
                  <?php endif; ?>
                  <a href="index.php?action=riwayat" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-base font-bold text-brown-dark hover:bg-cream/40 hover:text-olive transition-colors cursor-pointer text-decoration-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    Riwayat Reservasi
                  </a>
                  <a href="index.php?action=profil" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-base font-bold text-brown-dark hover:bg-cream/40 hover:text-olive transition-colors cursor-pointer text-decoration-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Profil
                  </a>
                  <div class="h-[1px] bg-brown-dark/10 my-1"></div>
                  <a href="index.php?action=logout" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-base font-bold text-red-600 hover:bg-red-50/50 transition-colors cursor-pointer text-decoration-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Keluar (Logout)
                  </a>
                </div>
              </div>
            <?php else: ?>
              <div class="flex items-center gap-2">
                <a href="index.php?action=login" class="font-sans text-base font-bold text-olive px-3 py-1.5 hover:text-pink-blush transition-colors cursor-pointer text-decoration-none">
                  Masuk (Login)
                </a>
                <a href="index.php?action=register" class="bg-pink-blush text-white font-sans text-base font-bold px-4 py-1.5 rounded-full hover:bg-[#c55770] transition-colors cursor-pointer shadow-sm text-decoration-none">
                  Daftar (Register)
                </a>
              </div>
            <?php endif; ?>
          </div>

          <div class="flex items-center gap-2 md:hidden">
            <a href="index.php?action=keranjang" class="relative p-1.5 text-olive hover:text-pink-blush transition-colors cursor-pointer text-decoration-none">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
              <?php if ($count > 0): ?>
                <span class="nav-cart-count absolute top-0 right-0 min-w-4 h-4 rounded-full bg-brown-dark text-[8px] font-sans font-bold text-white flex items-center justify-center px-1 border border-cream">
                  <?= $count ?>
                </span>
              <?php endif; ?>
            </a>
            <button onclick="toggleMobileMenu()" class="p-1.5 text-olive hover:text-pink-blush transition-colors cursor-pointer border-0 bg-transparent" id="btnMenuToggle">
              <svg id="iconMenu" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
              <svg id="iconClose" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 d-none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
          </div>

        </div>
      </div>

      <div id="mobileMenuDrawer" class="md:hidden bg-cream border-t border-brown-dark/10 py-3 px-4 animate-fade-in-up d-none">
        <div class="flex flex-col gap-2.5 pb-3">
          <a href="index.php?action=home" class="text-left font-sans text-lg font-semibold py-1.5 text-brown-dark/80 hover:text-olive cursor-pointer text-decoration-none">
            Beranda
          </a>
          <a href="index.php?action=layanan" class="text-left font-sans text-lg font-semibold py-1.5 text-brown-dark/80 hover:text-olive cursor-pointer text-decoration-none">
            Daftar Layanan
          </a>
        </div>
        
        <div class="border-t border-brown-dark/10 pt-3">
          <?php if ($login): ?>
            <div class="flex flex-col gap-2.5">
              <div class="text-base text-olive/60 font-medium">Logged in as: <?= e($_SESSION['nama'] ?? '') ?></div>
              <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <a href="admin/index.php" class="text-left font-sans text-lg font-bold py-1.5 text-olive cursor-pointer text-decoration-none">
                  Halaman Admin
                </a>
              <?php endif; ?>
              <a href="index.php?action=riwayat" class="text-left font-sans text-lg font-semibold py-1.5 text-brown-dark/80 hover:text-olive cursor-pointer text-decoration-none">
                Riwayat Reservasi
              </a>
              <a href="index.php?action=profil" class="text-left font-sans text-lg font-semibold py-1.5 text-brown-dark/80 hover:text-olive cursor-pointer text-decoration-none">
                Profil Saya
              </a>
              <a href="index.php?action=logout" class="text-left font-sans text-lg font-bold py-1.5 text-red-600 cursor-pointer text-decoration-none">
                Logout
              </a>
            </div>
          <?php else: ?>
            <div class="flex gap-2">
              <a href="index.php?action=login" class="flex-1 text-center font-sans text-base font-bold text-olive border border-olive/20 rounded-full py-2 cursor-pointer text-decoration-none">
                Login
              </a>
              <a href="index.php?action=register" class="flex-1 text-center font-sans text-base font-bold text-white bg-pink-blush rounded-full py-2 cursor-pointer text-decoration-none">
                Register
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </header>
    <main class="container-fluid p-0 m-0">

<script>
function toggleNavDropdown() {
    const menu = document.getElementById('navDropdownMenu');
    const icon = document.getElementById('chevronIcon');
    if (menu) {
        if (menu.classList.contains('d-none')) {
            menu.classList.remove('d-none');
            if(icon) icon.classList.add('rotate-180');
        } else {
            menu.classList.add('d-none');
            if(icon) icon.classList.remove('rotate-180');
        }
    }
}
function toggleMobileMenu() {
    const drawer = document.getElementById('mobileMenuDrawer');
    const iconMenu = document.getElementById('iconMenu');
    const iconClose = document.getElementById('iconClose');
    if (drawer) {
        if (drawer.classList.contains('d-none')) {
            drawer.classList.remove('d-none');
            iconMenu.classList.add('d-none');
            iconClose.classList.remove('d-none');
        } else {
            drawer.classList.add('d-none');
            iconMenu.classList.remove('d-none');
            iconClose.classList.add('d-none');
        }
    }
}
document.addEventListener('click', function(e) {
    const menu = document.getElementById('navDropdownMenu');
    const btn = menu ? menu.previousElementSibling : null;
    const icon = document.getElementById('chevronIcon');
    if (menu && btn && !menu.contains(e.target) && !btn.contains(e.target)) {
        menu.classList.add('d-none');
        if(icon) icon.classList.remove('rotate-180');
    }
});
</script>
