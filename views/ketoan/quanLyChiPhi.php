<?php
// View quản lý chi phí

$user = $user ?? Auth::user();
$error = $error ?? '';
$message = $message ?? '';
$danhSachChiPhi = $danhSachChiPhi ?? [];
$filters = $filters ?? [];
$tongChiPhi = $tongChiPhi ?? 0;
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$totalRecords = $totalRecords ?? 0;

// Giá trị mặc định
$tuNgay = $filters['tu_ngay'] ?? date('Y-m-01');
$denNgay = $filters['den_ngay'] ?? date('Y-m-t');
$trangThai = $filters['trang_thai'] ?? 'all';
$search = $filters['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý chi phí - Kế toán</title>
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
            --accent-amber: #f59e0b;
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
            background: linear-gradient(90deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%) !important;
            border-bottom: 1px solid var(--border-soft);
        }

        .brand-logo {
            font-weight: 700;
            letter-spacing: 0.5px;
            font-size: 0.95rem;
            color: var(--accent-blue) !important;
        }

        .main-wrapper {
            padding: 30px 20px 40px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 5px;
        }

        .page-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin: 0;
        }

        .card-glass {
            border-radius: 12px;
            border: 1px solid var(--border-soft);
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(15, 23, 42, 0.8) 100%);
            color: var(--text-main);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .section-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .form-control, .form-select {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(148, 163, 184, 0.25);
            color: var(--text-main);
            font-size: 0.9rem;
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

        .btn-primary-gradient {
            background: linear-gradient(135deg, var(--accent-blue), #0284c7);
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .btn-primary-gradient:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        .filter-section {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 24px;
        }

        .table-wrapper {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border-soft);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            background: rgba(15, 23, 42, 0.8);
        }

        table {
            font-size: 0.85rem;
            margin-bottom: 0;
            background: rgba(15, 23, 42, 0.8);
            color: #e2e8f0;
        }

        table thead th {
            background: rgba(15, 23, 42, 0.8);
            border-bottom: 1px solid var(--border-soft);
            color: #e2e8f0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            padding: 12px;
        }

        table tbody tr {
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
            transition: all 0.2s ease;
        }

        table tbody tr:hover {
            background-color: rgba(245, 158, 11, 0.05);
        }

        table tbody td {
            color: #e2e8f0;
            padding: 12px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.2);
            color: #fcd34d;
        }

        .status-approved {
            background: rgba(34, 197, 94, 0.2);
            color: #86efac;
        }

        .status-canceled {
            background: rgba(248, 113, 113, 0.2);
            color: #fca5a5;
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

        .alert-warning {
            border-color: rgba(245, 158, 11, 0.4);
            background: rgba(120, 53, 15, 0.7);
            color: #fef3c7;
        }

        .summary-card {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), transparent);
            border: 1px solid rgba(245, 158, 11, 0.25);
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 24px;
        }

        .summary-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 6px;
        }

        .summary-value {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--accent-amber);
        }

        .pagination {
            justify-content: center;
            margin-top: 24px;
            gap: 5px;
        }

        .pagination .page-link {
            background-color: rgba(15, 23, 42, 0.8);
            border: 1px solid var(--border-soft);
            color: var(--text-main);
            border-radius: 6px;
        }

        .pagination .page-link:hover {
            background-color: var(--accent-blue);
            border-color: var(--accent-blue);
            color: #0f172a;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--accent-blue);
            border-color: var(--accent-blue);
            color: #0f172a;
        }

        .text-secondary {
            color: var(--text-muted) !important;
        }

        .btn-back {
            background: rgba(14, 165, 233, 0.15);
            border: 1px solid var(--accent-blue);
            color: var(--accent-blue);
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: var(--accent-blue);
            color: #0f172a;
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.4rem;
            }

            .filter-section {
                padding: 12px;
            }

            table {
                font-size: 0.75rem;
            }

            table th, table td {
                padding: 8px 4px;
            }
        }

        .pagination .page-link {
            background-color: rgba(15, 23, 42, 0.92);
            border-color: rgba(148, 163, 184, 0.35);
            color: var(--text-main);
        }

        .pagination .page-link:hover {
            background-color: var(--accent-amber);
            border-color: var(--accent-amber);
            color: #0b1120;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--accent-amber);
            border-color: var(--accent-amber);
        }

        .text-secondary {
            color: var(--text-muted) !important;
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
    <div class="row g-3 mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="mb-1">Quản lý chi phí</h2>
            <p class="text-secondary mb-0">
                Ghi nhận chi phí vận hành: điện nước, sửa chữa, lương nhân viên, vật tư…
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="index.php?controller=ketoan&action=themChiPhi" class="btn btn-primary-gradient">
                <i class="fa-solid fa-plus me-1"></i>Thêm Chi Phí
            </a>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success" role="alert">
            <i class="fa-solid fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Tóm tắt chi phí -->
    <div class="summary-card">
        <div class="row g-3">
            <div class="col-md-6">
                <p class="section-title mb-2">Tổng chi phí (Kỳ này)</p>
                <p class="summary-value">
                    <i class="fa-solid fa-wallet me-2 text-warning"></i><?= number_format($tongChiPhi, 0, ',', '.') ?> ₫
                </p>
            </div>
            <div class="col-md-6">
                <p class="section-title mb-2">Tổng phiếu chi</p>
                <p class="summary-value">
                    <i class="fa-solid fa-file-invoice-dollar me-2 text-info"></i><?= $totalRecords ?> phiếu
                </p>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="card card-glass mb-4">
        <div class="card-body">
            <p class="section-title mb-3">Bộ lọc và tìm kiếm</p>
            <form method="get" class="row g-3">
                <input type="hidden" name="controller" value="ketoan">
                <input type="hidden" name="action" value="quanLyChiPhi">
                <div class="col-md-2">
                    <label class="form-label small">Từ ngày</label>
                    <input type="date" name="tu_ngay" class="form-control" value="<?= htmlspecialchars($tuNgay) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Đến ngày</label>
                    <input type="date" name="den_ngay" class="form-control" value="<?= htmlspecialchars($denNgay) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Trạng thái</label>
                    <select name="trang_thai" class="form-select">
                        <option value="all" <?= $trangThai === 'all' ? 'selected' : '' ?>>Tất cả</option>
                        <option value="ChoDuyet" <?= $trangThai === 'ChoDuyet' ? 'selected' : '' ?>>Chờ duyệt</option>
                        <option value="DaDuyet" <?= $trangThai === 'DaDuyet' ? 'selected' : '' ?>>Đã duyệt</option>
                        <option value="Huy" <?= $trangThai === 'Huy' ? 'selected' : '' ?>>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Tìm kiếm (tên chi phí, nội dung)</label>
                    <input type="text" name="search" class="form-control" placeholder="Nhập tên hoặc nội dung chi phí..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary-gradient w-100">
                        <i class="fa-solid fa-search me-1"></i>Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách chi phí -->
    <div class="card card-glass">
        <div class="card-body">
            <p class="section-title mb-3">Danh sách chi phí (<?= $totalRecords ?> bản ghi)</p>
            
            <?php if (empty($danhSachChiPhi)): ?>
                <div class="alert alert-warning" role="alert">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                    Không có dữ liệu với bộ lọc này. Vui lòng thay đổi điều kiện tìm kiếm.
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Mã chi phí</th>
                                <th>Tên chi phí</th>
                                <th>Ngày chi</th>
                                <th>Số tiền</th>
                                <th>Nội dung</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($danhSachChiPhi as $cp): ?>
                                <tr>
                                    <td>
                                        <strong>#<?= htmlspecialchars($cp['MaCP']) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($cp['TenChiPhi']) ?></td>
                                    <td><?= htmlspecialchars($cp['NgayChi']) ?></td>
                                    <td class="text-warning">
                                        <strong><?= number_format((float)$cp['SoTien'], 0, ',', '.') ?> ₫</strong>
                                    </td>
                                    <td>
                                        <span class="small text-secondary">
                                            <?= htmlspecialchars(substr($cp['NoiDung'] ?? 'N/A', 0, 40)) ?>
                                            <?= strlen($cp['NoiDung'] ?? '') > 40 ? '...' : '' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = 'status-pending';
                                        $statusText = $cp['TrangThai'];
                                        
                                        if ($cp['TrangThai'] === 'DaDuyet') {
                                            $statusClass = 'status-approved';
                                            $statusText = 'Đã duyệt';
                                        } elseif ($cp['TrangThai'] === 'Huy') {
                                            $statusClass = 'status-canceled';
                                            $statusText = 'Đã hủy';
                                        } elseif ($cp['TrangThai'] === 'ChoDuyet') {
                                            $statusText = 'Chờ duyệt';
                                        }
                                        ?>
                                        <span class="status-badge <?= $statusClass ?>">
                                            <?= htmlspecialchars($statusText) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="index.php?controller=ketoan&action=suaChiPhi&id=<?= $cp['MaCP'] ?>" class="btn btn-sm btn-outline-info" title="Sửa">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <a href="index.php?controller=ketoan&action=xoaChiPhi&id=<?= $cp['MaCP'] ?>&step=1" class="btn btn-sm btn-outline-danger" title="Xóa">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Điều hướng trang">
                        <ul class="pagination">
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?controller=ketoan&action=quanLyChiPhi&page=<?= $currentPage - 1 ?>&tu_ngay=<?= htmlspecialchars($tuNgay) ?>&den_ngay=<?= htmlspecialchars($denNgay) ?>&trang_thai=<?= htmlspecialchars($trangThai) ?>&search=<?= htmlspecialchars($search) ?>">
                                        <i class="fa-solid fa-chevron-left me-1"></i>Trước
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="index.php?controller=ketoan&action=quanLyChiPhi&page=<?= $i ?>&tu_ngay=<?= htmlspecialchars($tuNgay) ?>&den_ngay=<?= htmlspecialchars($denNgay) ?>&trang_thai=<?= htmlspecialchars($trangThai) ?>&search=<?= htmlspecialchars($search) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?controller=ketoan&action=quanLyChiPhi&page=<?= $currentPage + 1 ?>&tu_ngay=<?= htmlspecialchars($tuNgay) ?>&den_ngay=<?= htmlspecialchars($denNgay) ?>&trang_thai=<?= htmlspecialchars($trangThai) ?>&search=<?= htmlspecialchars($search) ?>">
                                        Tiếp<i class="fa-solid fa-chevron-right ms-1"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
