<?php


// ================= CẤU HÌNH KẾT NỐI DB =================
$dbHost = 'localhost';
$dbName = 'abc_resort1';
$dbUser = 'root';
$dbPass = '';

$error = '';
$success = '';

// ================= THÊM MỚI — NHẬN THÔNG BÁO SAU ĐĂNG KÝ =================
if (isset($_GET['registered']) && $_GET['registered'] === 'success') {
    $success = "🎉 Tạo tài khoản thành công! Bạn có thể đăng nhập ngay.";
}
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    $error = 'Không kết nối được CSDL. Vui lòng kiểm tra cấu hình.';
}

// ================= XỬ LÝ ĐĂNG NHẬP =================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Vui lòng nhập đầy đủ Tên đăng nhập và Mật khẩu.';
    } else {
        $stmt = $pdo->prepare("
            SELECT tk.*, vt.TenVaiTro
            FROM taikhoan tk
            JOIN vaitro vt ON tk.MaVaiTro = vt.MaVaiTro
            WHERE tk.Username = :u AND tk.TrangThai = 'HoatDong'
        ");
        $stmt->execute([':u' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $error = 'Tài khoản không tồn tại hoặc đã bị khóa.';
        } else {
            $dbPassword = $user['Password'];

            // 🔐 Kiểm tra mật khẩu: hỗ trợ cả hash và plain text (để tương thích với dữ liệu cũ)
            $passwordMatch = false;
            if (password_verify($password, $dbPassword)) {
                // Mật khẩu đã được hash
                $passwordMatch = true;
            } elseif ($password === $dbPassword) {
                // Mật khẩu plain text (dữ liệu cũ) - tự động hash lại để bảo mật
                $passwordMatch = true;
                // Cập nhật mật khẩu đã hash vào database
                $updateStmt = $pdo->prepare("UPDATE taikhoan SET Password = ? WHERE MaTK = ?");
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt->execute([$hashedPassword, $user['MaTK']]);
            }
            
            if ($passwordMatch) {
                // Lưu session
                $_SESSION['user_id']    = $user['MaTK'];
                $_SESSION['username']   = $user['Username'];
                $_SESSION['role_id']    = $user['MaVaiTro'];
                $_SESSION['role_name']  = $user['TenVaiTro'];

                // Chuyển hướng sau khi đăng nhập thành công
                header('Location: index.php');
                exit;
            } else {
                $error = 'Mật khẩu không chính xác.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập hệ thống - ABC Resort</title>
    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            min-height: 100vh;
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: radial-gradient(circle at top left, #1e3c72, #2a5298 40%, #0f172a 100%);
            color: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-wrapper {
            max-width: 1000px;
            width: 100%;
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
            gap: 24px;
        }

        @media (max-width: 768px) {
            .login-wrapper {
                grid-template-columns: 1fr;
            }
        }

        .login-card {
            position: relative;
            border-radius: 24px;
            padding: 32px 32px 28px;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(148, 163, 184, 0.3);
            box-shadow:
                0 20px 50px rgba(15, 23, 42, 0.8),
                0 0 0 1px rgba(15, 23, 42, 0.6);
        }

        .login-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(15, 118, 110, 0.12);
            color: #5eead4;
            font-size: 12px;
            margin-bottom: 12px;
        }

        .login-tag i {
            font-size: 14px;
        }

        .brand-title {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #e5e7eb;
        }

        .brand-title span {
            color: #38bdf8;
        }

        .login-subtitle {
            margin-top: 6px;
            color: #9ca3af;
            font-size: 14px;
        }

        .form-label {
            font-size: 13px;
            color: #cbd5f5;
            margin-bottom: 4px;
        }

        .form-control {
            background: rgba(15, 23, 42, 0.9);
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.6);
            color: #e5e7eb;
            font-size: 14px;
            padding: 10px 38px;
        }

        .form-control:focus {
            background: rgba(15, 23, 42, 1);
            border-color: #38bdf8;
            box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.4);
            color: #f9fafb;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            font-size: 16px;
        }

        .btn-login {
            width: 100%;
            border-radius: 999px;
            padding: 10px 0;
            border: none;
            font-weight: 600;
            letter-spacing: 0.04em;
            font-size: 14px;
            text-transform: uppercase;
            background: linear-gradient(135deg, #06b6d4, #22c55e);
            color: #0f172a;
            box-shadow:
                0 12px 30px rgba(34, 197, 94, 0.35),
                0 0 0 1px rgba(15, 23, 42, 0.6);
        }

        .btn-login:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow:
                0 16px 40px rgba(34, 197, 94, 0.45),
                0 0 0 1px rgba(15, 23, 42, 0.6);
        }

        .extra-row {
            font-size: 12px;
            color: #9ca3af;
        }

        .extra-row a {
            color: #38bdf8;
            text-decoration: none;
        }

        .extra-row a:hover {
            text-decoration: underline;
        }

        .badge-role {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(15, 23, 42, 0.9);
            border-radius: 999px;
            padding: 4px 10px;
            border: 1px solid rgba(148, 163, 184, 0.4);
            font-size: 11px;
            color: #9ca3af;
        }

        .badge-role span {
            color: #e5e7eb;
            font-weight: 500;
        }

        .info-card {
            border-radius: 24px;
            padding: 26px 24px;
            background: radial-gradient(circle at top, rgba(56, 189, 248, 0.16), transparent 60%),
                        rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(148, 163, 184, 0.35);
            backdrop-filter: blur(16px);
            position: relative;
            overflow: hidden;
        }

        .info-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 10% 20%, rgba(56, 189, 248, 0.15), transparent 50%),
                radial-gradient(circle at 80% 0%, rgba(34, 197, 94, 0.2), transparent 55%);
            opacity: 0.8;
            pointer-events: none;
        }

        .info-inner {
            position: relative;
            z-index: 1;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(15, 23, 42, 0.8);
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            color: #e5e7eb;
            margin-bottom: 8px;
        }

        .pill i {
            color: #22c55e;
            font-size: 13px;
        }

        .info-title {
            font-size: 18px;
            font-weight: 600;
            color: #f9fafb;
            margin-bottom: 6px;
        }

        .info-text {
            font-size: 13px;
            color: #cbd5f5;
            margin-bottom: 14px;
        }

        .info-list {
            list-style: none;
            padding: 0;
            margin: 0 0 12px;
            font-size: 13px;
            color: #e5e7eb;
        }

        .info-list li {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 6px;
        }

        .info-list li i {
            font-size: 13px;
            color: #38bdf8;
            margin-top: 2px;
        }

        .mini-tag {
            font-size: 11px;
            color: #9ca3af;
        }

        .role-highlight {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed rgba(148, 163, 184, 0.35);
            font-size: 12px;
            color: #e5e7eb;
        }

        .role-highlight span {
            color: #38bdf8;
            font-weight: 500;
        }

        .error-alert {
            border-radius: 999px;
            font-size: 12px;
            padding: 6px 12px;
        }
        
    </style>
</head>
<body>

<div class="login-wrapper">
    <!-- CỘT FORM ĐĂNG NHẬP -->
    <div class="login-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <div class="login-tag">
                    <i class="bi bi-shield-lock"></i>
                    <span>Hệ thống quản lý đặt phòng</span>
                </div>
                <div class="brand-title">
                    ABC <span>Resort</span>
                </div>
            </div>
            <div class="text-end d-none d-md-block">
                <span class="badge-role">
                    <i class="bi bi-person-badge"></i>
                    <span>6 vai trò nghiệp vụ</span>
                </span>
            </div>
        </div>

        <p class="login-subtitle mb-3">
            Đăng nhập để sử dụng các chức năng nghiệp vụ theo vai trò: Lễ tân, Kế toán, Dịch vụ, CSKH, Quản lý hoặc Khách hàng.
        </p>
        <?php if (!empty($success)): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger error-alert d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form method="post" autocomplete="off" class="mt-3">
            <div class="mb-3">
                <label for="username" class="form-label">Tên đăng nhập</label>
                <div class="input-icon">
                    <i class="bi bi-person"></i>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        class="form-control"
                        placeholder="Ví dụ: letan1, ketoan1, cskh1..."
                        value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                        required
                    >
                </div>
            </div>

            <div class="mb-2">
                <label for="password" class="form-label d-flex justify-content-between">
                    <span>Mật khẩu</span>
                    <span class="mini-tag">Mặc định demo: <code>123456</code></span>
                </label>
                <div class="input-icon">
                    <i class="bi bi-lock"></i>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Nhập mật khẩu"
                        required
                    >
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-1 mb-3">
                <div class="form-check" style="font-size: 12px;">
                    <input class="form-check-input" type="checkbox" value="1" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">
                        Ghi nhớ đăng nhập
                    </label>
                </div>
                <a href="#" class="extra-row">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="btn btn-login mt-1">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                Đăng nhập hệ thống
            </button>

            <div class="extra-row text-center mt-3">
    Chưa có tài khoản? 
    <a href="index.php?controller=auth&action=register">Đăng ký ngay</a>  
</div>
<div style="margin-top: 20px; text-align: center;">
    <a href="index.php"
       style="
            display:inline-block;
            padding:10px 20px;
            background:#3498db;
            color:white;
            border-radius:6px;
            text-decoration:none;
            transition:0.2s;
        "
       onmouseover="this.style.background='#2980b9'"
       onmouseout="this.style.background='#3498db'">
        ← Quay về trang chủ
    </a>
</div>
        </form>
    </div>

    <!-- CỘT THÔNG TIN / DECOR -->
    <div class="info-card">
        <div class="info-inner">
            <div class="pill">
                <i class="bi bi-stars"></i>
                <span>ABC Resort Nha Trang – 5★</span>
            </div>
            <div class="info-title">
                Không chỉ là phòng, mà là cả hệ sinh thái dịch vụ.
            </div>
            <p class="info-text">
                Tài khoản đăng nhập sẽ quyết định giao diện và chức năng bạn được sử dụng:
                từ đặt phòng, check-in, check-out, xuất hóa đơn, đến xử lý phản hồi và báo cáo doanh thu.
            </p>

            <ul class="info-list">
                <li>
                    <i class="bi bi-key"></i>
                    <span>
                        <b>Lễ tân</b> – Đặt phòng trực tiếp, quản lý tình trạng phòng, làm thủ tục nhận / trả phòng.
                    </span>
                </li>
                <li>
                    <i class="bi bi-cash-stack"></i>
                    <span>
                        <b>Kế toán</b> – Theo dõi hóa đơn, chi phí, doanh thu và báo cáo tài chính.
                    </span>
                </li>
                <li>
                    <i class="bi bi-person-vcard"></i>
                    <span>
                        <b>CSKH</b> – Xử lý phản hồi, tạo & cập nhật chương trình khuyến mãi, chăm sóc khách hàng thân thiết.
                    </span>
                </li>
                <li>
                    <i class="bi bi-person-hearts"></i>
                    <span>
                        <b>Khách hàng / Trưởng đoàn</b> – Xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.
                    </span>
                </li>
            </ul>

            <div class="role-highlight">
                Dùng thử nhanh:
                <br>
                <span>Lễ tân</span> → <code>letan1 / 123456</code><br>
                <span>Kế toán</span> → <code>ketoan1 / 123456</code><br>
                <span>Khách hàng</span> → <code>khach1 / 123456</code>
            </div>
        </div>
    </div>
</div>

</body>
</html>