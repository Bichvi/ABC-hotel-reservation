<?php
// View tạo báo cáo doanh thu / chi phí

$user       = $user       ?? Auth::user();
$error      = $error      ?? '';
$success    = $success    ?? '';
$noData     = $noData     ?? false;
$input      = $input      ?? [];
$dataBaoCao = $dataBaoCao ?? null;
$daGuiForm  = $daGuiForm  ?? false;

// Giá trị mặc định
$loaiBaoCao = $input['loai_baocao'] ?? 'doanhthu';
$kyHan      = $input['ky_han']      ?? 'thang';
$tuNgay     = $input['tu_ngay']     ?? date('Y-m-01');
$denNgay    = $input['den_ngay']    ?? date('Y-m-t');
$dinhDang   = $input['dinh_dang']   ?? 'html';

// Giữ nguyên, dù hiện tại không dùng trực tiếp
$loiNhuan   = isset($dataBaoCao['loi_nhuan']) ? $dataBaoCao['loi_nhuan'] : 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo báo cáo doanh thu / chi phí - Kế toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-main: #020617;
            --bg-card: rgba(15, 23, 42, 0.96);
            --bg-card-soft: rgba(15, 23, 42, 0.9);
            --border-soft: rgba(148, 163, 184, 0.35);
            --border-strong: rgba(30, 64, 175, 0.8);
            --text-main: #e5e7eb;
            --text-muted: #9ca3af;
            --accent-blue: #38bdf8;
            --accent-green: #22c55e;
            --accent-amber: #fbbf24;
            --danger: #f97373;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, 0.38), transparent 60%),
                radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.35), transparent 55%),
                linear-gradient(135deg, #020617, #020617);
            min-height: 100vh;
            color: var(--text-main);
        }

        .navbar {
            background: rgba(15, 23, 42, 0.98) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(51, 65, 85, 0.9);
        }

        .brand-logo {
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-size: 0.95rem;
        }

        .brand-logo i {
            font-size: 1.1rem;
        }

        .main-wrapper {
            padding: 28px 0 40px;
        }

        .card-glass {
            border-radius: 18px;
            border: 1px solid var(--border-soft);
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, 0.32), transparent 55%),
                radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.25), transparent 55%),
                linear-gradient(145deg, rgba(15, 23, 42, 0.95), rgba(15, 23, 42, 0.98));
            color: var(--text-main);
            box-shadow:
                0 20px 40px rgba(0, 0, 0, 0.7),
                0 0 0 1px rgba(15, 23, 42, 0.8);
        }

        .card-glass-2 {
            border-radius: 20px;
            border: 1px solid rgba(30, 64, 175, 0.45);
            background: radial-gradient(circle at top right, rgba(56, 189, 248, 0.12), transparent 55%),
                        linear-gradient(145deg, rgba(15, 23, 42, 0.98), rgba(15, 23, 42, 0.96));
            box-shadow:
                0 24px 50px rgba(0, 0, 0, 0.8),
                0 0 0 1px rgba(15, 23, 42, 0.9);
        }

        .section-title {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: .22em;
            color: var(--text-muted);
        }

        .form-control, .form-select {
            background: rgba(15, 23, 42, 0.92);
            border: 1px solid rgba(148, 163, 184, .5);
            color: var(--text-main);
            font-size: 0.85rem;
        }

        .form-control::placeholder {
            color: rgba(148, 163, 184, 0.75);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.55);
            background: rgba(15, 23, 42, 0.98);
            color: var(--text-main);
        }

        .badge-soft {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.85), rgba(56, 189, 248, 0.9));
            color: #eff6ff;
            border-radius: 999px;
            padding: 4px 14px;
            font-size: 0.7rem;
            box-shadow: 0 0 0 1px rgba(191, 219, 254, 0.2);
        }

        .btn-primary-gradient {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-green));
            color: #0b1120;
            font-weight: 600;
            border: none;
            border-radius: 999px;
            padding: 10px 20px;
            font-size: 0.87rem;
            box-shadow:
                0 10px 25px rgba(34, 197, 94, 0.35),
                0 0 0 1px rgba(15, 23, 42, 0.8);
        }

        .btn-primary-gradient:hover {
            filter: brightness(1.07);
            transform: translateY(-1px);
        }

        .btn-outline-soft {
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.7);
            color: var(--text-main);
            font-size: 0.82rem;
            padding: 8px 18px;
            background: rgba(15, 23, 42, 0.9);
        }

        .btn-outline-soft:hover {
            background: rgba(15, 23, 42, 0.98);
            border-color: var(--accent-blue);
        }

        .badge-type {
            border-radius: 999px;
            padding: 4px 11px;
            font-size: 0.72rem;
            border: 1px solid rgba(148, 163, 184, 0.35);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(15, 23, 42, 0.95);
        }

        .result-header {
            border-bottom: 1px solid rgba(15, 23, 42, 0.8);
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        table {
            font-size: 0.82rem;
        }

        .table-wrapper {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(31, 41, 55, 0.95);
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.7);
        }

        table thead th {
            background: radial-gradient(circle at top left, #020617, #020617);
            border-bottom: 1px solid rgba(55, 65, 81, 0.9);
            color: #e5e7eb;
            font-weight: 500;
        }

        table tbody tr:nth-child(even) td {
            background: rgba(15, 23, 42, 0.98);
        }

        table tbody tr:nth-child(odd) td {
            background: rgba(15, 23, 42, 0.94);
        }

        table tbody td {
            border-color: rgba(31, 41, 55, 0.95);
            color: #e5e7eb;
        }

        table tfoot th {
            background: rgba(15, 23, 42, 0.98);
            border-top: 1px solid rgba(55, 65, 81, 0.9);
        }

        .footer-text {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .text-muted-soft {
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .alert {
            border-radius: 12px;
            border-width: 1px;
        }

        .alert-danger {
            border-color: rgba(248, 113, 113, 0.5);
            background: rgba(127, 29, 29, 0.9);
            color: #fee2e2;
        }

        .alert-success {
            border-color: rgba(34, 197, 94, 0.5);
            background: rgba(6, 95, 70, 0.9);
            color: #dcfce7;
        }

        .alert-warning {
            border-color: rgba(251, 191, 36, 0.7);
            background: rgba(120, 53, 15, 0.95);
            color: #fffbeb;
        }

        .border-slate-600 {
            border-color: rgba(71, 85, 105, 0.9) !important;
        }

        .border-slate-700 {
            border-color: rgba(51, 65, 85, 1) !important;
        }

        .text-slate-300 {
            color: #e5e7eb !important;
        }

        .text-emerald-400 {
            color: var(--accent-green) !important;
        }

        .text-secondary {
            color: var(--text-muted) !important;
        }

        .bg-dark-soft {
            background: radial-gradient(circle at top left, #020617, #020617) !important;
        }

        .profit-card {
            background: radial-gradient(circle at top left, rgba(16, 185, 129, 0.12), transparent 60%),
                        radial-gradient(circle at bottom right, rgba(37, 99, 235, 0.14), transparent 55%),
                        linear-gradient(135deg, #020617, #020617);
            border-radius: 16px;
            border: 1px solid rgba(52, 211, 153, 0.5);
            box-shadow:
                0 18px 38px rgba(0, 0, 0, 0.85),
                0 0 0 1px rgba(15, 23, 42, 0.8);
        }

        .profit-value {
            font-size: 1.25rem;
            font-weight: 650;
            letter-spacing: 0.03em;
        }

        .chip-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.45);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--text-muted);
        }

        .chip-label i {
            color: var(--accent-amber);
        }

        .summary-period {
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        .summary-period b {
            color: #e5e7eb;
        }

        @media (max-width: 991.98px) {
            .main-wrapper {
                padding-top: 18px;
            }
            .card-glass, .card-glass-2 {
                border-radius: 16px;
            }
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
                <i class="fa-regular fa-user me-1"></i>
                <?= htmlspecialchars($user['Username'] ?? 'ketoan1') ?>
            </span>
            <a href="index.php?controller=ketoan&action=dashboard" class="btn btn-outline-info btn-sm me-2">
                <i class="fa-solid fa-arrow-left me-1"></i>Quay lại Dashboard
            </a>
            <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </a>
        </div>
    </div>
</nav>

<div class="container main-wrapper">
    <div class="row g-3 mb-3 align-items-center">
        <div class="col-md-8">
            <h2 class="mb-1">Tạo báo cáo doanh thu / chi phí</h2>
            <p class="text-secondary mb-0">
                Chọn loại báo cáo, khoảng thời gian để hệ thống tổng hợp dữ liệu và hiển thị kết quả.
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="badge-soft">
                <i class="fa-solid fa-circle-info me-1"></i>
                Báo cáo nội bộ kế toán
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- FORM -->
        <div class="col-lg-4">
            <div class="card card-glass h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="section-title">Tùy chọn báo cáo</span>
                        <i class="fa-solid fa-file-lines text-info"></i>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2 small mb-2">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success py-2 small mb-2">
                            <i class="fa-solid fa-circle-check me-1"></i>
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="index.php?controller=ketoan&action=baoCaoDoanhThuChiPhi">
                        <div class="mb-3">
                            <label class="form-label small">Loại báo cáo</label>
                            <select name="loai_baocao" class="form-select form-select-sm" required>
                                <option value="doanhthu" <?= $loaiBaoCao === 'doanhthu' ? 'selected' : '' ?>>Doanh thu</option>
                                <option value="chiphi"   <?= $loaiBaoCao === 'chiphi'   ? 'selected' : '' ?>>Chi phí</option>
                                <option value="tonghop"  <?= $loaiBaoCao === 'tonghop'  ? 'selected' : '' ?>>Tổng hợp</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small">Kỳ hạn</label>
                            <select name="ky_han" class="form-select form-select-sm">
                                <option value="ngay"   <?= $kyHan === 'ngay'   ? 'selected' : '' ?>>Theo ngày</option>
                                <option value="thang"  <?= $kyHan === 'thang'  ? 'selected' : '' ?>>Theo tháng</option>
                                <option value="khoang" <?= $kyHan === 'khoang' ? 'selected' : '' ?>>Khoảng tùy chọn</option>
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small">Từ ngày</label>
                                <input type="date" name="tu_ngay" value="<?= htmlspecialchars($tuNgay) ?>"
                                       class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Đến ngày</label>
                                <input type="date" name="den_ngay" value="<?= htmlspecialchars($denNgay) ?>"
                                       class="form-control form-control-sm" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Định dạng báo cáo</label>
                            <select name="dinh_dang" class="form-select form-select-sm">
                                <option value="html" <?= $dinhDang === 'html' ? 'selected' : '' ?>>Xem trên hệ thống</option>
                                <option value="csv"  <?= $dinhDang === 'csv'  ? 'selected' : '' ?>>Tải file CSV</option>
                            </select>
                        </div>

                        <div class="d-flex flex-column gap-2 mt-2">
                            <button type="submit" class="btn btn-primary-gradient w-100">
                                <i class="fa-solid fa-gear me-1"></i> Tạo báo cáo
                            </button>
                            <a href="index.php?controller=ketoan&action=dashboard"
                               class="btn btn-outline-soft w-100 btn-sm">
                                <i class="fa-solid fa-arrow-left-long me-1"></i> Quay về Dashboard kế toán
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- KẾT QUẢ -->
        <div class="col-lg-8">
            <div class="card card-glass-2 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center result-header">
                        <span class="section-title">Kết quả báo cáo</span>
                        <span class="badge-type">
                            <i class="fa-solid fa-filter me-1 text-info"></i><?= strtoupper($loaiBaoCao) ?>
                        </span>
                    </div>

                    <?php if (!$daGuiForm && !$dataBaoCao): ?>
                        <p class="text-muted-soft mb-0">
                            Vui lòng chọn thông tin và nhấn <b>“Tạo báo cáo”</b> để xem kết quả.
                        </p>
                    <?php else: ?>
                        <?php if ($noData): ?>
                            <div class="alert alert-warning small mb-2">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                Không có dữ liệu trong khoảng thời gian đã chọn.
                            </div>
                        <?php endif; ?>

                        <?php if ($dataBaoCao && !$noData): ?>
                            <!-- DOANH THU -->
                            <?php if ($loaiBaoCao === 'doanhthu'): ?>
                                <div class="mb-2 summary-period">
                                    <i class="fa-solid fa-calendar-days me-1"></i>
                                    Từ <b><?= htmlspecialchars($tuNgay) ?></b> đến <b><?= htmlspecialchars($denNgay) ?></b>
                                </div>
                                <div class="table-wrapper mb-3">
                                    <table class="table table-sm table-dark align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Ngày</th>
                                                <th class="text-end">Số giao dịch</th>
                                                <th class="text-end">Tổng doanh thu (VNĐ)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($dataBaoCao['rows'] as $row): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['Ngay']) ?></td>
                                                    <td class="text-end"><?= number_format($row['SoGiaoDich']) ?></td>
                                                    <td class="text-end"><?= number_format($row['TongDoanhThu'], 0, ',', '.') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Tổng</th>
                                                <th class="text-end"><?= number_format($dataBaoCao['tong']['so_gd']) ?></th>
                                                <th class="text-end"><?= number_format($dataBaoCao['tong']['doanh_thu'], 0, ',', '.') ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <!-- CHI PHÍ -->
                            <?php if ($loaiBaoCao === 'chiphi'): ?>
                                <div class="mb-2 summary-period">
                                    <i class="fa-solid fa-calendar-days me-1"></i>
                                    Từ <b><?= htmlspecialchars($tuNgay) ?></b> đến <b><?= htmlspecialchars($denNgay) ?></b>
                                </div>
                                <div class="table-wrapper mb-3">
                                    <table class="table table-sm table-dark align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Ngày</th>
                                                <th class="text-end">Số phiếu chi</th>
                                                <th class="text-end">Tổng chi phí (VNĐ)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($dataBaoCao['rows'] as $row): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['Ngay']) ?></td>
                                                    <td class="text-end"><?= number_format($row['SoPhieuChi']) ?></td>
                                                    <td class="text-end"><?= number_format($row['TongChiPhi'], 0, ',', '.') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Tổng</th>
                                                <th class="text-end"><?= number_format($dataBaoCao['tong']['so_phieu']) ?></th>
                                                <th class="text-end"><?= number_format($dataBaoCao['tong']['chi_phi'], 0, ',', '.') ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <!-- TỔNG HỢP -->
                            <?php if (isset($dataBaoCao['type']) && $dataBaoCao['type'] === 'tonghop'): ?>
                                <div class="summary-period mb-2">
                                    <span class="chip-label">
                                        <i class="fa-solid fa-layer-group"></i>
                                        Tổng hợp doanh thu &amp; chi phí
                                    </span>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="border rounded-3 p-2 mb-2 bg-dark-soft border-slate-600">
                                            <div class="small text-secondary mb-1 d-flex align-items-center">
                                                <i class="fa-solid fa-chart-line me-2 text-success"></i>
                                                Doanh thu
                                            </div>
                                            <div class="table-responsive">
                                                <div class="table-wrapper mb-0">
                                                    <table class="table table-sm table-dark align-middle mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Ngày</th>
                                                                <th class="text-end">Giao dịch</th>
                                                                <th class="text-end">Doanh thu</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($dataBaoCao['doanh_thu']['rows'] as $row): ?>
                                                                <tr>
                                                                    <td><?= htmlspecialchars($row['Ngay']) ?></td>
                                                                    <td class="text-end"><?= number_format($row['SoGiaoDich']) ?></td>
                                                                    <td class="text-end"><?= number_format($row['TongDoanhThu'], 0, ',', '.') ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>Tổng</th>
                                                                <th class="text-end">
                                                                    <?= number_format($dataBaoCao['doanh_thu']['tong']['so_gd']) ?>
                                                                </th>
                                                                <th class="text-end">
                                                                    <?= number_format($dataBaoCao['doanh_thu']['tong']['doanh_thu'], 0, ',', '.') ?>
                                                                </th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="border rounded-3 p-2 mb-2 bg-dark-soft border-slate-600">
                                            <div class="small text-secondary mb-1 d-flex align-items-center">
                                                <i class="fa-solid fa-receipt me-2 text-danger"></i>Chi phí
                                            </div>
                                            <div class="table-responsive">
                                                <div class="table-wrapper mb-0">
                                                    <table class="table table-sm table-dark align-middle mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Ngày</th>
                                                                <th class="text-end">Phiếu chi</th>
                                                                <th class="text-end">Chi phí</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($dataBaoCao['chi_phi']['rows'] as $row): ?>
                                                                <tr>
                                                                    <td><?= htmlspecialchars($row['Ngay']) ?></td>
                                                                    <td class="text-end"><?= number_format($row['SoPhieuChi']) ?></td>
                                                                    <td class="text-end"><?= number_format($row['TongChiPhi'], 0, ',', '.') ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>Tổng</th>
                                                                <th class="text-end">
                                                                    <?= number_format($dataBaoCao['chi_phi']['tong']['so_phieu']) ?>
                                                                </th>
                                                                <th class="text-end">
                                                                    <?= number_format($dataBaoCao['chi_phi']['tong']['chi_phi'], 0, ',', '.') ?>
                                                                </th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- KHỐI HIỂN THỊ LỢI NHUẬN -->
                                <div class="mt-3 p-3 profit-card">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small text-secondary mb-1 d-flex align-items-center">
                                                <i class="fa-solid fa-scale-balanced me-2 text-emerald-400"></i>
                                                Lợi nhuận (Doanh thu - Chi phí)
                                            </div>
                                            <div class="profit-value text-emerald-400">
                                                <?= isset($dataBaoCao['loi_nhuan']) 
                                                    ? number_format($dataBaoCao['loi_nhuan'], 0, ',', '.') . ' VNĐ'
                                                    : '0 VNĐ' ?>
                                            </div>
                                        </div>

                                        <div class="text-end small text-secondary">
                                            <div class="mb-1">
                                                <i class="fa-regular fa-calendar me-1"></i>
                                                Kỳ báo cáo
                                            </div>
                                            Từ <b><?= htmlspecialchars($tuNgay) ?></b><br>
                                            Đến <b><?= htmlspecialchars($denNgay) ?></b>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>
            </div>

            <div class="mt-3 text-center footer-text">
                ABC Resort – Kế toán | Báo cáo doanh thu &amp; chi phí theo kỳ
            </div>
        </div>
    </div>
</div>

</body>
</html>