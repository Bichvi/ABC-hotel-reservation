<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;

$errors        = $errors        ?? [];
$success       = $success       ?? null;
$searchKeyword = $searchKeyword ?? '';
$giaoDich      = $giaoDich      ?? null;
$chiTiet       = $chiTiet       ?? [];
$form          = $form          ?? [
    'ten_kh'   => '',
    'cccd'     => '',
    'sdt'      => '',
    'email'    => '',
    'ngay_den' => '',
    'ngay_di'  => '',
    'so_nguoi' => 1,
    'ma_phong' => ''
];
$dsPhong       = $dsPhong ?? [];

// controller có thể chưa truyền
$dsKhuyenMai   = $dsKhuyenMai ?? [];
$chiTietDichVu = $chiTietDichVu ?? [];

// khuyến mãi áp dụng (nếu controller có biến $khuyenMai)
$khuyenMai = $khuyenMai ?? null;

// ====== Dữ liệu KM cho realtime JS ======
$kmGiaTri = 0.0;
$kmText   = 'Không áp dụng';
if (!empty($khuyenMai) && isset($khuyenMai['TenKhuyenMai'], $khuyenMai['GiaTri'])) {
    $kmGiaTri = (float)$khuyenMai['GiaTri'];
    $kmText   = $khuyenMai['TenKhuyenMai'] . ' (' . $kmGiaTri . ($kmGiaTri < 100 ? '%' : 'đ') . ')';
}

