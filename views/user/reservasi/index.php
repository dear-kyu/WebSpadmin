<?php 
$judulHalaman = 'Reservasi - SPAdmin Spa Bandung'; 
$bodyClass = 'reservasi-page';
$cartItems = getCart();

// Generate the next 7 days dynamically for the premium date selector
$days = [];
$today = new DateTime();
$monthsIndo = [
    'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 'May' => 'Mei', 'Jun' => 'Jun',
    'Jul' => 'Jul', 'Aug' => 'Agt', 'Sep' => 'Sep', 'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des'
];
$daysIndo = [
    'Sun' => 'Min', 'Mon' => 'Sen', 'Tue' => 'Sel', 'Wed' => 'Rab', 'Thu' => 'Kam', 'Fri' => 'Jum', 'Sat' => 'Sab'
];

for ($i = 0; $i < 7; $i++) {
    $dayClone = clone $today;
    $dayClone->modify("+$i days");
    
    $rawMonth = $dayClone->format('M');
    $rawDayName = $dayClone->format('D');
    
    $days[] = [
        'value' => $dayClone->format('Y-m-d'),
        'dayNum' => $dayClone->format('d'),
        'dayName' => $daysIndo[$rawDayName] ?? $rawDayName,
        'monthName' => $monthsIndo[$rawMonth] ?? $rawMonth,
        'fullLabel' => $dayClone->format('j') . ' ' . ($monthsIndo[$rawMonth] ?? $rawMonth) . ' ' . $dayClone->format('Y')
    ];
}

// Calculate treatment subtotal
$treatmentSubtotal = cartTotal();
?>
<?php include __DIR__ . '/../templates/header.php'; ?>

