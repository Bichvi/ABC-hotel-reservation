<?php
// View sửa kiểm toán đêm

$user = $user ?? Auth::user();
$error = $error ?? '';
$kiemToan = $kiemToan ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa kiểm toán đêm - Kế toán</title>
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

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            color: var(--text-main);
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: linear-gradient(90deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%) !important;
            border-bottom: 1px solid var(--border-soft);
        }

        .brand-logo {
            font-weight: 700;
            letter-spacing: 0.5px;
            font-size: 0.95rem;
            color: var(--accent-blue) !important;
        }

        .container-wrapper {
            padding: 30px 20px 40px;
            flex: 1;
            max-width: 800px;
            margin: 0 auto;
        }

        .form-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(15, 23, 42, 0.8) 100%);
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .info-badge {
            display: inline-block;
            background: rgba(14, 165, 233, 0.2);
            color: var(--accent-blue);
            padding: 0.5rem 1rem;
            border-radius: 0.3rem;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-main);
        }

        input[type="date"],
        input[type="number"],
        input[type="text"],
        textarea,
        input[readonly] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-soft);
            border-radius: 0.4rem;
            background: rgba(15, 23, 42, 0.5);
            color: var(--text-main);
            font-family: inherit;
            transition: all 0.3s;
        }

        input[readonly] {
            background: rgba(15, 23, 42, 0.3);
            color: var(--text-muted);
            cursor: not-allowed;
        }

        input[type="number"]:focus,
        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border-color: #ef4444;
        }

        .form-buttons {
            display: flex;
            gap: 1rem;
            justify-content: space-between;
            margin-top: 2rem;
        }

        button, a.btn-cancel {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.4rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-submit {
            background: var(--accent-green);
            color: white;
            flex: 1;
        }

        .btn-submit:hover {
            background: #059669;
        }

        .btn-cancel {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border-soft);
        }

        .btn-cancel:hover {
            background: rgba(148, 163, 184, 0.1);
            color: var(--text-main);
        }

        .footer {
            background: linear-gradient(90deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
            border-top: 1px solid var(--border-soft);
            padding: 20px;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <!-- Header/Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand brand-logo" href="#">
                <i class="fa-solid fa-hotel me-2"></i>ABC Resort - Kế toán
            </a>
            <div class="navbar-collapse ms-auto">
                <div class="d-flex align-items-center gap-3">
                    <span class="small text-secondary">
                        <i class="fa-regular fa-user me-1"></i><?= htmlspecialchars($user['Username'] ?? 'N/A') ?>
                    </span>
                    <a href="index.php?controller=ketoan&action=dashboard" class="btn btn-outline-light btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-wrapper">
        <div class="form-card">
            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <h2 class="form-title"><i class="fas fa-moon me-2"></i>Sửa kiểm toán đêm</h2>
            
            <div class="info-badge">
                <i class="fas fa-calendar"></i> Kiểm toán ngày <?php echo date('d/m/Y', strtotime($kiemToan['NgayKTD'])); ?>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label for="ngay_ktd">Ngày kiểm toán</label>
                    <input type="date" id="ngay_ktd" readonly value="<?php echo $kiemToan['NgayKTD']; ?>">
                </div>

                <div class="form-group">
                    <label for="so_du_dau_ngay">Số dư đầu ngày (VNĐ) *</label>
                    <input type="number" id="so_du_dau_ngay" name="so_du_dau_ngay" required min="0" step="0.01" value="<?php echo $kiemToan['SoDuDauNgay']; ?>">
                </div>

                <div class="form-group">
                    <label for="so_du_cuoi_ngay">Số dư cuối ngày (VNĐ) *</label>
                    <input type="number" id="so_du_cuoi_ngay" name="so_du_cuoi_ngay" required min="0" step="0.01" value="<?php echo $kiemToan['SoDuCuoiNgay']; ?>">
                </div>

                <div class="form-group">
                    <label for="tong_doanh_thu">Tổng doanh thu (VNĐ) *</label>
                    <input type="number" id="tong_doanh_thu" name="tong_doanh_thu" required min="0" step="0.01" value="<?php echo $kiemToan['TongDoanhThu']; ?>">
                </div>

                <div class="form-group">
                    <label for="tong_chi_phi">Tổng chi phí (VNĐ) *</label>
                    <input type="number" id="tong_chi_phi" name="tong_chi_phi" required min="0" step="0.01" value="<?php echo $kiemToan['TongChiPhi']; ?>">
                </div>

                <div class="form-group">
                    <label for="ghi_chu">Ghi chú</label>
                    <textarea id="ghi_chu" name="ghi_chu" placeholder="Nhập ghi chú hoặc bất kỳ thông tin cần thiết..."><?php echo htmlspecialchars($kiemToan['GhiChu'] ?? ''); ?></textarea>
                </div>

                <div class="form-buttons">
                    <a href="index.php?controller=ketoan&action=kiemToanDem" class="btn-cancel">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        ABC Resort – Bộ phận Kế toán | Quản lý kiểm toán đêm
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
