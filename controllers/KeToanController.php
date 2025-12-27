<?php

class KetoanController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Bắt buộc đăng nhập + có vai trò kế toán (ví dụ MaVaiTro = 3 hoặc tên 'KeToan')
        $this->requireRole([3, 'KeToan', 'Kế toán']);
    }

    /**
     * Dashboard kế toán
     */
    public function dashboard()
    {
        $this->view('dashboard/ketoan');
    }

    /**
     * GET + POST cùng 1 action:
     * - GET  → hiển thị form tạo báo cáo
     * - POST → xử lý tạo báo cáo (HTML hoặc CSV)
     */
    public function baoCaoDoanhThuChiPhi()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';

        $model   = new BaoCaoKeToan();
        $error   = '';
        $success = '';
        $noData  = false;
        $dataBaoCao = null;
        $daGuiForm  = false;

        // Giá trị mặc định
        $input = [
            'loai_baocao' => 'doanhthu',
            'ky_han'      => 'thang',
            'tu_ngay'     => date('Y-m-01'),
            'den_ngay'    => date('Y-m-t'),
            'dinh_dang'   => 'html'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $daGuiForm = true;

            // Lấy dữ liệu từ form
            $input['loai_baocao'] = $_POST['loai_baocao'] ?? 'doanhthu';
            $input['ky_han']      = $_POST['ky_han'] ?? 'thang';
            $input['tu_ngay']     = $_POST['tu_ngay'] ?? '';
            $input['den_ngay']    = $_POST['den_ngay'] ?? '';
            $input['dinh_dang']   = $_POST['dinh_dang'] ?? 'html';

            // Validate cơ bản
            if ($input['loai_baocao'] === '' || $input['tu_ngay'] === '' || $input['den_ngay'] === '') {
                $error = "Vui lòng chọn đầy đủ loại báo cáo và khoảng thời gian.";
            } elseif ($input['tu_ngay'] > $input['den_ngay']) {
                $error = "Ngày bắt đầu không được lớn hơn ngày kết thúc.";
            } else {
                // === CSV EXPORT ===
                if ($input['dinh_dang'] === 'csv') {
                    $baoCao = $model->taoBaoCao(
                        $input['loai_baocao'],
                        $input['tu_ngay'],
                        $input['den_ngay']
                    );

                    $this->exportBaoCaoCsv($input['loai_baocao'], $baoCao, $input);
                    return; // đã exit ở trong hàm
                }

                // === XEM TRÊN HỆ THỐNG (HTML) ===
                $baoCao = $model->taoBaoCao(
                    $input['loai_baocao'],
                    $input['tu_ngay'],
                    $input['den_ngay']
                );

                // =============================
                // 🔥 FIX DUY NHẤT 1 CHỖ TẠI ĐÂY
                // =============================
                if ($input['loai_baocao'] === 'tonghop') {
                    $hasData =
                        !empty($baoCao['doanh_thu']['rows']) ||
                        !empty($baoCao['chi_phi']['rows']) ||
                        isset($baoCao['loi_nhuan']); // ⭐ THÊM DÒNG NÀY
                } else {
                    $hasData = !empty($baoCao['rows']);
                }
                // =============================

                if (!$hasData) {
                    $noData = true;
                    $success = '';
                } else {
                    $noData = false;
                    $success = "Tạo báo cáo thành công.";
                }

                $dataBaoCao = $baoCao;
            }
        }

        // Gửi sang view
        $this->view('ketoan/baocao', [
            'user'       => $this->user,
            'error'      => $error,
            'success'    => $success,
            'noData'     => $noData,
            'dataBaoCao' => $dataBaoCao,
            'input'      => $input,
            'daGuiForm'  => $daGuiForm
        ]);
    }

    /**
     * Xuất báo cáo dạng CSV
     * - Không dùng PDF để tránh lỗi thư viện
     */
    private function exportBaoCaoCsv(string $loaiBaoCao, array $baoCao, array $input): void
    {
        $fileName = "baocao_{$loaiBaoCao}_" . date('Ymd_His') . ".csv";

        header('Content-Type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=\"$fileName\"");

        $out = fopen('php://output', 'w');

        // Ghi BOM để Excel hiển thị tiếng Việt đúng
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

        // Ghi dòng tiêu đề chung
        fputcsv($out, ["BÁO CÁO " . strtoupper($loaiBaoCao)]);
        fputcsv($out, ["Từ ngày", $input['tu_ngay'], "Đến ngày", $input['den_ngay']]);
        fputcsv($out, []); // dòng trống

        if ($loaiBaoCao === 'doanhthu') {
            fputcsv($out, ["Ngày", "Số giao dịch", "Tổng doanh thu"]);

            foreach ($baoCao['rows'] as $r) {
                fputcsv($out, [
                    $r['Ngay'],
                    $r['SoGiaoDich'],
                    $r['TongDoanhThu']
                ]);
            }

            fputcsv($out, []);
            fputcsv($out, ["Tổng", $baoCao['tong']['so_gd'], $baoCao['tong']['doanh_thu']]);

        } elseif ($loaiBaoCao === 'chiphi') {
            fputcsv($out, ["Ngày", "Số phiếu chi", "Tổng chi phí"]);

            foreach ($baoCao['rows'] as $r) {
                fputcsv($out, [
                    $r['Ngay'],
                    $r['SoPhieuChi'],
                    $r['TongChiPhi']
                ]);
            }

            fputcsv($out, []);
            fputcsv($out, ["Tổng", $baoCao['tong']['so_phieu'], $baoCao['tong']['chi_phi']]);

        } elseif ($loaiBaoCao === 'tonghop') {

            fputcsv($out, ["--- Doanh thu ---"]);
            fputcsv($out, ["Ngày", "Số giao dịch", "Tổng doanh thu"]);

            foreach ($baoCao['doanh_thu']['rows'] as $r) {
                fputcsv($out, [
                    $r['Ngay'],
                    $r['SoGiaoDich'],
                    $r['TongDoanhThu']
                ]);
            }

            fputcsv($out, []);
            fputcsv($out, ["Tổng", $baoCao['doanh_thu']['tong']['so_gd'], $baoCao['doanh_thu']['tong']['doanh_thu']]);

            fputcsv($out, []);
            fputcsv($out, ["--- Chi phí ---"]);
            fputcsv($out, ["Ngày", "Số phiếu chi", "Tổng chi phí"]);

            foreach ($baoCao['chi_phi']['rows'] as $r) {
                fputcsv($out, [
                    $r['Ngay'],
                    $r['SoPhieuChi'],
                    $r['TongChiPhi']
                ]);
            }

            fputcsv($out, []);
            fputcsv($out, ["Tổng", $baoCao['chi_phi']['tong']['so_phieu'], $baoCao['chi_phi']['tong']['chi_phi']]);

            fputcsv($out, []);
            fputcsv($out, ["Lợi nhuận", $baoCao['loi_nhuan']]);
        }

        fclose($out);
        exit;
    }
    
    public function quanLyDoanhThu()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';

        $model = new BaoCaoKeToan();
        $error = '';
        $message = '';
        
        // Giá trị mặc định bộ lọc (90 ngày gần nhất)
        $filters = [
            'tu_ngay'      => $_GET['tu_ngay'] ?? date('Y-m-d', strtotime('-90 days')),
            'den_ngay'     => $_GET['den_ngay'] ?? date('Y-m-d'),
            'trang_thai'   => $_GET['trang_thai'] ?? 'all',
            'search'       => $_GET['search'] ?? '',
            'page'         => (int)($_GET['page'] ?? 1)
        ];

        $page = max(1, $filters['page']);
        $limit = 15;
        $offset = ($page - 1) * $limit;

        // Lấy danh sách giao dịch
        $result = $model->layDanhSachGiaoDich(
            $filters['tu_ngay'],
            $filters['den_ngay'],
            $filters['trang_thai'],
            $filters['search'],
            $limit,
            $offset
        );

        // Tính tổng số trang
        $totalRecords = $result['total'];
        $totalPages = ceil($totalRecords / $limit);

        // Tính tổng doanh thu
        $tongDoanhThu = $model->tinhTongDoanhThu(
            $filters['tu_ngay'],
            $filters['den_ngay'],
            $filters['trang_thai'],
            $filters['search']
        );

        // Gửi sang view
        $this->view('ketoan/quanLyDoanhThu', [
            'user'           => $this->user,
            'error'          => $error,
            'message'        => $message,
            'danhSachGiaoDich' => $result['rows'],
            'filters'        => $filters,
            'tongDoanhThu'   => $tongDoanhThu,
            'currentPage'    => $page,
            'totalPages'     => $totalPages,
            'totalRecords'   => $totalRecords
        ]);
    }

    /**
     * Sửa doanh thu (giao dịch)
     * GET:  hiển thị form sửa doanh thu
     * POST: xử lý sửa doanh thu
     */
    public function suaDoanhThu()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';

        $model = new BaoCaoKeToan();
        $error = '';
        $success = '';

        $maGiaoDich = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($maGiaoDich <= 0) {
            $error = "ID giao dịch không hợp lệ.";
            $this->view('ketoan/suaDoanhThu', [
                'user'  => $this->user,
                'error' => $error
            ]);
            return;
        }

        // Lấy thông tin giao dịch
        $giaoDich = $model->layGiaoDichById($maGiaoDich);

        if (!$giaoDich) {
            $error = "Không tìm thấy giao dịch với ID này.";
            $this->view('ketoan/suaDoanhThu', [
                'user'  => $this->user,
                'error' => $error
            ]);
            return;
        }

        $input = $giaoDich;
        
        // Lấy lịch sử sửa
        $lichSu = $model->layLichSuSuaGiaoDich($maGiaoDich);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input['TongTien']              = $_POST['TongTien'] ?? '';
            $input['TrangThai']             = $_POST['TrangThai'] ?? '';
            $input['PhuongThucThanhToan']   = $_POST['PhuongThucThanhToan'] ?? '';
            $input['GhiChu']                = $_POST['GhiChu'] ?? '';
            $lySua                          = $_POST['LySua'] ?? '';

            // Validate
            if (empty($input['TongTien']) || !is_numeric($input['TongTien']) || (float)$input['TongTien'] < 0) {
                $error = "Số tiền phải là số hợp lệ.";
            } elseif (empty($input['TrangThai'])) {
                $error = "Vui lòng chọn trạng thái.";
            } elseif (empty($lySua)) {
                $error = "Vui lòng nhập lý do sửa.";
            } else {
                // Cập nhật doanh thu
                if ($model->suaDoanhThu($maGiaoDich, $input['TongTien'], $input['TrangThai'], $input['PhuongThucThanhToan'], $input['GhiChu'])) {
                    // Ghi nhận lịch sử sửa
                    $model->ghiNhatLichSuSua(
                        $maGiaoDich,
                        $this->user['MaTK'] ?? 0,
                        (float)$giaoDich['TongTien'],
                        $giaoDich['TrangThai'],
                        $giaoDich['PhuongThucThanhToan'],
                        $giaoDich['GhiChu'],
                        (float)$input['TongTien'],
                        $input['TrangThai'],
                        $input['PhuongThucThanhToan'],
                        $input['GhiChu'],
                        $lySua
                    );
                    $success = "Cập nhật doanh thu thành công! (Lịch sử sửa đã được ghi nhận)";
                } else {
                    $error = "Lỗi khi cập nhật doanh thu. Vui lòng thử lại.";
                }
            }
        }

        $this->view('ketoan/suaDoanhThu', [
            'user'        => $this->user,
            'error'       => $error,
            'success'     => $success,
            'input'       => $input,
            'maGiaoDich'  => $maGiaoDich,
            'lichSu'      => $lichSu
        ]);
    }

    /**
     * Quản lý chi phí - xem danh sách chi phí
     * GET:  hiển thị danh sách chi phí với bộ lọc
     * POST: tìm kiếm, lọc theo trạng thái, ngày
     */
    public function quanLyChiPhi()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';

        $model = new BaoCaoKeToan();
        $error = '';
        $message = '';

        // Giá trị mặc định bộ lọc (90 ngày gần nhất)
        $filters = [
            'tu_ngay'      => $_GET['tu_ngay'] ?? date('Y-m-d', strtotime('-90 days')),
            'den_ngay'     => $_GET['den_ngay'] ?? date('Y-m-d'),
            'trang_thai'   => $_GET['trang_thai'] ?? 'all',
            'search'       => $_GET['search'] ?? '',
            'page'         => (int)($_GET['page'] ?? 1)
        ];

        $page = max(1, $filters['page']);
        $limit = 15;
        $offset = ($page - 1) * $limit;

        // Lấy danh sách chi phí
        $result = $model->layDanhSachChiPhi(
            $filters['tu_ngay'],
            $filters['den_ngay'],
            $filters['trang_thai'],
            $filters['search'],
            $limit,
            $offset
        );

        // Tính tổng số trang
        $totalRecords = $result['total'];
        $totalPages = ceil($totalRecords / $limit);

        // Tính tổng chi phí
        $tongChiPhi = $model->tinhTongChiPhi(
            $filters['tu_ngay'],
            $filters['den_ngay'],
            $filters['trang_thai'],
            $filters['search']
        );

        // Gửi sang view
        $this->view('ketoan/quanLyChiPhi', [
            'user'        => $this->user,
            'error'       => $error,
            'message'     => $message,
            'danhSachChiPhi' => $result['rows'],
            'filters'     => $filters,
            'tongChiPhi'  => $tongChiPhi,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'totalRecords' => $totalRecords
        ]);
    }

    /**
     * Tổng quan - hiển thị biểu đồ và thống kê tổng hợp
     */
    public function tongQuan()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';

        $model = new BaoCaoKeToan();
        $error = '';
        $message = '';

        // Xử lý lọc từ form
        $filterType = isset($_GET['filterType']) ? $_GET['filterType'] : '30days';
        $filterYear = isset($_GET['filterYear']) ? (int)$_GET['filterYear'] : date('Y');
        $filterMonth = isset($_GET['filterMonth']) ? (int)$_GET['filterMonth'] : date('m');
        $filterDay = isset($_GET['filterDay']) ? (int)$_GET['filterDay'] : date('d');
        $tuNgay = isset($_GET['tuNgay']) ? $_GET['tuNgay'] : '';
        $denNgay = isset($_GET['denNgay']) ? $_GET['denNgay'] : '';

        // Xác định khoảng ngày dựa trên filter type
        switch ($filterType) {
            case '7days':
                $tuNgay = date('Y-m-d', strtotime('-7 days'));
                $denNgay = date('Y-m-d');
                break;
            case '30days':
                $tuNgay = date('Y-m-d', strtotime('-30 days'));
                $denNgay = date('Y-m-d');
                break;
            case '90days':
                $tuNgay = date('Y-m-d', strtotime('-90 days'));
                $denNgay = date('Y-m-d');
                break;
            case 'year':
                $tuNgay = $filterYear . '-01-01';
                $denNgay = $filterYear . '-12-31';
                break;
            case 'month':
                $tuNgay = $filterYear . '-' . str_pad($filterMonth, 2, '0', STR_PAD_LEFT) . '-01';
                $denNgay = date('Y-m-t', strtotime($tuNgay));
                break;
            case 'custom':
                if (empty($tuNgay) || empty($denNgay)) {
                    $tuNgay = date('Y-m-d', strtotime('-30 days'));
                    $denNgay = date('Y-m-d');
                    $filterType = '30days';
                }
                break;
            default:
                $tuNgay = date('Y-m-d', strtotime('-30 days'));
                $denNgay = date('Y-m-d');
                $filterType = '30days';
        }

        // Doanh thu theo ngày
        $doanhThuTheoNgay = $model->doanhThuTheoNgay($tuNgay, $denNgay);

        // Doanh thu theo phòng
        $doanhThuTheoPhong = $model->doanhThuTheoPhong($tuNgay, $denNgay);

        // Chi phí theo loại
        $chiPhiTheoLoai = $model->chiPhiTheoLoai($tuNgay, $denNgay);

        // Tính tổng hợp
        $tongDoanhThu = 0;
        $tongChiPhi = 0;

        foreach ($doanhThuTheoNgay as $item) {
            $tongDoanhThu += (float)$item['TongDoanhThu'];
        }

        foreach ($chiPhiTheoLoai as $item) {
            $tongChiPhi += (float)$item['TongChiPhi'];
        }

        $loiNhuan = $tongDoanhThu - $tongChiPhi;

        // Gửi sang view
        $this->view('ketoan/tongQuan', [
            'user'                => $this->user,
            'error'               => $error,
            'message'             => $message,
            'doanhThuTheoNgay'    => $doanhThuTheoNgay,
            'doanhThuTheoPhong'   => $doanhThuTheoPhong,
            'chiPhiTheoLoai'      => $chiPhiTheoLoai,
            'tongDoanhThu'        => $tongDoanhThu,
            'tongChiPhi'          => $tongChiPhi,
            'loiNhuan'            => $loiNhuan,
            'tuNgay'              => $tuNgay,
            'denNgay'             => $denNgay,
            'filterType'          => $filterType,
            'filterYear'          => $filterYear,
            'filterMonth'         => $filterMonth,
            'filterDay'           => $filterDay
        ]);
    }

    /**
     * Thêm chi phí
     * GET:  hiển thị form thêm chi phí
     * POST: xử lý thêm chi phí mới
     */
    public function themChiPhi()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';

        $model = new BaoCaoKeToan();
        $error = '';
        $success = '';

        $input = [
            'TenChiPhi' => '',
            'NgayChi'   => date('Y-m-d'),
            'SoTien'    => '',
            'NoiDung'   => '',
            'TrangThai' => 'ChoDuyet'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input['TenChiPhi'] = $_POST['TenChiPhi'] ?? '';
            $input['NgayChi']   = $_POST['NgayChi'] ?? date('Y-m-d');
            $input['SoTien']    = $_POST['SoTien'] ?? '';
            $input['NoiDung']   = $_POST['NoiDung'] ?? '';
            $input['TrangThai'] = $_POST['TrangThai'] ?? 'ChoDuyet';

            // Validate
            if (empty($input['TenChiPhi'])) {
                $error = "Vui lòng nhập tên chi phí.";
            } elseif (empty($input['SoTien']) || !is_numeric($input['SoTien']) || (float)$input['SoTien'] <= 0) {
                $error = "Số tiền phải lớn hơn 0.";
            } elseif (empty($input['NgayChi'])) {
                $error = "Vui lòng chọn ngày chi.";
            } else {
                // Thêm chi phí vào database
                if ($model->themChiPhi($input['TenChiPhi'], $input['NgayChi'], $input['SoTien'], $input['NoiDung'], $input['TrangThai'])) {
                    $success = "Thêm chi phí thành công!";
                    // Reset form
                    $input = [
                        'TenChiPhi' => '',
                        'NgayChi'   => date('Y-m-d'),
                        'SoTien'    => '',
                        'NoiDung'   => '',
                        'TrangThai' => 'ChoDuyet'
                    ];
                } else {
                    $error = "Lỗi khi thêm chi phí. Vui lòng thử lại.";
                }
            }
        }

        $this->view('ketoan/themChiPhi', [
            'user'    => $this->user,
            'error'   => $error,
            'success' => $success,
            'input'   => $input
        ]);
    }

    /**
     * Sửa chi phí
     * GET:  hiển thị form sửa chi phí
     * POST: xử lý sửa chi phí
     */
    public function suaChiPhi()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';

        $model = new BaoCaoKeToan();
        $error = '';
        $success = '';

        $maCP = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($maCP <= 0) {
            $error = "ID chi phí không hợp lệ.";
            $this->view('ketoan/suaChiPhi', [
                'user'  => $this->user,
                'error' => $error
            ]);
            return;
        }

        // Lấy thông tin chi phí
        $chiPhi = $model->layChiPhiById($maCP);

        if (!$chiPhi) {
            $error = "Không tìm thấy chi phí với ID này.";
            $this->view('ketoan/suaChiPhi', [
                'user'  => $this->user,
                'error' => $error
            ]);
            return;
        }

        $input = $chiPhi;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input['TenChiPhi'] = $_POST['TenChiPhi'] ?? '';
            $input['NgayChi']   = $_POST['NgayChi'] ?? '';
            $input['SoTien']    = $_POST['SoTien'] ?? '';
            $input['NoiDung']   = $_POST['NoiDung'] ?? '';
            $input['TrangThai'] = $_POST['TrangThai'] ?? 'ChoDuyet';

            // Validate
            if (empty($input['TenChiPhi'])) {
                $error = "Vui lòng nhập tên chi phí.";
            } elseif (empty($input['SoTien']) || !is_numeric($input['SoTien']) || (float)$input['SoTien'] <= 0) {
                $error = "Số tiền phải lớn hơn 0.";
            } elseif (empty($input['NgayChi'])) {
                $error = "Vui lòng chọn ngày chi.";
            } else {
                // Cập nhật chi phí
                if ($model->suaChiPhi($maCP, $input['TenChiPhi'], $input['NgayChi'], $input['SoTien'], $input['NoiDung'], $input['TrangThai'])) {
                    $success = "Cập nhật chi phí thành công!";
                } else {
                    $error = "Lỗi khi cập nhật chi phí. Vui lòng thử lại.";
                }
            }
        }

        $this->view('ketoan/suaChiPhi', [
            'user'    => $this->user,
            'error'   => $error,
            'success' => $success,
            'input'   => $input,
            'maCP'    => $maCP
        ]);
    }

    /**
     * Xóa chi phí - Bước 1 & 2: Xác nhận 2 bước
     * DELETE chi phí với token xác nhận
     */
    public function xoaChiPhi()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';

        $model = new BaoCaoKeToan();

        $maCP = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
        $token = isset($_GET['token']) ? $_GET['token'] : '';

        if ($maCP <= 0) {
            header('Location: index.php?controller=ketoan&action=quanLyChiPhi&error=ID không hợp lệ');
            exit;
        }

        // Lấy thông tin chi phí
        $chiPhi = $model->layChiPhiById($maCP);

        if (!$chiPhi) {
            header('Location: index.php?controller=ketoan&action=quanLyChiPhi&error=Không tìm thấy chi phí');
            exit;
        }

        // Bước 1: Hiển thị xác nhận lần 1
        if ($step === 1) {
            // Tạo token xác nhận
            $token = md5($maCP . time() . uniqid());
            $_SESSION['delete_token_' . $maCP] = $token;
            $_SESSION['delete_token_time_' . $maCP] = time();

            $this->view('ketoan/xacnhanXoaChiPhi', [
                'user'   => $this->user,
                'chiPhi' => $chiPhi,
                'maCP'   => $maCP,
                'step'   => 1,
                'token'  => $token
            ]);
            return;
        }

        // Bước 2: Xác nhận lần 2
        if ($step === 2) {
            // Kiểm tra token
            $storedToken = $_SESSION['delete_token_' . $maCP] ?? null;
            $tokenTime = $_SESSION['delete_token_time_' . $maCP] ?? 0;

            // Token hết hạn sau 10 phút
            if (!$storedToken || $token !== $storedToken || (time() - $tokenTime > 600)) {
                header('Location: index.php?controller=ketoan&action=quanLyChiPhi&error=Yêu cầu hết hạn. Vui lòng thử lại');
                exit;
            }

            // Lấy ghi chú hủy từ POST
            $ghiChuHuy = $_POST['GhiChuHuy'] ?? 'Hủy chi phí';

            // Hủy chi phí (soft delete - không xóa vĩnh viễn)
            if ($model->HuyChiPhi($maCP, $ghiChuHuy)) {
                // Xóa token
                unset($_SESSION['delete_token_' . $maCP]);
                unset($_SESSION['delete_token_time_' . $maCP]);

                header('Location: index.php?controller=ketoan&action=quanLyChiPhi&success=Hủy chi phí thành công (chi phí đã được đánh dấu là Hủy)');
                exit;
            } else {
                header('Location: index.php?controller=ketoan&action=quanLyChiPhi&error=Lỗi khi hủy chi phí');
                exit;
            }
        }

        // Mặc định: hiển thị bước 1
        header('Location: index.php?controller=ketoan&action=xoaChiPhi&id=' . $maCP . '&step=1');
        exit;
    }

    /**
     * Kiểm toán đêm - Danh sách
     */
    public function kiemToanDem()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';

        $model = new BaoCaoKeToan();
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $danhSach = $model->layDanhSachKiemToanDem($limit, $offset);
        $total = $model->countKiemToanDem();
        $totalPages = ceil($total / $limit);

        $this->view('ketoan/kiemToanDem', [
            'user' => $this->user,
            'danhSach' => $danhSach,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total
        ]);
    }

    /**
     * Tạo kiểm toán đêm mới
     */
    public function taoKiemToanDem()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';

        $model = new BaoCaoKeToan();
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = [
                'ngay' => $_POST['ngay'] ?? date('Y-m-d'),
                'so_du_dau_ngay' => $_POST['so_du_dau_ngay'] ?? '0',
                'so_du_cuoi_ngay' => $_POST['so_du_cuoi_ngay'] ?? '0',
                'tong_doanh_thu' => $_POST['tong_doanh_thu'] ?? '0',
                'tong_chi_phi' => $_POST['tong_chi_phi'] ?? '0',
                'ghi_chu' => $_POST['ghi_chu'] ?? ''
            ];

            // Validate
            if (empty($input['ngay'])) {
                $error = "Vui lòng chọn ngày kiểm toán.";
            } elseif (!is_numeric($input['so_du_dau_ngay']) || !is_numeric($input['so_du_cuoi_ngay']) ||
                      !is_numeric($input['tong_doanh_thu']) || !is_numeric($input['tong_chi_phi'])) {
                $error = "Các trường số tiền phải là số hợp lệ.";
            } else {
                // Tính lợi nhuận
                $loiNhuan = floatval($input['tong_doanh_thu']) - floatval($input['tong_chi_phi']);

                // Thêm kiểm toán đêm
                if ($model->themKiemToanDem(
                    $input['ngay'],
                    $input['so_du_dau_ngay'],
                    $input['so_du_cuoi_ngay'],
                    $input['tong_doanh_thu'],
                    $input['tong_chi_phi'],
                    (string)$loiNhuan,
                    $input['ghi_chu'],
                    $this->user['MaTK'] ?? 0
                )) {
                    header('Location: index.php?controller=ketoan&action=kiemToanDem&success=Tạo kiểm toán đêm thành công');
                    exit;
                } else {
                    $error = "Lỗi khi tạo kiểm toán đêm. Ngày này có thể đã được kiểm toán.";
                }
            }
        }

        // Lấy dữ liệu cho ngày hôm nay
        $ngayHom = date('Y-m-d');
        $tongDoanhThu = $model->tinhTongDoanhThuHom($ngayHom);
        $tongChiPhi = $model->tinhTongChiPhiHom($ngayHom);

        $this->view('ketoan/taoKiemToanDem', [
            'user' => $this->user,
            'error' => $error,
            'success' => $success,
            'tongDoanhThu' => $tongDoanhThu,
            'tongChiPhi' => $tongChiPhi,
            'ngayHom' => $ngayHom
        ]);
    }

    /**
     * Sửa kiểm toán đêm
     */
    public function suaKiemToanDem()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';

        $model = new BaoCaoKeToan();
        $maKTD = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $kiemToan = $model->layKiemToanDemById($maKTD);

        if (!$kiemToan) {
            header('Location: index.php?controller=ketoan&action=kiemToanDem&error=Kiểm toán không tồn tại');
            exit;
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = [
                'so_du_dau_ngay' => $_POST['so_du_dau_ngay'] ?? '0',
                'so_du_cuoi_ngay' => $_POST['so_du_cuoi_ngay'] ?? '0',
                'tong_doanh_thu' => $_POST['tong_doanh_thu'] ?? '0',
                'tong_chi_phi' => $_POST['tong_chi_phi'] ?? '0',
                'ghi_chu' => $_POST['ghi_chu'] ?? ''
            ];

            // Validate
            if (!is_numeric($input['so_du_dau_ngay']) || !is_numeric($input['so_du_cuoi_ngay']) ||
                !is_numeric($input['tong_doanh_thu']) || !is_numeric($input['tong_chi_phi'])) {
                $error = "Các trường số tiền phải là số hợp lệ.";
            } else {
                // Tính lợi nhuận
                $loiNhuan = floatval($input['tong_doanh_thu']) - floatval($input['tong_chi_phi']);

                // Cập nhật kiểm toán đêm
                if ($model->suaKiemToanDem(
                    $maKTD,
                    $input['so_du_dau_ngay'],
                    $input['so_du_cuoi_ngay'],
                    $input['tong_doanh_thu'],
                    $input['tong_chi_phi'],
                    (string)$loiNhuan,
                    $input['ghi_chu']
                )) {
                    header('Location: index.php?controller=ketoan&action=kiemToanDem&success=Cập nhật kiểm toán đêm thành công');
                    exit;
                } else {
                    $error = "Lỗi khi cập nhật kiểm toán đêm.";
                }
            }
        }

        $this->view('ketoan/suaKiemToanDem', [
            'user' => $this->user,
            'error' => $error,
            'kiemToan' => $kiemToan
        ]);
    }

    /**
     * Xem chi tiết kiểm toán đêm
     */
    public function chiTietKiemToanDem()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';

        $model = new BaoCaoKeToan();
        $maKTD = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $kiemToan = $model->layKiemToanDemById($maKTD);

        if (!$kiemToan) {
            header('Location: index.php?controller=ketoan&action=kiemToanDem&error=Kiểm toán không tồn tại');
            exit;
        }

        $this->view('ketoan/chiTietKiemToanDem', [
            'user' => $this->user,
            'kiemToan' => $kiemToan
        ]);
    }

    /**
     * Xóa kiểm toán đêm
     */
    public function xoaKiemToanDem()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';

        $model = new BaoCaoKeToan();
        $maKTD = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($maKTD > 0) {
            if ($model->xoaKiemToanDem($maKTD)) {
                header('Location: index.php?controller=ketoan&action=kiemToanDem&success=Xóa kiểm toán đêm thành công');
            } else {
                header('Location: index.php?controller=ketoan&action=kiemToanDem&error=Lỗi khi xóa kiểm toán đêm');
            }
        } else {
            header('Location: index.php?controller=ketoan&action=kiemToanDem&error=ID kiểm toán không hợp lệ');
        }
        exit;
    }

    /**
     * Tách loại doanh thu - Phân tích chuẩn kế toán
     */
    public function tachDoanhThu()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';

        $model = new BaoCaoKeToan();
        $tuNgay = $_GET['tu_ngay'] ?? date('Y-m-01');
        $denNgay = $_GET['den_ngay'] ?? date('Y-m-t');

        // Lấy doanh thu theo loại
        $doanhThuTheoLoai = $model->doanhThuTheoLoai($tuNgay, $denNgay);
        
        // Lấy doanh thu phòng theo ngày lưu trú
        $doanhThuPhong = $model->doanhThuPhongTheoNgayLuuTru($tuNgay, $denNgay);
        
        // Lấy doanh thu dịch vụ theo ngày phát sinh
        $doanhThuDichVu = $model->doanhThuDichVuTheoNgayPhatSinh($tuNgay, $denNgay);

        $this->view('ketoan/tachDoanhThu', [
            'user' => $this->user,
            'tuNgay' => $tuNgay,
            'denNgay' => $denNgay,
            'doanhThuTheoLoai' => $doanhThuTheoLoai,
            'doanhThuPhong' => $doanhThuPhong,
            'doanhThuDichVu' => $doanhThuDichVu
        ]);
    }

    /**
     * Lịch sử sửa giao dịch (Audit Log)
     */
    public function lichSuSuaGiaoDich()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';

        $model = new BaoCaoKeToan();
        $tuNgay = $_GET['tu_ngay'] ?? date('Y-m-01');
        $denNgay = $_GET['den_ngay'] ?? date('Y-m-t');

        // Lấy thống kê giao dịch bị sửa
        $thongKe = $model->thongKeGiaoDichBiSua($tuNgay, $denNgay);

        $this->view('ketoan/lichSuSuaGiaoDich', [
            'user' => $this->user,
            'tuNgay' => $tuNgay,
            'denNgay' => $denNgay,
            'thongKe' => $thongKe
        ]);
    }

    /**
     * Tạo/Sửa chi phí với loại chi phí và phân bổ
     */
    public function quanLyChiPhiNangCao()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';

        $model = new BaoCaoKeToan();
        $error = '';
        $success = '';
        $input = [];

        $maCP = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $action = $maCP > 0 ? 'Sửa' : 'Tạo';

        // Lấy danh sách loại chi phí
        $danhSachLoaiChiPhi = $model->layDanhSachLoaiChiPhi(true);

        if ($maCP > 0) {
            // Mode Sửa
            $chiPhi = $model->layChiPhiById($maCP);
            if (!$chiPhi) {
                $error = "Không tìm thấy chi phí này.";
            } else {
                $input = $chiPhi;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input['TenChiPhi']         = $_POST['TenChiPhi'] ?? '';
            $input['MaLoaiChiPhi']      = $_POST['MaLoaiChiPhi'] ?? 0;
            $input['SoTien']            = $_POST['SoTien'] ?? 0;
            $input['NgayChi']           = $_POST['NgayChi'] ?? '';
            $input['TrangThai']         = $_POST['TrangThai'] ?? 'ChoDuyet';

            // Phân bổ
            $loaiPhanBo  = $_POST['LoaiPhanBo'] ?? '';
            $maPhong     = !empty($_POST['MaPhong']) ? (int)$_POST['MaPhong'] : null;
            $maBoPhap    = !empty($_POST['MaBoPhap']) ? (int)$_POST['MaBoPhap'] : null;
            $thoiGianTu  = $_POST['ThoiGianTu'] ?? null;
            $thoiGianDen = $_POST['ThoiGianDen'] ?? null;
            $tienPhanBo  = (float)($_POST['TienPhanBo'] ?? 0);

            // Validate
            if (empty($input['TenChiPhi'])) {
                $error = "Tên chi phí không được để trống.";
            } elseif ((float)$input['SoTien'] <= 0) {
                $error = "Số tiền phải lớn hơn 0.";
            } elseif (empty($input['NgayChi'])) {
                $error = "Vui lòng chọn ngày chi phí.";
            } elseif (empty($input['MaLoaiChiPhi'])) {
                $error = "Vui lòng chọn loại chi phí.";
            } else {
                if ($maCP > 0) {
                    // Cập nhật
                    if ($model->suaChiPhi($maCP, $input['TenChiPhi'], $input['MaLoaiChiPhi'], 
                                          $input['SoTien'], $input['NgayChi'], 
                                          $input['TrangThai'], $input['GhiChu'] ?? '')) {
                        $success = "Cập nhật chi phí thành công.";
                    } else {
                        $error = "Lỗi khi cập nhật chi phí.";
                    }
                } else {
                    // Tạo mới
                    $newId = $model->themChiPhi($input['TenChiPhi'], $input['MaLoaiChiPhi'], 
                                                 $input['SoTien'], $input['NgayChi'], 
                                                 $this->user['MaTK'] ?? 0, $input['GhiChu'] ?? '');
                    if ($newId) {
                        $success = "Tạo chi phí thành công.";
                        $maCP = $newId;
                    } else {
                        $error = "Lỗi khi tạo chi phí.";
                    }
                }

                // Phân bổ chi phí nếu có
                if (!$error && !empty($loaiPhanBo) && $tienPhanBo > 0) {
                    if (!$model->PhanBoChiPhi($maCP, $loaiPhanBo, $maPhong, $maBoPhap, 
                                              $thoiGianTu, $thoiGianDen, $tienPhanBo, 
                                              $_POST['GhiChuPhanBo'] ?? '')) {
                        $error = "Cảnh báo: Chi phí đã lưu nhưng lỗi khi phân bổ.";
                    } else {
                        $success = "Chi phí đã được phân bổ thành công.";
                    }
                }
            }
        }

        // Lấy danh sách phòng và bộ phận
        $danhSachPhong = $model->layDanhSachPhong() ?? [];
        $danhSachBoPhap = $model->layDanhSachBoPhap() ?? [];

        // Lấy chi tiết phân bổ nếu sửa
        $chiTietPhanBo = [];
        if ($maCP > 0) {
            $chiTietPhanBo = $model->layChiTietPhanBoChiPhi($maCP);
        }

        $this->view('ketoan/quanLyChiPhiNangCao', [
            'user'                  => $this->user,
            'action'                => $action,
            'error'                 => $error,
            'success'               => $success,
            'input'                 => $input,
            'maCP'                  => $maCP,
            'danhSachLoaiChiPhi'    => $danhSachLoaiChiPhi,
            'danhSachPhong'         => $danhSachPhong,
            'danhSachBoPhap'        => $danhSachBoPhap,
            'chiTietPhanBo'         => $chiTietPhanBo
        ]);
    }

    /**
     * Lấy danh sách bộ phận (helper)
     */
    private function layDanhSachBoPhap(): array
    {
        // This will be called from model
        return [];
    }

    /**
     * Xem báo cáo KQKD (Kết Quả Kinh Doanh)
     */
    public function baoCaoKQKD()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';
        $model = new BaoCaoKeToan();

        $kyKhoaSo = $_GET['ky'] ?? date('Y-m');
        $error = '';
        $baoCao = [];

        // Kiểm tra kỳ khóa sổ có hợp lệ không
        if (!empty($kyKhoaSo)) {
            try {
                $baoCao = $model->tinhBaoCaoKQKD($kyKhoaSo);
                if (empty($baoCao)) {
                    $error = "Không tìm thấy dữ liệu cho kỳ này.";
                }
            } catch (Exception $e) {
                $error = "Lỗi khi tính toán báo cáo: " . $e->getMessage();
            }
        }

        $this->view('ketoan/baoCaoKQKD', [
            'user' => $this->user,
            'kyKhoaSo' => $kyKhoaSo,
            'baoCao' => $baoCao,
            'error' => $error
        ]);
    }

    /**
     * Xuất báo cáo KQKD ra Excel
     */
    public function xuatKQKD()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';
        $model = new BaoCaoKeToan();

        $kyKhoaSo = $_GET['ky'] ?? date('Y-m');
        
        try {
            $baoCao = $model->tinhBaoCaoKQKD($kyKhoaSo);
            
            // Tính toán thêm thông tin
            $tongDoanhThu = $baoCao['tongDoanhThu'] ?? 0;
            $tongChiPhi = $baoCao['tongChiPhi'] ?? 0;
            $loiNhuanRong = $baoCao['loiNhuanRong'] ?? 0;
            $tyLeLN = $tongDoanhThu > 0 ? ($loiNhuanRong / $tongDoanhThu * 100) : 0;

            // Header Excel
            header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
            header('Content-Disposition: attachment; filename="KQKD_' . $kyKhoaSo . '.xls"');
            
            echo "\xEF\xBB\xBF"; // UTF-8 BOM
            
            // Nội dung báo cáo
            echo "BÁO CÁO KẾT QUẢ KINH DOANH (KQKD)\n";
            echo "ABC Resort\n";
            echo "Kỳ: " . htmlspecialchars($kyKhoaSo) . "\n";
            echo "Ngày xuất: " . date('d/m/Y H:i:s') . "\n";
            echo "\n";
            
            echo "CHỈ TIÊU\tGIÁ TRỊ (₫)\n";
            echo "--- DOANH THU ---\n";
            echo "Doanh thu hoạt động kinh doanh\t" . number_format($tongDoanhThu, 0, ',', '.') . "\n";
            echo "Cộng Doanh Thu\t" . number_format($tongDoanhThu, 0, ',', '.') . "\n";
            echo "\n";
            
            echo "--- CHI PHÍ ---\n";
            echo "Chi phí hoạt động\t" . number_format($tongChiPhi, 0, ',', '.') . "\n";
            echo "Cộng Chi Phí\t" . number_format($tongChiPhi, 0, ',', '.') . "\n";
            echo "\n";
            
            echo "--- LỢI NHUẬN ---\n";
            echo "Lợi nhuận ròng\t" . number_format($loiNhuanRong, 0, ',', '.') . "\n";
            echo "Tỷ lệ lợi nhuận (%)\t" . number_format($tyLeLN, 2, ',', '.') . "\n";
            
        } catch (Exception $e) {
            header('Content-Type: text/html; charset=UTF-8');
            echo "Lỗi: " . htmlspecialchars($e->getMessage());
        }
        exit;
    }

    /**
     * Xem báo cáo Lưu chuyển tiền tệ
     */
    public function luuChuyenTienTe()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';
        $model = new BaoCaoKeToan();

        $kyKhoaSo = $_GET['ky'] ?? date('Y-m');
        $error = '';
        $luuChuyenTien = [
            'kinhdoanh' => ['tienmат' => 0, 'chuyenkhoан' => 0, 'the' => 0, 'vidiental' => 0, 'total' => 0],
            'dautut' => ['tienmат' => 0, 'chuyenkhoан' => 0, 'the' => 0, 'vidiental' => 0, 'total' => 0],
            'taichinh' => ['tienmат' => 0, 'chuyenkhoан' => 0, 'the' => 0, 'vidiental' => 0, 'total' => 0]
        ];

        try {
            $data = $model->layBaoCaoLuuChuyenTienTe($kyKhoaSo);
            if (!empty($data)) {
                // Transform data from database format to view format
                foreach ($data as $row) {
                    $loaiHoatDong = $row['LoaiHoatDong'];
                    
                    // Map to view key
                    $viewKey = '';
                    if ($loaiHoatDong === 'HoatDongKinhDoanh') {
                        $viewKey = 'kinhdoanh';
                    } elseif ($loaiHoatDong === 'HoatDongDauTu') {
                        $viewKey = 'dautut';
                    } elseif ($loaiHoatDong === 'HoatDongTaiChinh') {
                        $viewKey = 'taichinh';
                    }
                    
                    if ($viewKey) {
                        // Add to existing data
                        $luuChuyenTien[$viewKey]['tienmат'] += (float)$row['GiaTriTienMat'];
                        $luuChuyenTien[$viewKey]['chuyenkhoан'] += (float)$row['GiaTriChuyenKhoan'];
                        $luuChuyenTien[$viewKey]['total'] += (float)$row['TongGiaTri'];
                    }
                }
            }
        } catch (Exception $e) {
            $error = "Lỗi khi lấy báo cáo: " . $e->getMessage();
        }

        $this->view('ketoan/luuChuyenTienTe', [
            'user' => $this->user,
            'kyKhoaSo' => $kyKhoaSo,
            'luuChuyenTien' => $luuChuyenTien,
            'error' => $error
        ]);
    }

    /**
     * Xem công nợ phải thu
     */
    public function congNoPhaiThu()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';
        $model = new BaoCaoKeToan();

        $error = '';
        $congNo = [];
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';

        try {
            // Get all receivables first
            $allData = $model->layCongNoPhaiThu();
            
            // Filter by status if provided
            if (!empty($status)) {
                $congNo = array_filter($allData, function($item) use ($status) {
                    return $item['TrangThaiThanhToan'] === $status;
                });
            } else {
                $congNo = $allData;
            }
            
            // Filter by search term if provided
            if (!empty($search)) {
                $congNo = array_filter($congNo, function($item) use ($search) {
                    return stripos($item['TenKH'] ?? '', $search) !== false;
                });
            }
        } catch (Exception $e) {
            $error = "Lỗi khi lấy dữ liệu: " . $e->getMessage();
        }

        $this->view('ketoan/congNoPhaiThu', [
            'user' => $this->user,
            'congNo' => $congNo,
            'error' => $error
        ]);
    }

    /**
     * Xem chi tiết công nợ phải thu
     */
    public function xemChiTietCongNo()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';
        $model = new BaoCaoKeToan();

        $maCongNo = $_GET['id'] ?? 0;
        $error = '';
        $chiTiet = [];
        $giaoDich = [];

        try {
            if ($maCongNo <= 0) {
                $error = "ID công nợ không hợp lệ";
            } else {
                // Lấy chi tiết công nợ
                $sql = "
                    SELECT 
                        cnpt.*,
                        kh.TenKH,
                        kh.SDT,
                        kh.Email,
                        kh.DiaChi,
                        gd.TongTien,
                        gd.TrangThai as TrangThaiGiaoDich
                    FROM CongNoPhaiThu cnpt
                    LEFT JOIN khachhang kh ON cnpt.MaKhachHang = kh.MaKhachHang
                    LEFT JOIN giaodich gd ON cnpt.MaGiaoDich = gd.MaGiaoDich
                    WHERE cnpt.MaCongNo = ?
                ";
                $db = Database::getConnection();
                $stmt = $db->prepare($sql);
                $stmt->bind_param('i', $maCongNo);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    $error = "Không tìm thấy công nợ";
                } else {
                    $chiTiet = $result->fetch_assoc();
                }
            }
        } catch (Exception $e) {
            $error = "Lỗi: " . $e->getMessage();
        }

        $this->view('ketoan/chiTietCongNo', [
            'user' => $this->user,
            'chiTiet' => $chiTiet,
            'error' => $error
        ]);
    }

    /**
     * Cập nhật thanh toán công nợ
     */
    public function thuCongNo()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận POST request']);
            return;
        }

        $maCongNo = $_POST['id'] ?? 0;
        $soTienThu = (float)($_POST['soTienThu'] ?? 0);
        $ghiChu = $_POST['ghiChu'] ?? '';

        try {
            $db = Database::getConnection();
            
            // Lấy thông tin công nợ hiện tại
            $stmt = $db->prepare("SELECT * FROM CongNoPhaiThu WHERE MaCongNo = ?");
            $stmt->bind_param('i', $maCongNo);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'Công nợ không tồn tại']);
                return;
            }

            $congNo = $result->fetch_assoc();
            $soTienDaThuMoi = $congNo['SoTienDaThu'] + $soTienThu;
            
            // Kiểm tra không được vượt quá số tiền gốc
            if ($soTienDaThuMoi > $congNo['SoTienGoc']) {
                echo json_encode(['success' => false, 'message' => 'Số tiền thanh toán vượt quá số tiền gốc']);
                return;
            }

            // Xác định trạng thái mới
            if ($soTienDaThuMoi >= $congNo['SoTienGoc']) {
                $trangThaiMoi = 'DaThu';
            } elseif ($soTienDaThuMoi > 0) {
                $trangThaiMoi = 'ThuMotPhan';
            } else {
                $trangThaiMoi = 'ChuaThu';
            }

            // Cập nhật công nợ
            $updateStmt = $db->prepare("
                UPDATE CongNoPhaiThu 
                SET SoTienDaThu = ?, TrangThaiThanhToan = ?, GhiChu = ?
                WHERE MaCongNo = ?
            ");
            $updateStmt->bind_param('dssi', $soTienDaThuMoi, $trangThaiMoi, $ghiChu, $maCongNo);
            
            if ($updateStmt->execute()) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Cập nhật thanh toán thành công',
                    'soTienDaThu' => $soTienDaThuMoi,
                    'trangThai' => $trangThaiMoi
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật database']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Cập nhật thanh toán công nợ phải trả
     */
    public function traCongNo()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận POST request']);
            return;
        }

        $maCongNo = $_POST['id'] ?? 0;
        $soTienTra = (float)($_POST['soTienTra'] ?? 0);
        $ghiChu = $_POST['ghiChu'] ?? '';

        try {
            $db = Database::getConnection();
            
            // Lấy thông tin công nợ hiện tại
            $stmt = $db->prepare("SELECT * FROM CongNoPhaiTra WHERE MaCongNo = ?");
            $stmt->bind_param('i', $maCongNo);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'Công nợ không tồn tại']);
                return;
            }

            $congNo = $result->fetch_assoc();
            $soTienDaTraMoi = $congNo['SoTienDaTra'] + $soTienTra;
            
            // Kiểm tra không được vượt quá số tiền gốc
            if ($soTienDaTraMoi > $congNo['SoTienGoc']) {
                echo json_encode(['success' => false, 'message' => 'Số tiền thanh toán vượt quá số tiền gốc']);
                return;
            }

            // Xác định trạng thái mới
            if ($soTienDaTraMoi >= $congNo['SoTienGoc']) {
                $trangThaiMoi = 'DaTra';
            } elseif ($soTienDaTraMoi > 0) {
                $trangThaiMoi = 'TraMotPhan';
            } else {
                $trangThaiMoi = 'ChuaTra';
            }

            // Cập nhật công nợ
            $updateStmt = $db->prepare("
                UPDATE CongNoPhaiTra 
                SET SoTienDaTra = ?, TrangThaiThanhToan = ?, GhiChu = ?
                WHERE MaCongNo = ?
            ");
            $updateStmt->bind_param('dssi', $soTienDaTraMoi, $trangThaiMoi, $ghiChu, $maCongNo);
            
            if ($updateStmt->execute()) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Cập nhật thanh toán thành công',
                    'soTienDaTra' => $soTienDaTraMoi,
                    'trangThai' => $trangThaiMoi
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật database']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Xem công nợ phải trả
     */
    public function congNoPhaiTra()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';
        $model = new BaoCaoKeToan();

        $error = '';
        $congNo = [];
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';

        try {
            // Get all payables first
            $allData = $model->layCongNoPhaiTra();
            
            // Filter by status if provided
            if (!empty($status)) {
                $congNo = array_filter($allData, function($item) use ($status) {
                    return $item['TrangThaiThanhToan'] === $status;
                });
            } else {
                $congNo = $allData;
            }
            
            // Filter by search term if provided
            if (!empty($search)) {
                $congNo = array_filter($congNo, function($item) use ($search) {
                    return stripos($item['TenNhaCungCap'] ?? '', $search) !== false;
                });
            }
        } catch (Exception $e) {
            $error = "Lỗi khi lấy dữ liệu: " . $e->getMessage();
        }

        $this->view('ketoan/congNoPhaiTra', [
            'user' => $this->user,
            'congNo' => $congNo,
            'error' => $error
        ]);
    }

    /**
     * Xem chi tiết công nợ phải trả
     */
    public function xemChiTietCongNoTra()
    {
        $maCongNo = $_GET['id'] ?? 0;
        $error = '';
        $chiTiet = [];

        try {
            if ($maCongNo <= 0) {
                $error = "ID công nợ không hợp lệ";
            } else {
                $db = Database::getConnection();
                $stmt = $db->prepare("SELECT * FROM CongNoPhaiTra WHERE MaCongNo = ?");
                $stmt->bind_param('i', $maCongNo);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    $error = "Không tìm thấy công nợ";
                } else {
                    $chiTiet = $result->fetch_assoc();
                }
            }
        } catch (Exception $e) {
            $error = "Lỗi: " . $e->getMessage();
        }

        $this->view('ketoan/chiTietCongNoTra', [
            'user' => $this->user,
            'chiTiet' => $chiTiet,
            'error' => $error
        ]);
    }

    /**
     * Xem sổ nganh quỹ
     */
    public function soNganQuy()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';
        $model = new BaoCaoKeToan();

        $kyKhoaSo = $_GET['ky'] ?? date('Y-m');
        $error = '';
        $soNganQuy = [];
        $tongTienMat = 0;
        $tongChuyenKhoan = 0;
        $tongThe = 0;
        $tongViDienTu = 0;

        try {
            $soNganQuy = $model->laySoNganQuy($kyKhoaSo);
            
            // Tính tổng theo phương thức thanh toán
            foreach ($soNganQuy as $item) {
                switch ($item['PhuongThucThanhToan'] ?? '') {
                    case 'TienMat':
                        $tongTienMat += $item['SoTien'] ?? 0;
                        break;
                    case 'ChuyenKhoan':
                        $tongChuyenKhoan += $item['SoTien'] ?? 0;
                        break;
                    case 'The':
                        $tongThe += $item['SoTien'] ?? 0;
                        break;
                    case 'ViDienTu':
                        $tongViDienTu += $item['SoTien'] ?? 0;
                        break;
                }
            }
        } catch (Exception $e) {
            $error = "Lỗi khi lấy dữ liệu: " . $e->getMessage();
        }

        $this->view('ketoan/soNganQuy', [
            'user' => $this->user,
            'kyKhoaSo' => $kyKhoaSo,
            'soNganQuy' => $soNganQuy,
            'tongTienMat' => $tongTienMat,
            'tongChuyenKhoan' => $tongChuyenKhoan,
            'tongThe' => $tongThe,
            'tongViDienTu' => $tongViDienTu,
            'error' => $error
        ]);
    }

    /**
     * Xem đối soát nganh quỹ
     */
    public function doiSoatNganQuy()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';
        $model = new BaoCaoKeToan();

        $kyKhoaSo = $_GET['ky'] ?? date('Y-m');
        $error = '';
        $doiSoat = [];
        $tongChieuDuGhi = 0;
        $tongChieuDuNganHang = 0;
        $chenhLech = 0;

        try {
            // Lấy dữ liệu giao dịch chưa đối soát
            $doiSoat = $model->laySoNganQuy($kyKhoaSo);
            
            // Tính số dư ghi sổ
            $tongChieuDuGhi = array_sum(array_map(function($item) {
                return $item['SoTien'] ?? 0;
            }, $doiSoat));

            // Lấy số dư ngân hàng từ bảng BienLaiThuTien
            // (Trong thực tế, cần manual nhập từ bảng kê ngân hàng)
            $tongChieuDuNganHang = $model->layTongChieuDuNganHang();
            
            $chenhLech = $tongChieuDuGhi - $tongChieuDuNganHang;
        } catch (Exception $e) {
            $error = "Lỗi khi lấy dữ liệu: " . $e->getMessage();
        }

        $this->view('ketoan/doiSoatNganQuy', [
            'user' => $this->user,
            'kyKhoaSo' => $kyKhoaSo,
            'doiSoat' => $doiSoat,
            'tongChieuDuGhi' => $tongChieuDuGhi,
            'tongChieuDuNganHang' => $tongChieuDuNganHang,
            'chenhLech' => $chenhLech,
            'error' => $error
        ]);
    }

    /**
     * Xem chi tiết giao dịch
     */
    public function xemChiTietGiaoDich()
    {
        $maGiaoDich = $_GET['id'] ?? 0;

        if ($maGiaoDich <= 0) {
            $this->view('error/404');
            return;
        }

        require_once __DIR__ . '/../models/BaoCaoKeToan.php';
        $model = new BaoCaoKeToan();
        $error = '';
        $giaoDich = [];

        try {
            $giaoDich = $model->layGiaoDichById($maGiaoDich);
            if (empty($giaoDich)) {
                $error = "Không tìm thấy giao dịch.";
            }
        } catch (Exception $e) {
            $error = "Lỗi: " . $e->getMessage();
        }

        $this->view('ketoan/chiTietGiaoDich', [
            'user' => $this->user,
            'giaoDich' => $giaoDich,
            'error' => $error
        ]);
    }

    /**
     * Đối soát một giao dịch
     */
    public function doiSoatGiaoDich()
    {
        // Clear any output before JSON
        ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận POST request'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $maNganQuy = $_POST['id'] ?? 0;

        if ($maNganQuy <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID không hợp lệ'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            $db = Database::getConnection();
            // Cập nhật trạng thái đối soát
            $stmt = $db->prepare("
                UPDATE songanquy 
                SET DaDoiSoat = 1
                WHERE MaNganQuy = ?
            ");
            $stmt->bind_param('i', $maNganQuy);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Đã đối soát giao dịch thành công'], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật'], JSON_UNESCAPED_UNICODE);
            }
            $stmt->close();
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống'], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    /**
     * Xem danh sách kỳ khóa sổ
     */
    public function khoaSo()
    {
        require_once __DIR__ . '/../models/BaoCaoKeToan.php';
        $model = new BaoCaoKeToan();

        $error = '';
        $success = $_GET['success'] ?? '';
        $danhSachKhoaSo = [];

        try {
            $db = Database::getConnection();
            // Lấy tất cả kỳ khóa sổ
            $sql = "SELECT * FROM khoaso ORDER BY KyKhoaSo DESC";
            $result = $db->query($sql);
            
            if ($result) {
                $danhSachKhoaSo = $result->fetch_all(MYSQLI_ASSOC);
            }
        } catch (Exception $e) {
            $error = "Lỗi khi lấy danh sách kỳ khóa sổ: " . $e->getMessage();
        }

        $this->view('ketoan/khoaSo', [
            'user' => $this->user,
            'danhSachKhoaSo' => $danhSachKhoaSo,
            'error' => $error,
            'success' => $success
        ]);
    }

    /**
     * Đóng sổ kỳ hạch toán
     */
    public function dongSoKy()
    {
        $kyKhoaSo = $_GET['ky'] ?? '';
        
        if (empty($kyKhoaSo)) {
            header('Location: index.php?controller=ketoan&action=khoaSo&error=Kỳ không hợp lệ');
            return;
        }

        try {
            $db = Database::getConnection();
            // Kiểm tra kỳ tồn tại
            $stmt = $db->prepare("SELECT * FROM khoaso WHERE KyKhoaSo = ?");
            $stmt->bind_param('s', $kyKhoaSo);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                header('Location: index.php?controller=ketoan&action=khoaSo&error=Kỳ không tồn tại');
                return;
            }

            $khoaSo = $result->fetch_assoc();
            
            // Kiểm tra trạng thái
            if ($khoaSo['TrangThai'] !== 'DangMo') {
                header('Location: index.php?controller=ketoan&action=khoaSo&error=Kỳ này không thể đóng');
                return;
            }

            // Đóng sổ
            $maNhanVien = (int)($this->user['MaTK'] ?? 0);
            $updateStmt = $db->prepare("
                UPDATE khoaso 
                SET TrangThai = 'DaDong', NgayDong = NOW(), MaNhanVienDong = ?
                WHERE KyKhoaSo = ?
            ");
            $updateStmt->bind_param('is', $maNhanVien, $kyKhoaSo);
            
            if ($updateStmt->execute()) {
                header('Location: index.php?controller=ketoan&action=khoaSo&success=Đóng sổ thành công');
            } else {
                header('Location: index.php?controller=ketoan&action=khoaSo&error=Lỗi khi đóng sổ');
            }
        } catch (Exception $e) {
            header('Location: index.php?controller=ketoan&action=khoaSo&error=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Xem chi tiết kỳ khóa sổ
     */
    public function xemChiTietKhoaSo()
    {
        $kyKhoaSo = $_GET['ky'] ?? '';
        
        if (empty($kyKhoaSo)) {
            header('Location: index.php?controller=ketoan&action=khoaSo');
            return;
        }

        $error = '';
        $khoaSo = [];
        $danhSachGiaoDich = [];

        try {
            // Lấy thông tin kỳ
            $stmt = $this->db->prepare("SELECT * FROM khoaso WHERE KyKhoaSo = ?");
            $stmt->bind_param('s', $kyKhoaSo);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                $error = "Kỳ không tồn tại";
            } else {
                $khoaSo = $result->fetch_assoc();
                
                // Lấy các giao dịch trong kỳ
                $gdStmt = $this->db->prepare("
                    SELECT * FROM giaodich 
                    WHERE DATE_FORMAT(NgayGiaoDich, '%Y-%m') = ? 
                    ORDER BY NgayGiaoDich DESC
                ");
                $gdStmt->bind_param('s', $kyKhoaSo);
                $gdStmt->execute();
                $gdResult = $gdStmt->get_result();
                $danhSachGiaoDich = $gdResult->fetch_all(MYSQLI_ASSOC);
            }
        } catch (Exception $e) {
            $error = "Lỗi: " . $e->getMessage();
        }

        // Hiển thị chi tiết hoặc chuyển hướng đơn giản
        header('Location: index.php?controller=ketoan&action=khoaSo');
    }
}


