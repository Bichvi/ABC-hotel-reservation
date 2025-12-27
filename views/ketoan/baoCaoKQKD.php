<?php
// View: Báo cáo kết quả kinh doanh (Income Statement)
$user = $user ?? Auth::user();
$kyKhoaSo = $kyKhoaSo ?? '';
$baoCao = $baoCao ?? [];
$error = $error ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo Cáo KQKD - Kế Toán</title>
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

        .income-statement {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            padding: 0;
            overflow: hidden;
        }

        .income-statement table {
            margin-bottom: 0;
        }

        .statement-row {
            border-bottom: 1px solid var(--border-soft);
        }

        .statement-row:last-child {
            border-bottom: none;
        }

        .label-column {
            padding: 18px 25px;
            font-weight: 500;
            width: 70%;
        }

        .value-column {
            padding: 18px 25px;
            text-align: right;
            font-weight: 600;
            font-size: 1.05rem;
            width: 30%;
        }

        .section-header {
            background: rgba(15, 23, 42, 0.8);
            padding: 15px 25px;
            font-weight: 700;
            color: var(--accent-blue);
            border-bottom: 2px solid var(--border-soft);
        }

        .total-row {
            background: rgba(59, 130, 246, 0.1);
            border-top: 2px solid var(--accent-blue);
            border-bottom: 2px solid var(--accent-blue);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .profit-row {
            background: linear-gradient(90deg, rgba(16, 185, 129, 0.2) 0%, rgba(16, 185, 129, 0.1) 100%);
            border: 2px solid var(--accent-green);
            padding: 20px 25px;
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--accent-green);
        }

        .profit-negative {
            background: linear-gradient(90deg, rgba(239, 68, 68, 0.2) 0%, rgba(239, 68, 68, 0.1) 100%) !important;
            border: 2px solid var(--accent-red) !important;
            color: var(--accent-red) !important;
        }

        .currency {
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 400;
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
                <i class="fas fa-chart-line me-2" style="color: #10b981;"></i>Báo Cáo KQKD
            </a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <a href="index.php?controller=ketoan&action=dashboard" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Quay lại Dashboard
                </a>
            <div class="ms-auto">
                <span style="color: #94a3b8; font-size: 0.9rem;">
                    <i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($user['Username'] ?? 'User') ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="container-fluid main-wrapper">
        <h1 class="page-title">
            <i class="fas fa-chart-line me-2"></i>Báo Cáo Kết Quả Kinh Doanh (KQKD)
        </h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <!-- Báo cáo KQKD -->
                <div class="report-header">
                    <div class="report-period">
                        <i class="fas fa-calendar-alt me-2"></i>Kỳ: <?= htmlspecialchars($kyKhoaSo) ?>
                    </div>
                    <div class="report-subtitle">
                        Báo cáo tình hình tài chính kinh doanh
                    </div>
                </div>

                <div class="income-statement">
                    <table class="w-100">
                        <!-- Doanh Thu -->
                        <tr class="statement-row">
                            <td class="section-header" colspan="2">
                                <i class="fas fa-arrow-up me-2" style="color: var(--accent-green);"></i>DOANH THU
                            </td>
                        </tr>
                        <tr class="statement-row">
                            <td class="label-column">Doanh thu hoạt động kinh doanh</td>
                            <td class="value-column">
                                <?= number_format($baoCao['tongDoanhThu'] ?? 0, 0, ',', '.') ?>
                                <span class="currency">₫</span>
                            </td>
                        </tr>

                        <!-- Tổng Doanh Thu -->
                        <tr class="statement-row total-row">
                            <td class="label-column">
                                <i class="fas fa-plus-circle me-2"></i>Cộng Doanh Thu
                            </td>
                            <td class="value-column">
                                <?= number_format($baoCao['tongDoanhThu'] ?? 0, 0, ',', '.') ?>
                                <span class="currency">₫</span>
                            </td>
                        </tr>

                        <!-- Chi Phí -->
                        <tr class="statement-row">
                            <td class="section-header" colspan="2">
                                <i class="fas fa-arrow-down me-2" style="color: var(--accent-red);"></i>CHI PHÍ
                            </td>
                        </tr>
                        <tr class="statement-row">
                            <td class="label-column">Chi phí hoạt động</td>
                            <td class="value-column">
                                <?= number_format($baoCao['tongChiPhi'] ?? 0, 0, ',', '.') ?>
                                <span class="currency">₫</span>
                            </td>
                        </tr>

                        <!-- Tổng Chi Phí -->
                        <tr class="statement-row total-row">
                            <td class="label-column">
                                <i class="fas fa-minus-circle me-2"></i>Cộng Chi Phí
                            </td>
                            <td class="value-column">
                                <?= number_format($baoCao['tongChiPhi'] ?? 0, 0, ',', '.') ?>
                                <span class="currency">₫</span>
                            </td>
                        </tr>

                        <!-- Lợi Nhuận Ròng -->
                        <tr class="statement-row">
                            <td colspan="2" class="p-0">
                                <div class="profit-row <?= ($baoCao['loiNhuanRong'] ?? 0) < 0 ? 'profit-negative' : '' ?>">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <i class="fas fa-chart-pie me-2"></i>LỢI NHUẬN RÒNG
                                        </div>
                                        <div>
                                            <?= number_format($baoCao['loiNhuanRong'] ?? 0, 0, ',', '.') ?>
                                            <span class="currency">₫</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Hành động -->
                <div class="action-buttons">
                    <button class="btn btn-custom btn-print" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>In báo cáo
                    </button>
                    <a href="index.php?controller=ketoan&action=xuatKQKD&ky=<?= urlencode($kyKhoaSo) ?>" 
                       class="btn btn-custom btn-export">
                        <i class="fas fa-download me-2"></i>Xuất Excel
                    </a>
                </div>
            </div>

            <!-- Thống kê bên phải -->
            <div class="col-lg-4">
                <div style="background: rgba(30, 41, 59, 0.8); border-radius: 8px; padding: 20px; border: 1px solid var(--border-soft);">
                    <h5 style="color: var(--accent-blue); margin-bottom: 20px;">
                        <i class="fas fa-info-circle me-2"></i>Thống Kê Nhanh
                    </h5>

                    <div style="margin-bottom: 20px;">
                        <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 5px;">Doanh Thu</div>
                        <div style="font-size: 1.3rem; font-weight: 700; color: var(--accent-green);">
                            <?= number_format($baoCao['tongDoanhThu'] ?? 0, 0, ',', '.') ?> ₫
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 5px;">Chi Phí</div>
                        <div style="font-size: 1.3rem; font-weight: 700; color: var(--accent-red);">
                            <?= number_format($baoCao['tongChiPhi'] ?? 0, 0, ',', '.') ?> ₫
                        </div>
                    </div>

                    <div style="border-top: 2px solid var(--border-soft); padding-top: 15px;">
                        <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 5px;">Lợi Nhuận</div>
                        <div style="font-size: 1.3rem; font-weight: 700; color: <?= ($baoCao['loiNhuanRong'] ?? 0) < 0 ? 'var(--accent-red)' : 'var(--accent-green)' ?>;">
                            <?= number_format($baoCao['loiNhuanRong'] ?? 0, 0, ',', '.') ?> ₫
                        </div>
                    </div>

                    <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid var(--border-soft); font-size: 0.85rem; color: var(--text-muted);">
                        <strong>Tỷ suất lợi nhuận:</strong><br>
                        <?php 
                        $margin = $baoCao['tongDoanhThu'] > 0 
                            ? (($baoCao['loiNhuanRong'] ?? 0) / $baoCao['tongDoanhThu'] * 100)
                            : 0;
                        ?>
                        <div style="font-size: 1.2rem; color: var(--accent-blue); font-weight: 700; margin-top: 5px;">
                            <?= number_format($margin, 2) ?>%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
