<?php
// View: Báo cáo lưu chuyển tiền tệ (Cash Flow Statement)
$user = $user ?? Auth::user();
$kyKhoaSo = $kyKhoaSo ?? '';
$luuChuyenTien = $luuChuyenTien ?? [];
$error = $error ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo Cáo Lưu Chuyển Tiền Tệ - Kế Toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css" rel="stylesheet">
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
            --accent-purple: #a855f7;
            --accent-orange: #f97316;
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

        .report-header {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.15) 0%, rgba(16, 185, 129, 0.15) 100%);
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
            border-left: 4px solid var(--accent-green);
        }

        .report-period {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent-blue);
            margin-bottom: 10px;
        }

        .report-subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .cash-flow-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .activity-card {
            background: rgba(30, 41, 59, 0.8);
            border: 2px solid var(--border-soft);
            border-radius: 8px;
            padding: 25px;
            transition: all 0.3s ease;
        }

        .activity-card:hover {
            border-color: var(--accent-blue);
            box-shadow: 0 0 20px rgba(14, 165, 233, 0.2);
        }

        .activity-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .activity-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--text-main);
        }

        .activity-description {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .method-list {
            display: grid;
            gap: 10px;
        }

        .method-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: rgba(15, 23, 42, 0.8);
            border-radius: 6px;
            border-left: 3px solid;
            font-size: 0.95rem;
        }

        .method-tienmат {
            border-left-color: #fbbf24;
        }

        .method-chuyenkhoан {
            border-left-color: #38bdf8;
        }

        .method-the {
            border-left-color: #a78bfa;
        }

        .method-vidiental {
            border-left-color: #34d399;
        }

        .method-value {
            font-weight: 700;
            color: var(--text-main);
        }

        .activity-total {
            padding-top: 20px;
            border-top: 2px solid var(--border-soft);
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-label {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .total-value {
            font-weight: 700;
            font-size: 1.2rem;
        }

        .activity-operating .activity-icon {
            color: var(--accent-green);
        }

        .activity-operating .activity-total .total-value {
            color: var(--accent-green);
        }

        .activity-investing .activity-icon {
            color: var(--accent-blue);
        }

        .activity-investing .activity-total .total-value {
            color: var(--accent-blue);
        }

        .activity-financing .activity-icon {
            color: var(--accent-purple);
        }

        .activity-financing .activity-total .total-value {
            color: var(--accent-purple);
        }

        .chart-container {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            height: 400px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-custom {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-print {
            background: var(--accent-blue);
            color: white;
        }

        .btn-print:hover {
            background: #0284c7;
            color: white;
        }

        .btn-export {
            background: var(--accent-green);
            color: white;
        }

        .btn-export:hover {
            background: #059669;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-water me-2" style="color: #10b981;"></i>Báo Cáo Lưu Chuyển Tiền Tệ
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
        <h1 class="page-title">
            <i class="fas fa-water me-2"></i>Báo Cáo Lưu Chuyển Tiền Tệ (Cash Flow)
        </h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="report-header">
            <div class="report-period">
                <i class="fas fa-calendar-alt me-2"></i>Kỳ: <?= htmlspecialchars($kyKhoaSo) ?>
            </div>
            <div class="report-subtitle">
                Báo cáo chi tiết lưu chuyển tiền từ các hoạt động kinh doanh, đầu tư và tài chính
            </div>
        </div>

        <!-- Các hoạt động -->
        <div class="cash-flow-container">
            <!-- Hoạt động kinh doanh -->
            <div class="activity-card activity-operating">
                <div class="activity-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="activity-title">Hoạt Động Kinh Doanh</div>
                <div class="activity-description">
                    Tiền từ hoạt động chính của doanh nghiệp
                </div>
                <div class="method-list">
                    <div class="method-item method-tienmат">
                        <span><i class="fas fa-coins me-2"></i>Tiền Mặt</span>
                        <span class="method-value">
                            <?= number_format($luuChuyenTien['kinhdoanh']['tienmат'] ?? 0, 0, ',', '.') ?> ₫
                        </span>
                    </div>
                    <div class="method-item method-chuyenkhoан">
                        <span><i class="fas fa-university me-2"></i>Chuyển Khoản</span>
                        <span class="method-value">
                            <?= number_format($luuChuyenTien['kinhdoanh']['chuyenkhoан'] ?? 0, 0, ',', '.') ?> ₫
                        </span>
                    </div>
                    <div class="method-item method-the">
                        <span><i class="fas fa-credit-card me-2"></i>Thẻ</span>
                        <span class="method-value">
                            <?= number_format($luuChuyenTien['kinhdoanh']['the'] ?? 0, 0, ',', '.') ?> ₫
                        </span>
                    </div>
                    <div class="method-item method-vidiental">
                        <span><i class="fas fa-mobile-alt me-2"></i>Ví Điện Tử</span>
                        <span class="method-value">
                            <?= number_format($luuChuyenTien['kinhdoanh']['vidiental'] ?? 0, 0, ',', '.') ?> ₫
                        </span>
                    </div>
                </div>
                <div class="activity-total">
                    <div class="total-label">Tổng Cộng</div>
                    <div class="total-value">
                        <?= number_format($luuChuyenTien['kinhdoanh']['total'] ?? 0, 0, ',', '.') ?> ₫
                    </div>
                </div>
            </div>

            <!-- Hoạt động đầu tư -->
            <div class="activity-card activity-investing">
                <div class="activity-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="activity-title">Hoạt Động Đầu Tư</div>
                <div class="activity-description">
                    Tiền từ hoạt động mua bán tài sản cố định
                </div>
                <div class="method-list">
                    <div class="method-item method-tienmат">
                        <span><i class="fas fa-coins me-2"></i>Tiền Mặt</span>
                        <span class="method-value">
                            <?= number_format($luuChuyenTien['dautut']['tienmат'] ?? 0, 0, ',', '.') ?> ₫
                        </span>
                    </div>
                    <div class="method-item method-chuyenkhoан">
                        <span><i class="fas fa-university me-2"></i>Chuyển Khoản</span>
                        <span class="method-value">
                            <?= number_format($luuChuyenTien['dautut']['chuyenkhoан'] ?? 0, 0, ',', '.') ?> ₫
                        </span>
                    </div>
                    <div class="method-item method-the">
                        <span><i class="fas fa-credit-card me-2"></i>Thẻ</span>
                        <span class="method-value">
                            <?= number_format($luuChuyenTien['dautut']['the'] ?? 0, 0, ',', '.') ?> ₫
                        </span>
                    </div>
                    <div class="method-item method-vidiental">
                        <span><i class="fas fa-mobile-alt me-2"></i>Ví Điện Tử</span>
                        <span class="method-value">
                            <?= number_format($luuChuyenTien['dautut']['vidiental'] ?? 0, 0, ',', '.') ?> ₫
                        </span>
                    </div>
                </div>
                <div class="activity-total">
                    <div class="total-label">Tổng Cộng</div>
                    <div class="total-value">
                        <?= number_format($luuChuyenTien['dautut']['total'] ?? 0, 0, ',', '.') ?> ₫
                    </div>
                </div>
            </div>

            <!-- Hoạt động tài chính -->
            <div class="activity-card activity-financing">
                <div class="activity-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div class="activity-title">Hoạt Động Tài Chính</div>
                <div class="activity-description">
                    Tiền từ vay, trả nợ, phát hành cổ phiếu, cổ tức
                </div>
                <div class="method-list">
                    <div class="method-item method-tienmат">
                        <span><i class="fas fa-coins me-2"></i>Tiền Mặt</span>
                        <span class="method-value">
                            <?= number_format($luuChuyenTien['taichinh']['tienmат'] ?? 0, 0, ',', '.') ?> ₫
                        </span>
                    </div>
                    <div class="method-item method-chuyenkhoан">
                        <span><i class="fas fa-university me-2"></i>Chuyển Khoản</span>
                        <span class="method-value">
                            <?= number_format($luuChuyenTien['taichinh']['chuyenkhoан'] ?? 0, 0, ',', '.') ?> ₫
                        </span>
                    </div>
                    <div class="method-item method-the">
                        <span><i class="fas fa-credit-card me-2"></i>Thẻ</span>
                        <span class="method-value">
                            <?= number_format($luuChuyenTien['taichinh']['the'] ?? 0, 0, ',', '.') ?> ₫
                        </span>
                    </div>
                    <div class="method-item method-vidiental">
                        <span><i class="fas fa-mobile-alt me-2"></i>Ví Điện Tử</span>
                        <span class="method-value">
                            <?= number_format($luuChuyenTien['taichinh']['vidiental'] ?? 0, 0, ',', '.') ?> ₫
                        </span>
                    </div>
                </div>
                <div class="activity-total">
                    <div class="total-label">Tổng Cộng</div>
                    <div class="total-value">
                        <?= number_format($luuChuyenTien['taichinh']['total'] ?? 0, 0, ',', '.') ?> ₫
                    </div>
                </div>
            </div>
        </div>

        <!-- Tóm tắt tổng thể -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(16, 185, 129, 0.1) 100%); border: 2px solid var(--accent-green); border-radius: 8px; padding: 20px;">
                <div style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 10px;">Lưu chuyển từ KD</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-green);">
                    <?= number_format($luuChuyenTien['kinhdoanh']['total'] ?? 0, 0, ',', '.') ?> ₫
                </div>
            </div>
            <div style="background: linear-gradient(135deg, rgba(14, 165, 233, 0.2) 0%, rgba(14, 165, 233, 0.1) 100%); border: 2px solid var(--accent-blue); border-radius: 8px; padding: 20px;">
                <div style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 10px;">Lưu chuyển từ ĐT</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-blue);">
                    <?= number_format($luuChuyenTien['dautut']['total'] ?? 0, 0, ',', '.') ?> ₫
                </div>
            </div>
            <div style="background: linear-gradient(135deg, rgba(168, 85, 247, 0.2) 0%, rgba(168, 85, 247, 0.1) 100%); border: 2px solid var(--accent-purple); border-radius: 8px; padding: 20px;">
                <div style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 10px;">Lưu chuyển từ TC</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-purple);">
                    <?= number_format($luuChuyenTien['taichinh']['total'] ?? 0, 0, ',', '.') ?> ₫
                </div>
            </div>
        </div>

        <!-- Hành động -->
        <div class="action-buttons">
            <button class="btn btn-custom btn-print" onclick="window.print()">
                <i class="fas fa-print me-2"></i>In báo cáo
            </button>
            <a href="index.php?controller=ketoan&action=xuatLuuChuyenTien&ky=<?= urlencode($kyKhoaSo) ?>" 
               class="btn btn-custom btn-export">
                <i class="fas fa-download me-2"></i>Xuất Excel
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
