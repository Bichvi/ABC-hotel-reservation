<?php
// View: Công nợ phải thu (Accounts Receivable)
$user = $user ?? Auth::user();
$congNo = $congNo ?? [];
$error = $error ?? '';
$tongSoTienConNo = $tongSoTienConNo ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Công Nợ Phải Thu - Kế Toán</title>
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
            --accent-yellow: #fbbf24;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(15, 23, 42, 0.8) 100%);
            border-radius: 8px;
            padding: 25px;
            border-left: 4px solid;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.2;
            position: absolute;
            right: 20px;
            top: 20px;
        }

        .stat-card.not-paid {
            border-left-color: var(--accent-red);
            position: relative;
        }

        .stat-card.not-paid .stat-value {
            color: var(--accent-red);
        }

        .stat-card.partial {
            border-left-color: var(--accent-yellow);
            position: relative;
        }

        .stat-card.partial .stat-value {
            color: var(--accent-yellow);
        }

        .stat-card.paid {
            border-left-color: var(--accent-green);
            position: relative;
        }

        .stat-card.paid .stat-value {
            color: var(--accent-green);
        }

        .stat-card.overdue {
            border-left-color: var(--accent-red);
            position: relative;
        }

        .stat-card.overdue .stat-value {
            color: var(--accent-red);
        }

        .table-container {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .table {
            margin-bottom: 0;
            color: var(--text-main);
        }

        .table thead {
            background: rgba(15, 23, 42, 0.8);
            border-bottom: 2px solid var(--border-soft);
        }

        .table th {
            padding: 18px;
            font-weight: 700;
            color: var(--accent-blue);
            border: none;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            border-bottom: 1px solid var(--border-soft);
            transition: background 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(14, 165, 233, 0.05);
        }

        .table td {
            padding: 16px 18px;
            vertical-align: middle;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-chuat {
            background: rgba(239, 68, 68, 0.2);
            color: var(--accent-red);
            border: 1px solid rgba(239, 68, 68, 0.5);
        }

        .status-thumphan {
            background: rgba(251, 191, 36, 0.2);
            color: var(--accent-yellow);
            border: 1px solid rgba(251, 191, 36, 0.5);
        }

        .status-dathi {
            background: rgba(16, 185, 129, 0.2);
            color: var(--accent-green);
            border: 1px solid rgba(16, 185, 129, 0.5);
        }

        .status-quahan {
            background: rgba(239, 68, 68, 0.3);
            color: var(--accent-red);
            border: 1px solid var(--accent-red);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 6px;
            border: none;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-view {
            background: var(--accent-blue);
            color: white;
        }

        .btn-view:hover {
            background: #0284c7;
            color: white;
        }

        .btn-collect {
            background: var(--accent-green);
            color: white;
        }

        .btn-collect:hover {
            background: #059669;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .filter-section {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .form-group {
            margin: 0;
        }

        .form-control {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid var(--border-soft);
            color: var(--text-main);
            padding: 10px 15px;
            border-radius: 6px;
        }

        .form-control:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: var(--accent-blue);
            color: var(--text-main);
            box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.25);
        }

        .form-label {
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-hand-holding-heart me-2" style="color: #10b981;"></i>Công Nợ Phải Thu
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
            <i class="fas fa-hand-holding-heart me-2"></i>Công Nợ Phải Thu (Accounts Receivable)
        </h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Thống kê -->
        <div class="stats-grid">
            <div class="stat-card not-paid">
                <div class="stat-label">Chưa Thanh Toán</div>
                <div class="stat-value">
                    <?php
                    $chuaThu = array_sum(array_map(function($item) {
                        return ($item['TrangThaiThanhToan'] === 'ChuaThu') ? ($item['SoTienGoc'] - $item['SoTienDaThu']) : 0;
                    }, $congNo));
                    echo number_format($chuaThu, 0, ',', '.');
                    ?>
                </div>
                <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
            </div>

            <div class="stat-card partial">
                <div class="stat-label">Thành Toán Một Phần</div>
                <div class="stat-value">
                    <?php
                    $thuMotPhan = array_sum(array_map(function($item) {
                        return ($item['TrangThaiThanhToan'] === 'ThuMotPhan') ? ($item['SoTienGoc'] - $item['SoTienDaThu']) : 0;
                    }, $congNo));
                    echo number_format($thuMotPhan, 0, ',', '.');
                    ?>
                </div>
                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
            </div>

            <div class="stat-card paid">
                <div class="stat-label">Đã Thanh Toán</div>
                <div class="stat-value">
                    <?php
                    $daThu = array_sum(array_map(function($item) {
                        return ($item['TrangThaiThanhToan'] === 'DaThu') ? $item['SoTienDaThu'] : 0;
                    }, $congNo));
                    echo number_format($daThu, 0, ',', '.');
                    ?>
                </div>
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            </div>

            <div class="stat-card overdue">
                <div class="stat-label">Quá Hạn Thanh Toán</div>
                <div class="stat-value">
                    <?php
                    $quaHan = array_sum(array_map(function($item) {
                        return ($item['TrangThaiThanhToan'] === 'QuaHan') ? ($item['SoTienGoc'] - $item['SoTienDaThu']) : 0;
                    }, $congNo));
                    echo number_format($quaHan, 0, ',', '.');
                    ?>
                </div>
                <div class="stat-icon"><i class="fas fa-exclamation-circle"></i></div>
            </div>
        </div>

        <!-- Lọc và tìm kiếm -->
        <div class="filter-section">
            <form method="GET" action="" class="filter-form">
                <input type="hidden" name="controller" value="ketoan">
                <input type="hidden" name="action" value="congNoPhaiThu">
                <div>
                    <label class="form-label">Tìm Kiếm</label>
                    <input type="text" name="search" class="form-control" placeholder="Nhập tên khách hàng..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label">Trạng Thái</label>
                    <select name="status" class="form-control">
                        <option value="">Tất cả</option>
                        <option value="ChuaThu" <?= ($_GET['status'] ?? '') === 'ChuaThu' ? 'selected' : '' ?>>Chưa Thanh Toán</option>
                        <option value="ThuMotPhan" <?= ($_GET['status'] ?? '') === 'ThuMotPhan' ? 'selected' : '' ?>>Thanh Toán Một Phần</option>
                        <option value="DaThu" <?= ($_GET['status'] ?? '') === 'DaThu' ? 'selected' : '' ?>>Đã Thanh Toán</option>
                        <option value="QuaHan" <?= ($_GET['status'] ?? '') === 'QuaHan' ? 'selected' : '' ?>>Quá Hạn</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Tìm Kiếm
                    </button>
                </div>
            </form>
        </div>

        <!-- Bảng công nợ -->
        <?php if (!empty($congNo)): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Khách Hàng</th>
                            <th>Số Tiền Gốc</th>
                            <th>Đã Thanh Toán</th>
                            <th>Còn Nợ</th>
                            <th>Trạng Thái</th>
                            <th>Ngày Tạo</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($congNo as $index => $item): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($item['TenKH'] ?? 'N/A') ?></strong><br>
                                    <small style="color: var(--text-muted);">
                                        <?= htmlspecialchars($item['SDT'] ?? '') ?>
                                    </small>
                                </td>
                                <td>
                                    <strong><?= number_format($item['SoTienGoc'] ?? 0, 0, ',', '.') ?></strong><br>
                                    <small style="color: var(--text-muted);">₫</small>
                                </td>
                                <td>
                                    <?= number_format($item['SoTienDaThu'] ?? 0, 0, ',', '.') ?><br>
                                    <small style="color: var(--text-muted);">₫</small>
                                </td>
                                <td>
                                    <strong style="<?= ($item['SoTienGoc'] - $item['SoTienDaThu']) > 0 ? 'color: var(--accent-red);' : 'color: var(--accent-green);' ?>">
                                        <?= number_format($item['SoTienGoc'] - $item['SoTienDaThu'], 0, ',', '.') ?>
                                    </strong><br>
                                    <small style="color: var(--text-muted);">₫</small>
                                </td>
                                <td>
                                    <?php
                                    $status = $item['TrangThaiThanhToan'] ?? 'ChuaThu';
                                    $badgeClass = match($status) {
                                        'ChuaThu' => 'status-chuat',
                                        'ThuMotPhan' => 'status-thumphan',
                                        'DaThu' => 'status-dathi',
                                        'QuaHan' => 'status-quahan',
                                        default => 'status-chuat'
                                    };
                                    $statusLabel = match($status) {
                                        'ChuaThu' => 'Chưa Thanh Toán',
                                        'ThuMotPhan' => 'Thanh Toán Một Phần',
                                        'DaThu' => 'Đã Thanh Toán',
                                        'QuaHan' => 'Quá Hạn Thanh Toán',
                                        default => 'Chưa Thanh Toán'
                                    };
                                    ?>
                                    <span class="status-badge <?= $badgeClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td>
                                    <small><?= isset($item['NgayTao']) ? date('d/m/Y', strtotime($item['NgayTao'])) : 'N/A' ?></small>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="index.php?controller=ketoan&action=xemChiTietCongNo&id=<?= $item['MaCongNo'] ?? 0 ?>" 
                                           class="btn btn-small btn-view">
                                            <i class="fas fa-eye me-1"></i>Xem
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="table-container">
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h5>Không có dữ liệu công nợ</h5>
                    <p style="color: var(--text-muted);">Hiện tại không có khách hàng nợ tiền</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
