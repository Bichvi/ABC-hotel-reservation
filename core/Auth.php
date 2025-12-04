<?php
class Auth
{
    // Lưu user vào session
    public static function login(array $user)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user'] = $user;
    }

    // Xóa session
    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['user']);
        session_destroy();
    }

    // Kiểm tra đã đăng nhập chưa
    public static function check(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user']);
    }

    // Lấy thông tin user hiện tại
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return $_SESSION['user'];
    }

    /**
     * Kiểm tra user có thuộc 1 trong các vai trò cho phép không
     * $allowed có thể là:
     *   - mã vai trò: [1,2,3]
     *   - tên vai trò: ['Admin', 'LeTan']
     *   - hoặc mix: [2, 'LeTan']
     */
    public static function hasRole(array $allowed): bool
    {
        if (!self::check()) {
            return false;
        }

        $user = self::user();
        $currentRoleId = isset($user['MaVaiTro']) ? (int)$user['MaVaiTro'] : null;

        if ($currentRoleId === null) {
            return false;
        }

        // Tách danh sách allowed thành ID và tên
        $allowedIds   = [];
        $allowedNames = [];

        foreach ($allowed as $role) {
            if (is_numeric($role)) {
                $allowedIds[] = (int)$role;
            } else {
                $allowedNames[] = $role;
            }
        }

        // Nếu truyền theo ID và trùng → OK
        if (!empty($allowedIds) && in_array($currentRoleId, $allowedIds, true)) {
            return true;
        }

        // Nếu truyền theo tên → truy vấn bảng vaitro để lấy TenVaiTro của user
        if (!empty($allowedNames)) {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT TenVaiTro FROM vaitro WHERE MaVaiTro = ? LIMIT 1");
            $stmt->bind_param("i", $currentRoleId);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res && in_array($res['TenVaiTro'], $allowedNames, true)) {
                return true;
            }
        }

        return false;
    }

    /* ================== THÊM MỚI PHẦN NÀY ================== */

    /**
     * Bắt buộc phải đăng nhập
     */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            // chuyển về trang login
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
    }

    /**
     * Bắt buộc phải thuộc 1 trong các vai trò cho phép
     * Ví dụ:
     *  - Auth::requireRole([2]);              // theo MaVaiTro
     *  - Auth::requireRole(['LeTan']);        // theo TenVaiTro
     *  - Auth::requireRole([7, 'KhachHang']); // mix
     */
    public static function requireRole(array $allowed): void
    {
        // Đảm bảo đã login
        self::requireLogin();

        if (!self::hasRole($allowed)) {
            http_response_code(403);
            echo "Bạn không có quyền truy cập chức năng này.<br>";
            echo "<a href='index.php'>Quay lại trang chủ</a>";
            exit;
        }
    }
}