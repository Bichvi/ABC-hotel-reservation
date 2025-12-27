<?php
// View Tổng Quan - Biểu đồ và thống kê

$user = $user ?? Auth::user();
$error = $error ?? '';
$message = $message ?? '';
$doanhThuTheoNgay = $doanhThuTheoNgay ?? [];
$doanhThuTheoPhong = $doanhThuTheoPhong ?? [];
$chiPhiTheoLoai = $chiPhiTheoLoai ?? [];
$tongDoanhThu = $tongDoanhThu ?? 0;
$tongChiPhi = $tongChiPhi ?? 0;
$loiNhuan = $loiNhuan ?? 0;
$tuNgay = $tuNgay ?? date('Y-m-d', strtotime('-30 days'));
$denNgay = $denNgay ?? date('Y-m-d');

// Chuẩn bị dữ liệu cho biểu đồ
$ngays = [];
$revenues = [];
foreach ($doanhThuTheoNgay as $item) {
    $ngays[] = $item['Ngay'];
    $revenues[] = (float)$item['TongDoanhThu'];
}

$phongs = [];
$phongRevenues = [];
foreach ($doanhThuTheoPhong as $item) {
    $phongs[] = 'Phòng ' . ($item['SoPhong'] ?? $item['MaPhong']);
    $phongRevenues[] = (float)$item['TongDoanhThu'];
}

