<?php
$user = Auth::user();
$mode = $mode ?? "list";
$errors  = $errors  ?? [];
$success = $success ?? "";
$customers = $customers ?? [];
$kh = $kh ?? [];
$data = $data ?? [];
$cccd = $cccd ?? "";
$duplicates = $duplicates ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý hồ sơ khách hàng - ABC Resort</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
    body {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        min-height: 100vh;
        color: #e5e7eb;
    }
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
    .form-control {
        background: rgba(15,23,42,0.78);
        border: 1px solid rgba(148,163,184,.35);
        color: #ffffff !important;
    }
    .form-control:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 2px rgba(56,189,248,.4);
        background: rgba(15,23,42,1);
        color: #ffffff !important;
    }
    .form-control::placeholder {
        color: #94a3b8;
        opacity: 0.7;
    }
    .form-select {
        color: #ffffff !important;
    }
    .form-select option {
        background: #1e293b;
        color: #ffffff;
    }
    .page-title {
        font-size: 26px;
        font-weight: 700;
        color: #f8fafc;
        margin-bottom: 15px;
    }
    .btn-save {
        background: linear-gradient(135deg, #0ea5e9, #22c55e);
        border: none;
        color: #0f172a !important;
        font-weight: 600;
        padding: 10px 24px;
        border-radius: 12px;
    }
    .btn-cancel {
        border: 1px solid #94a3b8;
        border-radius: 12px;
        color: #e2e8f0;
        padding: 10px 24px;
    }
    .btn-secondary {
        border-radius: 12px;
        padding: 10px 24px;
        background: #475569;
        color: white;
    }
    .error-box {
        background: rgba(220,38,38,0.25);
        border-left: 4px solid #ef4444;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 15px;
    }
    .field-error { font-size: 0.75rem; color: #fca5a5; margin-top: 4px; }
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
            <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </a>
        </div>
    </div>
</nav>

<div class="container">
<div class="wrapper mx-auto" style="max-width: 900px;">


<!-- ====================================================================== -->
<!-- 🔥 MODE 1: DANH SÁCH -->
<!-- ====================================================================== -->
<?php if ($mode === "list"): ?>

    <div class="mb-3">
        <a href="index.php?controller=dashboard&action=quanly" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Trang chủ
        </a>
    </div>

    <div class="page-title">
        <i class="fa-solid fa-users me-2 text-info"></i>Danh sách khách hàng
    </div>

    <form class="mb-4" method="get" action="index.php" id="searchFormKH">
        <input type="hidden" name="controller" value="quanly">
        <input type="hidden" name="action" value="danhsachKhachHang"> 

        <div class="input-group">
            <span class="input-group-text bg-dark border-secondary text-light">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            
            <input type="text" name="keyword" id="keywordInputKH" class="form-control"
                placeholder="Nhập Mã KH, Tên, SĐT, Email hoặc CCCD..."
                value="<?= htmlspecialchars($keyword ?? '') ?>"> <button type="submit" class="btn btn-primary fw-bold px-4">Tìm kiếm</button>
            
            <?php if (!empty($keyword)): ?>
                <a href="index.php?controller=quanly&action=danhsachKhachHang" 
                   class="btn btn-danger fw-bold ms-2 rounded">
                   <i class="fa-solid fa-xmark"></i> Hủy
                </a>
            <?php endif; ?>
        </div>
        <div class="text-muted small fst-italic mt-1">* Hệ thống sẽ tự động tìm trên mọi thông tin khớp.</div>
    </form>

    <div class="text-end mb-3">
        <a href="index.php?controller=quanly&action=themKhachHang" class="btn btn-save">
            <i class="fa-solid fa-user-plus me-1"></i> Thêm khách hàng
        </a>
    </div>

    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>Mã KH</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>SĐT</th>
                <th>CCCD</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($customers as $c): ?>
            <tr>
                <td><?= $c['MaKhachHang'] ?></td>
                <td><?= $c['TenKH'] ?></td>
                <td><?= $c['Email'] ?></td>
                <td><?= $c['SDT'] ?></td>
                <td><?= $c['CCCD'] ?></td>
                <td class="text-end">
                    <a href="index.php?controller=quanly&action=sua&id=<?= $c['MaKhachHang'] ?>"
                       class="btn btn-sm btn-primary">Sửa</a>

                    <a href="index.php?controller=quanly&action=xoa&id=<?= $c['MaKhachHang'] ?>"
                       onclick="return confirm('Xóa khách hàng này?')"
                       class="btn btn-sm btn-danger">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>


<!-- ====================================================================== -->
<!-- 🔥 MODE 2: THÊM KHÁCH -->
<!-- ====================================================================== -->
<?php if ($mode === "add"): ?>

    <div class="page-title">
        <i class="fa-solid fa-user-plus text-info me-2"></i>Thêm hồ sơ khách hàng
    </div>

    <?php if (!empty($errors)): ?>
        <div class="error-box"><ul>
            <?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?>
        </ul></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post" action="index.php?controller=quanly&action=luuThem">

        <div class="mb-3">
            <label>Họ tên:</label>
            <input class="form-control" name="TenKH"
                value="<?= htmlspecialchars($data['TenKH'] ?? "") ?>" required>
        </div>

        <div class="mb-3">
            <label>Số điện thoại:</label>
            <input class="form-control" name="SDT"
                value="<?= htmlspecialchars($data['SDT'] ?? "") ?>" required>
        </div>

        <div class="mb-3">
            <label>Email:</label>
            <input class="form-control" name="Email"
                value="<?= htmlspecialchars($data['Email'] ?? "") ?>" required>
        </div>

        <div class="mb-3">
            <label>CCCD/CMND:</label>
            <input class="form-control" name="CCCD"
                value="<?= htmlspecialchars($data['CCCD'] ?? "") ?>" required>
        </div>

        <div class="mb-3">
            <label>Mật khẩu:</label>
            <input class="form-control" type="password" name="Password" required>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <a href="index.php?controller=dashboard&action=quanly"
               class="btn btn-secondary">
               <i class="fa-solid fa-arrow-left"></i> Trang chủ
            </a>

            <button class="btn btn-save">Lưu hồ sơ</button>
        </div>
    </form>

<?php endif; ?>


<!-- ====================================================================== -->
<!-- 🔥 MODE 3: CHỈNH SỬA -->
<!-- ====================================================================== -->
<?php if ($mode === "edit"): ?>

    <div class="page-title">
        <i class="fa-solid fa-user-pen text-info me-2"></i>Cập nhật hồ sơ khách hàng
    </div>

    <?php if (!empty($errors)): ?>
        <div class="error-box"><ul>
            <?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?>
        </ul></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post" action="index.php?controller=quanly&action=luuCapNhat">

        <div class="mb-3">
            <label>Mã KH:</label>
            <input class="form-control" readonly name="MaKhachHang"
                   value="<?= $kh['MaKhachHang'] ?>">
        </div>

        <div class="mb-3">
            <label>Họ tên:</label>
            <input class="form-control" name="TenKH" value="<?= $kh['TenKH'] ?>" required>
        </div>

        <div class="mb-3">
            <label>Số điện thoại:</label>
            <input class="form-control" name="SDT" value="<?= $kh['SDT'] ?>" required>
        </div>

        <div class="mb-3">
            <label>Email:</label>
            <input class="form-control" name="Email" value="<?= $kh['Email'] ?>" required>
        </div>

        <div class="mb-3">
            <label>CCCD/CMND:</label>
            <input class="form-control" name="CCCD" value="<?= $kh['CCCD'] ?>" required>
        </div>

        <div class="mb-3">
            <label>Mật khẩu mới (tuỳ chọn):</label>
            <input class="form-control" type="password" name="Password">
        </div>

        <div class="d-flex justify-content-between mt-3">
            <a href="index.php?controller=dashboard&action=quanly"
               class="btn btn-secondary">
               <i class="fa-solid fa-arrow-left"></i> Trang chủ
            </a>

            <button class="btn btn-save">Cập nhật</button>
        </div>
    </form>

<?php endif; ?>


<!-- ====================================================================== -->
<!-- 🔥 MODE 4: PHÁT HIỆN TRÙNG -->
<!-- ====================================================================== -->
<?php if ($mode === "duplicate"): ?>

    <div class="page-title">
        <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>
        Phát hiện thông tin trùng
    </div>

    <div class="error-box">
        <strong>Dữ liệu bị trùng với khách hàng đã tồn tại.</strong>
    </div>

    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>Họ tên</th>
                <th>Email</th>
                <th>SĐT</th>
                <th>CCCD</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($duplicates as $c): ?>
            <tr>
                <td><?= $c['TenKH'] ?></td>
                <td><?= $c['Email'] ?></td>
                <td><?= $c['SDT'] ?></td>
                <td><?= $c['CCCD'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-between mt-3">
        <a href="index.php?controller=quanly&action=sua&id=<?= $duplicates[0]['MaKhachHang'] ?>"
           class="btn btn-primary">
            <i class="fa-solid fa-pen-to-square me-1"></i> Cập nhật hồ sơ cũ
        </a>

        <a href="index.php?controller=quanly&action=themKhachHang"
           class="btn btn-save">
           <i class="fa-solid fa-plus me-1"></i> Nhập mới
        </a>
    </div>

<?php endif; ?>

</div>
</div>

<script>
// Auto-submit form khi gõ từ khóa (sau 500ms không gõ nữa)
let typingTimerKH;
const keywordInputKH = document.getElementById('keywordInputKH');
const searchFormKH = document.getElementById('searchFormKH');

if (keywordInputKH && searchFormKH) {
    keywordInputKH.addEventListener('keyup', function() {
        clearTimeout(typingTimerKH);
        typingTimerKH = setTimeout(function() {
            searchFormKH.submit();
        }, 500); // Đợi 500ms sau khi người dùng ngừng gõ
    });

    keywordInputKH.addEventListener('keydown', function() {
        clearTimeout(typingTimerKH);
    });
}
</script>

</body>
</html>