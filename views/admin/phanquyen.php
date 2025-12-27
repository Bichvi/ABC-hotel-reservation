<?php
// Helper function
if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$mode    = $mode ?? 'list'; 
$users   = $users ?? [];
$user    = $user  ?? []; 
$roles   = $roles ?? [];
$error   = $error ?? '';
$success = $success ?? '';

/* ❇ Mô tả quyền mặc định theo vai trò */
$defaultRoleDesc = [
    1 => "Quản trị hệ thống: toàn quyền dữ liệu, phân quyền và giám sát toàn bộ hệ thống.",
    2 => "Nhân viên lễ tân: đặt phòng, check-in/out, xem tình trạng phòng.",
    3 => "Kế toán: xuất hóa đơn, xem báo cáo doanh thu, quản lý chi phí.",
    4 => "Nhân viên dịch vụ: nhận yêu cầu dịch vụ, xử lý và theo dõi tiến độ.",
    5 => "CSKH: phản hồi khách hàng, tạo và quản lý khuyến mãi.",
    6 => "Quản lý: giám sát tổng thể, xem báo cáo chi tiết, quản lý nhân sự.",
    7 => "Khách hàng: xem thông tin cá nhân và lịch sử đặt phòng."
];

// Luôn đặt mode là 'edit' nếu có $user được chọn
if (!empty($user)) {
    $mode = 'edit';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Phân quyền người dùng - Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
/* STYLE CHUNG (Đồng nhất với các giao diện khác) */
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
    border:1px solid rgba(147,197,253,0.6);
    color:#bfdbfe;
    border-radius: 999px;
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

/* STYLE RIÊNG CHO PHÂN QUYỀN */
.list-search-container {
    padding: 0 12px;
}
.search-input-group {
    position: relative;
    display: flex;
    align-items: center;
}
.search-input-group input[type="text"] {
    width: 100%;
    padding: 7px 10px 7px 35px;
    border-radius: 999px;
    border: 1px solid #4b5563;
    font-size: 14px;
    background:#020617;
    color:#e5e7eb;
    outline:none;
    transition:all .15s ease;
}
.search-input-group .fa-magnifying-glass {
    position: absolute;
    left: 12px;
    color: #9ca3af;
    font-size: 13px;
}
#userListContainer {
    height: 400px;
    overflow-y: auto;
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
    display: none;
}
.list-item-label strong {
    color: #93c5fd;
}
.current-role-badge {
    background-color: #0ea5e9;
    color: #fff;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 999px;
    font-size: 12px;
}

