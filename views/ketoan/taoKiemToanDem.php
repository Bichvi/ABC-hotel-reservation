<?php
// View tạo kiểm toán đêm mới

$user = $user ?? Auth::user();
$error = $error ?? '';
$success = $success ?? '';
$tongDoanhThu = $tongDoanhThu ?? 0;
$tongChiPhi = $tongChiPhi ?? 0;
$ngayHom = $ngayHom ?? date('Y-m-d');
$loiNhuan = $tongDoanhThu - $tongChiPhi;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo kiểm toán đêm - Kế toán</title>
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
        }

        .navbar {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-soft);
            padding: 1rem 2rem;
        }

        .navbar h4 {
            margin: 0;
            font-weight: 600;
        }

        .container-wrapper {
            padding: 2rem 1rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 0.75rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
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
        textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-soft);
            border-radius: 0.4rem;
            background: rgba(15, 23, 42, 0.5);
            color: var(--text-main);
            font-family: inherit;
            transition: all 0.3s;
        }

        input[type="date"]:focus,
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

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: rgba(30, 41, 59, 0.5);
            border-radius: 0.5rem;
            border: 1px solid var(--border-soft);
        }

        .info-item {
            text-align: center;
        }

        .info-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            font-weight: 600;
        }

        .info-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .info-value.positive {
            color: #10b981;
        }

        .info-value.negative {
            color: #ef4444;
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

        button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.4rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
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
    </style>
</head>
<body>
    <div class="navbar">
        <h4><i class="fas fa-moon"></i> Tạo kiểm toán đêm mới</h4>
    </div>

    <div class="container-wrapper">
        <div class="form-card">
            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <h2 class="form-title">Thông tin kiểm toán đêm</h2>

            <!-- Hiển thị thông tin từ hệ thống -->
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Tổng doanh thu hôm nay</div>
                    <div class="info-value"><?php echo number_format($tongDoanhThu, 0, ',', '.'); ?>đ</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tổng chi phí hôm nay</div>
                    <div class="info-value"><?php echo number_format($tongChiPhi, 0, ',', '.'); ?>đ</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Lợi nhuận ước tính</div>
                    <div class="info-value <?php echo $loiNhuan >= 0 ? 'positive' : 'negative'; ?>">
                        <?php echo number_format($loiNhuan, 0, ',', '.'); ?>đ
                    </div>
                </div>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label for="ngay">Ngày kiểm toán *</label>
                    <input type="date" id="ngay" name="ngay" required value="<?php echo $ngayHom; ?>">
                </div>

                <div class="form-group">
                    <label for="so_du_dau_ngay">Số dư đầu ngày (VNĐ) *</label>
                    <input type="number" id="so_du_dau_ngay" name="so_du_dau_ngay" required min="0" step="0.01" value="0">
                </div>

                <div class="form-group">
                    <label for="so_du_cuoi_ngay">Số dư cuối ngày (VNĐ) *</label>
                    <input type="number" id="so_du_cuoi_ngay" name="so_du_cuoi_ngay" required min="0" step="0.01" value="<?php echo $tongDoanhThu; ?>">
                </div>

                <div class="form-group">
                    <label for="tong_doanh_thu">Tổng doanh thu (VNĐ) *</label>
                    <input type="number" id="tong_doanh_thu" name="tong_doanh_thu" required min="0" step="0.01" value="<?php echo $tongDoanhThu; ?>">
                </div>

                <div class="form-group">
                    <label for="tong_chi_phi">Tổng chi phí (VNĐ) *</label>
                    <input type="number" id="tong_chi_phi" name="tong_chi_phi" required min="0" step="0.01" value="<?php echo $tongChiPhi; ?>">
                </div>

                <div class="form-group">
                    <label for="ghi_chu">Ghi chú</label>
                    <textarea id="ghi_chu" name="ghi_chu" placeholder="Nhập ghi chú hoặc bất kỳ thông tin cần thiết..."></textarea>
                </div>

                <div class="form-buttons">
                    <a href="index.php?controller=ketoan&action=kiemToanDem" class="btn-cancel">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Tạo kiểm toán
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
