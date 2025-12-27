<?php
// Sử dụng hàm e() để thoát dữ liệu an toàn cho HTML
if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

// Biến môi trường
$user = $user ?? null; // Thông tin người dùng hiện tại
$users = $users ?? []; // Danh sách người dùng
$roles = $roles ?? []; // Danh sách vai trò để chọn
$selectedUser = $selectedUser ?? null; // Người dùng được chọn để cập nhật
$errors = $errors ?? []; // Mảng lỗi
$success = $success ?? null; // Thông báo thành công

// Chuyển đổi lỗi server-side từ indexed array sang associative array (nếu cần)
$fieldErrors = [];
if ($errors && array_keys($errors) === range(0, count($errors) - 1)) {
    // Nếu là lỗi chung (indexed array)
    $generalErrors = $errors;
} else {
    // Nếu là lỗi cấp trường (associative array)
    $fieldErrors = $errors;
    $generalErrors = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật người dùng - Admin</title>
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
        input[type="email"],
        input[type="password"],
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
        select {
             /* Reset border-radius cho select để phù hợp với input, chỉ dùng 999px */
             border-radius: 999px; 
             -webkit-appearance: none;
             -moz-appearance: none;
             appearance: none;
             background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
             background-repeat: no-repeat;
             background-position: right 0.75rem center;
             background-size: 10px 10px;
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
        .alert-info{
            background:rgba(59,130,246,0.12);
            border:1px solid rgba(147,197,253,0.6);
            color:#bfdbfe;
        }
        .alert-success{
            background:rgba(16,185,129,0.15);
            border:1px solid rgba(45,212,191,0.7);
            color:#bbf7d0;
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
        
        /* Cập nhật styles cho khung tìm kiếm và danh sách */
        .list-search-container {
            padding: 0 12px;
            border-right: 1px solid rgba(148,163,184,0.3);
        }
        .search-input-group input[type="text"] {
            border-radius: 999px !important;
            padding-left: 35px;
            background-color: #0f172a;
            color: #e5e7eb;
            border: 1px solid #4b5563;
        }
        .search-input-group .fa-magnifying-glass {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: #9ca3af;
            font-size: 13px;
        }
        #userListContainer {
            height: 400px;
            overflow: auto;
            background: #0f172a;
            border-radius: 12px;
            border: 1px solid #1f2937;
            padding: 10px 14px !important;
            margin-top: 8px;
        }
        #userListContainer::-webkit-scrollbar {
            width: 6px;
        }
        #userListContainer::-webkit-scrollbar-thumb {
            background-color: #334155;
            border-radius: 3px;
        }
        .list-item-label {
            font-size: 13px;
            padding: 6px 8px;
            border-radius: 6px;
            display: block;
            cursor: pointer;
            transition: background 0.1s;
            margin-bottom: 2px;
        }
        .list-item-label:hover {
            background: rgba(148,163,184,0.1);
        }
        .list-item-radio:checked + .list-item-label {
            background: rgba(59,130,246,0.2);
            font-weight: 500;
        }
        .list-item-radio {
            display: none; /* Ẩn radio button, dùng label để click */
        }
        .list-item-label strong {
            color: #93c5fd; /* Màu nổi bật cho Username */
        }

        /* Styles cho Radio Trạng thái */
        .form-check-inline {
            display: inline-block;
            margin-right: 1rem;
        }
        .form-check-input {
            width: 1em;
            height: 1em;
            margin-top: 0.25em;
            vertical-align: top;
            background-color: #020617;
            border: 1px solid #4b5563;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: 50%;
            transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            cursor: pointer;
        }
        .form-check-input:checked {
            background-color: #22c55e;
            border-color: #16a34a;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%230b1120'/%3e%3c/svg%3e");
        }
        .form-check-label {
            font-size: 14px;
            color: #e5e7eb;
            margin-left: 0.5rem;
        }
        .form-check-input.is-invalid {
             border-color: #dc2626;
        }
    </style>
