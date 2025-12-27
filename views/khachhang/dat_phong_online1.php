<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $user ?? ($_SESSION['user'] ?? null);
if (!function_exists('e')) {
    function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

/* ====== LẤY THÔNG TIN KHÁCH HÀNG ====== */
$KH = $kh ?? $user ?? [];

$hoTen = $KH['TenKH'] ?? '';
$cccd  = $KH['CCCD']   ?? '';
$sdt   = $KH['SDT']    ?? '';
$email = $KH['Email']  ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đặt phòng - ABC Resort</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
    background: radial-gradient(circle at top, #020617, #020617 40%, #0f172a 100%);
    color:#e5e7eb;
}

/* TOPBAR */
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

/* CONTAINER */
.container-page{
    max-width:800px;
    margin:24px auto;
    padding:0 16px;
}

.card-shell{
    background:linear-gradient(135deg, rgba(15,23,42,0.98), rgba(15,23,42,0.95));
    border:1px solid rgba(148,163,184,0.35);
    border-radius:18px;
    padding:18px;
    margin-bottom:18px;
}

/* FORM */
label{font-size:14px;margin-bottom:6px;display:block;}
input, select{
    width:100%;
    border-radius:999px;
    padding:9px 14px;
    background:#020617;
    border:1px solid #4b5563;
    color:#e5e7eb;
    font-size:14px;
    box-sizing:border-box;
}
input:focus, select:focus{
    border-color:#3b82f6;
    box-shadow:0 0 0 1px rgba(59,130,246,.4);
    outline:none;
}

/* DATE INPUT CUSTOM */
.date-input {
    cursor:pointer;
}
.date-input::-webkit-calendar-picker-indicator {
    filter: invert(1);
}

/* BUTTON */
.btn-primary-modern{
    background:linear-gradient(135deg,#22c55e,#a3e635);
    border:none;
    border-radius:999px;
    padding:10px 24px;
    font-weight:bold;
    color:black;
    box-shadow:0 8px 18px rgba(22,163,74,.4);
    cursor:pointer;
    transition:0.2s;
}
.btn-primary-modern:hover{transform:translateY(-2px);}

.room-preview img{width:100%;border-radius:16px;}
</style>
</head>

<body>

<!-- TOPBAR -->
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · KHÁCH HÀNG</div>
        <div class="role">Đặt phòng</div>
    </div>
    <div>
        Xin chào, <strong><?= e($KH['Username'] ?? '') ?></strong>
        <a href="index.php">Trang chủ</a>
        <a href="index.php?controller=khachhang&action=dashboard">Trang khách hàng</a>
        <a href="index.php?controller=auth&action=logout">Đăng xuất</a>
    </div>
</div>

<div class="container-page">

    <!-- THÔNG TIN PHÒNG -->
    <div class="card-shell room-preview">
        <h2>Thông tin phòng đã chọn</h2>
        <img src="uploads/phong/<?= e($room['HinhAnh']) ?>" alt="">
        <h3 class="mt-2">Phòng <?= e($room['SoPhong']) ?> — <?= e($room['LoaiPhong']) ?></h3>
        <p>View: <?= e($room['ViewPhong']) ?></p>
        <p>
            <i class="fa-solid fa-users"></i>
            Sức chứa tối đa:
            <strong><?= (int)$room['SoKhachToiDa'] ?> khách</strong>
        </p>
        <p>Giá: <strong style="color:#4ade80;"><?= number_format($room['Gia']) ?> đ / đêm</strong></p>
    </div>

    <!-- FORM -->
    <form method="POST" action="index.php?controller=khachhang&action=reviewBooking">
        <input type="hidden" name="room_id" value="<?= e($room['MaPhong']) ?>">

        <div class="card-shell">
            <h2>Thông tin khách hàng</h2>

            <div class="row g-3">

                <div class="col-md-6">
                    <label>Họ tên *</label>
                    <input required name="booking[ten_khach]" value="<?= e($hoTen) ?>" readonly>
                </div>

                <div class="col-md-6">
                    <label>CCCD *</label>
                    <input required name="booking[cccd]" value="<?= e($cccd) ?>" readonly>
                </div>

                <div class="col-md-6">
                    <label>Số điện thoại *</label>
                    <input required name="booking[sdt]" value="<?= e($sdt) ?>" readonly>
                </div>

                <div class="col-md-6">
                    <label>Email *</label>
                    <input required name="booking[email]" value="<?= e($email) ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label>Số người *</label>
                   <input type="number" min="1" max="<?= (int)$maxGuests ?>" value="1" required name="booking[so_nguoi]" id="so_nguoi">
                </div>

                <div class="col-md-4">
                    <label>Ngày nhận *</label>
                    <input type="date" required class="date-input" name="booking[ngay_nhan]" id="ngay_nhan" onkeydown="return false;">
                </div>

                <div class="col-md-4">
                    <label>Ngày trả *</label>
                    <input type="date" required class="date-input" name="booking[ngay_tra]" id="ngay_tra" onkeydown="return false;">
                </div>

                <div class="col-md-6">
                    <label>Mã khuyến mãi</label>
                    <select name="booking[ma_km]" class="form-select">
                        <option value="">-- Không áp dụng --</option>
                        <?php foreach ($khuyenMaiList as $km): ?>
                            <option value="<?= $km['MaKhuyenMai'] ?>">
                                <?= e($km['TenChuongTrinh']) ?> 
                                (−<?= number_format($km['MucUuDai']) ?><?= $km['LoaiUuDai']==='PERCENT'?'%':'đ' ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <!-- QUAY LẠI -->
                <a href="javascript:history.back()"
                style="
                        color:#93c5fd;
                        font-size:14px;
                        font-weight:600;
                        text-decoration:none;
                ">
                    ← Quay lại
                </a>

                <!-- TIẾP TỤC -->
                <button class="btn-primary-modern">
                    Tiếp tục
                </button>
            </div>
    </form>
</div>

<script>
    // CHẶN CHỌN NGÀY HÔM QUA
    const today = new Date();
    today.setDate(today.getDate() + 1);
    const todayStr = today.toISOString().split("T")[0];

    const ngayNhan = document.getElementById("ngay_nhan");
    const ngayTra  = document.getElementById("ngay_tra");

    ngayNhan.min = todayStr;
    ngayTra.min  = todayStr;

    // DAY VALIDATION
    ngayNhan.addEventListener("change", function () {
        ngayTra.min = this.value;

        if (ngayTra.value && ngayTra.value <= ngayNhan.value) {
            alert("Ngày trả phải sau ngày nhận!");
            ngayTra.value = "";
        }
    });

    ngayTra.addEventListener("change", function () {
        if (ngayTra.value <= ngayNhan.value) {
            alert("Ngày trả phải sau ngày nhận!");
            ngayTra.value = "";
        }
    });
</script>

</body>
</html>
