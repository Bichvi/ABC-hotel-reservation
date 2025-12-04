<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$user    = $user    ?? ($_SESSION['user'] ?? null);
$errors  = $errors  ?? [];
$success = $success ?? null;
$services = $services ?? [];
$selectedService = $selectedService ?? null;
$bookings = $bookings ?? [];   // danh sách giao dịch đang Booked

function e($v){ return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đặt dịch vụ bổ sung - ABC Resort</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{ margin:0;background:radial-gradient(circle at top,#020617,#0f172a 80%);
      font-family:system-ui,-apple-system,"Segoe UI";color:#e5e7eb; }
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
    color:#93c5fd ;
    text-decoration:none;
    margin-left:12px;
}
/* Tăng size cho khu vực Xin chào, khach1 */
.topbar span {
    font-size:16px !important;      /* tăng chữ Xin chào */
    font-weight:500;
}

.topbar span strong {
    font-size:16px !important;      /* tên khách lớn hơn */
    font-weight:700;
    color:#e5e7eb !important;       /* màu xanh giống menu */
}
.topbar a:hover{text-decoration:underline;}
.brand{font-size:14px;font-weight:700;text-transform:uppercase;}
.container-page{max-width:1200px;margin:28px auto;padding:0 16px;}
.page-title{font-size:24px;font-weight:700;margin-bottom:12px;}
.card-shell{
    background:linear-gradient(135deg,rgba(15,23,42,.97),rgba(15,23,42,.94));
    padding:18px;border-radius:18px;border:1px solid rgba(148,163,184,.35);
    margin-bottom:18px;
}
.service-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;}
.service-card{
    border-radius:16px;overflow:hidden;border:1px solid rgba(148,163,184,.35);
    background:radial-gradient(circle at top left,rgba(56,189,248,.15),rgba(15,23,42,.95));
    box-shadow:0 16px 34px rgba(0,0,0,.6);
    display:flex;flex-direction:column;min-height:260px;
}
.service-img-wrap{height:150px;overflow:hidden;}
.service-img-wrap img{width:100%;height:100%;object-fit:cover;}
.service-body{padding:12px;display:flex;flex-direction:column;gap:4px;}
.service-title{font-size:16px;font-weight:700;color:#f9fafb;}
.service-price{font-size:17px;font-weight:700;color:#4ade80;}
.btn-primary-modern{
    border:none;border-radius:999px;background:linear-gradient(135deg,#06b6d4,#22c55e);
    padding:9px 18px;font-size:14px;font-weight:600;color:#0b1120;
    cursor:pointer;display:inline-flex;align-items:center;gap:6px;
}
.alert-success{
    background:rgba(16,185,129,.15);border:1px solid rgba(45,212,191,.7);
    color:#bbf7d0;border-radius:12px;padding:12px 16px;
}
.alert-error{
    background:rgba(239,68,68,.12);border:1px solid rgba(248,113,113,.6);
    color:#fecaca;border-radius:12px;padding:12px 16px;
}
select, input{
    width:100%;background:#020617;color:#e5e7eb;border-radius:12px;
    border:1px solid #334155;padding:10px;font-size:14px;
}
</style>
</head>

<body>
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · KHÁCH HÀNG</div>
        <div class="role">Đặt phòng Online</div>
    </div>
    <div>
        <?php if ($user): ?>
            <span style="font-size:13px;margin-right:10px;">
                Xin chào, <strong><?= e($user['Username'] ?? 'Khách') ?></strong>
            </span>
        <?php endif; ?>
        <a href="index.php" style="color:#93c5fd;text-decoration:none;margin-left:12px;">
            Trang chủ
        </a>
        <a href="index.php?controller=khachhang&action=dashboard" style="color:#93c5fd;text-decoration:none;margin-left:12px;">
            Trang khách hàng
        </a>
        <a href="index.php?controller=auth&action=logout" style="color:#93c5fd;text-decoration:none;margin-left:12px;">
            Đăng xuất
        </a>
    </div>
</div>

<div class="container-page">
    <h1 class="page-title">Đặt dịch vụ bổ sung</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <?php foreach($errors as $e): ?> • <?= e($e) ?><br> <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert-success"><?= e($success) ?></div>
    <?php endif; ?>

    <!-- ===================== A. Danh sách dịch vụ ===================== -->
    <div class="card-shell">
        <h4>Danh sách dịch vụ hiện có</h4>
        <p style="font-size:13px;color:#9ca3af;margin-bottom:12px;">
            Nhấp “Chọn dịch vụ” để tiếp tục đặt
        </p>

        <div class="service-grid">
            <?php foreach($services as $s): ?>
                <?php
                $img = $s["HinhAnh"] ?? "";
                $imgSrc = $img !== "" ? "uploads/dichvu/" . e($img)
                    : "https://via.placeholder.com/400x250?text=Service";
                ?>
                <div class="service-card">
                    <div class="service-img-wrap">
                        <img src="<?= $imgSrc ?>" alt="<?= e($s['TenDichVu'] ?? '') ?>">
                    </div>
                    <div class="service-body">
                        <h3 class="service-title"><?= e($s["TenDichVu"]) ?></h3>
                        <div style="font-size:13px;color:#9ca3af;">
                            <?= e($s["MoTa"] ?? '') ?>
                        </div>
                        <div class="service-price">
                            <?= number_format($s["GiaDichVu"] ?? 0, 0, ',', '.') ?> đ
                        </div>

                        <form method="post" class="mt-auto">
                            <input type="hidden" name="ma_dv" value="<?= (int)$s["MaDichVu"] ?>">
                            <button type="submit" class="btn-primary-modern" name="btn_action" value="choose_service">
                                Chọn dịch vụ
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ====================== B. FORM xác nhận ======================== -->
    <?php if($selectedService): ?>
        <form method="post">
            <input type="hidden" name="ma_dv" value="<?= (int)$selectedService['MaDichVu'] ?>">

            <div class="card-shell">
                <h4>Xác nhận đặt dịch vụ: <?= e($selectedService['TenDichVu']) ?></h4>

                <label>Số lượng:</label>
                <input type="number" name="so_luong" value="1" min="1" required>

                <label style="margin-top:10px;">Chọn mã giao dịch:</label>
                <select name="ma_gd" required>
                    <option value="">-- Chọn giao dịch --</option>
                    <?php foreach($bookings as $b): ?>
                        <option value="<?= (int)$b['MaGiaoDich'] ?>">
                            #<?= (int)$b['MaGiaoDich'] ?> — Phòng <?= e($b['SoPhong']) ?> — <?= date("d/m/Y", strtotime($b['NgayNhanDuKien'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="text-end mt-3">
                    <button type="submit" class="btn-primary-modern"
                        name="btn_action" value="confirm_booking_service"
                        onclick="return confirm('Xác nhận đặt dịch vụ này?');">
                        Xác nhận đặt dịch vụ
                    </button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
