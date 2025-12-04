<?php

class ChiTietDichVu
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
     * Lấy danh sách dịch vụ theo mã giao dịch
     * (dùng cho màn Đặt dịch vụ, Check-out…)
     */
    public function getByGiaoDich(int $maGiaoDich): array
    {
        $sql = "
            SELECT
                ctdv.*,
                dv.TenDichVu,
                dv.GiaDichVu,
                p.SoPhong
            FROM chitietdichvu ctdv
            LEFT JOIN dichvu dv ON dv.MaDichVu = ctdv.MaDichVu
            LEFT JOIN phong  p  ON p.MaPhong  = ctdv.MaPhong
            WHERE ctdv.MaGiaoDich = ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $maGiaoDich);
        $stmt->execute();
        $rs = $stmt->get_result();

        $rows = [];
        if ($rs) {
            while ($row = $rs->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    /**
     * Thêm dịch vụ vào giao dịch
     */
    public function addService(array $data): bool
    {
        $sql = "
            INSERT INTO chitietdichvu
                (MaGiaoDich, MaPhong, MaDichVu, SoLuong, GiaBan, ThanhTien, GhiChu)
            VALUES
                (?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->bind_param(
            'iiiidds',
            $data['MaGiaoDich'],
            $data['MaPhong'],
            $data['MaDichVu'],
            $data['SoLuong'],
            $data['GiaBan'],
            $data['ThanhTien'],
            $data['GhiChu']
        );

        return $stmt->execute();
    }
}