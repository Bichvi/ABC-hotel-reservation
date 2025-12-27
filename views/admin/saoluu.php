<?php
// Helper function
if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$user    = $user ?? null;
$backups = $backups ?? [];
$errors  = $errors ?? [];
$success = $success ?? null;
$autoBackupConfig = $autoBackupConfig ?? ['enabled' => false, 'mode' => 'manual', 'last_backup' => null];

// Hàm định dạng kích thước file (KB, MB, GB...)
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sao lưu dữ liệu - Admin</title>
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
        input[type="text"],
        input[type="radio"],
        select{
            font-size:14px;
        }
        input[type="text"],
        select{
            width:100%;
            padding:7px 10px;
            border-radius:999px;
            border:1px solid #4b5563;
            box-sizing:border-box;
            background:#020617;
            color:#e5e7eb;
            outline:none;
            transition:all .15s ease;
        }
        input:focus,select:focus{
            border-color:#3b82f6;
            box-shadow:0 0 0 1px rgba(59,130,246,.4);
            background:#020617;
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
        .backup-table{
            width:100%;
            border-collapse:collapse;
            font-size:13px;
        }
        .backup-table thead{
            background:rgba(30,41,59,0.5);
            border-bottom:2px solid rgba(148,163,184,0.45);
        }
        .backup-table th{
            padding:10px 12px;
            text-align:left;
            color:#cbd5f5;
            font-weight:600;
        }
        .backup-table td{
            padding:10px 12px;
            border-bottom:1px solid rgba(148,163,184,0.2);
            color:#e5e7eb;
        }
        .backup-table tbody tr:hover{
            background:rgba(30,64,175,0.15);
        }
        .backup-table-container{
            border-radius:12px;
            border:1px solid rgba(148,163,184,0.3);
            overflow:hidden;
            background:rgba(15,23,42,0.5);
            margin-top:12px;
        }
        .backup-table-scroll{
            max-height:400px;
            overflow-y:auto;
        }
        .badge{
            padding:4px 12px;
            border-radius:20px;
            font-size:.8rem;
            font-weight:600;
        }
        .badge-bak{
            background:rgba(249,115,22,0.2);
            color:#fdba74;
            border:1px solid rgba(249,115,22,0.5);
        }
        .btn-restore{
            border-radius:999px;
            padding:6px 14px;
            font-size:12px;
            background:linear-gradient(135deg,#3b82f6,#2563eb);
            color:#fff;
            border:none;
            cursor:pointer;
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            gap:4px;
        }
        .btn-restore:hover{
            filter:brightness(1.1);
        }
    </style>
</head>
<body>
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · ADMIN</div>
        <div class="role">Sao lưu dữ liệu</div>
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
            <a href="index.php?controller=admin&action=saoLuuDuLieu">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span>Sao lưu dữ liệu</span>
            </a>
            <span class="nav-separator">|</span>
            <a href="index.php?controller=admin&action=phucHoiDuLieu">
                <i class="fa-solid fa-rotate-left"></i>
                <span>Phục hồi dữ liệu</span>
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
            <h1>Sao lưu dữ liệu hệ thống</h1>
            <span>Quản lý hệ thống → Sao lưu dữ liệu</span>
        </div>
        <a href="index.php?controller=dashboard&action=admin" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Trở về
        </a>
    </div>

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

    <form method="post" action="index.php?controller=admin&action=saoLuuDuLieu">
        <input type="hidden" name="save_auto_backup" value="1">
        <div class="card-shell">
            <h2><i class="fa-solid fa-robot text-blue-400 me-2"></i> Thiết lập sao lưu tự động</h2>
            <p class="caption">
                Cấu hình sao lưu tự động cho hệ thống. Khi bật, hệ thống sẽ tự động sao lưu khi Admin đăng xuất.
            </p>

            <div class="field" style="margin-top:12px;">
                <label>Thư mục sao lưu</label>
                <input type="text" value="storage/backups" readonly style="background:rgba(30,41,59,0.5);">
                <small style="color:#9ca3af;font-size:12px;">Thư mục mặc định chứa các file backup</small>
            </div>

            <div class="field" style="margin-top:12px;">
                <label>Chế độ sao lưu tự động</label>
                <div class="radio-group-flex">
                    <label for="ab1">
                        <input type="radio" name="AutoBackup" id="ab1" value="auto" 
                               <?= ($autoBackupConfig['enabled'] ?? false) ? 'checked' : '' ?>>
                        Tự động sao lưu khi Admin đăng xuất
                    </label>
                    <label for="ab2">
                        <input type="radio" name="AutoBackup" id="ab2" value="manual"
                               <?= !($autoBackupConfig['enabled'] ?? false) ? 'checked' : '' ?>>
                        Không sao lưu tự động
                    </label>
                </div>
                <?php if (!empty($autoBackupConfig['last_backup'])): ?>
                    <small style="color:#9ca3af;font-size:12px;display:block;margin-top:8px;">
                        <i class="fa-solid fa-clock me-1"></i>
                        Lần sao lưu tự động cuối: <?= e($autoBackupConfig['last_backup']) ?>
                    </small>
                <?php endif; ?>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn-primary-modern">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Lưu cấu hình
                </button>
            </div>
        </div>
    </form>

    <form method="post" action="index.php?controller=admin&action=saoLuuDuLieu">
        <div class="card-shell">
            <h2><i class="fa-solid fa-database text-emerald-400 me-2"></i> Sao lưu thủ công</h2>
            <p class="caption">
                Tạo bản sao lưu dữ liệu ngay bây giờ. Các trường có dấu <span style="color:#dc2626">*</span> là bắt buộc.
            </p>

            <div class="row-flex">
                <div class="field">
                    <label>Tên tệp sao lưu <span style="color:#dc2626">*</span></label>
                    <input type="text" name="ten_file" placeholder="abc_resort_backup_yyyyMMdd" required>
                </div>
                <div class="field">
                    <label>Hình thức sao lưu <span style="color:#dc2626">*</span></label>
                    <select name="hinh_thuc" required id="hinh_thuc_select">
                        <option value="toan_bo" selected>Toàn bộ</option>
                        <option value="hom_nay">Ngày hôm nay</option>
                        <option value="chon_ngay">Chọn ngày</option>
                    </select>
                    <small id="backup_type_hint" style="color:#6b7280;font-size:12px;display:block;margin-top:4px;">
                        <strong>Toàn bộ:</strong> Sao lưu tất cả dữ liệu từ mọi bảng. Phù hợp cho backup định kỳ hoặc trước khi thay đổi lớn.
                    </small>
                </div>
            </div>

            <div class="field" id="ngay_chon_field" style="display:none;margin-top:12px;">
                <label>Chọn ngày cần sao lưu <span style="color:#dc2626">*</span></label>
                <input type="date" name="ngay_chon" id="ngay_chon_input" max="<?= date('Y-m-d') ?>">
                <small style="color:#9ca3af;font-size:12px;">Chọn ngày trong quá khứ hoặc hôm nay</small>
            </div>

            <div class="field" style="margin-top:12px;">
                <label>Thư mục lưu (tùy chọn)</label>
                <input type="text" name="thu_muc" placeholder="sub-folder">
                <small style="color:#9ca3af;font-size:12px;">Để trống để lưu vào thư mục mặc định</small>
            </div>

            <div class="btn-row">
                <button type="reset" class="btn-outline">
                    Nhập lại
                </button>
                <button type="submit" class="btn-primary-modern">
                    <i class="fa-solid fa-database"></i>
                    Thực hiện sao lưu ngay
                </button>
            </div>
        </div>
    </form>

    <div class="card-shell">
        <h2><i class="fa-solid fa-list text-amber-400 me-2"></i> Danh sách tệp đã sao lưu</h2>
        <p class="caption">
            Các bản sao lưu đã được tạo. Bạn có thể phục hồi từ bất kỳ bản sao lưu nào.
        </p>

        <div class="backup-table-container">
            <div class="backup-table-scroll">
                <table class="backup-table">
                    <thead>
                        <tr>
                            <th>Tên tệp</th>
                            <th>Ngày tạo</th>
                            <th>Loại file</th>
                            <th>Kích thước</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($backups)): ?>
                            <tr>
                                <td colspan="5" class="text-center" style="color: #9ca3af; padding: 20px;">
                                    <i class="fa-solid fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                    Chưa có bản sao lưu nào.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($backups as $bk): ?>
                                <tr>
                                    <td><strong><?= e($bk['name'] ?? 'N/A') ?></strong></td>
                                    <td>
                                        <?php 
                                        // Đảm bảo timezone đúng
                                        $timezone = new DateTimeZone('Asia/Ho_Chi_Minh');
                                        
                                        if (!empty($bk['created_at'])) {
                                            // Sử dụng thời gian từ metadata
                                            $dt = DateTime::createFromFormat('Y-m-d H:i:s', $bk['created_at'], $timezone);
                                            if (!$dt) {
                                                // Fallback về filemtime với timezone
                                                $dt = new DateTime('@' . ($bk['modified'] ?? time()));
                                                $dt->setTimezone($timezone);
                                            }
                                        } else {
                                            // Fallback về filemtime với timezone
                                            $dt = new DateTime('@' . ($bk['modified'] ?? time()));
                                            $dt->setTimezone($timezone);
                                        }
                                        
                                        // Định dạng: "11 tháng 12 năm 2025, 01:56:45"
                                        $ngay = (int)$dt->format('d');
                                        $thang = (int)$dt->format('m');
                                        $nam = $dt->format('Y');
                                        $gio = $dt->format('H:i:s');
                                        echo $ngay . ' tháng ' . $thang . ' năm ' . $nam . ', ' . $gio;
                                        ?>
                                    </td>
                                    <td><span class="badge badge-bak">BAK</span></td>
                                    <td><?= formatFileSize($bk['size'] ?? 0) ?></td>
                                    <td>
                                        <a href="index.php?controller=admin&action=phucHoiDuLieu&file=<?= e($bk['name']) ?>" class="btn-restore">
                                            <i class="fa-solid fa-rotate-left"></i> Phục hồi
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('hinh_thuc_select');
    const hint = document.getElementById('backup_type_hint');
    const ngayChonField = document.getElementById('ngay_chon_field');
    const ngayChonInput = document.getElementById('ngay_chon_input');
    
    if (select && hint) {
        function updateHint() {
            const value = select.value;
            if (value === 'toan_bo') {
                hint.innerHTML = '<strong>Toàn bộ:</strong> Sao lưu tất cả dữ liệu từ mọi bảng. Phù hợp cho backup định kỳ hoặc trước khi thay đổi lớn.';
                if (ngayChonField) ngayChonField.style.display = 'none';
                if (ngayChonInput) ngayChonInput.removeAttribute('required');
            } else if (value === 'hom_nay') {
                hint.innerHTML = '<strong>Ngày hôm nay:</strong> Chỉ sao lưu dữ liệu được tạo/cập nhật hôm nay. Các bảng cấu hình (phòng, dịch vụ, vai trò...) vẫn được sao lưu toàn bộ. Phù hợp cho backup nhanh dữ liệu mới.';
                if (ngayChonField) ngayChonField.style.display = 'none';
                if (ngayChonInput) ngayChonInput.removeAttribute('required');
            } else if (value === 'chon_ngay') {
                hint.innerHTML = '<strong>Chọn ngày:</strong> Sao lưu dữ liệu của một ngày cụ thể. Các bảng cấu hình vẫn được sao lưu toàn bộ. Phù hợp khi cần backup dữ liệu của một ngày trong quá khứ.';
                if (ngayChonField) ngayChonField.style.display = 'block';
                if (ngayChonInput) {
                    ngayChonInput.setAttribute('required', 'required');
                    // Mặc định là hôm nay nếu chưa có giá trị
                    if (!ngayChonInput.value) {
                        ngayChonInput.value = '<?= date('Y-m-d') ?>';
                    }
                }
            }
        }
        
        select.addEventListener('change', updateHint);
        updateHint(); // Cập nhật lần đầu
    }
});
</script>
</body>
</html>
