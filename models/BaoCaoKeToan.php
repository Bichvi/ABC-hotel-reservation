<?php

class BaoCaoKeToan
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Báo cáo doanh thu theo ngày
     * Lấy từ bảng giaodich (TongTien, NgayGiaoDich)
     */
    public function baoCaoDoanhThu(string $tuNgay, string $denNgay): array
    {
        $sql = "
            SELECT 
                DATE(NgayGiaoDich) AS Ngay,
                COUNT(*)           AS SoGiaoDich,
                SUM(TongTien)      AS TongDoanhThu
            FROM giaodich
            WHERE DATE(NgayGiaoDich) BETWEEN ? AND ?
              AND TrangThai IN ('Booked','Stayed','Paid')
            GROUP BY DATE(NgayGiaoDich)
            ORDER BY Ngay
        ";

        $stm = $this->db->prepare($sql);
        $stm->bind_param('ss', $tuNgay, $denNgay);
        $stm->execute();
        $rows = $stm->get_result()->fetch_all(MYSQLI_ASSOC);

        $tongGD    = 0;
        $tongTien  = 0.0;

        foreach ($rows as $r) {
            $tongGD   += (int)$r['SoGiaoDich'];
            $tongTien += (float)$r['TongDoanhThu'];
        }

        return [
            'rows' => $rows,
            'tong' => [
                'so_gd'     => $tongGD,
                'doanh_thu' => $tongTien
            ]
        ];
    }

    /**
     * Báo cáo chi phí theo ngày
     * Lấy từ bảng chiphi (SoTien, NgayChi)
     */
    public function baoCaoChiPhi(string $tuNgay, string $denNgay): array
    {
        $sql = "
            SELECT 
                NgayChi AS Ngay,
                COUNT(*)        AS SoPhieuChi,
                SUM(SoTien)     AS TongChiPhi
            FROM chiphi
            WHERE NgayChi BETWEEN ? AND ?
              AND TrangThai = 'DaDuyet'
            GROUP BY NgayChi
            ORDER BY NgayChi
        ";

        $stm = $this->db->prepare($sql);
        $stm->bind_param('ss', $tuNgay, $denNgay);
        $stm->execute();
        $rows = $stm->get_result()->fetch_all(MYSQLI_ASSOC);

        $tongPhieu = 0;
        $tongTien  = 0.0;

        foreach ($rows as $r) {
            $tongPhieu += (int)$r['SoPhieuChi'];
            $tongTien  += (float)$r['TongChiPhi'];
        }

        return [
            'rows' => $rows,
            'tong' => [
                'so_phieu' => $tongPhieu,
                'chi_phi'  => $tongTien
            ]
        ];
    }

    /**
     * Hàm tổng: tạo báo cáo theo loại
     * - doanhthu
     * - chiphi
     * - tonghop (doanh thu, chi phí, lợi nhuận)
     */
    public function taoBaoCao(string $loaiBaoCao, string $tuNgay, string $denNgay): array
    {
        $loaiBaoCao = strtolower($loaiBaoCao);

        if ($loaiBaoCao === 'doanhthu') {
            $dt = $this->baoCaoDoanhThu($tuNgay, $denNgay);

            return [
                'type'    => 'doanhthu',
                'rows'    => $dt['rows'],
                'tong'    => $dt['tong'],
                'hasData' => count($dt['rows']) > 0
            ];
        }

        if ($loaiBaoCao === 'chiphi') {
            $cp = $this->baoCaoChiPhi($tuNgay, $denNgay);

            return [
                'type'    => 'chiphi',
                'rows'    => $cp['rows'],
                'tong'    => $cp['tong'],
                'hasData' => count($cp['rows']) > 0
            ];
        }

        // Tổng hợp: doanh thu + chi phí + lợi nhuận
        $dt = $this->baoCaoDoanhThu($tuNgay, $denNgay);
        $cp = $this->baoCaoChiPhi($tuNgay, $denNgay);

        $loiNhuan = $dt['tong']['doanh_thu'] - $cp['tong']['chi_phi'];

        return [
            'type'      => 'tonghop',
            'doanh_thu' => $dt,
            'chi_phi'   => $cp,
            'loi_nhuan' => $loiNhuan,
            'hasData'   => (count($dt['rows']) > 0 || count($cp['rows']) > 0)
        ];
    }

    /**
     * Lấy danh sách giao dịch (doanh thu) với phân trang và bộ lọc
     * @param string $tuNgay Từ ngày (Y-m-d)
     * @param string $denNgay Đến ngày (Y-m-d)
     * @param string $trangThai Trạng thái ('all', 'Booked', 'Stayed', 'Paid', 'DaHuy')
     * @param string $search Tìm kiếm theo ID giao dịch hoặc tên khách
     * @param int $limit Giới hạn bản ghi trên 1 trang
     * @param int $offset Vị trí bắt đầu
     * @return array ['rows' => [], 'total' => int]
     */
    public function layDanhSachGiaoDich(string $tuNgay, string $denNgay, string $trangThai, string $search, int $limit, int $offset): array
    {
        // Xây dựng điều kiện WHERE - thêm 1 ngày để bao gồm toàn bộ ngày cuối
        $denNgayNext = date('Y-m-d', strtotime($denNgay . ' +1 day'));
        
        $whereClauses = [
            "gd.NgayGiaoDich >= ? AND gd.NgayGiaoDich < ?"
        ];
        $params = [$tuNgay . ' 00:00:00', $denNgayNext . ' 00:00:00'];
        $types = 'ss';

        // Lọc theo trạng thái
        if ($trangThai !== 'all') {
            $whereClauses[] = "gd.TrangThai = ?";
            $params[] = $trangThai;
            $types .= 's';
        }

        // Tìm kiếm
        if (!empty($search)) {
            $search = '%' . $search . '%';
            $whereClauses[] = "(gd.MaGiaoDich LIKE ? OR ctgd.TenKhach LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $types .= 'ss';
        }

        $whereStr = implode(' AND ', $whereClauses);

        // Lấy tổng số bản ghi
        $sqlCount = "
            SELECT COUNT(DISTINCT gd.MaGiaoDich) as total
            FROM giaodich gd
            LEFT JOIN chitietgiaodich ctgd ON gd.MaGiaoDich = ctgd.MaGiaoDich
            WHERE $whereStr
        ";

        $stmCount = $this->db->prepare($sqlCount);
        if (!empty($params)) {
            $stmCount->bind_param($types, ...$params);
        }
        $stmCount->execute();
        $totalResult = $stmCount->get_result()->fetch_assoc();
        $total = $totalResult['total'] ?? 0;

        // Lấy danh sách giao dịch
        // FIX: Use GROUP BY only on gd fields to avoid duplication
        // Get TenKhach from first chitietgiaodich record and actual room numbers from phong table
        $sql = "
            SELECT 
                gd.MaGiaoDich,
                DATE(gd.NgayGiaoDich) AS NgayGiaoDich,
                GROUP_CONCAT(DISTINCT p.SoPhong SEPARATOR ', ') AS SoPhong,
                SUM(ctgd.ThanhTien + COALESCE(ctgd.TienPhuThu, 0)) AS TongTien,
                gd.TrangThai,
                COALESCE(MAX(ctgd.TenKhach), 'Không xác định') AS TenKhach
            FROM giaodich gd
            LEFT JOIN chitietgiaodich ctgd ON gd.MaGiaoDich = ctgd.MaGiaoDich
            LEFT JOIN phong p ON ctgd.MaPhong = p.MaPhong
            WHERE $whereStr
            GROUP BY gd.MaGiaoDich, DATE(gd.NgayGiaoDich), gd.TrangThai
            ORDER BY gd.NgayGiaoDich DESC
            LIMIT ? OFFSET ?
        ";

        $stm = $this->db->prepare($sql);
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        
        if (!empty($params)) {
            $stm->bind_param($types, ...$params);
        }
        $stm->execute();
        $rows = $stm->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'rows'  => $rows,
            'total' => $total
        ];
    }

    /**
     * Lấy danh sách chi phí với phân trang và bộ lọc
     * @param string $tuNgay Từ ngày (Y-m-d)
     * @param string $denNgay Đến ngày (Y-m-d)
     * @param string $trangThai Trạng thái ('all', 'ChoDuyet', 'DaDuyet', 'Huy')
     * @param string $search Tìm kiếm theo tên chi phí hoặc nội dung
     * @param int $limit Giới hạn bản ghi trên 1 trang
     * @param int $offset Vị trí bắt đầu
     * @return array ['rows' => [], 'total' => int]
     */
    public function layDanhSachChiPhi(string $tuNgay, string $denNgay, string $trangThai, string $search, int $limit, int $offset): array
    {
        // Xây dựng điều kiện WHERE
        $whereClauses = [
            "cp.NgayChi BETWEEN ? AND ?"
        ];
        $params = [$tuNgay, $denNgay];
        $types = 'ss';

        // Lọc theo trạng thái
        if ($trangThai !== 'all') {
            $whereClauses[] = "cp.TrangThai = ?";
            $params[] = $trangThai;
            $types .= 's';
        }

        // Tìm kiếm
        if (!empty($search)) {
            $search = '%' . $search . '%';
            $whereClauses[] = "(cp.TenChiPhi LIKE ? OR cp.NoiDung LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $types .= 'ss';
        }

        $whereStr = implode(' AND ', $whereClauses);

        // Lấy tổng số bản ghi
        $sqlCount = "
            SELECT COUNT(*) as total
            FROM chiphi cp
            WHERE $whereStr
        ";

        $stmCount = $this->db->prepare($sqlCount);
        if (!empty($params)) {
            $countParams = $params;
            $stmCount->bind_param($types, ...$countParams);
        }
        $stmCount->execute();
        $totalResult = $stmCount->get_result()->fetch_assoc();
        $total = $totalResult['total'] ?? 0;

        // Lấy danh sách chi phí
        $sql = "
            SELECT 
                cp.MaCP,
                cp.TenChiPhi,
                cp.NgayChi,
                cp.SoTien,
                cp.NoiDung,
                cp.TrangThai
            FROM chiphi cp
            WHERE $whereStr
            ORDER BY cp.NgayChi DESC, cp.MaCP DESC
            LIMIT ? OFFSET ?
        ";

        $stm = $this->db->prepare($sql);
        $params[] = $limit;
        $params[] = $offset;
        $allTypes = $types . 'ii';
        
        if (!empty($params)) {
            $stm->bind_param($allTypes, ...$params);
        }
        $stm->execute();
        $rows = $stm->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'rows'  => $rows,
            'total' => $total
        ];
    }

    /**
     * Tính tổng doanh thu với bộ lọc
     */
    public function tinhTongDoanhThu(string $tuNgay, string $denNgay, string $trangThai, string $search): float
    {
        // Thêm 1 ngày để bao gồm toàn bộ ngày cuối
        $denNgayNext = date('Y-m-d', strtotime($denNgay . ' +1 day'));
        
        $whereClauses = [
            "gd.NgayGiaoDich >= ? AND gd.NgayGiaoDich < ?"
        ];
        $params = [$tuNgay . ' 00:00:00', $denNgayNext . ' 00:00:00'];
        $types = 'ss';

        if ($trangThai !== 'all') {
            $whereClauses[] = "gd.TrangThai = ?";
            $params[] = $trangThai;
            $types .= 's';
        }

        if (!empty($search)) {
            $search = '%' . $search . '%';
            $whereClauses[] = "(gd.MaGiaoDich LIKE ? OR ctgd.TenKhach LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $types .= 'ss';
        }

        $whereStr = implode(' AND ', $whereClauses);

        $sql = "
            SELECT SUM(ctgd.ThanhTien + COALESCE(ctgd.TienPhuThu, 0)) as tong
            FROM giaodich gd
            LEFT JOIN chitietgiaodich ctgd ON gd.MaGiaoDich = ctgd.MaGiaoDich
            WHERE $whereStr
        ";

        $stm = $this->db->prepare($sql);
        if (!empty($params)) {
            $stm->bind_param($types, ...$params);
        }
        $stm->execute();
        $result = $stm->get_result()->fetch_assoc();

        return (float)($result['tong'] ?? 0);
    }

    /**
     * Tính tổng chi phí với bộ lọc
     */
    public function tinhTongChiPhi(string $tuNgay, string $denNgay, string $trangThai, string $search): float
    {
        $whereClauses = [
            "cp.NgayChi BETWEEN ? AND ?"
        ];
        $params = [$tuNgay, $denNgay];
        $types = 'ss';

        if ($trangThai !== 'all') {
            $whereClauses[] = "cp.TrangThai = ?";
            $params[] = $trangThai;
            $types .= 's';
        }

        if (!empty($search)) {
            $search = '%' . $search . '%';
            $whereClauses[] = "(cp.TenChiPhi LIKE ? OR cp.NoiDung LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $types .= 'ss';
        }

        $whereStr = implode(' AND ', $whereClauses);

        $sql = "
            SELECT SUM(cp.SoTien) as tong
            FROM chiphi cp
            WHERE $whereStr
        ";

        $stm = $this->db->prepare($sql);
        if (!empty($params)) {
            $stm->bind_param($types, ...$params);
        }
        $stm->execute();
        $result = $stm->get_result()->fetch_assoc();

        return (float)($result['tong'] ?? 0);
    }

    /**
     * Doanh thu theo ngày (cho biểu đồ đường)
     */
    public function doanhThuTheoNgay(string $tuNgay, string $denNgay): array
    {
        $denNgayNext = date('Y-m-d', strtotime($denNgay . ' +1 day'));

        $sql = "
            SELECT 
                DATE(NgayGiaoDich) AS Ngay,
                SUM(TongTien) AS TongDoanhThu
            FROM giaodich
            WHERE NgayGiaoDich >= ? AND NgayGiaoDich < ?
              AND TrangThai IN ('Booked','Stayed','Paid')
            GROUP BY DATE(NgayGiaoDich)
            ORDER BY Ngay ASC
        ";

        $stm = $this->db->prepare($sql);
        $params = [$tuNgay . ' 00:00:00', $denNgayNext . ' 00:00:00'];
        $stm->bind_param('ss', ...$params);
        $stm->execute();
        
        return $stm->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Doanh thu theo phòng (cho biểu đồ cột)
     */
    public function doanhThuTheoPhong(string $tuNgay, string $denNgay): array
    {
        $denNgayNext = date('Y-m-d', strtotime($denNgay . ' +1 day'));

        $sql = "
            SELECT 
                p.MaPhong,
                p.SoPhong,
                COUNT(ctgd.MaCTGD) AS SoLanDat,
                SUM(ctgd.ThanhTien + COALESCE(ctgd.TienPhuThu, 0)) AS TongDoanhThu
            FROM phong p
            LEFT JOIN chitietgiaodich ctgd ON p.MaPhong = ctgd.MaPhong
            LEFT JOIN giaodich gd ON ctgd.MaGiaoDich = gd.MaGiaoDich
            WHERE gd.NgayGiaoDich IS NOT NULL
              AND gd.NgayGiaoDich >= ? AND gd.NgayGiaoDich < ?
              AND gd.TrangThai IN ('Booked','Stayed','Paid')
            GROUP BY p.MaPhong, p.SoPhong
            ORDER BY TongDoanhThu DESC
            LIMIT 10
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            error_log('SQL Error: ' . $this->db->error);
            return [];
        }
        
        $params = [$tuNgay . ' 00:00:00', $denNgayNext . ' 00:00:00'];
        $stm->bind_param('ss', ...$params);
        $stm->execute();
        
        return $stm->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Chi phí theo loại (cho biểu đồ tròn)
     */
    public function chiPhiTheoLoai(string $tuNgay, string $denNgay): array
    {
        $sql = "
            SELECT 
                TenChiPhi,
                SUM(SoTien) AS TongChiPhi
            FROM chiphi
            WHERE NgayChi BETWEEN ? AND ?
              AND TrangThai = 'DaDuyet'
            GROUP BY TenChiPhi
            ORDER BY TongChiPhi DESC
        ";

        $stm = $this->db->prepare($sql);
        $stm->bind_param('ss', $tuNgay, $denNgay);
        $stm->execute();
        
        return $stm->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Thêm chi phí mới
     */
    public function themChiPhi(string $tenChiPhi, string $ngayChi, string $soTien, string $noiDung, string $trangThai): bool
    {
        $sql = "
            INSERT INTO chiphi (TenChiPhi, NgayChi, SoTien, NoiDung, TrangThai)
            VALUES (?, ?, ?, ?, ?)
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            error_log('SQL Error: ' . $this->db->error);
            return false;
        }

        $stm->bind_param('ssdss', $tenChiPhi, $ngayChi, $soTien, $noiDung, $trangThai);
        $result = $stm->execute();

        return $result;
    }

    /**
     * Lấy chi phí theo ID
     */
    public function layChiPhiById(int $maCP): ?array
    {
        $sql = "SELECT * FROM chiphi WHERE MaCP = ?";
        $stm = $this->db->prepare($sql);
        if (!$stm) {
            return null;
        }

        $stm->bind_param('i', $maCP);
        $stm->execute();
        $result = $stm->get_result()->fetch_assoc();

        return $result ?: null;
    }

    /**
     * Sửa chi phí
     */
    public function suaChiPhi(int $maCP, string $tenChiPhi, string $ngayChi, string $soTien, string $noiDung, string $trangThai): bool
    {
        $sql = "
            UPDATE chiphi 
            SET TenChiPhi = ?, NgayChi = ?, SoTien = ?, NoiDung = ?, TrangThai = ?
            WHERE MaCP = ?
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            error_log('SQL Error: ' . $this->db->error);
            return false;
        }

        $stm->bind_param('ssdssi', $tenChiPhi, $ngayChi, $soTien, $noiDung, $trangThai, $maCP);
        $result = $stm->execute();

        return $result;
    }

    /**
     * Lấy giao dịch theo ID
     */
    public function layGiaoDichById(int $maGiaoDich): ?array
    {
        $sql = "
            SELECT 
                gd.*,
                COALESCE(MAX(ctgd.TenKhach), 'Không xác định') AS TenKhach,
                GROUP_CONCAT(DISTINCT p.SoPhong SEPARATOR ', ') AS SoPhong
            FROM giaodich gd
            LEFT JOIN chitietgiaodich ctgd ON gd.MaGiaoDich = ctgd.MaGiaoDich
            LEFT JOIN phong p ON ctgd.MaPhong = p.MaPhong
            WHERE gd.MaGiaoDich = ?
            GROUP BY gd.MaGiaoDich
        ";
        $stm = $this->db->prepare($sql);
        if (!$stm) {
            return null;
        }

        $stm->bind_param('i', $maGiaoDich);
        $stm->execute();
        $result = $stm->get_result()->fetch_assoc();

        return $result ?: null;
    }

    /**
     * Sửa doanh thu (giao dịch)
     */
    public function suaDoanhThu(int $maGiaoDich, string $tongTien, string $trangThai, string $phuongThucThanhToan, string $ghiChu): bool
    {
        $sql = "
            UPDATE giaodich 
            SET TongTien = ?, TrangThai = ?, PhuongThucThanhToan = ?, GhiChu = ?
            WHERE MaGiaoDich = ?
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            error_log('SQL Error: ' . $this->db->error);
            return false;
        }

        $stm->bind_param('dsssi', $tongTien, $trangThai, $phuongThucThanhToan, $ghiChu, $maGiaoDich);
        $result = $stm->execute();

        return $result;
    }

    /**
     * Xóa chi phí
     */
    public function xoaChiPhi(int $maCP): bool
    {
        $sql = "DELETE FROM chiphi WHERE MaCP = ?";
        $stm = $this->db->prepare($sql);
        if (!$stm) {
            error_log('SQL Error: ' . $this->db->error);
            return false;
        }

        $stm->bind_param('i', $maCP);
        $result = $stm->execute();

        return $result;
    }

    /**
     * Lấy danh sách kiểm toán đêm
     */
    public function layDanhSachKiemToanDem(int $limit, int $offset): array
    {
        $sql = "
            SELECT 
                ktd.MaKTD,
                ktd.NgayKTD,
                ktd.SoDuDauNgay,
                ktd.SoDuCuoiNgay,
                ktd.TongDoanhThu,
                ktd.TongChiPhi,
                ktd.LoiNhuan,
                ktd.TrangThai,
                ktd.ThoiGianTao,
                COALESCE(tk.Username, 'N/A') as TenNguoiDung
            FROM kiem_toan_dem ktd
            LEFT JOIN taikhoan tk ON ktd.MaTaiKhoan = tk.MaTK
            ORDER BY ktd.NgayKTD DESC
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
     * Đếm tổng số kiểm toán đêm
     */
    public function countKiemToanDem(): int
    {
        $sql = "SELECT COUNT(*) as total FROM kiem_toan_dem";
        $stm = $this->db->prepare($sql);
        if (!$stm) {
            return 0;
        }

        $stm->execute();
        $result = $stm->get_result()->fetch_assoc();
        return (int)($result['total'] ?? 0);
    }

    /**
     * Lấy kiểm toán đêm theo ID
     */
    public function layKiemToanDemById(int $maKTD): ?array
    {
        $sql = "
            SELECT 
                ktd.*,
                COALESCE(tk.Username, 'N/A') as TenNguoiDung
            FROM kiem_toan_dem ktd
            LEFT JOIN taikhoan tk ON ktd.MaTaiKhoan = tk.MaTK
            WHERE ktd.MaKTD = ?
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            return null;
        }

        $stm->bind_param('i', $maKTD);
        $stm->execute();
        $result = $stm->get_result()->fetch_assoc();

        return $result ?: null;
    }

    /**
     * Lấy kiểm toán đêm theo ngày
     */
    public function layKiemToanDemTheoNgay(string $ngay): ?array
    {
        $sql = "
            SELECT 
                ktd.*,
                COALESCE(tk.Username, 'N/A') as TenNguoiDung
            FROM kiem_toan_dem ktd
            LEFT JOIN taikhoan tk ON ktd.MaTaiKhoan = tk.MaTK
            WHERE ktd.NgayKTD = ?
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            return null;
        }

        $stm->bind_param('s', $ngay);
        $stm->execute();
        $result = $stm->get_result()->fetch_assoc();

        return $result ?: null;
    }

    /**
     * Tạo kiểm toán đêm mới
     */
    public function themKiemToanDem(string $ngay, string $soDuDauNgay, string $soDuCuoiNgay, string $tongDoanhThu, string $tongChiPhi, string $loiNhuan, string $ghiChu, int $maTaiKhoan): bool
    {
        $trangThai = 'DaKiemToan';
        
        $sql = "
            INSERT INTO kiem_toan_dem 
            (NgayKTD, SoDuDauNgay, SoDuCuoiNgay, TongDoanhThu, TongChiPhi, LoiNhuan, GhiChu, TrangThai, MaTaiKhoan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            error_log('SQL Error: ' . $this->db->error);
            return false;
        }

        $stm->bind_param(
            'sdddddssi',
            $ngay,
            $soDuDauNgay,
            $soDuCuoiNgay,
            $tongDoanhThu,
            $tongChiPhi,
            $loiNhuan,
            $ghiChu,
            $trangThai,
            $maTaiKhoan
        );

        $result = $stm->execute();
        return $result;
    }

    /**
     * Sửa kiểm toán đêm
     */
    public function suaKiemToanDem(int $maKTD, string $soDuDauNgay, string $soDuCuoiNgay, string $tongDoanhThu, string $tongChiPhi, string $loiNhuan, string $ghiChu): bool
    {
        $sql = "
            UPDATE kiem_toan_dem 
            SET SoDuDauNgay = ?, SoDuCuoiNgay = ?, TongDoanhThu = ?, TongChiPhi = ?, LoiNhuan = ?, GhiChu = ?
            WHERE MaKTD = ?
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            error_log('SQL Error: ' . $this->db->error);
            return false;
        }

        $stm->bind_param('dddddsi', $soDuDauNgay, $soDuCuoiNgay, $tongDoanhThu, $tongChiPhi, $loiNhuan, $ghiChu, $maKTD);
        $result = $stm->execute();

        return $result;
    }

    /**
     * Xóa kiểm toán đêm
     */
    public function xoaKiemToanDem(int $maKTD): bool
    {
        $sql = "DELETE FROM kiem_toan_dem WHERE MaKTD = ?";
        $stm = $this->db->prepare($sql);
        if (!$stm) {
            error_log('SQL Error: ' . $this->db->error);
            return false;
        }

        $stm->bind_param('i', $maKTD);
        $result = $stm->execute();

        return $result;
    }

    /**
     * Tính tổng doanh thu hôm nay
     */
    public function tinhTongDoanhThuHom(string $ngay): float
    {
        $ngayNext = date('Y-m-d', strtotime($ngay . ' +1 day'));
        $ngayStart = $ngay . ' 00:00:00';
        $ngayEnd = $ngayNext . ' 00:00:00';
        
        $sql = "
            SELECT SUM(TongTien) as tong
            FROM giaodich
            WHERE NgayGiaoDich >= ? AND NgayGiaoDich < ?
              AND TrangThai IN ('Booked','Stayed','Paid')
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            return 0;
        }

        $stm->bind_param('ss', $ngayStart, $ngayEnd);
        $stm->execute();
        $result = $stm->get_result()->fetch_assoc();

        return (float)($result['tong'] ?? 0);
    }

    /**
     * Tính tổng chi phí hôm nay
     */
    public function tinhTongChiPhiHom(string $ngay): float
    {
        $sql = "
            SELECT SUM(SoTien) as tong
            FROM chiphi
            WHERE NgayChi = ? AND TrangThai = 'DaDuyet'
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            return 0;
        }

        $stm->bind_param('s', $ngay);
        $stm->execute();
        $result = $stm->get_result()->fetch_assoc();

        return (float)($result['tong'] ?? 0);
    }

    /**
     * Ghi nhận lịch sử sửa giao dịch (Audit Log)
     */
    public function ghiNhatLichSuSua(
        int $maGiaoDich,
        int $maNhanVien,
        float $tongTienCu,
        string $trangThaiCu,
        string $phuongThucCu,
        string $ghiChuCu,
        float $tongTienMoi,
        string $trangThaiMoi,
        string $phuongThucMoi,
        string $ghiChuMoi,
        string $lySua
    ): bool {
        $sql = "
            INSERT INTO lich_su_sua_giaodich (
                MaGiaoDich, MaNhanVien,
                TongTienCu, TrangThaiCu, PhuongThucCu, GhiChuCu,
                TongTienMoi, TrangThaiMoi, PhuongThucMoi, GhiChuMoi,
                LySua, ThoiGianSua
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            error_log('SQL Error: ' . $this->db->error);
            return false;
        }

        $stm->bind_param(
            'iidsssdssss',
            $maGiaoDich, $maNhanVien,
            $tongTienCu, $trangThaiCu, $phuongThucCu, $ghiChuCu,
            $tongTienMoi, $trangThaiMoi, $phuongThucMoi, $ghiChuMoi,
            $lySua
        );

        return $stm->execute();
    }

    /**
     * Lấy lịch sử sửa giao dịch
     */
    public function layLichSuSuaGiaoDich(int $maGiaoDich): array
    {
        $sql = "
            SELECT 
                lsg.MaLichSu,
                lsg.ThoiGianSua,
                tk.Username as NhanVienSua,
                lsg.TongTienCu,
                lsg.TongTienMoi,
                lsg.TrangThaiCu,
                lsg.TrangThaiMoi,
                lsg.PhuongThucCu,
                lsg.PhuongThucMoi,
                lsg.LySua
            FROM lich_su_sua_giaodich lsg
            LEFT JOIN taikhoan tk ON lsg.MaNhanVien = tk.MaTK
            WHERE lsg.MaGiaoDich = ?
            ORDER BY lsg.ThoiGianSua DESC
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            return [];
        }

        $stm->bind_param('i', $maGiaoDich);
        $stm->execute();
        return $stm->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy doanh thu theo loại (Phòng, Dịch vụ, Phụ thu)
     */
    public function doanhThuTheoLoai(string $tuNgay, string $denNgay): array
    {
        $denNgayNext = date('Y-m-d', strtotime($denNgay . ' +1 day'));

        $sql = "
            SELECT 
                COALESCE(ctgd.LoaiDoanhThu, 'Phong') as LoaiDoanhThu,
                COUNT(*) as SoChiTiet,
                SUM(ctgd.ThanhTien) as DoanhThuPhong,
                SUM(ctgd.TienPhuThu) as DoanhThuPhuThu
            FROM chitietgiaodich ctgd
            JOIN giaodich gd ON ctgd.MaGiaoDich = gd.MaGiaoDich
            WHERE gd.NgayGiaoDich >= ? AND gd.NgayGiaoDich < ?
                AND gd.TrangThai IN ('Booked', 'Stayed', 'Paid')
            GROUP BY ctgd.LoaiDoanhThu
            ORDER BY LoaiDoanhThu
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            return [];
        }

        $params = [$tuNgay . ' 00:00:00', $denNgayNext . ' 00:00:00'];
        $stm->bind_param('ss', ...$params);
        $stm->execute();
        return $stm->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy doanh thu phòng theo ngày lưu trú (Accrual basis)
     */
    public function doanhThuPhongTheoNgayLuuTru(string $tuNgay, string $denNgay): array
    {
        $denNgayNext = date('Y-m-d', strtotime($denNgay . ' +1 day'));

        $sql = "
            SELECT 
                ctgd.NgayGhiNhanKeToan as NgayGhiNhan,
                COUNT(*) as SoNguoi,
                SUM(ctgd.ThanhTien) as DoanhThuPhong,
                SUM(ctgd.TienPhuThu) as DoanhThuPhuThu,
                COUNT(DISTINCT ctgd.MaPhong) as SoPhong
            FROM chitietgiaodich ctgd
            JOIN giaodich gd ON ctgd.MaGiaoDich = gd.MaGiaoDich
            WHERE ctgd.NgayGhiNhanKeToan >= ? AND ctgd.NgayGhiNhanKeToan < ?
                AND gd.TrangThai IN ('Booked', 'Stayed', 'Paid')
                AND (ctgd.LoaiDoanhThu = 'Phong' OR ctgd.LoaiDoanhThu IS NULL)
            GROUP BY ctgd.NgayGhiNhanKeToan
            ORDER BY ctgd.NgayGhiNhanKeToan DESC
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            return [];
        }

        $params = [$tuNgay, $denNgay];
        $stm->bind_param('ss', ...$params);
        $stm->execute();
        return $stm->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy doanh thu dịch vụ theo ngày phát sinh
     */
    public function doanhThuDichVuTheoNgayPhatSinh(string $tuNgay, string $denNgay): array
    {
        $denNgayNext = date('Y-m-d', strtotime($denNgay . ' +1 day'));

        $sql = "
            SELECT 
                DATE(gd.NgayGiaoDich) as NgayPhatSinh,
                COUNT(*) as SoChiTiet,
                SUM(ctgd.ThanhTien) as DoanhThuDichVu
            FROM chitietgiaodich ctgd
            JOIN giaodich gd ON ctgd.MaGiaoDich = gd.MaGiaoDich
            WHERE gd.NgayGiaoDich >= ? AND gd.NgayGiaoDich < ?
                AND gd.TrangThai IN ('Booked', 'Stayed', 'Paid')
                AND ctgd.LoaiDoanhThu = 'DichVu'
            GROUP BY DATE(gd.NgayGiaoDich)
            ORDER BY DATE(gd.NgayGiaoDich) DESC
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            return [];
        }

        $params = [$tuNgay . ' 00:00:00', $denNgayNext . ' 00:00:00'];
        $stm->bind_param('ss', ...$params);
        $stm->execute();
        return $stm->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Thống kê giao dịch bị sửa
     */
    public function thongKeGiaoDichBiSua(string $tuNgay, string $denNgay): array
    {
        $sql = "
            SELECT 
                tk.Username as NhanVienSua,
                COUNT(DISTINCT lsg.MaGiaoDich) as SoGiaoDichSua,
                COUNT(lsg.MaLichSu) as SoLanSua,
                MIN(lsg.ThoiGianSua) as LanSuaDauTien,
                MAX(lsg.ThoiGianSua) as LanSuaCuoiCung
            FROM lich_su_sua_giaodich lsg
            JOIN taikhoan tk ON lsg.MaNhanVien = tk.MaTK
            WHERE lsg.ThoiGianSua >= ? AND lsg.ThoiGianSua < ?
            GROUP BY tk.Username
            ORDER BY SoGiaoDichSua DESC
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            return [];
        }

        $tuNgayStart = $tuNgay . ' 00:00:00';
        $denNgayNext = date('Y-m-d', strtotime($denNgay . ' +1 day'));
        $denNgayEnd = $denNgayNext . ' 00:00:00';

        $stm->bind_param('ss', $tuNgayStart, $denNgayEnd);
        $stm->execute();
        return $stm->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy danh sách loại chi phí
     */
    public function layDanhSachLoaiChiPhi(bool $onlyActive = true): array
    {
        $sql = "SELECT * FROM LoaiChiPhi";
        if ($onlyActive) {
            $sql .= " WHERE IsActive = 1";
        }
        $sql .= " ORDER BY TenLoai";

        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Hủy chi phí (soft delete - không xóa vĩnh viễn)
     * Thay vì xóa, chúng ta đặt trạng thái thành 'Huy'
     */
    public function HuyChiPhi(int $maChiPhi, string $ghiChuHuy): bool
    {
        $sql = "
            UPDATE chiphi 
            SET TrangThaiChiPhi = 'Huy', 
                GhiChuHuy = ?
            WHERE MaChiPhi = ?
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            error_log('SQL Error: ' . $this->db->error);
            return false;
        }

        $stm->bind_param('si', $ghiChuHuy, $maChiPhi);
        return $stm->execute();
    }

    /**
     * Điều chỉnh chi phí (tạo bản ghi mới với trạng thái DieuChinh)
     * Để lại bản ghi cũ với trạng thái DieuChinh
     */
    public function DieuChinhChiPhi(int $maChiPhiCu, float $tongTienMoi, string $ghiChuDieuChinh): bool
    {
        // Lấy thông tin chi phí cũ
        $sql = "SELECT * FROM chiphi WHERE MaChiPhi = ?";
        $stm = $this->db->prepare($sql);
        $stm->bind_param('i', $maChiPhiCu);
        $stm->execute();
        $chiPhiCu = $stm->get_result()->fetch_assoc();

        if (!$chiPhiCu) {
            return false;
        }

        // Đánh dấu chi phí cũ là đã điều chỉnh
        $sql = "UPDATE chiphi SET TrangThaiChiPhi = 'DieuChinh', GhiChuHuy = ? WHERE MaChiPhi = ?";
        $stm = $this->db->prepare($sql);
        $stm->bind_param('si', $ghiChuDieuChinh, $maChiPhiCu);
        if (!$stm->execute()) {
            return false;
        }

        // Tạo chi phí mới với số tiền mới
        $sql = "
            INSERT INTO chiphi 
            (TenChiPhi, MaLoaiChiPhi, SoTienChiPhi, NgayChiPhi, MaNhanVien, TrangThaiChiPhi, GhiChu)
            VALUES (?, ?, ?, ?, ?, 'Active', ?)
        ";
        $stm = $this->db->prepare($sql);
        $tenChiPhi = $chiPhiCu['TenChiPhi'] . ' (Điều chỉnh)';
        $stm->bind_param('sidisi', 
            $tenChiPhi, 
            $chiPhiCu['MaLoaiChiPhi'],
            $tongTienMoi,
            $chiPhiCu['NgayChiPhi'],
            $chiPhiCu['MaNhanVien'],
            $ghiChuDieuChinh
        );

        return $stm->execute();
    }

    /**
     * Phân bổ chi phí
     */
    public function PhanBoChiPhi(int $maChiPhi, string $loaiPhanBo, ?int $maPhong, ?int $maBoPhap, 
                                 ?string $thoiGianTu, ?string $thoiGianDen, float $tienPhanBo, string $ghiChu): bool
    {
        $sql = "
            INSERT INTO PhanBoChiPhi 
            (MaChiPhi, LoaiPhanBo, MaPhong, MaBoPhap, ThoiGianTu, ThoiGianDen, TienPhanBo, GhiChu)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            error_log('SQL Error: ' . $this->db->error);
            return false;
        }

        $stm->bind_param('isiiiddss', 
            $maChiPhi, 
            $loaiPhanBo, 
            $maPhong, 
            $maBoPhap, 
            $thoiGianTu, 
            $thoiGianDen, 
            $tienPhanBo, 
            $ghiChu
        );

        return $stm->execute();
    }

    /**
     * Lấy chi tiết phân bổ chi phí
     */
    public function layChiTietPhanBoChiPhi(int $maChiPhi): array
    {
        $sql = "
            SELECT 
                pb.*,
                p.SoPhong,
                bp.TenBoPhap
            FROM PhanBoChiPhi pb
            LEFT JOIN phong p ON pb.MaPhong = p.MaPhong
            LEFT JOIN BoPhan bp ON pb.MaBoPhap = bp.MaBoPhap
            WHERE pb.MaChiPhi = ?
            ORDER BY pb.NgayTao DESC
        ";

        $stm = $this->db->prepare($sql);
        if (!$stm) {
            return [];
        }

        $stm->bind_param('i', $maChiPhi);
        $stm->execute();
        return $stm->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy danh sách phòng
     */
    public function layDanhSachPhong(): array
    {
        $sql = "SELECT MaPhong, SoPhong, LoaiPhong FROM phong ORDER BY SoPhong";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Lấy danh sách bộ phận
     */
    public function layDanhSachBoPhap(): array
    {
        $sql = "SELECT MaBoPhap, TenBoPhap FROM BoPhan WHERE IsActive = 1 ORDER BY TenBoPhap";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * ============================================
     * KHÓA SỔ (PERIOD CLOSING)
     * ============================================
     */

    /**
     * Kiểm tra xem kỳ có bị khóa không
     */
    public function kiemTraKhoaSo(string $kyKhoaSo): bool
    {
        $sql = "SELECT TrangThai FROM KhoaSo WHERE KyKhoaSo = ? AND TrangThai = 'DaDong'";
        $stm = $this->db->prepare($sql);
        $stm->bind_param('s', $kyKhoaSo);
        $stm->execute();
        return $stm->get_result()->num_rows > 0;
    }

    /**
     * Lấy danh sách kỳ khóa sổ
     */
    public function layDanhSachKhoaSo(): array
    {
        $sql = "SELECT * FROM KhoaSo ORDER BY KyKhoaSo DESC";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Đóng sổ kỳ (Lock period)
     */
    public function dongSoKy(string $kyKhoaSo, int $maNhanVien, string $ghiChu = ''): bool
    {
        $sql = "UPDATE KhoaSo SET TrangThai = 'DaDong', NgayDong = NOW(), MaNhanVienDong = ?, GhiChu = ? WHERE KyKhoaSo = ?";
        $stm = $this->db->prepare($sql);
        return $stm->bind_param('iss', $maNhanVien, $ghiChu, $kyKhoaSo) && $stm->execute();
    }

    /**
     * ============================================
     * BÁOCAO KQKD (INCOME STATEMENT)
     * ============================================
     */

    /**
     * Tính báo cáo kết quả kinh doanh
     */
    public function tinhBaoCaoKQKD(string $kyKhoaSo): array
    {
        // Tổng doanh thu
        $sql = "SELECT SUM(TongTien) as TongDoanhThu FROM giaodich WHERE DATE_FORMAT(NgayGiaoDich, '%Y-%m') = ? AND TrangThai IN ('Booked', 'Stayed', 'Paid')";
        $stm = $this->db->prepare($sql);
        $stm->bind_param('s', $kyKhoaSo);
        $stm->execute();
        $resultDoanhThu = $stm->get_result()->fetch_assoc();
        $tongDoanhThu = (float)($resultDoanhThu['TongDoanhThu'] ?? 0);

        // Tổng chi phí
        $sql = "SELECT SUM(SoTien) as TongChiPhi FROM chiphi WHERE DATE_FORMAT(NgayChi, '%Y-%m') = ? AND TrangThai = 'DaDuyet'";
        $stm = $this->db->prepare($sql);
        $stm->bind_param('s', $kyKhoaSo);
        $stm->execute();
        $resultChiPhi = $stm->get_result()->fetch_assoc();
        $tongChiPhi = (float)($resultChiPhi['TongChiPhi'] ?? 0);

        $loiNhuanRong = $tongDoanhThu - $tongChiPhi;

        return [
            'tongDoanhThu' => $tongDoanhThu,
            'tongChiPhi' => $tongChiPhi,
            'loiNhuanRong' => $loiNhuanRong
        ];
    }

    /**
     * Lưu báo cáo KQKD
     */
    public function luuBaoCaoKQKD(string $kyKhoaSo, float $tongDoanhThu, float $tongChiPhi, float $loiNhuanRong): bool
    {
        $sql = "INSERT INTO BaoCaoKQKD (KyKhoaSo, TongDoanhThu, TongChiPhi, LoiNhuanRong) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE TongDoanhThu = ?, TongChiPhi = ?, LoiNhuanRong = ?";
        $stm = $this->db->prepare($sql);
        return $stm->bind_param('sdddddd', $kyKhoaSo, $tongDoanhThu, $tongChiPhi, $loiNhuanRong, $tongDoanhThu, $tongChiPhi, $loiNhuanRong) 
               && $stm->execute();
    }

    /**
     * ============================================
     * LƯU CHUYỂN TIỀN TỆ (CASH FLOW)
     * ============================================
     */

    /**
     * Lấy báo cáo lưu chuyển tiền tệ
     */
    public function layBaoCaoLuuChuyenTienTe(string $kyKhoaSo): array
    {
        $sql = "
            SELECT 
                LoaiHoatDong,
                TenChiMuc,
                GiaTriTienMat,
                GiaTriChuyenKhoan,
                (GiaTriTienMat + GiaTriChuyenKhoan) as TongGiaTri
            FROM LuuChuyenTienTe 
            WHERE KyKhoaSo = ?
            ORDER BY LoaiHoatDong, TenChiMuc
        ";
        $stm = $this->db->prepare($sql);
        $stm->bind_param('s', $kyKhoaSo);
        $stm->execute();
        return $stm->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * ============================================
     * CÔNG NỢ PHẢI THU/PHẢI TRẢ
     * ============================================
     */

    /**
     * Lấy công nợ phải thu
     */
    public function layCongNoPhaiThu(string $filter = 'all'): array
    {
        $sql = "
            SELECT 
                cnpt.*,
                kh.TenKH,
                kh.SDT,
                kh.Email
            FROM CongNoPhaiThu cnpt
            LEFT JOIN khachhang kh ON cnpt.MaKhachHang = kh.MaKhachHang
            WHERE 1=1
        ";
        if ($filter === 'active') {
            $sql .= " AND cnpt.TrangThaiThanhToan IN ('ChuaThu', 'ThuMotPhan')";
        }
        $sql .= " ORDER BY cnpt.NgayDenHan ASC";
        
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Lấy công nợ phải trả
     */
    public function layCongNoPhaiTra(string $filter = 'all'): array
    {
        $sql = "SELECT * FROM CongNoPhaiTra WHERE 1=1";
        if ($filter === 'active') {
            $sql .= " AND TrangThaiThanhToan IN ('ChuaTra', 'TraMotPhan')";
        }
        $sql .= " ORDER BY NgayDenHan ASC";
        
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Thêm công nợ phải thu
     */
    public function themCongNoPhaiThu(int $maKhachHang, float $soTien, string $ngayPhatSinh, ?string $ngayDenHan = null): bool
    {
        $sql = "INSERT INTO CongNoPhaiThu (MaKhachHang, SoTienConNo, SoTienGoc, NgayPhatSinh, NgayDenHan, TrangThaiThanhToan) 
                VALUES (?, ?, ?, ?, ?, 'ChuaThu')";
        $stm = $this->db->prepare($sql);
        return $stm->bind_param('iddss', $maKhachHang, $soTien, $soTien, $ngayPhatSinh, $ngayDenHan) && $stm->execute();
    }

    /**
     * ============================================
     * PHÂN BIỆT TIỀN MẶT/CHUYỂN KHOẢN
     * ============================================
     */

    /**
     * Lấy báo cáo tính theo phương thức thanh toán
     */
    public function layThongKePhuongThucThanhToan(string $tuNgay, string $denNgay): array
    {
        $sql = "
            SELECT 
                PhuongThucThanhToan,
                COUNT(*) as SoGiaoDich,
                SUM(SoTien) as TongTien
            FROM BienLaiThuTien
            WHERE NgayThanhToan BETWEEN ? AND ?
            GROUP BY PhuongThucThanhToan
        ";
        $stm = $this->db->prepare($sql);
        $stm->bind_param('ss', $tuNgay, $denNgay);
        $stm->execute();
        return $stm->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy sổ ngân quỹ (Cash Book)
     */
    public function laySoNganQuy(string $kyKhoaSo): array
    {
        $sql = "
            SELECT 
                MaNganQuy,
                NgayGiaoDich,
                LoaiGiaoDich,
                PhuongThucThanhToan,
                MoTa,
                SoTien,
                SoDuCuoiKy,
                DaDoiSoat,
                NgayDoiSoat,
                NgayTao
            FROM songanquy
            WHERE KyKhoaSo = ?
            ORDER BY NgayGiaoDich ASC
        ";
        $stm = $this->db->prepare($sql);
        $stm->bind_param('s', $kyKhoaSo);
        $stm->execute();
        return $stm->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Đối soát ngân quỹ
     */
    public function doiSoatNganQuy(int $maNganQuy): bool
    {
        $sql = "UPDATE songanquy SET DaDoiSoat = 1 WHERE MaNganQuy = ?";
        $stm = $this->db->prepare($sql);
        return $stm->bind_param('i', $maNganQuy) && $stm->execute();
    }

    /**
     * Lấy tổng chiếu dư ngân hàng
     * (Lấy từ bảng BienLaiThuTien - receipts đã xác nhận)
     */
    public function layTongChieuDuNganHang(): float
    {
        $sql = "
            SELECT SUM(SoTien) as TongSoTien
            FROM BienLaiThuTien
            WHERE TrangThai = 'DaDoi'
        ";
        $result = $this->db->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            return floatval($row['TongSoTien'] ?? 0);
        }
        return 0;
    }
   
}
