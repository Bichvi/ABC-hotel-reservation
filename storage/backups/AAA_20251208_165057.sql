-- ABC Resort Backup File
-- Created: 2025-12-08 16:50:59
-- Database: abc_resort1
-- Type: hom_nay
-- Created by: admin
-- 
-- Metadata: {
    "version": "1.0",
    "type": "hom_nay",
    "database": "abc_resort1",
    "created_by": "admin",
    "created_at": "2025-12-08 16:50:59",
    "php_version": "8.2.12",
    "mysql_version": "10.4.32-MariaDB"
}
-- 

-- Database Export
-- Database: abc_resort1
-- Date: 2025-12-08 16:50:59

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET AUTOCOMMIT = 0;
START TRANSACTION;

-- Table structure for `chiphi`
DROP TABLE IF EXISTS `chiphi`;
CREATE TABLE `chiphi` (
  `MaCP` int(11) NOT NULL AUTO_INCREMENT,
  `TenChiPhi` varchar(100) NOT NULL,
  `NgayChi` date NOT NULL,
  `SoTien` decimal(14,2) NOT NULL DEFAULT 0.00,
  `NoiDung` varchar(300) DEFAULT NULL,
  `TrangThai` enum('ChoDuyet','DaDuyet','Huy') DEFAULT 'ChoDuyet',
  PRIMARY KEY (`MaCP`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `chiphi`
-- No data found for table `chiphi`

-- Table structure for `chitietdichvu`
DROP TABLE IF EXISTS `chitietdichvu`;
CREATE TABLE `chitietdichvu` (
  `MaCTDV` int(11) NOT NULL AUTO_INCREMENT,
  `MaGiaoDich` int(11) NOT NULL,
  `MaPhong` int(11) DEFAULT NULL,
  `MaDichVu` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL DEFAULT 1,
  `GiaBan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `ThanhTien` decimal(12,2) NOT NULL DEFAULT 0.00,
  `ThoiDiemGhiNhan` datetime NOT NULL DEFAULT current_timestamp(),
  `GhiChu` varchar(300) DEFAULT NULL,
  `NgayDat` datetime NOT NULL DEFAULT current_timestamp(),
  `TrangThaiDichVu` enum('ChuaSuDung','DangSuDung','DaSuDung') NOT NULL DEFAULT 'ChuaSuDung',
  PRIMARY KEY (`MaCTDV`),
  KEY `MaGiaoDich` (`MaGiaoDich`),
  KEY `MaPhong` (`MaPhong`),
  KEY `MaDichVu` (`MaDichVu`),
  CONSTRAINT `chitietdichvu_ibfk_1` FOREIGN KEY (`MaGiaoDich`) REFERENCES `giaodich` (`MaGiaoDich`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chitietdichvu_ibfk_2` FOREIGN KEY (`MaPhong`) REFERENCES `phong` (`MaPhong`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `chitietdichvu_ibfk_3` FOREIGN KEY (`MaDichVu`) REFERENCES `dichvu` (`MaDichVu`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `chitietdichvu`
-- No data found for table `chitietdichvu`

-- Table structure for `chitietgiaodich`
DROP TABLE IF EXISTS `chitietgiaodich`;
CREATE TABLE `chitietgiaodich` (
  `MaCTGD` int(11) NOT NULL AUTO_INCREMENT,
  `MaGiaoDich` int(11) NOT NULL,
  `MaPhong` int(11) NOT NULL,
  `SoNguoi` int(11) DEFAULT 1,
  `NgayNhanDuKien` datetime DEFAULT NULL,
  `NgayTraDuKien` datetime DEFAULT NULL,
  `NgayCheckIn` datetime DEFAULT NULL,
  `NgayCheckOut` datetime DEFAULT NULL,
  `DonGia` decimal(14,2) DEFAULT 0.00,
  `ThanhTien` decimal(14,2) DEFAULT 0.00,
  `TienPhuThu` decimal(14,2) DEFAULT 0.00,
  `TienBoiThuong` decimal(14,2) DEFAULT 0.00,
  `TrangThai` varchar(30) DEFAULT 'Booked',
  `GhiChu` varchar(500) DEFAULT NULL,
  `TenKhach` varchar(100) DEFAULT NULL,
  `CCCD` varchar(20) DEFAULT NULL,
  `SDT` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`MaCTGD`),
  KEY `MaGiaoDich` (`MaGiaoDich`),
  KEY `MaPhong` (`MaPhong`),
  CONSTRAINT `chitietgiaodich_ibfk_1` FOREIGN KEY (`MaGiaoDich`) REFERENCES `giaodich` (`MaGiaoDich`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chitietgiaodich_ibfk_2` FOREIGN KEY (`MaPhong`) REFERENCES `phong` (`MaPhong`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `chitietgiaodich`
-- No data found for table `chitietgiaodich`

-- Table structure for `chitietphanhoi`
DROP TABLE IF EXISTS `chitietphanhoi`;
CREATE TABLE `chitietphanhoi` (
  `MaCTPhanHoi` int(11) NOT NULL AUTO_INCREMENT,
  `MaPhanHoi` int(11) NOT NULL,
  `MaNhanVien` int(11) DEFAULT NULL,
  `NgayTraLoi` datetime NOT NULL DEFAULT current_timestamp(),
  `NoiDungTraLoi` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`MaCTPhanHoi`),
  KEY `MaPhanHoi` (`MaPhanHoi`),
  KEY `MaNhanVien` (`MaNhanVien`),
  CONSTRAINT `chitietphanhoi_ibfk_1` FOREIGN KEY (`MaPhanHoi`) REFERENCES `phanhoi` (`MaPH`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chitietphanhoi_ibfk_2` FOREIGN KEY (`MaNhanVien`) REFERENCES `nhanvien` (`MaNhanVien`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `chitietphanhoi`
-- No data found for table `chitietphanhoi`

-- Table structure for `dichvu`
DROP TABLE IF EXISTS `dichvu`;
CREATE TABLE `dichvu` (
  `MaDichVu` int(11) NOT NULL AUTO_INCREMENT,
  `TenDichVu` varchar(150) NOT NULL,
  `GiaDichVu` decimal(12,2) NOT NULL DEFAULT 0.00,
  `MoTa` varchar(500) DEFAULT NULL,
  `TrangThai` enum('HoatDong','NgungBan','BaoTri') DEFAULT 'HoatDong',
  PRIMARY KEY (`MaDichVu`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `dichvu`
INSERT INTO `dichvu` (`MaDichVu`,`TenDichVu`,`GiaDichVu`,`MoTa`,`TrangThai`) VALUES
(1,'Spa 60’',300000.00,'Massage toàn thân 60 phút','HoatDong'),
(2,'Giặt ủi',50000.00,'Giặt ủi quần áo','HoatDong'),
(3,'Đưa đón sân bay',400000.00,'Xe đưa đón sân bay','HoatDong');

-- Table structure for `doan`
DROP TABLE IF EXISTS `doan`;
CREATE TABLE `doan` (
  `MaDoan` int(11) NOT NULL AUTO_INCREMENT,
  `TenDoan` varchar(150) DEFAULT NULL,
  `MaTruongDoan` int(11) DEFAULT NULL,
  `SoNguoi` int(11) DEFAULT 0,
  `NgayDen` date DEFAULT NULL,
  `NgayDi` date DEFAULT NULL,
  `GhiChu` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`MaDoan`),
  KEY `MaTruongDoan` (`MaTruongDoan`),
  CONSTRAINT `doan_ibfk_1` FOREIGN KEY (`MaTruongDoan`) REFERENCES `khachhang` (`MaKhachHang`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `doan`
-- No data found for table `doan`

-- Table structure for `giaodich`
DROP TABLE IF EXISTS `giaodich`;
CREATE TABLE `giaodich` (
  `MaGiaoDich` int(11) NOT NULL AUTO_INCREMENT,
  `MaKhachHang` int(11) DEFAULT NULL,
  `MaDoan` int(11) DEFAULT NULL,
  `MaNhanVien` int(11) DEFAULT NULL,
  `MaKhuyenMai` int(11) DEFAULT NULL,
  `NgayGiaoDich` datetime NOT NULL DEFAULT current_timestamp(),
  `LoaiGiaoDich` enum('DatPhong','ThueTrucTiep') DEFAULT 'DatPhong',
  `TongTien` decimal(14,2) DEFAULT 0.00,
  `TrangThai` enum('Moi','Booked','DaHuy','Stayed','Paid') NOT NULL DEFAULT 'Moi',
  `PhuongThucThanhToan` enum('ChuaThanhToan','TienMat','The','ChuyenKhoan','ViDienTu') DEFAULT 'ChuaThanhToan',
  `GhiChu` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`MaGiaoDich`),
  KEY `MaKhachHang` (`MaKhachHang`),
  KEY `MaDoan` (`MaDoan`),
  KEY `MaNhanVien` (`MaNhanVien`),
  KEY `MaKhuyenMai` (`MaKhuyenMai`),
  CONSTRAINT `giaodich_ibfk_1` FOREIGN KEY (`MaKhachHang`) REFERENCES `khachhang` (`MaKhachHang`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `giaodich_ibfk_2` FOREIGN KEY (`MaDoan`) REFERENCES `doan` (`MaDoan`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `giaodich_ibfk_3` FOREIGN KEY (`MaNhanVien`) REFERENCES `nhanvien` (`MaNhanVien`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `giaodich_ibfk_4` FOREIGN KEY (`MaKhuyenMai`) REFERENCES `khuyenmai` (`MaKhuyenMai`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `giaodich`
-- No data found for table `giaodich`

-- Table structure for `hoadon`
DROP TABLE IF EXISTS `hoadon`;
CREATE TABLE `hoadon` (
  `MaHoaDon` int(11) NOT NULL AUTO_INCREMENT,
  `MaGiaoDich` int(11) NOT NULL,
  `MaKhachHang` int(11) DEFAULT NULL,
  `MaNhanVien` int(11) DEFAULT NULL,
  `NgayLap` datetime NOT NULL DEFAULT current_timestamp(),
  `TongTien` decimal(14,2) DEFAULT 0.00,
  `PhuongThucThanhToan` enum('TienMat','The','ChuyenKhoan','ViDienTu') DEFAULT 'TienMat',
  `TrangThai` enum('ChuaThanhToan','DaThanhToan','DaHuy') DEFAULT 'ChuaThanhToan',
  `GhiChu` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`MaHoaDon`),
  KEY `MaGiaoDich` (`MaGiaoDich`),
  KEY `MaKhachHang` (`MaKhachHang`),
  KEY `MaNhanVien` (`MaNhanVien`),
  CONSTRAINT `hoadon_ibfk_1` FOREIGN KEY (`MaGiaoDich`) REFERENCES `giaodich` (`MaGiaoDich`) ON UPDATE CASCADE,
  CONSTRAINT `hoadon_ibfk_2` FOREIGN KEY (`MaKhachHang`) REFERENCES `khachhang` (`MaKhachHang`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `hoadon_ibfk_3` FOREIGN KEY (`MaNhanVien`) REFERENCES `nhanvien` (`MaNhanVien`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `hoadon`
-- No data found for table `hoadon`

-- Table structure for `khachhang`
DROP TABLE IF EXISTS `khachhang`;
CREATE TABLE `khachhang` (
  `MaKhachHang` int(11) NOT NULL AUTO_INCREMENT,
  `MaTK` int(11) DEFAULT NULL,
  `TenKH` varchar(100) NOT NULL,
  `SDT` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `CCCD` varchar(20) DEFAULT NULL,
  `DiaChi` varchar(200) DEFAULT NULL,
  `LoaiKhach` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`MaKhachHang`),
  KEY `fk_khachhang_taikhoan` (`MaTK`),
  CONSTRAINT `fk_khachhang_taikhoan` FOREIGN KEY (`MaTK`) REFERENCES `taikhoan` (`MaTK`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `khachhang`
INSERT INTO `khachhang` (`MaKhachHang`,`MaTK`,`TenKH`,`SDT`,`Email`,`CCCD`,`DiaChi`,`LoaiKhach`) VALUES
(1,NULL,'Nguyễn An',909123456,'an@example.com',12345678901,'Hồ Chí Minh','Cá nhân'),
(3,NULL,'võ nhật Trường',333204860,'vnt181@gmail.com',22653661123,'Long An','Cá nhân'),
(4,NULL,'huo huy',8998999999,'vnt566@gmail.com',345435757,'long an ','Trưởng đoàn'),
(5,NULL,'hihihi',22226798,'vnt@gmail.com',123456789,'','Thành viên'),
(6,NULL,'vo truong',3979685757,'hyh644357@gmail.com',5676486788,NULL,'Cá nhân'),
(7,NULL,'vo truong',3332048601,'vnt1812@gmail.com',56764867889,NULL,'Cá nhân'),
(8,NULL,'Vo nhat truong',986343955,'hello@gmail.com',80204014712,NULL,'Cá nhân'),
(9,NULL,'hung huỵ',98564331,'hiihi123@gmail.com',342586067,NULL,'Cá nhân'),
(10,NULL,'hi hu ha',987876598,'hihihi89@gmail.com',456789345,NULL,'Cá nhân'),
(11,NULL,'ngo duy thong',8998999999,'thongkhung@gmail.com',98538299,'an giang','Trưởng đoàn'),
(12,NULL,'nhi',22226789,'nhi@gmail.com',22653661111,'','Thành viên'),
(16,NULL,'Sang',123456789,'a@gmail.com',NULL,NULL,'NhanVien'),
(17,NULL,'sangne',0,'sang@gmail.com',NULL,NULL,'NhanVien'),
(18,NULL,'Sang',342208348,'vosang348@gmail.com',NULL,NULL,'NhanVien'),
(19,23,'Sang',342208349,'c@gmail.com',NULL,NULL,'NhanVien'),
(20,24,'Sang',342208350,'d@gmail.com',NULL,NULL,'NhanVien'),
(21,25,'bảo',0342209568,'bao@gmail.com',1234567890,NULL,'NhanVien'),
(22,26,'Doan',0343456789,'Doan@gmai.com',123456788,NULL,'NhanVien'),
(23,25,'bảo',0342209568,'bao@gmail.com',1234567890,NULL,'KhachHang'),
(28,32,'âsasa',0937123123,'aaaa@gmail.com',123123123123,'an giang','KhachHang'),
(30,39,'uqweasd',0971231234,'nnaf@gmail.com',12345678954,'áhoof chí minhh','KhachHang');

-- Table structure for `khuyenmai`
DROP TABLE IF EXISTS `khuyenmai`;
CREATE TABLE `khuyenmai` (
  `MaKhuyenMai` int(11) NOT NULL AUTO_INCREMENT,
  `TenChuongTrinh` varchar(150) NOT NULL,
  `NgayBatDau` date DEFAULT NULL,
  `NgayKetThuc` date DEFAULT NULL,
  `MucUuDai` decimal(5,2) DEFAULT 0.00,
  `DoiTuong` varchar(50) DEFAULT NULL,
  `TrangThai` enum('DangApDung','TamNgung','HetHan') DEFAULT 'DangApDung',
  PRIMARY KEY (`MaKhuyenMai`),
  UNIQUE KEY `TenChuongTrinh` (`TenChuongTrinh`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `khuyenmai`
-- No data found for table `khuyenmai`

-- Table structure for `nhanvien`
DROP TABLE IF EXISTS `nhanvien`;
CREATE TABLE `nhanvien` (
  `MaNhanVien` int(11) NOT NULL AUTO_INCREMENT,
  `TenNV` varchar(100) NOT NULL,
  `SDT` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `ChucVu` varchar(50) DEFAULT NULL,
  `MaVaiTro` int(11) NOT NULL,
  PRIMARY KEY (`MaNhanVien`),
  KEY `MaVaiTro` (`MaVaiTro`),
  CONSTRAINT `nhanvien_ibfk_1` FOREIGN KEY (`MaVaiTro`) REFERENCES `vaitro` (`MaVaiTro`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `nhanvien`
INSERT INTO `nhanvien` (`MaNhanVien`,`TenNV`,`SDT`,`Email`,`ChucVu`,`MaVaiTro`) VALUES
(1,'Lê Tấn Lễ',988000001,'letanle@abcresort.com','Lễ tân',2),
(2,'Phạm Thanh Toán',988000002,'ketoan@abcresort.com','Kế toán',3),
(3,'Ngô Dịch Vụ',988000003,'dichvu@abcresort.com','Nhân viên dịch vụ',4),
(4,'Hoàng CSKH',988000004,'cskh@abcresort.com','CSKH',5),
(5,'Trần Quản Lý',988000005,'quanly@abcresort.com','Quản lý khách sạn',6),
(8,'nhi ne',0347777777,'nhine@gmail.com','CSKH',5),
(9,'aaaaa',0341111111,'aaaa@gmail.com','LeTan',2),
(10,'Lộc',0341888888,'Loc@gmail.com','DichVu',4),
(14,'testt',0312312123,'test@gmail.com','LeTan',2),
(15,'thanh sang',0342208340,'thanhsang@gmail.com','LeTan',2),
(17,'taonef',0912312341,'taone@gmail.com','CSKH',5),
(18,'cccc',0973412563,'ccc@gmail.com','DichVu',4),
(22,'anhba',0981231234,'anhba@gmail.com','CSKH',5);

-- Table structure for `phanhoi`
DROP TABLE IF EXISTS `phanhoi`;
CREATE TABLE `phanhoi` (
  `MaPH` int(11) NOT NULL AUTO_INCREMENT,
  `MaKhachHang` int(11) DEFAULT NULL,
  `LoaiDichVu` varchar(255) DEFAULT NULL,
  `MucDoHaiLong` int(1) DEFAULT NULL,
  `TepDinhKem` varchar(255) DEFAULT NULL,
  `HoTenKH` varchar(150) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `SDT` varchar(20) DEFAULT NULL,
  `NoiDung` text NOT NULL,
  `TinhTrang` enum('ChuaXuLy','DangXuLy','DaXuLy') DEFAULT 'ChuaXuLy',
  `NgayPhanHoi` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`MaPH`),
  KEY `MaKhachHang` (`MaKhachHang`),
  CONSTRAINT `phanhoi_ibfk_1` FOREIGN KEY (`MaKhachHang`) REFERENCES `khachhang` (`MaKhachHang`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `phanhoi`
-- No data found for table `phanhoi`

-- Table structure for `phong`
DROP TABLE IF EXISTS `phong`;
CREATE TABLE `phong` (
  `MaPhong` int(11) NOT NULL AUTO_INCREMENT,
  `SoPhong` varchar(10) NOT NULL,
  `LoaiPhong` varchar(50) NOT NULL,
  `DienTich` float NOT NULL,
  `LoaiGiuong` varchar(50) NOT NULL,
  `ViewPhong` varchar(50) DEFAULT NULL,
  `Gia` decimal(12,2) NOT NULL,
  `TrangThai` enum('Trong','Booked','Stayed','BaoTri') NOT NULL DEFAULT 'Trong',
  `SoKhachToiDa` int(11) NOT NULL DEFAULT 1,
  `GhiChu` varchar(255) DEFAULT NULL,
  `TinhTrangPhong` enum('Tot','CanVeSinh','HuHaiNhe','HuHaiNang','DangBaoTri') NOT NULL DEFAULT 'Tot',
  `HinhAnh` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`MaPhong`),
  UNIQUE KEY `SoPhong` (`SoPhong`)
) ENGINE=InnoDB AUTO_INCREMENT=306 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `phong`
INSERT INTO `phong` (`MaPhong`,`SoPhong`,`LoaiPhong`,`DienTich`,`LoaiGiuong`,`ViewPhong`,`Gia`,`TrangThai`,`SoKhachToiDa`,`GhiChu`,`TinhTrangPhong`,`HinhAnh`) VALUES
(1,101,'',20,'Đơn','Biển',500000.00,'Booked',3,'','Tot','1.png'),
(2,102,'Deluxe',30,'Đôi','Thành phố',800000.00,'Booked',3,NULL,'Tot','2.png'),
(3,201,'Suite',40,'King','Biển',1200000.00,'Booked',4,NULL,'Tot','3.png'),
(104,301,'Superior',28,'Đôi','Biển',700000.00,'Booked',3,NULL,'Tot','4.png'),
(105,302,'Superior',27,'Twin','Thành phố',680000.00,'Booked',3,NULL,'Tot','5.png'),
(106,303,'Deluxe',32,'King','Biển',850000.00,'Booked',3,NULL,'Tot','6.png'),
(107,304,'Deluxe',34,'Twin','Biển',880000.00,'Stayed',3,NULL,'Tot','7.png'),
(108,305,'Superior',29,'Đôi','Biển',720000.00,'Booked',3,NULL,'Tot','8.png'),
(109,306,'Deluxe',33,'King','Thành phố',830000.00,'Booked',3,NULL,'Tot','9.png'),
(110,307,'Deluxe',35,'King','Biển',900000.00,'Booked',4,NULL,'Tot','10.png'),
(111,308,'Superior',28,'Twin','Vườn',660000.00,'Booked',3,NULL,'Tot','11.png'),
(112,309,'Deluxe',36,'King','Biển',950000.00,'Booked',4,NULL,'Tot','12.png'),
(113,310,'Deluxe',33,'Đôi','Thành phố',820000.00,'Booked',3,NULL,'Tot','13.png'),
(114,401,'Deluxe',32,'Đôi','Biển',880000.00,'Booked',3,NULL,'Tot','14.png'),
(115,402,'Deluxe',34,'Twin','Thành phố',860000.00,'Booked',3,NULL,'Tot','15.png'),
(116,403,'Deluxe',36,'King','Biển',980000.00,'Booked',4,NULL,'Tot','16.png'),
(117,404,'Deluxe',35,'King','Biển',960000.00,'Booked',4,NULL,'Tot','17.png'),
(118,405,'Deluxe',33,'Đôi','Biển',900000.00,'Booked',3,NULL,'Tot','18.png'),
(119,406,'Deluxe',37,'King','Thành phố',870000.00,'Booked',4,NULL,'Tot','19.png'),
(120,407,'Deluxe',38,'King','Biển',995000.00,'Booked',4,NULL,'Tot','20.png'),
(121,408,'Deluxe',34,'Twin','Vườn',840000.00,'Booked',3,NULL,'Tot','21.png'),
(122,409,'Deluxe',33,'Đôi','Thành phố',865000.00,'Booked',3,NULL,'Tot','22.png'),
(123,410,'Deluxe',39,'King','Biển',1020000.00,'Booked',4,NULL,'Tot','23.png'),
(124,501,'Suite',40,'King','Biển',1200000.00,'Booked',4,NULL,'Tot','24.png'),
(125,502,'Suite',42,'King','Thành phố',1150000.00,'Booked',4,NULL,'Tot','25.png'),
(126,503,'Suite',45,'Twin','Biển',1250000.00,'Booked',4,NULL,'Tot','26.png'),
(127,504,'Suite',46,'King','Biển',1300000.00,'Booked',4,NULL,'Tot','27.png'),
(128,505,'Suite',44,'King','Thành phố',1180000.00,'Booked',4,NULL,'Tot','28.png'),
(129,506,'Suite',47,'King','Biển',1320000.00,'Booked',4,NULL,'Tot','29.png'),
(130,507,'Suite',48,'Twin','Biển',1280000.00,'Booked',4,NULL,'Tot','30.png'),
(131,508,'Suite',45,'King','Vườn',1100000.00,'Booked',4,NULL,'Tot','31.png'),
(132,509,'Suite',49,'King','Biển',1350000.00,'Trong',4,NULL,'Tot','32.png'),
(133,510,'Suite',50,'King','Thành phố',1220000.00,'Booked',4,NULL,'Tot','33.png'),
(134,601,'Suite',41,'King','Biển',1210000.00,'Booked',4,NULL,'Tot','34.png'),
(135,602,'Suite',43,'King','Thành phố',1160000.00,'Booked',4,NULL,'Tot','35.png'),
(136,603,'Deluxe',34,'Twin','Biển',880000.00,'Booked',3,NULL,'Tot','36.png'),
(137,604,'Deluxe',36,'King','Biển',910000.00,'Booked',3,NULL,'Tot','37.png'),
(138,605,'Suite',46,'King','Biển',1290000.00,'Booked',4,NULL,'Tot','38.png'),
(139,606,'Suite',48,'King','Thành phố',1240000.00,'Booked',4,NULL,'Tot','39.png'),
(140,607,'Deluxe',35,'Đôi','Vườn',860000.00,'Booked',3,NULL,'Tot','40.png'),
(141,608,'Deluxe',37,'King','Biển',940000.00,'Booked',3,NULL,'Tot','41.png'),
(142,609,'Suite',49,'King','Biển',1340000.00,'Booked',4,NULL,'Tot','42.png'),
(143,610,'Suite',50,'King','Thành phố',1250000.00,'Booked',4,NULL,'Tot','43.png'),
(144,701,'Suite',45,'King','Biển',1380000.00,'Trong',4,NULL,'Tot','44.png'),
(145,702,'Suite',47,'Twin','Biển',1400000.00,'Booked',4,NULL,'Tot','45.png'),
(146,703,'Suite',50,'King','Thành phố',1320000.00,'Booked',4,NULL,'Tot','46.png'),
(147,704,'Suite',48,'King','Biển',1390000.00,'Trong',4,NULL,'Tot','47.png'),
(148,705,'Suite',52,'King','Biển',1450000.00,'Trong',4,NULL,'Tot','48.png'),
(149,706,'Suite',51,'Twin','Vườn',1200000.00,'Booked',4,NULL,'Tot','49.png'),
(150,707,'Suite',49,'King','Biển',1360000.00,'Booked',4,NULL,'Tot','50.png'),
(176,1003,'VIP',74,'Twin','Biển',2400000.00,'Trong',5,NULL,'Tot','76.png'),
(177,1004,'VIP',76,'King','Biển',2700000.00,'Trong',5,NULL,'Tot','77.png'),
(178,1005,'VIP',80,'King','Thành phố',2300000.00,'Trong',5,NULL,'Tot','78.png'),
(180,1007,'VIP',78,'King','Biển',2900000.00,'Trong',5,NULL,'Tot','80.png'),
(181,1008,'VIP',85,'King','Biển',3200000.00,'Trong',5,NULL,'Tot','81.png'),
(182,1009,'VIP',83,'King','Thành phố',2400000.00,'Trong',5,NULL,'Tot','82.png'),
(183,1010,'VIP',88,'King','Biển',3500000.00,'Booked',5,NULL,'Tot','83.png'),
(303,'pa1111','Standard',25,'đôi','Biển',1000000.00,'Trong',5,NULL,'Tot','phong_1764664657_f976a135.webp'),
(304,'phong2','Standard',25,'đôi','kính',10000000.00,'Trong',4,NULL,'Tot','phong_1764679241_fb16ef6d.webp'),
(305,'pa000','Standard',67,'Đôi','Biển',6000000.00,'Trong',2,NULL,'Tot','');

-- Table structure for `taikhoan`
DROP TABLE IF EXISTS `taikhoan`;
CREATE TABLE `taikhoan` (
  `MaTK` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `NgayTao` datetime NOT NULL DEFAULT current_timestamp(),
  `TrangThai` enum('HoatDong','Khoa','Ngung') NOT NULL DEFAULT 'HoatDong',
  `MaVaiTro` int(11) NOT NULL,
  `MoTaQuyen` text DEFAULT NULL,
  `MaKhachHang` int(11) DEFAULT NULL,
  `MaNhanVien` int(11) DEFAULT NULL,
  PRIMARY KEY (`MaTK`),
  UNIQUE KEY `Username` (`Username`),
  KEY `MaVaiTro` (`MaVaiTro`),
  KEY `MaKhachHang` (`MaKhachHang`),
  KEY `MaNhanVien` (`MaNhanVien`),
  CONSTRAINT `taikhoan_ibfk_1` FOREIGN KEY (`MaVaiTro`) REFERENCES `vaitro` (`MaVaiTro`) ON UPDATE CASCADE,
  CONSTRAINT `taikhoan_ibfk_2` FOREIGN KEY (`MaKhachHang`) REFERENCES `khachhang` (`MaKhachHang`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `taikhoan_ibfk_3` FOREIGN KEY (`MaNhanVien`) REFERENCES `nhanvien` (`MaNhanVien`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `taikhoan`
INSERT INTO `taikhoan` (`MaTK`,`Username`,`Password`,`NgayTao`,`TrangThai`,`MaVaiTro`,`MoTaQuyen`,`MaKhachHang`,`MaNhanVien`) VALUES
(31,'tsang','$2y$10$kV/73WGz1H0ZdVjh4AvhDOeLANtmkGSwAL0lC1vkm0EkA508YkBZ2','2025-12-08 16:36:07','HoatDong',2,NULL,NULL,15),
(32,'Taaaaa','$2y$10$MEFd90xctdoprUSxiRovSegV4uLtjixtCwaG7aJQ83xMA5dOQQxHS','2025-12-08 16:49:00','HoatDong',7,NULL,28,NULL),
(35,'testcsdl','$2y$10$YdDwszDRtEVuIVZQ0/mtsuRxxN8Up1ZIUh.Uix8B7l/Yu/uldq9GG','2025-12-08 17:23:54','HoatDong',2,NULL,NULL,NULL),
(36,'bbbbbb','$2y$10$8/RtQuTyZ82IKwaRgnKqhukMU6yjgc0N515bhpsp63bf1tf2PzwwG','2025-12-08 17:29:02','HoatDong',4,NULL,NULL,NULL),
(37,'taoneee','$2y$10$dkQ1w9c19BsuGMAjYg6nVuS1h9U6YX7WGGdkxwzNfWOGQLrKMVu/y','2025-12-08 17:57:23','HoatDong',5,NULL,NULL,NULL),
(38,'ccccc','$2y$10$s4XeQUdKbTM22o6l0eCgHedMEcnMD.vTz/iEOHTmQ8PZMCxTGEBCm','2025-12-08 18:04:23','HoatDong',4,NULL,NULL,18),
(39,'aaabd','$2y$10$BlIeDyqanBtfVrnpRzWKQueKmGfMBg97khEUJnd7lTpJbtBH1V0xG','2025-12-08 18:06:34','HoatDong',7,NULL,30,NULL),
(40,'anhba','$2y$10$6bes1CSydOk/f5ayQw.R.eCuXoNtV7O0JMH88k.yJXa1PKQNkSuHe','2025-12-08 18:16:05','HoatDong',5,NULL,NULL,22);

-- Table structure for `thietbi`
DROP TABLE IF EXISTS `thietbi`;
CREATE TABLE `thietbi` (
  `MaThietBi` int(11) NOT NULL AUTO_INCREMENT,
  `TenThietBi` varchar(100) NOT NULL,
  `SoLuong` int(11) NOT NULL DEFAULT 1,
  `TinhTrang` varchar(50) NOT NULL DEFAULT 'Tốt',
  `MaPhong` int(11) DEFAULT NULL,
  PRIMARY KEY (`MaThietBi`),
  KEY `MaPhong` (`MaPhong`),
  CONSTRAINT `thietbi_ibfk_1` FOREIGN KEY (`MaPhong`) REFERENCES `phong` (`MaPhong`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `thietbi`
INSERT INTO `thietbi` (`MaThietBi`,`TenThietBi`,`SoLuong`,`TinhTrang`,`MaPhong`) VALUES
(1,'TV',1,'Tốt',1),
(2,'Máy lạnh',1,'Tốt',1),
(3,'Tủ lạnh mini',1,'Tốt',2);

-- Table structure for `vaitro`
DROP TABLE IF EXISTS `vaitro`;
CREATE TABLE `vaitro` (
  `MaVaiTro` int(11) NOT NULL AUTO_INCREMENT,
  `TenVaiTro` varchar(50) NOT NULL,
  `MoTa` text DEFAULT NULL,
  PRIMARY KEY (`MaVaiTro`),
  UNIQUE KEY `TenVaiTro` (`TenVaiTro`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `vaitro`
INSERT INTO `vaitro` (`MaVaiTro`,`TenVaiTro`,`MoTa`) VALUES
(1,'Admin','Quản trị toàn bộ hệ thống, phân quyền người dùng, quản lý phòng, vai trò và cấu hình hệ thống.'),
(2,'LeTan','Quản lý đặt phòng, check-in, check-out, cập nhật trạng thái phòng và hỗ trợ khách trực tiếp.'),
(3,'KeToan','Theo dõi hóa đơn, thanh toán, doanh thu, chi phí và lập báo cáo tài chính.'),
(4,'DichVu','Quản lý dịch vụ đi kèm, tạo đơn dịch vụ, cập nhật tình trạng dịch vụ và hỗ trợ khách hàng sử dụng dịch vụ.'),
(5,'CSKH','Quản lý phản hồi khách hàng, chương trình khuyến mãi, chăm sóc khách hàng thân thiết.'),
(6,'QuanLy','Quản lý cấp cao, xem toàn bộ báo cáo, tình trạng phòng, doanh thu, khách hàng, nhân viên.'),
(7,'KhachHang','Khách hàng cá nhân: xem lịch sử đặt phòng, đặt phòng, sử dụng dịch vụ và cập nhật thông tin cá nhân.');

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
