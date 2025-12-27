<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user          = $_SESSION['user'] ?? null;
$errors        = $errors        ?? [];
$success       = $success       ?? null;
$searchKeyword = $searchKeyword ?? '';
$giaoDich      = $giaoDich      ?? null;
$chiTiet       = $chiTiet       ?? [];
$allowCancel   = $allowCancel   ?? false;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hủy đặt phòng - Lễ tân · Premium UI</title>

    <style>
        :root {
            --bg-body: #020617;
            --bg-shell: #020617;
            --bg-card: rgba(15,23,42,0.97);
            --bg-elevated: rgba(15,23,42,0.98);
            --bg-chip: rgba(15,23,42,0.92);

            --border-soft: rgba(148,163,184,0.45);
            --border-strong: rgba(148,163,184,0.75);

            --text-main: #e5e7eb;
            --text-soft: #9ca3af;
            --text-muted: #6b7280;
            --text-strong: #f9fafb;

            --accent: #2563eb;
            --accent-2: #4f46e5;
            --accent-soft: rgba(59,130,246,0.25);

            --danger: #ef4444;
            --success: #22c55e;

            --divider: rgba(148,163,184,0.30);
            --shadow-strong: 0 24px 70px rgba(15,23,42,0.9);
        }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at 0% 0%, rgba(37,99,235,0.35) 0, transparent 55%),
                radial-gradient(circle at 100% 100%, rgba(79,70,229,0.35) 0, transparent 55%),
                var(--bg-body);
            color: var(--text-main);
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at 15% 0%, rgba(59,130,246,0.18) 0, transparent 55%),
                radial-gradient(circle at 85% 100%, rgba(129,140,248,0.20) 0, transparent 55%);
            opacity: 0.7;
            pointer-events: none;
            z-index: -1;
        }

        .app-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* TOPBAR PREMIUM – giống UC Đoàn */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 30;
            backdrop-filter: blur(20px);
            background: linear-gradient(120deg, rgba(15,23,42,0.97), rgba(15,23,42,0.93));
            border-bottom: 1px solid rgba(148,163,184,0.5);
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
            background: conic-gradient(from 210deg,#0ea5e9,#4f46e5,#22c55e,#a855f7,#0ea5e9);
            padding: 2px;
            box-shadow: 0 18px 40px rgba(37,99,235,0.75);
        }
        .brand-logo-inner {
            width: 100%;
            height: 100%;
            border-radius: inherit;
            background:
                radial-gradient(circle at 0 0, rgba(248,250,252,0.96), transparent 55%),
                radial-gradient(circle at 120% 120%, rgba(59,130,246,0.85), transparent 60%),
                #020617;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.06em;
            color: var(--text-main);
        }
        .brand-text-main {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.12em;
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
            background: radial-gradient(circle at 0 0, rgba(148,163,184,0.20), transparent 55%);
            background-color: rgba(15,23,42,0.90);
            border: 1px solid rgba(148,163,184,0.8);
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
                radial-gradient(circle at 0 0, rgba(59,130,246,0.20), transparent 60%),
                rgba(15,23,42,0.98);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.16s ease;
        }
        .topbar-nav a.logout {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            border-color: transparent;
        }
        .topbar-nav a:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(15,23,42,0.85);
        }

        .container {
            max-width: 1200px;
            margin: 22px auto 40px;
            padding: 0 18px 24px;
        }

        .page-header {
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
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
            border: 1px solid rgba(148,163,184,0.55);
            font-size: 11px;
            color: var(--text-soft);
        }
        .page-chip span {
            color: var(--text-strong);
            font-weight: 600;
        }
        .page-title-block h1 {
            margin: 0;
            font-size: 24px;
            color: var(--text-strong);
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
            white-space: nowrap;
            box-shadow: var(--shadow-strong);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .back-link::before {
            content: "←";
        }

        /* CARD / GRID */
        .card {
            background:
                radial-gradient(circle at 0 0, rgba(37,99,235,0.16), transparent 60%),
                radial-gradient(circle at 120% 120%, rgba(129,140,248,0.18), transparent 60%),
                var(--bg-card);
            border-radius: 18px;
            padding: 18px 20px 16px;
            border: 1px solid var(--border-soft);
            box-shadow: var(--shadow-strong);
            margin-bottom: 18px;
        }
        .card-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 6px;
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

        .row {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }
        .field {
            margin-bottom: 10px;
            flex: 1;
            min-width: 220px;
        }
        label {
            display: block;
            font-size: 12px;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        input[type="text"]{
            width: 100%;
            padding: 9px 11px;
            border-radius: 11px;
            border: 1px solid rgba(148,163,184,0.6);
            font-size: 13px;
            box-sizing: border-box;
            background:
                radial-gradient(circle at 0 0, rgba(148,163,184,0.24), transparent 55%),
                rgba(15,23,42,0.98);
            outline: none;
            color: var(--text-strong);
            transition: all .15s ease;
        }
        input:focus {
            border-color: rgba(59,130,246,0.9);
            box-shadow: 0 0 0 1px rgba(59,130,246,0.35);
            background:
                radial-gradient(circle at 0 0, rgba(59,130,246,0.28), transparent 55%),
                rgba(15,23,42,1);
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        thead {
            background: linear-gradient(to right, rgba(15,23,42,0.98), rgba(30,64,175,0.96));
        }
        th, td {
            padding: 7px 8px;
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
        tbody tr:hover {
            background: rgba(30,64,175,0.35);
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
        }

        /* ALERT */
        .alert {
            border-radius: 12px;
            padding: 10px 12px;
            margin-bottom: 12px;
            font-size: 12px;
            border: 1px solid transparent;
            backdrop-filter: blur(16px);
        }
        .alert-error {
            background:
                radial-gradient(circle at 0 0, rgba(248,113,113,0.40), transparent 55%),
                rgba(24,24,27,0.98);
            border-color: rgba(248,113,113,0.75);
            color: #fecaca;
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
            margin-top: 10px;
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
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 24px 50px rgba(37,99,235,0.9);
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

        /* CLIENT ERROR */
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
        .hint {
            font-size: 12px;
            color: var(--text-soft);
            margin-top: 4px;
        }

        @media (max-width: 960px) {
            .topbar-inner {
                padding-inline: 14px;
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
<div class="app-shell">
    <!-- TOPBAR -->
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
                <?php if (!empty($user)): ?>
                    <div class="user-pill">
                        <span style="opacity:.8;">Đang đăng nhập</span>
                        <strong><?php echo htmlspecialchars($user['Username']); ?></strong>
                    </div>
                <?php endif; ?>

                <nav class="topbar-nav">
                    <a href="index.php?controller=letan&action=index">🏠 Trang lễ tân</a>
                    <a href="index.php?controller=auth&action=logout" class="logout">Đăng xuất</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <!-- PAGE HEADER -->
            <div class="page-header">
                <div class="page-title-block">
                    <div class="page-chip">
                        Hủy đặt phòng · <span>Lễ tân</span>
                    </div>
                    <h1>Hủy đặt phòng</h1>
                    <div class="page-subtitle">
                        Tìm giao dịch → chọn phòng (hoặc toàn bộ) → nhập lý do → xác nhận hủy
                    </div>
                </div>
                <a class="back-link" href="index.php?controller=letan&action=index">
                    Quay lại trang lễ tân
                </a>
            </div>

            <!-- ALERT SERVER-SIDE -->
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

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- FORM DUY NHẤT -->
            <form method="post" id="formHuyPhong">
                <!-- CARD 1: TÌM KIẾM -->
                <div class="card">
                    <div class="card-header">
                        <h2>Tìm kiếm giao dịch</h2>
                        <div class="card-tag">Bước 1 · Tìm kiếm</div>
                    </div>
                    <p class="caption">
                        Nhập <strong>Mã giao dịch</strong> hoặc <strong>CMND/CCCD trưởng đoàn</strong>,
                        sau đó nhấn <strong>Tìm kiếm</strong>.
                    </p>

                    <div class="row" style="align-items:flex-end;">
                        <div class="field">
                            <label>Mã giao dịch / CMND</label>
                            <input type="text"
                                   id="search_keyword"
                                   name="search_keyword"
                                   value="<?php echo htmlspecialchars($searchKeyword); ?>"
                                   placeholder="VD: 1 hoặc 0123456789">
                            <div class="error-msg" id="err_search"></div>
                            <div class="hint">
                                • Mã giao dịch: chuỗi số ngắn (VD: 1, 15, 102).<br>
                                • CMND/CCCD: 9–12 chữ số.
                            </div>
                        </div>

                        <div class="field" style="flex:0 0 auto;display:flex;align-items:flex-end;gap:8px;">
                            <button type="submit" class="btn-primary" name="btn_action" value="search">
                                🔍 Tìm kiếm
                            </button>
                            <button type="submit" class="btn-secondary" name="btn_action" value="back">
                                ← Quay lại lễ tân
                            </button>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: THÔNG TIN GIAO DỊCH -->
                <div class="card">
                    <div class="card-header">
                        <h2>Thông tin giao dịch</h2>
                        <div class="card-tag">Bước 2 · Kiểm tra giao dịch</div>
                    </div>
                    <p class="caption">
                        Thông tin dưới đây được lấy từ giao dịch đã tìm thấy.
                    </p>

                    <?php if ($giaoDich): ?>
                        <div class="row">
                            <div class="field">
                                <label>Mã giao dịch</label>
                                <input type="text"
                                       value="<?php echo htmlspecialchars($giaoDich['MaGiaoDich']); ?>"
                                       disabled>
                            </div>
                            <div class="field">
                                <label>CMND/CCCD trưởng đoàn</label>
                                <input type="text"
                                       value="<?php echo htmlspecialchars($giaoDich['CCCD'] ?? ''); ?>"
                                       disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="field">
                                <label>Tên trưởng đoàn</label>
                                <input type="text"
                                       value="<?php echo htmlspecialchars($giaoDich['TenKH'] ?? ''); ?>"
                                       disabled>
                            </div>
                            <div class="field">
                                <label>Trạng thái giao dịch</label>
                                <input type="text"
                                       value="<?php echo htmlspecialchars($giaoDich['TrangThai'] ?? ''); ?>"
                                       disabled>
                            </div>
                        </div>

                        <!-- hidden để controller đọc lại khi hủy -->
                        <input type="hidden" name="ma_giao_dich"
                               value="<?php echo (int)$giaoDich['MaGiaoDich']; ?>">
                    <?php else: ?>
                        <p class="caption">
                            Chưa có giao dịch nào được chọn. Vui lòng tìm kiếm ở trên.
                        </p>
                    <?php endif; ?>
                </div>

                <!-- CARD 3: DANH SÁCH CHI TIẾT + HỦY -->
                <div class="card">
                    <div class="card-header">
                        <h2>Danh sách chi tiết giao dịch</h2>
                        <div class="card-tag">Bước 3 · Hủy đặt phòng</div>
                    </div>
                    <p class="caption">
                        Chọn các phòng cần hủy hoặc chọn <strong>Hủy toàn bộ giao dịch</strong>.
                    </p>

                    <?php if ($giaoDich && !empty($chiTiet)): ?>
                        <div style="overflow:auto; border-radius:12px; border:1px solid rgba(148,163,184,0.35);">
                            <table>
                                <thead>
                                <tr>
                                    <th>Phòng</th>
                                    <th>Loại phòng</th>
                                    <th>Ngày nhận dự kiến</th>
                                    <th>Ngày trả dự kiến</th>
                                    <th>Trạng thái</th>
                                    <th style="width:70px;text-align:center;">Chọn</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($chiTiet as $ct): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ct['SoPhong']); ?></td>
                                        <td><?php echo htmlspecialchars($ct['LoaiPhong']); ?></td>
                                        <td><?php echo htmlspecialchars($ct['NgayNhanDuKien']); ?></td>
                                        <td><?php echo htmlspecialchars($ct['NgayTraDuKien']); ?></td>
                                        <td>
                                            <?php
                                            $st  = $ct['TrangThai'];
                                            $cls = 'badge-blue';
                                            if ($st === ChiTietGiaoDich::STATUS_CANCELLED || $st === 'Cancelled') {
                                                $bg = '#fee2e2'; $color = '#b91c1c';
                                            } elseif (in_array($st, ['Stayed','CheckedIn'])) {
                                                $bg = '#dcfce7'; $color = '#166534';
                                            } else {
                                                $bg = '#dbeafe'; $color = '#1d4ed8';
                                            }
                                            ?>
                                            <span class="badge"
                                                  style="background:<?php echo $bg; ?>;color:<?php echo $color; ?>;">
                                                <?php echo htmlspecialchars($st); ?>
                                            </span>
                                        </td>
                                        <td style="text-align:center;">
                                            <?php if ($st !== ChiTietGiaoDich::STATUS_CANCELLED && $st !== 'Cancelled'): ?>
                                                <input type="checkbox"
                                                       name="phong_cancel[]"
                                                       class="chk-room"
                                                       value="<?php echo (int)$ct['MaPhong']; ?>"
                                                       style="transform:scale(1.1);cursor:pointer;">
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-top:10px;font-size:13px;">
                            <label style="display:flex;gap:6px;align-items:center;cursor:pointer;">
                                <input type="radio" name="cancel_scope" value="all" id="scope_all" checked>
                                <span>Hủy <strong>toàn bộ</strong> giao dịch (tự chọn tất cả phòng còn hiệu lực)</span>
                            </label>
                            <label style="display:flex;gap:6px;align-items:center;cursor:pointer;margin-top:4px;">
                                <input type="radio" name="cancel_scope" value="partial" id="scope_partial">
                                <span>Chỉ hủy <strong>phòng được chọn</strong></span>
                            </label>
                        </div>

                        <div class="field" style="margin-top:12px;">
                            <label>Lý do hủy (bắt buộc)</label>
                            <input type="text" id="ly_do_huy" name="ly_do_huy"
                                   placeholder="Ví dụ: Khách đổi kế hoạch, đặt nhầm ngày...">
                        </div>

                        <?php if (!$allowCancel): ?>
                            <div class="hint" style="color:#f97373;">
                                * Giao dịch này không đáp ứng điều kiện cho phép hủy (VD: đã check-in, đã quá hạn, đã thanh toán...).
                                Bạn vẫn có thể xem chi tiết nhưng hệ thống sẽ không cho phép lưu khi xác nhận hủy.
                            </div>
                        <?php endif; ?>

                        <div class="btn-row">
                            <button type="reset" class="btn-secondary">
                                🔄 Làm lại
                            </button>
                            <button type="submit" class="btn-primary"
                                    name="btn_action" value="cancel"
                                <?php echo $allowCancel ? '' : 'disabled'; ?>
                                    onclick="return confirm('Bạn có chắc chắn muốn hủy theo lựa chọn hiện tại không?');">
                                Xác nhận hủy
                            </button>
                        </div>
                    <?php else: ?>
                        <p class="caption">
                            Chưa có dữ liệu chi tiết. Vui lòng tìm kiếm giao dịch trước.
                        </p>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form        = document.getElementById('formHuyPhong');
    const searchInput = document.getElementById('search_keyword');
    const errSearch   = document.getElementById('err_search');

    const scopeAll     = document.getElementById('scope_all');
    const scopePartial = document.getElementById('scope_partial');

    function getRoomCheckboxes() {
        return document.querySelectorAll('.chk-room');
    }

    // --- VALIDATE Ô TÌM KIẾM ---
    function clearSearchError() {
        if (!searchInput || !errSearch) return;
        searchInput.classList.remove('input-error');
        errSearch.textContent = '';
    }

    function setSearchError(msg) {
        if (!searchInput || !errSearch) return;
        searchInput.classList.add('input-error');
        errSearch.textContent = msg;
    }

    function validateSearch() {
        if (!searchInput) return true;
        const v = searchInput.value.trim();
        clearSearchError();

        if (!v) {
            setSearchError('Vui lòng nhập mã giao dịch hoặc CMND/CCCD để tìm kiếm.');
            return false;
        }
        if (!/^\d+$/.test(v)) {
            setSearchError('Mã giao dịch/CMND chỉ được phép chứa chữ số.');
            return false;
        }
        if (v.length > 12) {
            setSearchError('Dữ liệu không hợp lệ (tối đa 12 chữ số).');
            return false;
        }
        return true;
    }

    if (searchInput) {
        searchInput.addEventListener('blur', validateSearch);
        searchInput.addEventListener('input', function () {
            validateSearch();
        });
    }

    // --- HÀNH VI RADIO "HỦY TOÀN BỘ" / "HỦY MỘT PHẦN" ---
    function applyCancelScope() {
        const cbs = getRoomCheckboxes();
        if (!cbs.length) return;

        if (scopeAll && scopeAll.checked) {
            cbs.forEach(function (cb) {
                cb.checked = true;
            });
        } else if (scopePartial && scopePartial.checked) {
            cbs.forEach(function (cb) {
                cb.checked = false;
            });
        }
    }

    if (scopeAll) scopeAll.addEventListener('change', applyCancelScope);
    if (scopePartial) scopePartial.addEventListener('change', applyCancelScope);

    // Gọi lần đầu sau khi trang load (mặc định scope_all checked → tick hết)
    applyCancelScope();

    // --- SUBMIT FORM ---
    if (form) {
        form.addEventListener('submit', function (e) {
            const submitter = e.submitter || {};
            const action    = submitter.value || '';

            // Nếu bấm TÌM KIẾM thì chỉ validate ô search
            if (action === 'search') {
                if (!validateSearch()) {
                    e.preventDefault();
                    return;
                }
            }

            // Nếu bấm XÁC NHẬN HỦY thì kiểm tra thêm:
            if (action === 'cancel') {
                // Nếu đang ở chế độ partial mà không chọn phòng nào
                if (scopePartial && scopePartial.checked) {
                    const cbs = getRoomCheckboxes();
                    let anyChecked = false;
                    cbs.forEach(function (cb) {
                        if (cb.checked) anyChecked = true;
                    });
                    if (!anyChecked) {
                        alert('Bạn đang chọn hủy một phần. Vui lòng tích ít nhất một phòng cần hủy.');
                        e.preventDefault();
                        return;
                    }
                }
                // Lý do hủy bắt buộc
                const lyDo = document.getElementById('ly_do_huy');
                if (lyDo && lyDo.value.trim() === '') {
                    alert('Vui lòng nhập lý do hủy.');
                    lyDo.focus();
                    e.preventDefault();
                    return;
                }
            }
        });
    }
});
</script>
</body>
</html>