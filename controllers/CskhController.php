<?php
require_once 'models/KhuyenMai.php';

class CskhController extends Controller {

    public function __construct() {
        $this->requireLogin();
        $this->requireRole([ 5, 6]); // Role 5 = CSKH, Role 6 = Quản lý
    }

    /* ====================================================
     * DASHBOARD CSKH
     * ==================================================== */
    public function dashboard() {
        $this->view('dashboard/cskh');
    }

    /* ====================================================
     * TẠO KHUYẾN MÃI - Hiển thị form
     * ==================================================== */
    public function taoKhuyenMai() {
        $this->view('cskh/khuyenmai_them');
    }

    /* ====================================================
     * DANH SÁCH KHUYẾN MÃI
     * ==================================================== */
    public function khuyenMai() {
        $model = new KhuyenMai();
        
        // Tự động cập nhật trạng thái "Hết hạn" cho các khuyến mãi quá ngày kết thúc
        $model->autoUpdateExpiredPromotions();
        
        $listKM = $model->getAll_xemDS_CSKH();

        $this->view('cskh/khuyenmai_list', [
            'listKM' => $listKM
        ]);
    }

    /* ====================================================
     * PHẢN HỒI KHÁCH HÀNG
     * ==================================================== */
    public function phanHoi() {
        require_once 'models/PhanHoiModel.php';
        $model = new PhanHoiModel();
        
        // Lấy tất cả phản hồi
        $dsPhanHoi = $model->getAllPhanHoi_QL();
        
        $this->view('cskh/phanhoi_xuly', [
            'mode' => 'list',
            'dsPhanHoi' => $dsPhanHoi
        ]);
    }


    // Hiển thị danh sách phản hồi với tìm kiếm đa năng và lọc trạng thái
    public function searchPhanHoi() {
        require_once 'models/PhanHoiModel.php';
        $model = new PhanHoiModel();

        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';

        if ($keyword !== '' || $status !== '') {
            $dataList = $model->searchPhanHoi_QL($keyword, $status);
        } else {
            $dataList = $model->getAllPhanHoi_QL();
        }

        $this->view('cskh/phanhoi_xuly', [
            'mode'      => 'list',
            'dsPhanHoi' => $dataList,
            'keyword'   => $keyword,
            'status'    => $status
        ]);
    }



    // Chi tiết phản hồi
    public function chiTietPhanHoi() {
        require_once 'models/PhanHoiModel.php';
        $model = new PhanHoiModel();
        
        $maPH = $_GET['id'] ?? 0;
        $info = $model->getOnePhanHoi_QL($maPH);
        
        if (!$info) {
            header('Location: index.php?controller=cskh&action=phanHoi');
            exit;
        }
        
        $history = $model->getLichSu_QL($maPH);
        
        $this->view('cskh/phanhoi_xuly', [
            'mode' => 'detail',
            'info' => $info,
            'history' => $history
        ]);
    }

    // Lưu phản hồi
    public function luuPhanHoi() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once 'models/PhanHoiModel.php';
            $model = new PhanHoiModel();
            
            $maPH = $_POST['MaPH'] ?? 0;
            $noiDung = $_POST['NoiDungTraLoi'] ?? '';
            $tinhTrang = $_POST['TinhTrang'] ?? '';
            
            // [TC15] Xóa khoảng trắng thừa và kiểm tra nội dung rỗng
            $noiDung = trim($noiDung);

            // Chuẩn hóa ký tự xuống dòng: ép tất cả CRLF/CR về LF duy nhất
            // Tránh vấn đề trên một số Host nơi CRLF có thể bị tính thành 2 ký tự
            $noiDung = str_replace(["\r\n", "\r"], "\n", $noiDung);
        
            // Kiểm tra trạng thái
        if (empty($tinhTrang)) {
            header('Location: index.php?controller=cskh&action=chiTietPhanHoi&id=' . $maPH . '&error=status');
            exit;
        }
        