// ====== Helper format ======
function vnd($n) {
    return number_format((float)$n, 0, ',', '.') . ' đ';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa thông tin đặt phòng</title>
    <style>
        :root {
            --bg-body: #020617;
            --bg-page: #020617;
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
            --success: #16a34a;
            --shadow-strong: 0 24px 70px rgba(15,23,42,0.9);
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at 0% 0%, rgba(37,99,235,0.18), transparent 55%),
                radial-gradient(circle at 100% 100%, rgba(79,70,229,0.22), transparent 55%),
                var(--bg-page);
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
            background: linear-gradient(120deg, rgba(15,23,42,0.98), rgba(15,23,42,0.94));
            color: #e5e7eb;
            padding: 10px 0;
            border-bottom: 1px solid rgba(148,163,184,0.45);
            backdrop-filter: blur(18px);
            position: sticky;
            top: 0;
            z-index: 30;
            box-shadow: 0 20px 55px rgba(15,23,42,0.95);
        }
        .topbar-inner {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand-logo {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            padding: 2px;
            background: conic-gradient(from 210deg,#0ea5e9,#4f46e5,#22c55e,#a855f7,#0ea5e9);
            box-shadow: 0 16px 35px rgba(37,99,235,0.8);
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
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.08em;
        }
        .brand-text-main {
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }
        .brand-text-sub {
            font-size: 12px;
            color: #9ca3af;
        }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .user-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            background: radial-gradient(circle at 0 0, rgba(148,163,184,0.30), transparent 55%);
            border: 1px solid rgba(148,163,184,0.7);
            font-size: 12px;
            color: var(--text-soft);
        }
        .user-pill strong {
            font-weight: 600;
            color: var(--text-strong);
        }
        .topbar-nav a {
            font-size: 12px;
            color: #e5e7eb;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid rgba(148,163,184,0.6);
            background:
                radial-gradient(circle at 0 0, rgba(59,130,246,0.18), transparent 60%),
                rgba(15,23,42,0.98);
            margin-left: 4px;
            display: inline-flex;
            gap: 6px;
            align-items: center;
            transition: all 0.16s ease;
        }
        .topbar-nav a:hover {
            border-color: rgba(129,140,248,0.95);
            box-shadow: 0 16px 40px rgba(15,23,42,0.9);
            transform: translateY(-1px);
        }
        .topbar-nav a.logout {
            background: linear-gradient(135deg,#ef4444,#b91c1c);
            border-color: transparent;
        }
        .topbar-nav a.logout:hover {
            filter: brightness(1.05);
            box-shadow: 0 18px 38px rgba(239,68,68,0.75);
        }

        /* MAIN LAYOUT */
        .main { flex: 1; }
        .container {
            max-width: 1180px;
            margin: 22px auto 40px;
            padding: 0 20px 24px;
        }

        .page-title {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
            color: var(--text-main);
        }
        .page-title-left {
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
            background: rgba(15,23,42,0.96);
            border: 1px solid rgba(148,163,184,0.7);
            font-size: 11px;
            color: var(--text-soft);
            text-transform: uppercase;
            letter-spacing: 0.09em;
        }
        .page-chip span { color: var(--text-strong); font-weight: 600; }
        .page-title h1 {
            margin: 0;
            font-size: 22px;
            color: var(--text-strong);
            letter-spacing: 0.04em;
        }
        .page-title span.subtitle { font-size: 13px; color: var(--text-soft); }

        .back-link {
            font-size: 13px;
            text-decoration: none;
            color: var(--text-main);
            border-radius: 999px;
            padding: 8px 14px;
            border: 1px solid rgba(148,163,184,0.7);
            background:
                radial-gradient(circle at 0 50%, rgba(59,130,246,0.22), transparent 55%),
                rgba(15,23,42,0.98);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            box-shadow: var(--shadow-strong);
            transition: all 0.16s ease;
        }
        .back-link::before { content: "←"; }
        .back-link:hover {
            border-color: rgba(129,140,248,0.98);
            transform: translateY(-1px);
        }

        /* CARD */
        .card {
            background:
                radial-gradient(circle at 0 0, rgba(37,99,235,0.16), transparent 55%),
                radial-gradient(circle at 120% 120%, rgba(129,140,248,0.20), transparent 60%),
                var(--bg-card);
            border-radius: 18px;
            padding: 16px 18px 14px;
            box-shadow: var(--shadow-strong);
            border: 1px solid var(--border-soft);
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
        }
        .card h2 {
            margin: 0 0 8px;
            font-size: 16px;
            color: var(--text-strong);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .card h2 span.tag {
            font-size: 11px;
            font-weight: 500;
            padding: 2px 8px;
            border-radius: 999px;
            background: rgba(15,23,42,0.96);
            color: #bfdbfe;
            border: 1px solid rgba(129,140,248,0.9);
        }
        .card p.caption {
            margin: 0 0 10px;
            font-size: 13px;
            color: var(--text-soft);
        }
        .card-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(148,163,184,0.45), transparent);
            margin: 6px 0 10px;
        }

        .row { display: flex; gap: 12px; flex-wrap: wrap; }
        .field { margin-bottom: 10px; flex: 1; min-width: 190px; }
        label { display: block; font-size: 13px; color: var(--text-main); margin-bottom: 4px; }
        label span.required { color: #fca5a5; }

        input[type="text"],
        input[type="date"],
        input[type="number"],
        select {
            width: 100%;
            padding: 8px 11px;
            border-radius: 11px;
            border: 1px solid rgba(148,163,184,0.6);
            font-size: 13px;
            box-sizing: border-box;
            background:
                radial-gradient(circle at 0 0, rgba(148,163,184,0.22), transparent 55%),
                rgba(15,23,42,0.98);
            color: var(--text-strong);
            outline: none;
            transition: all 0.16s ease;
        }
        input::placeholder { color: rgba(148,163,184,0.85); }
        input:focus, select:focus {
            border-color: rgba(59,130,246,0.95);
            box-shadow: 0 0 0 1px rgba(59,130,246,0.38);
            background:
                radial-gradient(circle at 0 0, rgba(59,130,246,0.25), transparent 55%),
                rgba(15,23,42,1);
        }

        .input-error {
            border-color: rgba(239,68,68,0.95) !important;
            background:
                radial-gradient(circle at 0 0, rgba(248,113,113,0.30), transparent 55%),
                rgba(24,24,27,1) !important;
        }
        .error-msg { font-size: 11px; color: #fecaca; margin-top: 2px; }

        .alert {
            border-radius: 12px;
            padding: 10px 12px;
            margin-bottom: 12px;
            font-size: 13px;
            border: 1px solid transparent;
            backdrop-filter: blur(18px);
        }
        .alert-error {
            background:
                radial-gradient(circle at 0 0, rgba(248,113,113,0.40), transparent 55%),
                rgba(24,24,27,0.98);
            border-color: rgba(248,113,113,0.78);
            color: #fee2e2;
        }
        .alert-success {
            background:
                radial-gradient(circle at 0 0, rgba(34,197,94,0.40), transparent 55%),
                rgba(22,163,74,0.96);
            border-color: rgba(134,239,172,0.92);
            color: #ecfdf5;
        }

        .btn-primary {
            border: none;
            border-radius: 999px;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 500;
            color: #f9fafb;
            background: linear-gradient(135deg,var(--accent),var(--accent-2));
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 20px 40px rgba(37,99,235,0.75);
            transition: transform 0.16s ease, box-shadow 0.16s ease, filter 0.16s ease;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 24px 52px rgba(37,99,235,0.85);
            filter: brightness(1.05);
        }
        .btn-secondary {
            border-radius: 999px;
            border: 1px solid rgba(148,163,184,0.7);
            background:
                radial-gradient(circle at 0 0, rgba(148,163,184,0.32), transparent 55%),
                rgba(15,23,42,0.98);
            color: var(--text-main);
            padding: 8px 14px;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.16s ease;
        }
        .btn-secondary:hover {
            border-color: rgba(209,213,219,0.98);
            transform: translateY(-1px);
        }
        .btn-row {
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 11px; }
        .badge-blue { background: #1d4ed8; color: #dbeafe; }
        .badge-green{ background: #16a34a; color: #dcfce7; }
        .badge-red  { background: #b91c1c; color: #fee2e2; }

        .table-wrapper {
            margin-top: 6px;
            border-radius: 14px;
            border: 1px solid rgba(148,163,184,0.45);
            overflow: hidden;
            background:
                radial-gradient(circle at 0 0, rgba(37,99,235,0.12), transparent 55%),
                rgba(15,23,42,0.98);
        }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        thead { background: linear-gradient(to right, rgba(15,23,42,0.98), rgba(30,64,175,0.96)); }
        th, td {
            padding: 7px 9px;
            border-bottom: 1px solid rgba(31,41,55,0.9);
            text-align: left;
            white-space: nowrap;
        }
        th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #e5e7eb;
        }
        tbody tr:hover { background: rgba(37,99,235,0.28); }

        .muted { color: var(--text-soft); font-size: 12px; }
        .card-nested { margin-top: 12px; }

        @media (max-width: 1024px) { .container { padding-inline: 14px; } }
        @media (max-width: 768px) {
            .topbar-inner, .page-title { flex-direction: column; align-items: flex-start; }
            .back-link { align-self: flex-start; margin-top: 4px; }
        }
    </style>
</head><body>
<div class="app-shell">
    <!-- ===== TOPBAR ===== -->
    <header class="topbar">
        <div class="topbar-inner">
            <div class="topbar-left">
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
                        <span style="opacity:.8;">Xin chào,</span>
                        <strong><?= htmlspecialchars($user['Username']) ?></strong>
                    </div>
                <?php endif; ?>
                <nav class="topbar-nav">
                    <a href="index.php?controller=letan&action=index">🏠 Trang lễ tân</a>
                    <a href="index.php?controller=auth&action=logout" class="logout">Đăng xuất</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- ===== MAIN ===== -->
    <main class="main">
        <div class="container">

            <!-- ===== TIÊU ĐỀ ===== -->
            <div class="page-title">
                <div class="page-title-left">
                    <div class="page-chip">Sửa thông tin đặt phòng · <span>Lễ tân</span></div>
                    <h1>Sửa thông tin đặt phòng</h1>
                    <span class="subtitle">Tìm giao dịch → chọn phòng → chỉnh sửa → lưu thông tin</span>
                </div>
                <a class="back-link" href="index.php?controller=letan&action=index">
                    Quay lại trang chủ lễ tân
                </a>
            </div>

            <!-- ===== ERROR / SUCCESS ===== -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <strong>Có lỗi xảy ra (server):</strong>
                    <ul style="margin:6px 0 0 18px;">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div id="clientErrors" class="alert alert-error" style="display:none;">
                <strong>Có lỗi xảy ra:</strong>
                <ul id="clientErrorsList" style="margin:6px 0 0 18px;"></ul>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <!-- ======================================================
                 FORM 1: TÌM KIẾM GIAO DỊCH (BƯỚC 1)
            ======================================================= -->
            <form method="post" id="formSearch">
                <div class="card">
                    <h2>Tìm kiếm giao dịch <span class="tag">Bước 1</span></h2>
                    <p class="caption">
                        Nhập <strong>Mã giao dịch</strong> hoặc <strong>CMND/CCCD trưởng đoàn</strong> rồi nhấn <strong>Tìm kiếm</strong>.
                    </p>
                    <div class="card-divider"></div>

                    <div class="row">
                        <div class="field">
                            <label>Mã giao dịch / CMND</label>
                            <input type="text"
                                   id="search_keyword"
                                   name="search_keyword"
                                   value="<?= htmlspecialchars($searchKeyword) ?>"
                                   placeholder="VD: 1 hoặc 0123456789">
                            <div class="error-msg" id="err_search_keyword"></div>
                        </div>

                        <div class="field" style="flex:0 0 auto;display:flex;align-items:flex-end;gap:8px;">
                            <button type="submit" class="btn-primary" name="btn_action" value="search">
                                🔍 Tìm kiếm
                            </button>
                            <button type="submit" class="btn-secondary" name="btn_action" value="cancel">
                                Hủy thao tác
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- ======================================================
                 FORM 2: SỬA THÔNG TIN ĐẶT PHÒNG (BƯỚC 2)
            ======================================================= -->
            <form method="post"
                  id="formEditBooking"
                  action="index.php?controller=letan&action=suaThongTinDatPhong">

                <div class="card">
                    <h2>Thông tin đặt phòng hiện tại <span class="tag">Bước 2</span></h2>

                    <?php if ($giaoDich && !empty($chiTiet)): ?>
                        <?php $ct0 = $chiTiet[0]; ?>

                        <!-- ===== HIDDEN ===== -->
                        <input type="hidden" name="ma_giao_dich" value="<?= (int)$giaoDich['MaGiaoDich'] ?>">
                        <input type="hidden" name="ma_phong_cu" value="<?= (int)$ct0['MaPhong'] ?>">
                        <input type="hidden" name="search_keyword" value="<?= htmlspecialchars($searchKeyword) ?>">

                        <!-- KHÓA: GIÁ TRỊ KHUYẾN MÃI CHO JS -->
                        <input type="hidden" id="km_giatri" value="<?= htmlspecialchars((string)$kmGiaTri) ?>">
                        <input type="hidden" id="tong_phong_khac"
       value="<?= (float)($tongPhongKhac ?? 0) ?>">

<input type="hidden" id="tien_phong_dang_sua"
       value="<?= (float)($tienPhongDangSua ?? 0) ?>">
                        <!-- ===== TỔNG TIỀN HIỆN TẠI ===== -->
                        <div class="row">
                            <div class="field">
                                <label>Tổng tiền hiện tại</label>
                                <input type="text" value="<?= vnd($giaoDich['TongTien'] ?? 0) ?>" readonly>
                            </div>
                        </div>

                        <!-- ===== KHUYẾN MÃI ===== -->
                        <div class="row">
                            <div class="field">
                                <label>Khuyến mãi áp dụng</label>
                                <input type="text" value="<?= htmlspecialchars($kmText) ?>" readonly>
                                <div class="muted">
                                    * Tiền giảm sẽ được tính lại realtime
                                </div>
                            </div>
                        </div>

                        <!-- ===== MÃ + TRẠNG THÁI ===== -->
                        <div class="row">
                            <div class="field">
                                <label>Mã giao dịch</label>
                                <input type="text" value="<?= htmlspecialchars($giaoDich['MaGiaoDich']) ?>" disabled>
                            </div>
                            <div class="field">
                                <label>Trạng thái giao dịch</label>
                                <input type="text" value="<?= htmlspecialchars($giaoDich['TrangThai']) ?>" disabled>
                            </div>
                        </div>

                        <!-- ===== THÔNG TIN KHÁCH ===== -->
                        <div class="row">
                            <div class="field">
                                <label>Họ tên trưởng đoàn <span class="required">*</span></label>
                                <input type="text" id="ten_kh" name="ten_kh"
                                       value="<?= htmlspecialchars($form['ten_kh']) ?>">
                                <div class="error-msg" id="err_ten_kh"></div>
                            </div>
                            <div class="field">
                                <label>CMND/CCCD <span class="required">*</span></label>
                                <input type="text" id="cccd" name="cccd"
                                       value="<?= htmlspecialchars($form['cccd']) ?>">
                                <div class="error-msg" id="err_cccd"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="field">
                                <label>Số điện thoại <span class="required">*</span></label>
                                <input type="text" id="sdt" name="sdt"
                                       value="<?= htmlspecialchars($form['sdt']) ?>">
                                <div class="error-msg" id="err_sdt"></div>
                            </div>
                            <div class="field">
                                <label>Email <span class="required">*</span></label>
                                <input type="text" id="email" name="email"
                                       value="<?= htmlspecialchars($form['email']) ?>">
                                <div class="error-msg" id="err_email"></div>
                            </div>
                        </div>
<?php if (!empty($thanhVien) && count($thanhVien) > 1): ?>
<hr>
<h4>Thông tin thành viên</h4>

<?php foreach ($thanhVien as $tv): ?>
    <?php if ($tv['MaKhachHang'] == $giaoDich['MaKhachHang']) continue; ?>

    <div class="member-box">

        <input type="hidden"
               name="members[<?= $tv['MaKhachHang'] ?>][id]"
               value="<?= $tv['MaKhachHang'] ?>">

        <label>Họ tên</label>
        <input type="text"
               name="members[<?= $tv['MaKhachHang'] ?>][ten]"
               value="<?= htmlspecialchars($tv['TenKH']) ?>">

        <label>CCCD</label>
        <input type="text"
               name="members[<?= $tv['MaKhachHang'] ?>][cccd]"
               value="<?= htmlspecialchars($tv['CCCD']) ?>">

        <label>SĐT</label>
        <input type="text"
               name="members[<?= $tv['MaKhachHang'] ?>][sdt]"
               value="<?= htmlspecialchars($tv['SDT']) ?>">

        <label>Email</label>
        <input type="text"
       class="member-email"
       name="members[<?= $tv['MaKhachHang'] ?>][email]"
       value="<?= htmlspecialchars($tv['Email']) ?>">
    </div>
<?php endforeach; ?>
<?php endif; ?>
                        <!-- ===== NGÀY + SỐ NGƯỜI ===== -->
                        <div class="row">
                            <div class="field">
                                <label>Ngày đến <span class="required">*</span></label>
                                <input type="date" id="ngay_den" name="ngay_den"
                                       value="<?= htmlspecialchars($form['ngay_den']) ?>">
                                <div class="error-msg" id="err_ngay_den"></div>
                            </div>
                            <div class="field">
                                <label>Ngày đi <span class="required">*</span></label>
                                <input type="date" id="ngay_di" name="ngay_di"
                                       value="<?= htmlspecialchars($form['ngay_di']) ?>">
                                <div class="error-msg" id="err_ngay_di"></div>
                            </div>
                            <div class="field">
                                <label>Số đêm</label>
                                <input type="text" id="so_dem" value="0" readonly>
                            </div>
                            <div class="field">
                                <label>Số người <span class="required">*</span></label>
                                <input type="number"
       class="form-control"
       value="<?= (int)$form['so_nguoi'] ?>"
       readonly
       disabled>

<small class="text-muted">
  Số người được xác định khi tạo giao dịch, không thể chỉnh sửa.
</small>
                                <div class="error-msg" id="err_so_nguoi"></div>
                            </div>
                        </div>

                        <!-- ===== CHỌN PHÒNG ===== -->
                        <div class="row">
                            <div class="field">
                                <label>Phòng <span class="required">*</span></label>
                                <select name="ma_phong" id="ma_phong">
                                    <option value="">-- Chọn phòng --</option>
                                    <?php foreach ($dsPhong as $p): ?>
                                        <?php
                                            $donGia = (float)($p['DonGia'] ?? $p['Gia'] ?? 0);
                                        ?>
                                        <option value="<?= (int)$p['MaPhong'] ?>"
                                                data-dongia="<?= $donGia ?>"
                                            <?= ((int)$p['MaPhong'] === (int)$form['ma_phong']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars(
                                                ($p['SoPhong'] ?? '') . ' - ' . ($p['LoaiPhong'] ?? '') .
                                                ' (tối đa ' . ($p['SoKhachToiDa'] ?? '?') . ' khách)'
                                            ) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="error-msg" id="err_ma_phong"></div>
                            </div>
                        </div>                        <!-- =========================
                             DỊCH VỤ ĐÃ ĐẶT
                        ========================== -->
                        <?php if (!empty($chiTietDichVu)): ?>
                            <h2 style="margin-top:12px;">Dịch vụ đã đặt</h2>
                            <p class="muted" style="margin:0 0 8px;">
                                * Đổi số lượng sẽ tự tính tiền ngay. Đặt <strong>0</strong> = xóa dịch vụ.
                            </p>

                            <div class="table-wrapper">
                                <table>
                                    <thead>
                                    <tr>
                                        <th>Dịch vụ</th>
                                        <th>Đơn giá</th>
                                        <th>Số lượng</th>
                                        <th>Thành tiền</th>
                                        <th>Xóa</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($chiTietDichVu as $dv): ?>
                                        <?php
                                        $maDV       = (int)($dv['MaDichVu'] ?? 0);
                                        $donGiaDV   = (int)($dv['GiaDichVu'] ?? 0);
                                        $soLuongDV  = (int)($dv['SoLuong'] ?? 0);
                                        $thanhTienDV = $donGiaDV * $soLuongDV;
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($dv['TenDichVu'] ?? '') ?></td>
                                            <td><?= vnd($donGiaDV) ?></td>
                                            <td>
                                                <!-- IMPORTANT:
                                                     - có class serviceQty
                                                     - có data-dongia
                                                     - có data-madv
                                                -->
                                                <input type="number"
                                                       class="serviceQty"
                                                       name="services[<?= $maDV ?>]"
                                                       value="<?= $soLuongDV ?>"
                                                       min="0"
                                                       data-dongia="<?= htmlspecialchars((string)$donGiaDV) ?>"
                                                       data-madv="<?= $maDV ?>"
                                                       style="width:80px">

                                                <!-- Checkbox remove (ẩn) để không phá controller cũ nếu đang dùng remove_services[] -->
                                                <input type="checkbox"
                                                       class="serviceRemove"
                                                       name="remove_services[]"
                                                       value="<?= $maDV ?>"
                                                       style="display:none;">
                                            </td>

                                            <!-- line total realtime -->
                                            <td class="serviceLineTotal" data-madv="<?= $maDV ?>">
                                                <?= vnd($thanhTienDV) ?>
                                            </td>

                                            <td style="text-align:center;">
                                                <!-- cho người dùng tick xóa (nếu muốn) -->
                                                <input type="checkbox"
                                                       class="serviceRemoveUi"
                                                       data-madv="<?= $maDV ?>"
                                                       <?= ($soLuongDV <= 0) ? 'checked' : '' ?>>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="muted" style="margin-top:10px;">Không có dịch vụ nào.</p>
                        <?php endif; ?>


                        <!-- =========================
                             THÔNG TIN THANH TOÁN (REALTIME)
                        ========================== -->
                        <div class="card" style="margin-top:14px;">
                            <h2>Thông tin thanh toán <span class="tag">Tự động tính</span></h2>
                            <div class="card-divider"></div>

                            <div class="row">
                                <div class="field">
                                    <label>Đơn giá phòng</label>
                                    <input type="text" id="view_dongia_phong" value="<?= vnd($donGiaHienTai ?? 0) ?>" readonly>
                                </div>
                                <div class="field">
                                    <label>Tổng tiền phòng</label>
                                    <input type="text" id="view_tong_phong" value="<?= vnd($tongPhong ?? 0) ?>" readonly>
                                </div>
                            </div>

                            <div class="row">
                                <div class="field">
                                    <label>Tổng tiền dịch vụ</label>
                                    <input type="text" id="view_tong_dv" value="<?= vnd($tongDV ?? 0) ?>" readonly>
                                </div>
                                <div class="field">
                                    <label>Tổng trước giảm</label>
                                    <input type="text" id="view_tong_truoc_giam" value="<?= vnd($tongTruocGiam ?? 0) ?>" readonly>
                                </div>
                            </div>

                            <div class="row">
                                <div class="field">
                                    <label>Tiền giảm</label>
                                    <input type="text" id="view_tien_giam" value="<?= vnd($tienGiam ?? 0) ?>" readonly>
                                </div>
                                <div class="field">
                                    <label><strong>THÀNH TIỀN</strong></label>
                                    <input type="text"
                                           id="view_thanh_tien"
                                           value="<?= vnd($thanhTien ?? 0) ?>"
                                           readonly
                                           style="font-weight:700;font-size:14px;">
                                </div>
                            </div>
                        </div>

                        <!-- =========================
                             NÚT
                        ========================== -->
                        <div class="btn-row">
                            <button type="reset" class="btn-secondary">🔄 Làm lại</button>

                            <div style="display:flex;gap:8px;">
                                <button type="submit"
                                        class="btn-secondary"
                                        name="btn_action"
                                        value="cancel"
                                        onclick="return confirm('Bạn chắc chắn muốn hủy thao tác? Dữ liệu sẽ không được lưu.');">
                                    Hủy thao tác
                                </button>

                                <button type="submit" class="btn-primary" name="btn_action" value="save">
                                    💾 Lưu thông tin
                                </button>
                            </div>
                        </div>

                    <?php else: ?>
                        <p class="caption">Chưa có giao dịch nào được chọn. Vui lòng tìm kiếm ở phần trên.</p>
                    <?php endif; ?>
                </div>
            </form>

            <!-- ======================================================
                 DANH SÁCH CHI TIẾT GIAO DỊCH (PICK ROOM)
            ======================================================= -->
            <?php if ($giaoDich && !empty($chiTiet)): ?>
                <div class="card">
                    <h2>Danh sách chi tiết giao dịch</h2>
                    <p class="caption">
                        Chọn đúng phòng cần sửa → bấm <strong>Sửa phòng này</strong> để load lại form theo phòng đó.
                    </p>

                    <div class="table-wrapper">
                        <table>
                            <thead>
                            <tr>
                                <th>Mã phòng</th>
                                <th>Số phòng</th>
                                <th>Loại phòng</th>
                                <th>Ngày bắt đầu</th>
                                <th>Ngày kết thúc</th>
                                <th>Số người</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($chiTiet as $ct): ?>
                                <tr>
                                    <td><?= (int)$ct['MaPhong'] ?></td>
                                    <td><?= htmlspecialchars($ct['SoPhong'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($ct['LoaiPhong'] ?? '') ?></td>
                                    <td><?= htmlspecialchars(substr((string)($ct['NgayNhanDuKien'] ?? ''), 0, 10)) ?></td>
                                    <td><?= htmlspecialchars(substr((string)($ct['NgayTraDuKien'] ?? ''), 0, 10)) ?></td>
                                    <td><?= isset($ct['SoNguoi']) ? (int)$ct['SoNguoi'] : 0 ?></td>
                                    <td>
                                        <?php
                                        $st  = $ct['TrangThai'] ?? '';
                                        $cls = 'badge-blue';
                                        if ($st === 'DaHuy' || $st === 'Cancelled') $cls = 'badge-red';
                                        elseif (in_array($st, ['Stayed','CheckedIn'], true)) $cls = 'badge-green';
                                        ?>
                                        <span class="badge <?= $cls ?>"><?= htmlspecialchars($st) ?></span>
                                    </td>
                                    <td>
                                        <form method="post"
                                              action="index.php?controller=letan&action=suaThongTinDatPhong"
                                              style="display:inline;">
                                            <input type="hidden" name="search_keyword"
                                                   value="<?= htmlspecialchars($searchKeyword ?: ($giaoDich['MaGiaoDich'] ?? '')) ?>">
                                            <input type="hidden" name="ma_giao_dich"
                                                   value="<?= (int)($giaoDich['MaGiaoDich'] ?? 0) ?>">
                                            <input type="hidden" name="ma_phong_cu"
                                                   value="<?= (int)($ct['MaPhong'] ?? 0) ?>">
                                            <button type="submit" class="btn-secondary" name="btn_action" value="pick_room">
                                                Sửa phòng này
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </main>
</div><script>
document.addEventListener('DOMContentLoaded', function () {

    /* =====================================================
       HELPER
    ====================================================== */
    function money(n){
        const x = Math.round(Number(n || 0));
        return x.toLocaleString('vi-VN') + ' đ';
    }

    function qs(sel){ return document.querySelector(sel); }
    function qsa(sel){ return document.querySelectorAll(sel); }

    /* =====================================================
       ELEMENTS
    ====================================================== */
    const fields = {
        ngay_den : qs('#ngay_den'),
        ngay_di  : qs('#ngay_di'),
        ma_phong : qs('#ma_phong')
    };
    const soDemInput = qs('#so_dem');

    const view = {
        dgPhong        : qs('#view_dongia_phong'),
        tongPhong      : qs('#view_tong_phong'),
        tongDV         : qs('#view_tong_dv'),
        tongTruocGiam  : qs('#view_tong_truoc_giam'),
        tienGiam       : qs('#view_tien_giam'),
        thanhTien      : qs('#view_thanh_tien')
    };

    // khuyến mãi (nếu controller không truyền thì = 0)
    const kmGiaTri = Number(qs('#km_giatri')?.value || 0);

    /* =====================================================
       CALC NIGHTS
    ====================================================== */
 function calcNights(){
    if (!fields.ngay_den?.value || !fields.ngay_di?.value) return 1;

    const d1 = new Date(fields.ngay_den.value + 'T00:00:00');
    const d2 = new Date(fields.ngay_di.value  + 'T00:00:00');

    const diff = Math.round((d2 - d1) / 86400000);
    return Math.max(1, diff);
}

    /* =====================================================
       UPDATE SERVICE LINE
    ====================================================== */
    function updateServiceLines(){
        qsa('.serviceQty').forEach(inp => {
            const qty  = Number(inp.value || 0);
            const dg   = Number(inp.dataset.dongia || 0);
            const madv = inp.dataset.madv;

            const line = qs('.serviceLineTotal[data-madv="'+madv+'"]');
            if (line) line.textContent = money(qty * dg);

            // auto tick checkbox remove khi qty = 0
            const cb = qs('.serviceRemove[value="'+madv+'"]');
            if (cb) cb.checked = (qty === 0);
        });
    }

    /* =====================================================
       MAIN REALTIME CALC
    ====================================================== */
    function tinhTienRealtime(){
        if (!fields.ma_phong) return;

        // số đêm
        const soDem = calcNights();
        if (soDemInput) soDemInput.value = soDem;

        // đơn giá phòng từ option
        const opt = fields.ma_phong.selectedOptions?.[0];
        const donGiaPhong = Number(opt?.dataset?.dongia || 0);

        // tiền phòng
        const tongPhongKhac =
    Number(document.getElementById('tong_phong_khac')?.value || 0);

// tiền phòng của phòng đang sửa (theo ngày / phòng mới)
const tienPhongMoi = soDem * donGiaPhong;

// tổng tiền phòng TOÀN GIAO DỊCH
const tongPhong = tongPhongKhac + tienPhongMoi;

        // tiền dịch vụ
        let tongDV = 0;
        qsa('.serviceQty').forEach(inp => {
            const qty = Number(inp.value || 0);
            const dg  = Number(inp.dataset.dongia || 0);
            tongDV += qty * dg;
        });

        const tongTruocGiam = tongPhong + tongDV;

        // tiền giảm
        let tienGiam = 0;
        if (kmGiaTri > 0){
            if (kmGiaTri < 100) tienGiam = tongTruocGiam * kmGiaTri / 100;
            else tienGiam = Math.min(kmGiaTri, tongTruocGiam);
        }

        const thanhTien = tongTruocGiam - tienGiam;

        // update UI
        if (view.dgPhong)       view.dgPhong.value       = money(donGiaPhong);
        if (view.tongPhong)     view.tongPhong.value     = money(tongPhong);
        if (view.tongDV)        view.tongDV.value        = money(tongDV);
        if (view.tongTruocGiam) view.tongTruocGiam.value = money(tongTruocGiam);
        if (view.tienGiam)      view.tienGiam.value      = money(tienGiam);
        if (view.thanhTien)     view.thanhTien.value     = money(thanhTien);

        updateServiceLines();
    }

    /* =====================================================
       EVENTS – REALTIME
    ====================================================== */

    // đổi ngày / đổi phòng
    ['change','input'].forEach(ev => {
        fields.ngay_den?.addEventListener(ev, tinhTienRealtime);
        fields.ngay_di?.addEventListener(ev, tinhTienRealtime);
        fields.ma_phong?.addEventListener(ev, tinhTienRealtime);
    });

    // đổi số lượng dịch vụ
    document.addEventListener('input', e => {
        if (e.target.classList.contains('serviceQty')) {
            tinhTienRealtime();
        }
    });

    // checkbox xóa UI → set qty = 0
    document.addEventListener('change', e => {
        if (e.target.classList.contains('serviceRemoveUi')){
            const madv = e.target.dataset.madv;
            const qtyInput = qs('.serviceQty[data-madv="'+madv+'"]');
            if (qtyInput){
                qtyInput.value = e.target.checked ? 0 : 1;
                tinhTienRealtime();
            }
        }
    });

    // chạy ngay khi load
    tinhTienRealtime();
    
});
/* =====================================================
   VALIDATE INPUT REALTIME (TRƯỞNG ĐOÀN + THÀNH VIÊN)
===================================================== */

function showError(input, msg){
    input.classList.add('input-error');
    let err = input.nextElementSibling;
    if (!err || !err.classList.contains('error-msg')){
        err = document.createElement('div');
        err.className = 'error-msg';
        input.after(err);
    }
    err.textContent = msg;
}

function clearError(input){
    input.classList.remove('input-error');
    let err = input.nextElementSibling;
    if (err && err.classList.contains('error-msg')){
        err.textContent = '';
    }
}

// regex chuẩn
const REG_NAME  = /^[\p{L}\s]+$/u;
const REG_CCCD  = /^\d{9,12}$/;
const REG_PHONE = /^0\d{8,10}$/;
const REG_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// ===== validate 1 input =====
function validateField(input){
    const name = input.name || '';
    const val  = input.value.trim();

    // tên
    if (name.includes('[ten]') || name === 'ten_kh'){
        if (val === '' || !REG_NAME.test(val)){
            showError(input, 'Tên chỉ được chứa chữ cái, không chứa số.');
            return false;
        }
    }

    // CCCD
    if (name.includes('[cccd]') || name === 'cccd'){
        if (val !== '' && !REG_CCCD.test(val)){
            showError(input, 'CCCD phải từ 9–12 chữ số.');
            return false;
        }
    }

    // SĐT
    if (name.includes('[sdt]') || name === 'sdt'){
        if (val !== '' && !REG_PHONE.test(val)){
            showError(input, 'SĐT không hợp lệ.');
            return false;
        }
    }

    // Email
    if (name.includes('[email]') || name === 'email'){
        if (val !== '' && !REG_EMAIL.test(val)){
            showError(input, 'Email không hợp lệ.');
            return false;
        }
    }

    clearError(input);
    return true;
}

// ===== bind realtime =====
document.querySelectorAll(
    'input[name="ten_kh"], input[name="cccd"], input[name="sdt"], input[name="email"],' +
    'input[name^="members"]'
).forEach(inp => {
    inp.addEventListener('input', () => validateField(inp));
});
document.getElementById('formEditBooking')?.addEventListener('submit', function (e){
    let ok = true;
    this.querySelectorAll('input').forEach(inp => {
        if (!validateField(inp)) ok = false;
    });

    if (!ok){
        e.preventDefault();
        alert('Vui lòng kiểm tra lại thông tin nhập.');
    }
});
</script>
</body>
</html>