<!-- Custom Premium Reservation CSS to implement the high-fidelity Figma mockup -->
<style>
    :root {
        --color-dark: #151210;
        --color-beige: #e8dfd8;
        --color-beige-dark: #d3c4b7;
        --color-light-cream: #fbf9f6;
        --color-brown-gold: #c3a88a;
        --color-text-dark: #221d1b;
        --color-text-muted: #6e645e;
        --wellness-green: #2b4c3f;
        --wellness-pink: #d66881;
        --wellness-bg: #f7f4f0;
    }

    body {
        background-color: #fcfaf7 !important; /* Extremely soft luxury background */
    }

    .reservasi-page-container {
        padding: 3rem 0 6rem;
    }

    /* Left Column Typography */
    .reservasi-eyebrow {
        font-family: 'Inter', sans-serif;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.28em;
        text-transform: uppercase;
        color: var(--wellness-pink);
        margin-bottom: 0.5rem;
        display: block;
    }

    .reservasi-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(2rem, 3.5vw, 2.8rem);
        font-weight: 700;
        color: var(--wellness-green);
        line-height: 1.2;
        margin-bottom: 2rem;
    }

    .booking-step-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--wellness-green);
        margin-bottom: 1.2rem;
        margin-top: 2.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .booking-step-number {
        font-family: 'Inter', sans-serif;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--wellness-pink);
        background: rgba(214, 104, 129, 0.1);
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* 1. Horizontal Date Selector Card Row */
    .date-selector-scroll {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding: 0.4rem 0.2rem 1.2rem;
        scrollbar-width: none; /* Hide standard Firefox scrollbar */
    }
    .date-selector-scroll::-webkit-scrollbar {
        display: none; /* Hide Chrome/Safari scrollbar */
    }

    .date-card {
        background: #ffffff;
        border: 1px solid #efeae4;
        border-radius: 14px;
        min-width: 72px;
        height: 82px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.35s cubic-bezier(0.25, 1, 0.5, 1);
        box-shadow: 0 4px 12px rgba(63, 48, 40, 0.015);
    }

    .date-card .date-day-name {
        font-family: 'Inter', sans-serif;
        font-size: 0.72rem;
        color: #8e847e;
        text-transform: uppercase;
        font-weight: 500;
        margin-bottom: 4px;
        transition: color 0.35s ease;
    }

    .date-card .date-day-num {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--wellness-green);
        line-height: 1;
        transition: color 0.35s ease;
    }

    .date-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(43, 76, 63, 0.08);
        border-color: rgba(43, 76, 63, 0.2);
    }

    .date-card.active {
        background: var(--wellness-green);
        border-color: var(--wellness-green);
        box-shadow: 0 12px 28px rgba(43, 76, 63, 0.18);
    }
    .date-card.active .date-day-name {
        color: rgba(255, 255, 255, 0.75);
    }
    .date-card.active .date-day-num {
        color: #ffffff;
    }

    /* 2. Time of Day Block Cards */
    .time-block-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 14px;
        margin-bottom: 1.8rem;
    }

    @media (max-width: 575.98px) {
        .time-block-row {
            grid-template-columns: 1fr;
            gap: 10px;
        }
    }

    .time-block-card {
        background: #ffffff;
        border: 1px solid #efeae4;
        border-radius: 16px;
        padding: 0.8rem 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        cursor: pointer;
        transition: all 0.35s cubic-bezier(0.25, 1, 0.5, 1);
        box-shadow: 0 4px 12px rgba(63, 48, 40, 0.015);
    }

    .time-block-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 1.5px dashed rgba(43, 76, 63, 0.22);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--wellness-green);
        margin-bottom: 10px;
        transition: all 0.35s ease;
    }

    .time-block-card .time-block-name {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1rem;
        font-weight: 700;
        color: var(--wellness-green);
        margin-bottom: 2px;
        transition: color 0.35s ease;
    }

    .time-block-card .time-block-range {
        font-family: 'Inter', sans-serif;
        font-size: 0.7rem;
        color: #8e847e;
        margin-bottom: 4px;
        transition: color 0.35s ease;
    }

    .time-block-card .time-block-slots {
        font-family: 'Inter', sans-serif;
        font-size: 0.68rem;
        font-weight: 700;
        color: var(--wellness-pink);
        background: rgba(214, 104, 129, 0.08);
        padding: 2px 8px;
        border-radius: 50px;
        transition: all 0.35s ease;
    }

    .time-block-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 24px rgba(43, 76, 63, 0.08);
        border-color: rgba(43, 76, 63, 0.2);
    }

    .time-block-card.active {
        background: var(--wellness-green);
        border-color: var(--wellness-green);
        box-shadow: 0 14px 32px rgba(43, 76, 63, 0.2);
    }
    .time-block-card.active .time-block-icon {
        border-color: rgba(255, 255, 255, 0.4);
        color: var(--wellness-pink);
    }
    .time-block-card.active .time-block-name {
        color: #ffffff;
    }
    .time-block-card.active .time-block-range {
        color: rgba(255, 255, 255, 0.7);
    }
    .time-block-card.active .time-block-slots {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.15);
    }

    /* 3. Time Slots Grid */
    .time-slots-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 480px) {
        .time-slots-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    .time-pill {
        background: #ffffff;
        border: 1px solid #efeae4;
        color: var(--wellness-green);
        font-family: 'Inter', sans-serif;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 0.65rem 0.5rem;
        border-radius: 50px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.01);
    }

    .time-pill:hover {
        border-color: var(--wellness-green);
        background: rgba(43, 76, 63, 0.02);
    }

    .time-pill.active {
        background: var(--wellness-green);
        border-color: var(--wellness-green);
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(43, 76, 63, 0.15);
    }

    .time-pill.disabled {
        background: #f5f2ef;
        border-color: #e5dfd8;
        color: #c0b7b1;
        cursor: not-allowed;
        pointer-events: none;
        text-decoration: line-through;
    }



    /* 5. Suite/Sanctuary Selection */
    .suite-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 12px;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 575.98px) {
        .suite-row {
            grid-template-columns: 1fr;
        }
    }

    .suite-card {
        background: #ffffff;
        border: 1px solid #efeae4;
        border-radius: 16px;
        padding: 1rem;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        gap: 4px;
        transition: all 0.35s cubic-bezier(0.25, 1, 0.5, 1);
        box-shadow: 0 4px 12px rgba(63, 48, 40, 0.015);
    }

    .suite-card .suite-card-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--wellness-green);
        margin: 0;
    }

    .suite-card .suite-card-sub {
        font-family: 'Inter', sans-serif;
        font-size: 0.72rem;
        color: #8e847e;
        margin: 0;
    }

    .suite-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(43, 76, 63, 0.08);
        border-color: rgba(43, 76, 63, 0.2);
    }

    .suite-card.active {
        border-color: var(--wellness-green);
        background: rgba(43, 76, 63, 0.03);
        box-shadow: 0 8px 24px rgba(43, 76, 63, 0.08);
    }

    /* Elegant Text Area */
    .premium-textarea {
        border: 1px solid #efeae4;
        border-radius: 16px;
        padding: 1rem;
        font-family: 'Inter', sans-serif;
        font-size: 0.85rem;
        color: var(--color-text-dark);
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(63, 48, 40, 0.01);
        transition: all 0.3s ease;
    }
    .premium-textarea:focus {
        border-color: var(--wellness-green);
        outline: none;
        box-shadow: 0 6px 18px rgba(43, 76, 63, 0.06);
    }

    /* ===================================================
       RIGHT COLUMN: YOUR JOURNEY (STUNNINGFIGMA THEME)
       =================================================== */
    .journey-sticky-container {
        margin-bottom: 2rem;
    }
    @media (min-width: 992px) {
        .journey-sticky-container {
            position: sticky;
            top: 70px;
            align-self: start;
        }
    }

    .journey-box {
        background-color: var(--wellness-green); /* solid premium forest green */
        border-radius: 26px;
        padding: 1.4rem 1.4rem 2rem 1.4rem; /* explicit padding-bottom of 2rem */
        color: #ffffff;
        box-shadow: 0 25px 60px rgba(43, 76, 63, 0.28);
        border: 1px solid rgba(255, 255, 255, 0.06);
        position: relative;
        overflow: hidden;
    }

    /* Subtle decorative elements in the journey box */
    .journey-box::before {
        content: '';
        position: absolute;
        top: -120px;
        right: -120px;
        width: 280px;
        height: 280px;
        background: radial-gradient(circle, rgba(214, 104, 129, 0.12) 0%, transparent 75%);
        filter: blur(40px);
        pointer-events: none;
    }

    .journey-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 1.2rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 0.8rem;
    }

    .journey-header-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--wellness-pink);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .journey-header-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.45rem;
        font-weight: 700;
        color: #ffffff;
        margin: 0;
        letter-spacing: -0.01em;
    }

    /* Journey Item Cards */
    .journey-items-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 1rem;
    }

    .journey-item-card {
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 0.9rem 1.1rem;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        position: relative;
    }

    .journey-item-icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.85);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.75rem;
        margin-top: 2px;
    }

    .journey-item-details {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        gap: 2px;
        min-width: 0;
    }

    .journey-item-details .item-label {
        font-family: 'Inter', sans-serif;
        font-size: 0.62rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.55);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .journey-item-details .item-value {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 0.95rem;
        font-weight: 700;
        color: #ffffff;
        margin: 0;
        word-break: break-word;
    }
    .journey-item-details .item-value.placeholder-value {
        color: rgba(255, 255, 255, 0.35);
        font-style: italic;
        font-weight: 500;
        font-family: 'Inter', sans-serif;
        font-size: 0.82rem;
    }

    .journey-item-right {
        margin-left: auto;
        font-family: 'Inter', sans-serif;
        font-size: 0.72rem;
        font-weight: 700;
        text-align: right;
        flex-shrink: 0;
        white-space: nowrap;
    }
    .journey-item-right .item-price {
        color: var(--wellness-pink);
        font-size: 0.88rem;
    }
    .journey-item-right .item-rating {
        color: #ffc107;
        display: flex;
        align-items: center;
        gap: 3px;
    }

    /* Enhancements Section */
    .enhance-title {
        font-family: 'Inter', sans-serif;
        font-size: 0.68rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.55);
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-bottom: 0.9rem;
    }

    .enhance-options-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 2rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 1.8rem;
    }

    .enhance-option {
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
    }

    .enhance-option-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Custom Checkbox Design */
    .enhance-checkbox-custom {
        width: 17px;
        height: 17px;
        border: 1.5px solid rgba(255, 255, 255, 0.4);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.25s ease;
        color: transparent;
        flex-shrink: 0;
    }

    .enhance-option:hover .enhance-checkbox-custom {
        border-color: #ffffff;
    }

    .enhance-checkbox-input {
        display: none;
    }

    .enhance-checkbox-input:checked + .enhance-checkbox-custom {
        background-color: var(--wellness-pink);
        border-color: var(--wellness-pink);
        color: #ffffff;
    }

    .enhance-option-name {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 0.92rem;
        font-weight: 700;
        color: #ffffff;
    }

    .enhance-option-price {
        font-family: 'Inter', sans-serif;
        font-size: 0.78rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.8);
    }

    /* Billing breakdown list */
    .billing-breakdown {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 1rem;
    }

    .billing-row {
        display: flex;
        justify-content: space-between;
        font-family: 'Inter', sans-serif;
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.7);
    }

    .billing-row.total-row {
        margin-top: 6px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 1rem;
        color: #ffffff;
    }

    .billing-row.total-row .total-label {
        font-family: 'Playfair Display', serif;
        font-size: 1.3rem;
        font-weight: 700;
    }

    .billing-row.total-row .total-amount {
        font-family: 'Playfair Display', serif;
        font-size: 1.38rem;
        font-weight: 800;
        color: var(--wellness-pink); /* rich coral pink */
    }

    /* CTA Confirm Button */
    .btn-confirm-journey {
        background-color: var(--wellness-pink);
        color: #ffffff;
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 0.88rem;
        letter-spacing: 0.04em;
        padding: 1.05rem 1.5rem;
        border-radius: 50px;
        border: none;
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 10px 24px rgba(214, 104, 129, 0.25);
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        outline: none;
        cursor: pointer;
        margin-bottom: 0;
    }

    .btn-confirm-journey:hover {
        background-color: #c55770;
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(214, 104, 129, 0.35);
    }

    /* ===== STICKY FLOATING CONFIRM BAR ===== */
    .sticky-confirm-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 999;
        background: rgba(43, 76, 63, 0.97);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        padding: 0.9rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        transform: translateY(100%);
        transition: transform 0.38s cubic-bezier(0.25, 1, 0.5, 1);
        box-shadow: 0 -8px 32px rgba(43, 76, 63, 0.22);
    }

    .sticky-confirm-bar.visible {
        transform: translateY(0);
    }

    .sticky-confirm-bar .sticky-bar-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
        flex: 1;
        min-width: 0;
    }

    .sticky-confirm-bar .sticky-bar-label {
        font-family: 'Inter', sans-serif;
        font-size: 0.65rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.5);
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    .sticky-confirm-bar .sticky-bar-total {
        font-family: 'Playfair Display', serif;
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--wellness-pink);
        white-space: nowrap;
    }

    .sticky-confirm-bar .btn-sticky-confirm {
        background-color: var(--wellness-pink);
        color: #ffffff;
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 0.85rem;
        letter-spacing: 0.03em;
        padding: 0.75rem 1.6rem;
        border-radius: 50px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 6px 18px rgba(214, 104, 129, 0.35);
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        cursor: pointer;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .sticky-confirm-bar .btn-sticky-confirm:hover {
        background-color: #c55770;
        transform: translateY(-1px);
        box-shadow: 0 8px 22px rgba(214, 104, 129, 0.45);
    }

    @media (max-width: 575.98px) {
        .sticky-confirm-bar {
            padding: 0.75rem 1rem;
        }
        .sticky-confirm-bar .sticky-bar-total {
            font-size: 0.95rem;
        }
        .sticky-confirm-bar .btn-sticky-confirm {
            padding: 0.65rem 1.1rem;
            font-size: 0.8rem;
        }
    }
    /* ===================================================
       NEW Figma-inspired Custom Calendar Modal Styling
       =================================================== */
    .calendar-trigger-card {
        background: #ffffff;
        border: 1px solid #efeae4;
        border-radius: 20px;
        padding: 1rem 1.4rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.35s cubic-bezier(0.25, 1, 0.5, 1);
        box-shadow: 0 4px 15px rgba(63, 48, 40, 0.015);
        margin-bottom: 1.5rem;
    }
    
    .calendar-trigger-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(43, 76, 63, 0.08);
        border-color: rgba(43, 76, 63, 0.2);
    }

    .calendar-trigger-content {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .calendar-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(43, 76, 63, 0.06);
        color: var(--wellness-green);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .calendar-trigger-card:hover .calendar-icon-box {
        background: var(--wellness-pink);
        color: #ffffff;
    }

    .calendar-trigger-details {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .calendar-trigger-sub {
        font-family: 'Inter', sans-serif;
        font-size: 0.65rem;
        font-weight: 700;
        color: #8e847e;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .calendar-trigger-value {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--wellness-green);
    }

    .btn-open-calendar {
        background: #fbf9f6;
        border: 1px solid #efeae4;
        color: var(--wellness-green);
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 0.78rem;
        padding: 0.55rem 1.1rem;
        border-radius: 50px;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-open-calendar:hover {
        background: var(--wellness-green);
        border-color: var(--wellness-green);
        color: #ffffff;
    }

    /* Tips Card Styling matching Figma Mockup */
    .calendar-tips-card {
        background: #fdf6f2; /* Soft pale pink/cream background */
        border: 1px solid rgba(214, 104, 129, 0.15);
        border-radius: 18px;
        padding: 1.2rem;
        transition: all 0.3s ease;
    }

    .tips-icon-box {
        color: var(--wellness-pink);
        flex-shrink: 0;
        margin-top: 2px;
    }

    .tips-card-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--wellness-green);
        margin-bottom: 4px;
    }

    .tips-card-text {
        font-family: 'Inter', sans-serif;
        font-size: 0.8rem;
        color: #6e645e;
        margin: 0;
        line-height: 1.4;
    }

    .calendar-subtips {
        font-family: 'Inter', sans-serif;
        font-size: 0.78rem;
        color: #6e645e;
        line-height: 1.45;
    }

    .subtips-icon {
        color: var(--wellness-pink);
    }

    /* CUSTOM CALENDAR MODAL POPUP */
    .calendar-modal {
        position: fixed;
        inset: 0;
        z-index: 1100;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .calendar-modal.active {
        display: flex;
    }

    .calendar-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(21, 18, 16, 0.5);
        backdrop-filter: blur(5px);
        transition: opacity 0.35s ease;
    }

    .calendar-modal-container {
        position: relative;
        z-index: 10;
        max-width: 480px;
        width: 90%;
        background: #ffffff;
        border-radius: 28px;
        box-shadow: 0 35px 80px rgba(43, 76, 63, 0.15);
        border: 1px solid #efeae4;
        overflow: hidden;
        animation: modalSlideUp 0.4s cubic-bezier(0.25, 1, 0.5, 1);
    }

    @keyframes modalSlideUp {
        from {
            transform: translateY(40px) scale(0.96);
            opacity: 0;
        }
        to {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
    }

    .calendar-modal-close {
        position: absolute;
        top: 20px;
        right: 22px;
        border: none;
        background: none;
        font-size: 1.8rem;
        color: #c0b7b1;
        cursor: pointer;
        z-index: 20;
        line-height: 1;
        transition: color 0.3s ease;
    }

    .calendar-modal-close:hover {
        color: var(--color-dark);
    }

    /* Figma Calendar Card Inside Modal */
    .figma-calendar-card {
        padding: 2.2rem 1.8rem;
    }

    .figma-calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.6rem;
        padding-right: 25px; /* Leave space for close button */
    }

    .figma-cal-month-year {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--wellness-green);
        margin: 0;
    }

    .figma-cal-nav-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #fbf9f6;
        border: 1px solid #efeae4;
        color: var(--wellness-green);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .figma-cal-nav-btn:hover {
        background: var(--wellness-pink);
        color: #ffffff;
        border-color: var(--wellness-pink);
    }

    .figma-calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
        font-family: 'Inter', sans-serif;
        font-size: 0.65rem;
        font-weight: 700;
        color: #8e847e;
        margin-bottom: 1rem;
        letter-spacing: 0.05em;
    }

    .figma-calendar-weekdays .weekend-header {
        color: var(--wellness-pink);
    }

    .figma-calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        row-gap: 8px;
        text-align: center;
    }

    .cal-day-cell {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', sans-serif;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--color-text-dark);
        margin: 0 auto;
        cursor: pointer;
        position: relative;
        transition: all 0.25s ease;
    }

    .cal-day-cell:hover:not(.prev-month-day):not(.next-month-day):not(.disabled-day) {
        background: #f5f0eb;
        color: var(--wellness-green);
    }

    .cal-day-cell.prev-month-day,
    .cal-day-cell.next-month-day {
        color: #c5bbb2;
        cursor: default;
        pointer-events: none;
    }

    .cal-day-cell.weekend-day:not(.prev-month-day):not(.next-month-day) {
        color: var(--wellness-pink);
    }

    .cal-day-cell.disabled-day {
        color: #e5dfd8;
        cursor: not-allowed;
        pointer-events: none;
        text-decoration: line-through;
    }

    .cal-day-cell.selected-day {
        background: var(--wellness-green) !important;
        color: #ffffff !important;
        box-shadow: 0 8px 20px rgba(43, 76, 63, 0.2);
    }

    .cal-day-cell.selected-day::after {
        content: '';
        position: absolute;
        bottom: 5px;
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background-color: #ffffff;
    }

    .figma-cal-divider {
        height: 1px;
        background: #efeae4;
        margin: 1.6rem 0;
    }

    /* Slots inside Modal Grid */
    .figma-slots-section {
        margin-bottom: 0.8rem;
    }

    .figma-slots-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .figma-slots-header .slots-header-left {
        display: flex;
        align-items: center;
        gap: 6px;
        font-family: 'Playfair Display', serif;
        font-size: 0.98rem;
        font-weight: 700;
        color: var(--wellness-green);
    }

    .figma-slots-header .slots-header-right {
        font-family: 'Inter', sans-serif;
        font-size: 0.72rem;
        color: #8e847e;
    }

    .figma-slots-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
    }

    @media (max-width: 480px) {
        .figma-slots-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* Modal Footer */
    .figma-modal-footer {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 1.8rem;
        border-top: 1px solid #efeae4;
        padding-top: 1.4rem;
    }

    .modal-footer-info {
        display: none;
    }

    .btn-confirm-selection {
        background: var(--wellness-green);
        color: #ffffff;
        border: none;
        border-radius: 50px;
        padding: 0.75rem 2.5rem;
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        width: 100%;
        text-align: center;
        transition: all 0.3s ease;
    }

    .btn-confirm-selection:hover {
        background: var(--color-dark);
        transform: translateY(-1px);
    }
    /* ===================================================
       NEW Minimalist Therapist Selector CSS
       =================================================== */
    .therapist-gender-selector {
        display: flex;
        gap: 12px;
        margin-bottom: 1.5rem;
    }

    .gender-pill {
        background: #ffffff;
        border: 1px solid #efeae4;
        color: var(--color-text-dark);
        font-family: 'Inter', sans-serif;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 0.65rem 1.6rem;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        outline: none;
    }

    .gender-pill:hover {
        border-color: var(--wellness-green);
        background: #fbf9f6;
    }

    .gender-pill.active {
        background: var(--wellness-green);
        border-color: var(--wellness-green);
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(43, 76, 63, 0.15);
    }

    /* Custom Dropdown Container */
    .custom-therapist-dropdown {
        position: relative;
        width: 100%;
        margin-bottom: 2rem;
    }

    .dropdown-trigger-card {
        background: #ffffff;
        border: 1px solid #efeae4;
        border-radius: 16px;
        padding: 1.1rem 1.4rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(63, 48, 40, 0.01);
    }

    .dropdown-trigger-card:hover {
        border-color: var(--wellness-green);
        box-shadow: 0 6px 18px rgba(43, 76, 63, 0.05);
    }

    .dropdown-trigger-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .dropdown-trigger-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--wellness-pink);
        color: #ffffff;
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .dropdown-trigger-value {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1rem;
        font-weight: 700;
        color: var(--wellness-green);
    }

    .dropdown-trigger-chevron {
        color: #8e847e;
        transition: transform 0.3s ease;
    }

    .custom-therapist-dropdown.open .dropdown-trigger-chevron {
        transform: rotate(180deg);
    }

    /* Floating Panel */
    .dropdown-panel-floating {
        position: absolute;
        top: 105%;
        left: 0;
        right: 0;
        background: #ffffff;
        border: 1px solid #efeae4;
        border-radius: 20px;
        box-shadow: 0 15px 45px rgba(63, 48, 40, 0.08);
        z-index: 100;
        display: none;
        overflow: hidden;
        animation: dropSlideUp 0.3s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .custom-therapist-dropdown.open .dropdown-panel-floating {
        display: block;
    }

    @keyframes dropSlideUp {
        from {
            transform: translateY(10px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .dropdown-items-container {
        max-height: 220px; /* limits dropdown height */
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #efeae4 transparent;
    }

    .dropdown-items-container::-webkit-scrollbar {
        width: 6px;
    }
    .dropdown-items-container::-webkit-scrollbar-track {
        background: transparent;
    }
    .dropdown-items-container::-webkit-scrollbar-thumb {
        background-color: #efeae4;
        border-radius: 10px;
    }

    /* Dropdown Item */
    .therapist-dropdown-item {
        padding: 1.1rem 1.4rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.25s ease;
        border-bottom: 1px solid #fbf9f6;
    }

    .therapist-dropdown-item:last-child {
        border-bottom: none;
    }

    .therapist-dropdown-item:hover {
        background: #fdfaf7;
    }

    .therapist-item-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .therapist-item-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(43, 76, 63, 0.06);
        color: var(--wellness-green);
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(43, 76, 63, 0.1);
        transition: all 0.25s ease;
    }

    .therapist-dropdown-item:hover .therapist-item-avatar {
        background: var(--wellness-pink);
        color: #ffffff;
        border-color: var(--wellness-pink);
    }

    .therapist-item-details {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .therapist-item-name {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 0.98rem;
        font-weight: 700;
        color: var(--wellness-green);
        margin: 0;
    }

    .therapist-item-meta {
        font-family: 'Inter', sans-serif;
        font-size: 0.72rem;
        color: #8e847e;
    }

    .therapist-item-right {
        text-align: right;
    }

    .therapist-item-rating {
        font-family: 'Inter', sans-serif;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--wellness-pink);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .therapist-dropdown-item.active {
        background: #f7f4f0;
    }
    .therapist-dropdown-item.active .therapist-item-name {
        color: var(--wellness-pink);
    }

    @media (min-width: 1200px) {
        .reservasi-page .reservasi-left-column {
            padding-right: clamp(1rem, 2vw, 2rem);
        }

        .reservasi-page .reservasi-left-column .reservasi-eyebrow {
            font-size: 0.86rem;
            margin-bottom: 0.7rem;
        }

        .reservasi-page .reservasi-left-column .reservasi-title {
            font-size: clamp(2.45rem, 4.1vw, 3.35rem);
            margin-bottom: 2.25rem;
        }

        .reservasi-page .reservasi-left-column .booking-step-title {
            font-size: 1.62rem;
            gap: 0.85rem;
            margin-bottom: 1.35rem;
        }

        .reservasi-page .reservasi-left-column .booking-step-number {
            width: 32px;
            height: 32px;
            font-size: 0.95rem;
        }

        .reservasi-page .reservasi-left-column .calendar-trigger-card,
        .reservasi-page .reservasi-left-column .calendar-tips-card {
            padding: 1.35rem 1.5rem;
            border-radius: 20px;
        }

        .reservasi-page .reservasi-left-column .calendar-icon-box,
        .reservasi-page .reservasi-left-column .tips-icon-box {
            width: 48px;
            height: 48px;
        }

        .reservasi-page .reservasi-left-column .calendar-trigger-sub {
            font-size: 0.84rem;
        }

        .reservasi-page .reservasi-left-column .calendar-trigger-value {
            font-size: 1.16rem;
        }

        .reservasi-page .reservasi-left-column .btn-open-calendar {
            min-height: 44px;
            padding: 0 1.2rem;
            font-size: 0.88rem;
        }

        .reservasi-page .reservasi-left-column .tips-card-title {
            font-size: 1.08rem;
        }

        .reservasi-page .reservasi-left-column .tips-card-text,
        .reservasi-page .reservasi-left-column .subtips-text {
            font-size: 0.95rem;
        }

        .reservasi-page .reservasi-left-column .time-block-row {
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .reservasi-page .reservasi-left-column .time-block-card {
            padding: 1rem 1.05rem;
            border-radius: 18px;
        }

        .reservasi-page .reservasi-left-column .time-block-icon {
            width: 38px;
            height: 38px;
        }

        .reservasi-page .reservasi-left-column .time-block-name {
            font-size: 1.15rem;
        }

        .reservasi-page .reservasi-left-column .time-block-range {
            font-size: 0.82rem;
        }

        .reservasi-page .reservasi-left-column .time-block-slots {
            font-size: 0.78rem;
            padding: 0.22rem 0.7rem;
        }

        .reservasi-page .reservasi-left-column .time-slots-grid {
            gap: 0.8rem;
            margin-bottom: 1.8rem;
        }

        .reservasi-page .reservasi-left-column .time-pill {
            padding: 0.82rem 0.65rem;
            font-size: 0.94rem;
        }

        .reservasi-page .reservasi-left-column .therapist-gender-selector {
            gap: 0.85rem;
        }

        .reservasi-page .reservasi-left-column .gender-pill {
            min-height: 46px;
            padding: 0 1.25rem;
            font-size: 0.95rem;
        }
    }
</style>

<div class="container-fluid w-100 reservasi-page-container">
    <div class="container">
        
        <?php if (isset($pesanError)): ?>
            <div class="alert alert-danger rounded-4 mb-4"><?= e($pesanError) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=simpan-reservasi" id="reservasiForm">
            <!-- Hidden inputs to bind the visual selections -->
            <input type="hidden" id="gender_terapis" name="gender_terapis" value="<?= htmlspecialchars($_POST['gender_terapis'] ?? 'Bebas') ?>" required>
            <input type="hidden" id="tanggal" name="tanggal" value="<?= htmlspecialchars($_POST['tanggal'] ?? $days[0]['value']) ?>" required>
            <input type="hidden" id="jam" name="jam" value="<?= htmlspecialchars($_POST['jam'] ?? '') ?>" required>
            
            <!-- Original textarea hidden, JavaScript will assemble the final metadata before submit -->
            <textarea id="catatan" name="catatan" class="d-none"><?= htmlspecialchars($_POST['catatan'] ?? '') ?></textarea>

            <div class="row g-5 align-items-start">
                
                <!-- LEFT COLUMN: DATE, TIME, THERAPIST, & SANCTUARY SELECTIONS -->
                <div class="col-lg-7 reservasi-left-column">
                    <span class="reservasi-eyebrow">CHOOSE YOUR MOMENT</span>
                    <h1 class="reservasi-title">When shall we expect you?</h1>

                    <!-- STEP 1: DATE SELECTOR -->
                    <h3 class="booking-step-title">
                        <span class="booking-step-number">1</span>
                        Pilih Tanggal Kunjungan
                    </h3>

                    <!-- Elegant Calendar Pop-up Trigger Card (Figma Style) -->
                    <div class="calendar-trigger-card" id="calendarTriggerBtn">
                        <div class="calendar-trigger-content">
                            <div class="calendar-icon-box">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </div>
                            <div class="calendar-trigger-details">
                                <span class="calendar-trigger-sub">Tanggal Kunjungan</span>
                                <span class="calendar-trigger-value" id="displaySelectedDate">Memuat tanggal...</span>
                            </div>
                        </div>
                        <button type="button" class="btn-open-calendar">
                            <span>Buka Kalender</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </button>
                    </div>

                    <!-- Tips Card Matching Figma mockup -->
                    <div class="calendar-tips-card">
                        <div class="tips-card-header d-flex align-items-start gap-3">
                            <div class="tips-icon-box">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </div>
                            <div>
                                <h5 class="tips-card-title">Reservasi lebih awal disarankan</h5>
                                <p class="tips-card-text">Untuk memastikan ketersediaan terapis dan waktu favorit Anda.</p>
                            </div>
                        </div>
                    </div>
                    <div class="calendar-subtips d-flex align-items-center gap-2 mt-3 mb-4">
                        <span class="subtips-text"><strong>Tips:</strong> Datang 10-15 menit lebih awal untuk pengalaman yang lebih optimal.</span>
                    </div>

                    <!-- STEP 2: TIME OF DAY BLOCK -->
                    <h3 class="booking-step-title">
                        <span class="booking-step-number">2</span>
                        Pilih Bagian Waktu Hari
                    </h3>

                    <?php
                    $globalInterval = intval(ambilPengaturan($conn, 'interval_reservasi', '30'));
                    $pMulai = ambilPengaturan($conn, 'sesi_pagi_mulai', '09:00');
                    $pSelesai = ambilPengaturan($conn, 'sesi_pagi_selesai', '11:30');
                    $pagiSlots = array_values(array_filter(explode(',', generasiSlotJam($pMulai, $pSelesai, $globalInterval)), 'trim'));
                    $sMulai = ambilPengaturan($conn, 'sesi_siang_mulai', '12:00');
                    $sSelesai = ambilPengaturan($conn, 'sesi_siang_selesai', '16:30');
                    $siangSlots = array_values(array_filter(explode(',', generasiSlotJam($sMulai, $sSelesai, $globalInterval)), 'trim'));
                    $soMulai = ambilPengaturan($conn, 'sesi_sore_mulai', '17:00');
                    $soSelesai = ambilPengaturan($conn, 'sesi_sore_selesai', '20:00');
                    $soreSlots = array_values(array_filter(explode(',', generasiSlotJam($soMulai, $soSelesai, $globalInterval)), 'trim'));
                    ?>

                    <div class="time-block-row">
                        <!-- Morning -->
                        <div class="time-block-card active" data-time-block="morning">
                            <div class="time-block-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path></svg>
                            </div>
                            <span class="time-block-name">Pagi Hari</span>
                            <span class="time-block-range">09.00 – 11.30 WIB</span>
                            <span class="time-block-slots" data-block-key="morning"><?php echo count(array_filter($pagiSlots, 'trim')); ?> slots</span>
                        </div>
                        <!-- Afternoon -->
                        <div class="time-block-card" data-time-block="afternoon">
                            <div class="time-block-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
                            </div>
                            <span class="time-block-name">Siang & Sore</span>
                            <span class="time-block-range">12.00 – 16.30 WIB</span>
                            <span class="time-block-slots" data-block-key="afternoon"><?php echo count(array_filter($siangSlots, 'trim')); ?> slots</span>
                        </div>
                        <!-- Evening -->
                        <div class="time-block-card" data-time-block="evening">
                            <div class="time-block-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path></svg>
                            </div>
                            <span class="time-block-name">Malam Hari</span>
                            <span class="time-block-range">17.00 – 20.00 WIB</span>
                            <span class="time-block-slots" data-block-key="evening"><?php echo count(array_filter($soreSlots, 'trim')); ?> slots</span>
                        </div>
                    </div>

                    <!-- STEP 3: TIME SLOTS GRID -->
                    <h3 class="booking-step-title">
                        <span class="booking-step-number">3</span>
                        Pilih Jam Kedatangan
                    </h3>

                    <!-- Dynamic Time Slots based on selected Block -->
                    <div class="time-slots-grid" id="timeSlotsContainer">
                        <!-- Morning Slots (Default active block) -->
                        <div class="time-pill" data-time="09:00">09:00 WIB</div>
                        <div class="time-pill" data-time="09:30">09:30 WIB</div>
                        <div class="time-pill" data-time="10:00">10:00 WIB</div>
                        <div class="time-pill disabled" data-time="10:30">10:30 WIB</div>
                        <div class="time-pill" data-time="11:00">11:00 WIB</div>
                        <div class="time-pill" data-time="11:30">11:30 WIB</div>
                    </div>

                    <!-- STEP 4: GENDER TERAPIS SELECTOR -->
                    <h3 class="booking-step-title" style="margin-top: 3rem;">
                        <span class="booking-step-number">4</span>
                        Pilih Jenis Kelamin Terapis
                    </h3>

                    <div class="therapist-gender-selector" style="margin-bottom: 2rem;">
                        <button type="button" class="gender-pill active" data-gender="Bebas">Bebas (Mana saja)</button>
                        <button type="button" class="gender-pill" data-gender="Perempuan">Perempuan</button>
                        <button type="button" class="gender-pill" data-gender="Laki-Laki">Laki-Laki</button>
                    </div>




                </div>

                <!-- RIGHT COLUMN: YOUR JOURNEY (LIVE STICKY SUMMARY BOX) -->
                <div class="col-lg-5 order-lg-2 order-1 journey-sticky-container">
                    <div class="journey-box">
                        
                        <div class="journey-header">
                            <span class="journey-header-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </span>
                            <h2 class="journey-header-title">Your Journey</h2>
                        </div>

                        <!-- Summary Cards List -->
                        <div class="journey-items-list">
                            <!-- 1. Treatment -->
                            <div class="journey-item-card">
                                <div class="journey-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                                </div>
                                <div class="journey-item-details">
                                    <span class="item-label">Treatment</span>
                                    <span class="item-value">
                                        <?php 
                                        $names = [];
                                        foreach ($cartItems as $item) {
                                            $names[] = $item['nama'];
                                        }
                                        echo e(implode(', ', $names));
                                        ?>
                                    </span>
                                </div>
                                <div class="journey-item-right">
                                    <span class="item-price"><?= rupiah($treatmentSubtotal) ?></span>
                                </div>
                            </div>

                            <!-- 2. Therapist -->
                            <div class="journey-item-card">
                                <div class="journey-item-icon" id="journey-terapis-avatar-box">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                </div>
                                <div class="journey-item-details">
                                    <span class="item-label">Therapist</span>
                                    <span class="item-value placeholder-value" id="journey-terapis-nama">Bebas (Mana saja)</span>
                                </div>
                                <div class="journey-item-right"></div>
                            </div>

                            <!-- 3. Date & Time -->
                            <div class="journey-item-card">
                                <div class="journey-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                </div>
                                <div class="journey-item-details">
                                    <span class="item-label">Date &amp; Time</span>
                                    <span class="item-value placeholder-value" id="journey-waktu-label">Pilih Tanggal &amp; Jam</span>
                                </div>
                                <div class="journey-item-right"></div>
                            </div>

                        </div>

                        <!-- Billing Breakdown -->
                        <div class="billing-breakdown">
                            <div class="billing-row">
                                <span>Total Perawatan</span>
                                <span id="bill-subtotal"><?= rupiah($treatmentSubtotal) ?></span>
                            </div>
                            
                            <div class="billing-row total-row">
                                <span class="total-label">Total</span>
                                <span class="total-amount" id="bill-total-amount"><?= rupiah($treatmentSubtotal) ?></span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-confirm-journey" id="confirmJourneyBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <span>Confirm Your Journey</span>
                        </button>

                    </div>
                </div>

            </div>
        </form>

    </div>
</div>

<!-- ===== STICKY FLOATING CONFIRM BAR ===== -->
<div class="sticky-confirm-bar" id="stickyConfirmBar">
    <div class="sticky-bar-info">
        <span class="sticky-bar-label">Total Pembayaran</span>
        <span class="sticky-bar-total" id="stickyBarTotal"><?= rupiah($treatmentSubtotal) ?></span>
    </div>
    <button type="button" class="btn-sticky-confirm" id="stickyConfirmBtn">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        <span>Confirm Your Journey</span>
    </button>
</div>

<!-- Custom Calendar Popup Modal -->
<div class="calendar-modal" id="calendarModal">
    <div class="calendar-modal-backdrop" id="calendarBackdrop"></div>
    <div class="calendar-modal-container">
        <button type="button" class="calendar-modal-close" id="closeCalendarBtn">&times;</button>
        <div class="figma-calendar-card">
            <div class="figma-calendar-header">
                <button type="button" class="figma-cal-nav-btn" id="prevMonthBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <h4 class="figma-cal-month-year" id="calMonthYear">Mei 2026</h4>
                <button type="button" class="figma-cal-nav-btn" id="nextMonthBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
            
            <div class="figma-calendar-weekdays">
                <span>SEN</span>
                <span>SEL</span>
                <span>RAB</span>
                <span>KAM</span>
                <span>JUM</span>
                <span class="weekend-header">SAB</span>
                <span class="weekend-header">MIN</span>
            </div>
            
            <div class="figma-calendar-days" id="calDaysGrid"></div>
            
            <div class="figma-modal-footer">
                <button type="button" class="btn-confirm-selection" id="confirmDateTimeBtn">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

<!-- High-Fidelity spa reservation interactive flow JS -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const hiddenGenderTerapisInput = document.getElementById('gender_terapis');
    const hiddenTanggalInput = document.getElementById('tanggal');
    const hiddenJamInput = document.getElementById('jam');
    const hiddenCatatanArea = document.getElementById('catatan');

    const timeBlockCards = document.querySelectorAll('.time-block-card');
    const timeSlotsContainer = document.getElementById('timeSlotsContainer');

    // Summary elements in Your Journey card
    const journeyTerapisNama = document.getElementById('journey-terapis-nama');
    const journeyTerapisAvatarBox = document.getElementById('journey-terapis-avatar-box');
    const journeyWaktuLabel = document.getElementById('journey-waktu-label');

    // Billing elements
    const billTotalAmount = document.getElementById('bill-total-amount');

    const treatmentSubtotal = <?= $treatmentSubtotal ?>;

    let selectedTimeLabel = '';
    let selectedTherapistName = '';

    // ===================================================
    // DYNAMIC FIGMA CALENDAR MODAL LOGIC (DEFENSIVE IMPLEMENTATION)
    // ===================================================
    const calendarModal = document.getElementById('calendarModal');
    const calendarTriggerBtn = document.getElementById('calendarTriggerBtn');
    const closeCalendarBtn = document.getElementById('closeCalendarBtn');
    const calendarBackdrop = document.getElementById('calendarBackdrop');
    const confirmDateTimeBtn = document.getElementById('confirmDateTimeBtn');
    const displaySelectedDate = document.getElementById('displaySelectedDate');

    const prevMonthBtn = document.getElementById('prevMonthBtn');
    const nextMonthBtn = document.getElementById('nextMonthBtn');

    // Parse the initial date safely
    let selectedYear = new Date().getFullYear();
    let selectedMonth = new Date().getMonth();
    let selectedDayNum = new Date().getDate();

    if (hiddenTanggalInput && hiddenTanggalInput.value) {
        const initialParts = hiddenTanggalInput.value.split('-');
        if (initialParts.length === 3) {
            selectedYear = parseInt(initialParts[0]) || selectedYear;
            selectedMonth = (parseInt(initialParts[1]) - 1) >= 0 ? (parseInt(initialParts[1]) - 1) : selectedMonth;
            selectedDayNum = parseInt(initialParts[2]) || selectedDayNum;
        }
    }

    let viewYear = selectedYear;
    let viewMonth = selectedMonth;
    let selectedDateLabel = '';

    // Initialize display selected date label safely
    try {
        const initialDateObj = new Date(selectedYear, selectedMonth, selectedDayNum);
        const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const formattedInitialDate = initialDateObj.toLocaleDateString('id-ID', dateOptions);
        if (displaySelectedDate) {
            displaySelectedDate.textContent = formattedInitialDate;
        }
        selectedDateLabel = formattedInitialDate;
        updateDateTimeSummary();
    } catch(e) {
        console.error("Gagal menginisialisasi label tanggal:", e);
    }

    // Modal Visibility Triggers
    const openModal = function(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        console.log("Membuka Modal Kalender...");
        if (calendarModal) {
            calendarModal.classList.add('active');
            renderCalendar();
        } else {
            console.error("Elemen calendarModal tidak ditemukan!");
        }
    };

    const closeModal = function(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (calendarModal) {
            calendarModal.classList.remove('active');
        }
    };

    // Bind triggers safely (both the card and the button inside it)
    if (calendarTriggerBtn) {
        calendarTriggerBtn.addEventListener('click', openModal);
    }

    const innerOpenBtn = document.querySelector('.btn-open-calendar');
    if (innerOpenBtn) {
        innerOpenBtn.addEventListener('click', openModal);
    }

    if (closeCalendarBtn) closeCalendarBtn.addEventListener('click', closeModal);
    if (calendarBackdrop) calendarBackdrop.addEventListener('click', closeModal);
    if (confirmDateTimeBtn) confirmDateTimeBtn.addEventListener('click', closeModal);

    // Prev/Next month triggers
    prevMonthBtn.addEventListener('click', function() {
        if (viewMonth === 0) {
            viewMonth = 11;
            viewYear--;
        } else {
            viewMonth--;
        }
        renderCalendar();
    });

    nextMonthBtn.addEventListener('click', function() {
        if (viewMonth === 11) {
            viewMonth = 0;
            viewYear++;
        } else {
            viewMonth++;
        }
        renderCalendar();
    });

    function renderCalendar() {
        const monthNames = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];
        
        document.getElementById('calMonthYear').textContent = monthNames[viewMonth] + ' ' + viewYear;
        
        const calDaysGrid = document.getElementById('calDaysGrid');
        calDaysGrid.innerHTML = '';
        
        // Calculate days logic
        const firstDay = new Date(viewYear, viewMonth, 1);
        let startDayOfWeek = firstDay.getDay(); // 0 (Sun) - 6 (Sat)
        // Convert to start on Monday (0=Mon, ..., 6=Sun)
        startDayOfWeek = startDayOfWeek === 0 ? 6 : startDayOfWeek - 1;
        
        const totalDaysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
        const totalDaysInPrevMonth = new Date(viewYear, viewMonth, 0).getDate();
        
        // Previous month days (greyed out)
        for (let i = startDayOfWeek - 1; i >= 0; i--) {
            const dayNum = totalDaysInPrevMonth - i;
            const cell = document.createElement('div');
            cell.className = 'cal-day-cell prev-month-day';
            cell.textContent = dayNum;
            calDaysGrid.appendChild(cell);
        }
        
        const today = new Date();
        const todayYear = today.getFullYear();
        const todayMonth = today.getMonth();
        const todayDay = today.getDate();
        
        // Current month days
        for (let day = 1; day <= totalDaysInMonth; day++) {
            const cell = document.createElement('div');
            cell.className = 'cal-day-cell';
            cell.textContent = day;
            
            const cellDate = new Date(viewYear, viewMonth, day);
            const dayOfWeek = cellDate.getDay(); // 0 = Sun, 6 = Sat
            
            if (dayOfWeek === 0 || dayOfWeek === 6) {
                cell.classList.add('weekend-day');
            }
            
            // Deterministic past date check (immune to timezone shifts)
            let isPast = false;
            if (viewYear < todayYear) {
                isPast = true;
            } else if (viewYear === todayYear) {
                if (viewMonth < todayMonth) {
                    isPast = true;
                } else if (viewMonth === todayMonth) {
                    if (day < todayDay) {
                        isPast = true;
                    }
                }
            }
            
            if (isPast) {
                cell.classList.add('disabled-day');
            } else {
                if (viewYear === selectedYear && viewMonth === selectedMonth && day === selectedDayNum) {
                    cell.classList.add('selected-day');
                }
                
                // Extremely reliable select handler with multi-event support
                const onDayClick = function(e) {
                    if (e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    console.log("Hari diklik:", day, "Bulan:", viewMonth + 1, "Tahun:", viewYear);
                    selectedYear = viewYear;
                    selectedMonth = viewMonth;
                    selectedDayNum = day;
                    
                    const paddedMonth = String(selectedMonth + 1).padStart(2, '0');
                    const paddedDay = String(selectedDayNum).padStart(2, '0');
                    const dateVal = `${selectedYear}-${paddedMonth}-${paddedDay}`;
                    
                    if (hiddenTanggalInput) {
                        hiddenTanggalInput.value = dateVal;
                    }
                    
                    try {
                        const indonesianDate = cellDate.toLocaleDateString('id-ID', dateOptions);
                        selectedDateLabel = indonesianDate;
                        if (displaySelectedDate) {
                            displaySelectedDate.textContent = indonesianDate;
                        }
                    } catch(err) {
                        console.error("Gagal format tanggal:", err);
                        selectedDateLabel = `${day} ${monthNames[viewMonth]} ${viewYear}`;
                        if (displaySelectedDate) {
                            displaySelectedDate.textContent = selectedDateLabel;
                        }
                    }
                    
                    updateDateTimeSummary();
                    renderCalendar();

                    const activeCard = document.querySelector('.time-block-card.active');
                    if (activeCard) {
                        const activeBlock = activeCard.getAttribute('data-time-block');
                        renderTimeSlots(activeBlock);
                    }
                };

                cell.addEventListener('click', onDayClick);
                cell.addEventListener('touchstart', onDayClick);
            }
            
            calDaysGrid.appendChild(cell);
        }
        
        // Next month days to pad grid
        const totalCells = startDayOfWeek + totalDaysInMonth;
        const remainingCells = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
        for (let day = 1; day <= remainingCells; day++) {
            const cell = document.createElement('div');
            cell.className = 'cal-day-cell next-month-day';
            cell.textContent = day;
            calDaysGrid.appendChild(cell);
        }
    }

    // Initialize calendar trigger render
    renderCalendar();

    <?php
    $disabledSlots = [
        'Bebas' => [],
        'Perempuan' => [],
        'Laki-Laki' => []
    ];
    $layananIds = isset($cartItems) ? array_keys($cartItems) : [];
    
    $datesToCheck = [];
    $startDate = new DateTime();
    for ($i = 0; $i < 7; $i++) {
        $dateClone = clone $startDate;
        $dateClone->modify("+$i days");
        $datesToCheck[] = $dateClone->format('Y-m-d');
    }

    $globalInterval = intval(ambilPengaturan($conn, 'interval_reservasi', '30'));
    
    $pMulai = ambilPengaturan($conn, 'sesi_pagi_mulai', '09:00');
    $pSelesai = ambilPengaturan($conn, 'sesi_pagi_selesai', '11:30');
    $pagiSlots = explode(',', generasiSlotJam($pMulai, $pSelesai, $globalInterval));

    $sMulai = ambilPengaturan($conn, 'sesi_siang_mulai', '12:00');
    $sSelesai = ambilPengaturan($conn, 'sesi_siang_selesai', '16:30');
    $siangSlots = explode(',', generasiSlotJam($sMulai, $sSelesai, $globalInterval));

    $soMulai = ambilPengaturan($conn, 'sesi_sore_mulai', '17:00');
    $soSelesai = ambilPengaturan($conn, 'sesi_sore_selesai', '20:00');
    $soreSlots = explode(',', generasiSlotJam($soMulai, $soSelesai, $globalInterval));

    $allSlots = array_merge($pagiSlots, $siangSlots, $soreSlots);

    foreach ($datesToCheck as $date) {
        foreach ($allSlots as $slotHour) {
            $slotHour = trim($slotHour);
            if (empty($slotHour)) continue;
            
            foreach (['Bebas', 'Perempuan', 'Laki-Laki'] as $g) {
                $available = cekKetersediaanSlot($conn, $layananIds, $g, $date, $slotHour);
                if (!$available) {
                    $disabledSlots[$g][$date][] = $slotHour;
                }
            }
        }
    }
    ?>
    const bookedSlotsData = <?php echo json_encode($disabledSlots); ?>;

    const timeSlotsData = {
        morning: [
            <?php 
            $globalInterval = intval(ambilPengaturan($conn, 'interval_reservasi', '30'));
            $pMulai = ambilPengaturan($conn, 'sesi_pagi_mulai', '09:00');
            $pSelesai = ambilPengaturan($conn, 'sesi_pagi_selesai', '11:30');
            $pagiString = generasiSlotJam($pMulai, $pSelesai, $globalInterval);
            $pagiArray = explode(',', $pagiString);
            foreach ($pagiArray as $t) {
                $t = trim($t);
                if (empty($t)) continue;
                echo "{ time: '$t', label: '$t WIB' },\n";
            }
            ?>
        ],
        afternoon: [
            <?php 
            $sMulai = ambilPengaturan($conn, 'sesi_siang_mulai', '12:00');
            $sSelesai = ambilPengaturan($conn, 'sesi_siang_selesai', '16:30');
            $siangString = generasiSlotJam($sMulai, $sSelesai, $globalInterval);
            $siangArray = explode(',', $siangString);
            foreach ($siangArray as $t) {
                $t = trim($t);
                if (empty($t)) continue;
                echo "{ time: '$t', label: '$t WIB' },\n";
            }
            ?>
        ],
        evening: [
            <?php 
            $soMulai = ambilPengaturan($conn, 'sesi_sore_mulai', '17:00');
            $soSelesai = ambilPengaturan($conn, 'sesi_sore_selesai', '20:00');
            $soreString = generasiSlotJam($soMulai, $soSelesai, $globalInterval);
            $soreArray = explode(',', $soreString);
            foreach ($soreArray as $t) {
                $t = trim($t);
                if (empty($t)) continue;
                echo "{ time: '$t', label: '$t WIB' },\n";
            }
            ?>
        ]
    };

    function updateSlotCounts() {
        const selectedDate = hiddenTanggalInput ? hiddenTanggalInput.value : '';
        const activeGender = hiddenGenderTerapisInput ? hiddenGenderTerapisInput.value : 'Bebas';
        const bookedTimes = (bookedSlotsData[activeGender] && bookedSlotsData[activeGender][selectedDate]) || [];
        Object.keys(timeSlotsData).forEach(function(bKey) {
            const total = timeSlotsData[bKey].length;
            const disabled = timeSlotsData[bKey].filter(function(s) { return bookedTimes.includes(s.time); }).length;
            const available = total - disabled;
            const span = document.querySelector('.time-block-slots[data-block-key="' + bKey + '"]');
            if (span) {
                span.textContent = available + ' slots';
            }
        });
    }

    function renderTimeSlots(blockKey) {
        timeSlotsContainer.innerHTML = '';
        const slots = timeSlotsData[blockKey] || [];
        const selectedDate = hiddenTanggalInput ? hiddenTanggalInput.value : '';
        const activeGender = hiddenGenderTerapisInput ? hiddenGenderTerapisInput.value : 'Bebas';
        const bookedTimes = (bookedSlotsData[activeGender] && bookedSlotsData[activeGender][selectedDate]) || [];
        
        // Get today's local date string (YYYY-MM-DD)
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const todayStr = `${year}-${month}-${day}`;
        
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();
        const currentTimeStr = String(currentHour).padStart(2, '0') + ':' + String(currentMinute).padStart(2, '0');
        
        slots.forEach(slot => {
            const isBooked = bookedTimes.includes(slot.time);
            const isPastSlot = (selectedDate === todayStr && slot.time < currentTimeStr);
            const isDisable = isBooked || isPastSlot;
            
            const pill = document.createElement('div');
            pill.className = 'time-pill' + (isDisable ? ' disabled' : '');
            if (hiddenJamInput.value === slot.time) {
                pill.className += ' active';
            }
            pill.setAttribute('data-time', slot.time);
            pill.textContent = slot.label;
            
            pill.addEventListener('click', function() {
                if (isDisable) return;
                
                document.querySelectorAll('.time-pill').forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                
                hiddenJamInput.value = slot.time;
                selectedTimeLabel = slot.time + ' WIB';
                updateDateTimeSummary();
            });
            
            timeSlotsContainer.appendChild(pill);
        });

        updateSlotCounts();
    }

    // Bind time block card clicks
    timeBlockCards.forEach(card => {
        card.addEventListener('click', function() {
            timeBlockCards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            
            const blockKey = this.getAttribute('data-time-block');
            renderTimeSlots(blockKey);
        });
    });

    // Helper to update date & time inside Your Journey summary block
    function updateDateTimeSummary() {
        if (selectedDateLabel && selectedTimeLabel) {
            journeyWaktuLabel.textContent = selectedDateLabel + ' – ' + selectedTimeLabel;
            journeyWaktuLabel.classList.remove('placeholder-value');
        } else if (selectedDateLabel) {
            journeyWaktuLabel.textContent = selectedDateLabel + ' – Pilih Jam';
            journeyWaktuLabel.classList.remove('placeholder-value');
        } else {
            journeyWaktuLabel.textContent = 'Pilih Tanggal & Jam';
            journeyWaktuLabel.classList.add('placeholder-value');
        }
    }

    // 4. GENDER TERAPIS SELECTION FLOW
    const genderPills = document.querySelectorAll('.gender-pill');

    genderPills.forEach(pill => {
        pill.addEventListener('click', function(e) {
            e.preventDefault();
            genderPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');

            const selectedGender = this.getAttribute('data-gender'); // "Bebas", "Perempuan", "Laki-Laki"
            
            if (hiddenGenderTerapisInput) {
                hiddenGenderTerapisInput.value = selectedGender;
            }

            // Update Your Journey Sidebar Summary
            if (journeyTerapisNama) {
                journeyTerapisNama.textContent = selectedGender === 'Bebas' ? 'Bebas (Mana saja)' : selectedGender;
                journeyTerapisNama.classList.remove('placeholder-value');
            }

            if (journeyTerapisAvatarBox) {
                if (selectedGender === 'Bebas') {
                    journeyTerapisAvatarBox.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
                    journeyTerapisAvatarBox.style.background = 'rgba(255, 255, 255, 0.1)';
                    journeyTerapisAvatarBox.style.color = 'rgba(255, 255, 255, 0.85)';
                } else {
                    journeyTerapisAvatarBox.textContent = selectedGender === 'Perempuan' ? 'P' : 'L';
                    journeyTerapisAvatarBox.style.background = 'var(--wellness-pink)';
                    journeyTerapisAvatarBox.style.color = '#ffffff';
                    journeyTerapisAvatarBox.style.fontWeight = '700';
                    journeyTerapisAvatarBox.style.fontSize = '0.72rem';
                }
            }

            // Update slots and slot counts based on new gender filter dynamically
            const activeCard = document.querySelector('.time-block-card.active');
            if (activeCard) {
                const activeBlock = activeCard.getAttribute('data-time-block');
                renderTimeSlots(activeBlock);
            }
        });
    });

    // Initialize UI state from hidden inputs (in case of post-error re-render)
    const initGender = hiddenGenderTerapisInput ? hiddenGenderTerapisInput.value : 'Bebas';
    const initJam = hiddenJamInput ? hiddenJamInput.value : '';
    
    // Set active class on gender pill
    genderPills.forEach(p => {
        if (p.getAttribute('data-gender') === initGender) {
            p.classList.add('active');
        } else {
            p.classList.remove('active');
        }
    });
    
    // Update Your Journey Sidebar Summary for gender
    if (journeyTerapisNama) {
        journeyTerapisNama.textContent = initGender === 'Bebas' ? 'Bebas (Mana saja)' : initGender;
        journeyTerapisNama.classList.remove('placeholder-value');
    }

    if (journeyTerapisAvatarBox) {
        if (initGender === 'Bebas') {
            journeyTerapisAvatarBox.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
            journeyTerapisAvatarBox.style.background = 'rgba(255, 255, 255, 0.1)';
            journeyTerapisAvatarBox.style.color = 'rgba(255, 255, 255, 0.85)';
        } else {
            journeyTerapisAvatarBox.textContent = initGender === 'Perempuan' ? 'P' : 'L';
            journeyTerapisAvatarBox.style.background = 'var(--wellness-pink)';
            journeyTerapisAvatarBox.style.color = '#ffffff';
            journeyTerapisAvatarBox.style.fontWeight = '700';
            journeyTerapisAvatarBox.style.fontSize = '0.72rem';
        }
    }

    if (initJam) {
        selectedTimeLabel = initJam + ' WIB';
        updateDateTimeSummary();
    }

    // Initialize morning slots after setting initial hidden inputs
    renderTimeSlots('morning');

    // 7. FORM SUBMISSION METADATA COMPILATION
    const form = document.getElementById('reservasiForm');
    form.addEventListener('submit', function(e) {
        // Validation Checks
        if (!hiddenGenderTerapisInput.value) {
            e.preventDefault();
            alert('Silakan pilih preferensi jenis kelamin Terapis Anda.');
            return false;
        }

        if (!hiddenJamInput.value) {
            e.preventDefault();
            alert('Silakan tentukan Jam Kedatangan Anda terlebih dahulu.');
            const stepJam = document.getElementById('timeSlotsContainer');
            if (stepJam) {
                stepJam.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        }

        hiddenCatatanArea.value = '';
    });
});
</script>
<?php include __DIR__ . '/../templates/footer.php'; ?>
