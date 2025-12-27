<?php
class PhanHoiModel {
    private $db;

    public function __construct() {
        // Kết nối CSDL theo chuẩn của nhóm
        $this->db = Database::getConnection();
        
        // [TC11] Đảm bảo lưu được Emoji và ký tự đặc biệt
        $this->db->set_charset("utf8mb4"); 
    }

    /* ============================================================
     * CÁC HÀM GET DỮ LIỆU
     * ============================================================ */

    // 1. Lấy danh sách phản hồi (Kèm tên KH và Email)
    public function getAllPhanHoi_QL() {
        $sql = "SELECT ph.*, 
                       COALESCE(ph.HoTenKH, kh.TenKH) as HoTenKH,
                       kh.TenKH as TenKH_Goc, 
                       COALESCE(ph.Email, kh.Email) as Email, 
                       COALESCE(ph.SDT, kh.SDT) as SDT
                FROM phanhoi ph 
                LEFT JOIN khachhang kh ON ph.MaKhachHang = kh.MaKhachHang 
                ORDER BY ph.NgayPhanHoi DESC";
        
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // 2. Lấy chi tiết 1 phản hồi theo ID
    public function getOnePhanHoi_QL($maPH) {
        $sql = "SELECT ph.*, kh.TenKH as TenKH_Tk, kh.Email, kh.SDT 
                FROM phanhoi ph
                LEFT JOIN khachhang kh ON ph.MaKhachHang = kh.MaKhachHang
                WHERE ph.MaPH = ? LIMIT 1";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $maPH);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }

    // 3. Lấy lịch sử trả lời
    public function getLichSu_QL($maPH) {
        $sql = "SELECT ct.*, nv.TenNV 
                FROM chitietphanhoi ct 
                LEFT JOIN nhanvien nv ON ct.MaNhanVien = nv.MaNhanVien
                WHERE ct.MaPhanHoi = ? 
                ORDER BY ct.NgayTraLoi DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $maPH);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* ============================================================
     * CÁC HÀM XỬ LÝ (UPDATE/INSERT)
     * ============================================================ */

    // [TC08] Transaction an toàn: Cập nhật trạng thái + Lưu lịch sử có gắn nhãn
    public function processFeedback_QL($maPH, $maNV, $noiDung, $trangThai) {
        // Bắt đầu giao dịch
        $this->db->begin_transaction();

        try {
            // Bước A: Update trạng thái ở bảng 'phanhoi'
            $sql1 = "UPDATE phanhoi SET TinhTrang = ? WHERE MaPH = ?";
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->bind_param("si", $trangThai, $maPH);
            
            if (!$stmt1->execute()) {
                throw new Exception("Lỗi cập nhật trạng thái");
            }

            // Bước B: Thêm vào bảng 'chitietphanhoi'
            if (!empty($noiDung)) {
                /* --- LOGIC LƯU NHÃN TRẠNG THÁI ĐỂ TÔ MÀU LỊCH SỬ --- */
                // Nếu chọn 'DaXuLy' -> Thêm [ĐÃ XỬ LÝ] vào đầu
                // Nếu chọn 'DangXuLy' -> Thêm [ĐANG XỬ LÝ] vào đầu
                $tagStatus = ($trangThai == 'DaXuLy') ? '[ĐÃ XỬ LÝ] ' : '[ĐANG XỬ LÝ] ';
                $noiDungLuu = $tagStatus . $noiDung;
                // Đảm bảo không vượt quá kích thước cột varchar(500)
                $noiDungLuu = mb_substr($noiDungLuu, 0, 500, 'UTF-8');
                /* --------------------------------------------------- */

                $sql2 = "INSERT INTO chitietphanhoi (MaPhanHoi, MaNhanVien, NoiDungTraLoi, NgayTraLoi) 
                         VALUES (?, ?, ?, NOW())";
                $stmt2 = $this->db->prepare($sql2);
                $stmt2->bind_param("iis", $maPH, $maNV, $noiDungLuu);

                if (!$stmt2->execute()) {
                    error_log("[PhanHoiModel] stmt2 execute error: " . ($stmt2->error ?? '')); 
                    error_log("[PhanHoiModel] db error: " . ($this->db->error ?? ''));
                    throw new Exception("Lỗi lưu lịch sử: " . ($stmt2->error ?? '')); 
                }
            }

            // Mọi thứ OK -> Lưu chính thức
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // Ghi log chi tiết để debug
            error_log('[PhanHoiModel] Exception in processFeedback_QL: ' . $e->getMessage());
            error_log('[PhanHoiModel] DB error on exception: ' . ($this->db->error ?? '')); 
            // Có lỗi -> Hoàn tác (Rollback)
            $this->db->rollback();
            return false;
        }
    }

    // --- HÀM TÌM KIẾM ĐA NĂNG (ĐÃ FIX LỖI TÌM MÃ SỐ + LỌC TRẠNG THÁI) ---
    public function searchPhanHoi_QL($keyword = '', $status = '', $rating = '', $dateFilter = '', $fromDate = '', $toDate = '') {
        $searchKey = "%{$keyword}%"; // Dùng cho tìm kiếm chuỗi (LIKE)
        
        // Xử lý riêng cho tìm kiếm mã số:
        // Nếu keyword là số (ví dụ "10"), ta sẽ tìm chính xác hoặc tìm trong chuỗi
        // Nếu keyword có chữ (ví dụ "PH10"), ta lọc bỏ chữ chỉ lấy số "10" để tìm
        $numberOnly = preg_replace('/[^0-9]/', '', $keyword);
        $searchId = $numberOnly !== '' ? $numberOnly : -1; // Nếu không có số thì gán -1 (không tìm ra gì)
        
        // Xử lý bộ lọc ngày
        $dateCondition = '';
        $dateStart = '';
        $dateEnd = '';
        
        if ($dateFilter === 'today') {
            $dateStart = date('Y-m-d 00:00:00');
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($dateFilter === '7days') {
            $dateStart = date('Y-m-d 00:00:00', strtotime('-7 days'));
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($dateFilter === '30days') {
            $dateStart = date('Y-m-d 00:00:00', strtotime('-30 days'));
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($dateFilter === 'custom' && $fromDate !== '' && $toDate !== '') {
            $dateStart = $fromDate . ' 00:00:00';
            $dateEnd = $toDate . ' 23:59:59';
        }

        // Xây dựng câu SQL động
        $sql = "SELECT ph.*, 
                       COALESCE(ph.HoTenKH, kh.TenKH) as HoTenKH,
                       kh.TenKH as TenKH_Goc, 
                       COALESCE(ph.Email, kh.Email) as Email, 
                       COALESCE(ph.SDT, kh.SDT) as SDT
                FROM phanhoi ph 
                LEFT JOIN khachhang kh ON ph.MaKhachHang = kh.MaKhachHang 
                WHERE 1=1 ";
        
        // Nếu có keyword, thêm điều kiện tìm kiếm
        if ($keyword !== '') {
            $sql .= " AND (
                       ph.MaPH = ? 
                       OR ph.MaKhachHang = ?
                       OR ph.HoTenKH LIKE ? 
                       OR kh.TenKH LIKE ? 
                       OR ph.Email LIKE ? 
                       OR kh.Email LIKE ? 
                       OR ph.SDT LIKE ? 
                       OR kh.SDT LIKE ?
                   ) ";
        }
        
        // Nếu có status, thêm điều kiện lọc trạng thái
        if ($status !== '') {
            $sql .= " AND ph.TinhTrang = ? ";
        }
        
        // Nếu có rating, thêm điều kiện lọc đánh giá sao
        if ($rating !== '') {
            $sql .= " AND ph.MucDoHaiLong = ? ";
        }
        
        // Nếu có lọc ngày, thêm điều kiện
        if ($dateStart !== '' && $dateEnd !== '') {
            $sql .= " AND ph.NgayPhanHoi BETWEEN ? AND ? ";
        }
        
        $sql .= " ORDER BY ph.NgayPhanHoi DESC";
        
        $stmt = $this->db->prepare($sql);

        if ($stmt === false) {
            die("Lỗi SQL: " . $this->db->error);
        }
        
        // Bind tham số động - sử dụng mảng để linh hoạt hơn
        $types = '';
        $params = [];
        
        // Nếu có keyword
        if ($keyword !== '') {
            $types .= 'iissssss';
            $params[] = &$searchId;
            $params[] = &$searchId;
            $params[] = &$searchKey;
            $params[] = &$searchKey;
            $params[] = &$searchKey;
            $params[] = &$searchKey;
            $params[] = &$searchKey;
            $params[] = &$searchKey;
        }
        
        // Nếu có status
        if ($status !== '') {
            $types .= 's';
            $params[] = &$status;
        }
        
        // Nếu có rating
        if ($rating !== '') {
            $types .= 'i';
            $params[] = &$rating;
        }
        
        // Nếu có date filter
        if ($dateStart !== '' && $dateEnd !== '') {
            $types .= 'ss';
            $params[] = &$dateStart;
            $params[] = &$dateEnd;
        }
        
        // Bind parameters nếu có
        if (!empty($params)) {
            array_unshift($params, $types);
            call_user_func_array(array($stmt, 'bind_param'), $params);
        }
        
        if (!$stmt->execute()) {
            die("Lỗi thực thi: " . $stmt->error);
        }
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>