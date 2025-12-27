<?php 
    $user = Auth::user(); 
    
    // Lấy dữ liệu cũ và lỗi từ Controller truyền sang
    $d = $data ?? $info ?? []; 
    $err = $errors ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật Khuyến mãi - CSKH</title>
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
            max-width: 800px;
            backdrop-filter: blur(10px);
        }
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
        }
        .form-label {
            color: #f1f5f9;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            background: rgba(15,23,42,0.78);
            border: 1px solid rgba(148, 163, 184, 0.3);
            color: #f8fafc;
            padding: 12px 16px;
            border-radius: 8px;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(15,23,42,0.9);
            color: #f8fafc;
            border-color: #a78bfa;
            box-shadow: 0 0 0 0.2rem rgba(167, 139, 250, 0.25);
        }
        .form-control::placeholder {
            color: #94a3b8;
        }
        .form-select option {
            background: #1e293b;
            color: #f8fafc;
        }
        .btn-save {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: white;
            padding: 12px 32px;
            font-weight: 700;
            border-radius: 8px;
            transition: 0.2s;
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
            color: white;
        }
        .btn-reset {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            color: white;
            padding: 12px 32px;
            font-weight: 700;
            border-radius: 8px;
            transition: 0.2s;
        }
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
            color: white;
        }
        .btn-back {
            background: rgba(148, 163, 184, 0.2);
            border: 1px solid rgba(148, 163, 184, 0.3);
            color: #e5e7eb;
            padding: 12px 32px;
            font-weight: 700;
            border-radius: 8px;
            transition: 0.2s;
        }
        .btn-back:hover {
            background: rgba(148, 163, 184, 0.3);
            color: #ffffff;
        }
        .btn-cancel {
            background: rgba(148, 163, 184, 0.2);
            border: 1px solid rgba(148, 163, 184, 0.3);
            color: #e5e7eb;
            padding: 12px 32px;
            font-weight: 700;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.2s;
            display: inline-block;
        }
        .btn-cancel:hover {
            background: rgba(148, 163, 184, 0.3);
            color: #ffffff;
        }
        .text-muted {
            color: #cbd5e1 !important;
        }
        .alert-info {
            background: rgba(56, 189, 248, 0.15);
            border: 1px solid rgba(56, 189, 248, 0.3);
            color: #7dd3fc;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 20px;
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
        <button type="button" class="btn-back mb-3" onclick="window.location.href='index.php?controller=cskh&action=action_xemDS_CSKH'">
            <i class="fa-solid fa-arrow-left me-2"></i>Quay về
        </button>
        
        <div class="page-title">
            <i class="fa-solid fa-pen-to-square text-warning"></i>
            Cập nhật Khuyến mãi
        </div>

        <?php if(isset($success)): ?>
            <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #86efac; border-radius: 8px; padding: 12px; margin-bottom: 20px;">
                <i class="fa-solid fa-circle-check me-2"></i><?= $success ?>
            </div>
        <?php endif; ?>

        <?php if(isset($errors['system'])): ?>
            <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; border-radius: 8px; padding: 12px; margin-bottom: 20px;">
                <i class="fa-solid fa-circle-exclamation me-2"></i><?= $errors['system'] ?>
            </div>
        <?php endif; ?>

        <div class="alert-info">
            <i class="fa-solid fa-circle-info me-2"></i>
            Đang chỉnh sửa chương trình: <strong><?= htmlspecialchars($d['TenChuongTrinh'] ?? $d['TenCTKM'] ?? '') ?></strong>
        </div>

          <form action="index.php?controller=cskh&action=action_luuSuaKM_CSKH" method="POST" id="formSuaKM">
            
            <input type="hidden" name="MaKhuyenMai" value="<?= $d['MaKhuyenMai'] ?? '' ?>">

            <div class="mb-3">
                <label class="form-label">
                    <i class="fa-solid fa-hashtag me-2"></i>Mã Khuyến Mãi
                </label>
                <input type="text" class="form-control" 
                       value="<?= str_pad($d['MaKhuyenMai'] ?? '0', 3, '0', STR_PAD_LEFT) ?>" 
                       readonly style="background-color: rgba(148,163,184,0.1); cursor: not-allowed;">
                <small class="text-muted fst-italic">Mã tự động, không thể sửa. Khách hàng sẽ chọn khuyến mãi theo tên chương trình.</small>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    <i class="fa-solid fa-tag me-2"></i>Tên chương trình <span class="text-danger">*</span>
                </label>
                <input type="text" name="TenCTKM" class="form-control <?= isset($err['TenCTKM']) ? 'is-invalid' : '' ?>" required
                       value="<?= htmlspecialchars($d['TenChuongTrinh'] ?? $d['TenCTKM'] ?? '') ?>" 
                       placeholder="Ví dụ: KHUYẾN MÃI HÈ 2025">
                <div class="invalid-feedback-custom" style="<?= isset($err['TenCTKM']) ? 'display:block;' : 'display:none;' ?>">
                    <?= $err['TenCTKM'] ?? 'Vui lòng nhập tên chương trình' ?>
                </div>
                <?php if(!isset($err['TenCTKM'])): ?>
                    <small class="text-muted fst-italic">Tên sẽ tự động chuẩn hóa (ghi hoa và xóa khoảng trắng thừa). Tên không được trùng với chương trình khác.</small>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fa-solid fa-calendar-check me-2"></i>Ngày bắt đầu <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="NgayBatDau" class="form-control <?= isset($err['NgayBatDau']) ? 'is-invalid' : '' ?>" required
                           value="<?= $d['NgayBatDau'] ?? '' ?>">
                    <div class="invalid-feedback-custom" style="<?= isset($err['NgayBatDau']) ? 'display:block;' : 'display:none;' ?>">
                        <?= $err['NgayBatDau'] ?? 'Vui lòng nhập ngày bắt đầu' ?>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fa-solid fa-calendar-xmark me-2"></i>Ngày kết thúc <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="NgayKetThuc" class="form-control <?= isset($err['NgayKetThuc']) ? 'is-invalid' : '' ?>" required
                           value="<?= $d['NgayKetThuc'] ?? '' ?>">
                    <div class="invalid-feedback-custom" style="<?= isset($err['NgayKetThuc']) ? 'display:block;' : 'display:none;' ?>">
                        <?= $err['NgayKetThuc'] ?? 'Vui lòng nhập ngày kết thúc' ?>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    <i class="fa-solid fa-money-bill-wave me-2"></i>Mức ưu đãi <span class="text-danger">*</span>
                </label>
                <input type="number" name="MucUuDai" class="form-control <?= isset($err['MucUuDai']) ? 'is-invalid' : '' ?>" required
                       value="<?= $d['MucUuDai'] ?? '' ?>" 
                       placeholder="Nhập 1-100 cho giảm % hoặc >= 1000 cho giảm tiền" 
                       min="1">
                <div class="invalid-feedback-custom" style="<?= isset($err['MucUuDai']) ? 'display:block;' : 'display:none;' ?>">
                    <?= $err['MucUuDai'] ?? 'Vui lòng nhập mức ưu đãi hợp lệ' ?>
                </div>
                <small class="text-muted fst-italic helper-text" style="<?= isset($err['MucUuDai']) ? 'display:none;' : '' ?>">
                    <i class="fa-solid fa-info-circle me-1"></i>
                    Nhập <strong>1-100</strong> để giảm theo % (ví dụ: 20 = giảm 20%). 
                    Nhập <strong>≥ 1000</strong> để giảm tiền cố định (ví dụ: 50000 = giảm 50,000đ).
                </small>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    <i class="fa-solid fa-users me-2"></i>Đối tượng áp dụng <span class="text-danger">*</span>
                </label>
                <select name="DoiTuong" class="form-select" required>
                    <option value="Tất cả KH" <?= ($d['DoiTuong'] ?? '')=='Tất cả KH'?'selected':'' ?>>Tất cả khách hàng</option>
                    <option value="Khách lẻ" <?= ($d['DoiTuong'] ?? '')=='Khách lẻ'?'selected':'' ?>>Khách lẻ</option>
                    <option value="Đoàn khách" <?= ($d['DoiTuong'] ?? '')=='Đoàn khách'?'selected':'' ?>>Đoàn khách</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">
                    <i class="fa-solid fa-signal me-2"></i>Trạng thái <span class="text-danger">*</span>
                </label>
                <select name="TrangThai" class="form-select" required>
                    <option value="DangApDung" <?= ($d['TrangThai'] ?? '')=='DangApDung'?'selected':'' ?>>Đang áp dụng</option>
                    <option value="TamNgung" <?= ($d['TrangThai'] ?? '')=='TamNgung'?'selected':'' ?>>Tạm ngưng</option>
                    <option value="HetHan" <?= ($d['TrangThai'] ?? '')=='HetHan'?'selected':'' ?>>Hết hạn</option>
                </select>
            </div>

            <div class="d-flex justify-content-center gap-3">
                <button type="button" class="btn-save" onclick="preCheckAndConfirm()">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Lưu cập nhật
                </button>
                <button type="button" class="btn-cancel" onclick="window.location.href='index.php?controller=cskh&action=action_xemDS_CSKH'">
                    <i class="fa-solid fa-xmark me-2"></i>Hủy bỏ
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Real-time validation
    
    // Tên chương trình với AJAX check trùng
    const tenInput = document.querySelector('input[name="TenCTKM"]');
    const maKhuyenMai = document.querySelector('input[name="MaKhuyenMai"]').value;
    let checkTimeout = null;
    
    tenInput.addEventListener('input', function() {
        // Debounce - đợi user ngừng gõ 500ms mới check
        clearTimeout(checkTimeout);
        const value = this.value.trim();
        const self = this;
        const feedback = this.nextElementSibling;
        
        if (value === '') {
            self.classList.remove('is-invalid', 'is-valid');
            if (feedback && feedback.classList.contains('invalid-feedback-custom')) {
                feedback.style.display = 'none';
            }
            return;
        }
        
        checkTimeout = setTimeout(function() {
            // Gọi AJAX check trùng tên (excludeId để không check chính nó)
            fetch('index.php?controller=cskh&action=checkTenKhuyenMai&ten=' + encodeURIComponent(value) + '&excludeId=' + maKhuyenMai)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        self.classList.add('is-invalid');
                        self.classList.remove('is-valid');
                        if (feedback && feedback.classList.contains('invalid-feedback-custom')) {
                            feedback.textContent = 'Tên chương trình "' + value + '" đã tồn tại! Vui lòng chọn tên khác.';
                            feedback.style.display = 'block';
                        }
                    } else {
                        self.classList.remove('is-invalid');
                        self.classList.add('is-valid');
                        if (feedback && feedback.classList.contains('invalid-feedback-custom')) {
                            feedback.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking tên:', error);
                });
        }, 500);
    });
    
    tenInput.addEventListener('blur', function() {
        const value = this.value.trim();
        const feedback = this.nextElementSibling;
        
        if (value === '') {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
            if (feedback && feedback.classList.contains('invalid-feedback-custom')) {
                feedback.textContent = 'Vui lòng nhập tên chương trình';
                feedback.style.display = 'block';
            }
        } else {
            // Blur sẽ trigger validation nếu chưa có (trường hợp user không gõ mà paste)
            if (!this.classList.contains('is-valid') && !this.classList.contains('is-invalid')) {
                // Trigger input event để check
                this.dispatchEvent(new Event('input'));
            }
        }
    });
    
    // Ngày bắt đầu
    const ngayBDInput = document.querySelector('input[name="NgayBatDau"]');
    ngayBDInput.addEventListener('blur', function() {
        validateDates();
    });
    
    // Ngày kết thúc
    const ngayKTInput = document.querySelector('input[name="NgayKetThuc"]');
    ngayKTInput.addEventListener('blur', function() {
        validateDates();
    });
    
    function validateDates() {
        const ngayBD = ngayBDInput.value;
        const ngayKT = ngayKTInput.value;
        const feedbackKT = ngayKTInput.parentElement.querySelector('.invalid-feedback-custom');
        
        if (!ngayBD || !ngayKT) return;
        
        if (ngayBD > ngayKT) {
            ngayKTInput.classList.add('is-invalid');
            ngayKTInput.classList.remove('is-valid');
            if (feedbackKT) {
                feedbackKT.textContent = 'Ngày kết thúc phải sau ngày bắt đầu';
                feedbackKT.style.display = 'block';
            }
        } else {
            const diff = (new Date(ngayKT) - new Date(ngayBD)) / (1000 * 60 * 60 * 24);
            if (diff > 365) {
                ngayKTInput.classList.add('is-invalid');
                ngayKTInput.classList.remove('is-valid');
                if (feedbackKT) {
                    feedbackKT.textContent = 'Chương trình không được kéo dài quá 1 năm (365 ngày)';
                    feedbackKT.style.display = 'block';
                }
            } else {
                ngayKTInput.classList.remove('is-invalid');
                ngayKTInput.classList.add('is-valid');
                if (feedbackKT) {
                    feedbackKT.style.display = 'none';
                }
            }
        }
    }
    
    // Mức ưu đãi
    const mucUuDaiInput = document.querySelector('input[name="MucUuDai"]');
    const mucUuDaiHelper = mucUuDaiInput.parentElement.querySelector('.helper-text');
    
    mucUuDaiInput.addEventListener('blur', function() {
        const value = parseFloat(this.value);
        const feedback = this.parentElement.querySelector('.invalid-feedback-custom');
        
        if (isNaN(value) || value <= 0) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
            if (feedback) {
                feedback.textContent = 'Số tiền giảm giá phải lớn hơn 0';
                feedback.style.display = 'block';
            }
            if (mucUuDaiHelper) mucUuDaiHelper.style.display = 'none';
        } else if (value > 100000000) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
            if (feedback) {
                feedback.textContent = 'Mức ưu đãi không được vượt quá 100,000,000đ';
                feedback.style.display = 'block';
            }
            if (mucUuDaiHelper) mucUuDaiHelper.style.display = 'none';
        } else if ((value >= 1 && value <= 100) || value >= 1000) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
            if (feedback) feedback.style.display = 'none';
            if (mucUuDaiHelper) mucUuDaiHelper.style.display = '';
        } else {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
            if (feedback) {
                feedback.textContent = 'Mức ưu đãi không hợp lệ! Nhập 1-100 cho giảm %, hoặc >= 1000 cho giảm tiền cố định.';
                feedback.style.display = 'block';
            }
            if (mucUuDaiHelper) mucUuDaiHelper.style.display = 'none';
        }
    });
