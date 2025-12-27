<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;

$errors         = $errors         ?? [];
$success        = $success        ?? null;
$giaoDich       = $giaoDich       ?? null;
$chiTiet        = $chiTiet        ?? [];
$searchMaGD     = $searchMaGD     ?? '';
$tenTruongDoan  = $tenTruongDoan  ?? '';
$cmndTruongDoan = $cmndTruongDoan ?? '';
$soThanhVien    = $soThanhVien    ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Check-in khách hàng</title>
    <style>
        :root {
            --bg-body: #020617;
            --bg-card: rgba(15,23,42,0.98);
            --border-soft: rgba(148,163,184,0.45);
            --border-strong: rgba(148,163,184,0.8);
            --text-main: #e5e7eb;
            --text-soft: #9ca3af;
            --text-muted: #6b7280;
            --text-strong: #f9fafb;
            --accent: #2563eb;
            --accent-2: #4f46e5;
            --danger: #ef4444;
            --success: #22c55e;
            --shadow-strong: 0 24px 70px rgba(15,23,42,0.9);
        }

        * { box-sizing: border-box; }

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
            backdrop-filter: blur(18px);
            background: linear-gradient(120deg, rgba(15,23,42,0.97), rgba(15,23,42,0.93));
            border-bottom: 1px solid rgba(148,163,184,0.4);
            box-shadow: 0 24px 60px rgba(15,23,42,0.9);
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
            width: 36px;
            height: 36px;
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
            letter-spacing: 0.08em;
            font-size: 14px;
            color: #e5e7eb;
        }
        .brand-text-main {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.14em;
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
            background-color: rgba(15,23,42,0.9);
            border: 1px solid rgba(148,163,184,0.7);
            font-size: 12px;
            color: var(--text-soft);
        }
        .user-pill strong {
            color: var(--text-strong);
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
            max-width: 1180px;
            margin: 22px auto 36px;
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
            background: rgba(15,23,42,0.95);
            border: 1px solid rgba(148,163,184,0.55);
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
        .back-link::before { content: "←"; }
        .back-link:hover {
            border-color: rgba(129,140,248,0.95);
            transform: translateY(-1px);
        }

        /* CARD */
        .card {
            background:
                radial-gradient(circle at 0 0, rgba(37,99,235,0.15), transparent 55%),
                radial-gradient(circle at 120% 120%, rgba(129,140,248,0.18), transparent 60%),
                var(--bg-card);
            border-radius: 18px;
            padding: 18px 20px 14px;
            border: 1px solid var(--border-soft);
            box-shadow: var(--shadow-strong);
            position: relative;
            overflow: hidden;
            margin-bottom: 16px;
        }
        .card-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 6px;
        }
        .card-header h2 {
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
            margin: 0 0 8px;
            font-size: 12px;
            color: var(--text-soft);
            line-height: 1.5;
        }
        .card-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(148,163,184,0.35), transparent);
            margin: 8px 0 10px;
        }

        .row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .field {
            margin-bottom: 10px;
            flex: 1;
        }
        label {
            display: block;
            font-size: 12px;
            color: var(--text-main);
            margin-bottom: 4px;
        }
        label .note {
            font-size: 10px;
            color: var(--text-muted);
            margin-left: 4px;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"] {
            width: 100%;
            padding: 8px 11px;
            border-radius: 11px;
            border: 1px solid rgba(148,163,184,0.6);
            font-size: 13px;
            box-sizing: border-box;
            background:
                radial-gradient(circle at 0 0, rgba(148,163,184,0.24), transparent 55%),
                rgba(15,23,42,0.98);
            color: var(--text-strong);
            outline: none;
            transition: all 0.15s ease;
        }
        input::placeholder {
            color: rgba(148,163,184,0.85);
        }
        input:focus {
            border-color: rgba(59,130,246,0.9);
            box-shadow: 0 0 0 1px rgba(59,130,246,0.35);
            background:
                radial-gradient(circle at 0 0, rgba(59,130,246,0.25), transparent 55%),
                rgba(15,23,42,1);
        }
        input[disabled] {
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

        /* BUTTONS */
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
            transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
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
                radial-gradient(circle at 0 0, rgba(248,113,113,0.35), transparent 55%),
                rgba(24,24,27,1);
            color: #fecaca;
            padding: 8px 14px;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-danger:hover {
            filter: brightness(1.03);
        }

        /* BADGE + SCOPE */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 11px;
        }
        .badge-blue { background:#1d4ed8; color:#e5e7eb; }
        .badge-green{ background:#16a34a; color:#ecfdf5; }
        .badge-red  { background:#b91c1c; color:#fee2e2; }

        .scope-group {
            font-size: 12px;
            margin-top: 10px;
            color: var(--text-main);
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
        }
        .scope-group label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }
        input[type="radio"],
        input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #4f46e5;
            cursor: pointer;
        }

        /* TABLE */
        .table-shell {
            border-radius: 14px;
            border: 1px solid rgba(148,163,184,0.45);
            overflow: hidden;
            background:
                radial-gradient(circle at 0 0, rgba(37,99,235,0.12), transparent 55%),
                rgba(15,23,42,0.98);
            margin-top: 6px;
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
            padding: 7px 9px;
            border-bottom: 1px solid rgba(31,41,55,0.9);
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
            background: rgba(37,99,235,0.30);
        }
        .table-empty {
            text-align: center;
            color: #9ca3af;
            padding: 10px;
        }

        @media (max-width: 720px) {
            .topbar-inner { padding-inline: 12px; }
            .container { padding-inline: 12px; }
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
                    <div class="brand-text-sub">Trạm làm thủ tục · Lễ tân</div>
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
                        Quy trình Check-in · <span>Khách đoàn</span>
                    </div>
                    <div class="page-title-row">
                        <h1>Check-in giao dịch đoàn · Premium UI</h1>
                    </div>
                    <div class="page-subtitle">
                        Bước 1: Tìm giao dịch → Bước 2: Chọn phòng cần check-in → Bước 3: Xác nhận giấy tờ & hoàn tất.
                    </div>
                </div>
                <a class="back-link" href="index.php?controller=letan&action=index">
                    Quay lại trang lễ tân
                </a>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <strong>Có lỗi xảy ra:</strong>
                    <ul style="margin:6px 0 0 18px;padding:0;">
                        <?php foreach ($errors as $e): ?>
                            <li><?php echo htmlspecialchars($e); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Lỗi phía client -->
            <div id="clientErrors" class="alert alert-error" style="display:none;">
                <strong>Có lỗi xảy ra:</strong>
                <ul id="clientErrorsList" style="margin:6px 0 0 18px;padding:0;"></ul>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- FORM 1: TÌM KIẾM GIAO DỊCH -->
            <form method="post" id="formSearch">
                <div class="card">
                    <div class="card-header">
                        <h2>Tìm kiếm giao dịch để check-in</h2>
                        <div class="card-tag">Bước 1 · Tra cứu</div>
                    </div>
                    <p class="caption">
                        Nhập <strong>Mã giao dịch</strong> hoặc <strong>CMND/CCCD trưởng đoàn</strong> (chỉ nhập số).
                        Hệ thống sẽ tải danh sách phòng trong giao dịch tương ứng.
                    </p>
                    <div class="card-divider"></div>
                    <div class="row">
                        <div class="field">
                            <label>
                                Mã giao dịch / CMND/CCCD
                                <span class="note">Chỉ nhập số</span>
                            </label>
                            <input type="text"
                                   id="search_ma_gd"
                                   name="search_ma_gd"
                                   value="<?php echo htmlspecialchars($searchMaGD); ?>"
                                   placeholder="VD: 7 hoặc 22653661123">
                            <div class="error-msg" id="err_search"></div>
                        </div>
                        <div class="field" style="flex:0 0 auto;display:flex;align-items:flex-end;gap:8px;">
                            <button type="submit" class="btn-primary" name="btn_action" value="search">
                                🔍 Tìm giao dịch
                            </button>
                            <button type="submit" class="btn-secondary" name="btn_action" value="back">
                                ⬅ Quay lại
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- FORM 2: CHECK-IN -->
            <form method="post" id="formCheckin">
                <div class="card">
                    <div class="card-header">
                        <h2>Thông tin giao dịch đoàn</h2>
                        <div class="card-tag">Bước 2–3 · Chọn phòng & hoàn tất</div>
                    </div>

                    <?php if ($giaoDich && !empty($chiTiet)): ?>
                        <input type="hidden" name="ma_giao_dich" value="<?php echo (int)$giaoDich['MaGiaoDich']; ?>">
                        <input type="hidden" name="search_ma_gd" value="<?php echo htmlspecialchars($searchMaGD); ?>">

                        <p class="caption">
                            Kiểm tra thông tin đoàn và chọn các phòng cần check-in. Chỉ những phòng đang ở trạng thái
                            <span style="color:#bfdbfe;">Booked</span> mới được phép check-in.
                        </p>
                        <div class="card-divider"></div>

                        <div class="row">
                            <div class="field">
                                <label>Mã giao dịch</label>
                                <input type="text"
                                       value="<?php echo htmlspecialchars($giaoDich['MaGiaoDich']); ?>"
                                       disabled>
                            </div>
                            <div class="field">
                                <label>Trạng thái giao dịch</label>
                                <input type="text"
                                       value="<?php echo htmlspecialchars($giaoDich['TrangThai'] ?? ''); ?>"
                                       disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="field">
                                <label>Tên trưởng đoàn</label>
                                <input type="text" value="<?php echo htmlspecialchars($tenTruongDoan); ?>" disabled>
                            </div>
                            <div class="field">
                                <label>CMND/CCCD trưởng đoàn</label>
                                <input type="text" value="<?php echo htmlspecialchars($cmndTruongDoan); ?>" disabled>
                            </div>
                            <div class="field">
                                <label>Số lượng thành viên</label>
                                <input type="number" value="<?php echo (int)$soThanhVien; ?>" disabled>
                            </div>
                        </div>
<?php if (!empty($danhSachThanhVien)): ?>
    <div class="section-title" style="margin-top:10px;font-size:12px;font-weight:600;">
        Danh sách thành viên trong đoàn
    </div>

    <div class="table-shell">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Họ tên</th>
                    <th>CCCD</th>
                    <th>SĐT</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($danhSachThanhVien as $i => $tv): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($tv['TenKH']) ?></td>
                        <td><?= htmlspecialchars($tv['CCCD']) ?></td>
                        <td><?= htmlspecialchars($tv['SDT']) ?></td>
                        <td><?= htmlspecialchars($tv['Email']) ?></td>
                        <td>
                            <?php if ($tv['MaKhachHang'] == $giaoDich['MaKhachHang']): ?>
                                <span class="badge badge-blue">Trưởng đoàn</span>
                            <?php else: ?>
                                <span class="badge badge-green">Thành viên</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
                        <div class="section-title" style="font-size:12px;font-weight:600;margin:10px 0 4px;">
                            Danh sách chi tiết giao dịch
                        </div>
                        <div class="table-shell">
                            <table>
                                <thead>
                                <tr>
                                    <th>Họ tên khách</th>
                                    <th>CMND/CCCD</th>
                                    <th>Mã phòng</th>
                                    <th>Số phòng</th>
                                    <th>Ngày bắt đầu</th>
                                    <th>Ngày kết thúc</th>
                                    <th>Trạng thái</th>
                                    <th style="text-align:center;">Check-in</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($chiTiet as $ct): ?>
                                    <?php
                                    $st  = $ct['TrangThai'];
                                    $cls = 'badge-blue';
                                    if (in_array($st, ['Stayed','DangO'])) $cls = 'badge-green';
                                    if (in_array($st, ['DaHuy','Cancelled'])) $cls = 'badge-red';
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ct['TenKhach'] ?? $tenTruongDoan); ?></td>
                                        <td><?php echo htmlspecialchars($ct['CCCD'] ?? $cmndTruongDoan); ?></td>
                                        <td><?php echo (int)$ct['MaPhong']; ?></td>
                                        <td><?php echo htmlspecialchars($ct['SoPhong']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars(
                                                $ct['NgayNhanDuKien']
                                                    ? substr($ct['NgayNhanDuKien'], 0, 10)
                                                    : ''
                                            ); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars(
                                                $ct['NgayTraDuKien']
                                                    ? substr($ct['NgayTraDuKien'], 0, 10)
                                                    : ''
                                            ); ?>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $cls; ?>">
                                                <?php echo htmlspecialchars($st); ?>
                                            </span>
                                        </td>
                                        <td style="text-align:center;">
                                            <?php if ($st === 'Booked'): ?>
                                                <input type="checkbox"
                                                       name="phong_checkin[]"
                                                       value="<?php echo (int)$ct['MaPhong']; ?>">
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="scope-group">
                            <label>
                                <input type="radio" id="check_scope_all" name="check_scope" value="all" checked>
                                <span>Check-in toàn bộ phòng đang Booked</span>
                            </label>
                            <label>
                                <input type="radio" id="check_scope_partial" name="check_scope" value="partial">
                                <span>Chỉ check-in các phòng được chọn ở bảng trên</span>
                            </label>
                        </div>

                        <div style="margin-top:10px;font-size:12px;">
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                                <input type="checkbox" name="xac_nhan_giay_to" value="1">
                                <span>Tôi xác nhận đã kiểm tra giấy tờ tùy thân hợp lệ của khách / trưởng đoàn.</span>
                            </label>
                        </div>

                        <div class="btn-row">
                            <button type="submit" class="btn-danger"
                                    name="btn_action" value="abort"
                                    onclick="return confirm('Bạn chắc chắn muốn hủy tiến trình check-in?');">
                                ✖ Hủy check-in
                            </button>
                            <div class="btn-group-right">
                                <button type="reset" class="btn-secondary">
                                    🔁 Làm lại chọn phòng
                                </button>
                                <button type="submit" class="btn-primary"
                                        name="btn_action" value="confirm"
                                        onclick="return confirm('Xác nhận hoàn tất check-in cho các phòng đã chọn?');">
                                    ✔ Hoàn tất Check-in
                                </button>
                            </div>
                        </div>

                    <?php else: ?>
                        <p class="caption" style="margin-top:4px;">
                            Chưa có giao dịch nào được chọn. Vui lòng tra cứu giao dịch ở phần trên để hiển thị
                            danh sách phòng và tiến hành check-in.
                        </p>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ====== VALIDATE Ô TÌM KIẾM (chỉ cho nhập số) ======
    const formSearch       = document.getElementById('formSearch');
    const inputSearch      = document.getElementById('search_ma_gd');
    const errSearch        = document.getElementById('err_search');
    const clientErrors     = document.getElementById('clientErrors');
    const clientErrorsList = document.getElementById('clientErrorsList');

    function clearFieldError() {
        if (!inputSearch || !errSearch) return;
        inputSearch.classList.remove('input-error');
        errSearch.textContent = '';
    }

    function setFieldError(msg) {
        if (!inputSearch || !errSearch) return;
        inputSearch.classList.add('input-error');
        errSearch.textContent = msg;
    }

    function clearClientErrorsBox() {
        if (!clientErrors || !clientErrorsList) return;
        clientErrors.style.display = 'none';
        clientErrorsList.innerHTML = '';
    }

    function pushClientError(msg) {
        if (!clientErrors || !clientErrorsList) return;
        const li = document.createElement('li');
        li.textContent = msg;
        clientErrorsList.appendChild(li);
        clientErrors.style.display = 'block';
    }

    // Validate live
    function validateSearchLive() {
        const v = (inputSearch?.value || '').trim();
        clearFieldError();
        clearClientErrorsBox();

        if (v === '') return;

        if (!/^\d+$/.test(v)) {
            const msg = 'Dữ liệu không hợp lệ. Chỉ được nhập số (mã giao dịch hoặc CMND/CCCD).';
            setFieldError(msg);
            pushClientError(msg);
        }
    }

    if (inputSearch) {
        inputSearch.addEventListener('input', validateSearchLive);
        inputSearch.addEventListener('blur', validateSearchLive);
    }

    if (formSearch && inputSearch) {
        formSearch.addEventListener('submit', function (e) {
            clearFieldError();
            clearClientErrorsBox();

            const v = inputSearch.value.trim();
            let hasError = false;

            if (!v) {
                const msg = 'Vui lòng nhập mã giao dịch hoặc CMND/CCCD để tìm kiếm.';
                hasError = true;
                setFieldError(msg);
                pushClientError(msg);
            } else if (!/^\d+$/.test(v)) {
                const msg = 'Dữ liệu không hợp lệ. Chỉ được nhập số (mã giao dịch hoặc CMND/CCCD).';
                hasError = true;
                setFieldError(msg);
                pushClientError(msg);
            }

            if (hasError) {
                e.preventDefault();
                window.scrollTo({top: 0, behavior: 'smooth'});
            }
        });
    }

    // ====== LOGIC CHECK TOÀN BỘ / MỘT PHẦN CHO PHÒNG ======
    const radioAll     = document.getElementById('check_scope_all');
    const radioPartial = document.getElementById('check_scope_partial');

    function getRoomCheckboxes() {
        return Array.from(document.querySelectorAll('input[name="phong_checkin[]"]'));
    }

    function checkAllRooms() {
        getRoomCheckboxes().forEach(cb => {
            cb.checked = true;
        });
    }

    function uncheckAllRooms() {
        getRoomCheckboxes().forEach(cb => {
            cb.checked = false;
        });
    }

    function applyScopeFromRadio() {
        const selected = document.querySelector('input[name="check_scope"]:checked');
        if (!selected) return;
        if (selected.value === 'all') {
            checkAllRooms();
        } else if (selected.value === 'partial') {
            uncheckAllRooms();
        }
    }

    if (radioAll) {
        radioAll.addEventListener('change', function () {
            if (radioAll.checked) applyScopeFromRadio();
        });
    }
    if (radioPartial) {
        radioPartial.addEventListener('change', function () {
            if (radioPartial.checked) applyScopeFromRadio();
        });
    }

    // Lần đầu vào trang
    applyScopeFromRadio();
});
</script>
</body>
</html>