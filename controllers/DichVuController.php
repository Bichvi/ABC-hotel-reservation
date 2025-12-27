<?php
require_once __DIR__ . '/../models/DichVu.php';

class DichvuController extends Controller
{
    private $model;

    public function __construct()
    {
        parent::__construct();

        // Chỉ cho phép nhân viên dịch vụ
        $this->requireRole([4]);

        $this->model = new DichVu();
    }


    /* ============================================================
        DANH SÁCH DỊCH VỤ
    ============================================================ */
    public function quanLyDichVu()
    {
        $services = $this->model->getAll();

        $this->view("dichvu/quanly", [
            "services" => $services,
            "success"  => ($_GET['success'] ?? ""),
            "error"    => ($_GET['error'] ?? "")
        ]);
    }



    /* ============================================================
        👉 FORM THÊM DỊCH VỤ
    ============================================================ */
    public function them()
    {
        $this->view("dichvu/them", [
            "errors"  => [],
            "success" => "",
            "old"     => []
        ]);
    }



    /* ============================================================
        👉 LƯU DỊCH VỤ MỚI
    ============================================================ */
    public function luuThem()
    {
        $errors = [];
        $data = [];

        $data['TenDichVu'] = trim($_POST['TenDichVu'] ?? "");
        $data['GiaDichVu'] = trim($_POST['GiaDichVu'] ?? "");
        $data['MoTa']      = trim($_POST['MoTa'] ?? "");
        $data['TrangThai'] = $_POST['TrangThai'] ?? "";
        $data['HinhAnh']   = "";

        // Validate dữ liệu
        if (!$this->model->validate($data, $errors)) {
            return $this->view("dichvu/them", [
                "errors"  => $errors,
                "success" => "",
                "old"     => $data
            ]);
        }

        /* -----------------------------
           UPLOAD ẢNH
        ----------------------------- */
        if (!empty($_FILES['HinhAnh']['name'])) {

            $file = $_FILES['HinhAnh'];
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allow = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allow)) {
                $errors[] = "Chỉ cho phép JPG / PNG / WEBP.";
                return $this->view("dichvu/them", [
                    "errors"  => $errors,
                    "success" => "",
                    "old"     => $data
                ]);
            }

            $newName = "dv_" . time() . "_" . rand(100, 999) . "." . $ext;

            $folder = __DIR__ . "/../public/uploads/dichvu/";
            if (!is_dir($folder)) mkdir($folder, 0777, true);

            $target = $folder . $newName;

            if (!move_uploaded_file($file['tmp_name'], $target)) {
                $errors[] = "Không thể upload hình ảnh.";
                return $this->view("dichvu/them", [
                    "errors"  => $errors,
                    "success" => "",
                    "old"     => $data
                ]);
            }

            $data['HinhAnh'] = $newName;

        } else {
            $data['HinhAnh'] = "default.jpg";
        }

        // Lưu database
        if (!$this->model->saveNew($data)) {
            return $this->view("dichvu/them", [
                "errors"  => ["Không thể lưu dịch vụ!"],
                "success" => "",
                "old"     => $data
            ]);
        }

        return $this->view("dichvu/them", [
            "errors"  => [],
            "success" => "Thêm dịch vụ thành công!",
            "old"     => []
        ]);
    }



    /* ============================================================
        👉 FORM SỬA
    ============================================================ */
    public function sua()
    {
        $id = $_GET['id'] ?? 0;

        $dv = $this->model->getById($id);
        if (!$dv) die("Dịch vụ không tồn tại!");

        $this->view("dichvu/sua", [
            "dv"      => $dv,
            "errors"  => [],
            "success" => ""
        ]);
    }



    /* ============================================================
        👉 LƯU SỬA
    ============================================================ */
public function luuSua()
{
    $id  = $_POST['MaDichVu'] ?? 0;
    $old = $this->model->getById($id);

    if (!$old) {
        die("Không tìm thấy dịch vụ!");
    }

    $errors = [];

    /* ============================
       LẤY DỮ LIỆU
    ============================ */
    $data = [
        "MaDichVu"  => $id,
        "TenDichVu" => trim($_POST['TenDichVu']),
        "GiaDichVu" => trim($_POST['GiaDichVu']),
        "MoTa"      => trim($_POST['MoTa']),
        "TrangThai" => $_POST['TrangThai'] ?? "",
        "HinhAnh"   => $old['HinhAnh']
    ];

    /* ============================
       KIỂM TRA ENUM
    ============================ */
    $allowed = ["HoatDong", "NgungBan", "BaoTri"];

    if (!in_array($data['TrangThai'], $allowed)) {
        $errors[] = "Trạng thái không hợp lệ.";
    }

    /* ============================
       VALIDATE INPUT
    ============================ */
    if (!$this->model->validateUpdate($data, $errors)) {
        return $this->view("dichvu/sua", [
            "dv"      => $data,
            "errors"  => $errors,
            "success" => ""
        ]);
    }

    /* ============================
       UPLOAD ẢNH (NẾU CÓ)
    ============================ */
    if (!empty($_FILES['HinhAnh']['name'])) {

        $folder = __DIR__ . "/../public/uploads/dichvu/";
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $ext = strtolower(pathinfo($_FILES['HinhAnh']['name'], PATHINFO_EXTENSION));
        $allowedExt = ['jpg','jpeg','png','webp'];

        if (!in_array($ext, $allowedExt)) {
            $errors[] = "Chỉ chấp nhận JPG, PNG, WEBP.";
            return $this->view("dichvu/sua", [
                "dv"      => $data,
                "errors"  => $errors,
                "success" => ""
            ]);
        }

        $newName = "dv_" . time() . "_" . rand(100,999) . "." . $ext;

        if (move_uploaded_file($_FILES['HinhAnh']['tmp_name'], $folder.$newName)) {
            $data['HinhAnh'] = $newName;
        } else {
            $errors[] = "Không thể upload hình.";
            return $this->view("dichvu/sua", [
                "dv"      => $data,
                "errors"  => $errors,
                "success" => ""
            ]);
        }
    }

    /* ============================
       UPDATE DB
    ============================ */
    if (!$this->model->update($data)) {
        return $this->view("dichvu/sua", [
            "dv"      => $data,
            "errors"  => ["Không thể cập nhật dịch vụ."],
            "success" => ""
        ]);
    }

    header("Location: index.php?controller=dichvu&action=quanLyDichVu&success=Dịch vụ đã được cập nhật thành công!");
    exit;
}


    /* ============================================================
        👉 XOÁ DỊCH VỤ
    ============================================================ */
   public function xoa()
{
    $id = $_GET['id'] ?? 0;

    $this->model->delete($id);

    header("Location: index.php?controller=dichvu&action=quanLyDichVu&success=Dịch vụ đã được xóa thành công!");
    exit;
}
/* ============================================================
   📌 UC: KIỂM TRA PHÒNG TRẢ
   Actor: Nhân viên dịch vụ
   Model sử dụng: Phong
=============================================================== */

