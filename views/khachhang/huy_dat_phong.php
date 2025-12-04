<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$user    = $user    ?? ($_SESSION['user'] ?? null);
$errors  = $errors  ?? [];
$success = $success ?? null;

$bookings = $bookings ?? [];

if (!function_exists('e')) {
    function e($v){ return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Hủy đặt phòng - ABC Resort</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
    background: radial-gradient(circle at top, #020617, #020617 40%, #0f172a 100%);
    color:#e5e7eb;
}
.topbar{
    background:rgba(15,23,42,0.96);
    padding:12px 24px;
    display:flex;align-items:center;justify-content:space-between;
    border-bottom:1px solid #1f2937;
}
.brand{font-size:14px;font-weight:700;text-transform:uppercase;}
.role{font-size:13px;opacity:.8;}
.topbar a{font-size:13px;color:#93c5fd;text-decoration:none;margin-left:12px;}
.topbar a:hover{text-decoration:underline;}

.container-page{max-width:1200px;margin:24px auto 40px;padding:0 16px;}
.page-title{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:16px;}
.page-title h1{margin:0;font-size:26px;color:#f9fafb;}
.back-link{color:#93c5fd;text-decoration:none;font-size:13px;}
.back-link:hover{text-decoration:underline;}

.card-shell{
    background:linear-gradient(135deg, rgba(15,23,42,0.98), rgba(15,23,42,0.95));
    padding:16px 18px;
    border-radius:18px;
    border:1px solid rgba(148,163,184,0.35);
    margin-bottom:16px;
}
.table{background:rgba(15,23,42,0.6);color:#e5e7eb;border-radius:12px;overflow:hidden;}
.table thead th{background:#0f172a;color:#e5e7eb;font-size:13px;}
.badge-status{
    background:#0ea5e9;padding:4px 10px;border-radius:999px;font-size:12px;color:white;
}

/* === NÚT HỦY ĐỎ ĐÔ === */
.btn-danger-modern{
    border:none;border-radius:999px;padding:7px 14px;font-size:14px;font-weight:600;
    background:#8B0000;color:white;cursor:pointer;
}
.btn-danger-modern:hover{background:#6e0000;}
</style>
</head>

<body>

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
            <h1>Hủy đặt phòng Online</h1>
            <span>Xem danh sách phòng đang đặt và hủy nếu cần</span>
        </div>
    </div>

    <?php if(!empty($errors)): ?>
        <div class="alert-danger" style="padding:10px;border-radius:10px;">
            <ul>
                <?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
    <?php endif; ?>

    <!-- LIST BOOKINGS -->
    <div class="card-shell">
        <h2>Danh sách đặt phòng</h2>
        <p style="font-size:13px;color:#9ca3af;">Chỉ hiển thị phòng chưa quá hạn check-in.</p>

        <?php if(empty($bookings)): ?>
            <p style="color:#94a3b8;">Bạn chưa có phòng nào đang đặt.</p>
        <?php else: ?>
            <table class="table table-bordered table-dark">
                <thead>
                    <tr>
                        <th>Mã GD</th>
                        <th>Ngày nhận</th>
                        <th>Ngày trả</th>
                        <th>Mã phòng</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($bookings as $b): ?>
                    <tr>
                        <td>#<?= (int)$b['MaGiaoDich'] ?></td>
                        <td><?= !empty($b['NgayNhanDuKien']) ? date("d/m/Y", strtotime($b['NgayNhanDuKien'])) : "---" ?></td>
                        <td><?= !empty($b['NgayTraDuKien']) ? date("d/m/Y", strtotime($b['NgayTraDuKien'])) : "---" ?></td>
                        <td>#<?= (int)$b['MaPhong'] ?></td>
                        <td><span class="badge-status"><?= e($b['TrangThai'] ?? '') ?></span></td>

                        <td>
                            <form method="post" onsubmit="return confirm('Xác nhận hủy phòng này?');">
                                <!-- tên field phải khớp với controller -->
                                <input type="hidden" name="ma_ctgd"  value="<?= (int)$b['MaCTGD'] ?>">
                                <input type="hidden" name="ma_phong" value="<?= (int)$b['MaPhong'] ?>">
                                <button class="btn-danger-modern" name="btn_action" value="cancel_room">
                                    Hủy phòng
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
