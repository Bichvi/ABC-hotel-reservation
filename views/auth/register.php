<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ================= CẤU HÌNH DB =================
$dbHost = "localhost";
$dbName = "abc_resort";
$dbUser = "root";
$dbPass = "";

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Exception $e) {
    die("Không thể kết nối CSDL!");
}

$errors  = [];
$success = "";

// ================= XỬ LÝ SUBMIT FORM =================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Lấy dữ liệu từ form
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $repass   = trim($_POST['repass'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $sdt      = trim($_POST['sdt'] ?? '');
    $cccd     = trim($_POST['cccd'] ?? '');

    // ===== VALIDATION CƠ BẢN =====
    if ($fullname === "") {
        $errors[] = "Vui lòng nhập họ tên.";
    }

    if (!preg_match('/^[a-zA-Z0-9_]{5,20}$/', $username)) {
        $errors[] = "Tên đăng nhập chỉ gồm chữ / số / gạch dưới (5–20 ký tự).";
    }

    if (strlen($password) < 6) {
        $errors[] = "Mật khẩu phải từ 6 ký tự trở lên.";
    }

    if ($password !== $repass) {
        $errors[] = "Mật khẩu nhập lại không khớp.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ.";
    }

    if (!preg_match('/^(0|\+84)\d{8,10}$/', $sdt)) {
        $errors[] = "Số điện thoại không hợp lệ.";
    }

    if (!preg_match('/^[0-9]{9,12}$/', $cccd)) {
        $errors[] = "CCCD/CMND không hợp lệ.";
    }

    // ===== CHECK TRÙNG USERNAME / EMAIL / CCCD =====
    if (empty($errors)) {
        // Username trùng?
        $stmt = $pdo->prepare("SELECT MaTK FROM taikhoan WHERE Username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = "Tên đăng nhập đã tồn tại, vui lòng chọn tên khác.";
        }

        // Email hoặc CCCD trùng trong bảng khách hàng?
        $stmt = $pdo->prepare("SELECT MaKhachHang FROM khachhang WHERE Email = ? OR CCCD = ?");
        $stmt->execute([$email, $cccd]);
        if ($stmt->fetch()) {
            $errors[] = "Email hoặc CCCD đã được sử dụng cho một tài khoản khác.";
        }
    }

    // ===== KHÔNG LỖI → TIẾN HÀNH TẠO TK & KHÁCH HÀNG =====
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // 1) Thêm vào bảng khachhang
            //    (KHÔNG chèn MaTK – vì bảng này trong CSDL không có cột MaTK)
            $stmt = $pdo->prepare("
                INSERT INTO khachhang (TenKH, Email, SDT, CCCD)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$fullname, $email, $sdt, $cccd]);

            $maKhachHang = (int)$pdo->lastInsertId();

            // 2) Hash mật khẩu
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // 3) Thêm vào bảng taikhoan
            //    Giả sử:
            //      - MaVaiTro = 7 là 'Khách hàng'
            //      - TrangThai = 'HoatDong'
            //      - Có cột MaKhachHang trong bảng taikhoan
            $stmt = $pdo->prepare("
                INSERT INTO taikhoan (Username, Password, MaVaiTro, TrangThai, MaKhachHang)
                VALUES (?, ?, 7, 'HoatDong', ?)
            ");
            $stmt->execute([$username, $hashedPassword, $maKhachHang]);

            $pdo->commit();

            // Lưu thông báo để hiển thị bên trang login (nếu muốn)
            $_SESSION['register_success'] = "Tạo tài khoản thành công! Bạn hãy đăng nhập để sử dụng hệ thống.";

            // Chuyển về trang đăng nhập
            header("Location: login.php");
            exit;

        } catch (Exception $ex) {
            $pdo->rollBack();
            $errors[] = "Lỗi hệ thống: " . $ex->getMessage();
        }
    }
}