</script>

<!-- Modal cảnh báo trùng lặp thời gian -->
<?php if(isset($warningOverlap)): ?>
<div class="modal fade" id="modalOverlapWarning" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: linear-gradient(135deg, rgba(251, 146, 60, 0.95), rgba(249, 115, 22, 0.95)); border: 2px solid rgba(251, 191, 36, 0.6); border-radius: 15px; box-shadow: 0 20px 60px rgba(251, 146, 60, 0.4);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fa-solid fa-triangle-exclamation fa-bounce me-2" style="color: #fef3c7;"></i>
                    Cảnh báo trùng lặp thời gian!
                </h5>
            </div>
            <div class="modal-body text-white pt-2">
                <p class="mb-2" style="font-size: 15px; line-height: 1.6;">
                    <?= $warningOverlap['message'] ?>
                </p>
                <p class="mb-0 fst-italic" style="font-size: 14px; opacity: 0.9;">
                    <i class="fa-solid fa-info-circle me-1"></i>
                    Nếu bạn tiếp tục, thay đổi vẫn được lưu và có thể áp dụng đồng thời với chương trình khác.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-4" onclick="window.history.back();" style="border-radius: 8px; font-weight: 600;">
                    <i class="fa-solid fa-xmark me-2"></i>Hủy bỏ
                </button>
                <button type="button" class="btn btn-success px-4" onclick="submitWithForce()" style="border-radius: 8px; font-weight: 600; background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fa-solid fa-check me-2"></i>Vẫn lưu
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Hiển thị modal khi có cảnh báo
    document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(document.getElementById('modalOverlapWarning'));
        modal.show();
    });
    
    // Hàm submit form với cờ force_save
    function submitWithForce() {
        var form = document.getElementById('formSuaKM');
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'force_save';
        input.value = '1';
        form.appendChild(input);
        form.submit();
    }
