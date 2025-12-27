-- ABC Resort Backup File
-- Created: 2025-12-08 16:45:23
-- Database: abc_resort1
-- Type: toan_bo
-- Created by: admin
-- 
-- Metadata: {
    "version": "1.0",
    "type": "toan_bo",
    "database": "abc_resort1",
    "created_by": "admin",
    "created_at": "2025-12-08 16:45:23",
    "php_version": "8.2.12",
    "mysql_version": "10.4.32-MariaDB"
}
-- 

-- Database Export
-- Database: abc_resort1
-- Date: 2025-12-08 16:45:23

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
INSERT INTO `chiphi` (`MaCP`,`TenChiPhi`,`NgayChi`,`SoTien`,`NoiDung`,`TrangThai`) VALUES
(1,'Điện nước tháng 10','2025-10-31',15000000.00,'Chi phí điện nước toàn resort','DaDuyet'),
(2,'Chi phí điện nước Tháng 11','2025-11-01',12000000.00,'Hóa đơn điện nước','DaDuyet'),
(3,'Chi phí lương nhân viên','2025-11-05',45000000.00,'Chi lương tháng 11','DaDuyet'),
(4,'Chi phí sửa chữa phòng','2025-11-10',8000000.00,'Sửa phòng Deluxe 102','DaDuyet'),
(5,'Chi phí mua vật tư','2025-11-15',5000000.00,'Mua ga giường và khăn tắm','DaDuyet'),
(6,'Chi phí marketing','2025-11-20',7000000.00,'Chạy quảng cáo Facebook','DaDuyet');

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
INSERT INTO `chitietdichvu` (`MaCTDV`,`MaGiaoDich`,`MaPhong`,`MaDichVu`,`SoLuong`,`GiaBan`,`ThanhTien`,`ThoiDiemGhiNhan`,`GhiChu`,`NgayDat`,`TrangThaiDichVu`) VALUES
(1,1,NULL,1,3,300000.00,900000.00,'2025-11-15 02:50:35','Đặt kèm khi đặt phòng','2025-11-27 03:27:12','ChuaSuDung'),
(2,1,NULL,2,3,50000.00,150000.00,'2025-11-15 02:50:35','Đặt kèm khi đặt phòng','2025-11-27 03:27:12','ChuaSuDung'),
(3,1,NULL,3,4,400000.00,1600000.00,'2025-11-15 02:50:35','Đặt kèm khi đặt phòng','2025-11-27 03:27:12','ChuaSuDung'),
(4,2,NULL,1,3,300000.00,900000.00,'2025-11-15 02:50:45','Đặt kèm khi đặt phòng','2025-11-27 03:27:12','ChuaSuDung'),
(5,2,NULL,2,3,50000.00,150000.00,'2025-11-15 02:50:45','Đặt kèm khi đặt phòng','2025-11-27 03:27:12','ChuaSuDung'),
(6,2,NULL,3,4,400000.00,1600000.00,'2025-11-15 02:50:45','Đặt kèm khi đặt phòng','2025-11-27 03:27:12','ChuaSuDung'),
(7,4,NULL,1,2,300000.00,600000.00,'2025-11-16 02:54:11','Đặt kèm khi đặt phòng','2025-11-27 03:27:12','ChuaSuDung'),
(8,4,NULL,2,1,50000.00,50000.00,'2025-11-16 02:54:11','Đặt kèm khi đặt phòng','2025-11-27 03:27:12','ChuaSuDung'),
(9,7,182,2,2,50000.00,100000.00,'2025-11-17 18:34:05','gihi','2025-11-27 03:27:12','ChuaSuDung'),
(10,8,NULL,1,1,300000.00,300000.00,'2025-11-18 22:09:26','Đặt kèm khi đặt phòng','2025-11-27 03:27:12','ChuaSuDung'),
(11,7,182,3,2,400000.00,800000.00,'2025-11-21 01:15:28','','2025-11-27 03:27:12','ChuaSuDung'),
(12,7,108,2,1,50000.00,50000.00,'2025-11-21 01:15:42','','2025-11-27 03:27:12','ChuaSuDung'),
(13,7,108,3,3,400000.00,1200000.00,'2025-11-21 01:16:12','','2025-11-27 03:27:12','ChuaSuDung'),
(16,34,NULL,1,1,0.00,300000.00,'2025-11-27 02:35:20',NULL,'2025-11-27 03:27:12','ChuaSuDung'),
(17,34,NULL,1,1,0.00,300000.00,'2025-11-27 02:36:34',NULL,'2025-11-27 03:27:12','ChuaSuDung'),
(18,34,NULL,1,1,0.00,300000.00,'2025-11-27 02:37:27',NULL,'2025-11-27 03:27:12','ChuaSuDung'),
(19,34,NULL,1,1,0.00,300000.00,'2025-11-27 02:37:42',NULL,'2025-11-27 03:27:12','ChuaSuDung'),
(20,34,NULL,2,1,0.00,50000.00,'2025-11-27 02:43:22',NULL,'2025-11-27 03:27:12','ChuaSuDung'),
(21,34,NULL,1,1,0.00,300000.00,'2025-11-27 02:47:40',NULL,'2025-11-27 03:27:12','ChuaSuDung'),
(24,34,140,2,1,50000.00,50000.00,'2025-11-27 03:30:19','','2025-11-27 03:30:19','DangSuDung'),
(25,55,131,2,1,50000.00,50000.00,'2025-11-28 01:45:58','','2025-11-28 01:45:58','ChuaSuDung'),
(26,77,129,1,1,300000.00,300000.00,'2025-11-28 01:46:39','','2025-11-28 01:46:39','ChuaSuDung'),
(27,77,129,1,1,300000.00,300000.00,'2025-11-28 01:50:50','','2025-11-28 01:50:50','ChuaSuDung');

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
INSERT INTO `chitietgiaodich` (`MaCTGD`,`MaGiaoDich`,`MaPhong`,`SoNguoi`,`NgayNhanDuKien`,`NgayTraDuKien`,`NgayCheckIn`,`NgayCheckOut`,`DonGia`,`ThanhTien`,`TienPhuThu`,`TienBoiThuong`,`TrangThai`,`GhiChu`,`TenKhach`,`CCCD`,`SDT`,`Email`) VALUES
(1,1,1,5,'2025-11-14 14:00:00','2025-11-15 12:00:00',NULL,NULL,500000.00,500000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL),
(2,1,2,5,'2025-11-14 14:00:00','2025-11-15 12:00:00',NULL,NULL,800000.00,800000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL),
(3,2,3,5,'2025-11-14 14:00:00','2025-11-15 12:00:00',NULL,NULL,1200000.00,1200000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL),
(4,3,104,5,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,700000.00,700000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL),
(6,4,177,4,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,2700000.00,2700000.00,0.00,0.00,'DaHuy','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL),
(7,5,106,3,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,850000.00,850000.00,0.00,0.00,'DaHuy','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL),
(8,5,107,3,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,880000.00,880000.00,0.00,0.00,'DaHuy','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL),
(9,6,105,2,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,680000.00,680000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL),
(10,7,182,1,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,850000.00,850000.00,0.00,0.00,'Stayed','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL),
(11,7,107,3,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,880000.00,880000.00,0.00,0.00,'Stayed','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL),
(12,7,108,3,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,720000.00,720000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL),
(13,7,109,3,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,830000.00,830000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL),
(14,8,112,2,'2025-11-18 14:00:00','2025-11-19 12:00:00',NULL,NULL,950000.00,950000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL),
(15,8,113,2,'2025-11-18 14:00:00','2025-11-19 12:00:00',NULL,NULL,820000.00,820000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL),
(31,27,136,1,'2025-11-26 00:00:00','2025-11-27 00:00:00',NULL,NULL,880000.00,880000.00,0.00,0.00,'DaHuy','Đặt phòng online','Lê Thị Kim Oanh',12345678912,912345675,'11hdgdy@gmail.com'),
(32,28,140,1,'2025-11-26 00:00:00','2025-11-27 00:00:00',NULL,NULL,860000.00,860000.00,0.00,0.00,'DaHuy','Đặt phòng online','Lê Thị Kim Oanh',12345678912,912345675,'11hdgdy@gmail.com'),
(34,30,118,2,'2025-12-03 00:00:00','2025-12-06 00:00:00',NULL,NULL,900000.00,2700000.00,0.00,0.00,'DaHuy','Đặt phòng online','Lê Thị Kim Oanh',12345678912,912345675,'11hdgdy@gmail.com'),
(35,31,140,1,'2025-11-26 00:00:00','2025-11-27 00:00:00',NULL,NULL,860000.00,860000.00,0.00,0.00,'DaHuy','Đặt phòng online','Lê Thị Kim Oanh',12345678912,912345675,'11hdgdy@gmail.com'),
(36,32,140,2,'2025-11-26 00:00:00','2025-11-27 00:00:00',NULL,NULL,860000.00,860000.00,0.00,0.00,'DaHuy','Đặt phòng online','Lê Thị Kim Oanh',12345678912,345628127,'11hdgdy@gmail.com'),
(37,33,140,2,'2025-11-28 00:00:00','2025-11-29 00:00:00',NULL,NULL,860000.00,860000.00,0.00,0.00,'DaHuy','Đặt phòng online','Lê Thị Kim Oanh',12345678912,345628127,'11hdgdy@gmail.com'),
(38,34,140,1,'2025-11-29 00:00:00','2025-12-01 00:00:00',NULL,NULL,860000.00,1720000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,345628127,'11hdgdy@gmail.com'),
(39,35,136,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,880000.00,880000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,345628127,'11hdgdy@gmail.com'),
(46,48,141,2,'2025-12-02 00:00:00','2026-01-08 00:00:00',NULL,NULL,940000.00,34780000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,345628127,'11hdgdy@gmail.com'),
(47,49,116,2,'2025-11-30 00:00:00','2025-12-01 00:00:00',NULL,NULL,980000.00,980000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,345628127,'11hdgdy@gmail.com'),
(48,50,117,3,'2025-12-02 00:00:00','2025-12-03 00:00:00',NULL,NULL,960000.00,960000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,345628127,'11hdgdy@gmail.com'),
(49,51,135,2,'2025-12-05 00:00:00','2025-12-06 00:00:00',NULL,NULL,1160000.00,1160000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,345628127,'11hdgdy@gmail.com'),
(50,52,123,2,'2025-12-02 00:00:00','2025-12-03 00:00:00',NULL,NULL,1020000.00,1020000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,345628127,'11hdgdy@gmail.com'),
(51,53,125,1,'2025-11-28 00:00:00','2025-11-29 00:00:00',NULL,NULL,1150000.00,1150000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,345628127,'11hdgdy@gmail.com'),
(52,54,120,2,'2025-11-28 00:00:00','2025-11-29 00:00:00',NULL,NULL,995000.00,995000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,345628127,'11hdgdy@gmail.com'),
(53,55,131,2,'2025-11-30 00:00:00','2025-12-01 00:00:00',NULL,NULL,1100000.00,1100000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,345628127,'11hdgdy@gmail.com'),
(54,56,128,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1180000.00,1180000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,345628127,'11hdgdy@gmail.com'),
(55,57,124,2,'2025-12-13 00:00:00','2025-12-16 00:00:00',NULL,NULL,1200000.00,3600000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,345628127,'11hdgdy@gmail.com'),
(56,58,134,2,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1210000.00,1210000.00,0.00,0.00,'Booked','Đặt phòng online','Nguyễn Hải Đăng ',12345678912,345628127,'11hdgdy@gmail.com'),
(57,59,149,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1200000.00,1200000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,345628127,'11hdgdy@gmail.com'),
(58,60,133,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1220000.00,1220000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,345628127,'11hdgdy@gmail.com'),
(59,61,126,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1250000.00,1250000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,3274732642,'11hdgdy@gmail.com'),
(60,62,143,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1250000.00,1250000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,3274732642,'11hdgdy@gmail.com'),
(61,63,143,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1250000.00,1250000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,3274732642,'11hdgdy@gmail.com'),
(62,64,143,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1250000.00,1250000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,3274732642,'11hdgdy@gmail.com'),
(63,69,139,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1240000.00,1240000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL),
(64,70,138,1,'2025-12-30 00:00:00','2026-01-01 00:00:00',NULL,NULL,1290000.00,2580000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL),
(65,71,130,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1280000.00,1280000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL),
(66,72,129,2,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1320000.00,1320000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL),
(67,73,129,2,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1320000.00,1320000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL),
(68,75,129,2,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1320000.00,1320000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL),
(69,76,129,2,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1320000.00,1320000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL),
(70,77,129,2,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1320000.00,1320000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL),
(71,78,146,2,'2025-12-02 00:00:00','2025-12-03 00:00:00',NULL,NULL,1320000.00,1320000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL),
(72,79,127,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1300000.00,1300000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL),
(73,80,142,1,'2025-11-30 00:00:00','2025-12-01 00:00:00',NULL,NULL,1340000.00,1340000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL),
(74,84,150,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1360000.00,1360000.00,0.00,0.00,'Booked','Đặt phòng online','Nguyễn Hải Đăng ',12345678912,3274732642,'11hdgdy@gmail.com'),
(75,85,145,1,'2025-11-27 00:00:00','2025-11-29 00:00:00',NULL,NULL,1400000.00,2800000.00,0.00,0.00,'Booked','Đặt phòng online','Nguyễn Hải Đăng ',12345678912,3274732642,'11hdgdy@gmail.com'),
(76,86,145,1,'2025-11-27 00:00:00','2025-11-29 00:00:00',NULL,NULL,1400000.00,2800000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',12345678912,3274732642,'11hdgdy@gmail.com'),
(77,87,145,1,'2025-11-28 00:00:00','2025-11-29 00:00:00',NULL,NULL,1400000.00,1400000.00,0.00,0.00,'Booked','Đặt phòng online','Nguyễn Hải Đăng ',12345678912,3274732642,'11hdgdy@gmail.com');

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
INSERT INTO `doan` (`MaDoan`,`TenDoan`,`MaTruongDoan`,`SoNguoi`,`NgayDen`,`NgayDi`,`GhiChu`) VALUES
(1,'Đoàn huo huy',4,1,NULL,NULL,''),
(2,'Đoàn ngo duy thong',11,1,NULL,NULL,'');

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
INSERT INTO `giaodich` (`MaGiaoDich`,`MaKhachHang`,`MaDoan`,`MaNhanVien`,`MaKhuyenMai`,`NgayGiaoDich`,`LoaiGiaoDich`,`TongTien`,`TrangThai`,`PhuongThucThanhToan`,`GhiChu`) VALUES
(1,3,NULL,1,NULL,'2025-11-15 02:50:35','DatPhong',3950000.00,'Booked','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(2,3,NULL,1,NULL,'2025-11-15 02:50:45','DatPhong',3850000.00,'Booked','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(3,3,NULL,1,NULL,'2025-11-16 02:23:17','DatPhong',700000.00,'Booked','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(4,3,NULL,1,NULL,'2025-11-16 02:54:11','DatPhong',6150000.00,'DaHuy','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(5,3,NULL,1,NULL,'2025-11-17 01:39:02','DatPhong',1730000.00,'DaHuy','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(6,3,NULL,1,NULL,'2025-11-17 02:24:22','DatPhong',680000.00,'Booked','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(7,3,NULL,1,NULL,'2025-11-17 02:26:15','DatPhong',1835000.00,'Stayed','ChuyenKhoan','Đặt phòng trực tiếp tại quầy | Check-in 2025-11-17 11:55:48\nCheck-out lúc 2025-11-17 19:03:45; PTTT: TienMat; Tổng: 3.380.000đ; Phụ thu: 0đ; Bồi thường: 0đ\nCheck-out lúc 2025-11-17 19:04:19; PTTT: TienMat; Tổng: 3.380.000đ; Phụ thu: 0đ; Bồi thường: 0đ | Check-in 2025-11-17 20:12:24\nCheck-out lúc 2025-11-17 21:02:23; PTTT: TienMat; Tổng: 950.000đ; Phụ thu: 0đ; Bồi thường: 0đ\nCheck-out lúc 2025-11-21 13:53:10; PTTT: ChuyenKhoan; Tổng: 1.835.000đ; Phụ thu: 85.000đ; Bồi thường: 0đ'),
(8,3,NULL,1,NULL,'2025-11-18 22:09:26','DatPhong',2070000.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(10,1,NULL,NULL,NULL,'2025-11-23 21:53:15','DatPhong',3500000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(11,1,NULL,NULL,NULL,'2025-11-23 22:08:09','DatPhong',840000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(12,1,NULL,NULL,NULL,'2025-11-26 21:11:33','DatPhong',660000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(13,1,NULL,NULL,NULL,'2025-11-26 23:15:37','DatPhong',865000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(14,1,NULL,NULL,NULL,'2025-11-26 23:18:18','DatPhong',860000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(15,1,NULL,NULL,NULL,'2025-11-26 23:44:27','DatPhong',860000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(16,1,NULL,NULL,NULL,'2025-11-26 23:44:53','DatPhong',720000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(17,1,NULL,NULL,NULL,'2025-11-26 23:48:56','DatPhong',720000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(18,1,NULL,NULL,NULL,'2025-11-26 23:49:08','DatPhong',850000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(19,1,NULL,NULL,NULL,'2025-11-26 23:50:38','DatPhong',850000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(20,1,NULL,NULL,NULL,'2025-11-26 23:51:01','DatPhong',870000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(21,1,NULL,NULL,NULL,'2025-11-26 23:53:20','DatPhong',870000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(22,1,NULL,NULL,NULL,'2025-11-26 23:53:36','DatPhong',880000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(23,1,NULL,NULL,NULL,'2025-11-26 23:58:40','DatPhong',880000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(24,1,NULL,NULL,NULL,'2025-11-26 23:58:55','DatPhong',900000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(27,1,NULL,NULL,NULL,'2025-11-27 00:03:08','DatPhong',880000.00,'DaHuy','ChuaThanhToan','Đặt phòng online'),
(28,1,NULL,NULL,NULL,'2025-11-27 00:21:37','DatPhong',860000.00,'DaHuy','ChuaThanhToan','Đặt phòng online'),
(29,1,NULL,NULL,NULL,'2025-11-27 00:27:46','DatPhong',900000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(30,1,NULL,NULL,NULL,'2025-11-27 00:42:38','DatPhong',2700000.00,'DaHuy','ChuaThanhToan','Đặt phòng online'),
(31,1,NULL,NULL,NULL,'2025-11-27 01:07:46','DatPhong',860000.00,'DaHuy','ChuaThanhToan','Đặt phòng online'),
(32,1,NULL,NULL,NULL,'2025-11-27 01:12:00','DatPhong',860000.00,'DaHuy','ChuaThanhToan','Đặt phòng online'),
(33,1,NULL,NULL,NULL,'2025-11-27 01:14:45','DatPhong',860000.00,'DaHuy','ChuaThanhToan','Đặt phòng online'),
(34,1,NULL,NULL,NULL,'2025-11-27 02:35:04','DatPhong',3320000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(35,1,NULL,NULL,NULL,'2025-11-27 04:58:10','DatPhong',880000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(36,1,NULL,NULL,NULL,'2025-11-27 22:37:41','DatPhong',0.00,'Booked','ChuaThanhToan',NULL),
(37,1,NULL,NULL,NULL,'2025-11-27 22:40:12','DatPhong',0.00,'Booked','ChuaThanhToan',NULL),
(38,1,NULL,NULL,NULL,'2025-11-27 22:41:02','DatPhong',0.00,'Booked','ChuaThanhToan',NULL),
(39,1,NULL,NULL,NULL,'2025-11-27 22:43:24','DatPhong',0.00,'Booked','ChuaThanhToan',NULL),
(40,1,NULL,NULL,NULL,'2025-11-27 22:45:06','DatPhong',0.00,'Booked','ChuaThanhToan',NULL),
(41,1,NULL,NULL,NULL,'2025-11-27 22:45:12','DatPhong',0.00,'Booked','ChuaThanhToan',NULL),
(42,1,NULL,NULL,NULL,'2025-11-27 22:51:51','DatPhong',0.00,'Booked','ChuaThanhToan',NULL),
(43,1,NULL,NULL,NULL,'2025-11-27 23:00:43','DatPhong',940000.00,'Booked','ChuaThanhToan',NULL),
(44,1,NULL,NULL,NULL,'2025-11-27 23:02:06','DatPhong',940000.00,'Booked','ChuaThanhToan',NULL),
(45,1,NULL,NULL,NULL,'2025-11-27 23:09:48','DatPhong',940000.00,'Booked','ChuaThanhToan',NULL),
(46,1,NULL,NULL,NULL,'2025-11-27 23:13:00','DatPhong',940000.00,'Booked','ChuaThanhToan',NULL),
(47,1,NULL,NULL,NULL,'2025-11-27 23:15:05','DatPhong',940000.00,'Booked','ChuaThanhToan',NULL),
(48,1,NULL,NULL,NULL,'2025-11-27 23:18:51','DatPhong',34780000.00,'Booked','ChuaThanhToan',NULL),
(49,1,NULL,NULL,NULL,'2025-11-27 23:20:32','DatPhong',980000.00,'Booked','ChuaThanhToan',NULL),
(50,1,NULL,NULL,NULL,'2025-11-27 23:23:27','DatPhong',960000.00,'Booked','ChuaThanhToan',NULL),
(51,1,NULL,NULL,NULL,'2025-11-27 23:25:03','DatPhong',1160000.00,'Booked','ChuaThanhToan',NULL),
(52,1,NULL,NULL,NULL,'2025-11-27 23:33:57','DatPhong',1020000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(53,1,NULL,NULL,NULL,'2025-11-27 23:36:29','DatPhong',1150000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(54,1,NULL,NULL,NULL,'2025-11-27 23:40:48','DatPhong',995000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(55,1,NULL,NULL,NULL,'2025-11-28 00:36:46','DatPhong',1150000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(56,1,NULL,NULL,NULL,'2025-11-28 00:45:08','DatPhong',1180000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(57,1,NULL,NULL,NULL,'2025-11-28 00:46:21','DatPhong',3600000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(58,1,NULL,NULL,NULL,'2025-11-28 00:47:27','DatPhong',1210000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(59,1,NULL,NULL,NULL,'2025-11-28 00:49:03','DatPhong',1200000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(60,1,NULL,NULL,NULL,'2025-11-28 00:50:36','DatPhong',1220000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(61,1,NULL,NULL,NULL,'2025-11-28 01:06:18','DatPhong',1250000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(62,1,NULL,NULL,NULL,'2025-11-28 01:08:08','DatPhong',1250000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(63,1,NULL,NULL,NULL,'2025-11-28 01:10:39','DatPhong',1250000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(64,1,NULL,NULL,NULL,'2025-11-28 01:12:09','DatPhong',1250000.00,'Booked','ChuaThanhToan',NULL),
(65,1,NULL,NULL,NULL,'2025-11-28 01:18:58','DatPhong',1240000.00,'Booked','ChuaThanhToan',NULL),
(66,1,NULL,NULL,NULL,'2025-11-28 01:21:30','DatPhong',2480000.00,'Booked','ChuaThanhToan',NULL),
(67,1,NULL,NULL,NULL,'2025-11-28 01:22:53','DatPhong',1290000.00,'Booked','ChuaThanhToan',NULL),
(69,1,NULL,NULL,NULL,'2025-11-28 01:31:26','DatPhong',1240000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(70,1,NULL,NULL,NULL,'2025-11-28 01:33:10','DatPhong',2580000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(71,1,NULL,NULL,NULL,'2025-11-28 01:36:53','DatPhong',1280000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(72,1,NULL,NULL,NULL,'2025-11-28 01:39:15','DatPhong',1320000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(73,1,NULL,NULL,NULL,'2025-11-28 01:40:46','DatPhong',1320000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(74,1,NULL,NULL,NULL,'2025-11-28 01:41:33','DatPhong',1320000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(75,1,NULL,NULL,NULL,'2025-11-28 01:43:53','DatPhong',1320000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(76,1,NULL,NULL,NULL,'2025-11-28 01:45:16','DatPhong',1320000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(77,1,NULL,NULL,NULL,'2025-11-28 01:45:23','DatPhong',1920000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(78,1,NULL,NULL,NULL,'2025-11-28 01:51:14','DatPhong',1320000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(79,1,NULL,NULL,NULL,'2025-11-28 01:56:54','DatPhong',1300000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(80,1,NULL,NULL,NULL,'2025-11-28 02:00:14','DatPhong',1340000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(84,1,NULL,NULL,NULL,'2025-11-28 02:11:07','DatPhong',1360000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(85,1,NULL,NULL,NULL,'2025-11-28 02:53:26','DatPhong',2800000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(86,1,NULL,NULL,NULL,'2025-11-28 02:56:58','DatPhong',2800000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(87,1,NULL,NULL,NULL,'2025-11-28 03:01:44','DatPhong',1400000.00,'Booked','ChuaThanhToan','Đặt phòng online');

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
INSERT INTO `khuyenmai` (`MaKhuyenMai`,`TenChuongTrinh`,`NgayBatDau`,`NgayKetThuc`,`MucUuDai`,`DoiTuong`,`TrangThai`) VALUES
(1,'Giảm 20% Mùa Hè','2025-06-01','2025-08-31',20.00,'Khách lẻ','DangApDung');

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
INSERT INTO `phanhoi` (`MaPH`,`MaKhachHang`,`LoaiDichVu`,`MucDoHaiLong`,`TepDinhKem`,`HoTenKH`,`Email`,`SDT`,`NoiDung`,`TinhTrang`,`NgayPhanHoi`) VALUES
(1,1,NULL,5,NULL,NULL,NULL,NULL,'','ChuaXuLy','2025-11-26 22:03:02'),
(2,1,'',5,NULL,'khach1','','','Tuyệt','ChuaXuLy','2025-11-26 22:05:41'),
(3,1,'',5,NULL,'khach1','','','Tuyệt vời','ChuaXuLy','2025-11-26 22:06:02'),
(5,1,'',5,NULL,'khach1','','','d','ChuaXuLy','2025-11-26 22:10:30'),
(6,1,'Nhà hàng',5,NULL,NULL,NULL,NULL,'tuyệt','ChuaXuLy','2025-11-26 22:24:49'),
(7,1,'Nhà hàng',5,'fb_1764170748_1631.jpg',NULL,NULL,NULL,'tốt','ChuaXuLy','2025-11-26 22:25:48'),
(8,1,'SPA',5,NULL,NULL,NULL,NULL,'','ChuaXuLy','2025-11-28 03:04:15');

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
(1,'admin','$2y$10$Wv8SmiyoS1CRNjQYWrJn1e5UaQyEdBAS.qAY9w7s5dd/wgcHOEmYm','2025-11-14 16:57:18','HoatDong',1,'Toàn quyền hệ thống: quản lý người dùng, phân quyền, quản lý phòng, dịch vụ, khuyến mãi, báo cáo, cấu hình hệ thống.',NULL,NULL),
(2,'letan1','$2y$10$NCAVZdKihPxrJLWngOAG.uVYYNxhCwdGzh4frSU5CAsWiNqf7oufa','2025-11-14 16:57:18','HoatDong',2,'Đặt phòng, sửa đặt phòng, hủy phòng, check-in, check-out, quản lý trạng thái phòng, hỗ trợ khách trực tiếp.',NULL,1),
(3,'ketoan1','$2y$10$.JWLKNs1T0C0nAeLh7RFyOxansHFjVYWtzwZFWO.0d3DEuGOuDeZe','2025-11-14 16:57:18','HoatDong',3,'Quản lý dịch vụ đi kèm, nhận yêu cầu dịch vụ, cập nhật tiến trình xử lý, báo cáo sử dụng dịch vụ.',NULL,2),
(4,'dichvu1','$2y$10$mvGI4Zd3Nz4wws36k4hEF.x3a7EStAvMSIf6MkYWVGSfBWYF6zBZm','2025-11-14 16:57:18','HoatDong',4,'Quản lý hóa đơn, thanh toán, đối soát doanh thu, xem báo cáo tài chính, xử lý các khoản phí phát sinh.',NULL,3),
(5,'cskh1','$2y$10$L1RPs0dZGWOe9R.Jo1hsZ.0p3a/xQkrBBVuTzy7prpBpOX512wA5S','2025-11-14 16:57:18','HoatDong',5,'Xử lý phản hồi khách hàng, tạo & quản lý chương trình khuyến mãi, chăm sóc khách hàng thân thiết.',NULL,4),
(6,'quanly1','$2y$10$1X9Gw9hhksu9DvsflYiE0O2SYz72rhH0iln4p9r77FpyhAWDwgElO','2025-11-14 16:57:18','HoatDong',6,'Quyền giám sát nghiệp vụ, xem báo cáo tổng hợp, phê duyệt thao tác của các bộ phận, quản lý hoạt động vận hành.',NULL,5),
(7,'khach1','$2y$10$HhnkjTuk24cCbUVatzIzAuhSjjC6YwkfalX6ZZDESg0zhtHyrEbaq','2025-11-14 16:57:18','HoatDong',7,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',1,NULL),
(9,'D001_Leader','$2y$10$Ab2ipboVLcuZ6.9om7QvNu2rYvjlbmC93O6TlKa9Md8WNhnA0vnsC','2025-11-16 19:27:05','HoatDong',7,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',4,NULL),
(10,'D001_M1','$2y$10$BUDg2qSomZJspnmopvxDi.3ib7DxZxXT69SbO.XcCPzIU9XSJJxlW','2025-11-16 19:27:05','HoatDong',7,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',5,NULL),
(12,'vnt181','$2y$10$Hk3UKch06aZS5.tpou9oz.WJ/ybpis1IgrIzG4jk9BNbOJqsjxGD6','2025-11-24 01:20:41','HoatDong',7,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',NULL,NULL),
(13,'guest','$2y$10$DkjHlbQCBU7gECxeC5.q.OoNv.0nbfwAkgr3XwVANjdeRLjCa5jse','2025-11-24 01:21:35','HoatDong',7,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',NULL,NULL),
(14,'bento181','$2y$10$A3wN9.kREzy8wXj41IBijOKVAaf7rt658Pso28RHxqcy1opwhApy2','2025-11-24 01:36:09','HoatDong',6,'Quản lý: giám sát toàn bộ hoạt động và báo cáo.',NULL,NULL),
(15,'bento2004','$2y$10$SRODgxnojIce2mhhx4XbJeIpMCPZtXgTUFizaQ0NkQFXTrJSRH0Su','2025-11-24 02:23:20','HoatDong',7,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',NULL,NULL),
(16,'bento900','$2y$10$Zmf2w55Ty7jN619DYN3KieSirc9aYEkDPCIqn1E8VhNKQjWUoOY8i','2025-11-24 02:29:16','HoatDong',2,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',NULL,NULL),
(17,'D002_Leader','$2y$10$mrIlNOiJxEgkzx/ozgluROGb1kg2bTIZprVk11eisK1Bd4mHl0lK6','2025-11-28 12:42:01','HoatDong',3,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',11,NULL),
(18,'D002_M1','$2y$10$hijELmBVjj4jWZ3HyOXH9uJI9xgvUh.vOXbrQi/.ruq8Hw5WJEvYq','2025-11-28 12:42:01','HoatDong',1,'',12,NULL),
(19,'bhihii12@gmail.com','$2y$10$pEg4VPxW0CTTUC3xydQFFOsSfEp9abEzennctzKfzXBNyyW7IyIcy','2025-11-28 16:44:23','HoatDong',7,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',NULL,NULL),
(23,'Sang2','$2y$10$/F7dbypQnK2kIGRqwpIX/Ogk9.5iXAf6I6UPriCjBRhkLqfcmPc.C','2025-12-01 00:39:12','HoatDong',1,NULL,19,NULL),
(24,'Sang0','$2y$10$HbM9vb7pgS0TmLmayV.6HO.1Z.sMz1U.MKKFh2C3Ff1F8rzCDoX62','2025-12-02 14:03:00','HoatDong',1,NULL,20,NULL),
(25,'Baone','$2y$10$EFSF58v/mL4.am./14Pv4ufQRfsSMOjJMjr8DmvHZbr7teNoI/FG6','2025-12-03 01:53:59','HoatDong',7,NULL,23,NULL),
(26,'Doanne','$2y$10$qfWcqDfpIbgeX/pHJG7Fg.Ka/FosMZNPQCP1/idWTs4.w./XuZ5gK','2025-12-03 01:55:22','HoatDong',6,NULL,22,NULL),
(27,'aaaaaa','$2y$10$MGjbNB0rMMyIJ8ZFVJcGse7giViPl62G0Qnej4jfK0P4bXjPWe4v6','2025-12-03 02:00:52','HoatDong',6,'Quản lý: giám sát tổng thể, xem báo cáo chi tiết, quản lý nhân sự.',NULL,9),
(28,'Locne','$2y$10$pPjFujxZspes74rHFbBG1uX34YyFkMW3JQJXglKlZuu2I5Qknitze','2025-12-03 02:04:19','HoatDong',4,'Nhân viên dịch vụ: nhận yêu cầu dịch vụ, xử lý và theo dõi tiến độ.',NULL,10),
(29,'nhiine','$2y$10$1FCanQsHy5A6TfYDuX8UBuc14lPVcle403km5.9Ho4BFDkwuwyAYm','2025-12-03 02:25:27','HoatDong',5,NULL,NULL,NULL),
(30,'nhiinee','$2y$10$wbZxoSWkmSy2QUosaOehM.ibNV5pcurFP1.Z9QwdiM6mWx2gFkVTW','2025-12-03 02:31:39','HoatDong',7,'Khách hàng: xem thông tin cá nhân và lịch sử đặt phòng.',NULL,8),
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
