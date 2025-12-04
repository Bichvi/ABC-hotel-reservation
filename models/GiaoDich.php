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
        $rs = $stmt->get_result();

        return $rs->fetch_assoc() ?: null;
    }

    public function getById($maGiaoDich)
    {
        $sql = "
            SELECT gd.*, kh.TenKH, kh.CCCD, kh.SDT
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
}