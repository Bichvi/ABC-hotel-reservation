<?php
$user = Auth::user();
$errors  = $errors  ?? [];
$success = $success ?? "";
$old     = $old ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thêm dịch vụ bổ sung - ABC Resort</title>

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
        max-width: 750px;
    }
    .form-control, .form-select {
        background: rgba(15,23,42,0.78);
        border: 1px solid rgba(148,163,184,.35);
        color: #e5e7eb;
    }
    .form-control:focus, .form-select:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 2px rgba(56,189,248,.4);
        background: rgba(15,23,42,1);
    }
    .page-title {
        font-size: 26px;
        font-weight: 700;
        color: #f8fafc;
        margin-bottom: 20px;
    }
    .btn-save {
        background: linear-gradient(135deg, #0ea5e9, #22c55e);
        border: none;
        color: #0f172a !important;
        font-weight: 600;
        padding: 10px 24px;
        border-radius: 12px;
    }
    .btn-cancel {
        border: 1px solid #94a3b8;
        border-radius: 12px;
        color: #e2e8f0;
        padding: 10px 24px;
    }
    .error-box {
        background: rgba(220,38,38,0.25);
        border-left: 4px solid #ef4444;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 15px;
    }
    .success-box {
        background: rgba(16,185,129,0.2);
        border-left: 4px solid #10b981;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 15px;
        color: #d1fae5;
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
<div class="wrapper">

    <div class="mb-3">
        <a href="index.php?controller=dichvu&action=quanLyDichVu" class="btn btn-cancel">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="page-title">
        <i class="fa-solid fa-plus text-info me-2"></i>Thêm dịch vụ bổ sung
    </div>

    <!-- HIỂN THỊ LỖI -->
    <?php if (!empty($errors)): ?>
        <div class="error-box"><ul class="mb-0">
            <?php foreach ($errors as $e): ?>
                <li><?= $e ?></li>
            <?php endforeach; ?>
        </ul></div>
    <?php endif; ?>

    <!-- THÔNG BÁO THÀNH CÔNG -->
    <?php if (!empty($success)): ?>
        <div class="success-box">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <!-- FORM THÊM DỊCH VỤ -->
    <form method="post"
          action="index.php?controller=dichvu&action=luuThem"
          enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label">Tên dịch vụ:</label>
            <input class="form-control" name="TenDichVu"
                   value="<?= htmlspecialchars($old['TenDichVu'] ?? "") ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Giá dịch vụ:</label>
            <input class="form-control" name="GiaDichVu" type="number" min="0"
                   value="<?= htmlspecialchars($old['GiaDichVu'] ?? "") ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả:</label>
            <textarea class="form-control" name="MoTa" rows="3"><?= htmlspecialchars($old['MoTa'] ?? "") ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái:</label>
            <select class="form-select" name="TrangThai">
                <option value="">-- Chọn trạng thái --</option>
                <option value="HoatDong" <?= (!empty($old['TrangThai']) && $old['TrangThai']=='HoatDong')?'selected':'' ?>>Hoạt động</option>
                <option value="TamNgung" <?= (!empty($old['TrangThai']) && $old['TrangThai']=='TamNgung')?'selected':'' ?>>Tạm ngưng</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Hình ảnh dịch vụ:</label>
            <input type="file" class="form-control" name="HinhAnh" accept="image/*">
        </div>

        <div class="d-flex justify-content-between mt-3">
            <a href="index.php?controller=dichvu&action=quanLyDichVu"
               class="btn btn-cancel">
               <i class="fa-solid fa-xmark"></i> Hủy
            </a>

            <button class="btn btn-save">
                <i class="fa-solid fa-floppy-disk me-1"></i> Lưu dịch vụ
            </button>
        </div>
    </form>

</div>
</div>

</body>
</html>