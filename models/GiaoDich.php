<?php

class GiaoDich
{
    /**
     * @var mysqli
     */
    private $db;

    const STATUS_MOI    = 'Moi';
    const STATUS_BOOKED = 'Booked';
    const STATUS_DA_HUY = 'DaHuy';
    const STATUS_STAYED = 'Stayed';
    const STATUS_PAID   = 'Paid';

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Tạo giao dịch đặt phòng
     * ---
     * GIẢI PHÁP FIX:
     *  • Không yêu cầu các key phải tồn tại
     *  • Nếu actor khác truyền lên → vẫn hoạt động bình thường
     *  • Nếu khách hàng không truyền → tự động NULL
     */
public function createBooking($data)
{
    
    $maKH   = $data['MaKhachHang']         ?? null;
    $maDoan = $data['MaDoan']              ?? null;
    $maNV   = $data['MaNhanVien']          ?? null;
    $maKM   = $data['MaKhuyenMai']         ?? null;

    // ENUM hợp lệ: DatPhong hoặc ThueTrucTiep
    $loai   = 'DatPhong';

    $tong   = (float)($data['TongTien'] ?? 0);
    $trang  = $data['TrangThai'] ?? self::STATUS_MOI;

    // ENUM hợp lệ
    $pay    = $data['PhuongThucThanhToan'] ?? 'ChuaThanhToan';
    $ghiChu = $data['GhiChu'] ?? null;

    $sql = "
        INSERT INTO giaodich
            (MaKhachHang, MaDoan, MaNhanVien, MaKhuyenMai,
             LoaiGiaoDich, TongTien, TrangThai, PhuongThucThanhToan, GhiChu)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) {
        throw new Exception("prepare error: " . $this->db->error);
    }

    // đúng 9 ký tự
    $stmt->bind_param(
        "iiiisdsss",
        $maKH,
        $maDoan,
        $maNV,
        $maKM,
        $loai,
        $tong,
        $trang,
        $pay,
        $ghiChu
    );

    if (!$stmt->execute()) {
        throw new Exception("execute error: " . $stmt->error);
    }

