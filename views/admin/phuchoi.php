<?php
// Helper function
if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$user    = Auth::user();
$backups = $backups ?? [];
$errors  = $errors ?? [];
$success = $success ?? null;

// Hàm định dạng kích thước file
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        return $bytes . ' bytes';
    } elseif ($bytes == 1) {
        return $bytes . ' byte';
    }
    return '0 bytes';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phục hồi dữ liệu - Admin</title>
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
        input[type="text"]{
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
        input:focus{
            border-color:#3b82f6;
            box-shadow:0 0 0 1px rgba(59,130,246,.4);
            background:#020617;
        }
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
        .alert-warning{
            background:rgba(251,191,36,0.15);
            border:1px solid rgba(253,224,71,0.7);
            color:#fcd34d;
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
        .restore-table{
            width:100%;
            border-collapse:collapse;
            font-size:13px;
        }
        .restore-table thead{
            background:rgba(30,41,59,0.5);
            border-bottom:2px solid rgba(148,163,184,0.45);
        }
        .restore-table th{
            padding:10px 12px;
            text-align:left;
            color:#cbd5f5;
            font-weight:600;
        }
        .restore-table td{
            padding:10px 12px;
            border-bottom:1px solid rgba(148,163,184,0.2);
            color:#e5e7eb;
        }
        .restore-table tbody tr:hover{
            background:rgba(30,64,175,0.15);
        }
        .restore-table-container{
            border-radius:12px;
            border:1px solid rgba(148,163,184,0.3);
            overflow:hidden;
            background:rgba(15,23,42,0.5);
            margin-top:12px;
        }
        .restore-table-scroll{
            max-height:400px;
            overflow-y:auto;
        }
        .form-check-input{
            width:1em;
            height:1em;
            margin-right:8px;
            background-color:#020617;
            border:1px solid #4b5563;
            border-radius:50%;
            cursor:pointer;
        }
        .form-check-input:checked{
            background-color:#22c55e;
            border-color:#16a34a;
        }
        .form-check-label{
            cursor:pointer;
            color:#e5e7eb;
            font-size:14px;
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
    </style>
</head>
<body>
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · ADMIN</div>
        <div class="role">Phục hồi dữ liệu</div>
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
            <h1>Phục hồi dữ liệu từ file sao lưu</h1>
            <span>Quản lý hệ thống → Phục hồi dữ liệu</span>
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

    <form method="post" action="index.php?controller=admin&action=phucHoiDuLieu">
        <div class="card-shell">
            <h2><i class="fa-solid fa-rotate-left text-blue-400 me-2"></i> Chọn file sao lưu</h2>
            <p class="caption">
                Chọn file sao lưu để phục hồi dữ liệu. <strong style="color:#f59e0b;">Lưu ý:</strong> Quá trình phục hồi sẽ ghi đè dữ liệu hiện tại.
            </p>

            <div class="field" style="margin-top:12px;">
                <label>Thư mục chứa file sao lưu</label>
                <input type="text" value="storage/backups" readonly style="background:rgba(30,41,59,0.5);">
                <small style="color:#9ca3af;font-size:12px;">Thư mục mặc định chứa các file sao lưu</small>
            </div>

            <div class="alert alert-warning" style="margin-top:12px;">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <strong>Cảnh báo:</strong> Phục hồi dữ liệu sẽ thay thế toàn bộ dữ liệu hiện tại. Vui lòng đảm bảo đã sao lưu dữ liệu hiện tại trước khi thực hiện.
            </div>

            <div class="restore-table-container" style="margin-top:16px;">
                <div class="restore-table-scroll">
                    <table class="restore-table">
                        <thead>
                            <tr>
                                <th style="width:50px;"></th>
                                <th>Tên tệp</th>
                                <th>Ngày tạo</th>
                                <th>Loại file</th>
                                <th>Kích thước</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($backups)): ?>
                                <tr>
                                    <td colspan="5" class="text-center" style="color: #9ca3af; padding: 20px;">
                                        <i class="fa-solid fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                        Không có file sao lưu khả dụng.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($backups as $index => $bk): ?>
                                    <tr>
                                        <td>
                                            <input type="radio" name="backup_file" value="<?= e($bk['name']) ?>" 
                                                   id="file<?= $index ?>" class="form-check-input"
                                                   <?= $index === 0 ? 'checked' : '' ?> required>
                                        </td>
                                        <td>
                                            <label for="file<?= $index ?>" class="form-check-label" style="cursor:pointer;">
                                                <strong><?= e($bk['name']) ?></strong>
                                            </label>
                                        </td>
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
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="btn-row">
                <button type="button" onclick="window.location.href='index.php?controller=dashboard&action=admin'" class="btn-outline">
                    <i class="fa-solid fa-times"></i> Hủy
                </button>
                <button type="submit" class="btn-primary-modern" onclick="return confirm('Bạn có chắc chắn muốn phục hồi dữ liệu? Quá trình này sẽ ghi đè dữ liệu hiện tại và không thể hoàn tác!');">
                    <i class="fa-solid fa-play"></i>
                    Phục hồi dữ liệu
                </button>
            </div>
        </div>
    </form>
</div>
</body>
</html>
