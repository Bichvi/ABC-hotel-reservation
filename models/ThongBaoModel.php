<?php
class ThongBaoModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    // Hàm lưu lịch sử gửi
    public function luuThongBao($tieuDe, $noiDung, $danhSachEmail, $nguoiGui) {
        // Chuyển mảng email thành chuỗi để lưu vào 1 dòng (VD: a@gmail.com, b@gmail.com)
        $emails = is_array($danhSachEmail) ? implode(', ', $danhSachEmail) : $danhSachEmail; 
        
        $sql = "INSERT INTO thong_bao (tieu_de, noi_dung, danh_sach_nhan, nguoi_gui) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssss", $tieuDe, $noiDung, $emails, $nguoiGui);
        return $stmt->execute();
    }

    // Hàm lấy danh sách lịch sử (Sắp xếp mới nhất lên đầu)
    public function getAll() {
        $sql = "SELECT * FROM thong_bao ORDER BY thoi_gian DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}