<?php
$user     = Auth::user();
$services = $services ?? [];
$success  = $success ?? "";
$error    = $error ?? "";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý dịch vụ bổ sung - ABC Resort</title>

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
        backdrop-filter: blur(12px);
    }
    .wrapper {
        background: rgba(15,23,42,0.86);
        border-radius: 20px;
        padding: 28px;
        border: 1px solid rgba(148,163,184,0.35);
        box-shadow: 0 20px 40px rgba(0,0,0,0.55);
        max-width: 1000px;
    }
    .page-title {
        font-size: 26px;
        font-weight: 700;
        color: #f8fafc;
        margin-bottom: 15px;
    }
    .btn-add {
        background: linear-gradient(135deg, #0ea5e9, #22c55e);
        border: none;
        color: #0f172a !important;
        font-weight: 600;
        padding: 10px 22px;
        border-radius: 12px;
    }
    .btn-edit {
        background: #3b82f6;
        border-radius: 8px;
        padding: 6px 14px;
        color: #f8fafc;
    }
    .btn-del {
        background: #ef4444;
        border-radius: 8px;
        padding: 6px 14px;
        color: white;
    }
    .btn-back {
        background: #64748b;
        border-radius: 12px;
        padding: 10px 22px;
        color: white;
    }
    table thead {
        background: rgba(30,41,59,0.85);
    }
    .dv-img {
        width: 65px;
        height: 65px;
        border-radius: 10px;
        object-fit: cover;
        border: 1px solid #475569;
    }
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark mb-4 border-bottom border-slate-700">
    <div class="container d-flex justify-content-between">
        <span class="navbar-brand">
            <i class="fa-solid fa-broom-ball me-2 text-info"></i>ABC Resort - Dịch vụ
        </span>
        <div class="d-flex align-items-center">
            <span class="me-3 small">
                <i class="fa-regular fa-user me-1"></i><?= htmlspecialchars($user['Username']) ?>
            </span>
            <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </a>
        </div>
    </div>
</nav>

<div class="container d-flex justify-content-center">
<div class="wrapper w-100">

    <div class="d-flex justify-content-between mb-3">
        <!-- Nút về trang dashboard (đúng action bạn đang dùng) -->
        <a href="index.php?controller=dashboard&action=dichvu" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Trang chủ
        </a>

        <a href="index.php?controller=dichvu&action=them" class="btn-add">
            <i class="fa-solid fa-plus me-1"></i> Thêm dịch vụ
        </a>
    </div>

    <div class="page-title">
        <i class="fa-solid fa-list me-2 text-info"></i>
        Danh sách dịch vụ bổ sung
    </div>

    <!-- THÔNG BÁO -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?= is_string($success) ? htmlspecialchars($success) : "Thao tác thành công." ?>
        </div>
    <?php endif; ?>

    <table class="table table-dark table-striped align-middle">
        <thead>
            <tr>
                <th>Hình</th>
                <th>Tên dịch vụ</th>
                <th>Giá</th>
                <th>Trạng thái</th>
                <th>Mô tả</th>
                <th class="text-end">Thao tác</th>
            </tr>
        </thead>

        <tbody>
        <?php if (empty($services)): ?>
            <tr>
                <td colspan="6" class="text-center py-3 text-secondary">
                    Không có dịch vụ nào.
                </td>
            </tr>
        <?php endif; ?>

        <?php foreach ($services as $dv): ?>
            <tr>
                <td>
                    <img class="dv-img"
                         src="public/uploads/dichvu/<?= htmlspecialchars($dv['HinhAnh']) ?>"
                         onerror="this.onerror=null; this.src='public/uploads/dichvu/noimg.jpg';">
                </td>

                <td><?= htmlspecialchars($dv['TenDichVu']) ?></td>

                <td><?= number_format($dv['GiaDichVu'], 0, ',', '.') ?> VNĐ</td>

                <td>
                    <?php if ($dv['TrangThai'] == 'HoatDong'): ?>
                        <span class="badge bg-success">Kích hoạt</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Tạm ngưng</span>
                    <?php endif; ?>
                </td>

                <td style="white-space:pre-line;"><?= htmlspecialchars($dv['MoTa']) ?></td>

                <td class="text-end">
                    <a href="index.php?controller=dichvu&action=sua&id=<?= $dv['MaDichVu'] ?>"
                       class="btn-edit me-1">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>

                    <a href="index.php?controller=dichvu&action=xoa&id=<?= $dv['MaDichVu'] ?>"
                       onclick="return confirm('Bạn chắc muốn xóa dịch vụ này?')"
                       class="btn-del">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

</div>
</div>

</body>
</html>