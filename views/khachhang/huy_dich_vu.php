<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$user    = $user    ?? ($_SESSION['user'] ?? null);
$errors  = $errors  ?? [];
$success = $success ?? null;

// Danh sách dịch vụ khách đã đặt (controller truyền)
$bookedServices = $list ?? [];

function e($v){ return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Hủy dịch vụ bổ sung - ABC Resort</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    margin:0;
    background: radial-gradient(circle at top, #020617, #0f172a 80%);
    font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
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
    background:linear-gradient(135deg, rgba(15,23,42,.97),rgba(15,23,42,.94));
    padding:18px;border-radius:18px;
    border:1px solid rgba(148,163,184,.35);
    margin-bottom:18px;
}
.service-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
    gap:16px;
}
.service-card{
    border-radius:16px;
    overflow:hidden;
    border:1px solid rgba(148,163,184,.35);
    background: radial-gradient(circle at top left, rgba(239,68,68,.15), rgba(15,23,42,.95));
    box-shadow:0 16px 34px rgba(0,0,0,.6);
    display:flex;flex-direction:column;min-height:260px;
}
.service-img-wrap{height:150px;overflow:hidden;}
.service-img-wrap img{width:100%;height:100%;object-fit:cover;}
.service-body{padding:12px;display:flex;flex-direction:column;gap:4px;}
.service-title{font-size:16px;font-weight:700;color:#f9fafb;}
.service-status{font-size:12px;color:#eab308;font-weight:600;}
.btn-danger-modern{
    border:none;border-radius:999px;
    background:linear-gradient(135deg,#ef4444,#dc2626);
    padding:9px 18px;font-size:14px;font-weight:600;
    color:#fef2f2;cursor:pointer;
    display:inline-flex;align-items:center;gap:6px;
}
.alert-success{background:rgba(16,185,129,.15);border:1px solid rgba(45,212,191,.7);color:#bbf7d0;border-radius:12px;padding:12px 16px;}
.alert-error{background:rgba(239,68,68,.12);border:1px solid rgba(248,113,113,.6);color:#fecaca;border-radius:12px;padding:12px 16px;}
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
    <h1 class="page-title">Hủy dịch vụ đã đặt</h1>

    <?php if(!empty($errors)): ?>
        <div class="alert-error">
            <?php foreach($errors as $e): ?>• <?= e($e) ?><br><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="alert-success"><?= e($success) ?></div>
    <?php endif; ?>

    <div class="card-shell">
        <h4>Danh sách dịch vụ của bạn</h4>
        <p style="font-size:13px;color:#9ca3af;margin-bottom:12px;">Chọn dịch vụ cần hủy nếu còn trong thời gian cho phép.</p>

        <div class="service-grid">
            <?php if(empty($bookedServices)): ?>
                <p style="padding:16px;font-size:14px;color:#9ca3af;">❗ Bạn chưa đặt dịch vụ nào.</p>
            <?php endif; ?>

            <?php foreach($bookedServices as $dv): ?>
                <?php
                $img = $dv["HinhAnh"] ?? "";
                $imgSrc = $img !== "" ? "public/uploads/dichvu/" . e($img)
                                      : "https://via.placeholder.com/400x250?text=Service";
                ?>
                <div class="service-card">
                    <div class="service-img-wrap">
                        <img src="<?= $imgSrc ?>" alt="">
                    </div>
                    <div class="service-body">
                        <h3 class="service-title"><?= e($dv["TenDichVu"]) ?></h3>
                        <div class="service-status" style="color:#38bdf8;font-weight:700;">
                            Phòng: <?= e($dv["SoPhong"] ?? "") ?>
                        </div>
                        <div class="service-status">
                            Đặt ngày: <?= date("d/m/Y", strtotime($dv["NgayDat"])) ?>
                        </div>
                        <div style="font-size:13px;color:#9ca3af;">
                            Giá: <?= number_format($dv["GiaBan"] ?? 0, 0, ',', '.') ?> đ
                        </div>

                        <form method="post" class="mt-auto">
                            <input type="hidden" name="ma_ctdv" value="<?= (int)$dv["MaCTDV"] ?>">
                            <button type="submit"
                                    class="btn-danger-modern"
                                    name="btn_action" value="cancel_service"
                                    onclick="return confirm('Bạn có chắc muốn hủy dịch vụ này?');">
                                Hủy dịch vụ
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>
</body>
</html>
