<?php
$judulHalaman = 'Riwayat Reservasi - SPAdmin Spa Bandung';
$bodyClass = 'riwayat-page';

$allReservations = $reservasi ?? [];
$activeReservations = [];
$completedReservations = [];
$canceledReservations = [];
$paymentWaitingCount = 0;

function rrFormatTanggal($tanggal) {
    $timestamp = strtotime($tanggal);
    if (!$timestamp) return $tanggal;

    $months = [
        'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 'May' => 'Mei', 'Jun' => 'Jun',
        'Jul' => 'Jul', 'Aug' => 'Agt', 'Sep' => 'Sep', 'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des'
    ];

    $month = $months[date('M', $timestamp)] ?? date('M', $timestamp);
    return date('j', $timestamp) . ' ' . $month . ' ' . date('Y', $timestamp);
}

function rrMainServiceName($name) {
    $parts = array_map('trim', explode(',', (string) $name));
    return $parts[0] ?? $name;
}

function rrOtherServiceCount($name) {
    $parts = array_filter(array_map('trim', explode(',', (string) $name)));
    return max(0, count($parts) - 1);
}

function rrStatusMeta($item) {
    $reservationStatus = $item['status_reservasi'] ?? '';
    $paymentStatus = $item['status_pembayaran'] ?? '';

    if ($paymentStatus === 'DP Hangus' || $paymentStatus === 'Pembayaran Hangus') {
        return ['label' => $paymentStatus, 'class' => 'canceled'];
    }

    if (in_array($reservationStatus, ['Hangus', 'Dibatalkan', 'Ditolak'], true)) {
        return ['label' => 'Dibatalkan', 'class' => 'canceled'];
    }

    if ($reservationStatus === 'Selesai') {
        return ['label' => 'Selesai', 'class' => 'done'];
    }

    if ($paymentStatus === 'Belum Upload') {
        return ['label' => 'Belum Upload', 'class' => 'need-upload'];
    }

    if ($paymentStatus === 'Menunggu Validasi') {
        return ['label' => 'Menunggu Pembayaran', 'class' => 'payment'];
    }

    return ['label' => $reservationStatus ?: 'Upcoming', 'class' => 'pending'];
}

function rrFilterGroups($item) {
    $groups = ['all'];
    $reservationStatus = $item['status_reservasi'] ?? '';
    $paymentStatus = $item['status_pembayaran'] ?? '';

    if ($reservationStatus === 'Selesai') {
        $groups[] = 'completed';
    } elseif (in_array($reservationStatus, ['Hangus', 'Dibatalkan', 'Ditolak'], true)) {
        $groups[] = 'canceled';
    } elseif ($paymentStatus === 'Belum Upload') {
        $groups[] = 'payment';
    } else {
        $groups[] = 'upcoming';
    }

    return implode(' ', $groups);
}

foreach ($allReservations as $item) {
    $status = $item['status_reservasi'] ?? '';
    $payment = $item['status_pembayaran'] ?? '';

    if ($status === 'Selesai') {
        $completedReservations[] = $item;
    } elseif (in_array($status, ['Hangus', 'Dibatalkan', 'Ditolak'], true)) {
        $canceledReservations[] = $item;
    } elseif ($payment === 'Belum Upload') {
        $paymentWaitingCount++;
    } else {
        $activeReservations[] = $item;
    }
}

$totalCount = count($allReservations);
$activeCount = count($activeReservations);
$completedCount = count($completedReservations);
$canceledCount = count($canceledReservations);

$nearestReservation = null;
$nowTs = time();
foreach ($allReservations as $item) {
    $s = $item['status_reservasi'] ?? '';
    if ($s !== 'Diterima' && $s !== 'Dikonfirmasi') continue;
    
    // Harus sudah dibayar (status_pembayaran = verified atau Diterima)
    $payStatus = $item['status_pembayaran'] ?? '';
    if ($payStatus !== 'verified' && $payStatus !== 'Diterima') continue;
    
    $itemFullTs = strtotime(($item['tanggal'] ?? '') . ' ' . ($item['jam'] ?? '00:00'));
    if (!$itemFullTs) continue;

    $durasiMenit = max(0, (int) ($item['durasi'] ?? 0));
    $itemEndTs = $itemFullTs + ($durasiMenit * 60);
    if ($itemEndTs <= $nowTs) continue;

    $nearestFullTs = $nearestReservation ? strtotime(($nearestReservation['tanggal'] ?? '') . ' ' . ($nearestReservation['jam'] ?? '00:00')) : 0;
    
    if (!$nearestReservation || $itemFullTs < $nearestFullTs) {
        $nearestReservation = $item;
    }
}

include __DIR__ . '/../templates/header.php';
?>

