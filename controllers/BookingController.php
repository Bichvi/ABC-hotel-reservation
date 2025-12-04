<?php

class KhachController extends Controller
{
    private $khModel;
    private $phongModel;
    private $gdModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Chỉ yêu cầu đã đăng nhập, KHÔNG check role để tránh đụng actor khác
        if (empty($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $this->khModel    = new KhachHang();
        $this->phongModel = new Phong();
        $this->gdModel    = new GiaoDich();
    }

    /**
     * Đặt phòng Online cho KHÁCH HÀNG
     * URL: index.php?controller=khach&action=datPhongOnline
     */
    public function datPhongOnline()
    {
        $errors        = [];
        $success       = null;

        // Bộ lọc tìm phòng
        $filters = [
            'hang_phong' => '',
            'so_giuong'  => '',
            'tang'       => '',
            'ngay_nhan'  => '',
            'ngay_tra'   => '',
            'so_phong'   => 1,
            'so_khach'   => 1,
        ];

        $rooms         = []; // danh sách phòng phù hợp
        $selectedRooms = []; // mảng MaPhong được chọn

        // Form thông tin khách
        $formKH = [
            'TenKH'  => '',
            'CCCD'   => '',
            'SDT'    => '',
            'Email'  => '',
            'DiaChi' => '',
        ];

        $preview = null; // tóm tắt đơn đặt phòng (sau khi lưu thành công)

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['btn_action'] ?? '';

            // Hủy → quay lại trang chủ khách (actor khách)
            if ($action === 'cancel') {
                header("Location: index.php?controller=home&action=index");
                exit;
            }

            // Lấy bộ lọc (dùng cho cả filter & book)
            $filters['hang_phong'] = trim($_POST['hang_phong'] ?? '');
            $filters['so_giuong']  = trim($_POST['so_giuong'] ?? '');
            $filters['tang']       = trim($_POST['tang'] ?? '');
            $filters['ngay_nhan']  = trim($_POST['ngay_nhan'] ?? '');
            $filters['ngay_tra']   = trim($_POST['ngay_tra'] ?? '');
            $filters['so_phong']   = (int)($_POST['so_phong'] ?? 1);
            $filters['so_khach']   = (int)($_POST['so_khach'] ?? 1);

            // VALIDATE chung cho bộ lọc
            if ($filters['ngay_nhan'] === '' || $filters['ngay_tra'] === '') {
                $errors[] = "Vui lòng chọn đầy đủ ngày nhận và ngày trả.";
            } else {
                if ($filters['ngay_tra'] <= $filters['ngay_nhan']) {
                    $errors[] = "Ngày trả phải lớn hơn ngày nhận.";
                }
            }

            if ($filters['so_phong'] <= 0) {
                $errors[] = "Số lượng phòng phải lớn hơn 0.";
            }
            if ($filters['so_khach'] <= 0) {
                $errors[] = "Số lượng khách phải lớn hơn 0.";
            }

            // ----------------- ACTION: TÌM PHÒNG -----------------
            if ($action === 'filter' && empty($errors)) {
                $rooms = $this->phongModel->findAvailableForRange(
                    $filters['ngay_nhan'],
                    $filters['ngay_tra'],
                    [
                        'hang_phong' => $filters['hang_phong'],
                        'so_giuong'  => $filters['so_giuong'],
                        'tang'       => $filters['tang'],
                    ]
                );

                if (empty($rooms)) {
                    // Luồng thay thế – không có phòng phù hợp
                    $errors[] = "Không tìm thấy phòng phù hợp với tiêu chí. Vui lòng điều chỉnh lại bộ lọc.";
                }
            }

            // ----------------- ACTION: ĐẶT PHÒNG -----------------
            if ($action === 'book' && empty($errors)) {

                // Load lại danh sách phòng theo bộ lọc để đảm bảo còn trống
                $rooms = $this->phongModel->findAvailableForRange(
                    $filters['ngay_nhan'],
                    $filters['ngay_tra'],
                    [
                        'hang_phong' => $filters['hang_phong'],
                        'so_giuong'  => $filters['so_giuong'],
                        'tang'       => $filters['tang'],
                    ]
                );

                if (empty($rooms)) {
                    $errors[] = "Danh sách phòng không còn khả dụng. Vui lòng tìm lại.";
                } else {

                    // Phòng được chọn
                    $selectedRooms = array_map('intval', $_POST['selected_rooms'] ?? []);
                    $selectedRooms = array_values(array_unique($selectedRooms));

                    if (empty($selectedRooms)) {
                        $errors[] = "Vui lòng chọn ít nhất một phòng để đặt.";
                    }

                    // Thông tin khách
                    $formKH['TenKH']  = trim($_POST['TenKH'] ?? '');
                    $formKH['CCCD']   = trim($_POST['CCCD'] ?? '');
                    $formKH['SDT']    = trim($_POST['SDT'] ?? '');
                    $formKH['Email']  = trim($_POST['Email'] ?? '');
                    $formKH['DiaChi'] = trim($_POST['DiaChi'] ?? '');

                    // VALIDATE thông tin khách (theo regex hệ thống bạn đang dùng)
                    if (
                        $formKH['TenKH'] === '' ||
                        $formKH['CCCD'] === '' ||
                        $formKH['SDT'] === '' ||
                        $formKH['Email'] === ''
                    ) {
                        $errors[] = "Vui lòng nhập đầy đủ Họ tên, CCCD, SĐT và Email.";
                    } else {
                        // Tên chỉ chữ + khoảng trắng
                        if (!preg_match('/^[\p{L}\s]+$/u', $formKH['TenKH'])) {
                            $errors[] = "Họ tên không hợp lệ (chỉ chứa chữ cái và khoảng trắng).";
                        }
                        // CCCD 9–12 số
                        if (!preg_match('/^\d{9,12}$/', $formKH['CCCD'])) {
                            $errors[] = "CMND/CCCD không hợp lệ (9–12 chữ số).";
                        }
                        // SĐT: 10 số, bắt đầu bằng 0
                        if (!preg_match('/^0\d{9}$/', $formKH['SDT'])) {
                            $errors[] = "Số điện thoại không hợp lệ (10 chữ số, bắt đầu bằng 0).";
                        }
                        // Email
                        if (!filter_var($formKH['Email'], FILTER_VALIDATE_EMAIL)) {
                            $errors[] = "Email không hợp lệ.";
                        }
                    }

                    if (empty($errors)) {
                        // 1. Tìm hoặc tạo khách hàng (KHÔNG sửa model KhachHang)
                        $khRow = $this->khModel->findByIdentity(
                            $formKH['CCCD'],
                            $formKH['SDT'],
                            $formKH['Email']
                        );

                        if ($khRow) {
                            $maKH = (int)$khRow['MaKhachHang'];
                            // cập nhật contact nếu cần
                            $this->khModel->updateContact($maKH, [
                                'SDT'    => $formKH['SDT'],
                                'Email'  => $formKH['Email'],
                                'DiaChi' => $formKH['DiaChi'],
                            ]);
                        } else {
                            $maKH = $this->khModel->create([
                                'TenKH'     => $formKH['TenKH'],
                                'SDT'       => $formKH['SDT'],
                                'Email'     => $formKH['Email'],
                                'CCCD'      => $formKH['CCCD'],
                                'DiaChi'    => $formKH['DiaChi'],
                                'LoaiKhach' => 'KhachOnline',
                            ]);
                            if (!$maKH) {
                                $errors[] = "Không thể tạo mới khách hàng. Vui lòng thử lại.";
                            }
                        }

                        if ($maKH && empty($errors)) {
                            // Chuẩn bị map phòng
                            $roomsById = [];
                            foreach ($rooms as $r) {
                                $roomsById[(int)$r['MaPhong']] = $r;
                            }

                            $ngayNhan = $filters['ngay_nhan'];
                            $ngayTra  = $filters['ngay_tra'];

                            $dateIn   = new DateTime($ngayNhan);
                            $dateOut  = new DateTime($ngayTra);
                            $diff     = $dateIn->diff($dateOut);
                            $soDem    = max(1, (int)$diff->days);

                            $tongTien = 0;
                            $details  = [];

                            foreach ($selectedRooms as $maPhong) {
                                if (!isset($roomsById[$maPhong])) {
                                    continue;
                                }
                                $room = $roomsById[$maPhong];

                                // Xác định cột giá (KHÔNG sửa model)
                                $giaPhong = 0;
                                if (isset($room['GiaPhong'])) {
                                    $giaPhong = (float)$room['GiaPhong'];
                                } elseif (isset($room['GiaCoBan'])) {
                                    $giaPhong = (float)$room['GiaCoBan'];
                                }

                                $thanhTien = $giaPhong * $soDem;
                                $tongTien += $thanhTien;

                                $details[] = [
                                    'MaPhong'   => $maPhong,
                                    'SoPhong'   => $room['SoPhong']   ?? '',
                                    'HangPhong' => $room['HangPhong'] ?? '',
                                    'GiaPhong'  => $giaPhong,
                                    'SoDem'     => $soDem,
                                    'ThanhTien' => $thanhTien,
                                ];
                            }

                            if (empty($details)) {
                                $errors[] = "Danh sách phòng chọn không hợp lệ. Vui lòng thử lại.";
                            }

                            if (empty($errors)) {
                                $db = Database::getConnection();
                                $db->begin_transaction();

                                try {
                                    // 2. Tạo giao dịch (KHÔNG dùng hằng STATUS_BOOKED để tránh lỗi, dùng chuỗi 'Booked')
                                    $maNV   = 0;   // online: không gán NV
                                    $maKM   = null;
                                    $ghiChu = "Đặt phòng online từ khách hàng.";

                                    $maGD = $this->gdModel->createBooking([
                                        'MaKhachHang'         => $maKH,
                                        'MaDoan'              => null,
                                        'MaNhanVien'          => $maNV,
                                        'MaKhuyenMai'         => $maKM,
                                        'LoaiGiaoDich'        => 'DatPhongOnline',
                                        'TongTien'            => $tongTien,
                                        'TrangThai'           => 'Booked',
                                        'PhuongThucThanhToan' => 'ChuaThanhToan',
                                        'GhiChu'              => $ghiChu,
                                    ]);

                                    if (!$maGD) {
                                        throw new Exception("Không thể tạo giao dịch.");
                                    }

                                    // 3. Tạo chi tiết giao dịch cho từng phòng (không sửa / dùng thêm model)
                                    $sqlCT = "
                                        INSERT INTO chitietgiaodich
                                            (MaGiaoDich, MaPhong, NgayNhanDuKien, NgayTraDuKien,
                                             SoNguoi, ThanhTien, TrangThai)
                                        VALUES (?, ?, ?, ?, ?, ?, ?)
                                    ";
                                    $stmtCT = $db->prepare($sqlCT);
                                    if (!$stmtCT) {
                                        throw new Exception("Không thể tạo chi tiết giao dịch.");
                                    }

                                    foreach ($details as $d) {
                                        $soNguoi   = $filters['so_khach']; // đơn giản: số khách tổng, bạn có thể tách sau
                                        $trangThai = 'Booked';

                                        $stmtCT->bind_param(
                                            "iissids",
                                            $maGD,
                                            $d['MaPhong'],
                                            $ngayNhan,
                                            $ngayTra,
                                            $soNguoi,
                                            $d['ThanhTien'],
                                            $trangThai
                                        );
                                        if (!$stmtCT->execute()) {
                                            throw new Exception("Lỗi khi lưu chi tiết phòng.");
                                        }

                                        // Cập nhật trạng thái phòng
                                        $this->phongModel->updateTrangThai($d['MaPhong'], 'Booked');
                                    }

                                    $db->commit();

                                    $preview = [
                                        'ma_giao_dich'  => $maGD,
                                        'ma_khach_hang' => $maKH,
                                        'ten_khach'     => $formKH['TenKH'],
                                        'so_phong'      => count($details),
                                        'ngay_nhan'     => $ngayNhan,
                                        'ngay_tra'      => $ngayTra,
                                        'so_khach'      => $filters['so_khach'],
                                        'tong_tien'     => $tongTien,
                                        'details'       => $details,
                                    ];

                                    $success = "Đặt phòng thành công! Mã giao dịch của bạn là #" . $maGD . ".";

                                } catch (\Throwable $ex) {
                                    $db->rollback();
                                    $errors[] = "Có lỗi xảy ra khi lưu đơn đặt phòng. Vui lòng thử lại hoặc liên hệ lễ tân. (Chi tiết: "
                                        . $ex->getMessage() . ")";
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->view("khach/dat_phong_online", [
            'errors'        => $errors,
            'success'       => $success,
            'filters'       => $filters,
            'rooms'         => $rooms,
            'selectedRooms' => $selectedRooms,
            'formKH'        => $formKH,
            'preview'       => $preview,
        ]);
    }
}