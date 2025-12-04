<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nếu controller có truyền $user thì dùng, không thì fallback từ session/Auth
$user   = $user   ?? ($_SESSION['user'] ?? (class_exists('Auth') ? Auth::user() : null));
$errors = $errors ?? [];
$success= $success ?? null;

$filter = $filter ?? [
    'loai_phong' => '',
    'view_phong' => '',
    'so_khach'   => '',
    'gia_goi_y'  => '',
];

// Danh sách phòng sau khi lọc (controller gán)
$rooms = $rooms ?? [];

// Phòng được chọn để đặt (1 phòng / lần)
$selectedRoom = $selectedRoom ?? null;

// Form thông tin khách (controller gán lại khi có lỗi để giữ input)
$bookingForm = $bookingForm ?? [
    'ten_khach' => '',
    'cccd'      => '',
    'sdt'       => '',
    'email'     => '',
    'so_nguoi'  => '',
];

// Tóm tắt đơn đặt phòng (controller tính)
$summary = $summary ?? null;

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
    <title>Đặt phòng Online - ABC Resort</title>
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
            backdrop-filter: blur(10px);
            color:#e5e7eb;
            padding:12px 24px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            border-bottom:1px solid #1f2937;
        }
        .brand{font-weight:700;letter-spacing:.08em;font-size:14px;text-transform:uppercase;}
        .role{font-size:13px;opacity:.8;}
        .topbar a{
            font-size:13px;
            color:#93c5fd ;
            text-decoration:none;
            margin-left:12px;
        }
        /* Tăng size cho khu vực Xin chào, khach1 */
        .topbar span {
            font-size:16px !important;      /* tăng chữ Xin chào */
            font-weight:500;
        }

        .topbar span strong {
            font-size:16px !important;      /* tên khách lớn hơn */
            font-weight:700;
            color:#e5e7eb !important;       /* màu xanh giống menu */
        }
        .topbar a:hover{text-decoration:underline;}
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
        .page-title h1{margin:0;font-size:24px;color:#f9fafb;}
        .page-title span{font-size:13px;color:#9ca3af;}
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
        .card-shell h2{margin:0 0 6px;font-size:17px;color:#e5e7eb;}
        .card-shell p.caption{margin:0 0 12px;font-size:13px;color:#9ca3af;}

        label{display:block;font-size:13px;color:#cbd5f5;margin-bottom:4px;}
        input[type="number"],
        input[type="text"],
        input[type="email"],
        select{
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
        input:focus,select:focus{
            border-color:#3b82f6;
            box-shadow:0 0 0 1px rgba(59,130,246,.4);
            background:#020617;
        }
        .row-flex{display:flex;flex-wrap:wrap;gap:12px;}
        .field{flex:1;min-width:180px;}

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

        .rooms-grid{
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
            gap:14px;
        }
        .room-card{
            position:relative;
            border-radius:16px;
            overflow:hidden;
            border:1px solid rgba(148,163,184,0.35);
            background: radial-gradient(circle at top left, rgba(56,189,248,0.18), rgba(15,23,42,0.98));
            box-shadow:0 16px 34px rgba(0,0,0,0.6);
            display:flex;
            flex-direction:column;
            min-height:260px;
        }
        .room-img-wrap{
            height:150px;
            overflow:hidden;
            position:relative;
        }
        .room-img-wrap img{
            width:100%;
            height:100%;
            object-fit:cover;
            display:block;
        }
        .room-tag{
            position:absolute;
            top:10px;
            left:10px;
            background:rgba(15,23,42,0.85);
            border-radius:999px;
            padding:4px 10px;
            font-size:11px;
            color:#e5e7eb;
            border:1px solid rgba(148,163,184,0.7);
        }
        .room-body{
            padding:10px 12px 12px;
            flex:1;
            display:flex;
            flex-direction:column;
            gap:4px;
        }
        .room-title{
            font-size:15px;
            font-weight:600;
            color:#f9fafb;
            margin:0;
        }
        .room-meta{
            font-size:12px;
            color:#9ca3af;
        }
        .room-price{
            font-size:16px;
            font-weight:700;
            color:#4ade80;
        }
        .room-footer{
            margin-top:auto;
            display:flex;
            justify-content:space-between;
            align-items:center;
            font-size:12px;
            color:#9ca3af;
        }
        .badge-small{
            border-radius:999px;
            border:1px solid rgba(148,163,184,0.6);
            padding:2px 8px;
            font-size:11px;
        }

        .summary-box{
            background:#020617;
            border-radius:12px;
            padding:10px 12px;
            border:1px dashed rgba(148,163,184,0.8);
            font-size:13px;
            color:#e5e7eb;
        }
        .summary-row{
            display:flex;
            justify-content:space-between;
            margin-bottom:4px;
        }
        .summary-total{
            margin-top:6px;
            border-top:1px dashed rgba(148,163,184,0.8);
            padding-top:6px;
            display:flex;
            justify-content:space-between;
            font-weight:600;
            color:#bbf7d0;
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

<div class="container-page">
    <div class="page-title">
        <div>
            <h1>Đặt phòng Online</h1>
            <span>Lọc phòng theo nhu cầu → Chọn phòng → Nhập thông tin → Xác nhận</span>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <strong>Có lỗi xảy ra:</strong>
            <ul class="mb-0 mt-1" style="padding-left:18px;">
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

    <!-- A. BỘ LỌC PHÒNG (BƯỚC 1) -->
    <form method="get" class="mb-3" action="index.php">
        <input type="hidden" name="controller" value="khachhang">
        <input type="hidden" name="action" value="searchRooms">
        <div class="card-shell">
            <h2>Bộ lọc tìm phòng</h2>
            <p class="caption">
                Chọn loại phòng, view, số khách và khoảng giá gợi ý. Hệ thống sẽ hiển thị các phòng trống có giá xấp xỉ giá bạn nhập.
            </p>

            <div class="row-flex">
                <div class="field">
                    <label>Loại phòng</label>
                    <select name="loai_phong">
                        <option value="">-- Tất cả --</option>
                        <option value="Standard" <?= ($filter['loai_phong'] ?? '') === 'Standard' ? 'selected' : '' ?>>Standard</option>
                        <option value="Superior" <?= ($filter['loai_phong'] ?? '') === 'Superior' ? 'selected' : '' ?>>Superior</option>
                        <option value="Deluxe"   <?= ($filter['loai_phong'] ?? '') === 'Deluxe'   ? 'selected' : '' ?>>Deluxe</option>
                        <option value="Suite"    <?= ($filter['loai_phong'] ?? '') === 'Suite'    ? 'selected' : '' ?>>Suite</option>
                        <option value="VIP"      <?= ($filter['loai_phong'] ?? '') === 'VIP'      ? 'selected' : '' ?>>VIP</option>
                    </select>
                </div>

                <div class="field">
                    <label>View phòng</label>
                    <select name="view_phong">
                        <option value="">-- Tất cả --</option>
                        <option value="Biển"        <?= ($filter['view_phong'] ?? '') === 'Biển' ? 'selected' : '' ?>>Hướng biển</option>
                        <option value="Thành phố"   <?= ($filter['view_phong'] ?? '') === 'Thành phố' ? 'selected' : '' ?>>Thành phố</option>
                        <option value="Vườn"        <?= ($filter['view_phong'] ?? '') === 'Vườn' ? 'selected' : '' ?>>Vườn</option>
                    </select>
                </div>

                <div class="field">
                    <label>Số khách dự kiến</label>
                    <input type="number" name="so_khach" min="1"
                           value="<?= e($filter['so_khach'] ?? '') ?>"
                           placeholder="VD: 2">
                </div>

                <div class="field">
                    <label>Giá gợi ý (VNĐ / đêm)</label>
                    <input type="number" name="gia_goi_y" min="0" step="50000"
                           value="<?= e($filter['gia_goi_y'] ?? '') ?>"
                           placeholder="VD: 1.000.000">
                </div>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn-outline" name="btn_action" value="reset">
                    Nhập lại
                </button>
                <button type="submit" class="btn-primary-modern" name="btn_action" value="filter">
                    Tìm phòng phù hợp
                </button>
            </div>
        </div>
    </form>

    <!-- B. DANH SÁCH PHÒNG KẾT QUẢ (BƯỚC 2) -->
    <?php if (!empty($rooms)): ?>
        <div class="card-shell">
            <h2>Phòng trống phù hợp</h2>
            <p class="caption">
                Nhấp <strong>Đặt phòng</strong> ở phòng bạn muốn, form điền thông tin khách sẽ xuất hiện phía dưới.
            </p>

            <div class="rooms-grid">
                <?php foreach ($rooms as $p): ?>
                    <?php
                    $gia   = (float)($p['Gia'] ?? 0);
                    $img   = $p['HinhAnh'] ?? '';
                    $imgSrc = $img !== ''
                        ? 'uploads/phong/' . e($img)   // chỉnh đúng path upload trên dự án của bạn
                        : 'https://via.placeholder.com/400x250?text=Room';
                    ?>
                    <div class="room-card">
                        <div class="room-img-wrap">
                            <img src="<?= $imgSrc ?>" alt="Phòng <?= e($p['SoPhong'] ?? '') ?>">
                            <div class="room-tag">
                                <?= e($p['LoaiPhong'] ?? 'Phòng') ?> · Tối đa <?= (int)($p['SoKhachToiDa'] ?? 1) ?> khách
                            </div>
                        </div>
                        <div class="room-body">
                            <h3 class="room-title">Phòng <?= e($p['SoPhong'] ?? '') ?></h3>
                            <div class="room-meta">
                                Giường: <?= e($p['LoaiGiuong'] ?? '') ?> · View: <?= e($p['ViewPhong'] ?? '') ?><br>
                                Diện tích: <?= e($p['DienTich'] ?? '') ?> m² · Tình trạng: <?= e($p['TinhTrangPhong'] ?? '') ?>
                            </div>
                            <div class="room-price mt-1">
                                <?= number_format($gia, 0, ',', '.') ?> đ / đêm
                            </div>
                            <div class="room-footer mt-2">
                                <span class="badge-small">
                                    Mã phòng: #<?= (int)($p['MaPhong'] ?? 0) ?>
                                </span>
                                <!-- Nút CHỌN PHÒNG (submit form riêng) -->
                                <form method="post" style="margin:0;">
                                    <!-- Giữ lại filter để controller refill -->
                                    <input type="hidden" name="loai_phong" value="<?= e($filter['loai_phong'] ?? '') ?>">
                                    <input type="hidden" name="view_phong" value="<?= e($filter['view_phong'] ?? '') ?>">
                                    <input type="hidden" name="so_khach"   value="<?= e($filter['so_khach'] ?? '') ?>">
                                    <input type="hidden" name="gia_goi_y"  value="<?= e($filter['gia_goi_y'] ?? '') ?>">
                                    

                                    <input type="hidden" name="room_id" value="<?= (int)$p['MaPhong'] ?>">
                                    <button type="submit"
                                            class="btn-primary-modern"
                                            name="btn_action"
                                            value="choose_room">
                                        Đặt phòng
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    <?php endif; ?>

    <!-- C. FORM THÔNG TIN KHÁCH (BƯỚC 3) -->
    <?php if ($selectedRoom): ?>
        <form method="post" class="mb-3">
            <!-- Giữ lại filter & room để controller dùng -->
            <input type="hidden" name="loai_phong" value="<?= e($filter['loai_phong'] ?? '') ?>">
            <input type="hidden" name="view_phong" value="<?= e($filter['view_phong'] ?? '') ?>">
            <input type="hidden" name="so_khach"   value="<?= e($filter['so_khach'] ?? '') ?>">
            <input type="hidden" name="gia_goi_y"  value="<?= e($filter['gia_goi_y'] ?? '') ?>">
            <input type="hidden" name="room_id"    value="<?= (int)$selectedRoom['MaPhong'] ?>">

            <div class="card-shell">
                <h2>Thông tin khách cho phòng <?= e($selectedRoom['SoPhong'] ?? '') ?></h2>
                <p class="caption">
                    Vui lòng nhập thông tin người ở phòng này. Hệ thống sẽ dùng thông tin này để lập giao dịch & gửi xác nhận.
                </p>

                <div class="row-flex">
                    <div class="field">
                        <label>Họ tên khách *</label>
                        <input type="text" name="booking[ten_khach]"
                               value="<?= e($bookingForm['ten_khach'] ?? '') ?>"
                               placeholder="VD: Nguyễn Văn A" required>
                    </div>
                    <div class="field">
                        <label>CMND / CCCD *</label>
                        <input type="text" name="booking[cccd]"
                               value="<?= e($bookingForm['cccd'] ?? '') ?>"
                               placeholder="Chỉ nhập số" required>
                    </div>
                </div>

                <div class="row-flex">
                    <div class="field">
                        <label>Số điện thoại</label>
                        <input type="text" name="booking[sdt]"
                               value="<?= e($bookingForm['sdt'] ?? '') ?>"
                               placeholder="VD: 09xxxxxxxx">
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="booking[email]"
                               value="<?= e($bookingForm['email'] ?? '') ?>"
                               placeholder="VD: email@domain.com">
                    </div>
                    <div class="field">
                        <label>Số người ở phòng *</label>
                        <input type="number" min="1"
                               name="booking[so_nguoi]"
                               value="<?= e($bookingForm['so_nguoi'] ?? 1) ?>"
                               required>
                    </div>
                </div>

                <div class="row-flex">
                    <div class="field">
                        <label>Ngày nhận phòng *</label>
                        <input type="date" name="booking[ngay_nhan]"
                            value="<?= e($bookingForm['ngay_nhan'] ?? date('Y-m-d')) ?>"
                            required>
                    </div>

                    <div class="field">
                        <label>Ngày trả phòng *</label>
                        <input type="date" name="booking[ngay_tra]"
                            value="<?= e($bookingForm['ngay_tra'] ?? date('Y-m-d', strtotime('+1 day'))) ?>"
                            required>
                    </div>
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn-outline" name="btn_action" value="cancel_booking">
                        Hủy đặt phòng này
                    </button>
                    <button type="submit" class="btn-primary-modern" name="btn_action" value="review_booking">
                        Xem tóm tắt & tính tiền
                    </button>
                </div>
            </div>
        </form>
    <?php endif; ?>

    <!-- D. TÓM TẮT ĐƠN ĐẶT PHÒNG (BƯỚC 4) -->
    <?php if ($summary && !empty($summary['phong'])): ?>
        <form method="post">
            <!-- Giữ lại filter -->
            <input type="hidden" name="loai_phong" value="<?= e($filter['loai_phong'] ?? '') ?>">
            <input type="hidden" name="view_phong" value="<?= e($filter['view_phong'] ?? '') ?>">
            <input type="hidden" name="so_khach"   value="<?= e($filter['so_khach'] ?? '') ?>">
            <input type="hidden" name="gia_goi_y"  value="<?= e($filter['gia_goi_y'] ?? '') ?>">

            <!-- Giữ lại room + booking -->
            <input type="hidden" name="room_id" value="<?= (int)$summary['phong']['MaPhong'] ?>">
            <input type="hidden" name="booking[ten_khach]" value="<?= e($summary['phong']['TenKhach'] ?? '') ?>">
            <input type="hidden" name="booking[cccd]"      value="<?= e($summary['phong']['CCCD'] ?? '') ?>">
            <input type="hidden" name="booking[sdt]"       value="<?= e($summary['phong']['SDT'] ?? '') ?>">
            <input type="hidden" name="booking[email]"     value="<?= e($summary['phong']['Email'] ?? '') ?>">
            <input type="hidden" name="booking[so_nguoi]"  value="<?= e($summary['phong']['SoNguoi'] ?? '') ?>">
            <input type="hidden" name="booking[ngay_nhan]" value="<?= e($summary['phong']['NgayNhan']) ?>">
            <input type="hidden" name="booking[ngay_tra]" value="<?= e($summary['phong']['NgayTra']) ?>">

            <div class="card-shell">
                <h2>Tóm tắt đơn đặt phòng</h2>
                <p class="caption">
                    Kiểm tra kỹ thông tin trước khi bấm <strong>Xác nhận đặt phòng</strong>.
                </p>

                <div class="row-flex mb-2">
                    <div class="field">
                        <label>Mã khách hàng</label>
                        <input type="text" value="<?= e($summary['ma_kh'] ?? '') ?>" disabled>
                    </div>
                    <div class="field">
                        <label>Tên khách hàng</label>
                        <input type="text" value="<?= e($summary['ten_kh'] ?? '') ?>" disabled>
                    </div>
                    <div class="field">
                        <label>Phòng & loại phòng</label>
                        <input type="text"
                               value="Phòng <?= e($summary['phong']['SoPhong'] ?? '') ?> · <?= e($summary['phong']['LoaiPhong'] ?? '') ?>"
                               disabled>
                    </div>
                </div>

                <div class="row-flex mb-2">
                    <div class="field">
                        <label>Khách ở</label>
                        <input type="text"
                               value="<?= e($summary['phong']['TenKhach'] ?? '') ?> (<?= (int)($summary['phong']['SoNguoi'] ?? 1) ?> người)"
                               disabled>
                    </div>
                    <div class="field">
                        <label>Thông tin liên hệ</label>
                        <input type="text"
                               value="SDT: <?= e($summary['phong']['SDT'] ?? '') ?> · Email: <?= e($summary['phong']['Email'] ?? '') ?>"
                               disabled>
                    </div>
                </div>

                <div class="summary-box mb-2">
                    <div class="summary-row">
                        <span>Đơn giá phòng:</span>
                        <span><?= number_format($summary['phong']['GiaPhong'] ?? 0, 0, ',', '.') ?> đ / đêm</span>
                    </div>
                    <div class="summary-row">
                        <span>Số đêm dự kiến:</span>
                        <span><?= (int)($summary['so_dem'] ?? 1) ?> đêm</span>
                    </div>
                    <div class="summary-row">
                        <span>Thành tiền phòng:</span>
                        <span><?= number_format($summary['phong']['ThanhTien'] ?? 0, 0, ',', '.') ?> đ</span>
                    </div>
                    <div class="summary-row">
                        <span>Khuyến mãi:</span>
                        <span><?= number_format($summary['khuyen_mai'] ?? 0, 0, ',', '.') ?> đ</span>
                    </div>
                    <div class="summary-total">
                        <span>Tổng tạm tính:</span>
                        <span><?= number_format(($summary['tong_tien'] ?? 0) - ($summary['khuyen_mai'] ?? 0), 0, ',', '.') ?> đ</span>
                    </div>
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn-outline" name="btn_action" value="back_to_form">
                        Quay lại chỉnh sửa
                    </button>
                    <button type="submit" class="btn-primary-modern"
                            name="btn_action" value="confirm_booking"
                            onclick="return confirm('Xác nhận đặt phòng với các thông tin trên?');">
                        Xác nhận đặt phòng
                    </button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>
<script>
document.addEventListener("DOMContentLoaded", () => {

    // Tìm form nhập thông tin khách
    const form = document.querySelector("form input[name='booking[ten_khach]']")?.closest("form");

    if (!form) return;

    const ten = form.querySelector("input[name='booking[ten_khach]']");
    const cccd = form.querySelector("input[name='booking[cccd]']");
    const sdt = form.querySelector("input[name='booking[sdt]']");
    const email = form.querySelector("input[name='booking[email]']");
    const soNguoi = form.querySelector("input[name='booking[so_nguoi]']");

    // Hàm báo lỗi
    function setError(input, message) {
        input.style.borderColor = "red";
        input.setCustomValidity(message);
        input.reportValidity();
    }

    // Hàm clear lỗi
    function clearError(input) {
        input.style.borderColor = "#4b5563";
        input.setCustomValidity("");
    }

    // Validate tên (không chứa số)
    ten?.addEventListener("input", () => {
        if (/[0-9]/.test(ten.value)) {
            setError(ten, "Tên khách không được chứa số!");
        } else {
            clearError(ten);
        }
    });

    // Validate CCCD (9–12 số)
    cccd?.addEventListener("input", () => {
        if (!/^[0-9]{9,12}$/.test(cccd.value)) {
            setError(cccd, "CCCD phải có 9–12 chữ số!");
        } else {
            clearError(cccd);
        }
    });

    // Validate SDT
    sdt?.addEventListener("input", () => {
        if (sdt.value !== "" && !/^(0|\+84)[0-9]{8,10}$/.test(sdt.value)) {
            setError(sdt, "Số điện thoại không hợp lệ!");
        } else {
            clearError(sdt);
        }
    });

    // Validate email
    email?.addEventListener("input", () => {
        if (email.value !== "" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
            setError(email, "Email không hợp lệ!");
        } else {
            clearError(email);
        }
    });

    // Validate số người
    soNguoi?.addEventListener("input", () => {
        if (parseInt(soNguoi.value) < 1) {
            setError(soNguoi, "Số người phải ≥ 1");
        } else {
            clearError(soNguoi);
        }
    });

    // Chặn submit nếu còn lỗi
    form.addEventListener("submit", (e) => {
        const inputs = [ten, cccd, sdt, email, soNguoi];

        for (let i of inputs) {
            if (i && !i.checkValidity()) {
                e.preventDefault();
                i.reportValidity();
                return;
            }
        }
    });

});
</script>
</body>
</html>