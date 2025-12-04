<?php
class DichVu {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

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
}