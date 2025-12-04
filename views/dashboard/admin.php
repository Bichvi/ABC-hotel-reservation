<?php
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin - ABC Resort</title>

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
        padding: 35px 0;
    }
    .card-module {
        border-radius: 18px;
        border: 1px solid rgba(148,163,184,0.3);
        background: radial-gradient(circle at top left, rgba(96,165,250,0.20), rgba(15,23,42,0.95));
        color: #e5e7eb;
        box-shadow: 0 18px 40px rgba(0,0,0,0.65);
        padding: 20px;
        height: 100%;
        transition: .2s ease-in-out;
    }
    .card-module:hover {
        transform: translateY(-4px);
        border-color: rgba(96,165,250,0.9);
        box-shadow: 0 24px 60px rgba(0,0,0,0.75);
    }
    .icon-circle {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: rgba(15,23,42,0.85);
        border: 1px solid rgba(148,163,184,0.4);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .quick-pill {
        background: rgba(15,23,42,0.8);
        border: 1px solid rgba(148,163,184,0.3);
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 0.75rem;
    }
    .footer-text {
        color: #94a3b8;
        margin-top: 25px;
        font-size: 0.85rem;
    }
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark border-bottom border-slate-700">
    <div class="container">
        <a class="navbar-brand brand-logo">
            <i class="fa-solid fa-shield-halved text-warning me-2"></i>
            ABC Resort - Admin
        </a>

        <div class="d-flex align-items-center">
            <span class="me-3 small">
                <i class="fa-regular fa-user me-1"></i><?= htmlspecialchars($user['Username']) ?>
            </span>

            <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
            </a>
        </div>
    </div>
</nav>

<!-- MAIN -->
<div class="container main-wrapper">

    <h2 class="mb-2">Xin chào, Admin!</h2>
    <p class="text-secondary">Quản lý phòng – phân quyền – hệ thống tài khoản.</p>

    <div class="row g-3 mt-4">

        <!-- QUẢN LÝ PHÒNG -->
        <div class="col-md-4">
            <a href="index.php?controller=admin&action=phong" class="text-decoration-none text-light">
                <div class="card-module">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-bed fa-lg text-info"></i>
                        </div>
                        <div class="quick-pill">Phòng</div>
                    </div>

                    <h5>Quản lý phòng</h5>
                    <p class="text-secondary small">Thêm phòng – sửa phòng – xóa phòng.</p>
                </div>
            </a>
        </div>

        <!-- PHÂN QUYỀN -->
        <div class="col-md-4">
            <a href="index.php?controller=admin&action=phanQuyen" class="text-decoration-none text-light">
                <div class="card-module">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-user-gear fa-lg text-warning"></i>
                        </div>
                        <div class="quick-pill">Quyền</div>
                    </div>

                    <h5>Phân quyền tài khoản</h5>
                    <p class="text-secondary small">Phân vai trò: Admin – Quản lý – Khách – Lễ tân.</p>
                </div>
            </a>
        </div>

        <!-- QUẢN LÝ TÀI KHOẢN -->
        <div class="col-md-4">
            <a href="index.php?controller=admin&action=taikhoan" class="text-decoration-none text-light">
                <div class="card-module">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-id-card fa-lg text-success"></i>
                        </div>
                        <div class="quick-pill">Tài khoản</div>
                    </div>

                    <h5>Quản lý tài khoản</h5>
                    <p class="text-secondary small">Khóa – mở – reset mật khẩu.</p>
                </div>
            </a>
        </div>

    </div>

    <div class="text-center footer-text">
        ABC Resort – Trang quản trị hệ thống
    </div>

</div>

</body>
</html>