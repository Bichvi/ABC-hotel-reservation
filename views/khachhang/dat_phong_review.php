<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user    = $_SESSION['user'] ?? null;
$room    = $room ?? [];
$booking = $booking ?? [];

// Helper
if (!function_exists('e')) {
    function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

// Tính tổng tiền hiển thị
$giaPhong = (float)$room['Gia'];
$soDem    = max(1, (strtotime($booking['ngay_tra']) - strtotime($booking['ngay_nhan'])) / (60*60*24));
$tong_tien = $giaPhong * $soDem;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Xác nhận đặt phòng - ABC Resort</title>
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
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:1px solid #1f2937;
}
.brand{font-weight:700;font-size:14px;text-transform:uppercase;}
.role{font-size:13px;opacity:.8;}
.topbar a{color:#93c5fd;font-size:13px;margin-left:10px;text-decoration:none;}
.topbar a:hover{text-decoration:underline;}

.container-page{max-width:900px;margin:24px auto;padding:0 16px;}

.card-shell{
    background:linear-gradient(135deg, rgba(15,23,42,0.98), rgba(15,23,42,0.95));
    border-radius:18px;
    padding:18px;
    border:1px solid rgba(148,163,184,0.35);
    box-shadow:0 18px 40px rgba(0,0,0,0.65);
}

.summary-box{
    background:#020617;
    border-radius:12px;
    padding:10px 12px;
    border:1px dashed rgba(148,163,184,0.8);
    font-size:13px;
    color:#e5e7eb;
}

.summary-row{display:flex;justify-content:space-between;margin-bottom:4px;}
.summary-total{
    margin-top:6px;border-top:1px dashed rgba(148,163,184,0.8);
    padding-top:6px;display:flex;justify-content:space-between;
    font-weight:600;color:#bbf7d0;
}

.btn-primary-modern{
    border:none;border-radius:999px;padding:10px 24px;font-size:14px;
    font-weight:500;color:#0b1120;background:linear-gradient(135deg,#22c55e,#a3e635);
    display:inline-flex;align-items:center;gap:6px;cursor:pointer;
    box-shadow:0 12px 24px rgba(22,163,74,.4);
}
.btn-primary-modern:hover{transform:translateY(-1px);}

.btn-outline{
    border-radius:999px;border:1px solid #4b5563;background:transparent;color:#e5e7eb;
    padding:9px 18px;font-size:13px;
}
.btn-row{margin-top:16px;display:flex;justify-content:flex-end;gap:12px;}
</style>
</head>

<body>

<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · KHÁCH HÀNG</div>
        <div class="role">Xem lại & xác nhận đơn</div>
    </div>
    <div>
        Xin chào, <strong><?= e($user['Username'] ?? '') ?></strong>
        <a href="index.php">Trang chủ</a>
        <a href="index.php?controller=khachhang&action=dashboard">Trang khách hàng</a>
        <a href="index.php?controller=auth&action=logout">Đăng xuất</a>
    </div>
</div>

<div class="container-page">
    <div class="card-shell">
        <h2>Tóm tắt thông tin</h2>
        <p class="caption">Kiểm tra lại thông tin phòng & khách trước khi xác nhận</p>

        <div class="summary-box">
            <div class="summary-row"><span>Phòng đã chọn</span><strong>Phòng <?= e($room['SoPhong']) ?> · <?= e($room['LoaiPhong']) ?></strong></div>
            <div class="summary-row"><span>View</span><span><?= e($room['ViewPhong']) ?></span></div>
            <div class="summary-row"><span>Số khách</span><strong><?= e($booking['so_nguoi']) ?> người</strong></div>
            <div class="summary-row"><span>Ngày nhận</span><strong><?= e($booking['ngay_nhan']) ?></strong></div>
            <div class="summary-row"><span>Ngày trả</span><strong><?= e($booking['ngay_tra']) ?></strong></div>
            <div class="summary-total"><span>Tổng tiền</span><strong><?= number_format($tong_tien, 0, ',', '.') ?> đ</strong></div>
        </div>

        <form method="POST" action="index.php?controller=khachhang&action=confirmBooking">
            <!-- Main values -->
            <input type="hidden" name="room_id" value="<?= e($room['MaPhong']) ?>">
            <input type="hidden" name="tong_tien" value="<?= $tong_tien ?>">

            <!-- Booking info -->
            <?php foreach($booking as $k => $v): ?>
                <input type="hidden" name="booking[<?= e($k) ?>]" value="<?= e($v) ?>">
            <?php endforeach; ?>

            <div class="btn-row">
                <button type="button" onclick="history.back()" class="btn-outline">Quay lại</button>
                <button type="submit" name="btn_action" value="confirm_booking" class="btn-primary-modern">
                    Xác nhận đặt phòng
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
