<?php
class AuthController extends Controller
{
    public function login()
    {
        // Hiện thông báo đăng ký thành công
        if (isset($_GET['registered']) && $_GET['registered'] === 'success') {
            $success = "Tạo tài khoản thành công! Bạn có thể đăng nhập ngay.";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $tk = new TaiKhoan();
            $user = $tk->getByUsername($username);

            if (!$user || !password_verify($password, $user['Password'])) {
                $error = "Sai tên đăng nhập hoặc mật khẩu!";
                return $this->view("auth/login", compact('error'));
            }

            Auth::login($user);

            // Điều hướng theo vai trò
            $roleId = (int)$user['MaVaiTro'];

            $map = [
                1 => ['dashboard', 'admin'],
                2 => ['letan', 'index'],
                3 => ['dashboard', 'ketoan'],
                4 => ['dashboard', 'dichvu'],
                5 => ['dashboard', 'cskh'],
                6 => ['dashboard', 'quanly'],
                7 => ['khachhang', 'dashboard'],
            ];

            if (!isset($map[$roleId])) {
                header("Location: index.php");
                exit;
            }

            header("Location: index.php?controller={$map[$roleId][0]}&action={$map[$roleId][1]}");
            exit;
        }

        $this->view("auth/login", isset($success) ? compact('success') : []);
    }

    // =======================
    //       ĐĂNG KÝ
    // =======================
    public function register()
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $fullname = trim($_POST['fullname'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $repass   = trim($_POST['repass'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $sdt      = trim($_POST['sdt'] ?? '');
            $cccd     = trim($_POST['cccd'] ?? '');

            // Validate
            if ($fullname === "") $errors[] = "Vui lòng nhập họ tên.";
            if (!preg_match('/^[a-zA-Z0-9_]{5,20}$/', $username))
                $errors[] = "Tên đăng nhập chỉ gồm chữ, số hoặc gạch dưới (5–20 ký tự).";

            if (strlen($password) < 6) $errors[] = "Mật khẩu phải >= 6 ký tự.";
            if ($password !== $repass) $errors[] = "Mật khẩu nhập lại không khớp.";

            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                $errors[] = "Email không hợp lệ.";

            if (!preg_match('/^(0|\+84)\d{8,10}$/', $sdt))
                $errors[] = "Số điện thoại không hợp lệ.";

            if (!preg_match('/^[0-9]{9,12}$/', $cccd))
                $errors[] = "CCCD không hợp lệ.";

            // Models
            $tk = new TaiKhoan();
            $kh = new KhachHang();

            if ($tk->existsUsername($username))
                $errors[] = "Tên đăng nhập đã tồn tại.";

            if ($kh->existsEmailOrCCCD($email, $cccd))
                $errors[] = "Email hoặc CCCD đã được sử dụng.";

            if (empty($errors)) {
                try {
                    $db = Database::getConnection();
                    $db->begin_transaction();

                    // Tạo tài khoản
                    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

                    $maTK = $tk->create([
                        'Username'  => $username,
                        'Password'  => $passwordHash,
                        'MaVaiTro'  => 7, // khách hàng
                        'TrangThai' => 'HoatDong'
                    ]);

                    // Tạo khách hàng
                    $kh->create([
                        'MaTK'   => $maTK,
                        'TenKH'  => $fullname,
                        'Email'  => $email,
                        'SDT'    => $sdt,
                        'CCCD'   => $cccd,
                    ]);

                    $db->commit();

                    // 🔥 FIX ĐÚNG TẠI ĐÂY
                    header("Location: index.php?controller=auth&action=login&registered=success");
                    exit;

                } catch (Exception $e) {
                    $db->rollback();
                    $errors[] = "Lỗi hệ thống: " . $e->getMessage();
                }
            }

            return $this->view("auth/register", compact('errors'));
        }

        $this->view("auth/register");
    }


    public function logout()
    {
        Auth::logout();
        header("Location: index.php?controller=auth&action=login");
        exit;
    }
}