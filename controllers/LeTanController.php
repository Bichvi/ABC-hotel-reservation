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
    $createdAccounts = [];

    // Dữ liệu form
    $form = [
        'leader_name'   => $_POST['leader_name']   ?? '',
        'leader_cccd'   => $_POST['leader_cccd']   ?? '',
        'leader_sdt'    => $_POST['leader_sdt']    ?? '',
        'leader_email'  => $_POST['leader_email']  ?? '',
        'leader_diachi' => $_POST['leader_diachi'] ?? '',
        'so_nguoi'      => $_POST['so_nguoi']      ?? '1',
        'members'       => $_POST['members']       ?? [],
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (isset($_POST['btn_cancel'])) {
            header("Location: index.php?controller=letan&action=index");
            exit;
        }

        // ========================
        // 1. VALIDATE TRƯỞNG ĐOÀN
        // ========================
        $leaderName   = trim($form['leader_name']);
        $leaderCCCD   = trim($form['leader_cccd']);
        $leaderSdt    = trim($form['leader_sdt']);
        $leaderEmail  = trim($form['leader_email']);
        $leaderDiaChi = trim($form['leader_diachi']);
        $soNguoi      = (int)$form['so_nguoi'];

        if ($leaderName === '') {
            $errors[] = "Vui lòng nhập họ tên trưởng đoàn.";
        }

        if ($leaderCCCD === '') {
            $errors[] = "Vui lòng nhập CCCD trưởng đoàn.";
        } elseif (!preg_match('/^\d{9,12}$/', $leaderCCCD)) {
            $errors[] = "CCCD trưởng đoàn sai định dạng.";
        } else {
            $existLeader = $khModel->searchByCCCDExact($leaderCCCD);
            if (!empty($existLeader)) {
                $errors[] = "CCCD trưởng đoàn đã tồn tại trong CSDL.";
            }
        }

        if ($leaderSdt === '' || !preg_match('/^0\d{8,10}$/', $leaderSdt)) {
            $errors[] = "Số điện thoại trưởng đoàn sai định dạng.";
        }

        if ($leaderEmail === '' || !filter_var($leaderEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email trưởng đoàn sai định dạng.";
        }

        if ($soNguoi <= 0) {
            $errors[] = "Số lượng thành viên phải lớn hơn 0.";
        }

        // =========================
        // 2. VALIDATE THÀNH VIÊN
        // =========================
        $membersRaw  = $form['members'];
        $members     = [];
        $countFilled = 0;

        for ($i = 1; $i <= $soNguoi; $i++) {

            $row = $membersRaw[$i] ?? [];

            $name   = trim($row['TenKH']  ?? '');
            $cccd   = trim($row['CCCD']   ?? '');
            $sdt    = trim($row['SDT']    ?? '');
            $email  = trim($row['Email']  ?? '');
            $diaChi = trim($row['DiaChi'] ?? '');

            if ($name === '' && $cccd === '' && $sdt === '' && $email === '' && $diaChi === '') {
                continue;
            }

            if ($name === '') {
                $errors[] = "Họ tên thành viên hàng {$i} không được để trống.";
            }

            if ($cccd !== '') {
                if (!preg_match('/^\d{9,12}$/', $cccd)) {
                    $errors[] = "CCCD thành viên hàng {$i} sai định dạng.";
                } else {
                    $existTV = $khModel->searchByCCCDExact($cccd);
                    if (!empty($existTV)) {
                        $errors[] = "CCCD thành viên hàng {$i} đã tồn tại trong CSDL.";
                    }
                }
            }

            if ($sdt !== '' && !preg_match('/^0\d{8,10}$/', $sdt)) {
                $errors[] = "SĐT thành viên hàng {$i} sai định dạng.";
            }

            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email thành viên hàng {$i} sai định dạng.";
            }

            $members[] = [
                'ho_ten' => $name,
                'cccd'   => $cccd,
                'sdt'    => $sdt,
                'email'  => $email,
                'diachi' => $diaChi,
            ];

            if ($name !== '') {
                $countFilled++;
            }
        }

        if ($countFilled !== $soNguoi) {
            $errors[] = "Số lượng thành viên không khớp ({$countFilled}/{$soNguoi}).";
        }

        // =========================
        // 3. CHECK TRÙNG CCCD TRONG FORM
        // =========================
        $cccdList = [$leaderCCCD];

        foreach ($members as $m) {
            if ($m['cccd'] !== '') {
                $cccdList[] = $m['cccd'];
            }
        }

        $dup = array_filter(array_count_values($cccdList), fn($c) => $c > 1);
        if (!empty($dup)) {
            $errors[] = "CCCD bị trùng giữa trưởng đoàn và thành viên.";
        }

        // =========================
        // 4. CÓ LỖI → STOP
        // =========================
        if (!empty($errors)) {
            return $this->view("letan/dangky_taikhoan_doan", [
                'errors'          => $errors,
                'success'         => null,
                'createdAccounts' => [],
                'form'            => $form,
            ]);
        }

        // =========================
        // 5. LƯU DB
        // =========================
        $db = Database::getConnection();
        $db->begin_transaction();

        try {

            // === 5.1 LƯU TRƯỞNG ĐOÀN ===
            $maTruongDoan = $khModel->create([
                'TenKH'     => $leaderName,
                'SDT'       => $leaderSdt,
                'Email'     => $leaderEmail,
                'CCCD'      => $leaderCCCD,
                'DiaChi'    => $leaderDiaChi,
                'LoaiKhach' => 'Trưởng đoàn',
            ]);

            if (!$maTruongDoan) throw new Exception("Không thể tạo trưởng đoàn.");

            // === 5.2 TẠO ĐOÀN ===
            $tenDoan = "Đoàn " . $leaderName;
            $maDoan  = $doanModel->create([
                'TenDoan'      => $tenDoan,
                'MaTruongDoan' => $maTruongDoan,
                'SoNguoi'      => $soNguoi + 1,
            ]);

            if (!$maDoan) throw new Exception("Không thể tạo đoàn.");

            // === TẠO USERNAME PREFIX ===
            $prefix = "D" . str_pad($maDoan, 3, "0", STR_PAD_LEFT);

            // === 5.3 TẠO TÀI KHOẢN TRƯỞNG ĐOÀN ===
            $leaderUsername = "{$prefix}_Leader";
            $leaderPass     = $tkModel->v2_randomPassword();

            $tkModel->v2_createCustomerAccount([
                'MaKhachHang' => $maTruongDoan,
                'Username'    => $leaderUsername,
                'Password'    => $leaderPass,
                'MaVaiTro'    => 7,
            ]);

            $createdAccounts[] = [
                'hoTen'    => $leaderName,
                'cmnd'     => $leaderCCCD,
                'username' => $leaderUsername,
                'password' => $leaderPass,
                'vaiTro'   => "Trưởng đoàn",
            ];

            // === 5.4 LƯU TỪNG THÀNH VIÊN + TẠO TK ===
            $index = 1;

            foreach ($members as $m) {

                $maKH = $khModel->create([
                    'TenKH'     => $m['ho_ten'],
                    'SDT'       => $m['sdt'],
                    'Email'     => $m['email'],
                    'CCCD'      => $m['cccd'],
                    'DiaChi'    => $m['diachi'],
                    'LoaiKhach' => 'Thành viên',
                ]);

                if (!$maKH) {
                    throw new Exception("Không thể tạo thành viên: " . $m['ho_ten']);
                }

                // Tạo username thành viên
                $username = "{$prefix}_M{$index}";
                $plainPwd = $tkModel->v2_randomPassword();

                $tkModel->v2_createCustomerAccount([
                    'MaKhachHang' => $maKH,
                    'Username'    => $username,
                    'Password'    => $plainPwd,
                    'MaVaiTro'    => 7,
                ]);

                $createdAccounts[] = [
                    'hoTen'    => $m['ho_ten'],
                    'cmnd'     => $m['cccd'],
                    'username' => $username,
                    'password' => $plainPwd,
                    'vaiTro'   => "Thành viên {$index}",
                ];

                $index++;
            }

            $db->commit();

            // RESET FORM SAU KHI TẠO THÀNH CÔNG
            $form = [
                'leader_name'   => '',
                'leader_cccd'   => '',
                'leader_sdt'    => '',
                'leader_email'  => '',
                'leader_diachi' => '',
                'so_nguoi'      => 1,
                'members'       => [],
            ];

            $success = "Đăng ký đoàn thành công (Mã đoàn: {$maDoan}).";

        } catch (Exception $e) {
            $db->rollback();
            $errors[] = "Lỗi hệ thống: " . $e->getMessage();
        }
    }

    // RENDER VIEW
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
public function suaThongTinDatPhong1()
{
    $this->requireRole(['LeTan']);

    $gdModel    = new GiaoDich();
    $ctgdModel  = new ChiTietGiaoDich();
    $phongModel = new Phong();
    $ctdvModel  = new ChiTietDichVu();
    $kmModel    = new KhuyenMai();

    $db = Database::getConnection();

    $errors  = [];
    $success = null;

    $thanhVien = [];
    $giaoDich       = null;
    $chiTietPhong   = [];
    $chiTietDichVu  = [];
    $dsPhong        = [];
    $dsKhuyenMai    = $kmModel->getAllActive();
    $khuyenMai      = null;

    $searchKeyword = "";

    // ====== Dữ liệu tính tiền để show ra view (giống UC đặt phòng)
    $donGiaHienTai   = 0;     // đơn giá phòng đang chọn (nếu nhiều phòng thì lấy phòng đang thao tác)
    $tongPhong       = 0;     // tổng tiền phòng
    $tongDV          = 0;     // tổng tiền dịch vụ
    $tongTruocGiam   = 0;
    $tienGiam        = 0;
    $thanhTien       = 0;

    $form = [
        'ten_kh'   => '',
        'cccd'     => '',
        'sdt'      => '',
        'email'    => '',
        'ngay_den' => '',
        'ngay_di'  => '',
        'so_nguoi' => 1,
        'ma_phong' => 0,

        // ⚠️ UC này KHÔNG cho sửa KM. Field này chỉ để giữ/hiển thị.
        'ma_khuyen_mai' => null
    ];

    /**
     * =========================================================
     * Helper: tính số đêm tối thiểu 1
     * =========================================================
     */
    $calcSoDem = function (string $ngayNhan, string $ngayTra): int {
        $t1 = strtotime(substr($ngayNhan, 0, 10));
        $t2 = strtotime(substr($ngayTra, 0, 10));
        if (!$t1 || !$t2) return 1;
        $dem = (int)ceil(($t2 - $t1) / 86400);
        return max(1, $dem);
    };

    /**
     * =========================================================
     * Helper: tính tiền phòng cho giao dịch (KHÔNG dùng loaiphong)
     * Ưu tiên: ctgd.DonGia nếu có > 0, fallback: phong.Gia
     * =========================================================
     */
    $tinhTongTienPhong_UC = function (int $maGD) use ($db, $calcSoDem): float {
        $sql = "
            SELECT
                ct.MaPhong,
                ct.NgayNhanDuKien,
                ct.NgayTraDuKien,
                ct.DonGia,
                p.Gia
            FROM chitietgiaodich ct
            INNER JOIN phong p ON p.MaPhong = ct.MaPhong
            WHERE ct.MaGiaoDich = ?
              AND ct.TrangThai IN ('Booked','CheckedIn','Stayed','Moi')
        ";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $maGD);
        $stmt->execute();
        $rs = $stmt->get_result();

        $tong = 0.0;
        while ($row = $rs->fetch_assoc()) {
            $soDem  = $calcSoDem($row['NgayNhanDuKien'], $row['NgayTraDuKien']);
            $donGia = 0;

            if (isset($row['DonGia']) && (float)$row['DonGia'] > 0) {
                $donGia = (float)$row['DonGia'];
            } else {
                $donGia = (float)($row['Gia'] ?? 0);
            }

            $tong += $soDem * $donGia;
        }
        return (float)$tong;
    };

    /**
     * =========================================================
     * Helper: tính tiền DV cho giao dịch (KHÔNG dùng dv.GiaBan, KHÔNG dùng ctdv.ThanhTien)
     * Dùng: SUM(SoLuong * dv.GiaDichVu)
     * =========================================================
     */
    $tinhTongTienDV_UC = function (int $maGD) use ($db): float {
        $sql = "
            SELECT SUM(ctdv.SoLuong * dv.GiaDichVu) AS Tong
            FROM chitietdichvu ctdv
            INNER JOIN dichvu dv ON dv.MaDichVu = ctdv.MaDichVu
            WHERE ctdv.MaGiaoDich = ?
        ";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $maGD);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (float)($row['Tong'] ?? 0);
    };

    /**
     * =========================================================
     * Helper: lấy DonGia hiện tại của 1 phòng trong GD (để hiển thị “Đơn giá”)
     * =========================================================
     */
    $getDonGiaPhongTrongGD_UC = function (int $maGD, int $maPhong) use ($db): float {
        $sql = "
            SELECT ct.DonGia, p.Gia
            FROM chitietgiaodich ct
            INNER JOIN phong p ON p.MaPhong = ct.MaPhong
            WHERE ct.MaGiaoDich = ?
              AND ct.MaPhong = ?
            LIMIT 1
        ";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ii", $maGD, $maPhong);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if (!$row) return 0;
        if (isset($row['DonGia']) && (float)$row['DonGia'] > 0) return (float)$row['DonGia'];
        return (float)($row['Gia'] ?? 0);
    };

    /**
     * =========================================================
     * Helper: tính giảm giá từ KM (chỉ đọc, không update)
     * =========================================================
     */
    $calcTienGiam = function (?array $km, float $tongTruocGiam): float {
        if (!$km) return 0.0;
        $giaTri = (float)($km['GiaTri'] ?? 0);
        if ($giaTri <= 0) return 0.0;

        // Nếu < 100 → hiểu là %
        if ($giaTri < 100) {
            return $tongTruocGiam * $giaTri / 100.0;
        }

        // Nếu >=100 → hiểu là số tiền giảm
        return min($giaTri, $tongTruocGiam);
    };
