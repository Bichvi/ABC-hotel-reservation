<?php
$user   = Auth::user();
$dv     = $dv ?? [];
$errors = $errors ?? [];
$success = $success ?? "";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Cập nhật dịch vụ - ABC Resort</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
    body {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        min-height: 100vh;
        color: #e5e7eb;
    }
    .navbar {
        background: rgba(15,23,42,0.95);
        backdrop-filter: blur(12px);
    }
    .wrapper {
        background: rgba(15,23,42,0.88);
        border-radius: 22px;
        padding: 28px;
        border: 1px solid rgba(148,163,184,0.35);
        box-shadow: 0 18px 40px rgba(0,0,0,0.55);
        max-width: 750px;
    }
    .page-title {
        font-size: 26px;
        font-weight: 700;
        color: #f8fafc;
    }
    .form-control, .form-select {
        background: rgba(15,23,42,0.78);
        border: 1px solid rgba(148,163,184,.35);
        color: #e5e7eb;
        border-radius: 12px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 2px rgba(56,189,248,.4);
        background: rgba(15,23,42,1);
        color: #f8fafc;
    }
    .btn-save {
        background: linear-gradient(135deg, #0ea5e9, #22c55e);
        border: none;
        color: #0f172a !important;
        font-weight: 600;
        padding: 10px 24px;
        border-radius: 12px;
    }
    .btn-back {
        background: #64748b;
        border: none;
        border-radius: 12px;
        padding: 10px 20px;
        color: white;
    }
    .error-box {
        background: rgba(220,38,38,0.25);
        border-left: 4px solid #ef4444;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 15px;
    }
    .dv-img {
        width: 120px;
        height: 120px;
        border-radius: 14px;
        object-fit: cover;
        border: 1px solid rgba(148,163,184,.35);
    }
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark mb-4 border-bottom border-slate-700">
    <div class="container d-flex justify-content-between">
        <span class="navbar-brand">
            <i class="fa-solid fa-broom-ball text-info me-2"></i>ABC Resort – Dịch vụ
        </span>
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

<div class="container d-flex justify-content-center">
<div class="wrapper w-100">

    <!-- HEADER -->
    <div class="d-flex justify-content-between mb-3">
        <a href="index.php?controller=dichvu&action=quanLyDichVu" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Trang danh sách
        </a>
    </div>

    <div class="page-title mb-3">
        <i class="fa-solid fa-pen-to-square text-info me-2"></i>
        Cập nhật dịch vụ
    </div>

    <!-- THÔNG BÁO -->
    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                    <li><?= $e ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>


    <!-- FORM -->
    <form method="post" enctype="multipart/form-data"
          action="index.php?controller=dichvu&action=luuSua">

        <input type="hidden" name="MaDichVu" value="<?= $dv['MaDichVu'] ?>">

        <div class="mb-3">
            <label class="form-label">Tên dịch vụ:</label>
            <input type="text" name="TenDichVu" class="form-control"
                   value="<?= htmlspecialchars($dv['TenDichVu']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Giá dịch vụ:</label>
            <input type="number" name="GiaDichVu" class="form-control"
                   value="<?= htmlspecialchars($dv['GiaDichVu']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái:</label>

            <select name="TrangThai" class="form-select">
                <option value="HoatDong" <?= $dv['TrangThai']=='HoatDong'?'selected':'' ?>>
                    Hoạt động
                </option>

                <option value="NgungBan" <?= $dv['TrangThai']=='NgungBan'?'selected':'' ?>>
                    tạm ngưng
                </option>

                <option value="BaoTri" <?= $dv['TrangThai']=='BaoTri'?'selected':'' ?>>
                    Bảo trì
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả:</label>
            <textarea name="MoTa" class="form-control" rows="3"><?= htmlspecialchars($dv['MoTa']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Ảnh hiện tại:</label><br>
            <img src="public/uploads/dichvu/<?= $dv['HinhAnh'] ?>"
                 class="dv-img"
                 onerror="this.src='public/uploads/dichvu/noimg.jpg'">
        </div>

        <div class="mb-3">
            <label class="form-label">Chọn ảnh mới (tuỳ chọn):</label>
            <input type="file" name="HinhAnh" class="form-control">
        </div>

        <div class="text-end">
            <button class="btn-save">
                <i class="fa-solid fa-floppy-disk me-1"></i> Lưu thay đổi
            </button>
        </div>

    </form>

</div>
</div>

</body>
</html>