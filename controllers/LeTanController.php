<?php
class LeTanController extends Controller
{
    // Trang menu chính của lễ tân
    public function index()
    {
        $this->requireRole(['LeTan']);
        $user = Auth::user();
        $this->view("dashboard/letan", compact('user'));
    }

    // =================== USE CASE: ĐẶT PHÒNG TRỰC TIẾP ===================
    public function datPhongTrucTiep()
    {
        $this->requireRole(['LeTan']);

        $phongModel = new Phong();
        $dvModel    = new DichVu();
        $khModel    = new KhachHang();
        $gdModel    = new GiaoDich();
        $ctgdModel  = new ChiTietGiaoDich();
        $ctdvModel  = new ChiTietDichVu();

        $errors  = [];
        $success = null;
        $maGiaoDichVuaTao = null;

        // dữ liệu cho view
        $dsPhong   = null;
        $dsDichVu  = $dvModel->getActive();
        $hasSearch = false;

        // default ngày
        $ngayDen = $_POST['ngay_den'] ?? date('Y-m-d');
        $ngayDi  = $_POST['ngay_di']  ?? date('Y-m-d', strtotime('+1 day'));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $action = $_POST['btn_action'] ?? 'search'; // search / book

            // === 1. Lấy dữ liệu form ===
            $tenKH  = trim($_POST['ten_kh'] ?? '');
            $cccd   = trim($_POST['cccd'] ?? '');
            $sdt    = trim($_POST['sdt'] ?? '');
            $email  = trim($_POST['email'] ?? '');
            $diachi = trim($_POST['diachi'] ?? '');

            $ngayDen = $_POST['ngay_den'] ?? '';
            $ngayDi  = $_POST['ngay_di'] ?? '';
            $soNguoi = (int)($_POST['so_nguoi'] ?? 1);

            $roomsSelected    = $_POST['rooms'] ?? [];     // mảng MaPhong
            $servicesSelected = $_POST['services'] ?? [];  // mảng MaDichVu => số lượng

            // === 2. VALIDATE THÔNG TIN KHÁCH HÀNG (Bước 4 + 5 + alt flow 4.1) ===
            if ($tenKH === '') {
                $errors[] = "Vui lòng nhập họ tên khách hàng.";
            }

            if ($cccd === '') {
                $errors[] = "Vui lòng nhập CMND/CCCD.";
            } elseif (!preg_match('/^\d{9,12}$/', $cccd)) {
                // CMND sai định dạng (Testcase 4)
                $errors[] = "CMND/CCCD sai định dạng (chỉ 9–12 chữ số).";
            }

            if ($sdt === '') {
                $errors[] = "Vui lòng nhập số điện thoại.";
            } elseif (!preg_match('/^0\d{8,10}$/', $sdt)) {
                $errors[] = "Số điện thoại sai định dạng.";
            }

            if ($email === '') {
                $errors[] = "Vui lòng nhập email.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // Email sai định dạng (Testcase 3)
                $errors[] = "Email sai định dạng.";
            }

            // === 3. VALIDATE YÊU CẦU ĐẶT PHÒNG (Bước 6 + alt flow 6.2) ===
            if ($ngayDen === '' || $ngayDi === '') {
                $errors[] = "Vui lòng chọn ngày đến và ngày đi.";
            } else {
                $today  = strtotime(date('Y-m-d'));
                $denTs  = strtotime($ngayDen);
                $diTs   = strtotime($ngayDi);

                if ($denTs < $today) {
                    $errors[] = "Ngày đến không được nhỏ hơn ngày hiện tại.";
                }
                if ($diTs <= $denTs) {
                    // Testcase 5
                    $errors[] = "Ngày đi phải lớn hơn ngày đến.";
                }
            }

            if ($soNguoi <= 0) {
                $errors[] = "Số khách phải lớn hơn 0.";
            }

            // Nếu là hành động ĐẶT PHÒNG thì bắt buộc phải chọn ít nhất 1 phòng
            if ($action === 'book' && empty($roomsSelected)) {
                // KHÁC với nút Tìm phòng – chỉ book mới cần phòng
                $errors[] = "Vui lòng chọn ít nhất một phòng trước khi nhấn Đặt phòng.";
            }

            // Nếu mới chỉ TÌM PHÒNG (search) mà có lỗi → không truy vấn phòng
            if ($action === 'search' && !empty($errors)) {
                $hasSearch = false;  // chưa có kết quả
            }

            // Nếu không có lỗi đến đây → tiếp tục
            if (empty($errors)) {

                // === 4. Nếu là TÌM PHÒNG (Step 6–7) → chỉ truy vấn phòng trống phù hợp ===
                if ($action === 'search') {

                    $hasSearch = true;

                    // Tùy em cài đặt: lọc theo ngày + số khách
                    if (method_exists($phongModel, 'searchAvailableByRequest')) {
                        $dsPhong = $phongModel->searchAvailableByRequest($ngayDen, $ngayDi, $soNguoi);
                    } else {
                        // fallback: lấy danh sách phòng đang Trống
                        $dsPhong = $phongModel->searchAvailable();
                    }

                    if ($dsPhong instanceof mysqli_result && $dsPhong->num_rows === 0) {
                        // Luồng 6.1 – Không có phòng phù hợp
                        $errors[]  = "Không còn phòng phù hợp với yêu cầu.";
                        $hasSearch = true; // đã tìm nhưng rỗng
                    }

                // === 5. Nếu là ĐẶT PHÒNG (Step 10–14) → tạo giao dịch ===
                } elseif ($action === 'book') {

                    // 5.1 Kiểm tra lại danh sách phòng được chọn
                    $roomIds = array_map('intval', $roomsSelected);
                    $rooms   = $phongModel->getByIds($roomIds);

                    if (empty($rooms)) {
                        $errors[] = "Không tìm thấy phòng tương ứng.";
                    } else {
                        // Kiểm tra trạng thái phòng (Bước 9 + alt 9.1)
                        foreach ($rooms as $r) {
                            if ($r['TrangThai'] !== 'Trong') {
                                $errors[] = "Phòng {$r['SoPhong']} đã có khách khác giữ. Vui lòng chọn phòng khác.";
                            }
                        }
                    }

                    // 5.2 Nếu vẫn không lỗi → xử lý tạo giao dịch
                    if (empty($errors)) {
                        // Tính số đêm
                        $soDem = (strtotime($ngayDi) - strtotime($ngayDen)) / (60 * 60 * 24);
                        if ($soDem < 1) $soDem = 1;

                        // Tính tiền phòng
                        $tongTienPhong = 0;
                        foreach ($rooms as $r) {
                            $tongTienPhong += $r['Gia'] * $soDem;
                        }

                        // Tính tiền dịch vụ
                        $tongTienDichVu = 0;
                        $dichVuActive   = $dvModel->getActive();

                        if (!empty($servicesSelected)) {
                            foreach ($servicesSelected as $maDV => $sl) {
                                $maDV = (int)$maDV;
                                $sl   = (int)$sl;
                                if ($sl <= 0) continue;
                                if (!isset($dichVuActive[$maDV])) continue;

                                $gia = (float)$dichVuActive[$maDV]['GiaDichVu'];
                                $tongTienDichVu += $gia * $sl;
                            }
                        }

                        // 5.3 Tìm / tạo khách hàng
                        $khach = $khModel->findByIdentity($cccd, $sdt, $email);
                        if ($khach) {
                            $maKH = (int)$khach['MaKhachHang'];
                        } else {
                            $maKH = $khModel->create([
                                'TenKH'     => $tenKH,
                                'SDT'       => $sdt,
                                'Email'     => $email,
                                'CCCD'      => $cccd,
                                'DiaChi'    => $diachi,
                                'LoaiKhach' => 'Cá nhân'
                            ]);
                            if (!$maKH) {
                                $errors[] = "Không tạo được hồ sơ khách hàng.";
                            }
                        }

                        // 5.4 Tạo giao dịch + chi tiết nếu vẫn OK
                        if (empty($errors)) {
                            $user = Auth::user();
                            $maNV = $user['MaNhanVien'] ?? null;

                            $tongTien = $tongTienPhong + $tongTienDichVu;

                            $maGD = $gdModel->createBooking([
                                'MaKhachHang'         => $maKH,
                                'MaDoan'              => null,
                                'MaNhanVien'          => $maNV,
                                'MaKhuyenMai'         => null,
                                'LoaiGiaoDich'        => 'DatPhong',
                                'TongTien'            => $tongTien,
                                'TrangThai'           => 'Moi',
                                'PhuongThucThanhToan' => 'ChuaThanhToan',
                                'GhiChu'              => 'Đặt phòng trực tiếp tại quầy'
                            ]);

                            if (!$maGD) {
                                $errors[] = "Đặt phòng thất bại, vui lòng thử lại.";
                            } else {
                                $maGiaoDichVuaTao = $maGD;

                                // 5.5 Chi tiết giao dịch phòng + cập nhật trạng thái
                                foreach ($rooms as $maPhong => $r) {
                                    $donGia    = (float)$r['Gia'];
                                    $thanhTien = $donGia * $soDem;

                                    $ctgdModel->addRoomBooking([
                                        'MaGiaoDich'     => $maGD,
                                        'MaPhong'        => $maPhong,
                                        'SoNguoi'        => $soNguoi,
                                        'NgayNhanDuKien' => $ngayDen . " 14:00:00",
                                        'NgayTraDuKien'  => $ngayDi   . " 12:00:00",
                                        'DonGia'         => $donGia,
                                        'ThanhTien'      => $thanhTien,
                                        'TrangThai'      => 'Booked',
                                        'GhiChu'         => 'Đặt phòng trực tiếp'
                                    ]);

                                    $phongModel->updateTrangThai($maPhong, 'Booked');
                                }

                                // 5.6 Chi tiết dịch vụ
                                if (!empty($servicesSelected)) {
                                    $dichVuActive = $dvModel->getActive();
                                    foreach ($servicesSelected as $maDV => $sl) {
                                        $maDV = (int)$maDV;
                                        $sl   = (int)$sl;
                                        if ($sl <= 0) continue;
                                        if (!isset($dichVuActive[$maDV])) continue;

                                        $gia         = (float)$dichVuActive[$maDV]['GiaDichVu'];
                                        $thanhTienDV = $gia * $sl;

                                        $ctdvModel->addService([
                                            'MaGiaoDich' => $maGD,
                                            'MaPhong'    => null,
                                            'MaDichVu'   => $maDV,
                                            'SoLuong'    => $sl,
                                            'GiaBan'     => $gia,
                                            'ThanhTien'  => $thanhTienDV,
                                            'GhiChu'     => 'Đặt kèm khi đặt phòng'
                                        ]);
                                    }
                                }

                                $success = "Đặt phòng thành công! Mã giao dịch: {$maGD}";
                            }
                        }
                    }
                }
            }
        }

