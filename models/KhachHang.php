<?php
class KhachHang {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Tìm khách theo CCCD hoặc SDT hoặc Email
     * Dùng cho đặt phòng trực tiếp (tìm khách cũ).
     */
    public function findByIdentity($cccd, $sdt, $email) {
        $sql = "
            SELECT * FROM khachhang
            WHERE (CCCD = ? AND CCCD IS NOT NULL)
               OR (SDT = ? AND SDT IS NOT NULL)
               OR (Email = ? AND Email IS NOT NULL)
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sss", $cccd, $sdt, $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * 👉 HÀM MỚI: Tìm mã khách hàng theo 1 giá trị nhận dạng (CCCD / SDT / Email)
     * Dùng cho BookingController->findIdByIdentity($cmnd)
     */
    public function findIdByIdentity($identity)
    {
        $sql = "
            SELECT MaKhachHang
            FROM khachhang
            WHERE (CCCD = ? AND CCCD IS NOT NULL)
               OR (SDT = ?   AND SDT   IS NOT NULL)
               OR (Email = ? AND Email IS NOT NULL)
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        // dùng cùng 1 giá trị truyền vào cho 3 cột (tùy DB trùng cái nào)
        $stmt->bind_param("sss", $identity, $identity, $identity);
        $stmt->execute();
        $rs = $stmt->get_result();
        if ($row = $rs->fetch_assoc()) {
            return (int)$row['MaKhachHang'];
        }
        return null;
    }

    /**
     * Tìm khách theo CCCD (riêng cho use case Đăng ký tài khoản đoàn)
     */
    public function findByCCCD($cccd) {
        $sql = "SELECT * FROM khachhang WHERE CCCD = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $cccd);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Tạo khách hàng mới
     * $data = [
     *   'TenKH'     => '',
     *   'SDT'       => '',
     *   'Email'     => '',
     *   'CCCD'      => '',
     *   'DiaChi'    => '',
     *   'LoaiKhach' => 'Cá nhân' | 'Trưởng đoàn' | 'Thành viên' ...
     * ]
     */
    public function create($data) {
        $sql = "
            INSERT INTO khachhang (TenKH, SDT, Email, CCCD, DiaChi, LoaiKhach)
            VALUES (?, ?, ?, ?, ?, ?)
        ";
        $stmt = $this->db->prepare($sql);

        $ten       = $data['TenKH']     ?? '';
        $sdt       = $data['SDT']       ?? null;
        $email     = $data['Email']     ?? null;
        $cccd      = $data['CCCD']      ?? null;
        $diachi    = $data['DiaChi']    ?? null;
        $loaikhach = $data['LoaiKhach'] ?? 'Cá nhân';

        $stmt->bind_param("ssssss", $ten, $sdt, $email, $cccd, $diachi, $loaikhach);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Cập nhật thông tin liên lạc (SĐT, Email, Địa chỉ)
     * Dùng khi khách đã tồn tại nhưng đổi số điện thoại / email.
     */
    public function updateContact($maKH, $data) {
        $sql = "
            UPDATE khachhang
               SET SDT   = ?,
                   Email = ?,
                   DiaChi = ?
             WHERE MaKhachHang = ?
        ";
        $stmt = $this->db->prepare($sql);

        $sdt    = $data['SDT']    ?? null;
        $email  = $data['Email']  ?? null;
        $diachi = $data['DiaChi'] ?? null;

        $stmt->bind_param("sssi", $sdt, $email, $diachi, $maKH);
        return $stmt->execute();
    }

    /**
     * Lấy 1 khách theo id
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM khachhang WHERE MaKhachHang = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function existsEmailOrCCCD($email, $cccd)
{
    $sql = "SELECT 1 FROM khachhang WHERE Email = ? OR CCCD = ? LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("ss", $email, $cccd);
    $stmt->execute();
    $rs = $stmt->get_result();

    return $rs->num_rows > 0;
}
/* ============================================================
 *  ⭐ CÁC HÀM BỔ SUNG DÀNH RIÊNG CHO QuanlyController
 *  (Không trùng với các hàm đã có trong model)
 * ============================================================ */

    /** Lấy toàn bộ khách hàng */
    public function getAllCustomers()
    {
        $sql = "SELECT * FROM khachhang ORDER BY MaKhachHang DESC";
        $rs  = $this->db->query($sql);
        return $rs->fetch_all(MYSQLI_ASSOC);
    }

    /** Tìm kiếm khách hàng theo tên / SDT / email / CCCD */
    public function searchCustomers1($keyword)
    {
        $keyword = "%$keyword%";
        $sql = "
            SELECT * FROM khachhang
            WHERE TenKH LIKE ?
               OR SDT   LIKE ?
               OR Email LIKE ?
               OR CCCD  LIKE ?
            ORDER BY MaKhachHang DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssss", $keyword, $keyword, $keyword, $keyword);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Kiểm tra trùng khi thêm mới (Email / SĐT / CCCD) */
    public function existsDuplicate($email, $sdt, $cccd)
    {
        $sql = "
            SELECT 1 
            FROM khachhang 
            WHERE Email = ? 
               OR SDT   = ? 
               OR CCCD  = ?
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sss", $email, $sdt, $cccd);
        $stmt->execute();
        $rs = $stmt->get_result();

        return $rs->num_rows > 0;
    }

    /** Lấy danh sách khách hàng bị trùng để hiển thị */
    public function findDuplicates($email, $sdt, $cccd)
    {
        $sql = "
            SELECT MaKhachHang, TenKH, SDT, Email, CCCD
            FROM khachhang 
            WHERE Email = ? 
               OR SDT   = ? 
               OR CCCD  = ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sss", $email, $sdt, $cccd);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Kiểm tra thông tin trùng lặp khi cập nhật hồ sơ */
    public function existsDuplicateForUpdate($id, $email, $sdt, $cccd)
    {
        $sql = "
            SELECT 1
            FROM khachhang
            WHERE MaKhachHang <> ?
              AND (
                    Email = ?
                 OR SDT   = ?
                 OR CCCD  = ?
                  )
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isss", $id, $email, $sdt, $cccd);
        $stmt->execute();
        $rs = $stmt->get_result();

        return $rs->num_rows > 0;
    }

    /** Cập nhật toàn bộ thông tin khách hàng */
    public function updateFull($id, $data)
    {
        $sql = "
            UPDATE khachhang
               SET TenKH = ?, 
                   SDT   = ?, 
                   Email = ?, 
                   CCCD  = ?
             WHERE MaKhachHang = ?
        ";

        $stmt = $this->db->prepare($sql);

        $ten   = $data['TenKH'];
        $sdt   = $data['SDT'];
        $email = $data['Email'];
        $cccd  = $data['CCCD'];

        $stmt->bind_param("ssssi", $ten, $sdt, $email, $cccd, $id);
        return $stmt->execute();
    }

    /** Xóa khách hàng */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM khachhang WHERE MaKhachHang = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /** Cập nhật mật khẩu theo MaKhachHang (dành cho QuanlyController) */
    public function updatePasswordByCustomerId($maKH, $newPassword)
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE taikhoan SET Password = ? WHERE MaKhachHang = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $hash, $maKH);

        return $stmt->execute();
    }
    public function searchCustomers($keyword)
{
    $keyword = "%{$keyword}%";
    
    // Xử lý tìm kiếm theo mã khách hàng (số)
    $numberOnly = preg_replace('/[^0-9]/', '', $keyword);
    $searchId = $numberOnly !== '' ? (int)$numberOnly : -1;

    $sql = "
        SELECT * FROM khachhang
        WHERE 
            MaKhachHang = ?
            OR TenKH LIKE ?
            OR Email LIKE ?
            OR SDT LIKE ?
            OR CCCD LIKE ?
        ORDER BY MaKhachHang DESC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("issss", $searchId, $keyword, $keyword, $keyword, $keyword);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
/** Tìm khách hàng theo CCCD (hàm mới, không ảnh hưởng logic khác) */
public function searchByCCCDExact($cccd)
{
    if (!$cccd) return [];

    $sql = "SELECT * FROM khachhang WHERE CCCD = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("s", $cccd);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
}