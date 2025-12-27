<?php
if (session_status() === PHP_SESSION_NONE) session_start();

/* ================== DATA ================== */
$invoice = $invoice ?? ($_SESSION['invoice'] ?? []);
$user    = $_SESSION['user'] ?? [];

$booking = $invoice['booking'] ?? [];
$rooms   = $invoice['rooms'] ?? [];
$tong    = (float)($invoice['tong_tien'] ?? 0);
$maGD    = $invoice['maGD'] ?? '---';

if (!function_exists('e')) {
    function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Hóa đơn đặt phòng - ABC Resort</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
/* ===== GIỮ NGUYÊN CSS TEMPLATE GỐC ===== */
body{
    margin:0;
    background: radial-gradient(circle at top, #020617, #020617 40%, #0f172a 100%);
    font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
    color:#e5e7eb;
}
.card-invoice{
    max-width:650px;
    margin:40px auto;
    background:linear-gradient(135deg, rgba(15,23,42,0.98), rgba(15,23,42,0.95));
    padding:24px 28px;
    border-radius:18px;
    border:1px solid rgba(148,163,184,0.45);
    box-shadow:0 18px 40px rgba(0,0,0,0.65);
}
.title-success{
    font-size:26px;
    font-weight:700;
    color:#4ade80;
    text-align:center;
}
.btn-primary-modern{
    border:none;
    border-radius:999px;
    padding:10px 24px;
    font-size:14px;
    font-weight:500;
    color:#0b1120;
    background:linear-gradient(135deg,#22c55e,#a3e635);
    display:inline-block;
    margin-top:18px;
    box-shadow:0 12px 24px rgba(22,163,74,.4);
    transition:.15s;
    text-decoration:none !important;
}
.btn-primary-modern:hover{
    transform:translateY(-2px);
    box-shadow:0 18px 38px rgba(22,163,74,.55);
}
.line{
    border-top:1px dashed rgba(148,163,184,0.6);
    margin:16px 0;
}
.label{
    color:#9ca3af;
    font-size:14px;
}
.value{
    color:#f9fafb;
    font-size:16px;
    font-weight:600;
}
</style>
</head>

<body>

<div class="card-invoice">

    <h2 class="title-success">Đặt phòng thành công!</h2>

    <p class="text-center mt-2">
        Cảm ơn <strong><?= e($user['Username'] ?? 'Khách') ?></strong>
        đã đặt phòng tại <strong>ABC Resort</strong>.
    </p>

    <div class="line"></div>

    <h4 class="text-center fw-bold mb-3">Thông tin hóa đơn</h4>

    <p>
        <span class="label">Mã giao dịch:</span>
        <span class="value">#<?= e($maGD) ?></span>
    </p>

    <!-- ===== MULTI ROOMS ===== -->
    <p class="label">Phòng đã đặt:</p>
    <?php foreach ($rooms as $r): ?>
        <p class="value">
            • Phòng <?= e($r['SoPhong']) ?>
            — <?= e($r['LoaiPhong']) ?>
            (<?= number_format($r['Gia']) ?> đ / đêm)
        </p>
    <?php endforeach; ?>

    <div class="line"></div>

    <p>
        <span class="label">Ngày nhận:</span>
        <span class="value"><?= e($booking['ngay_nhan'] ?? '') ?></span>
    </p>

    <p>
        <span class="label">Ngày trả:</span>
        <span class="value"><?= e($booking['ngay_tra'] ?? '') ?></span>
    </p>

    <p>
        <span class="label">Số người:</span>
        <span class="value"><?= e($booking['so_nguoi'] ?? 1) ?></span>
    </p>

    <p>
        <span class="label">Tổng tiền:</span>
        <span class="value" style="color:#4ade80;">
            <?= number_format($tong, 0, ',', '.') ?> đ
        </span>
    </p>

    <div class="text-center">
        <a href="index.php?controller=khachhang&action=dashboard"
           class="btn-primary-modern">
            Về trang khách hàng
        </a>
    </div>

</div>

</body>
</html>
