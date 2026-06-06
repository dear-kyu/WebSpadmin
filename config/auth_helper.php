<?php

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        if (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Silahkan lakukan login dahulu',
                'cart_count' => function_exists('cartCount') ? cartCount() : 0,
                'login_required' => true,
            ]);
            exit;
        }

        $tujuan = $_SERVER['REQUEST_URI'] ?? 'index.php?action=home';
        header("Location: index.php?action=login&next=" . urlencode($tujuan) . "&pesan_error=" . urlencode("Silahkan lakukan login dahulu"));
        exit;
    }
}

function sudahLogin() {
    return isset($_SESSION['user_id']);
}

function e($nilai) {
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}

function rupiah($angka) {
    return 'Rp ' . number_format((int) $angka, 0, ',', '.');
}

function formatDurasi($durasi) {
    if ((int)$durasi === 0) {
        return 'Mengikuti treatment utama';
    }
    return (int)$durasi . ' menit';
}

function formatDurasiJam($totalMenit) {
    if ($totalMenit <= 0) return '';

    $jam = $totalMenit / 60;
    if ($jam == floor($jam)) {
        return $totalMenit . ' menit (' . (int)$jam . ' jam)';
    }
    return $totalMenit . ' menit (' . str_replace('.', ',', number_format($jam, 1)) . ' jam)';
}

function tanggalIndo($tanggal) {
    if (!$tanggal) return '-';
    return date('d M Y H:i', strtotime($tanggal));
}

function getSapaan() {
    $jam = (int) date('H');
    if ($jam >= 5  && $jam < 11) return 'Selamat Pagi';
    if ($jam >= 11 && $jam < 15) return 'Selamat Siang';
    if ($jam >= 15 && $jam < 18) return 'Selamat Sore';
    return 'Selamat Malam';
}

function getInisialTerapis($namaTerapis) {
    if (empty($namaTerapis)) return 'TR';

    $bagian = explode(' ', trim($namaTerapis));
    $inisial = strtoupper(substr($bagian[0], 0, 1));
    if (count($bagian) > 1) {
        $inisial .= strtoupper(substr($bagian[1], 0, 1));
    }
    return $inisial;
}

function mediaLayanan($mediaOrNama = '', $namaLayanan = '') {
    if (empty($namaLayanan)) {
        $namaLayanan = $mediaOrNama;
        $media = '';
    } else {
        $media = $mediaOrNama;
    }

    if (!empty($media)) {
        return strpos($media, 'http') === 0
            ? $media
            : 'uploads/layanan/' . $media;
    }

    $nama = strtolower($namaLayanan);
    if (strpos($nama, 'aroma') !== false) {
        return 'https://images.unsplash.com/photo-1600334129128-685c5582fd35?auto=format&fit=crop&w=900&q=80';
    }
    if (strpos($nama, 'scrub') !== false) {
        return 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=900&q=80';
    }
    if (strpos($nama, 'facial') !== false) {
        return 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=900&q=80';
    }
    if (strpos($nama, 'reflex') !== false) {
        return 'https://images.unsplash.com/photo-1515377905703-c4788e51af15?auto=format&fit=crop&w=900&q=80';
    }
    return 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=900&q=80';
}
?>