        // Render view
        $this->view("letan/datphong_tructiep", [
            'errors'       => $errors,
            'success'      => $success,
            'maGiaoDich'   => $maGiaoDichVuaTao,
            'dsPhong'      => $dsPhong,
            'dsDichVu'     => $dsDichVu,
            'ngayDen'      => $ngayDen,
            'ngayDi'       => $ngayDi,
            'hasSearch'    => $hasSearch
        ]);
    }

    // =================== USE CASE: ĐĂNG KÝ TÀI KHOẢN ĐOÀN ===================
    // URL: index.php?controller=letan&action=dangKyTaiKhoan
    public function dangKyTaiKhoan()
    {
        $this->requireRole(['LeTan']);

        $khModel   = new KhachHang();
        $tkModel   = new TaiKhoan();
        $doanModel = new Doan();

        $errors  = [];
        $success = null;
        $createdAccounts = []; // để hiển thị username + pass tạm

        // Biến dùng để fill lại form
        $form = [
            'leader_name'   => $_POST['leader_name']   ?? '',
            'leader_cccd'   => $_POST['leader_cccd']   ?? '',
            'leader_sdt'    => $_POST['leader_sdt']    ?? '',
            'leader_email'  => $_POST['leader_email']  ?? '',
            'leader_diachi' => $_POST['leader_diachi'] ?? '',
            'so_nguoi'      => $_POST['so_nguoi']      ?? '3',
            'tv_ho_ten'     => $_POST['tv_ho_ten']     ?? [],
            'tv_cccd'       => $_POST['tv_cccd']       ?? [],
            'tv_ngaysinh'   => $_POST['tv_ngaysinh']   ?? [],
            'tv_sdt'        => $_POST['tv_sdt']        ?? [],
            'tv_email'      => $_POST['tv_email']      ?? [],
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // NÚT HỦY ĐĂNG KÝ (luồng E1)
            if (isset($_POST['btn_cancel'])) {
                // chỉ redirect, không lưu gì cả
                header("Location: index.php?controller=letan&action=index");
                exit;
            }

            // ==== VALIDATE SERVER-SIDE (theo testcase) ====
            $leaderName  = trim($form['leader_name']);
            $leaderCCCD  = trim($form['leader_cccd']);
            $leaderSdt   = trim($form['leader_sdt']);
            $leaderEmail = trim($form['leader_email']);
            $soNguoi     = (int)$form['so_nguoi'];

            // 1. Họ tên trưởng đoàn
            if ($leaderName === '') {
                $errors[] = "Vui lòng nhập họ tên trưởng đoàn.";
            } elseif (mb_strlen($leaderName) < 2) {
                // TC-15
                $errors[] = "Tên trưởng đoàn không hợp lệ (quá ngắn).";
            }

            // 2. CMND/CCCD trưởng đoàn: bắt buộc 9–12 số
            if ($leaderCCCD === '') {
                $errors[] = "Vui lòng nhập CMND/CCCD trưởng đoàn.";
            } elseif (!preg_match('/^\d{9,12}$/', $leaderCCCD)) {
                $errors[] = "CMND/CCCD trưởng đoàn sai định dạng (chỉ 9–12 chữ số).";
            }

            // 3. Số điện thoại trưởng đoàn
            if ($leaderSdt === '') {
                $errors[] = "Vui lòng nhập số điện thoại trưởng đoàn.";
            } elseif (!preg_match('/^0\d{8,10}$/', $leaderSdt)) {
                $errors[] = "Số điện thoại trưởng đoàn sai định dạng.";
            }

            // 4. Email trưởng đoàn
            if ($leaderEmail === '') {
                $errors[] = "Vui lòng nhập email trưởng đoàn.";
            } elseif (!filter_var($leaderEmail, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email trưởng đoàn sai định dạng.";
            }

            // 5. Số lượng thành viên
            if ($soNguoi <= 0) {
                $errors[] = "Số lượng thành viên phải lớn hơn 0.";
            } elseif ($soNguoi > 100) { // TC-16: 200 sẽ bị báo lỗi
                $errors[] = "Số lượng thành viên vượt giới hạn, vui lòng nhập số hợp lý (< 100).";
            }

            // ==== Xử lý danh sách thành viên ====
            $members = [];
            $countFilled = 0;

            $tvNames   = $form['tv_ho_ten'];
            $tvCCCDs   = $form['tv_cccd'];
            $tvDob     = $form['tv_ngaysinh'];
            $tvSdts    = $form['tv_sdt'];
            $tvEmails  = $form['tv_email'];

            $rowCount = max(
                count($tvNames),
                count($tvCCCDs),
                count($tvDob),
                count($tvSdts),
                count($tvEmails)
            );

            for ($i = 0; $i < $rowCount; $i++) {
                $name   = trim($tvNames[$i]  ?? '');
                $cccd   = trim($tvCCCDs[$i]  ?? '');
                $dob    = trim($tvDob[$i]    ?? '');
                $sdt    = trim($tvSdts[$i]   ?? '');
                $email  = trim($tvEmails[$i] ?? '');

                // Nếu hoàn toàn trống thì bỏ qua
                if ($name === '' && $cccd === '' && $dob === '' && $sdt === '' && $email === '') {
                    continue;
                }

                // Theo luồng 3.1: được phép thiếu CCCD/SĐT, nhưng phải có HỌ TÊN
                if ($name === '') {
                    $errors[] = "Họ tên thành viên không được để trống (hàng " . ($i+1) . ").";
                }

                if ($cccd !== '' && !preg_match('/^\d{9,12}$/', $cccd)) {
                    $errors[] = "CMND/CCCD thành viên hàng ".($i+1)." sai định dạng (9–12 chữ số).";
                }

                if ($sdt !== '' && !preg_match('/^0\d{8,10}$/', $sdt)) {
                    $errors[] = "Số điện thoại thành viên hàng ".($i+1)." sai định dạng.";
                }

                if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Email thành viên hàng ".($i+1)." sai định dạng.";
                }

                $members[] = [
                    'ho_ten'   => $name,
                    'cccd'     => $cccd,
                    'ngay_sinh'=> $dob,
                    'sdt'      => $sdt,
                    'email'    => $email,
                ];

                if ($name !== '') {
                    $countFilled++;
                }
            }

            // TC-17: số thành viên nhập != số người
            if ($countFilled !== $soNguoi) {
                $errors[] = "Số lượng thành viên không khớp với số người đã khai báo (đã nhập $countFilled / $soNguoi).";
            }

            // ==== Kiểm tra trùng CMND/Email giữa trưởng đoàn & thành viên (TC-18) ====
            $cmndList  = [];
            $emailList = [];

            // Trưởng đoàn
            $cmndList[]  = $leaderCCCD;
            $emailList[] = $leaderEmail;

            foreach ($members as $idx => $m) {
                if ($m['cccd'] !== '') {
                    $cmndList[] = $m['cccd'];
                }
                if ($m['email'] !== '') {
                    $emailList[] = $m['email'];
                }
            }

            // Trùng CMND trong danh sách nhập
            $dupCMND = array_filter(array_count_values($cmndList), function($c){ return $c > 1; });
            if (!empty($dupCMND)) {
                $errors[] = "Phát hiện CMND/CCCD trùng lặp trong danh sách (trưởng đoàn / thành viên).";
            }

            // Trùng email giữa các thành viên (TC-18)
            $dupEmail = array_filter(array_count_values($emailList), function($c){ return $c > 1; });
            if (!empty($dupEmail)) {
                $errors[] = "Email bị trùng lặp giữa trưởng đoàn và/hoặc các thành viên.";
            }

            // Nếu có lỗi → không lưu DB
            if (empty($errors)) {
                $db = Database::getConnection();
                $db->begin_transaction();

                try {
                    // ========== 1. Xử lý TRƯỞNG ĐOÀN ==========
                    $leaderKH = $khModel->findByCCCD($leaderCCCD);

                    if ($leaderKH) {
                        // đã có KH → cập nhật lại SĐT, email, địa chỉ (luồng 7a)
                        $khModel->updateContact($leaderKH['MaKhachHang'], [
                            'SDT'    => $leaderSdt,
                            'Email'  => $leaderEmail,
                            'DiaChi' => $form['leader_diachi'],
                        ]);
                        $maTruongDoan = (int)$leaderKH['MaKhachHang'];
                    } else {
                        // chưa có → tạo mới
                        $maTruongDoan = $khModel->create([
                            'TenKH'     => $leaderName,
                            'SDT'       => $leaderSdt,
                            'Email'     => $leaderEmail,
                            'CCCD'      => $leaderCCCD,
                            'DiaChi'    => $form['leader_diachi'],
                            'LoaiKhach' => 'Trưởng đoàn',
                        ]);
                    }

                    if (!$maTruongDoan) {
                        throw new Exception("Không thể lưu thông tin trưởng đoàn.");
                    }

                    // ========== 2. Tạo DOÀN ==========
                    $tenDoan = "Đoàn " . $leaderName;
                    $maDoan  = $doanModel->create([
                        'TenDoan'      => $tenDoan,
                        'MaTruongDoan' => $maTruongDoan,
                        'SoNguoi'      => $soNguoi,
                        'GhiChu'       => '',
                    ]);
                    if (!$maDoan) {
                        throw new Exception("Không thể tạo thông tin đoàn.");
                    }

                    $prefix = 'D' . str_pad($maDoan, 3, '0', STR_PAD_LEFT);

                    // ========== 3. Tạo/ liên kết TÀI KHOẢN cho TRƯỞNG ĐOÀN ==========
                    $tkLeader = $tkModel->getByKhachHangId($maTruongDoan);
                    if ($tkLeader) {
                        // đã có tài khoản
                        $createdAccounts[] = [
                            'hoTen'    => $leaderName,
                            'cmnd'     => $leaderCCCD,
                            'username' => $tkLeader['Username'],
                            'password' => '(Tài khoản đã tồn tại)',
                            'vaiTro'   => 'Trưởng đoàn (đã có TK)',
                        ];
                    } else {
                        $username = $prefix . '_Leader';
                        $plainPwd = $tkModel->generateRandomPassword();
                        $tkModel->createForCustomer([
                            'MaKhachHang' => $maTruongDoan,
                            'Username'    => $username,
                            'Password'    => $plainPwd,
                            'MaVaiTro'    => 7, // KhachHang
                        ]);

                        $createdAccounts[] = [
                            'hoTen'    => $leaderName,
                            'cmnd'     => $leaderCCCD,
                            'username' => $username,
                            'password' => $plainPwd,
                            'vaiTro'   => 'Trưởng đoàn',
                        ];
                    }

                    // ========== 4. Xử lý từng thành viên ==========
                    $indexMember = 1;
                    foreach ($members as $m) {
                        $name   = $m['ho_ten'];
                        if ($name === '') continue; // hàng hoàn toàn trống

                        $cccd   = $m['cccd'];
                        $sdt    = $m['sdt'];
                        $email  = $m['email'];

                        $existKH = null;
                        if ($cccd !== '') {
                            $existKH = $khModel->findByCCCD($cccd);
                        }

                        if ($existKH) {
                            $maKH = (int)$existKH['MaKhachHang'];
                            // cập nhật lại contact nếu có thay đổi
                            $khModel->updateContact($maKH, [
                                'SDT'   => $sdt ?: $existKH['SDT'],
                                'Email' => $email ?: $existKH['Email'],
                            ]);
                        } else {
                            // tạo mới
                            $maKH = $khModel->create([
                                'TenKH'     => $name,
                                'SDT'       => $sdt,
                                'Email'     => $email,
                                'CCCD'      => $cccd,
                                'DiaChi'    => '',
                                'LoaiKhach' => ($cccd === '' ? 'Thành viên (thiếu thông tin)' : 'Thành viên'),
                            ]);
                        }

                        if (!$maKH) {
                            throw new Exception("Không thể lưu thông tin thành viên: $name");
                        }

                        // (Tùy thiết kế, có thể tạo bảng doan_khach (MaDoan - MaKhachHang))

                        // Tạo/ liên kết tài khoản
                        $tk = $tkModel->getByKhachHangId($maKH);
                        if ($tk) {
                            $createdAccounts[] = [
                                'hoTen'    => $name,
                                'cmnd'     => $cccd,
                                'username' => $tk['Username'],
                                'password' => '(Đã có TK)',
                                'vaiTro'   => "Thành viên $indexMember (đã có TK)",
                            ];
                        } else {
                            $username = $prefix . '_M' . $indexMember;
                            $plainPwd = $tkModel->generateRandomPassword();
                            $tkModel->createForCustomer([
                                'MaKhachHang' => $maKH,
                                'Username'    => $username,
                                'Password'    => $plainPwd,
                                'MaVaiTro'    => 7,
                            ]);

                            $createdAccounts[] = [
                                'hoTen'    => $name,
                                'cmnd'     => $cccd,
                                'username' => $username,
                                'password' => $plainPwd,
                                'vaiTro'   => "Thành viên $indexMember",
                            ];
                        }

                        $indexMember++;
                    }

                    $db->commit();
                    $success = "Đăng ký tài khoản đoàn thành công. Mã đoàn: {$maDoan} ({$prefix}).";

                } catch (\Throwable $ex) {
                    $db->rollback();
                    $errors[] = "Đăng ký thất bại, vui lòng thử lại sau (lỗi hệ thống).";
                }
            }
        }

        $this->view("letan/dangky_taikhoan_doan", [
            'errors'          => $errors,
            'success'         => $success,
            'createdAccounts' => $createdAccounts,
            'form'            => $form,
        ]);
    }