/* Form */
.form-info-line {
    font-size: 14px;
    margin-bottom: 8px;
    color: #cbd5f5;
}
.form-info-line b {
    color: #e5e7eb;
}
.role-select {
    width: 100%;
    padding:7px 10px;
    border-radius:999px;
    border:1px solid #4b5563;
    font-size:14px;
    background:#020617;
    color:#e5e7eb;
    outline:none;
    transition:all .15s ease;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 10px 10px;
}
textarea {
    resize: vertical;
    border-radius: 12px !important;
    min-height: 100px;
}
label {
    display: block;
    font-size: 13px;
    color: #cbd5f5;
    margin-bottom: 4px;
}
</style>
</head>
<body>
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · ADMIN</div>
        <div class="role">Phân quyền người dùng</div>
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
            <h1>Phân quyền người dùng</h1>
            <span>Quản lý người dùng → Phân quyền</span>
        </div>
        <a href="index.php?controller=dashboard&action=admin" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Trở về
        </a>
    </div>

    <div class="card-shell">
        <div class="d-flex" style="gap: 24px;">
            <div style="flex-basis: 350px; max-width: 350px;">
                <h2><i class="fa-solid fa-users text-blue-400 me-2"></i> Chọn tài khoản</h2>
                <p class="caption">
                    Tìm và chọn người dùng để thay đổi vai trò.
                </p>

                <div class="search-input-group mb-2">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input id="userSearch" type="text" 
                           placeholder="Nhập tên hoặc username cần tìm...">
                </div>

                <div id="userListContainer" style="display: block !important;">
                    <form id="userSelectorForm" method="get" action="index.php">
                        <input type="hidden" name="controller" value="admin">
                        <input type="hidden" name="action" value="phanQuyen">
                        
                        <?php if (empty($users)): ?>
                             <p class="small text-secondary text-center py-2 mb-0">Không có người dùng nào.</p>
                        <?php endif; ?>
                        
                        <?php foreach ($users as $u): ?>
                            <div>
                                <input class="list-item-radio" type="radio" name="id"
                                       value="<?= e($u['MaTK']) ?>" id="usr<?= e($u['MaTK']) ?>"
                                    <?= ($mode === 'edit' && $user && $user['MaTK'] == $u['MaTK']) ? 'checked' : '' ?>
                                       onchange="this.form.submit()"
                                       data-username="<?= e(strtolower($u['Username'] ?? '')) ?>"
                                       data-name="<?= e(strtolower($u['TenKH'] ?? '')) ?>">
                                <label class="list-item-label" for="usr<?= e($u['MaTK']) ?>">
                                    <strong><?= e($u['Username'] ?? '') ?></strong> — <?= e($u['TenKH'] ?? '') ?>
                                    <span class="current-role-badge float-end"><?= e($u['TenVaiTro'] ?? 'N/A') ?></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </form>
                </div>
            </div>

            <div style="flex: 1; padding-left: 12px; border-left: 1px solid rgba(148,163,184,0.3);">
                
                <h2><i class="fa-solid fa-user-gear text-orange-400 me-2"></i> Cấu hình vai trò</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?= e($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= e($success) ?></div>
                <?php endif; ?>

                <?php if ($mode === 'edit' && $user): ?>
                    <p class="caption">
                        Cập nhật vai trò và mô tả quyền cho tài khoản <span class="text-info"><?= e($user['Username']) ?></span>.
                    </p>

                    <div class="form-info-line"><b>Họ tên:</b> <?= e($user['TenKH'] ?? 'N/A') ?></div>
                    <div class="form-info-line"><b>Email:</b> <?= e($user['Email'] ?? 'N/A') ?></div>
                    <div class="form-info-line mb-3">
                        <b>Vai trò hiện tại:</b> 
                        <span class="current-role-badge" style="background-color: #ef4444;"><?= e($user['TenVaiTro'] ?? 'N/A') ?></span>
                    </div>

                    <form id="roleForm" method="post" action="index.php?controller=admin&action=luuPhanQuyen">

                        <input type="hidden" name="MaTK" value="<?= e($user['MaTK']) ?>">
                        <input type="hidden" id="roleDescJSON" value='<?= e(json_encode($defaultRoleDesc)) ?>'>

                        <div class="mb-3">
                            <label for="MaVaiTro">Chọn vai trò mới:</label>
                            <select id="MaVaiTro" name="MaVaiTro" class="role-select" onchange="updateRoleDesc()" required>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= e($r['MaVaiTro']) ?>"
                                        <?= $r['MaVaiTro'] == ($user['MaVaiTro'] ?? 0) ? 'selected' : '' ?>>
                                        <?= e($r['TenVaiTro']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="MoTa">Mô tả quyền:</label>
                            <textarea id="MoTa" name="MoTa" class="role-select" rows="4"><?= e($user['MoTaQuyen'] ?? $defaultRoleDesc[$user['MaVaiTro']] ?? '') ?></textarea>
                            <small class="text-secondary d-block mt-1">Nội dung này được tạo tự động dựa trên vai trò nhưng có thể chỉnh sửa.</small>
                        </div>

                        <div class="btn-row justify-content-end">
                            <a href="index.php?controller=admin&action=phanQuyen" class="btn-outline">
                                <i class="fa-solid fa-times"></i> Hủy
                            </a>
                            <button class="btn-primary-modern">
                                <i class="fa-solid fa-floppy-disk"></i> Lưu phân quyền
                            </button>
                        </div>

                    </form>
                <?php else: ?>
                    <div class="alert alert-info" style="margin-top: 20px;">
                        <i class="fa-solid fa-circle-info me-2"></i> Vui lòng chọn người dùng từ danh sách bên trái để phân quyền.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // === Logic Tra cứu mô tả vai trò ===
    function updateRoleDesc() {
        const mapEl = document.getElementById("roleDescJSON");
        const roleIdEl = document.getElementById("MaVaiTro");
        const moTaEl = document.getElementById("MoTa");

        if (!mapEl || !roleIdEl || !moTaEl) return;
        
        try {
            const map = JSON.parse(mapEl.value);
            const id = roleIdEl.value;
            moTaEl.value = map[id] ?? "";
        } catch (e) {
            console.error("Lỗi khi phân tích JSON mô tả vai trò:", e);
            moTaEl.value = "Không thể tải mô tả vai trò mặc định.";
        }
    }
    window.updateRoleDesc = updateRoleDesc;

    if (document.getElementById("MaVaiTro")) {
        const moTaEl = document.getElementById("MoTa");
        if (!moTaEl.value.trim()) {
             updateRoleDesc();
        }
    }

    // === Logic Tìm kiếm (Đảm bảo danh sách vẫn hiện) ===
    const searchInput = document.getElementById('userSearch');
    const userSelectorForm = document.getElementById('userSelectorForm');
    const userListContainer = document.getElementById('userListContainer');

    // Đảm bảo danh sách luôn hiển thị
    if (userListContainer) {
        userListContainer.style.display = 'block';
        // Theo dõi và đảm bảo container không bị ẩn
        const observer = new MutationObserver(function(mutations) {
            if (userListContainer.style.display === 'none') {
                userListContainer.style.display = 'block';
            }
        });
        observer.observe(userListContainer, { attributes: true, attributeFilter: ['style'] });
    }

    if (searchInput && userSelectorForm) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const listItems = userSelectorForm.querySelectorAll('.list-item-radio');
            
            listItems.forEach(function(item) {
                const username = item.getAttribute('data-username') || '';
                const name = item.getAttribute('data-name') || '';
                const combined = username + ' ' + name;
                const container = item.closest('div');
                
                const match = query === '' || combined.includes(query);

                if (container) {
                    container.style.display = match ? 'block' : 'none';
                }
            });
            
            // Đảm bảo container luôn hiển thị sau khi filter
            if (userListContainer) {
                userListContainer.style.display = 'block';
            }
        });
    }
});
</script>

</body>
</html>