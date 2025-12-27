<?php
// Sử dụng hàm e() để thoát dữ liệu an toàn cho HTML
if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

// Biến môi trường
$user = $user ?? null; // Thông tin người dùng hiện tại
$roles = $roles ?? []; // Danh sách vai trò để chọn
$errors = $errors ?? []; // Mảng lỗi
$success = $success ?? null; // Thông báo thành công
$old = $old ?? []; // Dữ liệu cũ đã nhập

// Tách lỗi thành lỗi cấp trường (cho inline feedback) và lỗi chung (cho thông báo chung)
$fieldErrors = [];
$generalErrors = [];
if ($errors) {
    // Nếu mảng lỗi là associative (key là tên trường)
    $isAssoc = array_keys($errors) !== range(0, count($errors) - 1);
    if ($isAssoc) {
        $fieldErrors = $errors;
    } else {
        $generalErrors = $errors;
    }
}

// Hàm trợ giúp để xác định trạng thái checked cho radio button (sử dụng TrangThai từ $old)
function checked_status($value, $old, $default) {
    if (isset($old['TrangThai'])) {
        return $old['TrangThai'] === $value ? 'checked' : '';
    }
    return $default === $value ? 'checked' : '';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm người dùng - Admin</title>
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
        /* Input có nút bên trong cần padding-right */
        .field > div[style*="position: relative"] input {
            padding-right: 90px;
        }
        .field > div[style*="position: relative"] input[type="password"] {
            padding-right: 40px;
        }
        textarea{
            border-radius:12px;
            min-height:80px;
            resize:vertical;
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

        /* Thêm style cho radio buttons */
        .radio-group-flex {
            display: flex;
            gap: 24px;
            margin-top: 8px;
        }
        .radio-group-flex input[type="radio"] {
            width: auto;
            margin-right: 6px;
            border-radius: 50%;
            border: 1px solid #4b5563;
            background: #0f172a;
        }
        .radio-group-flex input[type="radio"]:checked {
            background-color: #22c55e;
            border-color: #16a34a;
            box-shadow: 0 0 0 1px rgba(34, 197, 94, .4);
        }
        .radio-group-flex label {
            display: inline-flex;
            align-items: center;
            font-size: 14px;
            color: #e5e7eb;
            margin-bottom: 0;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · ADMIN</div>
        <div class="role">Thêm người dùng</div>
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
            <h1>Thêm người dùng mới</h1>
            <span>Quản lý người dùng → Thêm người dùng</span>
        </div>
        <a href="index.php?controller=dashboard&action=admin" class="btn-outline">
            <i class="fa-solid fa-arrow-left me-1"></i> Trở về
        </a>
    </div>

    <?php if (!empty($generalErrors)): ?>
        <div class="alert alert-error">
            <strong>Có lỗi xảy ra:</strong>
            <ul class="mb-0 mt-1" style="padding-left:18px;">
                <?php foreach ($generalErrors as $e): ?>
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

    <form method="post" action="index.php?controller=admin&action=nguoiDungThem" novalidate>
        <div class="card-shell">
            <h2><i class="fa-solid fa-user-plus text-emerald-400 me-2"></i> Thông tin tài khoản</h2>
            <p class="caption">
                Điền đầy đủ thông tin người dùng mới. Các trường có dấu <span style="color:#dc2626">*</span> là bắt buộc.
            </p>

            <div class="row-flex mb-3">
                <div class="field">
                    <label>Họ tên <span style="color:#dc2626">*</span></label>
                    <input id="HoTen" type="text" name="HoTen" placeholder="Nhập họ tên..." 
                           value="<?= e($old['HoTen'] ?? '') ?>" 
                           pattern="^[a-zA-ZÀ-ỹĂăÂâĐđÊêÔôƠơƯư\s]+$" 
                           title="Họ tên chỉ được chứa chữ cái và khoảng trắng" 
                           required minlength="2" maxlength="100">
                    <div class="invalid-feedback d-none" data-error-for="HoTen"></div>
                </div>
                <div class="field">
                    <label>Email <span style="color:#dc2626">*</span></label>
                    <input id="Email" type="email" name="Email" placeholder="email@abc.com" 
                           value="<?= e($old['Email'] ?? '') ?>" required maxlength="255">
                    <div class="invalid-feedback d-none" data-error-for="Email"></div>
                </div>
            </div>

            <div class="row-flex mb-3">
                <div class="field">
                    <label>Số điện thoại <span style="color:#dc2626">*</span></label>
                    <input id="SoDienThoai" type="text" name="SoDienThoai" placeholder="0xxxxxxxxx" 
                           value="<?= e($old['SoDienThoai'] ?? '') ?>" 
                           pattern="^0[35789]\d{8}$" title="Số di động Việt Nam: 10 chữ số, bắt đầu bằng 0 và tiếp theo là 3,5,7,8 hoặc 9" 
                           required minlength="10" maxlength="10">
                    <div class="invalid-feedback d-none" data-error-for="SoDienThoai"></div>
                </div>
                <div class="field">
                    <label>CCCD/CMND <span id="cccdRequired" style="color:#dc2626">*</span></label>
                    <input id="CCCD" type="text" name="CCCD" placeholder="Nhập số CCCD/CMND..." 
                           value="<?= e($old['CCCD'] ?? '') ?>" 
                           pattern="^[0-9]{9,12}$" title="CCCD/CMND: 9-12 chữ số" 
                           required minlength="9" maxlength="12">
                    <small class="text-secondary" id="cccdNote">Bắt buộc cho khách hàng</small>
                    <div class="invalid-feedback d-none" data-error-for="CCCD"></div>
                </div>
            </div>
            
            <div class="row-flex mb-3">
                <div class="field" style="flex: 1 1 100%;">
                    <label>Địa chỉ</label>
                    <input id="DiaChi" type="text" name="DiaChi" placeholder="Nhập địa chỉ..." 
                           value="<?= e($old['DiaChi'] ?? '') ?>" 
                           maxlength="200">
                    <div class="invalid-feedback d-none" data-error-for="DiaChi"></div>
                </div>
            </div>
            
            <div class="row-flex mb-3">
                <div class="field">
                    <label>Vai trò <span style="color:#dc2626">*</span></label>
                    <select id="VaiTro" name="VaiTro" required>
                        <option value="" disabled selected>-- Chọn vai trò --</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= e($role['MaVaiTro']) ?>"
                                <?= ($old['VaiTro'] ?? '') == $role['MaVaiTro'] ? 'selected' : '' ?>>
                                <?= e($role['TenVaiTro']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback d-none" data-error-for="VaiTro"></div>
                </div>
                <div class="field" id="loaiKhachField" style="display: none;">
                    <label>Phân loại khách hàng <span style="color:#dc2626">*</span></label>
                    <select id="LoaiKhach" name="LoaiKhach">
                        <option value="Cá nhân" <?= ($old['LoaiKhach'] ?? 'Cá nhân') == 'Cá nhân' ? 'selected' : '' ?>>Cá nhân</option>
                        <option value="Trưởng đoàn" <?= ($old['LoaiKhach'] ?? '') == 'Trưởng đoàn' ? 'selected' : '' ?>>Trưởng đoàn</option>
                        <option value="Thành viên" <?= ($old['LoaiKhach'] ?? '') == 'Thành viên' ? 'selected' : '' ?>>Thành viên</option>
                    </select>
                    <div class="invalid-feedback d-none" data-error-for="LoaiKhach"></div>
                </div>
            </div>
            
            <div class="row-flex mb-3">
                <div class="field">
                    <label>Tên đăng nhập <span style="color:#dc2626">*</span></label>
                    <div style="position: relative;">
                        <input id="Username" type="text" name="Username" placeholder="username" 
                               value="<?= e($old['Username'] ?? '') ?>" 
                               pattern="^[A-Za-z0-9]{5,20}$" title="Tên đăng nhập 5-20 ký tự: chỉ chứa chữ cái và số (A-Z, a-z, 0-9)" 
                               required minlength="5" maxlength="20">
                        <button type="button" id="suggestUsernameBtn" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: #3b82f6; color: white; border: none; border-radius: 6px; padding: 4px 10px; font-size: 11px; cursor: pointer; white-space: nowrap;">
                            <i class="fa-solid fa-wand-magic-sparkles"></i> Gợi ý
                        </button>
                    </div>
                    <small class="text-secondary" id="usernameSuggestion" style="display: none; color: #60a5fa; margin-top: 4px;"></small>
                    <div class="invalid-feedback d-none" data-error-for="Username"></div>
                </div>
                <div class="field">
                    <label>Mật khẩu <span style="color:#dc2626">*</span></label>
                    <div style="position: relative;">
                        <input id="Password" type="password" name="Password" placeholder="******" 
                               pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{6,}" title="Mật khẩu ít nhất 6 ký tự, bao gồm 1 chữ hoa và 1 ký tự đặc biệt" 
                               required minlength="6" data-password-pattern>
                        <button type="button" id="togglePasswordBtn" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: transparent; color: #9ca3af; border: none; padding: 4px 8px; cursor: pointer; font-size: 14px;">
                            <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    <small class="text-secondary" id="passwordSuggestion" style="display: none; color: #60a5fa; margin-top: 4px;"></small>
                    <div class="invalid-feedback d-none" data-error-for="Password"></div>
                </div>
            </div>

            <div class="field" style="margin-top:12px;">
                <label>Trạng thái <span style="color:#dc2626">*</span></label>
                <div class="radio-group-flex">
                    <label for="tt1">
                        <input type="radio" name="TrangThai" id="tt1" value="HoatDong" required
                               <?= checked_status('HoatDong', $old, 'HoatDong') ?>>
                        Kích hoạt
                    </label>
                    <label for="tt2">
                        <input type="radio" name="TrangThai" id="tt2" value="Ngung" required
                               <?= checked_status('Ngung', $old, '') ?>>
                        Tạm ngưng
                    </label>
                </div>
                <div class="invalid-feedback d-none" data-error-for="TrangThai"></div>
            </div>

            <div class="btn-row">
                <button type="reset" class="btn-outline">
                    Nhập lại
                </button>
                <button type="submit" class="btn-primary-modern">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Lưu người dùng mới
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form[novalidate]');
    if (!form) return;

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

    // Hàm hiển thị/ẩn lỗi trường
    function toggleFieldError(name, msg) {
        const el = form.querySelector('[data-error-for="' + name + '"]');
        let field = form.querySelector('[name="' + name + '"]');
        
        // Xử lý riêng cho radio group (chọn 1 input bất kỳ để đánh dấu class)
        if (name === 'TrangThai') {
            field = form.querySelector('[name="' + name + '"][value="HoatDong"]') || form.querySelector('[name="' + name + '"]');
        }

        if (el) {
            el.textContent = msg;
            el.classList.toggle('d-none', !msg);
        }
        if (field) {
            field.classList.remove('is-valid');
            field.classList.toggle('is-invalid', !!msg);
            if (!msg && field.checkValidity()) {
                field.classList.add('is-valid');
            }
        }
        // Riêng trường hợp radio, chỉ đánh dấu trạng thái cho feedback box, không đánh dấu từng radio
        if (name === 'TrangThai' && field) {
             const allRadios = form.querySelectorAll('[name="TrangThai"]');
             allRadios.forEach(radio => {
                radio.classList.toggle('is-invalid', !!msg);
                radio.classList.toggle('is-valid', !msg);
             });
        }
    }

    // Xóa tất cả lỗi hiện tại
    function clearAllFieldErrors() {
        const fields = ['HoTen','Email','SoDienThoai','CCCD','VaiTro','Username','Password'];
        fields.forEach(name => toggleFieldError(name, ''));
        // Xử lý riêng cho radio group TrangThai
        toggleFieldError('TrangThai', ''); 
    }

    // Thu thập lỗi
    function collectErrors() {
        const items = [];
        const requiredFields = form.querySelectorAll('[required]:not([type="radio"])');
        
        // Kiểm tra vai trò để quyết định CCCD có bắt buộc không
        const vaiTroSelect = form.querySelector('[name="VaiTro"]');
        const selectedRole = parseInt(vaiTroSelect?.value) || 0;
        const isEmployee = [2, 3, 4, 5, 6].includes(selectedRole);
        const isKhachHang = selectedRole === 7;
        
        // 1. Lỗi cho các trường input/select
        requiredFields.forEach(f => {
            // Bỏ qua CCCD nếu là nhân viên
            if (f.name === 'CCCD' && isEmployee) {
                return;
            }
            
            // Validation đặc biệt cho Password
            if (f.name === 'Password') {
                const value = f.value;
                if (value.length < 6) {
                    items.push({name: 'Password', msg: 'Mật khẩu phải có ít nhất 6 ký tự.'});
                } else if (!/[A-Z]/.test(value)) {
                    items.push({name: 'Password', msg: 'Mật khẩu phải có ít nhất 1 chữ hoa.'});
                } else if (!/[^A-Za-z0-9]/.test(value)) {
                    items.push({name: 'Password', msg: 'Mật khẩu phải có ít nhất 1 ký tự đặc biệt.'});
                }
                // Không cần kiểm tra số nữa
            } else {
                // Validation cho các trường khác
                if (!f.checkValidity()) {
                    const name = f.getAttribute('name');
                    const custom = errorMessages[name];
                    // Sử dụng custom message nếu có, ngược lại dùng validationMessage của trình duyệt
                    const msg = custom || f.validationMessage || 'Giá trị không hợp lệ.'; 
                    items.push({name, msg});
                }
            }
        });
        
        // Kiểm tra LoaiKhach nếu là khách hàng
        if (isKhachHang) {
            const loaiKhachSelect = form.querySelector('[name="LoaiKhach"]');
            if (!loaiKhachSelect || !loaiKhachSelect.value) {
                items.push({name: 'LoaiKhach', msg: 'Vui lòng chọn phân loại khách hàng.'});
            }
        }

        // 2. Lỗi cho radio group (Trạng thái)
        const statusChecked = form.querySelectorAll('[name="TrangThai"]:checked').length > 0;
        if (!statusChecked) {
            items.push({name: 'TrangThai', msg: errorMessages.TrangThai || 'Vui lòng chọn trạng thái.'});
        }

        return items;
    }

    // Xử lý khi Submit
    form.addEventListener('submit', (e) => {
        // Xóa lỗi cũ để bắt đầu validation mới
        clearAllFieldErrors(); 
        const errors = collectErrors();

        if (errors.length) {
            e.preventDefault();
            e.stopPropagation();
            errors.forEach(it => toggleFieldError(it.name, it.msg));
            
            // Focus vào trường đầu tiên bị lỗi
            const firstInvalid = form.querySelector('.is-invalid:not([type="radio"])') || form.querySelector('[data-error-for]:not(.d-none)').closest('.field').querySelector('input, select');
            if (firstInvalid) {
                firstInvalid.focus();
                // Cuộn đến trường bị lỗi
                firstInvalid.scrollIntoView({behavior: 'smooth', block: 'center'});
            }
        } else {
            clearAllFieldErrors();
        }
    });

    // Validation Real-time: blur và input/change
    const inputs = form.querySelectorAll('input:not([type="radio"]), select');
    inputs.forEach(input => {
        const name = input.getAttribute('name');
        
        // Kiểm tra lỗi khi focusout (blur)
        input.addEventListener('blur', () => {
             // Chỉ kiểm tra lỗi nếu trường là required
            if (input.hasAttribute('required')) {
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
            } else {
                toggleFieldError(name, '');
            }
        });

        // Kiểm tra và cập nhật trạng thái ngay khi nhập (input) hoặc thay đổi (change - cho select)
        const eventType = input.tagName === 'SELECT' ? 'change' : 'input';
        input.addEventListener(eventType, () => {
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
                } else if(input.value.length > 0){
                    // Hiển thị lỗi ngay nếu giá trị đã nhập không hợp lệ
                    const msg = errorMessages[name] || input.validationMessage || 'Giá trị không hợp lệ.';
                    toggleFieldError(name, msg);
                }
            }
        });
    });

    // Validation Real-time cho radio button (Trạng thái)
    const statusRadios = form.querySelectorAll('[name="TrangThai"]');
    statusRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            const statusChecked = form.querySelectorAll('[name="TrangThai"]:checked').length > 0;
            toggleFieldError('TrangThai', statusChecked ? '' : errorMessages.TrangThai);
        });
    });

    // Xử lý lỗi từ Server-side khi trang load
    const serverFieldErrors = <?= json_encode($fieldErrors, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT); ?>;
    Object.keys(serverFieldErrors).forEach(name => {
        toggleFieldError(name, serverFieldErrors[name]);
    });
    
    // Xử lý CCCD: không bắt buộc nếu là nhân viên (MaVaiTro 2-6)
    const vaiTroSelect = document.getElementById('VaiTro');
    const cccdInput = document.getElementById('CCCD');
    const cccdRequired = document.getElementById('cccdRequired');
    const cccdNote = document.getElementById('cccdNote');
    
    function updateCCCDRequirement() {
        const selectedRole = parseInt(vaiTroSelect.value) || 0;
        const isEmployee = [2, 3, 4, 5, 6].includes(selectedRole);
        
        if (isEmployee) {
            cccdInput.removeAttribute('required');
            if (cccdRequired) cccdRequired.style.display = 'none';
            if (cccdNote) cccdNote.textContent = 'Nhân viên không bắt buộc CCCD/CMND';
        } else {
            cccdInput.setAttribute('required', 'required');
            if (cccdRequired) cccdRequired.style.display = 'inline';
            if (cccdNote) cccdNote.textContent = 'Bắt buộc cho khách hàng';
        }
    }
    
    if (vaiTroSelect) {
        vaiTroSelect.addEventListener('change', updateCCCDRequirement);
        // Cập nhật ngay khi load trang
        updateCCCDRequirement();
    }
    
    // Xử lý hiển thị/ẩn trường Phân loại khách hàng
    const loaiKhachField = document.getElementById('loaiKhachField');
    const loaiKhachSelect = document.getElementById('LoaiKhach');
    
    function updateLoaiKhachVisibility() {
        const selectedRole = parseInt(vaiTroSelect.value) || 0;
        const isKhachHang = selectedRole === 7; // MaVaiTro = 7 là Khách hàng
        
        if (isKhachHang) {
            loaiKhachField.style.display = 'block';
            loaiKhachSelect.setAttribute('required', 'required');
        } else {
            loaiKhachField.style.display = 'none';
            loaiKhachSelect.removeAttribute('required');
        }
    }
    
    if (vaiTroSelect && loaiKhachField) {
        vaiTroSelect.addEventListener('change', updateLoaiKhachVisibility);
        // Cập nhật ngay khi load trang
        updateLoaiKhachVisibility();
    }

    // ========== TỰ ĐỘNG GỢI Ý USERNAME VÀ PASSWORD ==========
    const hoTenInput = document.getElementById('HoTen');
    const emailInput = document.getElementById('Email');
    const usernameInput = document.getElementById('Username');
    const passwordInput = document.getElementById('Password');

    // Hàm chuẩn hóa tên để tạo username (bỏ dấu, chuyển thành chữ thường, chỉ giữ chữ cái)
    function normalizeForUsername(text) {
        if (!text) return '';
        // Bỏ dấu tiếng Việt
        const withoutAccents = text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        // Chỉ giữ chữ cái, chuyển thành chữ thường
        return withoutAccents.replace(/[^a-zA-Z]/g, '').toLowerCase();
    }

    // Hàm tạo username từ họ tên hoặc email
    function generateUsername(hoTen, email) {
        let username = '';
        
        // Ưu tiên lấy từ họ tên
        if (hoTen && hoTen.trim() !== '') {
            username = normalizeForUsername(hoTen);
            // Lấy 5-20 ký tự đầu tiên
            if (username.length > 20) {
                username = username.substring(0, 20);
            }
            // Nếu quá ngắn, thêm phần từ email
            if (username.length < 5 && email) {
                const emailPart = email.split('@')[0].replace(/[^a-zA-Z]/g, '').toLowerCase();
                username = (username + emailPart).substring(0, 20);
            }
        } else if (email) {
            // Nếu không có họ tên, lấy từ email
            username = email.split('@')[0].replace(/[^a-zA-Z]/g, '').toLowerCase();
            if (username.length > 20) {
                username = username.substring(0, 20);
            }
        }
        
        // Đảm bảo tối thiểu 5 ký tự
        if (username.length < 5) {
            username = username.padEnd(5, 'x');
        }
        
        return username;
    }

    // Hàm tạo password từ họ tên: Tên + @ + 123
    function generatePassword(hoTen) {
        if (!hoTen || hoTen.trim() === '') return '';
        
        // Lấy từ cuối (tên) - phần sau khoảng trắng cuối cùng
        const parts = hoTen.trim().split(/\s+/);
        let ten = parts[parts.length - 1];
        
        // Bỏ dấu và chuyển chữ hoa đầu
        ten = ten.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        ten = ten.charAt(0).toUpperCase() + ten.slice(1).toLowerCase();
        
        // Format: Tên + @ + 123
        return ten + '@123';
    }

    // Hiển thị gợi ý username (không tự động điền)
    function showUsernameSuggestion() {
        const hoTen = hoTenInput.value.trim();
        const email = emailInput.value.trim();
        const suggestionEl = document.getElementById('usernameSuggestion');
        
        if (hoTen || email) {
            const suggested = generateUsername(hoTen, email);
            if (suggested.length >= 5) {
                suggestionEl.textContent = 'Gợi ý: ' + suggested;
                suggestionEl.style.display = 'block';
            } else {
                suggestionEl.style.display = 'none';
            }
        } else {
            suggestionEl.style.display = 'none';
        }
    }

    // Hiển thị gợi ý password (không tự động điền)
    function showPasswordSuggestion() {
        const hoTen = hoTenInput.value.trim();
        const suggestionEl = document.getElementById('passwordSuggestion');
        
        if (hoTen) {
            const suggested = generatePassword(hoTen);
            if (suggested) {
                suggestionEl.textContent = 'Gợi ý: ' + suggested;
                suggestionEl.style.display = 'block';
            } else {
                suggestionEl.style.display = 'none';
            }
        } else {
            suggestionEl.style.display = 'none';
        }
    }

    // Tự động điền username khi click nút gợi ý
    function applyUsernameSuggestion() {
        const hoTen = hoTenInput.value.trim();
        const email = emailInput.value.trim();
        const suggested = generateUsername(hoTen, email);
        
        if (suggested.length >= 5) {
            usernameInput.value = suggested;
            usernameInput.dispatchEvent(new Event('input'));
            // Ẩn gợi ý sau khi áp dụng
            document.getElementById('usernameSuggestion').style.display = 'none';
        }
    }

    // Tự động điền password khi click nút gợi ý (nếu có)
    function applyPasswordSuggestion() {
        const hoTen = hoTenInput.value.trim();
        const suggested = generatePassword(hoTen);
        
        if (suggested) {
            passwordInput.value = suggested;
            passwordInput.dispatchEvent(new Event('input'));
            // Ẩn gợi ý sau khi áp dụng
            document.getElementById('passwordSuggestion').style.display = 'none';
        }
    }

    // Nút gợi ý username
    const suggestUsernameBtn = document.getElementById('suggestUsernameBtn');
    if (suggestUsernameBtn) {
        suggestUsernameBtn.addEventListener('click', () => {
            applyUsernameSuggestion();
        });
    }

    // Nút hiện/ẩn mật khẩu
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');
    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            if (type === 'text') {
                togglePasswordIcon.classList.remove('fa-eye');
                togglePasswordIcon.classList.add('fa-eye-slash');
            } else {
                togglePasswordIcon.classList.remove('fa-eye-slash');
                togglePasswordIcon.classList.add('fa-eye');
            }
        });
    }

    // Lắng nghe sự kiện khi nhập Họ tên
    if (hoTenInput) {
        hoTenInput.addEventListener('input', () => {
            showUsernameSuggestion();
            showPasswordSuggestion();
        });
        
        hoTenInput.addEventListener('blur', () => {
            showUsernameSuggestion();
            showPasswordSuggestion();
        });
    }

    // Lắng nghe sự kiện khi nhập Email
    if (emailInput) {
        emailInput.addEventListener('input', () => {
            showUsernameSuggestion();
        });
        
        emailInput.addEventListener('blur', () => {
            showUsernameSuggestion();
        });
    }

    // Click vào gợi ý để áp dụng
    const usernameSuggestionEl = document.getElementById('usernameSuggestion');
    if (usernameSuggestionEl) {
        usernameSuggestionEl.style.cursor = 'pointer';
        usernameSuggestionEl.addEventListener('click', () => {
            applyUsernameSuggestion();
        });
    }

    const passwordSuggestionEl = document.getElementById('passwordSuggestion');
    if (passwordSuggestionEl) {
        passwordSuggestionEl.style.cursor = 'pointer';
        passwordSuggestionEl.addEventListener('click', () => {
            applyPasswordSuggestion();
        });
    }
});
</script>
</body>
</html>