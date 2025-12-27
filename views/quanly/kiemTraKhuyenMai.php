<?php
// View kiểm tra khuyến mãi

$user = $user ?? Auth::user();
$danhSach = $danhSach ?? [];
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
$total = $total ?? 0;
$khuyenMaiDangApDung = $khuyenMaiDangApDung ?? [];
$khuyenMaiSapDienRa = $khuyenMaiSapDienRa ?? [];
$khuyenMaiTamNgung = $khuyenMaiTamNgung ?? [];
$khuyenMaiHetHan = $khuyenMaiHetHan ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kiểm tra khuyến mãi - Quản lý</title>
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
            --accent-yellow: #f59e0b;
        }
        
    .home-icon { border-radius: 6px; background: rgba(15,23,42,0.15); padding: 4px; }
    .home-icon:hover { background: rgba(56,189,248,0.08); cursor: pointer; }


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

        .container-wrapper {
            padding: 2rem 1rem;
            max-width: 1400px;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 0.75rem;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            font-size: 2rem;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            background: rgba(15, 23, 42, 0.5);
        }

        .stat-content h4 {
            margin: 0;
            font-size: 0.9rem;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .stat-content .number {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.25rem 0 0 0;
        }

        .stat-card.active .stat-icon {
            color: var(--accent-green);
            background: rgba(16, 185, 129, 0.1);
        }

        .stat-card.upcoming .stat-icon {
            color: var(--accent-blue);
            background: rgba(14, 165, 233, 0.1);
        }

        .stat-card.paused .stat-icon {
            color: var(--accent-yellow);
            background: rgba(245, 158, 11, 0.1);
        }

        .stat-card.expired .stat-icon {
            color: var(--text-muted);
            background: rgba(148, 163, 184, 0.1);
        }

        .table-wrapper {
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 0.75rem;
            overflow: hidden;
            margin-bottom: 2rem;
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

        .badge {
            padding: 0.4rem 0.8rem;
            border-radius: 0.25rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-active {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .badge-expired {
            background: rgba(94, 109, 132, 0.2);
            color: #cbd5e1;
        }

        .badge-paused {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .badge-percent {
            background: rgba(14, 165, 233, 0.1);
            color: var(--accent-blue);
        }

        .badge-fixed {
            background: rgba(16, 185, 129, 0.1);
            color: var(--accent-green);
        }

        .btn-small {
            background: none;
            border: none;
            color: var(--accent-blue);
            cursor: pointer;
            font-size: 1rem;
            padding: 0.5rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
        }

        .btn-small:hover {
            color: var(--accent-green);
            transform: scale(1.2);
        }

        .btn-small.delete {
            color: var(--accent-red);
        }

        .btn-small.delete:hover {
            color: #dc2626;
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

        .currency {
            font-weight: 600;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            margin-top: 2rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-soft);
        }

        footer {
            background: var(--bg-card);
            border-top: 1px solid var(--border-soft);
            padding: 1.5rem 2rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <!-- Header/Navbar -->
    <nav class="navbar">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <h4 style="margin: 0; color: var(--accent-blue); font-weight: 700;">
                <i class="fas fa-ticket me-2"></i>Kiểm tra khuyến mãi
            </h4>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span class="small text-secondary">
                    <i class="fa-regular fa-user me-1"></i><?= htmlspecialchars($user['Username'] ?? 'N/A') ?>
                </span>
                <a href="index.php?controller=quanly&action=dashboard" class="btn btn-outline-light btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i>Quay lại Dashboard
                </a>
                            <a href="index.php" class="me-2 d-inline-flex align-items-center justify-content-center home-link" title="Trang chủ">
                <i class="fa-solid fa-house fa-lg"></i>
            </a>
                <a href="index.php?controller=auth&action=logout" class="btn btn-light btn-sm">
                    <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
                </a>
            </div>
        </div>
    </nav>

    <div class="container-wrapper">
        <div class="header-section">
            <h2>Quản lý khuyến mãi</h2>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card active">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h4>Đang áp dụng</h4>
                    <p class="number"><?= count($khuyenMaiDangApDung) ?></p>
                </div>
            </div>

            <div class="stat-card upcoming">
                <div class="stat-icon">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="stat-content">
                    <h4>Sắp diễn ra</h4>
                    <p class="number"><?= count($khuyenMaiSapDienRa) ?></p>
                </div>
            </div>

            <div class="stat-card paused">
                <div class="stat-icon">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <div class="stat-content">
                    <h4>Tạm ngưng</h4>
                    <p class="number"><?= count($khuyenMaiTamNgung) ?></p>
                </div>
            </div>

            <div class="stat-card expired">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h4>Hết hạn</h4>
                    <p class="number"><?= count($khuyenMaiHetHan) ?></p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="color: var(--accent-blue); background: rgba(14, 165, 233, 0.1);">
                    <i class="fas fa-gift"></i>
                </div>
                <div class="stat-content">
                    <h4>Tổng cộng</h4>
                    <p class="number"><?= $total ?></p>
                </div>
            </div>
        </div>

        <!-- Danh sách khuyến mãi -->
        <div class="section-title">Danh sách tất cả khuyến mãi (<?= $total ?> bản ghi)</div>

        <?php if (empty($danhSach)): ?>
        <div class="table-wrapper">
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <p>Chưa có khuyến mãi nào</p>
            </div>
        </div>
        <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 8%;">ID</th>
                        <th style="width: 25%;">Tên chương trình</th>
                        <th style="width: 12%;">Ngày bắt đầu</th>
                        <th style="width: 12%;">Ngày kết thúc</th>
                        <th style="width: 10%;">Mức ưu đãi</th>
                        <th style="width: 10%;">Loại</th>
                        <th style="width: 10%;">Đối tượng</th>
                        <th style="width: 10%;">Trạng thái</th>
                        <th style="width: 8%;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($danhSach as $km): ?>
                    <tr>
                        <td>#<?= htmlspecialchars($km['MaKhuyenMai']) ?></td>
                        <td><?= htmlspecialchars($km['TenChuongTrinh']) ?></td>
                        <td><?= date('d/m/Y', strtotime($km['NgayBatDau'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($km['NgayKetThuc'])) ?></td>
                        <td class="currency">
                            <?php
                            $loai = $km['LoaiUuDai'];
                            $muc = $km['MucUuDai'];
                            if ($loai === 'PERCENT') {
                                echo htmlspecialchars($muc) . '%';
                            } else {
                                echo number_format($muc, 0, ',', '.') . 'đ';
                            }
                            ?>
                        </td>
                        <td>
                            <span class="badge <?= $km['LoaiUuDai'] === 'PERCENT' ? 'badge-percent' : 'badge-fixed' ?>">
                                <?= $km['LoaiUuDai'] === 'PERCENT' ? 'Giảm %' : 'Giảm tiền' ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($km['DoiTuong']) ?></td>
                        <td>
                            <span class="badge <?php
                                if ($km['TrangThai'] === 'DangApDung') {
                                    echo 'badge-active';
                                } elseif ($km['TrangThai'] === 'HetHan') {
                                    echo 'badge-expired';
                                } else {
                                    echo 'badge-paused';
                                }
                            ?>">
                                <?php
                                if ($km['TrangThai'] === 'DangApDung') {
                                    echo '<i class="fas fa-check-circle me-1"></i>Đang áp dụng';
                                } elseif ($km['TrangThai'] === 'HetHan') {
                                    echo '<i class="fas fa-times-circle me-1"></i>Hết hạn';
                                } else {
                                    echo '<i class="fas fa-pause-circle me-1"></i>Tạm ngưng';
                                }
                                ?>
                            </span>
                        </td>
                        <td>
                            <a href="index.php?controller=quanly&action=chiTietKhuyenMai&id=<?= $km['MaKhuyenMai'] ?>" class="btn-small" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="index.php?controller=quanly&action=kiemTraKhuyenMai&page=1">«</a>
            <a href="index.php?controller=quanly&action=kiemTraKhuyenMai&page=<?= $page - 1 ?>">‹</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                <span class="active"><?= $i ?></span>
                <?php else: ?>
                <a href="index.php?controller=quanly&action=kiemTraKhuyenMai&page=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
            <a href="index.php?controller=quanly&action=kiemTraKhuyenMai&page=<?= $page + 1 ?>">›</a>
            <a href="index.php?controller=quanly&action=kiemTraKhuyenMai&page=<?= $totalPages ?>">»</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <i class="fas fa-shield-alt me-2"></i>ABC Resort – Bộ phận Quản lý | Kiểm tra khuyến mãi
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
