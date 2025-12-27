<?php
// View chi tiết khuyến mãi

$user = $user ?? Auth::user();
$khuyenMai = $khuyenMai ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết khuyến mãi - Quản lý</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        

    .home-icon { border-radius: 6px; background: rgba(15,23,42,0.15); padding: 4px; }
    .home-icon:hover { background: rgba(56,189,248,0.08); cursor: pointer; }

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
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-soft);
            padding: 1rem 2rem;
        }

        .container-wrapper {
            flex: 1;
            padding: 2rem 1rem;
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
        }

        .header-section {
            margin-bottom: 2rem;
        }

        .detail-card {
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 0.75rem;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }

        .detail-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--border-soft);
        }

        .detail-header .icon {
            font-size: 2.5rem;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            background: rgba(14, 165, 233, 0.1);
            color: var(--accent-blue);
        }

        .detail-header-content h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .detail-header-content .meta {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-top: 0.25rem;
        }

        .badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 0.5rem;
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

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .info-item {
            padding: 1.5rem;
            background: rgba(15, 23, 42, 0.5);
            border-radius: 0.5rem;
            border-left: 3px solid var(--border-soft);
        }

        .info-item.highlight {
            border-left-color: var(--accent-blue);
        }

        .info-label {
            color: var(--text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .info-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .info-value.currency {
            color: var(--accent-green);
            font-size: 1.5rem;
        }

        .divider {
            height: 2px;
            background: var(--border-soft);
            margin: 2rem 0;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--accent-blue);
            color: white;
        }

        .btn-primary:hover {
            background: #0284c7;
            color: white;
        }

        .btn-secondary {
            background: var(--border-soft);
            color: var(--text-main);
        }

        .btn-secondary:hover {
            background: rgba(148, 163, 184, 0.3);
            color: white;
        }

        footer {
            background: var(--bg-card);
            border-top: 1px solid var(--border-soft);
            padding: 1.5rem 2rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: auto;
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--border-soft);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -2.5rem;
            top: 0.5rem;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--accent-blue);
            border: 2px solid var(--bg-card);
        }

        .timeline-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }

        .timeline-date {
            font-weight: 700;
            color: var(--text-main);
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <!-- Header/Navbar -->
    <nav class="navbar">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <h4 style="margin: 0; color: var(--accent-blue); font-weight: 700;">
                <i class="fas fa-ticket me-2"></i>Chi tiết khuyến mãi
            </h4>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span class="small text-secondary">
                    <i class="fa-regular fa-user me-1"></i><?= htmlspecialchars($user['Username'] ?? 'N/A') ?>
                </span>
                <a href="index.php?controller=quanly&action=kiemTraKhuyenMai" class="btn btn-outline-light btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i>Quay lại danh sách
                </a>
                <a href="index.php?controller=auth&action=logout" class="btn btn-light btn-sm">
                    <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
                </a>
            </div>
        </div>
    </nav>

    <div class="container-wrapper">
        <div class="detail-card">
            <!-- Header Info -->
            <div class="detail-header">
                <div class="icon">
                    <i class="fas fa-gift"></i>
                </div>
                <div class="detail-header-content">
                    <h2><?= htmlspecialchars($khuyenMai['TenChuongTrinh']) ?></h2>
                    <div class="meta">
                        <i class="fas fa-hashtag me-1"></i>Mã: <?= $khuyenMai['MaKhuyenMai'] ?>
                    </div>
                    <div>
                        <span class="badge <?php
                            if ($khuyenMai['TrangThai'] === 'DangApDung') {
                                echo 'badge-active';
                            } elseif ($khuyenMai['TrangThai'] === 'HetHan') {
                                echo 'badge-expired';
                            } else {
                                echo 'badge-paused';
                            }
                        ?>">
                            <?php
                            if ($khuyenMai['TrangThai'] === 'DangApDung') {
                                echo '<i class="fas fa-check-circle me-1"></i>Đang áp dụng';
                            } elseif ($khuyenMai['TrangThai'] === 'HetHan') {
                                echo '<i class="fas fa-times-circle me-1"></i>Hết hạn';
                            } else {
                                echo '<i class="fas fa-pause-circle me-1"></i>Tạm ngưng';
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Main Info Grid -->
            <div class="info-grid">
                <div class="info-item highlight">
                    <div class="info-label">
                        <i class="fas fa-percentage me-1"></i>Mức ưu đãi
                    </div>
                    <div class="info-value currency">
                        <?php
                        $loai = $khuyenMai['LoaiUuDai'];
                        $muc = $khuyenMai['MucUuDai'];
                        if ($loai === 'PERCENT') {
                            echo number_format($muc, 1, ',', '.') . '%';
                        } else {
                            echo number_format($muc, 0, ',', '.') . 'đ';
                        }
                        ?>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-tag me-1"></i>Loại ưu đãi
                    </div>
                    <div class="info-value">
                        <?= $khuyenMai['LoaiUuDai'] === 'PERCENT' ? 'Giảm phần trăm' : 'Giảm tiền mặt' ?>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-users me-1"></i>Đối tượng
                    </div>
                    <div class="info-value">
                        <?= htmlspecialchars($khuyenMai['DoiTuong']) ?>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Timeline Section -->
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-label">Ngày bắt đầu áp dụng</div>
                    <div class="timeline-date">
                        <i class="fas fa-calendar-check me-2" style="color: var(--accent-green);"></i>
                        <?= date('d/m/Y', strtotime($khuyenMai['NgayBatDau'])) ?>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-label">Ngày kết thúc áp dụng</div>
                    <div class="timeline-date">
                        <i class="fas fa-calendar-times me-2" style="color: var(--accent-red);"></i>
                        <?= date('d/m/Y', strtotime($khuyenMai['NgayKetThuc'])) ?>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-label">Thời gian còn lại</div>
                    <div class="timeline-date">
                        <?php
                        $today = new DateTime(date('Y-m-d'));
                        $endDate = new DateTime($khuyenMai['NgayKetThuc']);
                        $diff = $endDate->diff($today);
                        
                        if ($diff->invert == 0) {
                            echo '<i class="fas fa-clock me-2" style="color: var(--accent-red);"></i>Hết hạn từ ' . $diff->days . ' ngày trước';
                        } else {
                            echo '<i class="fas fa-hourglass-end me-2" style="color: var(--accent-green);"></i>Còn ' . $diff->days . ' ngày';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Duration Info -->
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-calendar me-1"></i>Ngày bắt đầu
                    </div>
                    <div class="info-value" style="font-size: 1.1rem;">
                        <?= date('d/m/Y', strtotime($khuyenMai['NgayBatDau'])) ?>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-calendar me-1"></i>Ngày kết thúc
                    </div>
                    <div class="info-value" style="font-size: 1.1rem;">
                        <?= date('d/m/Y', strtotime($khuyenMai['NgayKetThuc'])) ?>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-calculator me-1"></i>Tổng ngày
                    </div>
                    <div class="info-value" style="font-size: 1.1rem;">
                        <?php
                        $start = new DateTime($khuyenMai['NgayBatDau']);
                        $end = new DateTime($khuyenMai['NgayKetThuc']);
                        $days = $end->diff($start)->days + 1;
                        echo $days . ' ngày';
                        ?>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="button-group">
                <a href="index.php?controller=quanly&action=kiemTraKhuyenMai" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>Quay lại danh sách
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <i class="fas fa-shield-alt me-2"></i>ABC Resort – Bộ phận Quản lý | Chi tiết khuyến mãi
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
