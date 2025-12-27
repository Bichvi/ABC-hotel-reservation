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

    // ÉP VỀ SỐ NGUYÊN
    $maGD      = (int)$data['MaGiaoDich'];
    $maPhong   = (int)$data['MaPhong'];
    $maDV      = (int)$data['MaDichVu'];
    $soLuong   = (int)$data['SoLuong'];
    $giaBan    = (int)$data['GiaBan'];
    $thanhTien = (int)$data['ThanhTien'];
    $ghiChu    = $data['GhiChu'] ?? "";

    // TẤT CẢ LÀ INT → SỬ DỤNG "iiiiiis"
    $stmt->bind_param(
        'iiiiiss',
        $maGD,
        $maPhong,
        $maDV,
        $soLuong,
        $giaBan,
        $thanhTien,
        $ghiChu
    );

    return $stmt->execute();
}
    public function co_getServicesByRoom(int $maGD, int $maPhong): array
{
    $sql = "
        SELECT 
            ctdv.*,
            dv.TenDichVu,
            dv.GiaDichVu,
            p.SoPhong
        FROM chitietdichvu ctdv
        LEFT JOIN dichvu dv ON dv.MaDichVu = ctdv.MaDichVu
        LEFT JOIN phong p ON p.MaPhong = ctdv.MaPhong
        WHERE ctdv.MaGiaoDich = ?
          AND ctdv.MaPhong = ?
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("ii", $maGD, $maPhong);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
    /* ============================================================
     *  V2 – THÊM DỊCH VỤ THEO PHÒNG
     *  - Bắt buộc truyền MaPhong (để biết phòng nào sử dụng DV)
     * ============================================================
     */

    /**
     * @param array $data [
     *   'MaGiaoDich',
     *   'MaPhong',
     *   'MaDichVu',
     *   'SoLuong',
     *   'GiaBan',
     *   'GhiChu'
     * ]
     */
    public function v2_addServiceForRoom(array $data): bool
    {
        $maGD    = (int)$data['MaGiaoDich'];
        $maPhong = (int)$data['MaPhong']; // bắt buộc
        $maDV    = (int)$data['MaDichVu'];

        $soLuong = (int)($data['SoLuong'] ?? 1);
        if ($soLuong <= 0) $soLuong = 1;

        $giaBan  = (float)($data['GiaBan'] ?? 0);
        $thanhTien = $giaBan * $soLuong;

        $ghiChu  = $data['GhiChu'] ?? '';

        $sql = "
            INSERT INTO chitietdichvu
                (MaGiaoDich, MaPhong, MaDichVu,
                 SoLuong, GiaBan, ThanhTien, GhiChu)
            VALUES
                (?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("ChiTietDichVu::v2_addServiceForRoom - prepare fail: " . $this->db->error);
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

        return $stmt->execute();
    }
    /**
 * V2 – Ghi dịch vụ theo phòng
 */
public function v2_addService(int $maGD, int $maDV, int $soLuong, int $maPhong): bool
{
    $sql = "
        INSERT INTO chitietdichvu (MaGiaoDich, MaDichVu, SoLuong, MaPhong)
        VALUES (?, ?, ?, ?)
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("iiii", $maGD, $maDV, $soLuong, $maPhong);
    return $stmt->execute();
}
public function syncFromForm(int $maGD, array $services, array $removeServices = [])
{
    $db = Database::getConnection();

    foreach ($services as $maDV => $soLuong) {

        $maDV    = (int)$maDV;
        $soLuong = (int)$soLuong;

        // ===== XÓA DỊCH VỤ =====
        if ($soLuong <= 0 || in_array($maDV, $removeServices, true)) {
            $stmt = $db->prepare("
                DELETE FROM chitietdichvu
                WHERE MaGiaoDich = ? AND MaDichVu = ?
            ");
            $stmt->bind_param("ii", $maGD, $maDV);
            $stmt->execute();
            continue;
        }

        // ===== KIỂM TRA ĐÃ TỒN TẠI CHƯA =====
        $stmt = $db->prepare("
            SELECT 1
            FROM chitietdichvu
            WHERE MaGiaoDich = ? AND MaDichVu = ?
            LIMIT 1
        ");
        $stmt->bind_param("ii", $maGD, $maDV);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;

        if ($exists) {
            // ===== UPDATE (KHÔNG CỘNG) =====
            $stmt = $db->prepare("
                UPDATE chitietdichvu
                SET SoLuong = ?
                WHERE MaGiaoDich = ? AND MaDichVu = ?
            ");
            $stmt->bind_param("iii", $soLuong, $maGD, $maDV);
            $stmt->execute();
        } else {
            // ===== INSERT MỚI =====
            $stmt = $db->prepare("
                INSERT INTO chitietdichvu (MaGiaoDich, MaDichVu, SoLuong)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("iii", $maGD, $maDV, $soLuong);
            $stmt->execute();
        }
    }
}
public function tinhTongDichVu(int $maGD): float
{
    $sql = "
        SELECT COALESCE(SUM(ctdv.SoLuong * dv.GiaDichVu), 0) AS Tong
        FROM chitietdichvu ctdv
        INNER JOIN dichvu dv ON dv.MaDichVu = ctdv.MaDichVu
        WHERE ctdv.MaGiaoDich = ?
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $maGD);
    $stmt->execute();

    return (float)$stmt->get_result()->fetch_assoc()['Tong'];
}
}