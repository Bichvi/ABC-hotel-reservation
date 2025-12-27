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
        $sql = "INSERT INTO chitietgiaodich 
            (MaGiaoDich, MaPhong, NgayNhanDuKien, NgayTraDuKien,
             SoNguoi, DonGia, ThanhTien, TrangThai, GhiChu,
             TenKhach, CCCD, SDT, Email, MaKhuyenMai)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        // 14 dấu ? → 14 biến → 14 ký tự types
        // i = int, d = double(float), s = string
        $stmt->bind_param(
            "iissiddssssssi",
            $data['MaGiaoDich'],     // i
            $data['MaPhong'],        // i
            $data['NgayNhanDuKien'], // s
            $data['NgayTraDuKien'],  // s
            $data['SoNguoi'],        // i
            $data['DonGia'],         // d
            $data['ThanhTien'],      // d
            $data['TrangThai'],      // ✅ s (Booked)
            $data['GhiChu'],         // s
            $data['TenKhach'],       // s
            $data['CCCD'],           // s
            $data['SDT'],            // s
            $data['Email'],          // s
            $data['MaKhuyenMai']     // i
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
    public function co_listRoomsForCheckout(int $maGiaoDich): array
{
    $sql = "
        SELECT 
            ct.*,
            p.SoPhong,
            p.LoaiPhong,
            p.TinhTrangPhong
        FROM chitietgiaodich ct
        INNER JOIN phong p ON p.MaPhong = ct.MaPhong
        WHERE ct.MaGiaoDich = ?
          AND ct.TrangThai = 'Stayed'
        ORDER BY p.SoPhong
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $maGiaoDich);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function co_checkoutRoom(int $maGD, int $maPhong): bool
{
    // 1) cập nhật trạng thái trong chitietgiaodich
    $sql1 = "
        UPDATE chitietgiaodich
        SET TrangThai = 'CheckedOut'
        WHERE MaGiaoDich = ?
          AND MaPhong = ?
          AND TrangThai = 'Stayed'
    ";

    $stmt1 = $this->db->prepare($sql1);
    $stmt1->bind_param("ii", $maGD, $maPhong);
    if (!$stmt1->execute()) return false;

    // 2) cập nhật trạng thái phòng thật
    $sql2 = "UPDATE phong SET TrangThai = 'Trong' WHERE MaPhong = ?";

    $stmt2 = $this->db->prepare($sql2);
    $stmt2->bind_param("i", $maPhong);
    return $stmt2->execute();
}
public function co_getStayedRoomsByGiaoDich(int $maGD): array
{
    $sql = "
        SELECT 
            ct.MaGiaoDich,
            ct.MaPhong,
            ct.SoNguoi,
            ct.NgayNhanDuKien,
            ct.NgayTraDuKien,
            ct.ThanhTien,
            p.SoPhong,
            p.LoaiPhong,
            p.TrangThai AS TrangThaiPhong
        FROM chitietgiaodich ct
        INNER JOIN phong p ON p.MaPhong = ct.MaPhong
        WHERE ct.MaGiaoDich = ?
          AND p.TrangThai = 'Stayed'
        ORDER BY p.SoPhong
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $maGD);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function co_getRoomDetail(int $maGD, int $maPhong): ?array
{
    $sql = "
        SELECT 
            ct.*,
            p.SoPhong,
            p.LoaiPhong,
            p.TinhTrangPhong,
            p.TrangThai AS TrangThaiPhong
        FROM chitietgiaodich ct
        INNER JOIN phong p ON p.MaPhong = ct.MaPhong
        WHERE ct.MaGiaoDich = ?
          AND ct.MaPhong = ?
        LIMIT 1
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("ii", $maGD, $maPhong);
    $stmt->execute();
    $rs = $stmt->get_result();
    return $rs->fetch_assoc() ?: null;
}
    /* ============================================================
     *  V2 – THÊM CHI TIẾT GIAO DỊCH PHÒNG (ĐẶT PHÒNG TRỰC TIẾP)
     *  - Lưu TenKhach, CCCD, SDT, Email vào bảng
     *  - ThanhTien = DonGia * SoDem + PhuThu + BoiThuong
     *  - Có thể ghi chú số đêm để dễ xem
     * ============================================================
     */

    /**
     * @param array $data [
     *   'MaGiaoDich',
     *   'MaPhong',
     *   'SoNguoi',
     *   'NgayNhanDuKien' (Y-m-d H:i:s),
     *   'NgayTraDuKien'  (Y-m-d H:i:s),
     *   'DonGia',
     *   'SoDem',
     *   'TienPhuThu',
     *   'TienBoiThuong',
     *   'TrangThai'  (Booked / Stayed ...),
     *   'TenKhach',
     *   'CCCD',
     *   'SDT',
     *   'Email',
     *   'GhiChu'
     * ]
     */
    public function v2_addRoomBookingWithGuest(array $data): bool
    {
        $maGD   = (int)$data['MaGiaoDich'];
        $maPhong= (int)$data['MaPhong'];
        $soNguoi= (int)($data['SoNguoi'] ?? 1);

        $ngayNhan = $data['NgayNhanDuKien'];
        $ngayTra  = $data['NgayTraDuKien'];

        $donGia   = (float)($data['DonGia'] ?? 0);
        $soDem    = (int)($data['SoDem'] ?? 1);
        if ($soDem <= 0) $soDem = 1;

        $phuThu   = (float)($data['TienPhuThu']     ?? 0);
        $boiThuong= (float)($data['TienBoiThuong']  ?? 0);

        $thanhTien = $donGia * $soDem + $phuThu + $boiThuong;

        $trangThai = $data['TrangThai'] ?? 'Booked';

        $tenKhach  = $data['TenKhach'] ?? null;
        $cccd      = $data['CCCD']     ?? null;
        $sdt       = $data['SDT']      ?? null;
        $email     = $data['Email']    ?? null;

        $ghiChu    = $data['GhiChu']   ?? '';
        $ghiChu    = trim($ghiChu . " | SoDem: {$soDem}");

        $sql = "
            INSERT INTO chitietgiaodich
                (MaGiaoDich, MaPhong, SoNguoi,
                 NgayNhanDuKien, NgayTraDuKien,
                 DonGia, ThanhTien, TienPhuThu, TienBoiThuong,
                 TrangThai, GhiChu,
                 TenKhach, CCCD, SDT, Email)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("ChiTietGiaoDich::v2_addRoomBookingWithGuest - prepare fail: " . $this->db->error);
        }

        $stmt->bind_param(
            "iiissddddsssss",
            $maGD,
            $maPhong,
            $soNguoi,
            $ngayNhan,
            $ngayTra,
            $donGia,
            $thanhTien,
            $phuThu,
            $boiThuong,
            $trangThai,
            $ghiChu,
            $tenKhach,
            $cccd,
            $sdt,
            $email
        );

        return $stmt->execute();
    }
    /**
 * V2 – Thêm chi tiết giao dịch phòng,
 *  lưu TenKhach, CCCD, SDT, Email, SoNgay (thông qua ThanhTien).
 */

