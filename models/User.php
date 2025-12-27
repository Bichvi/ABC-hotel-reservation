<?php
class User {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll() {
        $sql = "
            SELECT 
                tk.*,
                COALESCE(kh.TenKH, nv.TenNV) AS TenKH,
                COALESCE(kh.Email, nv.Email) AS Email,
                COALESCE(kh.SDT, nv.SDT) AS SDT,
                kh.CCCD,
                kh.DiaChi,
                nv.ChucVu,
                vt.TenVaiTro
            FROM taikhoan tk
            LEFT JOIN khachhang kh ON tk.MaKhachHang = kh.MaKhachHang
            LEFT JOIN nhanvien nv ON tk.MaNhanVien = nv.MaNhanVien
            LEFT JOIN vaitro vt ON tk.MaVaiTro = vt.MaVaiTro
            WHERE (tk.MaKhachHang IS NOT NULL OR tk.MaNhanVien IS NOT NULL OR tk.MaVaiTro = 1)
            ORDER BY tk.MaTK DESC
        ";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }


    public function getById($id) {
        $stm = $this->db->prepare("
            SELECT 
                tk.*,
                COALESCE(kh.TenKH, nv.TenNV) AS TenKH,
                COALESCE(kh.Email, nv.Email) AS Email,
                COALESCE(kh.SDT, nv.SDT) AS SDT,
                kh.CCCD,
                kh.DiaChi,
                kh.LoaiKhach,
                nv.ChucVu,
                vt.TenVaiTro
            FROM taikhoan tk
            LEFT JOIN khachhang kh ON tk.MaKhachHang = kh.MaKhachHang
            LEFT JOIN nhanvien nv ON tk.MaNhanVien = nv.MaNhanVien
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
        SELECT 
            tk.*,
            COALESCE(kh.TenKH, nv.TenNV) AS TenKH,
            COALESCE(kh.Email, nv.Email) AS Email,
            COALESCE(kh.SDT, nv.SDT) AS SDT,
            kh.CCCD,
            kh.DiaChi,
            nv.ChucVu,
            vt.TenVaiTro
        FROM taikhoan tk
        LEFT JOIN khachhang kh ON tk.MaKhachHang = kh.MaKhachHang
        LEFT JOIN nhanvien nv ON tk.MaNhanVien = nv.MaNhanVien
        LEFT JOIN vaitro vt ON tk.MaVaiTro = vt.MaVaiTro
        WHERE 
            tk.Username LIKE ?
            OR COALESCE(kh.TenKH, nv.TenNV) LIKE ?
            OR COALESCE(kh.Email, nv.Email) LIKE ?
        ORDER BY tk.MaTK DESC
    ";

    $stm = $this->db->prepare($sql);
    $stm->bind_param("sss", $k, $k, $k);
    $stm->execute();
    return $stm->get_result()->fetch_all(MYSQLI_ASSOC);
}

}