// ===== helper chuẩn hoá khuyến mãi cho view + tính tiền =====
$normalizeKhuyenMai = function (?array $km): ?array {
    if (!$km) return null;

    // Nếu đã đúng format thì trả luôn
    if (isset($km['GiaTri'])) return $km;

    // Chuẩn hoá từ record DB
    $giaTri = (float)($km['MucUuDai'] ?? 0);

    return [
        'TenKhuyenMai' => $km['TenChuongTrinh'] ?? '',
        'GiaTri'       => $giaTri,
        'LoaiUuDai'    => $km['LoaiUuDai'] ?? 'PERCENT',
    ];
};
    /**
     * =========================================================
     * HANDLE POST
     * =========================================================
     */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['btn_action'] ?? '';

        if ($action === 'cancel') {
            header("Location: index.php?controller=letan&action=index");
            exit;
        }

        /**
         * ================= SEARCH =================
         */
        if ($action === 'search') {
            $searchKeyword = trim($_POST['search_keyword'] ?? '');

            if ($searchKeyword === '') {
                $errors[] = "Vui lòng nhập mã giao dịch hoặc CCCD.";
            } elseif (!ctype_digit($searchKeyword)) {
                $errors[] = "Chỉ được nhập số.";
            }

            if (!$errors) {
                if (strlen($searchKeyword) <= 8) {
                    $giaoDich = $gdModel->findByMaOrCCCD($searchKeyword, null);
                } else {
                    $giaoDich = $gdModel->findByMaOrCCCD(null, $searchKeyword);
                }

                if (!$giaoDich) {
                    $errors[] = "Không tìm thấy giao dịch.";
                } else {
                    $chiTietPhong  = $ctgdModel->getPhongByGiaoDich($giaoDich['MaGiaoDich']);
                    $chiTietDichVu = $ctdvModel->getByGiaoDich($giaoDich['MaGiaoDich']);

                    if (!$chiTietPhong) {
                        $errors[] = "Giao dịch không có chi tiết phòng.";
                    } else {
                        // ưu tiên phòng Booked
                        $ct0 = null;
                        foreach ($chiTietPhong as $ct) {
                            if (($ct['TrangThai'] ?? '') === 'Booked') {
                                $ct0 = $ct;
                                break;
                            }
                        }
                        if (!$ct0) $ct0 = $chiTietPhong[0];

                        // load KH
                        $stmt = $db->prepare("SELECT * FROM khachhang WHERE MaKhachHang=?");
                        $stmt->bind_param("i", $giaoDich['MaKhachHang']);
                        $stmt->execute();
                        $kh = $stmt->get_result()->fetch_assoc();

                        $form['ten_kh']   = $kh['TenKH']  ?? '';
                        $form['cccd']     = $kh['CCCD']   ?? '';
                        $form['sdt']      = $kh['SDT']    ?? '';
                        $form['email']    = $kh['Email']  ?? '';
                        $form['ngay_den'] = substr($ct0['NgayNhanDuKien'], 0, 10);
                        $form['ngay_di']  = substr($ct0['NgayTraDuKien'], 0, 10);
                        $form['so_nguoi'] = (int)($ct0['SoNguoi'] ?? 1);
                        $form['ma_phong'] = (int)($ct0['MaPhong'] ?? 0);

                        // ✅ KM: CHỈ ĐỌC (KHÔNG cho sửa)
                        $form['ma_khuyen_mai'] = $giaoDich['MaKhuyenMai'] ?? null;
                        if (!empty($giaoDich['MaKhuyenMai'])) {
                            $tmpKm = $kmModel->getById((int)$giaoDich['MaKhuyenMai']);
                        // ===== LOAD THÀNH VIÊN TRONG PHÒNG =====


if ($tmpKm) {
    $khuyenMai = [
        'TenKhuyenMai' => $tmpKm['TenChuongTrinh'] ?? '',
        'GiaTri'       => (float)($tmpKm['LoaiUuDai'] === 'PERCENT'
                                ? $tmpKm['MucUuDai']
                                : $tmpKm['MucUuDai']),
        'LoaiUuDai'    => $tmpKm['LoaiUuDai'] ?? 'PERCENT',
    ];
} else {
    $khuyenMai = null;
}
                        }

                        // load phòng trống
                        $rs = $phongModel->searchAvailable();
                        while ($row = $rs->fetch_assoc()) $dsPhong[] = $row;

                        // thêm phòng hiện tại nếu không có trong list
                        $maPhongDangChon = (int)$ct0['MaPhong'];
                        $listIds = array_map(fn($x) => (int)$x['MaPhong'], $dsPhong);
                        if (!in_array($maPhongDangChon, $listIds, true)) {
                            $dsPhong[] = $phongModel->getById($maPhongDangChon) ?: $ct0;
                        }
// ===== LOAD THÀNH VIÊN TRONG GIAO DỊCH =====
// ===== LOAD THÀNH VIÊN TRONG GIAO DỊCH (ĐÚNG) =====
$stmt = $db->prepare("
    SELECT *
    FROM khachhang
    WHERE MaGiaoDich = ?
    ORDER BY MaKhachHang
");
$stmt->bind_param("i", $giaoDich['MaGiaoDich']);
$stmt->execute();
$thanhVien = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// đồng bộ số người từ CSDL
$form['so_nguoi'] = count($thanhVien);
                        // ===== TÍNH TIỀN ĐỂ HIỂN THỊ FORM
                        $donGiaHienTai = $getDonGiaPhongTrongGD_UC((int)$giaoDich['MaGiaoDich'], (int)$form['ma_phong']);
                        $tongPhong     = $tinhTongTienPhong_UC((int)$giaoDich['MaGiaoDich']);
                        $tongDV        = $tinhTongTienDV_UC((int)$giaoDich['MaGiaoDich']);
                        $tongTruocGiam = $tongPhong + $tongDV;
                        $tienGiam      = $calcTienGiam($khuyenMai, $tongTruocGiam);
                        $thanhTien     = max(0, $tongTruocGiam - $tienGiam);
                    }
                }
            }
        }

        /**
         * ================= PICK ROOM =================
         */
        if ($action === 'pick_room') {
            $maGD      = (int)($_POST['ma_giao_dich'] ?? 0);
            $maPhongCu = (int)($_POST['ma_phong_cu'] ?? 0);

            $giaoDich      = $gdModel->getById($maGD);
            $chiTietPhong  = $ctgdModel->getPhongByGiaoDich($maGD);
            $chiTietDichVu = $ctdvModel->getByGiaoDich($maGD);

            $ct0 = null;
            // ===== LOAD THÀNH VIÊN THEO PHÒNG ĐANG PICK =====

            foreach ($chiTietPhong as $ct) {
                if ((int)$ct['MaPhong'] === $maPhongCu) {
                    $ct0 = $ct;
                    break;
                }
            }
            if (!$ct0 && !empty($chiTietPhong)) $ct0 = $chiTietPhong[0];

            if ($giaoDich && $ct0) {
                // ===== LOAD THÀNH VIÊN TRONG GIAO DỊCH =====
// ===== LOAD THÀNH VIÊN TRONG GIAO DỊCH (ĐÚNG) =====
$stmt = $db->prepare("
    SELECT *
    FROM khachhang
    WHERE MaGiaoDich = ?
    ORDER BY MaKhachHang
");
$stmt->bind_param("i", $giaoDich['MaGiaoDich']);
$stmt->execute();
$thanhVien = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// đồng bộ số người
$form['so_nguoi'] = count($thanhVien);
                // load KH
                $stmt = $db->prepare("SELECT * FROM khachhang WHERE MaKhachHang=?");
                $stmt->bind_param("i", $giaoDich['MaKhachHang']);
                $stmt->execute();
                $kh = $stmt->get_result()->fetch_assoc();

                $form['ten_kh']   = $kh['TenKH']  ?? '';
                $form['cccd']     = $kh['CCCD']   ?? '';
                $form['sdt']      = $kh['SDT']    ?? '';
                $form['email']    = $kh['Email']  ?? '';
                $form['ngay_den'] = substr($ct0['NgayNhanDuKien'], 0, 10);
                $form['ngay_di']  = substr($ct0['NgayTraDuKien'], 0, 10);
                
                $form['ma_phong'] = (int)($ct0['MaPhong'] ?? 0);

                // ✅ KM: CHỈ ĐỌC (KHÔNG sửa)
                $form['ma_khuyen_mai'] = $giaoDich['MaKhuyenMai'] ?? null;
               if (!empty($giaoDich['MaKhuyenMai'])) {
    $tmpKm = $kmModel->getById((int)$giaoDich['MaKhuyenMai']);
    $khuyenMai = $normalizeKhuyenMai($tmpKm);
} else {
    $khuyenMai = null;
}

                $rs = $phongModel->searchAvailable();
                while ($row = $rs->fetch_assoc()) $dsPhong[] = $row;

                $maPhongDangChon = (int)$ct0['MaPhong'];
                $listIds = array_map(fn($x) => (int)$x['MaPhong'], $dsPhong);
                if (!in_array($maPhongDangChon, $listIds, true)) {
                    $dsPhong[] = $phongModel->getById($maPhongDangChon) ?: $ct0;
                }

                $searchKeyword = (string)$maGD;

                // ===== TÍNH TIỀN ĐỂ HIỂN THỊ FORM
                $donGiaHienTai = $getDonGiaPhongTrongGD_UC($maGD, (int)$form['ma_phong']);
                $tongPhong     = $tinhTongTienPhong_UC($maGD);
                $tongDV        = $tinhTongTienDV_UC($maGD);
                $tongTruocGiam = $tongPhong + $tongDV;
                $tienGiam      = $calcTienGiam($khuyenMai, $tongTruocGiam);
                $thanhTien     = max(0, $tongTruocGiam - $tienGiam);
            } else {
                $errors[] = "Không tìm thấy dữ liệu để sửa phòng.";
            }
        }

        /**
         * ================= SAVE =================
         */
        if ($action === 'save') {
            $maGD      = (int)($_POST['ma_giao_dich'] ?? 0);
            $maPhongCu = (int)($_POST['ma_phong_cu'] ?? 0);

            $giaoDich = $gdModel->getById($maGD);
            if (!$giaoDich) {
                $errors[] = "Không tìm thấy giao dịch.";
            } else {
                // ✅ KM: lấy từ giao dịch (DB) - KHÔNG nhận từ POST
                $form['ma_khuyen_mai'] = $giaoDich['MaKhuyenMai'] ?? null;
                if (!empty($giaoDich['MaKhuyenMai'])) {
    $tmpKm = $kmModel->getById((int)$giaoDich['MaKhuyenMai']);
    $khuyenMai = $normalizeKhuyenMai($tmpKm);
} else {
    $khuyenMai = null;
}
            }

            // nhận form (loại trừ ma_khuyen_mai để khỏi “vô tình update”)
            $fieldsAllow = ['ten_kh','cccd','sdt','email','ngay_den','ngay_di','so_nguoi','ma_phong'];
            foreach ($fieldsAllow as $k) {
                if (isset($_POST[$k])) $form[$k] = trim((string)$_POST[$k]);
            }

            // validate cơ bản
            if ($form['ten_kh'] === '') $errors[] = "Thiếu họ tên.";
            if (!preg_match('/^\d{9,12}$/', $form['cccd'])) $errors[] = "CCCD không hợp lệ.";
            if (!preg_match('/^0\d{8,10}$/', $form['sdt'])) $errors[] = "SĐT không hợp lệ.";
            if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ.";
            if (empty($form['ngay_den']) || empty($form['ngay_di'])) $errors[] = "Thiếu ngày đến/ngày đi.";
            if ((int)$form['so_nguoi'] <= 0) $errors[] = "Số người phải > 0.";
            if ((int)$form['ma_phong'] <= 0) $errors[] = "Chưa chọn phòng.";
// ===== validate bổ sung =====

// 1) Tên không được là số / ký tự lạ (chỉ chữ + khoảng trắng)
if ($form['ten_kh'] !== '' && !preg_match('/^[\p{L}\s]+$/u', $form['ten_kh'])) {
    $errors[] = "Họ tên không hợp lệ (không được chứa số/ký tự đặc biệt).";
}

// 2) Ngày đi phải sau ngày đến
if (!empty($form['ngay_den']) && !empty($form['ngay_di'])) {
    if (strtotime($form['ngay_di']) <= strtotime($form['ngay_den'])) {
        $errors[] = "Ngày đi phải sau ngày đến.";
    }
}

// 3) Số người không vượt quá tối đa của phòng (nếu phòng tồn tại)
if ((int)$form['ma_phong'] > 0) {
    $ph = $phongModel->getById((int)$form['ma_phong']);
    if ($ph && isset($ph['SoKhachToiDa']) && (int)$form['so_nguoi'] > (int)$ph['SoKhachToiDa']) {
        $errors[] = "Số người vượt quá tối đa của phòng (" . (int)$ph['SoKhachToiDa'] . ").";
    }
}
            if (!$errors) {
                $db->begin_transaction();
                try {
                    // ===== UPDATE KH =====
                    $stmt = $db->prepare("
                        UPDATE khachhang
                        SET TenKH=?, CCCD=?, SDT=?, Email=?
                        WHERE MaKhachHang=?
                    ");
                    $stmt->bind_param(
                        "ssssi",
                        $form['ten_kh'],
                        $form['cccd'],
                        $form['sdt'],
                        $form['email'],
                        $giaoDich['MaKhachHang']
                    );
                    $stmt->execute();

                    // ===== UPDATE CHI TIẾT PHÒNG =====
                // ===== TÍNH TIỀN PHÒNG CHO DÒNG CHI TIẾT ĐANG SỬA =====
// Lưu ý: tính theo ngày + đơn giá phòng (ưu tiên ct.DonGia nếu có, fallback phong.Gia)
$ngayNhan = $form['ngay_den'] . ' 14:00:00';
$ngayTra  = $form['ngay_di']  . ' 12:00:00';

$soDem = $calcSoDem($ngayNhan, $ngayTra);
$donGiaPhong = $getDonGiaPhongTrongGD_UC($maGD, (int)$form['ma_phong']);
$thanhTienPhong = $soDem * $donGiaPhong;

// ===== UPDATE CHI TIẾT PHÒNG (CHÍNH XÁC: cập nhật cả tiền + khách) =====
$ctgdModel->updateBooking(
    $maGD,
    $maPhongCu,
    [
        'MaPhong'        => (int)$form['ma_phong'],
        'SoNguoi'        => (int)$form['so_nguoi'],
        'NgayNhanDuKien' => $ngayNhan,
        'NgayTraDuKien'  => $ngayTra,

        // cập nhật khách trong chitietgiaodich (nếu bảng có các cột này)
        'TenKhach' => $form['ten_kh'],
        'CCCD'     => $form['cccd'],
        'SDT'      => $form['sdt'],
        'Email'    => $form['email'],

        // cập nhật tiền dòng chi tiết
        'DonGia'   => $donGiaPhong,
        'ThanhTien'=> $thanhTienPhong
    ]
);

                    // đổi phòng thì cập nhật trạng thái phòng
                    if ((int)$form['ma_phong'] !== $maPhongCu) {
                        $phongModel->updateTrangThai($maPhongCu, 'Trong');
                        $phongModel->updateTrangThai((int)$form['ma_phong'], 'Booked');
                    }

                    // ===== UPDATE DỊCH VỤ (giữ nguyên hàm syncFromForm bạn đang có)
                    // Nếu syncFromForm của bạn đang dính ThanhTien/GiaBan → nó sẽ lỗi.
                    // Nhưng bạn đang dùng nó rồi, nên mình giữ nguyên (UC của bạn muốn cập nhật số lượng/xoá).
                    // Nếu nó đang lỗi, bạn gửi mình code syncFromForm hiện tại để mình fix đúng theo schema của bạn.
                    $ctdvModel->syncFromForm(
                        $maGD,
                        $_POST['services'] ?? [],
                        $_POST['remove_services'] ?? []
                    );

                   // ===== TÍNH TIỀN THEO NGHIỆP VỤ CHUẨN =====

// Tổng tiền phòng (chưa trừ KM)
$tongPhong = $tinhTongTienPhong_UC($maGD);

// Tổng tiền dịch vụ
$tongDV = $tinhTongTienDV_UC($maGD);

// 👉 TongTien trong giaodich = phòng + dịch vụ (KHÔNG TRỪ KM)
$tongTienGiaoDich = $tongPhong + $tongDV;
// ===== UPDATE THÀNH VIÊN (BẢNG KHACHHANG) =====
if (!empty($_POST['members']) && is_array($_POST['members'])) {
    foreach ($_POST['members'] as $m) {

        if (empty($m['id'])) {
            continue;
        }

        $maKH = (int)$m['id'];

        // validate
        if (!preg_match('/^[\p{L}\s]+$/u', $m['ten'])) {
            throw new Exception("Tên thành viên không hợp lệ");
        }

        if (!empty($m['cccd']) && !preg_match('/^\d{9,12}$/', $m['cccd'])) {
            throw new Exception("CCCD thành viên không hợp lệ");
        }

        if (!empty($m['sdt']) && !preg_match('/^0\d{8,10}$/', $m['sdt'])) {
            throw new Exception("SĐT thành viên không hợp lệ");
        }

        if (!empty($m['email']) && !filter_var($m['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email thành viên không hợp lệ");
        }

        $stmt = $db->prepare("
            UPDATE khachhang
            SET TenKH=?, CCCD=?, SDT=?, Email=?
            WHERE MaKhachHang=?
        ");

        $stmt->bind_param(
    "ssssi",
    $m['ten'],
    $m['cccd'],
    $m['sdt'],
    $m['email'],
    (int)$maKH
);

        $stmt->execute();
    }
}
// 👉 tiền giảm CHỈ để hiển thị
$tienGiam = $calcTienGiam($khuyenMai, $tongTienGiaoDich);

// 👉 thành tiền hiển thị (KHÔNG lưu DB)
$thanhTien = max(0, $tongTienGiaoDich - $tienGiam);
// ===== UPDATE THÀNH VIÊN (BẢNG KHACHHANG) =====

// ✅ CHỈ update TongTien (chưa trừ KM)
$gdModel->updateTongTien($maGD, $tongTienGiaoDich);
// ===== UPDATE THÀNH VIÊN =====

                    $db->commit();
                    $success = "Cập nhật đặt phòng thành công.";

                    // reload data để render lại view đúng
                    $giaoDich      = $gdModel->getById($maGD);
                    $chiTietPhong  = $ctgdModel->getPhongByGiaoDich($maGD);
                    $chiTietDichVu = $ctdvModel->getByGiaoDich($maGD);

                    // load danh sách phòng
                    $rs = $phongModel->searchAvailable();
                    while ($row = $rs->fetch_assoc()) $dsPhong[] = $row;

                    $listIds = array_map(fn($x) => (int)$x['MaPhong'], $dsPhong);
                    if (!in_array((int)$form['ma_phong'], $listIds, true)) {
                        $dsPhong[] = $phongModel->getById((int)$form['ma_phong']);
                    }

                } catch (Throwable $e) {
                    $db->rollback();
                    $errors[] = $e->getMessage();
                }
            } else {
                // nếu validate fail, vẫn tính tiền để view hiển thị (nếu có giao dịch)
                if ($giaoDich) {
                    $donGiaHienTai = $getDonGiaPhongTrongGD_UC($maGD, (int)$form['ma_phong']);
                    $tongPhong     = $tinhTongTienPhong_UC($maGD);
                    $tongDV        = $tinhTongTienDV_UC($maGD);
                    $tongTruocGiam = $tongPhong + $tongDV;
                    $tienGiam      = $calcTienGiam($khuyenMai, $tongTruocGiam);
                    $thanhTien     = max(0, $tongTruocGiam - $tienGiam);
                }
            }
        }
    }

    /**
     * ================= RENDER =================
     * Truyền đủ các biến tiền để view làm form giống UC đặt phòng
     */
    $this->view("letan/sua_dat_phong", [
        'errors'        => $errors,
        'success'       => $success,
        'giaoDich'      => $giaoDich,
        'chiTiet'       => $chiTietPhong,
        'chiTietDichVu' => $chiTietDichVu,
        'searchKeyword' => $searchKeyword,
        'form'          => $form,
        'dsPhong'       => $dsPhong,
        'dsKhuyenMai'   => $dsKhuyenMai,
        'thanhVien' => $thanhVien,

        // ✅ thêm data để view hiển thị
        'khuyenMai'     => $khuyenMai,
        'donGiaHienTai' => $donGiaHienTai,
        'tongPhong'     => $tongPhong,
        'tongDV'        => $tongDV,
        'tongTruocGiam' => $tongTruocGiam,
        'tienGiam'      => $tienGiam,
        'thanhTien'     => $thanhTien,
    ]);
}
public function suaThongTinDatPhong()
{
    try {

       $this->requireRole(['LeTan']);

    $gdModel    = new GiaoDich();
    $ctgdModel  = new ChiTietGiaoDich();
    $phongModel = new Phong();
    $ctdvModel  = new ChiTietDichVu();
    $kmModel    = new KhuyenMai();

    $db = Database::getConnection();

    $errors  = [];
    $success = null;

    $thanhVien = [];
    $giaoDich       = null;
    $chiTietPhong   = [];
    $chiTietDichVu  = [];
    $dsPhong        = [];
    $dsKhuyenMai    = $kmModel->getAllActive();
    $khuyenMai      = null;

    $searchKeyword = "";

    // ====== Dữ liệu tính tiền để show ra view (giống UC đặt phòng)
    $donGiaHienTai   = 0;     // đơn giá phòng đang chọn (nếu nhiều phòng thì lấy phòng đang thao tác)
    $tongPhong       = 0;     // tổng tiền phòng
    $tongDV          = 0;     // tổng tiền dịch vụ
    $tongTruocGiam   = 0;
    $tienGiam        = 0;
    $thanhTien       = 0;

    $form = [
        'ten_kh'   => '',
        'cccd'     => '',
        'sdt'      => '',
        'email'    => '',
        'ngay_den' => '',
        'ngay_di'  => '',
        'so_nguoi' => 1,
        'ma_phong' => 0,

        // ⚠️ UC này KHÔNG cho sửa KM. Field này chỉ để giữ/hiển thị.
        'ma_khuyen_mai' => null
    ];

    /**
     * =========================================================
     * Helper: tính số đêm tối thiểu 1
     * =========================================================
     */
    $calcSoDem = function (string $ngayNhan, string $ngayTra): int {
        $t1 = strtotime(substr($ngayNhan, 0, 10));
        $t2 = strtotime(substr($ngayTra, 0, 10));
        if (!$t1 || !$t2) return 1;
        $dem = (int)ceil(($t2 - $t1) / 86400);
        return max(1, $dem);
    };

    /**
     * =========================================================
     * Helper: tính tiền phòng cho giao dịch (KHÔNG dùng loaiphong)
     * Ưu tiên: ctgd.DonGia nếu có > 0, fallback: phong.Gia
     * =========================================================
     */
    $tinhTongTienPhong_UC = function (int $maGD) use ($db, $calcSoDem): float {
        $sql = "
            SELECT
                ct.MaPhong,
                ct.NgayNhanDuKien,
                ct.NgayTraDuKien,
                ct.DonGia,
                p.Gia
            FROM chitietgiaodich ct
            INNER JOIN phong p ON p.MaPhong = ct.MaPhong
            WHERE ct.MaGiaoDich = ?
              AND ct.TrangThai IN ('Booked','CheckedIn','Stayed','Moi')
        ";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $maGD);
        $stmt->execute();
        $rs = $stmt->get_result();

        $tong = 0.0;
        while ($row = $rs->fetch_assoc()) {
            $soDem  = $calcSoDem($row['NgayNhanDuKien'], $row['NgayTraDuKien']);
            $donGia = 0;

            if (isset($row['DonGia']) && (float)$row['DonGia'] > 0) {
                $donGia = (float)$row['DonGia'];
            } else {
                $donGia = (float)($row['Gia'] ?? 0);
            }

            $tong += $soDem * $donGia;
        }
        return (float)$tong;
    };

    /**
     * =========================================================
     * Helper: tính tiền DV cho giao dịch (KHÔNG dùng dv.GiaBan, KHÔNG dùng ctdv.ThanhTien)
     * Dùng: SUM(SoLuong * dv.GiaDichVu)
     * =========================================================
     */
    $tinhTongTienDV_UC = function (int $maGD) use ($db): float {
        $sql = "
            SELECT SUM(ctdv.SoLuong * dv.GiaDichVu) AS Tong
            FROM chitietdichvu ctdv
            INNER JOIN dichvu dv ON dv.MaDichVu = ctdv.MaDichVu
            WHERE ctdv.MaGiaoDich = ?
        ";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $maGD);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (float)($row['Tong'] ?? 0);
    };

    /**
     * =========================================================
     * Helper: lấy DonGia hiện tại của 1 phòng trong GD (để hiển thị “Đơn giá”)
     * =========================================================
     */
    $getDonGiaPhongTrongGD_UC = function (int $maGD, int $maPhong) use ($db): float {
        $sql = "
            SELECT ct.DonGia, p.Gia
            FROM chitietgiaodich ct
            INNER JOIN phong p ON p.MaPhong = ct.MaPhong
            WHERE ct.MaGiaoDich = ?
              AND ct.MaPhong = ?
            LIMIT 1
        ";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ii", $maGD, $maPhong);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if (!$row) return 0;
        if (isset($row['DonGia']) && (float)$row['DonGia'] > 0) return (float)$row['DonGia'];
        return (float)($row['Gia'] ?? 0);
    };

    /**
     * =========================================================
     * Helper: tính giảm giá từ KM (chỉ đọc, không update)
     * =========================================================
     */
    $calcTienGiam = function (?array $km, float $tongTruocGiam): float {
        if (!$km) return 0.0;
        $giaTri = (float)($km['GiaTri'] ?? 0);
        if ($giaTri <= 0) return 0.0;

        // Nếu < 100 → hiểu là %
        if ($giaTri < 100) {
            return $tongTruocGiam * $giaTri / 100.0;
        }

        // Nếu >=100 → hiểu là số tiền giảm
        return min($giaTri, $tongTruocGiam);
    };
// ===== helper chuẩn hoá khuyến mãi cho view + tính tiền =====
$normalizeKhuyenMai = function (?array $km): ?array {
    if (!$km) return null;

    // Nếu đã đúng format thì trả luôn
    if (isset($km['GiaTri'])) return $km;

    // Chuẩn hoá từ record DB
    $giaTri = (float)($km['MucUuDai'] ?? 0);

    return [
        'TenKhuyenMai' => $km['TenChuongTrinh'] ?? '',
        'GiaTri'       => $giaTri,
        'LoaiUuDai'    => $km['LoaiUuDai'] ?? 'PERCENT',
    ];
};
    /**
     * =========================================================
     * HANDLE POST
     * =========================================================
     */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['btn_action'] ?? '';

        if ($action === 'cancel') {
            header("Location: index.php?controller=letan&action=index");
            exit;
        }

        /**
         * ================= SEARCH =================
         */
        if ($action === 'search') {
            $searchKeyword = trim($_POST['search_keyword'] ?? '');

            if ($searchKeyword === '') {
                $errors[] = "Vui lòng nhập mã giao dịch hoặc CCCD.";
            } elseif (!ctype_digit($searchKeyword)) {
                $errors[] = "Chỉ được nhập số.";
            }

            if (!$errors) {
                if (strlen($searchKeyword) <= 8) {
                    $giaoDich = $gdModel->findByMaOrCCCD($searchKeyword, null);
                } else {
                    $giaoDich = $gdModel->findByMaOrCCCD(null, $searchKeyword);
                }

                if (!$giaoDich) {
                    $errors[] = "Không tìm thấy giao dịch.";
                } else {
                    $chiTietPhong  = $ctgdModel->getPhongByGiaoDich($giaoDich['MaGiaoDich']);
                    $chiTietDichVu = $ctdvModel->getByGiaoDich($giaoDich['MaGiaoDich']);

                    if (!$chiTietPhong) {
                        $errors[] = "Giao dịch không có chi tiết phòng.";
                    } else {
                        // ưu tiên phòng Booked
                        $ct0 = null;
                        foreach ($chiTietPhong as $ct) {
                            if (($ct['TrangThai'] ?? '') === 'Booked') {
                                $ct0 = $ct;
                                break;
                            }
                        }
                        if (!$ct0) $ct0 = $chiTietPhong[0];

                        // load KH
                        $stmt = $db->prepare("SELECT * FROM khachhang WHERE MaKhachHang=?");
                        $stmt->bind_param("i", $giaoDich['MaKhachHang']);
                        $stmt->execute();
                        $kh = $stmt->get_result()->fetch_assoc();

                        $form['ten_kh']   = $kh['TenKH']  ?? '';
                        $form['cccd']     = $kh['CCCD']   ?? '';
                        $form['sdt']      = $kh['SDT']    ?? '';
                        $form['email']    = $kh['Email']  ?? '';
                        $form['ngay_den'] = substr($ct0['NgayNhanDuKien'], 0, 10);
                        $form['ngay_di']  = substr($ct0['NgayTraDuKien'], 0, 10);
                        $form['so_nguoi'] = (int)($ct0['SoNguoi'] ?? 1);
                        $form['ma_phong'] = (int)($ct0['MaPhong'] ?? 0);
                        // ================= FIX 2: TIỀN PHÒNG NỀN (KHÔNG CỘNG DỒN) =================

// tiền phòng của PHÒNG ĐANG SỬA
$ngayNhan0 = $ct0['NgayNhanDuKien'];
$ngayTra0  = $ct0['NgayTraDuKien'];

$soDem0  = $calcSoDem($ngayNhan0, $ngayTra0);
$donGia0 = $getDonGiaPhongTrongGD_UC(
    (int)$giaoDich['MaGiaoDich'],
    (int)$ct0['MaPhong']
);

$tienPhongDangSua = $soDem0 * $donGia0;

// tổng tiền phòng toàn giao dịch
$tongPhongAll = $tinhTongTienPhong_UC((int)$giaoDich['MaGiaoDich']);

// tiền phòng CÁC PHÒNG KHÁC (làm nền cho JS)
$tongPhongKhac = max(0, $tongPhongAll - $tienPhongDangSua);

                        // ✅ KM: CHỈ ĐỌC (KHÔNG cho sửa)
                        $form['ma_khuyen_mai'] = $giaoDich['MaKhuyenMai'] ?? null;
                        if (!empty($giaoDich['MaKhuyenMai'])) {
                            $tmpKm = $kmModel->getById((int)$giaoDich['MaKhuyenMai']);
                        // ===== LOAD THÀNH VIÊN TRONG PHÒNG =====


if ($tmpKm) {
    $khuyenMai = [
        'TenKhuyenMai' => $tmpKm['TenChuongTrinh'] ?? '',
        'GiaTri'       => (float)($tmpKm['LoaiUuDai'] === 'PERCENT'
                                ? $tmpKm['MucUuDai']
                                : $tmpKm['MucUuDai']),
        'LoaiUuDai'    => $tmpKm['LoaiUuDai'] ?? 'PERCENT',
    ];
} else {
    $khuyenMai = null;
}
                        }

                        // load phòng trống
                        $rs = $phongModel->searchAvailable();
                        while ($row = $rs->fetch_assoc()) $dsPhong[] = $row;

                        // thêm phòng hiện tại nếu không có trong list
                        $maPhongDangChon = (int)$ct0['MaPhong'];
                        $listIds = array_map(fn($x) => (int)$x['MaPhong'], $dsPhong);
                        if (!in_array($maPhongDangChon, $listIds, true)) {
                            $dsPhong[] = $phongModel->getById($maPhongDangChon) ?: $ct0;
                        }
// ===== LOAD THÀNH VIÊN TRONG GIAO DỊCH =====
// ===== LOAD THÀNH VIÊN TRONG GIAO DỊCH (ĐÚNG) =====
$stmt = $db->prepare("
    SELECT *
    FROM khachhang
    WHERE MaGiaoDich = ?
    ORDER BY MaKhachHang
");
$stmt->bind_param("i", $giaoDich['MaGiaoDich']);
$stmt->execute();
$thanhVien = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// đồng bộ số người từ CSDL
$form['so_nguoi'] = count($thanhVien);
                        // ===== TÍNH TIỀN ĐỂ HIỂN THỊ FORM
                        $donGiaHienTai = $getDonGiaPhongTrongGD_UC((int)$giaoDich['MaGiaoDich'], (int)$form['ma_phong']);
                        $tongPhong     = $tinhTongTienPhong_UC((int)$giaoDich['MaGiaoDich']);
                        $tongDV        = $tinhTongTienDV_UC((int)$giaoDich['MaGiaoDich']);
                        $tongTruocGiam = $tongPhong + $tongDV;
                        $tienGiam      = $calcTienGiam($khuyenMai, $tongTruocGiam);
                        $thanhTien     = max(0, $tongTruocGiam - $tienGiam);
                    }
                }
            }
        }

        /**
         * ================= PICK ROOM =================
         */
        if ($action === 'pick_room') {
            $maGD      = (int)($_POST['ma_giao_dich'] ?? 0);
            $maPhongCu = (int)($_POST['ma_phong_cu'] ?? 0);

            $giaoDich      = $gdModel->getById($maGD);
            $chiTietPhong  = $ctgdModel->getPhongByGiaoDich($maGD);
            $chiTietDichVu = $ctdvModel->getByGiaoDich($maGD);

            $ct0 = null;
            // ===== LOAD THÀNH VIÊN THEO PHÒNG ĐANG PICK =====

            foreach ($chiTietPhong as $ct) {
                if ((int)$ct['MaPhong'] === $maPhongCu) {
                    $ct0 = $ct;
                    break;
                }
            }
            if (!$ct0 && !empty($chiTietPhong)) $ct0 = $chiTietPhong[0];

            if ($giaoDich && $ct0) {
                // ===== LOAD THÀNH VIÊN TRONG GIAO DỊCH =====
// ===== LOAD THÀNH VIÊN TRONG GIAO DỊCH (ĐÚNG) =====
$stmt = $db->prepare("
    SELECT *
    FROM khachhang
    WHERE MaGiaoDich = ?
    ORDER BY MaKhachHang
");
$stmt->bind_param("i", $giaoDich['MaGiaoDich']);
$stmt->execute();
$thanhVien = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// đồng bộ số người
$form['so_nguoi'] = count($thanhVien);
                // load KH
                $stmt = $db->prepare("SELECT * FROM khachhang WHERE MaKhachHang=?");
                $stmt->bind_param("i", $giaoDich['MaKhachHang']);
                $stmt->execute();
                $kh = $stmt->get_result()->fetch_assoc();

                $form['ten_kh']   = $kh['TenKH']  ?? '';
                $form['cccd']     = $kh['CCCD']   ?? '';
                $form['sdt']      = $kh['SDT']    ?? '';
                $form['email']    = $kh['Email']  ?? '';
                $form['ngay_den'] = substr($ct0['NgayNhanDuKien'], 0, 10);
                $form['ngay_di']  = substr($ct0['NgayTraDuKien'], 0, 10);
                
                $form['ma_phong'] = (int)($ct0['MaPhong'] ?? 0);
                // ================= FIX 2: TIỀN PHÒNG NỀN (KHÔNG CỘNG DỒN) =================

$ngayNhan0 = $ct0['NgayNhanDuKien'];
$ngayTra0  = $ct0['NgayTraDuKien'];

$soDem0  = $calcSoDem($ngayNhan0, $ngayTra0);
$donGia0 = $getDonGiaPhongTrongGD_UC(
    (int)$giaoDich['MaGiaoDich'],
    (int)$ct0['MaPhong']
);

$tienPhongDangSua = $soDem0 * $donGia0;
$tongPhongAll     = $tinhTongTienPhong_UC((int)$giaoDich['MaGiaoDich']);
$tongPhongKhac    = max(0, $tongPhongAll - $tienPhongDangSua);

                // ✅ KM: CHỈ ĐỌC (KHÔNG sửa)
                $form['ma_khuyen_mai'] = $giaoDich['MaKhuyenMai'] ?? null;
               if (!empty($giaoDich['MaKhuyenMai'])) {
    $tmpKm = $kmModel->getById((int)$giaoDich['MaKhuyenMai']);
    $khuyenMai = $normalizeKhuyenMai($tmpKm);
} else {
    $khuyenMai = null;
}

                $rs = $phongModel->searchAvailable();
                while ($row = $rs->fetch_assoc()) $dsPhong[] = $row;

                $maPhongDangChon = (int)$ct0['MaPhong'];
                $listIds = array_map(fn($x) => (int)$x['MaPhong'], $dsPhong);
                if (!in_array($maPhongDangChon, $listIds, true)) {
                    $dsPhong[] = $phongModel->getById($maPhongDangChon) ?: $ct0;
                }

                $searchKeyword = (string)$maGD;

                // ===== TÍNH TIỀN ĐỂ HIỂN THỊ FORM
                $donGiaHienTai = $getDonGiaPhongTrongGD_UC($maGD, (int)$form['ma_phong']);
                $tongPhong     = $tinhTongTienPhong_UC($maGD);
                $tongDV        = $tinhTongTienDV_UC($maGD);
                $tongTruocGiam = $tongPhong + $tongDV;
                $tienGiam      = $calcTienGiam($khuyenMai, $tongTruocGiam);
                $thanhTien     = max(0, $tongTruocGiam - $tienGiam);
            } else {
                $errors[] = "Không tìm thấy dữ liệu để sửa phòng.";
            }
        }

        /**
         * ================= SAVE =================
         */
        if ($action === 'save') {
            $maGD      = (int)($_POST['ma_giao_dich'] ?? 0);
            $maPhongCu = (int)($_POST['ma_phong_cu'] ?? 0);

            $giaoDich = $gdModel->getById($maGD);
            if (!$giaoDich) {
                $errors[] = "Không tìm thấy giao dịch.";
            } else {
                // ✅ KM: lấy từ giao dịch (DB) - KHÔNG nhận từ POST
                $form['ma_khuyen_mai'] = $giaoDich['MaKhuyenMai'] ?? null;
                if (!empty($giaoDich['MaKhuyenMai'])) {
    $tmpKm = $kmModel->getById((int)$giaoDich['MaKhuyenMai']);
    $khuyenMai = $normalizeKhuyenMai($tmpKm);
} else {
    $khuyenMai = null;
}
            }

            // nhận form (loại trừ ma_khuyen_mai để khỏi “vô tình update”)
            $fieldsAllow = ['ten_kh','cccd','sdt','email','ngay_den','ngay_di','so_nguoi','ma_phong'];
            foreach ($fieldsAllow as $k) {
                if (isset($_POST[$k])) $form[$k] = trim((string)$_POST[$k]);
            }

            // validate cơ bản
            if ($form['ten_kh'] === '') $errors[] = "Thiếu họ tên.";
            if (!preg_match('/^\d{9,12}$/', $form['cccd'])) $errors[] = "CCCD không hợp lệ.";
            if (!preg_match('/^0\d{8,10}$/', $form['sdt'])) $errors[] = "SĐT không hợp lệ.";
            if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ.";
            if (empty($form['ngay_den']) || empty($form['ngay_di'])) $errors[] = "Thiếu ngày đến/ngày đi.";
            if ((int)$form['so_nguoi'] <= 0) $errors[] = "Số người phải > 0.";
            if ((int)$form['ma_phong'] <= 0) $errors[] = "Chưa chọn phòng.";
// ===== validate bổ sung =====

// 1) Tên không được là số / ký tự lạ (chỉ chữ + khoảng trắng)
if ($form['ten_kh'] !== '' && !preg_match('/^[\p{L}\s]+$/u', $form['ten_kh'])) {
    $errors[] = "Họ tên không hợp lệ (không được chứa số/ký tự đặc biệt).";
}

// 2) Ngày đi phải sau ngày đến
if (!empty($form['ngay_den']) && !empty($form['ngay_di'])) {
    if (strtotime($form['ngay_di']) <= strtotime($form['ngay_den'])) {
        $errors[] = "Ngày đi phải sau ngày đến.";
    }
}

// 3) Số người không vượt quá tối đa của phòng (nếu phòng tồn tại)
if ((int)$form['ma_phong'] > 0) {
    $ph = $phongModel->getById((int)$form['ma_phong']);
    if ($ph && isset($ph['SoKhachToiDa']) && (int)$form['so_nguoi'] > (int)$ph['SoKhachToiDa']) {
        $errors[] = "Số người vượt quá tối đa của phòng (" . (int)$ph['SoKhachToiDa'] . ").";
    }
}
            if (!$errors) {
                $db->begin_transaction();
                try {
                    // ===== UPDATE KH =====
                    $stmt = $db->prepare("
                        UPDATE khachhang
                        SET TenKH=?, CCCD=?, SDT=?, Email=?
                        WHERE MaKhachHang=?
                    ");
                    $stmt->bind_param(
                        "ssssi",
                        $form['ten_kh'],
                        $form['cccd'],
                        $form['sdt'],
                        $form['email'],
                        $giaoDich['MaKhachHang']
                    );
                    $stmt->execute();

                    // ===== UPDATE CHI TIẾT PHÒNG =====
                // ===== TÍNH TIỀN PHÒNG CHO DÒNG CHI TIẾT ĐANG SỬA =====
// Lưu ý: tính theo ngày + đơn giá phòng (ưu tiên ct.DonGia nếu có, fallback phong.Gia)
$ngayNhan = $form['ngay_den'] . ' 14:00:00';
$ngayTra  = $form['ngay_di']  . ' 12:00:00';

$soDem = $calcSoDem($ngayNhan, $ngayTra);
$donGiaPhong = $getDonGiaPhongTrongGD_UC($maGD, (int)$form['ma_phong']);
$thanhTienPhong = $soDem * $donGiaPhong;

// ===== UPDATE CHI TIẾT PHÒNG (CHÍNH XÁC: cập nhật cả tiền + khách) =====
$ctgdModel->updateBooking(
    $maGD,
    $maPhongCu,
    [
        'MaPhong'        => (int)$form['ma_phong'],
        'SoNguoi'        => (int)$form['so_nguoi'],
        'NgayNhanDuKien' => $ngayNhan,
        'NgayTraDuKien'  => $ngayTra,

        // cập nhật khách trong chitietgiaodich (nếu bảng có các cột này)
        'TenKhach' => $form['ten_kh'],
        'CCCD'     => $form['cccd'],
        'SDT'      => $form['sdt'],
        'Email'    => $form['email'],

        // cập nhật tiền dòng chi tiết
        'DonGia'   => $donGiaPhong,
        'ThanhTien'=> $thanhTienPhong
    ]
);

                    // đổi phòng thì cập nhật trạng thái phòng
                    if ((int)$form['ma_phong'] !== $maPhongCu) {
                        $phongModel->updateTrangThai($maPhongCu, 'Trong');
                        $phongModel->updateTrangThai((int)$form['ma_phong'], 'Booked');
                    }

                    // ===== UPDATE DỊCH VỤ (giữ nguyên hàm syncFromForm bạn đang có)
                    // Nếu syncFromForm của bạn đang dính ThanhTien/GiaBan → nó sẽ lỗi.
                    // Nhưng bạn đang dùng nó rồi, nên mình giữ nguyên (UC của bạn muốn cập nhật số lượng/xoá).
                    // Nếu nó đang lỗi, bạn gửi mình code syncFromForm hiện tại để mình fix đúng theo schema của bạn.
                    $ctdvModel->syncFromForm(
                        $maGD,
                        $_POST['services'] ?? [],
                        $_POST['remove_services'] ?? []
                    );

                   // ===== TÍNH TIỀN THEO NGHIỆP VỤ CHUẨN =====

// Tổng tiền phòng (chưa trừ KM)
$tongPhong = $tinhTongTienPhong_UC($maGD);

// Tổng tiền dịch vụ
$tongDV = $tinhTongTienDV_UC($maGD);

// 👉 TongTien trong giaodich = phòng + dịch vụ (KHÔNG TRỪ KM)
$tongTienGiaoDich = $tongPhong + $tongDV;
// ===== UPDATE THÀNH VIÊN (BẢNG KHACHHANG) =====
if (!empty($_POST['members']) && is_array($_POST['members'])) {
    foreach ($_POST['members'] as $m) {

        if (empty($m['id'])) {
            continue;
        }

        $maKH = (int)$m['id'];

        // validate
        if (!preg_match('/^[\p{L}\s]+$/u', $m['ten'])) {
            throw new Exception("Tên thành viên không hợp lệ");
        }

        if (!empty($m['cccd']) && !preg_match('/^\d{9,12}$/', $m['cccd'])) {
            throw new Exception("CCCD thành viên không hợp lệ");
        }

        if (!empty($m['sdt']) && !preg_match('/^0\d{8,10}$/', $m['sdt'])) {
            throw new Exception("SĐT thành viên không hợp lệ");
        }

        if (!empty($m['email']) && !filter_var($m['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email thành viên không hợp lệ");
        }

        $stmt = $db->prepare("
            UPDATE khachhang
            SET TenKH=?, CCCD=?, SDT=?, Email=?
            WHERE MaKhachHang=?
        ");

$tenTV   = (string)($m['ten'] ?? '');
$cccdTV  = (string)($m['cccd'] ?? '');
$sdtTV   = (string)($m['sdt'] ?? '');
$emailTV = (string)($m['email'] ?? '');
$maKH_i  = (int)$maKH;

$stmt = $db->prepare("
    UPDATE khachhang
    SET TenKH=?, CCCD=?, SDT=?, Email=?
    WHERE MaKhachHang=?
");

$stmt->bind_param(
    "ssssi",
    $tenTV,
    $cccdTV,
    $sdtTV,
    $emailTV,
    $maKH_i
);

$stmt->execute();

        $stmt->execute();
    }
}
// 👉 tiền giảm CHỈ để hiển thị
$tienGiam = $calcTienGiam($khuyenMai, $tongTienGiaoDich);

// 👉 thành tiền hiển thị (KHÔNG lưu DB)
$thanhTien = max(0, $tongTienGiaoDich - $tienGiam);
// ===== UPDATE THÀNH VIÊN (BẢNG KHACHHANG) =====

// ✅ CHỈ update TongTien (chưa trừ KM)
$gdModel->updateTongTien($maGD, $tongTienGiaoDich);
// ===== UPDATE THÀNH VIÊN =====

                    $db->commit();
                    $success = "Cập nhật đặt phòng thành công.";

                    // reload data để render lại view đúng
                    $giaoDich      = $gdModel->getById($maGD);
                    $chiTietPhong  = $ctgdModel->getPhongByGiaoDich($maGD);
                    $chiTietDichVu = $ctdvModel->getByGiaoDich($maGD);

                    // load danh sách phòng
                    $rs = $phongModel->searchAvailable();
                    while ($row = $rs->fetch_assoc()) $dsPhong[] = $row;

                    $listIds = array_map(fn($x) => (int)$x['MaPhong'], $dsPhong);
                    if (!in_array((int)$form['ma_phong'], $listIds, true)) {
                        $dsPhong[] = $phongModel->getById((int)$form['ma_phong']);
                    }

                } catch (Throwable $e) {
                    $db->rollback();
                    $errors[] = $e->getMessage();
                }
            } else {
                // nếu validate fail, vẫn tính tiền để view hiển thị (nếu có giao dịch)
                if ($giaoDich) {
                    $donGiaHienTai = $getDonGiaPhongTrongGD_UC($maGD, (int)$form['ma_phong']);
                    $tongPhong     = $tinhTongTienPhong_UC($maGD);
                    $tongDV        = $tinhTongTienDV_UC($maGD);
                    $tongTruocGiam = $tongPhong + $tongDV;
                    $tienGiam      = $calcTienGiam($khuyenMai, $tongTruocGiam);
                    $thanhTien     = max(0, $tongTruocGiam - $tienGiam);
                }
            }
        }
    }

    /**
     * ================= RENDER =================
     * Truyền đủ các biến tiền để view làm form giống UC đặt phòng
     */
    $this->view("letan/sua_dat_phong", [
        'errors'        => $errors,
        'success'       => $success,
        'giaoDich'      => $giaoDich,
        'chiTiet'       => $chiTietPhong,
        'chiTietDichVu' => $chiTietDichVu,
        'searchKeyword' => $searchKeyword,
        'form'          => $form,
        'dsPhong'       => $dsPhong,
        'dsKhuyenMai'   => $dsKhuyenMai,
        'thanhVien' => $thanhVien,

        // ✅ thêm data để view hiển thị
        'khuyenMai'     => $khuyenMai,
        'donGiaHienTai' => $donGiaHienTai,
        'tongPhong'     => $tongPhong,
        'tongDV'        => $tongDV,
        'tongTruocGiam' => $tongTruocGiam,
        'tienGiam'      => $tienGiam,
        'thanhTien'     => $thanhTien,
        'tongPhongKhac'     => $tongPhongKhac ?? 0,
        'tienPhongDangSua'  => $tienPhongDangSua ?? 0,
        'donGiaPhongDangSua'=> $donGia0 ?? 0,
    ]);

  } catch (Throwable $e) {

    http_response_code(500);

    echo "<pre style='color:red'>";
    echo "❌ MYSQL / PHP ERROR\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File   : " . $e->getFile() . "\n";
    echo "Line   : " . $e->getLine() . "\n";
    echo "</pre>";
    exit;
}
}
public function checkIn()
{
    $this->requireRole(['LeTan']);

    $gdModel    = new GiaoDich();
    $ctgdModel  = new ChiTietGiaoDich();
    $phongModel = new Phong();

    $errors  = [];
    $success = null;

    $giaoDich = null;
    $chiTiet  = [];
$danhSachThanhVien = [];
    $searchMaGD = $_POST['search_ma_gd'] ?? '';

    // Trưởng đoàn
    $tenTruongDoan  = '';
    $cmndTruongDoan = '';
    $soThanhVien    = 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $action = $_POST['btn_action'] ?? '';

        /* ------------------------------------------------------------
         * QUAY LẠI
         * ------------------------------------------------------------ */
        if ($action === 'back') {
            header("Location: index.php?controller=letan&action=index");
            exit;
        }

        /* ------------------------------------------------------------
         * 1) TÌM KIẾM GIAO DỊCH THEO MÃ GD HOẶC CCCD
         * ------------------------------------------------------------ */
        if ($action === 'search') {

            $keyword = trim($_POST['search_ma_gd'] ?? '');
            $searchMaGD = $keyword;

            if ($keyword === '') {
                $errors[] = "Vui lòng nhập mã giao dịch hoặc CMND/CCCD.";
            } elseif (!preg_match('/^\d+$/', $keyword)) {
                $errors[] = "Chỉ được nhập số.";
            }

            if (empty($errors)) {
                try {

                    // phân loại: ≤ 8 số → mã GD ; >= 9 số → CCCD
                    if (strlen($keyword) <= 8) {
                        $giaoDich = $gdModel->findByMaOrCCCD($keyword, null);
                    } else {
                        $giaoDich = $gdModel->findByMaOrCCCD(null, $keyword);
                    }

                } catch (\Throwable $ex) {
                    $errors[] = "Không thể truy vấn.";
                }
            }

            if (empty($errors)) {

                if (!$giaoDich) {
                    $errors[] = "Không tìm thấy giao dịch.";
                } else {

                    $maGD = (int)$giaoDich['MaGiaoDich'];
                    $chiTiet = $ctgdModel->getPhongByGiaoDich($maGD);
                    // ===== ĐẾM SỐ THÀNH VIÊN THEO GIAO DỊCH (ĐÚNG) =====
$db = Database::getConnection();
$stmt = $db->prepare("
    SELECT COUNT(*) AS Tong
    FROM khachhang
    WHERE MaGiaoDich = ?
");
$stmt->bind_param("i", $maGD);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

$soThanhVien = (int)($row['Tong'] ?? 0);

                    if (empty($chiTiet)) {
                        $errors[] = "Giao dịch không có phòng.";
                    } else {

                        // ======= ĐẾM TRẠNG THÁI =========
                        $countBooked = 0;
                        $countCancelled = 0;
                        $countStayed = 0;

                        foreach ($chiTiet as $ct) {
                            $st = $ct['TrangThai'];
                            if ($st === 'Booked') $countBooked++;
                            elseif ($st === 'Stayed') $countStayed++;
                            elseif ($st === 'DaHuy') $countCancelled++;
                        }

                        if ($countCancelled > 0 && $countBooked == 0 && $countStayed == 0) {
                            $errors[] = "Tất cả phòng đã bị hủy.";
                        }

                        if ($countStayed > 0 && $countBooked == 0) {
                            $errors[] = "Giao dịch đã check-in trước đó.";
                        }

                        // ================================
                        // LẤY TRƯỞNG ĐOÀN + ĐÚNG SỐ THÀNH VIÊN
                        // ================================
                        if (empty($errors)) {

                            // Lấy trưởng đoàn
                            $db = Database::getConnection();
                            $stmt = $db->prepare("SELECT * FROM khachhang WHERE MaKhachHang = ?");
                            $stmt->bind_param("i", $giaoDich['MaKhachHang']);
                            $stmt->execute();
                            $khach = $stmt->get_result()->fetch_assoc() ?: null;

                            $tenTruongDoan  = $khach['TenKH'] ?? '';
                            $cmndTruongDoan = $khach['CCCD'] ?? '';
                            // ===== LOAD DANH SÁCH THÀNH VIÊN TRONG GIAO DỊCH =====
$stmt = $db->prepare("
    SELECT 
        MaKhachHang,
        TenKH,
        CCCD,
        SDT,
        Email
    FROM khachhang
    WHERE MaGiaoDich = ?
    ORDER BY MaKhachHang
");
$stmt->bind_param("i", $maGD);
$stmt->execute();

$danhSachThanhVien = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                            // ==== FIX QUAN TRỌNG: TÍNH SỐ KHÁCH ====

                        }
                    }
                }
            }
        }

        /* ------------------------------------------------------------
         * 2) XÁC NHẬN CHECK-IN
         * ------------------------------------------------------------ */
        if ($action === 'confirm') {

            $maGiaoDich = (int)($_POST['ma_giao_dich'] ?? 0);
            $scope      = $_POST['check_scope'] ?? 'all';
            $xacNhanGT  = isset($_POST['xac_nhan_giay_to']);
            $searchMaGD = trim($_POST['search_ma_gd'] ?? '');

            if ($maGiaoDich <= 0) $errors[] = "Thiếu mã GD.";
            if (!$xacNhanGT) $errors[] = "Vui lòng xác nhận đã kiểm tra giấy tờ.";

            if (empty($errors)) {
                try {
                    $giaoDich = $gdModel->getById($maGiaoDich);
                    $chiTiet  = $ctgdModel->getPhongByGiaoDich($maGiaoDich);
                } catch (\Throwable $ex) {
                    $errors[] = "Không thể truy vấn.";
                }
            }

            if (empty($errors)) {

                if (empty($chiTiet)) {
                    $errors[] = "Không tìm thấy chi tiết phòng.";
                } else {

                    $countBooked = $countStayed = $countCancelled = 0;
                    foreach ($chiTiet as $ct) {
                        if ($ct['TrangThai'] === 'Booked') $countBooked++;
                        elseif ($ct['TrangThai'] === 'Stayed') $countStayed++;
                        elseif ($ct['TrangThai'] === 'DaHuy') $countCancelled++;
                    }

                    if ($countCancelled > 0 && $countBooked == 0) {
                        $errors[] = "Giao dịch đã bị hủy.";
                    }

                    if ($countStayed > 0 && $countBooked == 0) {
                        $errors[] = "Đã check-in rồi.";
                    }

                    // chọn phòng check-in
                    if ($scope === 'all') {
                        $selectedPhong = array_column($chiTiet, 'MaPhong');
                    } else {
                        $selectedPhong = array_map('intval', $_POST['phong_checkin'] ?? []);
                        if (empty($selectedPhong)) {
                            $errors[] = "Vui lòng chọn phòng.";
                        }
                    }
                }
            }

            /* ------------------------------------------------------------
             * KIỂM TRA NGÀY NHẬN PHÒNG + TRẠNG THÁI
             * ------------------------------------------------------------ */
            if (empty($errors)) {

                $today = new DateTime(date('Y-m-d'));

                $phongDuDieuKien = [];
                $phongSaiTrangThai = [];
                $phongTooSoon = [];
                $phongQuaHan = [];

                foreach ($chiTiet as $ct) {

                    $maPhong = (int)$ct['MaPhong'];
                    if (!in_array($maPhong, $selectedPhong, true)) continue;

                    if ($ct['TrangThai'] !== 'Booked') {
                        $phongSaiTrangThai[] = $ct['SoPhong'];
                        continue;
                    }

                    $ngayNhan = DateTime::createFromFormat('Y-m-d', substr($ct['NgayNhanDuKien'], 0, 10));
                    $diff = (int)$today->diff($ngayNhan)->format('%r%a');

                    if ($diff > 0) {
                        $phongTooSoon[] = $ct['SoPhong'];
                    } elseif ($diff < -1) {
                        $phongQuaHan[] = $ct['SoPhong'];
                    } else {
                        $phongDuDieuKien[] = $maPhong;
                    }
                }

                if (!empty($phongSaiTrangThai))
                    $errors[] = "Các phòng không ở trạng thái Booked: " . implode(', ', $phongSaiTrangThai);

                if (!empty($phongTooSoon))
                    $errors[] = "Chưa đến ngày nhận: " . implode(', ', $phongTooSoon);

                if (empty($phongDuDieuKien) && empty($errors))
                    $errors[] = "Không có phòng đủ điều kiện check-in.";
            }

            /* ------------------------------------------------------------
             * THỰC HIỆN CHECK-IN
             * ------------------------------------------------------------ */
            if (empty($errors)) {

                $db = Database::getConnection();
                $db->begin_transaction();

                try {

                    $now = date('Y-m-d H:i:s');

                    // cập nhật phòng
                    $sqlCT = "UPDATE chitietgiaodich SET TrangThai='Stayed'
                              WHERE MaGiaoDich=? AND MaPhong=? AND TrangThai='Booked'";
                    $stmtCT = $db->prepare($sqlCT);

                    foreach ($phongDuDieuKien as $roomID) {
                        $stmtCT->bind_param("ii", $maGiaoDich, $roomID);
                        $stmtCT->execute();
                        $phongModel->updateTrangThai($roomID, 'Stayed');
                    }

                    // cập nhật giao dịch
                    $sqlGD = "
                        UPDATE giaodich 
                        SET TrangThai='Stayed',
                            GhiChu = CONCAT(IFNULL(GhiChu,''), ' | Check-in {$now}')
                        WHERE MaGiaoDich=?
                    ";
                    $stmtGD = $db->prepare($sqlGD);
                    $stmtGD->bind_param("i", $maGiaoDich);
                    $stmtGD->execute();

                    $db->commit();

                    $success = "Check-in thành công.";

                    // reload lại dữ liệu
                    $giaoDich = $gdModel->getById($maGiaoDich);
                    $chiTiet  = $ctgdModel->getPhongByGiaoDich($maGiaoDich);

                    // TÍNH LẠI SỐ KHÁCH
                    $soThanhVien = 0;
                    foreach ($chiTiet as $ct) {
                        $soThanhVien += (int)$ct['SoNguoi'];
                    }

                } catch (\Throwable $ex) {
                    $db->rollback();
                    $errors[] = "Lỗi check-in: " . $ex->getMessage();
                }
            }
        }

        /* ------------------------------------------------------------
         * 3) HỦY CHECK-IN
         * ------------------------------------------------------------ */
        if ($action === 'abort') {
            $success = "Đã hủy thao tác.";
        }
    }

    // render view
    $this->view("letan/check_in", [
        'errors'         => $errors,
        'success'        => $success,
        'giaoDich'       => $giaoDich,
        'chiTiet'        => $chiTiet,
        'searchMaGD'     => $searchMaGD,
        'tenTruongDoan'  => $tenTruongDoan,
        'cmndTruongDoan' => $cmndTruongDoan,
        'soThanhVien'    => $soThanhVien,
        'danhSachThanhVien' => $danhSachThanhVien
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

                        $gia = (int)$dvRow['GiaDichVu'];      // LUÔN LÀ INT
$sl  = (int)$soLuong;

$thanhTien = $gia * $sl;              // -> luôn ra số nguyên
$tongTienThem += $thanhTien;          // -> cộng dồn nguyên

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
                                "iiiii",
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
                        $stmtTong->bind_param("ii", $tongTienThem, $maGiaoDich);
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
public function checkOutV2()
{
    $this->requireRole(['LeTan']);

    require_once __DIR__ . '/../models/GiaoDich.php';
    require_once __DIR__ . '/../models/Phong.php';
    require_once __DIR__ . '/../models/ChiTietGiaoDich.php';
    require_once __DIR__ . '/../models/ChiTietDichVu.php';
    require_once __DIR__ . '/../models/KhuyenMai.php';   // ★ THÊM

    $gdModel   = new GiaoDich();
    $pModel    = new Phong();
    $ctgdModel = new ChiTietGiaoDich();
    $ctdvModel = new ChiTietDichVu();
    $kmModel   = new KhuyenMai();  // ★ THÊM

    $errors = [];
    $success = null;

    $action = $_POST['btn_action'] ?? '';
    $giaoDich = null;
    $roomsStayed = [];
    $selectedRoomDetail = null;
    $selectedRoomServices = [];
// ===== FIX: KHAI BÁO MẶC ĐỊNH ĐỂ TRÁNH UNDEFINED =====
$soThanhVien = 0;
// tổng số người theo các phòng (SUM SoNguoi trong chitietgiaodich)
$tongNguoiOPhong = 0;

// helper: load số thành viên + tổng người theo phòng
$loadSoNguoi = function(int $maGD) use (&$soThanhVien, &$tongNguoiOPhong) {
    $db = Database::getConnection();

    // 1) Đếm thành viên theo khachhang
    $stmt = $db->prepare("SELECT COUNT(*) AS Tong FROM khachhang WHERE MaGiaoDich = ?");
    $stmt->bind_param("i", $maGD);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $soThanhVien = (int)($row['Tong'] ?? 0);

    // 2) Tổng số người theo phòng (SoNguoi của chitietgiaodich)
    $stmt2 = $db->prepare("SELECT COALESCE(SUM(SoNguoi),0) AS TongNguoi FROM chitietgiaodich WHERE MaGiaoDich = ?");
    $stmt2->bind_param("i", $maGD);
    $stmt2->execute();
    $row2 = $stmt2->get_result()->fetch_assoc();
    $tongNguoiOPhong = (int)($row2['TongNguoi'] ?? 0);
};
    $roomFee = 0;
    $serviceFee = 0;
    $damageFee = 0;
    $discountFee = 0;
    $khuyenMaiInfo = null; // ★ THÊM

    $searchKeyword = trim($_POST['search_keyword'] ?? '');
    $selectedRoomId = (int)($_POST['selected_room'] ?? 0);

    /* ============================================================
        1) TÌM KIẾM GIAO DỊCH
    ============================================================ */
    if ($action === "search") {

        if ($searchKeyword === '') {
            $errors[] = "Vui lòng nhập mã giao dịch hoặc CCCD.";
        } else {

            $giaoDich = null;

            // Tìm bằng CCCD 9–12 số
            if (preg_match('/^\d{9,12}$/', $searchKeyword)) {
                $giaoDich = $gdModel->findByMaOrCCCD(null, $searchKeyword);
            }
            // Tìm bằng mã giao dịch
            elseif (ctype_digit($searchKeyword)) {
                $giaoDich = $gdModel->getById((int)$searchKeyword);
            }
            else {
                $errors[] = "Chỉ được nhập số.";
            }

            if (!$giaoDich) {
                $errors[] = "Không tìm thấy giao dịch.";
            } else {
                $roomsStayed = $ctgdModel->co_getStayedRoomsByGiaoDich($giaoDich['MaGiaoDich']);
                // ===== FIX: load số thành viên & tổng người theo phòng ngay từ SEARCH =====
$loadSoNguoi((int)$giaoDich['MaGiaoDich']);
                if (empty($roomsStayed)) {
                    $errors[] = "Không có phòng Stayed.";
                }
            }
        }
    }

    /* ============================================================
        2) LOAD PHÒNG – GIỮ NGUYÊN LOGIC CŨ
    ============================================================ */
    if ($action === "load_room") {

        $maGD = (int)$_POST['ma_gd'];
        $selectedRoomId = (int)$_POST['selected_room'];

        $giaoDich = $gdModel->getById($maGD);
        $roomsStayed = $ctgdModel->co_getStayedRoomsByGiaoDich($maGD);
        // ===== FIX: ĐẾM SỐ THÀNH VIÊN ĐÚNG THEO GIAO DỊCH =====
$db = Database::getConnection();
$stmt = $db->prepare("
    SELECT COUNT(*) AS Tong
    FROM khachhang
    WHERE MaGiaoDich = ?
");
$stmt->bind_param("i", $maGD);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

$soThanhVien = (int)($row['Tong'] ?? 0);

        $selectedRoomDetail = $ctgdModel->co_getRoomDetail($maGD, $selectedRoomId);

        if ($selectedRoomDetail) {

            // GIỮ NGUYÊN — LẤY TIỀN PHÒNG
            // ===== FIX: TIỀN PHÒNG LẤY TỪ GIAO DỊCH (ĐÃ TÍNH KM) =====
// ===== FIX CHUẨN UC =====
// TongTien = tiền phòng + dịch vụ (đã tính rồi)
$roomFee = (float)$giaoDich['TongTien'];

// ❌ KHÔNG cộng lại dịch vụ
$serviceFee = 0;

// Chỉ load dịch vụ để HIỂN THỊ cho khách xem
$selectedRoomServices = $ctdvModel->co_getServicesByRoom($maGD, $selectedRoomId);

            // GIỮ NGUYÊN — BỒI THƯỜNG
            $damageFee = $pModel->co_calculateDamageFee($selectedRoomDetail['TinhTrangPhong']);

            // ★ THÊM — LẤY THÔNG TIN KM
            if (!empty($giaoDich['MaKhuyenMai'])) {

                $khuyenMaiInfo = $kmModel->getById((int)$giaoDich['MaKhuyenMai']);

                $discountFee = $kmModel->calculateDiscountById(
                    (int)$giaoDich['MaKhuyenMai'],
                    $roomFee + $serviceFee + $damageFee
                );
            }
        } 
        else {
            $errors[] = "Không tìm thấy thông tin phòng.";
        }
    }

    /* ============================================================
        3) CHECKOUT — GIỮ NGUYÊN + THÊM KHUYẾN MÃI
    ============================================================ */
    if ($action === 'checkout') {

        $maGD = (int)$_POST['ma_gd'];
        $maPhong = (int)$_POST['room_id'];

        $selectedRoomDetail = $ctgdModel->co_getRoomDetail($maGD, $maPhong);

        if (!$selectedRoomDetail) {
            $errors[] = "Không tìm thấy phòng.";
        } 
        else {

            // TIỀN PHÒNG (GIỮ NGUYÊN)
            // ===== FIX: TIỀN PHÒNG CHUẨN =====
$giao = $gdModel->getById($maGD);
$roomFee = (float)$giao['TongTien'];

            // DỊCH VỤ (GIỮ NGUYÊN)
            // ❌ KHÔNG CỘNG LẠI DỊCH VỤ
$serviceFee = 0;

// chỉ load để hiển thị
$services = $ctdvModel->co_getServicesByRoom($maGD, $maPhong);

            // BỒI THƯỜNG (GIỮ NGUYÊN)
            $damageFee = $pModel->co_calculateDamageFee($selectedRoomDetail['TinhTrangPhong']);

            // ★ THÊM — LẤY KM
            $giao = $gdModel->getById($maGD);
            if (!empty($giao['MaKhuyenMai'])) {

                $khuyenMaiInfo = $kmModel->getById((int)$giao['MaKhuyenMai']);

                $discountFee = $kmModel->calculateDiscountById(
                    (int)$giao['MaKhuyenMai'],
                    $roomFee + $serviceFee + $damageFee
                );
            }

            // ★ TÍNH TỔNG MỚI
            $total = $roomFee + $damageFee - $discountFee;

            // CHECKOUT (GIỮ NGUYÊN)
            $ctgdModel->co_checkoutRoom($maGD, $maPhong);

            // GHI CHÚ (THÊM KM)
            $ghiChu = "Checkout phòng {$selectedRoomDetail['SoPhong']} | "
                    . "Phòng: " . number_format($roomFee)
                    . " | DV: " . number_format($serviceFee)
                    . " | Bồi thường: " . number_format($damageFee)
                    . " | KM: -" . number_format($discountFee)
                    . " | Tổng: " . number_format($total);

            $gdModel->co_updateCheckoutStatus($maGD, $total, "TienMat", $ghiChu);

            $success = "Check-out phòng thành công!";
        }

        $giaoDich = $gdModel->getById($maGD);
        $roomsStayed = $ctgdModel->co_getStayedRoomsByGiaoDich($maGD);
        // ===== FIX: sau checkout vẫn phải load lại số thành viên & tổng người =====
$loadSoNguoi($maGD);
    }

    /* ============================================================
        4) TRẢ DỮ LIỆU RA VIEW
    ============================================================ */
    $this->view("letan/check_out_v2", [
        "errors" => $errors,
        "success" => $success,

        "giaoDich" => $giaoDich,
        "roomsStayed" => $roomsStayed,
"soThanhVien" => $soThanhVien,
        "selectedRoomId" => $selectedRoomId,
        "selectedRoomDetail" => $selectedRoomDetail,
        "selectedRoomServices" => $selectedRoomServices,
"tongNguoiOPhong" => $tongNguoiOPhong,
        "roomFee" => $roomFee,
        "serviceFee" => $serviceFee,
        "damageFee" => $damageFee,
        
        // ★ THÊM
        "discountFee" => $discountFee,
        "khuyenMaiInfo" => $khuyenMaiInfo,
    ]);
}   
public function datPhongTrucTiepV2()
{
    require_once "models/KhachHang.php";
    require_once "models/Phong.php";
    require_once "models/Doan.php";
    require_once "models/GiaoDich.php";
    require_once "models/ChiTietGiaoDich.php";
    require_once "models/DichVu.php";
    require_once "models/KhuyenMai.php";
    require_once "models/TaiKhoan.php";

    $khModel = new KhachHang();
    $phongM  = new Phong();
    $doanM   = new Doan();
    $gdM     = new GiaoDich();
    $ctgdM   = new ChiTietGiaoDich();
    $dvM     = new DichVu();
    $kmM     = new KhuyenMai();
    $tkModel = new TaiKhoan();

    $errors  = [];
    $success = null;
    $createdAccounts = [];

    // =========================
    // GIỮ GIÁ TRỊ FORM
    // =========================
    $ngayDen  = $_POST['ngay_den'] ?? '';
    $ngayDi   = $_POST['ngay_di'] ?? '';
    $soNguoi  = max(1, (int)($_POST['so_nguoi'] ?? 1));

    $soDem = null;
    if ($ngayDen && $ngayDi) {
        $diff = (strtotime($ngayDi) - strtotime($ngayDen)) / 86400;
        if ($diff > 0) $soDem = (int)$diff;
    }

    $dsPhong     = [];
    $dsDichVu    = $dvM->getActive();
    $dsKhuyenMai = $kmM->getActive();
    $hasSearch   = false;

    $btnAction = $_POST['btn_action'] ?? "";

    // =========================
    // 1) TÌM PHÒNG
    // =========================
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $btnAction === 'search') {

        if (!$ngayDen || !$ngayDi) {
            $errors[] = "Vui lòng chọn ngày đến – ngày đi.";
        } elseif ($ngayDen >= $ngayDi) {
            $errors[] = "Ngày đi phải lớn hơn ngày đến.";
        } elseif ($soNguoi <= 0) {
            $errors[] = "Số khách không hợp lệ.";
        }

        if (empty($errors)) {
            $dsPhong   = $phongM->v2_findAvailableForDirectBooking($ngayDen, $ngayDi, $soNguoi);
            $hasSearch = true;

            if (empty($dsPhong)) {
                $errors[] = "Không còn phòng phù hợp.";
            }
        }

        require "views/letan/dat_phong_truc_tiep_v2_premium.php";
        return;
    }

    // =========================
    // 2) ĐẶT PHÒNG
    // =========================
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $btnAction === 'book') {

        // ==== INPUT TRƯỞNG ĐOÀN ====
        $tenKH  = trim($_POST['leader_ten_kh'] ?? '');
        $cccd   = trim($_POST['leader_cccd'] ?? '');
        $sdt    = trim($_POST['leader_sdt'] ?? '');
        $email  = trim($_POST['leader_email'] ?? '');
        $diachi = trim($_POST['leader_diachi'] ?? '');
        $ghichu = trim($_POST['ghichu'] ?? '');

        $rooms      = $_POST['rooms'] ?? [];
        $services   = $_POST['services'] ?? [];
        $servicesRm = $_POST['services_room'] ?? [];
        $roomGuests = $_POST['room_guests'] ?? [];
        $maKM       = $_POST['ma_khuyen_mai'] ?? null;

        // ==== VALIDATE CƠ BẢN ====
        if ($tenKH === '') $errors[] = "Thiếu tên trưởng đoàn.";
        if (!preg_match('/^\d{9,12}$/', $cccd)) $errors[] = "CCCD trưởng đoàn không hợp lệ.";
        if ($sdt !== '' && !preg_match('/^0\d{8,10}$/', $sdt)) $errors[] = "SĐT trưởng đoàn không hợp lệ.";
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email trưởng đoàn không hợp lệ.";
        if (!$ngayDen || !$ngayDi || $ngayDen >= $ngayDi) $errors[] = "Ngày không hợp lệ.";
        if ($soDem === null || $soDem <= 0) $errors[] = "Số đêm không hợp lệ.";
        if (empty($rooms)) $errors[] = "Chưa chọn phòng.";

        // ==== VALIDATE: Dịch vụ có SL thì phải chọn phòng ====
        foreach ($services as $dvID => $sl) {
            $sl = (int)$sl;
            if ($sl <= 0) continue;
            $roomUse = (int)($servicesRm[$dvID] ?? 0);
            if ($roomUse <= 0) {
                $errors[] = "Dịch vụ #{$dvID} có số lượng > 0 phải chọn phòng sử dụng.";
            }
        }

        // ==== VALIDATE: CHECK TRÙNG CCCD TRONG DB (TRƯỞNG ĐOÀN) ====
        // YÊU CẦU CỦA BẠN: trùng CCCD trong DB => KHÔNG ĐẶT
        $existLeader = $khModel->searchByCCCDExact($cccd);
        if (!empty($existLeader)) {
            $errors[] = "CCCD trưởng đoàn đã tồn tại trong hệ thống. Không thể đặt phòng với CCCD này.";
        }

        // ==== VALIDATE: MEMBERS (trùng CCCD trong form + format) ====
        $usedCccds = [$cccd];
        foreach ($roomGuests as $roomId => $g) {

            $tenTV  = trim($g['TenKhach'] ?? '');
            $cccdTV = trim($g['CCCD'] ?? '');
            $sdtTV  = trim($g['SDT'] ?? '');
            $emailTV= trim($g['Email'] ?? '');

            // bỏ qua nếu trống hết (phòng dùng trưởng đoàn)
            if ($tenTV === '' && $cccdTV === '' && $sdtTV === '' && $emailTV === '') continue;

            if ($tenTV === '') $errors[] = "Thiếu họ tên thành viên (phòng {$roomId}).";
            if (!preg_match('/^\d{9,12}$/', $cccdTV)) $errors[] = "CCCD thành viên (phòng {$roomId}) không hợp lệ.";

            if ($cccdTV !== '') {
                if (in_array($cccdTV, $usedCccds, true)) {
                    $errors[] = "CCCD bị trùng trong form: {$cccdTV} (phòng {$roomId}).";
                } else {
                    $usedCccds[] = $cccdTV;
                }

                // CHECK TRÙNG CCCD TRONG DB (THÀNH VIÊN) => cũng chặn luôn cho chắc
                $existMember = $khModel->searchByCCCDExact($cccdTV);
                if (!empty($existMember)) {
                    $errors[] = "CCCD thành viên (phòng {$roomId}) đã tồn tại trong hệ thống: {$cccdTV}.";
                }
            }
        }

        if (!empty($errors)) {
            require "views/letan/dat_phong_truc_tiep_v2_premium.php";
            return;
        }

        // ==== TRANSACTION ====
        $db = Database::getConnection();
        $db->begin_transaction();

        try {

            // ===== 1) TẠO KHÁCH TRƯỞNG ĐOÀN (CHẮC CHẮN INSERT vì đã chặn trùng DB ở trên) =====
            $maKH = $khModel->create([
                'TenKH'     => $tenKH,
                'SDT'       => $sdt,
                'Email'     => $email,
                'CCCD'      => $cccd,
                'DiaChi'    => $diachi,
                'LoaiKhach' => 'Trưởng đoàn',
            ]);
            if (!$maKH) throw new Exception("Không thể tạo khách hàng trưởng đoàn.");

            // ===== 2) TẠO ĐOÀN =====
            $maDoan = $doanM->createV2([
                'TenDoan'       => "Đoàn của {$tenKH}",
                'MaTruongDoan'  => $maKH,
                'SoNguoi'       => $soNguoi,
                'GhiChu'        => $ghichu
            ]);
            if (!$maDoan) throw new Exception("Không thể tạo đoàn.");

            // PREFIX PHẢI CÓ NGAY SAU KHI CÓ $maDoan
            $prefix = "D" . str_pad($maDoan, 3, "0", STR_PAD_LEFT);

            // ===== 3) TẠO GIAO DỊCH =====
            $maGD = $gdM->createV2([
                'MaKhachHang'  => $maKH,
                'MaDoan'       => $maDoan,
                'NgayDen'      => $ngayDen,
                'NgayDi'       => $ngayDi,
                'SoNgay'       => $soDem,
                'MaKhuyenMai'  => $maKM,
                'GhiChu'       => $ghichu,
                'TongTien'     => 0
            ]);
            if (!$maGD) throw new Exception("Không thể tạo giao dịch.");
            // GÁN GIAO DỊCH CHO TRƯỞNG ĐOÀN
$db->query("
    UPDATE khachhang
    SET MaGiaoDich = {$maGD}
    WHERE MaKhachHang = {$maKH}
");

            // FIX TRẠNG THÁI: vì nhiều createV2 không nhận field TrangThai
            if (method_exists($gdM, 'updateTrangThai')) {
                $gdM->updateTrangThai($maGD, 'Booked');
            } else {
                // nếu bạn chưa có hàm updateTrangThai, bạn tự viết 1 câu SQL update trong model GiaoDich
                // throw new Exception("Thiếu hàm updateTrangThai() trong model GiaoDich.");
            }

            // ===== 4) CHI TIẾT PHÒNG =====
            $tongPhong = 0;
            foreach ($rooms as $phongID) {
                $p = $phongM->getById((int)$phongID);
                if (!$p) continue;

                $thanhTien = (float)$p['Gia'] * $soDem;
                $tongPhong += $thanhTien;

                $ctgdM->addRoomBookingV2([
                    'MaGiaoDich'     => $maGD,
                    'MaPhong'        => (int)$phongID,
                    'SoNguoi'        => $soNguoi,
                    'NgayNhanDuKien' => $ngayDen,
                    'NgayTraDuKien'  => $ngayDi,
                    'DonGia'         => $p['Gia'],
                    'ThanhTien'      => $thanhTien,
                    'TrangThai'      => 'Booked',
                    'TenKhach'       => $tenKH,
                    'CCCD'           => $cccd,
                    'SDT'            => $sdt,
                    'Email'          => $email,
                ]);
            }

            // ===== 5) DỊCH VỤ =====
            $tongDV = 0;
            foreach ($services as $dvID => $sl) {
                $sl = (int)$sl;
                if ($sl <= 0) continue;

                $dv = $dvM->getById((int)$dvID);
                if (!$dv) continue;

                $tt = (float)$dv['GiaDichVu'] * $sl;
                $tongDV += $tt;

                $ctgdM->addServiceV2([
                    'MaGiaoDich' => $maGD,
                    'MaPhong'    => (int)($servicesRm[$dvID] ?? 0),
                    'MaDichVu'   => (int)$dvID,
                    'SoLuong'    => $sl,
                    'GiaBan'     => $dv['GiaDichVu'],
                    'ThanhTien'  => $tt,
                ]);
            }

            // ===== 6) TÍNH TIỀN =====
            $tongTruocGiam = $tongPhong + $tongDV;
            $giam = 0;
            if ($maKM && $tongTruocGiam > 0) {
                $giam = $kmM->calculateDiscountV2((int)$maKM, $tongTruocGiam);
                $giam = min($giam, $tongTruocGiam);
            }
            $tongTien = max(0, $tongTruocGiam - $giam);
            $gdM->updateTongTien($maGD, $tongTien);

            // ===== 7) TẠO TÀI KHOẢN LEADER =====
            $leaderUser = "{$prefix}_Leader";
            $leaderPass = $tkModel->v2_randomPassword();

            // Nếu có check trùng username thì check ở đây
            // if ($tkModel->existsByUsername($leaderUser)) throw new Exception("Username đã tồn tại: {$leaderUser}");

            $tkModel->v2_createCustomerAccount([
                'MaKhachHang' => $maKH,
                'Username'    => $leaderUser,
                'Password'    => $leaderPass,
                'MaVaiTro'    => 7
            ]);

            $createdAccounts[] = [
                'hoTen'    => $tenKH,
                'username' => $leaderUser,
                'password' => $leaderPass,
                'vaiTro'   => 'Trưởng đoàn'
            ];

            // ===== 8) TẠO THÀNH VIÊN + TÀI KHOẢN =====
            $memberIndex = 1;
            foreach ($roomGuests as $roomId => $g) {

                $tenTV   = trim($g['TenKhach'] ?? '');
                $cccdTV  = trim($g['CCCD'] ?? '');
                $sdtTV   = trim($g['SDT'] ?? '');
                $emailTV = trim($g['Email'] ?? '');
                $dcTV    = trim($g['DiaChi'] ?? '');

                if ($tenTV === '' && $cccdTV === '' && $sdtTV === '' && $emailTV === '') continue;

                // chắc chắn insert vì đã chặn trùng CCCD DB trước đó
                $maTV = $khModel->create([
                    'TenKH'     => $tenTV,
                    'SDT'       => $sdtTV,
                    'Email'     => $emailTV,
                    'CCCD'      => $cccdTV,
                    'DiaChi'    => $dcTV,
                    'LoaiKhach' => 'Thành viên',
                ]);
                // GÁN GIAO DỊCH CHO THÀNH VIÊN
                if (!$maTV) throw new Exception("Không thể tạo thành viên (phòng {$roomId}).");
                $stmt = $db->prepare("UPDATE khachhang SET MaGiaoDich = ? WHERE MaKhachHang = ?");
$stmt->bind_param("ii", $maGD, $maTV);
$stmt->execute();

                $username = "{$prefix}_M{$memberIndex}";
                $password = $tkModel->v2_randomPassword();

                // if ($tkModel->existsByUsername($username)) throw new Exception("Username đã tồn tại: {$username}");

                $tkModel->v2_createCustomerAccount([
                    'MaKhachHang' => $maTV,
                    'Username'    => $username,
                    'Password'    => $password,
                    'MaVaiTro'    => 7
                ]);

                $createdAccounts[] = [
                    'hoTen'    => $tenTV,
                    'username' => $username,
                    'password' => $password,
                    'vaiTro'   => "Thành viên (phòng {$roomId})"
                ];

                $memberIndex++;
            }

            $db->commit();
            $success = "Đặt phòng thành công! Mã giao dịch #{$maGD}";

        } catch (Exception $e) {
            $db->rollback();
            $errors[] = "Lỗi hệ thống: " . $e->getMessage();
        }
    }

    require "views/letan/dat_phong_truc_tiep_v2_premium.php";
}
}
