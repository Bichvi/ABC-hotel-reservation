<?php
// View: Tách loại doanh thu theo tiêu chuẩn kế toán

$user = $user ?? Auth::user();
$tuNgay = $_GET['tu_ngay'] ?? date('Y-m-01');
$denNgay = $_GET['den_ngay'] ?? date('Y-m-t');
$error = $_GET['error'] ?? '';
$doanhThuTheoLoai = $doanhThuTheoLoai ?? [];
$doanhThuPhong = $doanhThuPhong ?? [];
$doanhThuDichVu = $doanhThuDichVu ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tách loại doanh thu - Kế toán</title>
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
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-soft);
            padding: 1rem 2rem;
        }

        .container-wrapper {
            padding: 2rem 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card-wrapper {
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: rgba(15, 23, 42, 0.5);
            border-left: 4px solid var(--accent-blue);
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .stat-card h4 {
            margin: 0;
            font-size: 0.9rem;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-top: 0.5rem;
            color: var(--accent-green);
        }

        table {
            margin: 0;
            width: 100%;
        }

        thead {
            background: rgba(30, 41, 59, 0.5);
            border-bottom: 1px solid var(--border-soft);
        }

        th {
            padding: 1rem;
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        td {
            padding: 1rem;
            border-top: 1px solid var(--border-soft);
        }

        tbody tr:hover {
            background: rgba(15, 23, 42, 0.5);
        }

        .badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 0.25rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-phong {
            background: rgba(14, 165, 233, 0.2);
            color: #0ea5e9;
        }

        .badge-dichvu {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .badge-phuthu {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
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

        .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--border-soft);
        }

        .filter-box {
            background: rgba(15, 23, 42, 0.5);
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-box input {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid var(--border-soft);
            color: var(--text-main);
        }

        .filter-box input:focus {
            background: rgba(15, 23, 42, 0.9);
            border-color: var(--accent-blue);
            color: var(--text-main);
        }

        .btn-filter {
            background: var(--accent-blue);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-filter:hover {
            background: #0284c7;
        }

        .comparison-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <h4 style="margin: 0; color: var(--accent-blue); font-weight: 700;">
                <i class="fas fa-chart-bar me-2"></i>Tách loại doanh thu
            </h4>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span class="small text-secondary">
                    <i class="fa-regular fa-user me-1"></i><?= htmlspecialchars($user['Username'] ?? 'N/A') ?>
                </span>
                <a href="index.php?controller=ketoan&action=dashboard" class="btn btn-outline-light btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i>Quay lại
                </a>
            </div>
        </div>
    </nav>

    <div class="container-wrapper">
        <h2 class="mb-1">Phân tích doanh thu theo loại</h2>
        <p class="text-secondary mb-3">
            Tách doanh thu thành: <strong>Phòng</strong> (theo ngày lưu trú) | <strong>Dịch vụ</strong> (theo ngày phát sinh) | <strong>Phụ thu</strong>
        </p>

        <!-- BỘ LỌC -->
        <div class="filter-box">
            <form method="get" action="" class="row g-2">
                <input type="hidden" name="controller" value="ketoan">
                <input type="hidden" name="action" value="tachDoanhThu">
                
                <div class="col-md-3">
                    <label class="form-label small">Từ ngày</label>
                    <input type="date" name="tu_ngay" value="<?= htmlspecialchars($tuNgay) ?>" class="form-control form-control-sm">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label small">Đến ngày</label>
                    <input type="date" name="den_ngay" value="<?= htmlspecialchars($denNgay) ?>" class="form-control form-control-sm">
                </div>
                
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn-filter w-100">
                        <i class="fas fa-search me-1"></i>Lọc dữ liệu
                    </button>
                </div>
            </form>
        </div>

        <!-- THỐNG KÊ -->
        <h3 class="section-title">Thống kê tổng hợp</h3>
        <div class="comparison-grid">
            <div class="stat-card">
                <h4><i class="fas fa-door-open me-2" style="color: var(--accent-blue);"></i>Doanh thu Phòng</h4>
                <div class="stat-value">
                    <?php
                    $tongPhong = 0;
                    foreach ($doanhThuTheoLoai as $row) {
                        if ($row['LoaiDoanhThu'] === 'Phong' || $row['LoaiDoanhThu'] === NULL) {
                            $tongPhong += (float)$row['DoanhThuPhong'];
                        }
                    }
                    echo number_format($tongPhong, 0, ',', '.') . 'đ';
                    ?>
                </div>
            </div>

            <div class="stat-card">
                <h4><i class="fas fa-concierge-bell me-2" style="color: var(--accent-green);"></i>Doanh thu Dịch vụ</h4>
                <div class="stat-value">
                    <?php
                    $tongDichVu = 0;
                    foreach ($doanhThuTheoLoai as $row) {
                        if ($row['LoaiDoanhThu'] === 'DichVu') {
                            $tongDichVu += (float)$row['DoanhThuPhong'];
                        }
                    }
                    echo number_format($tongDichVu, 0, ',', '.') . 'đ';
                    ?>
                </div>
            </div>

            <div class="stat-card">
                <h4><i class="fas fa-coins me-2" style="color: #f59e0b;"></i>Phụ thu</h4>
                <div class="stat-value">
                    <?php
                    $tongPhuThu = 0;
                    foreach ($doanhThuTheoLoai as $row) {
                        $tongPhuThu += (float)$row['DoanhThuPhuThu'];
                    }
                    echo number_format($tongPhuThu, 0, ',', '.') . 'đ';
                    ?>
                </div>
            </div>

            <div class="stat-card" style="border-left-color: var(--accent-green);">
                <h4><i class="fas fa-chart-line me-2" style="color: var(--accent-green);"></i>Tổng doanh thu</h4>
                <div class="stat-value">
                    <?php echo number_format($tongPhong + $tongDichVu + $tongPhuThu, 0, ',', '.') . 'đ'; ?>
                </div>
            </div>
        </div>

        <!-- DOANH THU PHÒNG THEO NGÀY LƯU TRÚ -->
        <h3 class="section-title">Doanh thu Phòng (Accrual - Theo ngày lưu trú)</h3>
        <div class="card-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Ngày ghi nhận</th>
                        <th>Số phòng</th>
                        <th>Số khách</th>
                        <th>Doanh thu phòng</th>
                        <th>Phụ thu</th>
                        <th>Cộng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($doanhThuPhong)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted);">Không có dữ liệu</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($doanhThuPhong as $row): ?>
                        <tr>
                            <td>
                                <span class="badge badge-phong">
                                    <?= date('d/m/Y', strtotime($row['NgayGhiNhan'])) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['SoPhong']) ?></td>
                            <td><?= htmlspecialchars($row['SoNguoi']) ?></td>
                            <td style="color: var(--accent-green); font-weight: 600;">
                                <?= number_format($row['DoanhThuPhong'], 0, ',', '.') ?>đ
                            </td>
                            <td style="color: #f59e0b;">
                                <?= number_format($row['DoanhThuPhuThu'], 0, ',', '.') ?>đ
                            </td>
                            <td style="font-weight: 600;">
                                <?= number_format($row['DoanhThuPhong'] + $row['DoanhThuPhuThu'], 0, ',', '.') ?>đ
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- DOANH THU DỊCH VỤ THEO NGÀY PHÁT SINH -->
        <h3 class="section-title">Doanh thu Dịch vụ (Cash basis - Theo ngày phát sinh)</h3>
        <div class="card-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Ngày phát sinh</th>
                        <th>Số chỉ tiết</th>
                        <th>Doanh thu dịch vụ</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($doanhThuDichVu)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted);">Không có dữ liệu</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($doanhThuDichVu as $row): ?>
                        <tr>
                            <td>
                                <span class="badge badge-dichvu">
                                    <?= date('d/m/Y', strtotime($row['NgayPhatSinh'])) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['SoChiTiet']) ?></td>
                            <td style="color: var(--accent-green); font-weight: 600;">
                                <?= number_format($row['DoanhThuDichVu'], 0, ',', '.') ?>đ
                            </td>
                            <td><i class="fas fa-check" style="color: var(--accent-green);"></i></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- HƯỚNG DẪN -->
        <div class="card-wrapper" style="border-left: 4px solid var(--accent-blue); margin-top: 2rem;">
            <h4 style="color: var(--accent-blue); margin-top: 0;">
                <i class="fas fa-info-circle me-2"></i>Hướng dẫn chuẩn kế toán
            </h4>
            <ul style="margin-bottom: 0; color: var(--text-muted); font-size: 0.95rem;">
                <li><strong>Doanh thu Phòng:</strong> Ghi nhận theo ngày lưu trú (Accrual basis), tính toán dựa trên NgayGhiNhanKeToan từ CheckIn đến CheckOut</li>
                <li><strong>Doanh thu Dịch vụ:</strong> Ghi nhận theo ngày phát sinh (Cash basis), tính toán dựa trên NgayGiaoDich thực tế</li>
                <li><strong>Phụ thu:</strong> Những khoản chi thêm (phòng nâng cấp, tiếp tân, v.v.), tính chung với phòng</li>
                <li><strong>Dữ liệu phải có:</strong> LoaiDoanhThu (Phong/DichVu/PhuThu) và NgayGhiNhanKeToan trên từng chi tiết giao dịch</li>
            </ul>
        </div>
    </div>

    <footer>
        <i class="fas fa-balance-scale me-2"></i>ABC Resort – Kế toán | Phân tích doanh thu chuẩn
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