        if (empty($noiDung)) {
            // Redirect về trang chi tiết với thông báo lỗi
            header('Location: index.php?controller=cskh&action=chiTietPhanHoi&id='.$maPH.'&error=empty');
            exit;
        }
        
        // Kiểm tra độ dài: chỉ kiểm tra nội dung do người dùng nhập (<= 500)
        // Model sẽ thêm nhãn trạng thái và cắt chuỗi trước khi lưu vào DB.
        if (mb_strlen($noiDung, 'UTF-8') > 500) {
            header('Location: index.php?controller=cskh&action=chiTietPhanHoi&id=' . $maPH . '&error=toolong');
            exit;
        }
        
        // Xóa khoảng trắng thừa giữa các từ (nhiều khoảng trắng thành 1)
        $noiDung = preg_replace('/\s+/', ' ', $noiDung);
        
        // Viết hoa chỉ ký tự đầu tiên của câu
        $noiDung = mb_strtoupper(mb_substr($noiDung, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($noiDung, 1, null, 'UTF-8');
        
        $user = Auth::user();
        $maNV = $user['MaNhanVien'] ?? 0;
        
        $result = $model->processFeedback_QL($maPH, $maNV, $noiDung, $tinhTrang);
        
        if ($result) {
            // Lấy thông tin phản hồi để gửi email
            $info = $model->getOnePhanHoi_QL($maPH);
            
            if ($info && !empty($info['Email'])) {
                // Gửi email thông báo cho khách hàng
                require_once 'libraries/MailService.php';
                
                $tenKhach = $info['HoTenKH'] ?: $info['TenKH_Tk'] ?: 'Quý khách';
                $emailKhach = $info['Email'];
                
                try {
                    $emailSent = MailService::sendPhanHoi($emailKhach, $tenKhach, $noiDung);
                    
                    if ($emailSent) {
                        error_log("✅ Đã gửi email phản hồi đến: $emailKhach");
                        // Lưu log vào file
                        $logMessage = date('Y-m-d H:i:s') . " - Gửi email phản hồi thành công đến $emailKhach (Mã PH: $maPH)\n";
                        file_put_contents('email_log.txt', $logMessage, FILE_APPEND);
                    } else {
                        error_log("❌ Không gửi được email đến: $emailKhach");
                    }
                } catch (Exception $e) {
                    error_log("❌ Lỗi gửi email phản hồi: " . $e->getMessage());
                }
            } else {
                error_log("⚠️ Không có email khách hàng để gửi phản hồi (Mã PH: $maPH)");
            }
            
            header('Location: index.php?controller=cskh&action=chiTietPhanHoi&id='.$maPH.'&msg=success');
            exit;
        } else {
            header('Location: index.php?controller=cskh&action=chiTietPhanHoi&id='.$maPH.'&msg=error');
            exit;
        }
        }
    }



    /* ====================================================
     * DANH SÁCH KHÁCH HÀNG
     * ==================================================== */
    public function danhsachKhachHang() {
        // Chuyển hướng đến QuanlyController
        header('Location: index.php?controller=quanly&action=danhsachKhachHang');
        exit;
    }

    /* ====================================================
     * 1. XEM DANH SÁCH (Màn hình chính)
     * ==================================================== */
    public function action_xemDS_CSKH() {
        $model = new KhuyenMai();
        
        // Tự động cập nhật trạng thái "Hết hạn" cho các khuyến mãi quá ngày kết thúc
        $model->autoUpdateExpiredPromotions();
        
        // Gọi hàm Model mới: getAll_xemDS_CSKH
        $listKM = $model->getAll_xemDS_CSKH();

        $this->view('cskh/khuyenmai_list', [
            'listKM' => $listKM
        ]);
    }

    /* ====================================================
     * 2. HIỂN THỊ FORM TẠO MỚI
     * ==================================================== */
    public function action_formTaoKM_CSKH() {
        $this->view('cskh/khuyenmai_add', [
            'data' => [],
            'error' => []
        ]);
    }

