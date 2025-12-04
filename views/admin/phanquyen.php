<?php
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
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Phân quyền người dùng - Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
body {
    background: radial-gradient(circle at top, #0f172a, #1e293b, #0a0f1a);
    min-height: 100vh;
    color: #e5e7eb;
    font-family: "Segoe UI", sans-serif;
}
.wrapper {
    background: rgba(15,23,42,0.87);
    border-radius: 24px;
    padding: 32px;
    border: 1px solid rgba(148,163,184,0.25);
    box-shadow: 0 25px 50px rgba(0,0,0,0.7);
    max-width: 900px;
    width: 100%;
}

.page-title {
    font-size: 26px;
    font-weight: 700;
    color: #f8fafc;
}

.form-control {
    background: rgba(15,23,42,0.8);
    border: 1px solid rgba(148,163,184,0.3);
    color: #e5e7eb;
    border-radius: 12px;
}
.form-control:focus {
    border-color: #38bdf8;
    box-shadow: 0 0 0 2px rgba(56,189,248,.35);
}

.btn-save {
    background: linear-gradient(135deg, #0ea5e9, #22c55e);
    border: none;
    color: #0f172a;
    padding: 10px 24px;
    border-radius: 12px;
    font-weight: 600;
}
.btn-cancel, .btn-secondary {
    border-radius: 12px;
    padding: 10px 22px;
    background: #475569;
    color: #fff;
    border: 1px solid rgba(148,163,184,.5);
}
.btn-secondary:hover, .btn-cancel:hover {
    background: #64748b;
}

.list-group-item {
    background: rgba(30,41,59,0.8);
    border: 1px solid rgba(148,163,184,0.2);
    color: #e5e7eb;
    border-radius: 12px !important;
    margin-bottom: 8px;
    transition: 0.25s;
}
.list-group-item:hover {
    background: rgba(51,65,85,0.95);
    transform: translateX(4px);
}

.badge-info {
    background: #0ea5e9 !important;
}

textarea {
    resize: none;
}
</style>
</head>
<body>

<div class="container py-4 d-flex justify-content-center">
<div class="wrapper">

<!-- ================= LIST MODE ================= -->
<?php if ($mode === 'list'): ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title">
            <i class="fa-solid fa-user-shield text-info me-2"></i>
            Phân quyền người dùng
        </h2>

        <a href="index.php?controller=dashboard&action=admin" class="btn btn-secondary">
            <i class="fa-solid fa-house"></i> Trang chủ Admin
        </a>
    </div>

<form method="get" action="index.php" class="mb-4">
    <input type="hidden" name="controller" value="admin">
    <input type="hidden" name="action" value="phanQuyen">

    <div class="input-group">
        <input type="text" 
               class="form-control"
               name="search"
               placeholder="Nhập tên hoặc username cần tìm..."
               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

        <button class="btn btn-primary">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </div>
</form>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<div class="list-group">
    <?php foreach ($users as $u): ?>
        <a href="index.php?controller=admin&action=phanQuyen&id=<?= $u['MaTK'] ?>"
           class="list-group-item list-group-item-action d-flex justify-content-between">
            <span>
                <b><?= $u['Username'] ?></b> — <?= $u['TenKH'] ?>
            </span>
            <span class="badge bg-info"><?= $u['TenVaiTro'] ?></span>
        </a>
    <?php endforeach; ?>
</div>

<?php endif; ?>


<!-- ================= EDIT MODE ================= -->
<?php if ($mode === 'edit'): ?>

<input type="hidden" id="roleDescJSON" value='<?= json_encode($defaultRoleDesc) ?>'>

<a href="index.php?controller=admin&action=phanQuyen"
   class="btn btn-secondary mb-3">
   <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
</a>

<h3 class="mb-3">
    <i class="fa-solid fa-user-pen text-info me-2"></i>
    Phân quyền cho: <span class="text-info"><?= $user['Username'] ?></span>
</h3>

<div class="mb-3"><b>Họ tên:</b> <?= $user['TenKH'] ?></div>
<div class="mb-3"><b>Email:</b> <?= $user['Email'] ?></div>
<div class="mb-3">
    <b>Vai trò hiện tại:</b> 
    <span class="badge bg-info"><?= $user['TenVaiTro'] ?></span>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="post" action="index.php?controller=admin&action=luuPhanQuyen">

    <input type="hidden" name="MaTK" value="<?= $user['MaTK'] ?>">

    <label class="mb-1 fw-bold">Chọn vai trò mới:</label>
    <select id="MaVaiTro" name="MaVaiTro" class="form-control mb-3" onchange="updateRoleDesc()" required>
        <?php foreach ($roles as $r): ?>
            <option value="<?= $r['MaVaiTro'] ?>"
                <?= $r['MaVaiTro'] == $user['MaVaiTro'] ? 'selected' : '' ?>>
                <?= $r['TenVaiTro'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label class="mb-1 fw-bold">Mô tả quyền:</label>
    <textarea id="MoTa" name="MoTa" class="form-control mb-3" rows="4"><?= htmlspecialchars($user['MoTaQuyen'] ?? '') ?></textarea>

    <div class="d-flex justify-content-between">
        <a href="index.php?controller=admin&action=phanQuyen" class="btn btn-cancel">Hủy</a>
        <button class="btn btn-save">Lưu</button>
    </div>

</form>
<?php endif; ?>

</div>
</div>

<script>
function updateRoleDesc() {
    let map  = JSON.parse(document.getElementById("roleDescJSON").value);
    let id   = document.getElementById("MaVaiTro").value;
    document.getElementById("MoTa").value = map[id] ?? "";
}
</script>

</body>
</html>