<?php
// View xác nhận xóa chi phí (2 bước)

$user = $user ?? Auth::user();
$chiPhi = $chiPhi ?? [];
$maCP = $maCP ?? 0;
$step = $step ?? 1;
$token = $token ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận xóa chi phí - Kế toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-main: #0f172a;
            --bg-card: rgba(15, 23, 42, 0.95);
            --border-soft: rgba(148, 163, 184, 0.2);
            --text-main: #e2e8f0;
            --text-muted: #94a3b8;
            --accent-red: #ef4444;
            --accent-blue: #0ea5e9;
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(90deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%) !important;
            border-bottom: 1px solid var(--border-soft);
            z-index: 1000;
        }

        .brand-logo {
            font-weight: 700;
            color: var(--accent-blue) !important;
        }

        .confirmation-card {
            border-radius: 12px;
            border: 2px solid rgba(239, 68, 68, 0.3);
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(15, 23, 42, 0.8) 100%);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            margin-top: 80px;
        }

        .confirmation-icon {
            font-size: 3.5rem;
            color: var(--accent-red);
            margin-bottom: 20px;
            animation: bounce 0.6s ease-in-out;
        }

        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .confirmation-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--text-main);
        }

        .confirmation-text {
            color: var(--text-muted);
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .info-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            border-left: 4px solid var(--accent-red);
        }

        .info-item {
            margin-bottom: 12px;
        }

        .info-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .btn-group-actions {
            display: flex;
            gap: 12px;
            margin-top: 28px;
        }

        .btn-danger-confirm {
            background: linear-gradient(135deg, var(--accent-red), #dc2626);
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            padding: 12px 28px;
            flex: 1;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-danger-confirm:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            color: #fff;
        }

        .btn-cancel {
            background: rgba(14, 165, 233, 0.15);
            border: 1px solid var(--accent-blue);
            color: var(--accent-blue);
            font-weight: 600;
            border-radius: 6px;
            padding: 12px 28px;
            flex: 1;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-cancel:hover {
            background: var(--accent-blue);
            color: #0f172a;
        }

        .warning-text {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.4);
            border-radius: 6px;
            padding: 12px 16px;
            margin-top: 20px;
            color: #fecaca;
            font-size: 0.9rem;
            line-height: 1.5;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark border-bottom border-slate-700 w-100">
    <div class="container">
        <a class="navbar-brand brand-logo" href="index.php?controller=ketoan&action=dashboard">
            <i class="fa-solid fa-calculator me-2 text-warning"></i>ABC Resort - Kế toán
        </a>
        <div class="d-flex align-items-center ms-auto">
            <span class="me-3 small text-slate-300">
                <i class="fa-regular fa-user me-1"></i><?= htmlspecialchars($user['Username'] ?? '') ?>
            </span>
            <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </a>
        </div>
    </div>
</nav>

<div class="confirmation-card">
    <div class="text-center">
        <i class="fa-solid fa-exclamation-triangle confirmation-icon"></i>
        
        <?php if ($step === 1): ?>
            <h2 class="confirmation-title">Xác nhận xóa (Bước 1 của 2)</h2>
            <p class="confirmation-text">
                Bạn chắc chắn muốn xóa chi phí này không?<br>
                <strong>Hành động này không thể hoàn tác!</strong>
            </p>
        <?php else: ?>
            <h2 class="confirmation-title">Xác nhận xóa (Bước 2 của 2)</h2>
            <p class="confirmation-text">
                Nhấn nút xóa lần nữa để hoàn tất xóa chi phí<br>
                <strong>Đây là lần xác nhận cuối cùng!</strong>
            </p>
        <?php endif; ?>
    </div>

    <div class="info-box">
        <div class="info-item">
            <div class="info-label">ID Chi Phí</div>
            <div class="info-value">#<?= htmlspecialchars($chiPhi['MaCP'] ?? $maCP) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Tên Chi Phí</div>
            <div class="info-value"><?= htmlspecialchars($chiPhi['TenChiPhi'] ?? '') ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Số Tiền</div>
            <div class="info-value"><?= number_format((float)($chiPhi['SoTien'] ?? 0), 0, ',', '.') ?> ₫</div>
        </div>
        <div class="info-item">
            <div class="info-label">Ngày Chi</div>
            <div class="info-value"><?= htmlspecialchars($chiPhi['NgayChi'] ?? '') ?></div>
        </div>
    </div>

    <div class="warning-text">
        <i class="fa-solid fa-exclamation-circle me-2"></i>
        Dữ liệu chi phí sẽ bị xóa vĩnh viễn khỏi hệ thống.
    </div>

    <div class="btn-group-actions">
        <?php if ($step === 1): ?>
            <a href="index.php?controller=ketoan&action=xoaChiPhi&id=<?= htmlspecialchars($maCP) ?>&step=2&token=<?= htmlspecialchars($token) ?>" class="btn-danger-confirm">
                <i class="fa-solid fa-trash me-2"></i>Tiếp tục xóa
            </a>
        <?php else: ?>
            <a href="index.php?controller=ketoan&action=xoaChiPhi&id=<?= htmlspecialchars($maCP) ?>&step=2&token=<?= htmlspecialchars($token) ?>&confirm=1" class="btn-danger-confirm">
                <i class="fa-solid fa-trash me-2"></i>Xóa chi phí
            </a>
        <?php endif; ?>
        <a href="index.php?controller=ketoan&action=quanLyChiPhi" class="btn-cancel">
            <i class="fa-solid fa-times me-2"></i>Hủy
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
