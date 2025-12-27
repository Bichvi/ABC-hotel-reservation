<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;

$errors        = $errors        ?? [];
$success       = $success       ?? null;
$searchKeyword = $searchKeyword ?? '';
$giaoDich      = $giaoDich      ?? null;
$chiTietPhong  = $chiTietPhong  ?? [];
$dsDichVu      = $dsDichVu      ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt dịch vụ - Lễ tân</title>
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

        /* TOPBAR PREMIUM */

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

        /* MAIN LAYOUT */

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

        .grid {
            display: grid;
            grid-template-columns: minmax(0, 1.9fr) minmax(0, 2.3fr);
            gap: 20px;
        }

        /* CARD PREMIUM */

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
            flex-wrap: wrap;
        }

        .field {
            margin-bottom: 11px;
            flex: 1;
            min-width: 0;
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
        input[type="number"],
        input[type="date"],
        input[type="email"],
        select,
        textarea {
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
            box-sizing: border-box;
        }

        input::placeholder,
        textarea::placeholder {
            color: rgba(148,163,184,0.85);
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: rgba(59,130,246,0.9);
            box-shadow: 0 0 0 1px rgba(59,130,246,0.35);
            background:
                radial-gradient(circle at 0 0, rgba(59,130,246,0.25), transparent 55%),
                rgba(15,23,42,1);
        }

        input[disabled],
        textarea[disabled] {
            background:
                radial-gradient(circle at 0 0, rgba(148,163,184,0.25), transparent 55%),
                rgba(15,23,42,0.96);
            color: var(--text-soft);
            cursor: not-allowed;
        }

        textarea {
            resize: vertical;
            min-height: 40px;
            font-size: 12px;
        }

        .error-msg {
            font-size: 11px;
            color: #fecaca;
            margin-top: 2px;
        }

        .input-error {
            border-color: rgba(239,68,68,0.95) !important;
            background:
                radial-gradient(circle at 0 0, rgba(248,113,113,0.28), transparent 55%),
                rgba(24,24,27,1) !important;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
            background: rgba(22,163,74,0.16);
            color: #4ade80;
            border: 1px solid rgba(22,163,74,0.6);
        }

        .badge-blue {
            background: rgba(37,99,235,0.25);
            color: #bfdbfe;
            border-color: rgba(37,99,235,0.8);
        }

        /* ALERT PREMIUM */

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

        /* BUTTONS PREMIUM */

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
            gap: 8px;
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

        .btn-danger {
            border-radius: 999px;
            border: 1px solid rgba(248,113,113,0.9);
            background:
                radial-gradient(circle at 0 0, rgba(248,113,113,0.40), transparent 55%),
                rgba(24,24,27,1);
            color: #fee2e2;
            padding: 8px 14px;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-danger:hover {
            filter: brightness(1.05);
        }

        /* TABLE PREMIUM */

        .table-wrapper {
            border-radius: 12px;
            border: 1px solid rgba(148,163,184,0.45);
            background:
                radial-gradient(circle at 0 0, rgba(37,99,235,0.12), transparent 55%),
                rgba(15,23,42,0.98);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        thead {
            background: linear-gradient(to right, rgba(15,23,42,0.98), rgba(30,64,175,0.95));
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
                        🏠 Trang lễ tân
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
                        Đặt dịch vụ · <span>Lễ tân</span>
                    </div>
                    <div class="page-title-row">
                        <h1>Đăng ký dịch vụ cho giao dịch đã check-in</h1>
                    </div>
                    <div class="page-subtitle">
                        Tìm giao dịch đã <span style="color:#4ade80;font-weight:600;">Stayed</span>, sau đó gán dịch vụ cho từng phòng với số lượng & ghi chú chi tiết.
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

            <!-- SUCCESS -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <div class="grid">
                <!-- CỘT 1: TÌM KIẾM GIAO DỊCH -->
                <div>
                    <!-- FORM 1: TÌM KIẾM -->
                    <form method="post" id="formSearch">
                        <div class="card">
                            <div class="card-header">
                                <h2>Tìm kiếm giao dịch</h2>
                                <div class="card-tag">Bước 1 · Chọn giao dịch</div>
                            </div>
                            <p class="caption">
                                Nhập <strong>Mã giao dịch</strong> hoặc <strong>CMND/CCCD trưởng đoàn</strong>, sau đó nhấn
                                <strong>Tìm kiếm</strong>.<br>
                                Chỉ những giao dịch có trạng thái
                                <span class="badge">Stayed</span>
                                mới được phép đặt dịch vụ.
                            </p>
                            <div class="card-divider"></div>

                            <div class="section-title">
                                Thông tin tra cứu
                                <span>Chỉ nhập số · tối đa một giá trị</span>
                            </div>

                            <div class="row">
                                <div class="field">
                                    <label>
                                        Mã giao dịch / CMND
                                        <span class="note">VD: 1001 hoặc 0123456789</span>
                                    </label>
                                    <input type="text"
                                           id="search_keyword"
                                           name="search_keyword"
                                           value="<?php echo htmlspecialchars($searchKeyword); ?>"
                                           placeholder="Chỉ nhập số (mã giao dịch hoặc CMND/CCCD)">
                                    <div class="error-msg" id="err_search"></div>
                                </div>
                            </div>

                            <div class="btn-row">
                                <div style="font-size:11px;color:var(--text-muted);">
                                    Gợi ý: nếu khách không nhớ mã giao dịch, có thể tìm theo CCCD trưởng đoàn.
                                </div>
                                <div class="btn-group-right">
                                    <button type="submit" class="btn-secondary" name="btn_action" value="cancel">
                                        ✖ Hủy bỏ
                                    </button>
                                    <button type="submit" class="btn-primary" name="btn_action" value="search">
                                        🔍 Tìm kiếm giao dịch
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- THÔNG TIN GIAO DỊCH -->
                    <form method="post">
                        <input type="hidden" name="search_keyword" value="<?php echo htmlspecialchars($searchKeyword); ?>">

                        <div class="card">
                            <div class="card-header">
                                <h2>Thông tin giao dịch</h2>
                                <div class="card-tag">Bước 2 · Xác nhận đoàn & phòng</div>
                            </div>

                            <?php if ($giaoDich): ?>
                                <?php
                                $tenTruongDoan = $giaoDich['TenKhachHang']
                                    ?? $giaoDich['TenKH']
                                    ?? $giaoDich['TenTruongDoan']
                                    ?? $giaoDich['HoTenTruongDoan']
                                    ?? '';

                                $cccdTruongDoan = $giaoDich['CCCD']
                                    ?? $giaoDich['CMND']
                                    ?? $giaoDich['SoCMND']
                                    ?? $giaoDich['CCCDTruongDoan']
                                    ?? '';

                                $sdtTruongDoan = $giaoDich['SDT']
                                    ?? $giaoDich['Sdt']
                                    ?? $giaoDich['SoDienThoai']
                                    ?? '';
                                ?>
                                <input type="hidden" name="ma_giao_dich" value="<?php echo (int)$giaoDich['MaGiaoDich']; ?>">

                                <p class="caption">
                                    Kiểm tra lại thông tin giao dịch trước khi gán dịch vụ. Các dịch vụ sẽ được tính vào hóa đơn của giao dịch này.
                                </p>
                                <div class="card-divider"></div>

                                <div class="row">
                                    <div class="field">
                                        <label>Mã giao dịch</label>
                                        <input type="text" value="<?php echo htmlspecialchars($giaoDich['MaGiaoDich']); ?>" disabled>
                                    </div>
                                    <div class="field">
                                        <label>Trạng thái</label>
                                        <input type="text" value="<?php echo htmlspecialchars($giaoDich['TrangThai']); ?>" disabled>
                                    </div>
                                    <div class="field">
                                        <label>Tổng tiền hiện tại (đ)</label>
                                        <input type="text"
                                               value="<?php echo number_format($giaoDich['TongTien'] ?? 0, 0, ',', '.'); ?>"
                                               disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="field">
                                        <label>Trưởng đoàn / khách chính</label>
                                        <input type="text"
                                               value="<?php echo htmlspecialchars($tenTruongDoan); ?>"
                                               disabled>
                                    </div>
                                    <div class="field">
                                        <label>CMND/CCCD trưởng đoàn</label>
                                        <input type="text"
                                               value="<?php echo htmlspecialchars($cccdTruongDoan); ?>"
                                               disabled>
                                    </div>
                                    <div class="field">
                                        <label>Số điện thoại trưởng đoàn</label>
                                        <input type="text"
                                               value="<?php echo htmlspecialchars($sdtTruongDoan); ?>"
                                               disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="field">
                                        <label>Số phòng trong giao dịch</label>
                                        <input type="text" value="<?php echo count($chiTietPhong); ?>" disabled>
                                    </div>
                                </div>

                                <?php if (!empty($chiTietPhong)): ?>
                                    <p class="caption" style="margin-top:4px;">
                                        Danh sách phòng thuộc giao dịch:
                                        <?php
                                        $labels = [];
                                        foreach ($chiTietPhong as $ct) {
                                            $labels[] = 'P' . $ct['SoPhong'] . ' (' . $ct['TrangThai'] . ')';
                                        }
                                        echo htmlspecialchars(implode(', ', $labels));
                                        ?>
                                    </p>
                                <?php endif; ?>

                            <?php else: ?>
                                <p class="caption">
                                    Chưa có giao dịch nào được chọn. Vui lòng thực hiện tìm kiếm ở bước 1 bên trên.
                                </p>
                            <?php endif; ?>
                        </div>

                </div>

                <!-- CỘT 2: DANH SÁCH DỊCH VỤ -->
                <div>
                        <div class="card">
                            <div class="card-header">
                                <h2>Danh sách dịch vụ</h2>
                                <div class="card-tag">Bước 3 · Gán dịch vụ cho phòng</div>
                            </div>
                            <p class="caption">
                                Chọn dịch vụ, nhập số lượng và chỉ định phòng sử dụng. Để trống (số lượng 0) nếu phòng không dùng dịch vụ đó.
                            </p>
                            <div class="card-divider"></div>

                            <?php if ($giaoDich && !empty($dsDichVu) && !empty($chiTietPhong)): ?>
                                <div class="table-wrapper">
                                    <table>
                                        <thead>
                                        <tr>
                                            <th style="width:32%;">Dịch vụ</th>
                                            <th style="width:14%;">Đơn giá (đ)</th>
                                            <th style="width:14%;">Số lượng</th>
                                            <th style="width:18%;">Phòng</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($dsDichVu as $dv): ?>
                                            <?php
                                            $maDV  = (int)$dv['MaDichVu'];
                                            $name  = $dv['TenDichVu'] ?? ('Dịch vụ #' . $maDV);
                                            $price = $dv['GiaDichVu'] ?? 0;
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong style="color:var(--text-strong);">
                                                        <?php echo htmlspecialchars($name); ?>
                                                    </strong>
                                                    <div style="font-size:11px;color:var(--text-soft);margin-top:2px;">
                                                        Mã DV: <?php echo $maDV; ?>
                                                        <?php if (!empty($dv['SoLuongToiDa'])): ?>
                                                            · Tối đa <?php echo (int)$dv['SoLuongToiDa']; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php echo number_format($price, 0, ',', '.'); ?> đ
                                                </td>
                                                <td>
                                                    <input type="number"
                                                           name="services[<?php echo $maDV; ?>][so_luong]"
                                                           min="0" value="0">
                                                </td>
                                                <td>
                                                    <select name="services[<?php echo $maDV; ?>][ma_phong]">
                                                        <option value="0">-- Chọn phòng --</option>
                                                        <?php foreach ($chiTietPhong as $ct): ?>
                                                            <option value="<?php echo (int)$ct['MaPhong']; ?>">
                                                                <?php echo 'P' . htmlspecialchars($ct['SoPhong']); ?>
                                                                (<?php echo htmlspecialchars($ct['TrangThai']); ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea name="services[<?php echo $maDV; ?>][note]"
                                                              placeholder="Ghi chú thêm (nếu có)"></textarea>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="btn-row">
                                    <div style="font-size:11px;color:var(--text-muted);max-width:60%;">
                                        Lưu ý: nếu nhập <strong>số lượng &gt; 0</strong> mà không chọn phòng, hệ thống có thể báo lỗi hoặc bỏ qua dịch vụ đó tùy theo controller.
                                    </div>
                                    <div class="btn-group-right">
                                        <button type="reset" class="btn-secondary">
                                            🔄 Làm lại
                                        </button>
                                        <button type="submit" class="btn-danger"
                                                name="btn_action" value="cancel"
                                                onclick="return confirm('Bạn chắc chắn muốn hủy? Các thông tin nhập sẽ không được lưu.');">
                                            ✖ Hủy bỏ
                                        </button>
                                        <button type="submit" class="btn-primary"
                                                name="btn_action" value="save"
                                                onclick="return confirm('Xác nhận đặt dịch vụ cho giao dịch này?');">
                                            Xác nhận đặt dịch vụ
                                            <span class="icon">→</span>
                                        </button>
                                    </div>
                                </div>
                            <?php elseif ($giaoDich && empty($dsDichVu)): ?>
                                <p class="caption">
                                    Hiện chưa có dịch vụ nào đang hoạt động. Vui lòng cấu hình danh mục dịch vụ trước.
                                </p>
                            <?php elseif ($giaoDich && empty($chiTietPhong)): ?>
                                <p class="caption">
                                    Giao dịch không có phòng hợp lệ để đặt dịch vụ.
                                </p>
                            <?php else: ?>
                                <p class="caption">
                                    Vui lòng chọn giao dịch trước khi đặt dịch vụ.
                                </p>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div> <!-- end .grid -->
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const formSearch       = document.getElementById('formSearch');
    const searchInput      = document.getElementById('search_keyword');
    const clientErrors     = document.getElementById('clientErrors');
    const clientErrorsList = document.getElementById('clientErrorsList');
    const errSearch        = document.getElementById('err_search');

    function clearSearchError() {
        if (!searchInput) return;
        searchInput.classList.remove('input-error');
        if (errSearch) errSearch.textContent = '';
        if (clientErrors && clientErrorsList) {
            clientErrors.style.display = 'none';
            clientErrorsList.innerHTML = '';
        }
    }

    function showSearchError(msg) {
        if (!searchInput) return;

        searchInput.classList.add('input-error');
        if (errSearch) errSearch.textContent = msg;

        if (clientErrors && clientErrorsList) {
            clientErrorsList.innerHTML = '';
            const li = document.createElement('li');
            li.textContent = msg;
            clientErrorsList.appendChild(li);
            clientErrors.style.display = 'block';
        }
    }

    function validateSearchLive() {
        const v = searchInput.value.trim();
        clearSearchError();

        if (v !== '' && !/^\d+$/.test(v)) {
            showSearchError('Dữ liệu nhập không hợp lệ. Chỉ nhập số (mã giao dịch hoặc CMND/CCCD).');
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', validateSearchLive);
        searchInput.addEventListener('blur', validateSearchLive);
    }

    if (formSearch && searchInput) {
        formSearch.addEventListener('submit', function (e) {
            clearSearchError();

            const v = searchInput.value.trim();
            let msg = '';

            if (!v) {
                msg = 'Vui lòng nhập thông tin tìm kiếm (mã giao dịch hoặc CMND/CCCD).';
            } else if (!/^\d+$/.test(v)) {
                msg = 'Dữ liệu nhập không hợp lệ. Chỉ nhập số (mã giao dịch hoặc CMND/CCCD).';
            }

            if (msg) {
                e.preventDefault();
                showSearchError(msg);
                window.scrollTo({top: 0, behavior: 'smooth'});
            }
        });
    }
});
</script>
</body>
</html>