</head>
<body>
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · ADMIN</div>
        <div class="role">Cập nhật người dùng</div>
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
            <a href="index.php?controller=admin&action=nguoiDungThem">
                <i class="fa-solid fa-user-plus"></i>
                <span>Thêm người dùng</span>
            </a>
            <span class="nav-separator">|</span>
            <a href="index.php?controller=admin&action=nguoiDungCapNhat">
                <i class="fa-solid fa-user-pen"></i>
                <span>Cập nhật người dùng</span>
            </a>
            <span class="nav-separator">|</span>
            <a href="index.php?controller=admin&action=nguoiDungXoa">
                <i class="fa-solid fa-user-minus"></i>
                <span>Xóa người dùng</span>
            </a>
            <span class="nav-separator">|</span>
            <a href="index.php?controller=admin&action=phanQuyen">
                <i class="fa-solid fa-user-shield"></i>
                <span>Phân quyền người dùng</span>
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
            <h1>Cập nhật thông tin người dùng</h1>
            <span>Quản lý người dùng → Cập nhật</span>
        </div>
    </div>

    <div class="card-shell">
        <div class="row-flex" style="gap: 24px;">
            <div class="list-search-container" style="flex-basis: 300px; max-width: 300px;">
                <label class="small mb-1">Tìm kiếm người dùng</label>
                <div class="search-input-group position-relative">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input id="userSearch" type="text" placeholder="Tìm theo tên hoặc username" style="border-radius: 999px;">
                </div>

                <label class="small mt-3 mb-1">Danh sách người dùng</label>
                <div id="userListContainer">
                    <form id="userSelectorForm" method="get" action="index.php">
                        <input type="hidden" name="controller" value="admin">
                        <input type="hidden" name="action" value="nguoiDungCapNhat">
                        <?php foreach ($users as $u): ?>
                            <div>
                                <input class="list-item-radio" type="radio" name="id"
                                       value="<?= e($u['MaTK']) ?>" id="usr<?= e($u['MaTK']) ?>"
                                    <?= $selectedUser && $selectedUser['MaTK'] == $u['MaTK'] ? 'checked' : '' ?>
                                       onchange="this.form.submit()"
                                       data-username="<?= e(strtolower($u['Username'] ?? '')) ?>"
                                       data-name="<?= e(strtolower($u['TenKH'] ?? '')) ?>">
                                <label class="list-item-label" for="usr<?= e($u['MaTK']) ?>">
                                    <strong><?= e($u['Username'] ?? '') ?></strong> — <?= e($u['TenKH'] ?? '') ?>
                                    <?php if (!empty($u['TenVaiTro'])): ?>
                                        <span style="display: block; font-size: 11px; color: #6b7280; margin-top: 2px;">
                                            <i class="fa-solid fa-user-tag" style="font-size: 10px;"></i> <?= e($u['TenVaiTro']) ?>
                                        </span>
                                    <?php endif; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($users)): ?>
                            <p class="small text-secondary text-center py-2 mb-0">Không có người dùng nào.</p>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div style="flex: 1; min-width: 300px;">
                <h2><i class="fa-solid fa-pen-to-square text-blue-400 me-2"></i> Chi tiết cập nhật</h2>
                <p class="caption">
                    Thông tin sẽ được tải sau khi chọn người dùng bên trái.
                </p>

                <?php if (!empty($generalErrors)): ?>
                    <div class="alert alert-error">
                        <strong>Có lỗi xảy ra:</strong>
                        <ul class="mb-0 mt-1" style="padding-left:18px;">
                            <?php foreach ($generalErrors as $err): ?>
                                <li><?= e($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <?= e($success) ?>
                    </div>
                <?php endif; ?>

                <?php if ($selectedUser): 
                    // Xác định xem có phải nhân viên không
                    $isEmployee = in_array((int)($selectedUser['MaVaiTro'] ?? 0), [2, 3, 4, 5, 6], true);
                    // Lấy tên từ TenKH (đã được COALESCE trong query)
                    $hoTen = $selectedUser['TenKH'] ?? '';
                    $email = $selectedUser['Email'] ?? '';
                    $sdt = $selectedUser['SDT'] ?? '';
                    $cccd = $selectedUser['CCCD'] ?? '';
                    $diaChi = $selectedUser['DiaChi'] ?? '';
                    
                    // Debug: Uncomment để xem dữ liệu thực tế
                    // echo "<!-- DEBUG: " . print_r($selectedUser, true) . " -->";
                ?>
                    <form id="updateForm" method="post" action="index.php?controller=admin&action=nguoiDungCapNhat" novalidate>
                        <input type="hidden" name="MaTK" value="<?= e($selectedUser['MaTK']) ?>">
                        
                        <?php if ($isEmployee && !empty($selectedUser['ChucVu'])): ?>
                            <div class="alert alert-info" style="margin-bottom: 16px;">
                                <i class="fa-solid fa-briefcase me-2"></i>
                                <strong>Chức vụ hiện tại:</strong> <?= e($selectedUser['ChucVu']) ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row-flex mb-3">
                            <div class="field">
                                <label>Họ tên <span style="color:#dc2626">*</span></label>
                                <input type="text" name="HoTen" placeholder="Nhập họ tên..."
                                       value="<?= e($hoTen) ?>"
                                       pattern="^[a-zA-ZÀ-ỹĂăÂâĐđÊêÔôƠơƯư\s]+$" 
                                       title="Họ tên chỉ được chứa chữ cái và khoảng trắng"
                                       required minlength="2" maxlength="100">
                                <div class="invalid-feedback d-none" data-error-for="HoTen"></div>
                            </div>
                            <div class="field">
                                <label>Email <span style="color:#dc2626">*</span></label>
                                <input type="email" name="Email" placeholder="email@abc.com"
                                       value="<?= e($email) ?>"
                                       required maxlength="255">
                                <div class="invalid-feedback d-none" data-error-for="Email"></div>
                            </div>
                        </div>

                        <div class="row-flex mb-3">
                            <div class="field">
                                <label>Số điện thoại <span style="color:#dc2626">*</span></label>
                                <input type="text" name="SoDienThoai" placeholder="0xxxxxxxxx"
                                       value="<?= e($sdt) ?>"
                                       pattern="^0[35789]\d{8}$" minlength="10" maxlength="10"
                                       title="Số di động Việt Nam: 10 chữ số, bắt đầu bằng 0 và tiếp theo là 3,5,7,8 hoặc 9" required>
                                <div class="invalid-feedback d-none" data-error-for="SoDienThoai"></div>
                            </div>
                            <div class="field">
                                <label>CCCD/CMND <?php 
                                    if (!$isEmployee): 
                                ?><span style="color:#dc2626">*</span><?php endif; ?></label>
                                <input type="text" name="CCCD" placeholder="Nhập số CCCD/CMND..."
                                       value="<?= e($cccd) ?>"
                                       pattern="^[0-9]{9,12}$" title="CCCD/CMND: 9-12 chữ số" 
                                       <?php if (!$isEmployee): ?>required<?php endif; ?> minlength="9" maxlength="12">
                                <small class="text-secondary"><?php if ($isEmployee): ?>Nhân viên không bắt buộc CCCD/CMND<?php else: ?>Bắt buộc cho khách hàng<?php endif; ?></small>
                                <div class="invalid-feedback d-none" data-error-for="CCCD"></div>
                            </div>
                        </div>
                        
                        <div class="row-flex mb-3">
                            <div class="field" style="flex: 1 1 100%;">
                                <label>Địa chỉ</label>
                                <input type="text" name="DiaChi" placeholder="Nhập địa chỉ..."
                                       value="<?= e($diaChi) ?>"
                                       maxlength="200">
                                <div class="invalid-feedback d-none" data-error-for="DiaChi"></div>
                            </div>
                        </div>
                        
                        <div class="row-flex mb-3">
                            <div class="field">
                                <label>Vai trò <span style="color:#dc2626">*</span></label>
                                <select id="VaiTro" name="VaiTro" required>
                                    <option value="" disabled>-- Chọn vai trò --</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= e($role['MaVaiTro']) ?>"
                                            <?= ($selectedUser['MaVaiTro'] ?? 0) == $role['MaVaiTro'] ? 'selected' : '' ?>>
                                            <?= e($role['TenVaiTro']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback d-none" data-error-for="VaiTro"></div>
                            </div>
                            <div class="field" id="loaiKhachField" style="display: none;">
                                <label>Phân loại khách hàng <span style="color:#dc2626">*</span></label>
                                <select id="LoaiKhach" name="LoaiKhach">
                                    <option value="Cá nhân" <?= ($selectedUser['LoaiKhach'] ?? 'Cá nhân') == 'Cá nhân' ? 'selected' : '' ?>>Cá nhân</option>
                                    <option value="Trưởng đoàn" <?= ($selectedUser['LoaiKhach'] ?? '') == 'Trưởng đoàn' ? 'selected' : '' ?>>Trưởng đoàn</option>
                                    <option value="Thành viên" <?= ($selectedUser['LoaiKhach'] ?? '') == 'Thành viên' ? 'selected' : '' ?>>Thành viên</option>
                                </select>
                                <div class="invalid-feedback d-none" data-error-for="LoaiKhach"></div>
                            </div>
                        </div>

                        <div class="row-flex mb-3">
                            <div class="field">
                                <label>Tên đăng nhập <span style="color:#dc2626">*</span></label>
                                <input type="text" name="Username" placeholder="username"
                                       value="<?= e($selectedUser['Username'] ?? '') ?>"
                                       required minlength="5" maxlength="20" pattern="^[A-Za-z0-9]{5,20}$"
                                       title="Tên đăng nhập 5-20 ký tự: chỉ chứa chữ cái và số (A-Z, a-z, 0-9)">
                                <div class="invalid-feedback d-none" data-error-for="Username"></div>
                            </div>
                            <div class="field">
                                <label>Mật khẩu (Để trống nếu không đổi)</label>
                                <input type="password" name="Password" id="Password" placeholder="******"
                                       minlength="6" pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{6,}"
                                       title="Mật khẩu ít nhất 6 ký tự, bao gồm 1 chữ hoa và 1 ký tự đặc biệt">
                                <small class="text-secondary">Chỉ cần nhập nếu bạn muốn đổi mật khẩu.</small>
                                <div class="invalid-feedback d-none" data-error-for="Password"></div>
                            </div>
                        </div>

                        <div class="field" style="margin-top:12px;">
                            <label>Trạng thái <span style="color:#dc2626">*</span></label>
                            <div class="d-flex align-items-center" style="gap: 24px; margin-top: 8px;">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="TrangThai" id="tt1" required
                                           value="HoatDong"
                                        <?= ($selectedUser['TrangThai'] ?? '') === 'HoatDong' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tt1">Kích hoạt</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="TrangThai" id="tt2" required
                                           value="Ngung"
                                        <?= ($selectedUser['TrangThai'] ?? '') === 'Ngung' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tt2">Tạm ngưng</label>
                                </div>
                            </div>
                            <div class="invalid-feedback d-block mt-2" data-error-for="TrangThai"></div>
                        </div>

                        <div class="btn-row">
                            <a href="index.php?controller=dashboard&action=admin" class="btn-outline">
                                Hủy
                            </a>
                            <button type="submit" class="btn-primary-modern">
                                <i class="fa-solid fa-floppy-disk"></i>
                                Lưu thay đổi
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info" style="margin-top: 20px; border-radius: 999px;">
                        <i class="fa-solid fa-circle-info me-2"></i> Vui lòng chọn người dùng từ danh sách bên trái để xem và cập nhật thông tin.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ----------------------------------------------------------------------
    // LOGIC TÌM KIẾM VÀ CHỌN NGƯỜI DÙNG
    // ----------------------------------------------------------------------
    const searchInput = document.getElementById('userSearch');
    const userSelectorForm = document.getElementById('userSelectorForm');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const checkboxes = userSelectorForm.querySelectorAll('.list-item-radio');
            
            checkboxes.forEach(function(cb) {
                const username = cb.getAttribute('data-username') || '';
                const name = cb.getAttribute('data-name') || '';
                const combined = username + ' ' + name;
                
                const container = cb.closest('div');
                const match = query === '' || combined.includes(query);

                if (container) container.style.display = match ? 'block' : 'none';
            });
        });
    }

    // ----------------------------------------------------------------------
    // LOGIC FORM VALIDATION (cho form cập nhật)
    // ----------------------------------------------------------------------
    const updateForm = document.getElementById('updateForm');
    if (!updateForm) return;

    const fields = ['HoTen', 'Email', 'SoDienThoai', 'VaiTro', 'Username', 'Password', 'TrangThai'];
    const errorMessages = {
        HoTen: 'Họ tên chỉ được chứa chữ cái và khoảng trắng, không được có số và ký tự đặc biệt.',
        Email: 'Vui lòng nhập email hợp lệ.',
        SoDienThoai: 'Vui lòng nhập số điện thoại hợp lệ (10 chữ số, bắt đầu bằng 0).',
        CCCD: 'Vui lòng nhập CCCD/CMND hợp lệ (9-12 chữ số).',
        VaiTro: 'Vui lòng chọn vai trò.',
        Username: 'Tên đăng nhập 5-20 ký tự: chỉ chứa chữ cái và số (A-Z, a-z, 0-9).',
        Password: 'Mật khẩu phải có ít nhất 6 ký tự, bao gồm 1 chữ hoa và 1 ký tự đặc biệt.',
        TrangThai: 'Vui lòng chọn trạng thái.'
    };
    
    // Lỗi từ server-side (dùng để hiển thị khi load trang)
    const serverFieldErrors = <?= json_encode($fieldErrors, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT); ?>;

    function toggleFieldError(name, msg) {
        const el = updateForm.querySelector('[data-error-for="' + name + '"]');
        let field = updateForm.querySelector('[name="' + name + '"]');

        if (el) {
            el.textContent = msg;
            el.classList.toggle('d-none', !msg);
        }
        if (field) {
            field.classList.remove('is-valid');
            field.classList.toggle('is-invalid', !!msg);
            
            // Xử lý riêng cho radio group
            if (name === 'TrangThai') {
                const allRadios = updateForm.querySelectorAll('[name="TrangThai"]');
                allRadios.forEach(radio => {
                    radio.classList.toggle('is-invalid', !!msg);
                    radio.classList.toggle('is-valid', !msg);
                });
            } else if (!msg && field.checkValidity()) {
                field.classList.add('is-valid');
            }
        }
    }
    
    // Hiển thị lỗi server khi tải trang
    Object.keys(serverFieldErrors).forEach(name => {
        // Chỉ hiện lỗi cho trường đang tồn tại trong form
        if(updateForm.querySelector('[name="' + name + '"]')){
            toggleFieldError(name, serverFieldErrors[name]);
        }
    });

    // Thu thập lỗi cho nút submit
    function collectErrors() {
        const items = [];
        // Lấy tất cả input/select/textarea CÓ attribute required HOẶC là trường password (để check pattern nếu có giá trị)
        const checkFields = updateForm.querySelectorAll('[required], input[name="Password"]');
        
        // Kiểm tra vai trò để quyết định CCCD có bắt buộc không
        const vaiTroSelect = updateForm.querySelector('[name="VaiTro"]');
        const selectedRole = parseInt(vaiTroSelect?.value) || 0;
        const isEmployee = [2, 3, 4, 5, 6].includes(selectedRole);
        
        checkFields.forEach(f => {
            const name = f.getAttribute('name');
            const isPassword = name === 'Password';
            
            // Bỏ qua Password nếu nó trống (nghĩa là không đổi)
            if (isPassword && f.value.length === 0) return;
            
            // Bỏ qua CCCD nếu là nhân viên (không bắt buộc)
            if (name === 'CCCD' && isEmployee) {
                return;
            }
            
            // Xử lý radio (Trạng thái)
            if (name === 'TrangThai') {
                const statusChecked = updateForm.querySelectorAll('[name="TrangThai"]:checked').length > 0;
                if (!statusChecked) {
                    items.push({name: 'TrangThai', msg: errorMessages.TrangThai || 'Vui lòng chọn trạng thái.'});
                }
                return;
            }
            
            // Validation đặc biệt cho Password
            if (isPassword) {
                const value = f.value;
                if (value.length < 6) {
                    items.push({name: 'Password', msg: 'Mật khẩu phải có ít nhất 6 ký tự.'});
                } else if (!/[A-Z]/.test(value)) {
                    items.push({name: 'Password', msg: 'Mật khẩu phải có ít nhất 1 chữ hoa.'});
                } else if (!/[^A-Za-z0-9]/.test(value)) {
                    items.push({name: 'Password', msg: 'Mật khẩu phải có ít nhất 1 ký tự đặc biệt.'});
                }
                // Không cần kiểm tra số nữa
            } else if (!f.checkValidity()) {
                const custom = errorMessages[name];
                const msg = custom || f.validationMessage || 'Giá trị không hợp lệ.'; 
                items.push({name, msg});
            }
        });
        
        // Kiểm tra LoaiKhach nếu là khách hàng
        const isKhachHang = selectedRole === 7;
        if (isKhachHang) {
            const loaiKhachSelect = updateForm.querySelector('[name="LoaiKhach"]');
            if (!loaiKhachSelect || !loaiKhachSelect.value) {
                items.push({name: 'LoaiKhach', msg: 'Vui lòng chọn phân loại khách hàng.'});
            }
        }

        return items;
    }

    // Validation Real-time: blur và input/change
    const inputs = updateForm.querySelectorAll('input:not([type="radio"]), select');
    inputs.forEach(input => {
        const name = input.getAttribute('name');
        
        // Kiểm tra lỗi khi focusout (blur)
        input.addEventListener('blur', () => {
            // Kiểm tra vai trò để quyết định CCCD có bắt buộc không
            const vaiTroSelect = updateForm.querySelector('[name="VaiTro"]');
            const selectedRole = parseInt(vaiTroSelect?.value) || 0;
            const isEmployee = [2, 3, 4, 5, 6].includes(selectedRole);
            
            // Bỏ qua CCCD nếu là nhân viên và trường trống
            if (name === 'CCCD' && isEmployee && input.value.length === 0) {
                toggleFieldError(name, '');
                return;
            }
            
             // Chỉ kiểm tra lỗi nếu trường là required HOẶC là Password (dù không required nhưng có pattern)
            if (input.hasAttribute('required') || name === 'Password') {
                // Nếu là Password và trống, bỏ qua (không bắt buộc nhập)
                if(name === 'Password' && input.value.length === 0) {
                    toggleFieldError(name, '');
                    return;
                }
                
                // Validation đặc biệt cho Password
                if (name === 'Password') {
                    const value = input.value;
                    let errorMsg = '';
                    if (value.length < 6) {
                        errorMsg = 'Mật khẩu phải có ít nhất 6 ký tự.';
                    } else if (!/[A-Z]/.test(value)) {
                        errorMsg = 'Mật khẩu phải có ít nhất 1 chữ hoa.';
                    } else if (!/[^A-Za-z0-9]/.test(value)) {
                        errorMsg = 'Mật khẩu phải có ít nhất 1 ký tự đặc biệt.';
                    }
                    toggleFieldError(name, errorMsg);
                } else {
                    if (!input.checkValidity()) {
                        const msg = errorMessages[name] || input.validationMessage || 'Giá trị không hợp lệ.';
                        toggleFieldError(name, msg);
                    } else {
                        toggleFieldError(name, '');
                    }
                }
            } else if(input.value.length > 0){
                // Hiển thị lỗi ngay nếu giá trị đã nhập không hợp lệ (cho các trường không required nhưng có pattern)
                if (!input.checkValidity()) {
                    const msg = errorMessages[name] || input.validationMessage || 'Giá trị không hợp lệ.';
                    toggleFieldError(name, msg);
                } else {
                    toggleFieldError(name, '');
                }
            }
        });

        // Kiểm tra và cập nhật trạng thái ngay khi nhập (input) hoặc thay đổi (change - cho select)
        const eventType = input.tagName === 'SELECT' ? 'change' : 'input';
        input.addEventListener(eventType, () => {
            // Nếu là Password và trống, bỏ qua (không bắt buộc nhập)
            if(name === 'Password' && input.value.length === 0) {
                toggleFieldError(name, '');
                return;
            }
            
            // Validation đặc biệt cho Password
            if (name === 'Password') {
                const value = input.value;
                let errorMsg = '';
                if (value.length > 0) {
                    if (value.length < 6) {
                        errorMsg = 'Mật khẩu phải có ít nhất 6 ký tự.';
                    } else if (!/[A-Z]/.test(value)) {
                        errorMsg = 'Mật khẩu phải có ít nhất 1 chữ hoa.';
                    } else if (!/[^A-Za-z0-9]/.test(value)) {
                        errorMsg = 'Mật khẩu phải có ít nhất 1 ký tự đặc biệt.';
                    }
                }
                toggleFieldError(name, errorMsg);
            } else {
                if (input.checkValidity()) {
                    toggleFieldError(name, '');
                } else if(input.value.length > 0 || input.hasAttribute('required')){ 
                    // Hiển thị lỗi ngay nếu giá trị đã nhập không hợp lệ
                    const msg = errorMessages[name] || input.validationMessage || 'Giá trị không hợp lệ.';
                    toggleFieldError(name, msg);
                }
            }
        });
    });

    // Validation Real-time cho radio button (Trạng thái)
    const statusRadios = updateForm.querySelectorAll('[name="TrangThai"]');
    statusRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            const statusChecked = updateForm.querySelectorAll('[name="TrangThai"]:checked').length > 0;
            toggleFieldError('TrangThai', statusChecked ? '' : errorMessages.TrangThai);
        });
    });

    // Xử lý hiển thị/ẩn trường Phân loại khách hàng
    const vaiTroSelectUpdate = document.getElementById('VaiTro');
    const loaiKhachFieldUpdate = document.getElementById('loaiKhachField');
    const loaiKhachSelectUpdate = document.getElementById('LoaiKhach');
    
    function updateLoaiKhachVisibilityUpdate() {
        const selectedRole = parseInt(vaiTroSelectUpdate?.value) || 0;
        const isKhachHang = selectedRole === 7; // MaVaiTro = 7 là Khách hàng
        
        if (isKhachHang) {
            loaiKhachFieldUpdate.style.display = 'block';
            loaiKhachSelectUpdate.setAttribute('required', 'required');
        } else {
            loaiKhachFieldUpdate.style.display = 'none';
            loaiKhachSelectUpdate.removeAttribute('required');
        }
    }
    
    if (vaiTroSelectUpdate && loaiKhachFieldUpdate) {
        vaiTroSelectUpdate.addEventListener('change', updateLoaiKhachVisibilityUpdate);
        // Cập nhật ngay khi load trang
        updateLoaiKhachVisibilityUpdate();
    }


    // Xử lý khi Submit
    updateForm.addEventListener('submit', (e) => {
        // Xóa lỗi cũ để bắt đầu validation mới
        fields.forEach(name => toggleFieldError(name, ''));
        
        const errors = collectErrors();

        if (errors.length) {
            e.preventDefault();
            e.stopPropagation();
            errors.forEach(it => toggleFieldError(it.name, it.msg));
            
            // Focus vào trường đầu tiên bị lỗi
            const firstInvalid = updateForm.querySelector('.is-invalid:not([type="radio"])') || updateForm.querySelector('[data-error-for]:not(.d-none)').closest('.field').querySelector('input, select');
            if (firstInvalid) {
                firstInvalid.focus();
                // Cuộn đến trường bị lỗi
                firstInvalid.scrollIntoView({behavior: 'smooth', block: 'center'});
            }
        }
    });

});
</script>
</body>
</html>