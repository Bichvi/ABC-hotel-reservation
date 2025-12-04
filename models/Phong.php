<?php

/**
 * Model Phong
 * Làm việc với bảng `phong`
 */
class Phong
{
    /**
     * @var mysqli
     */
    private $db;

    public function __construct()
    {
        // Giả sử Database::getConnection() trả về đối tượng mysqli
        $this->db = Database::getConnection();
    }

    /**
     * Lấy danh sách tất cả phòng đang Trống
     * Dùng cho các chức năng tham khảo nhanh.
     *
     * @return mysqli_result|false
     */
    public function searchAvailable()
    {
        $sql = "SELECT * FROM phong WHERE TrangThai = 'Trong'";
        return $this->db->query($sql);
    }

    /**
     * Tìm phòng trống theo yêu cầu:
     * - Trạng thái: 'Trong'
     * - Sức chứa >= số khách
     * (Nếu bạn có logic kiểm tra trùng lịch theo giao dịch thì bổ sung sau)
     *
     * @param string $ngayDen  (Y-m-d)
     * @param string $ngayDi   (Y-m-d)
     * @param int    $soNguoi
     * @return mysqli_result|false
     */
    public function searchAvailableByRequest($ngayDen, $ngayDi, $soNguoi)
    {
        $sql = "
            SELECT *
            FROM phong
            WHERE TrangThai = 'Trong'
              AND SoKhachToiDa >= ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $soNguoi);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Lấy thông tin nhiều phòng theo mảng id
     * Trả về dạng mảng [MaPhong => row]
     *
     * @param int[] $ids
     * @return array
     */
    public function getByIds(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        // Loại bỏ trùng & ép int
        $ids = array_values(array_unique(array_map('intval', $ids)));

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types        = str_repeat('i', count($ids));

        $sql = "SELECT * FROM phong WHERE MaPhong IN ($placeholders)";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $rs = $stmt->get_result();

        $data = [];
        while ($row = $rs->fetch_assoc()) {
            $data[(int)$row['MaPhong']] = $row;
        }
        return $data;
    }

    /**
     * Cập nhật trạng thái của 1 phòng
     *
     * @param int    $maPhong
     * @param string $trangThai  (VD: 'Trong', 'Booked', 'Stayed', 'BaoTri'...)
     * @return bool
     */
    public function updateTrangThai($maPhong, $trangThai)
    {
        $sql = "UPDATE phong SET TrangThai = ? WHERE MaPhong = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $trangThai, $maPhong);
        return $stmt->execute();
    }

    public function updateTinhTrangPhong(int $maPhong, string $tinhTrang): bool
    {
        $sql = "UPDATE phong SET TinhTrangPhong = ? WHERE MaPhong = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $tinhTrang, $maPhong);
        return $stmt->execute();
    }

    /**
     * Lấy chi tiết 1 phòng (bao gồm TinhTrangPhong)
     */
    public function getById(int $maPhong): ?array
    {
        $sql = "SELECT * FROM phong WHERE MaPhong = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $maPhong);
        $stmt->execute();
        $rs = $stmt->get_result();
        return $rs->fetch_assoc() ?: null;
    }

