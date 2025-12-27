<?php
// View: Chi tiết giao dịch
$user = $user ?? Auth::user();
$giaoDich = $giaoDich ?? [];
$error = $error ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Giao Dịch - Kế Toán</title>
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
            --accent-red: #ef4444;
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

        .main-wrapper {
            padding: 30px 20px;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: var(--accent-blue);
        }

        .detail-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .detail-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid var(--border-soft);
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .detail-value {
            font-size: 1.1rem;
            color: var(--text-main);
            font-weight: 500;
        }

        .detail-value.amount {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--accent-green);
        }

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--accent-blue);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            color: #0284c7;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-receipt me-2" style="color: #10b981;"></i>Chi Tiết Giao Dịch
            </a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <a href="index.php?controller=ketoan&action=dashboard" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Quay lại Dashboard
                </a>
                <span style="color: #94a3b8; font-size: 0.9rem;">
                    <i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($user['Username'] ?? 'User') ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="container-fluid main-wrapper">
        <a href="javascript:history.back()" class="back-button">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>

        <h1 class="page-title">
            <i class="fas fa-receipt me-2"></i>Chi Tiết Giao Dịch
        </h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($giaoDich)): ?>
            <div class="detail-card">
                <div class="detail-row">
                    <div class="detail-item">
                        <div class="detail-label">Mã Giao Dịch</div>
                        <div class="detail-value"><?= htmlspecialchars($giaoDich['MaGiaoDich'] ?? 'N/A') ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Ngày Giao Dịch</div>
                        <div class="detail-value"><?= isset($giaoDich['NgayGiaoDich']) ? date('d/m/Y H:i', strtotime($giaoDich['NgayGiaoDich'])) : 'N/A' ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Khách Hàng</div>
                        <div class="detail-value"><?= htmlspecialchars($giaoDich['TenKhachHang'] ?? 'N/A') ?></div>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-item">
                        <div class="detail-label">Diễn Giải</div>
                        <div class="detail-value"><?= htmlspecialchars($giaoDich['DienGiai'] ?? 'N/A') ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Phương Thức Thanh Toán</div>
                        <div class="detail-value">
                            <?php
                            $methods = [
                                'TienMat' => 'Tiền Mặt',
                                'ChuyenKhoan' => 'Chuyển Khoản',
                                'The' => 'Thẻ',
                                'ViDienTu' => 'Ví Điện Tử'
                            ];
                            $method = $giaoDich['PhuongThucThanhToan'] ?? 'TienMat';
                            echo $methods[$method] ?? $method;
                            ?>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Số Tiền</div>
                        <div class="detail-value amount">
                            <?= number_format($giaoDich['SoTien'] ?? 0, 0, ',', '.') ?> ₫
                        </div>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-item">
                        <div class="detail-label">Trạng Thái Đối Soát</div>
                        <div class="detail-value">
                            <?php if ($giaoDich['DaDoiSoat'] ?? 0): ?>
                                <span style="color: var(--accent-green);">
                                    <i class="fas fa-check-circle me-2"></i>Đã Đối Soát
                                </span>
                            <?php else: ?>
                                <span style="color: var(--accent-red);">
                                    <i class="fas fa-times-circle me-2"></i>Chưa Đối Soát
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Ngày Đối Soát</div>
                        <div class="detail-value">
                            <?= isset($giaoDich['NgayDoiSoat']) ? date('d/m/Y H:i', strtotime($giaoDich['NgayDoiSoat'])) : 'Chưa đối soát' ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div style="background: rgba(30, 41, 59, 0.8); border: 1px solid var(--border-soft); border-radius: 8px; padding: 60px 20px; text-align: center; color: var(--text-muted);">
                <i class="fas fa-inbox" style="font-size: 3rem; opacity: 0.3; display: block; margin-bottom: 20px;"></i>
                <h5>Không tìm thấy giao dịch</h5>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