public function huyDatPhong()
{
    $this->requireRole(['LeTan']);

    $gdModel    = new GiaoDich();
    $ctgdModel  = new ChiTietGiaoDich();
    $phongModel = new Phong();

    $errors        = [];
    $success       = null;
    $giaoDich      = null;
    $chiTietPhong  = [];
    $searchKeyword = '';
    $allowCancel   = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $action = $_POST['btn_action'] ?? '';

        // Quay lại màn hình lễ tân
        if ($action === 'back') {
            header("Location: index.php?controller=letan&action=index");
            exit;
        }

        // ========== 1. TÌM KIẾM GIAO DỊCH ==========
        if ($action === 'search') {

            $searchKeyword = trim($_POST['search_keyword'] ?? '');

            if ($searchKeyword === '') {
                $errors[] = "Vui lòng nhập thông tin tìm kiếm.";
            } else {
                // Nếu có ký tự đặc biệt & KHÔNG phải CMND 9–12 số → lỗi (TC-14)
                if (preg_match('/[^a-zA-Z0-9]/', $searchKeyword) && !preg_match('/^\d{9,12}$/', $searchKeyword)) {
                    $errors[] = "Dữ liệu nhập không hợp lệ.";
                } else {
                    // Tìm theo MaGiaoDich hoặc CCCD trưởng đoàn
                    if (ctype_digit($searchKeyword)) {
                        // số → ưu tiên tìm theo mã GD, đồng thời dùng luôn làm CCCD
                        $giaoDich = $gdModel->findByMaOrCCCD($searchKeyword, $searchKeyword);
                    } else {
                        // chuỗi không hoàn toàn là số → chỉ tìm theo CCCD
                        $giaoDich = $gdModel->findByMaOrCCCD(null, $searchKeyword);
                    }

                    if (!$giaoDich) {
                        // TC-3 – Không tìm thấy
                        $errors[] = "Không tìm thấy giao dịch phù hợp.";
                    } else {
                        $maGD         = (int)$giaoDich['MaGiaoDich'];
                        $chiTietPhong = $ctgdModel->getPhongByGiaoDich($maGD);

                        // Cho phép hủy nếu giao dịch chưa bị hủy / chưa stayed
                        if (!empty($chiTietPhong)
                            && $giaoDich['TrangThai'] !== GiaoDich::STATUS_DA_HUY
                            && $giaoDich['TrangThai'] !== GiaoDich::STATUS_STAYED
                        ) {
                            $allowCancel = true;
                        }
                    }
                }
            }
        }

        // ========== 2. XÁC NHẬN HỦY ĐẶT PHÒNG ==========
        if ($action === 'cancel') {

            $maGiaoDich   = (int)($_POST['ma_giao_dich'] ?? 0);
            $scope        = $_POST['cancel_scope'] ?? 'all';  // all / partial
            $lyDo         = trim($_POST['ly_do_huy'] ?? '');
            $selectedPhong = [];

            if ($maGiaoDich <= 0) {
                $errors[] = "Thiếu thông tin mã giao dịch.";
            }
            if ($lyDo === '') {
                $errors[] = "Vui lòng nhập lý do hủy.";
            }

            // Lấy chi tiết để biết danh sách phòng
            $allCt = $ctgdModel->getPhongByGiaoDich($maGiaoDich);
            if (empty($allCt)) {
                $errors[] = "Không tìm thấy chi tiết giao dịch để hủy.";
            }

            // Xác định danh sách phòng cần hủy
            if ($scope === 'all') {
                // Hủy toàn bộ giao dịch → lấy tất cả MaPhong
                $selectedPhong = array_column($allCt, 'MaPhong');
            } else {
                // Hủy một phần → lấy các phòng được chọn từ form
                $selectedPhong = array_map('intval', $_POST['phong_cancel'] ?? []);
                if (empty($selectedPhong)) {
                    $errors[] = "Vui lòng chọn ít nhất một phòng để hủy hoặc chọn hủy toàn bộ.";
                }
            }

            if (empty($errors)) {
                // Lấy lại giao dịch để kiểm tra điều kiện hủy
                $giaoDich = $gdModel->getById($maGiaoDich);
                if (!$giaoDich) {
                    $errors[] = "Không tìm thấy giao dịch để hủy.";
                } else {

                    // VD: nếu TrangThai = Stayed → không cho hủy (TC-4,6)
                    if ($giaoDich['TrangThai'] === GiaoDich::STATUS_STAYED) {
                        $errors[] = "Không thể hủy giao dịch (đã check-in).";
                    }

                    if (empty($errors)) {
                        $db = Database::getConnection();
                        $db->begin_transaction();

                        try {
                            // 1. Cập nhật chi tiết giao dịch → DaHuy (STATUS_CANCELLED)
                            // KHÔNG truyền lý do vào, vì tham số 3 là trạng thái
                            $ok = $ctgdModel->cancelByPhong($maGiaoDich, $selectedPhong);
                            if (!$ok) {
                                throw new Exception("Không thể cập nhật chi tiết giao dịch.");
                            }

                            // 2. Cập nhật trạng thái phòng → Trong
                            foreach ($selectedPhong as $maPhong) {
                                $phongModel->updateTrangThai((int)$maPhong, 'Trong');
                            }

                            // TODO: nếu muốn lưu lý do hủy, em có thể:
                            // - append vào cột GhiChu trong bảng giaodich
                            // - hoặc tạo bảng log riêng

                            // 3. Kiểm tra còn phòng nào chưa hủy không
                            $chiTietConLai    = $ctgdModel->getPhongByGiaoDich($maGiaoDich);
                            $conPhongHoatDong = false;
                            foreach ($chiTietConLai as $ct) {
                                if ($ct['TrangThai'] !== ChiTietGiaoDich::STATUS_CANCELLED) {
                                    $conPhongHoatDong = true;
                                    break;
                                }
                            }

                            // Nếu không còn phòng nào mở → set giao dịch = DaHuy
                            if (!$conPhongHoatDong) {
                                $gdModel->cancel($maGiaoDich);
                            }

                            $db->commit();
                            $success       = "Hủy đặt phòng thành công.";
                            $giaoDich      = $gdModel->getById($maGiaoDich);
                            $chiTietPhong  = $ctgdModel->getPhongByGiaoDich($maGiaoDich);
                            $searchKeyword = (string)$maGiaoDich;
                            $allowCancel   = true;

                        } catch (\Throwable $ex) {
                            $db->rollback();
                            $errors[] = "Không thể lưu thay đổi, vui lòng thử lại. (Chi tiết: " . $ex->getMessage() . ")";
                        }
                    }
                }
            }
        }
    }

    // Render view
    $this->view("letan/huy_dat_phong", [
        'errors'        => $errors,
        'success'       => $success,
        'giaoDich'      => $giaoDich,
        'chiTiet'       => $chiTietPhong,   // view dùng $chiTiet
        'searchKeyword' => $searchKeyword,
        'allowCancel'   => $allowCancel,
    ]);
}
public function suaThongTinDatPhong()
{
    $this->requireRole(['LeTan']);

    $gdModel    = new GiaoDich();
    $ctgdModel  = new ChiTietGiaoDich();
    $phongModel = new Phong();

    $errors        = [];
    $success       = null;
    $giaoDich      = null;
    $chiTietPhong  = [];
    $searchKeyword = '';

    // Form chính (sau khi load xong)
    $form = [
        'ten_kh'   => '',
        'cccd'     => '',
        'sdt'      => '',
        'email'    => '',
        'ngay_den' => '',
        'ngay_di'  => '',
        'so_nguoi' => 1,
        'ma_phong' => ''
    ];

    // danh sách phòng trống để đổi (cộng thêm phòng hiện tại)
    $dsPhong = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $action        = $_POST['btn_action'] ?? '';
        $searchKeyword = trim($_POST['search_keyword'] ?? '');

        // Hủy thao tác → quay lại lễ tân
        if ($action === 'cancel') {
            header("Location: index.php?controller=letan&action=index");
            exit;
        }

        // ========== 1. TÌM KIẾM GIAO DỊCH (THEO MÃ/CCCD) ==========
        if ($action === 'search') {

            if ($searchKeyword === '') {
                $errors[] = "Vui lòng nhập mã giao dịch hoặc CMND/CCCD.";
            } else {
                // Nếu có ký tự đặc biệt & không phải 9–12 số → lỗi
                if (preg_match('/[^a-zA-Z0-9]/', $searchKeyword) && !preg_match('/^\d{9,12}$/', $searchKeyword)) {
                    $errors[] = "Dữ liệu nhập không hợp lệ.";
                } else {
                    try {
                        // tìm theo MaGiaoDich hoặc CCCD
                        if (ctype_digit($searchKeyword)) {
                            $giaoDich = $gdModel->findByMaOrCCCD($searchKeyword, $searchKeyword);
                        } else {
                            $giaoDich = $gdModel->findByMaOrCCCD(null, $searchKeyword);
                        }
                    } catch (\Throwable $ex) {
                        $errors[] = "Không thể truy vấn dữ liệu.";
                    }

                    if (!$giaoDich) {
                        $errors[] = "Không tìm thấy giao dịch.";
                    } else {
                        // CHỈ CHO SỬA KHI ĐANG BOOKED (so sánh không phân biệt hoa thường, có trim)
                        $trangThai = trim($giaoDich['TrangThai'] ?? '');
                        if (strcasecmp($trangThai, 'Booked') !== 0) {
                            $errors[] = "Chỉ có thể sửa giao dịch đang ở trạng thái 'Booked'.";
                        } else {
                            $maGD         = (int)$giaoDich['MaGiaoDich'];
                            $chiTietPhong = $ctgdModel->getPhongByGiaoDich($maGD);

                            if (empty($chiTietPhong)) {
                                $errors[] = "Giao dịch không có chi tiết phòng.";
                            } else {
                                // lấy dòng đầu tiên làm mặc định
                                $ct0 = $chiTietPhong[0];

                                // Lấy thông tin khách hàng
                                $db    = Database::getConnection();
                                $sqlKH = "SELECT * FROM khachhang WHERE MaKhachHang = ?";
                                $stmtKH = $db->prepare($sqlKH);
                                $stmtKH->bind_param("i", $giaoDich['MaKhachHang']);
                                $stmtKH->execute();
                                $rsKH  = $stmtKH->get_result();
                                $khach = $rsKH->fetch_assoc() ?: null;

                                $form['ten_kh']   = $khach['TenKH']  ?? ($giaoDich['TenKH'] ?? '');
                                $form['cccd']     = $khach['CCCD']   ?? ($giaoDich['CCCD'] ?? '');
                                $form['sdt']      = $khach['SDT']    ?? '';
                                $form['email']    = $khach['Email']  ?? '';
                                $form['ngay_den'] = substr($ct0['NgayNhanDuKien'], 0, 10);
                                $form['ngay_di']  = substr($ct0['NgayTraDuKien'], 0, 10);
                                $form['so_nguoi'] = isset($ct0['SoNguoi']) ? (int)$ct0['SoNguoi'] : 1;
                                $form['ma_phong'] = (int)$ct0['MaPhong'];

                                // Lấy danh sách phòng đang Trống để đổi
                                $rsPhong = $phongModel->searchAvailable();
                                if ($rsPhong instanceof \mysqli_result) {
                                    while ($row = $rsPhong->fetch_assoc()) {
                                        $dsPhong[] = $row;
                                    }
                                }
                                // Luôn thêm phòng hiện tại vào danh sách nếu chưa có
                                $foundCurrent = false;
                                foreach ($dsPhong as $p) {
                                    if ((int)$p['MaPhong'] === (int)$ct0['MaPhong']) {
                                        $foundCurrent = true;
                                        break;
                                    }
                                }
                                if (!$foundCurrent) {
                                    $dsPhong[] = [
                                        'MaPhong'       => $ct0['MaPhong'],
                                        'SoPhong'       => $ct0['SoPhong'],
                                        'LoaiPhong'     => $ct0['LoaiPhong'],
                                        'SoKhachToiDa'  => $ct0['SoKhachToiDa'],
                                        'TrangThai'     => 'Booked',
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        // ========== 1b. CHỌN PHÒNG CẦN SỬA (Cách 1) ==========
        if ($action === 'pick_room') {

            $maGiaoDich = (int)($_POST['ma_giao_dich'] ?? 0);
            $maPhongCu  = (int)($_POST['ma_phong_cu'] ?? 0);

            if ($maGiaoDich <= 0 || $maPhongCu <= 0) {
                $errors[] = "Thiếu thông tin giao dịch/phòng.";
            } else {
                try {
                    $giaoDich = $gdModel->getById($maGiaoDich);
                } catch (\Throwable $ex) {
                    $giaoDich = null;
                    $errors[] = "Không thể truy vấn dữ liệu.";
                }

                if (!$giaoDich) {
                    $errors[] = "Không tìm thấy giao dịch.";
                } else {
                    $trangThai = trim($giaoDich['TrangThai'] ?? '');
                    if (strcasecmp($trangThai, 'Booked') !== 0) {
                        $errors[] = "Chỉ có thể sửa giao dịch đang ở trạng thái 'Booked'.";
                    } else {
                        $chiTietPhong = $ctgdModel->getPhongByGiaoDich($maGiaoDich);
                        if (empty($chiTietPhong)) {
                            $errors[] = "Giao dịch không có chi tiết phòng.";
                        } else {
                            // TÌM DÒNG CHI TIẾT CÓ MaPhong = $maPhongCu
                            $ct0 = $chiTietPhong[0];
                            foreach ($chiTietPhong as $ct) {
                                if ((int)$ct['MaPhong'] === $maPhongCu) {
                                    $ct0 = $ct;
                                    break;
                                }
                            }

                            // Lấy thông tin khách hàng
                            $db    = Database::getConnection();
                            $sqlKH = "SELECT * FROM khachhang WHERE MaKhachHang = ?";
                            $stmtKH = $db->prepare($sqlKH);
                            $stmtKH->bind_param("i", $giaoDich['MaKhachHang']);
                            $stmtKH->execute();
                            $rsKH  = $stmtKH->get_result();
                            $khach = $rsKH->fetch_assoc() ?: null;

                            $form['ten_kh']   = $khach['TenKH']  ?? ($giaoDich['TenKH'] ?? '');
                            $form['cccd']     = $khach['CCCD']   ?? ($giaoDich['CCCD'] ?? '');
                            $form['sdt']      = $khach['SDT']    ?? '';
                            $form['email']    = $khach['Email']  ?? '';
                            $form['ngay_den'] = substr($ct0['NgayNhanDuKien'], 0, 10);
                            $form['ngay_di']  = substr($ct0['NgayTraDuKien'], 0, 10);
                            $form['so_nguoi'] = isset($ct0['SoNguoi']) ? (int)$ct0['SoNguoi'] : 1;
                            $form['ma_phong'] = (int)$ct0['MaPhong'];

                            // Lấy danh sách phòng đang Trống
                            $rsPhong = $phongModel->searchAvailable();
                            if ($rsPhong instanceof \mysqli_result) {
                                while ($row = $rsPhong->fetch_assoc()) {
                                    $dsPhong[] = $row;
                                }
                            }
                            // Luôn thêm phòng hiện tại vào danh sách
                            $foundCurrent = false;
                            foreach ($dsPhong as $p) {
                                if ((int)$p['MaPhong'] === (int)$ct0['MaPhong']) {
                                    $foundCurrent = true;
                                    break;
                                }
                            }
                            if (!$foundCurrent) {
                                $dsPhong[] = [
                                    'MaPhong'       => $ct0['MaPhong'],
                                    'SoPhong'       => $ct0['SoPhong'],
                                    'LoaiPhong'     => $ct0['LoaiPhong'],
                                    'SoKhachToiDa'  => $ct0['SoKhachToiDa'],
                                    'TrangThai'     => 'Booked',
                                ];
                            }

                            // Cập nhật lại searchKeyword cho đẹp
                            if ($searchKeyword === '') {
                                $searchKeyword = (string)$maGiaoDich;
                            }
                        }
                    }
                }
            }
        }

        // ========== 2. LƯU THAY ĐỔI ==========
        if ($action === 'save') {

            $maGiaoDich = (int)($_POST['ma_giao_dich'] ?? 0);
            $maPhongCu  = (int)($_POST['ma_phong_cu'] ?? 0);

            // Lấy lại form nhập
            $form['ten_kh']   = trim($_POST['ten_kh']   ?? '');
            $form['cccd']     = trim($_POST['cccd']     ?? '');
            $form['sdt']      = trim($_POST['sdt']      ?? '');
            $form['email']    = trim($_POST['email']    ?? '');
            $form['ngay_den'] = trim($_POST['ngay_den'] ?? '');
            $form['ngay_di']  = trim($_POST['ngay_di']  ?? '');
            $form['so_nguoi'] = (int)($_POST['so_nguoi'] ?? 1);
            $form['ma_phong'] = (int)($_POST['ma_phong'] ?? 0);

            // === validate thông tin khách hàng ===
            if ($form['ten_kh'] === '') {
                $errors[] = "Vui lòng nhập họ tên khách hàng.";
            }
            if ($form['cccd'] === '' || !preg_match('/^\d{9,12}$/', $form['cccd'])) {
                $errors[] = "CMND/CCCD không hợp lệ (9–12 chữ số).";
            }
            if ($form['sdt'] === '' || !preg_match('/^0\d{8,10}$/', $form['sdt'])) {
                $errors[] = "Số điện thoại không hợp lệ.";
            }
            if ($form['email'] === '' || !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email không hợp lệ.";
            }

            // === validate ngày ===
            if ($form['ngay_den'] === '' || $form['ngay_di'] === '') {
                $errors[] = "Vui lòng nhập ngày đến và ngày đi.";
            } else {
                $d1 = strtotime($form['ngay_den']);
                $d2 = strtotime($form['ngay_di']);
                if (!$d1 || !$d2) {
                    $errors[] = "Định dạng ngày không hợp lệ.";
                } elseif ($d2 <= $d1) {
                    $errors[] = "Ngày đi phải lớn hơn ngày đến.";
                }
            }

            if ($form['so_nguoi'] <= 0) {
                $errors[] = "Số người phải lớn hơn 0.";
            }

            if ($maGiaoDich <= 0 || $maPhongCu <= 0 || $form['ma_phong'] <= 0) {
                $errors[] = "Thiếu thông tin giao dịch/phòng.";
            }

            // kiểm tra phòng mới có tồn tại và sức chứa
            $roomNewArr = $phongModel->getByIds([$form['ma_phong']]);
            $roomNew    = $roomNewArr[$form['ma_phong']] ?? null;

            if (!$roomNew) {
                $errors[] = "Không tìm thấy phòng được chọn.";
            } else {
                if ($form['so_nguoi'] > (int)$roomNew['SoKhachToiDa']) {
                    $errors[] = "Số người vượt quá sức chứa phòng.";
                }
                // nếu đổi sang phòng khác, phòng mới phải đang Trống
                if ($form['ma_phong'] !== $maPhongCu && $roomNew['TrangThai'] !== 'Trong') {
                    $errors[] = "Phòng không khả dụng.";
                }
            }

            // TODO: có thể thêm check trùng lịch giao dịch khác ở đây

            if (empty($errors)) {
                $db = Database::getConnection();
                $db->begin_transaction();

                try {
                    // 1. Lấy lại giao dịch để biết MaKhachHang & kiểm tra trạng thái
                    $giaoDich = $gdModel->getById($maGiaoDich);
                    if (!$giaoDich) {
                        throw new Exception("Không tìm thấy giao dịch để cập nhật.");
                    }
                    $trangThai = trim($giaoDich['TrangThai'] ?? '');
                    if (strcasecmp($trangThai, 'Booked') !== 0) {
                        throw new Exception("Chỉ được sửa giao dịch đang Booked.");
                    }

                    $maKH = (int)$giaoDich['MaKhachHang'];

                    // 2. Cập nhật thông tin khách hàng
                    $sqlKH = "
                        UPDATE khachhang
                        SET TenKH = ?, CCCD = ?, SDT = ?, Email = ?
                        WHERE MaKhachHang = ?
                    ";
                    $stmtKH = $db->prepare($sqlKH);
                    $stmtKH->bind_param(
                        "ssssi",
                        $form['ten_kh'],
                        $form['cccd'],
                        $form['sdt'],
                        $form['email'],
                        $maKH
                    );
                    if (!$stmtKH->execute()) {
                        throw new Exception("Không thể cập nhật thông tin khách hàng.");
                    }

                    // 3. Cập nhật chi tiết đặt phòng (CHỈ 1 PHÒNG: maPhongCu)
                    $dataUpdate = [
                        'MaPhong'        => $form['ma_phong'],
                        'SoNguoi'        => $form['so_nguoi'],
                        'NgayNhanDuKien' => $form['ngay_den'] . " 14:00:00",
                        'NgayTraDuKien'  => $form['ngay_di']  . " 12:00:00",
                    ];

                    if (!$ctgdModel->updateBooking($maGiaoDich, $maPhongCu, $dataUpdate)) {
                        throw new Exception("Không thể cập nhật chi tiết đặt phòng.");
                    }

                    // 4. Nếu đổi phòng → cập nhật trạng thái phòng cũ/mới
                    if ($form['ma_phong'] !== $maPhongCu) {
                        $phongModel->updateTrangThai($maPhongCu, 'Trong');   // phòng cũ
                        $phongModel->updateTrangThai($form['ma_phong'], 'Booked'); // phòng mới
                    }

                    $db->commit();
                    $success = "Cập nhật thành công.";

                    // load lại dữ liệu để hiển thị
                    $giaoDich     = $gdModel->getById($maGiaoDich);
                    $chiTietPhong = $ctgdModel->getPhongByGiaoDich($maGiaoDich);

                    if (!empty($chiTietPhong)) {
                        // tìm lại dòng ứng với phòng mới
                        $ct0 = $chiTietPhong[0];
                        foreach ($chiTietPhong as $ct) {
                            if ((int)$ct['MaPhong'] === (int)$form['ma_phong']) {
                                $ct0 = $ct;
                                break;
                            }
                        }

                        $form['ngay_den'] = substr($ct0['NgayNhanDuKien'], 0, 10);
                        $form['ngay_di']  = substr($ct0['NgayTraDuKien'], 0, 10);
                        $form['so_nguoi'] = isset($ct0['SoNguoi']) ? (int)$ct0['SoNguoi'] : $form['so_nguoi'];
                        $form['ma_phong'] = (int)$ct0['MaPhong'];
                    }

                    $searchKeyword = (string)$maGiaoDich;

                    // làm lại danh sách phòng trống
                    $rsPhong = $phongModel->searchAvailable();
                    if ($rsPhong instanceof \mysqli_result) {
                        while ($row = $rsPhong->fetch_assoc()) {
                            $dsPhong[] = $row;
                        }
                    }
                } catch (\Throwable $ex) {
                    $db->rollback();
                    $errors[] = "Cập nhật thất bại, vui lòng thử lại. (" . $ex->getMessage() . ")";
                }
            }
        }
    }

    // Render view
    $this->view("letan/sua_dat_phong", [
        'errors'        => $errors,
        'success'       => $success,
        'giaoDich'      => $giaoDich,
        'chiTiet'       => $chiTietPhong,
        'searchKeyword' => $searchKeyword,
        'form'          => $form,
        'dsPhong'       => $dsPhong,
    ]);
}
public function checkIn()
{
    $this->requireRole(['LeTan']);

    $gdModel    = new GiaoDich();
    $ctgdModel  = new ChiTietGiaoDich();
    $phongModel = new Phong();

    $errors   = [];
    $success  = null;

    $giaoDich = null;
    $chiTiet  = [];

    // Dùng cho form tìm kiếm
    $searchMaGD  = $_POST['search_ma_gd']  ?? '';
    $searchCMND  = $_POST['search_cmnd']   ?? '';

    // Thông tin giao dịch đoàn
    $tenTruongDoan = '';
    $cmndTruongDoan = '';
    $soThanhVien   = 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $action = $_POST['btn_action'] ?? '';

        // Quay lại màn hình lễ tân
        if ($action === 'back') {
            header("Location: index.php?controller=letan&action=index");
            exit;
        }

        // ================== 1. TÌM KIẾM GIAO DỊCH ==================
        if ($action === 'search') {

            $searchMaGD = trim($searchMaGD);
            $searchCMND = trim($searchCMND);

            if ($searchMaGD === '' && $searchCMND === '') {
                // TC-9
                $errors[] = "Vui lòng nhập mã giao dịch hoặc CMND để tra cứu.";
            } else {

                // Validate mã giao dịch nếu có (TC-7)
                if ($searchMaGD !== '') {
                    if (preg_match('/[^a-zA-Z0-9]/', $searchMaGD)) {
                        $errors[] = "Dữ liệu mã giao dịch không hợp lệ.";
                    }
                }

                // Validate CMND nếu có (TC-8)
                if ($searchCMND !== '') {
                    if (!preg_match('/^\d{9,12}$/', $searchCMND)) {
                        $errors[] = "CMND/CCCD không hợp lệ.";
                    }
                }

                if (empty($errors)) {
                    try {
                        if ($searchMaGD !== '') {
                            // khách nhớ mã giao dịch
                            $giaoDich = $gdModel->findByMaOrCCCD($searchMaGD, null);
                        } else {
                            // khách quên mã, tìm theo CMND
                            $giaoDich = $gdModel->findByMaOrCCCD(null, $searchCMND);
                        }
                    } catch (\Throwable $ex) {
                        $giaoDich = null;
                        $errors[] = "Không thể truy vấn dữ liệu.";
                    }

                    if (!$giaoDich) {
                        // TC-5,6
                        $errors[] = "Không tồn tại giao dịch phù hợp.";
                    } else {
                        $maGD   = (int)$giaoDich['MaGiaoDich'];
                        $chiTiet = $ctgdModel->getPhongByGiaoDich($maGD);

                        if (empty($chiTiet)) {
                            $errors[] = "Giao dịch không có chi tiết phòng.";
                        } else {
                            // Đếm trạng thái để xử lý các case 12, 13, 14
                            $countBooked    = 0;
                            $countCancelled = 0;
                            $countStayed    = 0;

                            foreach ($chiTiet as $ct) {
                                $st = $ct['TrangThai'];
                                if ($st === 'Booked') {
                                    $countBooked++;
                                } elseif (in_array($st, ['DaHuy','Cancelled'])) {
                                    $countCancelled++;
                                } elseif (in_array($st, ['Stayed','DangO'])) {
                                    $countStayed++;
                                }
                            }

                            // TC-14 – tất cả phòng Cancelled
                            if ($countCancelled > 0 && $countBooked === 0 && $countStayed === 0) {
                                $errors[] = "Không thể check-in, giao dịch đã bị hủy (tất cả phòng đã Cancelled).";
                            }

                            // TC-12 – giao dịch đã Stayed trước đó (tất cả phòng đã Stayed)
                            if ($countStayed > 0 && $countBooked === 0) {
                                $errors[] = "Giao dịch này đã được check-in trước đó.";
                            }

                            // Lấy thông tin trưởng đoàn
                            if (empty($errors)) {
                                $db    = Database::getConnection();
                                $sqlKH = "SELECT * FROM khachhang WHERE MaKhachHang = ?";
                                $stmt  = $db->prepare($sqlKH);
                                $stmt->bind_param("i", $giaoDich['MaKhachHang']);
                                $stmt->execute();
                                $rsKH  = $stmt->get_result();
                                $khach = $rsKH->fetch_assoc() ?: null;

                                $tenTruongDoan  = $khach['TenKH'] ?? '';
                                $cmndTruongDoan = $khach['CCCD']  ?? '';
                                $soThanhVien    = $giaoDich['SoNguoi'] ?? count($chiTiet);
                            }
                        }
                    }
                }
            }
        }

        // ================== 2. XÁC NHẬN CHECK-IN ==================
        if ($action === 'confirm') {

            $maGiaoDich = (int)($_POST['ma_giao_dich'] ?? 0);
            $scope      = $_POST['check_scope'] ?? 'all';   // all / partial
            $xacNhanGT  = isset($_POST['xac_nhan_giay_to']); // checkbox
            $searchMaGD = trim($_POST['search_ma_gd'] ?? '');
            $searchCMND = trim($_POST['search_cmnd']  ?? '');

            if ($maGiaoDich <= 0) {
                $errors[] = "Thiếu thông tin mã giao dịch.";
            }

            // TC-10: thiếu giấy tờ hợp lệ
            if (!$xacNhanGT) {
                $errors[] = "Không có giấy tờ tùy thân hợp lệ, từ chối check-in.";
            }

            // Lấy chi tiết giao dịch
            if (empty($errors)) {
                try {
                    $giaoDich = $gdModel->getById($maGiaoDich);
                    $chiTiet  = $ctgdModel->getPhongByGiaoDich($maGiaoDich);
                } catch (\Throwable $ex) {
                    $giaoDich = null;
                    $chiTiet  = [];
                    $errors[] = "Không thể truy vấn dữ liệu.";
                }
            }

            if (empty($errors)) {

                if (empty($chiTiet)) {
                    $errors[] = "Không tìm thấy chi tiết giao dịch để check-in.";
                } else {

                    // đếm trạng thái toàn giao dịch
                    $countBooked    = 0;
                    $countCancelled = 0;
                    $countStayed    = 0;

                    foreach ($chiTiet as $ct) {
                        $st = $ct['TrangThai'];
                        if ($st === 'Booked') {
                            $countBooked++;
                        } elseif (in_array($st, ['DaHuy','Cancelled'])) {
                            $countCancelled++;
                        } elseif (in_array($st, ['Stayed','DangO'])) {
                            $countStayed++;
                        }
                    }

                    if ($countCancelled > 0 && $countBooked === 0 && $countStayed === 0) {
                        // TC-14
                        $errors[] = "Không thể check-in, giao dịch đã bị hủy.";
                    }
                    if ($countStayed > 0 && $countBooked === 0) {
                        // TC-12
                        $errors[] = "Giao dịch này đã được check-in trước đó.";
                    }

                    // Xác định danh sách phòng được chọn
                    $selectedPhong = [];
                    if ($scope === 'all') {
                        // check-in toàn bộ
                        $selectedPhong = array_column($chiTiet, 'MaPhong');
                    } else {
                        $selectedPhong = array_map('intval', $_POST['phong_checkin'] ?? []);
                        if (empty($selectedPhong)) {
                            $errors[] = "Vui lòng chọn ít nhất một phòng để check-in hoặc chọn toàn bộ.";
                        }
                    }
                }
            }

            if (empty($errors)) {

                // Phân loại phòng đủ điều kiện / quá hạn / sai trạng thái
                $today = new DateTime(date('Y-m-d'));

                $phongDuDieuKien   = []; // sẽ được check-in
                $phongQuaHan       = []; // quá hạn giữ phòng
                $phongTooSoon      = []; // chưa đến ngày
                $phongSaiTrangThai = []; // không phải Booked

                foreach ($chiTiet as $ct) {
                    $maPhong = (int)$ct['MaPhong'];

                    // nếu không nằm trong danh sách được chọn → bỏ qua
                    if (!in_array($maPhong, $selectedPhong, true)) {
                        continue;
                    }

                    $st = $ct['TrangThai'];
                    if ($st !== 'Booked') {
                        $phongSaiTrangThai[] = [
                            'SoPhong'  => $ct['SoPhong'],
                            'TrangThai'=> $st,
                        ];
                        continue;
                    }

                    // Kiểm tra ngày nhận phòng
                    $ngayNhanStr = substr($ct['NgayNhanDuKien'], 0, 10);
                    $ngayNhan    = DateTime::createFromFormat('Y-m-d', $ngayNhanStr);
                    if (!$ngayNhan) {
                        // nếu lỗi định dạng thì cho qua nhưng ghi chú
                        $phongDuDieuKien[] = $maPhong;
                        continue;
                    }

                    $diffDays = (int)$today->diff($ngayNhan)->format('%r%a'); // >0: future, 0: today, <0: past

                    if ($diffDays > 0) {
                        // quá sớm
                        $phongTooSoon[] = $ct['SoPhong'];
                    } elseif ($diffDays < -1) {
                        // muộn hơn 1 ngày so với ngày nhận → quá hạn giữ phòng (TC-15,16)
                        $phongQuaHan[] = $ct['SoPhong'];
                    } else {
                        // hợp lệ (hôm nay hoặc trễ <=1 ngày)
                        $phongDuDieuKien[] = $maPhong;
                    }
                }

                if (!empty($phongSaiTrangThai)) {
                    foreach ($phongSaiTrangThai as $p) {
                        $errors[] = "Phòng {$p['SoPhong']} không ở trạng thái Booked (hiện: {$p['TrangThai']}).";
                    }
                }
                if (!empty($phongTooSoon)) {
                    $errors[] = "Một số phòng chưa đến ngày nhận phòng: " . implode(', ', $phongTooSoon) . ".";
                }

                if (empty($phongDuDieuKien)) {
                    if (!empty($phongQuaHan)) {
                        // TC-15 – tất cả quá hạn
                        $errors[] = "Không thể check-in, các phòng sau đã quá hạn giữ phòng: " . implode(', ', $phongQuaHan) . ".";
                    } elseif (empty($errors)) {
                        $errors[] = "Không có phòng nào đủ điều kiện check-in.";
                    }
                }

                // Nếu vẫn ổn → thực hiện cập nhật DB
                if (empty($errors) && !empty($phongDuDieuKien)) {

                    $db = Database::getConnection();
                    $db->begin_transaction();

                    try {
                        $now = date('Y-m-d H:i:s');

                        // 1. Cập nhật trạng thái phòng: Booked → Stayed
                        $sqlCT = "UPDATE chitietgiaodich 
                                  SET TrangThai = 'Stayed'
                                  WHERE MaGiaoDich = ? AND MaPhong = ? AND TrangThai = 'Booked'";
                        $stmtCT = $db->prepare($sqlCT);

                        foreach ($phongDuDieuKien as $maPhong) {
                            $stmtCT->bind_param("ii", $maGiaoDich, $maPhong);
                            if (!$stmtCT->execute()) {
                                throw new Exception("Không thể cập nhật trạng thái phòng $maPhong.");
                            }

                            // Cập nhật trạng thái bảng phong
                            $phongModel->updateTrangThai($maPhong, 'Stayed');
                        }

                        // 2. Xác định trạng thái giao dịch sau check-in
                        $sqlCheck = "SELECT TrangThai FROM chitietgiaodich WHERE MaGiaoDich = ?";
                        $stmtChk  = $db->prepare($sqlCheck);
                        $stmtChk->bind_param("i", $maGiaoDich);
                        $stmtChk->execute();
                        $rsChk = $stmtChk->get_result();

                        $conPhongBooked = false;
                        while ($row = $rsChk->fetch_assoc()) {
                            if ($row['TrangThai'] === 'Booked') {
                                $conPhongBooked = true;
                                break;
                            }
                        }

                        $trangThaiGD = $conPhongBooked ? 'DangO_MotPhan' : 'DangO';

                     // 3. Cập nhật giao dịch: trạng thái + log thời điểm check-in vào GhiChu
// Giao dịch chỉ cần biết là "đã check-in" => dùng trạng thái Stayed (đã có sẵn trong hệ thống)
$trangThaiGD = GiaoDich::STATUS_STAYED; // nếu em có constant
// hoặc đơn giản:
/// $trangThaiGD = 'Stayed';

$sqlGD = "UPDATE giaodich 
          SET TrangThai = ?, 
              GhiChu = CONCAT(IFNULL(GhiChu,''), ?) 
          WHERE MaGiaoDich = ?";
$stmtGD = $db->prepare($sqlGD);
$noteAdd = " | Check-in {$now}";
$stmtGD->bind_param("ssi", $trangThaiGD, $noteAdd, $maGiaoDich);
                        if (!$stmtGD->execute()) {
                            throw new Exception("Không thể cập nhật trạng thái giao dịch.");
                        }

                        // (Tuỳ hệ thống) kích hoạt tài khoản giao dịch để dùng dịch vụ thêm
                        // TODO: Nếu em có bảng tai_khoan_giao_dich, có thể cập nhật ở đây.

                        $db->commit();

                        // Tạo thông báo phù hợp: toàn bộ / một phần / có phòng quá hạn
                        $soPhongOK   = count($phongDuDieuKien);
                        $soPhongChon = count($selectedPhong);

                        if (!empty($phongQuaHan)) {
                            // TC-16
                            $success = "{$soPhongOK} phòng đã check-in thành công. "
                                     . "Các phòng sau không thể check-in do quá hạn: "
                                     . implode(', ', $phongQuaHan) . ".";
                        } else {
                            if ($scope === 'all' && !$conPhongBooked) {
                                // TC-1
                                $success = "Check-in toàn bộ phòng thành công.";
                            } elseif ($soPhongOK < $soPhongChon || $conPhongBooked) {
                                // TC-2,13
                                $success = "Check-in một phần thành công.";
                            } else {
                                $success = "Check-in thành công.";
                            }
                        }

                        // load lại dữ liệu sau khi lưu
                        $giaoDich = $gdModel->getById($maGiaoDich);
                        $chiTiet  = $ctgdModel->getPhongByGiaoDich($maGiaoDich);

                        // thông tin trưởng đoàn
                        $db2   = Database::getConnection();
                        $sqlKH = "SELECT * FROM khachhang WHERE MaKhachHang = ?";
                        $stmt2 = $db2->prepare($sqlKH);
                        $stmt2->bind_param("i", $giaoDich['MaKhachHang']);
                        $stmt2->execute();
                        $rs2   = $stmt2->get_result();
                        $khach = $rs2->fetch_assoc() ?: null;

                        $tenTruongDoan  = $khach['TenKH'] ?? '';
                        $cmndTruongDoan = $khach['CCCD']  ?? '';
                        $soThanhVien    = $giaoDich['SoNguoi'] ?? count($chiTiet);

                    } catch (\Throwable $ex) {
                        $db->rollback();
                        // TC-17 – lỗi DB
                        $errors[] = "Không thể ghi nhận Check-in, vui lòng thử lại hoặc liên hệ kỹ thuật. (Chi tiết: "
                                  . $ex->getMessage() . ")";
                    }
                }
            }
        }

        // ================== 3. HỦY CHECK-IN (KHÔNG LƯU) ==================
        if ($action === 'abort') {
            // TC-11
            $success = "Đã hủy thao tác check-in. Không có dữ liệu nào được thay đổi.";
        }
    }

    // Render view
    $this->view("letan/check_in", [
        'errors'         => $errors,
        'success'        => $success,
        'giaoDich'       => $giaoDich,
        'chiTiet'        => $chiTiet,
        'searchMaGD'     => $searchMaGD,
        'searchCMND'     => $searchCMND,
        'tenTruongDoan'  => $tenTruongDoan,
        'cmndTruongDoan' => $cmndTruongDoan,
        'soThanhVien'    => $soThanhVien,
    ]);
}
public function datDichVu()
{
    $this->requireRole(['LeTan']);

    $gdModel    = new GiaoDich();
    $ctgdModel  = new ChiTietGiaoDich();
    $dvModel    = new DichVu();
    $ctdvModel  = new ChiTietDichVu();

    $errors        = [];
    $success       = null;
    $searchKeyword = '';
    $giaoDich      = null;
    $chiTietPhong  = [];
    $dsDichVu      = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $action = $_POST['btn_action'] ?? '';

        // HỦY → quay về lễ tân
        if ($action === 'cancel') {
            header("Location: index.php?controller=letan&action=index");
            exit;
        }

        // 1. TÌM KIẾM
        if ($action === 'search') {

            $searchKeyword = trim($_POST['search_keyword'] ?? '');

            if ($searchKeyword === '') {
                $errors[] = "Vui lòng nhập mã giao dịch hoặc CMND để tra cứu.";
            } else {
                // nếu có ký tự không phải chữ + số thì coi là không hợp lệ
                if (preg_match('/[^0-9]/', $searchKeyword)) {
                    $errors[] = "Dữ liệu nhập không hợp lệ. Chỉ nhập số (mã giao dịch hoặc CMND/CCCD).";
                } else {
                    try {
                        // toàn số → tìm theo mã GD hoặc CCCD
                        $giaoDich = $gdModel->findByMaOrCCCD($searchKeyword, $searchKeyword);
                    } catch (\Throwable $ex) {
                        $errors[] = "Không thể truy vấn dữ liệu.";
                    }

                    if (!$giaoDich) {
                        $errors[] = "Không tìm thấy giao dịch.";
                    } else {
                        // chỉ chấp nhận Stayed
                        if ($giaoDich['TrangThai'] !== GiaoDich::STATUS_STAYED
                            && $giaoDich['TrangThai'] !== 'Stayed') {

                            $errors[] = "Không tìm thấy giao dịch hợp lệ (giao dịch chưa check-in).";
                            $giaoDich = null;
                        } else {

                            $maGD = (int)$giaoDich['MaGiaoDich'];

                            $chiTietPhong = $ctgdModel->getPhongByGiaoDich($maGD) ?? [];
                            $chiTietPhong = array_values(array_filter($chiTietPhong, function ($ct) {
                                return $ct['TrangThai'] !== ChiTietGiaoDich::STATUS_CANCELLED
                                       && $ct['TrangThai'] !== 'DaHuy';
                            }));

                            $dsDichVu = $dvModel->getActive() ?? [];
                        }
                    }
                }
            }
        }

        // 2. LƯU ĐẶT DỊCH VỤ
        if ($action === 'save') {

            $maGiaoDich   = (int)($_POST['ma_giao_dich'] ?? 0);
            $searchKeyword = trim($_POST['search_keyword'] ?? '');

            if ($maGiaoDich <= 0) {
                $errors[] = "Thiếu thông tin mã giao dịch.";
            }

            if ($maGiaoDich > 0) {
                try {
                    $giaoDich = $gdModel->getById($maGiaoDich);
                } catch (\Throwable $ex) {
                    $errors[] = "Không thể truy vấn dữ liệu giao dịch.";
                }

                if ($giaoDich) {
                    if ($giaoDich['TrangThai'] !== GiaoDich::STATUS_STAYED
                        && $giaoDich['TrangThai'] !== 'Stayed') {
                        $errors[] = "Giao dịch chưa check-in, không thể đặt dịch vụ.";
                    }

                    $chiTietPhong = $ctgdModel->getPhongByGiaoDich($maGiaoDich) ?? [];
                    $chiTietPhong = array_values(array_filter($chiTietPhong, function ($ct) {
                        return $ct['TrangThai'] !== ChiTietGiaoDich::STATUS_CANCELLED
                               && $ct['TrangThai'] !== 'DaHuy';
                    }));

                    $dsDichVu = $dvModel->getActive() ?? [];

                    // index dịch vụ
                    $dvIndex = [];
                    foreach ($dsDichVu as $dv) {
                        $dvIndex[(int)$dv['MaDichVu']] = $dv;
                    }
                } else {
                    $errors[] = "Không tìm thấy giao dịch.";
                }
            }

            // list phòng hợp lệ
            $phongHopLe = [];
            foreach ($chiTietPhong as $ct) {
                $phongHopLe[(int)$ct['MaPhong']] = $ct;
            }

            $services      = $_POST['services'] ?? [];
            $selectedItems = [];

            if (empty($services)) {
                $errors[] = "Vui lòng chọn ít nhất một dịch vụ.";
            } else {
                foreach ($services as $maDV => $info) {
                    $maDV    = (int)$maDV;
                    $soLuong = (int)($info['so_luong'] ?? 0);
                    $maPhong = (int)($info['ma_phong'] ?? 0);
                    $ghiChu  = trim($info['note'] ?? '');

                    if ($soLuong === 0 && $maPhong === 0 && $ghiChu === '') {
                        continue;
                    }

                    if ($soLuong <= 0) {
                        $errors[] = "Số lượng không hợp lệ cho dịch vụ ID {$maDV}.";
                        continue;
                    }

                    if (!isset($dvIndex[$maDV])) {
                        $errors[] = "Dịch vụ không khả dụng.";
                        continue;
                    }

                    $dvRow = $dvIndex[$maDV];

                    if (isset($dvRow['TrangThai']) && $dvRow['TrangThai'] !== 'HoatDong') {
                        $errors[] = "Dịch vụ {$dvRow['TenDichVu']} không khả dụng.";
                    }

                    if ($maPhong <= 0 || !isset($phongHopLe[$maPhong])) {
                        $errors[] = "Mã phòng không hợp lệ hoặc không thuộc giao dịch.";
                    }

                    $selectedItems[] = [
                        'ma_dv'    => $maDV,
                        'dv'       => $dvRow,
                        'so_luong' => $soLuong,
                        'ma_phong' => $maPhong,
                        'ghi_chu'  => $ghiChu,
                    ];
                }

                if (empty($selectedItems) && empty($errors)) {
                    $errors[] = "Vui lòng nhập số lượng cho ít nhất một dịch vụ.";
                }
            }

            if (empty($errors) && !empty($selectedItems)) {
                $db = Database::getConnection();
                $db->begin_transaction();

                try {
                    $tongTienThem = 0;

                    foreach ($selectedItems as $item) {
                        $maDV    = $item['ma_dv'];
                        $dvRow   = $item['dv'];
                        $maPhong = $item['ma_phong'];
                        $soLuong = $item['so_luong'];
                        $ghiChu  = $item['ghi_chu'];

                        $gia        = (float)$dvRow['GiaDichVu'];
                        $thanhTien  = $gia * $soLuong;
                        $tongTienThem += $thanhTien;

                        // kiểm tra đã tồn tại dòng dịch vụ đó chưa
                        $sqlExist = "
                            SELECT SoLuong, ThanhTien
                            FROM chitietdichvu
                            WHERE MaGiaoDich = ? AND MaPhong = ? AND MaDichVu = ?
                            LIMIT 1
                        ";
                        $stmtEx = $db->prepare($sqlExist);
                        $stmtEx->bind_param("iii", $maGiaoDich, $maPhong, $maDV);
                        $stmtEx->execute();
                        $rsEx  = $stmtEx->get_result();
                        $rowEx = $rsEx->fetch_assoc();

                        if ($rowEx) {
                            $sqlUpd = "
                                UPDATE chitietdichvu
                                SET SoLuong = SoLuong + ?, 
                                    ThanhTien = ThanhTien + ?
                                WHERE MaGiaoDich = ? AND MaPhong = ? AND MaDichVu = ?
                            ";
                            $stmtUpd = $db->prepare($sqlUpd);
                            $stmtUpd->bind_param(
                                "idiii",
                                $soLuong,
                                $thanhTien,
                                $maGiaoDich,
                                $maPhong,
                                $maDV
                            );
                            if (!$stmtUpd->execute()) {
                                throw new Exception("Không thể cập nhật dịch vụ.");
                            }
                        } else {
                            $ctdvModel->addService([
                                'MaGiaoDich' => $maGiaoDich,
                                'MaPhong'    => $maPhong,
                                'MaDichVu'   => $maDV,
                                'SoLuong'    => $soLuong,
                                'GiaBan'     => $gia,
                                'ThanhTien'  => $thanhTien,
                                'GhiChu'     => $ghiChu,
                            ]);
                        }
                    }

                    if ($tongTienThem > 0) {
                        $sqlTong  = "UPDATE giaodich SET TongTien = TongTien + ? WHERE MaGiaoDich = ?";
                        $stmtTong = $db->prepare($sqlTong);
                        $stmtTong->bind_param("di", $tongTienThem, $maGiaoDich);
                        if (!$stmtTong->execute()) {
                            throw new Exception("Không thể cập nhật tổng tiền.");
                        }
                    }

                    $db->commit();

                    $summaryLines = [];
                    foreach ($selectedItems as $item) {
                        $summaryLines[] = sprintf(
                            "%s - Phòng %s x%d",
                            $item['dv']['TenDichVu'],
                            $phongHopLe[$item['ma_phong']]['SoPhong'] ?? $item['ma_phong'],
                            $item['so_luong']
                        );
                    }

                    $success = "Đặt dịch vụ thành công. "
                        . "Chi tiết: " . implode("; ", $summaryLines)
                        . ". Tổng cộng thêm: " . number_format($tongTienThem, 0, ',', '.') . " đ.";

                } catch (\Throwable $ex) {
                    $db->rollback();
                    $errors[] = "Đặt dịch vụ thất bại. Vui lòng thử lại sau.";
                }

                // load lại dữ liệu để show
                $giaoDich     = $gdModel->getById($maGiaoDich);
                $chiTietPhong = $ctgdModel->getPhongByGiaoDich($maGiaoDich) ?? [];
                $chiTietPhong = array_values(array_filter($chiTietPhong, function ($ct) {
                    return $ct['TrangThai'] !== ChiTietGiaoDich::STATUS_CANCELLED
                           && $ct['TrangThai'] !== 'DaHuy';
                }));
                $dsDichVu = $dvModel->getActive() ?? [];
            }
        }
    }

    $this->view("letan/dat_dich_vu", [
        'errors'        => $errors,
        'success'       => $success,
        'searchKeyword' => $searchKeyword,
        'giaoDich'      => $giaoDich,
        'chiTietPhong'  => $chiTietPhong,
        'dsDichVu'      => $dsDichVu,
    ]);

}
// =================== USE CASE: CHECK-OUT ===================
public function checkOut()
{
    $this->requireRole(['LeTan']);

    $gdModel    = new GiaoDich();
    $ctgdModel  = new ChiTietGiaoDich();
    $ctdvModel  = new ChiTietDichVu();   // Đảm bảo đã có model này
    $phongModel = new Phong();
    $khModel    = new KhachHang();

    $errors        = [];
    $success       = null;
    $searchKeyword = '';
    $giaoDich      = null;
    $chiTietPhong  = [];
    $chiTietDV     = [];

    // dữ liệu form
    $form = [
        'selected_rooms' => [],
        'late_fee'       => 0,
        'has_damage'     => 'none',
        'damage_note'    => '',
        'damage_fee'     => 0,
        'payment_method' => '',
        'payment_status' => 'unpaid',
    ];

    // Tóm tắt chi phí hiển thị trên view
    $summary = [
        'room_total'    => 0,
        'service_total' => 0,
        'late_fee'      => 0,
        'damage_fee'    => 0,
        'grand_total'   => 0,
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['btn_action'] ?? '';

        // ---- HỦY THAO TÁC: quay lại trang lễ tân ----
        if ($action === 'cancel') {
            header("Location: index.php?controller=letan&action=index");
            exit;
        }

        // ================= 1. TÌM KIẾM GIAO DỊCH =================
        if ($action === 'search') {

            $searchKeyword = trim($_POST['search_keyword'] ?? '');

            if ($searchKeyword === '') {
                $errors[] = "Vui lòng nhập thông tin tìm kiếm.";
            } else {
                try {
                    // Nếu toàn số → ưu tiên tìm theo MaGiaoDich (PK trong DB)
                    if (ctype_digit($searchKeyword)) {
                        $maGD     = (int)$searchKeyword;
                        $giaoDich = $gdModel->getById($maGD);

                        // fallback: nếu không có, mà là 9–12 số → thử tìm theo CCCD
                        if (!$giaoDich && preg_match('/^\d{9,12}$/', $searchKeyword)) {
                            $giaoDich = $gdModel->findByMaOrCCCD(null, $searchKeyword);
                        }
                    } else {
                        // Không phải toàn số → coi là CCCD / mã đặc biệt
                        $giaoDich = $gdModel->findByMaOrCCCD(null, $searchKeyword);
                    }
                } catch (\Throwable $ex) {
                    $errors[] = "Không thể truy vấn dữ liệu.";
                }

                if (!$giaoDich) {
                    $errors[] = "Không tìm thấy giao dịch phù hợp.";
                } else {
                    $maGD = (int)$giaoDich['MaGiaoDich'];

                    // Lấy thêm thông tin khách hàng (TenKH, CCCD, SDT)
                    $maKH = (int)($giaoDich['MaKhachHang'] ?? 0);
                    if ($maKH > 0) {
                        $kh = null;
                        if (method_exists($khModel, 'getById')) {
                            $kh = $khModel->getById($maKH);
                        } else {
                            $db = Database::getConnection();
                            $stmt = $db->prepare("SELECT * FROM khachhang WHERE MaKhachHang = ?");
                            $stmt->bind_param("i", $maKH);
                            $stmt->execute();
                            $kh = $stmt->get_result()->fetch_assoc();
                        }
                        if ($kh) {
                            $giaoDich['TenKH'] = $kh['TenKH'] ?? ($giaoDich['TenKH'] ?? '');
                            $giaoDich['CCCD']  = $kh['CCCD']  ?? ($giaoDich['CCCD'] ?? '');
                            $giaoDich['SDT']   = $kh['SDT']   ?? ($giaoDich['SDT'] ?? '');
                        }
                    }

                    // Lấy toàn bộ chi tiết phòng trong giao dịch
                    $allRooms = $ctgdModel->getPhongByGiaoDich($maGD);

                    // 🔥 Chỉ giữ lại phòng đang Stayed để hiển thị / cho phép check-out
                    $chiTietPhong = [];
                    foreach ($allRooms as $ct) {
                        if (isset($ct['TrangThai']) && $ct['TrangThai'] === 'Stayed') {
                            $chiTietPhong[] = $ct;
                        }
                    }

                    if (empty($chiTietPhong)) {
                        $errors[] = "Không có phòng phù hợp để Check-out (không có phòng đang Stayed).";
                    } else {
                        // Lấy dịch vụ gắn với giao dịch (chưa lọc theo phòng ở bước này)
                        $chiTietDV = $ctdvModel->getByGiaoDich($maGD);

                        // Tính tổng tiền phòng/dịch vụ cho tất cả phòng Stayed (để xem tổng tạm)
                        $roomTotal = 0;
                        foreach ($chiTietPhong as $ct) {
                            $roomTotal += (float)($ct['ThanhTien'] ?? 0);
                        }

                        $serviceTotal = 0;
                        foreach ($chiTietDV as $dv) {
                            $serviceTotal += (float)($dv['ThanhTien'] ?? 0);
                        }

                        $summary['room_total']    = $roomTotal;
                        $summary['service_total'] = $serviceTotal;
                        $summary['late_fee']      = 0;
                        $summary['damage_fee']    = 0;
                        $summary['grand_total']   = $roomTotal + $serviceTotal;
                    }
                }
            }
        }

        // ================= 2. HOÀN TẤT CHECK-OUT =================
        if ($action === 'checkout') {

            $maGiaoDich    = (int)($_POST['ma_giao_dich'] ?? 0);
            $selectedRooms = array_map('intval', $_POST['rooms'] ?? []);

            $form['selected_rooms'] = $selectedRooms;
            $form['has_damage']     = $_POST['has_damage']     ?? 'none';
            $form['damage_note']    = trim($_POST['damage_note'] ?? '');
            $form['payment_method'] = $_POST['payment_method'] ?? '';
            $form['payment_status'] = $_POST['payment_status'] ?? 'unpaid';

            if ($maGiaoDich <= 0) {
                $errors[] = "Thiếu thông tin mã giao dịch.";
            }
            if (empty($selectedRooms)) {
                $errors[] = "Vui lòng chọn ít nhất một phòng để Check-out.";
            }
            if ($form['payment_method'] === '') {
                $errors[] = "Vui lòng chọn phương thức thanh toán.";
            }
            if ($form['payment_status'] !== 'paid') {
                $errors[] = "Vui lòng xác nhận 'Đã thanh toán' trước khi hoàn tất Check-out.";
            }

            // Lấy lại GD + chi tiết để tính toán chính xác
            if (empty($errors)) {
                try {
                    $giaoDich = $gdModel->getById($maGiaoDich);
                    if (!$giaoDich) {
                        $errors[] = "Không tìm thấy giao dịch.";
                    } else {
                        $maKH = (int)($giaoDich['MaKhachHang'] ?? 0);
                        if ($maKH > 0) {
                            $kh = null;
                            if (method_exists($khModel, 'getById')) {
                                $kh = $khModel->getById($maKH);
                            } else {
                                $db = Database::getConnection();
                                $stmt = $db->prepare("SELECT * FROM khachhang WHERE MaKhachHang = ?");
                                $stmt->bind_param("i", $maKH);
                                $stmt->execute();
                                $kh = $stmt->get_result()->fetch_assoc();
                            }
                            if ($kh) {
                                $giaoDich['TenKH'] = $kh['TenKH'] ?? ($giaoDich['TenKH'] ?? '');
                                $giaoDich['CCCD']  = $kh['CCCD']  ?? ($giaoDich['CCCD'] ?? '');
                                $giaoDich['SDT']   = $kh['SDT']   ?? ($giaoDich['SDT'] ?? '');
                            }
                        }

                        // Toàn bộ chi tiết phòng của GD
                        $allRooms = $ctgdModel->getPhongByGiaoDich($maGiaoDich);
                        if (empty($allRooms)) {
                            $errors[] = "Giao dịch không có phòng.";
                        } else {
                            // chỉ các phòng Stayed (đúng logic)
                            $chiTietPhong = [];
                            foreach ($allRooms as $ct) {
                                if (isset($ct['TrangThai']) && $ct['TrangThai'] === 'Stayed') {
                                    $chiTietPhong[] = $ct;
                                }
                            }
                            if (empty($chiTietPhong)) {
                                $errors[] = "Không có phòng đang Stayed để Check-out.";
                            } else {
                                $chiTietDV = $ctdvModel->getByGiaoDich($maGiaoDich);
                            }
                        }
                    }
                } catch (\Throwable $ex) {
                    $errors[] = "Không thể truy vấn dữ liệu.";
                }
            }

            // ===== TÍNH TIỀN CHO CÁC PHÒNG ĐƯỢC CHỌN =====
            if (empty($errors)) {
                $now = new DateTime();

                $roomTotalSelected    = 0;   // chỉ các phòng được chọn
                $lateFee              = 0;
                $serviceTotalSelected = 0;

                // 2.1 Tiền phòng & phụ thu chỉ trên phòng được chọn + trạng thái Stayed
                foreach ($chiTietPhong as $ct) {
                    $maPhong = (int)$ct['MaPhong'];
                    if (!in_array($maPhong, $selectedRooms, true)) {
                        continue; // không chọn thì bỏ qua
                    }

                    if (($ct['TrangThai'] ?? '') !== 'Stayed') {
                        continue; // chỉ cho check-out phòng đang Stayed
                    }

                    $money = (float)($ct['ThanhTien'] ?? 0);
                    $roomTotalSelected += $money;

                    // kiểm tra trả phòng muộn
                    $dueStr = $ct['NgayTraDuKien'] ?? null;
                    if ($dueStr) {
                        $due = new DateTime($dueStr);
                        if ($now > $due) {
                            // ví dụ: phụ thu 10% tiền phòng
                            $lateFee += $money * 0.1;
                        }
                    }
                }

                // 2.2 Tiền dịch vụ cho các phòng được chọn
                //  - nếu MaPhong gắn với 1 phòng → chỉ tính nếu phòng đó trong selectedRooms
                //  - nếu MaPhong NULL → coi là dùng chung, vẫn tính (hoặc bạn tự chỉnh logic)
                foreach ($chiTietDV as $dv) {
                    $maPhongDV = isset($dv['MaPhong']) ? (int)$dv['MaPhong'] : 0;

                    if ($maPhongDV > 0 && !in_array($maPhongDV, $selectedRooms, true)) {
                        continue; // dịch vụ của phòng chưa trả → không tính
                    }

                    $serviceTotalSelected += (float)($dv['ThanhTien'] ?? 0);
                }

                // 2.3 Bồi thường (cho phép nhập tay, tùy theo has_damage)
                $damageFee = 0;
                if ($form['has_damage'] === 'light' || $form['has_damage'] === 'heavy') {
                    $damageFee = max(0, (float)($_POST['damage_fee'] ?? 0));
                }

                $form['late_fee']   = $lateFee;
                $form['damage_fee'] = $damageFee;

                $summary['room_total']    = $roomTotalSelected;
                $summary['service_total'] = $serviceTotalSelected;
                $summary['late_fee']      = $lateFee;
                $summary['damage_fee']    = $damageFee;
                $summary['grand_total']   = $roomTotalSelected + $serviceTotalSelected + $lateFee + $damageFee;

                $tongTien = $summary['grand_total'];

                // Map phương thức thanh toán sang enum / text trong DB
                switch ($form['payment_method']) {
                    case 'cash':
                        $pttt = 'TienMat';
                        break;
                    case 'transfer':
                        $pttt = 'ChuyenKhoan';
                        break;
                    case 'card':
                        $pttt = 'The';
                        break;
                    default:
                        $pttt = 'TienMat';
                }

                $ghiChuThem = "Check-out lúc " . $now->format('Y-m-d H:i:s')
                    . "; PTTT: {$pttt}; Tổng: " . number_format($tongTien, 0, ',', '.') . "đ"
                    . "; Phụ thu: " . number_format($lateFee, 0, ',', '.') . "đ"
                    . "; Bồi thường: " . number_format($damageFee, 0, ',', '.') . "đ"
                    . ($form['damage_note'] ? ("; Ghi chú: " . $form['damage_note']) : "");

                $db = Database::getConnection();
                $db->begin_transaction();

                try {
                    // 1. Cập nhật trạng thái phòng được chọn về 'Trong' (Available)
                    foreach ($selectedRooms as $maPhong) {
                        $phongModel->updateTrangThai((int)$maPhong, 'Trong');
                    }

                    // 2. Cập nhật tổng tiền + phương thức thanh toán + ghi chú vào giao dịch
                    $sqlGD = "
                        UPDATE giaodich
                        SET TongTien = ?,
                            PhuongThucThanhToan = ?,
                            GhiChu = CONCAT(IFNULL(GhiChu,''), '\n', ?)
                        WHERE MaGiaoDich = ?
                    ";
                    $stmtGD = $db->prepare($sqlGD);
                    $stmtGD->bind_param(
                        "dssi",
                        $tongTien,
                        $pttt,
                        $ghiChuThem,
                        $maGiaoDich
                    );
                    if (!$stmtGD->execute()) {
                        throw new Exception("Không thể cập nhật thông tin thanh toán.");
                    }

                    // TODO: nếu muốn, kiểm tra xem còn phòng nào Stayed không,
                    // nếu không còn → cập nhật trạng thái giao dịch = 'DaTraPhong' / 'Closed'...

                    $db->commit();

                    // 🔥 Không redirect nữa để giữ thông báo thành công
                    $success = "Trả phòng và thanh toán thành công.";

                    // Sau khi thành công, load lại dữ liệu để hiển thị (nếu muốn xem lại)
                    $giaoDich     = $gdModel->getById($maGiaoDich);
                    $chiTietPhong = $ctgdModel->getPhongByGiaoDich($maGiaoDich);
                    $chiTietDV    = $ctdvModel->getByGiaoDich($maGiaoDich);

                } catch (\Throwable $ex) {
                    $db->rollback();
                    $errors[] = "Không thể lưu thay đổi, vui lòng thử lại. (Chi tiết: " . $ex->getMessage() . ")";
                }
            }
        }
    }

    // Render view
    $this->view("letan/check_out", [
        'errors'        => $errors,
        'success'       => $success,
        'searchKeyword' => $searchKeyword,
        'giaoDich'      => $giaoDich,
        'chiTietPhong'  => $chiTietPhong,   // chỉ phòng Stayed (ở search), hoặc full sau checkout
        'chiTietDV'     => $chiTietDV,
        'form'          => $form,
        'summary'       => $summary,
    ]);
}
}