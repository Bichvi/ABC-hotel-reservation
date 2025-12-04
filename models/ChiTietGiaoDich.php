<?php

class ChiTietGiaoDich
{
    /**
     * @var mysqli
     */
    private $db;

    // Trạng thái hủy trong bảng chitietgiaodich
    const STATUS_CANCELLED = 'DaHuy';
    const STATUS_BOOKED    = 'Booked'; // ⭐ THÊM Ở ĐÂY

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Thêm chi tiết đặt phòng
     */
    public function addRoomBooking($data)
    {
        $sql = "
            INSERT INTO chitietgiaodich
                (MaGiaoDich, MaPhong, SoNguoi, NgayNhanDuKien, NgayTraDuKien, DonGia, ThanhTien, TrangThai, GhiChu)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->bind_param(
            'iiissddss',
            $data['MaGiaoDich'],
            $data['MaPhong'],
            $data['SoNguoi'],
            $data['NgayNhanDuKien'],
            $data['NgayTraDuKien'],
            $data['DonGia'],
            $data['ThanhTien'],
            $data['TrangThai'],
            $data['GhiChu']
        );

        return $stmt->execute();
    }

    /**
     * Thêm chi tiết dịch vụ
     */
    public function addService($data)
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

    /**
     * Lấy danh sách phòng trong 1 giao dịch (JOIN phòng)
     */
    public function getPhongByGiaoDich($maGiaoDich)
{
    $sql = "
        SELECT 
            ct.MaGiaoDich,
            ct.SoNguoi, 
            ct.MaPhong,
            ct.TrangThai      AS TrangThaiCT,      -- trạng thái trong chi tiết GD
            ct.NgayNhanDuKien,
            ct.NgayTraDuKien,
            ct.DonGia,
            ct.ThanhTien,
            p.SoPhong,
            p.LoaiPhong,
            p.SoKhachToiDa,
            p.TrangThai       AS TrangThaiPhong    -- trạng thái thực tế của phòng
        FROM chitietgiaodich ct
        INNER JOIN phong p ON ct.MaPhong = p.MaPhong
        WHERE ct.MaGiaoDich = ?
        ORDER BY p.SoPhong
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param('i', $maGiaoDich);
    $stmt->execute();
    $rs = $stmt->get_result();

    $data = [];
    while ($row = $rs->fetch_assoc()) {
        // ƯU TIÊN: trạng thái chi tiết, nếu null thì lấy trạng thái phòng
        $status = $row['TrangThaiCT'] ?? $row['TrangThaiPhong'] ?? '';
        $row['TrangThai'] = $status; // ĐẢM BẢO luôn có key TrangThai để view/controller dùng

        $data[] = $row;
    }
    return $data;
}
    /**
     * Hủy một số phòng trong giao dịch
     */
    public function cancelByPhong($maGiaoDich, array $listMaPhong, ?string $statusCancelled = null)
    {
        if (empty($listMaPhong)) {
            return false;
        }

        $maGiaoDich      = (int)$maGiaoDich;
        $statusCancelled = $statusCancelled ?? self::STATUS_CANCELLED;

        $ids = array_map('intval', $listMaPhong);
        $in  = implode(',', $ids);

        $statusEscaped = $this->db->real_escape_string($statusCancelled);

        $sql = "
            UPDATE chitietgiaodich
            SET TrangThai = '{$statusEscaped}'
            WHERE MaGiaoDich = {$maGiaoDich}
              AND MaPhong IN ({$in})
        ";

        return $this->db->query($sql);
    }

    /**
     * Cập nhật chi tiết đặt phòng (đổi phòng, đổi ngày, đổi số người)
     */
    public function updateBooking($maGiaoDich, $maPhongCu, array $data)
    {
        $sql = "
            UPDATE chitietgiaodich
            SET MaPhong        = ?, 
                SoNguoi        = ?, 
                NgayNhanDuKien = ?, 
                NgayTraDuKien  = ?
            WHERE MaGiaoDich   = ? 
              AND MaPhong      = ?
        ";

        $stmt     = $this->db->prepare($sql);
        $maGD     = (int)$maGiaoDich;
        $maPhongC = (int)$maPhongCu;

        $stmt->bind_param(
            'iissii',
            $data['MaPhong'],
            $data['SoNguoi'],
            $data['NgayNhanDuKien'],
            $data['NgayTraDuKien'],
            $maGD,
            $maPhongC
        );

        return $stmt->execute();
    }

    /**
     * Lấy danh sách DỊCH VỤ theo giao dịch
     *  -> dùng cho màn Check-out, Đặt dịch vụ
     */
    public function getByGiaoDich(int $maGiaoDich): array
    {
        // JOIN để lấy luôn tên dịch vụ + số phòng nếu có
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
        $stmt->bind_param("i", $maGiaoDich);
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
    public function createDetail(array $data)
    {
        $sql = "
            INSERT INTO chitietgiaodich
                (MaGiaoDich, MaPhong, NgayNhanDuKien, NgayTraDuKien,
                 SoNguoi, ThanhTien, TrangThai)
            VALUES
                (?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        $maGD   = (int)$data['MaGiaoDich'];
        $maPhong= (int)$data['MaPhong'];
        $nhan   = $data['NgayNhanDuKien'];
        $tra    = $data['NgayTraDuKien'];
        $soNguoi= (int)$data['SoNguoi'];
        $tt     = (float)$data['ThanhTien'];
        $status = $data['TrangThai'];

        $stmt->bind_param(
            "iissids",
            $maGD,
            $maPhong,
            $nhan,
            $tra,
            $soNguoi,
            $tt,
            $status
        );

        return $stmt->execute();
    }

    public function createDetail1(array $data)
    {
         $sql = "
            INSERT INTO chitietgiaodich
                (MaGiaoDich, MaPhong, NgayNhanDuKien, NgayTraDuKien,
                SoNguoi, DonGia, ThanhTien, TrangThai, GhiChu,
                TenKhach, CCCD, SDT, Email)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            die("Chuẩn bị SQL thất bại: " . $this->db->error);
        }

        $stmt->bind_param(
            "iissiddssssss",
            $data['MaGiaoDich'],
            $data['MaPhong'],
            $data['NgayNhanDuKien'],
            $data['NgayTraDuKien'],
            $data['SoNguoi'],
            $data['DonGia'],
            $data['ThanhTien'],
            $data['TrangThai'],
            $data['GhiChu'],
            $data['TenKhach'],
            $data['CCCD'],
            $data['SDT'],
            $data['Email']
        );

        if (!$stmt->execute()) {
            die("Lỗi thực thi INSERT: " . $stmt->error);
        }

        return true;
    }

    public function create(array $data)
    {
        $sql = "
            INSERT INTO chitietgiaodich
                (MaGiaoDich, MaPhong, NgayNhanDuKien, NgayTraDuKien,
                 SoNguoi, ThanhTien, TrangThai, TenKhach, CCCD)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->bind_param(
            "iissidsss",
            $data['MaGiaoDich'],
            $data['MaPhong'],
            $data['NgayNhanDuKien'],
            $data['NgayTraDuKien'],
            $data['SoNguoi'],
            $data['ThanhTien'],
            $data['TrangThai'],
            $data['TenKhach'],
            $data['CCCD']
        );

        return $stmt->execute();
    }
}
