<?php
$user    = Auth::user();
$rooms   = $rooms ?? [];
$errors  = $errors ?? [];
$success = $success ?? null;
$old     = $old ?? [];

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
    <title>Thêm phòng - Admin</title>
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
        input[readonly]{
            background:rgba(30,41,59,0.5) !important;
            cursor:not-allowed;
            opacity:0.8;
            border-color:#4b5563;
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

        .rooms-table{
            width:100%;
            border-collapse:collapse;
            margin-top:12px;
        }
        .rooms-table th,
        .rooms-table td{
            padding:8px 10px;
            text-align:left;
            border-bottom:1px solid rgba(148,163,184,0.2);
            font-size:12px;
        }
        .rooms-table th{
            color:#cbd5f5;
            font-weight:600;
            white-space: nowrap;
        }
        .rooms-table td{
            color:#e5e7eb;
        }
        .rooms-table tr:hover{
            background:rgba(148,163,184,0.05);
        }
        #roomsTableContainer {
            scrollbar-width: thin;
            scrollbar-color: #4b5563 #1f2937;
        }
        #roomsTableContainer::-webkit-scrollbar {
            width: 8px;
        }
        #roomsTableContainer::-webkit-scrollbar-track {
            background: #1f2937;
            border-radius: 4px;
        }
        #roomsTableContainer::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 4px;
        }
        #roomsTableContainer::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
    </style>
