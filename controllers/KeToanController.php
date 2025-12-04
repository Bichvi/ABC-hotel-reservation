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
}