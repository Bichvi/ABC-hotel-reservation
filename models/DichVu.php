<?php
class DichVu {
    private $db;

    // ⚠️ FIX: thêm biến đếm lỗi để tránh undefined property
    private int $errorCount = 0;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /* ============================  
        ❗ GIỮ NGUYÊN HÀM CŨ  
    ============================ */
    public function getActive() {
        $sql = "SELECT * FROM dichvu WHERE TrangThai = 'HoatDong'";
        $res = $this->db->query($sql);
        $list = [];
        while ($row = $res->fetch_assoc()) {
            $list[$row['MaDichVu']] = $row;
        }
        return $list;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM dichvu WHERE MaDichVu = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }



    /* ============================  
        📌 CÁC HÀM MỚI BỔ SUNG  
    ============================ */

    /** Kiểm tra tên dịch vụ đã tồn tại chưa */
    public function isNameExists($tenDV)
    {
        $stmt = $this->db->prepare("SELECT MaDichVu FROM dichvu WHERE TenDichVu = ?");
        $stmt->bind_param("s", $tenDV);
        $stmt->execute();
        $rs = $stmt->get_result();
        return $rs->num_rows > 0;
    }

    /** Validate thông tin trước khi lưu */
    public function validate($data, &$errors)
    {
        if (empty($data['TenDichVu'])) {
            $errors[] = "Tên dịch vụ không được để trống.";
        }

        if (!isset($data['GiaDichVu']) || $data['GiaDichVu'] === "") {
            $errors[] = "Giá dịch vụ không được để trống.";
        } elseif (!is_numeric($data['GiaDichVu']) || $data['GiaDichVu'] < 0) {
            $errors[] = "Giá dịch vụ phải là số ≥ 0.";
        }

        if (empty($data['TrangThai'])) {
            $errors[] = "Vui lòng chọn trạng thái dịch vụ.";
        }

        // Không kiểm tra trùng khi edit, nhưng đây là UC thêm mới → OK
        if ($this->isNameExists($data['TenDichVu'])) {
            $errors[] = "Tên dịch vụ đã tồn tại trong hệ thống.";
        }

        return empty($errors);
    }


