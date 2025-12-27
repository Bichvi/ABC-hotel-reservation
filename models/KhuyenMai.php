<?php

class KhuyenMai
{
    /** @var mysqli */
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /* ============================================================
       CÁC HÀM CŨ (GIỮ NGUYÊN, KHÔNG ĐỤNG TỚI)
    ============================================================ */

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

    public function getById(int $maKM): ?array
    {
        $sql = "SELECT * FROM khuyenmai WHERE MaKhuyenMai = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $maKM);
        $stmt->execute();
        $rs = $stmt->get_result();

        return $rs->fetch_assoc() ?: null;
    }

    public function getActive(): array
    {
        $sql = "
            SELECT *
            FROM khuyenmai
            WHERE TrangThai = 'DangApDung'
        ";

        $rs = $this->db->query($sql);
        $rows = [];

        if ($rs) {
            while ($r = $rs->fetch_assoc()) {
                $rows[] = $r;
            }
        }
        return $rows;
    }

    public function v2_calculateDiscountFixed(float $tongTruocGiam, ?int $maKM): array
    {
        if ($maKM === null || $maKM <= 0) {
            return [
                'giam' => 0.0,
                'tong_sau_giam' => $tongTruocGiam,
            ];
        }

        $km = $this->getById($maKM);
        if (!$km) {
            return [
                'giam' => 0.0,
                'tong_sau_giam' => $tongTruocGiam,
            ];
        }

        $mucUuDai = (float)$km['MucUuDai'];

        if ($mucUuDai <= 0) {
            return [
                'giam' => 0.0,
                'tong_sau_giam' => $tongTruocGiam,
            ];
        }

        $giam = min($mucUuDai, $tongTruocGiam);
        return [
            'giam' => $giam,
            'tong_sau_giam' => $tongTruocGiam - $giam,
        ];
    }

    public function calculateDiscountV2(int $maKM, float $tongTruocGiam): float
    {
        if ($maKM <= 0 || $tongTruocGiam <= 0) return 0.0;

        $sql = "SELECT * FROM khuyenmai WHERE MaKhuyenMai = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $maKM);
        $stmt->execute();

        $km = $stmt->get_result()->fetch_assoc();
        if (!$km) return 0.0;

        $mucUuDai = (float)$km['MucUuDai'];
        if ($mucUuDai <= 0) return 0.0;

        return min($mucUuDai, $tongTruocGiam);
    }

    public function v2_getActivePromotions(): array
    {
        $sql = "
            SELECT MaKhuyenMai, TenChuongTrinh, MaCode, MucUuDai, TrangThai
            FROM khuyenmai
            WHERE TrangThai IN ('HoatDong','DangApDung')
        ";

        $rs = $this->db->query($sql);
        $rows = [];

        if ($rs) {
            while ($r = $rs->fetch_assoc()) {
                $rows[] = $r;
            }
        }
        return $rows;
    }

    public function isValid(array $km): bool
    {
        if (!$km) return false;

        if (isset($km['TrangThai']) && $km['TrangThai'] == 'Inactive') {
            return false;
        }

        if (!empty($km['NgayBatDau']) && !empty($km['NgayKetThuc'])) {
            $today = date("Y-m-d");
            if ($today < $km['NgayBatDau'] || $today > $km['NgayKetThuc']) {
                return false;
            }
        }
        return true;
    }

    public function calcDiscount(int $totalAmount, array $km): int
    {
        if (!$km) return 0;

        $loai = $km['LoaiKM'];  // percent | fixed
        $giatri = (int)$km['GiaTri'];

        if ($loai === 'percent') {
            $discount = ($totalAmount * $giatri) / 100;
        } else {
            $discount = $giatri;
        }

        if ($discount < 0) $discount = 0;
        if ($discount > $totalAmount) $discount = $totalAmount;

        return (int)$discount;
    }


    /* ============================================================
       HÀM MỚI — DÙNG CHO CHECK-OUT V2 (AN TOÀN, KHÔNG ĐỤNG HÀM CŨ)
    ============================================================ */

