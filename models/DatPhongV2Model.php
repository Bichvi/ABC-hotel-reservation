<?php
class DatPhongV2Model
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Dummy function để tránh lỗi tiếp theo
    public function getAvailableRooms($data)
    {
        return [];
    }

    public function getDichVu()
    {
        return [];
    }

    public function xuLyDatPhongV2($data)
    {
        return [
            'success' => false,
            'message' => '',
            'errors'  => ['Model V2 chưa triển khai'],
        ];
    }
}