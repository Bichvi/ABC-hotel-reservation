<?php
class AdminController extends Controller
{
    public function __construct() {
        $this->requireLogin();
        $this->requireRole([1, 'Admin']);
    }


    // === QUẢN LÝ PHÒNG (MỖI MÀN HÌNH RIÊNG) ===
    public function phong()
    {
        header("Location: index.php?controller=admin&action=phongThem");
        exit;
    }

    public function phongThem()
    {
        $model  = new Phong();
        $errors = [];
        $success = null;
        $old = [
            'SoPhong'       => '',
            'LoaiPhong'     => 'Standard',
            'DienTich'      => '',
            'LoaiGiuong'    => '',
            'ViewPhong'     => '',
            'Gia'           => '',
            'SoKhachToiDa'  => '',
            'TinhTrangPhong'=> 'Tot',
            'HinhAnh'       => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = [
                'SoPhong'       => trim($_POST['SoPhong'] ?? ''),
                'LoaiPhong'     => trim($_POST['LoaiPhong'] ?? 'Standard'),
                'DienTich'      => trim($_POST['DienTich'] ?? ''),
                'LoaiGiuong'    => trim($_POST['LoaiGiuong'] ?? ''),
                'ViewPhong'     => trim($_POST['ViewPhong'] ?? ''),
                'Gia'           => trim($_POST['Gia'] ?? ''),
                'SoKhachToiDa'  => trim($_POST['SoKhachToiDa'] ?? ''),
                'TinhTrangPhong'=> trim($_POST['TinhTrangPhong'] ?? 'Tot'),
                'HinhAnh'       => ''
            ];

            if ($old['SoPhong'] === '') {
                $errors[] = "Vui lòng nhập số phòng.";
            } elseif ($model->existsBySoPhong($old['SoPhong'])) {
                $errors[] = "Số phòng đã tồn tại. Vui lòng nhập số khác.";
            }

            $dienTich = (float)$old['DienTich'];
            if ($dienTich < 15) {
                $errors[] = "Diện tích phải lớn hơn hoặc bằng 15m².";
            }

            $gia = (float)$old['Gia'];
            if ($gia < 0) {
                $errors[] = "Giá phải lớn hơn hoặc bằng 0.";
            }

            $soKhach = (int)$old['SoKhachToiDa'];
            if ($soKhach <= 0) {
                $errors[] = "Số lượng khách tối đa phải lớn hơn 0.";
            }

            // Đảm bảo TinhTrangPhong luôn có giá trị hợp lệ, mặc định là 'Tot'
            if (empty($old['TinhTrangPhong']) || !in_array($old['TinhTrangPhong'], ['Tot', 'CanVeSinh', 'HuHaiNhe', 'HuHaiNang', 'DangBaoTri'], true)) {
                $old['TinhTrangPhong'] = 'Tot';
            }
            
            // Validate ViewPhong - chỉ chấp nhận 3 giá trị từ database
            if (!in_array($old['ViewPhong'], ['Biển', 'Thành phố', 'Vườn'], true)) {
                $errors[] = "View phòng không hợp lệ. Vui lòng chọn một trong: Biển, Thành phố, Vườn.";
            }

            // Xử lý upload hình ảnh
            $hinhAnh = null; // Mặc định là null
            if (!empty($_FILES['HinhAnh']) && $_FILES['HinhAnh']['size'] > 0) {
                $uploadResult = $this->handleImageUpload($_FILES['HinhAnh'], 'phong');
                if (is_array($uploadResult) && isset($uploadResult['error'])) {
                    $errors[] = $uploadResult['error'];
                } else {
                    $hinhAnh = $uploadResult; // Tên file đã upload
                }
            }

            if (empty($errors)) {
                try {
                    $model->createRoom([
                        'SoPhong'        => $old['SoPhong'],
                        'LoaiPhong'      => $old['LoaiPhong'],
                        'DienTich'       => $dienTich,
                        'LoaiGiuong'     => $old['LoaiGiuong'],
                        'ViewPhong'      => $old['ViewPhong'],
                        'Gia'            => $gia,
                        'TrangThai'      => 'Trong',
                        'SoKhachToiDa'   => $soKhach,
                        'TinhTrangPhong' => $old['TinhTrangPhong'],
                        'HinhAnh'        => $hinhAnh
                    ]);
                    $success = "Thêm phòng thành công.";
                    $old = [
                        'SoPhong'       => '',
                        'LoaiPhong'     => 'Standard',
                        'DienTich'      => '',
                        'LoaiGiuong'    => '',
                        'ViewPhong'     => '',
                        'Gia'           => '',
                        'SoKhachToiDa'  => '',
                        'TinhTrangPhong'=> 'Tot',
                        'HinhAnh'       => ''
                    ];
                } catch (Exception $e) {
                    $errorMsg = $e->getMessage();
                    error_log("Lỗi thêm phòng: " . $errorMsg);
                    // Hiển thị lỗi chi tiết để debug
                    $errors[] = "Không thể lưu dữ liệu: " . $errorMsg;
                }
            }
        }

        $this->view('admin/phong_them', [
            'rooms'   => $model->getAllRooms(),
            'errors'  => $errors,
            'success' => $success,
            'old'     => $old,
            'loaiGiuongList' => $model->getDistinctLoaiGiuong()
        ]);
    }

    public function phongSua()
    {
        $model   = new Phong();
        $errors  = [];
        $success = null;
        $rooms   = $model->getAllRooms();

        $selectedId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $selectedId = (int)($_POST['MaPhong'] ?? 0);
            $currentRoom = $model->getById($selectedId);
            
            if ($selectedId <= 0 || !$currentRoom) {
                $errors[] = "Không thể tải thông tin phòng.";
            } else {
                // Lấy giá trị từ POST, nếu không có hoặc rỗng thì dùng giá trị hiện tại
            $payload = [
                    'SoPhong'       => trim($_POST['SoPhong'] ?? '') ?: $currentRoom['SoPhong'] ?? '',
                    'LoaiPhong'     => trim($_POST['LoaiPhong'] ?? '') ?: $currentRoom['LoaiPhong'] ?? '',
                    'DienTich'      => !empty($_POST['DienTich']) ? (float)$_POST['DienTich'] : (float)($currentRoom['DienTich'] ?? 0),
                    'LoaiGiuong'    => trim($_POST['LoaiGiuong'] ?? '') ?: $currentRoom['LoaiGiuong'] ?? '',
                    'ViewPhong'     => trim($_POST['ViewPhong'] ?? '') ?: $currentRoom['ViewPhong'] ?? '',
                    'Gia'           => !empty($_POST['Gia']) ? (float)$_POST['Gia'] : (float)($currentRoom['Gia'] ?? 0),
                    'SoKhachToiDa'  => !empty($_POST['SoKhachToiDa']) ? (int)$_POST['SoKhachToiDa'] : (int)($currentRoom['SoKhachToiDa'] ?? 0),
                    'GhiChu'        => trim($_POST['GhiChu'] ?? '') ?: ($currentRoom['GhiChu'] ?? ''),
                    'TinhTrangPhong'=> trim($_POST['TinhTrangPhong'] ?? '') ?: ($currentRoom['TinhTrangPhong'] ?? 'Tot'),
                    'HinhAnh'       => $currentRoom['HinhAnh'] ?? ''
                ];

                // Validation chỉ cho các trường bắt buộc
            if ($payload['SoPhong'] === '') {
                $errors[] = "Số phòng không được để trống.";
            } elseif ($model->existsBySoPhong($payload['SoPhong'], $selectedId)) {
                $errors[] = "Số phòng đã tồn tại.";
            }

            if ($payload['DienTich'] < 15) {
                $errors[] = "Diện tích phải lớn hơn hoặc bằng 15m².";
            }

            if ($payload['Gia'] < 0) {
                $errors[] = "Giá phải lớn hơn hoặc bằng 0.";
            }

            if ($payload['SoKhachToiDa'] <= 0) {
                $errors[] = "Số lượng khách tối đa phải lớn hơn 0.";
            }

            if (!in_array($payload['TinhTrangPhong'], ['Tot', 'CanVeSinh', 'HuHaiNhe', 'HuHaiNang', 'DangBaoTri'], true)) {
                $errors[] = "Tình trạng phòng không hợp lệ.";
            }
            
            // Validate ViewPhong - chỉ chấp nhận 3 giá trị từ database
            if (!in_array($payload['ViewPhong'], ['Biển', 'Thành phố', 'Vườn'], true)) {
                $errors[] = "View phòng không hợp lệ. Vui lòng chọn một trong: Biển, Thành phố, Vườn.";
            }

            // Xử lý upload hình ảnh nếu có
            if (!empty($_FILES['HinhAnh']) && $_FILES['HinhAnh']['size'] > 0) {
                $uploadResult = $this->handleImageUpload($_FILES['HinhAnh'], 'phong');
                if (is_array($uploadResult) && isset($uploadResult['error'])) {
                    $errors[] = $uploadResult['error'];
                } else {
                    $payload['HinhAnh'] = $uploadResult;
                    }
                }
            }

            if (empty($errors)) {
                try {
                    $model->updateRoom($selectedId, $payload);
                    $success = "Cập nhật phòng thành công.";
                } catch (Exception $e) {
                    $errors[] = "Không thể lưu dữ liệu. Vui lòng thử lại.";
                    error_log($e->getMessage());
                }
            }
        }