    /**
     * Lấy khuyến mãi không phân biệt trạng thái
     * (dùng cho UC check-out khi mã KM đã gắn với giao dịch)
     */
    public function getById_Any(int $maKM): ?array
    {
        $sql = "SELECT * FROM khuyenmai WHERE MaKhuyenMai = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $maKM);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /**
     * Tính giảm giá theo MaKhuyenMai
     * - Hỗ trợ: % hoặc cố định
     */
    public function calculateDiscountById(int $maKM, float $tongTien): float
    {
        if ($maKM <= 0 || $tongTien <= 0) return 0;

        $km = $this->getById_Any($maKM);
        if (!$km) return 0;

        $value = (float)$km['MucUuDai'];
        $type  = $km['LoaiUuDai'] ?? 'PERCENT';

        if ($value <= 0) return 0;

        if ($type === 'PERCENT') {
            $giam = ($tongTien * $value) / 100;
        } else {
            $giam = $value;
        }

        return min($giam, $tongTien);
    }
       public function tinhGiamGia($maKM, $tongTien)
    {
        $km = $this->getById($maKM);
        if (!$km) return 0;

        // GiaTri < 100 => %
        if ($km['GiaTri'] < 100) {
            return $tongTien * ($km['GiaTri'] / 100);
        }

        // ngược lại là số tiền cố định
        return min($km['GiaTri'], $tongTien);
    }
    public function getAllActive()
{
    $db = Database::getConnection();
    return $db->query("
        SELECT *
        FROM khuyenmai
        WHERE TrangThai = 'HoatDong'
          AND (NgayKetThuc IS NULL OR NgayKetThuc >= CURDATE())
    ")->fetch_all(MYSQLI_ASSOC);
}





    // ... (viiii========quản lí khuyến mãi dành cho CSKH) ==================)

//A. THÊM KHUYẾN MÃI MỚI
    
    /* ====================================================
     * PHẦN 1: QUẢN LÝ MÃ KHUYẾN MÃI (Tạo, Sửa, Check)
     * ==================================================== */

    
    // 1. Kiểm tra Trùng Tên Chương Trình
    public function checkTenExists_taoKM_CSKH($ten, $excludeId = 0) {
        $sql = "SELECT COUNT(*) as count FROM khuyenmai WHERE TenChuongTrinh = ? AND MaKhuyenMai != ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $ten, $excludeId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'] > 0;
    }

    // 2. Kiểm tra Trùng thời gian (Logic TC5)
    public function checkOverlap_taoKM_CSKH($doiTuong, $ngayBD, $ngayKT, $excludeId = 0) {
        // Kiểm tra nếu đối tượng = "Tất cả KH" thì cần check trùng với mọi đối tượng
        // Nếu đối tượng khác thì check trùng với "Tất cả KH" + chính đối tượng đó
        
        if ($doiTuong === 'Tất cả KH') {
            // Nếu tạo/sửa CTKM cho "Tất cả KH" -> check trùng với MỌI chương trình
            $sql = "SELECT COUNT(*) as count FROM khuyenmai 
                    WHERE MaKhuyenMai != ? 
                    AND TrangThai != 'HetHan'
                    AND (
                        (NgayBatDau <= ? AND NgayKetThuc >= ?) OR 
                        (NgayBatDau <= ? AND NgayKetThuc >= ?) OR 
                        (NgayBatDau >= ? AND NgayKetThuc <= ?)
                    )";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("issssss", $excludeId, $ngayKT, $ngayBD, $ngayKT, $ngayBD, $ngayBD, $ngayKT);
        } else {
            // Nếu tạo/sửa CTKM cho "Khách lẻ" hoặc "Đoàn khách" 
            // -> check trùng với "Tất cả KH" HOẶC chính đối tượng đó
            $sql = "SELECT COUNT(*) as count FROM khuyenmai 
                    WHERE (DoiTuong = ? OR DoiTuong = 'Tất cả KH')
                    AND MaKhuyenMai != ? 
                    AND TrangThai != 'HetHan'
                    AND (
                        (NgayBatDau <= ? AND NgayKetThuc >= ?) OR 
                        (NgayBatDau <= ? AND NgayKetThuc >= ?) OR 
                        (NgayBatDau >= ? AND NgayKetThuc <= ?)
                    )";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("sissssss", $doiTuong, $excludeId, $ngayKT, $ngayBD, $ngayKT, $ngayBD, $ngayBD, $ngayKT);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'] > 0;
    }

    /* ====================================================
     * NHÓM HÀM THỰC THI (TẠO, SỬA, LẤY)
     * ==================================================== */

    // 3. TẠO MỚI KHUYẾN MÃI
    // Tên hàm: create_taoKM_CSKH
    public function create_taoKM_CSKH($data) {
        $sql = "INSERT INTO khuyenmai (TenChuongTrinh, NgayBatDau, NgayKetThuc, MucUuDai, LoaiUuDai, DoiTuong, TrangThai) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bind_param("sssdsss", 
            $data['TenCTKM'], 
            $data['NgayBD'], 
            $data['NgayKT'], 
            $data['MucUuDai'], 
            $data['LoaiUuDai'],
            $data['DoiTuong'], 
            $data['TrangThai']
        );
        
        return $stmt->execute();
    }

    // 4. CẬP NHẬT KHUYẾN MÃI
    public function update_suaKM_CSKH($id, $data) {
        $sql = "UPDATE khuyenmai 
                SET TenChuongTrinh = ?, NgayBatDau = ?, NgayKetThuc = ?, 
                    MucUuDai = ?, LoaiUuDai = ?, DoiTuong = ?, TrangThai = ? 
                WHERE MaKhuyenMai = ?";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bind_param("sssdsssi", 
            $data['TenCTKM'], 
            $data['NgayBD'], 
            $data['NgayKT'], 
            $data['MucUuDai'], 
            $data['LoaiUuDai'],
            $data['DoiTuong'], 
            $data['TrangThai'], 
            $id
        );
        return $stmt->execute();
    }
    
    // 5. LẤY DANH SÁCH (Để hiển thị)
    // Tên hàm: getAll_xemDS_CSKH
    public function getAll_xemDS_CSKH() {
        return $this->db->query("SELECT * FROM khuyenmai ORDER BY MaKhuyenMai DESC")->fetch_all(MYSQLI_ASSOC);
    }

    // 6. LẤY CHI TIẾT (Để sửa)
    // Tên hàm: getOne_xemChiTiet_CSKH
    public function getOne_xemChiTiet_CSKH($id) {
        $stmt = $this->db->prepare("SELECT * FROM khuyenmai WHERE MaKhuyenMai = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /* ====================================================
     * PHẦN 2: HÀM MỚI - KIỂM TRA KHÁCH HÀNG (GỘP VÀO ĐÂY LUÔN)
     * ==================================================== */
    
    // Hàm này dùng để xem khách đang login thuộc loại nào (VIP, NEW hay ALL)
    public function checkLoaiKhach_ApDungMa($maKH) {
        // Đếm số đơn đặt phòng ĐÃ THANH TOÁN
        // (Lưu ý: Bạn cần chắc chắn tên bảng là 'hoadon' hay 'datphong'. Ở đây mình dùng 'datphong')
        $sql = "SELECT COUNT(*) as SoLan, SUM(TongTien) as TongTien 
                FROM datphong 
                WHERE MaKhachHang = ? AND TrangThai = 'DaThanhToan'"; 
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $maKH);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        $soLan = (int)$result['SoLan'];
        $tongTien = (float)$result['TongTien'];

        // Logic phân loại:
        if ($soLan == 0) {
            return 'NEW'; // Khách mới
        } 
        
        if ($tongTien >= 10000000) { 
            return 'VIP'; // Khách VIP (Chi trên 10 triệu)
        }

        return 'NORMAL'; // Khách thường
    }
    
    /* ====================================================
     * TỰ ĐỘNG CẬP NHẬT TRẠNG THÁI HẾT HẠN
     * ==================================================== */
    public function autoUpdateExpiredPromotions() {
        $today = date('Y-m-d');
        
        $sql = "UPDATE khuyenmai 
                SET TrangThai = 'HetHan' 
                WHERE NgayKetThuc < ? 
                AND TrangThai != 'HetHan'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $today);
        $stmt->execute();
        
        $affectedRows = $stmt->affected_rows;
        if ($affectedRows > 0) {
            error_log("✅ Tự động cập nhật {$affectedRows} khuyến mãi thành 'Hết hạn'");
        }
        
        return $affectedRows;
    }

    /**
     * Lấy danh sách tất cả khuyến mãi với phân trang
     */
    public function layDanhSachKhuyenMai(int $limit, int $offset): array
    {
        $sql = "
            SELECT 
                MaKhuyenMai,
                TenChuongTrinh,
                NgayBatDau,
                NgayKetThuc,
                MucUuDai,
                DoiTuong,
                TrangThai,
                LoaiUuDai
            FROM khuyenmai
            ORDER BY NgayBatDau DESC
            LIMIT ? OFFSET ?
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            error_log('SQL Error: ' . $this->db->error);
            return [];
        }

        $stm->bind_param('ii', $limit, $offset);
        $stm->execute();
        $rows = $stm->get_result()->fetch_all(MYSQLI_ASSOC);

        return $rows;
    }

