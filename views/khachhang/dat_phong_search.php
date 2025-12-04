<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nếu controller có truyền $user thì dùng, không thì fallback từ session/Auth
$user = $user ?? ($_SESSION['user'] ?? (class_exists('Auth') ? Auth::user() : null));

// Đảm bảo $rooms luôn là mảng
$rooms = $rooms ?? [];

// Helper tránh lỗi htmlspecialchars(null)
if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tìm kiếm phòng - ABC Resort</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #020617, #0f172a);
            min-height: 100vh;
            color: #e5e7eb;
        }
        .search-container {
            max-width: 700px;
            margin: 30px auto;
            position: relative;
        }
        .search-input {
            width: 100%;
            background: rgba(15,23,42,0.85);
            border: 1px solid rgba(148,163,184,0.3);
            padding: 12px 18px;
            border-radius: 30px;
            color: #e5e7eb;
            font-size: 1rem;
        }
        .search-input::placeholder {
            color: #94a3b8;
        }
        .btn-search {
            position: absolute;
            top: 50%;
            right: 6px;
            transform: translateY(-50%);
            border-radius: 50px;
            padding: 9px 18px;
            background: linear-gradient(135deg, #0ea5e9, #38bdf8);
            border: none;
            font-weight: 600;
            color: white;
            transition: 0.2s;
        }
        .btn-search:hover {
            transform: translateY(-50%) scale(1.05);
            box-shadow: 0 0 10px rgba(56,189,248,0.7);
        }

        /* FILTER BOX */
        .filter-box {
            display: none;
            background: rgba(15,23,42,0.95);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(148,163,184,0.3);
            padding: 18px;
            border-radius: 16px;
            margin-top: 10px;
        }
        .search-container:hover .filter-box {
            display: block;
        }
        .filter-box label {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-bottom: 4px;
        }
        .filter-box .form-control, .filter-box .form-select {
            background: rgba(30,41,59,0.9);
            color: #e5e7eb;
            border: 1px solid rgba(148,163,184,0.3);
        }
        .filter-box .form-control:focus, .filter-box .form-select:focus {
            box-shadow: 0 0 0 2px rgba(56,189,248,0.4);
        }

        /* ROOM CARD */
        .room-card {
            border-radius: 18px;
            background: radial-gradient(circle at top left, rgba(56,189,248,0.20), rgba(15,23,42,0.96));
            border: 1px solid rgba(148,163,184,0.3);
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
            transition: 0.25s;
            overflow: hidden;
        }
        .room-card:hover {
            transform: translateY(-6px);
            border-color: rgba(56,189,248,0.8);
        }
        .room-card img {
            height: 180px;
            width: 100%;
            object-fit: cover;
        }
        .btn-book {
            border-radius: 12px;
            background: linear-gradient(135deg,#22c55e,#a3e635);
            color: black;
            font-weight: 600;
            padding: 10px;
            transition: 0.2s;
        }
        .btn-book:hover {
            transform: scale(1.05);
            box-shadow: 0 0 12px rgba(22,163,74,.55);
        }
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
            color:#93c5fd ;
            text-decoration:none;
            margin-left:12px;
        }

        .topbar span {
            font-size:16px !important;
            font-weight:500;
        }

        .topbar span strong {
            font-size:16px !important;
            font-weight:700;
            color:#e5e7eb !important;
        }

        .topbar a:hover{
            text-decoration:underline;
        }
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

<div class="container py-4">
    <h2 class="text-center fw-bold mb-4">Tìm kiếm phòng trống</h2>

        <div class="filter-box">
            <div class="row g-3">
                <div class="col-md-3">
                    <label>Loại phòng</label>
                    <select name="loai_phong" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="Standard">Standard</option>
                        <option value="Deluxe">Deluxe</option>
                        <option value="Suite">Suite</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>View phòng</label>
                    <select name="view_phong" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="Biển">Biển</option>
                        <option value="Thành phố">Thành phố</option>
                        <option value="Vườn">Vườn</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Số khách</label>
                    <input type="number" min="1" name="so_khach" class="form-control" placeholder="VD: 2">
                </div>

                <div class="col-md-3">
                    <label>Giá mong muốn (VNĐ)</label>
                    <input type="number" name="gia_goi_y" class="form-control" placeholder="1.000.000">
                </div>
            </div>
        </div>
    </form>

    <hr class="text-secondary">

    <h4 class="mb-3">Kết quả tìm kiếm:</h4>

    <div class="row g-3">
        <?php if (!empty($rooms)): ?>
            <?php foreach ($rooms as $room): ?>
                <div class="col-md-4">
                    <div class="room-card p-3">
                        <img src="uploads/phong/<?= $room['HinhAnh'] ?>" alt="room_img">
                        <h5 class="mt-2 fw-bold">Phòng <?= $room['SoPhong'] ?> — <?= $room['LoaiPhong'] ?></h5>
                        <p class="mb-1">View: <?= $room['ViewPhong'] ?></p>
                        <p class="fw-bold text-info"><?= number_format($room['Gia']) ?> VNĐ / đêm</p>

                        <form method="GET" action="index.php">
                            <input type="hidden" name="controller" value="khachhang">
                            <input type="hidden" name="action" value="datPhongOnline2">
                            <input type="hidden" name="room_id" value="<?= $room['MaPhong'] ?>">
                            <button class="btn-book w-100 mt-2">Đặt phòng</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <p class="text-danger mt-3 ms-2">Không có phòng nào phù hợp.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
