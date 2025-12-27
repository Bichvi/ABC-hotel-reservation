<?php

require_once __DIR__ . '/../libraries/MailService.php';

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

    /**
     * Backwards-compatible dashboard action used by views linking
     * to `controller=quanly&action=dashboard`.
     */
    public function dashboard()
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
        $username = trim($_GET['username'] ?? '');
    $loaiKhach = trim($_GET['loaiKhach'] ?? '');
    $coTaiKhoan = trim($_GET['coTaiKhoan'] ?? '');

    // ƯU TIÊN TÌM THEO CCCD
    if ($cccd !== '') {
        $customers = $kh->searchByCCCDExact($cccd);
    }
    // KHÔNG CÓ CCCD → TÌM THEO KEYWORD CŨ (Tên, SĐT, Email...)
    else if ($keyword !== '') {
        // If username/account filters provided, use the dedicated searchByAccount method
        if ($username !== '' || $coTaiKhoan !== '') {
            $customers = $kh->searchByAccount($keyword, $loaiKhach, $coTaiKhoan, $username);
        } else {
            $customers = $kh->searchCustomers($keyword, $loaiKhach, $coTaiKhoan);
        }
    }
    // KHÔNG NHẬP GÌ → LẤY TẤT CẢ (có thể có filter)
    else {
        if ($loaiKhach !== '' || $coTaiKhoan !== '' || $username !== '') {
            $customers = $kh->searchByAccount('', $loaiKhach, $coTaiKhoan, $username);
        } else {
            $customers = $kh->getAllCustomersWithUsername();
        }
    }

    $this->view('quanly/khachhang_sua', [
        'mode'      => 'list',
        'customers' => $customers,
        'cccd'      => $cccd,
            'keyword'   => $keyword,
            'username'  => $username,
            'loaiKhach' => $loaiKhach,
            'coTaiKhoan' => $coTaiKhoan
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
        $DiaChi = trim($_POST['DiaChi'] ?? '');
        $LoaiKhach = trim($_POST['LoaiKhach'] ?? '');
        
        $CreateAccount = isset($_POST['CreateAccount']) && $_POST['CreateAccount'] == '1';
        $Username = trim($_POST['Username'] ?? '');
        $Pass  = trim($_POST['Password'] ?? '');
        $ConfirmPass = trim($_POST['ConfirmPassword'] ?? '');

        $errors = [];

        // VALIDATE
        if ($TenKH === '') {
            $errors[] = 'Vui lòng nhập họ tên khách hàng.';
        } else {
            // Chuẩn hóa tên: Loại bỏ khoảng trắng thừa và viết hoa chữ cái đầu mỗi từ
            $TenKH = $this->formatName($TenKH);
            
            // Kiểm tra sau khi đã chuẩn hóa - Dùng \p{L} để match tất cả ký tự chữ cái Unicode
            if (!preg_match('/^[\p{L}\s]+$/u', $TenKH)) {
                $errors[] = "Họ tên chỉ được chứa chữ cái và khoảng trắng. Bạn đã nhập: '<strong>" . htmlspecialchars($TenKH) . "</strong>'";
            } elseif (mb_strlen($TenKH, 'UTF-8') < 3 || mb_strlen($TenKH, 'UTF-8') > 100) {
                $errors[] = 'Họ tên phải từ 3 đến 100 ký tự.';
            }
        }
        
        if (!preg_match('/^(0|\+84)(3|5|7|8|9)\d{8}$/', $SDT)) {
            $errors[] = 'Số điện thoại không hợp lệ (phải là số Việt Nam 10 số, bắt đầu 03/05/07/08/09).';
        }
        
        if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ.';
        } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $Email)) {
            $errors[] = 'Định dạng email không đúng.';
        }
        
        if (!preg_match('/^\d{9,12}$/', $CCCD)) {
            $errors[] = 'CCCD/CMND phải là 9-12 chữ số, không chứa ký tự đặc biệt.';
        }
        // Kiểm tra độ dài Địa chỉ để tránh lỗi DB (giới hạn 150 ký tự)
        if ($DiaChi !== '' && mb_strlen($DiaChi, 'UTF-8') > 150) {
            $errors[] = 'Địa chỉ quá dài. Vui lòng nhập tối đa 150 ký tự.';
        }

        if ($LoaiKhach === '') $errors[] = 'Vui lòng chọn loại khách.';
        
        // Validate tài khoản nếu được chọn
        if ($CreateAccount) {
            if ($Username === '') {
                $errors[] = 'Vui lòng nhập tên đăng nhập.';
            } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $Username)) {
                $errors[] = 'Tên đăng nhập chỉ chứa chữ, số, gạch dưới (3-30 ký tự).';
            }
            if ($Pass === '' || strlen($Pass) < 6) $errors[] = 'Mật khẩu phải >= 6 ký tự.';
            if ($Pass !== $ConfirmPass) $errors[] = 'Mật khẩu xác nhận không khớp.';
        }

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

            if ($CreateAccount && $tk->existsUsername($Username)) {
                $errors[] = "Tên đăng nhập '<strong>$Username</strong>' đã tồn tại trong hệ thống. Vui lòng chọn tên khác.";
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
                'CCCD'  => $CCCD,
                'DiaChi' => $DiaChi,
                'LoaiKhach' => $LoaiKhach
            ]);

            // Tạo tài khoản nếu được chọn
            if ($CreateAccount) {
                $maTK = $tk->createForCustomer([
                    'Username'    => $Username,
                    'Password'    => $Pass,
                    'MaKhachHang' => $maKH,
                    'MaVaiTro'    => 7
                ]);
                
                // Cập nhật MaTK cho khách hàng
                $kh->updateAccountLink($maKH, $maTK);
                
                // Gửi email thông báo tài khoản mới
                try {
                    $noiDungEmail = "
                        <div style='background: #f0fdf4; border-left: 4px solid #22c55e; padding: 15px; margin: 10px 0;'>
                            <h4 style='color: #15803d; margin-top: 0;'>🎉 Chúc mừng! Tài khoản của bạn đã được tạo thành công</h4>
                            <p><strong>Thông tin đăng nhập:</strong></p>
                            <ul style='background: white; padding: 15px; border-radius: 8px;'>
                                <li><strong>Tên đăng nhập:</strong> <code style='background: #1e293b; color: #22c55e; padding: 4px 8px; border-radius: 4px;'>$Username</code></li>
                                <li><strong>Mật khẩu:</strong> <code style='background: #1e293b; color: #22c55e; padding: 4px 8px; border-radius: 4px;'>$Pass</code></li>
                            </ul>
                            <div style='background: #fef3c7; border: 1px solid #fbbf24; padding: 10px; border-radius: 6px; margin-top: 15px;'>
                                <strong style='color: #92400e;'>⚠️ Lưu ý quan trọng:</strong>
                                <p style='margin: 5px 0 0 0; color: #78350f;'>Vui lòng lưu lại thông tin này và đổi mật khẩu sau khi đăng nhập lần đầu.</p>
                            </div>
                        </div>
                        
                    ";
                    
                    MailService::sendEmailChung(
                        $Email,
                        $TenKH,
                        '[ABC Resort] Tài khoản của bạn đã được tạo',
                        $noiDungEmail
                    );
                } catch (Exception $e) {
                    // Không chặn giao dịch nếu email lỗi
                    error_log("Lỗi gửi email: " . $e->getMessage());
                }
            }

            $db->commit();

            // Hiển thị thông tin vừa tạo
            $successData = [
                'TenKH' => $TenKH,
                'CCCD' => $CCCD,
                'SDT' => $SDT,
                'Email' => $Email,
                'LoaiKhach' => $LoaiKhach
            ];

            if ($CreateAccount) {
                $successData['Username'] = $Username;
                $successData['PlainPassword'] = $Pass; // Lưu password chưa mã hóa để hiển thị
            }

            return $this->view('quanly/khachhang_sua', [
                'mode'    => 'add',
                'success' => 'Thêm hồ sơ khách hàng thành công!' . ($CreateAccount ? ' Tài khoản đã được tạo.' : ''),
                'data'    => $successData
            ]);

        } catch (Exception $e) {
            $db->rollback();

            $errMsg = $e->getMessage();
            // Map DB 'data too long' error for DiaChi to friendly message
            if (stripos($errMsg, "Data too long for column 'DiaChi'") !== false || stripos($errMsg, 'Data too long for column \"DiaChi\"') !== false) {
                $friendly = 'Địa chỉ quá dài. Vui lòng rút ngắn (tối đa 150 ký tự).';
                $errorsOut = [$friendly];
            } else {
                $errorsOut = ['Lỗi hệ thống: ' . $errMsg];
            }

            return $this->view('quanly/khachhang_sua', [
                'mode'   => 'add',
                'errors' => $errorsOut,
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
        $tk  = new TaiKhoan();
        $row = $kh->getById($id);

        if (!$row) {
            header('Location: index.php?controller=quanly&action=danhsachKhachHang');
            exit;
        }

        // Lấy thông tin tài khoản nếu có
        if (!empty($row['MaTK'])) {
            $accountInfo = $tk->getById($row['MaTK']);
            if ($accountInfo) {
                $row['Username'] = $accountInfo['Username'];
            }
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
        $DiaChi = trim($_POST['DiaChi'] ?? '');
        $LoaiKhach = trim($_POST['LoaiKhach'] ?? '');
        $Pass  = trim($_POST['Password'] ?? '');
        
        $CreateAccount = isset($_POST['CreateAccount']) && $_POST['CreateAccount'] == '1';
        $Username = trim($_POST['Username'] ?? '');
        $ConfirmPass = trim($_POST['ConfirmPassword'] ?? '');

        if ($id <= 0) {
            header('Location: index.php?controller=quanly&action=danhsachKhachHang');
            exit;
        }

        $errors = [];

        // VALIDATE
        if ($TenKH === '') {
            $errors[] = 'Vui lòng nhập họ tên khách hàng.';
        } else {
            // Chuẩn hóa tên: Loại bỏ khoảng trắng thừa và viết hoa chữ cái đầu mỗi từ
            $TenKH = $this->formatName($TenKH);
            
            // Kiểm tra sau khi đã chuẩn hóa - Dùng \p{L} để match tất cả ký tự chữ cái Unicode
            if (!preg_match('/^[\p{L}\s]+$/u', $TenKH)) {
                $errors[] = "Họ tên chỉ được chứa chữ cái và khoảng trắng. Bạn đã nhập: '<strong>" . htmlspecialchars($TenKH) . "</strong>'";
            } elseif (mb_strlen($TenKH, 'UTF-8') < 3 || mb_strlen($TenKH, 'UTF-8') > 100) {
                $errors[] = 'Họ tên phải từ 3 đến 100 ký tự.';
            }
        }
        
        if (!preg_match('/^(0|\+84)(3|5|7|8|9)\d{8}$/', $SDT)) {
            $errors[] = 'Số điện thoại không hợp lệ (phải là số Việt Nam 10 số, bắt đầu 03/05/07/08/09).';
        }
        
        if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ.';
        } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $Email)) {
            $errors[] = 'Định dạng email không đúng.';
        }
        
        if (!preg_match('/^\d{9,12}$/', $CCCD)) {
            $errors[] = 'CCCD/CMND phải là 9-12 chữ số, không chứa ký tự đặc biệt.';
        }
        // Kiểm tra độ dài Địa chỉ để tránh lỗi DB (giới hạn 150 ký tự)
        if ($DiaChi !== '' && mb_strlen($DiaChi, 'UTF-8') > 150) {
            $errors[] = 'Địa chỉ quá dài. Vui lòng nhập tối đa 150 ký tự.';
        }
        if ($LoaiKhach === '') $errors[] = 'Vui lòng chọn loại khách.';
        
        $kh = new KhachHang();
        $tk = new TaiKhoan();
        
        $currentData = $kh->getById($id);
        $hasMaTK = !empty($currentData['MaTK']);
        
        // Validate tạo tài khoản mới
        if ($CreateAccount && !$hasMaTK) {
            if ($Username === '') {
                $errors[] = 'Vui lòng nhập tên đăng nhập.';
            } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $Username)) {
                $errors[] = 'Tên đăng nhập chỉ chứa chữ, số, gạch dưới (3-30 ký tự).';
            }
            if ($Pass === '' || strlen($Pass) < 6) $errors[] = 'Mật khẩu phải >= 6 ký tự.';
            if ($Pass !== $ConfirmPass) $errors[] = 'Mật khẩu xác nhận không khớp.';
            
            if ($Username !== '' && $tk->existsUsername($Username)) {
                $errors[] = "Tên đăng nhập '<strong>$Username</strong>' đã tồn tại trong hệ thống. Vui lòng chọn tên khác.";
            }
        }
        
        // Validate đổi mật khẩu (nếu đã có tài khoản)
        if ($hasMaTK && $Pass !== '' && strlen($Pass) < 6) {
            $errors[] = 'Mật khẩu mới phải >= 6 ký tự.';
        }

        // Kiểm tra trùng
        if (empty($errors) && $kh->existsDuplicateForUpdate($id, $Email, $SDT, $CCCD)) {
            $errors[] = 'Thông tin bị trùng với khách hàng khác.';
        }

        if (!empty($errors)) {
            $postData = $_POST;
            $postData['MaKhachHang'] = $id;
            if (!empty($currentData['MaTK'])) {
                $accountInfo = $tk->getById($currentData['MaTK']);
                if ($accountInfo) {
                    $postData['Username'] = $accountInfo['Username'];
                }
            }

            return $this->view('quanly/khachhang_sua', [
                'mode'   => 'edit',
                'errors' => $errors,
                'kh'     => $postData
            ]);
        }

        // UPDATE
        try {
            $db = Database::getConnection();
            $db->begin_transaction();
            
            $kh->updateFull($id, [
                'TenKH' => $TenKH,
                'SDT'   => $SDT,
                'Email' => $Email,
                'CCCD'  => $CCCD,
                'DiaChi' => $DiaChi,
                'LoaiKhach' => $LoaiKhach
            ]);

            // Nếu đã có tài khoản và có thay mật khẩu
            if ($hasMaTK && $Pass !== '') {
                $tk->updatePasswordByCustomerId_QL($id, $Pass);
                
                // Gửi email thông báo đổi mật khẩu
                try {
                    $noiDungEmail = "
                        <div style='background: #fef3c7; border-left: 4px solid #fbbf24; padding: 15px; margin: 10px 0;'>
                            <h4 style='color: #92400e; margin-top: 0;'>🔑 Mật khẩu của bạn đã được thay đổi</h4>
                            <p>Chúng tôi xác nhận rằng mật khẩu tài khoản của bạn vừa được cập nhật bởi quản lý.</p>
                            <p><strong>Mật khẩu mới của bạn:</strong> <code style='background: #1e293b; color: #fbbf24; padding: 4px 12px; border-radius: 4px; font-size: 16px;'>$Pass</code></p>
                            <div style='background: #fee2e2; border: 1px solid #ef4444; padding: 10px; border-radius: 6px; margin-top: 15px;'>
                                <strong style='color: #991b1b;'>⚠️ Chú ý bảo mật:</strong>
                                <p style='margin: 5px 0 0 0; color: #7f1d1d;'>Nếu bạn không yêu cầu thay đổi này, vui lòng liên hệ bộ phận quản lý ngay lập tức!</p>
                            </div>
                        </div>
                        <p><strong>Bạn có thể đăng nhập với mật khẩu mới tại:</strong> <a href='http://localhost/final/final/code1/'>ABC Resort</a></p>
                    ";
                    
                    MailService::sendEmailChung(
                        $Email,
                        $TenKH,
                        '[ABC Resort] Mật khẩu của bạn đã được thay đổi',
                        $noiDungEmail
                    );
                } catch (Exception $e) {
                    error_log("Lỗi gửi email: " . $e->getMessage());
                }
            }
            
            // Nếu chưa có tài khoản và chọn tạo mới
            if (!$hasMaTK && $CreateAccount) {
                $maTK = $tk->createForCustomer([
                    'Username'    => $Username,
                    'Password'    => $Pass,
                    'MaKhachHang' => $id,
                    'MaVaiTro'    => 7
                ]);
                
                $kh->updateAccountLink($id, $maTK);
                
                // Gửi email thông báo tài khoản mới
                try {
                    $noiDungEmail = "
                        <div style='background: #f0fdf4; border-left: 4px solid #22c55e; padding: 15px; margin: 10px 0;'>
                            <h4 style='color: #15803d; margin-top: 0;'>🎉 Tài khoản của bạn đã được tạo thành công</h4>
                            <p><strong>Thông tin đăng nhập:</strong></p>
                            <ul style='background: white; padding: 15px; border-radius: 8px;'>
                                <li><strong>Tên đăng nhập:</strong> <code style='background: #1e293b; color: #22c55e; padding: 4px 8px; border-radius: 4px;'>$Username</code></li>
                                <li><strong>Mật khẩu:</strong> <code style='background: #1e293b; color: #22c55e; padding: 4px 8px; border-radius: 4px;'>$Pass</code></li>
                            </ul>
                            <div style='background: #fef3c7; border: 1px solid #fbbf24; padding: 10px; border-radius: 6px; margin-top: 15px;'>
                                <strong style='color: #92400e;'>⚠️ Lưu ý quan trọng:</strong>
                                <p style='margin: 5px 0 0 0; color: #78350f;'>Vui lòng lưu lại thông tin này và đổi mật khẩu sau khi đăng nhập lần đầu.</p>
                            </div>
                        </div>
                        <p><strong>Bạn có thể đăng nhập tại:</strong> <a href='http://localhost/final/final/code1/'>ABC Resort</a></p>
                    ";
                    
                    MailService::sendEmailChung(
                        $Email,
                        $TenKH,
                        '[ABC Resort] Tài khoản của bạn đã được tạo',
                        $noiDungEmail
                    );
                } catch (Exception $e) {
                    error_log("Lỗi gửi email: " . $e->getMessage());
                }
            }
            
            $db->commit();

            // Lấy dữ liệu mới nhất để hiển thị
            $updatedData = $kh->getById($id);
            if (!empty($updatedData['MaTK'])) {
                $accountInfo = $tk->getById($updatedData['MaTK']);
                if ($accountInfo) {
                    $updatedData['Username'] = $accountInfo['Username'];
                }
            }

            // Thêm mật khẩu mới nếu có thay đổi/tạo mới
            if ($Pass !== '') {
                $updatedData['PlainPassword'] = $Pass;
            }

            return $this->view('quanly/khachhang_sua', [
                'mode'    => 'edit',
                'success' => 'Cập nhật hồ sơ thành công!' . 
                            ($CreateAccount && !$hasMaTK ? ' Tài khoản đã được tạo.' : '') . 
                            ($hasMaTK && $Pass !== '' ? ' Mật khẩu đã được đổi.' : ''),
                'kh'      => $updatedData
            ]);

        } catch (Exception $e) {
            $db->rollback();
            
            $postData = $_POST;
            $postData['MaKhachHang'] = $id;

            $errMsg = $e->getMessage();
            if (stripos($errMsg, "Data too long for column 'DiaChi'") !== false || stripos($errMsg, 'Data too long for column \"DiaChi\"') !== false) {
                $errorsOut = ['Địa chỉ quá dài. Vui lòng rút ngắn (tối đa 150 ký tự).'];
            } else {
                $errorsOut = ['Có lỗi xảy ra: ' . $errMsg];
            }

            return $this->view('quanly/khachhang_sua', [
                'mode'   => 'edit',
                'errors' => $errorsOut,
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

    /* ======================================
     *  XÓA KHÁCH HÀNG (KIỂM TRA RÀNG BUỘC)
     * ====================================== */
    public function xoaKhachHang()
    {
        $id = (int)($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            header('Location: index.php?controller=quanly&action=danhsachKhachHang');
            exit;
        }

        $kh = new KhachHang();
        
        // Lấy thông tin khách hàng để hiển thị thông báo
        $customer = $kh->getById($id);
        
        if (!$customer) {
            echo "<script>
                alert('Không tìm thấy khách hàng!');
                window.location.href = 'index.php?controller=quanly&action=danhsachKhachHang';
            </script>";
            exit;
        }

        // Kiểm tra ràng buộc trước khi xóa
        $checkResult = $kh->checkDeleteConstraints($id);
        
        if (!$checkResult['can_delete']) {
            // Không thể xóa do vi phạm ràng buộc - Hiển thị view với thông tin chi tiết
            $this->view('quanly/khachhang_xoa_loi', [
                'customer' => $customer,
                'checkResult' => $checkResult
            ]);
            exit;
        }

        // Thực hiện xóa
        $deleteSuccess = $kh->deleteCustomer($id);
        
        if ($deleteSuccess) {
            // Hiển thị trang kết quả với thông báo thành công đẹp hơn
            $this->view('quanly/khachhang_xoa_loi', [
                'customer' => $customer,
                'checkResult' => [],
                'successMessage' => "Xóa hồ sơ khách hàng '" . $customer['TenKH'] . "' thành công!"
            ]);
            exit;
        } else {
            // Lỗi khi xóa - chuyển về danh sách với thông báo lỗi (giữ hành vi cũ)
            echo "<script>
                alert('Có lỗi xảy ra khi xóa khách hàng!');
                window.location.href = 'index.php?controller=quanly&action=danhsachKhachHang';
            </script>";
            exit;
        }
    }

    
    /**
     * Chuẩn hóa tên: Viết hoa chữ cái đầu mỗi từ, viết thường phần còn lại
     * Ví dụ: "NgUyỄn vĂn A" -> "Nguyễn Văn A"
     */
    private function formatName($name) {
        // Loại bỏ khoảng trắng thừa
        $name = trim(preg_replace('/\s+/', ' ', $name));
        
        // Tách thành các từ
        $words = explode(' ', $name);
        
        // Chuẩn hóa từng từ
        $formattedWords = array_map(function($word) {
            if (empty($word)) return $word;
            // Viết hoa ký tự đầu, viết thường phần còn lại (hỗ trợ UTF-8)
            return mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8') . 
                   mb_strtolower(mb_substr($word, 1, null, 'UTF-8'), 'UTF-8');
        }, $words);
        
        return implode(' ', $formattedWords);
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

            // Chuẩn hóa ký tự xuống dòng: ép tất cả CRLF/CR về LF duy nhất
            // (Giúp tránh trường hợp CRLF bị tính thành 2 ký tự trên một số Host)
            $noiDung = str_replace(["\r\n", "\r"], "\n", $noiDung);
            $maNV = $_SESSION['user']['MaNhanVien'] ?? 1; 

            // --- KIỂM TRA ĐIỀU KIỆN (TEST CASES) ---

            // [TC04] Kiểm tra Trạng thái
            if (empty($trangThai)) {
                header('Location: index.php?controller=quanly&action=chiTietPhanHoi&id=' . $maPH . '&error=status');
                exit;
            }

            // [TC05] Kiểm tra Nội dung rỗng
            if (empty($noiDung)) {
                header('Location: index.php?controller=quanly&action=chiTietPhanHoi&id=' . $maPH . '&error=empty');
                exit;
            }

            // [TC12] Kiểm tra độ dài (CSDL varchar(500))
            // Chỉ kiểm tra độ dài nội dung do người dùng nhập (<= 500).
            // Model sẽ tự xử lý việc thêm nhãn trạng thái và cắt chuỗi cho phù hợp trước khi lưu.
            if (mb_strlen($noiDung, 'UTF-8') > 500) {
                header('Location: index.php?controller=quanly&action=chiTietPhanHoi&id=' . $maPH . '&error=toolong');
                exit;
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

                // Redirect về chi tiết phản hồi với thông báo thành công
                header('Location: index.php?controller=quanly&action=chiTietPhanHoi&id=' . $maPH . '&msg=success');
                exit;
            } else {
                // [TC08] Thông báo lỗi hệ thống
                header('Location: index.php?controller=quanly&action=chiTietPhanHoi&id=' . $maPH . '&msg=error');
                exit;
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
            $customers = $kh->getAllCustomersWithUsername();
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
        $listKH = $khModel->getAllCustomersWithUsername(); // Hàm lấy tất cả khách

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
                $listKH = $khModel->getAllCustomersWithUsername();
                foreach ($listKH as $kh) {
                    if (!empty($kh['Email'])) {
                        if (MailService::sendEmailChung($kh['Email'], $kh['TenKH'], $tieuDe, $noiDung)) $countSuccess++;
                    }
                }
            } 
            else {
                // Gửi cho từng người được chọn
                foreach ($nguoiNhan as $maKH) {
                    // Kiểm tra xem có phải email nhập tay không (chứa ký tự @)
                    if (strpos($maKH, '@') !== false) {
                        // Đây là email nhập tay, gửi trực tiếp
                        if (MailService::sendEmailChung($maKH, 'Quý khách', $tieuDe, $noiDung)) $countSuccess++;
                    } else {
                        // Đây là MaKhachHang, lấy thông tin từ database
                        $kh = $khModel->getById($maKH);
                        if ($kh && !empty($kh['Email'])) {
                            if (MailService::sendEmailChung($kh['Email'], $kh['TenKH'], $tieuDe, $noiDung)) $countSuccess++;
                        }
                    }
                }
            }

            echo "<script>
                    alert('Đã gửi thành công cho $countSuccess người!');
                    window.location.href = 'index.php?controller=quanly&action=soanThongBao';
                  </script>";
        }
    }

    /**
     * AJAX: Kiểm tra tên đăng nhập đã tồn tại hay chưa.
     * Params: username, excludeMaTK (optional)
     * Returns JSON: { exists: true|false }
     */
    public function checkUsernameAjax() {
        header('Content-Type: application/json');

        $username = trim($_GET['username'] ?? '');
        $excludeMaTK = isset($_GET['excludeMaTK']) ? (int)$_GET['excludeMaTK'] : 0;

        if ($username === '') {
            echo json_encode(['exists' => false]);
            exit;
        }

        try {
            $db = Database::getConnection();
            $sql = "SELECT MaTK FROM taikhoan WHERE Username = ? LIMIT 1";
            $stmt = $db->prepare($sql);
            if (!$stmt) {
                echo json_encode(['exists' => false, 'error' => 'prepare_failed']);
                exit;
            }
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();

            $exists = false;
            if ($row) {
                $foundMaTK = (int)$row['MaTK'];
                if ($excludeMaTK > 0 && $foundMaTK === $excludeMaTK) {
                    $exists = false;
                } else {
                    $exists = true;
                }
            }

            echo json_encode(['exists' => $exists]);
            exit;
        } catch (Exception $e) {
            echo json_encode(['exists' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * AJAX: Kiểm tra trùng theo một trường của khách hàng (CCCD / SDT / Email)
     * Params: field (cccd|sdt|email), value, excludeMaKhachHang (optional)
     * Returns JSON: { exists: bool, field: 'CCCD'|'SDT'|'Email', ma: int|null, name: string|null }
     */
    public function checkCustomerFieldAjax() {
        header('Content-Type: application/json');

        $field = trim(strtolower($_GET['field'] ?? ''));
        $value = trim($_GET['value'] ?? '');
        $exclude = isset($_GET['excludeMaKhachHang']) ? (int)$_GET['excludeMaKhachHang'] : 0;

        if ($field === '' || $value === '') {
            echo json_encode(['exists' => false]);
            exit;
        }

        // Map allowed fields to column names
        $map = [
            'cccd' => 'CCCD',
            'sdt'  => 'SDT',
            'email'=> 'Email'
        ];

        if (!isset($map[$field])) {
            echo json_encode(['exists' => false, 'error' => 'invalid_field']);
            exit;
        }

        $col = $map[$field];

        try {
            $db = Database::getConnection();
            if ($exclude > 0) {
                $sql = "SELECT MaKhachHang, TenKH FROM khachhang WHERE $col = ? AND MaKhachHang <> ? LIMIT 1";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('si', $value, $exclude);
            } else {
                $sql = "SELECT MaKhachHang, TenKH FROM khachhang WHERE $col = ? LIMIT 1";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('s', $value);
            }

            if (!$stmt) {
                echo json_encode(['exists' => false, 'error' => 'prepare_failed']);
                exit;
            }

            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();

            if ($row) {
                echo json_encode([
                    'exists' => true,
                    'field'  => strtoupper($col),
                    'ma'     => (int)$row['MaKhachHang'],
                    'name'   => $row['TenKH'] ?? null
                ]);
            } else {
                echo json_encode(['exists' => false]);
            }
            exit;
        } catch (Exception $e) {
            echo json_encode(['exists' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    /* ======================================
     *  KIỂM TRA KHUYẾN MÃI
     * ====================================== */
    public function kiemTraKhuyenMai()
    {
        require_once __DIR__ . '/../models/KhuyenMai.php';

        $model = new KhuyenMai();
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $danhSach = $model->layDanhSachKhuyenMai($limit, $offset);
        $total = $model->countKhuyenMai();
        $totalPages = ceil($total / $limit);

        // Thống kê
        $khuyenMaiDangApDung = $model->layKhuyenMaiDangApDung();
        $khuyenMaiSapDienRa = $model->layKhuyenMaiSapDienRa();
        $khuyenMaiTamNgung = $model->layKhuyenMaiTamNgung();
        $khuyenMaiHetHan = $model->layKhuyenMaiHetHan();

        $this->view('quanly/kiemTraKhuyenMai', [
            'user' => $this->user,
            'danhSach' => $danhSach,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'khuyenMaiDangApDung' => $khuyenMaiDangApDung,
            'khuyenMaiSapDienRa' => $khuyenMaiSapDienRa,
            'khuyenMaiTamNgung' => $khuyenMaiTamNgung,
            'khuyenMaiHetHan' => $khuyenMaiHetHan
        ]);
    }

    /**
     * Xem chi tiết khuyến mãi
     */
    public function chiTietKhuyenMai()
    {
        require_once __DIR__ . '/../models/KhuyenMai.php';

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: index.php?controller=quanly&action=kiemTraKhuyenMai&error=ID không hợp lệ');
            exit;
        }

        $model = new KhuyenMai();
        $khuyenMai = $model->getById($id);

        if (!$khuyenMai) {
            header('Location: index.php?controller=quanly&action=kiemTraKhuyenMai&error=Khuyến mãi không tồn tại');
            exit;
        }

        $this->view('quanly/chiTietKhuyenMai', [
            'user' => $this->user,
            'khuyenMai' => $khuyenMai
        ]);
    }
}