</head>
<body>
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · ADMIN</div>
        <div class="role">Thêm phòng</div>
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
            <h1>Thêm phòng mới</h1>
            <span>Quản lý phòng → Thêm phòng</span>
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

    <form method="post" action="index.php?controller=admin&action=phongThem" enctype="multipart/form-data" novalidate>
        <div class="card-shell">
            <h2><i class="fa-solid fa-plus text-emerald-400 me-2"></i> Thông tin phòng mới</h2>
            <p class="caption">
                Điền đầy đủ thông tin phòng. Các trường có dấu <span style="color:#dc2626">*</span> là bắt buộc.
            </p>

            <div class="row-flex">
                <div class="field">
                    <label>Số phòng <span style="color:#dc2626">*</span></label>
                    <input type="text" name="SoPhong" placeholder="VD: Pa101"
                           value="<?= e($old['SoPhong'] ?? '') ?>" required>
                    <div class="invalid-feedback d-none" data-error-for="SoPhong"></div>
                </div>
                <div class="field">
                    <label>Diện tích (m²) <span style="color:#dc2626">*</span></label>
                    <input type="number" step="0.1" name="DienTich" min="15"
                           value="<?= e($old['DienTich'] ?? '') ?>" required>
                    <div class="invalid-feedback d-none" data-error-for="DienTich"></div>
                </div>
                <div class="field">
                    <label>Loại giường <span style="color:#dc2626">*</span></label>
                    <input type="text" name="LoaiGiuong" placeholder="VD: 1 giường đôi"
                           value="<?= e($old['LoaiGiuong'] ?? '') ?>" 
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
                        <option value="Standard" <?= ($old['LoaiPhong'] ?? 'Standard') === 'Standard' ? 'selected' : '' ?>>Standard</option>
                        <option value="Deluxe" <?= ($old['LoaiPhong'] ?? '') === 'Deluxe' ? 'selected' : '' ?>>Deluxe</option>
                        <option value="Superior" <?= ($old['LoaiPhong'] ?? '') === 'Superior' ? 'selected' : '' ?>>Superior</option>
                        <option value="Suite" <?= ($old['LoaiPhong'] ?? '') === 'Suite' ? 'selected' : '' ?>>Suite</option>
                        <option value="VIP" <?= ($old['LoaiPhong'] ?? '') === 'VIP' ? 'selected' : '' ?>>VIP</option>
                    </select>
                    <div class="invalid-feedback d-none" data-error-for="LoaiPhong"></div>
                </div>
                <div class="field">
                    <label>View phòng <span style="color:#dc2626">*</span></label>
                    <select name="ViewPhong" required>
                        <option value="" disabled>-- Chọn view phòng --</option>
                        <option value="Biển" <?= ($old['ViewPhong'] ?? '') === 'Biển' ? 'selected' : '' ?>>Biển</option>
                        <option value="Thành phố" <?= ($old['ViewPhong'] ?? '') === 'Thành phố' ? 'selected' : '' ?>>Thành phố</option>
                        <option value="Vườn" <?= ($old['ViewPhong'] ?? '') === 'Vườn' ? 'selected' : '' ?>>Vườn</option>
                    </select>
                    <div class="invalid-feedback d-none" data-error-for="ViewPhong"></div>
                </div>
                <div class="field">
                    <label>Tình trạng</label>
                    <input type="text" value="Tốt" readonly>
                    <input type="hidden" name="TinhTrangPhong" value="Tot">
                    <small class="text-secondary">Phòng mới mặc định là Tốt</small>
                    <div class="invalid-feedback d-none" data-error-for="TinhTrangPhong"></div>
                </div>
            </div>

            <div class="row-flex">
                <div class="field">
                    <label>Giá (VND) <span style="color:#dc2626">*</span></label>
                    <input type="number" name="Gia" min="0" placeholder="VD: 1000000 (1,000,000 VND)"
                           value="<?= e($old['Gia'] ?? '') ?>" 
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
                           value="<?= e($old['SoKhachToiDa'] ?? '') ?>" required>
                    <div class="invalid-feedback d-none" data-error-for="SoKhachToiDa"></div>
                </div>
                <div class="field">
                    <label>Hình ảnh</label>
                    <input type="file" name="HinhAnh" id="hinhAnhInput" accept="image/*">
                    <div id="imagePreview" style="margin-top: 12px; display: none;">
                        <img id="previewImg" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 12px; border: 1px solid #4b5563; object-fit: contain;">
                        <button type="button" id="removeImageBtn" style="margin-top: 8px; padding: 6px 12px; background: #dc2626; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 12px;">
                            <i class="fa-solid fa-trash"></i> Xóa ảnh
                        </button>
                    </div>
                    <small class="text-secondary">Chỉ hỗ trợ JPG, PNG, GIF, WebP (tối đa 5MB)</small>
                </div>
            </div>

            <div class="field" style="margin-top:12px;">
                <label>Ghi chú</label>
                <textarea name="GhiChu" rows="3" placeholder="Nhập ghi chú về phòng (nếu có)..."><?= e($old['GhiChu'] ?? '') ?></textarea>
            </div>

            <div class="btn-row">
                <button type="reset" class="btn-outline">
                    Nhập lại
                </button>
                <button type="submit" class="btn-primary-modern">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Lưu phòng mới
                </button>
            </div>
        </div>
    </form>

    <?php if (!empty($rooms)): 
        $displayCount = 6;
        $totalRooms = count($rooms);
        $roomsToShow = array_slice($rooms, 0, $displayCount);
    ?>
        <div class="card-shell">
            <h2><i class="fa-solid fa-list text-warning me-2"></i> Danh sách phòng gần đây</h2>
            <p class="caption">
                Hiển thị <span id="displayedCount"><?= count($roomsToShow) ?></span> phòng được thêm gần đây nhất.
            </p>
            <div style="max-height: 400px; overflow-y: auto;" id="roomsTableContainer">
                <table class="rooms-table">
                    <thead>
                        <tr>
                            <th>Số phòng</th>
                            <th>Loại</th>
                            <th>Diện tích</th>
                            <th>Loại giường</th>
                            <th>View</th>
                            <th>Số khách</th>
                            <th>Giá</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody id="roomsTableBody">
                        <?php foreach ($roomsToShow as $room): ?>
                            <tr>
                                <td><?= e($room['SoPhong']) ?></td>
                                <td><?= e($room['LoaiPhong']) ?></td>
                                <td><?= number_format((float)($room['DienTich'] ?? 0), 1, ',', '.') ?> m²</td>
                                <td><?= e($room['LoaiGiuong'] ?? '-') ?></td>
                                <td><?= e($room['ViewPhong'] ?? '-') ?></td>
                                <td><?= (int)($room['SoKhachToiDa'] ?? 0) ?> người</td>
                                <td><?= number_format($room['Gia'], 0, ',', '.') ?> đ</td>
                                <td><?= e($room['TrangThai']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($totalRooms > $displayCount): ?>
                <div style="text-align: center; margin-top: 16px;">
                    <button type="button" id="loadMoreRooms" class="btn-outline" style="cursor: pointer;">
                        <i class="fa-solid fa-chevron-down"></i>
                        Xem thêm phòng (<?= $totalRooms - $displayCount ?> phòng còn lại)
                    </button>
                </div>
            <?php endif; ?>
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
                };
                reader.readAsDataURL(file);
            }
        });

        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', function() {
                hinhAnhInput.value = '';
                previewImg.src = '';
                imagePreview.style.display = 'none';
            });
        }
    }

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

    // Xử lý nút "Xem thêm phòng"
    const loadMoreBtn = document.getElementById('loadMoreRooms');
    const roomsTableBody = document.getElementById('roomsTableBody');
    const displayedCountEl = document.getElementById('displayedCount');
    
    if (loadMoreBtn && roomsTableBody) {
        const allRooms = <?= json_encode($rooms) ?>;
        let currentDisplayCount = <?= $displayCount ?? 6 ?>;
        const totalRooms = allRooms.length;
        
        loadMoreBtn.addEventListener('click', function() {
            // Hiển thị thêm 6 phòng
            const nextBatch = allRooms.slice(currentDisplayCount, currentDisplayCount + 6);
            
            nextBatch.forEach(room => {
                const row = document.createElement('tr');
                const dienTich = parseFloat(room.DienTich || 0).toFixed(1).replace('.', ',');
                const soKhach = parseInt(room.SoKhachToiDa || 0);
                row.innerHTML = `
                    <td>${escapeHtml(room.SoPhong)}</td>
                    <td>${escapeHtml(room.LoaiPhong)}</td>
                    <td>${dienTich} m²</td>
                    <td>${escapeHtml(room.LoaiGiuong || '-')}</td>
                    <td>${escapeHtml(room.ViewPhong || '-')}</td>
                    <td>${soKhach} người</td>
                    <td>${formatNumber(room.Gia)} đ</td>
                    <td>${escapeHtml(room.TrangThai)}</td>
                `;
                roomsTableBody.appendChild(row);
            });
            
            currentDisplayCount += nextBatch.length;
            if (displayedCountEl) {
                displayedCountEl.textContent = currentDisplayCount;
            }
            
            // Ẩn nút nếu đã hiển thị hết
            if (currentDisplayCount >= totalRooms) {
                loadMoreBtn.style.display = 'none';
            } else {
                loadMoreBtn.innerHTML = `
                    <i class="fa-solid fa-chevron-down"></i>
                    Xem thêm phòng (${totalRooms - currentDisplayCount} phòng còn lại)
                `;
            }
            
            // Cuộn xuống phòng mới được thêm
            const newRows = roomsTableBody.querySelectorAll('tr');
            if (newRows.length > 0) {
                newRows[newRows.length - 1].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
});
</script>
</body>
</html>
