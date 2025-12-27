<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản đoàn - Premium UI</title>
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

        .topbar-nav a.logout {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            border-color: transparent;
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
        }

    </style>
</head>

<body>
<div class="app-shell">
    <header class="topbar">
        <div class="topbar-inner">
            <div class="brand-block">
                <div class="brand-logo"><div class="brand-logo-inner">AB</div></div>
                <div>
                    <div class="brand-text-main">ABC RESORT</div>
                    <div class="brand-text-sub">Bảng điều khiển · Lễ tân</div>
                </div>
            </div>

            <div class="topbar-right">
                <?php if (!empty($_SESSION['user'])): ?>
                    <div class="user-pill">
                        <span style="opacity:.8;">Đang đăng nhập</span>
                        <strong><?php echo htmlspecialchars($_SESSION['user']['Username']); ?></strong>
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
                        <!-- PHẦN 2: CSS bổ sung + Header + Card Trưởng đoàn -->

            <style>
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
                    border: 1px solid rgba(148,163,184,0.5);
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

                /* GRID & CARD */
                .grid {
                    display: grid;
                    grid-template-columns: minmax(0, 2.2fr) minmax(0, 3fr);
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
                input[type="email"] {
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
                input:focus {
                    border-color: rgba(59,130,246,0.9);
                    box-shadow: 0 0 0 1px rgba(59,130,246,0.35);
                    background:
                        radial-gradient(circle at 0 0, rgba(59,130,246,0.25), transparent 55%),
                        rgba(15,23,42,1);
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
                .btn-danger::before {
                    content: "✕";
                    font-size: 11px;
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
                .pill span { font-weight: 600; }

                /* TABLE THÀNH VIÊN */
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
                    padding: 6px 8px;
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
                tbody tr:hover {
                    background: rgba(30,64,175,0.3);
                }

                .result-card {
                    margin-top: 20px;
                    background:
                        radial-gradient(circle at 0 0, rgba(37,99,235,0.18), transparent 55%),
                        rgba(15,23,42,0.98);
                    border-radius: 18px;
                    padding: 16px 18px;
                    border: 1px solid var(--border-soft);
                    box-shadow: var(--shadow-strong);
                }

                @media (max-width: 960px) {
                    .grid {
                        grid-template-columns: 1fr;
                    }
                }
            </style>

            <?php
            // Đảm bảo các biến luôn tồn tại (controller có thể override)
            $form            = $form            ?? [];
            $errors          = $errors          ?? [];
            $success         = $success         ?? null;
            $createdAccounts = $createdAccounts ?? [];
            ?>

            <div class="page-header">
                <div class="page-title-block">
                    <div class="page-chip">
                        Đăng ký tài khoản đoàn · <span>Lễ tân</span>
                    </div>
                    <h1>Đăng ký tài khoản cho đoàn khách</h1>
                    <div class="page-subtitle">
                        Nhập thông tin trưởng đoàn & danh sách thành viên. Hệ thống sẽ sinh username + mật khẩu tạm,
                        đảm bảo không trùng tài khoản cũ và giữ nguyên logic xử lý trong controller.
                    </div>
                </div>
                <a class="back-link" href="index.php?controller=letan&action=index">
                    Quay lại trang lễ tân
                </a>
            </div>

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

            <!-- Lỗi phía client -->
            <div id="clientErrors" class="alert alert-error" style="display:none;">
                <strong>Có lỗi xảy ra:</strong>
                <ul id="clientErrorsList"></ul>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="post" id="formDangKyDoan">
                <div class="grid">
                    <!-- CỘT TRÁI: TRƯỞNG ĐOÀN + SỐ THÀNH VIÊN -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Thông tin trưởng đoàn</h2>
                            <div class="card-tag">Bước 1 · Trưởng đoàn</div>
                        </div>
                        <p class="caption">
                            Các trường có dấu <strong style="color:#f97373;">*</strong> là bắt buộc.
                            Nếu thiếu hoặc sai định dạng, ô sẽ được tô đỏ (tương tự luồng cũ, chỉ nâng cấp giao diện).
                        </p>
                        <div class="card-divider"></div>

                        <div class="field">
                            <label>
                                Họ tên trưởng đoàn <span style="color:#f97373;">*</span>
                                <span class="note">VD: Nguyễn Văn A</span>
                            </label>
                            <input type="text"
                                   id="leader_name"
                                   name="leader_name"
                                   value="<?php echo htmlspecialchars($form['leader_name'] ?? ''); ?>"
                                   placeholder="Họ tên đầy đủ trưởng đoàn">
                            <div class="error-msg" id="err_leader_name"></div>
                        </div>

                        <div class="row">
                            <div class="field">
                                <label>
                                    CMND / CCCD <span style="color:#f97373;">*</span>
                                    <span class="note">Chỉ 9–12 chữ số</span>
                                </label>
                                <input type="text"
                                       id="leader_cccd"
                                       name="leader_cccd"
                                       value="<?php echo htmlspecialchars($form['leader_cccd'] ?? ''); ?>"
                                       placeholder="Ví dụ: 0123456789">
                                <div class="error-msg" id="err_leader_cccd"></div>
                            </div>
                            <div class="field">
                                <label>
                                    Số điện thoại <span style="color:#f97373;">*</span>
                                    <span class="note">Bắt đầu bằng 0</span>
                                </label>
                                <input type="text"
                                       id="leader_sdt"
                                       name="leader_sdt"
                                       value="<?php echo htmlspecialchars($form['leader_sdt'] ?? ''); ?>"
                                       placeholder="09xxxxxxxx">
                                <div class="error-msg" id="err_leader_sdt"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="field">
                                <label>
                                    Email <span style="color:#f97373;">*</span>
                                    <span class="note">Dùng để gửi thông tin tài khoản</span>
                                </label>
                                <input type="email"
                                       id="leader_email"
                                       name="leader_email"
                                       value="<?php echo htmlspecialchars($form['leader_email'] ?? ''); ?>"
                                       placeholder="truongdoan@email.com">
                                <div class="error-msg" id="err_leader_email"></div>
                            </div>
                            <div class="field">
                                <label>
                                    Địa chỉ
                                    <span class="note">Không bắt buộc</span>
                                </label>
                                <input type="text"
                                       id="leader_diachi"
                                       name="leader_diachi"
                                       value="<?php echo htmlspecialchars($form['leader_diachi'] ?? ''); ?>"
                                       placeholder="Địa chỉ liên hệ trưởng đoàn">
                            </div>
                        </div>

                        <div class="field">
                            <label>
                                Số lượng thành viên <span style="color:#f97373;">*</span>
                                <span class="note">Phải khớp số dòng có họ tên</span>
                            </label>
                            <input type="number"
                                   id="so_nguoi"
                                   name="so_nguoi"
                                   min="1"
                                   max="200"
                                   value="<?php echo htmlspecialchars($form['so_nguoi'] ?? '3'); ?>">
                            <div class="error-msg" id="err_so_nguoi"></div>
                        </div>

                        <div class="btn-row">
                            <button type="button" class="btn-danger" id="btnCancel" name="btn_cancel" value="1">
                                Hủy đăng ký
                            </button>
                            <div class="btn-group-right">
                                <button type="reset" class="btn-secondary">
                                    🔄 Làm lại
                                </button>
                                <button type="submit" name="btn_register" value="1" class="btn-primary">
                                    Đăng ký tài khoản đoàn →
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Hết cột trái, cột phải (danh sách thành viên) sẽ ở PHẦN 3 -->                    <!-- CỘT PHẢI: DANH SÁCH THÀNH VIÊN -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Danh sách thành viên</h2>
                            <div class="card-tag">Bước 2 · Thành viên</div>
                        </div>
                        <p class="caption">
                            Nhập thông tin các thành viên đi cùng đoàn.  
                            Nếu chỉ có 1 người (trưởng đoàn), phần này có thể để trống.
                        </p>
                        <div class="card-divider"></div>

                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Họ tên</th>
                                    <th>CCCD</th>
                                    <th>SĐT</th>
                                    <th>Email</th>
                                    <th>Địa chỉ</th> 
                                </tr>
                            </thead>
                            <tbody id="membersBody">
                                <?php for ($i = 1; $i <= (int)$form['so_nguoi']; $i++): 
    $tv = $form['members'][$i] ?? [];
?>
<tr>
    <td><?= $i ?></td>

    <td>
        <input type="text" class="form-control"
               name="members[<?= $i ?>][TenKH]"
               value="<?= htmlspecialchars($tv['TenKH'] ?? '') ?>">
    </td>

    <td>
        <input type="text" class="form-control"
               name="members[<?= $i ?>][CCCD]"
               value="<?= htmlspecialchars($tv['CCCD'] ?? '') ?>">
    </td>

    <td>
        <input type="text" class="form-control"
               name="members[<?= $i ?>][SDT]"
               value="<?= htmlspecialchars($tv['SDT'] ?? '') ?>">
    </td>

    <td>
        <input type="email" class="form-control"
               name="members[<?= $i ?>][Email]"
               value="<?= htmlspecialchars($tv['Email'] ?? '') ?>">
    </td>

    <td>
        <input type="text" class="form-control"
               name="members[<?= $i ?>][DiaChi]"
               placeholder="Địa chỉ"
               value="<?= htmlspecialchars($tv['DiaChi'] ?? '') ?>">
    </td>
</tr>
<?php endfor; ?>
                            </tbody>
                        </table>

                        <p class="caption" style="margin-top:8px;">
                            * Hệ thống sẽ tự sinh username và mật khẩu cho từng thành viên.  
                            * CCCD trùng với khách cũ → hệ thống sẽ **giữ thông tin cũ**, không tạo mới.
                        </p>
                    </div>
                </div> <!-- end grid -->
            </form>
            <?php if (!empty($createdAccounts)): ?>
    <div class="card" style="margin-top:20px;">
        <div class="card-header">
            <h2>Kết quả tạo tài khoản</h2>
            <div class="card-tag">Hoàn tất</div>
        </div>

        <p class="caption" style="margin-bottom:10px;">
            Hệ thống đã tạo xong đoàn và tài khoản đăng nhập cho các thành viên sau:
        </p>

        <table>
            <thead>
                <tr>
                    <th>Họ tên</th>
                    <th>CCCD</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Vai trò</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($createdAccounts as $acc): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($acc['hoTen']); ?></td>
                        <td><?php echo htmlspecialchars($acc['cmnd']); ?></td>
                        <td><strong><?php echo htmlspecialchars($acc['username']); ?></strong></td>
                        <td style="color:#22c55e;"><?php echo htmlspecialchars($acc['password']); ?></td>
                        <td><?php echo htmlspecialchars($acc['vaiTro']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top:14px; display:flex; justify-content:flex-end;">
            <a href="index.php?controller=letan&action=index"
               class="btn-secondary">
                ← Quay lại trang lễ tân
            </a>
        </div>
    </div>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ============================
       LEADER FIELDS
       ============================ */

    const form = document.getElementById('formDangKyDoan');
    const clientErrors = document.getElementById('clientErrors');
    const clientErrorsList = document.getElementById('clientErrorsList');

    const fields = {
        leader_name:  document.getElementById('leader_name'),
        leader_cccd:  document.getElementById('leader_cccd'),
        leader_sdt:   document.getElementById('leader_sdt'),
        leader_email: document.getElementById('leader_email'),
        so_nguoi:     document.getElementById('so_nguoi'),
    };

    const errsDom = {
        leader_name:  document.getElementById('err_leader_name'),
        leader_cccd:  document.getElementById('err_leader_cccd'),
        leader_sdt:   document.getElementById('err_leader_sdt'),
        leader_email: document.getElementById('err_leader_email'),
        so_nguoi:     document.getElementById('err_so_nguoi'),
    };

    const touched = {
        leader_name:false, leader_cccd:false, leader_sdt:false,
        leader_email:false, so_nguoi:false
    };

    function setError(name, msg) {
        fields[name].classList.add('input-error');
        errsDom[name].textContent = msg;
    }
    function clearError(name) {
        fields[name].classList.remove('input-error');
        errsDom[name].textContent = '';
    }

    function validateField(name, fromSubmit=false) {
        if (!fromSubmit && !touched[name]) {
            clearError(name);
            return;
        }
        const v = (fields[name].value || '').trim();
        clearError(name);

        switch(name) {
            case 'leader_name':
                if (!v) return setError(name,'Vui lòng nhập họ tên.');
                if (!/^[A-Za-zÀ-ỹ\s]{2,60}$/u.test(v))
                    return setError(name,'Họ tên chỉ gồm chữ, 2–60 ký tự.');
                break;

            case 'leader_cccd':
                if (!v) return setError(name,'Vui lòng nhập CCCD.');
                if (!/^\d{9,12}$/.test(v))
                    return setError(name,'CCCD phải 9–12 chữ số.');
                break;

            case 'leader_sdt':
                if (!v) return setError(name,'Vui lòng nhập số điện thoại.');
                if (!/^0\d{8,10}$/.test(v))
                    return setError(name,'Số điện thoại sai định dạng.');
                break;

            case 'leader_email':
                if (!v) return setError(name,'Vui lòng nhập email.');
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v))
                    return setError(name,'Email sai định dạng.');
                break;

            case 'so_nguoi':
                const n = parseInt(v || "0");
                if (!n || n <= 0) return setError(name,'Số lượng thành viên phải > 0.');
                break;
        }
    }

    Object.keys(fields).forEach(name => {
        fields[name].addEventListener('blur', () => { touched[name]=true; validateField(name,false); });
        fields[name].addEventListener('input',()=>{ if(touched[name]) validateField(name,false); });
    });

    /* ============================
       MEMBER VALIDATION REAL-TIME
       ============================ */

    const membersBody = document.getElementById('membersBody');

    function addMemberRowEvents(tr, index) {
        const nameInput  = tr.querySelector('input[name="members['+index+'][TenKH]"]');
        const cccdInput  = tr.querySelector('input[name="members['+index+'][CCCD]"]');
        const sdtInput   = tr.querySelector('input[name="members['+index+'][SDT]"]');
        const emailInput = tr.querySelector('input[name="members['+index+'][Email]"]');

        function validateMemberInput(input, type, label) {
            const v = input.value.trim();
            input.classList.remove("input-error");
            input.nextElementSibling && input.nextElementSibling.remove();

            let msg = "";
            if (type === "name") {
                if (!v) msg = "Họ tên không được trống.";
                else if (!/^[A-Za-zÀ-ỹ\s]{2,60}$/u.test(v)) msg = "Tên sai định dạng.";
            }
            if (type === "cccd" && v) {
                if (!/^\d{9,12}$/.test(v)) msg = "CCCD phải 9–12 số.";
            }
            if (type === "sdt" && v) {
                if (!/^0\d{8,10}$/.test(v)) msg = "SĐT sai định dạng.";
            }
            if (type === "email" && v) {
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) msg = "Email sai định dạng.";
            }

            if (msg !== "") {
                input.classList.add("input-error");

                const err = document.createElement("div");
                err.classList.add("error-msg");
                err.textContent = msg;
                input.parentElement.appendChild(err);
            }
        }

        nameInput.addEventListener('blur',()=>validateMemberInput(nameInput,"name"));
        cccdInput.addEventListener('blur',()=>validateMemberInput(cccdInput,"cccd"));
        sdtInput.addEventListener('blur',()=>validateMemberInput(sdtInput,"sdt"));
        emailInput.addEventListener('blur',()=>validateMemberInput(emailInput,"email"));
    }

    function rerenderMemberRows() {
        const count = parseInt(fields['so_nguoi'].value || "1");

        while (membersBody.children.length < count) {
            const idx = membersBody.children.length + 1;
            const tr = document.createElement('tr');
            tr.innerHTML = `
    <td>${idx}</td>
    <td>
        <input type="text" name="members[${idx}][TenKH]" placeholder="Tên thành viên">
    </td>
    <td>
        <input type="text" name="members[${idx}][CCCD]" placeholder="9–12 số">
    </td>
    <td>
        <input type="text" name="members[${idx}][SDT]" placeholder="09xxxxxxxx">
    </td>
    <td>
        <input type="email" name="members[${idx}][Email]" placeholder="email@domain.com">
    </td>
    <td>
        <input type="text" name="members[${idx}][DiaChi]" placeholder="Địa chỉ">
    </td>
`;
            membersBody.appendChild(tr);
            addMemberRowEvents(tr, idx);
        }

        while (membersBody.children.length > count) {
            membersBody.removeChild(membersBody.lastElementChild);
        }
    }

    fields['so_nguoi'].addEventListener('change', () => {
        validateField("so_nguoi", true);
        rerenderMemberRows();
    });

    // Gắn validate realtime cho các dòng load từ server
    [...membersBody.children].forEach((tr,i)=> addMemberRowEvents(tr, i+1));

    /* ============================
       FINAL SUBMIT VALIDATION
       ============================ */

    function validateMembersOnSubmit() {
        const rows = membersBody.querySelectorAll("tr");
        let filled = 0;
        let errs = [];

        rows.forEach((tr, i) => {
            const idx = i+1;
            const name = tr.querySelector('input[name="members['+idx+'][TenKH]"]').value.trim();
            const cccd = tr.querySelector('input[name="members['+idx+'][CCCD]"]').value.trim();
            const sdt  = tr.querySelector('input[name="members['+idx+'][SDT]"]').value.trim();
            const email= tr.querySelector('input[name="members['+idx+'][Email]"]').value.trim();

            if (!name && !cccd && !sdt && !email) return;
            filled++;

            if (!name) errs.push(`Thành viên ${idx}: Họ tên không được trống.`);
            if (cccd && !/^\d{9,12}$/.test(cccd)) errs.push(`Thành viên ${idx}: CCCD sai định dạng.`);
            if (sdt  && !/^0\d{8,10}$/.test(sdt))  errs.push(`Thành viên ${idx}: SĐT sai định dạng.`);
            if (email&& !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))
                errs.push(`Thành viên ${idx}: Email sai định dạng.`);
        });

        const required = parseInt(fields['so_nguoi'].value || "0");
        if (filled !== required)
            errs.push(`Số lượng thành viên không khớp (${filled}/${required}).`);

        return errs;
    }

    form.addEventListener('submit', function (e) {
        clientErrors.style.display = 'none';
        clientErrorsList.innerHTML = '';

        Object.keys(fields).forEach(name => validateField(name,true));

        let errs = [];
        Object.keys(errsDom).forEach(name=>{
            if (errsDom[name].textContent) errs.push(errsDom[name].textContent);
        });

        errs = errs.concat(validateMembersOnSubmit());

        if (errs.length > 0) {
            e.preventDefault();
            errs.forEach(msg=>{
                const li=document.createElement('li');
                li.textContent=msg;
                clientErrorsList.appendChild(li);
            });
            clientErrors.style.display='block';
            window.scrollTo({top:0,behavior:'smooth'});
        }
    });

});
</script>