<?php

class TaiKhoan {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /** Lấy tài khoản theo username */
    public function getByUsername($username) {
        $stmt = $this->db->prepare("
            SELECT * FROM taikhoan 
            WHERE Username = ? 
            LIMIT 1
        ");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /** Kiểm tra username tồn tại */
    public function existsUsername($username) {
        $stmt = $this->db->prepare("
            SELECT 1 FROM taikhoan WHERE Username = ?
        ");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /** Chuẩn hóa key để không ảnh hưởng các controller khác */
    private function safeGet($arr, $key) {
        // Cho phép Username hoặc username
        if (isset($arr[$key])) return $arr[$key];

        $lower = strtolower($key);
        foreach ($arr as $k => $v) {
            if (strtolower($k) === $lower) return $v;
        }
        return null;
    }

    /** Tạo tài khoản mới (hash mật khẩu) */
    public function create($data) {
        // Nhận cả Username và username
        $username  = $this->safeGet($data, 'Username');
        $password  = $this->safeGet($data, 'Password');
        $role      = (int)($this->safeGet($data, 'MaVaiTro') ?? 7);
        $maKH      = $this->safeGet($data, 'MaKhachHang'); // có thể null

        if (!$username || !$password) {
            throw new Exception("Thiếu Username hoặc Password khi tạo tài khoản!");
        }

        // HASH MẬT KHẨU
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            INSERT INTO taikhoan (Username, Password, MaVaiTro, MaKhachHang, TrangThai)
            VALUES (?, ?, ?, ?, 'HoatDong')
        ");

        $stmt->bind_param("ssii", $username, $hashed, $role, $maKH);

        if (!$stmt->execute()) {
            throw new Exception("Lỗi SQL (TaiKhoan->create): " . $stmt->error);
        }

        return $this->db->insert_id;
    }

    /** Tạo TK cho khách hàng (đoàn / cá nhân) */
    public function createForCustomer($data) {
        return $this->create([
            'Username'      => $this->safeGet($data, 'Username'),
            'Password'      => $this->safeGet($data, 'Password'),
            'MaVaiTro'      => $this->safeGet($data, 'MaVaiTro') ?? 7,
            'MaKhachHang'   => $this->safeGet($data, 'MaKhachHang')
        ]);
    }

    /** Lấy TK theo mã khách hàng */
    public function getByKhachHangId($maKH) {
        $stmt = $this->db->prepare("
            SELECT * FROM taikhoan
            WHERE MaKhachHang = ?
            LIMIT 1
        ");
        $stmt->bind_param("i", $maKH);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /** Cập nhật mật khẩu */
    public function updatePassword($maTK, $pwd) {
        $hashed = password_hash($pwd, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            UPDATE taikhoan SET Password = ? WHERE MaTK = ?
        ");
        $stmt->bind_param("si", $hashed, $maTK);
        return $stmt->execute();
    }

    /** Password random */
    public function generateRandomPassword($len = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $pwd = '';
        for ($i = 0; $i < $len; $i++) {
            $pwd .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $pwd;
    }
        public function updatePasswordByCustomerId($maKH, $passwordHash)
{
    $sql = "UPDATE taikhoan SET Password = ? WHERE MaTK = (
                SELECT MaTK FROM khachhang WHERE MaKhachHang = ?
            )";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("si", $passwordHash, $maKH);
    return $stmt->execute();
}
}