$loais = [];
$loaiExpenses = [];
$colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'];
$colorIndex = 0;
$loaiColors = [];
foreach ($chiPhiTheoLoai as $item) {
    $loais[] = $item['TenChiPhi'];
    $loaiExpenses[] = (float)$item['TongChiPhi'];
    $loaiColors[] = $colors[$colorIndex % count($colors)];
    $colorIndex++;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tổng Quan - Kế toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --bg-main: #020617;
            --bg-card: rgba(15, 23, 42, 0.96);
            --border-soft: rgba(148, 163, 184, 0.35);
            --text-main: #e5e7eb;
            --text-muted: #9ca3af;
            --accent-blue: #38bdf8;
            --accent-green: #22c55e;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #020617, #020617);
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

        .main-wrapper {
            padding: 28px 0 40px;
        }

        .card-glass {
            border-radius: 18px;
            border: 1px solid var(--border-soft);
            background: linear-gradient(145deg, rgba(15, 23, 42, 0.95), rgba(15, 23, 42, 0.98));
            color: var(--text-main);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
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

        .btn-primary-gradient {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-green));
            color: #0b1120;
            font-weight: 600;
            border: none;
            border-radius: 999px;
            padding: 10px 20px;
            font-size: 0.87rem;
            box-shadow: 0 8px 16px rgba(56, 189, 248, 0.3);
        }

        .btn-primary-gradient:hover {
            filter: brightness(1.07);
            transform: translateY(-1px);
        }

        .badge-soft {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.85), rgba(56, 189, 248, 0.9));
            color: #eff6ff;
            border-radius: 999px;
            padding: 4px 14px;
            font-size: 0.7rem;
            box-shadow: 0 0 0 1px rgba(191, 219, 254, 0.2);
        }

        .table-wrapper {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(31, 41, 55, 0.95);
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.7);
        }

        table {
            font-size: 0.82rem;
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

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-booked {
            background: rgba(59, 130, 246, 0.3);
            color: #93c5fd;
        }

        .status-stayed {
            background: rgba(34, 197, 94, 0.3);
            color: #86efac;
        }

        .status-paid {
            background: rgba(34, 197, 94, 0.4);
            color: #4ade80;
        }

        .status-cancel {
            background: rgba(248, 113, 113, 0.3);
            color: #fca5a5;
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

        .summary-card {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), transparent);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .summary-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--accent-green);
        }

        .filter-card {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), transparent);
            border: 1px solid rgba(37, 99, 235, 0.3);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .chart-wrapper {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(15, 23, 42, 0.98));
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .chart-title {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #e5e7eb;
        }

        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
        }

        .text-secondary {
            color: var(--text-muted) !important;
        }

        @media (max-width: 768px) {
            .summary-card {
                padding: 12px;
            }

            .summary-value {
                font-size: 1.1rem;
            }

            .chart-container {
                height: 300px;
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
    <!-- Page Title -->
    <div class="row g-3 mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="mb-1">Tổng Quan Kinh Doanh</h2>
            <p class="text-secondary mb-0">
                Biểu đồ và thống kê kinh doanh
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="badge-soft">
                <i class="fa-solid fa-calendar me-1"></i>
                <?= htmlspecialchars($tuNgay) ?> — <?= htmlspecialchars($denNgay) ?>
            </span>
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

    <!-- Filter Form -->
    <div class="filter-card">
        <form method="get" action="index.php" class="row g-2 align-items-end">
            <input type="hidden" name="controller" value="ketoan">
            <input type="hidden" name="action" value="tongQuan">

            <div class="col-md-3">
                <label class="form-label small">Loại Lọc</label>
                <select class="form-select form-select-sm" name="filterType" onchange="document.querySelector('form').submit();">
                    <option value="7days" <?= ($filterType ?? '30days') === '7days' ? 'selected' : '' ?>>7 Ngày</option>
                    <option value="30days" <?= ($filterType ?? '30days') === '30days' ? 'selected' : '' ?>>30 Ngày</option>
                    <option value="90days" <?= ($filterType ?? '30days') === '90days' ? 'selected' : '' ?>>90 Ngày</option>
                    <option value="month" <?= ($filterType ?? '30days') === 'month' ? 'selected' : '' ?>>Theo Tháng</option>
                    <option value="year" <?= ($filterType ?? '30days') === 'year' ? 'selected' : '' ?>>Theo Năm</option>
                    <option value="custom" <?= ($filterType ?? '30days') === 'custom' ? 'selected' : '' ?>>Tùy chọn</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small">Từ Ngày</label>
                <input type="date" class="form-control form-control-sm" name="tuNgay" value="<?= htmlspecialchars($tuNgay) ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label small">Đến Ngày</label>
                <input type="date" class="form-control form-control-sm" name="denNgay" value="<?= htmlspecialchars($denNgay) ?>">
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-primary-gradient btn-sm w-100">
                    <i class="fa-solid fa-filter me-1"></i>Lọc Dữ Liệu
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="summary-card">
                <p class="section-title mb-2">Tổng Doanh Thu</p>
                <p class="summary-value">
                    <i class="fa-solid fa-coins me-2" style="color: #10b981;"></i><?= number_format($tongDoanhThu, 0, ',', '.') ?> ₫
                </p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="summary-card" style="border-color: rgba(248, 113, 113, 0.3); background: linear-gradient(135deg, rgba(248, 113, 113, 0.1), transparent);">
                <p class="section-title mb-2">Tổng Chi Phí</p>
                <p class="summary-value" style="color: #f87171;">
                    <i class="fa-solid fa-cash me-2"></i><?= number_format($tongChiPhi, 0, ',', '.') ?> ₫
                </p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="summary-card" style="border-color: rgba(59, 130, 246, 0.3); background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), transparent);">
                <p class="section-title mb-2">Lợi Nhuận</p>
                <p class="summary-value" style="color: #38bdf8;">
                    <i class="fa-solid fa-chart-line me-2"></i><?= number_format($loiNhuan, 0, ',', '.') ?> ₫
                </p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="summary-card" style="border-color: rgba(251, 146, 60, 0.3); background: linear-gradient(135deg, rgba(251, 146, 60, 0.1), transparent);">
                <p class="section-title mb-2">Tỉ Lệ Lợi Nhuận</p>
                <p class="summary-value" style="color: #fb923c;">
                    <i class="fa-solid fa-percent me-2"></i><?= $tongDoanhThu > 0 ? number_format(($loiNhuan / $tongDoanhThu) * 100, 1) : '0' ?>%
                </p>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="chart-wrapper">
                <h6 class="chart-title">
                    <i class="fa-solid fa-chart-line me-2" style="color: #10b981;"></i>Doanh Thu Theo Ngày
                </h6>
                <div class="chart-container">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-wrapper">
                <h6 class="chart-title">
                    <i class="fa-solid fa-chart-pie me-2" style="color: #f59e0b;"></i>Chi Phí Theo Loại
                </h6>
                <div class="chart-container">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="chart-wrapper">
                <h6 class="chart-title">
                    <i class="fa-solid fa-chart-bar me-2" style="color: #38bdf8;"></i>Top 10 Phòng Có Doanh Thu Cao
                </h6>
                <div class="chart-container" style="height: 400px;">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cấu hình chung
        Chart.defaults.color = '#9ca3af';
        Chart.defaults.borderColor = 'rgba(148, 163, 184, 0.1)';

        // Line Chart - Doanh Thu Theo Ngày
        const lineCanvas = document.getElementById('lineChart');
        if (lineCanvas) {
            const lineCtx = lineCanvas.getContext('2d');
            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($ngays) ?>,
                    datasets: [{
                        label: 'Doanh Thu (đ)',
                        data: <?= json_encode($revenues) ?>,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: { color: '#9ca3af', font: { size: 11 } }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.1)' },
                            ticks: {
                                color: '#9ca3af',
                                callback: function(value) {
                                    return new Intl.NumberFormat('vi-VN', {
                                        notation: 'compact',
                                        compactDisplay: 'short'
                                    }).format(value);
                                }
                            }
                        },
                        x: {
                            grid: { color: 'rgba(148, 163, 184, 0.1)' },
                            ticks: { color: '#9ca3af' }
                        }
                    }
                }
            });
        }

        // Pie Chart - Chi Phí Theo Loại
        const pieCanvas = document.getElementById('pieChart');
        if (pieCanvas) {
            const pieCtx = pieCanvas.getContext('2d');
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($loais) ?>,
                    datasets: [{
                        data: <?= json_encode($loaiExpenses) ?>,
                        backgroundColor: <?= json_encode($loaiColors) ?>,
                        borderColor: 'rgba(15, 23, 42, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: '#9ca3af', font: { size: 11 }, padding: 12 }
                        }
                    }
                }
            });
        }

        // Bar Chart - Top 10 Phòng
        const barCanvas = document.getElementById('barChart');
        if (barCanvas) {
            const barCtx = barCanvas.getContext('2d');
            console.log('Bar chart data - phongs:', <?= json_encode($phongs) ?>);
            console.log('Bar chart data - revenues:', <?= json_encode($phongRevenues) ?>);
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($phongs) ?>,
                    datasets: [{
                        label: 'Doanh Thu (đ)',
                        data: <?= json_encode($phongRevenues) ?>,
                        backgroundColor: 'rgba(56, 189, 248, 0.8)',
                        borderColor: '#38bdf8',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: { color: '#9ca3af', font: { size: 11 } }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.1)' },
                            ticks: {
                                color: '#9ca3af',
                                callback: function(value) {
                                    return new Intl.NumberFormat('vi-VN', {
                                        notation: 'compact',
                                        compactDisplay: 'short'
                                    }).format(value);
                                }
                            }
                        },
                        y: {
                            grid: { color: 'rgba(148, 163, 184, 0.1)' },
                            ticks: { color: '#9ca3af' }
                        }
                    }
                }
            });
        }
    });
</script>

</body>
</html>
