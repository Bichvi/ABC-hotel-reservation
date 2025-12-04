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
                /* --------------------------------------------------- */

                $sql2 = "INSERT INTO chitietphanhoi (MaPhanHoi, MaNhanVien, NoiDungTraLoi, NgayTraLoi) 
                         VALUES (?, ?, ?, NOW())";
                $stmt2 = $this->db->prepare($sql2);
                $stmt2->bind_param("iis", $maPH, $maNV, $noiDungLuu);
                
                if (!$stmt2->execute()) {
                    throw new Exception("Lỗi lưu lịch sử");
                }
            }

            // Mọi thứ OK -> Lưu chính thức
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // Có lỗi -> Hoàn tác (Rollback)
            $this->db->rollback();
            return false;
        }
    }

    // --- HÀM TÌM KIẾM ĐA NĂNG (ĐÃ FIX LỖI TÌM MÃ SỐ + LỌC TRẠNG THÁI) ---
    public function searchPhanHoi_QL($keyword = '', $status = '') {
        $searchKey = "%{$keyword}%"; // Dùng cho tìm kiếm chuỗi (LIKE)
        
        // Xử lý riêng cho tìm kiếm mã số:
        // Nếu keyword là số (ví dụ "10"), ta sẽ tìm chính xác hoặc tìm trong chuỗi
        // Nếu keyword có chữ (ví dụ "PH10"), ta lọc bỏ chữ chỉ lấy số "10" để tìm
        $numberOnly = preg_replace('/[^0-9]/', '', $keyword);
        $searchId = $numberOnly !== '' ? $numberOnly : -1; // Nếu không có số thì gán -1 (không tìm ra gì)

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
        
        $sql .= " ORDER BY ph.NgayPhanHoi DESC";
        
        $stmt = $this->db->prepare($sql);

        if ($stmt === false) {
            die("Lỗi SQL: " . $this->db->error);
        }
        
        // Bind tham số động dựa vào điều kiện
        if ($keyword !== '' && $status !== '') {
            // Có cả keyword và status
            $stmt->bind_param("iisssssss", 
                $searchId, $searchId,        // Mã số (i)
                $searchKey, $searchKey,      // Tên (s)
                $searchKey, $searchKey,      // Email (s)
                $searchKey, $searchKey,      // SĐT (s)
                $status                      // Trạng thái (s)
            );
        } elseif ($keyword !== '') {
            // Chỉ có keyword
            $stmt->bind_param("iissssss", 
                $searchId, $searchId,        // Mã số (i)
                $searchKey, $searchKey,      // Tên (s)
                $searchKey, $searchKey,      // Email (s)
                $searchKey, $searchKey       // SĐT (s)
            );
        } elseif ($status !== '') {
            // Chỉ có status
            $stmt->bind_param("s", $status);
        }
        
        if (!$stmt->execute()) {
            die("Lỗi thực thi: " . $stmt->error);
        }
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>