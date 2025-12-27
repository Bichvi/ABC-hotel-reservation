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

    $sql = "
        SELECT * FROM khachhang
        WHERE 
            TenKH LIKE ?
            OR Email LIKE ?
            OR SDT LIKE ?
            OR CCCD LIKE ?
        ORDER BY MaKhachHang DESC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("ssss", $keyword, $keyword, $keyword, $keyword);
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

/**
 * Tìm kiếm hỗ trợ lọc theo tài khoản (username) và trạng thái có/không có tài khoản.
 * Giữ nguyên hàm `searchCustomers` để tránh thay đổi API hiện có.
 * @param string $keyword
 * @param string $loaiKhach
 * @param string $coTaiKhoan 'co' | 'khong' | ''
 * @param string $username
 * @return array
 */
public function searchByAccount($keyword = '', $loaiKhach = '', $coTaiKhoan = '', $username = '')
{
    $sql = "SELECT k.*, t.Username
            FROM khachhang k
            LEFT JOIN taikhoan t ON k.MaTK = t.MaTK
            WHERE 1=1";

    $types = '';
    $params = [];

    if ($keyword !== '') {
        $kw = "%{$keyword}%";
        $sql .= " AND (k.TenKH LIKE ? OR k.Email LIKE ? OR k.SDT LIKE ? OR k.CCCD LIKE ? )";
        $types .= 'ssss';
        array_push($params, $kw, $kw, $kw, $kw);
    }

    if ($loaiKhach !== '') {
        $sql .= " AND k.LoaiKhach = ?";
        $types .= 's';
        $params[] = $loaiKhach;
    }

    if ($username !== '') {
        $u = "%{$username}%";
        $sql .= " AND t.Username LIKE ?";
        $types .= 's';
        $params[] = $u;
    }

    if ($coTaiKhoan === 'co') {
        $sql .= " AND k.MaTK IS NOT NULL";
    } elseif ($coTaiKhoan === 'khong') {
        $sql .= " AND k.MaTK IS NULL";
    }

    $sql .= " ORDER BY k.MaKhachHang DESC";

    $stmt = $this->db->prepare($sql);
    if ($stmt === false) {
        throw new Exception('Prepare failed: ' . $this->db->error);
    }

    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
    /* ============================================================
     *  V2 – HỖ TRỢ ĐẶT PHÒNG TRỰC TIẾP
     * ============================================================
     */

    /**
     * V2: Tìm nhanh khách theo CCCD (dùng cho AJAX check CCCD).
     * Trả về thông tin cơ bản để fill form.
     */
    public function v2_findLiteByCCCD(string $cccd): ?array
    {
        if (trim($cccd) === '') {
            return null;
        }

        $sql = "
            SELECT MaKhachHang, TenKH, SDT, Email, CCCD, DiaChi, LoaiKhach
            FROM khachhang
            WHERE CCCD = ?
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $cccd);
        $stmt->execute();
        $rs = $stmt->get_result();

        return $rs->fetch_assoc() ?: null;
    }

    /**
     * V2: Tạo mới khách từ form đặt phòng trực tiếp.
     *  - Nếu CCCD đã tồn tại nhưng bạn CHỐT "dùng CCCD mới"
     *    thì nên tự kiểm tra ở controller trước khi gọi hàm này.
     *  - Ở đây chỉ đơn giản insert.
     *
     * @throws Exception khi insert lỗi
     */
    public function v2_createFromBookingForm(array $data): int
    {
        $sql = "
            INSERT INTO khachhang (TenKH, SDT, Email, CCCD, DiaChi, LoaiKhach)
            VALUES (?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("KhachHang::v2_createFromBookingForm - prepare fail: " . $this->db->error);
        }

        $ten       = $data['TenKH']     ?? '';
        $sdt       = $data['SDT']       ?? null;
        $email     = $data['Email']     ?? null;
        $cccd      = $data['CCCD']      ?? null;
        $diachi    = $data['DiaChi']    ?? null;
        $loaikhach = $data['LoaiKhach'] ?? 'Cá nhân';

        $stmt->bind_param("ssssss", $ten, $sdt, $email, $cccd, $diachi, $loaikhach);

        if (!$stmt->execute()) {
            throw new Exception("KhachHang::v2_createFromBookingForm - execute fail: " . $stmt->error);
        }

        return (int)$this->db->insert_id;
    }

    /**
     * V2: Tạo hoặc lấy khách theo CCCD.
     *  - Nếu tồn tại: trả về MaKhachHang (và optionally cập nhật liên lạc).
     *  - Nếu không: tạo mới.
     *  => Dùng cho trưởng đoàn / khách chính.
     */

    /** ============================
 *  V2 — TÌM KHÁCH KHÔNG GHI ĐÈ
 *  DÙNG CHO ĐĂNG KÝ ĐOÀN
 * ============================ */

    /** ============================
     *  V2 — TẠO KHÁCH MỚI KHÔNG ĐỤNG LOGIC CŨ
     * ============================ */
    public function v2_createNew($data)
    {
        $sql = "
            INSERT INTO khachhang (TenKH, SDT, Email, CCCD, DiaChi, LoaiKhach)
            VALUES (?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "ssssss",
            $data['TenKH'],
            $data['SDT'],
            $data['Email'],
            $data['CCCD'],
            $data['DiaChi'],
            $data['LoaiKhach']
        );

        if (!$stmt->execute()) {
            throw new Exception("KhachHang::v2_createNew: " . $stmt->error);
        }

        return (int)$this->db->insert_id;
    }
    public function existsCCCD($cccd)
    {
        if (!$cccd) return false;

        $sql = "SELECT 1 FROM khachhang WHERE CCCD = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $cccd);
        $stmt->execute();
        $rs = $stmt->get_result();

        return $rs->num_rows > 0;
    }
    public function v2_findByCCCD(string $cccd): ?array
{
    $sql = "SELECT * FROM khachhang WHERE CCCD = ? LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("s", $cccd);
    $stmt->execute();
    $rs = $stmt->get_result();
    return $rs->fetch_assoc() ?: null;
}

