<?php
require_once 'libraries/MailService.php';

class QuanlyController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
        $this->requireRole([6, 'QuanLy', 'Quản lý']);
    }

    /* ======================================
     *  DASHBOARD QUẢN LÝ
     * ====================================== */
    public function index()
    {
        $this->view('dashboard/quanly');
    }

    /* ======================================
     *  DANH SÁCH KHÁCH HÀNG + TÌM KIẾM
     * ====================================== */
  /*  public function danhsachKhachHang()
    {
        $kh      = new KhachHang();
        $keyword = trim($_GET['keyword'] ?? '');
        $deleteStatus = $_GET['delete'] ?? '';

        $customers = ($keyword !== '')
            ? $kh->searchCustomers($keyword)
            : $kh->getAllCustomers();

        $this->view('quanly/khachhang_sua', [
            'mode'        => 'list',
            'customers'   => $customers,
            'keyword'     => $keyword,
            'deleteStatus'=> $deleteStatus
        ]);
    }*/
public function danhsachKhachHang()
{
    $kh = new KhachHang();

    $cccd = trim($_GET['cccd'] ?? '');  
    $keyword = trim($_GET['keyword'] ?? ''); 

    // ƯU TIÊN TÌM THEO CCCD
    if ($cccd !== '') {
        $customers = $kh->searchByCCCDExact($cccd);
    }
    // KHÔNG CÓ CCCD → TÌM THEO KEYWORD CŨ (Tên, SĐT, Email...)
    else if ($keyword !== '') {
        $customers = $kh->searchCustomers($keyword);
    }
    // KHÔNG NHẬP GÌ → LẤY TẤT CẢ
    else {
        $customers = $kh->getAllCustomers();
    }

    $this->view('quanly/khachhang_sua', [
        'mode'      => 'list',
        'customers' => $customers,
        'cccd'      => $cccd,
        'keyword'   => $keyword
    ]);
}
    /* ======================================
     *  FORM THÊM KHÁCH
     * ====================================== */
    public function themKhachHang()
    {
        $this->view('quanly/khachhang_sua', [
            'mode'   => 'add',
            'data'   => [],
            'errors' => []
        ]);
    }

    /* ======================================
     *  LƯU THÊM KHÁCH MỚI
     * ====================================== */
    public function luuThem()
    {
        $TenKH = trim($_POST['TenKH'] ?? '');
        $SDT   = trim($_POST['SDT'] ?? '');
        $Email = trim($_POST['Email'] ?? '');
        $CCCD  = trim($_POST['CCCD'] ?? '');
        $Pass  = trim($_POST['Password'] ?? '');

        $errors = [];

        // VALIDATE
        if ($TenKH === '') $errors[] = 'Vui lòng nhập họ tên khách hàng.';
        if (!preg_match('/^(0|\+84)\d{8,10}$/', $SDT)) $errors[] = 'Số điện thoại không hợp lệ.';
        if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ.';
        if (!preg_match('/^\d{9,12}$/', $CCCD)) $errors[] = 'CCCD/CMND phải từ 9 đến 12 số.';
        if ($Pass === '' || strlen($Pass) < 6) $errors[] = 'Mật khẩu phải >= 6 ký tự.';

        $kh = new KhachHang();
        $tk = new TaiKhoan();

        // Kiểm tra trùng
        if (empty($errors)) {
            if ($kh->existsDuplicate($Email, $SDT, $CCCD)) {
                return $this->view('quanly/khachhang_sua', [
                    'mode'       => 'duplicate',
                    'duplicates' => $kh->findDuplicates($Email, $SDT, $CCCD),
                    'data'       => $_POST
                ]);
            }

            if ($tk->existsUsername($Email)) {
                $errors[] = 'Email đã tồn tại trong hệ thống (dùng làm Username).';
            }
        }

        if (!empty($errors)) {
            return $this->view('quanly/khachhang_sua', [
                'mode'   => 'add',
                'errors' => $errors,
                'data'   => $_POST
            ]);
        }

        // LƯU DB
        try {
            $db = Database::getConnection();
            $db->begin_transaction();

            // Tạo hồ sơ khách
            $maKH = $kh->create([
                'TenKH' => $TenKH,
                'SDT'   => $SDT,
                'Email' => $Email,
                'CCCD'  => $CCCD
            ]);

            /* FIX LỖI QUAN TRỌNG — tạo tài khoản phải truyền ARRAY */
            $tk->createForCustomer([
                'Username'    => $Email,
                'Password'    => $Pass,
                'MaKhachHang' => $maKH,
                'MaVaiTro'    => 7
            ]);

            $db->commit();

            return $this->view('quanly/khachhang_sua', [
                'mode'    => 'add',
                'success' => 'Thêm hồ sơ khách hàng thành công!',
                'data'    => []
            ]);

        } catch (Exception $e) {
            $db->rollback();

            return $this->view('quanly/khachhang_sua', [
                'mode'   => 'add',
                'errors' => ['Lỗi hệ thống, không thể lưu dữ liệu.'],
                'data'   => $_POST
            ]);
        }
    }

    /* ======================================
     *  FORM SỬA KHÁCH HÀNG
     * ====================================== */
    public function sua()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: index.php?controller=quanly&action=danhsachKhachHang');
            exit;
        }

        $kh  = new KhachHang();
        $row = $kh->getById($id);

        if (!$row) {
            header('Location: index.php?controller=quanly&action=danhsachKhachHang');
            exit;
        }

        return $this->view('quanly/khachhang_sua', [
            'mode' => 'edit',
            'kh'   => $row
        ]);
    }

    /* ======================================
     *  LƯU CẬP NHẬT KHÁCH HÀNG
     * ====================================== */
    public function luuCapNhat()
    {
        $id    = (int)($_POST['MaKhachHang'] ?? 0);
        $TenKH = trim($_POST['TenKH'] ?? '');
        $SDT   = trim($_POST['SDT'] ?? '');
        $Email = trim($_POST['Email'] ?? '');
        $CCCD  = trim($_POST['CCCD'] ?? '');
        $Pass  = trim($_POST['Password'] ?? '');

        if ($id <= 0) {
            header('Location: index.php?controller=quanly&action=danhsachKhachHang');
            exit;
        }

        $errors = [];

        // VALIDATE
        if ($TenKH === '') $errors[] = 'Vui lòng nhập họ tên khách hàng.';
        if (!preg_match('/^(0|\+84)\d{8,10}$/', $SDT)) $errors[] = 'SĐT không hợp lệ.';
        if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ.';
        if (!preg_match('/^\d{9,12}$/', $CCCD)) $errors[] = 'CCCD/CMND phải 9–12 số.';
        if ($Pass !== '' && strlen($Pass) < 6) $errors[] = 'Mật khẩu mới phải >= 6 ký tự.';

        $kh = new KhachHang();

        // Kiểm tra trùng
        if (empty($errors) && $kh->existsDuplicateForUpdate($id, $Email, $SDT, $CCCD)) {
            $errors[] = 'Thông tin bị trùng với khách hàng khác.';
        }

        if (!empty($errors)) {
            $postData = $_POST;
            $postData['MaKhachHang'] = $id;

            return $this->view('quanly/khachhang_sua', [
                'mode'   => 'edit',
                'errors' => $errors,
                'kh'     => $postData
            ]);
        }

        // UPDATE
        try {
            $kh->updateFull($id, [
                'TenKH' => $TenKH,
                'SDT'   => $SDT,
                'Email' => $Email,
                'CCCD'  => $CCCD
            ]);

            // Nếu có thay mật khẩu
            if ($Pass !== '') {
                (new TaiKhoan())->updatePasswordByCustomerId($id, $Pass);
            }

            return $this->view('quanly/khachhang_sua', [
                'mode'    => 'edit',
                'success' => 'Cập nhật hồ sơ thành công!',
                'kh'      => $kh->getById($id)
            ]);

        } catch (Exception $e) {
            $postData = $_POST;
            $postData['MaKhachHang'] = $id;

            return $this->view('quanly/khachhang_sua', [
                'mode'   => 'edit',
                'errors' => ['Có lỗi xảy ra khi lưu dữ liệu.'],
                'kh'     => $postData
            ]);
        }
    }

    /* ======================================
     *  XÓA KHÁCH HÀNG
     * ====================================== */
    public function xoa()
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            header('Location: index.php?controller=quanly&action=danhsachKhachHang');
            exit;
        }

        try {
            (new KhachHang())->delete($id);

            header('Location: index.php?controller=quanly&action=danhsachKhachHang&delete=success');
            exit;

        } catch (Exception $e) {
            header('Location: index.php?controller=quanly&action=danhsachKhachHang&delete=fail');
            exit;
        }
    }
    