// Helper tránh lỗi khi echo
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản - ABC Resort</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: radial-gradient(circle at top left, #1e3c72, #2a5298 40%, #020617 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px;
            color: #e5e7eb;
        }
        .reg-wrapper {
            max-width: 900px;
            width: 100%;
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
            gap: 20px;
        }
        @media (max-width: 768px) {
            .reg-wrapper {
                grid-template-columns: 1fr;
            }
        }
        .reg-card {
            border-radius: 24px;
            padding: 28px 26px;
            background: rgba(15,23,42,0.9);
            border: 1px solid rgba(148,163,184,0.35);
            box-shadow: 0 22px 50px rgba(0,0,0,0.7);
            backdrop-filter: blur(16px);
        }
        .reg-title {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        .reg-title span {
            color: #38bdf8;
        }
        .reg-subtitle {
            font-size: 13px;
            color: #9ca3af;
        }
        .form-label {
            font-size: 13px;
            color: #cbd5f5;
            margin-bottom: 4px;
        }
        .form-control {
            background: rgba(15,23,42,0.95);
            border-radius: 999px;
            border: 1px solid rgba(148,163,184,0.6);
            color: #e5e7eb;
            font-size: 14px;
            padding: 10px 16px;
        }
        .form-control:focus {
            background: #020617;
            border-color: #38bdf8;
            box-shadow: 0 0 0 1px rgba(56,189,248,0.4);
        }
        .btn-submit {
            width: 100%;
            border-radius: 999px;
            padding: 10px 0;
            background: linear-gradient(135deg, #06b6d4, #22c55e);
            border: none;
            color: #0f172a;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            font-size: 13px;
            box-shadow: 0 14px 30px rgba(34,197,94,0.35);
        }
        .btn-submit:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 20px 40px rgba(34,197,94,0.45);
        }
        .info-card {
            border-radius: 24px;
            padding: 24px 22px;
            background: radial-gradient(circle at top, rgba(56,189,248,.18), transparent 60%),
                        rgba(15,23,42,0.85);
            border: 1px solid rgba(148,163,184,0.35);
            box-shadow: 0 18px 40px rgba(0,0,0,0.65);
        }
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(15,23,42,0.85);
            border: 1px solid rgba(148,163,184,0.4);
            font-size: 11px;
            margin-bottom: 10px;
        }
        .pill i { color: #22c55e; }
        .info-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .info-text {
            font-size: 13px;
            color: #cbd5f5;
            margin-bottom: 10px;
        }
        .info-list {
            list-style: none;
            padding-left: 0;
            font-size: 13px;
        }
        .info-list li {
            display: flex;
            gap: 8px;
            margin-bottom: 6px;
        }
        .info-list li i {
            font-size: 13px;
            color: #38bdf8;
            margin-top: 2px;
        }
        .error-alert {
            font-size: 13px;
        }
        a.link-login {
            color: #38bdf8;
            text-decoration: none;
            font-size: 13px;
        }
        a.link-login:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="reg-wrapper">
    <!-- FORM ĐĂNG KÝ -->
    <div class="reg-card">
        <div class="mb-3">
            <div class="reg-title">
                ABC <span>Resort</span>
            </div>
            <div class="reg-subtitle">
                Đăng ký tài khoản khách hàng để đặt phòng online, xem lịch sử lưu trú và sử dụng dịch vụ nhanh chóng.
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger error-alert">
                <strong>Có lỗi xảy ra:</strong>
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $e): ?>
                        <li><?= e($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" autocomplete="off" class="mt-2">
            <div class="mb-2">
                <label class="form-label">Họ tên</label>
                <input type="text" name="fullname" class="form-control"
                       value="<?= e($_POST['fullname'] ?? '') ?>" required>
            </div>

            <div class="mb-2">
                <label class="form-label">Tên đăng nhập</label>
                <input type="text" name="username" class="form-control"
                       value="<?= e($_POST['username'] ?? '') ?>" required>
            </div>

            <div class="row g-2">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Nhập lại mật khẩu</label>
                    <input type="password" name="repass" class="form-control" required>
                </div>
            </div>

            <div class="mb-2">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                       value="<?= e($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="row g-2">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="sdt" class="form-control"
                           value="<?= e($_POST['sdt'] ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">CCCD / CMND</label>
                    <input type="text" name="cccd" class="form-control"
                           value="<?= e($_POST['cccd'] ?? '') ?>" required>
                </div>
            </div>

            <button class="btn-submit">
                <i class="bi bi-person-plus-fill me-2"></i> Tạo tài khoản
            </button>

            <p class="text-center mt-3 mb-0" style="font-size:13px;">
                Đã có tài khoản?
                <a href="login.php" class="link-login">Đăng nhập ngay</a>
            </p>
        </form>
    </div>

    <!-- CỘT THÔNG TIN / MÔ TẢ -->
    <div class="info-card d-none d-md-block">
        <div class="pill">
            <i class="bi bi-stars"></i>
            <span>Trải nghiệm nghỉ dưỡng 5★</span>
        </div>
        <div class="info-title">
            Tài khoản của bạn – chìa khóa cho mọi dịch vụ tại ABC Resort.
        </div>
        <p class="info-text">
            Sau khi đăng ký, bạn có thể đặt phòng trực tuyến, theo dõi trạng thái đặt phòng,
            quản lý thông tin cá nhân và nhận ưu đãi dành riêng cho khách hàng thành viên.
        </p>
        <ul class="info-list">
            <li>
                <i class="bi bi-calendar-check"></i>
                <span>Chủ động chọn loại phòng, view và số khách phù hợp cho từng kỳ nghỉ.</span>
            </li>
            <li>
                <i class="bi bi-bell"></i>
                <span>Nhận thông báo xác nhận đặt phòng, nhắc lịch nhận/trả phòng.</span>
            </li>
            <li>
                <i class="bi bi-gift"></i>
                <span>Tích điểm và nhận ưu đãi dành cho khách hàng thân thiết.</span>
            </li>
        </ul>
        <p class="info-text mb-0">
            Mọi thông tin cá nhân của bạn được bảo mật và chỉ sử dụng cho mục đích phục vụ dịch vụ tại resort.
        </p>
    </div>
</div>

</body>
</html>