        $selectedRoom = $selectedId ? $model->getById($selectedId) : null;
        $this->view('admin/phong_sua', [
            'rooms'        => $rooms,
            'selectedRoom' => $selectedRoom,
            'errors'       => $errors,
            'success'      => $success,
            'loaiGiuongList' => $model->getDistinctLoaiGiuong()
        ]);
    }

    public function phongXoa()
    {
        $model   = new Phong();
        $errors  = [];
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ids = $_POST['chon'] ?? [];
            if (empty($ids)) {
                $errors[] = "Vui lòng chọn ít nhất một phòng cần xóa.";
            } else {
                $deleted = [];
                foreach ($ids as $id) {
                    $id = (int)$id;
                    $room = $model->getById($id);
                    if (!$room) {
                        $errors[] = "Không thể tải thông tin phòng #$id.";
                        continue;
                    }
                    if ($model->hasActiveBooking($id)) {
                        $errors[] = "Không thể xóa phòng {$room['SoPhong']} vì đang có giao dịch xử lý.";
                        continue;
                    }
                    try {
                        $model->deleteRoom($id);
                        $deleted[] = $room['SoPhong'];
                    } catch (Exception $e) {
                        $errors[] = "Xóa phòng {$room['SoPhong']} thất bại.";
                        error_log($e->getMessage());
                    }
                }
                if ($deleted) {
                    $success = "Đã xóa phòng: " . implode(', ', $deleted);
                }
            }
        }

        $this->view('admin/phong_xoa', [
            'rooms'   => $model->getAllRooms(),
            'errors'  => $errors,
            'success' => $success
        ]);
    }

    // === QUẢN LÝ TÀI KHOẢN (CÁC MÀN HÌNH CON) ===
    public function nguoiDungThem()
    {
        $roleModel = new Role();
        $tkModel   = new TaiKhoan();
        $errors    = [];
        $success   = null;
        $old = [
            'HoTen'     => '',
            'Email'     => '',
            'SoDienThoai'=> '',
            'CCCD'      => '',
            'DiaChi'    => '',
            'VaiTro'    => '',
            'Username'  => '',
            'TrangThai' => 'HoatDong'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = [
                'HoTen'       => trim($_POST['HoTen'] ?? ''),
                'Email'       => trim($_POST['Email'] ?? ''),
                'SoDienThoai' => trim($_POST['SoDienThoai'] ?? ''),
                'CCCD'        => trim($_POST['CCCD'] ?? ''),
                'DiaChi'      => trim($_POST['DiaChi'] ?? ''),
                'VaiTro'      => (int)($_POST['VaiTro'] ?? 7),
                'LoaiKhach'   => trim($_POST['LoaiKhach'] ?? 'Cá nhân'),
                'Username'    => trim($_POST['Username'] ?? ''),
                'TrangThai'   => $_POST['TrangThai'] ?? 'HoatDong'
            ];
            $password = $_POST['Password'] ?? '';

            if ($old['HoTen'] === '') {
                $errors[] = "Vui lòng nhập họ tên.";
            } elseif (!preg_match('/^[a-zA-ZÀ-ỹĂăÂâĐđÊêÔôƠơƯư\s]+$/u', $old['HoTen'])) {
                $errors[] = "Họ tên chỉ được chứa chữ cái và khoảng trắng, không được có số và ký tự đặc biệt.";
            }
            if (!filter_var($old['Email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email không hợp lệ.";
            } elseif ($this->emailExists($old['Email'])) {
                $errors[] = "Email đã tồn tại trong hệ thống. Vui lòng nhập email khác.";
            }

            if (!preg_match('/^0\d{8,11}$/', $old['SoDienThoai'])) {
                $errors[] = "Số điện thoại không hợp lệ.";
            }
            // Bỏ kiểm tra số điện thoại tồn tại khi thêm người dùng

            if ($old['Username'] === '' || !$this->isValidUsername($old['Username'])) {
                $errors[] = "Tên đăng nhập chỉ gồm chữ cái và số (A-Z, a-z, 0-9), 5-20 ký tự.";
            } elseif ($tkModel->existsUsername($old['Username'])) {
                $errors[] = "Tên đăng nhập đã tồn tại.";
            }
            
            $cccd = trim($_POST['CCCD'] ?? '');
            $isEmployee = in_array($old['VaiTro'], [2, 3, 4, 5, 6], true);
            
            // CCCD chỉ bắt buộc nếu không phải nhân viên
            if (!$isEmployee) {
                if ($cccd === '') {
                    $errors[] = "Vui lòng nhập CCCD/CMND.";
                } elseif (!preg_match('/^[0-9]{9,12}$/', $cccd)) {
                    $errors[] = "CCCD/CMND không hợp lệ (9-12 chữ số).";
                } elseif ($this->cccdExists($cccd)) {
                    $errors[] = "CCCD/CMND đã tồn tại trong hệ thống.";
                }
            } elseif ($cccd !== '' && !preg_match('/^[0-9]{9,12}$/', $cccd)) {
                // Nếu nhân viên nhập CCCD thì vẫn phải đúng format
                $errors[] = "CCCD/CMND không hợp lệ (9-12 chữ số).";
            }

            if (!$this->isStrongPassword($password)) {
                $errors[] = "Mật khẩu quá yếu. Vui lòng nhập mật khẩu có ít nhất 1 chữ hoa và 1 ký tự đặc biệt (tối thiểu 6 ký tự).";
            }

            if (!in_array($old['TrangThai'], ['HoatDong','Khoa','Ngung'], true)) {
                $old['TrangThai'] = 'HoatDong';
            }

            if (empty($errors)) {
                $db = Database::getConnection();
                try {
                    $db->begin_transaction();

                    // Kiểm tra nếu là vai trò nhân viên (MaVaiTro từ 2-6)
                    $isEmployee = in_array($old['VaiTro'], [2, 3, 4, 5, 6], true);
                    
                    $maKH = null;
                    $maNV = null;
                    
                    if ($isEmployee) {
                        // Tạo bản ghi trong bảng nhanvien
                        $roleModel = new Role();
                        $role = $roleModel->getById($old['VaiTro']);
                        $chucVu = $role['TenVaiTro'] ?? '';
                        
                        $stmt = $db->prepare("INSERT INTO nhanvien (TenNV, SDT, Email, ChucVu, MaVaiTro) VALUES (?, ?, ?, ?, ?)");
                        $stmt->bind_param("ssssi", $old['HoTen'], $old['SoDienThoai'], $old['Email'], $chucVu, $old['VaiTro']);
                        $stmt->execute();
                        $maNV = $db->insert_id;
                    } else {
                        // Tạo bản ghi trong bảng khachhang cho khách hàng
                        $loaiKhach = $old['LoaiKhach'] ?? 'Cá nhân';
                        $stmt = $db->prepare("INSERT INTO khachhang (TenKH, SDT, Email, CCCD, DiaChi, LoaiKhach) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("ssssss", $old['HoTen'], $old['SoDienThoai'], $old['Email'], $cccd, $old['DiaChi'], $loaiKhach);
                    $stmt->execute();
                    $maKH = $db->insert_id;
                    }

                    // Tạo tài khoản
                    $maTK = $tkModel->create([
                        'Username'    => $old['Username'],
                        'Password'    => $password,
                        'MaVaiTro'    => $old['VaiTro'],
                        'MaKhachHang' => $maKH,
                        'MaNhanVien'  => $maNV,
                        'TrangThai'   => $old['TrangThai']
                    ]);

                    // Cập nhật MaTK vào khachhang nếu là khách hàng
                    if ($maKH) {
                    $stmt = $db->prepare("UPDATE khachhang SET MaTK = ? WHERE MaKhachHang = ?");
                    $stmt->bind_param("ii", $maTK, $maKH);
                    $stmt->execute();
                    }

                    $db->commit();
                    $success = "Thêm người dùng thành công.";
                    $old = [
                        'HoTen'       => '',
                        'Email'       => '',
                        'SoDienThoai' => '',
                        'CCCD'        => '',
                        'DiaChi'      => '',
                        'VaiTro'      => '',
                        'Username'    => '',
                        'TrangThai'   => 'HoatDong'
                    ];
                } catch (Exception $e) {
                    $db->rollback();
                    $errors[] = "Không thể lưu dữ liệu. Vui lòng thử lại.";
                    error_log($e->getMessage());
                }
            }
        }

        $this->view('admin/nguoidung_them', [
            'roles'   => $roleModel->getAll(),
            'errors'  => $errors,
            'success' => $success,
            'old'     => $old
        ]);
    }

    public function nguoiDungCapNhat()
    {
        $userModel = new User();
        $roleModel = new Role();
        $tkModel   = new TaiKhoan();

        $users   = $userModel->getAll();
        $errors  = [];
        $success = null;

        $selectedId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $selectedId = (int)($_POST['MaTK'] ?? 0);
            $payload = [
                'HoTen'       => trim($_POST['HoTen'] ?? ''),
                'Email'       => trim($_POST['Email'] ?? ''),
                'SoDienThoai' => trim($_POST['SoDienThoai'] ?? ''),
                'CCCD'        => trim($_POST['CCCD'] ?? ''),
                'DiaChi'      => trim($_POST['DiaChi'] ?? ''),
                'VaiTro'      => (int)($_POST['VaiTro'] ?? 7),
                'LoaiKhach'   => trim($_POST['LoaiKhach'] ?? 'Cá nhân'),
                'TrangThai'   => $_POST['TrangThai'] ?? 'HoatDong'
            ];
            $password = trim($_POST['Password'] ?? '');

            $current = $selectedId ? $userModel->getById($selectedId) : null;
            if (!$current) {
                $errors[] = "Không thể tải thông tin người dùng.";
            } else {
                $maKH = $current['MaKhachHang'] ?? null;
                $maNV = $current['MaNhanVien'] ?? null;
                $oldRole = (int)($current['MaVaiTro'] ?? 7);
                $newRole = $payload['VaiTro'];
                $isEmployeeOld = in_array($oldRole, [2, 3, 4, 5, 6], true);
                $isEmployeeNew = in_array($newRole, [2, 3, 4, 5, 6], true);
                $roleChanged = $oldRole !== $newRole;
                
                if ($payload['HoTen'] === '') {
                    $errors[] = "Vui lòng nhập họ tên.";
                } elseif (!preg_match('/^[a-zA-ZÀ-ỹĂăÂâĐđÊêÔôƠơƯư\s]+$/u', $payload['HoTen'])) {
                    $errors[] = "Họ tên chỉ được chứa chữ cái và khoảng trắng, không được có số và ký tự đặc biệt.";
                }
                
                // Chỉ kiểm tra trùng lặp nếu giá trị thay đổi
                if (!filter_var($payload['Email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Email không hợp lệ.";
                } elseif ($payload['Email'] !== ($current['Email'] ?? '')) {
                    if ($this->emailExists($payload['Email'], $maKH)) {
                        $errors[] = "Email đã tồn tại trong hệ thống. Vui lòng nhập email khác.";
                    }
                }
                if (!preg_match('/^0\d{8,11}$/', $payload['SoDienThoai'])) {
                    $errors[] = "Số điện thoại không hợp lệ.";
                } elseif ($payload['SoDienThoai'] !== ($current['SDT'] ?? '')) {
                    if ($this->phoneExists($payload['SoDienThoai'], $maKH)) {
                        $errors[] = "Số điện thoại đã tồn tại trong hệ thống. Vui lòng nhập số khác.";
                    }
                }
                
                // Chỉ validate CCCD nếu vai trò mới không phải nhân viên
                if (!$isEmployeeNew) {
                    if ($payload['CCCD'] === '') {
                        $errors[] = "Vui lòng nhập CCCD/CMND.";
                    } elseif (!preg_match('/^[0-9]{9,12}$/', $payload['CCCD'])) {
                        $errors[] = "CCCD/CMND không hợp lệ (9-12 chữ số).";
                    } elseif ($payload['CCCD'] !== ($current['CCCD'] ?? '')) {
                        if ($this->cccdExists($payload['CCCD'], $maKH)) {
                            $errors[] = "CCCD/CMND đã tồn tại trong hệ thống.";
                        }
                    }
                }
                
                if ($password !== '' && !$this->isStrongPassword($password)) {
                    $errors[] = "Mật khẩu quá yếu. Vui lòng nhập mật khẩu có ít nhất 1 chữ hoa và 1 ký tự đặc biệt (tối thiểu 6 ký tự).";
                }
                
                // Validate Username chỉ chứa chữ cái
                $username = trim($_POST['Username'] ?? '');
                if ($username === '' || !$this->isValidUsername($username)) {
                    $errors[] = "Tên đăng nhập chỉ gồm chữ cái (A-Z, a-z), 5-20 ký tự.";
                } elseif ($username !== $current['Username'] && $tkModel->existsUsername($username)) {
                    $errors[] = "Tên đăng nhập đã tồn tại.";
                }

                if (empty($errors)) {
                    $db = Database::getConnection();
                    try {
                        $db->begin_transaction();
                        
                        // Xử lý khi đổi vai trò
                        if ($roleChanged) {
                            // Nếu chuyển từ nhân viên sang khách hàng
                            if ($isEmployeeOld && !$isEmployeeNew) {
                                // Tạo bản ghi mới trong khachhang
                                $loaiKhach = $payload['LoaiKhach'] ?? 'Cá nhân';
                                $stmt = $db->prepare("INSERT INTO khachhang (TenKH, SDT, Email, CCCD, DiaChi, LoaiKhach) VALUES (?, ?, ?, ?, ?, ?)");
                                $stmt->bind_param("ssssss", $payload['HoTen'], $payload['SoDienThoai'], $payload['Email'], $payload['CCCD'], $payload['DiaChi'], $loaiKhach);
                                $stmt->execute();
                                $maKH = $db->insert_id;
                                
                                // Xóa bản ghi cũ trong nhanvien
                                if ($maNV) {
                                    $stmt = $db->prepare("DELETE FROM nhanvien WHERE MaNhanVien = ?");
                                    $stmt->bind_param("i", $maNV);
                                    $stmt->execute();
                                    $maNV = null;
                                }
                                
                                // Cập nhật MaKhachHang và MaNhanVien trong taikhoan
                                $stmt = $db->prepare("UPDATE taikhoan SET MaKhachHang = ?, MaNhanVien = NULL WHERE MaTK = ?");
                                $stmt->bind_param("ii", $maKH, $selectedId);
                                $stmt->execute();
                                
                                // Cập nhật MaTK vào khachhang
                                $stmt = $db->prepare("UPDATE khachhang SET MaTK = ? WHERE MaKhachHang = ?");
                                $stmt->bind_param("ii", $selectedId, $maKH);
                                $stmt->execute();
                            }
                            // Nếu chuyển từ khách hàng sang nhân viên
                            elseif (!$isEmployeeOld && $isEmployeeNew) {
                                // Tạo bản ghi mới trong nhanvien
                                $role = $roleModel->getById($newRole);
                                $chucVu = $role['TenVaiTro'] ?? '';
                                
                                $stmt = $db->prepare("INSERT INTO nhanvien (TenNV, SDT, Email, ChucVu, MaVaiTro) VALUES (?, ?, ?, ?, ?)");
                                $stmt->bind_param("ssssi", $payload['HoTen'], $payload['SoDienThoai'], $payload['Email'], $chucVu, $newRole);
                                $stmt->execute();
                                $maNV = $db->insert_id;
                                
                                // Xóa bản ghi cũ trong khachhang
                                if ($maKH) {
                                    $stmt = $db->prepare("DELETE FROM khachhang WHERE MaKhachHang = ?");
                                    $stmt->bind_param("i", $maKH);
                                    $stmt->execute();
                                    $maKH = null;
                                }
                                
                                // Cập nhật MaNhanVien và MaKhachHang trong taikhoan
                                $stmt = $db->prepare("UPDATE taikhoan SET MaNhanVien = ?, MaKhachHang = NULL WHERE MaTK = ?");
                                $stmt->bind_param("ii", $maNV, $selectedId);
                                $stmt->execute();
                            }
                            // Nếu chuyển giữa các vai trò nhân viên
                            elseif ($isEmployeeOld && $isEmployeeNew) {
                                // Cập nhật bảng nhanvien và MaVaiTro
                                $role = $roleModel->getById($newRole);
                                $chucVu = $role['TenVaiTro'] ?? '';
                                
                                $stmt = $db->prepare("UPDATE nhanvien SET TenNV = ?, Email = ?, SDT = ?, ChucVu = ?, MaVaiTro = ? WHERE MaNhanVien = ?");
                                $stmt->bind_param("ssssii", $payload['HoTen'], $payload['Email'], $payload['SoDienThoai'], $chucVu, $newRole, $maNV);
                                $stmt->execute();
                            }
                        } else {
                            // Không đổi vai trò, chỉ cập nhật thông tin
                            if ($isEmployeeNew) {
                                // Cập nhật bảng nhanvien
                                $stmt = $db->prepare("UPDATE nhanvien SET TenNV = ?, Email = ?, SDT = ? WHERE MaNhanVien = ?");
                                $stmt->bind_param("sssi", $payload['HoTen'], $payload['Email'], $payload['SoDienThoai'], $maNV);
                                $stmt->execute();
                            } else {
                                // Cập nhật bảng khachhang
                                $loaiKhach = $payload['LoaiKhach'] ?? 'Cá nhân';
                                $stmt = $db->prepare("UPDATE khachhang SET TenKH = ?, Email = ?, SDT = ?, CCCD = ?, DiaChi = ?, LoaiKhach = ? WHERE MaKhachHang = ?");
                                $stmt->bind_param("ssssssi", $payload['HoTen'], $payload['Email'], $payload['SoDienThoai'], $payload['CCCD'], $payload['DiaChi'], $loaiKhach, $maKH);
                                $stmt->execute();
                            }
                        }
                        
                        // Cập nhật Username nếu có thay đổi
                        if ($username !== $current['Username']) {
                            $stmt = $db->prepare("UPDATE taikhoan SET Username = ? WHERE MaTK = ?");
                            $stmt->bind_param("si", $username, $selectedId);
                            $stmt->execute();
                        }

                        $meta = [
                            'MaVaiTro'  => $payload['VaiTro'],
                            'TrangThai' => $payload['TrangThai']
                        ];
                        if ($password !== '') {
                            $meta['Password'] = $password;
                        }

                        $tkModel->updateMeta($selectedId, $meta);
                        $db->commit();
                        $success = "Cập nhật người dùng thành công.";
                    } catch (Exception $e) {
                        $db->rollback();
                        $errors[] = "Không thể lưu thông tin. Vui lòng thử lại.";
                        error_log($e->getMessage());
                    }
                }
            }
        }

        $selectedUser = $selectedId ? $userModel->getById($selectedId) : null;
        $this->view('admin/nguoidung_capnhat', [
            'users'        => $users,
            'roles'        => $roleModel->getAll(),
            'selectedUser' => $selectedUser,
            'errors'       => $errors,
            'success'      => $success
        ]);
    }

    public function nguoiDungXoa()
    {
        $userModel = new User();
        $tkModel   = new TaiKhoan();
        $khModel   = new KhachHang();

        $users   = $userModel->getAll();
        $errors  = [];
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ids = $_POST['chon'] ?? [];
            if (empty($ids)) {
                $errors[] = "Vui lòng chọn người dùng cần xóa.";
            } else {
                $deleted = [];
                foreach ($ids as $id) {
                    $id = (int)$id;
                    $user = $userModel->getById($id);
                    if (!$user) {
                        $errors[] = "Không thể tải thông tin người dùng #$id.";
                        continue;
                    }
                    $maKH = $user['MaKhachHang'] ?? null;
                    $maNV = $user['MaNhanVien'] ?? null;
                    $db = Database::getConnection();
                    try {
                        $db->begin_transaction();
                        
                        // Xóa tài khoản trước (vì taikhoan có foreign key đến khachhang và nhanvien)
                        $tkModel->deleteAccount($id);
                        
                        // Xóa khách hàng nếu có
                        if ($maKH) {
                            $khModel->delete($maKH);
                        }
                        
                        // Xóa nhân viên nếu có
                        if ($maNV) {
                            $stmt = $db->prepare("DELETE FROM nhanvien WHERE MaNhanVien = ?");
                            $stmt->bind_param("i", $maNV);
                            $stmt->execute();
                        }
                        
                        $db->commit();
                        $deleted[] = $user['Username'];
                    } catch (Exception $e) {
                        $db->rollback();
                        $errors[] = "Xóa người dùng {$user['Username']} thất bại: " . $e->getMessage();
                        error_log("Lỗi xóa người dùng: " . $e->getMessage());
                    }
                }
                if ($deleted) {
                    $success = "Xóa người dùng và tài khoản thành công: " . implode(', ', $deleted);
                }
                $users = $userModel->getAll();
            }
        }

        $this->view('admin/nguoidung_xoa', [
            'users'   => $users,
            'errors'  => $errors,
            'success' => $success
        ]);
    }

    public function saoLuuDuLieu()
    {
        $errors = [];
        $success = null;
        $backups = $this->listBackupFiles();
        $user = Auth::user();
        
        // Đọc cấu hình sao lưu tự động
        $autoBackupConfig = $this->getAutoBackupConfig();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Xử lý lưu cấu hình sao lưu tự động
            if (isset($_POST['save_auto_backup'])) {
                $autoBackup = $_POST['AutoBackup'] ?? 'manual';
                $this->saveAutoBackupConfig($autoBackup);
                $success = "Đã lưu cấu hình sao lưu tự động.";
                $autoBackupConfig = $this->getAutoBackupConfig();
            }
            // Xử lý sao lưu thủ công
            else {
            $tenFile = trim($_POST['ten_file'] ?? '');
            $hinhThuc = $_POST['hinh_thuc'] ?? 'toan_bo';
            $thuMuc = trim($_POST['thu_muc'] ?? '');
            $ngayChon = $_POST['ngay_chon'] ?? null;

                if (empty($tenFile)) {
                    $errors[] = "Vui lòng nhập tên file sao lưu.";
                } elseif (!preg_match('/^[A-Za-z0-9_\-]+$/', $tenFile)) {
                $errors[] = "Tên file không hợp lệ. Vui lòng chỉ dùng chữ, số, -, _.";
            }

            // Validate ngày chọn nếu hình thức là "chon_ngay"
            if ($hinhThuc === 'chon_ngay') {
                if (empty($ngayChon)) {
                    $errors[] = "Vui lòng chọn ngày cần sao lưu.";
                } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $ngayChon)) {
                    $errors[] = "Ngày không hợp lệ.";
                } elseif (strtotime($ngayChon) > strtotime(date('Y-m-d'))) {
                    $errors[] = "Không thể sao lưu dữ liệu của ngày tương lai.";
                }
            }

            $dir = $this->ensureBackupDir($thuMuc);
            if (!$dir) {
                    $errors[] = "Không thể tạo thư mục sao lưu.";
            } else {
                $free = @disk_free_space($dir);
                if ($free !== false && $free < 10 * 1024 * 1024) {
                        $errors[] = "Không đủ dung lượng để lưu file sao lưu (cần ít nhất 10MB).";
                }
            }

            if (empty($errors) && $dir) {
                    $fileName = $tenFile . '_' . date('Ymd_His') . '.sql';
                    $filePath = $dir . DIRECTORY_SEPARATOR . $fileName;
                    
                    try {
                        // Thực hiện export database
                        $sqlDump = $this->exportDatabase($hinhThuc, $ngayChon);
                        
                        if ($sqlDump === false) {
                            $errors[] = "Không thể export dữ liệu từ database.";
                        } else {
                            // Lưu metadata vào đầu file
                            $metadata = [
                                'version' => '1.0',
                                'type' => $hinhThuc,
                                'database' => DB_NAME,
                                'created_by' => $user['Username'] ?? 'admin',
                                'created_at' => date('Y-m-d H:i:s'),
                                'php_version' => PHP_VERSION,
                                'mysql_version' => $this->getMySQLVersion()
                            ];
                            
                            // Thêm thông tin ngày được chọn nếu có
                            if ($hinhThuc === 'chon_ngay' && $ngayChon) {
                                $metadata['backup_date'] = $ngayChon;
                            }
                            
                            $header = "-- ABC Resort Backup File\n";
                            $header .= "-- Created: " . $metadata['created_at'] . "\n";
                            $header .= "-- Database: " . $metadata['database'] . "\n";
                            $header .= "-- Type: " . $metadata['type'] . "\n";
                            if (isset($metadata['backup_date'])) {
                                $header .= "-- Backup Date: " . $metadata['backup_date'] . "\n";
                            }
                            $header .= "-- Created by: " . $metadata['created_by'] . "\n";
                            $header .= "-- \n";
                            
                            // Comment metadata JSON - mỗi dòng đều có -- ở đầu
                            $metadataJson = json_encode($metadata, JSON_PRETTY_PRINT);
                            $header .= "-- Metadata:\n";
                            $metadataLines = explode("\n", $metadataJson);
                            foreach ($metadataLines as $line) {
                                $header .= "-- " . $line . "\n";
                            }
                            $header .= "-- \n\n";
                            
                            $fullContent = $header . $sqlDump;
                            
                            if (@file_put_contents($filePath, $fullContent)) {
                                $success = "Sao lưu thành công! File: " . $fileName;
                    $backups = $this->listBackupFiles();
                } else {
                                $errors[] = "Không thể ghi file sao lưu. Kiểm tra quyền ghi file.";
                            }
                        }
                    } catch (Exception $e) {
                        $errors[] = "Lỗi khi sao lưu: " . $e->getMessage();
                        error_log("Backup error: " . $e->getMessage());
                    }
                }
            }
        }

        $this->view('admin/saoluu', [
            'user' => $user,
            'backups' => $backups,
            'autoBackupConfig' => $autoBackupConfig,
            'errors'  => $errors,
            'success' => $success
        ]);
    }

    public function phucHoiDuLieu()
    {
        $errors = [];
        $success = null;
        $backups = $this->listBackupFiles();
        $user = Auth::user();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $file = $_POST['backup_file'] ?? '';
            if ($file === '') {
                $errors[] = "Vui lòng chọn file sao lưu.";
            } else {
                $baseDir = $this->ensureBackupDir();
                // File có thể nằm trong thư mục con, nên cần xử lý đường dẫn tương đối
                $path = realpath($baseDir . DIRECTORY_SEPARATOR . $file);
                if (!$path || strpos($path, realpath($baseDir)) !== 0) {
                    $errors[] = "File sao lưu không hợp lệ hoặc không nằm trong thư mục backup.";
                } elseif (!file_exists($path) || filesize($path) <= 0) {
                    $errors[] = "File sao lưu bị lỗi hoặc không tồn tại.";
                } elseif (pathinfo($path, PATHINFO_EXTENSION) !== 'sql') {
                    $errors[] = "File không phải là file SQL backup hợp lệ.";
                }
            }

            if (empty($errors) && isset($path)) {
                try {
                    // Đọc và parse file backup
                    $content = @file_get_contents($path);
                    if ($content === false) {
                        $errors[] = "Không thể đọc file sao lưu.";
                } else {
                        // Loại bỏ metadata header nếu có
                        $sqlContent = $this->extractSQLFromBackup($content);
                        
                        if (empty($sqlContent)) {
                            $errors[] = "File sao lưu không chứa dữ liệu SQL hợp lệ.";
                        } else {
                            // Thực hiện restore database
                            $result = $this->importDatabase($sqlContent);
                            
                            if ($result['success']) {
                                $success = "Phục hồi dữ liệu thành công từ file " . basename($path) . ". " . $result['message'];
                            } else {
                                $errors[] = "Lỗi khi phục hồi: " . $result['message'];
                            }
                        }
                    }
                } catch (Exception $e) {
                    $errors[] = "Lỗi khi phục hồi: " . $e->getMessage();
                    error_log("Restore error: " . $e->getMessage());
                }
            }
        }

        $this->view('admin/phuchoi', [
            'user' => $user,
            'backups' => $backups,
            'errors'  => $errors,
            'success' => $success
        ]);
    }

    // === PHÂN QUYỀN ===
    public function phanQuyen()
    {
        $modelUser = new User();
        $modelRole = new Role();
        $success = isset($_GET['success']) ? "Phân quyền thành công." : '';
        $error   = isset($_GET['error']) ? "Không thể lưu quyền. Vui lòng thử lại." : '';

        // Luôn load danh sách users để hiển thị
        $search = trim($_GET['search'] ?? '');
        $users = $search !== '' ? $modelUser->search($search) : $modelUser->getAll();

        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $user = $modelUser->getById($id);

            return $this->view('admin/phanquyen', [
                'mode'    => 'edit',
                'user'    => $user,
                'users'   => $users, // Luôn truyền danh sách users
                'roles'   => $modelRole->getAll(),
                'success' => $success,
                'error'   => $error
            ]);
        }

        $this->view('admin/phanquyen', [
            'mode'    => 'list',
            'users'   => $users,
            'success' => $success,
            'error'   => $error
        ]);
    }

    public function luuPhanQuyen() {
        $id   = (int)($_POST['MaTK'] ?? 0);
        $role = (int)($_POST['MaVaiTro'] ?? 0);
        $mota = trim($_POST['MoTa'] ?? '');

        $userModel = new User();
        $user = $userModel->getById($id);
        
        if (!$user) {
            header("Location: index.php?controller=admin&action=phanQuyen&error=1");
            exit;
        }

        $db = Database::getConnection();
        try {
            $db->begin_transaction();
            
            $oldRole = (int)($user['MaVaiTro'] ?? 7);
            $isEmployeeOld = in_array($oldRole, [2, 3, 4, 5, 6], true);
            $isEmployeeNew = in_array($role, [2, 3, 4, 5, 6], true);
            $roleChanged = $oldRole !== $role;
            
            $maKH = $user['MaKhachHang'] ?? null;
            $maNV = $user['MaNhanVien'] ?? null;
            $roleModel = new Role();
            
            // Xử lý khi đổi vai trò
            if ($roleChanged) {
                // Nếu chuyển từ nhân viên sang khách hàng
                if ($isEmployeeOld && !$isEmployeeNew) {
                    // Lấy thông tin từ nhanvien
                    if ($maNV) {
                        $stmt = $db->prepare("SELECT TenNV, SDT, Email FROM nhanvien WHERE MaNhanVien = ?");
                        $stmt->bind_param("i", $maNV);
                        $stmt->execute();
                        $nvInfo = $stmt->get_result()->fetch_assoc();
                        
                        if ($nvInfo) {
                            // Tạo bản ghi mới trong khachhang (CCCD để trống, có thể cập nhật sau)
                            $stmt = $db->prepare("INSERT INTO khachhang (TenKH, SDT, Email, CCCD, LoaiKhach) VALUES (?, ?, ?, ?, 'KhachHang')");
                            $cccd = ''; // CCCD để trống khi chuyển từ nhân viên
                            $stmt->bind_param("ssss", $nvInfo['TenNV'], $nvInfo['SDT'], $nvInfo['Email'], $cccd);
                            $stmt->execute();
                            $maKH = $db->insert_id;
                            
                            // Xóa bản ghi cũ trong nhanvien
                            $stmt = $db->prepare("DELETE FROM nhanvien WHERE MaNhanVien = ?");
                            $stmt->bind_param("i", $maNV);
                            $stmt->execute();
                            $maNV = null;
                            
                            // Cập nhật MaKhachHang và MaNhanVien trong taikhoan
                            $stmt = $db->prepare("UPDATE taikhoan SET MaKhachHang = ?, MaNhanVien = NULL WHERE MaTK = ?");
                            $stmt->bind_param("ii", $maKH, $id);
                            $stmt->execute();
                            
                            // Cập nhật MaTK vào khachhang
                            $stmt = $db->prepare("UPDATE khachhang SET MaTK = ? WHERE MaKhachHang = ?");
                            $stmt->bind_param("ii", $id, $maKH);
                            $stmt->execute();
                        }
                    }
                }
                // Nếu chuyển từ khách hàng sang nhân viên
                elseif (!$isEmployeeOld && $isEmployeeNew) {
                    // Lấy thông tin từ khachhang
                    if ($maKH) {
                        $stmt = $db->prepare("SELECT TenKH, SDT, Email FROM khachhang WHERE MaKhachHang = ?");
                        $stmt->bind_param("i", $maKH);
                        $stmt->execute();
                        $khInfo = $stmt->get_result()->fetch_assoc();
                        
                        if ($khInfo) {
                            // Tạo bản ghi mới trong nhanvien
                            $roleInfo = $roleModel->getById($role);
                            $chucVu = $roleInfo['TenVaiTro'] ?? '';
                            
                            $stmt = $db->prepare("INSERT INTO nhanvien (TenNV, SDT, Email, ChucVu, MaVaiTro) VALUES (?, ?, ?, ?, ?)");
                            $stmt->bind_param("ssssi", $khInfo['TenKH'], $khInfo['SDT'], $khInfo['Email'], $chucVu, $role);
                            $stmt->execute();
                            $maNV = $db->insert_id;
                            
                            // Xóa bản ghi cũ trong khachhang
                            $stmt = $db->prepare("DELETE FROM khachhang WHERE MaKhachHang = ?");
                            $stmt->bind_param("i", $maKH);
                            $stmt->execute();
                            $maKH = null;
                            
                            // Cập nhật MaNhanVien và MaKhachHang trong taikhoan
                            $stmt = $db->prepare("UPDATE taikhoan SET MaNhanVien = ?, MaKhachHang = NULL WHERE MaTK = ?");
                            $stmt->bind_param("ii", $maNV, $id);
                            $stmt->execute();
                        }
                    }
                }
                // Nếu chuyển giữa các vai trò nhân viên
                elseif ($isEmployeeOld && $isEmployeeNew) {
                    // Cập nhật bảng nhanvien
                    if ($maNV) {
                        $roleInfo = $roleModel->getById($role);
                        $chucVu = $roleInfo['TenVaiTro'] ?? '';
                        
                        $stmt = $db->prepare("UPDATE nhanvien SET ChucVu = ?, MaVaiTro = ? WHERE MaNhanVien = ?");
                        $stmt->bind_param("sii", $chucVu, $role, $maNV);
                        $stmt->execute();
                    }
                }
            }
            
            // Cập nhật vai trò trong taikhoan
            $tkModel = new TaiKhoan();
            $tkModel->updateMeta($id, [
                'MaVaiTro' => $role,
                'MoTaQuyen' => $mota
            ]);
            
            $db->commit();
            header("Location: index.php?controller=admin&action=phanQuyen&id=$id&success=1");
        } catch (Exception $e) {
            $db->rollback();
            error_log("Lỗi phân quyền: " . $e->getMessage());
            header("Location: index.php?controller=admin&action=phanQuyen&id=$id&error=1");
        }
        exit;
    }

    // === HELPER FUNCTIONS ===
    private function emailExists(string $email, ?int $exclude = null): bool
    {
        if ($email === '') return false;
        $sql = "SELECT 1 FROM khachhang WHERE Email = ?";
        $types = "s";
        $params = [$email];
        if ($exclude) {
            $sql .= " AND MaKhachHang <> ?";
            $types .= "i";
            $params[] = $exclude;
        }
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    private function phoneExists(string $phone, ?int $exclude = null): bool
    {
        if ($phone === '') return false;
        $sql = "SELECT 1 FROM khachhang WHERE SDT = ?";
        $types = "s";
        $params = [$phone];
        if ($exclude) {
            $sql .= " AND MaKhachHang <> ?";
            $types .= "i";
            $params[] = $exclude;
        }
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    private function cccdExists(string $cccd, ?int $exclude = null): bool
    {
        if ($cccd === '') return false;
        $sql = "SELECT 1 FROM khachhang WHERE CCCD = ?";
        $types = "s";
        $params = [$cccd];
        if ($exclude) {
            $sql .= " AND MaKhachHang <> ?";
            $types .= "i";
            $params[] = $exclude;
        }
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    private function isValidUsername(string $username): bool
    {
        // Cho phép chữ cái và số, 5-20 ký tự
        return (bool)preg_match('/^[a-zA-Z0-9]{5,20}$/', $username);
    }

    private function isStrongPassword(string $password): bool
    {
        // Bỏ yêu cầu số, chỉ cần chữ hoa và ký tự đặc biệt, tối thiểu 6 ký tự
        return (bool)preg_match('/^(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{6,}$/', $password);
    }

    private function hasPendingTransactions(?int $maKH): bool
    {
        if (!$maKH) return false;
        $sql = "
            SELECT 1 FROM giaodich
            WHERE MaKhachHang = ?
              AND TrangThai IN ('Moi','Booked','Stayed')
            LIMIT 1
        ";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bind_param("i", $maKH);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    // === BACKUP & RESTORE HELPER FUNCTIONS ===
    
    /**
     * Đảm bảo thư mục backup tồn tại
     */
    private function ensureBackupDir(?string $subDir = null): ?string
    {
        $baseDir = __DIR__ . '/../storage';
        if (!is_dir($baseDir)) {
            @mkdir($baseDir, 0755, true);
        }
        
        $baseDir .= '/backups';
        if ($subDir) {
            $baseDir .= DIRECTORY_SEPARATOR . preg_replace('/[^A-Za-z0-9_\-]/', '', $subDir);
        }
        
        if (!is_dir($baseDir)) {
            if (!@mkdir($baseDir, 0755, true)) {
            return null;
        }
        }
        
        return realpath($baseDir) ?: $baseDir;
    }

    /**
     * Liệt kê các file backup (đệ quy trong tất cả thư mục con)
     */
    private function listBackupFiles(): array
    {
        $baseDir = $this->ensureBackupDir();
        if (!$baseDir || !is_dir($baseDir)) {
            return [];
        }
        
        $files = [];
        
        // Hàm đệ quy để quét tất cả thư mục
        $scanDirectory = function($dir, $relativePath = '') use (&$scanDirectory, &$files) {
            $items = @scandir($dir);
            if ($items === false) {
                return;
            }
            
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                
                $path = $dir . DIRECTORY_SEPARATOR . $item;
                $relativeItemPath = $relativePath ? $relativePath . DIRECTORY_SEPARATOR . $item : $item;
                
                if (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'sql') {
                    // Đọc thời gian tạo từ metadata trong file
                    $createdAt = $this->getBackupCreatedTime($path);
                    
                    $files[] = [
                        'name' => $relativeItemPath, // Giữ nguyên đường dẫn tương đối nếu có thư mục con
                        'size' => filesize($path),
                        'modified' => $createdAt ? strtotime($createdAt) : filemtime($path),
                        'created_at' => $createdAt,
                        'full_path' => $path
                    ];
                } elseif (is_dir($path)) {
                    // Đệ quy vào thư mục con
                    $scanDirectory($path, $relativeItemPath);
                }
            }
        };
        
        $scanDirectory($baseDir);
        
        // Sắp xếp theo thời gian tạo (mới nhất trước)
        usort($files, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });
        
        return $files;
    }
    
    /**
     * Đọc thời gian tạo từ metadata trong file backup
     */
    private function getBackupCreatedTime(string $filePath): ?string
    {
        $handle = @fopen($filePath, 'r');
        if (!$handle) {
            return null;
        }
        
        // Đọc 20 dòng đầu để tìm metadata
        $lineCount = 0;
        $createdAt = null;
        
        while (!feof($handle) && $lineCount < 20) {
            $line = fgets($handle);
            if ($line === false) break;
            
            $lineCount++;
            
            // Tìm dòng "-- Created: YYYY-MM-DD HH:MM:SS"
            if (preg_match('/^--\s+Created:\s+(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2})/', $line, $matches)) {
                $createdAt = $matches[1];
                break;
            }
            
            // Hoặc tìm trong JSON metadata
            if (preg_match('/"created_at"\s*:\s*"([^"]+)"/', $line, $matches)) {
                $createdAt = $matches[1];
                break;
            }
        }
        
        fclose($handle);
        return $createdAt;
    }

    /**
     * Export database thành SQL dump
     * @param string $type Loại backup: 'toan_bo', 'hom_nay', 'chon_ngay'
     * @param string|null $ngayChon Ngày cần backup (format: Y-m-d), chỉ dùng khi $type = 'chon_ngay'
     */
    private function exportDatabase(string $type = 'toan_bo', ?string $ngayChon = null): string|false
    {
        $db = Database::getConnection();
        $output = '';
        
        try {
            // Lấy danh sách tất cả các bảng
            $tables = [];
            $result = $db->query("SHOW TABLES");
            while ($row = $result->fetch_array()) {
                $tables[] = $row[0];
            }
            
            if (empty($tables)) {
                return false;
            }
            
                            $output .= "-- Database Export\n";
            $output .= "-- Database: " . DB_NAME . "\n";
            $output .= "-- Date: " . date('Y-m-d H:i:s') . "\n";
            if ($type === 'chon_ngay' && $ngayChon) {
                $output .= "-- Backup Date: " . $ngayChon . "\n";
            }
            $output .= "\n";
            $output .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $output .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
            $output .= "SET AUTOCOMMIT = 0;\n";
            $output .= "START TRANSACTION;\n\n";
            
            foreach ($tables as $table) {
                // Export cấu trúc bảng
                $output .= "-- Table structure for `{$table}`\n";
                $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $createResult = $db->query("SHOW CREATE TABLE `{$table}`");
                if ($createRow = $createResult->fetch_assoc()) {
                    $output .= $createRow['Create Table'] . ";\n\n";
                }
                
                // Export dữ liệu
                if ($type === 'toan_bo' || $type === 'hom_nay' || $type === 'chon_ngay') {
                    $whereClause = '';
                    
                    if ($type === 'hom_nay' || $type === 'chon_ngay') {
                        // Xác định ngày cần filter
                        $targetDate = ($type === 'chon_ngay' && $ngayChon) ? $ngayChon : date('Y-m-d');
                        
                        // Export tất cả các bảng, nhưng filter theo ngày nếu bảng có cột ngày
                        $dateColumns = $this->getDateColumns($table);
                        if (!empty($dateColumns)) {
                            // Tìm cột ngày phù hợp nhất (ưu tiên: NgayTao, ThoiGian, Ngay, NgayGio)
                            $preferredColumns = ['NgayTao', 'ThoiGian', 'Ngay', 'NgayGio', 'NgayChi', 'NgayDat'];
                            $dateCol = null;
                            foreach ($preferredColumns as $pref) {
                                if (in_array($pref, $dateColumns)) {
                                    $dateCol = $pref;
                                    break;
                                }
                            }
                            // Nếu không tìm thấy cột ưu tiên, dùng cột đầu tiên
                            if (!$dateCol) {
                                $dateCol = $dateColumns[0];
                            }
                            // Escape ngày để tránh SQL injection
                            $targetDateEscaped = $db->real_escape_string($targetDate);
                            $whereClause = " WHERE DATE({$dateCol}) = '{$targetDateEscaped}'";
                        } else {
                            // Bảng không có cột ngày: export toàn bộ (vì không thể filter theo ngày)
                            $whereClause = ''; // Export toàn bộ cho bảng không có cột ngày
                        }
                    }
                    
                    // Export tất cả các bảng (không bỏ qua bất kỳ bảng nào)
                    $output .= "-- Dumping data for table `{$table}`\n";
                    $dataResult = $db->query("SELECT * FROM `{$table}`" . $whereClause);
                    
                    if ($dataResult && $dataResult->num_rows > 0) {
                        // Lấy tên các cột từ kết quả đầu tiên
                        $firstRow = $dataResult->fetch_assoc();
                        if ($firstRow) {
                            $columns = array_keys($firstRow);
                            $columnNames = '`' . implode('`,`', $columns) . '`';
                            
                            $output .= "INSERT INTO `{$table}` ({$columnNames}) VALUES\n";
                            $rows = [];
                            
                            // Xử lý dòng đầu tiên
                            $values = [];
                            foreach ($firstRow as $value) {
                                if ($value === null) {
                                    $values[] = 'NULL';
                                } elseif (is_numeric($value)) {
                                    $values[] = $value;
                                } else {
                                    $values[] = "'" . $db->real_escape_string($value) . "'";
                                }
                            }
                            $rows[] = "(" . implode(',', $values) . ")";
                            
                            // Xử lý các dòng còn lại
                            while ($row = $dataResult->fetch_assoc()) {
                                $values = [];
                                foreach ($row as $value) {
                                    if ($value === null) {
                                        $values[] = 'NULL';
                                    } elseif (is_numeric($value)) {
                                        $values[] = $value;
                                    } else {
                                        $values[] = "'" . $db->real_escape_string($value) . "'";
                                    }
                                }
                                $rows[] = "(" . implode(',', $values) . ")";
                            }
                            $output .= implode(",\n", $rows) . ";\n\n";
                        }
                    } else {
                        // Bảng không có dữ liệu thỏa điều kiện
                        $output .= "-- No data found for table `{$table}`\n\n";
                    }
                }
            }
            
            $output .= "COMMIT;\n";
            $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
            
            return $output;
        } catch (Exception $e) {
            error_log("Export error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy các cột ngày tháng trong bảng
     */
    private function getDateColumns(string $table): array
    {
        $db = Database::getConnection();
        $columns = [];
        try {
            $result = $db->query("SHOW COLUMNS FROM `{$table}`");
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    if (stripos($row['Type'], 'date') !== false || 
                        stripos($row['Type'], 'datetime') !== false ||
                        stripos($row['Type'], 'timestamp') !== false) {
                        $columns[] = $row['Field'];
                    }
                }
            }
        } catch (Exception $e) {
            // Ignore errors
        }
        return $columns;
    }

    /**
     * Kiểm tra xem bảng có phải là bảng cấu hình/tham chiếu không
     * Các bảng này luôn được export toàn bộ ngay cả khi chọn "Ngày hôm nay"
     */
    private function isReferenceTable(string $table): bool
    {
        $referenceTables = [
            'vaitro',      // Vai trò
            'phong',       // Phòng
            'loaiphong',   // Loại phòng
            'dichvu',      // Dịch vụ
            'loaidichvu',  // Loại dịch vụ
            'taikhoan',    // Tài khoản (cần để restore đúng)
            'khachhang',   // Khách hàng (thông tin cơ bản)
            'nhanvien',    // Nhân viên (thông tin cơ bản)
        ];
        return in_array(strtolower($table), $referenceTables);
    }

    /**
     * Trích xuất SQL từ file backup (loại bỏ metadata)
     */
    private function extractSQLFromBackup(string $content): string
    {
        // Tìm vị trí bắt đầu SQL (sau dòng "-- \n\n")
        $pos = strpos($content, "-- \n\n");
        if ($pos !== false) {
            return substr($content, $pos + 4);
        }
        
        // Nếu không có metadata, trả về toàn bộ
        return $content;
    }

    /**
     * Import database từ SQL dump
     */
    private function importDatabase(string $sql): array
    {
        $db = Database::getConnection();
        
        try {
            // Tắt foreign key checks
            $db->query("SET FOREIGN_KEY_CHECKS=0");
            $db->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'");
            $db->autocommit(false);
            
            // Chia SQL thành các câu lệnh
            $statements = $this->splitSQL($sql);
            $executed = 0;
            $errors = [];
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (empty($statement) || substr($statement, 0, 2) === '--') {
                    continue;
                }
                
                if (!$db->query($statement)) {
                    $errors[] = $db->error;
                    error_log("SQL Error: " . $db->error . " | Statement: " . substr($statement, 0, 100));
                } else {
                    $executed++;
                }
            }
            
            if (empty($errors)) {
                $db->commit();
                $db->query("SET FOREIGN_KEY_CHECKS=1");
                return [
                    'success' => true,
                    'message' => "Đã thực thi {$executed} câu lệnh SQL thành công."
                ];
            } else {
                $db->rollback();
                $db->query("SET FOREIGN_KEY_CHECKS=1");
                return [
                    'success' => false,
                    'message' => "Có lỗi xảy ra: " . implode('; ', array_slice($errors, 0, 3))
                ];
            }
        } catch (Exception $e) {
            $db->rollback();
            $db->query("SET FOREIGN_KEY_CHECKS=1");
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } finally {
            $db->autocommit(true);
        }
    }

    /**
     * Chia SQL thành các câu lệnh riêng biệt
     */
    private function splitSQL(string $sql): array
    {
        // Loại bỏ comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        
        // Chia theo dấu ; nhưng giữ nguyên trong string
        $statements = [];
        $current = '';
        $inString = false;
        $stringChar = '';
        
        for ($i = 0; $i < strlen($sql); $i++) {
            $char = $sql[$i];
            $current .= $char;
            
            if (($char === '"' || $char === "'") && ($i === 0 || $sql[$i-1] !== '\\')) {
                if (!$inString) {
                    $inString = true;
                    $stringChar = $char;
                } elseif ($char === $stringChar) {
                    $inString = false;
                }
            }
            
            if (!$inString && $char === ';') {
                $statements[] = trim($current);
                $current = '';
            }
        }
        
        if (!empty(trim($current))) {
            $statements[] = trim($current);
        }
        
        return array_filter($statements, function($stmt) {
            return !empty(trim($stmt));
        });
    }

    /**
     * Lấy phiên bản MySQL
     */
    private function getMySQLVersion(): string
    {
        try {
            $result = Database::getConnection()->query("SELECT VERSION() as version");
            if ($row = $result->fetch_assoc()) {
                return $row['version'];
            }
        } catch (Exception $e) {
            // Ignore
        }
        return 'Unknown';
    }

    /**
     * Lấy cấu hình sao lưu tự động
     */
    private function getAutoBackupConfig(): array
    {
        $configFile = __DIR__ . '/../storage/backup_config.json';
        if (file_exists($configFile)) {
            $content = @file_get_contents($configFile);
            if ($content) {
                $config = json_decode($content, true);
                if ($config) {
                    return $config;
                }
            }
        }
        // Mặc định: không tự động
        return [
            'enabled' => false,
            'mode' => 'manual',
            'last_backup' => null
        ];
    }

    /**
     * Lưu cấu hình sao lưu tự động
     */
    private function saveAutoBackupConfig(string $mode): bool
    {
        $configFile = __DIR__ . '/../storage/backup_config.json';
        $configDir = dirname($configFile);
        
        if (!is_dir($configDir)) {
            @mkdir($configDir, 0755, true);
        }
        
        $config = [
            'enabled' => $mode === 'auto',
            'mode' => $mode,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::user()['Username'] ?? 'admin'
        ];
        
        return @file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT)) !== false;
    }

    /**
     * Thực hiện sao lưu tự động (gọi từ logout hoặc cron)
     * Public method để có thể gọi từ controller khác
     */
    public function autoBackup(): bool
    {
        $config = $this->getAutoBackupConfig();
        
        if (!$config['enabled']) {
            return false;
        }
        
        try {
            $dir = $this->ensureBackupDir();
            if (!$dir) {
                return false;
            }
            
            $fileName = 'auto_backup_' . date('Ymd_His') . '.sql';
            $filePath = $dir . DIRECTORY_SEPARATOR . $fileName;
            
            $sqlDump = $this->exportDatabase('toan_bo');
            if ($sqlDump === false) {
                return false;
            }
            
            $user = Auth::user();
            $metadata = [
                'version' => '1.0',
                'type' => 'toan_bo',
                'mode' => 'auto',
                'database' => DB_NAME,
                'created_by' => $user['Username'] ?? 'system',
                'created_at' => date('Y-m-d H:i:s'),
                'php_version' => PHP_VERSION,
                'mysql_version' => $this->getMySQLVersion()
            ];
            
            $header = "-- ABC Resort Auto Backup File\n";
            $header .= "-- Created: " . $metadata['created_at'] . "\n";
            $header .= "-- Database: " . $metadata['database'] . "\n";
            $header .= "-- Type: Auto Backup\n";
            $header .= "-- Created by: " . $metadata['created_by'] . "\n";
            $header .= "-- \n";
            
            // Comment metadata JSON - mỗi dòng đều có -- ở đầu
            $metadataJson = json_encode($metadata, JSON_PRETTY_PRINT);
            $header .= "-- Metadata:\n";
            $metadataLines = explode("\n", $metadataJson);
            foreach ($metadataLines as $line) {
                $header .= "-- " . $line . "\n";
            }
            $header .= "-- \n\n";
            
            $fullContent = $header . $sqlDump;
            
            if (@file_put_contents($filePath, $fullContent)) {
                // Cập nhật last_backup
                $config['last_backup'] = date('Y-m-d H:i:s');
                $configFile = __DIR__ . '/../storage/backup_config.json';
                @file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
                return true;
            }
        } catch (Exception $e) {
            error_log("Auto backup error: " . $e->getMessage());
        }
        
        return false;
    }


    /**
     * Xử lý upload hình ảnh
     * @param array $file - $_FILES['name']
     * @param string $folder - Thư mục con (phong, khachhang, ...)
     * @return string|array - Tên file hoặc array ['error' => message]
     */
    private function handleImageUpload(array $file, string $folder = 'phong')
    {
        // Kiểm tra lỗi upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors = [
                UPLOAD_ERR_INI_SIZE => "File quá lớn (vượt giới hạn max_upload_size).",
                UPLOAD_ERR_FORM_SIZE => "File quá lớn (vượt giới hạn form).",
                UPLOAD_ERR_PARTIAL => "File upload không hoàn toàn.",
                UPLOAD_ERR_NO_FILE => "Không có file được chọn.",
                UPLOAD_ERR_NO_TMP_DIR => "Thư mục tạm thời không tồn tại.",
                UPLOAD_ERR_CANT_WRITE => "Không thể ghi file.",
            ];
            return ['error' => $errors[$file['error']] ?? "Lỗi upload không xác định."];
        }

        // Kiểm tra kích thước file (max 5MB)
        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            return ['error' => "File quá lớn. Tối đa 5MB."];
        }

        // Kiểm tra MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);

        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mimeType, $allowedMimes, true)) {
            return ['error' => "Chỉ hỗ trợ JPG, PNG, GIF, WebP."];
        }

        // Tạo thư mục nếu chưa tồn tại
        $uploadDir = __DIR__ . '/../uploads/' . $folder;
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                return ['error' => "Không thể tạo thư mục upload."];
            }
        }

        // Tạo tên file duy nhất
        $ext = match($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'jpg'
        };

        $fileName = $folder . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $filePath = $uploadDir . '/' . $fileName;

        // Lưu file
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return ['error' => "Không thể lưu file."];
        }

        return $fileName;
    }
}