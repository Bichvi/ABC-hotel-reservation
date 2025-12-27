<?php
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Dịch vụ - ABC Resort</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            min-height: 100vh;
            color: #e5e7eb;
        }
        .navbar {
            background: rgba(15,23,42,0.95) !important; 
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
            border: 1px solid rgba(148,163,184,0.3);
            background: radial-gradient(circle at top left, rgba(59,130,246,0.25), rgba(15,23,42,0.95));
            color: #e5e7eb;
            box-shadow: 0 18px 40px rgba(0,0,0,0.65);
            transition: all 0.2s ease-in-out;
            position: relative;
            overflow: hidden;
            padding: 18px;
        }
        .card-module:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 24px 60px rgba(0,0,0,0.7);
            border-color: rgba(96,165,250,0.9);
        }
        .icon-circle {
            width: 52px;
            height: 52px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(15,23,42,0.85);
            border: 1px solid rgba(148,163,184,0.5);
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
        .section-title {
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: .18em;
            color: #9ca3af;
        }
        .footer-text {
            font-size: 0.8rem;
            color: #9ca3af;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark border-bottom border-slate-700">
    <div class="container">
        <a class="navbar-brand brand-logo" href="#">
            <i class="fa-solid fa-broom-ball me-2 text-info"></i>ABC Resort – Dịch vụ
        </a>

        <div class="d-flex align-items-center">
            <span class="me-3 small">
                <i class="fa-regular fa-user me-1"></i>
                <?= htmlspecialchars($user['Username'] ?? 'dichvu1') ?>
            </span>

            <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </a>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container main-wrapper">

    <div class="row mb-4">
        <div class="col-lg-8">
            <h2 class="mb-1">Xin chào, Nhân viên dịch vụ!</h2>
            <p class="text-secondary">
                Quản lý dịch vụ và theo dõi tình trạng phòng một cách nhanh chóng.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <span class="quick-pill">
                <i class="fa-solid fa-calendar-days"></i> <?= date('d/m/Y') ?>
            </span>
        </div>
    </div>

    <div class="mb-3">
        <span class="section-title">Nghiệp vụ chính</span>
    </div>

    <div class="row g-3">

        <!-- Quản lý dịch vụ -->
        <div class="col-md-4">
            <a href="index.php?controller=dichvu&action=quanLyDichVu" class="text-decoration-none text-light">
                <div class="card-module h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-list-check text-info"></i>
                        </div>
                        <span class="quick-pill">CRUD dịch vụ</span>
                    </div>

                    <h5 class="mb-1">Quản lý dịch vụ</h5>
                    <p class="small text-secondary">
                        Thêm, chỉnh sửa, xóa các dịch vụ cung cấp cho khách trong resort.
                    </p>

                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-gear me-1"></i>Cài đặt dịch vụ</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Kiểm tra phòng -->
        <div class="col-md-4">
            <a href="index.php?controller=dichvu&action=kiemTraPhong" class="text-decoration-none text-light">
                <div class="card-module h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-door-open text-warning"></i>
                        </div>
                        <span class="quick-pill">Room Status</span>
                    </div>

                    <h5 class="mb-1">Kiểm tra tình trạng phòng</h5>
                    <p class="small text-secondary">
                        Xem phòng trống, đang ở, cần dọn, đang dọn hoặc bảo trì.
                    </p>

                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-bed me-1"></i>Trạng thái hiện tại</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <div class="mt-4 text-center footer-text">
        ABC Resort – Dịch vụ | Quản lý phòng & dịch vụ cho khách
    </div>

</div>

</body>
</html>