/**
 * V2 – Thêm dịch vụ gắn với phòng khi đặt phòng trực tiếp.
 *  Chèn vào bảng chitietdichvu.
 */
public function addServiceV2(array $data): bool
{
    $maGD    = (int)$data['MaGiaoDich'];
    $maPhong = (int)$data['MaPhong'];
    $maDV    = (int)$data['MaDichVu'];
    $soLuong = (int)($data['SoLuong'] ?? 1);
    if ($soLuong <= 0) $soLuong = 1;

    $giaBan     = (float)($data['GiaBan']     ?? 0);
    $thanhTien  = (float)($data['ThanhTien']  ?? $giaBan * $soLuong);
    $ghiChu     = $data['GhiChu']             ?? 'Đặt dịch vụ khi đặt phòng trực tiếp V2';

    $sql = "
        INSERT INTO chitietdichvu
            (MaGiaoDich, MaPhong, MaDichVu,
             SoLuong, GiaBan, ThanhTien, GhiChu)
        VALUES
            (?, ?, ?, ?, ?, ?, ?)
    ";

    if (!$stmt = $this->db->prepare($sql)) {
        error_log("ChiTietGiaoDich::addServiceV2 prepare fail: " . $this->db->error);
        return false;
    }

    $stmt->bind_param(
        "iiiidds",
        $maGD,
        $maPhong,
        $maDV,
        $soLuong,
        $giaBan,
        $thanhTien,
        $ghiChu
    );

    if (!$stmt->execute()) {
        error_log("ChiTietGiaoDich::addServiceV2 execute fail: " . $stmt->error);
        return false;
    }

    return true;
}
/**
 * V2 – thêm phòng vào giao dịch
 */
public function v2_addRoom(int $maGD, int $maPhong, float $gia, float $thanhTien): bool
{
    $sql = "
        INSERT INTO chitietgiaodich (MaGiaoDich, MaPhong, DonGia, ThanhTien)
        VALUES (?, ?, ?, ?)
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("iidd", $maGD, $maPhong, $gia, $thanhTien);
    return $stmt->execute();
}
/**
 * Cập nhật thông tin khách cho PHÒNG trong giao dịch
 * - Dùng khi thêm thành viên vào phòng
 */
