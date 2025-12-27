<?php
// View danh sách kiểm toán đêm

$user = $user ?? Auth::user();
$danhSach = $danhSach ?? [];
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
$total = $total ?? 0;
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kiểm toán đêm - Kế toán</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-section h2 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .btn-create {
            background: var(--accent-green);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.3s;
        }

        .btn-create:hover {
            background: #059669;
            color: white;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border-color: #10b981;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border-color: #ef4444;
        }

        .table-wrapper {
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 0.75rem;
            overflow: hidden;
        }

        table {
            margin: 0;
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: rgba(30, 41, 59, 0.5);
            border-bottom: 1px solid var(--border-soft);
        }

        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        td {
            padding: 1rem;
            border-top: 1px solid var(--border-soft);
            color: var(--text-main);
        }

        tbody tr:hover {
            background: rgba(15, 23, 42, 0.5);
        }

        .number {
            text-align: right;
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 0.3rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-completed {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-small {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            border: none;
            border-radius: 0.3rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.3s;
        }

        .btn-view {
            background: var(--accent-blue);
            color: white;
        }

        .btn-view:hover {
            background: #0284c7;
        }

        .btn-edit {
            background: var(--accent-blue);
            color: white;
        }

        .btn-edit:hover {
            background: #0284c7;
        }

        .btn-delete {
            background: var(--accent-red);
            color: white;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        .pagination {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }

        .pagination a, .pagination span {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border-soft);
            border-radius: 0.3rem;
            text-decoration: none;
            color: var(--text-main);
            cursor: pointer;
            transition: all 0.3s;
        }

        .pagination a:hover {
            background: var(--accent-blue);
            border-color: var(--accent-blue);
            color: white;
        }

        .pagination .active {
            background: var(--accent-blue);
            border-color: var(--accent-blue);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <!-- Header/Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="#" style="font-weight: 700; font-size: 1.1rem; color: var(--accent-blue);">
                <i class="fas fa-moon me-2"></i>Kiểm toán đêm
            </a>
            <div class="ms-auto">
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-secondary">
                        <i class="fa-regular fa-user me-1"></i><?= htmlspecialchars($user['Username'] ?? 'N/A') ?>
                    </span>
                    <a href="index.php?controller=ketoan&action=dashboard" class="btn btn-outline-light btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i>Quay lại Dashboard
                    </a>
                    <a href="index.php?controller=auth&action=logout" class="btn btn-light btn-sm">
                        <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-wrapper">
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>

        <div class="header-section">
            <h2>Quản lý kiểm toán đêm</h2>
            <a href="index.php?controller=ketoan&action=taoKiemToanDem" class="btn-create">
                <i class="fas fa-plus"></i> Tạo kiểm toán mới
            </a>
        </div>

        <?php if (empty($danhSach)): ?>
        <div class="table-wrapper">
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <p>Chưa có kiểm toán đêm nào</p>
            </div>
        </div>
        <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ngày kiểm toán</th>
                        <th class="number">Số dư đầu ngày</th>
                        <th class="number">Số dư cuối ngày</th>
                        <th class="number">Doanh thu</th>
                        <th class="number">Chi phí</th>
                        <th class="number">Lợi nhuận</th>
                        <th>Trạng thái</th>
                        <th>Người kiểm toán</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($danhSach as $kt): ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($kt['MaKTD']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($kt['NgayKTD'])); ?></td>
                        <td class="number"><?php echo number_format($kt['SoDuDauNgay'], 0, ',', '.'); ?>đ</td>
                        <td class="number"><?php echo number_format($kt['SoDuCuoiNgay'], 0, ',', '.'); ?>đ</td>
                        <td class="number"><?php echo number_format($kt['TongDoanhThu'], 0, ',', '.'); ?>đ</td>
                        <td class="number"><?php echo number_format($kt['TongChiPhi'], 0, ',', '.'); ?>đ</td>
                        <td class="number">
                            <span style="color: <?php echo $kt['LoiNhuan'] >= 0 ? '#10b981' : '#ef4444'; ?>">
                                <?php echo number_format($kt['LoiNhuan'], 0, ',', '.'); ?>đ
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-completed">
                                <i class="fas fa-check"></i> Đã kiểm toán
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($kt['TenNguoiDung'] ?? 'N/A'); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="index.php?controller=ketoan&action=chiTietKiemToanDem&id=<?php echo $kt['MaKTD']; ?>" class="btn-small btn-view" title="Xem">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="index.php?controller=ketoan&action=suaKiemToanDem&id=<?php echo $kt['MaKTD']; ?>" class="btn-small btn-edit" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="index.php?controller=ketoan&action=xoaKiemToanDem&id=<?php echo $kt['MaKTD']; ?>" class="btn-small btn-delete" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="index.php?controller=ketoan&action=kiemToanDem&page=1">«</a>
            <a href="index.php?controller=ketoan&action=kiemToanDem&page=<?php echo $page - 1; ?>">‹</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                <span class="active"><?php echo $i; ?></span>
                <?php else: ?>
                <a href="index.php?controller=ketoan&action=kiemToanDem&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
            <a href="index.php?controller=ketoan&action=kiemToanDem&page=<?php echo $page + 1; ?>">›</a>
            <a href="index.php?controller=ketoan&action=kiemToanDem&page=<?php echo $totalPages; ?>">»</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
