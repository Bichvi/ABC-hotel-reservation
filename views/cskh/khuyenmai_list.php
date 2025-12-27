<?php $user = Auth::user(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách Khuyến mãi - CSKH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            min-height: 100vh;
            color: #e5e7eb;
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar {
            background: rgba(15, 23, 42, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(148, 163, 184, 0.2);
        }
        .brand-logo {
            font-weight: 700;
            letter-spacing: 1px;
        }
        .wrapper {
            background: radial-gradient(circle at top left, rgba(168, 85, 247, 0.2), rgba(15,23,42,0.95));
            border-radius: 24px;
            padding: 35px;
            border: 1px solid rgba(148, 163, 184, 0.3);
            box-shadow: 0 20px 60px rgba(0,0,0,0.6);
            margin: 40px auto;
            max-width: 1200px;
            backdrop-filter: blur(10px);
        .page-title {
            font-size: 30px;
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, #a78bfa, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }   gap: 12px;
        }
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.2);
            color: #f8fafc;
            font-weight: 600;
            font-size: 15px;
        }
        .btn-create {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: white;
            padding: 8px 24px;
            font-weight: bold;
            border-radius: 8px;
            transition: 0.2s;
        }
        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
            color: white;
        }
        .btn-delete {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: none;
            color: white;
            padding: 8px 24px;
            font-weight: bold;
            border-radius: 8px;
        }
        .btn-back {
            background: rgba(148, 163, 184, 0.2);
            border: 1px solid rgba(148, 163, 184, 0.3);
            color: #e5e7eb;
            padding: 8px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.2s;
        }
        .btn-back:hover {
            background: rgba(148, 163, 184, 0.3);
            color: #ffffff;
        }
        .table-custom {
            background: transparent;
            border-radius: 12px;
            overflow: hidden;
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-custom thead {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(99, 102, 241, 0.2));
            backdrop-filter: blur(10px);
        }
        .table-custom thead th {
            color: #090909ff;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 18px 15px;
            border: 1px solid rgba(12, 19, 29, 0.15);
            border-bottom: 2px solid rgba(139, 92, 246, 0.3);
        }
        .table-custom tbody td {
            color: #f8fafc;
            padding: 18px 15px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.15);
            font-weight: 500;
            background: rgba(15,23,42,0.4);
            vertical-align: middle;
        }
        .table-custom tbody tr {
            transition: all 0.2s ease;
        }
        .table-custom tbody tr:hover {
            background: linear-gradient(90deg, rgba(139, 92, 246, 0.15), rgba(99, 102, 241, 0.15));
            transform: scale(1.01);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);
        }
        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }
        .link-update {
            color: #60a5fa;
            text-decoration: none;
            font-weight: 700;
            transition: 0.2s;
            font-size: 14px;
            padding: 6px 12px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(96, 165, 250, 0.1);
        }
        .link-update:hover {
            color: #ffffff;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .status-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .status-active {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            border: 1px solid rgba(16, 185, 129, 0.5);
        }
        .status-pause {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #ffffff;
            border: 1px solid rgba(245, 158, 11, 0.5);
        }
        .status-expired {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #ffffff;
            border: 1px solid rgba(239, 68, 68, 0.5);
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand brand-logo" href="index.php?controller=dashboard&action=cskh">
            <i class="fa-solid fa-hotel me-2 text-info"></i>ABC Resort - CSKH
        </a>
        <div class="d-flex align-items-center">
            <span class="me-3 small text-slate-300">
                <i class="fa-regular fa-user me-1"></i>
                <?= htmlspecialchars($user['Username'] ?? 'CSKH') ?>
            </span>
            <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="wrapper">
        <div class="page-title">
            <i class="fa-solid fa-gift text-warning"></i>
            Quản lý Khuyến mãi
        </div>

        <div class="action-bar">
            <a href="index.php?controller=dashboard&action=cskh" class="btn-back">
                <i class="fa-solid fa-arrow-left me-2"></i>Quay lại Dashboard
            </a>
            <div>
                <a href="index.php?controller=cskh&action=taoKhuyenMai" class="btn btn-create">
                    <i class="fa-solid fa-plus me-2"></i>Tạo mới
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th style="width: 28%;">
                            <i class="fa-solid fa-tag me-2"></i>Tên chương trình
                        </th>
                        <th style="width: 18%;" class="text-center">
                            <i class="fa-solid fa-calendar-days me-2"></i>Thời gian
                        </th>
                        <th style="width: 12%;" class="text-center">
                            <i class="fa-solid fa-percent me-2"></i>Ưu đãi
                        </th>
                        <th style="width: 15%;" class="text-center">
                            <i class="fa-solid fa-users me-2"></i>Đối tượng
                        </th>
                        <th style="width: 15%;" class="text-center">
                            <i class="fa-solid fa-signal me-2"></i>Trạng thái
                        </th>
                        <th style="width: 12%;" class="text-center">
                            <i class="fa-solid fa-pen-to-square me-2"></i>Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($listKM)): ?>
                        <?php foreach ($listKM as $km): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #8b5cf6, #6366f1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-gift" style="color: white;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="font-size: 15px;"><?= htmlspecialchars($km['TenChuongTrinh']) ?></div>
                                        <?php if(!empty($km['MaCode'])): ?>
                                            <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                                                <i class="fa-solid fa-barcode me-1"></i><?= htmlspecialchars($km['MaCode']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center" style="font-size: 13px;">
                                <div><i class="fa-solid fa-calendar-check me-1" style="color: #10b981;"></i><?= date('d/m/Y', strtotime($km['NgayBatDau'])) ?></div>
                                <div style="color: #94a3b8; margin: 3px 0;">đến</div>
                                <div><i class="fa-solid fa-calendar-xmark me-1" style="color: #ef4444;"></i><?= date('d/m/Y', strtotime($km['NgayKetThuc'])) ?></div>
                            </td>
                            <td class="text-center">
                                <div style="background: linear-gradient(135deg, #10b981, #059669); padding: 8px 16px; border-radius: 12px; display: inline-block; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);">
                                    <span class="fw-bold" style="color: #ffffff; font-size: 16px;">
                                        <?php if($km['LoaiUuDai'] == 'PERCENT'): ?>
                                            -<?= number_format($km['MucUuDai'], 0) ?>%
                                        <?php else: ?>
                                            -<?= number_format($km['MucUuDai'], 0, ',', '.') ?>đ
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div style="font-size: 13px;">
                                    <?= $km['DoiTuong'] == 'Tất cả KH' ? '<i class="fa-solid fa-users-line me-1"></i>Tất cả KH' : '<i class="fa-solid fa-user-tag me-1"></i>' . htmlspecialchars($km['DoiTuong']) ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if($km['TrangThai'] == 'DangApDung'): ?>
                                    <span class="status-badge status-active">
                                        <i class="fa-solid fa-circle-check"></i>Hoạt động
                                    </span>
                                <?php elseif($km['TrangThai'] == 'TamNgung'): ?>
                                    <span class="status-badge status-pause">
                                        <i class="fa-solid fa-pause-circle"></i>Tạm dừng
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge status-expired">
                                        <i class="fa-solid fa-circle-xmark"></i>Hết hạn
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="index.php?controller=cskh&action=action_formSuaKM_CSKH&id=<?= $km['MaKhuyenMai'] ?>" 
                                   class="link-update">
                                    <i class="fa-solid fa-pen-to-square"></i>Sửa
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4" style="color: #94a3b8;">
                                <i class="fa-solid fa-box-open fa-3x mb-3" style="opacity: 0.5;"></i><br>
                                <span style="font-size: 16px;">Chưa có chương trình khuyến mãi nào.</span>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>