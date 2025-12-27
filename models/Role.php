<?php
class Role {

    private $db;

    public function __construct(){
        $this->db = Database::getConnection();
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM vaitro")->fetch_all(MYSQLI_ASSOC);
    }
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM vaitro WHERE MaVaiTro = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}