    /* ====================================================
     * 3. XỬ LÝ LƯU (TẠO MỚI) - Logic TC5
     * ==================================================== */
    /* ====================================================
     * 3. XỬ LÝ LƯU (TẠO MỚI) - CHUẨN TESTCASE
     * ==================================================== */
    public function luuTaoKM_CSKH() { // <--- Tên hàm đã rút gọn
        // 1. Bật hiển thị lỗi để debug (tránh màn hình trắng)
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        
        // DEBUG: Kiểm tra hàm có được gọi
        echo "<!-- DEBUG: Function luuTaoKM_CSKH được gọi -->";
        error_log("===== BẮT ĐẦU luuTaoKM_CSKH =====");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("POST Data: " . print_r($_POST, true));
            echo "<!-- DEBUG: Bắt đầu xử lý POST -->";
            try {
                // --- NHẬN DỮ LIỆU ---
                $maCode    = trim($_POST['MaCode'] ?? '');
                $ten       = trim($_POST['TenCTKM'] ?? '');
                $ngayBD    = $_POST['NgayBatDau'];
                $ngayKT    = $_POST['NgayKetThuc'];
                $uuDaiRaw  = $_POST['MucUuDai']; 
                $doiTuong  = $_POST['DoiTuong'];
                $trangThai = $_POST['TrangThai'];
                
                // Chuẩn hóa dữ liệu: Xóa khoảng trắng thừa
                $maCode = preg_replace('/\s+/', ' ', $maCode); // Nhiều khoảng trắng thành 1
                $ten = preg_replace('/\s+/', ' ', $ten);
                
                // Ghi hoa tên chương trình và mã code
                $maCode = mb_strtoupper($maCode, 'UTF-8');
                $ten = mb_strtoupper($ten, 'UTF-8');
                
                // Cờ xác nhận "Vẫn lưu" (dùng cho trường hợp trùng thời gian)
                $forceSave = isset($_POST['force_save']) && $_POST['force_save'] == '1';

                $model = new KhuyenMai(); 
                $errors = []; // Mảng chứa lỗi
                
                // --- 2. VALIDATE DỮ LIỆU (Theo Test Case) ---

                // [TC2] Kiểm tra rỗng
                if ($ten === '') $errors['TenCTKM'] = 'Vui lòng nhập tên chương trình';
                if ($ngayBD === '') $errors['NgayBatDau'] = 'Vui lòng chọn ngày bắt đầu';
                if ($ngayKT === '') $errors['NgayKetThuc'] = 'Vui lòng chọn ngày kết thúc';
                if ($uuDaiRaw === '') $errors['MucUuDai'] = 'Vui lòng nhập mức ưu đãi';
                
                // [TC - Unique Name] Trùng Tên Chương Trình -> Chặn luôn
                if (!empty($ten) && $model->checkTenExists_taoKM_CSKH($ten)) {
                   $errors['TenCTKM'] = "Tên \"$ten\" đã tồn tại! Vui lòng chọn tên khác.";
                }
                
                // [TC - Kiểm tra định dạng ngày hợp lệ]
                if (!empty($ngayBD)) {
                    $dateCheck = \DateTime::createFromFormat('Y-m-d', $ngayBD);
                    if (!$dateCheck || $dateCheck->format('Y-m-d') !== $ngayBD) {
                        $errors['NgayBatDau'] = 'Ngày không hợp lệ! Vui lòng nhập đúng định dạng.';
                    }
                }
                if (!empty($ngayKT)) {
                    $dateCheck = \DateTime::createFromFormat('Y-m-d', $ngayKT);
                    if (!$dateCheck || $dateCheck->format('Y-m-d') !== $ngayKT) {
                        $errors['NgayKetThuc'] = 'Ngày không hợp lệ! Vui lòng nhập đúng định dạng.';
                    }
                }

                // [TC3] Logic Ngày (Kết thúc < Bắt đầu)
// [TC3] Logic Ngày
                if (!empty($ngayBD) && !empty($ngayKT)) {
                    if ($ngayBD > $ngayKT) {
                        $errors['NgayKetThuc'] = 'Ngày kết thúc phải sau ngày bắt đầu';
                    }
                    if (strtotime($ngayKT) < strtotime(date('Y-m-d'))) {
                        $errors['NgayKetThuc'] = 'Ngày kết thúc phải ở tương lai';
                    }
                    // Độ dài 1 năm
                    // [TC3 Nâng cao] Kiểm tra quá khứ & Độ dài 1 năm
                    $diff = strtotime($ngayKT) - strtotime($ngayBD);
                    $days = floor($diff / (60 * 60 * 24));
                    if ($days > 365) { // Tính khoảng cách ngày
                        $errors['NgayKetThuc'] = 'Chương trình không được kéo dài quá 1 năm (365 ngày)!';
                    }
                }


                // [TC4] Số tiền (Phải > 0)
                if (!empty($uuDaiRaw)) {
                    if (!is_numeric($uuDaiRaw) || (float)$uuDaiRaw <= 0) {
                        $errors['MucUuDai'] = 'Số tiền giảm giá phải lớn hơn 0';
                    } elseif ((float)$uuDaiRaw > 100000000) {
                        $errors['MucUuDai'] = 'Mức ưu đãi không được vượt quá 100,000,000đ';
                    } else {
                        $uuDai = (float)$uuDaiRaw;
                        
                        // Tự động xác định loại ưu đãi
                        if ($uuDai >= 1 && $uuDai <= 100) {
                            $loaiUuDai = 'PERCENT';
                        } elseif ($uuDai >= 1000) {
                            $loaiUuDai = 'FIXED';
                        } else {
                            $errors['MucUuDai'] = 'Mức ưu đãi không hợp lệ! Nhập 1-100 cho giảm %, hoặc >= 1000 cho giảm tiền cố định.';
                        }
                    }
                } else {
                    $uuDai = 0;
                }
                
                // Nếu có lỗi, trả về form với dữ liệu cũ và lỗi
                // === NẾU CÓ LỖI -> TRẢ VỀ FORM NGAY ===
                if (!empty($errors)) {
                    // Cập nhật dữ liệu đã chuẩn hóa để hiển thị lại
                    $_POST['TenCTKM'] = $ten;
                    
                    $this->view('cskh/khuyenmai_them', [
                        'data' => $_POST, // Trả lại dữ liệu người dùng vừa nhập
                        'errors' => $errors // Trả lại danh sách lỗi
                    ]);
                    return; // Dừng chạy hàm
                }


                // --- 3. CHECK TRÙNG LẶP THỜI GIAN (TC5 - Warning) ---
                // Nếu chưa có cờ forceSave VÀ Bị trùng -> Hiện Modal
                if (!$forceSave && $model->checkOverlap_taoKM_CSKH($doiTuong, $ngayBD, $ngayKT)) {
                    // Truyền data về view với cờ warning
                    $this->view('cskh/khuyenmai_them', [
                        'data' => $_POST,
                        'errors' => [],
                        'warningOverlap' => [
                            'doiTuong' => $doiTuong,
                            'message' => "Đã có chương trình khuyến mãi khác cho đối tượng <strong>\"$doiTuong\"</strong> trong khoảng thời gian này!"
                        ]
                    ]);
                    return;
                }

                // --- 4. LƯU VÀO DATABASE ---
                $data = [
                    'TenCTKM'   => $ten, 
                    'NgayBD'    => $ngayBD, 
                    'NgayKT'    => $ngayKT, 
                    'MucUuDai'  => $uuDai, 
                    'LoaiUuDai' => $loaiUuDai, // Tự động: PERCENT hoặc FIXED
                    'DoiTuong'  => $doiTuong, 
                    'TrangThai' => $trangThai
                ];

                if ($model->create_taoKM_CSKH($data)) {
                    // Thành công - Hiển thị form trống với thông báo success
                    $this->view('cskh/khuyenmai_them', [
                        'success' => 'Tạo chương trình "' . $ten . '" thành công! Bạn có thể tiếp tục tạo mới.',
                        'data' => [], // Form trống để nhập tiếp
                        'errors' => []
                    ]);
                    return;
                } else {
                    $errors['system'] = 'Lỗi hệ thống: Không thể lưu dữ liệu!';
                    $this->view('cskh/khuyenmai_them', [
                        'data' => $_POST,
                        'errors' => $errors
                    ]);
                    return;
                }

            } catch (Exception $e) {
                // In lỗi ra màn hình để biết tại sao
                // Lấy nội dung lỗi
                $msg = $e->getMessage();

                // 1. Kiểm tra lỗi trùng tên (như cũ)
                if (strpos($msg, "Duplicate entry") !== false && strpos($msg, "'TenChuongTrinh'") !== false) {
                    $errors['TenCTKM'] = "Tên chương trình \"$ten\" đã tồn tại! Vui lòng đặt tên khác.";
                }
                // 2. Nếu là các lỗi khác (SQL sai, mất kết nối, v.v...)
                else {
                    // Gán vào một key chung, ví dụ 'system'
                    $errors['system'] = "Lỗi hệ thống không mong muốn: " . $msg;
                }

                // QUAN TRỌNG: Gọi lại View để hiển thị lỗi (Không dùng die nữa)
                $this->view('cskh/khuyenmai_them', [
                    'data' => $_POST,   // Giữ lại dữ liệu cũ để không phải nhập lại
                    'errors' => $errors // Truyền lỗi sang View
                ]);
            }
        } else {
            echo "DEBUG: REQUEST_METHOD = " . $_SERVER['REQUEST_METHOD'] . "<br>";
            echo "<script>alert('Phương thức không hợp lệ! Method: " . $_SERVER['REQUEST_METHOD'] . "'); window.location.href='index.php?controller=cskh&action=action_formTaoKM_CSKH';</script>";
            exit;
        }
    }


    /* ====================================================
     * 4. HIỂN THỊ FORM SỬA KHUYẾN MÃI
     * ==================================================== */
    public function action_formSuaKM_CSKH() {
        $id = $_GET['id'] ?? 0;
        $model = new KhuyenMai();
        
        $data = $model->getOne_xemChiTiet_CSKH($id);
        
        if (!$data) {
            echo "<script>alert('Không tìm thấy khuyến mãi!'); window.location.href='index.php?controller=cskh&action=action_xemDS_CSKH';</script>";
            exit;
        }
        
        $this->view('cskh/khuyenmai_sua', [
            'data' => $data
        ]);
    }

    /* ====================================================
     * 5. XỬ LÝ LƯU CẬP NHẬT (Đáp ứng Test Case TC-CNKM)
     * ==================================================== */
    public function action_luuSuaKM_CSKH() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Nhận dữ liệu
                $id        = (int)$_POST['MaKhuyenMai']; 
                $maCode    = trim($_POST['MaCode'] ?? '');
                $ten       = trim($_POST['TenCTKM'] ?? '');
                $ngayBD    = $_POST['NgayBatDau'];
                $ngayKT    = $_POST['NgayKetThuc'];
                $uuDaiRaw  = $_POST['MucUuDai']; 
                $doiTuong  = $_POST['DoiTuong'];
                $trangThai = $_POST['TrangThai'];
                
                // Chuẩn hóa dữ liệu: Xóa khoảng trắng thừa
                $maCode = preg_replace('/\s+/', ' ', $maCode); // Nhiều khoảng trắng thành 1
                $ten = preg_replace('/\s+/', ' ', $ten);
                
                // Ghi hoa tên chương trình và mã code
                $maCode = mb_strtoupper($maCode, 'UTF-8');
                $ten = mb_strtoupper($ten, 'UTF-8');
                
                $forceSave = isset($_POST['force_save']) && $_POST['force_save'] == '1';
                $model = new KhuyenMai();
                $errors = []; // Mảng chứa lỗi

                // --- 1. VALIDATE CƠ BẢN ---
                if ($ten === '') $errors['TenCTKM'] = 'Vui lòng nhập tên chương trình';
                if ($ngayBD === '') $errors['NgayBatDau'] = 'Vui lòng chọn ngày bắt đầu';
                if ($ngayKT === '') $errors['NgayKetThuc'] = 'Vui lòng chọn ngày kết thúc';
                if ($uuDaiRaw === '') $errors['MucUuDai'] = 'Vui lòng nhập mức ưu đãi';
                
                // [TC - Kiểm tra định dạng ngày hợp lệ]
                if (!empty($ngayBD)) {
                    $dateCheck = \DateTime::createFromFormat('Y-m-d', $ngayBD);
                    if (!$dateCheck || $dateCheck->format('Y-m-d') !== $ngayBD) {
                        $errors['NgayBatDau'] = 'Ngày không hợp lệ! Vui lòng nhập đúng định dạng.';
                    }
                }
                if (!empty($ngayKT)) {
                    $dateCheck = \DateTime::createFromFormat('Y-m-d', $ngayKT);
                    if (!$dateCheck || $dateCheck->format('Y-m-d') !== $ngayKT) {
                        $errors['NgayKetThuc'] = 'Ngày không hợp lệ! Vui lòng nhập đúng định dạng.';
                    }
                }

                // [TC - Ngày không hợp lệ]
                if (!empty($ngayBD) && !empty($ngayKT) && $ngayBD > $ngayKT) {
                    $errors['NgayKetThuc'] = 'Ngày kết thúc phải sau ngày bắt đầu!';
                }

                // [TC4] Logic số tiền
                if (!empty($uuDaiRaw)) {
                    if (!is_numeric($uuDaiRaw) || (float)$uuDaiRaw <= 0) {
                        $errors['MucUuDai'] = 'Số tiền giảm giá phải lớn hơn 0';
                    } elseif ((float)$uuDaiRaw > 100000000) {
                        $errors['MucUuDai'] = 'Mức ưu đãi không được vượt quá 100,000,000đ (Một trăm triệu đồng)';
                    } else {
                        $uuDai = (float)$uuDaiRaw;
                        
                        // Tự động xác định loại ưu đãi
                        if ($uuDai >= 1 && $uuDai <= 100) {
                            $loaiUuDai = 'PERCENT';
                        } elseif ($uuDai >= 1000) {
                            $loaiUuDai = 'FIXED';
                        } else {
                            $errors['MucUuDai'] = 'Mức ưu đãi không hợp lệ! Nhập 1-100 cho giảm %, hoặc >= 1000 cho giảm tiền cố định.';
                        }
                    }
                } else {
                    $uuDai = 0;
                }
                
                // Nếu có lỗi, trả về form với dữ liệu cũ và lỗi
                if (!empty($errors)) {
                    // Cập nhật dữ liệu đã chuẩn hóa để hiển thị lại
                    $_POST['TenCTKM'] = $ten;
                    
                    // Lấy lại thông tin để hiển thị form
                    $info = $model->getOne_xemChiTiet_CSKH($id);
                    $this->view('cskh/khuyenmai_sua', [
                        'data' => $_POST,
                        'errors' => $errors,
                        'info' => $info
                    ]);
                    return;
                }
                
                // --- 2. CHECK TRÙNG LẶP THỜI GIAN (TC - Cảnh báo trùng) ---
                if (!$forceSave && $model->checkOverlap_taoKM_CSKH($doiTuong, $ngayBD, $ngayKT, $id)) {
                    // Truyền data về view với cờ warning
                    $this->view('cskh/khuyenmai_sua', [
                        'data' => $_POST,
                        'errors' => [],
                        'warningOverlap' => [
                            'doiTuong' => $doiTuong,
                            'message' => "Đã có chương trình khuyến mãi khác cho đối tượng <strong>\"$doiTuong\"</strong> trong khoảng thời gian này!"
                        ]
                    ]);
                    return;
                            
                }

                // --- 3. LƯU DỮ LIỆU ---
                $data = [
                    'TenCTKM' => $ten, 'NgayBD' => $ngayBD, 
                    'NgayKT' => $ngayKT, 'MucUuDai' => $uuDai, 
                    'LoaiUuDai' => $loaiUuDai, // Tự động: PERCENT hoặc FIXED
                    'DoiTuong' => $doiTuong, 'TrangThai' => $trangThai
                ];

                if ($model->update_suaKM_CSKH($id, $data)) {
                    // Thành công - Hiển thị lại form với thông báo success
                    $updatedData = $model->getOne_xemChiTiet_CSKH($id);
                    $this->view('cskh/khuyenmai_sua', [
                        'success' => 'Cập nhật chương trình "' . $ten . '" thành công!',
                        'data' => $updatedData,
                        'errors' => []
                    ]);
                    return;
                } else {
                    $errors['system'] = 'Lỗi hệ thống: Không thể cập nhật!';
                    $this->view('cskh/khuyenmai_sua', [
                        'data' => array_merge(['MaKhuyenMai' => $id], $_POST),
                        'errors' => $errors
                    ]);
                    return;
                }

            } catch (Exception $e) {
                echo "<script>alert('Lỗi ngoại lệ: {$e->getMessage()}'); window.history.back();</script>";
            }
        }
    }

    /* ====================================================
     * HÀM HỖ TRỢ: GỬI EMAIL THÔNG BÁO KHUYẾN MÃI
     * ==================================================== */
    private function sendPromotionEmail($doiTuong, $tenCTKM, $mucUuDai, $maCode = '', $ngayBD = '', $ngayKT = '') {
        try {
            require_once 'libraries/MailService.php';
            
            $db = Database::getConnection();
            
            // Lấy danh sách email theo đối tượng
            $sql = "";
            if ($doiTuong == 'Tất cả KH') {
                $sql = "SELECT Email, TenKH FROM khachhang WHERE Email IS NOT NULL AND Email != '' LIMIT 5";
            } elseif ($doiTuong == 'Khách VIP') {
                $sql = "SELECT DISTINCT kh.Email, kh.TenKH 
                        FROM khachhang kh 
                        INNER JOIN datphong dp ON kh.MaKhachHang = dp.MaKhachHang 
                        WHERE dp.TrangThai = 'DaThanhToan' 
                        AND kh.Email IS NOT NULL AND kh.Email != ''
                        GROUP BY kh.MaKhachHang 
                        HAVING SUM(dp.TongTien) >= 10000000
                        LIMIT 5";
            } elseif ($doiTuong == 'Khách mới') {
                $sql = "SELECT kh.Email, kh.TenKH 
                        FROM khachhang kh 
                        LEFT JOIN datphong dp ON kh.MaKhachHang = dp.MaKhachHang 
                        WHERE dp.MaDatPhong IS NULL 
                        AND kh.Email IS NOT NULL AND kh.Email != ''
                        LIMIT 5";
            }
            
            if (empty($sql)) {
                error_log("Không có đối tượng email phù hợp: " . $doiTuong);
                return;
            }
            
            $result = $db->query($sql);
            if (!$result) {
                error_log("Lỗi query email: " . $db->error);
                return;
            }
            
            $customers = $result->fetch_all(MYSQLI_ASSOC);
            
            if (empty($customers)) {
                error_log("Không tìm thấy khách hàng nào có email cho: " . $doiTuong);
                return;
            }
            
            $emailCount = 0;
            $errorCount = 0;
            
            // Format ngày theo định dạng đẹp hơn
            $ngayBDFormatted = !empty($ngayBD) ? date('d/m/Y', strtotime($ngayBD)) : date('d/m/Y');
            $ngayKTFormatted = !empty($ngayKT) ? date('d/m/Y', strtotime($ngayKT)) : date('d/m/Y', strtotime('+30 days'));
            
            // Format mức ưu đãi
            $mucUuDaiFormatted = number_format($mucUuDai, 0, ',', '.') . ' VNĐ';
            
            // Tạo mã code nếu không có
            $maCodeFinal = !empty($maCode) ? $maCode : 'KM' . date('Ymd') . rand(100, 999);
            
            // Gửi email cho từng khách hàng
            foreach ($customers as $customer) {
                try {
                    if (MailService::sendKhuyenMai(
                        $customer['Email'], 
                        $customer['TenKH'], 
                        $tenCTKM, 
                        $maCodeFinal, 
                        $mucUuDaiFormatted, 
                        $ngayBDFormatted, 
                        $ngayKTFormatted, 
                        $doiTuong
                    )) {
                        $emailCount++;
                        error_log("✅ Gửi email khuyến mãi thành công đến: " . $customer['Email']);
                    } else {
                        $errorCount++;
                        error_log("❌ Gửi email khuyến mãi thất bại đến: " . $customer['Email']);
                    }
                } catch (Exception $e) {
                    $errorCount++;
                    error_log("❌ Lỗi gửi email cho {$customer['Email']}: " . $e->getMessage());
                }
            }
            
            error_log("📊 Kết quả gửi email khuyến mãi '{$tenCTKM}': Thành công {$emailCount}/" . count($customers) . ", Lỗi {$errorCount}");
        } catch (Exception $e) {
            error_log("❌ Lỗi tổng quát gửi email khuyến mãi: " . $e->getMessage());
        }
    }
    
    /* ====================================================
     * AJAX CHECK TRÙNG TÊN KHUYẾN MÃI
     * ==================================================== */
    public function checkTenKhuyenMai() {
        header('Content-Type: application/json');
        
        $ten = trim($_GET['ten'] ?? '');
        $excludeId = (int)($_GET['excludeId'] ?? 0);
        
        if (empty($ten)) {
            echo json_encode(['exists' => false]);
            exit;
        }
        
        // Chuẩn hóa tên (giống như khi lưu)
        $ten = preg_replace('/\s+/', ' ', $ten);
        $ten = mb_strtoupper($ten, 'UTF-8');
        
        $model = new KhuyenMai();
        $exists = $model->checkTenExists_taoKM_CSKH($ten, $excludeId);
        
        echo json_encode(['exists' => $exists]);
        exit;
    }

    // AJAX: Kiểm tra trùng lặp thời gian (client-side pre-check)
    public function checkOverlapAjax() {
        header('Content-Type: application/json');
        $doiTuong = $_REQUEST['DoiTuong'] ?? '';
        $ngayBD = $_REQUEST['NgayBatDau'] ?? '';
        $ngayKT = $_REQUEST['NgayKetThuc'] ?? '';
        $excludeId = isset($_REQUEST['excludeId']) ? (int)$_REQUEST['excludeId'] : 0;

        if (empty($doiTuong) || empty($ngayBD) || empty($ngayKT)) {
            echo json_encode(['ok' => false, 'error' => 'Missing parameters']);
            exit;
        }

        $model = new KhuyenMai();
        $exists = $model->checkOverlap_taoKM_CSKH($doiTuong, $ngayBD, $ngayKT, $excludeId);

        $message = '';
        if ($exists) {
            $message = "Đã có chương trình khuyến mãi khác cho đối tượng \"$doiTuong\" trong khoảng thời gian này.";
        }

        echo json_encode(['ok' => true, 'overlap' => (bool)$exists, 'message' => $message]);
        exit;
    }
}
?>