</script>
<?php endif; ?>

<!-- Modal xác nhận cập nhật (Bootstrap) -->
<div class="modal fade" id="modalConfirmUpdate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: linear-gradient(180deg, rgba(8,12,20,0.98), rgba(12,16,26,0.98)); border: 1px solid rgba(148,163,184,0.06); color: #e5e7eb; border-radius: 12px;">
            <div class="modal-header" style="background: linear-gradient(90deg,#0f172a,#0b1220); border-bottom: 1px solid rgba(148,163,184,0.04);">
                <h5 class="modal-title text-white"><i class="fa-solid fa-shield-check me-2" style="color:#86efac"></i> Xác nhận cập nhật</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p style="color: #cbd5e1; margin-bottom:0;">Bạn có chắc muốn cập nhật chương trình khuyến mãi này? Thao tác sẽ lưu thay đổi vào hệ thống.</p>
            </div>
            <div class="modal-footer" style="border-top: 1px solid rgba(148,163,184,0.03);">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal" style="border-radius:8px;">Hủy</button>
                <button type="button" class="btn" id="confirmUpdateBtn" style="background: linear-gradient(135deg,#10b981,#059669); color:#fff; border-radius:8px;">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showConfirmUpdateModal() {
        var modalEl = document.getElementById('modalConfirmUpdate');
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
    document.getElementById('confirmUpdateBtn').addEventListener('click', function() {
        document.getElementById('formSuaKM').submit();
    });
</script>

<script>
    // Pre-check overlap via AJAX; if overlap -> show warning modal, else show confirm modal
    function preCheckAndConfirm() {
        var doiTuong = document.querySelector('select[name="DoiTuong"]').value;
        var ngayBD = document.querySelector('input[name="NgayBatDau"]').value;
        var ngayKT = document.querySelector('input[name="NgayKetThuc"]').value;
        var excludeId = document.querySelector('input[name="MaKhuyenMai"]').value || 0;

        // Basic client validation
        if (!doiTuong || !ngayBD || !ngayKT) {
            showConfirmUpdateModal();
            return;
        }

        fetch('index.php?controller=cskh&action=checkOverlapAjax', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'DoiTuong=' + encodeURIComponent(doiTuong) + '&NgayBatDau=' + encodeURIComponent(ngayBD) + '&NgayKetThuc=' + encodeURIComponent(ngayKT) + '&excludeId=' + encodeURIComponent(excludeId)
        })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) {
                // fallback to confirm
                showConfirmUpdateModal();
                return;
            }
            if (data.overlap) {
                // populate and show warning modal
                var modalMsg = document.querySelector('#modalClientOverlap .modal-body p');
                if (modalMsg) modalMsg.textContent = data.message;
                var modalEl = document.getElementById('modalClientOverlap');
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            } else {
                showConfirmUpdateModal();
            }
        })
        .catch(err => {
            console.error('Overlap check error', err);
            showConfirmUpdateModal();
        });
    }
