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

        <!-- 0. Tổng Quan -->
        <div class="col-md-4">
            <a href="index.php?controller=ketoan&action=tongQuan" class="text-decoration-none text-light">
                <div class="card-module h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-chart-line fa-lg text-primary"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-gauge"></i> Dashboard
                        </span>
                    </div>

                    <h5 class="mb-1">Tổng Quan</h5>
                    <p class="small text-secondary">
                        Biểu đồ trực quan và thống kê kinh doanh 30 ngày gần nhất.
                    </p>

                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-bar-chart me-1"></i>Xem biểu đồ</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

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

        <!-- 4. Kiểm toán đêm -->
        <div class="col-md-4">
            <a href="index.php?controller=ketoan&action=kiemToanDem" class="text-decoration-none text-light">
                <div class="card-module h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-moon fa-lg text-warning"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-clock"></i> Hằng đêm
                        </span>
                    </div>

                    <h5 class="mb-1">Kiểm toán đêm</h5>
                    <p class="small text-secondary">
                        Kiểm toán doanh thu, chi phí và lợi nhuận hàng đêm một cách nhanh chóng.
                    </p>

                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-check-circle me-1"></i>Kiểm toán</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <!-- Báo cáo Nâng Cao -->
    <div class="mb-3 mt-5">
        <span class="section-title">Báo cáo tài chính nâng cao</span>
    </div>

    <div class="row g-3">

        <!-- 5. Báo cáo KQKD -->
        <div class="col-md-4">
            <a href="index.php?controller=ketoan&action=baoCaoKQKD" class="text-decoration-none text-light">
                <div class="card-module h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-chart-column fa-lg" style="color: #10b981;"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-file"></i> Báo cáo
                        </span>
                    </div>

                    <h5 class="mb-1">KQKD</h5>
                    <p class="small text-secondary">
                        Báo cáo kết quả kinh doanh: Doanh thu - Chi phí = Lợi nhuận
                    </p>

                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-arrow-up me-1"></i>Xem báo cáo</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 6. Lưu chuyển tiền tệ -->
        <div class="col-md-4">
            <a href="index.php?controller=ketoan&action=luuChuyenTienTe" class="text-decoration-none text-light">
                <div class="card-module h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-water fa-lg" style="color: #0ea5e9;"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-file"></i> Báo cáo
                        </span>
                    </div>

                    <h5 class="mb-1">Lưu chuyển tiền tệ</h5>
                    <p class="small text-secondary">
                        Báo cáo cash flow: Hoạt động kinh doanh, đầu tư, tài chính
                    </p>

                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-water me-1"></i>Xem báo cáo</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 7. Công nợ phải thu -->
        <div class="col-md-4">
            <a href="index.php?controller=ketoan&action=congNoPhaiThu" class="text-decoration-none text-light">
                <div class="card-module h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-hand-holding-heart fa-lg" style="color: #f59e0b;"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-list"></i> A/R
                        </span>
                    </div>

                    <h5 class="mb-1">Công nợ phải thu</h5>
                    <p class="small text-secondary">
                        Quản lý tiền khách hàng nợ, trạng thái thanh toán, thống kê nợ
                    </p>

                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-users me-1"></i>Xem công nợ</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 8. Công nợ phải trả -->
        <div class="col-md-4">
            <a href="index.php?controller=ketoan&action=congNoPhaiTra" class="text-decoration-none text-light">
                <div class="card-module h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-coins fa-lg" style="color: #ef4444;"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-list"></i> A/P
                        </span>
                    </div>

                    <h5 class="mb-1">Công nợ phải trả</h5>
                    <p class="small text-secondary">
                        Quản lý tiền nhà cung cấp nợ, trạng thái thanh toán, thống kê nợ
                    </p>

                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-building me-1"></i>Xem công nợ</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 9. Sổ nganh quỹ -->
        <div class="col-md-4">
            <a href="index.php?controller=ketoan&action=soNganQuy" class="text-decoration-none text-light">
                <div class="card-module h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-book fa-lg" style="color: #8b5cf6;"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-list"></i> Sổ
                        </span>
                    </div>

                    <h5 class="mb-1">Sổ nganh quỹ</h5>
                    <p class="small text-secondary">
                        Theo dõi giao dịch tiền: Tiền mặt, Chuyển khoản, Thẻ, Ví điện tử
                    </p>

                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-money-bills me-1"></i>Xem sổ</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 10. Đối soát nganh quỹ -->
        <div class="col-md-4">
            <a href="index.php?controller=ketoan&action=doiSoatNganQuy" class="text-decoration-none text-light">
                <div class="card-module h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-sync-alt fa-lg" style="color: #06b6d4;"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-check"></i> Đối soát
                        </span>
                    </div>

                    <h5 class="mb-1">Đối soát nganh quỹ</h5>
                    <p class="small text-secondary">
                        So sánh sổ ghi vs ngân hàng, kiểm tra chênh lệch, xác nhận giao dịch
                    </p>

                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-balance-scale me-1"></i>Đối soát</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 11. Quản lý kỳ khóa sổ -->
        <div class="col-md-4">
            <a href="index.php?controller=ketoan&action=khoaSo" class="text-decoration-none text-light">
                <div class="card-module h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-lock fa-lg" style="color: #ec4899;"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-calendar"></i> Kỳ
                        </span>
                    </div>

                    <h5 class="mb-1">Quản lý kỳ khóa sổ</h5>
                    <p class="small text-secondary">
                        Khóa kỳ, ngăn sửa giao dịch quá khứ, lịch sử kỳ khóa
                    </p>

                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-key me-1"></i>Quản lý</span>
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