<style>
    body {
        background: #f9f5f0;
    }
    .rr-page {
        font-family: 'Inter', sans-serif;
        background: #f9f5f0;
        color: #2d241e;
        padding-bottom: 4rem;
    }
    .rr-page button,
    .rr-page input,
    .rr-page select,
    .rr-page textarea {
        font-family: 'Inter', sans-serif;
    }
    .rr-page h1,
    .rr-page h2,
    .rr-page h3,
    .rr-page h4,
    .rr-page .rr-hero-title,
    .rr-page .rr-card-title,
    .rr-page .rr-empty-title,
    .rr-page .rr-cta-title,
    .rr-page .rr-cta-accent {
        font-family: 'Playfair Display', Georgia, serif;
    }
    .rr-hero {
        position: relative;
        min-height: 335px;
        display: flex;
        align-items: flex-end;
        overflow: hidden;
        background: #fffaf5;
        border-bottom: none;
    }
    .rr-hero::before {
        content: '';
        position: absolute;
        inset: 0 0 0 auto;
        width: 76%;
        background: url("assets/images/riwayat-hero.png") right center / cover no-repeat;
        opacity: 0.98;
        -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.08) 14%, rgba(0, 0, 0, 0.55) 32%, #000 52%);
        mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.08) 14%, rgba(0, 0, 0, 0.55) 32%, #000 52%);
    }
    .rr-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(255, 250, 245, 0.99) 0%, rgba(255, 250, 245, 0.88) 33%, rgba(255, 250, 245, 0.28) 64%, rgba(255, 250, 245, 0) 100%);
        pointer-events: none;
    }
    .rr-hero .container {
        position: relative;
        z-index: 1;
    }
    .rr-hero-copy {
        max-width: 500px;
        padding: 2.55rem 0 2.25rem;
    }
    .rr-hero-title {
        font-family: 'Playfair Display', Georgia, serif;
        color: #2f2925;
        font-size: clamp(2.05rem, 3.9vw, 3.45rem);
        font-weight: 600;
        line-height: 1.03;
        margin: 0 0 0.8rem;
    }
    .rr-hero-text {
        color: #6f625a;
        font-size: 0.82rem;
        line-height: 1.7;
        max-width: 360px;
        margin: 0;
    }
    .rr-hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.7rem;
        color: #d66881;
        font-size: 0.66rem;
        font-weight: 800;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        margin-bottom: 0.75rem;
    }
    .rr-hero-eyebrow::after {
        content: '';
        width: 52px;
        height: 1px;
        background: rgba(214, 104, 129, 0.35);
    }
    .rr-title-accent {
        color: #c4526b;
        font-style: italic;
    }
    .rr-tabs-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-top: 32px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .rr-tabs-wrapper {
        background: #ffffff;
        border: 1px solid rgba(122, 91, 67, 0.08);
        border-radius: 999px;
        padding: 6px;
        display: flex;
        gap: 4px;
        box-shadow: 0 4px 12px rgba(63, 48, 40, 0.02);
    }
    .rr-tab-btn {
        border: none;
        background: transparent;
        border-radius: 999px;
        padding: 7px 16px 7px 10px;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 500;
        color: #6b5e55;
        cursor: pointer;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .rr-tab-icon {
        width: 24px;
        height: 24px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: currentColor;
        background: rgba(122, 91, 67, 0.07);
        border: 1px solid rgba(122, 91, 67, 0.08);
        transition: inherit;
        flex: 0 0 auto;
    }
    .rr-tab-icon svg {
        width: 13px;
        height: 13px;
        display: block;
    }
    .rr-tab-label {
        line-height: 1;
    }
    .rr-tab-btn:hover {
        color: #3f3028;
        background: rgba(79, 96, 72, 0.06);
    }
    .rr-tab-btn:hover .rr-tab-icon {
        background: rgba(79, 96, 72, 0.1);
        border-color: rgba(79, 96, 72, 0.14);
    }
    .rr-tab-btn[data-filter="payment"] {
        color: #c4526b;
    }
    .rr-tab-btn[data-filter="payment"] .rr-tab-icon {
        background: rgba(214, 104, 129, 0.09);
        border-color: rgba(214, 104, 129, 0.12);
    }
    .rr-tab-btn.active {
        background: #4a5e43;
        color: #ffffff;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(74, 94, 67, 0.18);
    }
    .rr-tab-btn.active .rr-tab-icon {
        color: #4a5e43;
        background: rgba(255, 255, 255, 0.92);
        border-color: rgba(255, 255, 255, 0.65);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.72);
    }
    .rr-tab-btn[data-filter="payment"].active {
        color: #ffffff;
    }
    .rr-search-box-wrapper {
        position: relative;
        flex: 1;
        max-width: 320px;
        min-width: 260px;
    }
    .rr-search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0958e;
        pointer-events: none;
    }
    .rr-search-input {
        width: 100%;
        height: 44px;
        padding: 0 16px 0 44px;
        border: 1px solid rgba(122, 91, 67, 0.1);
        border-radius: 999px;
        background: #ffffff;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 400;
        color: #3f3028;
        box-shadow: 0 4px 12px rgba(63, 48, 40, 0.02);
        outline: none;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .rr-search-input::placeholder {
        color: #a0958e;
    }
    .rr-search-input:focus {
        border-color: #73836d;
        box-shadow: 0 4px 12px rgba(115, 131, 109, 0.1);
    }
    .rr-layout-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 265px;
        gap: 28px;
        align-items: start;
        margin-top: 8px;
    }
    .rr-card-list-new {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .rr-card-new {
        display: flex;
        align-items: center;
        background: #ffffff;
        border: 1px solid rgba(122, 91, 67, 0.08);
        border-radius: 16px;
        padding: 18px 20px;
        box-shadow: 0 2px 8px rgba(63, 48, 40, 0.04);
        transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .rr-card-new:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(63, 48, 40, 0.07);
    }
    .rr-card-image-wrapper {
        width: 148px;
        height: 96px;
        border-radius: 10px;
        overflow: hidden;
        flex-shrink: 0;
    }
    .rr-card-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .rr-card-content-wrapper {
        flex: 1;
        margin-left: 20px;
        display: flex;
        flex-direction: column;
    }
    .rr-badge-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }
    .rr-status-badge {
        font-family: 'Inter', sans-serif;
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 3px 10px;
        border-radius: 999px;
        display: inline-block;
    }
    .rr-badge-terjadwal {
        background: #dbeafe;
        color: #1d4ed8;
    }
    .rr-badge-selesai {
        background: #e6f4e8;
        color: #3a6b46;
    }
    .rr-badge-belum-bayar {
        background: #fde8ef;
        color: #c4526b;
    }
    .rr-badge-dibatalkan {
        background: #f0ece9;
        color: #8a7669;
    }
    .rr-badge-pending {
        background: #fef3c7;
        color: #92400e;
    }
    .rr-booking-id {
        font-family: 'Inter', sans-serif;
        font-size: 12.5px;
        font-weight: 400;
        color: #9a8b82;
    }
    .rr-card-title-text {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 19px;
        font-weight: 700;
        color: #2e241e;
        margin: 0 0 7px 0;
        line-height: 1.25;
    }
    .rr-card-meta-row {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 6px;
        color: #8a7669;
        font-family: 'Inter', sans-serif;
        font-size: 12.5px;
        font-weight: 400;
    }
    .rr-meta-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .rr-meta-item svg {
        color: #b0a098;
        flex-shrink: 0;
    }
    .rr-therapist-text {
        font-family: 'Inter', sans-serif;
        font-size: 12.5px;
        color: #8a7669;
    }
    .rr-therapist-name {
        font-weight: 600;
        color: #2e241e;
    }
    .rr-card-divider-line {
        border-left: 1px dashed rgba(122, 91, 67, 0.18);
        align-self: stretch;
        margin: 0 20px;
    }
    .rr-card-pricing-wrapper {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: center;
        width: 150px;
        flex-shrink: 0;
    }

    .rr-billing-amount {
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 16px;
        color: #2e241e;
        margin-bottom: 10px;
    }
    .rr-btn-outline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 34px;
        padding: 0 18px;
        border: 1px solid rgba(122, 91, 67, 0.22);
        border-radius: 999px;
        color: #5f5047;
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
        background: transparent;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        width: 100%;
    }
    .rr-btn-outline:hover {
        background: #4a5e43;
        border-color: #4a5e43;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(74, 94, 67, 0.15);
    }
    .rr-btn-solid-burgundy {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 34px;
        padding: 0 18px;
        background: #c4526b;
        color: #ffffff;
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        text-decoration: none;
        border: none;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        width: 100%;
    }
    .rr-btn-solid-burgundy:hover {
        background: #a83d55;
        box-shadow: 0 4px 10px rgba(196, 82, 107, 0.25);
    }
    .rr-badge-ulasan {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        height: 34px;
        padding: 0 16px;
        background: #e8f5e9;
        border: 1px solid #c8e6c9;
        color: #2e7d32;
        font-family: 'Inter', sans-serif;
        font-size: 12.5px;
        font-weight: 600;
        border-radius: 999px;
        width: 100%;
        cursor: pointer;
    }
    .rr-badge-ulasan:hover {
        background: #dff0e1;
    }
    .rr-review-panel {
        display: none;
        background-color: #faf8f5;
        border: 1px solid #efeae4;
        border-radius: 12px;
        padding: 14px 16px;
        margin-top: 5px;
    }
    .rr-review-panel.is-open {
        display: block;
    }
    .rr-sidebar-wrapper {
        display: flex;
        flex-direction: column;
        gap: 16px;
        position: sticky;
        top: 24px;
    }
    .rr-sidebar-card {
        background: #ffffff;
        border: 1px solid rgba(122, 91, 67, 0.08);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(63, 48, 40, 0.04);
    }
    .rr-sidebar-card-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 16px;
        font-weight: 700;
        color: #2e241e;
        margin: 0 0 14px 0;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(122, 91, 67, 0.08);
    }
    .rr-sidebar-card-title-burgundy {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 16px;
        font-weight: 700;
        color: #c4526b;
        margin: 0 0 8px 0;
    }
    .rr-stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid rgba(122, 91, 67, 0.07);
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        color: #6b5e55;
    }
    .rr-stat-row:first-child {
        padding-top: 0;
    }
    .rr-stat-row:last-child {
        padding-bottom: 0;
        border-bottom: none;
    }
    .rr-stat-left {
        display: flex;
        align-items: center;
        gap: 9px;
    }
    .rr-stat-left svg {
        color: #a0958e;
        flex-shrink: 0;
    }
    .rr-stat-left-warning svg {
        color: #c4526b;
    }
    .rr-stat-value {
        font-weight: 600;
        color: #2e241e;
        font-size: 13.5px;
    }
    .rr-stat-value-warning {
        font-weight: 600;
        color: #c4526b;
        font-size: 13.5px;
    }
    .rr-help-text-new {
        font-family: 'Inter', sans-serif;
        font-size: 12.5px;
        color: #8a7669;
        line-height: 1.55;
        margin: 0 0 14px 0;
    }
    .rr-help-wa-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 38px;
        border: 1px solid rgba(122, 91, 67, 0.22);
        background: transparent;
        color: #5f5047;
        font-family: 'Inter', sans-serif;
        font-size: 12.5px;
        font-weight: 500;
        border-radius: 999px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        width: 100%;
    }
    .rr-help-wa-btn:hover {
        background: #4a5e43;
        border-color: #4a5e43;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(74, 94, 67, 0.15);
    }
    .rr-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.55rem;
        margin-top: 1.5rem;
    }
    .rr-pagination-button {
        border: 1px solid rgba(122, 91, 67, 0.12);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.82);
        color: #5f5047;
        font-size: 12px;
        font-weight: 700;
        min-width: 38px;
        min-height: 36px;
        padding: 0.65rem 0.95rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.18s ease;
        cursor: pointer;
    }
    .rr-pagination-button:hover:not(:disabled) {
        background: #4a5e43;
        border-color: #4a5e43;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(74, 94, 67, 0.15);
    }
    .rr-pagination-button.active {
        background: #4a5e43;
        border-color: #4a5e43;
        color: #ffffff;
    }
    .rr-pagination-button:disabled {
        cursor: not-allowed;
        opacity: 0.42;
    }
    .rr-filter-hidden, .rr-page-hidden {
        display: none !important;
    }
    .rr-empty {
        background: #ffffff;
        border: 1px dashed rgba(122, 91, 67, 0.18);
        border-radius: 20px;
        padding: 40px;
        color: #76675d;
        text-align: center;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        margin-top: 16px;
    }
    @media (min-width: 1200px) {
        .riwayat-page .rr-page {
            padding-bottom: 5rem;
        }
        .riwayat-page .rr-hero {
            min-height: 410px;
        }
        .riwayat-page .rr-hero .container,
        .riwayat-page .rr-page > .container {
            max-width: min(92vw, 1500px);
            padding-left: clamp(2rem, 3vw, 3.75rem);
            padding-right: clamp(2rem, 3vw, 3.75rem);
        }
        .riwayat-page .rr-hero-copy {
            max-width: 620px;
            padding: 3.2rem 0 3rem;
        }
        .riwayat-page .rr-hero-eyebrow {
            font-size: 0.82rem;
            margin-bottom: 1rem;
        }
        .riwayat-page .rr-hero-title {
            font-size: clamp(2.7rem, 4.8vw, 4.25rem);
            margin-bottom: 1rem;
        }
        .riwayat-page .rr-hero-text {
            max-width: 470px;
            font-size: 1rem;
            line-height: 1.72;
        }
        .riwayat-page .rr-tabs-container {
            margin-top: 2rem;
            margin-bottom: 1.25rem;
            gap: 1.25rem;
        }
        .riwayat-page .rr-tabs-wrapper {
            gap: 0.8rem;
        }
        .riwayat-page .rr-tab-btn {
            min-height: 48px;
            padding: 0.7rem 1rem 0.7rem 0.75rem;
        }
        .riwayat-page .rr-tab-icon {
            width: 34px;
            height: 34px;
        }
        .riwayat-page .rr-tab-label {
            font-size: 0.92rem;
        }
        .riwayat-page .rr-search-box-wrapper {
            min-width: 360px;
        }
        .riwayat-page .rr-search-input {
            height: 50px;
            font-size: 0.98rem;
            padding-left: 3rem;
        }
        .riwayat-page .rr-layout-grid {
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 2.2rem;
            margin-top: 1.25rem;
        }
        .riwayat-page .rr-card-list-new {
            gap: 1.25rem;
        }
        .riwayat-page .rr-card-new {
            padding: 1.45rem 1.55rem !important;
            gap: 1.1rem !important;
            border-radius: 18px;
        }
        .riwayat-page .rr-card-image-wrapper {
            width: 182px;
            height: 118px;
            border-radius: 12px;
        }
        .riwayat-page .rr-card-content-wrapper {
            margin-left: 1.4rem;
        }
        .riwayat-page .rr-status-badge {
            font-size: 0.72rem;
            padding: 0.3rem 0.75rem;
        }
        .riwayat-page .rr-booking-id {
            font-size: 0.9rem;
        }
        .riwayat-page .rr-card-title-text {
            font-size: 1.45rem;
            margin-bottom: 0.5rem;
        }
        .riwayat-page .rr-card-meta-row,
        .riwayat-page .rr-therapist-text {
            font-size: 0.92rem;
        }
        .riwayat-page .rr-card-meta-row {
            gap: 1rem;
            margin-bottom: 0.45rem;
        }
        .riwayat-page .rr-card-divider-line {
            margin: 0 1rem !important;
        }
        .riwayat-page .rr-card-pricing-wrapper {
            min-width: 165px !important;
            width: 165px !important;
        }
        .riwayat-page .rr-billing-amount {
            font-size: 1.15rem;
            margin-bottom: 0.75rem !important;
        }
        .riwayat-page .rr-btn-outline,
        .riwayat-page .rr-btn-solid-burgundy,
        .riwayat-page .rr-badge-ulasan {
            height: 40px;
            font-size: 0.9rem;
        }
        .riwayat-page .rr-review-panel {
            padding: 1rem 1.1rem;
            font-size: 0.95rem;
        }
        .riwayat-page .rr-sidebar-wrapper {
            top: 96px;
            gap: 1.25rem;
        }
        .riwayat-page .rr-sidebar-card {
            padding: 1.45rem;
            border-radius: 18px;
        }
        .riwayat-page .rr-sidebar-card-title,
        .riwayat-page .rr-sidebar-card-title-burgundy {
            font-size: 1.25rem;
        }
        .riwayat-page .rr-stat-row {
            padding: 0.85rem 0;
            font-size: 0.95rem;
        }
        .riwayat-page .rr-stat-value,
        .riwayat-page .rr-stat-value-warning {
            font-size: 1.05rem;
        }
        .riwayat-page .rr-help-text-new,
        .riwayat-page .rr-next-meta-item,
        .riwayat-page .rr-next-therapist {
            font-size: 0.9rem;
        }
        .riwayat-page .rr-help-wa-btn {
            height: 42px;
            font-size: 0.9rem;
        }
        .riwayat-page .rr-next-img {
            height: 150px;
        }
        .riwayat-page .rr-next-service-name {
            font-size: 1.25rem;
        }
        .riwayat-page .rr-pagination {
            margin-top: 1.75rem;
        }
        .riwayat-page .rr-pagination-button {
            min-width: 42px;
            min-height: 40px;
            font-size: 0.88rem;
        }
        .riwayat-page .rr-bottom-cta {
            margin-top: 2.35rem;
            min-height: 128px;
            padding: 1.55rem 2.65rem 1.55rem clamp(11rem, 20vw, 14rem);
        }
        .riwayat-page .rr-cta-title {
            font-size: clamp(1.55rem, 2.25vw, 2.05rem);
        }
        .riwayat-page .rr-cta-text {
            font-size: 1.08rem;
        }
        .riwayat-page .rr-cta-button {
            min-height: 46px;
            min-width: 210px;
            font-size: 0.95rem;
        }
    }
    @media (max-width: 991.98px) {
        .rr-layout-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }
        .rr-sidebar-wrapper {
            position: static;
            flex-direction: row;
            gap: 20px;
        }
        .rr-sidebar-card {
            flex: 1;
        }
    }
    @media (max-width: 767.98px) {
        .rr-hero {
            min-height: 315px;
            align-items: flex-end;
        }
        .rr-hero::before {
            width: 100%;
            opacity: 0.42;
            -webkit-mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.18), #000);
            mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.18), #000);
        }
        .rr-hero::after {
            background: rgba(255, 250, 245, 0.72);
        }
        .rr-hero-copy {
            padding: 2.2rem 0;
        }
        .rr-tabs-container {
            flex-direction: column;
            align-items: stretch;
            gap: 16px;
        }
        .rr-tabs-wrapper {
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: none;
        }
        .rr-tab-btn {
            padding: 7px 14px 7px 9px;
            gap: 7px;
        }
        .rr-tab-icon {
            width: 22px;
            height: 22px;
        }
        .rr-tabs-wrapper::-webkit-scrollbar {
            display: none;
        }
        .rr-search-box-wrapper {
            max-width: 100%;
        }
        .rr-card-new {
            flex-direction: column;
            align-items: flex-start;
            padding: 16px;
        }
        .rr-card-image-wrapper {
            width: 100%;
            height: 160px;
            margin-bottom: 16px;
        }
        .rr-card-content-wrapper {
            margin-left: 0;
            width: 100%;
            margin-bottom: 16px;
        }
        .rr-card-divider-line {
            display: none;
        }
        .rr-card-pricing-wrapper {
            width: 100%;
            align-items: flex-start;
            border-top: 1px solid rgba(122, 91, 67, 0.08);
            padding-top: 16px;
        }
        .rr-card-pricing-wrapper .rr-billing-amount {
            margin-bottom: 8px;
        }
        .rr-sidebar-wrapper {
            flex-direction: column;
        }
    }
    .rr-next-img {
        width: 100%;
        height: 106px;
        border-radius: 10px;
        object-fit: cover;
        margin-bottom: 12px;
    }
    .rr-next-service-name {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 15px;
        font-weight: 700;
        color: #2e241e;
        margin: 0 0 10px 0;
        line-height: 1.3;
    }
    .rr-next-meta {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 10px;
    }
    .rr-next-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-family: 'Inter', sans-serif;
        font-size: 12.5px;
        color: #8a7669;
    }
    .rr-next-meta-item svg {
        color: #b0a098;
        flex-shrink: 0;
    }
    .rr-next-therapist {
        font-family: 'Inter', sans-serif;
        font-size: 12.5px;
        color: #8a7669;
        margin-bottom: 14px;
    }
    .rr-next-therapist span {
        font-weight: 600;
        color: #2e241e;
    }
    .rr-next-empty {
        text-align: center;
        padding: 14px 0;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        color: #8a7669;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .rr-bottom-cta {
        margin-top: 2.15rem;
        display: grid;
        grid-template-columns: 1fr auto;
        align-items: center;
        gap: 1.8rem;
        min-height: 104px;
        padding: 1.15rem 2.35rem 1.15rem clamp(10.4rem, 20vw, 13.2rem);
        background:
            linear-gradient(90deg, rgba(255, 250, 245, 0.24), rgba(255, 250, 245, 0.94)),
            url("assets/images/riwayat-cta-leaf.png") -8.2rem center / 560px auto no-repeat,
            #fbf7ef;
        border: 1px solid rgba(122, 91, 67, 0.12);
        border-radius: 18px;
        box-shadow: 0 12px 34px rgba(63, 48, 40, 0.045);
    }
    .rr-cta-title {
        font-family: 'Playfair Display', Georgia, serif;
        color: #1f1712;
        font-size: clamp(1.18rem, 1.75vw, 1.52rem);
        font-weight: 700;
        line-height: 1.12;
        margin: 0 0 0.25rem;
    }
    .rr-cta-text {
        margin: 0;
        color: #d66881;
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(0.9rem, 1.25vw, 1.05rem);
        font-style: italic;
        font-weight: 600;
        line-height: 1.2;
    }
    .rr-cta-button {
        background: #2b4c3f;
        color: #fff;
        text-decoration: none;
        border-radius: 999px;
        min-width: 188px;
        padding: 0.82rem 1.45rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', sans-serif;
        font-size: 0.78rem;
        font-weight: 700;
        white-space: nowrap;
        box-shadow: 0 10px 24px rgba(43, 76, 63, 0.18);
        transition: background 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
    }
    .rr-cta-button:hover {
        background: #20382f;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(43, 76, 63, 0.24);
    }
    @media (max-width: 767.98px) {
        .rr-bottom-cta {
            grid-template-columns: 1fr;
            min-height: auto;
            min-height: 112px;
            padding: 1.15rem 1.25rem 1.15rem 9.25rem;
            background:
                linear-gradient(90deg, rgba(255, 250, 245, 0.28), rgba(255, 250, 245, 0.96)),
                url("assets/images/riwayat-cta-leaf.png") -7.2rem center / 430px auto no-repeat,
                #fbf7ef;
        }
        .rr-cta-button {
            width: 100%;
        }
    }