public function updateGuestForRoom(int $maGD, int $maPhong, array $data): bool
{
    $sql = "
        UPDATE chitietgiaodich
        SET 
            TenKhach = ?,
            CCCD     = ?,
            SDT      = ?,
            Email    = ?
        WHERE MaGiaoDich = ?
          AND MaPhong = ?
        LIMIT 1
    ";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) {
        error_log("ChiTietGiaoDich::updateGuestForRoom prepare fail: " . $this->db->error);
        return false;
    }

    $ten   = $data['TenKhach'] ?? null;
    $cccd  = $data['CCCD']     ?? null;
    $sdt   = $data['SDT']      ?? null;
    $email = $data['Email']    ?? null;

    $stmt->bind_param(
        "ssssii",
        $ten,
        $cccd,
        $sdt,
        $email,
        $maGD,
        $maPhong
    );

    if (!$stmt->execute()) {
        error_log("ChiTietGiaoDich::updateGuestForRoom execute fail: " . $stmt->error);
        return false;
    }

    return true;
}
public function addRoomBookingV2(array $data): bool
{
    $sql = "
        INSERT INTO chitietgiaodich
        (MaGiaoDich, MaPhong, SoNguoi,
         NgayNhanDuKien, NgayTraDuKien,
         DonGia, ThanhTien,
         TienPhuThu, TienBoiThuong,
         TrangThai, GhiChu,
         TenKhach, CCCD, SDT, Email)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->bind_param(
        "iiissdddsssssss",
        $data['MaGiaoDich'],
        $data['MaPhong'],
        $data['SoNguoi'],
        $data['NgayNhanDuKien'],
        $data['NgayTraDuKien'],
        $data['DonGia'],
        $data['ThanhTien'],
        $data['TienPhuThu'],
        $data['TienBoiThuong'],
        $data['TrangThai'],
        $data['GhiChu'],
        $data['TenKhach'],
        $data['CCCD'],
        $data['SDT'],
        $data['Email']
    );

    return $stmt->execute();
}

    public function updateThanhTien($maGD, $thanhTien)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE chitietgiaodich
            SET ThanhTien = ?
            WHERE MaGiaoDich = ?
        ");
        $stmt->bind_param("di", $thanhTien, $maGD);
        $stmt->execute();
    }
   public function tinhTienPhong(int $maGiaoDich): float
{
    $sql = "
        SELECT 
            ct.NgayNhanDuKien,
            ct.NgayTraDuKien,
            p.Gia
        FROM chitietgiaodich ct
        INNER JOIN phong p ON p.MaPhong = ct.MaPhong
        WHERE ct.MaGiaoDich = ?
          AND ct.TrangThai IN ('Booked','CheckedIn','Stayed')
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $maGiaoDich);
    $stmt->execute();
    $rs = $stmt->get_result();

    $tongTienPhong = 0;

    while ($row = $rs->fetch_assoc()) {
        $ngayNhan = strtotime($row['NgayNhanDuKien']);
        $ngayTra  = strtotime($row['NgayTraDuKien']);

        $soDem = max(1, ceil(($ngayTra - $ngayNhan) / 86400));
        $tongTienPhong += $soDem * (float)$row['Gia'];
    }

    return $tongTienPhong;
}
public function updateBooking(int $maGiaoDich, int $maPhongCu, array $data): bool
{
    $maPhong   = (int)$data['MaPhong'];
    $soNguoi   = (int)$data['SoNguoi'];
    $ngayNhan  = (string)$data['NgayNhanDuKien'];
    $ngayTra   = (string)$data['NgayTraDuKien'];

    $donGia    = (float)$data['DonGia'];
    $thanhTien = (float)$data['ThanhTien'];

    $tenKhach = (string)$data['TenKhach'];
    $cccd     = (string)$data['CCCD'];
    $sdt      = (string)$data['SDT'];
    $email    = (string)$data['Email'];

    $maGD = (int)$maGiaoDich;
    $maPC = (int)$maPhongCu;

    $sql = "
        UPDATE chitietgiaodich
        SET
            MaPhong        = ?,
            SoNguoi        = ?,
            NgayNhanDuKien = ?,
            NgayTraDuKien  = ?,
            DonGia         = ?,
            ThanhTien      = ?,
            TenKhach       = ?,
            CCCD           = ?,
            SDT            = ?,
            Email          = ?
        WHERE MaGiaoDich = ?
          AND MaPhong    = ?
        LIMIT 1
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->bind_param(
        "iissddssssii",
        $maPhong,
        $soNguoi,
        $ngayNhan,
        $ngayTra,
        $donGia,
        $thanhTien,
        $tenKhach,
        $cccd,
        $sdt,
        $email,
        $maGD,
        $maPC
    );

    $stmt->execute();
    return true;
}
public function updateThanhTienByCTGD(int $maCTGD, float $thanhTien): bool
{
    $stmt = $this->db->prepare("
        UPDATE chitietgiaodich
        SET ThanhTien = ?
        WHERE MaChiTietGiaoDich = ?
        LIMIT 1
    ");
    $stmt->bind_param("di", $thanhTien, $maCTGD);
    return $stmt->execute();
}
// Lấy danh sách thành viên trong 1 phòng của giao dịch
}