    /**
     * (CŨ) Tìm phòng theo khoảng ngày + filter
     * -> dùng cho các actor khác (Lễ tân...), giữ nguyên.
     */
    public function findAvailableForRange($ngayNhan, $ngayTra, array $filters = [])
    {
        $sql = "
            SELECT p.*
            FROM phong p
            WHERE 1=1
              AND p.TrangThai IN ('Trong', 'Available')
              AND NOT EXISTS (
                    SELECT 1
                    FROM chitietgiaodich ct
                    JOIN giaodich gd ON gd.MaGiaoDich = ct.MaGiaoDich
                    WHERE ct.MaPhong = p.MaPhong
                      AND gd.TrangThai IN ('Moi', 'Booked', 'Stayed', 'Paid')
                      AND NOT (
                            ct.NgayTraDuKien   <= ? 
                        OR  ct.NgayNhanDuKien >= ?
                      )
              )
        ";

        $params = [];
        $types  = "";

        $params[] = $ngayNhan;
        $params[] = $ngayTra;
        $types   .= "ss";

        if (!empty($filters['hang_phong'])) {
            $sql      .= " AND p.HangPhong = ? ";
            $params[]  = $filters['hang_phong'];
            $types    .= "s";
        }

        if (!empty($filters['so_giuong'])) {
            $sql      .= " AND p.KieuGiuong = ? ";
            $params[]  = $filters['so_giuong'];
            $types    .= "s";
        }

        if (!empty($filters['tang'])) {
            $sql      .= " AND p.Tang = ? ";
            $params[]  = (int)$filters['tang'];
            $types    .= "i";
        }

        $sql .= " ORDER BY p.Tang, p.SoPhong ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \Exception("Lỗi prepare findAvailableForRange: " . $this->db->error);
        }

        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            throw new \Exception("Lỗi execute findAvailableForRange: " . $stmt->error);
        }

        $rs = $stmt->get_result();
        $data = [];
        while ($row = $rs->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * (MỚI) Tìm phòng cho ĐẶT PHÒNG ONLINE của khách hàng
     * Lọc theo:
     *  - TrangThai = 'Trong'
     *  - LoaiPhong (tùy chọn)
     *  - Giá từ / đến
     *  - Số khách tối thiểu (SoKhachToiDa >= ?)
     * KHÔNG dùng ngày nhận / ngày trả để lọc.
     *
     * @param array $filters
     * @return array
     */
    public function searchForOnline(array $filters = []): array
    {
        $sql = "SELECT * FROM phong WHERE TrangThai = 'Trong'";
        $params = [];
        $types  = "";

        // Loại phòng
        if (!empty($filters['loai_phong'])) {
            $sql      .= " AND LoaiPhong = ? ";
            $params[]  = $filters['loai_phong'];
            $types    .= "s";
        }

        // Giá tối thiểu
        if (isset($filters['gia_min']) && $filters['gia_min'] !== '' && $filters['gia_min'] !== null) {
            $sql      .= " AND Gia >= ? ";
            $params[]  = (float)$filters['gia_min'];
            $types    .= "d";
        }

        // Giá tối đa
        if (isset($filters['gia_max']) && $filters['gia_max'] !== '' && $filters['gia_max'] !== null) {
            $sql      .= " AND Gia <= ? ";
            $params[]  = (float)$filters['gia_max'];
            $types    .= "d";
        }

        // Số khách (sức chứa)
        if (!empty($filters['so_khach'])) {
            $sql      .= " AND SoKhachToiDa >= ? ";
            $params[]  = (int)$filters['so_khach'];
            $types    .= "i";
        }

        $sql .= " ORDER BY Gia ASC, SoPhong ASC";

        if (empty($params)) {
            $rs = $this->db->query($sql);
            $data = [];
            if ($rs) {
                while ($row = $rs->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            return $data;
        }

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            return [];
        }

        $rs = $stmt->get_result();
        $data = [];
        while ($row = $rs->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
     public function searchForOnlineBooking(array $filters = []): array
    {
        $sql = "
            SELECT 
                MaPhong,
                SoPhong,
                LoaiPhong,
                DienTich,
                LoaiGiuong,
                ViewPhong,
                Gia,
                SoKhachToiDa,
                TinhTrangPhong,
                HinhAnh
            FROM phong
            WHERE TrangThai = 'Trong'
        ";

        $types  = "";
        $params = [];

        // Lọc theo loại phòng (Standard, Deluxe, Superior, Suite, VIP)
        if (!empty($filters['loai_phong'])) {
            $sql     .= " AND LoaiPhong = ? ";
            $types   .= "s";
            $params[] = $filters['loai_phong'];
        }

        // Lọc theo view (Biển / Thành phố / Vườn...)
        if (!empty($filters['view_phong'])) {
            $sql     .= " AND ViewPhong = ? ";
            $types   .= "s";
            $params[] = $filters['view_phong'];
        }

        // Lọc theo số khách tối thiểu
        if (!empty($filters['so_khach']) && (int)$filters['so_khach'] > 0) {
            $sql     .= " AND SoKhachToiDa >= ? ";
            $types   .= "i";
            $params[] = (int)$filters['so_khach'];
        }

        // Lọc theo giá xung quanh "giá gợi ý"
        if (!empty($filters['gia_goi_y']) && (float)$filters['gia_goi_y'] > 0) {
            $g = (float)$filters['gia_goi_y'];
            // Ví dụ ±20% quanh giá gợi ý
            $minGia = $g * 0.8;
            $maxGia = $g * 1.2;

            $sql     .= " AND Gia BETWEEN ? AND ? ";
            $types   .= "dd";
            $params[] = $minGia;
            $params[] = $maxGia;
        }

        $sql .= " ORDER BY Gia ASC, SoPhong ASC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \Exception("Lỗi prepare searchForOnlineBooking: " . $this->db->error);
        }

        if ($types !== "") {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new \Exception("Lỗi execute searchForOnlineBooking: " . $stmt->error);
        }

        $rs = $stmt->get_result();

        $rooms = [];
        while ($row = $rs->fetch_assoc()) {
            $rooms[] = $row;
        }

        return $rooms;
    }

    /**
     * Lấy thông tin nhiều phòng theo mảng id
     * Trả về dạng mảng [MaPhong => row]
     *
     * @param int[] $ids
     * @return array
     */
     public function searchByFilter(
        ?float $giaMongMuon = null,
        ?int   $soKhach = null,
        string $loaiPhong = '',
        string $viewPhong = ''
    ): array {
        $sql = "
            SELECT *
            FROM phong
            WHERE TrangThai = 'Trong'
        ";

        $params = [];
        $types  = "";

        // Lọc theo khoảng giá gần với giá mong muốn
        if (!is_null($giaMongMuon) && $giaMongMuon > 0) {
            // +/- 25% hoặc tối thiểu 200.000
            $delta   = max(200000, $giaMongMuon * 0.25);
            $minGia  = max(0, $giaMongMuon - $delta);
            $maxGia  = $giaMongMuon + $delta;

            $sql      .= " AND Gia BETWEEN ? AND ? ";
            $types    .= "dd";
            $params[]  = $minGia;
            $params[]  = $maxGia;
        }

        // Lọc theo số khách
        if (!is_null($soKhach) && $soKhach > 0) {
            $sql      .= " AND SoKhachToiDa >= ? ";
            $types    .= "i";
            $params[]  = $soKhach;
        }

        // Lọc theo loại phòng
        if ($loaiPhong !== '') {
            $sql      .= " AND LoaiPhong = ? ";
            $types    .= "s";
            $params[]  = $loaiPhong;
        }

        // Lọc theo view phòng (Biển / Thành phố / Vườn ...)
        if ($viewPhong !== '') {
            $sql      .= " AND ViewPhong = ? ";
            $types    .= "s";
            $params[]  = $viewPhong;
        }

        // Sắp xếp: giá tăng dần, rồi sức chứa, rồi số phòng
        $sql .= " ORDER BY Gia ASC, SoKhachToiDa ASC, SoPhong ASC ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \Exception("Lỗi prepare searchByFilter: " . $this->db->error);
        }

        if ($types !== "") {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new \Exception("Lỗi execute searchByFilter: " . $stmt->error);
        }

        $rs   = $stmt->get_result();
        $data = [];

        while ($row = $rs->fetch_assoc()) {
            // Ép kiểu giá về float cho dễ xử lý
            $row['Gia'] = (float)$row['Gia'];
            $data[]     = $row;
        }

        return $data;
    }

    /**
     * Hàm gợi ý khoảng giá (để hiển thị text trên giao diện nếu muốn)
     * Ví dụ: nhập 1.000.000 → trả về [min=800.000, max=1.200.000]
     */
    public function suggestPriceRange(?float $giaMongMuon): ?array
    {
        if (is_null($giaMongMuon) || $giaMongMuon <= 0) {
            return null;
        }

        $delta   = max(200000, $giaMongMuon * 0.25);
        $minGia  = max(0, $giaMongMuon - $delta);
        $maxGia  = $giaMongMuon + $delta;

        return [
            'min' => $minGia,
            'max' => $maxGia,
        ];
    }

    public function searchByFilter1(
        ?float $giaMongMuon = null,
        ?int   $soKhach = null,
        string $loaiPhong = '',
        string $viewPhong = ''
    ): array {

        // lấy kết nối DB đúng chuẩn
        $conn = Database::getConnection();

        $sql = "
            SELECT *
            FROM phong
            WHERE TrangThai = 'Trong'
        ";

        $params = [];
        $types  = "";

        // Giá gần đúng ±25%
        if (!is_null($giaMongMuon) && $giaMongMuon > 0) {
            $delta   = max(200000, $giaMongMuon * 0.25);
            $minGia  = max(0, $giaMongMuon - $delta);
            $maxGia  = $giaMongMuon + $delta;

            $sql      .= " AND Gia BETWEEN ? AND ? ";
            $types    .= "dd";
            $params[]  = $minGia;
            $params[]  = $maxGia;
        }

        // Số khách
        if (!is_null($soKhach) && $soKhach > 0) {
            $sql      .= " AND SoKhachToiDa >= ? ";
            $types    .= "i";
            $params[]  = $soKhach;
        }

        // Loại phòng
        if ($loaiPhong !== '') {
            $sql      .= " AND LoaiPhong LIKE CONCAT('%', ?, '%') ";
            $types    .= "s";
            $params[]  = $loaiPhong;
        }

        // View phòng
        if ($viewPhong !== '') {
            $sql      .= " AND ViewPhong LIKE CONCAT('%', ?, '%') ";
            $types    .= "s";
            $params[]  = $viewPhong;
        }

        $stmt = $conn->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAll() {
        $sql = "SELECT * FROM phong WHERE TrangThai = 'Trong'";
        $rs = $this->db->query($sql);

        $data = [];
        while ($row = $rs->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
}