<?php

class BaoCaoKeToan
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Báo cáo doanh thu theo ngày
     * Lấy từ bảng giaodich (TongTien, NgayGiaoDich)
     */
    public function baoCaoDoanhThu(string $tuNgay, string $denNgay): array
    {
        $sql = "
            SELECT 
                DATE(NgayGiaoDich) AS Ngay,
                COUNT(*)           AS SoGiaoDich,
                SUM(TongTien)      AS TongDoanhThu
            FROM giaodich
            WHERE DATE(NgayGiaoDich) BETWEEN ? AND ?
              AND TrangThai IN ('Booked','Stayed','Paid')
            GROUP BY DATE(NgayGiaoDich)
            ORDER BY Ngay
        ";

        $stm = $this->db->prepare($sql);
        $stm->bind_param('ss', $tuNgay, $denNgay);
        $stm->execute();
        $rows = $stm->get_result()->fetch_all(MYSQLI_ASSOC);

        $tongGD    = 0;
        $tongTien  = 0.0;

        foreach ($rows as $r) {
            $tongGD   += (int)$r['SoGiaoDich'];
            $tongTien += (float)$r['TongDoanhThu'];
        }

        return [
            'rows' => $rows,
            'tong' => [
                'so_gd'     => $tongGD,
                'doanh_thu' => $tongTien
            ]
        ];
    }

    /**
     * Báo cáo chi phí theo ngày
     * Lấy từ bảng chiphi (SoTien, NgayChi)
     */
    public function baoCaoChiPhi(string $tuNgay, string $denNgay): array
    {
        $sql = "
            SELECT 
                NgayChi AS Ngay,
                COUNT(*)        AS SoPhieuChi,
                SUM(SoTien)     AS TongChiPhi
            FROM chiphi
            WHERE NgayChi BETWEEN ? AND ?
              AND TrangThai = 'DaDuyet'
            GROUP BY NgayChi
            ORDER BY NgayChi
        ";

        $stm = $this->db->prepare($sql);
        $stm->bind_param('ss', $tuNgay, $denNgay);
        $stm->execute();
        $rows = $stm->get_result()->fetch_all(MYSQLI_ASSOC);

        $tongPhieu = 0;
        $tongTien  = 0.0;

        foreach ($rows as $r) {
            $tongPhieu += (int)$r['SoPhieuChi'];
            $tongTien  += (float)$r['TongChiPhi'];
        }

        return [
            'rows' => $rows,
            'tong' => [
                'so_phieu' => $tongPhieu,
                'chi_phi'  => $tongTien
            ]
        ];
    }

    /**
     * Hàm tổng: tạo báo cáo theo loại
     * - doanhthu
     * - chiphi
     * - tonghop (doanh thu, chi phí, lợi nhuận)
     */
    public function taoBaoCao(string $loaiBaoCao, string $tuNgay, string $denNgay): array
    {
        $loaiBaoCao = strtolower($loaiBaoCao);

        if ($loaiBaoCao === 'doanhthu') {
            $dt = $this->baoCaoDoanhThu($tuNgay, $denNgay);

            return [
                'type'    => 'doanhthu',
                'rows'    => $dt['rows'],
                'tong'    => $dt['tong'],
                'hasData' => count($dt['rows']) > 0
            ];
        }

        if ($loaiBaoCao === 'chiphi') {
            $cp = $this->baoCaoChiPhi($tuNgay, $denNgay);

            return [
                'type'    => 'chiphi',
                'rows'    => $cp['rows'],
                'tong'    => $cp['tong'],
                'hasData' => count($cp['rows']) > 0
            ];
        }

        // Tổng hợp: doanh thu + chi phí + lợi nhuận
        $dt = $this->baoCaoDoanhThu($tuNgay, $denNgay);
        $cp = $this->baoCaoChiPhi($tuNgay, $denNgay);

        $loiNhuan = $dt['tong']['doanh_thu'] - $cp['tong']['chi_phi'];

        return [
            'type'      => 'tonghop',
            'doanh_thu' => $dt,
            'chi_phi'   => $cp,
            'loi_nhuan' => $loiNhuan,
            'hasData'   => (count($dt['rows']) > 0 || count($cp['rows']) > 0)
        ];
    }
   
}