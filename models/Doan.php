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
        /* ============================================================
     *  V2 – TẠO ĐOÀN KHI ĐẶT PHÒNG TRỰC TIẾP
     *  Mọi giao dịch đều gắn với 1 đoàn,
     *  khách chính là Trưởng đoàn.
     * ============================================================
     */

    /**
     * Tạo đoàn cho booking:
     *  - TenDoan: cho phép truyền từ form, nếu không có thì lấy theo TenKH trưởng đoàn.
     *  - NgayDen/NgayDi: có thể lưu theo ngày đặt hoặc để null tùy nghiệp vụ.
     *
     * @param array $data [
     *    'TenDoan',
     *    'MaTruongDoan',
     *    'SoNguoi',
     *    'NgayDen' (Y-m-d) | null,
     *    'NgayDi'  (Y-m-d) | null,
     *    'GhiChu'
     * ]
     */
    public function v2_createForBooking(array $data): int
    {
        $sql = "
            INSERT INTO doan (TenDoan, MaTruongDoan, SoNguoi, NgayDen, NgayDi, GhiChu)
            VALUES (?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Doan::v2_createForBooking - prepare fail: " . $this->db->error);
        }

        $tenDoan     = $data['TenDoan']      ?? null;
        $maTruong    = (int)($data['MaTruongDoan'] ?? 0);
        $soNguoi     = (int)($data['SoNguoi'] ?? 0);
        $ngayDen     = $data['NgayDen']      ?? null;
        $ngayDi      = $data['NgayDi']       ?? null;
        $ghiChu      = $data['GhiChu']       ?? null;

        $stmt->bind_param(
            "siisss",
            $tenDoan,
            $maTruong,
            $soNguoi,
            $ngayDen,
            $ngayDi,
            $ghiChu
        );

        if (!$stmt->execute()) {
            throw new Exception("Doan::v2_createForBooking - execute fail: " . $stmt->error);
        }

        return (int)$this->db->insert_id;
    }
    public function createV2(array $data): ?int
{
    $sql = "
        INSERT INTO doan (TenDoan, MaTruongDoan, SoNguoi, NgayDen, NgayDi, GhiChu)
        VALUES (?, ?, ?, NULL, NULL, ?)
    ";

    if (!$stmt = $this->db->prepare($sql)) {
        error_log("Doan::createV2 prepare fail: " . $this->db->error);
        return null;
    }

    $tenDoan      = $data['TenDoan']      ?? null;
    $maTruongDoan = (int)($data['MaTruongDoan'] ?? 0);
    $soNguoi      = (int)($data['SoNguoi']      ?? 0);
    $ghiChu       = $data['GhiChu']       ?? null;

    $stmt->bind_param(
        "siis",
        $tenDoan,
        $maTruongDoan,
        $soNguoi,
        $ghiChu
    );

    if (!$stmt->execute()) {
        error_log("Doan::createV2 execute fail: " . $stmt->error);
        return null;
    }

    return (int)$this->db->insert_id;
}
/** ============================
 *  V2 — TẠO ĐOÀN MỚI KHÔNG ẢNH HƯỞNG HÀM CŨ
 * ============================ */
public function v2_create($data)
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
        throw new Exception("Doan::v2_create error: " . $stmt->error);
    }

    return (int)$this->db->insert_id;
}
}