#====vi-- quản lí=====

   /* ========================================================
     * XỬ LÝ PHẢN HỒI (Đã tối ưu theo Test Case TC-XLPH)
     * ======================================================== */

    public function phanHoi() {
        $model = new PhanHoiModel();
        $this->view('quanly/phanhoi_xuly', [
            'mode'      => 'list',
            'dsPhanHoi' => $model->getAllPhanHoi_QL()
        ]);
    }

    public function chiTietPhanHoi() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) header('Location: index.php?controller=quanly&action=phanHoi');

        $model = new PhanHoiModel();
        $info = $model->getOnePhanHoi_QL($id);
        
        if(!$info) die("Không tìm thấy phản hồi");

        $this->view('quanly/phanhoi_xuly', [
            'mode'    => 'detail',
            'info'    => $info,
            'history' => $model->getLichSu_QL($id)
        ]);
    }

    public function luuPhanHoi() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maPH = $_POST['MaPH'];
            $trangThai = $_POST['TinhTrang'] ?? '';
            $noiDung = trim($_POST['NoiDungTraLoi'] ?? '');
            $maNV = $_SESSION['user']['MaNhanVien'] ?? 1; 

            // --- KIỂM TRA ĐIỀU KIỆN (TEST CASES) ---

            // [TC04] Kiểm tra Trạng thái
            if (empty($trangThai)) {
                echo "<script>alert('Vui lòng chọn trạng thái xử lý!'); window.history.back();</script>";
                return;
            }

            // [TC05] Kiểm tra Nội dung rỗng
            if (empty($noiDung)) {
                echo "<script>alert('Vui lòng nhập nội dung trả lời!'); window.history.back();</script>";
                return;
            }

            // [TC12] Kiểm tra độ dài (CSDL varchar(500))
            if (mb_strlen($noiDung) > 500) {
                echo "<script>alert('Nội dung quá dài! Vui lòng nhập dưới 500 ký tự (Hiện tại: ".mb_strlen($noiDung).")'); window.history.back();</script>";
                return;
            }

            // --- XỬ LÝ ---
            $model = new PhanHoiModel();
            
            // [TC08] Xử lý lỗi Database
            $kqLuu = $model->processFeedback_QL($maPH, $maNV, $noiDung, $trangThai);

            if ($kqLuu) {
                // [TC01, TC02, TC11] Lưu thành công -> Gửi Mail
                $info = $model->getOnePhanHoi_QL($maPH);
                $tenKH = $info['HoTenKH'] ?? $info['TenKH_Tk'] ?? 'Quý khách';
                
                // [TC07] Xử lý lỗi Mạng/Mail (MailService trả về false nếu lỗi)
                $mailSent = false;
                if (!empty($info['Email'])) {
                    $mailSent = MailService::sendPhanHoi($info['Email'], $tenKH, $noiDung);
                }

                $msg = $mailSent ? "Gửi phản hồi thành công!" : "Đã lưu DB nhưng gửi mail thất bại (Kiểm tra mạng).";
                
                // [TC09] Chọn Thử lại (Ở đây là quay về danh sách để check lại)
                echo "<script>
                        alert('$msg');
                        window.location.href = 'index.php?controller=quanly&action=phanHoi';
                      </script>";
            } else {
                // [TC08] Thông báo lỗi hệ thống
                echo "<script>alert('Không thể lưu, vui lòng thử lại (Lỗi CSDL)!'); window.history.back();</script>";
            }
        }
    }

    // 1. Hiển thị danh sách phản hồi (CÓ TÌM KIẾM ĐA NĂNG + LỌC TRẠNG THÁI)
    public function searchPhanHoi() {
        $model = new PhanHoiModel();
        
        // Lấy từ khóa từ URL, xóa khoảng trắng thừa
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';

        if ($keyword !== '' || $status !== '') {
            // Gọi hàm tìm kiếm có lọc trạng thái
            $dataList = $model->searchPhanHoi_QL($keyword, $status);
        } else {
            // Không tìm thì lấy hết
            $dataList = $model->getAllPhanHoi_QL();
        }

        $this->view('quanly/phanhoi_xuly', [
            'mode'      => 'list',
            'dsPhanHoi' => $dataList,
            'keyword'   => $keyword,
            'status'    => $status
        ]);
    }


    /* ======================================
     * DANH SÁCH KHÁCH HÀNG (ĐÃ FIX TÌM KIẾM)
     * ====================================== */
    public function danhsachKhachHang_ql()
    {
        $kh = new KhachHang();

        // Chỉ lấy 1 tham số keyword duy nhất từ form
        $keyword = trim($_GET['keyword'] ?? ''); 

        if ($keyword !== '') {
            // Gọi hàm tìm kiếm đa năng trong Model
            // (Hàm này đã có logic tìm theo Tên OR Email OR SĐT OR CCCD)
            $customers = $kh->searchCustomers($keyword);
        } else {
            // Không nhập gì -> Lấy tất cả
            $customers = $kh->getAllCustomers();
        }

        $this->view('quanly/khachhang_sua', [
            'mode'      => 'list',
            'customers' => $customers,
            'keyword'   => $keyword
        ]);
    }


    /* ========================================================
     * GỬI THÔNG BÁO (EMAIL MARKETING)
     * ======================================================== */

    // 1. Hiển thị Form soạn thảo
    public function soanThongBao() {
        // Lấy danh sách khách hàng để chọn người nhận
        $khModel = new KhachHang(); // Giả sử bạn đã có Model KhachHang
        $listKH = $khModel->getAllCustomers(); // Hàm lấy tất cả khách

        $this->view('quanly/thongbao_form', [
            'listKH' => $listKH
        ]);
    }

    // 2. Xử lý gửi Email
    // 2. Xử lý gửi Email (Nâng cấp gửi nhiều người)
    public function guiThongBao() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tieuDe   = $_POST['TieuDe'] ?? '';
            $noiDung  = $_POST['NoiDung'] ?? '';
            // Lấy mảng người nhận (hoặc chuỗi nếu chỉ chọn 1)
            $nguoiNhan = $_POST['NguoiNhan'] ?? []; 
            if (!is_array($nguoiNhan)) $nguoiNhan = [$nguoiNhan];

            if (empty($tieuDe) || empty($noiDung) || empty($nguoiNhan)) {
                echo "<script>alert('Vui lòng nhập đủ thông tin!'); window.history.back();</script>"; return;
            }

            require_once 'libraries/MailService.php';
            $khModel = new KhachHang();
            $countSuccess = 0;

            // Kiểm tra nếu có chọn 'ALL' trong mảng
            if (in_array('ALL', $nguoiNhan)) {
                $listKH = $khModel->getAllCustomers();
                foreach ($listKH as $kh) {
                    if (!empty($kh['Email'])) {
                        if (MailService::sendEmailChung($kh['Email'], $kh['TenKH'], $tieuDe, $noiDung)) $countSuccess++;
                    }
                }
            } 
            else {
                // Gửi cho từng người được chọn
                foreach ($nguoiNhan as $maKH) {
                    $kh = $khModel->getById($maKH);
                    if ($kh && !empty($kh['Email'])) {
                        if (MailService::sendEmailChung($kh['Email'], $kh['TenKH'], $tieuDe, $noiDung)) $countSuccess++;
                    }
                }
            }

            echo "<script>
                    alert('Đã gửi thành công cho $countSuccess người!');
                    window.location.href = 'index.php?controller=quanly&action=soanThongBao';
                  </script>";
        }
    }
}