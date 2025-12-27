<?php

class KhachhangController extends Controller
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->user = Auth::user();
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $this->requireRole(['KhachHang']);
        $this->view('dashboard/khachhang', ['user' => $this->user]);
    }

    /**
     * ĐẶT PHÒNG ONLINE 4 BƯỚC TRÊN 1 TRANG:
     *
     * 1. Lọc phòng → hiển thị danh sách phòng
     * 2. Nhấn “Đặt phòng” → hiển thị form điền thông tin
     * 3. Nhấn “Xem tóm tắt” → hiển thị tóm tắt
     * 4. Nhấn “Xác nhận đặt phòng” → lưu DB
     */
    public function datPhongOnline()
    {
        $this->requireRole(['KhachHang']);

        $phongModel = new Phong();
        $gdModel    = new GiaoDich();
        $ctgdModel  = new ChiTietGiaoDich();

        // DATA ĐỂ RENDER VIEW
        $errors         = [];
        $success        = null;
        $rooms          = [];
        $selectedRoom   = null;
        $bookingForm    = [];
        $summary        = null;

        // Nhận action
        $btn = $_POST['btn_action'] ?? '';

        // Xử lý filter
        if ($btn === 'reset') {
            $filter = [
                'loai_phong' => '',
                'view_phong' => '',
                'so_khach'   => null,
                'gia_goi_y'  => null,
            ];
        } else {
            $filter = [
                'loai_phong' => $_POST['loai_phong'] ?? '',
                'view_phong' => $_POST['view_phong'] ?? '',
                'so_khach'   => (isset($_POST['so_khach']) && $_POST['so_khach'] !== '')
                    ? (int)$_POST['so_khach']
                    : null,
                'gia_goi_y'  => (isset($_POST['gia_goi_y']) && $_POST['gia_goi_y'] !== '')
                    ? (int)$_POST['gia_goi_y']
                    : null,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | BƯỚC 1: TÌM PHÒNG
        |--------------------------------------------------------------------------
        */
        if ($btn === 'filter') {
            try {
                $rooms = $phongModel->searchForOnlineBooking($filter);
                if (empty($rooms)) {
                    $errors[] = "Không có phòng nào phù hợp tiêu chí!";
                }
            } catch (\Throwable $e) {
                $errors[] = "Lỗi tải danh sách phòng. Hãy thử lại!";
            }
        }

        /*
        |--------------------------------------------------------------------------
        | BƯỚC 2: CHỌN PHÒNG
        |--------------------------------------------------------------------------
        */
        if ($btn === 'choose_room') {

            $roomId = (int)($_POST['room_id'] ?? 0);

            // Lấy lại danh sách phòng theo filter
            try {
                $rooms = $phongModel->searchForOnlineBooking($filter);
            } catch (\Throwable $e) {
                $rooms = [];
            }

            // Chỉ giữ lại phòng vừa chọn (ẩn các phòng khác)
            if (!empty($rooms)) {
                $rooms = array_filter($rooms, function ($r) use ($roomId) {
                    return (int)$r['MaPhong'] === $roomId;
                });
            }

            // Lấy chi tiết phòng chọn
            $selectedRoom = $phongModel->getById($roomId);
            if (!$selectedRoom) {
                $errors[] = "Không tìm thấy phòng!";
            }

            // Reset form & summary
            $bookingForm = [
                'ten_khach' => '',
                'cccd'      => '',
                'sdt'       => '',
                'email'     => '',
                'so_nguoi'  => 1,
            ];
            $summary = null;
        }

        /*
        |--------------------------------------------------------------------------
        | BƯỚC 3: XEM TÓM TẮT
        |--------------------------------------------------------------------------
        */
        if ($btn === 'review_booking') {

            $roomId = (int)($_POST['room_id'] ?? 0);

            // Lấy lại danh sách phòng theo filter
            try {
                $rooms = $phongModel->searchForOnlineBooking($filter);
            } catch (\Throwable $e) {
                $rooms = [];
            }

            // Chỉ giữ lại phòng đang đặt
            if (!empty($rooms)) {
                $rooms = array_filter($rooms, function ($r) use ($roomId) {
                    return (int)$r['MaPhong'] === $roomId;
                });
            }

            // Lấy chi tiết phòng
            $selectedRoom = $phongModel->getById($roomId);
            if (!$selectedRoom) {
                $errors[] = "Không tìm thấy phòng!";
            }

            // Lấy form
            $bookingForm = $_POST['booking'] ?? [];

            // Validate dữ liệu nhập (regex)
            if (empty($bookingForm['ten_khach'])) {
                $errors[] = "Vui lòng nhập tên khách!";
            }
            if (empty($bookingForm['cccd']) || !preg_match('/^[0-9]{9,12}$/', $bookingForm['cccd'])) {
                $errors[] = "CMND/CCCD không hợp lệ!";
            }
            if (!empty($bookingForm['email']) && !filter_var($bookingForm['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email không hợp lệ!";
            }
            if (!empty($bookingForm['sdt']) && !preg_match('/^(0|\+84)\d{8,10}$/', $bookingForm['sdt'])) {
                $errors[] = "Số điện thoại không hợp lệ!";
            }
            if ((int)($bookingForm['so_nguoi'] ?? 0) < 1) {
                $errors[] = "Số người phải >= 1";
            }
            if ($selectedRoom && (int)$bookingForm['so_nguoi'] > (int)$selectedRoom['SoKhachToiDa']) {
                $errors[] = "Số khách vượt quá sức chứa phòng!";
            }

            if (empty($errors) && $selectedRoom) {
                // Tạm tính: 1 đêm
                $gia       = (int)$selectedRoom['Gia'];
                $soDem     = 1;
                $thanhTien = $gia * $soDem;

                $summary = [
                    'ma_kh'       => $this->user['MaKhachHang'],
                    'ten_kh'      => $this->user['Username'],
                    'so_dem'      => $soDem,
                    'tong_tien'   => $thanhTien,
                    'khuyen_mai'  => 0,
                    'phong' => [
                        'MaPhong'   => $selectedRoom['MaPhong'],
                        'SoPhong'   => $selectedRoom['SoPhong'],
                        'LoaiPhong' => $selectedRoom['LoaiPhong'],
                        'GiaPhong'  => $gia,
                        'ThanhTien' => $thanhTien,
                        'TenKhach'  => $bookingForm['ten_khach'],
                        'CCCD'      => $bookingForm['cccd'],
                        'SDT'       => $bookingForm['sdt'] ?? '',
                        'Email'     => $bookingForm['email'] ?? '',
                        'SoNguoi'   => $bookingForm['so_nguoi'],
                    ]
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | QUAY LẠI CHỈNH SỬA TỪ TÓM TẮT
        |--------------------------------------------------------------------------
        */
        if ($btn === 'back_to_form') {

            $roomId = (int)($_POST['room_id'] ?? 0);

            // Lấy lại danh sách phòng
            try {
                $rooms = $phongModel->searchForOnlineBooking($filter);
            } catch (\Throwable $e) {
                $rooms = [];
            }

            // Chỉ giữ lại phòng đang đặt
            if (!empty($rooms)) {
                $rooms = array_filter($rooms, function ($r) use ($roomId) {
                    return (int)$r['MaPhong'] === $roomId;
                });
            }

            $selectedRoom = $phongModel->getById($roomId);
            $bookingForm  = $_POST['booking'] ?? [];
            $summary      = null;
        }

        /*
        |--------------------------------------------------------------------------
        | HỦY ĐẶT PHÒNG (TỪ FORM NHẬP THÔNG TIN)
        |--------------------------------------------------------------------------
        */
        if ($btn === 'cancel_booking') {

            // Có thể load lại list phòng theo filter nếu muốn hiển thị lại
            try {
                $rooms = $phongModel->searchForOnlineBooking($filter);
            } catch (\Throwable $e) {
                $rooms = [];
            }

            $selectedRoom = null;
            $bookingForm  = [];
            $summary      = null;
        }

        /*
        |--------------------------------------------------------------------------
        | BƯỚC 4: XÁC NHẬN ĐẶT PHÒNG
        |--------------------------------------------------------------------------
        */
        if ($btn === 'confirm_booking') {

            $roomId      = (int)($_POST['room_id'] ?? 0);
            $selectedRoom= $phongModel->getById($roomId);
            $bookingForm = $_POST['booking'] ?? [];

            if (!$selectedRoom) {
                $errors[] = "Không tìm thấy phòng!";
            }

            if (empty($errors)) {
                $db = Database::getConnection();
                $db->begin_transaction();

                try {
                    // 1) Tạo giao dịch
                    $maGD = $gdModel->createBooking([
                        'MaKhachHang'          => $this->user['MaKhachHang'],
                        'LoaiGiaoDich'         => 'DatPhong', // đúng enum trong DB
                        'TongTien'             => (float)$selectedRoom['Gia'],
                        'TrangThai'            => GiaoDich::STATUS_BOOKED,
                        'PhuongThucThanhToan'  => 'ChuaThanhToan',
                        'GhiChu'               => 'Đặt phòng online',
                    ]);

                    // 2) Chi tiết giao dịch
                    // (ở DB chitietgiaodich KHÔNG có TenKhach, CCCD → chỉ lưu các trường có thật)
                    $ngayNhan = date('Y-m-d');
                    $ngayTra  = date('Y-m-d', strtotime('+1 day'));

                    $ctgdModel->createDetail([
                        'MaGiaoDich'     => $maGD,
                        'MaPhong'        => $selectedRoom['MaPhong'],
                        'NgayNhanDuKien' => $ngayNhan,
                        'NgayTraDuKien'  => $ngayTra,
                        'SoNguoi'        => (int)$bookingForm['so_nguoi'],
                        'ThanhTien'      => (float)$selectedRoom['Gia'],
                        'TrangThai'      => ChiTietGiaoDich::STATUS_BOOKED,
                    ]);

                    // 3) Cập nhật trạng thái phòng
                    $phongModel->updateTrangThai($roomId, 'Booked');

                    $db->commit();

                    $success = "Đặt phòng thành công! Mã giao dịch của bạn: #$maGD";
                    $rooms          = [];
                    $selectedRoom   = null;
                    $bookingForm    = [];
                    $summary        = null;

                } catch (\Throwable $ex) {
                    $db->rollback();
                    $errors[] = "Lỗi SQL: " . $ex->getMessage();
                    error_log("ERR_DAT_PHONG: " . $ex->getMessage());
                }
            }
        }

        // Render view
        $this->view('khachhang/dat_phong_online', [
            'user'         => $this->user,
            'errors'       => $errors,
            'success'      => $success,
            'filter'       => $filter,
            'rooms'        => $rooms,
            'selectedRoom' => $selectedRoom,
            'bookingForm'  => $bookingForm,
            'summary'      => $summary,
        ]);
    }
    private function getCustomerFull()
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user || empty($user['MaKhachHang'])) {
            return null;
        }

        require_once "models/KhachHang.php";
        $khModel = new KhachHang();
        $kh = $khModel->getById($user['MaKhachHang']);

        return array_merge($user, $kh ?? []);
    }
        // =============================
    // 2) HỦY ĐẶT PHÒNG ONLINE
    // =============================
   public function huyDatPhong()
    {
        $this->requireRole(['KhachHang']);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $errors  = [];
        $success = null;

        $maKH = $this->user['MaKhachHang'];
        $db   = Database::getConnection();

        /* ================== LOAD BOOKINGS ================== */
        $sql = "
            SELECT 
                gd.MaGiaoDich,
                ct.MaCTGD,
                ct.MaPhong,
                p.SoPhong,
                p.LoaiPhong,
                ct.NgayNhanDuKien,
                ct.NgayTraDuKien,
                ct.TrangThai
            FROM giaodich gd
            JOIN chitietgiaodich ct ON ct.MaGiaoDich = gd.MaGiaoDich
            JOIN phong p ON p.MaPhong = ct.MaPhong
            WHERE gd.MaKhachHang = ?
            AND ct.TrangThai = 'Booked'
            ORDER BY gd.MaGiaoDich DESC, ct.MaCTGD DESC
        ";

        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $maKH);
        $stmt->execute();
        $bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        /* ================== HANDLE CANCEL ================== */
        if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["btn_action"] ?? '') === "cancel_room") {

            $maCTGD  = (int)($_POST['ma_ctgd'] ?? 0);
            $maPhong = (int)($_POST['ma_phong'] ?? 0);

            if ($maCTGD <= 0 || $maPhong <= 0) {
                $errors[] = "Dữ liệu hủy không hợp lệ!";
            }

            /* ===== LẤY NGÀY NHẬN ===== */
            $stmtDate = $db->prepare("
                SELECT NgayNhanDuKien, MaGiaoDich 
                FROM chitietgiaodich 
                WHERE MaCTGD = ?
            ");
            $stmtDate->bind_param("i", $maCTGD);
            $stmtDate->execute();
            $row = $stmtDate->get_result()->fetch_assoc();

            if (!$row) {
                $errors[] = "Không tìm thấy thông tin đặt phòng!";
            } else {
                date_default_timezone_set("Asia/Ho_Chi_Minh");

                $today     = new DateTime(date("Y-m-d"));
                $ngayNhan  = new DateTime($row['NgayNhanDuKien']);
                $maGD      = (int)$row['MaGiaoDich'];

                if ($today >= $ngayNhan) {
                    $errors[] = "Không thể hủy vì đã đến hoặc quá ngày check-in!";
                }
            }

            /* ================== EXECUTE CANCEL ================== */
            if (empty($errors)) {

                $db->begin_transaction();

                try {

                    /* HỦY CTGD */
                    $db->query("
                        UPDATE chitietgiaodich 
                        SET TrangThai = 'DaHuy' 
                        WHERE MaCTGD = $maCTGD
                    ");

                    /* TRẢ PHÒNG VỀ TRỐNG */
                    $db->query("
                        UPDATE phong 
                        SET TrangThai = 'Trong' 
                        WHERE MaPhong = $maPhong
                    ");

                    /* KIỂM TRA CÒN PHÒNG BOOKED KHÔNG (FIX LOGIC) */
                    $sqlCheck = "
                        SELECT COUNT(*) AS c
                        FROM chitietgiaodich
                        WHERE TrangThai = 'Booked'
                        AND MaGiaoDich = $maGD
                    ";

                    $check = (int)$db->query($sqlCheck)->fetch_assoc()['c'];

                    /* NẾU KHÔNG CÒN PHÒNG BOOKED → HỦY GIAO DỊCH */
                    if ($check === 0) {
                        $db->query("
                            UPDATE giaodich 
                            SET TrangThai = 'DaHuy'
                            WHERE MaGiaoDich = $maGD
                        ");
                    }

                    $db->commit();
                    $success = "Hủy đặt phòng thành công!";

                } catch (\Throwable $e) {
                    $db->rollback();
                    $errors[] = "Lỗi hệ thống: " . $e->getMessage();
                }
            }
        }

        /* ================== RENDER VIEW ================== */
        return $this->view("khachhang/huy_dat_phong", [
            "bookings" => $bookings,
            "errors"   => $errors,
            "success"  => $success
        ]);
    }

    // =============================
    // 3) CẬP NHẬT THÔNG TIN CÁ NHÂN
    // =============================
    public function capNhatThongTin()
    {
        $this->requireRole(['KhachHang']);
        $kh = new KhachHang();

        $errors = [];
        $success = null;

        // Lấy thông tin từ DB
        $profile = $kh->getById($this->user['MaKhachHang']);

        // Bind vào form
        $form = [
            'TenKH'  => $profile['TenKH']  ?? '',
            'SDT'    => $profile['SDT']    ?? '',
            'Email'  => $profile['Email']  ?? '',
            'DiaChi' => $profile['DiaChi'] ?? '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email  = trim($_POST['Email'] ?? '');
            $sdt    = trim($_POST['SDT'] ?? '');
            $diachi = trim($_POST['DiaChi'] ?? '');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                $errors[] = "Email không hợp lệ";

            if (!preg_match('/^(0|\+84)\d{8,10}$/', $sdt))
                $errors[] = "Số điện thoại không hợp lệ";

            if (empty($errors)) {

                $kh->updateContact($this->user['MaKhachHang'], [
                    'Email'  => $email,
                    'SDT'    => $sdt,
                    'DiaChi' => $diachi,
                ]);

                // Cập nhật lại session
                $_SESSION['user']['Email']  = $email;
                $_SESSION['user']['SDT']    = $sdt;
                $_SESSION['user']['DiaChi'] = $diachi;

                $success = "Cập nhật thông tin thành công!";
            }

            // Cập nhật form sau khi POST để giữ giá trị nếu lỗi
            $form['Email']  = $email;
            $form['SDT']    = $sdt;
            $form['DiaChi'] = $diachi;
        }

        $this->view("khachhang/cap_nhat_thong_tin", [
            "profile" => $profile,
            "form"    => $form,
            "errors"  => $errors,
            "success" => $success
        ]);
    }


    // =============================
    // 4) ĐẶT DỊCH VỤ BỔ SUNG
    // =============================
    public function datDichVuBoSung()
    {
        $this->requireRole(['KhachHang']);

        $dv = new DichVu();
        $errors = [];
        $success = null;

        // List dịch vụ
        $services = $dv->getActive();

        $maKH = $this->user['MaKhachHang'];
        $db   = Database::getConnection();

        // ⚡ ALWAYS load bookings FIRST
        $sqlBookings = "
            SELECT 
                gd.MaGiaoDich,
                gd.TongTien,
                gd.TrangThai AS TrangThaiGD,
                ctgd.MaCTGD,
                ctgd.MaPhong,
                ctgd.NgayNhanDuKien,
                ctgd.NgayTraDuKien,
                ctgd.TrangThai AS TrangThaiCT,
                p.SoPhong
            FROM giaodich gd
            JOIN chitietgiaodich ctgd ON ctgd.MaGiaoDich = gd.MaGiaoDich
            JOIN phong p ON p.MaPhong = ctgd.MaPhong
            WHERE gd.MaKhachHang = $maKH
            AND TRIM(ctgd.TrangThai) = 'Booked'
            AND DATE(ctgd.NgayNhanDuKien) >= CURDATE()
            GROUP BY gd.MaGiaoDich
            ORDER BY gd.MaGiaoDich DESC
        ";

        $bookings = $db->query($sqlBookings)->fetch_all(MYSQLI_ASSOC);

        $selectedService = null;

        // ===================== FORM POST =====================
        if ($_SERVER["REQUEST_METHOD"] === "POST")
        {
            $btn = $_POST["btn_action"] ?? "";

            if ($btn === "choose_service") {
                $maDV = (int)($_POST["ma_dv"] ?? 0);
                $selectedService = $dv->getById($maDV);
            }

            if ($btn === "confirm_booking_service") {

                 $maDV    = (int)($_POST["ma_dv"] ?? 0);
                $soLuong = (int)($_POST["so_luong"] ?? 0);
                $maGD    = (int)($_POST["ma_gd"] ?? 0);

                if ($maGD <= 0)    $errors[] = "Vui lòng chọn giao dịch!";
                if ($maDV <= 0)    $errors[] = "Dịch vụ không hợp lệ!";
                if ($soLuong <= 0) $errors[] = "Số lượng phải > 0!";

                $row = $dv->getById($maDV);
                if (!$row) $errors[] = "Không tìm thấy thông tin dịch vụ!";

                if (empty($errors)) {

                    $giaBan = (int)$row["GiaDichVu"];
                    $tong   = $soLuong * $giaBan;
                    $ghiChu = "";   // có thể lấy thêm từ form nếu có

                    $sqlInsert = "
                        INSERT INTO chitietdichvu
                            (MaGiaoDich, MaPhong, MaDichVu, SoLuong, GiaBan, ThanhTien, ThoiDiemGhiNhan, GhiChu)
                        VALUES
                            ($maGD, 
                            (SELECT MaPhong FROM chitietgiaodich WHERE MaGiaoDich = $maGD LIMIT 1),
                            $maDV, 
                            $soLuong, 
                            $giaBan, 
                            $tong, 
                            NOW(), 
                            '$ghiChu')";
                    
                    $db->query($sqlInsert);

                    // Cập nhật tổng tiền giao dịch
                    $db->query("
                        UPDATE giaodich 
                        SET TongTien = TongTien + $tong
                        WHERE MaGiaoDich = $maGD
                    ");

                    $success = "Đặt dịch vụ thành công!";
                }
            }
        }

        // Render view
        $this->view("khachhang/dat_dich_vu", [
            "services"        => $services,
            "bookings"        => $bookings,     // ALWAYS EXISTS
            "selectedService" => $selectedService,
            "errors"          => $errors,
            "success"         => $success
        ]);
    }


    // =============================
    // 5) HỦY DỊCH VỤ BỔ SUNG
    // =============================
    public function huyDichVuBoSung()
    {
        $this->requireRole(['KhachHang']);
        $db = Database::getConnection();
        $maKH = $this->user['MaKhachHang'];

        $success = null;
        $errors = [];

        // Lấy danh sách dịch vụ khách đã đặt
        $sql = "
            SELECT 
                ctdv.MaCTDV,
                ctdv.MaGiaoDich,
                ctdv.MaDichVu,
                dv.TenDichVu,
                ctdv.SoLuong,
                ctdv.GiaBan,
                ctdv.ThanhTien,
                ctdv.NgayDat,
                ctdv.TrangThaiDichVu,
                p.SoPhong,
                ctgd.NgayTraDuKien
            FROM chitietdichvu ctdv
            JOIN dichvu dv ON dv.MaDichVu = ctdv.MaDichVu
            JOIN giaodich gd ON gd.MaGiaoDich = ctdv.MaGiaoDich
            JOIN chitietgiaodich ctgd ON ctgd.MaGiaoDich = gd.MaGiaoDich
            LEFT JOIN phong p ON p.MaPhong = ctgd.MaPhong
            WHERE gd.MaKhachHang = $maKH
            AND gd.TrangThai = 'Booked'
            ORDER BY ctdv.MaCTDV DESC
        ";

        $list = $db->query($sql)->fetch_all(MYSQLI_ASSOC);

        // Xử lý POST hủy dịch vụ
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $maCTDV = (int)$_POST['ma_ctdv'];

            // Lấy thông tin dịch vụ để kiểm tra điều kiện
            $rs = $db->query("
                SELECT ctdv.*, ctgd.NgayTraDuKien
                FROM chitietdichvu ctdv
                JOIN chitietgiaodich ctgd ON ctgd.MaGiaoDich = ctdv.MaGiaoDich
                WHERE ctdv.MaCTDV = $maCTDV
            ")->fetch_assoc();

            if (!$rs) {
                $errors[] = "Không tìm thấy thông tin dịch vụ để hủy!";
            } else {

                $maGD = (int)$rs['MaGiaoDich'];
                $tien = (float)$rs['ThanhTien'];

                // ===== Điều kiện không cho hủy =====
                $trangThai = $rs['TrangThaiDichVu'];
                $ngayTra = new DateTime($rs['NgayTraDuKien']);
                $today = new DateTime(date('Y-m-d'));

                if ($trangThai === 'DangSuDung' || $trangThai === 'DaSuDung') {
                    $errors[] = "Không thể hủy vì dịch vụ đang sử dụng hoặc đã sử dụng!";
                }

                if ($today >= $ngayTra) {
                    $errors[] = "Không thể hủy vì đã quá hạn trả phòng!";
                }

                if (empty($errors)) {
                    // Xóa dịch vụ
                    $db->query("DELETE FROM chitietdichvu WHERE MaCTDV = $maCTDV");

                    // Trừ tiền khỏi giao dịch gốc
                    $db->query("UPDATE giaodich SET TongTien = TongTien - $tien WHERE MaGiaoDich = $maGD");

                    $success = "Hủy dịch vụ bổ sung thành công!";
                }
            }
        }

        $this->view("khachhang/huy_dich_vu", compact("list", "success", "errors"));
    }

    // =============================
    // 6) GỬI PHẢN HỒI
    // =============================
    public function guiPhanHoi()
    {
        $this->requireRole(['KhachHang']);  // bắt buộc là khách
        $db = Database::getConnection();

        $errors   = [];
        $success  = null;
        $feedback = [
            'loai'     => '',
            'muc_do'   => '',
            'noi_dung' => '',
        ];

        // Lấy user từ session
        $user = $this->user;
        $maKH = $user['MaKhachHang'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $btn = $_POST['btn_action'] ?? '';

            if ($btn === 'submit_feedback') {

                // LƯU LẠI INPUT USER
                $feedback['loai']     = trim($_POST['loai'] ?? '');
                $feedback['muc_do']   = trim($_POST['muc_do'] ?? '');
                $feedback['noi_dung'] = trim($_POST['noi_dung'] ?? '');

                // VALIDATION
                if ($feedback['loai'] === '') {
                    $errors['loai'] = "Vui lòng chọn loại dịch vụ.";
                }
                if ($feedback['muc_do'] === '') {
                    $errors['muc_do'] = "Vui lòng chọn mức độ hài lòng.";
                }

                // Xử lý upload ảnh (tùy chọn)
                $fileName = null;
                if (!empty($_FILES['file']['name'])) {
                    $uploadDir = "uploads/phanhoi/";
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    $fileName = "fb_" . time() . "_" . rand(1000, 9999) . "." . $ext;
                    $path = $uploadDir . $fileName;

                    $allowed = ['jpg','jpeg','png','webp'];
                    if (!in_array(strtolower($ext), $allowed)) {
                        $errors['file'] = "Chỉ chấp nhận file ảnh JPG, PNG, WEBP.";
                    } else {
                        move_uploaded_file($_FILES['file']['tmp_name'], $path);
                    }
                }

                // Nếu có lỗi, trả lại VIEW & dữ liệu cũ
                if (!empty($errors)) {
                    return $this->view("khachhang/gui_phan_hoi", [
                        'errors'   => $errors,
                        'feedback' => $feedback,
                        'user'     => $user
                    ]);
                }

                // LƯU DB
                $noi_dung_sql = $db->real_escape_string($feedback['noi_dung']);

                $sql = "
                    INSERT INTO phanhoi (MaKhachHang, LoaiDichVu, MucDoHaiLong, NoiDung, TepDinhKem, TinhTrang, NgayPhanHoi)
                    VALUES ('$maKH', '{$feedback['loai']}', '{$feedback['muc_do']}', '$noi_dung_sql', 
                            " . ($fileName ? "'$fileName'" : "NULL") . ",
                            'ChuaXuLy', NOW())
                ";

                if ($db->query($sql)) {
                    $success = "Cảm ơn phản hồi của bạn! Bộ phận CSKH sẽ liên hệ sớm nhất.";
                    // Reset form sau khi gửi thành công
                    $feedback = ['loai'=>'','muc_do'=>'','noi_dung'=>''];
                } else {
                    $errors['db'] = "Lỗi hệ thống: Không thể lưu phản hồi.";
                }
            }
        }

        // RETURN VIEW
        return $this->view("khachhang/gui_phan_hoi", [
            'feedback' => $feedback,
            'errors'   => $errors,
            'success'  => $success,
            'user'     => $user
        ]);
    }

   public function datPhongOnline1()
    {
        $this->requireRole(['KhachHang']);

        // Đảm bảo đúng múi giờ Việt Nam
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $phongModel = new Phong();
        $gdModel    = new GiaoDich();
        $ctgdModel  = new ChiTietGiaoDich();

        $errors         = [];
        $success        = null;
        $rooms          = [];
        $selectedRoom   = null;
        $bookingForm    = [];
        $summary        = null;

        $btn = $_POST['btn_action'] ?? '';

        // FILTER INPUT
        if ($btn === 'reset') {
            $filter = [
                'loai_phong' => '',
                'view_phong' => '',
                'so_khach'   => null,
                'gia_goi_y'  => null,
            ];
        } else {
            $filter = [
                'loai_phong' => $_POST['loai_phong'] ?? '',
                'view_phong' => $_POST['view_phong'] ?? '',
                'so_khach'   => ($_POST['so_khach'] ?? '') !== '' ? (int)$_POST['so_khach'] : null,
                'gia_goi_y'  => ($_POST['gia_goi_y'] ?? '') !== '' ? (int)$_POST['gia_goi_y'] : null,
            ];
        }

        // STEP 1 – Filter rooms
        if ($btn === 'filter') {
            try {
                $rooms = $phongModel->searchForOnlineBooking($filter);
                if (empty($rooms)) $errors[] = "Không có phòng nào phù hợp tiêu chí tìm kiếm!";
            } catch (\Throwable $e) {
                $errors[] = "Lỗi tải phòng! Hãy thử lại.";
            }
        }

        // STEP 2 – Choose room
        if ($btn === 'choose_room') {

            $roomId = (int)($_POST['room_id'] ?? 0);

            try {
                $rooms = $phongModel->searchForOnlineBooking($filter);
            } catch (\Throwable $e) {
                $rooms = [];
            }

            if (!empty($rooms)) {
                $rooms = array_filter($rooms, fn($r) => (int)$r['MaPhong'] === $roomId);
            }

            $selectedRoom = $phongModel->getById($roomId);
            if (!$selectedRoom) $errors[] = "Không tìm thấy phòng!";

            $bookingForm = [
                'ten_khach' => '',
                'cccd'      => '',
                'sdt'       => '',
                'email'     => '',
                'so_nguoi'  => 1,
                'ngay_nhan' => '',
                'ngay_tra'  => ''
            ];

            $summary = null;
        }

        // STEP 3 – Review booking summary
        if ($btn === 'review_booking') {

            $roomId = (int)($_POST['room_id'] ?? 0);

            try {
                $rooms = $phongModel->searchForOnlineBooking($filter);
            } catch (\Throwable $e) {
                $rooms = [];
            }

            if (!empty($rooms)) {
                $rooms = array_filter($rooms, fn($r) => (int)$r['MaPhong'] === $roomId);
            }

            $selectedRoom = $phongModel->getById($roomId);
            if (!$selectedRoom) $errors[] = "Không tìm thấy phòng!";

            $bookingForm = $_POST['booking'] ?? [];

            // Validate
            if (empty($bookingForm['ten_khach'])) $errors[] = "Vui lòng nhập tên khách!";
            if (empty($bookingForm['cccd']) || !preg_match('/^[0-9]{9,12}$/', $bookingForm['cccd']))
                $errors[] = "CCCD không hợp lệ!";
            if (!empty($bookingForm['email']) && !filter_var($bookingForm['email'], FILTER_VALIDATE_EMAIL))
                $errors[] = "Email không hợp lệ!";
            if (!empty($bookingForm['sdt']) && !preg_match('/^(0|\+84)\d{8,10}$/', $bookingForm['sdt']))
                $errors[] = "Số điện thoại không hợp lệ!";
            if ((int)($bookingForm['so_nguoi'] ?? 0) < 1)
                $errors[] = "Số người phải >= 1";

            // Validate ngày
            if (empty($bookingForm['ngay_nhan']) || empty($bookingForm['ngay_tra'])) {
                $errors[] = "Vui lòng chọn ngày nhận & ngày trả!";
            } else {
                $today = strtotime(date("Y-m-d"));
                $ngayNhan = strtotime($bookingForm['ngay_nhan']);
                $ngayTra  = strtotime($bookingForm['ngay_tra']);

                if ($ngayNhan <= $today) {
                    $errors[] = "Ngày nhận phòng phải sau hôm nay!";
                }

                if ($ngayTra <= $ngayNhan) {
                    $errors[] = "Ngày trả phải sau ngày nhận!";
                }
            }

            if (empty($errors) && $selectedRoom) {
                $gia       = (int)$selectedRoom['Gia'];
                $soDem     = max(1, round(($ngayTra - $ngayNhan) / 86400));
                $thanhTien = $gia * $soDem;

                $summary = [
                    'ma_kh'      => $this->user['MaKhachHang'],
                    'ten_kh'     => $this->user['Username'],
                    'so_dem'     => $soDem,
                    'tong_tien'  => $thanhTien,
                    'khuyen_mai' => 0,
                    'phong' => [
                        'MaPhong'   => $selectedRoom['MaPhong'],
                        'SoPhong'   => $selectedRoom['SoPhong'],
                        'LoaiPhong' => $selectedRoom['LoaiPhong'],
                        'GiaPhong'  => $gia,
                        'ThanhTien' => $thanhTien,
                        'TenKhach'  => $bookingForm['ten_khach'],
                        'CCCD'      => $bookingForm['cccd'],
                        'SDT'       => $bookingForm['sdt'],
                        'Email'     => $bookingForm['email'],
                        'SoNguoi'   => $bookingForm['so_nguoi'],
                        'NgayNhan'  => $bookingForm['ngay_nhan'],
                        'NgayTra'   => $bookingForm['ngay_tra'],
                    ]
                ];
            }
        }

        // STEP 4 – Confirm booking
        if ($btn === 'confirm_booking') {

            $roomId = (int)($_POST['room_id'] ?? 0);
            $selectedRoom = $phongModel->getById($roomId);
            $bookingForm  = $_POST['booking'] ?? [];

            if (empty($bookingForm['ngay_nhan']) || empty($bookingForm['ngay_tra'])) {
                $errors[] = "Thiếu ngày nhận / ngày trả!";
            } else {
                $today = strtotime(date("Y-m-d"));
                if (strtotime($bookingForm['ngay_nhan']) <= $today) {
                    $errors[] = "Ngày nhận phòng phải sau hôm nay!";
                }
            }

            if (empty($errors)) {

                $db = Database::getConnection();
                $db->begin_transaction();

                try {

                    $soDem = max(1, round((strtotime($bookingForm['ngay_tra']) - strtotime($bookingForm['ngay_nhan'])) / 86400));
                    $thanhTien = $soDem * (int)$selectedRoom['Gia'];

                    $maGD = $gdModel->createBooking([
                        'MaKhachHang'         => $this->user['MaKhachHang'],
                        'LoaiGiaoDich'        => 'DatPhong',
                        'TongTien'            => $thanhTien,
                        'TrangThai'           => GiaoDich::STATUS_BOOKED,
                        'PhuongThucThanhToan' => 'ChuaThanhToan',
                        'GhiChu'              => 'Đặt phòng online'
                    ]);

                    $ctgdModel->createDetail1([
                        'MaGiaoDich'     => $maGD,
                        'MaPhong'        => $selectedRoom['MaPhong'],
                        'NgayNhanDuKien' => $bookingForm['ngay_nhan'],
                        'NgayTraDuKien'  => $bookingForm['ngay_tra'],
                        'SoNguoi'        => (int)$bookingForm['so_nguoi'],
                        'DonGia'         => (float)$selectedRoom['Gia'],
                        'ThanhTien'      => $thanhTien,
                        'TrangThai'      => ChiTietGiaoDich::STATUS_BOOKED,
                        'GhiChu'         => 'Đặt phòng online',
                        'TenKhach'       => $bookingForm['ten_khach'],
                        'CCCD'           => $bookingForm['cccd'],
                        'SDT'            => $bookingForm['sdt'],
                        'Email'          => $bookingForm['email'],
                    ]);

                    $phongModel->updateTrangThai($roomId, 'Booked');

                    $db->commit();

                    $success       = "Đặt phòng thành công! Mã giao dịch của bạn: #$maGD";
                    $rooms         = [];
                    $selectedRoom  = null;
                    $bookingForm   = [];
                    $summary       = null;

                } catch (\Throwable $ex) {
                    $db->rollback();
                    $errors[] = "Lỗi SQL: " . $ex->getMessage();
                }
            }
        }

        // RENDER VIEW
        $this->view('khachhang/dat_phong_online', [
            'user'         => $this->user,
            'errors'       => $errors,
            'success'      => $success,
            'filter'       => $filter,
            'rooms'        => $rooms,
            'selectedRoom' => $selectedRoom,
            'bookingForm'  => $bookingForm,
            'summary'      => $summary,
        ]);
    }

    public function searchRooms()
    {
        $this->requireRole(['KhachHang']);

        $phongModel = new Phong();

        // Lấy giá trị filter từ GET
        $filters = [
            'loai_phong' => $_GET['loai_phong'] ?? '',
            'view_phong' => $_GET['view_phong'] ?? '',
            'so_khach'   => $_GET['so_khach'] ?? '',
            'gia_goi_y'  => $_GET['gia_goi_y'] ?? '',
        ];

        // Nếu nhấn nút Reset thì đặt lại filter trắng
        if (($_GET['btn_action'] ?? '') === 'reset') {
            $rooms = []; // không query gì cả
            return $this->view("khachhang/dat_phong_search", [
                'rooms' => $rooms,
                'filters' => [
                    'loai_phong' => '',
                    'view_phong' => '',
                    'so_khach' => '',
                    'gia_goi_y' => ''
                ],
                'query' => ''
            ]);
        }

        // Map "View phòng" từ UI sang DB
        $viewConvert = [
            'Hướng biển'       => 'Biển',
            'Hướng hồ bơi'     => 'Hồ bơi',
            'Hướng thành phố'  => 'Thành phố',
            'Hướng vườn'       => 'Vườn',
        ];

        $viewPhong = $viewConvert[$filters['view_phong']] ?? $filters['view_phong'];

        // Ép kiểu trước khi gửi vào Model
        $giaMongMuon = ($filters['gia_goi_y'] !== '') ? (float)$filters['gia_goi_y'] : null;
        $soKhach = ($filters['so_khach'] !== '') ? (int)$filters['so_khach'] : null;
        $loaiPhong = (string)($filters['loai_phong'] ?? '');
        $viewPhong = (string)$viewPhong;

        // Gọi hàm Model
        $rooms = $phongModel->searchByFilter(
            $giaMongMuon,
            $soKhach,
            $loaiPhong,
            $viewPhong
        );

        // Trả JSON dùng cho AJAX
        if (isset($_GET['ajax'])) {
            die(json_encode($rooms));
        }

        // Render ra view
        return $this->view("khachhang/dat_phong_search", compact("rooms", "filters"));
    }

    public function enterCustomerInfo()
    {
        $this->requireRole(['KhachHang']);
        $roomId = $_POST['room_id'] ?? null;
        $form = $_POST['booking'] ?? [];

        return $this->view("khachhang/dat_phong_info", compact("roomId", "form"));
    }

    public function reviewBooking()
    {
        $this->requireRole(['KhachHang']);

        $roomId  = (int)($_POST['room_id'] ?? 0);
        $booking = $_POST['booking'] ?? [];

        $phongModel = new Phong();
        $room = $phongModel->getById($roomId);

        if (!$room) {
            $_SESSION['error'] = "Không tìm thấy phòng!";
            header("Location: index.php?controller=khachhang&action=searchRooms");
            exit;
        }

        // Tính tiền phòng
        $giaPhong = (float)$room['Gia'];
        $soDem = max(1, (strtotime($booking['ngay_tra']) - strtotime($booking['ngay_nhan'])) / 86400);
        $tongTien = $giaPhong * $soDem;

        // Khuyến mãi
        require_once "models/KhuyenMai.php";
        $kmModel = new KhuyenMai();

        $maKM = (!empty($booking['ma_km'])) ? (int)$booking['ma_km'] : null;
        $giamGia = 0;

        if ($maKM > 0) {
            $km = $kmModel->getById($maKM);

            // ⭐ CHỈ SỬA 1 DÒNG NÀY
            if ($km) {  
                $giamGia = $kmModel->calculateDiscountById($maKM, $tongTien);
            }
        }

        $tongSauGiam = max(0, $tongTien - $giamGia);

        $summary = [
            'room'        => $room,
            'booking'     => $booking,
            'so_dem'      => $soDem,
            'tong_tien'   => $tongTien,
            'ma_km'       => $maKM,
            'giam_gia'    => $giamGia,
            'tong_sau_km' => $tongSauGiam,
        ];

        return $this->view("khachhang/dat_phong_review", [
            "room"    => $room,
            "booking" => $booking,
            "summary" => $summary,
        ]);
    }

    public function confirmBooking()
    {
        $this->requireRole(['KhachHang']);

        // Lấy thông tin user từ session
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $roomId  = $_POST['room_id'] ?? null;
        $booking = $_POST['booking'] ?? null;

        if (!$roomId || !$booking) {
            die("Thiếu dữ liệu đặt phòng");
        }

        // Lấy thông tin phòng
        $phongModel = new Phong();
        $room = $phongModel->getById($roomId);
        if (!$room) {
            die("Không tìm thấy phòng");
        }

        require_once "models/GiaoDich.php";
        require_once "models/ChiTietGiaoDich.php";
        require_once "models/KhuyenMai.php";  
        require_once "models/KhachHang.php"; 

        $gdModel = new GiaoDich();
        $ctModel = new ChiTietGiaoDich();
        $kmModel = new KhuyenMai();
        $khModel = new KhachHang();           

        $db = Database::getConnection();
        $db->begin_transaction();

        try {

            // ==== TÍNH TIỀN GỐC ====
            $soDem = max(1, (strtotime($booking['ngay_tra']) - strtotime($booking['ngay_nhan'])) / 86400);
            $tongTien = $soDem * (float)$room['Gia'];


            /* ============================================================
             XỬ LÝ KHUYẾN MÃI
            ============================================================ */
            $maKM = (!empty($booking['ma_km'])) ? (int)$booking['ma_km'] : null;
            $giamGia = 0;

            if ($maKM > 0) {
                $giamGia = $kmModel->calculateDiscountById($maKM, $tongTien);
            }

            $tongSauGiam = max(0, $tongTien - $giamGia);
            /* ========================================================== */


            // ==== TẠO GIAO DỊCH CHUNG ==== (sửa TongTien & thêm MaKhuyenMai)
            $maGD = $gdModel->createBooking([
                'MaKhachHang'         => $user['MaKhachHang'],
                'TongTien'            => $tongSauGiam,                  
                'TrangThai'           => GiaoDich::STATUS_BOOKED,
                'PhuongThucThanhToan' => 'ChuaThanhToan',
                'GhiChu'              => 'Đặt phòng online',
                'MaKhuyenMai'         => $maKM                          
            ]);

            $khModel->updateMaGiaoDich(
                $user['MaKhachHang'],
                $maGD
            );

            // ==== LẤY THÔNG TIN KHÁCH ==== (ƯU TIÊN LẤY TỪ BOOKING FORM)
            $tenKhach = $booking['ten_khach'] 
                ?? ($user['TenKH'] ?? ($user['HoTen'] ?? ''));

            $cccd = $booking['cccd'] 
                ?? ($user['CCCD'] ?? '');

            $sdt   = $booking['sdt']   ?? ($user['SDT']   ?? '');
            $email = $booking['email'] ?? ($user['Email'] ?? '');

            // Nếu vẫn thiếu thì mới chặn
            if (!$tenKhach || !$cccd) {
                throw new Exception("Thiếu thông tin khách hàng trong hồ sơ. Vui lòng cập nhật trước khi đặt phòng.");
            }

            // ==== TẠO CHI TIẾT GIAO DỊCH ==== (giữ nguyên, không giảm trong chi tiết)
            $ctModel->createDetail1([
                'MaGiaoDich'     => $maGD,
                'MaPhong'        => $room['MaPhong'],
                'NgayNhanDuKien' => $booking['ngay_nhan'],
                'NgayTraDuKien'  => $booking['ngay_tra'],
                'SoNguoi'        => (int)$booking['so_nguoi'],
                'DonGia'         => (float)$room['Gia'],
                'ThanhTien'      => $tongSauGiam,   // giá sau khuyến mãi → chuẩn kế toán
                'TrangThai'      => ChiTietGiaoDich::STATUS_BOOKED,
                'GhiChu'         => 'Đặt phòng online',

                'TenKhach'       => $tenKhach,
                'CCCD'           => $cccd,
                'SDT'            => $sdt,
                'Email'          => $email,

                'MaKhuyenMai'    => $maKM    // thêm dòng này
            ]);

            // Cập nhật trạng thái phòng
            $phongModel->updateTrangThai($roomId, 'Booked');

            // Commit giao dịch
            $db->commit();


            // ==== LƯU INVOICE === (thêm tổngSauGiam & giamGia)
            $_SESSION['invoice'] = [
                'maGD'      => $maGD,
                'room'      => $room,
                'booking'   => [
                    'ngay_nhan' => $booking['ngay_nhan'],
                    'ngay_tra'  => $booking['ngay_tra'],
                    'so_nguoi'  => $booking['so_nguoi'],
                    'ten_khach' => $tenKhach,
                    'cccd'      => $cccd,
                    'sdt'       => $sdt,
                    'email'     => $email,
                ],

                // ⭐ THÊM 3 GIÁ TRỊ QUAN TRỌNG
                'giam_gia'    => $giamGia,
                'tong_tien'   => $tongSauGiam,
                'so_dem'      => $soDem
            ];

            header("Location: index.php?controller=khachhang&action=invoice");
            exit;

        } catch (Throwable $ex) {
            $db->rollback();
            die("Lỗi DB: " . $ex->getMessage());
        }
    }

    public function confirmBookingMulti()
    {
        $this->requireRole(['KhachHang']);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        /* ================= AUTH ================= */
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            die("Chưa đăng nhập");
        }

        /* ================= INPUT ================= */
        $roomIds = $_POST['rooms'] ?? [];
        $booking = $_POST['booking'] ?? [];

        if (empty($roomIds) || empty($booking)) {
            die("Thiếu dữ liệu đặt phòng");
        }

        if (
            empty($booking['ngay_nhan']) ||
            empty($booking['ngay_tra']) ||
            empty($booking['so_nguoi'])
        ) {
            die("Thiếu thông tin ngày hoặc số người");
        }

        /* ================= MODELS ================= */
        require_once "models/Phong.php";
        require_once "models/GiaoDich.php";
        require_once "models/ChiTietGiaoDich.php";
        require_once "models/KhuyenMai.php";

        $phongModel = new Phong();
        $gdModel    = new GiaoDich();
        $ctModel    = new ChiTietGiaoDich();
        $kmModel    = new KhuyenMai();
        $khModel = new KhachHang();

        $db = Database::getConnection();
        $db->begin_transaction();

        try {

            /* ================= DATE ================= */
            $ngayNhan = $booking['ngay_nhan'];
            $ngayTra  = $booking['ngay_tra'];

            $soDem = (strtotime($ngayTra) - strtotime($ngayNhan)) / 86400;
            if ($soDem <= 0) {
                throw new Exception("Ngày trả phải sau ngày nhận");
            }

            /* ================= LOAD ROOMS ================= */
            $rooms = [];
            $tongTien = 0;
            $tongSucChua = 0;

            foreach ($roomIds as $roomId) {
            $room = $phongModel->getById($roomId);

            if (!$room) {
                throw new Exception("Không tìm thấy phòng ID = $roomId");
            }

            // FIX TRẠNG THÁI PHÒNG
            if (trim($room['TrangThai']) !== 'Trong') {
                throw new Exception("Phòng {$room['SoPhong']} không còn trống");
            }

            $rooms[] = $room;
            $tongTien += $soDem * (float)$room['Gia'];
            $tongSucChua += (int)$room['SoKhachToiDa'];
        }

            /* ================= VALIDATE SỐ KHÁCH ================= */
            $soNguoi = (int)$booking['so_nguoi'];

            if ($soNguoi <= 0) {
                throw new Exception("Số khách không hợp lệ");
            }

            if ($soNguoi > $tongSucChua) {
                throw new Exception("Số khách vượt quá sức chứa các phòng");
            }

            /* ================= KHUYẾN MÃI (FIX FK) ================= */
            $maKM = (!empty($booking['ma_km']) && is_numeric($booking['ma_km']))
                ? (int)$booking['ma_km']
                : null;

            $giamGia = 0;
            if ($maKM) {
                $giamGia = $kmModel->calculateDiscountById($maKM, $tongTien);
            }

            $tongSauGiam = max(0, $tongTien - $giamGia);

            /* ================= TẠO GIAO DỊCH ================= */
            $maGD = $gdModel->createBooking([
                'MaKhachHang'         => $user['MaKhachHang'],
                'TongTien'            => $tongSauGiam,
                'TrangThai'           => GiaoDich::STATUS_BOOKED,
                'PhuongThucThanhToan' => 'ChuaThanhToan',
                'GhiChu'              => 'Đặt nhiều phòng online',
                'MaKhuyenMai'         => $maKM   //  NULL hoặc ID hợp lệ
            ]);

            $khModel->updateMaGiaoDich($user['MaKhachHang'], $maGD);
            /* ================= CHI TIẾT GIAO DỊCH ================= */
            foreach ($rooms as $room) {

                $ctModel->createDetail1([
                    'MaGiaoDich'     => $maGD,
                    'MaPhong'        => $room['MaPhong'],
                    'NgayNhanDuKien' => $ngayNhan,
                    'NgayTraDuKien'  => $ngayTra,
                    'SoNguoi'        => $soNguoi,
                    'DonGia'         => (float)$room['Gia'],
                    'ThanhTien'      => $soDem * (float)$room['Gia'],
                    'TrangThai'      => ChiTietGiaoDich::STATUS_BOOKED,
                    'GhiChu'         => 'Đặt phòng online',

                    'TenKhach'       => $booking['ten_khach'] ?? $user['TenKH'],
                    'CCCD'           => $booking['cccd']      ?? $user['CCCD'],
                    'SDT'            => $booking['sdt']       ?? $user['SDT'],
                    'Email'          => $booking['email']     ?? $user['Email'],
                    'MaKhuyenMai'    => $maKM
                ]);

                // cập nhật phòng
                $phongModel->updateTrangThai($room['MaPhong'], 'Booked');
            }

            $db->commit();

            /* ================= LƯU INVOICE ================= */
            $_SESSION['invoice'] = [
                'maGD'        => $maGD,
                'rooms'       => $rooms,
                'booking'     => $booking,
                'tong_tien'   => $tongSauGiam,
                'giam_gia'    => $giamGia,
                'so_dem'      => $soDem
            ];

            unset($_SESSION['cart_rooms']);

            header("Location: index.php?controller=khachhang&action=invoice1");
            exit;

        } catch (Throwable $e) {
            $db->rollback();
            die("Lỗi đặt phòng: " . $e->getMessage());
        }
    }


    public function invoice()
    {
        $this->requireRole(['KhachHang']);

        $invoice = $_SESSION['invoice'] ?? null;

        if (!$invoice) {
            header("Location: index.php?controller=khachhang&action=dashboard");
            exit;
        }

        // room đã lưu full thông tin trong session theo confirmBooking()
        $room = $invoice['room'] ?? null;

        return $this->view("khachhang/dat_phong_invoice", [
            'invoice' => $invoice,
            'room'    => $room
        ]);
    }

    public function chooseRoom()
    {
        $this->requireRole(['KhachHang']);

        $roomId = $_POST['room_id'] ?? null;

        if (!$roomId) {
            header("Location: index.php?controller=khachhang&action=searchRooms&error=no_room");
            exit();
        }

        $phong = new Phong();
        $room = $phong->getById($roomId);

        if (!$room) {
            header("Location: index.php?controller=khachhang&action=searchRooms&error=room_not_found");
            exit();
        }

        // Chuyển sang form nhập thông tin khách
        return $this->view("khachhang/dat_phong_info", [
            "room" => $room,
            "user" => Auth::user()
        ]);
    }

    public function datPhongOnline2()
    {
        $this->requireRole(['KhachHang']);

        $roomId = $_GET['room_id'] ?? null;
        if (!$roomId) {
            header("Location: index.php?controller=khachhang&action=searchRooms");
            exit;
        }

        $phong = new Phong();
        $room = $phong->getById($roomId);
        if (!$room) {
            header("Location: index.php?controller=khachhang&action=searchRooms&error=notfound");
            exit;
        }

        // ===========================
        // NẠP THÔNG TIN KHÁCH HÀNG
        // ===========================
        $user = $_SESSION['user'];  // chứa Username, Role,...
        $maKH = $user['MaKhachHang'];

        $kmModel = new KhuyenMai();
        $khuyenMaiList = $kmModel->getActive();
        require_once "models/KhachHang.php";
        $khModel = new KhachHang();
        $khachHang = $khModel->getById($maKH);

        // Gộp vào để view dùng chung
        $userFull = array_merge($user, $khachHang);

        return $this->view("khachhang/dat_phong_online1", [
            "room" => $room,
            "user" => $userFull,
            "khuyenMaiList" => $khuyenMaiList,
            "maxGuests" => $room['SoKhachToiDa']
        ]);
    }

    public function addToCart()
    {
        $this->requireRole(['KhachHang']);

        $roomId = $_GET['room_id'] ?? null;
        if (!$roomId) {
            die("Thiếu room_id");
        }

        if (!isset($_SESSION['cart_rooms'])) {
            $_SESSION['cart_rooms'] = [];
        }

        // Không cho trùng phòng
        if (!in_array($roomId, $_SESSION['cart_rooms'])) {
            $_SESSION['cart_rooms'][] = $roomId;
        }

        // Quay lại trang tìm kiếm / hoặc viewCart
        header("Location: index.php?controller=khachhang&action=searchRooms");
        exit;
    }

    public function viewCart()
    {
        $this->requireRole(['KhachHang']);

        /* ================== GIỎ PHÒNG ================== */
        $cart = $_SESSION['cart_rooms'] ?? [];

        if (empty($cart)) {
            // Giỏ trống → quay về tìm phòng
            header("Location: index.php?controller=khachhang&action=searchRooms");
            exit;
        }

        /* ================== LOAD MODELS ================== */
        require_once "models/Phong.php";
        require_once "models/KhuyenMai.php";
        require_once "models/KhachHang.php";

        $phongModel = new Phong();
        $kmModel    = new KhuyenMai();
        $khModel    = new KhachHang();

        /* ================== LOAD PHÒNG ================== */
        $rooms = [];
        $tongSucChua = 0;

        foreach ($cart as $roomId) {
            $room = $phongModel->getById($roomId);
            if ($room) {
                $rooms[] = $room;
                $tongSucChua += (int)$room['SoKhachToiDa'];
            }
        }

        if (empty($rooms)) {
            // Trường hợp phòng bị xóa / lỗi DB
            unset($_SESSION['cart_rooms']);
            header("Location: index.php?controller=khachhang&action=searchRooms");
            exit;
        }

        /* ================== LOAD KHÁCH HÀNG ================== */
        $maKH = $this->user['MaKhachHang'];
        $kh   = $khModel->getById($maKH);

        /* ================== LOAD KHUYẾN MÃI ================== */
        $khuyenMaiList = $kmModel->getActive();

        /* ================== RENDER VIEW ================== */
        return $this->view('khachhang/cart', [
            'rooms'          => $rooms,
            'kh'             => $kh,
            'khuyenMaiList'  => $khuyenMaiList,
            'maxGuests'      => $tongSucChua
        ]);
    }
    
    public function reviewBookingMulti()
    {
        $this->requireRole(['KhachHang']);

        /* ========= AUTH ========= */
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            die("Chưa đăng nhập");
        }

        /* ========= INPUT ========= */
        $roomIds = $_POST['rooms'] ?? [];
        $booking = $_POST['booking'] ?? [];

        if (empty($roomIds) || empty($booking)) {
            die("Thiếu dữ liệu đặt phòng");
        }

        /* ========= MODEL ========= */
        require_once "models/Phong.php";
        require_once "models/KhuyenMai.php";
        require_once "models/KhachHang.php";

        $phongModel = new Phong();
        $kmModel    = new KhuyenMai();
        $khModel    = new KhachHang();

        /* ========= KHÁCH HÀNG ========= */
        $kh = $khModel->getById($user['MaKhachHang']);

        /* ========= DATE ========= */
        $ngayNhan = $booking['ngay_nhan'];
        $ngayTra  = $booking['ngay_tra'];

        $soDem = (strtotime($ngayTra) - strtotime($ngayNhan)) / 86400;
        if ($soDem <= 0) {
            die("Ngày trả phải sau ngày nhận");
        }

        /* ========= LOAD PHÒNG ========= */
        $rooms = [];
        $tongTien = 0;
        $tongSucChua = 0;

        foreach ($roomIds as $roomId) {
            $room = $phongModel->getById($roomId);
            if (!$room) {
                die("Không tìm thấy phòng ID = $roomId");
            }

            $rooms[] = $room;
            $tongTien += $soDem * (float)$room['Gia'];
            $tongSucChua += (int)$room['SoKhachToiDa'];
        }

        /* ========= VALIDATE SỐ KHÁCH ========= */
        $soNguoi = (int)$booking['so_nguoi'];
        if ($soNguoi <= 0 || $soNguoi > $tongSucChua) {
            die("Số khách không hợp lệ");
        }

        /* ========= KHUYẾN MÃI ========= */
        $maKM = !empty($booking['ma_km']) ? (int)$booking['ma_km'] : null;
        $giamGia = 0;

        if ($maKM) {
            $giamGia = $kmModel->calculateDiscountById($maKM, $tongTien);
        }

        $tongSauGiam = max(0, $tongTien - $giamGia);

        /* ========= LƯU SESSION REVIEW ========= */
        $_SESSION['multi_booking_review'] = [
            'rooms'        => $rooms,
            'booking'      => $booking,
            'kh'           => $kh,
            'so_dem'       => $soDem,
            'tong_tien'    => $tongTien,
            'giam_gia'     => $giamGia,
            'tong_sau_km'  => $tongSauGiam,
            'ma_km'        => $maKM
        ];

        /* ========= VIEW REVIEW ========= */
        return $this->view("khachhang/dat_phong_multi_review", [
            'rooms' => $rooms,
            'booking' => $booking,
            'kh' => $kh,
            'so_dem' => $soDem,
            'tong_tien' => $tongTien,
            'giam_gia' => $giamGia,
            'tong_sau_km' => $tongSauGiam
        ]);
    }

    public function invoice1()
    {
        $this->requireRole(['KhachHang']);

        $invoice = $_SESSION['invoice'] ?? null;

        if (!$invoice) {
            header("Location: index.php?controller=khachhang&action=dashboard");
            exit;
        }

        return $this->view("khachhang/dat_phong_invoicenp", [
            'invoice' => $invoice
        ]);
    }

    public function removeFromCart()
    {
        $this->requireRole(['KhachHang']);

        $roomId = $_GET['room_id'] ?? null;
        if (!$roomId) die("Thiếu room_id");

        $_SESSION['cart_rooms'] = array_values(
            array_filter($_SESSION['cart_rooms'] ?? [], fn($id) => $id != $roomId)
        );

        header("Location: index.php?controller=khachhang&action=viewCart");
        exit;
    }

}