<?php
// View sửa doanh thu (giao dịch)

$user = $user ?? Auth::user();
$error = $error ?? '';
$success = $success ?? '';
$input = $input ?? [];
$maGiaoDich = $maGiaoDich ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa doanh thu - Kế toán</title>
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
            --accent-green: #10b981;
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
            background: linear-gradient(135deg, var(--accent-green), #16a34a);
            color: #fff;
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
            padding: 16px;
            margin-bottom: 20px;
            color: #93c5fd;
            font-size: 0.9rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }

        .info-item {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(148, 163, 184, 0.25);
            padding: 12px;
            border-radius: 6px;
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: #93c5fd;
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
        <div class="col-md-9">
            <a href="index.php?controller=ketoan&action=quanLyDoanhThu" class="btn-back mb-4">
                <i class="fa-solid fa-chevron-left me-2"></i>Quay lại danh sách
            </a>

            <div class="form-card p-4">
                <h2 class="form-title">
                    <i class="fa-solid fa-edit me-2" style="color: #10b981;"></i>Sửa doanh thu
                </h2>
                <p class="form-subtitle">Cập nhật thông tin giao dịch #<?= htmlspecialchars($maGiaoDich) ?></p>

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
                    ID Giao dịch: <strong>#<?= htmlspecialchars($maGiaoDich) ?></strong>
                </div>

                <!-- Thông tin giao dịch (chỉ đọc) -->
                <div class="info-grid mb-4">
                    <div class="info-item">
                        <div class="info-label">Ngày giao dịch</div>
                        <div class="info-value"><?= htmlspecialchars($input['NgayGiaoDich'] ?? 'N/A') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Loại giao dịch</div>
                        <div class="info-value"><?= htmlspecialchars($input['LoaiGiaoDich'] ?? 'N/A') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tên khách</div>
                        <div class="info-value"><?= htmlspecialchars($input['TenKhach'] ?? 'N/A') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Số phòng</div>
                        <div class="info-value"><?= htmlspecialchars($input['SoPhong'] ?? 'N/A') ?></div>
                    </div>
                </div>

                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <label for="TongTien" class="form-label">Tổng tiền (₫) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="TongTien" name="TongTien" 
                               placeholder="0" value="<?= htmlspecialchars($input['TongTien'] ?? '') ?>" 
                               min="0" step="1000" required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="TrangThai" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select" id="TrangThai" name="TrangThai" required>
                                <option value="">Chọn trạng thái</option>
                                <option value="Moi" <?= ($input['TrangThai'] ?? '') === 'Moi' ? 'selected' : '' ?>>Mới</option>
                                <option value="Booked" <?= ($input['TrangThai'] ?? '') === 'Booked' ? 'selected' : '' ?>>Đã đặt</option>
                                <option value="Stayed" <?= ($input['TrangThai'] ?? '') === 'Stayed' ? 'selected' : '' ?>>Đã ở</option>
                                <option value="Paid" <?= ($input['TrangThai'] ?? '') === 'Paid' ? 'selected' : '' ?>>Đã thanh toán</option>
                                <option value="DaHuy" <?= ($input['TrangThai'] ?? '') === 'DaHuy' ? 'selected' : '' ?>>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="PhuongThucThanhToan" class="form-label">Phương thức thanh toán</label>
                            <select class="form-select" id="PhuongThucThanhToan" name="PhuongThucThanhToan">
                                <option value="ChuaThanhToan" <?= ($input['PhuongThucThanhToan'] ?? '') === 'ChuaThanhToan' ? 'selected' : '' ?>>Chưa thanh toán</option>
                                <option value="TienMat" <?= ($input['PhuongThucThanhToan'] ?? '') === 'TienMat' ? 'selected' : '' ?>>Tiền mặt</option>
                                <option value="The" <?= ($input['PhuongThucThanhToan'] ?? '') === 'The' ? 'selected' : '' ?>>Thẻ</option>
                                <option value="ChuyenKhoan" <?= ($input['PhuongThucThanhToan'] ?? '') === 'ChuyenKhoan' ? 'selected' : '' ?>>Chuyển khoản</option>
                                <option value="ViDienTu" <?= ($input['PhuongThucThanhToan'] ?? '') === 'ViDienTu' ? 'selected' : '' ?>>Ví điện tử</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="GhiChu" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="GhiChu" name="GhiChu" rows="4" 
                                  placeholder="Ghi chú thêm về giao dịch này..."><?= htmlspecialchars($input['GhiChu'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="LySua" class="form-label">Lý do sửa <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="LySua" name="LySua" rows="3" 
                                  placeholder="Vui lòng nhập lý do sửa giao dịch này..." required></textarea>
                        <small class="text-muted d-block mt-2">
                            <i class="fa-solid fa-info-circle me-1"></i>Trường này bắt buộc và sẽ được ghi nhận trong lịch sử sửa giao dịch
                        </small>
                    </div>

                    <div class="btn-group-actions">
                        <button type="submit" class="btn btn-submit">
                            <i class="fa-solid fa-check me-2"></i>Cập nhật doanh thu
                        </button>
                        <a href="index.php?controller=ketoan&action=quanLyDoanhThu" class="btn btn-back">
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
