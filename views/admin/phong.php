<?php
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý phòng - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body{background:radial-gradient(circle at top,#0f172a,#1e293b,#020617);min-height:100vh;color:#e5e7eb;font-family:"Segoe UI",sans-serif;}
        .layout{max-width:1100px;margin:24px auto;}
        .shell{background:rgba(15,23,42,0.95);border-radius:24px;border:1px solid rgba(148,163,184,0.3);box-shadow:0 24px 60px rgba(0,0,0,0.8);padding:24px;}
        .sidebar{border-right:1px solid rgba(148,163,184,0.3);padding-right:18px;}
        .avatar-card{background:linear-gradient(145deg,#1e293b,#020617);border-radius:20px;padding:18px 14px;text-align:center;margin-bottom:18px;border:1px solid rgba(148,163,184,0.35);}
        .avatar-circle{width:72px;height:72px;border-radius:50%;border:2px solid rgba(148,163,184,0.7);display:flex;align-items:center;justify-content:center;margin:0 auto 8px;background:radial-gradient(circle at top,#0ea5e9,#0f172a);}
        .nav-section-title{font-size:.8rem;letter-spacing:.03em;text-transform:uppercase;color:#9ca3af;margin-bottom:6px;}
        .side-link{display:block;border-radius:999px;padding:8px 14px;font-size:.9rem;border:1px solid transparent;color:#e5e7eb;text-decoration:none;margin-bottom:6px;}
        .side-link.active{background:linear-gradient(135deg,#22c55e,#16a34a);color:#022c22;font-weight:600;}
        .side-link:hover{background:rgba(30,64,175,0.15);border-color:rgba(129,140,248,0.6);}
        .content-header{border-bottom:1px solid rgba(148,163,184,0.3);margin-bottom:18px;padding-bottom:10px;}
        .card-placeholder{background:rgba(15,23,42,0.9);border-radius:18px;border:1px solid rgba(148,163,184,0.45);padding:18px;}
        .card-placeholder h5{margin-bottom:10px;}
        .form-control,.form-select{background:rgba(15,23,42,0.85);border:1px solid rgba(148,163,184,0.45);color:#e5e7eb;border-radius:12px;}
        .form-control:focus,.form-select:focus{border-color:#38bdf8;box-shadow:0 0 0 2px rgba(56,189,248,.4);}
        .btn-success-modern{background:linear-gradient(135deg,#22c55e,#4ade80);border:none;color:#022c22;font-weight:600;border-radius:12px;padding-inline:22px;}
        .btn-outline-light-modern{border-radius:12px;border-color:rgba(148,163,184,0.7);}
    </style>
</head>
<body>
<div class="layout">
    <div class="shell">
        <div class="row g-4">
            <div class="col-md-3 sidebar">
                <div class="avatar-card">
                    <div class="avatar-circle mb-2">
                        <i class="fa-solid fa-user-shield fa-2x text-slate-100"></i>
                    </div>
                    <div class="fw-semibold">ADMIN</div>
                    <div class="small text-secondary">
                        <?= htmlspecialchars($user['Username']) ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="nav-section-title">Trang chủ</div>
                    <a href="index.php?controller=dashboard&action=admin" class="side-link">
                        <i class="fa-solid fa-house me-2"></i> Trang chủ
                    </a>
                </div>
                <div>
                    <div class="nav-section-title">Quản lý phòng</div>
                    <a href="#" class="side-link active">
                        <i class="fa-solid fa-plus me-2"></i> Thêm phòng
                    </a>
                    <a href="#" class="side-link">
                        <i class="fa-solid fa-pen-to-square me-2"></i> Cập nhật phòng
                    </a>
                    <a href="#" class="side-link">
                        <i class="fa-solid fa-trash-can me-2"></i> Xóa phòng
                    </a>
                </div>
            </div>
            <div class="col-md-9">
                <div class="content-header">
                    <div class="small text-secondary mb-1">
                        Quản lý phòng &gt; Thêm / sửa / xóa phòng
                    </div>
                    <h3 class="mb-0">Quản lý phòng</h3>
                </div>

                <!-- Chỉ là giao diện minh họa, chưa có xử lý CRUD -->
                <div class="card-placeholder mb-3">
                    <h5><i class="fa-solid fa-plus text-success me-1"></i> Thêm phòng</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tên phòng</label>
                            <input type="text" class="form-control" placeholder="Phòng 101">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Loại phòng</label>
                            <select class="form-select">
                                <option>Standard</option>
                                <option>Deluxe</option>
                                <option>Suite</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Giá/đêm</label>
                            <input type="number" class="form-control" placeholder="1.000.000">
                        </div>
                    </div>
                    <button class="btn btn-success-modern">Lưu phòng mới</button>
                </div>

                <div class="card-placeholder mb-3">
                    <h5><i class="fa-solid fa-pen-to-square text-info me-1"></i> Cập nhật phòng</h5>
                    <p class="small text-secondary mb-2">Chọn phòng cần sửa và cập nhật thông tin (demo giao diện).</p>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <select class="form-select">
                                <option>Phòng 101</option>
                                <option>Phòng 102</option>
                                <option>Phòng 201</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Tên phòng mới">
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-light-modern w-100">Cập nhật</button>
                        </div>
                    </div>
                </div>

                <div class="card-placeholder">
                    <h5><i class="fa-solid fa-trash-can text-danger me-1"></i> Xóa phòng</h5>
                    <p class="small text-secondary mb-2">Danh sách phòng (demo). Chọn và xóa.</p>
                    <div class="row g-2">
                        <div class="col-md-8">
                            <select class="form-select">
                                <option>Phòng 101</option>
                                <option>Phòng 102</option>
                                <option>Phòng 201</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-light-modern w-100 text-danger">
                                Xóa phòng
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</body>
</html>


