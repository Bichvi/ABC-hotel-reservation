<?php
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kế toán - ABC Resort</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            min-height: 100vh;
            color: #e5e7eb;
        }
        .navbar {
            background: rgba(15, 23, 42, 0.95);
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
            border: 1px solid rgba(148, 163, 184, 0.35);
            background: radial-gradient(circle at top left, rgba(59,130,246,0.25), rgba(15,23,42,0.95));
            color: #e5e7eb;
            padding: 22px;
            box-shadow: 0 18px 40px rgba(0,0,0,0.65);
            transition: all 0.2s ease-in-out;
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
            background: rgba(15,23,42,0.9);
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
        .badge-soft {
            background: rgba(30,64,175,0.6);
            color: #bfdbfe;
            border-radius: 999px;
            padding: 4px 10px;
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

<nav class="navbar navbar-expand-lg navbar-dark border-bottom border-slate-700">
    <div class="container">
        <a class="navbar-brand brand-logo" href="#">
            <i class="fa-solid fa-hotel me-2 text-info"></i>ABC Resort - Kế toán
        </a>

        <div class="d-flex align-items-center">
            <span class="me-3 small text-slate-300">
                <i class="fa-regular fa-user me-1"></i><?= htmlspecialchars($user['Username']) ?>
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
            <h2 class="mb-1">Xin chào, Kế toán!</h2>
            <p class="text-secondary">
                Theo dõi doanh thu, quản lý chi phí và tạo báo cáo tài chính một cách nhanh chóng.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <span class="badge-soft">
                <i class="fa-solid fa-calendar-day me-1"></i>
                Ngày làm việc: <?= date('d/m/Y') ?>
            </span>
        </div>
    </div>

    <div class="mb-3">
        <span class="section-title">Nghiệp vụ kế toán</span>
    </div>

    <div class="row g-3">

        <!-- 1. Quản lý doanh thu -->
        <div class="col-md-4">
            <a href="index.php?controller=ketoan&action=quanLyDoanhThu" class="text-decoration-none text-light">
                <div class="card-module h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-money-bill-trend-up fa-lg text-success"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-chart-line"></i> Tổng hợp
                        </span>
                    </div>

                    <h5 class="mb-1">Quản lý doanh thu</h5>
                    <p class="small text-secondary">
                        Theo dõi doanh thu phòng, dịch vụ và thống kê từng ngày / tháng.
                    </p>

                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-coins me-1"></i>Tổng doanh thu</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 2. Quản lý chi phí -->
        <div class="col-md-4">
            <a href="index.php?controller=ketoan&action=quanLyChiPhi" class="text-decoration-none text-light">
                <div class="card-module h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-wallet fa-lg text-warning"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-list"></i> Vận hành
                        </span>
                    </div>

                    <h5 class="mb-1">Quản lý chi phí</h5>
                    <p class="small text-secondary">
                        Ghi nhận chi phí: điện nước, sửa chữa, lương nhân viên, vật tư…
                    </p>

                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-file-invoice-dollar me-1"></i>Chi phí hằng ngày</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 3. Báo cáo tài chính -->
        <div class="col-md-4">
            <a href="index.php?controller=ketoan&action=baoCaoDoanhThuChiPhi" class="text-decoration-none text-light">
                <div class="card-module h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-chart-pie fa-lg text-info"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-file-lines"></i> PDF / Excel
                        </span>
                    </div>

                    <h5 class="mb-1">Báo cáo doanh thu - chi phí</h5>
                    <p class="small text-secondary">
                        Xuất báo cáo tổng hợp theo ngày, tháng, quý, năm một cách nhanh chóng.
                    </p>

                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-folder-open me-1"></i>Tạo báo cáo</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <div class="mt-4 text-center footer-text">
        ABC Resort – Bộ phận Kế toán | Quản lý tài chính & báo cáo doanh thu
    </div>

</div>

</body>
</html>