public function v2_getOrCreateByCCCD(array $data)
{
    $sql = "SELECT MaKhachHang FROM khachhang WHERE CCCD = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("s", $data['CCCD']);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if ($res) {
        return (int)$res['MaKhachHang'];
    }

    // 👉 CHẮC CHẮN INSERT
    $sql = "INSERT INTO khachhang 
        (TenKH, SDT, Email, CCCD, DiaChi, LoaiKhach)
        VALUES (?,?,?,?,?,?)";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param(
        "ssssss",
        $data['TenKH'],
        $data['SDT'],
        $data['Email'],
        $data['CCCD'],
        $data['DiaChi'],
        $data['LoaiKhach']
    );

    if (!$stmt->execute()) {
        throw new Exception("Không thể tạo khách hàng CCCD: {$data['CCCD']}");
    }

    return $this->db->insert_id;
}
// Lấy toàn bộ khách thuộc 1 giao dịch (đoàn)
public function getByGiaoDich(int $maGD): array
{
    $sql = "
        SELECT *
        FROM khachhang
        WHERE MaGiaoDich = ?
           OR MaKhachHang = (
                SELECT MaKhachHang
                FROM giaodich
                WHERE MaGiaoDich = ?
           )
        ORDER BY MaKhachHang
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("ii", $maGD, $maGD);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
// Update thông tin khách (trưởng đoàn hoặc thành viên)
public function updateThongTin(int $maKH, array $data): bool
{
    $sql = "
        UPDATE khachhang
        SET TenKH = ?, CCCD = ?, SDT = ?, Email = ?
        WHERE MaKhachHang = ?
        LIMIT 1
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param(
        "ssssi",
        $data['TenKH'],
        $data['CCCD'],
        $data['SDT'],
        $data['Email'],
        $maKH
    );
    return $stmt->execute();
}




#=====vi============================================

    /** Cập nhật liên kết tài khoản cho khách hàng */
    public function updateAccountLink($maKH, $maTK)
    {
        $sql = "UPDATE khachhang SET MaTK = ? WHERE MaKhachHang = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $maTK, $maKH);
        return $stmt->execute();
    }



    
    /** Lấy toàn bộ khách hàng kèm Username (dành cho Quản lý) */
    public function getAllCustomersWithUsername()
    {
        $sql = "SELECT k.*, t.Username 
                FROM khachhang k
                LEFT JOIN taikhoan t ON k.MaTK = t.MaTK
                ORDER BY k.MaKhachHang DESC";
        $rs  = $this->db->query($sql);
        return $rs->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Kiểm tra ràng buộc trước khi xóa khách hàng
     * Trả về mảng với 'can_delete', 'message', và 'details' (danh sách giao dịch/hóa đơn)
     */
    public function checkDeleteConstraints($maKH)
    {
        $result = [
            'can_delete' => true,
            'message' => '',
            'unpaid_transactions' => [],
            'unpaid_invoices' => [],
            'unresolved_feedback' => []
        ];

        // Kiểm tra 1: Giao dịch chưa thanh toán hoặc đang hoạt động
        // KHÔNG chặn nếu: TrangThai = 'DaHuy' (đã hủy) hoặc 'Paid' (đã thanh toán)
        // CHẶN nếu:
        //   - TrangThai IN ('Moi', 'Booked', 'Stayed') = Đang hoạt động
        //   - HOẶC: PhuongThucThanhToan = 'ChuaThanhToan' NHƯNG chưa hủy
        $sqlGiaoDich = "
            SELECT 
                MaGiaoDich,
                NgayGiaoDich,
                LoaiGiaoDich,
                TongTien,
                TrangThai,
                PhuongThucThanhToan,
                GhiChu
            FROM giaodich 
            WHERE MaKhachHang = ? 
            AND TrangThai NOT IN ('DaHuy', 'Paid')
            AND (
                TrangThai IN ('Moi', 'Booked', 'Stayed')
                OR PhuongThucThanhToan = 'ChuaThanhToan'
            )
            ORDER BY NgayGiaoDich DESC
        ";
        $stmt = $this->db->prepare($sqlGiaoDich);
        $stmt->bind_param("i", $maKH);
        $stmt->execute();
        $rs = $stmt->get_result();
        
        while ($row = $rs->fetch_assoc()) {
            $result['unpaid_transactions'][] = $row;
        }
        
        if (count($result['unpaid_transactions']) > 0) {
            $result['can_delete'] = false;
            $result['message'] = "Không thể xóa hồ sơ khách hàng vì còn giao dịch chưa thanh toán hoặc đang hoạt động.";
        }

        // Kiểm tra 1b: Hóa đơn chưa thanh toán (nếu có)
        $sqlHoaDon = "
            SELECT 
                MaHoaDon,
                NgayLap,
                TongTien,
                PhuongThucThanhToan,
                TrangThai,
                GhiChu
            FROM hoadon 
            WHERE MaKhachHang = ? 
            AND (TrangThai != 'DaThanhToan' OR PhuongThucThanhToan = 'ChuaThanhToan')
            ORDER BY NgayLap DESC
        ";
        $stmt = $this->db->prepare($sqlHoaDon);
        $stmt->bind_param("i", $maKH);
        $stmt->execute();
        $rs = $stmt->get_result();
        
        while ($row = $rs->fetch_assoc()) {
            $result['unpaid_invoices'][] = $row;
        }
        
        if (count($result['unpaid_invoices']) > 0) {
            $result['can_delete'] = false;
            if (empty($result['message'])) {
                $result['message'] = "Không thể xóa hồ sơ khách hàng vì còn hóa đơn chưa thanh toán.";
            }
        }

        // Kiểm tra 2: Phản hồi/Khiếu nại chưa xử lý
        $sqlPhanHoi = "
            SELECT 
                MaPH,
                LoaiDichVu,
                NoiDung,
                TinhTrang,
                NgayPhanHoi
            FROM phanhoi 
            WHERE MaKhachHang = ? 
            AND TinhTrang IN ('ChuaXuLy', 'DangXuLy')
            ORDER BY NgayPhanHoi DESC
        ";
        $stmt = $this->db->prepare($sqlPhanHoi);
        $stmt->bind_param("i", $maKH);
        $stmt->execute();
        $rs = $stmt->get_result();
        
        while ($row = $rs->fetch_assoc()) {
            $result['unresolved_feedback'][] = $row;
        }
        
        if (count($result['unresolved_feedback']) > 0) {
            $result['can_delete'] = false;
            if (empty($result['message'])) {
                $result['message'] = "Không thể xóa hồ sơ khách hàng vì còn phản hồi hoặc khiếu nại chưa được xử lý.";
            } else {
                $result['message'] .= " Ngoài ra còn phản hồi/khiếu nại chưa xử lý.";
            }
        }

        return $result;
    }

    /**
     * Xóa khách hàng (sau khi đã kiểm tra ràng buộc)
     * Trả về true nếu thành công, false nếu thất bại
     */
    public function deleteCustomer($maKH)
    {
        // Xóa tài khoản liên kết (nếu có)
        $sqlGetMaTK = "SELECT MaTK FROM khachhang WHERE MaKhachHang = ?";
        $stmt = $this->db->prepare($sqlGetMaTK);
        $stmt->bind_param("i", $maKH);
        $stmt->execute();
        $rs = $stmt->get_result()->fetch_assoc();
        
        if ($rs && !empty($rs['MaTK'])) {
            $maTK = $rs['MaTK'];
            $sqlDeleteAccount = "DELETE FROM taikhoan WHERE MaTK = ?";
            $stmtAcc = $this->db->prepare($sqlDeleteAccount);
            $stmtAcc->bind_param("i", $maTK);
            $stmtAcc->execute();
        }

        // Xóa khách hàng
        $sql = "DELETE FROM khachhang WHERE MaKhachHang = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $maKH);
        return $stmt->execute();
    }

    public function updateMaGiaoDich($maKH, $maGD)
    {
        $sql = "UPDATE khachhang SET MaGiaoDich = ? WHERE MaKhachHang = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $maGD, $maKH);
        return $stmt->execute();
    }

}