    /** Lưu dịch vụ mới vào database */
    public function saveNew($data)
    {
        try {
            $sql = "INSERT INTO dichvu (TenDichVu, GiaDichVu, MoTa, TrangThai, HinhAnh)
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new Exception("Không thể prepare SQL");
            }

            $stmt->bind_param(
                "sisss",
                $data['TenDichVu'],
                $data['GiaDichVu'],
                $data['MoTa'],
                $data['TrangThai'],
                $data['HinhAnh']
            );

            if (!$stmt->execute()) {
                throw new Exception("Không thể execute SQL");
            }

            return true;

        } catch (Exception $e) {

            // ⛔ FIX: tránh lỗi undefined property
            $this->errorCount++;

            // Log khi lỗi >= 3 lần
            if ($this->errorCount >= 3) {
                $this->logError("Lỗi thêm dịch vụ: " . $e->getMessage());
            }

            return false;
        }
    }


    /** Ghi log lỗi */
    private function logError($msg)
    {
        $file = __DIR__ . "/../logs/dichvu_error.log";
        $time = date("Y-m-d H:i:s");
        file_put_contents($file, "[$time] $msg\n", FILE_APPEND);
    }
        /* Lấy tất cả dịch vụ (phục vụ trang danh sách) */
    public function getAll()
    {
        $sql = "SELECT * FROM dichvu ORDER BY MaDichVu DESC";
        $res = $this->db->query($sql);
        $list = [];

        while ($row = $res->fetch_assoc()) {
            $list[] = $row;
        }

        return $list;
    }
        public function isNameExistsOther($tenDV, $id)
    {
        $stmt = $this->db->prepare(
            "SELECT MaDichVu 
             FROM dichvu 
             WHERE TenDichVu = ? AND MaDichVu != ?"
        );

        $stmt->bind_param("si", $tenDV, $id);
        $stmt->execute();
        $rs = $stmt->get_result();

        return $rs->num_rows > 0;
    }
        public function validateUpdate($data, &$errors)
    {
        if (empty($data['TenDichVu'])) {
            $errors[] = "Tên dịch vụ không được để trống.";
        }

        if (!isset($data['GiaDichVu']) || $data['GiaDichVu'] === "") {
            $errors[] = "Giá dịch vụ không được để trống.";
        } elseif (!is_numeric($data['GiaDichVu']) || $data['GiaDichVu'] < 0) {
            $errors[] = "Giá dịch vụ phải ≥ 0.";
        }

        if (empty($data['TrangThai'])) {
            $errors[] = "Vui lòng chọn trạng thái.";
        }

        // Kiểm tra trùng tên (loại trừ chính nó)
        if ($this->isNameExistsOther($data['TenDichVu'], $data['MaDichVu'])) {
            $errors[] = "Tên dịch vụ đã tồn tại ở dịch vụ khác.";
        }

        return empty($errors);
    }
        public function update($data)
    {
        try {
            $sql = "UPDATE dichvu 
                    SET TenDichVu = ?, 
                        GiaDichVu = ?, 
                        MoTa = ?, 
                        TrangThai = ?, 
                        HinhAnh = ?
                    WHERE MaDichVu = ?";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new Exception("Không thể prepare update");
            }

            $stmt->bind_param(
                "sisssi",
                $data['TenDichVu'],
                $data['GiaDichVu'],
                $data['MoTa'],
                $data['TrangThai'],
                $data['HinhAnh'],
                $data['MaDichVu']
            );

            if (!$stmt->execute()) {
                throw new Exception("Không thể execute update");
            }

            return true;

        } catch (Exception $e) {
            $this->errorCount++;

            if ($this->errorCount >= 3) {
                $this->logError("Lỗi cập nhật dịch vụ: " . $e->getMessage());
            }

            return false;
        }
    }
    public function delete($id)
{
    try {
        // Lấy dịch vụ để biết tên hình cũ
        $dv = $this->getById($id);
        if (!$dv) return false;

        $stmt = $this->db->prepare("DELETE FROM dichvu WHERE MaDichVu = ?");
        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            throw new Exception("Không thể xóa dịch vụ!");
        }

        // Xóa ảnh nếu không phải ảnh default
        if (!empty($dv['HinhAnh']) && $dv['HinhAnh'] !== 'default.jpg') {
            $path = __DIR__ . "/../public/uploads/dichvu/" . $dv['HinhAnh'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        return true;

    } catch (Exception $e) {
        $this->errorCount++;

        if ($this->errorCount >= 3) {
            $this->logError("Lỗi khi xóa dịch vụ: " . $e->getMessage());
        }

        return false;
    }
}
    public function tinhTongTienDichVu($maGiaoDich)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT SUM(ct.SoLuong * dv.GiaBan) AS Tong
            FROM chitietdichvu ct
            JOIN dichvu dv ON ct.MaDichVu = dv.MaDichVu
            WHERE ct.MaGiaoDich = ?
        ");
        $stmt->bind_param("i", $maGiaoDich);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        return (float)($row['Tong'] ?? 0);
    }

    /* OPTIONAL – dùng khi sửa dịch vụ */
    public function updateSoLuong($maGD, $maDV, $soLuong)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE chitietdichvu 
            SET SoLuong = ?
            WHERE MaGiaoDich = ? AND MaDichVu = ?
        ");
        $stmt->bind_param("iii", $soLuong, $maGD, $maDV);
        $stmt->execute();
    }

    public function removeDichVu($maGD, $maDV)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            DELETE FROM chitietdichvu 
            WHERE MaGiaoDich = ? AND MaDichVu = ?
        ");
        $stmt->bind_param("ii", $maGD, $maDV);
        $stmt->execute();
    }
}