    return $this->db->insert_id;
}    /**
     * Tìm giao dịch theo mã hoặc CCCD
     */
   public function findByMaOrCCCD($maGiaoDich = null, $cccd = null)
{

    // Ép input sang string để tránh lỗi ctype_digit deprecated
    $inputGD = $maGiaoDich !== null ? (string)$maGiaoDich : null;
    $inputCC = $cccd !== null ? (string)$cccd : null;

    // Nhận dạng loại dữ liệu dựa trên độ dài
    if ($inputGD !== null && ctype_digit($inputGD)) {

        // Nếu số ≤ 8 → mã giao dịch
        if (strlen($inputGD) <= 8) {
            $maGiaoDich = (int)$inputGD;
            $cccd = null;
        }
        // Nếu số 9–12 → CCCD
        elseif (strlen($inputGD) >= 9 && strlen($inputGD) <= 12) {
            $cccd = $inputGD;
            $maGiaoDich = null;
        }
    }

    // Nếu truyền riêng CCCD → dùng CCCD
    if ($inputCC !== null && ctype_digit($inputCC)) {
        $cccd = $inputCC;
        $maGiaoDich = null;
    }

    // Build query
    $conditions = [];
    $params = [];
    $types = "";

    if (!empty($maGiaoDich)) {
        $conditions[] = "gd.MaGiaoDich = ?";
        $types .= "i";
        $params[] = (int)$maGiaoDich;
    }

    if (!empty($cccd)) {
        $conditions[] = "kh.CCCD = ?";
        $types .= "s";
        $params[] = $cccd;
    }

    if (empty($conditions)) {
        return null;
    }

    $where = implode(" OR ", $conditions);

    $sql = "
        SELECT gd.*, kh.TenKH, kh.CCCD, kh.SDT
        FROM giaodich gd
        INNER JOIN khachhang kh ON gd.MaKhachHang = kh.MaKhachHang
        WHERE $where
        ORDER BY gd.MaGiaoDich DESC
        LIMIT 1
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc() ?: null;
}
    public function getById($maGiaoDich)
    {
        $sql = "
            SELECT gd.*, kh.TenKH, kh.CCCD, kh.SDT,gd.MaKhuyenMai
            FROM giaodich gd
            LEFT JOIN khachhang kh ON gd.MaKhachHang = kh.MaKhachHang
            WHERE gd.MaGiaoDich = ?
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $maGiaoDich);
        $stmt->execute();
        $rs = $stmt->get_result();

        return $rs->fetch_assoc() ?: null;
    }

    public function updateTrangThai($maGiaoDich, $trangThai)
    {
        $sql = "UPDATE giaodich SET TrangThai = ? WHERE MaGiaoDich = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $trangThai, $maGiaoDich);
        return $stmt->execute();
    }

    public function cancel($maGiaoDich)
    {
        return $this->updateTrangThai($maGiaoDich, self::STATUS_DA_HUY);
    }
    public function co_updateCheckoutStatus(int $maGiaoDich, float $tongTien, string $pttt, string $ghiChu): bool
{
    $sql = "
        UPDATE giaodich
        SET TongTien = ?, 
            PhuongThucThanhToan = ?, 
            TrangThai = 'Paid',
            GhiChu = CONCAT(IFNULL(GhiChu, ''), '\n', ?)
        WHERE MaGiaoDich = ?
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("dssi", $tongTien, $pttt, $ghiChu, $maGiaoDich);
    return $stmt->execute();
}
// =======================
// CHECKOUT V2 – Lấy giao dịch (FULL INFO)
// =======================
public function getByIdV2(int $maGD): ?array
{
    $sql = "
        SELECT gd.*, kh.TenKH, kh.CCCD, kh.SDT
        FROM giaodich gd
        LEFT JOIN khachhang kh ON kh.MaKhachHang = gd.MaKhachHang
        WHERE gd.MaGiaoDich = ?
        LIMIT 1
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $maGD);
    $stmt->execute();
    $rs = $stmt->get_result();
    return $rs->fetch_assoc() ?: null;
}
    /* ============================================================
     *  V2 – TẠO GIAO DỊCH ĐẶT PHÒNG TRỰC TIẾP
     *  - Có MaDoan, MaKhuyenMai
     *  - Tính giảm giá cố định
     *  - Ghi chú chi tiết để sau này đối soát
     * ============================================================
     */

    /**
     * Tạo giao dịch cho use case Đặt phòng trực tiếp V2.
     *
     * @param array $data [
     *    'MaKhachHang',
     *    'MaDoan',
     *    'MaNhanVien',
     *    'MaKhuyenMai',
     *    'TongTienPhong',
     *    'TongTienDichVu',
     *    'TienGiam',
     *    'LoaiGiaoDich' (mặc định 'DatPhong'),
     *    'PhuongThucThanhToan' (mặc định 'ChuaThanhToan'),
     *    'GhiChu' (thêm thông tin V2 / soDem ... )
     * ]
     *
     * @return int MaGiaoDich
     * @throws Exception
     */
    public function v2_createDirectBooking(array $data): int
    {
        $maKH   = $data['MaKhachHang']  ?? null;
        $maDoan = $data['MaDoan']       ?? null;
        $maNV   = $data['MaNhanVien']   ?? null;
        $maKM   = $data['MaKhuyenMai']  ?? null;

        $tongPhong = (float)($data['TongTienPhong']  ?? 0);
        $tongDV    = (float)($data['TongTienDichVu'] ?? 0);
        $giam      = (float)($data['TienGiam']       ?? 0);

        $tong = max(0, $tongPhong + $tongDV - $giam);

        $loai  = $data['LoaiGiaoDich']        ?? 'DatPhong';
        $tt    = $data['TrangThai']           ?? self::STATUS_MOI;
        $pay   = $data['PhuongThucThanhToan'] ?? 'ChuaThanhToan';

        $ghiChuExtra = $data['GhiChu'] ?? '';
        $ghiChu = trim("Đặt phòng trực tiếp V2. 
Tổng phòng: {$tongPhong}; Tổng DV: {$tongDV}; Giảm: {$giam}.
{$ghiChuExtra}");

        $sql = "
            INSERT INTO giaodich
                (MaKhachHang, MaDoan, MaNhanVien, MaKhuyenMai,
                 LoaiGiaoDich, TongTien, TrangThai, PhuongThucThanhToan, GhiChu)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("GiaoDich::v2_createDirectBooking - prepare fail: " . $this->db->error);
        }

        $stmt->bind_param(
            "iiiisdsss",
            $maKH,
            $maDoan,
            $maNV,
            $maKM,
            $loai,
            $tong,
            $tt,
            $pay,
            $ghiChu
        );

        if (!$stmt->execute()) {
            throw new Exception("GiaoDich::v2_createDirectBooking - execute fail: " . $stmt->error);
        }

        return (int)$this->db->insert_id;
    }
    /**
 * Tạo giao dịch cho Đặt phòng trực tiếp V2.
 * - Không bắt buộc phải lưu ngày đến/đi, chỉ ghi vào GhiChu cho dễ tra cứu.
 */