</script>

<!-- Client-side overlap warning modal -->
<div class="modal fade" id="modalClientOverlap" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: linear-gradient(180deg, rgba(8,12,20,0.98), rgba(12,16,26,0.98)); border: 1px solid rgba(148,163,184,0.06); color: #e5e7eb; border-radius: 12px;">
            <div class="modal-header" style="background: linear-gradient(90deg,#0f172a,#0b1220); border-bottom: 1px solid rgba(148,163,184,0.04);">
                <h5 class="modal-title text-white"><i class="fa-solid fa-triangle-exclamation me-2" style="color:#fef3c7"></i> Cảnh báo trùng lặp</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p style="color: #cbd5e1; margin-bottom:0;">Nội dung cảnh báo sẽ hiển thị ở đây.</p>
            </div>
            <div class="modal-footer" style="border-top: 1px solid rgba(148,163,184,0.03);">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal" style="border-radius:8px;">Hủy</button>
                <button type="button" class="btn" id="clientOverlapContinueBtn" style="background: linear-gradient(135deg,#10b981,#059669); color:#fff; border-radius:8px;">Vẫn lưu</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('clientOverlapContinueBtn').addEventListener('click', function() {
        // Add force_save and submit
        var form = document.getElementById('formSuaKM');
        var input = document.createElement('input'); input.type='hidden'; input.name='force_save'; input.value='1';
        form.appendChild(input);
        form.submit();
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
