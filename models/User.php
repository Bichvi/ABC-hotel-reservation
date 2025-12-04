<?php
class User {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll() {
        $sql = "
            SELECT tk.*, kh.TenKH, kh.Email, vt.TenVaiTro
            FROM taikhoan tk
            LEFT JOIN khachhang kh ON tk.MaKhachHang = kh.MaKhachHang
            LEFT JOIN vaitro vt ON tk.MaVaiTro = vt.MaVaiTro
            ORDER BY tk.MaTK DESC
        ";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }


    public function getById($id) {
        $stm = $this->db->prepare("
            SELECT tk.*, kh.TenKH, kh.Email, vt.TenVaiTro
            FROM taikhoan tk
            LEFT JOIN khachhang kh ON tk.MaKhachHang = kh.MaKhachHang
            LEFT JOIN vaitro vt ON tk.MaVaiTro = vt.MaVaiTro
            WHERE tk.MaTK = ?
        ");
        $stm->bind_param("i", $id);
        $stm->execute();
        return $stm->get_result()->fetch_assoc();
    }

 public function updateRole($id, $role, $mota) {
    $stm = $this->db->prepare("
        UPDATE taikhoan 
        SET MaVaiTro = ?, MoTaQuyen = ?
        WHERE MaTK = ?
    ");
    $stm->bind_param("isi", $role, $mota, $id);
    return $stm->execute();
}
public function search($k)
{
    $k = "%$k%";

    $sql = "
        SELECT tk.*, kh.TenKH, kh.Email, vt.TenVaiTro
        FROM taikhoan tk
        LEFT JOIN khachhang kh ON tk.MaKhachHang = kh.MaKhachHang
        LEFT JOIN vaitro vt ON tk.MaVaiTro = vt.MaVaiTro
        WHERE 
            tk.Username LIKE ?
            OR kh.TenKH LIKE ?
            OR kh.Email LIKE ?
        ORDER BY tk.MaTK DESC
    ";

    $stm = $this->db->prepare($sql);
    $stm->bind_param("sss", $k, $k, $k);
    $stm->execute();
    return $stm->get_result()->fetch_all(MYSQLI_ASSOC);
}
}