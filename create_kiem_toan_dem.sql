CREATE TABLE IF NOT EXISTS kiem_toan_dem (
    MaKTD INT PRIMARY KEY AUTO_INCREMENT,
    NgayKTD DATE NOT NULL,
    MaTaiKhoan INT,
    SoDuDauNgay DECIMAL(14,2) DEFAULT 0,
    SoDuCuoiNgay DECIMAL(14,2) DEFAULT 0,
    TongDoanhThu DECIMAL(14,2) DEFAULT 0,
    TongChiPhi DECIMAL(14,2) DEFAULT 0,
    LoiNhuan DECIMAL(14,2) DEFAULT 0,
    TrangThai ENUM('DangKiemToan', 'DaKiemToan', 'ChuaKiemToan') DEFAULT 'ChuaKiemToan',
    GhiChu TEXT,
    ThoiGianTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ThoiGianCapNhat DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_ngay (NgayKTD),
    KEY idx_trangthai (TrangThai),
    FOREIGN KEY (MaTaiKhoan) REFERENCES taikhoan(MaTK) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO kiem_toan_dem (NgayKTD, MaTaiKhoan, SoDuDauNgay, SoDuCuoiNgay, TongDoanhThu, TongChiPhi, LoiNhuan, TrangThai, GhiChu) VALUES
('2025-12-16', 1, 50000000, 72700000, 22700000, 13300000, 9400000, 'DaKiemToan', 'Kiem toan ngay 16/12/2025'),
('2025-12-15', 1, 48000000, 50000000, 5000000, 3000000, 2000000, 'DaKiemToan', 'Kiem toan ngay 15/12/2025'),
('2025-12-14', 1, 45000000, 48000000, 4500000, 2500000, 2000000, 'DaKiemToan', 'Kiem toan ngay 14/12/2025');