    /**
     * Đếm tổng số khuyến mãi
     */
    public function countKhuyenMai(): int
    {
        $sql = "SELECT COUNT(*) as total FROM khuyenmai";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    /**
     * Lấy thống kê khuyến mãi
     */
    public function layThongKeKhuyenMai(): array
    {
        $sql = "
            SELECT 
                TrangThai,
                COUNT(*) as total,
                LoaiUuDai
            FROM khuyenmai
            GROUP BY TrangThai, LoaiUuDai
        ";

        $result = $this->db->query($sql);
        $stats = [];

        while ($row = $result->fetch_assoc()) {
            $stats[] = $row;
        }

        return $stats;
    }

    /**
     * Lấy khuyến mãi đang áp dụng (DangApDung và ngày hôm nay nằm trong khoảng)
     */
    public function layKhuyenMaiDangApDung(): array
    {
        $today = date('Y-m-d');
        $sql = "
            SELECT *
            FROM khuyenmai
            WHERE TrangThai = 'DangApDung'
                AND NgayBatDau <= ?
                AND NgayKetThuc >= ?
            ORDER BY NgayKetThuc ASC
        ";

        $stm = $this->db->prepare($sql);
        $stm->bind_param('ss', $today, $today);
        $stm->execute();
        $rows = $stm->get_result()->fetch_all(MYSQLI_ASSOC);

        return $rows;
    }

    /**
     * Lấy khuyến mãi đã hết hạn
     */
    public function layKhuyenMaiHetHan(): array
    {
        $sql = "
            SELECT *
            FROM khuyenmai
            WHERE TrangThai = 'HetHan'
            ORDER BY NgayKetThuc DESC
            LIMIT 50
        ";

        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy khuyến mãi tạm ngưng
     */
    public function layKhuyenMaiTamNgung(): array
    {
        $sql = "
            SELECT *
            FROM khuyenmai
            WHERE TrangThai = 'TamNgung'
            ORDER BY NgayBatDau DESC
        ";

        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy khuyến mãi sắp diễn ra (NgayBatDau > ngày hôm nay)
     */
    public function layKhuyenMaiSapDienRa(): array
    {
        $today = date('Y-m-d');
        $sql = "
            SELECT *
            FROM khuyenmai
            WHERE NgayBatDau > ?
                AND TrangThai = 'DangApDung'
            ORDER BY NgayBatDau ASC
            LIMIT 20
        ";

        $stm = $this->db->prepare($sql);
        $stm->bind_param('s', $today);
        $stm->execute();
        $rows = $stm->get_result()->fetch_all(MYSQLI_ASSOC);

        return $rows;
    }
}
