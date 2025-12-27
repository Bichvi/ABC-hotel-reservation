<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;

// Controller nên set $hasSearch = true sau khi bấm nút "Tìm phòng phù hợp"
$hasSearch = $hasSearch ?? false;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt phòng trực tiếp - Lễ tân</title>
    <style>
    :root {
        --bg-main: #0f172a;
        --bg-card: #020617;
        --border-subtle: rgba(148, 163, 184, 0.35);
        --accent: #2563eb;
        --accent-strong: #4f46e5;
        --danger: #ef4444;
        --text-main: #e5e7eb;
        --text-soft: #9ca3af;
        --text-muted: #6b7280;
        --text-strong: #f9fafb;
        --divider: rgba(148, 163, 184, 0.25);
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
            radial-gradient(circle at top left, #1d4ed8 0, transparent 55%),
            radial-gradient(circle at bottom right, #4f46e5 0, transparent 50%),
            #020617;
        color: var(--text-main);
    }

    body::before {
        content: "";
        position: fixed;
        inset: 0;
        background:
            radial-gradient(circle at 10% 0%, rgba(59, 130, 246, 0.25) 0, transparent 55%),
            radial-gradient(circle at 90% 100%, rgba(129, 140, 248, 0.2) 0, transparent 50%);
        opacity: 0.45;
        pointer-events: none;
        z-index: -1;
    }

    .app-shell {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .topbar {
        position: sticky;
        top: 0;
        z-index: 20;
        backdrop-filter: blur(18px);
        background: linear-gradient(120deg, rgba(15, 23, 42, 0.96), rgba(15, 23, 42, 0.94));
        border-bottom: 1px solid rgba(148, 163, 184, 0.3);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.75);
    }

    .topbar-inner {
        max-width: 1180px;
        margin: 0 auto;
        padding: 12px 20px 10px;
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
        width: 34px;
        height: 34px;
        border-radius: 999px;
        background: conic-gradient(from 220deg, #38bdf8, #4f46e5, #22c55e, #38bdf8);
        padding: 2px;
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.55);
    }

    .brand-logo-inner {
        width: 100%;
        height: 100%;
        border-radius: inherit;
        background:
            radial-gradient(circle at 30% 0%, rgba(248, 250, 252, 0.85), transparent 55%),
            radial-gradient(circle at 80% 120%, rgba(59, 130, 246, 0.8), transparent 60%),
            #020617;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        color: #e5e7eb;
        letter-spacing: 0.04em;
    }

    .brand-text-main {
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #e5e7eb;
    }

    .brand-text-sub {
        font-size: 13px;
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
        padding: 5px 12px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(148, 163, 184, 0.5);
        font-size: 12px;
        color: var(--text-soft);
    }

    .user-pill strong {
        color: var(--text-strong);
        font-weight: 600;
    }

    .topbar-nav a {
        font-size: 13px;
        color: #e5e7eb;
        text-decoration: none;
        margin-left: 10px;
        padding: 6px 12px;
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        background:
            radial-gradient(circle at 0 0, rgba(59, 130, 246, 0.25), transparent 50%),
            rgba(15, 23, 42, 0.92);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }

    .topbar-nav a:hover {
        border-color: rgba(129, 140, 248, 0.9);
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.7);
        transform: translateY(-1px);
    }

    .topbar-nav a:last-child {
        background: linear-gradient(135deg, #ef4444, #b91c1c);
        border-color: transparent;
    }

    .topbar-nav a:last-child:hover {
        filter: brightness(1.05);
        box-shadow: 0 14px 30px rgba(220, 38, 38, 0.55);
    }

    .main {
        flex: 1;
    }

    .container {
        max-width: 1180px;
        margin: 22px auto 40px;
        padding: 0 18px;
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
        gap: 4px;
    }

    .page-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 9px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(148, 163, 184, 0.4);
        font-size: 11px;
        color: var(--text-soft);
    }

    .page-title-row h1 {
        margin: 0;
        font-size: 24px;
        color: #f9fafb;
        letter-spacing: 0.02em;
    }

    .page-subtitle {
        font-size: 13px;
        color: var(--text-soft);
        margin-top: 2px;
    }

    .back-link {
        font-size: 13px;
        text-decoration: none;
        color: #e5e7eb;
        border-radius: 999px;
        padding: 7px 13px;
        border: 1px solid rgba(148, 163, 184, 0.4);
        background:
            radial-gradient(circle at 0 50%, rgba(59, 130, 246, 0.2), transparent 55%),
            rgba(15, 23, 42, 0.86);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.7);
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .back-link::before {
        content: "←";
    }

    .back-link:hover {
        border-color: rgba(129, 140, 248, 0.95);
        transform: translateY(-1px);
    }

    .grid {
        display: grid;
        grid-template-columns: minmax(0, 3fr) minmax(0, 2.2fr);
        gap: 20px;
    }

    .card {
        background:
            radial-gradient(circle at 0 0, rgba(37, 99, 235, 0.16), transparent 50%),
            rgba(15, 23, 42, 0.96);
        border-radius: 16px;
        padding: 18px 20px 16px;
        border: 1px solid var(--border-subtle);
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.55);
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
        font-size: 17px;
        color: #f9fafb;
        letter-spacing: 0.01em;
    }

    .card-tag {
        font-size: 11px;
        padding: 3px 9px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(148, 163, 184, 0.45);
        color: var(--text-soft);
    }

    .card p.caption {
        margin: 0 0 12px;
        font-size: 12px;
        color: var(--text-soft);
        line-height: 1.5;
    }

    .card-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, var(--divider), transparent);
        margin: 8px 0 14px;
    }

    .section-title {
        font-size: 13px;
        font-weight: 600;
        margin: 10px 0 6px;
        color: #e5e7eb;
        letter-spacing: 0.03em;
        text-transform: uppercase;
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
        margin-bottom: 10px;
        flex: 1;
    }

    label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12px;
        color: #e5e7eb;
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
        padding: 8px 11px;
        border-radius: 10px;
        border: 1px solid rgba(148, 163, 184, 0.55);
        font-size: 13px;
        outline: none;
        background:
            radial-gradient(circle at 0 0, rgba(148, 163, 184, 0.22), transparent 55%),
            rgba(15, 23, 42, 0.9);
        color: #f9fafb;
        transition: all 0.16s ease;
    }

    input::placeholder {
        color: rgba(148, 163, 184, 0.85);
    }

    input:focus,
    select:focus {
        border-color: rgba(59, 130, 246, 0.9);
        box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.35);
        background:
            radial-gradient(circle at 0 0, rgba(59, 130, 246, 0.3), transparent 55%),
            rgba(15, 23, 42, 0.95);
    }

    .input-error {
        border-color: rgba(239, 68, 68, 0.9) !important;
        background:
            radial-gradient(circle at 0 0, rgba(248, 113, 113, 0.25), transparent 55%),
            rgba(24, 24, 27, 0.98) !important;
    }

    .error-msg {
        font-size: 11px;
        color: #fecaca;
        margin-top: 2px;
    }

    .alert {
        border-radius: 12px;
        padding: 9px 12px;
        margin-bottom: 12px;
        font-size: 12px;
        border: 1px solid transparent;
        backdrop-filter: blur(20px);
    }

    .alert-error {
        background:
            radial-gradient(circle at 0 0, rgba(248, 113, 113, 0.35), transparent 55%),
            rgba(24, 24, 27, 0.96);
        border-color: rgba(248, 113, 113, 0.65);
        color: #fecaca;
    }

    .alert-success {
        background:
            radial-gradient(circle at 0 0, rgba(52, 211, 153, 0.45), transparent 55%),
            rgba(22, 163, 74, 0.9);
        border-color: rgba(134, 239, 172, 0.85);
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
        background: rgba(15, 23, 42, 0.9);
        border: 1px solid rgba(148, 163, 184, 0.6);
        color: #f9fafb;
        font-size: 11px;
    }

    .pill span {
        font-weight: 600;
    }

    .btn-row {
        margin-top: 12px;
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
        color: #f9fafb;
        background: linear-gradient(135deg, #2563eb, #4f46e5);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 18px 35px rgba(37, 99, 235, 0.65);
        transition: transform 0.14s ease, box-shadow 0.14s ease, filter 0.14s ease;
        letter-spacing: 0.02em;
    }

    .btn-primary span {
        font-size: 14px;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 22px 42px rgba(37, 99, 235, 0.75);
        filter: brightness(1.03);
    }

    .btn-secondary {
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.6);
        background:
            radial-gradient(circle at 0 0, rgba(148, 163, 184, 0.35), transparent 55%),
            rgba(15, 23, 42, 0.96);
        color: #e5e7eb;
        padding: 8px 14px;
        font-size: 12px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-secondary:hover {
        border-color: rgba(209, 213, 219, 0.95);
        transform: translateY(-1px);
    }

    .btn-danger {
        border-radius: 999px;
        border: 1px solid rgba(248, 113, 113, 0.8);
        background:
            radial-gradient(circle at 0 0, rgba(248, 113, 113, 0.35), transparent 55%),
            rgba(24, 24, 27, 0.98);
        color: #fecaca;
        padding: 6px 10px;
        font-size: 11px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-danger::before {
        content: "⚠";
    }

    .room-list {
        max-height: 260px;
        overflow: auto;
        border-radius: 10px;
        border: 1px solid rgba(148, 163, 184, 0.4);
        background:
            radial-gradient(circle at 0 0, rgba(37, 99, 235, 0.12), transparent 55%),
            rgba(15, 23, 42, 0.96);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    thead {
        background: linear-gradient(to right, rgba(15, 23, 42, 0.98), rgba(30, 64, 175, 0.95));
        position: sticky;
        top: 0;
        z-index: 1;
    }

    th,
    td {
        padding: 8px 10px;
        border-bottom: 1px solid rgba(31, 41, 55, 0.9);
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
        background: rgba(30, 64, 175, 0.35);
    }

    .table-empty {
        text-align: center;
        color: #9ca3af;
        padding: 12px;
        font-size: 12px;
    }

    input[type="checkbox"] {
        width: 15px;
        height: 15px;
        cursor: pointer;
        accent-color: #4f46e5;
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
            gap: 8px;
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
                    <!-- ĐÃ FIX: trỏ đúng controller letan -->
                    <a href="index.php?controller=letan&action=index">
                        <span style="font-size:12px;">🏠</span> Trang lễ tân
                    </a>
                    <a href="index.php?controller=auth&action=logout">
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
                        Đặt phòng trực tiếp · <span style="font-weight:600;color:#e5e7eb;">Lễ tân</span>
                    </div>
                    <div class="page-title-row">
                        <h1>Đặt phòng tại quầy</h1>
                    </div>
                    <div class="page-subtitle">
                        Nhập thông tin khách · Tìm phòng phù hợp · Chọn phòng & dịch vụ · Xác nhận đặt phòng.
                    </div>
                </div>
                <!-- ĐÃ FIX: trỏ đúng controller letan -->
                <a class="back-link" href="index.php?controller=letan&action=index">
                    Quay lại trang chủ lễ tân
                </a>
            </div>

            <!-- Lỗi validate từ server -->
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

            <!-- Lỗi validate phía client -->
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

            <form method="post" id="formDatPhong">
                <div class="grid">
                    <!-- CỘT 1: THÔNG TIN KHÁCH + YÊU CẦU -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Thông tin khách hàng</h2>
                            <div class="card-tag">Bước 1 · Thông tin cơ bản</div>
                        </div>
                        <p class="caption">
                            Lễ tân vui lòng nhập đầy đủ thông tin khách. Các ô sai định dạng sẽ được tô đỏ và hiển thị lỗi.
                        </p>
                        <div class="card-divider"></div>

                        <div class="row">
                            <div class="field">
                                <label>
                                    Họ tên khách <span style="color:#f97373;">*</span>
                                    <span class="note">Ví dụ: Nguyễn Văn A</span>
                                </label>
                                <input type="text" id="ten_kh" name="ten_kh"
                                       value="<?php echo htmlspecialchars($_POST['ten_kh'] ?? ''); ?>"
                                       placeholder="Nguyễn Văn A">
                                <div class="error-msg" id="err_ten_kh"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="field">
                                <label>
                                    CCCD / CMND <span style="color:#f97373;">*</span>
                                    <span class="note">9–12 chữ số</span>
                                </label>
                                <input type="text" id="cccd" name="cccd"
                                       value="<?php echo htmlspecialchars($_POST['cccd'] ?? ''); ?>"
                                       placeholder="Chỉ gồm 9–12 chữ số">
                                <div class="error-msg" id="err_cccd"></div>
                            </div>
                            <div class="field">
                                <label>
                                    Số điện thoại <span style="color:#f97373;">*</span>
                                    <span class="note">Bắt đầu bằng 0</span>
                                </label>
                                <input type="text" id="sdt" name="sdt"
                                       value="<?php echo htmlspecialchars($_POST['sdt'] ?? ''); ?>"
                                       placeholder="09xxxxxxxx">
                                <div class="error-msg" id="err_sdt"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="field">
                                <label>
                                    Email <span style="color:#f97373;">*</span>
                                    <span class="note">Dùng gửi xác nhận</span>
                                </label>
                                <input type="email" id="email" name="email"
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                       placeholder="ten@email.com">
                                <div class="error-msg" id="err_email"></div>
                            </div>
                            <div class="field">
                                <label>
                                    Địa chỉ
                                    <span class="note">Không bắt buộc</span>
                                </label>
                                <input type="text" name="diachi"
                                       value="<?php echo htmlspecialchars($_POST['diachi'] ?? ''); ?>"
                                       placeholder="Địa chỉ cư trú">
                            </div>
                        </div>

                        <div class="section-title" style="margin-top:18px;">
                            Thông tin đặt phòng <span>Bắt buộc để gợi ý phòng</span>
                        </div>

                        <div class="row">
                            <div class="field">
                                <label>Ngày đến <span style="color:#f97373;">*</span></label>
                                <input type="date" id="ngay_den" name="ngay_den"
                                       value="<?php echo htmlspecialchars($ngayDen ?? ''); ?>">
                                <div class="error-msg" id="err_ngay_den"></div>
                            </div>
                            <div class="field">
                                <label>Ngày đi <span style="color:#f97373;">*</span></label>
                                <input type="date" id="ngay_di" name="ngay_di"
                                       value="<?php echo htmlspecialchars($ngayDi ?? ''); ?>">
                                <div class="error-msg" id="err_ngay_di"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="field">
                                <label>
                                    Số khách (ước tính)
                                    <span class="note">Dùng để lọc sức chứa phòng</span>
                                </label>
                                <input type="number" id="so_nguoi" name="so_nguoi" min="1"
                                       value="<?php echo htmlspecialchars($_POST['so_nguoi'] ?? '2'); ?>">
                                <div class="error-msg" id="err_so_nguoi"></div>
                            </div>
                            <div class="field">
                                <label>
                                    Ghi chú yêu cầu đặc biệt
                                    <span class="note">Giường phụ, ăn chay...</span>
                                </label>
                                <input type="text" name="ghichu"
                                       value="<?php echo htmlspecialchars($_POST['ghichu'] ?? ''); ?>"
                                       placeholder="VD: cần giường phụ, ăn chay...">
                            </div>
                        </div>

                        <div class="btn-row">
                            <div id="btnDanger" class="btn-danger" style="display:none;">
                                Thiếu hoặc sai thông tin. Vui lòng kiểm tra lại các ô màu đỏ.
                            </div>
                            <div class="btn-group-right">
                                <!-- ĐÃ FIX: làm lại = reload trang, xoá sạch POST -->
                                <button type="button" class="btn-secondary"
                                        onclick="window.location='index.php?controller=letan&action=datPhongTrucTiep'">
                                    🔄 Làm lại
                                </button>

                                <!-- Nút TÌM PHÒNG: chỉ tìm phòng, chưa đặt -->
                                <button type="submit" name="btn_action" value="search" class="btn-secondary">
                                    🔍 Tìm phòng phù hợp
                                </button>

                                <!-- Nút ĐẶT PHÒNG: sau khi đã có kết quả & chọn phòng -->
                                <button type="submit" name="btn_action" value="book" class="btn-primary">
                                    Đặt phòng
                                    <span>→</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- CỘT 2: CHỌN PHÒNG + DỊCH VỤ -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Chọn phòng & dịch vụ</h2>
                            <div class="card-tag">Bước 2 & 3 · Phòng + Dịch vụ</div>
                        </div>
                        <p class="caption">
                            Bước 1: Nhấn “Tìm phòng phù hợp” để hệ thống gợi ý.<br>
                            Bước 2: Chọn phòng & nhập số lượng dịch vụ nếu khách yêu cầu.<br>
                            Nút “Đặt phòng” chỉ tạo giao dịch khi đã chọn ít nhất một phòng.
                        </p>
                        <div class="card-divider"></div>

                        <div class="section-title">Danh sách phòng phù hợp</div>
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
                                <?php if ($hasSearch && !empty($dsPhong) && $dsPhong instanceof mysqli_result && $dsPhong->num_rows > 0): ?>
                                    <?php while ($p = $dsPhong->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox"
                                                       name="rooms[]"
                                                       value="<?php echo (int)$p['MaPhong']; ?>"
                                                    <?php
                                                    if (!empty($_POST['rooms']) &&
                                                        in_array($p['MaPhong'], array_map('intval', $_POST['rooms']))) {
                                                        echo 'checked';
                                                    }
                                                    ?>
                                                >
                                            </td>
                                            <td><strong><?php echo htmlspecialchars($p['SoPhong']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($p['LoaiPhong']); ?></td>
                                            <td><?php echo (int)$p['SoKhachToiDa']; ?> khách</td>
                                            <td><?php echo number_format($p['Gia'], 0, ',', '.'); ?> đ</td>
                                            <td><?php echo htmlspecialchars($p['ViewPhong']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
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

                        <div class="section-title" style="margin-top:14px;">
                            Đăng ký dịch vụ kèm theo
                            <span>Ghi nhận trước, có thể thêm sau</span>
                        </div>
                        <p class="caption" style="margin-bottom:8px;">
                            Ghi nhận trước các dịch vụ khách muốn sử dụng. Có thể đặt thêm sau tại màn hình “Đặt dịch vụ”.
                        </p>

                        <?php if (!empty($dsDichVu)): ?>
                            <table>
                                <thead>
                                <tr>
                                    <th>Dịch vụ</th>
                                    <th>Giá</th>
                                    <th style="width:90px;">Số lượng</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($dsDichVu as $dv): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($dv['TenDichVu']); ?></td>
                                        <td><?php echo number_format($dv['GiaDichVu'], 0, ',', '.'); ?> đ</td>
                                        <td>
                                            <input type="number"
                                                   name="services[<?php echo (int)$dv['MaDichVu']; ?>]"
                                                   min="0"
                                                   value="<?php echo htmlspecialchars($_POST['services'][$dv['MaDichVu']] ?? '0'); ?>"
                                                   style="width:70px;">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div style="font-size:12px;color:#9ca3af;margin-top:4px;">
                                Hiện chưa cấu hình dịch vụ nào.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('formDatPhong');
        const clientErrors = document.getElementById('clientErrors');
        const clientErrorsList = document.getElementById('clientErrorsList');
        const btnDanger = document.getElementById('btnDanger');

        const fields = {
            ten_kh:   document.getElementById('ten_kh'),
            cccd:     document.getElementById('cccd'),
            sdt:      document.getElementById('sdt'),
            email:    document.getElementById('email'),
            ngay_den: document.getElementById('ngay_den'),
            ngay_di:  document.getElementById('ngay_di'),
            so_nguoi: document.getElementById('so_nguoi')
        };

        const errsDom = {
            ten_kh:   document.getElementById('err_ten_kh'),
            cccd:     document.getElementById('err_cccd'),
            sdt:      document.getElementById('err_sdt'),
            email:    document.getElementById('err_email'),
            ngay_den: document.getElementById('err_ngay_den'),
            ngay_di:  document.getElementById('err_ngay_di'),
            so_nguoi: document.getElementById('err_so_nguoi')
        };

        const touched = {
            ten_kh:false, cccd:false, sdt:false, email:false,
            ngay_den:false, ngay_di:false, so_nguoi:false
        };

        function setError(name, msg) {
            fields[name].classList.add('input-error');
            errsDom[name].textContent = msg;
        }
        function clearError(name) {
            fields[name].classList.remove('input-error');
            errsDom[name].textContent = '';
        }

        function validateField(name, fromSubmit = false) {
            if (!fromSubmit && !touched[name]) {
                clearError(name);
                return;
            }

            const v = fields[name].value.trim();
            clearError(name);

            switch (name) {
                case 'ten_kh': {
                    if (!v) {
                        setError(name, 'Vui lòng nhập họ tên khách hàng.');
                        break;
                    }
                    const reName = /^[A-Za-zÀ-Ỹà-ỹ\s]{2,50}$/;
                    if (!reName.test(v)) {
                        setError(
                            name,
                            'Họ tên chỉ được chứa chữ cái và khoảng trắng, độ dài 2–50 ký tự (không chứa số hoặc ký tự đặc biệt).'
                        );
                    }
                    break;
                }

                case 'cccd':
                    if (!v) {
                        setError(name, 'Vui lòng nhập CMND/CCCD.');
                    } else if (!/^\d{9,12}$/.test(v)) {
                        setError(name, 'CMND/CCCD sai định dạng (chỉ 9–12 chữ số).');
                    }
                    break;

                case 'sdt':
                    if (!v) {
                        setError(name, 'Vui lòng nhập số điện thoại.');
                    } else if (!/^0\d{8,10}$/.test(v)) {
                        setError(name, 'Số điện thoại sai định dạng.');
                    }
                    break;

                case 'email': {
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
                        setError('ngay_den', 'Ngày đến không được nhỏ hơn ngày hiện tại.');
                    }

                    if (!dDiVal) {
                        setError('ngay_di', 'Vui lòng chọn ngày đi.');
                    } else if (dDen && dDi && dDi <= dDen) {
                        setError('ngay_di', 'Ngày đi phải lớn hơn ngày đến.');
                    }
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
            Object.keys(fields).forEach(function (name) {
                validateField(name, fromSubmit);
            });

            const errs = [];
            Object.keys(errsDom).forEach(function (name) {
                if (errsDom[name].textContent) errs.push(errsDom[name].textContent);
            });
            return errs;
        }

        function updateDangerButton() {
            const hasError = validateAll(false).length > 0;
            btnDanger.style.display = hasError ? 'inline-flex' : 'none';
        }

        Object.keys(fields).forEach(function (name) {
            fields[name].addEventListener('blur', function () {
                touched[name] = true;
                validateField(name, false);
                updateDangerButton();
            });
            fields[name].addEventListener('input', function () {
                if (touched[name]) {
                    validateField(name, false);
                    updateDangerButton();
                }
            });
        });

        form.addEventListener('submit', function (e) {
            const submitter = e.submitter || null;
            const action = submitter ? submitter.value : '';

            clientErrors.style.display = 'none';
            clientErrorsList.innerHTML = '';

            const errs = validateAll(true);

            if (action === 'book') {
                const checkedRooms = document.querySelectorAll('input[name="rooms[]"]:checked');
                if (checkedRooms.length === 0) {
                    errs.push('Vui lòng chọn ít nhất một phòng trước khi nhấn Đặt phòng.');
                }
            }

            if (errs.length > 0) {
                e.preventDefault();
                clientErrorsList.innerHTML = '';
                errs.forEach(function (msg) {
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
    });
</script>
</body>
</html>