<?php
// View sửa chi phí

$user = $user ?? Auth::user();
$error = $error ?? '';
$success = $success ?? '';
$input = $input ?? [];
$maCP = $maCP ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa chi phí - Kế toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-main: #0f172a;
            --bg-card: rgba(15, 23, 42, 0.95);
            --border-soft: rgba(148, 163, 184, 0.2);
            --text-main: #e2e8f0;
            --text-muted: #94a3b8;
            --accent-blue: #0ea5e9;
            --accent-amber: #f59e0b;
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            color: var(--text-main);
        }

        .navbar {
            background: linear-gradient(90deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%) !important;
            border-bottom: 1px solid var(--border-soft);
        }

        .brand-logo {
            font-weight: 700;
            color: var(--accent-blue) !important;
        }

        .main-wrapper {
            padding: 30px 20px;
        }

        .form-card {
            border-radius: 12px;
            border: 1px solid var(--border-soft);
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(15, 23, 42, 0.8) 100%);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .form-label {
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(148, 163, 184, 0.25);
            color: var(--text-main);
            border-radius: 6px;
        }

        .form-control::placeholder {
            color: rgba(148, 163, 184, 0.5);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
            background: rgba(15, 23, 42, 0.9);
            color: var(--text-main);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--accent-amber), #f59e0b);
            color: #0f172a;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            padding: 10px 24px;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        .btn-back {
            background: rgba(14, 165, 233, 0.15);
            border: 1px solid var(--accent-blue);
            color: var(--accent-blue);
            font-weight: 600;
            border-radius: 6px;
            padding: 10px 20px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-back:hover {
            background: var(--accent-blue);
            color: #0f172a;
        }

        .alert {
            border-radius: 8px;
            border: 1px solid;
            margin-bottom: 20px;
        }

        .alert-danger {
            border-color: rgba(248, 113, 113, 0.4);
            background: rgba(127, 29, 29, 0.7);
            color: #fee2e2;
        }

        .alert-success {
            border-color: rgba(34, 197, 94, 0.4);
            background: rgba(6, 95, 70, 0.7);
            color: #dcfce7;
        }

        .form-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .form-subtitle {
            color: var(--text-muted);
            margin-bottom: 24px;
        }

        .btn-group-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .info-box {
            background: rgba(14, 165, 233, 0.1);
            border: 1px solid rgba(14, 165, 233, 0.3);
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #93c5fd;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark border-bottom border-slate-700">
    <div class="container">
        <a class="navbar-brand brand-logo" href="index.php?controller=ketoan&action=dashboard">
            <i class="fa-solid fa-calculator me-2 text-warning"></i>ABC Resort - Kế toán
        </a>
        <div class="d-flex align-items-center">
            <span class="me-3 small text-slate-300">
                <i class="fa-regular fa-user me-1"></i><?= htmlspecialchars($user['Username'] ?? '') ?>
            </span>
            <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </a>
        </div>
    </div>
</nav>

<div class="container main-wrapper">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <a href="index.php?controller=ketoan&action=quanLyChiPhi" class="btn-back mb-4">
                <i class="fa-solid fa-chevron-left me-2"></i>Quay lại danh sách
            </a>

            <div class="form-card p-4">
                <h2 class="form-title">
                    <i class="fa-solid fa-edit me-2" style="color: #f59e0b;"></i>Sửa chi phí
                </h2>
                <p class="form-subtitle">Cập nhật thông tin chi phí #<?= htmlspecialchars($maCP) ?></p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fa-solid fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <div class="info-box">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    ID: <strong>#<?= htmlspecialchars($maCP) ?></strong>
                </div>

                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <label for="TenChiPhi" class="form-label">Tên chi phí <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="TenChiPhi" name="TenChiPhi" 
                               placeholder="Ví dụ: Điện nước tháng 11, Sửa chữa phòng 101..."
                               value="<?= htmlspecialchars($input['TenChiPhi'] ?? '') ?>" required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="NgayChi" class="form-label">Ngày chi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="NgayChi" name="NgayChi" 
                                   value="<?= htmlspecialchars($input['NgayChi'] ?? date('Y-m-d')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="SoTien" class="form-label">Số tiền (₫) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="SoTien" name="SoTien" 
                                   placeholder="0" value="<?= htmlspecialchars($input['SoTien'] ?? '') ?>" 
                                   min="1" step="1000" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="NoiDung" class="form-label">Nội dung chi phí</label>
                        <textarea class="form-control" id="NoiDung" name="NoiDung" rows="4" 
                                  placeholder="Mô tả chi tiết về chi phí này..."><?= htmlspecialchars($input['NoiDung'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="TrangThai" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select class="form-select" id="TrangThai" name="TrangThai" required>
                            <option value="ChoDuyet" <?= ($input['TrangThai'] ?? '') === 'ChoDuyet' ? 'selected' : '' ?>>Chờ duyệt</option>
                            <option value="DaDuyet" <?= ($input['TrangThai'] ?? '') === 'DaDuyet' ? 'selected' : '' ?>>Đã duyệt</option>
                            <option value="Huy" <?= ($input['TrangThai'] ?? '') === 'Huy' ? 'selected' : '' ?>>Đã hủy</option>
                        </select>
                    </div>

                    <div class="btn-group-actions">
                        <button type="submit" class="btn btn-submit">
                            <i class="fa-solid fa-check me-2"></i>Cập nhật chi phí
                        </button>
                        <a href="index.php?controller=ketoan&action=quanLyChiPhi" class="btn btn-back">
                            <i class="fa-solid fa-times me-2"></i>Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
