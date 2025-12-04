<!-- views/dashboard/letan.php -->
<?php
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Lễ tân - ABC Resort</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            min-height: 100vh;
            color: #e5e7eb;
        }
        .navbar {
            background: rgba(15, 23, 42, 0.95) !important;
            backdrop-filter: blur(10px);
        }
        .brand-logo {
            font-weight: 700;
            letter-spacing: 1px;
        }
        .main-wrapper {
            padding: 30px 0;
        }
        .card-module {
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.3);
            background: radial-gradient(circle at top left, rgba(59,130,246,0.25), rgba(15,23,42,0.95));
            color: #e5e7eb;
            box-shadow: 0 18px 40px rgba(0,0,0,0.65);
            transition: all 0.2s ease-in-out;
            position: relative;
            overflow: hidden;
        }
        .card-module:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 24px 60px rgba(0,0,0,0.7);
            border-color: rgba(96, 165, 250, 0.9);
        }
        .card-module .icon-circle {
            width: 52px;
            height: 52px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(15,23,42,0.85);
            border: 1px solid rgba(148,163,184,0.5);
        }
        .badge-soft {
            background: rgba(30,64,175,0.6);
            color: #bfdbfe;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 0.75rem;
        }
        .quick-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 9px;
            border-radius: 999px;
            background: rgba(15,23,42,0.85);
            border: 1px solid rgba(148,163,184,0.3);
            font-size: 0.75rem;
        }
        .footer-text {
            font-size: 0.8rem;
            color: #9ca3af;
        }
        .section-title {
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: .18em;
            color: #9ca3af;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark border-bottom border-slate-700">
    <div class="container">
        <a class="navbar-brand brand-logo" href="#">
            <i class="fa-solid fa-hotel me-2 text-info"></i>ABC Resort - Lễ tân
        </a>
        <div class="d-flex align-items-center">
            <span class="me-3 small text-slate-300">
                <i class="fa-regular fa-user me-1"></i>
                <?= htmlspecialchars($user['Username'] ?? 'letan') ?>
            </span>
            <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </a>
        </div>
    </div>
</nav>

<div class="container main-wrapper">
    <div class="row mb-4">
        <div class="col-lg-8">
            <h2 class="mb-1">Xin chào, Lễ tân!</h2>
            <p class="text-secondary">
                Quản lý nhanh các nghiệp vụ tại quầy: đăng ký tài khoản, đặt phòng, check-in, check-out, sửa/hủy phòng và đặt dịch vụ.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <span class="badge-soft">
                <i class="fa-solid fa-circle-info me-1"></i>
                Ca trực hôm nay: <?= date('d/m/Y') ?>
            </span>
        </div>
    </div>

    <div class="mb-3">
        <span class="section-title">Nghiệp vụ chính</span>
    </div>

    <div class="row g-3">
        <!-- 1. Đăng ký tài khoản -->
        <div class="col-md-4">
            <a href="index.php?controller=letan&action=dangKyTaiKhoan" class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-id-card fa-lg text-primary"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-regular fa-clock"></i> 2–3 phút
                        </span>
                    </div>
                    <h5 class="mb-1">Đăng ký tài khoản đoàn/khách</h5>
                    <p class="mb-2 small text-secondary">
                        Nhập thông tin trưởng đoàn, thành viên, cấp username & mật khẩu tạm cho từng người.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-users me-1"></i>Khách mới / đoàn</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 2. Đặt phòng trực tiếp -->
        <div class="col-md-4">
            <a href="index.php?controller=letan&action=datPhongTrucTiep" class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-bed fa-lg text-success"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-bolt"></i> Walk-in
                        </span>
                    </div>
                    <h5 class="mb-1">Đặt phòng trực tiếp tại quầy</h5>
                    <p class="mb-2 small text-secondary">
                        Nhập thông tin khách, lọc phòng trống, chọn phòng & dịch vụ, tạo giao dịch và mã đặt phòng.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-filter me-1"></i>Lọc theo ngày, loại phòng</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 3. Sửa thông tin đặt phòng -->
        <div class="col-md-4">
            <a href="index.php?controller=letan&action=suaThongTinDatPhong" class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-pen-to-square fa-lg text-warning"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-magnifying-glass"></i> Tìm mã giao dịch
                        </span>
                    </div>
                    <h5 class="mb-1">Sửa thông tin đặt phòng</h5>
                    <p class="mb-2 small text-secondary">
                        Điều chỉnh ngày đến/đi, phòng, dịch vụ hoặc thông tin khách, vẫn đảm bảo phòng còn khả dụng.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-calendar-days me-1"></i>Điều chỉnh lịch lưu trú</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 4. Hủy đặt phòng -->
        <div class="col-md-4">
            <a href="index.php?controller=letan&action=huyDatPhong" class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-ban fa-lg text-danger"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-shield-halved"></i> Kiểm tra điều kiện
                        </span>
                    </div>
                    <h5 class="mb-1">Hủy đặt phòng</h5>
                    <p class="mb-2 small text-secondary">
                        Tìm giao dịch theo mã hoặc CMND, cho phép hủy toàn bộ hoặc từng phòng nếu còn trong thời hạn cho phép.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-door-open me-1"></i>Giải phóng phòng trống</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 5. Check-in -->
        <div class="col-md-4">
            <a href="index.php?controller=letan&action=checkIn" class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-key fa-lg text-info"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-regular fa-id-card"></i> CMND / Mã GD
                        </span>
                    </div>
                    <h5 class="mb-1">Check-in khách / đoàn</h5>
                    <p class="mb-2 small text-secondary">
                        Xác thực khách, chọn phòng cần nhận, cập nhật trạng thái Stayed và kích hoạt tài khoản sử dụng dịch vụ.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-user-check me-1"></i>Nhận phòng từng phần</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 6. Check-out -->
        <div class="col-md-4">
            <a href="index.php?controller=letan&action=checkOut" class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-door-closed fa-lg text-light"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-receipt"></i> Hóa đơn tạm tính
                        </span>
                    </div>
                    <h5 class="mb-1">Check-out & thanh toán</h5>
                    <p class="mb-2 small text-secondary">
                        Ghi nhận giờ trả phòng, phụ thu, bồi thường (nếu có), lập hóa đơn và cập nhật phòng về trạng thái trống.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-credit-card me-1"></i>Tiền mặt / QR / thẻ</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 7. Đặt dịch vụ -->
        <div class="col-md-4">
            <a href="index.php?controller=letan&action=datDichVu" class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-concierge-bell fa-lg text-warning"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-spa"></i> Spa / Giặt ủi / ...
                        </span>
                    </div>
                    <h5 class="mb-1">Đặt dịch vụ cho khách đang ở</h5>
                    <p class="mb-2 small text-secondary">
                        Gắn dịch vụ vào mã giao dịch + phòng, tính tiền tự động vào hóa đơn cuối cùng.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-plus-circle me-1"></i>Dịch vụ phát sinh</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="mt-4 text-center footer-text">
        ABC Resort – Lễ tân | Quản lý nghiệp vụ đặt phòng & khách lưu trú
    </div>
</div>

</body>
</html>