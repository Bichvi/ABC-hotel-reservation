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
            WHERE Username = ? AND TrangThai = 'HoatDong'
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
        $maNV      = $this->safeGet($data, 'MaNhanVien'); // có thể null
        $trangThai = $this->safeGet($data, 'TrangThai') ?? 'HoatDong';

        if (!$username || !$password) {
            throw new Exception("Thiếu Username hoặc Password khi tạo tài khoản!");
        }

        // HASH MẬT KHẨU
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            INSERT INTO taikhoan (Username, Password, MaVaiTro, MaKhachHang, MaNhanVien, TrangThai)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("ssiiis", $username, $hashed, $role, $maKH, $maNV, $trangThai);

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
public function updateMeta(int $id, array $data): bool
{
    $sets = [];
    $params = [];
    $types = "";

    foreach ($data as $col => $val) {
        // Nếu là Password, phải hash trước khi cập nhật
        if (strtolower($col) === 'password') {
            // Kiểm tra xem đã được hash chưa (password hash thường bắt đầu bằng $2y$ hoặc $2a$)
            if (!preg_match('/^\$2[ay]\$/', $val)) {
                // Chưa hash, hash lại
                $val = password_hash($val, PASSWORD_DEFAULT);
            }
            // Nếu đã hash rồi thì giữ nguyên
        }
        
        $sets[] = "$col = ?";
        $params[] = $val;
        $types .= "s";
    }

    $sql = "UPDATE taikhoan SET " . implode(', ', $sets) . " WHERE MaTK = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    return $stmt->execute();
}

public function deleteAccount(int $id): bool
{
    $stmt = $this->db->prepare("DELETE FROM taikhoan WHERE MaTK = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
public function getById(int $id): ?array
{
    $stmt = $this->db->prepare("
        SELECT * FROM taikhoan 
        WHERE MaTK = ? 
        LIMIT 1
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}
/** ============================
 *  V2 — TẠO TÀI KHOẢN KHÁCH HÀNG
 * ============================ */
public function v2_createCustomerAccount($data)
{
    $hashed = password_hash($data['Password'], PASSWORD_DEFAULT);

    $stmt = $this->db->prepare("
        INSERT INTO taikhoan (Username, Password, MaVaiTro, MaKhachHang, TrangThai)
        VALUES (?, ?, ?, ?, 'HoatDong')
    ");

    $stmt->bind_param(
        "ssii",
        $data['Username'],
        $hashed,
        $data['MaVaiTro'],
        $data['MaKhachHang']
    );

    if (!$stmt->execute()) {
        throw new Exception("TaiKhoan::v2_createCustomerAccount error: " . $stmt->error);
    }

    return $this->db->insert_id;
}

/** Random password mới */
public function v2_randomPassword($len = 8)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $pass  = "";

    for ($i = 0; $i < $len; $i++) {
        $pass .= $chars[random_int(0, strlen($chars) - 1)];
    }

    return $pass;
}
public function v2_createAccountForKhachHang(int $maKH, string $username, string $plainPassword): bool
{
    $hash = password_hash($plainPassword, PASSWORD_BCRYPT);

    $sql = "
        INSERT INTO taikhoan (MaKhachHang, Username, Password, MaVaiTro)
        VALUES (?, ?, ?, 7)
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("iss", $maKH, $username, $hash);
    return $stmt->execute();
}



#====vi_tạo mới hàm cập nhật trạng thái tài khoản theo mã khách hàng====#

/* ========================================================================
 * CÁC PHƯƠNG THỨC DÀNH RIÊNG CHO QUẢN LÝ (Hậu tố _QL)
 * Không ảnh hưởng đến các actor khác
 * ======================================================================== */

/** Lấy tài khoản theo MaTK */
// public function getById($maTK) {
//     $stmt = $this->db->prepare("
//         SELECT * FROM taikhoan
//         WHERE MaTK = ?
//         LIMIT 1
//     ");
//     $stmt->bind_param("i", $maTK);
//     $stmt->execute();
//     return $stmt->get_result()->fetch_assoc();
// }



/** Lấy tài khoản theo MaTK - Dành cho Quản lý */
public function getById_QL($maTK) {
    $stmt = $this->db->prepare("
        SELECT * FROM taikhoan
        WHERE MaTK = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $maTK);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/** Tạo tài khoản cho khách hàng - Dành cho Quản lý */
public function createForCustomer_QL($data) {
    return $this->create([
        'Username'      => $this->safeGet($data, 'Username'),
        'Password'      => $this->safeGet($data, 'Password'),
        'MaVaiTro'      => $this->safeGet($data, 'MaVaiTro') ?? 7,
        'MaKhachHang'   => $this->safeGet($data, 'MaKhachHang')
    ]);
}

/** Cập nhật mật khẩu theo MaKhachHang - Dành cho Quản lý */
public function updatePasswordByCustomerId_QL($maKH, $newPassword)
{
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $sql = "UPDATE taikhoan SET Password = ? WHERE MaTK = (
                SELECT MaTK FROM khachhang WHERE MaKhachHang = ?
            )";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("si", $passwordHash, $maKH);
    return $stmt->execute();
}

/** Kiểm tra username tồn tại - Dành cho Quản lý */
public function existsUsername_QL($username) {
    $stmt = $this->db->prepare("
        SELECT 1 FROM taikhoan WHERE Username = ?
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}


}