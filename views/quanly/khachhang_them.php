<?php
$user  = Auth::user();
$errors = $errors ?? [];
$success = $success ?? '';
$old = $old ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thêm Hồ Sơ Khách Hàng - ABC Resort</title>

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
        background: rgba(15,23,42,0.85);
        border-radius: 20px;
        padding: 30px;
        border: 1px solid rgba(148,163,184,0.35);
        box-shadow: 0 20px 40px rgba(0,0,0,0.55);
        margin-top: 24px;
        margin-bottom: 24px;
    }
    .form-control {
        background: rgba(15,23,42,0.75);
        border: 1px solid rgba(148,163,184,.35);
        color: #e5e7eb;
    }
    .form-control:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 2px rgba(56,189,248,.4);
        background: rgba(15,23,42,1);
        color: #fff;
    }
    .form-control.is-invalid {
        border-color: #f97373;
        box-shadow: 0 0 0 1px rgba(248,113,113,0.6);
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
    .error-box {
        background: rgba(220,38,38,0.25);
        border-left: 4px solid #ef4444;
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 22px;
    }
    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: #f8fafc;
        margin-bottom: 8px;
    }
    .page-subtitle {
        font-size: 13px;
        color: #9ca3af;
        margin-bottom: 18px;
    }
    .badge-soft {
        background: rgba(15,118,110,0.35);
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
        color: #a5f3fc;
    }
    .text-error-inline {
        font-size: 12px;
        color: #fecaca;
        margin-top: 4px;
        display: none;
    }
    .text-error-inline.active {
        display: block;
    }
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark border-bottom border-slate-700">
    <div class="container d-flex justify-content-between">
        <a class="navbar-brand" href="index.php?controller=dashboard&action=quanly">
            <i class="fa-solid fa-hotel me-2 text-info"></i>ABC Resort - Quản lý
        </a>
        <div class="d-flex align-items-center">
            <span class="me-3 small">
                <i class="fa-regular fa-user me-1"></i>
                <?= htmlspecialchars($user['Username'] ?? 'manager') ?>
            </span>
            <a href="index.php?controller=auth&action=logout"
               class="btn btn-outline-light btn-sm">
               <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="wrapper mx-auto" style="max-width: 720px;">

        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <div class="page-title">
                    <i class="fa-solid fa-user-plus text-info me-2"></i>
                    Thêm hồ sơ khách hàng
                </div>
                <div class="page-subtitle">
                    Nhập thông tin cơ bản & tài khoản đăng nhập cho khách hàng cá nhân.
                </div>
            </div>
            <div class="text-end">
                <span class="badge-soft">
                    <i class="fa-regular fa-clipboard me-1"></i>
                    UC: Thêm Hồ sơ KH
                </span>
            </div>
        </div>

        <!-- Thông báo lỗi server-side -->
        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <strong>⚠ Có lỗi khi lưu hồ sơ:</strong>
                <ul class="mt-2 mb-0">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Thông báo thành công -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success py-2">
                <i class="fa-solid fa-circle-check me-1"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- FORM THÊM HỒ SƠ -->
        <form method="post"
              action="index.php?controller=quanly&action=luuThem"
              id="frm-khachhang">

            <!-- Họ tên -->
            <div class="mb-3">
                <label class="form-label">Họ tên khách hàng <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="TenKH"
                    id="TenKH"
                    class="form-control"
                    value="<?= htmlspecialchars($old['TenKH'] ?? '') ?>"
                    required
                >
                <div id="err_TenKH" class="text-error-inline">
                    Họ tên không được để trống.
                </div>
            </div>

            <!-- Số điện thoại -->
            <div class="mb-3">
                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="SDT"
                    id="SDT"
                    class="form-control"
                    value="<?= htmlspecialchars($old['SDT'] ?? '') ?>"
                    required
                >
                <div id="err_SDT" class="text-error-inline">
                    SĐT không hợp lệ. Ví dụ: 0901234567 hoặc +84901234567.
                </div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input
                    type="email"
                    name="Email"
                    id="Email"
                    class="form-control"
                    value="<?= htmlspecialchars($old['Email'] ?? '') ?>"
                    required
                >
                <div id="err_Email" class="text-error-inline">
                    Email không hợp lệ. Ví dụ: ten@gmail.com.
                </div>
            </div>

            <!-- CCCD/CMND -->
            <div class="mb-3">
                <label class="form-label">CCCD/CMND <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="CCCD"
                    id="CCCD"
                    class="form-control"
                    value="<?= htmlspecialchars($old['CCCD'] ?? '') ?>"
                    required
                >
                <div id="err_CCCD" class="text-error-inline">
                    CCCD/CMND phải gồm 9 hoặc 12 chữ số.
                </div>
            </div>

            <!-- Mật khẩu -->
            <div class="mb-3">
                <label class="form-label">Mật khẩu tài khoản khách hàng <span class="text-danger">*</span></label>
                <input
                    type="password"
                    name="Password"
                    id="Password"
                    class="form-control"
                    required
                >
                <div id="err_Password" class="text-error-inline">
                    Mật khẩu phải từ 6 ký tự trở lên.
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="index.php?controller=dashboard&action=quanly"
                   class="btn btn-cancel">
                    <i class="fa-solid fa-arrow-left-long me-1"></i> Quay lại dashboard
                </a>

                <button type="submit" class="btn btn-save">
                    <i class="fa-solid fa-floppy-disk me-1"></i>
                    Lưu hồ sơ
                </button>
            </div>
        </form>

    </div>
</div>

<!-- VALIDATION REALTIME JS -->
<script>
    const elTen   = document.getElementById('TenKH');
    const elSDT   = document.getElementById('SDT');
    const elEmail = document.getElementById('Email');
    const elCCCD  = document.getElementById('CCCD');
    const elPass  = document.getElementById('Password');

    const reSDT   = /^(0|\+84)\d{8,10}$/;
    const reCCCD  = /^(\d{9}|\d{12})$/;

    function setError(el, errEl, condition) {
        if (condition) {
            el.classList.add('is-invalid');
            errEl.classList.add('active');
        } else {
            el.classList.remove('is-invalid');
            errEl.classList.remove('active');
        }
    }

    elTen.addEventListener('input', () => {
        setError(elTen, document.getElementById('err_TenKH'), elTen.value.trim() === '');
    });

    elSDT.addEventListener('input', () => {
        setError(elSDT, document.getElementById('err_SDT'), !reSDT.test(elSDT.value.trim()));
    });

    elEmail.addEventListener('input', () => {
        const v = elEmail.value.trim();
        const ok = v !== '' && v.includes('@') && v.includes('.');
        setError(elEmail, document.getElementById('err_Email'), !ok);
    });

    elCCCD.addEventListener('input', () => {
        setError(elCCCD, document.getElementById('err_CCCD'), !reCCCD.test(elCCCD.value.trim()));
    });

    elPass.addEventListener('input', () => {
        setError(elPass, document.getElementById('err_Password'), elPass.value.length < 6);
    });

    // Ngăn submit nếu còn lỗi basic phía client
    document.getElementById('frm-khachhang').addEventListener('submit', function (e) {
        elTen.dispatchEvent(new Event('input'));
        elSDT.dispatchEvent(new Event('input'));
        elEmail.dispatchEvent(new Event('input'));
        elCCCD.dispatchEvent(new Event('input'));
        elPass.dispatchEvent(new Event('input'));

        const hasError =
            document.querySelectorAll('.text-error-inline.active').length > 0;

        if (hasError) {
            e.preventDefault();
        }
    });
</script>

</body>
</html>