<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$user    = $user    ?? ($_SESSION['user'] ?? null);
$errors  = $errors  ?? [];
$success = $success ?? null;
$btn_action = $_POST['btn_action'] ?? null;

$feedback = $feedback ?? [
    'noi_dung' => '',
    'muc_do'   => '',
    'loai'     => '',
];

function e($v){ return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Gửi phản hồi - ABC Resort</title>
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
.container-page{max-width:900px;margin:28px auto;padding:0 16px;}
.page-title{font-size:24px;font-weight:700;margin-bottom:16px;}
.card-shell{
    background:linear-gradient(135deg, rgba(15,23,42,.97),rgba(15,23,42,.94));
    padding:18px;border-radius:18px;
    border:1px solid rgba(148,163,184,.35);
    margin-bottom:18px;
}
label{font-size:13px;margin-bottom:4px;color:#cbd5f5;}
textarea, select, input[type="file"], input[type="text"]{
    width:100%;padding:10px;border-radius:12px;border:1px solid #4b5563;
    background:#020617;color:#e5e7eb;resize:none;font-size:14px;
}
textarea:focus, select:focus, input:focus{border-color:#3b82f6;outline:none;}
.btn-primary-modern{
    border:none;border-radius:999px;padding:10px 18px;font-size:14px;font-weight:600;
    background:linear-gradient(135deg,#22c55e,#a3e635);color:#0b1120;cursor:pointer;
}
.btn-primary-modern:hover{filter:brightness(1.05);}

/* lỗi popup balloon */
.error-balloon {
    position: absolute;
    background: #fff;
    color: #b91c1c;
    font-size: 13px;
    padding: 6px 12px;
    border-radius: 10px;
    border: 1px solid #f87171;
    margin-top: 4px;
    box-shadow:0 8px 16px rgba(0,0,0,.35);
    z-index: 20;
    animation: fade .25s ease;
}
.error-balloon:before {
    content: "";
    position:absolute;
    top:-6px; left:20px;
    border-width:0 6px 6px 6px;
    border-style:solid;
    border-color:transparent transparent #fff transparent;
}
@keyframes fade{
    from{opacity:0;transform:translateY(-4px);}
    to{opacity:1;transform:translateY(0);}
}

/* rating buttons */
.rating-row{display:flex;gap:8px;margin-top:8px;}
.rating-btn{
    padding:6px 14px;border-radius:999px;border:1px solid #4b5563;cursor:pointer;
    background:#020617;color:#e5e7eb;font-size:13px;
}
.rating-btn.active{background:#22c55e;color:#0b1120;border-color:#22c55e;}
.border-error{border-color:#dc2626 !important;}
.back-link{text-decoration:none;font-size:13px;color:#93c5fd;margin-bottom:10px;display:inline-block;}
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

    <h1 class="page-title">Gửi phản hồi</h1>

    <?php if($success): ?>
        <div class="alert-success" style="background:rgba(16,185,129,.15);border:1px solid rgba(45,212,191,.7);padding:12px 16px;border-radius:12px;color:#bbf7d0;">
            <?= e($success) ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="card-shell">
            <h4>Chúng tôi luôn trân trọng ý kiến đóng góp của bạn</h4>
            <p style="font-size:13px;color:#9ca3af;">Vui lòng chia sẻ trải nghiệm. CSKH sẽ phản hồi sớm nhất.</p>

            <!-- Thông tin khách hàng -->
            <div class="row" style="margin-bottom:10px;">
                <div class="col-md-6">
                    <label>Họ tên khách hàng</label>
                    <input type="text" value="<?= e($user['Username'] ?? '') ?>" disabled>
                </div>
                <div class="col-md-6">
                    <label>Email</label>
                    <input type="text" value="<?= e($user['Email'] ?? '') ?>" disabled>
                </div>
            </div>

            <!-- Loại dịch vụ -->
            <div class="mb-3" style="position:relative;">
                <label>Loại dịch vụ *</label>
                <select name="loai"
                        id="service_type"
                        class="<?= ($btn_action && in_array('Vui lòng chọn loại dịch vụ.', $errors)) ? 'border-error' : '' ?>">
                    <option value="">-- Chọn loại dịch vụ --</option>
                    <option value="Lưu trú"     <?= $feedback['loai']==='Lưu trú'?'selected':'' ?>>Lưu trú</option>
                    <option value="Nhà hàng"    <?= $feedback['loai']==='Nhà hàng'?'selected':'' ?>>Nhà hàng</option>
                    <option value="SPA"         <?= $feedback['loai']==='SPA'?'selected':'' ?>>SPA</option>
                    <option value="Giặt là"     <?= $feedback['loai']==='Giặt là'?'selected':'' ?>>Giặt là</option>
                    <option value="Dịch vụ khác"<?= $feedback['loai']==='Dịch vụ khác'?'selected':'' ?>>Dịch vụ khác</option>
                </select>

                <?php if($btn_action && in_array('Vui lòng chọn loại dịch vụ.', $errors)): ?>
                    <div class="error-balloon">⚠ Vui lòng chọn loại dịch vụ.</div>
                <?php endif; ?>
            </div>

            <!-- Rating -->
            <div style="position:relative;">
                <label>Mức độ hài lòng *</label>
                <div class="rating-row">
                    <?php foreach([1,2,3,4,5] as $r): ?>
                    <label class="rating-btn <?= ($feedback['muc_do']==$r)?'active':'' ?>">
                        <input type="radio" name="muc_do" value="<?= $r ?>" style="display:none;"
                               <?= ($feedback['muc_do']==$r)?'checked':'' ?>>
                        <?= $r ?> ★
                    </label>
                    <?php endforeach; ?>
                </div>

                <?php if($btn_action && in_array('Vui lòng chọn mức độ hài lòng.', $errors)): ?>
                    <div class="error-balloon" style="left:10px;">⚠ Chọn mức độ hài lòng</div>
                <?php endif; ?>
            </div>

            <!-- Nội dung -->
            <div class="mt-3">
                <label>Nội dung phản hồi (tùy chọn)</label>
                <textarea name="noi_dung" rows="5" placeholder="Viết phản hồi của bạn tại đây..."><?= e($feedback['noi_dung']) ?></textarea>
            </div>

            <!-- File -->
            <div class="mt-3">
                <label>Đính kèm ảnh minh chứng (tùy chọn)</label>
                <input type="file" name="file" accept="image/*">
            </div>

            <div style="margin-top:18px;text-align:right;">
                <button class="btn-primary-modern"
                        name="btn_action" value="submit_feedback"
                        onclick="return confirm('Xác nhận gửi phản hồi này?');">
                    Gửi phản hồi
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// toggle nút rating
document.querySelectorAll(".rating-btn").forEach(btn=>{
    btn.addEventListener("click", ()=>{
        document.querySelectorAll(".rating-btn").forEach(b=>b.classList.remove("active"));
        btn.classList.add("active");
        btn.querySelector("input").checked=true;
    });
});
</script>

</body>
</html>
