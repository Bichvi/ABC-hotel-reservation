<?php
class Role {

    private $db;

    public function __construct(){
        $this->db = Database::getConnection();
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM vaitro")->fetch_all(MYSQLI_ASSOC);
    }
}