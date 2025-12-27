<?php
$user         = Auth::user();
$rooms        = $rooms ?? [];
$selectedRoom = $selectedRoom ?? null;
$errors       = $errors ?? [];
$success      = $success ?? null;

// Helper function
if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật phòng - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body{
            margin:0;
            font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
            background: radial-gradient(circle at top, #020617, #020617 40%, #0f172a 100%);
            color:#e5e7eb;
        }
        .topbar{
            background:rgba(15,23,42,0.96);
            backdrop-filter: blur(10px);
            color:#e5e7eb;
            padding:12px 24px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            border-bottom:1px solid #1f2937;
        }
        .brand{font-weight:700;letter-spacing:.08em;font-size:14px;text-transform:uppercase;}
        .role{font-size:13px;opacity:.8;}
        .topbar a{
            font-size:13px;
            color:#93c5fd;
            text-decoration:none;
            margin-left:0;
            padding:6px 12px;
            border-radius:6px;
            transition:all 0.2s ease;
            display:inline-flex;
            align-items:center;
            gap:6px;
        }
        .topbar a:hover{
            background:rgba(147,197,253,0.1);
            text-decoration:none;
            color:#bfdbfe;
        }
        .topbar .nav-separator{
            color:#4b5563;
            margin:0 8px;
            font-size:12px;
        }
        .topbar .nav-group{
            display:flex;
            align-items:center;
            gap:4px;
        }
        .topbar span {
            font-size:16px !important;
            font-weight:500;
            margin-right:16px;
        }
        .topbar span strong {
            font-size:16px !important;
            font-weight:700;
            color:#e5e7eb !important;
        }
        .container-page{
            max-width:1200px;
            margin:24px auto 40px;
            padding:0 16px;
        }
        .page-title{
            display:flex;
            align-items:baseline;
            justify-content:space-between;
            gap:8px;
            margin-bottom:16px;
        }
        .page-title h1{margin:0;font-size:24px;color:#f9fafb;}
        .page-title span{font-size:13px;color:#9ca3af;}

        .card-shell{
            background: linear-gradient(135deg, rgba(15,23,42,0.98), rgba(15,23,42,0.95));
            border-radius:18px;
            padding:16px 18px;
            border:1px solid rgba(148,163,184,0.35);
            box-shadow:0 18px 40px rgba(0,0,0,0.65);
            margin-bottom:16px;
        }
        .card-shell h2{margin:0 0 6px;font-size:17px;color:#e5e7eb;}
        .card-shell p.caption{margin:0 0 12px;font-size:13px;color:#9ca3af;}

        label{display:block;font-size:13px;color:#cbd5f5;margin-bottom:4px;}
        input[type="number"],
        input[type="text"],
        input[type="file"],
        select,
        textarea{
            width:100%;
            padding:7px 10px;
            border-radius:999px;
            border:1px solid #4b5563;
            font-size:14px;
            box-sizing:border-box;
            background:#020617;
            color:#e5e7eb;
            outline:none;
            transition:all .15s ease;
        }
        textarea{
            border-radius:12px;
            min-height:80px;
            resize:vertical;
        }
        input[type="file"]{
            padding:6px;
            border-radius:12px;
        }
        input:focus,select:focus,textarea:focus{
            border-color:#3b82f6;
            box-shadow:0 0 0 1px rgba(59,130,246,.4);
            background:#020617;
        }
        input.is-invalid, select.is-invalid, textarea.is-invalid{
            border-color:#dc2626;
        }
        input.is-invalid:focus, select.is-invalid:focus, textarea.is-invalid:focus{
            box-shadow:0 0 0 1px rgba(220,38,38,.4);
        }
        input.is-valid, select.is-valid, textarea.is-valid{
            border-color:#16a34a;
        }
        input.is-valid:focus, select.is-valid:focus, textarea.is-valid:focus{
            box-shadow:0 0 0 1px rgba(22,163,74,.4);
        }
        .row-flex{display:flex;flex-wrap:wrap;gap:12px;}
        .field{flex:1;min-width:180px;}

        .alert{
            border-radius:12px;
            padding:10px 12px;
            margin-bottom:12px;
            font-size:13px;
        }
        .alert-error{
            background:rgba(239,68,68,0.12);
            border:1px solid rgba(248,113,113,0.6);
            color:#fecaca;
        }
        .alert-success{
            background:rgba(16,185,129,0.15);
            border:1px solid rgba(45,212,191,0.7);
            color:#bbf7d0;
        }
        .alert-info{
            background:rgba(59,130,246,0.12);
            border:1px solid rgba(96,165,250,0.6);
            color:#bfdbfe;
        }
        .btn-primary-modern{
            border:none;
            border-radius:999px;
            padding:9px 18px;
            font-size:14px;
            font-weight:500;
            color:#0b1120;
            background:linear-gradient(135deg,#22c55e,#a3e635);
            cursor:pointer;
            display:inline-flex;
            align-items:center;
            gap:6px;
            box-shadow:0 12px 24px rgba(22,163,74,.4);
            transition:transform .12s ease,box-shadow .12s ease,filter .12s ease;
        }
        .btn-primary-modern:hover{
            transform:translateY(-1px);
            box-shadow:0 18px 38px rgba(22,163,74,.55);
            filter:brightness(1.03);
        }
        .btn-outline{
            border-radius:999px;
            border:1px solid #4b5563;
            background:transparent;
            color:#e5e7eb;
            padding:8px 14px;
            font-size:13px;
            cursor:pointer;
        }
        .btn-outline:hover{
            background:#020617;
        }
        .btn-row{
            margin-top:10px;
            display:flex;
            justify-content:flex-end;
            gap:10px;
            flex-wrap:wrap;
            align-items:center;
        }
        .invalid-feedback{
            display:block;
            color:#dc2626;
            font-size:12px;
            margin-top:4px;
        }
        .invalid-feedback.d-none{
            display:none;
        }
        small.text-secondary{
            color:#9ca3af;
            font-size:12px;
        }

        .room-list-container{
            border:1px solid rgba(148,163,184,0.3);
            border-radius:12px;
            background:rgba(15,23,42,0.5);
            padding:12px;
            max-height:400px;
            overflow-y:auto;
        }
        .room-item{
            padding:8px 10px;
            border-bottom:1px solid rgba(148,163,184,0.2);
            cursor:pointer;
            transition:.15s;
            border-radius:8px;
            margin-bottom:4px;
        }
        .room-item:hover{
            background:rgba(30,64,175,0.15);
        }
        .room-item.active{
            background:linear-gradient(135deg,rgba(34,197,94,0.15),rgba(34,197,94,0.05));
            border-left:3px solid #22c55e;
            padding-left:7px;
        }
        .form-check-input{
            margin-right:8px;
            width:1em;
            height:1em;
            background-color:#020617;
            border:1px solid #4b5563;
            border-radius:50%;
            cursor:pointer;
        }
        .form-check-input:checked{
            background-color:#22c55e;
            border-color:#16a34a;
        }
        .form-check-label{
            cursor:pointer;
            color:#e5e7eb;
            font-size:13px;
        }
        .search-box{
            border-radius:999px !important;
            background:#020617 !important;
            border:1px solid #4b5563 !important;
            color:#e5e7eb !important;
        }
        .search-box:focus{
            border-color:#3b82f6 !important;
            box-shadow:0 0 0 1px rgba(59,130,246,.4) !important;
        }

        .filter-section{
            background:rgba(30,41,59,0.5);
            border-radius:12px;
            padding:14px;
            margin-bottom:16px;
            border:1px solid rgba(148,163,184,0.25);
        }
        .filter-group{
            display:inline-block;
            margin-right:16px;
            margin-bottom:8px;
        }
        .filter-group label{
            font-size:.9rem;
            color:#9ca3af;
            margin-right:8px;
            margin-bottom:4px;
            text-transform:uppercase;
            letter-spacing:.03em;
            display:block;
        }
        .filter-group select,
        .filter-group input{
            font-size:1rem;
            padding:8px 12px;
            border-radius:999px;
            background:#020617;
            border:1px solid #4b5563;
            color:#e5e7eb;
        }
        .filter-group select:focus,
        .filter-group input:focus{
            border-color:#3b82f6;
            box-shadow:0 0 0 1px rgba(59,130,246,.4);
        }

        .rooms-table{
            width:100%;
            border-collapse:collapse;
            font-size:14px;
        }
        .rooms-table thead{
            background:rgba(30,41,59,0.5);
            border-bottom:2px solid rgba(148,163,184,0.45);
        }
        .rooms-table th{
            padding:12px 14px;
            text-align:left;
            color:#cbd5f5;
            font-weight:600;
            font-size:13px;
        }
        .rooms-table td{
            padding:12px 14px;
            border-bottom:1px solid rgba(148,163,184,0.2);
            color:#e5e7eb;
            font-size:14px;
        }
        .rooms-table tbody tr{
            transition:.15s;
            cursor:pointer;
        }
        .rooms-table tbody tr:hover{
            background:rgba(30,64,175,0.15);
        }
        .rooms-table tbody tr.selected{
            background:linear-gradient(135deg,rgba(34,197,94,0.15),rgba(34,197,94,0.05));
            border-left:4px solid #22c55e;
        }
        .rooms-table tbody tr strong{
            font-size:15px;
            font-weight:600;
        }
        .room-table-container{
            border-radius:12px;
            border:1px solid rgba(148,163,184,0.3);
            overflow:hidden;
            background:rgba(15,23,42,0.5);
            margin-bottom:16px;
        }
        .room-table-scroll{
            max-height:500px;
            overflow-y:auto;
            overflow-x:auto;
        }
        /* Khi chưa chọn phòng - bảng to hơn */
        .card-shell:only-child .room-table-scroll{
            max-height:650px;
        }
        .card-shell:only-child .rooms-table{
            font-size:15px;
        }
        .card-shell:only-child .rooms-table th{
            padding:14px 16px;
            font-size:14px;
        }
        .card-shell:only-child .rooms-table td{
            padding:14px 16px;
            font-size:15px;
        }
        .status-badge{
            display:inline-block;
            padding:5px 12px;
            border-radius:20px;
            font-size:.85rem;
            font-weight:600;
        }
        .status-hoatdong{
            background:rgba(34,197,94,0.2);
            color:#86efac;
            border:1px solid rgba(34,197,94,0.5);
        }
        .status-ngung{
            background:rgba(249,115,22,0.2);
            color:#fdba74;
            border:1px solid rgba(249,115,22,0.5);
        }
        .status-baotrisinh{
            background:rgba(59,130,246,0.2);
            color:#93c5fd;
            border:1px solid rgba(59,130,246,0.5);
        }
    </style>
</head>
<body>
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · ADMIN</div>
        <div class="role">Cập nhật phòng</div>
    </div>
    <div style="display:flex;align-items:center;gap:8px;">
        <?php if ($user): ?>
            <span style="font-size:13px;margin-right:8px;">
                Xin chào, <strong><?= e($user['Username'] ?? 'Admin') ?></strong>
            </span>
            <span class="nav-separator">|</span>
        <?php endif; ?>
        <div class="nav-group">
            <a href="index.php?controller=home&action=index">
                <i class="fa-solid fa-home"></i>
                <span>Trang chủ</span>
            </a>
            <span class="nav-separator">|</span>
            <a href="index.php?controller=dashboard&action=admin">
                <i class="fa-solid fa-gauge"></i>
                <span>Dashboard</span>
            </a>
            <span class="nav-separator">|</span>
            <a href="index.php?controller=admin&action=phongThem">
                <i class="fa-solid fa-plus"></i>
                <span>Thêm phòng</span>
            </a>
            <span class="nav-separator">|</span>
            <a href="index.php?controller=admin&action=phongSua">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Cập nhật phòng</span>
            </a>
            <span class="nav-separator">|</span>
            <a href="index.php?controller=admin&action=phongXoa">
                <i class="fa-solid fa-trash-can"></i>
                <span>Xóa phòng</span>
            </a>
            <span class="nav-separator">|</span>
            <a href="index.php?controller=auth&action=logout" style="color:#f87171;">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Đăng xuất</span>
            </a>
        </div>
    </div>
</div>

<div class="container-page">
    <div class="page-title">
        <div>
            <h1>Cập nhật phòng</h1>
            <span>Quản lý phòng → Cập nhật phòng</span>
        </div>
        <div>
            <a href="index.php?controller=dashboard&action=admin" class="btn-outline" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-arrow-left"></i>
                Trở về
            </a>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <strong>Có lỗi xảy ra:</strong>
            <ul class="mb-0 mt-1" style="padding-left:18px;">
                <?php foreach ($errors as $e): ?>
                    <li><?= e($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?= e($success) ?>
        </div>
    <?php endif; ?>

    <?php if (!$selectedRoom): ?>
        <!-- Full width khi chưa chọn phòng -->
        <div class="card-shell">
            <h2><i class="fa-solid fa-list me-2"></i> Danh sách phòng</h2>
            <p class="caption">
                Chọn phòng cần cập nhật từ danh sách bên dưới.
            </p>
    <?php else: ?>
        <!-- Layout 2 cột khi đã chọn phòng -->
        <div class="row-flex" style="gap:16px;">
            <!-- Left: Room List -->
            <div class="field" style="flex:0 0 380px;max-width:380px;">
                <div class="card-shell">
                    <h2><i class="fa-solid fa-list me-2"></i> Danh sách phòng</h2>
                    <p class="caption">
                        Chọn phòng cần cập nhật từ danh sách bên dưới.
                    </p>
    <?php endif; ?>

                <!-- Bộ lọc -->
                <div class="filter-section">
                    <div class="filter-group">
                        <label>Loại phòng:</label>
                        <select id="filterLoaiPhong">
                            <option value="">— Tất cả —</option>
                            <option value="standard">Standard</option>
                            <option value="deluxe">Deluxe</option>
                            <option value="superior">Superior</option>
                            <option value="suite">Suite</option>
                            <option value="vip">VIP</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>View phòng:</label>
                        <select id="filterViewPhong">
                            <option value="">— Tất cả —</option>
                            <option value="biển">Biển</option>
                            <option value="thành phố">Thành phố</option>
                            <option value="vườn">Vườn</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Tình trạng:</label>
                        <select id="filterTinhTrang">
                            <option value="">— Tất cả —</option>
                            <option value="tot">Tốt</option>
                            <option value="canvesinh">Cần vệ sinh</option>
                            <option value="huhahe">Hư hại nhẹ</option>
                            <option value="huhainang">Hư hại nặng</option>
                            <option value="dangbaotri">Đang bảo trì</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Trạng thái:</label>
                        <select id="filterTrangThai">
                            <option value="">— Tất cả —</option>
                            <option value="trong">Trống</option>
                            <option value="booked">Booked</option>
                            <option value="stayed">Stayed</option>
                            <option value="baotri">Bảo trì</option>
                        </select>
                    </div>

                    <div style="display:block;margin-top:8px;">
                        <div class="filter-group">
                            <label>Tìm kiếm:</label>
                            <input id="roomSearch" type="text" placeholder="Số phòng, loại giường, giá...">
                        </div>
                        <button type="button" class="btn-outline" id="resetFilters" style="margin-left:8px;">
                            Đặt lại
                        </button>
                    </div>
                </div>

                <form id="roomSelectorForm" method="get" action="index.php">
                    <input type="hidden" name="controller" value="admin">
                    <input type="hidden" name="action" value="phongSua">
                    <div class="room-table-container">
                        <div class="room-table-scroll">
                            <table class="rooms-table">
                                <thead>
                                    <tr>
                                        <th style="min-width:<?= !$selectedRoom ? '120px' : '100px' ?>;">Số Phòng</th>
                                        <th style="min-width:<?= !$selectedRoom ? '100px' : '90px' ?>;">Loại</th>
                                        <?php if (!$selectedRoom): ?>
                                            <th style="min-width:100px;">Loại Giường</th>
                                            <th style="min-width:80px;">Diện Tích</th>
                                            <th style="min-width:90px;">View</th>
                                        <?php endif; ?>
                                        <th style="min-width:<?= !$selectedRoom ? '130px' : '120px' ?>;">Giá</th>
                                        <th style="min-width:<?= !$selectedRoom ? '100px' : '110px' ?>;">Tình Trạng</th>
                                        <?php if (!$selectedRoom): ?>
                                            <th style="min-width:100px;">Trạng Thái</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody id="roomListContainer">
                                    <?php if (empty($rooms)): ?>
                                        <tr>
                                            <td colspan="<?= !$selectedRoom ? '8' : '4' ?>" class="text-center text-secondary py-4" style="text-align:center;padding:16px;color:#9ca3af;">
                                                <i class="fa-solid fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                                Chưa có dữ liệu phòng.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($rooms as $room): ?>
                                            <tr class="room-row <?= $selectedRoom && $selectedRoom['MaPhong'] == $room['MaPhong'] ? 'selected' : '' ?>"
                                                data-search="<?= e(strtolower($room['SoPhong'] . ' ' . $room['LoaiPhong'] . ' ' . ($room['LoaiGiuong'] ?? '') . ' ' . ($room['ViewPhong'] ?? '') . ' ' . ($room['Gia'] ?? ''))) ?>"
                                                data-loai="<?= e(strtolower($room['LoaiPhong'] ?? '')) ?>"
                                                data-view="<?= e(strtolower($room['ViewPhong'] ?? '')) ?>"
                                                data-tinhtrang="<?= e(strtolower($room['TinhTrangPhong'] ?? 'tot')) ?>"
                                                data-trangthai="<?= e(strtolower($room['TrangThai'] ?? 'trong')) ?>"
                                                onclick="document.getElementById('ph<?= $room['MaPhong'] ?>').checked = true; document.getElementById('roomSelectorForm').submit();">
                                                <td>
                                                    <input class="form-check-input" type="radio" name="id"
                                                           value="<?= $room['MaPhong'] ?>" id="ph<?= $room['MaPhong'] ?>"
                                                        <?= $selectedRoom && $selectedRoom['MaPhong'] == $room['MaPhong'] ? 'checked' : '' ?>
                                                        style="margin:0;cursor:pointer;width:<?= !$selectedRoom ? '20px' : '18px' ?>;height:<?= !$selectedRoom ? '20px' : '18px' ?>;">
                                                    <strong style="margin-left:8px;font-size:<?= !$selectedRoom ? '16px' : '15px' ?>;"><?= e($room['SoPhong']) ?></strong>
                                                </td>
                                                <td><?= e($room['LoaiPhong'] ?? '—') ?></td>
                                                <?php if (!$selectedRoom): ?>
                                                    <td><?= e($room['LoaiGiuong'] ?? '—') ?></td>
                                                    <td><?= e($room['DienTich'] ?? '—') ?> m²</td>
                                                    <td><?= e($room['ViewPhong'] ?? '—') ?></td>
                                                <?php endif; ?>
                                                <td><?= number_format($room['Gia'] ?? 0, 0, ',', '.') ?> đ</td>
                                                <td>
                                                    <?php
                                                        $tinhTrang = $room['TinhTrangPhong'] ?? 'Tot';
                                                        $tinhtrangMap = [
                                                            'Tot' => 'Tốt', 
                                                            'CanVeSinh' => 'Cần vệ sinh',
                                                            'HuHaiNhe' => 'Hư hại nhẹ',
                                                            'HuHaiNang' => 'Hư hại nặng',
                                                            'DangBaoTri' => 'Đang bảo trì'
                                                        ];
                                                        $tinhTrangText = $tinhtrangMap[$tinhTrang] ?? $tinhTrang;
                                                        
                                                        $badgeClass = 'status-hoatdong';
                                                        if ($tinhTrang === 'CanVeSinh') $badgeClass = 'status-ngung';
                                                        elseif ($tinhTrang === 'HuHaiNhe' || $tinhTrang === 'HuHaiNang') $badgeClass = 'status-baotrisinh';
                                                        elseif ($tinhTrang === 'DangBaoTri') $badgeClass = 'status-ngung';
                                                    ?>
                                                    <span class="status-badge <?= $badgeClass ?>">
                                                        <?= $tinhTrangText ?>
                                                    </span>
                                                </td>
                                                <?php if (!$selectedRoom): ?>
                                                    <td>
                                                        <?php
                                                            $trangThai = $room['TrangThai'] ?? 'Trong';
                                                            $thaiMap = ['Trong' => 'Trống', 'Booked' => 'Booked', 'Stayed' => 'Stayed', 'BaoTri' => 'Bảo trì'];
                                                            $thaiText = $thaiMap[$trangThai] ?? $trangThai;
                                                        ?>
                                                        <span class="status-badge status-hoatdong">
                                                            <?= e($thaiText) ?>
                                                        </span>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            <?php if (!$selectedRoom): ?>
                </div>
            <?php else: ?>
            </div>
        </div>

        <!-- Right: Form -->
        <div class="field" style="flex:1;">
            <form action="index.php?controller=admin&action=phongSua" method="post" enctype="multipart/form-data" novalidate>
                <div class="card-shell">
                    <h2><i class="fa-solid fa-pen-to-square text-emerald-400 me-2"></i> Thông tin phòng: <?= e($selectedRoom['SoPhong']) ?></h2>
                    <p class="caption">
                        Cập nhật thông tin phòng. Các trường có dấu <span style="color:#dc2626">*</span> là bắt buộc.
                    </p>

                    <input type="hidden" name="MaPhong" value="<?= $selectedRoom['MaPhong'] ?>">

                        <div class="row-flex">
                            <div class="field">
                                <label>Số phòng <span style="color:#dc2626">*</span></label>
                                <input type="text" name="SoPhong" placeholder="VD: Pa101"
                                       value="<?= e($selectedRoom['SoPhong'] ?? '') ?>" required>
                                <div class="invalid-feedback d-none" data-error-for="SoPhong"></div>
                            </div>
                            <div class="field">
                                <label>Diện tích (m²) <span style="color:#dc2626">*</span></label>
                                <input type="number" step="0.1" name="DienTich" min="15"
                                       value="<?= e($selectedRoom['DienTich'] ?? '') ?>" required>
                                <div class="invalid-feedback d-none" data-error-for="DienTich"></div>
                            </div>
                            <div class="field">
                                <label>Loại giường <span style="color:#dc2626">*</span></label>
                                <input type="text" name="LoaiGiuong" placeholder="VD: 1 giường đôi"
                                       value="<?= e($selectedRoom['LoaiGiuong'] ?? '') ?>" 
                                       list="loaiGiuongSuggestions" required>
                                <datalist id="loaiGiuongSuggestions">
                                    <?php 
                                    $loaiGiuongList = $loaiGiuongList ?? [];
                                    foreach ($loaiGiuongList as $loaiGiuong): 
                                        if (!empty($loaiGiuong)):
                                    ?>
                                        <option value="<?= e($loaiGiuong) ?>"><?= e($loaiGiuong) ?></option>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </datalist>
                                <small class="text-secondary">Gõ để xem gợi ý hoặc nhập giá trị tùy ý</small>
                                <div class="invalid-feedback d-none" data-error-for="LoaiGiuong"></div>
                            </div>
                        </div>

                        <div class="row-flex">
                            <div class="field">
                                <label>Loại phòng <span style="color:#dc2626">*</span></label>
                                <select name="LoaiPhong" required>
                                    <option value="" disabled>-- Chọn loại phòng --</option>
                                    <option value="Standard" <?= ($selectedRoom['LoaiPhong'] ?? '') === 'Standard' ? 'selected' : '' ?>>Standard</option>
                                    <option value="Deluxe" <?= ($selectedRoom['LoaiPhong'] ?? '') === 'Deluxe' ? 'selected' : '' ?>>Deluxe</option>
                                    <option value="Superior" <?= ($selectedRoom['LoaiPhong'] ?? '') === 'Superior' ? 'selected' : '' ?>>Superior</option>
                                    <option value="Suite" <?= ($selectedRoom['LoaiPhong'] ?? '') === 'Suite' ? 'selected' : '' ?>>Suite</option>
                                    <option value="VIP" <?= ($selectedRoom['LoaiPhong'] ?? '') === 'VIP' ? 'selected' : '' ?>>VIP</option>
                                </select>
                                <div class="invalid-feedback d-none" data-error-for="LoaiPhong"></div>
                            </div>
                            <div class="field">
                                <label>View phòng <span style="color:#dc2626">*</span></label>
                                <select name="ViewPhong" required>
                                    <option value="" disabled>-- Chọn view phòng --</option>
                                    <option value="Biển" <?= ($selectedRoom['ViewPhong'] ?? '') === 'Biển' ? 'selected' : '' ?>>Biển</option>
                                    <option value="Thành phố" <?= ($selectedRoom['ViewPhong'] ?? '') === 'Thành phố' ? 'selected' : '' ?>>Thành phố</option>
                                    <option value="Vườn" <?= ($selectedRoom['ViewPhong'] ?? '') === 'Vườn' ? 'selected' : '' ?>>Vườn</option>
                                </select>
                                <div class="invalid-feedback d-none" data-error-for="ViewPhong"></div>
                            </div>
                            <div class="field">
                                <label>Tình trạng</label>
                                <select name="TinhTrangPhong">
                                    <option value="Tot" <?= ($selectedRoom['TinhTrangPhong'] ?? 'Tot') === 'Tot' ? 'selected' : '' ?>>Tốt</option>
                                    <option value="CanVeSinh" <?= ($selectedRoom['TinhTrangPhong'] ?? '') === 'CanVeSinh' ? 'selected' : '' ?>>Cần vệ sinh</option>
                                    <option value="HuHaiNhe" <?= ($selectedRoom['TinhTrangPhong'] ?? '') === 'HuHaiNhe' ? 'selected' : '' ?>>Hư hại nhẹ</option>
                                    <option value="HuHaiNang" <?= ($selectedRoom['TinhTrangPhong'] ?? '') === 'HuHaiNang' ? 'selected' : '' ?>>Hư hại nặng</option>
                                    <option value="DangBaoTri" <?= ($selectedRoom['TinhTrangPhong'] ?? '') === 'DangBaoTri' ? 'selected' : '' ?>>Đang bảo trì</option>
                                </select>
                                <div class="invalid-feedback d-none" data-error-for="TinhTrangPhong"></div>
                            </div>
                        </div>

                        <div class="row-flex">
                            <div class="field">
                                <label>Giá (VND) <span style="color:#dc2626">*</span></label>
                                <input type="number" name="Gia" min="0" placeholder="VD: 1000000 (1,000,000 VND)"
                                       value="<?= e($selectedRoom['Gia'] ?? '') ?>" 
                                       list="giaSuggestions" required>
                                <small class="text-secondary">Nhập giá trị trực tiếp bằng VND (ví dụ: 1000000 = 1,000,000 VND)</small>
                                <datalist id="giaSuggestions">
                                    <option value="500000">500,000 VND</option>
                                    <option value="660000">660,000 VND</option>
                                    <option value="680000">680,000 VND</option>
                                    <option value="700000">700,000 VND</option>
                                    <option value="720000">720,000 VND</option>
                                    <option value="800000">800,000 VND</option>
                                    <option value="820000">820,000 VND</option>
                                    <option value="830000">830,000 VND</option>
                                    <option value="850000">850,000 VND</option>
                                    <option value="880000">880,000 VND</option>
                                    <option value="900000">900,000 VND</option>
                                    <option value="950000">950,000 VND</option>
                                    <option value="1000000">1,000,000 VND</option>
                                    <option value="1200000">1,200,000 VND</option>
                                    <option value="1500000">1,500,000 VND</option>
                                    <option value="2000000">2,000,000 VND</option>
                                </datalist>
                                <small class="text-secondary">Gõ để xem gợi ý hoặc nhập giá trị tùy ý</small>
                                <div class="invalid-feedback d-none" data-error-for="Gia"></div>
                            </div>
                            <div class="field">
                                <label>Số khách tối đa <span style="color:#dc2626">*</span></label>
                                <input type="number" name="SoKhachToiDa" min="1" placeholder="VD: 2"
                                       value="<?= e($selectedRoom['SoKhachToiDa'] ?? '') ?>" required>
                                <div class="invalid-feedback d-none" data-error-for="SoKhachToiDa"></div>
                            </div>
                            <div class="field">
                                <label>Hình ảnh</label>
                                <?php if (!empty($selectedRoom['HinhAnh'])): ?>
                                    <small class="text-secondary d-block mb-2">
                                        File hiện tại: <strong><?= e($selectedRoom['HinhAnh']) ?></strong>
                                    </small>
                                    <div id="currentImagePreview" style="margin-bottom: 12px;">
                                        <img src="uploads/phong/<?= e($selectedRoom['HinhAnh']) ?>" alt="Current" style="max-width: 100%; max-height: 300px; border-radius: 12px; border: 1px solid #4b5563; object-fit: contain;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="HinhAnh" id="hinhAnhInput" accept="image/*">
                                <div id="imagePreview" style="margin-top: 12px; display: none;">
                                    <img id="previewImg" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 12px; border: 1px solid #4b5563; object-fit: contain;">
                                    <button type="button" id="removeImageBtn" style="margin-top: 8px; padding: 6px 12px; background: #dc2626; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 12px;">
                                        <i class="fa-solid fa-trash"></i> Xóa ảnh
                                    </button>
                                </div>
                                <small class="text-secondary">Để trống nếu không muốn thay đổi. Chỉ hỗ trợ JPG, PNG, GIF, WebP (tối đa 5MB)</small>
                            </div>
                        </div>

                        <div class="field" style="margin-top:12px;">
                            <label>Ghi chú</label>
                            <textarea name="GhiChu" rows="3" placeholder="Nhập ghi chú về phòng (nếu có)..."><?= e($selectedRoom['GhiChu'] ?? '') ?></textarea>
                        </div>

                        <div class="btn-row">
                            <button type="button" class="btn-outline" onclick="window.location.href='index.php?controller=admin&action=phongSua'">
                                Hủy
                            </button>
                            <button type="submit" class="btn-primary-modern">
                                <i class="fa-solid fa-floppy-disk"></i>
                                Lưu thay đổi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Image preview
    const hinhAnhInput = document.getElementById('hinhAnhInput');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeImageBtn = document.getElementById('removeImageBtn');
    const currentImagePreview = document.getElementById('currentImagePreview');

    if (hinhAnhInput && imagePreview && previewImg) {
        hinhAnhInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Kiểm tra loại file
                if (!file.type.match('image.*')) {
                    alert('Vui lòng chọn file hình ảnh!');
                    e.target.value = '';
                    return;
                }
                
                // Kiểm tra kích thước (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File quá lớn! Vui lòng chọn file nhỏ hơn 5MB.');
                    e.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                    // Ẩn ảnh hiện tại nếu có
                    if (currentImagePreview) {
                        currentImagePreview.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', function() {
                hinhAnhInput.value = '';
                previewImg.src = '';
                imagePreview.style.display = 'none';
                // Hiện lại ảnh hiện tại nếu có
                if (currentImagePreview) {
                    currentImagePreview.style.display = 'block';
                }
            });
        }
    }

    // Room search and filter
    const search = document.getElementById('roomSearch');
    const filterLoai = document.getElementById('filterLoaiPhong');
    const filterView = document.getElementById('filterViewPhong');
    const filterTinhTrang = document.getElementById('filterTinhTrang');
    const filterTrangThai = document.getElementById('filterTrangThai');
    const resetBtn = document.getElementById('resetFilters');
    const container = document.getElementById('roomListContainer');

    if (container) {
        function applyFilters() {
            const q = (search ? search.value.trim().toLowerCase() : '');
            const loaiVal = (filterLoai ? filterLoai.value.toLowerCase() : '');
            const viewVal = (filterView ? filterView.value.toLowerCase() : '');
            const tinhTrangVal = (filterTinhTrang ? filterTinhTrang.value.toLowerCase() : '');
            const thaiVal = (filterTrangThai ? filterTrangThai.value.toLowerCase() : '');

            const rows = container.querySelectorAll('.room-row');
            rows.forEach(row => {
                const searchData = (row.getAttribute('data-search') || '').toLowerCase();
                const loai = (row.getAttribute('data-loai') || '').toLowerCase();
                const view = (row.getAttribute('data-view') || '').toLowerCase();
                const tinhTrang = (row.getAttribute('data-tinhtrang') || '').toLowerCase();
                const thai = (row.getAttribute('data-trangthai') || '').toLowerCase();

                const matchSearch = searchData.includes(q);
                const matchLoai = loaiVal === '' || loai.includes(loaiVal);
                const matchView = viewVal === '' || view.includes(viewVal);
                const matchTinhTrang = tinhTrangVal === '' || tinhTrang.includes(tinhTrangVal);
                const matchThai = thaiVal === '' || thai.includes(thaiVal);

                row.style.display = (matchSearch && matchLoai && matchView && matchTinhTrang && matchThai) ? '' : 'none';
            });
        }

        if (search) search.addEventListener('input', applyFilters);
        if (filterLoai) filterLoai.addEventListener('change', applyFilters);
        if (filterView) filterView.addEventListener('change', applyFilters);
        if (filterTinhTrang) filterTinhTrang.addEventListener('change', applyFilters);
        if (filterTrangThai) filterTrangThai.addEventListener('change', applyFilters);

        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                if (search) search.value = '';
                if (filterLoai) filterLoai.value = '';
                if (filterView) filterView.value = '';
                if (filterTinhTrang) filterTinhTrang.value = '';
                if (filterTrangThai) filterTrangThai.value = '';
                applyFilters();
            });
        }
    }

    // Form validation
    const form = document.querySelector('form[novalidate]');
    if (!form) return;

    const errorMessages = {
        SoPhong: 'Vui lòng nhập số phòng.',
        DienTich: 'Diện tích phải lớn hơn hoặc bằng 15m².',
        LoaiGiuong: 'Vui lòng nhập loại giường.',
        LoaiPhong: 'Vui lòng chọn loại phòng.',
        ViewPhong: 'Vui lòng nhập view phòng.',
        Gia: 'Giá phải lớn hơn hoặc bằng 0.',
        SoKhachToiDa: 'Số khách tối đa phải lớn hơn 0.'
    };

    function showFieldError(name, msg) {
        const el = form.querySelector('[data-error-for="' + name + '"]');
        const field = form.querySelector('[name="' + name + '"]');
        if (el) {
            el.textContent = msg;
            el.classList.remove('d-none');
        }
        if (field) {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
        }
    }

    function hideFieldError(name) {
        const el = form.querySelector('[data-error-for="' + name + '"]');
        const field = form.querySelector('[name="' + name + '"]');
        if (el) {
            el.textContent = '';
            el.classList.add('d-none');
        }
        if (field) {
            field.classList.remove('is-invalid');
            if (field.checkValidity()) {
                field.classList.add('is-valid');
            }
        }
    }

    function clearAllFieldErrors() {
        const els = form.querySelectorAll('[data-error-for]');
        els.forEach(e => { e.textContent = ''; e.classList.add('d-none'); });
        const fields = form.querySelectorAll('input, select, textarea');
        fields.forEach(f => { 
            f.classList.remove('is-invalid');
            if (f.checkValidity()) {
                f.classList.add('is-valid');
            }
        });
    }

    function collectErrors() {
        const items = [];
        const fields = form.querySelectorAll('input[required], select[required], textarea[required]');
        fields.forEach(f => {
            const name = f.getAttribute('name') || f.id || 'field';
            
            // Validation đặc biệt cho trường Gia
            if (name === 'Gia') {
                const value = parseFloat(f.value);
                if (isNaN(value) || value === '') {
                    items.push({name, msg: 'Vui lòng nhập giá.'});
                } else if (value < 0) {
                    items.push({name, msg: 'Giá phải lớn hơn hoặc bằng 0.'});
                }
                // Không kiểm tra step vì cho phép nhập bất kỳ giá trị nào >= 0
            } else {
                // Validation cho các trường khác
                if (!f.checkValidity()) {
                    const custom = errorMessages[name];
                    const msg = custom || f.validationMessage || 'Giá trị không hợp lệ.';
                    items.push({name, msg});
                }
            }
        });
        return items;
    }

    // On submit
    form.addEventListener('submit', (e) => {
        clearAllFieldErrors();
        const errors = collectErrors();
        if (errors.length) {
            e.preventDefault();
            e.stopPropagation();
            errors.forEach(it => showFieldError(it.name, it.msg));
            const first = form.querySelector('.is-invalid');
            if (first) {
                first.focus();
                first.scrollIntoView({behavior: 'smooth', block: 'center'});
            }
        } else {
            clearAllFieldErrors();
        }
    });

    // Real-time validation
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', () => {
            const name = input.getAttribute('name') || input.id;
            
            // Validation đặc biệt cho trường Gia
            if (name === 'Gia') {
                const value = parseFloat(input.value);
                if (input.hasAttribute('required') && (isNaN(value) || input.value === '')) {
                    showFieldError(name, 'Vui lòng nhập giá.');
                } else if (!isNaN(value) && value < 0) {
                    showFieldError(name, 'Giá phải lớn hơn hoặc bằng 0.');
                } else {
                    hideFieldError(name);
                }
            } else {
                if (!input.checkValidity()) {
                    const msg = errorMessages[name] || input.validationMessage || 'Giá trị không hợp lệ.';
                    showFieldError(name, msg);
                } else {
                    hideFieldError(name);
                }
            }
        });

        input.addEventListener('input', () => {
            const name = input.getAttribute('name') || input.id;
            
            // Validation đặc biệt cho trường Gia
            if (name === 'Gia') {
                const value = parseFloat(input.value);
                if (input.value === '') {
                    // Cho phép để trống khi đang gõ
                    hideFieldError(name);
                } else if (isNaN(value)) {
                    showFieldError(name, 'Giá phải là số.');
                } else if (value < 0) {
                    showFieldError(name, 'Giá phải lớn hơn hoặc bằng 0.');
                } else {
                    hideFieldError(name);
                }
            } else {
                if (input.checkValidity()) {
                    hideFieldError(name);
                } else {
                    const msg = errorMessages[name] || input.validationMessage || 'Giá trị không hợp lệ.';
                    showFieldError(name, msg);
                }
            }
        });

        input.addEventListener('change', () => {
            const name = input.getAttribute('name') || input.id;
            if (input.checkValidity()) {
                hideFieldError(name);
            }
        });
    });
});
</script>
</body>
</html>
