<?php

function initCart() {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        @session_start();
    }
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function getCart() {
    initCart();
    return $_SESSION['cart'];
}

function isInCart($layananId) {
    initCart();
    return isset($_SESSION['cart'][$layananId]);
}

function hasMainServiceInCart() {
    initCart();
    foreach ($_SESSION['cart'] as $item) {
        $kategori = $item['kategori'] ?? '';
        if ($kategori !== 'Tambahan' && $kategori !== 'Tambahan Bekam') {
            return true;
        }
    }
    return false;
}

function hasBekamServiceInCart() {
    initCart();
    foreach ($_SESSION['cart'] as $item) {
        $kategori = $item['kategori'] ?? '';
        $nama     = strtolower($item['nama'] ?? '');
        $isLayananUtama = $kategori !== 'Tambahan' && $kategori !== 'Tambahan Bekam';
        if ($isLayananUtama && strpos($nama, 'bekam') !== false) {
            return true;
        }
    }
    return false;
}

function addToCart($layananId, $namaLayanan, $harga, $kategori, $durasi, $media = '') {
    initCart();

    if (isInCart($layananId)) {
        return gagal('Layanan "' . $namaLayanan . '" sudah ada di keranjang Anda.');
    }

    if ($kategori === 'Tambahan Bekam' && !hasBekamServiceInCart()) {
        return gagal('Layanan tambahan bekam "' . $namaLayanan . '" hanya dapat dipilih jika Anda telah memilih setidaknya satu layanan utama bekam (seperti Bekam Kering, Bekam Basah, atau Pijat Sehat dan Bekam 9 Titik).');
    }

    $isAddon = ($kategori === 'Tambahan' || $kategori === 'Tambahan Bekam');
    if ($isAddon && !hasMainServiceInCart()) {
        return gagal('Layanan tambahan "' . $namaLayanan . '" hanya dapat dipilih jika Anda telah memilih setidaknya satu layanan utama.');
    }

    $_SESSION['cart'][$layananId] = [
        'id'       => (int)$layananId,
        'nama'     => $namaLayanan,
        'harga'    => (int)$harga,
        'kategori' => $kategori,
        'durasi'   => (int)$durasi,
        'media'    => $media,
    ];

    return sukses('Layanan "' . $namaLayanan . '" berhasil ditambahkan ke keranjang.');
}

function removeFromCart($layananId) {
    initCart();

    if (!isset($_SESSION['cart'][$layananId])) {
        return gagal('Layanan tidak ditemukan di keranjang.');
    }

    $namaLayanan = $_SESSION['cart'][$layananId]['nama'] ?? '';
    unset($_SESSION['cart'][$layananId]);

    if (!empty($_SESSION['cart']) && !hasMainServiceInCart()) {
        $_SESSION['cart'] = [];
        return sukses('Layanan utama "' . $namaLayanan . '" dihapus. Keranjang dikosongkan karena layanan tambahan memerlukan setidaknya satu layanan utama.', true);
    }

    if (!empty($_SESSION['cart']) && !hasBekamServiceInCart()) {
        return hapusBekamAddons($namaLayanan);
    }

    return sukses('Layanan "' . $namaLayanan . '" berhasil dihapus dari keranjang.', false);
}

function hapusBekamAddons($namaLayananDihapus) {
    $addonsYangDihapus = [];

    foreach ($_SESSION['cart'] as $id => $item) {
        if ($item['kategori'] === 'Tambahan Bekam') {
            $addonsYangDihapus[] = $item['nama'];
            unset($_SESSION['cart'][$id]);
        }
    }

    if (empty($addonsYangDihapus)) {
        return sukses('Layanan "' . $namaLayananDihapus . '" berhasil dihapus dari keranjang.', false);
    }

    if (empty($_SESSION['cart']) || !hasMainServiceInCart()) {
        $_SESSION['cart'] = [];
        return sukses('Layanan "' . $namaLayananDihapus . '" dihapus. Keranjang dikosongkan karena layanan tambahan bekam memerlukan layanan utama bekam.', true);
    }

    $namaAddons = implode(', ', $addonsYangDihapus);
    return sukses('Layanan "' . $namaLayananDihapus . '" dihapus. Layanan tambahan bekam (' . $namaAddons . ') otomatis dihapus karena memerlukan layanan utama bekam.', false);
}

function clearCart() {
    initCart();
    $_SESSION['cart'] = [];
    return sukses('Keranjang belanja berhasil dikosongkan.');
}

function cartTotal() {
    initCart();
    return array_sum(array_column($_SESSION['cart'], 'harga'));
}

function cartCount() {
    initCart();
    return count($_SESSION['cart']);
}

function sukses($pesan, $cleared = false) {
    $hasil = ['success' => true, 'message' => $pesan];
    if ($cleared !== false) {
        $hasil['cleared'] = $cleared;
    }
    return $hasil;
}

function gagal($pesan) {
    return ['success' => false, 'message' => $pesan];
}
?>
