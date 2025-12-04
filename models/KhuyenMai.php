<?php

class KhuyenMai
{
    /**
     * @var mysqli
     */
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Tìm khuyến mãi hợp lệ theo mã & khoảng ngày.
     * Giả định bảng: khuyenmai(MaKhuyenMai, MaCode, PhanTramGiam, NgayBatDau, NgayKetThuc, TrangThai)
     */
    public function findValidByCode($code, $ngayNhan, $ngayTra)
    {
        $sql = "
            SELECT *
            FROM khuyenmai
            WHERE MaCode = ?
              AND TrangThai = 'HoatDong'
              AND NgayBatDau <= ?
              AND NgayKetThuc >= ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sss", $code, $ngayNhan, $ngayTra);
        $stmt->execute();
        $rs = $stmt->get_result();
        return $rs->fetch_assoc() ?: null;
    }
}