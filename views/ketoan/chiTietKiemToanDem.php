<?php
// View chi tiết kiểm toán đêm

$user = $user ?? Auth::user();
$kiemToan = $kiemToan ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết kiểm toán đêm - Kế toán</title>
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
            max-width: 900px;
            margin: 0 auto;
        }

        .detail-card {
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 0.75rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .detail-header h2 {
            margin: 0 0 0.5rem 0;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .detail-header .date-info {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-item {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid var(--border-soft);
            border-radius: 0.5rem;
            padding: 1.5rem;
        }

        .info-label {
            display: block;
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .info-value.positive {
            color: #10b981;
        }

        .info-value.negative {
            color: #ef4444;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: var(--accent-blue);
        }

        .ghi-chu-box {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid var(--border-soft);
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            line-height: 1.6;
            color: var(--text-main);
        }

        .ghi-chu-box.empty {
            color: var(--text-muted);
            font-style: italic;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(30, 41, 59, 0.5);
            border-radius: 0.5rem;
            border: 1px solid var(--border-soft);
            flex-wrap: wrap;
            gap: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .meta-item i {
            color: var(--accent-blue);
            width: 20px;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        a {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.4rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }

        .btn-edit {
            background: var(--accent-blue);
            color: white;
        }

        .btn-edit:hover {
            background: #0284c7;
        }

        .btn-back {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border-soft);
        }

        .btn-back:hover {
            background: rgba(148, 163, 184, 0.1);
            color: var(--text-main);
        }

        .status-badge {
            display: inline-block;
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            padding: 0.5rem 1rem;
            border-radius: 0.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
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
        <div class="detail-card">
            <div class="detail-header">
                <h2>Kiểm toán đêm #<?php echo htmlspecialchars($kiemToan['MaKTD']); ?></h2>
                <div class="date-info">
                    <i class="fas fa-calendar"></i> 
                    <?php echo date('d/m/Y', strtotime($kiemToan['NgayKTD'])); ?>
                </div>
                <div style="margin-top: 0.5rem;">
                    <span class="status-badge">
                        <i class="fas fa-check-circle"></i> Đã kiểm toán
                    </span>
                </div>
            </div>

            <!-- Thông tin tài chính chính -->
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Số dư đầu ngày</span>
                    <span class="info-value"><?php echo number_format($kiemToan['SoDuDauNgay'], 0, ',', '.'); ?>đ</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Số dư cuối ngày</span>
                    <span class="info-value"><?php echo number_format($kiemToan['SoDuCuoiNgay'], 0, ',', '.'); ?>đ</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tổng doanh thu</span>
                    <span class="info-value positive"><?php echo number_format($kiemToan['TongDoanhThu'], 0, ',', '.'); ?>đ</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tổng chi phí</span>
                    <span class="info-value"><?php echo number_format($kiemToan['TongChiPhi'], 0, ',', '.'); ?>đ</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Lợi nhuận</span>
                    <span class="info-value <?php echo $kiemToan['LoiNhuan'] >= 0 ? 'positive' : 'negative'; ?>">
                        <?php echo number_format($kiemToan['LoiNhuan'], 0, ',', '.'); ?>đ
                    </span>
                </div>
            </div>

            <!-- Ghi chú -->
            <?php if (!empty($kiemToan['GhiChu'])): ?>
            <div class="section-title">Ghi chú</div>
            <div class="ghi-chu-box">
                <?php echo nl2br(htmlspecialchars($kiemToan['GhiChu'])); ?>
            </div>
            <?php else: ?>
            <div class="section-title">Ghi chú</div>
            <div class="ghi-chu-box empty">
                <i class="fas fa-info-circle"></i> Không có ghi chú
            </div>
            <?php endif; ?>

            <!-- Thông tin meta -->
            <div class="meta-info">
                <div class="meta-item">
                    <i class="fas fa-user"></i>
                    <span>Kiểm toán bởi: <strong><?php echo htmlspecialchars($kiemToan['TenNguoiDung'] ?? 'N/A'); ?></strong></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    <span>Thời gian: <strong><?php echo date('d/m/Y H:i', strtotime($kiemToan['ThoiGianTao'])); ?></strong></span>
                </div>
            </div>

            <!-- Nút hành động -->
            <div class="action-buttons">
                <a href="index.php?controller=ketoan&action=dashboard" class="btn-back">
                    <i class="fas fa-home"></i> Quay lại dashboard
                </a>
                <a href="index.php?controller=ketoan&action=kiemToanDem" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
                <a href="index.php?controller=ketoan&action=suaKiemToanDem&id=<?php echo $kiemToan['MaKTD']; ?>" class="btn-edit">
                    <i class="fas fa-edit"></i> Chỉnh sửa
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        ABC Resort – Bộ phận Kế toán | Quản lý kiểm toán đêm
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
