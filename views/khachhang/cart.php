<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user  = $_SESSION['user'] ?? null;
$rooms = $rooms ?? [];
$kh    = $kh ?? [];
$khuyenMaiList = $khuyenMaiList ?? [];

if (!function_exists('e')) {
    function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

/* ===== THÔNG TIN KHÁCH HÀNG TỪ DB ===== */
$hoTen = $kh['TenKH'] ?? '';
$cccd  = $kh['CCCD']  ?? '';
$sdt   = $kh['SDT']   ?? '';
$email = $kh['Email'] ?? '';

/* ===== TỔNG SỨC CHỨA ===== */
$totalMaxGuests = 0;
foreach ($rooms as $r) {
    $totalMaxGuests += (int)$r['SoKhachToiDa'];
}

/* ===== GIỮ SỐ NGƯỜI CŨ ===== */
$oldBooking = $_SESSION['old_booking'] ?? [];
$soNguoi = $oldBooking['so_nguoi'] ?? 1;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Giỏ phòng - ABC Resort</title>
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
label{font-size:14px;margin-bottom:6px;display:block;}
input, select{
    width:100%;
    border-radius:999px;
    padding:9px 14px;
    background:#020617;
    border:1px solid #4b5563;
    color:#e5e7eb;
    font-size:14px;
}
input:focus, select:focus{
    border-color:#3b82f6;
    box-shadow:0 0 0 1px rgba(59,130,246,.4);
    outline:none;
}
.date-input{cursor:pointer;}
.date-input::-webkit-calendar-picker-indicator{filter:invert(1);}

.btn-primary-modern{
    background:linear-gradient(135deg,#22c55e,#a3e635);
    border:none;
    border-radius:999px;
    padding:10px 24px;
    font-weight:bold;
    color:black;
    box-shadow:0 8px 18px rgba(22,163,74,.4);
    cursor:pointer;
}
.room-preview img{width:100%;border-radius:16px;}
</style>
</head>

<body>

<!-- TOPBAR -->
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · KHÁCH HÀNG</div>
        <div class="role">Giỏ phòng</div>
    </div>
    <div>
        Xin chào, <strong><?= e($user['Username'] ?? '') ?></strong>
        <a href="index.php">Trang chủ</a>
        <a href="index.php?controller=khachhang&action=dashboard">Trang khách hàng</a>
        <a href="index.php?controller=auth&action=logout">Đăng xuất</a>
    </div>
</div>

<div class="container-page">

<form method="POST" action="index.php?controller=khachhang&action=reviewBookingMulti">

<!-- ================= PHÒNG ĐÃ CHỌN ================= -->
<div class="card-shell">
    <h2>Thông tin phòng đã chọn</h2>

    <?php foreach ($rooms as $room): ?>
        <div class="card-shell room-preview">
            <img src="uploads/phong/<?= e($room['HinhAnh']) ?>" alt="">

            <h3 class="mt-2">
                Phòng <?= e($room['SoPhong']) ?> — <?= e($room['LoaiPhong']) ?>
            </h3>

            <p>View: <?= e($room['ViewPhong']) ?></p>

            <p>
                Sức chứa tối đa:
                <strong><?= (int)$room['SoKhachToiDa'] ?> khách</strong>
            </p>

            <p>
                Giá:
                <strong style="color:#4ade80;">
                    <?= number_format($room['Gia']) ?> đ / đêm
                </strong>
            </p>

            <!-- NÚT BỎ PHÒNG -->
            <a href="index.php?controller=khachhang&action=removeFromCart&room_id=<?= $room['MaPhong'] ?>"
               style="display:inline-block;margin-top:6px;color:#f87171;font-size:13px;font-weight:600;text-decoration:none;"
               onclick="return confirm('Bỏ phòng này khỏi giỏ?')">
                Bỏ phòng này
            </a>

            <input type="hidden" name="rooms[]" value="<?= $room['MaPhong'] ?>">
        </div>
    <?php endforeach; ?>
</div>

<!-- ================= THÔNG TIN KHÁCH HÀNG ================= -->
<div class="card-shell">
    <h2>Thông tin khách hàng</h2>

    <!-- gửi data -->
    <input type="hidden" name="booking[ten_khach]" value="<?= e($hoTen) ?>">
    <input type="hidden" name="booking[cccd]" value="<?= e($cccd) ?>">
    <input type="hidden" name="booking[sdt]" value="<?= e($sdt) ?>">
    <input type="hidden" name="booking[email]" value="<?= e($email) ?>">

    <div class="row g-3">
        <div class="col-md-6">
            <label>Họ tên *</label>
            <input value="<?= e($hoTen) ?>" readonly>
        </div>

        <div class="col-md-6">
            <label>CCCD *</label>
            <input value="<?= e($cccd) ?>" readonly>
        </div>

        <div class="col-md-6">
            <label>Số điện thoại *</label>
            <input value="<?= e($sdt) ?>" readonly>
        </div>

        <div class="col-md-6">
            <label>Email *</label>
            <input value="<?= e($email) ?>" readonly>
        </div>

        <div class="col-md-4">
            <label>Số người *</label>
            <input type="number"
                   min="1"
                   max="<?= $totalMaxGuests ?>"
                   name="booking[so_nguoi]"
                   value="<?= e($soNguoi) ?>"
                   required>
            <small>Tối đa <?= $totalMaxGuests ?> khách</small>
        </div>

        <div class="col-md-4">
            <label>Ngày nhận *</label>
            <input
                type="date"
                class="date-input"
                name="booking[ngay_nhan]"
                id="ngay_nhan"
                required
            >
        </div>

        <div class="col-md-4">
            <label>Ngày trả *</label>
            <input
                type="date"
                class="date-input"
                name="booking[ngay_tra]"
                id="ngay_tra"
                required
            >
        </div>

        <div class="col-md-6">
            <label>Mã khuyến mãi</label>
            <select name="booking[ma_km]" style="background:#020617;color:#e5e7eb;">
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
    const soNguoi = document.querySelector('[name="booking[so_nguoi]"]');
    const maxGuests = <?= $totalMaxGuests ?>;
    soNguoi.addEventListener('input', function(){
        if (+this.value > maxGuests) {
            alert(`Số khách tối đa là ${maxGuests}`);
            this.value = maxGuests;
        }
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        const ngayNhan = document.getElementById("ngay_nhan");
        const ngayTra  = document.getElementById("ngay_tra");

        // ===== NGÀY TỐI THIỂU = NGÀY MAI =====
        const today = new Date();
        today.setDate(today.getDate() + 1);
        const minDate = today.toISOString().split("T")[0];

        ngayNhan.min = minDate;
        ngayTra.min  = minDate;

        // ===== KHI CHỌN NGÀY NHẬN =====
        ngayNhan.addEventListener("change", function () {

            if (!this.value) return;

            // ép ngày trả >= ngày nhận + 1
            const nextDay = new Date(this.value);
            nextDay.setDate(nextDay.getDate() + 1);
            const nextDayStr = nextDay.toISOString().split("T")[0];

            ngayTra.min = nextDayStr;

            // nếu ngày trả đang <= ngày nhận → reset
            if (ngayTra.value && ngayTra.value < nextDayStr) {
                ngayTra.value = "";
            }
        });

        // ===== KHI CHỌN NGÀY TRẢ =====
        ngayTra.addEventListener("change", function () {

            if (!ngayNhan.value) {
                alert("Vui lòng chọn ngày nhận trước!");
                this.value = "";
                return;
            }

            if (this.value <= ngayNhan.value) {
                alert("Ngày trả phải sau ngày nhận ít nhất 1 ngày!");
                this.value = "";
            }
        });

    });
</script>
</body>
</html>
