<?php 
    $user = Auth::user(); 
    
    // Lấy dữ liệu cũ và lỗi từ Controller truyền sang
    $d = $data ?? []; 
    $err = $errors ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo Khuyến Mãi - CSKH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        /* --- GIAO DIỆN DARK MODE --- */
        body { background: linear-gradient(135deg, #0f172a, #1e293b); min-height: 100vh; color: #e5e7eb; font-family: 'Segoe UI', sans-serif; }
        .navbar { background: rgba(15, 23, 42, 0.95) !important; backdrop-filter: blur(10px); border-bottom: 1px solid rgba(148, 163, 184, 0.2); }
        .wrapper { background: radial-gradient(circle at top left, rgba(168, 85, 247, 0.15), rgba(15,23,42,0.95)); border-radius: 20px; padding: 35px; border: 1px solid rgba(148, 163, 184, 0.3); box-shadow: 0 20px 60px rgba(0,0,0,0.6); margin: 40px auto; max-width: 800px; backdrop-filter: blur(10px); }
        .page-title { font-size: 30px; font-weight: 700; color: #f8fafc; margin-bottom: 25px; text-align: center; text-transform: uppercase; letter-spacing: 1px; }
        .form-label { color: #f1f5f9; font-weight: 600; font-size: 14px; margin-bottom: 8px; }
        
        /* INPUT STYLE */
        .form-control, .form-select { background: rgba(15,23,42,0.78); border: 1px solid rgba(148, 163, 184, 0.3); color: #f8fafc; padding: 12px 16px; border-radius: 8px; }
        .form-control:focus, .form-select:focus { background: rgba(15,23,42,0.9); color: #f8fafc; border-color: #a78bfa; box-shadow: 0 0 0 0.2rem rgba(167, 139, 250, 0.25); }
        .input-group-text { background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; font-weight: bold; }
        
        /* CLASS IN HOA */
        .text-uppercase-input { text-transform: uppercase; }

        /* BUTTONS */
        .btn-save { background: linear-gradient(135deg, #10b981, #059669); border: none; color: white; padding: 12px 32px; font-weight: 700; border-radius: 8px; transition: 0.2s; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4); color: white; }
        .btn-reset { background: linear-gradient(135deg, #3b82f6, #2563eb); border: none; color: white; padding: 12px 32px; font-weight: 700; border-radius: 8px; transition: 0.2s; }
        .btn-reset:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4); color: white; }
        .btn-back { background: rgba(148, 163, 184, 0.2); border: 1px solid rgba(148, 163, 184, 0.3); color: #e5e7eb; padding: 12px 32px; font-weight: 700; border-radius: 8px; transition: 0.2s; }
        .btn-back:hover { background: rgba(148, 163, 184, 0.3); color: #ffffff; }
        .btn-cancel { background: rgba(148, 163, 184, 0.2); border: 1px solid rgba(148, 163, 184, 0.3); color: #e5e7eb; padding: 12px 32px; font-weight: 700; border-radius: 8px; text-decoration: none; transition: 0.2s; display: inline-block; }
        .btn-cancel:hover { background: rgba(148, 163, 184, 0.3); color: #ffffff; }

        /* CSS HIỂN THỊ LỖI */
        .is-invalid { border-color: #ef4444 !important; }
        .invalid-feedback-custom { color: #ef4444; font-size: 0.85rem; margin-top: 5px; font-style: italic; display: block; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php?controller=dashboard&action=cskh">
            <i class="fa-solid fa-hotel me-2 text-info"></i>ABC Resort - CSKH
        </a>
        <div class="d-flex align-items-center">
            <span class="me-3 small text-white-50"><i class="fa-regular fa-user me-1"></i> <?= htmlspecialchars($user['Username'] ?? 'CSKH') ?></span>
            <a href="index.php?controller=auth&action=logout" class="btn btn-sm btn-outline-light"><i class="fa-solid fa-right-from-bracket"></i></a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="wrapper">
        <button type="button" class="btn-back mb-3" onclick="window.location.href='index.php?controller=cskh&action=action_xemDS_CSKH'">
            <i class="fa-solid fa-arrow-left me-2"></i>Quay về
        </button>
        
        <h3 class="page-title text-success">
            <i class="fa-solid fa-wand-magic-sparkles me-2"></i>Tạo Chương Trình Khuyến Mãi
        </h3>

        <?php if(isset($success)): ?>
            <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #86efac; border-radius: 8px;">
                <i class="fa-solid fa-circle-check me-2"></i><?= $success ?>
            </div>
        <?php endif; ?>

        <?php if(isset($errors['system'])): ?>
            <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; border-radius: 8px;">
                <i class="fa-solid fa-circle-exclamation me-2"></i><?= $errors['system'] ?>
            </div>
        <?php endif; ?>

        <form action="index.php?controller=cskh&action=luuTaoKM_CSKH" method="POST" id="formTaoKM">

            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-tag me-2"></i>Tên chương trình <span class="text-danger">*</span></label>
                <input type="text" name="TenCTKM" 
                       class="form-control text-uppercase-input <?= isset($err['TenCTKM']) ? 'is-invalid' : '' ?>" required
                       value="<?= htmlspecialchars($d['TenCTKM'] ?? '') ?>" 
                       placeholder="VÍ DỤ: KHUYẾN MÃI HÈ 2025">
                <div class="invalid-feedback-custom" style="<?= isset($err['TenCTKM']) ? 'display:block;' : 'display:none;' ?>">
                    <?= $err['TenCTKM'] ?? 'Vui lòng nhập tên chương trình' ?>
                </div>
                <?php if(!isset($err['TenCTKM'])): ?>
                    <small class="text-white-50 fst-italic">Tên sẽ tự động chuẩn hóa (ghi hoa và xóa khoảng trắng thừa). Tên không được trùng với chương trình khác.</small>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="fa-solid fa-calendar-check me-2"></i>Ngày bắt đầu <span class="text-danger">*</span></label>
                    <input type="date" name="NgayBatDau" 
                           class="form-control <?= isset($err['NgayBatDau']) ? 'is-invalid' : '' ?>" required
                           value="<?= $d['NgayBatDau'] ?? '' ?>">
                    <div class="invalid-feedback-custom" style="<?= isset($err['NgayBatDau']) ? 'display:block;' : 'display:none;' ?>">
                        <?= $err['NgayBatDau'] ?? 'Vui lòng nhập ngày bắt đầu' ?>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="fa-solid fa-calendar-xmark me-2"></i>Ngày kết thúc <span class="text-danger">*</span></label>
                    <input type="date" name="NgayKetThuc" 
                           class="form-control <?= isset($err['NgayKetThuc']) ? 'is-invalid' : '' ?>" required
                           value="<?= $d['NgayKetThuc'] ?? '' ?>">
                    <div class="invalid-feedback-custom" style="<?= isset($err['NgayKetThuc']) ? 'display:block;' : 'display:none;' ?>">
                        <?= $err['NgayKetThuc'] ?? 'Vui lòng nhập ngày kết thúc' ?>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-money-bill-wave me-2"></i>Mức ưu đãi <span class="text-danger">*</span></label>
                <input type="number" name="MucUuDai" 
                       class="form-control <?= isset($err['MucUuDai']) ? 'is-invalid' : '' ?>" required
                       value="<?= $d['MucUuDai'] ?? '' ?>" 
                       placeholder="Nhập 1-100 cho giảm % hoặc >= 1000 cho giảm tiền" 
                       min="1">
                <div class="invalid-feedback-custom" style="<?= isset($err['MucUuDai']) ? 'display:block;' : 'display:none;' ?>">
                    <?= $err['MucUuDai'] ?? 'Vui lòng nhập mức ưu đãi hợp lệ' ?>
                </div>
                <small class="text-white-50 fst-italic helper-text" style="<?= isset($err['MucUuDai']) ? 'display:none;' : '' ?>">
                    <i class="fa-solid fa-info-circle me-1"></i>
                    Nhập <strong>1-100</strong> để giảm theo % (ví dụ: 20 = giảm 20%). 
                    Nhập <strong>≥ 1000</strong> để giảm tiền cố định (ví dụ: 50000 = giảm 50,000đ).
                </small>
            </div>

            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-users me-2"></i>Đối tượng áp dụng <span class="text-danger">*</span></label>
                <select name="DoiTuong" class="form-select" required>
                    <option value="Tất cả KH" <?= ($d['DoiTuong'] ?? '')=='Tất cả KH'?'selected':'' ?>>Tất cả khách hàng</option>
                    <option value="Khách lẻ" <?= ($d['DoiTuong'] ?? '')=='Khách lẻ'?'selected':'' ?>>Khách lẻ</option>
                    <option value="Đoàn khách" <?= ($d['DoiTuong'] ?? '')=='Đoàn khách'?'selected':'' ?>>Đoàn khách</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label"><i class="fa-solid fa-signal me-2"></i>Trạng thái <span class="text-danger">*</span></label>
                <select name="TrangThai" class="form-select" required>
                    <option value="DangApDung" <?= ($d['TrangThai'] ?? 'DangApDung')=='DangApDung'?'selected':'' ?>>Đang hoạt động</option>
                    <option value="TamNgung" <?= ($d['TrangThai'] ?? '')=='TamNgung'?'selected':'' ?>>Tạm ngưng</option>
                    
                </select>
            </div>

            <div class="d-flex justify-content-center gap-3 pt-3 border-top" style="border-color: rgba(148,163,184,0.2) !important;">
                <button type="submit" class="btn-save">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Lưu Chương Trình
                </button>
                <button type="button" class="btn-reset" onclick="resetForm()">
                    <i class="fa-solid fa-redo me-2"></i>Làm mới
                </button>
                <button type="button" class="btn-cancel" onclick="window.location.href='index.php?controller=cskh&action=action_xemDS_CSKH'">
                    <i class="fa-solid fa-xmark me-2"></i>Hủy bỏ
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    // Reset form function
    function resetForm() {
        // Clear form fields explicitly (do not use form.reset() because server-rendered values would be restored)
        var form = document.getElementById('formTaoKM');

        // Clear common input types (skip hidden inputs)
        form.querySelectorAll('input').forEach(function(input) {
            var t = (input.type || '').toLowerCase();
            if (t === 'text' || t === 'date' || t === 'number' || t === 'email' || t === 'tel' || t === 'search' || t === 'password') {
                input.value = '';
            } else if (t === 'checkbox' || t === 'radio') {
                input.checked = false;
            }
        });

        // Clear textareas
        form.querySelectorAll('textarea').forEach(function(t){ t.value = ''; });

        // Reset selects to first option
        form.querySelectorAll('select').forEach(function(sel){ sel.selectedIndex = 0; });

        // Remove validation classes
        form.querySelectorAll('.is-invalid, .is-valid').forEach(function(el) {
            el.classList.remove('is-invalid', 'is-valid');
        });

        // Hide validation messages
        form.querySelectorAll('.invalid-feedback-custom').forEach(function(el) {
            el.style.display = 'none';
        });

        // Show helper texts again
        form.querySelectorAll('.helper-text').forEach(function(el) {
            el.style.display = '';
        });

        // Scroll to top
        window.scrollTo({top: 0, behavior: 'smooth'});
    }
    
    // Uppercase input
    document.querySelectorAll('.text-uppercase-input').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    });
    
    // Real-time validation
    const form = document.getElementById('formTaoKM');
    
    // Tên chương trình
    const tenInput = document.querySelector('input[name="TenCTKM"]');
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
            // Gọi AJAX check trùng tên
            fetch('index.php?controller=cskh&action=checkTenKhuyenMai&ten=' + encodeURIComponent(value))
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
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
            if (feedback && feedback.classList.contains('invalid-feedback-custom')) {
                feedback.style.display = 'none';
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
                    Nếu bạn tiếp tục, chương trình vẫn được lưu và có thể áp dụng đồng thời.
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
        var form = document.getElementById('formTaoKM');
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'force_save';
        input.value = '1';
        form.appendChild(input);
        form.submit();
    }
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>