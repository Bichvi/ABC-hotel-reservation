<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nếu controller có truyền $user thì dùng, không thì fallback từ session/Auth
$user    = $user    ?? ($_SESSION['user'] ?? (class_exists('Auth') ? Auth::user() : null));
$errors  = $errors  ?? [];
$success = $success ?? null;

// Form dữ liệu ban đầu (giữ lại khi validate lỗi)
$form = $form ?? [
    'TenKH'   => $user['TenKH']  ?? '',
    'SDT'     => $user['SDT']    ?? '',
    'Email'   => $user['Email']  ?? '',
    'DiaChi'  => $user['DiaChi'] ?? '',
    'GhiChu'  => $user['GhiChu'] ?? '',
];

// Helper escape
if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật thông tin cá nhân - ABC Resort</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ======= BASE LAYOUT – GIỐNG TEMPLATE CÁC MÀN HÌNH KHÁC ======= */
        body{
            margin:0;
            font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
            background: radial-gradient(circle at top, #020617, #020617 40%, #0f172a 100%);
            color:#e5e7eb;
        }
        .topbar{
            background:rgba(15,23,42,0.96);
            backdrop-filter: blur(10px);
            color:#e5e7eb;
            padding:12px 24px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            border-bottom:1px solid #1f2937;
        }
        .brand{
            font-weight:700;
            letter-spacing:.08em;
            font-size:14px;
            text-transform:uppercase;
        }
        .role{
            font-size:13px;
            opacity:.8;
        }
        .topbar a{
            font-size:13px;
            color:#93c5fd;
            text-decoration:none;
            margin-left:12px;
        }
        .topbar a:hover{text-decoration:underline;}

        /* Tăng size Xin chào giống các màn hình khác */
        .topbar span {
            font-size:16px !important;
            font-weight:500;
        }
        .topbar span strong {
            font-size:16px !important;
            font-weight:700;
            color:#e5e7eb !important;
        }

        .container-page{
            max-width:1200px;
            margin:24px auto 40px;
            padding:0 16px;
        }
        .page-title{
            display:flex;
            align-items:baseline;
            justify-content:space-between;
            gap:8px;
            margin-bottom:16px;
        }
        .page-title h1{
            margin:0;
            font-size:24px;
            color:#f9fafb;
        }
        .page-title span{
            font-size:13px;
            color:#9ca3af;
        }
        .back-link{
            font-size:13px;
            text-decoration:none;
            color:#93c5fd;
            display:inline-flex;
            align-items:center;
            gap:4px;
        }
        .back-link:hover{text-decoration:underline;}

        .card-shell{
            background: linear-gradient(135deg, rgba(15,23,42,0.98), rgba(15,23,42,0.95));
            border-radius:18px;
            padding:16px 18px;
            border:1px solid rgba(148,163,184,0.35);
            box-shadow:0 18px 40px rgba(0,0,0,0.65);
            margin-bottom:16px;
        }
        .card-shell h2{
            margin:0 0 6px;
            font-size:18px;
            color:#e5e7eb;
        }
        .card-shell p.caption{
            margin:0 0 12px;
            font-size:13px;
            color:#9ca3af;
        }

        label{
            display:block;
            font-size:13px;
            color:#cbd5f5;
            margin-bottom:4px;
        }
        input[type="text"],
        input[type="email"]{
            width:100%;
            padding:7px 10px;
            border-radius:999px;
            border:1px solid #4b5563;
            font-size:14px;
            box-sizing:border-box;
            background:#020617;
            color:#e5e7eb;
            outline:none;
            transition:all .15s ease;
        }
        input:focus{
            border-color:#3b82f6;
            box-shadow:0 0 0 1px rgba(59,130,246,.4);
            background:#020617;
        }

        .row-flex{
            display:flex;
            flex-wrap:wrap;
            gap:12px;
        }
        .field{
            flex:1;
            min-width:220px;
        }

        .alert{
            border-radius:12px;
            padding:10px 12px;
            margin-bottom:12px;
            font-size:13px;
        }
        .alert-error{
            background:rgba(239,68,68,0.12);
            border:1px solid rgba(248,113,113,0.6);
            color:#fecaca;
        }
        .alert-success{
            background:rgba(16,185,129,0.15);
            border:1px solid rgba(45,212,191,0.7);
            color:#bbf7d0;
        }

        .btn-primary-modern{
            border:none;
            border-radius:999px;
            padding:9px 18px;
            font-size:14px;
            font-weight:500;
            color:#0b1120;
            background:linear-gradient(135deg,#22c55e,#a3e635);
            cursor:pointer;
            display:inline-flex;
            align-items:center;
            gap:6px;
            box-shadow:0 12px 24px rgba(22,163,74,.4);
            transition:transform .12s ease,box-shadow .12s ease,filter .12s ease;
        }
        .btn-primary-modern:hover{
            transform:translateY(-1px);
            box-shadow:0 18px 38px rgba(22,163,74,.55);
            filter:brightness(1.03);
        }
        .btn-outline{
            border-radius:999px;
            border:1px solid #4b5563;
            background:transparent;
            color:#e5e7eb;
            padding:8px 14px;
            font-size:13px;
            cursor:pointer;
        }
        .btn-outline:hover{
            background:#020617;
        }
        .btn-row{
            margin-top:10px;
            display:flex;
            justify-content:flex-end;
            gap:10px;
            flex-wrap:wrap;
            align-items:center;
        }
    </style>
</head>
<body>

<!-- ========================== TOP BAR ========================== -->
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · KHÁCH HÀNG</div>
        <div class="role">Đặt phòng Online</div>
    </div>
    <div>
        <?php if ($user): ?>
            <span style="font-size:13px;margin-right:10px;">
                Xin chào, <strong><?= e($user['Username'] ?? 'Khách') ?></strong>
            </span>
        <?php endif; ?>
        <a href="index.php" style="color:#93c5fd;text-decoration:none;margin-left:12px;">
            Trang chủ
        </a>
        <a href="index.php?controller=khachhang&action=dashboard" style="color:#93c5fd;text-decoration:none;margin-left:12px;">
            Trang khách hàng
        </a>
        <a href="index.php?controller=auth&action=logout" style="color:#93c5fd;text-decoration:none;margin-left:12px;">
            Đăng xuất
        </a>
    </div>
</div>

<div class="container-page">
    <div class="page-title">
        <div>
            <h1>Cập nhật thông tin cá nhân</h1>
            <span>Thay đổi thông tin hồ sơ → Lưu → Nhận xác nhận</span>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <strong>Có lỗi xảy ra:</strong>
            <ul style="margin-top:6px;padding-left:18px;">
                <?php foreach ($errors as $e): ?>
                    <li><?= e($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?= e($success) ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="card-shell">
            <h2>Thông tin hồ sơ cá nhân</h2>
            <p class="caption">
                Vui lòng cập nhật chính xác thông tin liên hệ để nhận thông báo đặt phòng & dịch vụ.
            </p>

            <div class="row-flex mb-2">
                <div class="field">
                    <label>Họ tên *</label>
                    <input type="text" name="TenKH" value="<?= e($form['TenKH']) ?>" required>
                </div>
                <div class="field">
                    <label>Số điện thoại *</label>
                    <input type="text" name="SDT" value="<?= e($form['SDT']) ?>" required>
                </div>
            </div>

            <div class="row-flex mb-2">
                <div class="field">
                    <label>Email *</label>
                    <input type="email" name="Email" value="<?= e($form['Email']) ?>" required>
                </div>
                <div class="field">
                    <label>Địa chỉ</label>
                    <input type="text" name="DiaChi" value="<?= e($form['DiaChi']) ?>">
                </div>
            </div>

            <div class="btn-row">
                <button type="reset" class="btn-outline">Nhập lại</button>
                <button type="submit"
                        name="btn_action"
                        value="update"
                        class="btn-primary-modern"
                        onclick="return confirm('Xác nhận cập nhật hồ sơ cá nhân?');">
                    Lưu thay đổi
                </button>
            </div>
        </div>
    </form>

</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const phone = document.querySelector("input[name='SDT']");
    const email = document.querySelector("input[name='Email']");

    if (phone) {
        phone.addEventListener("input", () => {
            if (phone.value !== "" && !/^(0|\+84)[0-9]{8,10}$/.test(phone.value)) {
                phone.style.borderColor = "red";
            } else {
                phone.style.borderColor = "#4b5563";
            }
        });
    }

    if (email) {
        email.addEventListener("input", () => {
            if (email.value !== "" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                email.style.borderColor = "red";
            } else {
                email.style.borderColor = "#4b5563";
            }
        });
    }
});
</script>

</body>
</html>
