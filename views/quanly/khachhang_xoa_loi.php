<?php
$user = Auth::user();
$customer = $customer ?? [];
$checkResult = $checkResult ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Không thể xóa khách hàng - ABC Resort</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            min-height: 100vh;
            color: #e5e7eb;
        }
        
    .home-icon { border-radius: 6px; background: rgba(15,23,42,0.15); padding: 4px; }
    .home-icon:hover { background: rgba(56,189,248,0.08); cursor: pointer; }

        .navbar {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(12px);
        }
        .wrapper {
            background: rgba(15,23,42,0.86);
            border-radius: 20px;
            padding: 28px;
            border: 1px solid rgba(148,163,184,0.35);
            box-shadow: 0 20px 40px rgba(0,0,0,0.55);
        }
        .alert-custom {
            background: rgba(220, 38, 38, 0.15);
            border-left: 4px solid #ef4444;
            border-radius: 12px;
            padding: 20px;
        }
        .info-card {
            background: rgba(15, 23, 42, 0.5);
            border-radius: 10px;
            border: 1px solid #475569;
            padding: 15px;
            margin-bottom: 15px;
        }
        .table-custom {
            background: rgba(15, 23, 42, 0.7);
            border-radius: 10px;
            overflow: hidden;
        }
        .table-custom th {
            background: rgba(51, 65, 85, 0.8);
            color: #f1f5f9;
            font-weight: 600;
            border-bottom: 2px solid #475569;
        }
        .table-custom td {
            color: #e2e8f0;
            border-bottom: 1px solid #334155;
        }
        .badge-status {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark mb-4 border-bottom border-slate-700">
    <div class="container d-flex justify-content-between">
        <span class="navbar-brand">
            <i class="fa-solid fa-hotel me-2 text-info"></i>ABC Resort - Quản lý
        </span>
        <div class="d-flex align-items-center">
            <span class="me-3 small">
                <i class="fa-regular fa-user me-1"></i><?= htmlspecialchars($user['Username']) ?>
            </span>
                        <a href="index.php" class="me-2 d-inline-flex align-items-center justify-content-center home-link" title="Trang chủ">
                <i class="fa-solid fa-house fa-lg"></i>
            </a>
            <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="wrapper mx-auto" style="max-width: 1000px;">
        
        <!-- Header -->
        <div class="mb-4">
            <a href="index.php?controller=quanly&action=danhsachKhachHang" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách
            </a>
        </div>

        <!-- Thông báo chính: hiển thị success (xanh) hoặc lỗi (đỏ) -->
        <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success mb-4 d-flex align-items-start" role="alert" style="border-left:4px solid #198754; background: rgba(25,135,84,0.12); color: #e6fff2;">
            <div class="flex-shrink-0 me-3">
                <div style="width:56px; height:56px; border-radius:10px; background:#198754; display:flex; align-items:center; justify-content:center;">
                    <i class="fa-solid fa-circle-check fa-2x text-white"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <h4 class="mb-2 text-white"><i class="fa-solid fa-check me-2"></i>Thành công</h4>
                <p class="mb-0 fs-5"><?= htmlspecialchars($successMessage) ?></p>
            </div>
        </div>
        <?php else: ?>
        <div class="alert-custom mb-4">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-circle-exclamation fa-3x text-danger me-3"></i>
                </div>
                <div class="flex-grow-1">
                    <h4 class="text-danger mb-3">
                        <i class="fa-solid fa-ban me-2"></i>Không thể xóa khách hàng
                    </h4>
                    <p class="mb-0 fs-5"><?= htmlspecialchars($checkResult['message']) ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Thông tin khách hàng -->
        <div class="info-card mb-4">
            <h5 class="text-info mb-3">
                <i class="fa-solid fa-user-circle me-2"></i>Thông tin khách hàng
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Mã KH:</strong> #<?= $customer['MaKhachHang'] ?></p>
                    <p><strong>Họ tên:</strong> <?= htmlspecialchars($customer['TenKH']) ?></p>
                    <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($customer['SDT'] ?? 'Chưa có') ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Email:</strong> <?= htmlspecialchars($customer['Email'] ?? 'Chưa có') ?></p>
                    <p><strong>CCCD:</strong> <?= htmlspecialchars($customer['CCCD'] ?? 'Chưa có') ?></p>
                    <p><strong>Loại khách:</strong> <?= htmlspecialchars($customer['LoaiKhach'] ?? 'Chưa xác định') ?></p>
                </div>
            </div>
        </div>

        <!-- Danh sách giao dịch chưa thanh toán -->
        <?php if (!empty($checkResult['unpaid_transactions'])): ?>
        <div class="mb-4">
            <h5 class="text-warning mb-3">
                <i class="fa-solid fa-file-invoice-dollar me-2"></i>
                Giao dịch chưa thanh toán / Đang hoạt động (<?= count($checkResult['unpaid_transactions']) ?>)
            </h5>
            <div class="table-responsive table-custom">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Mã GD</th>
                            <th>Ngày giao dịch</th>
                            <th>Loại</th>
                            <th class="text-end">Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thanh toán</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($checkResult['unpaid_transactions'] as $gd): ?>
                        <tr>
                            <td class="fw-bold text-info">#<?= $gd['MaGiaoDich'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($gd['NgayGiaoDich'])) ?></td>
                            <td><?= htmlspecialchars($gd['LoaiGiaoDich']) ?></td>
                            <td class="text-end fw-bold text-warning">
                                <?= number_format($gd['TongTien'], 0, ',', '.') ?>đ
                            </td>
                            <td>
                                <?php 
                                $statusClass = 'secondary';
                                if ($gd['TrangThai'] == 'Booked') $statusClass = 'primary';
                                if ($gd['TrangThai'] == 'Stayed') $statusClass = 'success';
                                if ($gd['TrangThai'] == 'Moi') $statusClass = 'info';
                                ?>
                                <span class="badge bg-<?= $statusClass ?> badge-status">
                                    <?= htmlspecialchars($gd['TrangThai']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($gd['PhuongThucThanhToan'] == 'ChuaThanhToan'): ?>
                                    <span class="badge bg-danger badge-status">
                                        <i class="fa-solid fa-circle-xmark me-1"></i>Chưa thanh toán
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success badge-status">
                                        <?= htmlspecialchars($gd['PhuongThucThanhToan']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Danh sách hóa đơn chưa thanh toán -->
        <?php if (!empty($checkResult['unpaid_invoices'])): ?>
        <div class="mb-4">
            <h5 class="text-warning mb-3">
                <i class="fa-solid fa-receipt me-2"></i>
                Hóa đơn chưa thanh toán (<?= count($checkResult['unpaid_invoices']) ?>)
            </h5>
            <div class="table-responsive table-custom">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Mã HĐ</th>
                            <th>Ngày lập</th>
                            <th class="text-end">Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thanh toán</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($checkResult['unpaid_invoices'] as $hd): ?>
                        <tr>
                            <td class="fw-bold text-info">#<?= $hd['MaHoaDon'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($hd['NgayLap'])) ?></td>
                            <td class="text-end fw-bold text-warning">
                                <?= number_format($hd['TongTien'], 0, ',', '.') ?>đ
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark badge-status">
                                    <?= htmlspecialchars($hd['TrangThai'] ?? 'Chưa xác định') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($hd['PhuongThucThanhToan'] == 'ChuaThanhToan'): ?>
                                    <span class="badge bg-danger badge-status">
                                        <i class="fa-solid fa-circle-xmark me-1"></i>Chưa thanh toán
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success badge-status">
                                        <?= htmlspecialchars($hd['PhuongThucThanhToan']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Danh sách phản hồi chưa xử lý -->
        <?php if (!empty($checkResult['unresolved_feedback'])): ?>
        <div class="mb-4">
            <h5 class="text-warning mb-3">
                <i class="fa-solid fa-comments me-2"></i>
                Phản hồi/Khiếu nại chưa xử lý (<?= count($checkResult['unresolved_feedback']) ?>)
            </h5>
            <div class="table-responsive table-custom">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Mã PH</th>
                            <th>Ngày phản hồi</th>
                            <th>Loại dịch vụ</th>
                            <th>Nội dung</th>
                            <th>Tình trạng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($checkResult['unresolved_feedback'] as $ph): ?>
                        <tr>
                            <td class="fw-bold text-info">#<?= $ph['MaPH'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($ph['NgayPhanHoi'])) ?></td>
                            <td><?= htmlspecialchars($ph['LoaiDichVu'] ?? 'Chưa xác định') ?></td>
                            <td><?= htmlspecialchars(mb_substr($ph['NoiDung'], 0, 50, 'UTF-8')) ?>...</td>
                            <td>
                                <?php 
                                $statusClass = $ph['TinhTrang'] == 'ChuaXuLy' ? 'danger' : 'warning';
                                ?>
                                <span class="badge bg-<?= $statusClass ?> badge-status">
                                    <?= htmlspecialchars($ph['TinhTrang']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Hướng dẫn -->
        <div class="alert alert-info" style="background: rgba(56, 189, 248, 0.15); border-left: 4px solid #0ea5e9; border-radius: 12px;">
            <h6 class="text-info mb-2">
                <i class="fa-solid fa-lightbulb me-2"></i>Hướng dẫn
            </h6>
            <ul class="mb-0 small">
                <li>Để xóa khách hàng này, vui lòng hoàn tất tất cả giao dịch/hóa đơn chưa thanh toán</li>
                <li>Xử lý hết các phản hồi/khiếu nại đang chờ</li>
                <li>Sau đó bạn có thể thực hiện xóa hồ sơ khách hàng</li>
            </ul>
        </div>

        <!-- Nút hành động -->
        <div class="text-center mt-4">
            <a href="index.php?controller=quanly&action=danhsachKhachHang" class="btn btn-primary btn-lg">
                <i class="fa-solid fa-arrow-left me-2"></i>Quay lại danh sách khách hàng
            </a>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
