<?php
// Helper function
if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$user = $user ?? null;
$users = $users ?? [];
$errors = $errors ?? [];
$success = $success ?? null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xóa người dùng - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        /* STYLE CHUNG (Đồng nhất với giao diện Thêm/Cập nhật) */
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
            /* Tăng chiều rộng tối đa một chút khi không có sidebar */
            max-width:1100px;
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
        .alert-warning {
             background: rgba(251,191,36,0.15);
             border: 1px solid rgba(253,224,71,0.7);
             color: #fcd34d;
             border-radius: 10px;
             padding: 10px 14px;
        }
        .btn-outline{
            border-radius:999px;
            border:1px solid #4b5563;
            background:transparent;
            color:#e5e7eb;
            padding:8px 14px;
            font-size:13px;
            cursor:pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
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
        /* Style cho nút Xoá hiện đại */
        .btn-danger-modern{
            border:none;
            border-radius:999px;
            padding:9px 18px;
            font-size:14px;
            font-weight:500;
            color:#fee2e2;
            background:linear-gradient(135deg,#ef4444,#dc2626);
            cursor:pointer;
            display:inline-flex;
            align-items:center;
            gap:6px;
            box-shadow:0 12px 24px rgba(220,38,38,.4);
            transition:transform .12s ease,box-shadow .12s ease,filter .12s ease;
        }
        .btn-danger-modern:hover{
            transform:translateY(-1px);
            box-shadow:0 18px 38px rgba(220,38,38,.55);
            filter:brightness(1.03);
        }

        /* Bảng người dùng */
        .users-table {
            width:100%;
            border-collapse:collapse;
        }
        .users-table th,
        .users-table td {
            padding:10px 12px;
            text-align:left;
            border-bottom:1px solid rgba(148,163,184,0.2);
            font-size: 13px;
        }
        .users-table thead {
             background: rgba(148,163,184,0.1);
             position: sticky;
             top: 0;
             z-index: 10;
        }
        .users-table th {
            color:#cbd5f5;
            font-weight:600;
        }
        .users-table td {
            color:#e5e7eb;
        }
        .users-table tr:hover {
            background:rgba(127,29,29,0.1);
        }
        .users-table strong {
            color: #93c5fd;
        }
        /* Checkbox styling */
        .form-check-input {
            width: 1em;
            height: 1em;
            background-color: #020617;
            border: 1px solid #4b5563;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: 4px;
            transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
            cursor: pointer;
        }
        .form-check-input:checked {
            background-color: #dc2626;
            border-color: #991b1b;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='m6 10 3 3 6-6'/%3e%3c/svg%3e");
            background-size: 100% 100%;
            background-repeat: no-repeat;
        }
        .form-check-input:indeterminate {
             background-color: #f59e0b;
             border-color: #b45309;
             background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M5 10h10'/%3e%3c/svg%3e");
             background-size: 100% 100%;
             background-repeat: no-repeat;
        }
    </style>
</head>
<body>
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · ADMIN</div>
        <div class="role">Xóa người dùng</div>
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
            <h1>Xóa người dùng</h1>
            <span>Quản lý người dùng → Xóa</span>
        </div>
        <a href="index.php?controller=dashboard&action=admin" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Trở về
        </a>
    </div>

    <div style="flex: 1;"> 
        <div class="card-shell">
            <h2><i class="fa-solid fa-trash-can text-red-500 me-2"></i> Danh sách tài khoản</h2>
            <p class="caption">
                Chọn (các) tài khoản bạn muốn xóa khỏi hệ thống.
            </p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <strong>Có lỗi xảy ra:</strong>
                    <ul class="mb-0 mt-1" style="padding-left:18px;">
                        <?php foreach ($errors as $err): ?>
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

            <form method="post" action="index.php?controller=admin&action=nguoiDungXoa">
                <div style="max-height:500px;overflow:auto; border: 1px solid #1f2937; border-radius: 10px;">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th style="width:50px;"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                <th>Tên đăng nhập</th>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="text-center" style="color: #9ca3af; padding: 20px;">Không có người dùng nào trong hệ thống.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input user-checkbox" name="chon[]" value="<?= e($u['MaTK']) ?>">
                                        </td>
                                        <td><strong><?= e($u['Username'] ?? '') ?></strong></td>
                                        <td><?= e($u['TenKH'] ?? '') ?></td>
                                        <td><?= e($u['Email'] ?? '') ?></td>
                                        <td><?= e($u['SDT'] ?? '') ?></td>
                                        <td><span class="badge" style="background-color: #3b82f6; color: #e0f2fe;"><?= e($u['TenVaiTro'] ?? 'Chưa gán vai trò') ?></span></td>
                                        <td>
                                            <?php if (($u['TrangThai'] ?? '') === 'HoatDong'): ?>
                                                <span class="badge" style="background-color: #10b981; color: #d1fae5;">Kích hoạt</span>
                                            <?php else: ?>
                                                <span class="badge" style="background-color: #f59e0b; color: #fffbeb;">Tạm ngưng</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-warning small mt-3">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    Hành động này **không thể hoàn tác**. Vui lòng kiểm tra kỹ các tài khoản đã chọn trước khi xác nhận xóa.
                </div>

                <div class="btn-row">
                    <button type="button" onclick="window.location.href='index.php?controller=dashboard&action=admin'" class="btn-outline">
                        <i class="fa-solid fa-arrow-left"></i> Hủy và Quay lại
                    </button>
                    <button type="submit" class="btn-danger-modern" onclick="return confirm('Bạn có chắc chắn muốn xóa các người dùng đã chọn không? Hành động này không thể hoàn tác.');">
                        <i class="fa-solid fa-user-slash"></i> Xóa người dùng đã chọn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    
    if(selectAllCheckbox){
        // Xử lý khi nhấn nút "Chọn tất cả"
        selectAllCheckbox.addEventListener('change', function(){
            userCheckboxes.forEach(function(cb){
                cb.checked = selectAllCheckbox.checked;
            });
            selectAllCheckbox.indeterminate = false;
        });
        
        // Xử lý khi người dùng chọn/bỏ chọn từng mục
        userCheckboxes.forEach(function(cb){
            cb.addEventListener('change', function(){
                const total = userCheckboxes.length;
                const checkedCount = Array.from(userCheckboxes).filter(function(c){ return c.checked; }).length;
                
                if (checkedCount === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                } else if (checkedCount === total) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                }
            });
        });

        // Thiết lập trạng thái ban đầu của "Chọn tất cả"
        const initialCheckedCount = Array.from(userCheckboxes).filter(function(c){ return c.checked; }).length;
        if (initialCheckedCount > 0 && initialCheckedCount < userCheckboxes.length) {
            selectAllCheckbox.indeterminate = true;
        } else if (initialCheckedCount === userCheckboxes.length && userCheckboxes.length > 0) {
            selectAllCheckbox.checked = true;
        }
    }
});
</script>