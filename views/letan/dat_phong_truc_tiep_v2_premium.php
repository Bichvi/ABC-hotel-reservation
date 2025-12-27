<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;

/**
 * Biến controller nên truyền vào:
 * - $errors        : array
 * - $success       : string|null
 * - $maGiaoDich    : int|null
 * - $hasSearch     : bool
 * - $dsPhong       : array (danh sách phòng gợi ý từ v2_findAvailableForDirectBooking)
 * - $dsDichVu      : array (danh sách dịch vụ đang hoạt động)
 * - $dsKhuyenMai   : array (danh sách khuyến mãi đang áp dụng)
 * - $ngayDen       : string (Y-m-d) hoặc null
 * - $ngayDi        : string (Y-m-d) hoặc null
 * - $soDem         : int|null  (nếu controller có tính trước)
 */

$errors      = $errors      ?? [];
$success     = $success     ?? null;
$maGiaoDich  = $maGiaoDich  ?? null;
$hasSearch   = $hasSearch   ?? false;
$dsPhong     = $dsPhong     ?? [];
$dsDichVu    = $dsDichVu    ?? [];
$dsKhuyenMai = $dsKhuyenMai ?? [];
$ngayDen     = $ngayDen     ?? ($_POST['ngay_den'] ?? '');
$ngayDi      = $ngayDi      ?? ($_POST['ngay_di'] ?? '');
$soDem       = $soDem       ?? ($_POST['so_dem'] ?? '');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt phòng trực tiếp V2 - Lễ tân</title>
    <style>
        :root {
            --bg-body: #020617;
            --bg-shell: #020617;
            --bg-card: #020617;
            --bg-elevated: #020617;
            --bg-chip: rgba(15,23,42,0.95);
            --border-soft: rgba(148,163,184,0.4);
            --border-strong: rgba(148,163,184,0.7);
            --text-main: #e5e7eb;
            --text-soft: #9ca3af;
            --text-muted: #6b7280;
            --text-strong: #f9fafb;
            --accent: #2563eb;
            --accent-2: #4f46e5;
            --accent-soft: rgba(59,130,246,0.2);
            --danger: #ef4444;
            --success: #22c55e;
            --divider: rgba(148,163,184,0.35);
            --shadow-strong: 0 24px 70px rgba(15,23,42,0.9);
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at 0% 0%, #1d4ed8 0, transparent 50%),
                radial-gradient(circle at 100% 100%, #4f46e5 0, transparent 55%),
                var(--bg-body);
            color: var(--text-main);
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at 15% 0%, rgba(59,130,246,0.20) 0, transparent 55%),
                radial-gradient(circle at 85% 100%, rgba(129,140,248,0.22) 0, transparent 55%);
            opacity: 0.6;
            pointer-events: none;
            z-index: -1;
        }

        .app-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* TOPBAR */

        .topbar {
            position: sticky;
            top: 0;
            z-index: 30;
            backdrop-filter: blur(20px);
            background: linear-gradient(120deg, rgba(15,23,42,0.97), rgba(15,23,42,0.93));
            border-bottom: 1px solid rgba(148,163,184,0.4);
            box-shadow: 0 24px 60px rgba(15,23,42,0.9);
        }

        .topbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 14px 20px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-block {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            background: conic-gradient(from 210deg, #0ea5e9, #4f46e5, #22c55e, #a855f7, #0ea5e9);
            padding: 2px;
            box-shadow: 0 18px 40px rgba(37,99,235,0.8);
        }

        .brand-logo-inner {
            width: 100%;
            height: 100%;
            border-radius: inherit;
            background:
                radial-gradient(circle at 0 0, rgba(248,250,252,0.95), transparent 55%),
                radial-gradient(circle at 120% 120%, rgba(59,130,246,0.8), transparent 60%),
                #020617;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            letter-spacing: 0.05em;
            font-size: 16px;
            color: #e5e7eb;
        }

        .brand-text-main {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--text-strong);
        }

        .brand-text-sub {
            font-size: 12px;
            color: var(--text-soft);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 13px;
            border-radius: 999px;
            background: radial-gradient(circle at 0 0, rgba(148,163,184,0.25), transparent 55%);
            background-color: rgba(15,23,42,0.90);
            border: 1px solid rgba(148,163,184,0.7);
            font-size: 12px;
            color: var(--text-soft);
        }

        .user-pill strong {
            color: var(--text-strong);
            font-weight: 600;
        }

        .topbar-nav a {
            font-size: 12px;
            color: #e5e7eb;
            text-decoration: none;
            margin-left: 8px;
            padding: 7px 13px;
            border-radius: 999px;
            border: 1px solid rgba(148,163,184,0.55);
            background:
                radial-gradient(circle at 0 0, rgba(59,130,246,0.18), transparent 60%),
                rgba(15,23,42,0.98);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.16s ease;
        }

        .topbar-nav a:hover {
            border-color: rgba(129,140,248,0.95);
            box-shadow: 0 16px 40px rgba(15,23,42,0.9);
            transform: translateY(-1px);
        }

        .topbar-nav a.logout {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            border-color: transparent;
        }

        .topbar-nav a.logout:hover {
            filter: brightness(1.05);
            box-shadow: 0 18px 40px rgba(220,38,38,0.7);
        }

        /* MAIN */

        .main {
            flex: 1;
        }

        .container {
            max-width: 1200px;
            margin: 22px auto 40px;
            padding: 0 18px 24px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
            align-items: flex-start;
        }

        .page-title-block {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .page-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: var(--bg-chip);
            border: 1px solid rgba(148,163,184,0.5);
            font-size: 11px;
            color: var(--text-soft);
        }

        .page-chip span {
            color: var(--text-strong);
            font-weight: 600;
        }

        .page-title-row h1 {
            margin: 0;
            font-size: 24px;
            color: var(--text-strong);
            letter-spacing: 0.03em;
        }

        .page-subtitle {
            font-size: 13px;
            color: var(--text-soft);
        }

        .back-link {
            font-size: 13px;
            text-decoration: none;
            color: var(--text-main);
            border-radius: 999px;
            padding: 8px 14px;
            border: 1px solid rgba(148,163,184,0.55);
            background:
                radial-gradient(circle at 0 50%, rgba(59,130,246,0.2), transparent 55%),
                rgba(15,23,42,0.98);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: var(--shadow-strong);
            transition: all 0.16s ease;
            white-space: nowrap;
        }

        .back-link::before {
            content: "←";
        }

        .back-link:hover {
            border-color: rgba(129,140,248,0.95);
            transform: translateY(-1px);
        }

        /* GRID & CARD */

        .grid {
            display: grid;
            grid-template-columns: minmax(0, 3.1fr) minmax(0, 2.4fr);
            gap: 20px;
        }

        .card {
            background:
                radial-gradient(circle at 0 0, rgba(37,99,235,0.15), transparent 55%),
                radial-gradient(circle at 120% 120%, rgba(129,140,248,0.18), transparent 60%),
                rgba(15,23,42,0.98);
            border-radius: 18px;
            padding: 18px 20px 16px;
            border: 1px solid var(--border-soft);
            box-shadow: var(--shadow-strong);
            position: relative;
            overflow: hidden;
        }

        .card-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .card h2 {
            margin: 0;
            font-size: 18px;
            color: var(--text-strong);
        }

        .card-tag {
            font-size: 11px;
            padding: 3px 9px;
            border-radius: 999px;
            background: rgba(15,23,42,0.9);
            border: 1px solid rgba(148,163,184,0.7);
            color: var(--text-soft);
        }

        .card p.caption {
            margin: 0 0 10px;
            font-size: 12px;
            color: var(--text-soft);
            line-height: 1.5;
        }

        .card-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, var(--divider), transparent);
            margin: 8px 0 12px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 600;
            margin: 10px 0 6px;
            color: var(--text-main);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .section-title span {
            font-weight: 400;
            font-size: 11px;
            color: var(--text-muted);
            text-transform: none;
        }

        .row {
            display: flex;
            gap: 12px;
        }

        .field {
            margin-bottom: 11px;
            flex: 1;
        }

        label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: var(--text-main);
            margin-bottom: 3px;
        }

        label span.note {
            font-size: 10px;
            color: var(--text-muted);
        }

        input[type="text"],
        input[type="date"],
        input[type="number"],
        input[type="email"],
        select {
            width: 100%;
            padding: 9px 11px;
            border-radius: 11px;
            border: 1px solid rgba(148,163,184,0.6);
            font-size: 13px;
            outline: none;
            background:
                radial-gradient(circle at 0 0, rgba(148,163,184,0.24), transparent 55%),
                rgba(15,23,42,0.98);
            color: var(--text-strong);
            transition: all 0.16s ease;
        }

        input::placeholder {
            color: rgba(148,163,184,0.85);
        }

        input:focus,
        select:focus {
            border-color: rgba(59,130,246,0.9);
            box-shadow: 0 0 0 1px rgba(59,130,246,0.35);
            background:
                radial-gradient(circle at 0 0, rgba(59,130,246,0.25), transparent 55%),
                rgba(15,23,42,1);
        }

        input[readonly] {
            background:
                radial-gradient(circle at 0 0, rgba(148,163,184,0.25), transparent 55%),
                rgba(15,23,42,0.96);
            color: var(--text-soft);
        }

        .input-error {
            border-color: rgba(239,68,68,0.95) !important;
            background:
                radial-gradient(circle at 0 0, rgba(248,113,113,0.28), transparent 55%),
                rgba(24,24,27,1) !important;
        }

        .error-msg {
            font-size: 11px;
            color: #fecaca;
            margin-top: 2px;
        }

        /* ALERT */

        .alert {
            border-radius: 12px;
            padding: 9px 12px;
            margin-bottom: 12px;
            font-size: 12px;
            border: 1px solid transparent;
            backdrop-filter: blur(18px);
        }

        .alert-error {
            background:
                radial-gradient(circle at 0 0, rgba(248,113,113,0.40), transparent 55%),
                rgba(24,24,27,0.98);
            border-color: rgba(248,113,113,0.75);
            color: #fee2e2;
        }

        .alert-success {
            background:
                radial-gradient(circle at 0 0, rgba(52,211,153,0.45), transparent 55%),
                rgba(22,163,74,0.95);
            border-color: rgba(134,239,172,0.9);
            color: #ecfdf5;
        }

        .alert ul {
            margin: 6px 0 0 18px;
            padding: 0;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(15,23,42,0.95);
            border: 1px solid rgba(148,163,184,0.7);
            color: var(--text-strong);
            font-size: 11px;
        }

        .pill span {
            font-weight: 600;
        }

        /* BUTTONS */

        .btn-row {
            margin-top: 14px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn-group-right {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-primary {
            border: none;
            border-radius: 999px;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-strong);
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 20px 40px rgba(37,99,235,0.75);
            transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
            letter-spacing: 0.03em;
        }

        .btn-primary span.icon {
            font-size: 15px;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 24px 50px rgba(37,99,235,0.85);
            filter: brightness(1.05);
        }

        .btn-secondary {
            border-radius: 999px;
            border: 1px solid rgba(148,163,184,0.7);
            background:
                radial-gradient(circle at 0 0, rgba(148,163,184,0.35), transparent 55%),
                rgba(15,23,42,0.98);
            color: var(--text-main);
            padding: 8px 14px;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-secondary:hover {
            border-color: rgba(209,213,219,0.95);
            transform: translateY(-1px);
        }

        .btn-danger-banner {
            border-radius: 999px;
            border: 1px solid rgba(248,113,113,0.8);
            background:
                radial-gradient(circle at 0 0, rgba(248,113,113,0.35), transparent 55%),
                rgba(24,24,27,1);
            color: #fee2e2;
            padding: 6px 10px;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-danger-banner::before {
            content: "⚠";
        }

        /* TABLES */

        .room-list {
            max-height: 260px;
            overflow: auto;
            border-radius: 12px;
            border: 1px solid rgba(148,163,184,0.45);
            background:
                radial-gradient(circle at 0 0, rgba(37,99,235,0.12), transparent 55%),
                rgba(15,23,42,0.98);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        thead {
            background: linear-gradient(to right, rgba(15,23,42,0.98), rgba(30,64,175,0.95));
            position: sticky;
            top: 0;
            z-index: 1;
        }

        th, td {
            padding: 8px 10px;
            border-bottom: 1px solid rgba(31,41,55,0.92);
            text-align: left;
        }

        th {
            font-weight: 500;
            color: #e5e7eb;
            font-size: 11px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        tbody tr {
            transition: background 0.12s ease;
        }

        tbody tr:hover {
            background: rgba(30,64,175,0.38);
        }

        .table-empty {
            text-align: center;
            color: #9ca3af;
            padding: 12px;
            font-size: 12px;
        }

        input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #4f46e5;
        }

        /* ROOM GUEST CARDS */

        .room-guests-wrapper {
            margin-top: 12px;
            border-radius: 12px;
            border: 1px dashed rgba(148,163,184,0.6);
            padding: 10px 10px 6px;
            background:
                radial-gradient(circle at 0 0, rgba(37,99,235,0.15), transparent 55%),
                rgba(15,23,42,0.96);
        }

        .room-guests-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            margin-bottom: 4px;
        }

        .room-guests-header span {
            font-size: 11px;
            color: var(--text-soft);
        }

        .room-guest-card {
            margin-top: 8px;
            padding: 8px 10px 6px;
            border-radius: 10px;
            background:
                radial-gradient(circle at 0 0, rgba(37,99,235,0.2), transparent 55%),
                rgba(15,23,42,0.98);
            border: 1px solid rgba(148,163,184,0.6);
        }

        .room-guest-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .room-guest-title small {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* MODAL CCCD */

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 10% 0%, rgba(15,23,42,0.9), transparent 65%),
                        radial-gradient(circle at 90% 100%, rgba(15,23,42,0.9), transparent 70%),
                        rgba(15,23,42,0.88);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }

        .modal {
            width: 100%;
            max-width: 420px;
            border-radius: 20px;
            border: 1px solid rgba(148,163,184,0.6);
            padding: 16px 16px 14px;
            background:
                radial-gradient(circle at 0 0, rgba(59,130,246,0.4), transparent 60%),
                radial-gradient(circle at 100% 100%, rgba(129,140,248,0.35), transparent 60%),
                rgba(15,23,42,0.98);
            box-shadow: 0 30px 80px rgba(15,23,42,1);
            backdrop-filter: blur(24px);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 16px;
            color: var(--text-strong);
        }

        .modal-header span.badge {
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 999px;
            border: 1px solid rgba(148,163,184,0.7);
            color: var(--text-soft);
        }

        .modal-body {
            font-size: 12px;
            color: var(--text-soft);
            margin-bottom: 10px;
        }

        .modal-body p {
            margin: 0 0 8px;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 4px;
        }

        .btn-ghost {
            border-radius: 999px;
            border: 1px solid rgba(148,163,184,0.8);
            background: transparent;
            color: var(--text-main);
            padding: 6px 11px;
            font-size: 12px;
            cursor: pointer;
        }

        .btn-ghost:hover {
            background: rgba(15,23,42,0.85);
        }

        .btn-outline-danger {
            border-radius: 999px;
            border: 1px solid rgba(248,113,113,0.9);
            background: rgba(24,24,27,0.98);
            color: #fecaca;
            padding: 6px 11px;
            font-size: 12px;
            cursor: pointer;
        }

        .btn-outline-primary {
            border-radius: 999px;
            border: 1px solid rgba(59,130,246,0.9);
            background: linear-gradient(135deg, #1d4ed8, #4f46e5);
            color: #f9fafb;
            padding: 6px 12px;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-outline-primary:hover {
            filter: brightness(1.05);
        }

        /* SERVICES */

        .service-room-select {
            width: 100%;
            padding: 6px 9px;
            border-radius: 9px;
            border: 1px solid rgba(148,163,184,0.6);
            font-size: 12px;
            background:
                radial-gradient(circle at 0 0, rgba(148,163,184,0.22), transparent 55%),
                rgba(15,23,42,0.98);
            color: var(--text-main);
        }

        .service-room-select:focus {
            border-color: rgba(59,130,246,0.9);
            box-shadow: 0 0 0 1px rgba(59,130,246,0.35);
        }

        .service-note {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        @media (max-width: 960px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .topbar-inner {
                padding-inline: 12px;
            }
            .container {
                padding-inline: 12px;
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .back-link {
                align-self: flex-start;
            }
        }
    </style>
</head>
<body>
<div class="app-shell">
    <header class="topbar">
        <div class="topbar-inner">
            <div class="brand-block">
                <div class="brand-logo">
                    <div class="brand-logo-inner">AB</div>
                </div>
                <div>
                    <div class="brand-text-main">ABC RESORT</div>
                    <div class="brand-text-sub">Bảng điều khiển · Lễ tân</div>
                </div>
            </div>
            <div class="topbar-right">
                <?php if ($user): ?>
                    <div class="user-pill">
                        <span style="opacity:.8;">Đang đăng nhập</span>
                        <strong><?php echo htmlspecialchars($user['Username']); ?></strong>
                    </div>
                <?php endif; ?>
                <nav class="topbar-nav">
                    <a href="index.php?controller=letan&action=index">
                        <span style="font-size:13px;">🏠</span> Trang lễ tân
                    </a>
                    <a href="index.php?controller=auth&action=logout" class="logout">
                        Đăng xuất
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <div class="page-header">
                <div class="page-title-block">
                    <div class="page-chip">
                        Đặt phòng trực tiếp V2 · <span>Lễ tân</span>
                    </div>
                    <div class="page-title-row">
                        <h1>Đặt phòng tại quầy · Phiên bản Premium</h1>
                    </div>
                    <div class="page-subtitle">
                        Trưởng đoàn luôn được tạo ⇒ mỗi giao dịch là một đoàn riêng. Ghi nhận số đêm, mã giảm giá, khách theo từng phòng & dịch vụ theo phòng.
                    </div>
                </div>
                <a class="back-link" href="index.php?controller=letan&action=index">
                    Quay lại trang lễ tân
                </a>
            </div>

            <!-- SERVER ERRORS -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <strong>Có lỗi xảy ra:</strong>
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?php echo htmlspecialchars($e); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- CLIENT ERRORS -->
            <div id="clientErrors" class="alert alert-error" style="display:none;">
                <strong>Có lỗi xảy ra:</strong>
                <ul id="clientErrorsList"></ul>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                    <?php if (!empty($maGiaoDich)): ?>
                        <div class="pill" style="margin-top:6px;">
                            Mã giao dịch: <span>#<?php echo (int)$maGiaoDich; ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
<?php if (!empty($createdAccounts)): ?>
    <div class="card" style="margin-top:20px;">
        <div class="card-header">
            <h2>Thông tin tài khoản khách hàng</h2>
            <div class="card-tag">Tạo tự động</div>
        </div>

        <p class="caption">
            Hệ thống đã tạo tài khoản đăng nhập cho trưởng đoàn và các thành viên:
        </p>

        <table>
            <thead>
                <tr>
                    <th>Họ tên</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Vai trò</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($createdAccounts as $acc): ?>
                    <tr>
                        <td><?= htmlspecialchars($acc['hoTen']) ?></td>
                        <td><strong><?= htmlspecialchars($acc['username']) ?></strong></td>
                        <td style="color:#22c55e;">
                            <?= htmlspecialchars($acc['password']) ?>
                        </td>
                        <td><?= htmlspecialchars($acc['vaiTro']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p class="caption" style="margin-top:10px;">
            * Vui lòng lưu lại thông tin này hoặc chụp ảnh màn hình để gửi cho khách.
        </p>
    </div>
<?php endif; ?>
            <form method="post" id="formDatPhongV2">
                <div class="grid">
                    <!-- CỘT 1: TRƯỞNG ĐOÀN + THÔNG TIN ĐẶT PHÒNG -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Thông tin trưởng đoàn</h2>
                            <div class="card-tag">Bước 1 · Khách & đoàn</div>
                        </div>
                        <p class="caption">
                            Hệ thống sẽ <strong>tự tạo đoàn mới</strong>, trưởng đoàn chính là khách này.
                            CCCD trùng sẽ được hỏi dùng thông tin cũ hay nhập mới.
                        </p>
                        <div class="card-divider"></div>

                        <div class="row">
                            <div class="field">
                                <label>
                                    Họ tên trưởng đoàn <span style="color:#f97373;">*</span>
                                    <span class="note">VD: Nguyễn Văn A</span>
                                </label>
                                <input type="text" id="leader_ten_kh" name="leader_ten_kh"
                                       value="<?php echo htmlspecialchars($_POST['leader_ten_kh'] ?? ($_POST['ten_kh'] ?? '')); ?>"
                                       placeholder="Họ tên trưởng đoàn">
                                <div class="error-msg" id="err_leader_ten_kh"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="field">
                                <label>
                                    CCCD trưởng đoàn <span style="color:#f97373;">*</span>
                                    <span class="note">9–12 chữ số</span>
                                </label>
                                <input type="text" id="leader_cccd" name="leader_cccd"
                                       value="<?php echo htmlspecialchars($_POST['leader_cccd'] ?? ($_POST['cccd'] ?? '')); ?>"
                                       placeholder="Chỉ gồm 9–12 chữ số">
                                <div class="error-msg" id="err_leader_cccd"></div>
                            </div>
                            <div class="field">
                                <label>
                                    Số điện thoại <span style="color:#f97373;">*</span>
                                    <span class="note">Bắt đầu bằng 0</span>
                                </label>
                                <input type="text" id="leader_sdt" name="leader_sdt"
                                       value="<?php echo htmlspecialchars($_POST['leader_sdt'] ?? ($_POST['sdt'] ?? '')); ?>"
                                       placeholder="09xxxxxxxx">
                                <div class="error-msg" id="err_leader_sdt"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="field">
                                <label>
                                    Email <span style="color:#f97373;">*</span>
                                    <span class="note">Gửi xác nhận & hóa đơn</span>
                                </label>
                                <input type="email" id="leader_email" name="leader_email"
                                       value="<?php echo htmlspecialchars($_POST['leader_email'] ?? ($_POST['email'] ?? '')); ?>"
                                       placeholder="email@domain.com">
                                <div class="error-msg" id="err_leader_email"></div>
                            </div>
                            <div class="field">
                                <label>
                                    Địa chỉ
                                    <span class="note">Không bắt buộc</span>
                                </label>
                                <input type="text" id="leader_diachi" name="leader_diachi"
                                       value="<?php echo htmlspecialchars($_POST['leader_diachi'] ?? ($_POST['diachi'] ?? '')); ?>"
                                       placeholder="Địa chỉ trưởng đoàn">
                            </div>
                        </div>

                        <div class="section-title" style="margin-top:16px;">
                            Thông tin đặt phòng
                            <span>Dùng để tìm phòng, tính số đêm & áp dụng khuyến mãi</span>
                        </div>

                        <div class="row">
                            <div class="field">
                                <label>
                                    Ngày đến <span style="color:#f97373;">*</span>
                                </label>
                                <input type="date" id="ngay_den" name="ngay_den"
                                       value="<?php echo htmlspecialchars($ngayDen); ?>">
                                <div class="error-msg" id="err_ngay_den"></div>
                            </div>
                            <div class="field">
                                <label>
                                    Ngày đi <span style="color:#f97373;">*</span>
                                </label>
                                <input type="date" id="ngay_di" name="ngay_di"
                                       value="<?php echo htmlspecialchars($ngayDi); ?>">
                                <div class="error-msg" id="err_ngay_di"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="field">
                                <label>
                                    Số đêm lưu trú
                                    <span class="note">Tự tính từ ngày đến / đi</span>
                                </label>
                                <input type="number" id="so_dem_display" readonly
                                       value="<?php echo htmlspecialchars($soDem); ?>"
                                       placeholder="0">
                                <input type="hidden" id="so_dem" name="so_dem"
                                       value="<?php echo htmlspecialchars($soDem); ?>">
                            </div>
                            <div class="field">
                                <label>
                                    Số khách (ước tính)
                                    <span class="note">Để lọc sức chứa phòng</span>
                                </label>
                                <input type="number" id="so_nguoi" name="so_nguoi" min="1"
                                       value="<?php echo htmlspecialchars($_POST['so_nguoi'] ?? '2'); ?>">
                                <div class="error-msg" id="err_so_nguoi"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="field">
                                <label>
                                    Mã khuyến mãi
                                    <span class="note">Giảm theo số tiền cố định (VND)</span>
                                </label>
                                <select name="ma_khuyen_mai" id="ma_khuyen_mai">
                                    <option value="">-- Không áp dụng --</option>
                                    <?php if (!empty($dsKhuyenMai)): ?>
                                        <?php foreach ($dsKhuyenMai as $km): ?>
                                            <option value="<?php echo (int)$km['MaKhuyenMai']; ?>"
                                                <?php echo (!empty($_POST['ma_khuyen_mai']) && $_POST['ma_khuyen_mai'] == $km['MaKhuyenMai']) ? 'selected' : ''; ?>>
                                                <?php
                                                $label = $km['TenChuongTrinh'] ?? ('KM #' . $km['MaKhuyenMai']);
                                                $muc  = (float)($km['MucUuDai'] ?? 0);
                                                echo htmlspecialchars($label) . ' - Giảm ' . number_format($muc, 0, ',', '.') . ' đ';
                                                ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="field">
                                <label>
                                    Ghi chú chung
                                    <span class="note">Yêu cầu đặc biệt, thông điệp thêm</span>
                                </label>
                                <input type="text" name="ghichu"
                                       value="<?php echo htmlspecialchars($_POST['ghichu'] ?? ''); ?>"
                                       placeholder="VD: cần giường phụ, ăn chay...">
                            </div>
                        </div>

                        <div class="btn-row">
                            <div id="btnDanger" class="btn-danger-banner" style="display:none;">
                                Thiếu hoặc sai thông tin. Vui lòng kiểm tra lại các ô màu đỏ.
                            </div>
                            <div class="btn-group-right">
                                <button type="button" class="btn-secondary"
                                        onclick="window.location='index.php?controller=letan&action=datPhongTrucTiepV2'">
                                    🔄 Làm mới form
                                </button>

                                <button type="submit" name="btn_action" value="search" class="btn-secondary">
                                    🔍 Tìm phòng phù hợp
                                </button>

                                <button type="submit" name="btn_action" value="book" class="btn-primary">
                                    Đặt phòng & tạo đoàn
                                    <span class="icon">→</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- CỘT 2: CHỌN PHÒNG + KHÁCH THEO PHÒNG + DỊCH VỤ -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Chọn phòng & dịch vụ</h2>
                            <div class="card-tag">Bước 2–3 · Phòng · Thành viên · Dịch vụ</div>
                        </div>
                        <p class="caption">
                            Bước 1: Nhấn “Tìm phòng phù hợp” để gợi ý.<br>
                            Bước 2: Chọn phòng, nhập khách cho từng phòng (nếu khác trưởng đoàn).<br>
                            Bước 3: Chọn dịch vụ & phòng sử dụng dịch vụ.
                        </p>
                        <div class="card-divider"></div>

                        <div class="section-title">
                            Danh sách phòng phù hợp
                            <span>Chọn ít nhất 1 phòng trước khi đặt</span>
                        </div>
                        <div class="room-list">
                            <table>
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Phòng</th>
                                    <th>Loại</th>
                                    <th>Sức chứa</th>
                                    <th>Giá / đêm</th>
                                    <th>View</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $postRooms = isset($_POST['rooms']) && is_array($_POST['rooms'])
                                    ? array_map('intval', $_POST['rooms'])
                                    : [];
                                ?>
                                <?php if ($hasSearch && !empty($dsPhong)): ?>
                                    <?php foreach ($dsPhong as $p): ?>
                                        <?php
                                        $maPhong = (int)$p['MaPhong'];
                                        $checked = in_array($maPhong, $postRooms, true);
                                        $soPhong = $p['SoPhong'] ?? ('P' . $maPhong);
                                        ?>
                                        <tr data-room-id="<?php echo $maPhong; ?>"
    data-room-name="<?php echo htmlspecialchars($soPhong); ?>"
    data-room-price="<?php echo (float)$p['Gia']; ?>">
                                            <td>
                                                <input type="checkbox"
                                                       class="room-checkbox"
                                                       name="rooms[]"
                                                       value="<?php echo $maPhong; ?>"
                                                    <?php echo $checked ? 'checked' : ''; ?>>
                                            </td>
                                            <td><strong><?php echo htmlspecialchars($soPhong); ?></strong></td>
                                            <td><?php echo htmlspecialchars($p['LoaiPhong'] ?? ''); ?></td>
                                            <td><?php echo (int)($p['SoKhachToiDa'] ?? 0); ?> khách</td>
                                            <td><?php echo number_format((float)($p['Gia'] ?? 0), 0, ',', '.'); ?> đ</td>
                                            <td><?php echo htmlspecialchars($p['ViewPhong'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php elseif ($hasSearch): ?>
                                    <tr>
                                        <td colspan="6" class="table-empty" style="color:#fecaca;">
                                            Không còn phòng phù hợp với yêu cầu. Vui lòng điều chỉnh ngày /
                                            số khách / loại phòng.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="table-empty">
                                            Chưa tìm kiếm. Vui lòng nhập thông tin rồi bấm
                                            <strong>“Tìm phòng phù hợp”</strong>.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Khách theo từng phòng -->
                        <div class="room-guests-wrapper" id="roomGuestsWrapper">
                            <div class="room-guests-header">
                                <strong style="font-size:12px;">Thông tin khách theo từng phòng</strong>
                                <span>Để trống sẽ dùng thông tin trưởng đoàn.</span>
                            </div>
                            <div id="roomGuestsContainer">
                                <?php
                                // Dữ liệu POST cũ để giữ lại khi submit lỗi
                                if (!empty($postRooms)):
                                    foreach ($postRooms as $roomIdInt):
                                        $roomName = 'Phòng ' . $roomIdInt;
                                        $guestData = $_POST['room_guests'][$roomIdInt] ?? [];
                                        ?>
                                        <div class="room-guest-card" data-room-id="<?php echo $roomIdInt; ?>">
                                            <div class="room-guest-title">
                                                <span><?php echo htmlspecialchars($roomName); ?></span>
                                                <small>Để trống = dùng trưởng đoàn</small>
                                            </div>
                                            <div class="row">
                                                <div class="field">
                                                    <label>Tên khách phòng</label>
                                                    <input type="text"
                                                           name="room_guests[<?php echo $roomIdInt; ?>][TenKhach]"
                                                           value="<?php echo htmlspecialchars($guestData['TenKhach'] ?? ''); ?>"
                                                           placeholder="Nếu khác trưởng đoàn">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="field">
                                                    <label>
                                                        CCCD khách
                                                        <span class="note">Có thể kiểm tra trùng</span>
                                                    </label>
                                                    <input type="text"
                                                           class="member-cccd-input"
                                                           data-room-id="<?php echo $roomIdInt; ?>"
                                                           name="room_guests[<?php echo $roomIdInt; ?>][CCCD]"
                                                           value="<?php echo htmlspecialchars($guestData['CCCD'] ?? ''); ?>"
                                                           placeholder="9–12 chữ số">
                                                </div>
                                                <div class="field">
                                                    <label>SĐT khách</label>
                                                    <input type="text"
                                                           name="room_guests[<?php echo $roomIdInt; ?>][SDT]"
                                                           value="<?php echo htmlspecialchars($guestData['SDT'] ?? ''); ?>"
                                                           placeholder="Nếu khác trưởng đoàn">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="field">
                                                    <label>Email khách</label>
                                                    <input type="email"
                                                           name="room_guests[<?php echo $roomIdInt; ?>][Email]"
                                                           value="<?php echo htmlspecialchars($guestData['Email'] ?? ''); ?>"
                                                           placeholder="Nếu khác trưởng đoàn">
                                                </div>
                                            </div>
                                            <div class="row">
    <div class="field">
        <label>Địa chỉ khách</label>
        <input type="text"
               name="room_guests[<?php echo $roomIdInt; ?>][DiaChi]"
               value="<?php echo htmlspecialchars($guestData['DiaChi'] ?? ''); ?>"
               placeholder="Nếu khác trưởng đoàn">
        <div class="error-msg"></div>
    </div>
</div>
                                        </div>
                                        <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                        </div>

                        <div class="card-divider" style="margin-top:16px;"></div>

                        <div class="section-title">
                            Đăng ký dịch vụ kèm theo
                            <span>Chọn phòng nào sử dụng dịch vụ</span>
                        </div>
                        <p class="caption" style="margin-bottom:6px;">
                            Số lượng & phòng sử dụng dịch vụ sẽ được ghi vào chi tiết dịch vụ (có mã phòng tương ứng).
                            Có thể thêm bớt dịch vụ sau tại màn “Đặt dịch vụ”.
                        </p>

                        <?php if (!empty($dsDichVu)): ?>
                            <table>
                                <thead>
                                <tr>
                                    <th>Dịch vụ</th>
                                    <th>Giá</th>
                                    <th style="width:80px;">Số lượng</th>
                                    <th>Phòng sử dụng</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $postServices = $_POST['services'] ?? [];
                                $postServicesRoom = $_POST['services_room'] ?? [];
                                ?>
                                <?php foreach ($dsDichVu as $dv): ?>
                                    <?php
                                    $idDV   = (int)$dv['MaDichVu'];
                                    $qtyVal = isset($postServices[$idDV]) ? (int)$postServices[$idDV] : 0;
                                    $roomVal = $postServicesRoom[$idDV] ?? '';
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($dv['TenDichVu']); ?></td>
                                        <td><?php echo number_format((float)$dv['GiaDichVu'], 0, ',', '.'); ?> đ</td>
                                        <td>
                                            <input type="number"
       name="services[<?php echo $idDV; ?>]"
       min="0"
       value="<?php echo htmlspecialchars($qtyVal); ?>"
       data-price="<?php echo (float)$dv['GiaDichVu']; ?>"
       style="width:68px;">
                                                   
                                        </td>
                                        <td>
                                            <select
                                                class="service-room-select js-service-room"
                                                name="services_room[<?php echo $idDV; ?>]"
                                                data-selected-room="<?php echo htmlspecialchars($roomVal); ?>"
                                            >
                                                <option value="">-- Chọn phòng --</option>
                                                <!-- JS sẽ render danh sách phòng theo rooms[] -->
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="service-note">
                                Lưu ý: Nếu nhập số lượng & không chọn phòng, hệ thống sẽ báo lỗi khi đặt phòng.
                            </div>
                        <?php else: ?>
                            <div style="font-size:12px;color:#9ca3af;margin-top:4px;">
                                Hiện chưa cấu hình dịch vụ nào.
                            </div>
                        <?php endif; ?>
                        <div class="card-divider" style="margin-top:16px;"></div>

<div class="section-title">
    Tổng tiền tạm tính
    <span>Tự động cập nhật theo phòng, dịch vụ & khuyến mãi</span>
</div>

<div style="font-size:13px;line-height:1.8;margin-top:6px;">
    <div>Tiền phòng:
        <strong id="sumRoom">0 đ</strong>
    </div>
    <div>Tiền dịch vụ:
        <strong id="sumService">0 đ</strong>
    </div>
    <div>Khuyến mãi:
        <strong id="sumDiscount">- 0 đ</strong>
    </div>
    <div style="margin-top:6px;font-size:15px;">
        Tổng thanh toán:
        <strong style="color:#22c55e;" id="sumTotal">0 đ</strong>
    </div>
</div>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>

<!-- MODAL CHECK CCCD -->
<div class="modal-backdrop" id="cccdModalBackdrop">
    <div class="modal" id="cccdModal">
        <div class="modal-header">
            <h3>CCCD đã tồn tại trong hệ thống</h3>
            <span class="badge">Trùng thông tin khách</span>
        </div>
        <div class="modal-body">
            <p>
                Hệ thống tìm thấy khách hàng với CCCD này. Bạn muốn:
            </p>
            <ul style="margin:0 0 8px 18px;padding:0;">
                <li>Dùng lại <strong>thông tin cũ</strong> (họ tên, SĐT, email, địa chỉ), hoặc</li>
                <li>Nhập <strong>CCCD khác</strong> nếu đây là khách mới.</li>
            </ul>
            <div id="cccdModalInfo" style="font-size:11px;color:#e5e7eb;"></div>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-ghost" id="btnCccdClose">Đóng</button>
            <button type="button" class="btn-outline-danger" id="btnCccdNhapLai">
                Nhập CCCD khác
            </button>
            <button type="button" class="btn-outline-primary" id="btnCccdDungThongTinCu">
                ✔ Dùng thông tin cũ
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formDatPhongV2');
    const clientErrors = document.getElementById('clientErrors');
    const clientErrorsList = document.getElementById('clientErrorsList');
    const btnDanger = document.getElementById('btnDanger');

    const fields = {
        leader_ten_kh: document.getElementById('leader_ten_kh'),
        leader_cccd:   document.getElementById('leader_cccd'),
        leader_sdt:    document.getElementById('leader_sdt'),
        leader_email:  document.getElementById('leader_email'),
        ngay_den:      document.getElementById('ngay_den'),
        ngay_di:       document.getElementById('ngay_di'),
        so_nguoi:      document.getElementById('so_nguoi')
    };

    const errsDom = {
        leader_ten_kh: document.getElementById('err_leader_ten_kh'),
        leader_cccd:   document.getElementById('err_leader_cccd'),
        leader_sdt:    document.getElementById('err_leader_sdt'),
        leader_email:  document.getElementById('err_leader_email'),
        ngay_den:      document.getElementById('err_ngay_den'),
        ngay_di:       document.getElementById('err_ngay_di'),
        so_nguoi:      document.getElementById('err_so_nguoi')
    };

    const touched = {
        leader_ten_kh:false, leader_cccd:false, leader_sdt:false, leader_email:false,
        ngay_den:false, ngay_di:false, so_nguoi:false
    };

    /* ========== LEADER + BOOKING VALIDATION ========== */

    function setError(name, msg) {
        if (!fields[name]) return;
        fields[name].classList.add('input-error');
        if (errsDom[name]) errsDom[name].textContent = msg;
    }

    function clearError(name) {
        if (!fields[name]) return;
        fields[name].classList.remove('input-error');
        if (errsDom[name]) errsDom[name].textContent = '';
    }

    function calcSoDem() {
        const dDenVal = fields['ngay_den'].value;
        const dDiVal  = fields['ngay_di'].value;
        const soDemInput = document.getElementById('so_dem');
        const soDemDisplay = document.getElementById('so_dem_display');

        if (!dDenVal || !dDiVal) {
            soDemInput.value = '';
            soDemDisplay.value = '';
            return;
        }

        const dDen = new Date(dDenVal);
        const dDi  = new Date(dDiVal);
        const diffMs = dDi.getTime() - dDen.getTime();
        const diffDays = diffMs / (1000 * 60 * 60 * 24);

        const soDem = diffDays > 0 ? diffDays : 0;
        soDemInput.value = soDem || '';
        soDemDisplay.value = soDem || '';
    }

    function validateField(name, fromSubmit = false) {
        if (!fromSubmit && !touched[name]) {
            clearError(name);
            return;
        }

        const v = (fields[name]?.value || '').trim();
        clearError(name);

        switch (name) {
            case 'leader_ten_kh': {
                if (!v) {
                    setError(name, 'Vui lòng nhập họ tên trưởng đoàn.');
                    break;
                }
                const reName = /^[A-Za-zÀ-Ỹà-ỹ\s]{2,60}$/;
                if (!reName.test(v)) {
                    setError(name, 'Họ tên chỉ chứa chữ & khoảng trắng, 2–60 ký tự.');
                }
                break;
            }

            case 'leader_cccd':
                if (!v) {
                    setError(name, 'Vui lòng nhập CCCD trưởng đoàn.');
                } else if (!/^\d{9,12}$/.test(v)) {
                    setError(name, 'CCCD sai định dạng (9–12 chữ số).');
                }
                break;

            case 'leader_sdt':
                if (!v) {
                    setError(name, 'Vui lòng nhập số điện thoại.');
                } else if (!/^0\d{8,10}$/.test(v)) {
                    setError(name, 'Số điện thoại sai định dạng.');
                }
                break;

            case 'leader_email': {
                if (!v) {
                    setError(name, 'Vui lòng nhập email.');
                } else {
                    const reEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!reEmail.test(v)) {
                        setError(name, 'Email sai định dạng.');
                    }
                }
                break;
            }

            case 'ngay_den':
            case 'ngay_di': {
                const dDenVal = fields['ngay_den'].value;
                const dDiVal  = fields['ngay_di'].value;
                const dDen = dDenVal ? new Date(dDenVal) : null;
                const dDi  = dDiVal  ? new Date(dDiVal)  : null;
                const today = new Date();
                today.setHours(0,0,0,0);

                clearError('ngay_den');
                clearError('ngay_di');

                if (!dDenVal) {
                    setError('ngay_den', 'Vui lòng chọn ngày đến.');
                } else if (dDen < today) {
                    setError('ngay_den', 'Ngày đến không được nhỏ hơn hôm nay.');
                }

                if (!dDiVal) {
                    setError('ngay_di', 'Vui lòng chọn ngày đi.');
                } else if (dDen && dDi && dDi <= dDen) {
                    setError('ngay_di', 'Ngày đi phải lớn hơn ngày đến.');
                }

                calcSoDem();
                
                break;
            }

            case 'so_nguoi':
                if (!v || parseInt(v, 10) <= 0) {
                    setError(name, 'Số khách phải lớn hơn 0.');
                }
                break;
        }
    }

    function validateAll(fromSubmit = false) {
        Object.keys(fields).forEach(name => validateField(name, fromSubmit));

        const errs = [];
        Object.keys(errsDom).forEach(name => {
            if (errsDom[name] && errsDom[name].textContent) {
                errs.push(errsDom[name].textContent);
            }
        });
        return errs;
    }

    function updateDangerBanner() {
        const hasError = validateAll(false).length > 0;
        btnDanger.style.display = hasError ? 'inline-flex' : 'none';
    }

    Object.keys(fields).forEach(function (name) {
        if (!fields[name]) return;
        fields[name].addEventListener('blur', function () {
            touched[name] = true;
            validateField(name, false);
            updateDangerBanner();
        });
        fields[name].addEventListener('input', function () {
            if (touched[name]) {
                validateField(name, false);
                updateDangerBanner();
            }
        });
    });


    /* ========== ROOM GUEST CARD (CLIENT SIDE) ========== */

    const roomGuestsContainer = document.getElementById('roomGuestsContainer');
    const roomCheckboxes = document.querySelectorAll('.room-checkbox');

    function renderRoomGuestCard(roomId, roomName) {
        if (!roomGuestsContainer) return;
        if (roomGuestsContainer.querySelector('[data-room-id="' + roomId + '"]')) return;

        const div = document.createElement('div');
        div.className = 'room-guest-card';
        div.dataset.roomId = roomId;

        div.innerHTML = `
            <div class="room-guest-title">
                <span>${roomName}</span>
                <small>Để trống = dùng thông tin trưởng đoàn</small>
            </div>
            <div class="row">
                <div class="field">
                    <label>Tên khách phòng</label>
                    <input type="text"
                           name="room_guests[${roomId}][TenKhach]"
                           placeholder="Nếu khác trưởng đoàn">
                </div>
            </div>
            <div class="row">
                <div class="field">
                    <label>CCCD khách <span class="note">Có thể kiểm tra trùng</span></label>
                    <input type="text"
                           class="member-cccd-input"
                           data-room-id="${roomId}"
                           name="room_guests[${roomId}][CCCD]"
                           placeholder="9–12 chữ số">
                </div>
                <div class="field">
                    <label>SĐT khách</label>
                    <input type="text"
                           name="room_guests[${roomId}][SDT]"
                           placeholder="Nếu khác trưởng đoàn">
                </div>
            </div>
            <div class="row">
                <div class="field">
                    <label>Email khách</label>
                    <input type="email"
                           name="room_guests[${roomId}][Email]"
                           placeholder="Nếu khác trưởng đoàn">
                </div>
            </div>
            <div class="row">
                <div class="field">
                    <label>Địa chỉ khách</label>
                    <input type="text"
                           name="room_guests[${roomId}][DiaChi]"
                           placeholder="Nếu khác trưởng đoàn">
                    <div class="error-msg"></div>
                </div>
            </div>
        `;

        roomGuestsContainer.appendChild(div);

        // Gắn event CCCD + validate blur cho card mới
        attachMemberCccdEvents(div.querySelector('.member-cccd-input'));
        attachMemberInputValidation(div);
    }

    function removeRoomGuestCard(roomId) {
        if (!roomGuestsContainer) return;
        const card = roomGuestsContainer.querySelector('[data-room-id="' + roomId + '"]');
        if (card) card.remove();
    }

    roomCheckboxes.forEach(function (cb) {
        cb.addEventListener('change', function () {
            const tr = cb.closest('tr');
            const roomId = cb.value;
            const roomName = tr ? (tr.getAttribute('data-room-name') || ('Phòng ' + roomId)) : ('Phòng ' + roomId);

            if (cb.checked) {
                renderRoomGuestCard(roomId, roomName);
            } else {
                removeRoomGuestCard(roomId);
            }
            updateServiceRoomOptions();
        });
    });


    /* ========== SERVICES ROOM SELECT OPTIONS ========== */

    function getSelectedRooms() {
        const selected = [];
        document.querySelectorAll('.room-checkbox:checked').forEach(function (cb) {
            const tr = cb.closest('tr');
            const id = cb.value;
            const name = tr ? (tr.getAttribute('data-room-name') || ('Phòng ' + id)) : ('Phòng ' + id);
            selected.push({id, name});
        });
        return selected;
    }

    function updateServiceRoomOptions() {
        const selectedRooms = getSelectedRooms();
        document.querySelectorAll('.js-service-room').forEach(function (sel) {
            const currentVal = sel.getAttribute('data-selected-room') || sel.value || '';
            sel.innerHTML = '<option value="">-- Chọn phòng --</option>';
            selectedRooms.forEach(function (r) {
                const opt = document.createElement('option');
                opt.value = r.id;
                opt.textContent = r.name;
                sel.appendChild(opt);
            });
            if (currentVal && selectedRooms.some(r => r.id === currentVal)) {
                sel.value = currentVal;
            } else {
                sel.value = '';
            }
        });
    }

    updateServiceRoomOptions();


    /* ========== CCCD MODAL LOGIC ========== */

    const modalBackdrop = document.getElementById('cccdModalBackdrop');
    const modalInfo = document.getElementById('cccdModalInfo');
    const btnCccdClose = document.getElementById('btnCccdClose');
    const btnCccdNhapLai = document.getElementById('btnCccdNhapLai');
    const btnCccdDungThongTinCu = document.getElementById('btnCccdDungThongTinCu');

    let currentCccdContext = null; // 'leader' | 'member'
    let currentCccdInput = null;

    function openCccdModal(context, inputEl, khData) {
        currentCccdContext = context;
        currentCccdInput = inputEl;

        if (khData) {
            modalInfo.innerHTML = `
                <div><strong>Khách hàng:</strong> ${khData.TenKH || ''}</div>
                <div>SDT: ${khData.SDT || ''}</div>
                <div>Email: ${khData.Email || ''}</div>
                <div>Địa chỉ: ${khData.DiaChi || ''}</div>
            `;
        } else {
            modalInfo.textContent = '';
        }

        modalBackdrop.style.display = 'flex';
    }

    function closeCccdModal() {
        modalBackdrop.style.display = 'none';
        currentCccdContext = null;
        currentCccdInput = null;
    }

    btnCccdClose.addEventListener('click', closeCccdModal);

    btnCccdNhapLai.addEventListener('click', function () {
        if (currentCccdInput) {
            currentCccdInput.value = '';
            currentCccdInput.focus();
        }
        closeCccdModal();
    });

    btnCccdDungThongTinCu.addEventListener('click', function () {
        if (!currentCccdInput) {
            closeCccdModal();
            return;
        }
        const raw = currentCccdInput.dataset.khachhang || '';
        if (raw) {
            try {
                const kh = JSON.parse(raw);
                if (currentCccdContext === 'leader') {
                    if (kh.TenKH) fields.leader_ten_kh.value = kh.TenKH;
                    if (kh.SDT) fields.leader_sdt.value = kh.SDT;
                    if (kh.Email) fields.leader_email.value = kh.Email;
                    const leaderDiaChi = document.getElementById('leader_diachi');
                    if (leaderDiaChi && kh.DiaChi) leaderDiaChi.value = kh.DiaChi;
                } else if (currentCccdContext === 'member') {
                    const card = currentCccdInput.closest('.room-guest-card');
                    if (card && kh) {
                        const tenInput    = card.querySelector('input[name*="[TenKhach]"]');
                        const sdtInput    = card.querySelector('input[name*="[SDT]"]');
                        const emailInput  = card.querySelector('input[name*="[Email]"]');
                        const diachiInput = card.querySelector('input[name*="[DiaChi]"]');
                        if (tenInput && kh.TenKH) tenInput.value = kh.TenKH;
                        if (sdtInput && kh.SDT) sdtInput.value = kh.SDT;
                        if (emailInput && kh.Email) emailInput.value = kh.Email;
                        if (diachiInput && kh.DiaChi) diachiInput.value = kh.DiaChi;
                    }
                }
            } catch (e) { console.error(e); }
        }

        closeCccdModal();
    });

    function callCheckIdentity(identity, callback) {
        if (!identity) { callback(null); return; }

        const url = 'index.php?controller=letan&action=checkIdentityAjax';
        const formData = new FormData();
        formData.append('identity', identity);

        fetch(url, {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            body: formData
        })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (data && data.MaKhachHang) callback(data);
            else callback(null);
        })
        .catch(() => callback(null));
    }

    function attachLeaderCccdEvent() {
        const leaderCccd = document.getElementById('leader_cccd');
        if (!leaderCccd) return;

        leaderCccd.addEventListener('blur', function () {
            const v = leaderCccd.value.trim();
            if (!/^\d{9,12}$/.test(v)) return;

            callCheckIdentity(v, function (kh) {
                if (!kh) return;
                leaderCccd.dataset.khachhang = JSON.stringify(kh);
                openCccdModal('leader', leaderCccd, kh);
            });
        });
    }

    function attachMemberCccdEvents(rootEl) {
        function handler(input) {
            input.addEventListener('blur', function () {
                const v = input.value.trim();
                if (!v || !/^\d{9,12}$/.test(v)) return;

                callCheckIdentity(v, function (kh) {
                    if (!kh) return;
                    input.dataset.khachhang = JSON.stringify(kh);
                    openCccdModal('member', input, kh);
                });
            });
        }

        if (rootEl) {
            handler(rootEl);
        } else {
            document.querySelectorAll('.member-cccd-input').forEach(handler);
        }
    }

    attachLeaderCccdEvent();
    attachMemberCccdEvents();


    /* ========== VALIDATE MEMBER INPUT (BLUR) ========== */

    function attachMemberInputValidation(card) {
        let ten   = card.querySelector('input[name*="[TenKhach]"]');
        let cccd  = card.querySelector('input[name*="[CCCD]"]');
        let sdt   = card.querySelector('input[name*="[SDT]"]');
        let email = card.querySelector('input[name*="[Email]"]');
        let diachi = card.querySelector('input[name*="[DiaChi]"]');

        if (!ten || !cccd || !sdt || !email || !diachi) return;

        const reName  = /^[A-Za-zÀ-Ỹà-ỹ\s]{2,60}$/;
        const reCCCD  = /^\d{9,12}$/;
        const reSDT   = /^0\d{8,10}$/;
        const reEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        function showError(input, msg) {
            input.classList.add("input-error");
            let err = input.nextElementSibling;
            if (!err || !err.classList.contains("error-msg")) {
                err = document.createElement("div");
                err.className = "error-msg";
                input.after(err);
            }
            err.textContent = msg;
        }

        function clearError(input) {
            input.classList.remove("input-error");
            let err = input.nextElementSibling;
            if (err && err.classList.contains("error-msg")) err.textContent = "";
        }

        ten.addEventListener("blur", () => {
            let v = ten.value.trim();
            if (v && !reName.test(v)) showError(ten, "Tên không hợp lệ.");
            else clearError(ten);
        });

        cccd.addEventListener("blur", () => {
            let v = cccd.value.trim();
            if (v && !reCCCD.test(v)) showError(cccd, "CCCD phải 9–12 số.");
            else clearError(cccd);
        });

        sdt.addEventListener("blur", () => {
            let v = sdt.value.trim();
            if (v && !reSDT.test(v)) showError(sdt, "SĐT không hợp lệ.");
            else clearError(sdt);
        });

        email.addEventListener("blur", () => {
            let v = email.value.trim();
            if (v && !reEmail.test(v)) showError(email, "Email không hợp lệ.");
            else clearError(email);
        });

        diachi.addEventListener("blur", () => {
            let v = diachi.value.trim();
            if (v && v.length < 3) showError(diachi, "Địa chỉ quá ngắn.");
            else clearError(diachi);
        });
    }

    // Gán validate blur cho các card đã render sẵn từ PHP (POST cũ)
    document.querySelectorAll('.room-guest-card').forEach(card => {
        attachMemberInputValidation(card);
    });


    /* ========== VALIDATE MEMBER KHI SUBMIT ========== */

    function validateMemberCard(card) {
        let errors = [];

        let ten    = card.querySelector('input[name*="[TenKhach]"]')?.value.trim() || '';
        let cccd   = card.querySelector('input[name*="[CCCD]"]')?.value.trim() || '';
        let sdt    = card.querySelector('input[name*="[SDT]"]')?.value.trim() || '';
        let email  = card.querySelector('input[name*="[Email]"]')?.value.trim() || '';
        let diachi = card.querySelector('input[name*="[DiaChi]"]')?.value.trim() || '';

        const reName  = /^[A-Za-zÀ-Ỹà-ỹ\s]{2,60}$/;
        const reCCCD  = /^\d{9,12}$/;
        const reSDT   = /^0\d{8,10}$/;
        const reEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (ten && !reName.test(ten))   errors.push("Tên thành viên không hợp lệ: " + ten);
        if (cccd && !reCCCD.test(cccd)) errors.push("CCCD thành viên sai định dạng: " + cccd);
        if (sdt && !reSDT.test(sdt))    errors.push("SĐT thành viên sai định dạng: " + sdt);
        if (email && !reEmail.test(email)) errors.push("Email thành viên sai định dạng: " + email);
        if (diachi && diachi.length < 3) errors.push("Địa chỉ thành viên quá ngắn: " + diachi);

        return errors;
    }


    /* ========== FORM SUBMIT ========== */

    form.addEventListener('submit', function (e) {
        const submitter = e.submitter || null;
        const action = submitter ? submitter.value : '';

        clientErrors.style.display = 'none';
        clientErrorsList.innerHTML = '';

        const errs = validateAll(true);

        if (action === 'book') {
            const checkedRooms = document.querySelectorAll('input[name="rooms[]"]:checked');
            if (checkedRooms.length === 0) {
                errs.push('Vui lòng chọn ít nhất một phòng trước khi nhấn Đặt phòng & tạo đoàn.');
            }

            // Dịch vụ: có số lượng thì phải chọn phòng
            document.querySelectorAll('input[name^="services["]').forEach(function (qtyInput) {
                const val = parseInt(qtyInput.value || '0', 10);
                if (val > 0) {
                    const match = qtyInput.name.match(/services\[(\d+)]/);
                    if (match) {
                        const idDV = match[1];
                        const roomSelect = document.querySelector('select[name="services_room[' + idDV + ']"]');
                        if (roomSelect && !roomSelect.value) {
                            errs.push('Dịch vụ có số lượng > 0 phải chọn phòng sử dụng (mã DV: ' + idDV + ').');
                        }
                    }
                }
            });
        }

        // Lỗi thành viên
        document.querySelectorAll('.room-guest-card').forEach(card => {
            errs.push(...validateMemberCard(card));
        });

        // Trùng CCCD
        let leaderCccd = document.getElementById('leader_cccd').value.trim();
        let memberCccds = [];

        document.querySelectorAll('.room-guest-card input[name*="[CCCD]"]').forEach(function (input) {
            let v = input.value.trim();
            if (!v) return;

            if (v === leaderCccd) {
                errs.push("CCCD thành viên không được trùng trưởng đoàn: " + v);
            }
            if (memberCccds.includes(v)) {
                errs.push("CCCD bị trùng giữa các thành viên: " + v);
            }
            memberCccds.push(v);
        });

        if (errs.length > 0) {
            e.preventDefault();
            errs.forEach(msg => {
                const li = document.createElement('li');
                li.textContent = msg;
                clientErrorsList.appendChild(li);
            });
            clientErrors.style.display = 'block';
            btnDanger.style.display = 'inline-flex';
            window.scrollTo({top: 0, behavior: 'smooth'});
        }
    });

    btnDanger.style.display = 'none';
    calcSoDem();
    calcTotalMoney();
});
function formatVnd(n) {
    return new Intl.NumberFormat('vi-VN').format(n) + ' đ';
}

function calcTotalMoney() {
    let soDem = parseInt(document.getElementById('so_dem').value || '0', 10);
    let sumRoom = 0;
    let sumService = 0;

    // PHÒNG
    document.querySelectorAll('.room-checkbox:checked').forEach(cb => {
        const tr = cb.closest('tr');
        const price = parseFloat(tr.dataset.roomPrice || '0');
        sumRoom += price * soDem;
    });

    // DỊCH VỤ
    document.querySelectorAll('input[name^="services["]').forEach(input => {
        const qty = parseInt(input.value || '0', 10);
        if (qty > 0) {
            const price = parseFloat(input.dataset.price || '0');
            sumService += qty * price;
        }
    });

    
    // KHUYẾN MÃI ( % hoặc cố định )
let discount = 0;
const kmSelect = document.getElementById('ma_khuyen_mai');

if (kmSelect && kmSelect.value) {
    const opt = kmSelect.options[kmSelect.selectedIndex];
    const match = opt.textContent.match(/Giảm\s([\d\.]+)/);

    if (match) {
        const kmValue = parseInt(match[1].replace(/\./g, ''), 10);
        const baseAmount = sumRoom + sumService;

        if (kmValue < 100) {
            // GIẢM THEO %
            discount = Math.round(baseAmount * kmValue / 100);
        } else {
            // GIẢM CỐ ĐỊNH (VND)
            discount = kmValue;
        }
    }
}

    const total = Math.max(0, sumRoom + sumService - discount);

    document.getElementById('sumRoom').textContent = formatVnd(sumRoom);
    document.getElementById('sumService').textContent = formatVnd(sumService);
    document.getElementById('sumDiscount').textContent = '- ' + formatVnd(discount);
    document.getElementById('sumTotal').textContent = formatVnd(total);
}

// GẮN EVENT
document.addEventListener('change', function (e) {
    if (
        e.target.classList.contains('room-checkbox') ||
        e.target.name?.startsWith('services[') ||
        e.target.id === 'ngay_den' ||
        e.target.id === 'ngay_di' ||
        e.target.id === 'ma_khuyen_mai'
    ) {
        calcTotalMoney();
    }
});
</script>
</body>
</html>