public function createV2(array $data): ?int
{
    $maKH   = (int)($data['MaKhachHang'] ?? 0);
    $maDoan = !empty($data['MaDoan']) ? (int)$data['MaDoan'] : null;
    $maNV   = !empty($data['MaNhanVien']) ? (int)$data['MaNhanVien'] : null;
    $maKM   = !empty($data['MaKhuyenMai']) ? (int)$data['MaKhuyenMai'] : null;
    $tong   = (float)($data['TongTien'] ?? 0);

    // Các thông tin này CHỈ dùng để ghép vào GhiChu, không phải cột riêng
    $ngayDen = $data['NgayDen'] ?? null;
    $ngayDi  = $data['NgayDi']  ?? null;
    $soNgay  = $data['SoNgay']  ?? null;
    $ghiChu  = trim($data['GhiChu'] ?? '');

    // Ghi chú “header” cho V2
    $parts = ["Đặt phòng trực tiếp V2"];
    if ($ngayDen) $parts[] = "Ngày đến: {$ngayDen}";
    if ($ngayDi)  $parts[] = "Ngày đi: {$ngayDi}";
    if ($soNgay)  $parts[] = "Số ngày: {$soNgay}";

    $header = implode(', ', $parts) . '.';

    if ($ghiChu !== '') {
        $ghiChuFull = $header . "\n" . $ghiChu;
    } else {
        $ghiChuFull = $header;
    }

    $loai      = 'DatPhong';
    $trangThai = 'Booked';
    $pttt      = 'ChuaThanhToan';

    // BẢNG giaodich của anh có cột MaKhuyenMai, KHÔNG có NgayDen / NgayDi / SoNgay
    $sql = "
        INSERT INTO giaodich
            (MaKhachHang, MaDoan, MaNhanVien, MaKhuyenMai, LoaiGiaoDich, TongTien, TrangThai, PhuongThucThanhToan, GhiChu)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    if (!$stmt = $this->db->prepare($sql)) {
        error_log("GiaoDich::createV2 prepare fail: " . $this->db->error);
        return null;
    }

    // 4 int + 1 string + 1 double + 3 string  => iiiisdsss
    $stmt->bind_param(
        "iiiisdsss",
        $maKH,
        $maDoan,
        $maNV,
        $maKM,
        $loai,
        $tong,
        $trangThai,
        $pttt,
        $ghiChuFull
    );

    if (!$stmt->execute()) {
        error_log("GiaoDich::createV2 execute fail: " . $stmt->error);
        return null;
    }

    return (int)$this->db->insert_id;
}

/**
 * Cập nhật tổng tiền cho giao dịch.
 */
public function updateTongTien(int $maGiaoDich, float $tongTien): bool
{
    $sql = "UPDATE giaodich SET TongTien = ? WHERE MaGiaoDich = ?";

    if (!$stmt = $this->db->prepare($sql)) {
        error_log("GiaoDich::updateTongTien prepare fail: " . $this->db->error);
        return false;
    }

    $stmt->bind_param("di", $tongTien, $maGiaoDich);

    if (!$stmt->execute()) {
        error_log("GiaoDich::updateTongTien execute fail: " . $stmt->error);
        return false;
    }

    return true;
}
/**
 * V2 – Tạo giao dịch mới (trưởng đoàn + ngày + KM)
 */
public function v2_createBooking(int $maKH, string $ngayDen, string $ngayDi, ?int $maKM): ?int
{
    $sql = "
        INSERT INTO giaodich (MaKhachHang, NgayTao, NgayNhan, NgayTra, MaKhuyenMai, TrangThai)
        VALUES (?, NOW(), ?, ?, ?, 'Moi')
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("issi", $maKH, $ngayDen, $ngayDi, $maKM);
    $stmt->execute();

    return $stmt->insert_id ?: null;
}
    public function updateTien($maGD, $data)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE giaodich
            SET TongTien = ?, MaKhuyenMai = ?
            WHERE MaGiaoDich = ?
        ");
        $stmt->bind_param(
            "dii",
            $data['TongTien'],
            $data['MaKhuyenMai'],
            $maGD
        );
        $stmt->execute();
    }
public function updateMoney($maGD, $tongTien, $maKhuyenMai)
{
    $sql = "
        UPDATE giaodich
        SET TongTien = ?,
            MaKhuyenMai = ?
        WHERE MaGiaoDich = ?
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param(
        "dii",
        $tongTien,
        $maKhuyenMai,
        $maGD
    );

    return $stmt->execute();
}
private function normalizeMaKhuyenMai($maKM): ?int
{
    // null, '', '0', 0 => NULL
    if ($maKM === null) return null;
    if ($maKM === '' || $maKM === '0' || $maKM === 0) return null;

    $maKM = (int)$maKM;
    if ($maKM <= 0) return null;

    // check tồn tại trong bảng khuyenmai
    $sql = "SELECT 1 FROM khuyenmai WHERE MaKhuyenMai = ? LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $maKM);
    $stmt->execute();

    return ($stmt->get_result()->num_rows > 0) ? $maKM : null;
}

}