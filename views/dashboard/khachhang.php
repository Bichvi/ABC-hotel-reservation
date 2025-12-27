<?php
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Khách hàng - ABC Resort</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #020617, #0f172a);
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
            background: radial-gradient(circle at top left, rgba(56,189,248,0.25), rgba(15,23,42,0.96));
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
            background: rgba(15,23,42,0.9);
            border: 1px solid rgba(148,163,184,0.6);
        }
        .badge-soft {
            background: rgba(8,47,73,0.8);
            color: #bae6fd;
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
            <i class="fa-solid fa-hotel me-2 text-info"></i>ABC Resort - Khách hàng
        </a>
        <div class="d-flex align-items-center">
            <span class="me-3 small text-slate-300">
                <i class="fa-regular fa-user me-1"></i>
                <?= htmlspecialchars($user['Username'] ?? 'khach') ?>
            </span>
            <a href="index.php" class="btn btn-outline-light btn-sm me-3" title="Trang chủ">
                <i class="fa-solid fa-house"></i>
            </a>
            <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </a>
        </div>
    </div>
</nav>

<div class="container main-wrapper">
    <div class="row mb-4">
        <div class="col-lg-8">
            <h2 class="mb-1">Xin chào, <?= htmlspecialchars($user['Username'] ?? 'Quý khách') ?>!</h2>
            <p class="text-secondary">
                Từ đây, bạn có thể đặt phòng online, quản lý đặt phòng, đặt dịch vụ bổ sung và gửi phản hồi cho ABC Resort.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <span class="badge-soft">
                <i class="fa-solid fa-circle-info me-1"></i>
                Hôm nay: <?= date('d/m/Y') ?>
            </span>
        </div>
    </div>

    <div class="mb-3">
        <span class="section-title">Chức năng dành cho khách hàng</span>
    </div>

    <div class="row g-3">
        <!-- 1. Đặt phòng Online -->
        <div class="col-md-4">
            <a href="index.php?controller=khachhang&action=datPhongOnline1"
               class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-bed fa-lg text-success"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-globe"></i> Đặt phòng Online
                        </span>
                    </div>
                    <h5 class="mb-1">Đặt phòng Online</h5>
                    <p class="mb-2 small text-secondary">
                        Chọn ngày lưu trú, lọc phòng trống theo hạng phòng, số giường, tầng và xác nhận đặt phòng ngay trên hệ thống.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-filter me-1"></i>Lọc theo nhu cầu</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 2. Hủy đặt phòng -->
        <div class="col-md-4">
            <a href="index.php?controller=khachhang&action=huyDatPhong"
               class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-ban fa-lg text-danger"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-receipt"></i> Mã giao dịch
                        </span>
                    </div>
                    <h5 class="mb-1">Hủy đặt phòng</h5>
                    <p class="mb-2 small text-secondary">
                        Xem các đặt phòng hiện có, kiểm tra điều kiện và thực hiện hủy đặt phòng nếu còn trong thời hạn cho phép.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-door-open me-1"></i>Giải phóng lịch lưu trú</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 3. Cập nhật thông tin cá nhân -->
        <div class="col-md-4">
            <a href="index.php?controller=khachhang&action=capNhatThongTin"
               class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-user-pen fa-lg text-warning"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-regular fa-id-card"></i> Thông tin cá nhân
                        </span>
                    </div>
                    <h5 class="mb-1">Cập nhật thông tin cá nhân</h5>
                    <p class="mb-2 small text-secondary">
                        Cập nhật số điện thoại, email, địa chỉ liên hệ để đảm bảo nhận đủ thông tin về đặt phòng và khuyến mãi.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-shield-halved me-1"></i>Bảo mật thông tin</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 4. Đặt dịch vụ bổ sung -->
        <div class="col-md-4">
            <a href="index.php?controller=khachhang&action=datDichVuBoSung"
               class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-concierge-bell fa-lg text-info"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-spa"></i> Spa / Ăn uống /...
                        </span>
                    </div>
                    <h5 class="mb-1">Đặt dịch vụ bổ sung</h5>
                    <p class="mb-2 small text-secondary">
                        Đăng ký sử dụng các dịch vụ thêm như spa, ăn uống, giặt ủi, đưa đón... gắn với phòng đang lưu trú.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-plus-circle me-1"></i>Dịch vụ linh hoạt</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 5. Hủy đặt dịch vụ bổ sung -->
        <div class="col-md-4">
            <a href="index.php?controller=khachhang&action=huyDichVuBoSung"
               class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-xmark-circle fa-lg text-danger"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-file-invoice"></i> Lịch sử dịch vụ
                        </span>
                    </div>
                    <h5 class="mb-1">Hủy dịch vụ bổ sung</h5>
                    <p class="mb-2 small text-secondary">
                        Xem lại các dịch vụ đã đặt và thực hiện hủy nếu chưa đến thời gian sử dụng hoặc theo chính sách resort.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-clock-rotate-left me-1"></i>Chủ động thay đổi</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 6. Gửi phản hồi -->
        <div class="col-md-4">
            <a href="index.php?controller=khachhang&action=guiPhanHoi"
               class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-comment-dots fa-lg text-success"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-regular fa-star"></i> Đánh giá / Góp ý
                        </span>
                    </div>
                    <h5 class="mb-1">Gửi phản hồi cho resort</h5>
                    <p class="mb-2 small text-secondary">
                        Gửi đánh giá, góp ý hoặc khiếu nại để ABC Resort cải thiện chất lượng dịch vụ trong tương lai.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-heart me-1"></i>Chăm sóc khách hàng</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="mt-4 text-center footer-text">
        ABC Resort – Khách hàng | Đặt phòng & quản lý dịch vụ trực tuyến
    </div>
</div>

</body>
</html>