</style>

<section class="rr-page">
    <div class="rr-hero">
        <div class="container">
            <div class="rr-hero-copy">
                <span class="rr-hero-eyebrow">Your Wellness Journey</span>
                <h1 class="rr-hero-title">Moments of Calm,<br>All in <span class="rr-title-accent">One Place.</span></h1>
                <p class="rr-hero-text">Pantau semua reservasi, status pembayaran, dan pengalaman relaksasi Anda dengan mudah.</p>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if (($_GET['pesan'] ?? '') === 'reservasi-berhasil'): ?>
            <div class="pesan-sukses mt-4">Reservasi berhasil dibuat. Silakan unggah bukti pembayaran.</div>
        <?php elseif (($_GET['pesan'] ?? '') === 'upload-berhasil'): ?>
            <div class="pesan-sukses mt-4">Bukti pembayaran berhasil diunggah.</div>
        <?php elseif (($_GET['pesan'] ?? '') === 'format-bukti-salah'): ?>
            <div class="pesan-error mt-4">Bukti pembayaran harus JPG atau PNG dengan ukuran maksimal 2 MB.</div>
        <?php elseif (($_GET['pesan'] ?? '') === 'upload-gagal'): ?>
            <div class="pesan-error mt-4">Upload bukti pembayaran gagal. Coba lagi.</div>
        <?php endif; ?>

        <div class="rr-tabs-container">
            <div class="rr-tabs-wrapper" aria-label="Filter riwayat reservasi">
                <button class="rr-tab-btn active" type="button" data-filter="all">
                    <span class="rr-tab-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="6" height="6" rx="1.5"></rect><rect x="14" y="4" width="6" height="6" rx="1.5"></rect><rect x="4" y="14" width="6" height="6" rx="1.5"></rect><rect x="14" y="14" width="6" height="6" rx="1.5"></rect></svg>
                    </span>
                    <span class="rr-tab-label">Semua (<?= $totalCount ?>)</span>
                </button>
                <button class="rr-tab-btn" type="button" data-filter="upcoming">
                    <span class="rr-tab-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect x="3" y="5" width="18" height="16" rx="3"></rect><path d="M3 10h18"></path><path d="m9 15 2 2 4-4"></path></svg>
                    </span>
                    <span class="rr-tab-label">Upcoming (<?= $activeCount ?>)</span>
                </button>
                <button class="rr-tab-btn" type="button" data-filter="payment">
                    <span class="rr-tab-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="13" rx="3"></rect><path d="M3 10h18"></path><path d="M7 15h.01"></path><path d="M11 15h2"></path></svg>
                    </span>
                    <span class="rr-tab-label">Menunggu Bayar (<?= $paymentWaitingCount ?>)</span>
                </button>
                <button class="rr-tab-btn" type="button" data-filter="completed">
                    <span class="rr-tab-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="m8.5 12.5 2.2 2.2 4.8-5.1"></path></svg>
                    </span>
                    <span class="rr-tab-label">Selesai (<?= $completedCount ?>)</span>
                </button>
                <button class="rr-tab-btn" type="button" data-filter="canceled">
                    <span class="rr-tab-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="m9 9 6 6"></path><path d="m15 9-6 6"></path></svg>
                    </span>
                    <span class="rr-tab-label">Dibatalkan (<?= $canceledCount ?>)</span>
                </button>
            </div>

            <div class="rr-search-box-wrapper">
                <svg class="rr-search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" id="rrSearchInput" class="rr-search-input" placeholder="Cari terapis, no ID, atau layanan...">
            </div>
        </div>

        <div class="rr-layout-grid">
            <div>
                <div class="rr-card-list-new">
                    <?php foreach ($allReservations as $item): ?>
                        <?php
                            $statusMeta = rrStatusMeta($item);
                            $otherCount = rrOtherServiceCount($item['nama_layanan']);
                            
                            $badgeClass = 'rr-badge-dibatalkan';
                            $statusLabel = $statusMeta['label'];
                            
                            if ($item['status_reservasi'] === 'Selesai') {
                                $badgeClass = 'rr-badge-selesai';
                                $statusLabel = 'SELESAI';
                            } elseif (in_array(($item['status_pembayaran'] ?? ''), ['DP Hangus', 'Pembayaran Hangus'], true)) {
                                $badgeClass = 'rr-badge-dibatalkan';
                                $statusLabel = strtoupper($item['status_pembayaran']);
                            } elseif ($item['status_reservasi'] === 'Hangus' || $item['status_reservasi'] === 'Dibatalkan' || $item['status_reservasi'] === 'Ditolak') {
                                $badgeClass = 'rr-badge-dibatalkan';
                                $statusLabel = 'DIBATALKAN';
                            } elseif ($item['status_pembayaran'] === 'Belum Upload') {
                                $badgeClass = 'rr-badge-belum-bayar';
                                $statusLabel = 'BELUM BAYAR';
                            } elseif ($item['status_reservasi'] === 'Diterima' || $item['status_reservasi'] === 'Dikonfirmasi') {
                                $badgeClass = 'rr-badge-terjadwal';
                                $statusLabel = 'TERJADWAL';
                            } else {
                                $badgeClass = 'rr-badge-pending';
                                $statusLabel = 'PENDING';
                            }
                        ?>
                        <article class="rr-card-new" data-groups="<?= e(rrFilterGroups($item)) ?>" style="flex-direction: column; align-items: stretch; gap: 14px;">
                            <div style="display: flex; align-items: center; width: 100%; flex-direction: row; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                                <div style="display: flex; align-items: center; flex: 1; min-width: 250px;">
                                    <div class="rr-card-image-wrapper">
                                        <img class="rr-card-image" src="<?= e(mediaLayanan($item['media'], $item['nama_layanan'])) ?>" alt="Gambar <?= e(rrMainServiceName($item['nama_layanan'])) ?>" onerror="this.onerror=null; this.src='assets/images/hero_spa_bg.jpg';">
                                    </div>

                                    <div class="rr-card-content-wrapper">
                                        <div class="rr-badge-row">
                                            <span class="rr-status-badge <?= $badgeClass ?>"><?= $statusLabel ?></span>
                                            <span class="rr-booking-id">Booking #<?= (int) $item['id'] ?></span>
                                        </div>

                                        <h3 class="rr-card-title-text">
                                            <?= e(rrMainServiceName($item['nama_layanan'])) ?>
                                            <?php if ($otherCount > 0): ?>
                                                <span class="rr-more-count" style="color: #d66881 !important; font-family: 'Inter', sans-serif !important; font-size: 11px !important; font-weight: 700 !important; margin-left: 4px !important;">+<?= $otherCount ?> lainnya</span>
                                            <?php endif; ?>
                                        </h3>

                                        <div class="rr-card-meta-row">
                                            <span class="rr-meta-item">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                                <?= e(rrFormatTanggal($item['tanggal'])) ?>
                                            </span>
                                            <span class="rr-meta-item">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                                <?= e(substr($item['jam'], 0, 5)) ?> WIB
                                            </span>
                                        </div>
                                        
                                        <span class="rr-therapist-text">Terapis: <span class="rr-therapist-name"><?= !empty($item['nama_terapis']) ? e($item['nama_terapis']) : 'Belum Ditugaskan' ?></span></span>
                                    </div>
                                </div>

                                <div class="rr-card-divider-line" style="margin: 0 10px;"></div>

                                <div class="rr-card-pricing-wrapper" style="width: auto; min-width: 140px; align-items: flex-end;">
                                    <span class="rr-billing-amount" style="margin-bottom: 8px;"><?= rupiah($item['harga']) ?></span>
                                    
                                    <?php if ($item['status_reservasi'] === 'Selesai'): ?>
                                        <?php if (ulasanSudahAda($conn, (int)$item['id'])): ?>
                                            <button class="rr-badge-ulasan" type="button" data-review-toggle="<?= (int) $item['id'] ?>">
                                                Lihat Ulasan
                                            </button>
                                        <?php else: ?>
                                            <a class="rr-btn-solid-burgundy" href="index.php?action=ulasan&id=<?= (int) $item['id'] ?>" style="background-color: var(--olive) !important; color: white !important;">
                                                Beri Ulasan
                                            </a>
                                        <?php endif; ?>
                                    <?php elseif ($statusLabel === 'BELUM BAYAR'): ?>
                                        <a class="rr-btn-solid-burgundy" href="index.php?action=pembayaran&id=<?= (int) $item['id'] ?>">
                                            Bayar Sekarang
                                        </a>
                                    <?php else: ?>
                                        <a class="rr-btn-outline" href="index.php?action=pembayaran&id=<?= (int) $item['id'] ?>">
                                            Lihat Detail
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if (in_array(($item['status_pembayaran'] ?? ''), ['DP Hangus', 'Pembayaran Hangus'], true)): ?>
                                <div style="padding: 10px 12px; border-radius: 10px; background: #fce8e6; color: #a61b1b; font-size: 0.82rem; font-weight: 600;">
                                    Reservasi dibatalkan karena pelanggan tidak datang. Seluruh pembayaran yang sudah masuk hangus dan tidak dapat dikembalikan.
                                </div>
                            <?php endif; ?>

                            <?php if ($item['status_reservasi'] === 'Selesai'): ?>
                                <?php
                                $stmtUl = $conn->prepare("SELECT rating, ulasan, balasan_admin FROM ulasan WHERE reservasi_id = ? OR (reservasi_id IS NULL AND user_id = ? AND id_layanan = ?) LIMIT 1");
                                $stmtUl->bind_param("iii", $item['id'], $item['user_id'], $item['layanan_id']);
                                $stmtUl->execute();
                                $ulRes = $stmtUl->get_result()->fetch_assoc();
                                $stmtUl->close();
                                ?>
                                <?php if (!empty($ulRes)): ?>
                                    <div class="rr-review-panel" id="rr-review-<?= (int) $item['id'] ?>">
                                        <div style="display: flex; gap: 3px; margin-bottom: 8px; color: #d66881;">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="<?= $i <= (int)$ulRes['rating'] ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                            <?php endfor; ?>
                                        </div>
                                        <div style="font-size: 0.88rem; color: #5f5047; font-style: italic; line-height: 1.5; display: flex; gap: 8px; align-items: flex-start;">
                                            <i class="fa-solid fa-quote-left" style="color: #d66881; font-size: 0.95rem; margin-top: 3px;"></i>
                                            <div>
                                                <strong>Ulasan Anda:</strong> "<?= htmlspecialchars($ulRes['ulasan']) ?>"
                                            </div>
                                        </div>
                                        
                                        <?php if (!empty($ulRes['balasan_admin'])): ?>
                                            <div style="background-color: #f1ebd9; border-left: 3px solid #db83a6; border-radius: 4px 8px 8px 4px; padding: 10px 12px; margin-top: 10px; font-size: 0.85rem; color: #2e241e; line-height: 1.55;">
                                                <div style="font-weight: 700; color: #d66881; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                                                    SPADMIN
                                                </div>
                                                <?= htmlspecialchars($ulRes['balasan_admin']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if (!$allReservations): ?>
                    <div class="rr-empty">Belum ada riwayat reservasi. Pilih layanan favorit Anda untuk memulai pengalaman relaksasi pertama.</div>
                <?php else: ?>
                    <div class="rr-empty rr-no-filter rr-filter-hidden">Tidak ada reservasi untuk filter ini atau hasil pencarian tersebut.</div>
                    <nav class="rr-pagination" data-pagination aria-label="Navigasi halaman riwayat reservasi"></nav>
                <?php endif; ?>

                <section class="rr-bottom-cta">
                    <div>
                        <h2 class="rr-cta-title">Take time for yourself.</h2>
                        <p class="rr-cta-text">Your next moment of calm is waiting.</p>
                    </div>
                    <a class="rr-cta-button" href="index.php?action=layanan">Buat Reservasi Baru</a>
                </section>
            </div>

            <aside class="rr-sidebar-wrapper">
                <div class="rr-sidebar-card">
                    <h2 class="rr-sidebar-card-title">Reservasi Terdekat</h2>
                    <?php if ($nearestReservation): ?>
                        <img class="rr-next-img"
                             src="<?= e(mediaLayanan($nearestReservation['media'], $nearestReservation['nama_layanan'])) ?>"
                             alt="<?= e(rrMainServiceName($nearestReservation['nama_layanan'])) ?>"
                             onerror="this.onerror=null; this.src='assets/images/hero_spa_bg.jpg';">
                        <h3 class="rr-next-service-name"><?= e(rrMainServiceName($nearestReservation['nama_layanan'])) ?></h3>
                        <div class="rr-next-meta">
                            <span class="rr-next-meta-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                <?= e(rrFormatTanggal($nearestReservation['tanggal'])) ?>
                            </span>
                            <span class="rr-next-meta-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <?= e(substr($nearestReservation['jam'], 0, 5)) ?> WIB
                            </span>
                        </div>
                        <p class="rr-next-therapist">Terapis: <span><?= !empty($nearestReservation['nama_terapis']) ? e($nearestReservation['nama_terapis']) : 'Belum Ditugaskan' ?></span></p>
                        <a class="rr-btn-outline" href="index.php?action=pembayaran&id=<?= (int) $nearestReservation['id'] ?>">Lihat Detail</a>
                    <?php else: ?>
                        <div class="rr-next-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.3;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <p style="margin: 0 0 12px;">Belum ada reservasi aktif.</p>
                            <a class="rr-btn-solid-burgundy" href="index.php?action=reservasi">Buat Reservasi</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="rr-sidebar-card">
                    <h2 class="rr-sidebar-card-title-burgundy">Butuh Bantuan?</h2>
                    <p class="rr-help-text-new">Ada kendala dengan reservasi atau pembayaran? Hubungi kami, tim SPAdmin siap membantu.</p>
                    <a class="rr-help-wa-btn" href="https://wa.me/6281234567890" target="_blank" rel="noopener">
                        <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 0.45rem;">
                            <path d="M20.52 3.48A11.86 11.86 0 0 0 12.07 0C5.49 0 .14 5.35.14 11.93c0 2.1.55 4.15 1.6 5.96L.04 24l6.25-1.64a11.9 11.9 0 0 0 5.78 1.47h.01c6.58 0 11.93-5.35 11.93-11.93a11.86 11.86 0 0 0-3.49-8.42ZM12.08 21.8h-.01a9.9 9.9 0 0 1-5.04-1.38l-.36-.21-3.71.97.99-3.62-.23-.37a9.85 9.85 0 0 1-1.51-5.26c0-5.44 4.43-9.87 9.88-9.87a9.8 9.8 0 0 1 6.98 2.9 9.8 9.8 0 0 1 2.89 6.97c0 5.45-4.43 9.88-9.88 9.88Zm5.42-7.39c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.64-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.61-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.27.49 1.7.63.71.23 1.36.2 1.87.12.57-.09 1.76-.72 2.01-1.42.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35Z"/>
                        </svg>
                        Hubungi Kami
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = Array.from(document.querySelectorAll('.rr-tab-btn'));
        const cards = Array.from(document.querySelectorAll('.rr-card-new'));
        const searchInput = document.getElementById('rrSearchInput');
        const empty = document.querySelector('.rr-no-filter');
        const pagination = document.querySelector('[data-pagination]');
        const cardsPerPage = 5;
        let currentFilter = 'all';
        let searchQuery = '';
        let currentPage = 1;

        function filteredCards() {
            return cards.filter(card => {
                const matchesTab = (card.dataset.groups || '').split(' ').includes(currentFilter);
                if (!matchesTab) return false;
                if (!searchQuery) return true;
                
                const titleText = card.querySelector('.rr-card-title-text')?.textContent.toLowerCase() || '';
                const therapistText = card.querySelector('.rr-therapist-name')?.textContent.toLowerCase() || '';
                const bookingText = card.querySelector('.rr-booking-id')?.textContent.toLowerCase() || '';
                
                return titleText.includes(searchQuery) || therapistText.includes(searchQuery) || bookingText.includes(searchQuery);
            });
        }

        function renderPagination(totalPages) {
            if (!pagination) return;
            pagination.innerHTML = '';
            pagination.classList.toggle('rr-filter-hidden', totalPages <= 1);
            if (totalPages <= 1) return;

            const makeButton = (label, page, options = {}) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'rr-pagination-button';
                button.textContent = label;
                button.disabled = !!options.disabled;
                button.classList.toggle('active', !!options.active);
                button.addEventListener('click', () => {
                    currentPage = page;
                    updatePagination();
                });
                return button;
            };

            pagination.appendChild(makeButton('Sebelumnya', Math.max(1, currentPage - 1), { disabled: currentPage === 1 }));

            for (let page = 1; page <= totalPages; page++) {
                pagination.appendChild(makeButton(String(page), page, { active: page === currentPage }));
            }

            pagination.appendChild(makeButton('Berikutnya', Math.min(totalPages, currentPage + 1), { disabled: currentPage === totalPages }));
        }

        function updatePagination() {
            const visibleCards = filteredCards();
            const totalPages = Math.max(1, Math.ceil(visibleCards.length / cardsPerPage));
            currentPage = Math.min(currentPage, totalPages);

            cards.forEach(card => card.classList.add('rr-page-hidden'));
            
            visibleCards.forEach((card, index) => {
                const startsAt = (currentPage - 1) * cardsPerPage;
                const endsAt = startsAt + cardsPerPage;
                card.classList.toggle('rr-page-hidden', index < startsAt || index >= endsAt);
            });

            renderPagination(totalPages);
            
            if (empty) {
                empty.classList.toggle('rr-filter-hidden', visibleCards.length !== 0);
            }
        }

        function applyFilter(filter) {
            currentFilter = filter;
            currentPage = 1;
            tabs.forEach(tab => tab.classList.toggle('active', tab.dataset.filter === filter));

            cards.forEach(card => {
                const groups = (card.dataset.groups || '').split(' ');
                const visible = groups.includes(filter);
                card.classList.toggle('rr-filter-hidden', !visible);
            });

            updatePagination();
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', () => applyFilter(tab.dataset.filter || 'all'));
        });

        if (searchInput) {
            searchInput.addEventListener('input', function (e) {
                searchQuery = e.target.value.toLowerCase().trim();
                currentPage = 1;
                updatePagination();
            });
        }

        document.querySelectorAll('[data-review-toggle]').forEach(button => {
            button.addEventListener('click', function () {
                const panel = document.getElementById('rr-review-' + this.dataset.reviewToggle);
                if (!panel) return;

                const isOpen = panel.classList.toggle('is-open');
                this.textContent = isOpen ? 'Tutup Ulasan' : 'Lihat Ulasan';
            });
        });

        applyFilter('all');
    });
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
