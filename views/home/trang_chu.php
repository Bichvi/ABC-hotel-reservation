<?php
// trang_chu.php - Trang chủ dành cho khách (trước khi đăng nhập)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Nếu sau này muốn xử lý khác khi đã đăng nhập thì dùng biến này
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>ABC Resort - Đặt phòng khách sạn & nghỉ dưỡng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --accent: #f97316;
            --bg: #0b1120;
            --text-main: #111827;
            --text-muted: #6b7280;
            --radius-lg: 18px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: radial-gradient(circle at top, #001d3d, #00172a 40%, #000814 100%);
            color: var(--text-main);
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* HEADER */
        .site-header {
            position: sticky;
            top: 0;
            z-index: 40;
            backdrop-filter: blur(16px);
            background: rgba(15,23,42,0.9);
            border-bottom: 1px solid rgba(148,163,184,0.25);
        }
        .header-inner {
            max-width: 1180px;
            margin: 0 auto;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #e5e7eb;
        }
        .brand-logo {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            background: radial-gradient(circle at 30% 20%, #38bdf8, #4f46e5 60%, #0f172a);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #e5e7eb;
            box-shadow: 0 10px 25px rgba(15,23,42,0.6);
        }
        .brand-text-title {
            font-weight: 600;
            letter-spacing: .08em;
            font-size: 13px;
        }
        .brand-text-sub {
            font-size: 11px;
            opacity: .75;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 18px;
            font-size: 13px;
            color: #e5e7eb;
        }
        .nav-links a {
            opacity: .8;
        }
        .nav-links a:hover {
            opacity: 1;
        }

        .btn-login {
            border-radius: 999px;
            border: 1px solid rgba(148,163,184,0.7);
            padding: 7px 16px;
            font-size: 13px;
            color: #e5e7eb;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: transparent;
            cursor: pointer;
            transition: all .15s ease;
        }
        .btn-login:hover {
            background: rgba(15,23,42,0.9);
            border-color: #e5e7eb;
        }

        /* HERO */
        .hero {
            background: radial-gradient(circle at top left, #1d4ed8 0, #0b1120 45%, #020617 100%);
            color: #e5e7eb;
        }
        .hero-inner {
            max-width: 1180px;
            margin: 0 auto;
            padding: 40px 16px 48px;
            display: grid;
            grid-template-columns: minmax(0, 3fr) minmax(0, 2.5fr);
            gap: 32px;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(15,23,42,0.8);
            border: 1px solid rgba(148,163,184,0.6);
            font-size: 11px;
            margin-bottom: 10px;
        }
        .hero-badge span {
            padding: 2px 8px;
            border-radius: 999px;
            background: rgba(34,197,94,0.12);
            color: #bbf7d0;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .08em;
        }
        .hero-title {
            font-size: 32px;
            line-height: 1.2;
            font-weight: 700;
            margin: 0 0 10px;
        }
        .hero-subtitle {
            font-size: 14px;
            color: #cbd5f5;
            max-width: 480px;
            margin-bottom: 18px;
        }
        .hero-highlight {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 12px;
            margin-bottom: 18px;
        }
        .hero-highlight-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(15,23,42,0.8);
            border: 1px solid rgba(148,163,184,0.4);
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn-primary {
            border: none;
            border-radius: 999px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            color: #f9fafb;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 16px 35px rgba(37,99,235,0.45);
            transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 22px 45px rgba(37,99,235,0.55);
            filter: brightness(1.03);
        }
        .btn-ghost {
            border-radius: 999px;
            border: 1px solid rgba(148,163,184,0.7);
            padding: 9px 16px;
            font-size: 13px;
            color: #e5e7eb;
            background: transparent;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all .15s ease;
        }
        .btn-ghost:hover {
            background: rgba(15,23,42,0.9);
        }

        .hero-metrics {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            font-size: 12px;
            color: #cbd5f5;
        }
        .hero-metric strong {
            display: block;
            font-size: 16px;
            color: #e5e7eb;
        }

        .hero-right {
            position: relative;
        }
        .hero-card-main {
            border-radius: 24px;
            background: #020617;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(15,23,42,0.8);
            border: 1px solid rgba(148,163,184,0.3);
        }
        .hero-card-image {
            position: relative;
            height: 230px;
            background-image: url('assets/images/hero-room.jpg');
            background-size: cover;
            background-position: center;
        }
        .hero-card-image::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(15,23,42,0.85), transparent 50%);
        }
        .hero-card-content {
            padding: 12px 14px 14px;
            font-size: 13px;
            color: #e5e7eb;
        }
        .hero-card-tag {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 999px;
            background: rgba(34,197,94,0.12);
            color: #bbf7d0;
            display: inline-block;
            margin-bottom: 4px;
        }
        .hero-card-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .hero-card-meta {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 4px;
        }
        .hero-card-price-main {
            font-size: 18px;
            font-weight: 700;
            color: #fde68a;
        }
        .hero-card-price-sub {
            font-size: 11px;
            color: #9ca3af;
        }
        .hero-card-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 6px;
            font-size: 11px;
        }
        .hero-card-chip {
            padding: 3px 8px;
            border-radius: 999px;
            background: rgba(15,23,42,0.9);
            border: 1px solid rgba(75,85,99,0.8);
        }

        .hero-badge-floating {
            position: absolute;
            top: -10px;
            right: -4px;
            border-radius: 18px;
            background: #020617;
            padding: 8px 12px;
            font-size: 11px;
            color: #e5e7eb;
            box-shadow: 0 14px 40px rgba(15,23,42,0.85);
            border: 1px solid rgba(148,163,184,0.4);
        }
        .hero-badge-floating strong {
            display: block;
            font-size: 13px;
        }

        /* MAIN CONTENT WRAPPER */
        .content-section {
            max-width: 1180px;
            margin: 26px auto 40px;
            padding: 0 16px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 12px;
            margin-bottom: 14px;
        }
        .section-title {
            font-size: 20px;
            margin: 0;
            color: #ffffff;
        }
        .section-subtitle {
            font-size: 13px;
            color: #f1f5f9;
        }

        /* ROOMS GRID */
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0,1fr));
            gap: 16px;
        }
        .room-card {
            border-radius: var(--radius-lg);
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 25px rgba(15,23,42,0.04);
            display: flex;
            flex-direction: column;
        }
        .room-image {
            position: relative;
            height: 160px;
            background-size: cover;
            background-position: center;
        }
        .room-pill {
            position: absolute;
            left: 10px;
            top: 10px;
            padding: 3px 8px;
            font-size: 11px;
            border-radius: 999px;
            background: rgba(15,23,42,0.72);
            color: #e5e7eb;
        }
        .room-body {
            padding: 10px 12px 12px;
            font-size: 13px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .room-name {
            font-weight: 600;
            margin-bottom: 4px;
        }
        .room-meta {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 6px;
        }
        .room-price-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-top: auto;
        }
        .room-price-main {
            font-size: 15px;
            font-weight: 700;
            color: var(--primary-dark);
        }
        .room-price-sub {
            font-size: 11px;
            color: var(--text-muted);
        }
        .room-cta {
            margin-top: 8px;
            display: flex;
            justify-content: flex-end;
        }
        .btn-room-book {
            border-radius: 999px;
            border: none;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 500;
            color: #f9fafb;
            background: linear-gradient(135deg, #f97316, #ea580c);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-room-book:hover {
            filter: brightness(1.05);
        }

        /* ABOUT + AMENITIES */
        .two-col {
            display: grid;
            grid-template-columns: minmax(0, 3fr) minmax(0, 2.2fr);
            gap: 20px;
            margin-top: 26px;
        }
        .about-card, .amenities-card, .promo-card {
            border-radius: var(--radius-lg);
            background: #ffffff;
            border: 1px solid #e5e7eb;
            padding: 14px 14px 16px;
            box-shadow: 0 10px 24px rgba(15,23,42,0.04);
            font-size: 13px;
        }
        .about-card h3,
        .amenities-card h3,
        .promo-card h3 {
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 16px;
        }
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0,1fr));
            gap: 8px;
            margin-top: 6px;
        }
        .amenity-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 13px;
            color: var(--text-main);
        }
        .amenity-dot {
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: var(--primary);
            margin-top: 5px;
        }

        /* PROMO */
        .promo-banner {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 14px;
            background: linear-gradient(135deg,#f97316,#e11d48);
            color: #fefce8;
            margin-top: 8px;
        }
        .promo-banner strong {
            font-size: 15px;
        }
        .promo-chip {
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 999px;
            border: 1px solid rgba(254,249,195,0.8);
        }

        /* REVIEWS */
        .reviews {
            margin-top: 26px;
        }
        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(3,minmax(0,1fr));
            gap: 16px;
        }
        .review-card {
            border-radius: var(--radius-lg);
            background: #ffffff;
            border: 1px solid #e5e7eb;
            padding: 12px 14px;
            font-size: 13px;
            box-shadow: 0 10px 24px rgba(15,23,42,0.04);
        }
        .review-name {
            font-weight: 600;
            margin-bottom: 2px;
        }
        .review-meta {
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 6px;
        }
        .review-rating {
            font-size: 12px;
            color: #f97316;
            margin-bottom: 6px;
        }

        /* FOOTER */
        .site-footer {
            background: #0b1120;
            color: #9ca3af;
            padding: 16px;
            margin-top: 32px;
        }
        .footer-inner {
            max-width: 1180px;
            margin: 0 auto;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* RESPONSIVE */
        @media (max-width: 960px) {
            .hero-inner {
                grid-template-columns: minmax(0,1fr);
            }
            .hero-right {
                order: -1;
            }
            .rooms-grid {
                grid-template-columns: repeat(2,minmax(0,1fr));
            }
            .two-col {
                grid-template-columns: minmax(0,1fr);
            }
            .reviews-grid {
                grid-template-columns: repeat(2,minmax(0,1fr));
            }
        }
        @media (max-width: 640px) {
            .rooms-grid {
                grid-template-columns: minmax(0,1fr);
            }
            .reviews-grid {
                grid-template-columns: minmax(0,1fr);
            }
            .header-inner {
                flex-wrap: wrap;
                gap: 8px;
            }
            .nav-links {
                width: 100%;
                justify-content: center;
            }
        }
        .search-box {
            display: flex;
            align-items: center;
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(148, 163, 184, 0.35);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.25s ease-in-out;
        }
        .search-box:focus-within {
            border-color: rgba(96, 165, 250, 0.9);
            box-shadow: 0 0 12px rgba(56,189,248,0.65);
            transform: scale(1.02);
        }
        .search-box input {
            background: transparent;
            border: none;
            color: #e5e7eb;
            padding: 8px 12px;
            font-size: 0.9rem;
            width: 220px;
        }
        .search-box input::placeholder {
            color: #94a3b8;
        }
        .search-box button {
            border: none;
            background: rgba(56,189,248,0.2);
            color: #38bdf8;
            font-weight: 600;
            padding: 8px 14px;
            font-size: 0.85rem;
            transition: all 0.25s ease;
        }
        .search-box button:hover {
            background: rgba(56,189,248,0.35);
            color: #e0f2fe;
        }
        .filter-panel {
            width: 100%;
            max-width: 900px;
            background: #0f172a;
            color: white;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0,0,0,.35);
            padding: 22px;
            position: absolute;
            top: 70px;
            left: 0;
            display: none;
        }
        .search-item:hover .filter-panel,
        .search-item:focus-within .filter-panel {
            display: block;
        }
        .filter-input {
            background: #020617;
            border: 1px solid #334155;
            color: white;
        }
        .filter-input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 1px #38bdf8;
        }
        .btn-success {
            background: linear-gradient(90deg,#22c55e,#16a34a);
            font-weight: 600;
            padding: 10px 22px;
            border-radius: 8px;
        }
        .btn-success:hover {
            filter: brightness(1.1);
        }
        .form-control, .form-select {
            height: 46px;
            border-radius: 50px;
            padding-left: 18px;
        }
        .search-wrapper {
            width: 100%;
            background: #0b1120;
            padding: 18px 0 30px;
        }

        .search-container {
            max-width: 920px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 0;
            border-radius: 50px;
            overflow: visible;   
            background: rgba(15,23,42,.85);
            border: 1px solid rgba(148,163,184,.4);
            position: relative;
        }


        .search-input {
            flex: 1;
            padding: 14px 18px;
            font-size: 1.05rem;
            background: transparent;
            border: none;
            color: #e5e7eb;
        }

        .search-input::placeholder {
            color: #94a3b8;
        }

        .search-btn {
            padding: 14px 36px;
            font-size: 1.05rem;
            font-weight: 600;
            background: linear-gradient(135deg,#2563eb,#1d4ed8);;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 0 50px 50px 0;  
            height: 100%;  
        }

        .search-btn:hover {
            background: #1d4ed8;
        }

        /* FILTER PANEL DROPDOWN */
        .filter-panel {
            width: 100%;
            margin-top: 8px;
            background: rgba(15,23,42,0.98); 
            backdrop-filter: blur(12px);     
            border-radius: 14px;
            padding: 20px;
            display: none;
            position: absolute;              
            top: 52px;                       
            left: 0;
            z-index: 2000;                   
            box-shadow: 0 16px 30px rgba(0,0,0,.6);
        }

        .filter-panel.show {
            display: block !important;
        }
        .search-item {
            position: relative;
            width: 100%;
        }
        /* ===== Buttons in filter panel ===== */
        .btn-reset,
        .btn-find {
            height: 46px;
            padding: 0 28px;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: all .2s ease-in-out;
        }

        /* Reset button style */
        .btn-reset {
            background: transparent;
            border: 2px solid #64748b;
            color: #e2e8f0;
        }
        .btn-reset:hover {
            background: rgba(100,116,139,.25);
            border-color: #94a3b8;
            color: white;
        }

        /* Primary search button */
        .btn-find {
            background: linear-gradient(135deg,#22c55e,#a3e635);
            border: none;
            color: black;
            box-shadow: 0 12px 24px rgba(37,99,235,0.45);
            margin-left: 10px;
        }
        .btn-find:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }
        .btn-find:active {
            transform: scale(.98);
        }
        .filter-panel .row.g-3 > div {
            margin-top: 10px;
            margin-bottom: 10px;
            margin-left: 10px;
            margin-right: 10px;
        }
        .btn-reset, .btn-find {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;
$role = $user['MaVaiTro'] ?? null;

$roleMenu = [
    1 => ["Trang Admin",      "admin",     "dashboard"],
    2 => ["Trang lễ tân",     "letan",     "index"],
    3 => ["Trang kế toán",    "ketoan",    "dashboard"],
    4 => ["Trang dịch vụ",    "dichvu",    "dashboard"],
    5 => ["Trang CSKH",       "cskh",      "dashboard"],
    6 => ["Trang quản lý",    "quanly",    "dashboard"],
    7 => ["Trang khách hàng", "khachhang", "dashboard"],
];
?>


<header class="site-header">
    <div class="header-inner">
        <a href="index.php" class="brand">
            <div class="brand-logo">AR</div>
            <div>
                <div class="brand-text-title">ABC RESORT</div>
                <div class="brand-text-sub">Biển & Spa · 5★</div>
            </div>
        </a>
        <nav class="nav-links">
            <a href="#rooms">Phòng & giá</a>
            <a href="#about">Giới thiệu</a>
            <a href="#promo">Ưu đãi</a>
            <a href="#contact">Liên hệ</a>
            <!-- <a href="index.php?controller=auth&action=login" class="btn-login">
                Đăng nhập
                <span>→</span>
            </a> -->
            <?php if ($user): ?>
                <span style="opacity:1;">Xin chào, <strong><?= $user['Username'] ?></strong></span>
                <?php if(isset($roleMenu[$role])): ?>
                    <a href="index.php?controller=<?= $roleMenu[$role][1] ?>&action=<?= $roleMenu[$role][2] ?>" 
                    class="btn-login" style="color:#4ade80;">
                        <?= $roleMenu[$role][0] ?> →
                    </a>
                <?php endif; ?>
                <a href="index.php?controller=auth&action=logout" class="btn-login" style="color:#f87171;">
                    Đăng xuất
                </a>
            <?php else: ?>
                <a href="index.php?controller=auth&action=login" class="btn-login">
                    Đăng nhập →
                </a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<!-- SEARCH BAR SECTION -->
<form method="get" action="index.php" class="search-wrapper">
    <input type="hidden" name="controller" value="khachhang">
    <input type="hidden" name="action" value="searchRooms">

    <div class="search-container">
        <div class="search-item" style="flex:1;">
            <input id="searchToggle" name="q" type="text" placeholder="Bạn muốn đến đâu?" class="search-input">

            <div class="filter-panel" id="filterPanel">
                <h5 class="fw-bold mb-1">Bộ lọc tìm phòng</h5>
                <p class="text-secondary mb-3">Chọn loại phòng, view, số khách và khoảng giá gợi ý.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Loại phòng</label>
                        <select class="form-select filter-input" name="loai_phong">
                            <option value="">-- Tất cả --</option>
                            <option>Standard</option>
                            <option>Superior</option>
                            <option>Deluxe</option>
                            <option>Suite</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">View phòng</label>
                        <select class="form-select filter-input" name="view_phong">
                            <option value="">-- Tất cả --</option>
                            <option>Hướng biển</option>
                            <option>Hướng hồ bơi</option>
                            <option>Hướng thành phố</option>
                            <option>Hướng vườn</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Số khách dự kiến</label>
                        <input type="number" name="so_khach" placeholder="VD: 2" class="form-control filter-input">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Giá gợi ý (VNĐ / đêm)</label>
                        <input type="text" name="gia_goi_y" placeholder="VD: 1.000.000" class="form-control filter-input">
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12 d-flex justify-content-center" style="gap: 16px;">
                        <button type="reset" class="btn-reset">Nhập lại</button>
                        <button type="submit" class="btn-find">Tìm phòng phù hợp</button>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" id="btnSearch" class="search-btn">Tìm</button>
    </div>
</form>

<section class="hero">
    <div class="hero-inner">
        <div>
            <div class="hero-badge">
                Trải nghiệm nghỉ dưỡng cao cấp
                <span>Ưu đãi tới -30%</span>
            </div>
            <h1 class="hero-title">
                Đặt phòng ABC Resort<br>
                Nghỉ dưỡng sang trọng bên bờ biển.
            </h1>
            <p class="hero-subtitle">
                Phòng rộng, view biển, hồ bơi vô cực, spa & nhà hàng quốc tế. 
                Đặt trực tiếp tại website để nhận <strong>giá tốt hơn</strong> và nhiều quyền lợi độc quyền.
            </p>

            <div class="hero-highlight">
                <div class="hero-highlight-item">
                    ⭐ 4.8/5 từ 2.300+ lượt đánh giá
                </div>
                <div class="hero-highlight-item">
                    🏖 Cách biển 50m · Hồ bơi vô cực
                </div>
                <div class="hero-highlight-item">
                    🕒 Nhận phòng sớm & trả phòng trễ (tùy tình trạng)
                </div>
            </div>

            <div class="hero-actions">
                <!-- <a href="index.php?controller=auth&action=login" class="btn-primary">
                    Đặt phòng ngay
                    <span>→</span>
                </a> -->
                <?php if ($user): ?>
                    <a href="index.php?controller=khachhang&action=datPhongOnline1" class="btn-primary">
                        Đặt phòng ngay <span>→</span>
                    </a>
                <?php else: ?>
                    <a href="index.php?controller=auth&action=login" class="btn-primary">
                        Đặt phòng ngay <span>→</span>
                    </a>
                <?php endif; ?>
                <a href="#rooms" class="btn-ghost">
                    Xem các hạng phòng
                </a>
            </div>

            <div class="hero-metrics">
                <div class="hero-metric">
                    <strong>+150</strong>
                    Phòng & suite cao cấp
                </div>
                <div class="hero-metric">
                    <strong>24/7</strong>
                    Lễ tân & hỗ trợ khách hàng
                </div>
                <div class="hero-metric">
                    <strong>Free</strong>
                    Đưa đón sân bay (theo gói)
                </div>
            </div>
        </div>

        <div class="hero-right">
            <div class="hero-card-main">
                <div class="hero-card-image" style="background-image:url('assets/images/room-ocean-view.png');"></div>
                <div class="hero-card-content">
                    <div class="hero-card-tag">Phổ biến nhất</div>
                    <div class="hero-card-title">Phòng Deluxe Ocean View</div>
                    <div class="hero-card-meta">
                        <div>
                            <div class="hero-card-price-main">1.950.000đ<span style="font-size:11px;">/đêm</span></div>
                            <div class="hero-card-price-sub">Đã gồm ăn sáng & hồ bơi</div>
                        </div>
                        <div style="font-size:11px;color:#9ca3af;">
                            Tối đa 2 người lớn + 1 trẻ em
                        </div>
                    </div>
                    <div class="hero-card-chip-row">
                        <div class="hero-card-chip">View biển trực diện</div>
                        <div class="hero-card-chip">Giường King 2m</div>
                        <div class="hero-card-chip">Miễn phí minibar ngày đầu</div>
                    </div>
                </div>
            </div>
            <div class="hero-badge-floating">
                <strong>Đặt ngay để giữ chỗ</strong>
                Chỉ còn vài phòng trống cho cuối tuần này.
            </div>
        </div>
    </div>
</section>

<?php
require_once "models/Phong.php";
$phongModel = new Phong();
$rooms = $phongModel->getAll();
?>

<main class="content-section">

    <!-- PHÒNG & GIÁ -->
    <section id="rooms">
        <div class="section-header">
            <div>
                <h2 class="section-title">Hạng phòng & Giá tham khảo</h2>
                <div class="section-subtitle">
                    Lựa chọn nhiều loại phòng từ Standard đến Suite, phù hợp cho cặp đôi, gia đình hoặc nhóm bạn.
                </div>
            </div>
        </div>

        <div class="rooms-grid">
            <?php 
                $limit = 0;
                foreach ($rooms as $room): 
                    if ($limit >= 3) break;
                    $limit++;
                ?>
                    <article class="room-card">
                        <div class="room-image" 
                            style="background-image:url('uploads/phong/<?= htmlspecialchars($room['HinhAnh']) ?>');">
                            <div class="room-pill"><?= htmlspecialchars($room['LoaiPhong']) ?></div>
                        </div>

                        <div class="room-body">
                            <div class="room-name">
                                <?= htmlspecialchars($room['LoaiPhong']) ?> • Phòng <?= htmlspecialchars($room['SoPhong']) ?>
                            </div>
                            <div class="room-meta">
                                <?= htmlspecialchars($room['DienTich']) ?>m² · <?= htmlspecialchars($room['LoaiGiuong']) ?> · <?= htmlspecialchars($room['ViewPhong']) ?>
                            </div>

                            <div class="room-price-row">
                                <div>
                                    <div class="room-price-main"><?= number_format($room['Gia'], 0, ',', '.') ?>đ</div>
                                    <div class="room-price-sub">/đêm</div>
                                </div>

                                <div class="room-cta">
                                    <?php if ($user): ?>
                                        <a href="index.php?controller=khachhang&action=datPhongOnline2&room_id=<?= $room['MaPhong'] ?>" class="btn-room-book">
                                            Đặt phòng <span>→</span>
                                        </a>
                                    <?php else: ?>
                                        <a href="index.php?controller=auth&action=login" class="btn-room-book">
                                            Đặt phòng <span>→</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
        </div>
    </section>

    <!-- GIỚI THIỆU & TIỆN ÍCH -->
    <section id="about" class="two-col">
        <article class="about-card">
            <h3>Về ABC Resort</h3>
            <p style="margin-bottom:8px;">
                ABC Resort nằm ngay sát bờ biển, cách trung tâm thành phố chỉ 10 phút di chuyển.
                Khách sạn được thiết kế theo phong cách hiện đại kết hợp tinh tế với các chi tiết nhiệt đới,
                mang tới không gian nghỉ dưỡng vừa sang trọng vừa gần gũi.
            </p>
            <p style="margin-bottom:8px;">
                Tất cả các phòng đều được trang bị đầy đủ tiện nghi: giường cao cấp, nệm lông vũ,
                phòng tắm rộng với bồn tắm/ phòng tắm đứng, TV smart kết nối Netflix, wifi tốc độ cao.
            </p>
            <p style="margin-bottom:0;">
                Đặc biệt, khi đặt phòng trực tiếp tại website, quý khách được:
            </p>
            <ul style="margin-top:6px;padding-left:18px;font-size:13px;color:var(--text-main);">
                <li>Giá tốt hơn so với OTA (Booking, Agoda, v.v.).</li>
                <li>Ưu tiên nhận phòng sớm và trả phòng trễ (nếu còn phòng).</li>
                <li>Ưu đãi thêm với các dịch vụ: spa, nhà hàng, đưa đón sân bay.</li>
            </ul>
        </article>

        <article class="amenities-card">
            <h3>Tiện ích & Dịch vụ</h3>
            <div class="amenities-grid">
                <div class="amenity-item">
                    <div class="amenity-dot"></div>
                    <div>Hồ bơi vô cực ngoài trời nhìn thẳng ra biển.</div>
                </div>
                <div class="amenity-item">
                    <div class="amenity-dot"></div>
                    <div>Spa & massage, phòng xông hơi, jacuzzi.</div>
                </div>
                <div class="amenity-item">
                    <div class="amenity-dot"></div>
                    <div>Nhà hàng buffet sáng & gọi món Âu - Á.</div>
                </div>
                <div class="amenity-item">
                    <div class="amenity-dot"></div>
                    <div>Quầy bar trên mái · live music cuối tuần.</div>
                </div>
                <div class="amenity-item">
                    <div class="amenity-dot"></div>
                    <div>Khu vui chơi trẻ em & phòng gym hiện đại.</div>
                </div>
                <div class="amenity-item">
                    <div class="amenity-dot"></div>
                    <div>Dịch vụ tour tham quan, thuê xe, đưa đón sân bay.</div>
                </div>
            </div>
        </article>
    </section>

    <!-- ƯU ĐÃI / KHUYẾN MÃI -->
    <section id="promo" style="margin-top:26px;">
        <article class="promo-card">
            <h3>Ưu đãi & Gói nghỉ dưỡng hiện có</h3>
            <p style="margin-bottom:6px;">
                Chỉ áp dụng cho khách đặt trực tiếp trên website hoặc qua hotline của ABC Resort.
            </p>
            <div class="promo-banner">
                <div>
                    <strong>Combo "Stay & Dine" - Giảm tới 30%</strong><br>
                    Đặt từ 2 đêm trở lên: miễn phí 01 bữa tối set menu cho 2 người + miễn phí nhận phòng sớm.
                </div>
                <div>
                    <div class="promo-chip">Áp dụng đến 31/12</div>
                </div>
            </div>
            <ul style="margin-top:8px;padding-left:18px;font-size:13px;">
                <li><strong>Ưu đãi khách mới:</strong> Giảm thêm 5% cho lần đặt đầu tiên qua website.</li>
                <li><strong>Ưu đãi dài ngày:</strong> Ở từ 5 đêm trở lên, tặng 01 lần massage chân miễn phí/khách.</li>
                <li><strong>Ưu đãi gia đình:</strong> Trẻ em dưới 6 tuổi miễn phí ngủ chung & ăn sáng cùng bố mẹ.</li>
            </ul>
            <div style="margin-top:8px;font-size:13px;">
                Để sử dụng ưu đãi, vui lòng <a href="index.php?controller=auth&action=login" style="color:var(--primary);font-weight:500;">đăng nhập & đặt phòng trực tiếp</a>
                hoặc liên hệ hotline để được hỗ trợ.
            </div>
        </article>
    </section>

    <!-- REVIEW -->
    <section class="reviews">
        <div class="section-header">
            <div>
                <h2 class="section-title">Khách nói gì về ABC Resort</h2>
                <div class="section-subtitle">
                    Những trải nghiệm thực tế từ khách đã đặt phòng và nghỉ dưỡng tại khách sạn.
                </div>
            </div>
        </div>

        <div class="reviews-grid">
            <article class="review-card">
                <div class="review-name">Anh Minh · TP.HCM</div>
                <div class="review-meta">Nghỉ dưỡng gia đình · 3 đêm</div>
                <div class="review-rating">★★★★★ · “Phòng đẹp, sạch, hồ bơi rất chill!”</div>
                <p>
                    Gia đình mình ở 3 đêm phòng Family Suite, nhân viên siêu dễ thương, 
                    hồ bơi nhìn ra biển cực kỳ thư giãn. Đặt trực tiếp trên web được tặng thêm 
                    voucher spa nữa, rất hài lòng.
                </p>
            </article>

            <article class="review-card">
                <div class="review-name">Chị Thảo · Hà Nội</div>
                <div class="review-meta">Công tác kết hợp nghỉ dưỡng · 2 đêm</div>
                <div class="review-rating">★★★★☆ · “Wifi mạnh, đồ ăn ngon”</div>
                <p>
                    Mình đi công tác nên cần wifi ổn định, ở đây wifi mạnh, phòng yên tĩnh, 
                    buffet sáng đa dạng. Lần sau có dịp sẽ quay lại cùng gia đình.
                </p>
            </article>

            <article class="review-card">
                <div class="review-name">Bạn Long & người yêu</div>
                <div class="review-meta">Kỷ niệm ngày đặc biệt · 1 đêm</div>
                <div class="review-rating">★★★★★ · “Trang trí phòng siêu xinh”</div>
                <p>
                    Mình đặt gói kỷ niệm, khách sạn trang trí phòng rất đẹp, có bánh nhỏ & hoa tươi.
                    Check-in online trước, tới nhận phòng rất nhanh, không phải chờ lâu.
                </p>
            </article>
        </div>
    </section>

    <!-- LIÊN HỆ -->
    <section id="contact" style="margin-top:26px;">
        <article class="about-card">
            <h3>Liên hệ & Hỗ trợ đặt phòng</h3>
            <p style="margin-bottom:6px;">
                Đội ngũ lễ tân của ABC Resort luôn sẵn sàng hỗ trợ bạn 24/7.
            </p>
            <ul style="margin-top:4px;padding-left:18px;font-size:13px;">
                <li><strong>Hotline:</strong> 1900 1234 567 · Zalo/Viber hỗ trợ đặt phòng.</li>
                <li><strong>Email:</strong> reservation@abcresort.vn</li>
                <li><strong>Địa chỉ:</strong> Số 123 Đường Biển, Thành phố Du Lịch, Việt Nam.</li>
            </ul>
            <p style="font-size:13px;margin-top:6px;">
                Hoặc bạn có thể <a href="index.php?controller=auth&action=login" style="color:var(--primary);font-weight:500;">đăng nhập tài khoản</a>
                để quản lý đặt phòng, xem lịch sử giao dịch và xuất hóa đơn nhanh chóng.
            </p>
        </article>
    </section>

</main>

<footer class="site-footer">
    <div class="footer-inner">
        <div>© <?php echo date('Y'); ?> ABC Resort. All rights reserved.</div>
        <div>Website đặt phòng trực tiếp · Bảo mật & an toàn.</div>
    </div>
</footer>

</body>
</html>