public function kiemTraPhong()
{
    require_once __DIR__ . '/../models/Phong.php';
    $phongModel = new Phong();

    $mode = $_GET['mode'] ?? "list";

    /* ===============================
       1) MODE: DANH SÁCH PHÒNG STAYED
       =============================== */
    if ($mode === "list") {

        $rooms = $phongModel->getRoomsStayed();   // chỉ lấy phòng Stayed

        return $this->view("dichvu/kiemtraphong", [
            "mode"   => "list",
            "rooms"  => $rooms,
            "errors" => [],
            "success"=> ""
        ]);
    }

    /* ===============================
       2) MODE: FORM KIỂM TRA PHÒNG
       =============================== */
    if ($mode === "form") {

        $id   = intval($_GET['id'] ?? 0);
        $room = $phongModel->getRoomStayedById($id);

        if (!$room) {
            return $this->view("dichvu/kiemtraphong", [
                "mode"   => "list",
                "rooms"  => $phongModel->getRoomsStayed(),
                "errors" => ["Không tìm thấy phòng hoặc phòng không ở trạng thái Stayed!"],
                "success"=> ""
            ]);
        }

        return $this->view("dichvu/kiemtraphong", [
            "mode"   => "form",
            "room"   => $room,
            "errors" => [],
            "success"=> ""
        ]);
    }
}



/* ============================================================
   📌 LƯU KẾT QUẢ KIỂM TRA PHÒNG TRẢ
=============================================================== */

public function luuKiemTraPhong()
{
    require_once __DIR__ . '/../models/Phong.php';
    $phongModel = new Phong();

    $maPhong = intval($_POST['MaPhong'] ?? 0);

    // Lấy lại room cũ
    $room = $phongModel->getRoomStayedById($maPhong);
    if (!$room) {
        return $this->view("dichvu/kiemtraphong", [
            "mode"   => "list",
            "rooms"  => $phongModel->getRoomsStayed(),
            "errors" => ["Không tìm thấy phòng hoặc phòng không ở trạng thái Stayed!"],
            "success"=> ""
        ]);
    }

    // DATA – chỉ còn TinhTrangPhong
    $data = [
        "MaPhong"        => $maPhong,
        "TinhTrangPhong" => trim($_POST['TinhTrangPhong'] ?? "")
    ];

    // Kiểm tra giá trị hợp lệ theo enum bạn dùng cho UC này
    $allowedTinhTrang = ['Tot', 'HuHaiNhe', 'HuHaiNang'];
    $errors = [];

    if (!in_array($data['TinhTrangPhong'], $allowedTinhTrang, true)) {
        $errors[] = "Tình trạng phòng không hợp lệ.";
    }

    // VALIDATE chung trong model (chỉ check rỗng / thiếu)
    if (!$phongModel->validateRoomCheckSimple($data, $errors)) {

        return $this->view("dichvu/kiemtraphong", [
            "mode"   => "form",
            "room"   => $room,
            "errors" => $errors,
            "success"=> ""
        ]);
    }

    // UPDATE – chỉ cập nhật TinhTrangPhong, KHÔNG đụng TrangThai
    $ok = $phongModel->updateAfterCheckSimple(
        $maPhong,
        $data['TinhTrangPhong']
    );

    if (!$ok) {
        return $this->view("dichvu/kiemtraphong", [
            "mode"   => "form",
            "room"   => $room,
            "errors" => ["Không thể cập nhật dữ liệu phòng. Vui lòng thử lại."],
            "success"=> ""
        ]);
    }

    // DONE — về danh sách Stayed
    return $this->view("dichvu/kiemtraphong", [
        "mode"   => "list",
        "rooms"  => $phongModel->getRoomsStayed(),
        "errors" => [],
        "success"=> "Cập nhật tình trạng phòng thành công!"
    ]);
}}