<?php
// View: Khóa sổ kỳ hạch toán
$user = $user ?? Auth::user();
$danhSachKhoaSo = $danhSachKhoaSo ?? [];
$error = $error ?? '';
$success = $success ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Khóa Sổ Kỳ Hạch Toán - Kế Toán</title>
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

        .card-header-custom {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.15) 0%, rgba(16, 185, 129, 0.15) 100%);
            border-bottom: 2px solid var(--accent-blue);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
        }

        .status-mo-chua {
            background: rgba(107, 114, 128, 0.2);
            color: #d1d5db;
        }

        .status-dang-mo {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .status-da-dong {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .table-container {
            background: rgba(30, 41, 59, 0.8);
            border-radius: 8px;
            border: 1px solid var(--border-soft);
            overflow: hidden;
        }

        table {
            margin-bottom: 0;
        }

        table thead {
            background: rgba(15, 23, 42, 0.8);
            border-bottom: 2px solid var(--border-soft);
        }

        table th {
            color: var(--accent-blue);
            font-weight: 600;
            padding: 15px;
        }

        table tbody tr {
            border-bottom: 1px solid var(--border-soft);
        }

        table tbody tr:hover {
            background: rgba(59, 130, 246, 0.1);
        }

        table td {
            padding: 15px;
            vertical-align: middle;
        }

        .btn-group-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .btn-lock {
            background: linear-gradient(135deg, var(--accent-red), #dc2626);
            color: white;
            border: none;
        }

        .btn-lock:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
        }

        .btn-view {
            background: rgba(14, 165, 233, 0.2);
            color: var(--accent-blue);
            border: 1px solid var(--accent-blue);
        }

        .btn-view:hover {
            background: rgba(14, 165, 233, 0.3);
            color: var(--accent-blue);
        }

        .alert {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fecaca;
            border-radius: 6px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #86efac;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            padding: 20px;
        }

        .info-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .info-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent-blue);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-lock-open me-2" style="color: #10b981;"></i>Khóa Sổ
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
            <i class="fas fa-lock-open me-2"></i>Khóa Sổ Kỳ Hạch Toán
        </h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- Thống kê tóm tắt -->
        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">
                    <i class="fas fa-calendar-check me-2"></i>Kỳ đang mở
                </div>
                <div class="info-value">
                    <?php 
                    $kyDangMo = array_filter($danhSachKhoaSo, fn($k) => $k['TrangThai'] === 'DangMo');
                    echo count($kyDangMo) > 0 ? array_key_first($kyDangMo) + 1 : '—';
                    ?>
                </div>
            </div>
            <div class="info-card">
                <div class="info-label">
                    <i class="fas fa-lock me-2"></i>Kỳ đã khóa
                </div>
                <div class="info-value">
                    <?php 
                    $kyDaKhoa = array_filter($danhSachKhoaSo, fn($k) => $k['TrangThai'] === 'DaDong');
                    echo count($kyDaKhoa);
                    ?>
                </div>
            </div>
            <div class="info-card">
                <div class="info-label">
                    <i class="fas fa-list me-2"></i>Tổng kỳ
                </div>
                <div class="info-value">
                    <?= count($danhSachKhoaSo) ?>
                </div>
            </div>
        </div>

        <!-- Danh sách kỳ khóa sổ -->
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kỳ Hạch Toán</th>
                        <th>Ngày Mở</th>
                        <th>Ngày Đóng</th>
                        <th>Trạng Thái</th>
                        <th>Người Đóng</th>
                        <th>Ghi Chú</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($danhSachKhoaSo)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox me-2"></i>Chưa có dữ liệu
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($danhSachKhoaSo as $khoaSo): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($khoaSo['KyKhoaSo']) ?></strong></td>
                                <td><?= date('d/m/Y', strtotime($khoaSo['NgayMo'])) ?></td>
                                <td><?= $khoaSo['NgayDong'] ? date('d/m/Y', strtotime($khoaSo['NgayDong'])) : '—' ?></td>
                                <td>
                                    <?php
                                    $statusClass = 'status-' . strtolower(str_replace('_', '-', $khoaSo['TrangThai']));
                                    $statusText = match($khoaSo['TrangThai']) {
                                        'MoChua' => 'Chưa mở',
                                        'DangMo' => 'Đang mở',
                                        'DaDong' => 'Đã khóa',
                                        default => $khoaSo['TrangThai']
                                    };
                                    ?>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td><?= $khoaSo['MaNhanVienDong'] ? htmlspecialchars($khoaSo['MaNhanVienDong']) : '—' ?></td>
                                <td><?= htmlspecialchars($khoaSo['GhiChu'] ?? '—') ?></td>
                                <td>
                                    <div class="btn-group-actions">
                                        <?php if ($khoaSo['TrangThai'] === 'DangMo'): ?>
                                            <a href="index.php?controller=ketoan&action=dongSoKy&ky=<?= urlencode($khoaSo['KyKhoaSo']) ?>" 
                                               class="btn btn-sm btn-lock" title="Đóng sổ kỳ này">
                                                <i class="fas fa-lock me-1"></i>Đóng
                                            </a>
                                        <?php endif; ?>
                                        <a href="index.php?controller=ketoan&action=xemChiTietKhoaSo&ky=<?= urlencode($khoaSo['KyKhoaSo']) ?>" 
                                           class="btn btn-sm btn-view" title="Xem chi tiết">
                                            <i class="fas fa-eye me-1"></i>Xem
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Hướng dẫn -->
        <div class="card-header-custom mt-4">
            <h5><i class="fas fa-info-circle me-2"></i>Hướng Dẫn Khóa Sổ</h5>
            <ul style="margin: 15px 0 0 0; padding-left: 20px;">
                <li><strong>Chưa mở:</strong> Kỳ hạch toán chưa được bắt đầu</li>
                <li><strong>Đang mở:</strong> Kỳ đang hoạt động, có thể tạo/sửa giao dịch</li>
                <li><strong>Đã khóa:</strong> Kỳ đã đóng sổ, <strong>KHÔNG</strong> thể sửa/xóa giao dịch</li>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
