<?php
// models/Doan.php
class Doan
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO doan (TenDoan, MaTruongDoan, SoNguoi, NgayDen, NgayDi, GhiChu)
            VALUES (?, ?, ?, NULL, NULL, ?)
        ");
        $stmt->bind_param(
            "siis",
            $data['TenDoan'],
            $data['MaTruongDoan'],
            $data['SoNguoi'],
            $data['GhiChu']
        );
        if (!$stmt->execute()) {
            return false;
        }
        return $this->db->insert_id;
    }
}