-- ABC Resort Backup File
-- Created: 2025-12-11 01:56:45
-- Database: abc_resort1
-- Type: toan_bo
-- Created by: admin
-- 
-- Metadata:
-- {
--     "version": "1.0",
--     "type": "toan_bo",
--     "database": "abc_resort1",
--     "created_by": "admin",
--     "created_at": "2025-12-11 01:56:45",
--     "php_version": "8.2.12",
--     "mysql_version": "10.4.32-MariaDB"
-- }
-- 

-- Database Export
-- Database: abc_resort1
-- Date: 2025-12-11 01:56:45

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
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(19,34,NULL,1,1,0.00,300000.00,'2025-11-27 02:37:42',NULL,'2025-11-27 03:27:12','ChuaSuDung'),
(20,34,NULL,2,1,0.00,50000.00,'2025-11-27 02:43:22',NULL,'2025-11-27 03:27:12','ChuaSuDung'),
(24,34,140,2,1,50000.00,50000.00,'2025-11-27 03:30:19','','2025-11-27 03:30:19','DangSuDung'),
(26,77,129,1,1,300000.00,300000.00,'2025-11-28 01:46:39','','2025-11-28 01:46:39','ChuaSuDung'),
(27,77,129,1,1,300000.00,300000.00,'2025-11-28 01:50:50','','2025-11-28 01:50:50','ChuaSuDung'),
(29,80,142,2,1,50000.00,50000.00,'2025-11-30 19:44:30','','2025-11-30 19:44:30','ChuaSuDung'),
(30,88,NULL,1,1,300000.00,300000.00,'2025-12-05 01:05:43','Đặt kèm khi đặt phòng','2025-12-05 01:05:43','ChuaSuDung'),
(31,89,NULL,1,1,300000.00,300000.00,'2025-12-05 16:23:29','Đặt kèm khi đặt phòng','2025-12-05 16:23:29','ChuaSuDung'),
(32,89,NULL,2,1,50000.00,50000.00,'2025-12-05 16:23:29','Đặt kèm khi đặt phòng','2025-12-05 16:23:29','ChuaSuDung'),
(33,91,NULL,1,1,300000.00,300000.00,'2025-12-06 00:12:47','Đặt kèm khi đặt phòng','2025-12-06 00:12:47','ChuaSuDung'),
(34,92,302,1,1,300000.00,300000.00,'2025-12-06 17:49:43','Dịch vụ khi đặt phòng trực tiếp','2025-12-06 17:49:43','ChuaSuDung'),
(35,92,302,2,1,50000.00,50000.00,'2025-12-06 17:49:43','Dịch vụ khi đặt phòng trực tiếp','2025-12-06 17:49:43','ChuaSuDung'),
(36,92,302,3,1,400000.00,400000.00,'2025-12-06 17:49:43','Dịch vụ khi đặt phòng trực tiếp','2025-12-06 17:49:43','ChuaSuDung'),
(37,93,302,1,1,300000.00,300000.00,'2025-12-06 18:27:03','Dịch vụ đặt kèm','2025-12-06 18:27:03','ChuaSuDung'),
(38,93,302,2,1,50000.00,50000.00,'2025-12-06 18:27:03','Dịch vụ đặt kèm','2025-12-06 18:27:03','ChuaSuDung'),
(39,93,302,3,1,400000.00,400000.00,'2025-12-06 18:27:03','Dịch vụ đặt kèm','2025-12-06 18:27:03','ChuaSuDung'),
(40,94,NULL,1,1,300000.00,300000.00,'2025-12-07 01:22:28','Đặt kèm khi đặt phòng','2025-12-07 01:22:28','ChuaSuDung'),
(41,94,NULL,2,2,50000.00,100000.00,'2025-12-07 01:22:28','Đặt kèm khi đặt phòng','2025-12-07 01:22:28','ChuaSuDung'),
(42,95,179,1,1,300000.00,300000.00,'2025-12-07 01:33:25','Dịch vụ đặt kèm','2025-12-07 01:33:25','ChuaSuDung'),
(43,95,179,2,1,50000.00,50000.00,'2025-12-07 01:33:25','Dịch vụ đặt kèm','2025-12-07 01:33:25','ChuaSuDung'),
(44,96,302,1,1,300000.00,300000.00,'2025-12-07 11:11:24','Dịch vụ đặt kèm','2025-12-07 11:11:24','ChuaSuDung'),
(45,97,179,1,1,300000.00,300000.00,'2025-12-07 11:24:49','Dịch vụ đặt kèm','2025-12-07 11:24:49','ChuaSuDung'),
(46,98,179,1,1,300000.00,300000.00,'2025-12-07 14:11:04','Dịch vụ đặt kèm','2025-12-07 14:11:04','ChuaSuDung'),
(47,99,179,1,1,300000.00,300000.00,'2025-12-07 14:13:37','Dịch vụ đặt kèm','2025-12-07 14:13:37','ChuaSuDung'),
(48,100,179,1,1,300000.00,300000.00,'2025-12-07 14:13:57','Dịch vụ đặt kèm','2025-12-07 14:13:57','ChuaSuDung'),
(49,101,179,1,1,300000.00,300000.00,'2025-12-07 14:15:55','Dịch vụ đặt kèm','2025-12-07 14:15:55','ChuaSuDung'),
(50,102,179,1,1,300000.00,300000.00,'2025-12-07 14:19:45','Dịch vụ đặt kèm','2025-12-07 14:19:45','ChuaSuDung'),
(51,103,179,1,1,300000.00,300000.00,'2025-12-07 14:32:50','Dịch vụ đặt kèm','2025-12-07 14:32:50','ChuaSuDung'),
(52,104,182,1,1,300000.00,300000.00,'2025-12-07 15:01:20','Dịch vụ đặt kèm','2025-12-07 15:01:20','ChuaSuDung'),
(53,105,180,1,1,300000.00,300000.00,'2025-12-07 15:02:59','Dịch vụ đặt kèm','2025-12-07 15:02:59','ChuaSuDung'),
(54,106,3,1,1,300000.00,300000.00,'2025-12-07 15:17:17','Dịch vụ đặt kèm','2025-12-07 15:17:17','ChuaSuDung'),
(55,107,105,1,1,300000.00,300000.00,'2025-12-07 16:11:10','Dịch vụ đặt kèm','2025-12-07 16:11:10','ChuaSuDung'),
(56,108,111,1,1,300000.00,300000.00,'2025-12-07 16:15:19','Dịch vụ đặt kèm','2025-12-07 16:15:19','ChuaSuDung'),
(57,109,109,1,1,300000.00,300000.00,'2025-12-07 16:39:32','Dịch vụ đặt kèm','2025-12-07 16:39:32','ChuaSuDung'),
(58,110,113,1,1,300000.00,300000.00,'2025-12-07 16:44:38','Dịch vụ đặt kèm','2025-12-07 16:44:38','ChuaSuDung'),
(59,111,112,1,1,300000.00,300000.00,'2025-12-07 16:53:38','Dịch vụ đặt kèm','2025-12-07 16:53:38','ChuaSuDung'),
(60,112,123,1,1,300000.00,300000.00,'2025-12-07 16:58:03','Dịch vụ đặt kèm','2025-12-07 16:58:03','ChuaSuDung'),
(61,113,179,1,2,300000.00,600000.00,'2025-12-07 17:42:00','Dịch vụ đặt kèm','2025-12-07 17:42:00','ChuaSuDung'),
(62,113,179,2,2,50000.00,100000.00,'2025-12-07 19:23:35','hi','2025-12-07 19:23:35','ChuaSuDung'),
(63,114,3,1,1,300000.00,300000.00,'2025-12-08 02:50:41','Dịch vụ đặt kèm','2025-12-08 02:50:41','ChuaSuDung'),
(64,114,3,2,1,50000.00,50000.00,'2025-12-08 02:50:41','Dịch vụ đặt kèm','2025-12-08 02:50:41','ChuaSuDung'),
(65,115,109,1,1,300000.00,300000.00,'2025-12-08 03:12:25','Dịch vụ đặt kèm','2025-12-08 03:12:25','ChuaSuDung'),
(66,115,109,2,1,50000.00,50000.00,'2025-12-08 03:12:25','Dịch vụ đặt kèm','2025-12-08 03:12:25','ChuaSuDung'),
(67,116,117,1,1,300000.00,300000.00,'2025-12-08 03:20:23','Dịch vụ đặt kèm','2025-12-08 03:20:23','ChuaSuDung'),
(68,116,117,2,1,50000.00,50000.00,'2025-12-08 03:20:23','Dịch vụ đặt kèm','2025-12-08 03:20:23','ChuaSuDung'),
(69,117,108,1,1,300000.00,300000.00,'2025-12-08 03:25:01','Dịch vụ đặt kèm','2025-12-08 03:25:01','ChuaSuDung'),
(70,117,108,2,1,50000.00,50000.00,'2025-12-08 03:25:01','Dịch vụ đặt kèm','2025-12-08 03:25:01','ChuaSuDung'),
(71,119,111,1,1,300000.00,300000.00,'2025-12-08 09:06:29','Dịch vụ đặt kèm','2025-12-08 09:06:29','ChuaSuDung'),
(72,119,111,2,1,50000.00,50000.00,'2025-12-08 09:06:29','Dịch vụ đặt kèm','2025-12-08 09:06:29','ChuaSuDung'),
(73,120,111,1,1,300000.00,300000.00,'2025-12-08 09:37:57','Dịch vụ đặt kèm','2025-12-08 09:37:57','ChuaSuDung'),
(74,120,111,2,1,50000.00,50000.00,'2025-12-08 09:37:57','Dịch vụ đặt kèm','2025-12-08 09:37:57','ChuaSuDung'),
(75,121,125,1,1,300000.00,300000.00,'2025-12-08 09:40:40','Dịch vụ đặt kèm','2025-12-08 09:40:40','ChuaSuDung'),
(76,121,125,2,1,50000.00,50000.00,'2025-12-08 09:40:40','Dịch vụ đặt kèm','2025-12-08 09:40:40','ChuaSuDung'),
(77,122,302,1,1,300000.00,300000.00,'2025-12-08 14:41:48','Dịch vụ đặt kèm','2025-12-08 14:41:48','ChuaSuDung'),
(78,122,302,2,1,50000.00,50000.00,'2025-12-08 14:41:48','Dịch vụ đặt kèm','2025-12-08 14:41:48','ChuaSuDung'),
(79,123,108,1,1,300000.00,300000.00,'2025-12-08 14:46:50','Dịch vụ đặt kèm','2025-12-08 14:46:50','ChuaSuDung'),
(80,123,108,2,1,50000.00,50000.00,'2025-12-08 14:46:50','Dịch vụ đặt kèm','2025-12-08 14:46:50','ChuaSuDung'),
(81,124,123,1,3,300000.00,900000.00,'2025-12-08 14:56:10','Dịch vụ đặt kèm','2025-12-08 14:56:10','ChuaSuDung'),
(82,124,123,2,2,50000.00,100000.00,'2025-12-08 14:56:10','Dịch vụ đặt kèm','2025-12-08 14:56:10','ChuaSuDung');

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
  `MaKhuyenMai` int(11) DEFAULT NULL,
  PRIMARY KEY (`MaCTGD`),
  KEY `MaGiaoDich` (`MaGiaoDich`),
  KEY `MaPhong` (`MaPhong`),
  KEY `fk_ctgd_khuyenmai` (`MaKhuyenMai`),
  CONSTRAINT `chitietgiaodich_ibfk_1` FOREIGN KEY (`MaGiaoDich`) REFERENCES `giaodich` (`MaGiaoDich`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chitietgiaodich_ibfk_2` FOREIGN KEY (`MaPhong`) REFERENCES `phong` (`MaPhong`) ON UPDATE CASCADE,
  CONSTRAINT `fk_ctgd_khuyenmai` FOREIGN KEY (`MaKhuyenMai`) REFERENCES `khuyenmai` (`MaKhuyenMai`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `chitietgiaodich`
INSERT INTO `chitietgiaodich` (`MaCTGD`,`MaGiaoDich`,`MaPhong`,`SoNguoi`,`NgayNhanDuKien`,`NgayTraDuKien`,`NgayCheckIn`,`NgayCheckOut`,`DonGia`,`ThanhTien`,`TienPhuThu`,`TienBoiThuong`,`TrangThai`,`GhiChu`,`TenKhach`,`CCCD`,`SDT`,`Email`,`MaKhuyenMai`) VALUES
(1,1,1,5,'2025-11-14 14:00:00','2025-11-15 12:00:00',NULL,NULL,500000.00,500000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(2,1,2,5,'2025-11-14 14:00:00','2025-11-15 12:00:00',NULL,NULL,800000.00,800000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(3,2,3,5,'2025-11-14 14:00:00','2025-11-15 12:00:00',NULL,NULL,1200000.00,1200000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp','meo meo',098978781,0986343955,'vntyy@gmail.com',NULL),
(4,3,104,5,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,700000.00,700000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(6,4,177,4,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,2700000.00,2700000.00,0.00,0.00,'DaHuy','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(7,5,106,3,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,850000.00,850000.00,0.00,0.00,'DaHuy','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(8,5,107,3,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,880000.00,880000.00,0.00,0.00,'DaHuy','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(9,6,105,2,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,680000.00,680000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(10,7,182,1,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,2400000.00,2400000.00,0.00,0.00,'Stayed','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(11,7,107,3,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,880000.00,880000.00,0.00,0.00,'Stayed','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(12,7,108,3,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,720000.00,720000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(13,7,109,3,'2025-11-17 14:00:00','2025-11-18 12:00:00',NULL,NULL,830000.00,830000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(14,8,112,2,'2025-11-18 14:00:00','2025-11-19 12:00:00',NULL,NULL,950000.00,950000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(15,8,113,2,'2025-11-18 14:00:00','2025-11-19 12:00:00',NULL,NULL,820000.00,820000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(31,27,136,1,'2025-11-26 00:00:00','2025-11-27 00:00:00',NULL,NULL,880000.00,880000.00,0.00,0.00,'DaHuy','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0912345675,'11hdgdy@gmail.com',NULL),
(32,28,140,1,'2025-11-26 00:00:00','2025-11-27 00:00:00',NULL,NULL,860000.00,860000.00,0.00,0.00,'DaHuy','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0912345675,'11hdgdy@gmail.com',NULL),
(34,30,118,2,'2025-12-03 00:00:00','2025-12-06 00:00:00',NULL,NULL,900000.00,2700000.00,0.00,0.00,'DaHuy','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0912345675,'11hdgdy@gmail.com',NULL),
(35,31,140,1,'2025-11-26 00:00:00','2025-11-27 00:00:00',NULL,NULL,860000.00,860000.00,0.00,0.00,'DaHuy','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0912345675,'11hdgdy@gmail.com',NULL),
(36,32,140,2,'2025-11-26 00:00:00','2025-11-27 00:00:00',NULL,NULL,860000.00,860000.00,0.00,0.00,'DaHuy','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0345628127,'11hdgdy@gmail.com',NULL),
(37,33,140,2,'2025-11-28 00:00:00','2025-11-29 00:00:00',NULL,NULL,860000.00,860000.00,0.00,0.00,'DaHuy','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0345628127,'11hdgdy@gmail.com',NULL),
(38,34,140,1,'2025-11-29 00:00:00','2025-12-01 00:00:00',NULL,NULL,860000.00,1720000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0345628127,'11hdgdy@gmail.com',NULL),
(39,35,136,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,880000.00,880000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0345628127,'11hdgdy@gmail.com',NULL),
(46,48,141,2,'2025-12-02 00:00:00','2026-01-08 00:00:00',NULL,NULL,940000.00,34780000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0345628127,'11hdgdy@gmail.com',NULL),
(47,49,116,2,'2025-11-30 00:00:00','2025-12-01 00:00:00',NULL,NULL,980000.00,980000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0345628127,'11hdgdy@gmail.com',NULL),
(48,50,105,2,'2025-12-07 14:00:00','2025-12-08 12:00:00',NULL,NULL,680000.00,680000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0345628127,'11hdgdy@gmail.com',NULL),
(49,51,135,2,'2025-12-05 00:00:00','2025-12-06 00:00:00',NULL,NULL,1160000.00,1160000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0345628127,'11hdgdy@gmail.com',NULL),
(50,52,123,2,'2025-12-02 00:00:00','2025-12-03 00:00:00',NULL,NULL,1020000.00,1020000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0345628127,'11hdgdy@gmail.com',NULL),
(51,53,125,1,'2025-11-28 00:00:00','2025-11-29 00:00:00',NULL,NULL,1150000.00,1150000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0345628127,'11hdgdy@gmail.com',NULL),
(52,54,120,2,'2025-11-28 00:00:00','2025-11-29 00:00:00',NULL,NULL,995000.00,995000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0345628127,'11hdgdy@gmail.com',NULL),
(53,55,131,2,'2025-11-30 00:00:00','2025-12-01 00:00:00',NULL,NULL,1100000.00,1100000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0345628127,'11hdgdy@gmail.com',NULL),
(54,56,128,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1180000.00,1180000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0345628127,'11hdgdy@gmail.com',NULL),
(55,57,124,2,'2025-12-13 00:00:00','2025-12-16 00:00:00',NULL,NULL,1200000.00,3600000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0345628127,'11hdgdy@gmail.com',NULL),
(56,58,134,2,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1210000.00,1210000.00,0.00,0.00,'Booked','Đặt phòng online','Nguyễn Hải Đăng ',012345678912,0345628127,'11hdgdy@gmail.com',NULL),
(57,59,149,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1200000.00,1200000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0345628127,'11hdgdy@gmail.com',NULL),
(58,60,133,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1220000.00,1220000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,0345628127,'11hdgdy@gmail.com',NULL),
(59,61,126,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1250000.00,1250000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,03274732642,'11hdgdy@gmail.com',NULL),
(60,62,143,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1250000.00,1250000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,03274732642,'11hdgdy@gmail.com',NULL),
(61,63,143,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1250000.00,1250000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,03274732642,'11hdgdy@gmail.com',NULL),
(62,64,143,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1250000.00,1250000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,03274732642,'11hdgdy@gmail.com',NULL),
(63,69,139,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1240000.00,1240000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL,NULL),
(64,70,138,1,'2025-12-30 00:00:00','2026-01-01 00:00:00',NULL,NULL,1290000.00,2580000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL,NULL),
(65,71,130,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1280000.00,1280000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL,NULL),
(66,72,129,2,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1320000.00,1320000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL,NULL),
(67,73,129,2,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1320000.00,1320000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL,NULL),
(68,75,129,2,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1320000.00,1320000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL,NULL),
(69,76,129,2,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1320000.00,1320000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL,NULL),
(70,77,129,2,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1320000.00,1320000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL,NULL),
(71,78,146,2,'2025-12-02 00:00:00','2025-12-03 00:00:00',NULL,NULL,1320000.00,1320000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL,NULL),
(72,79,127,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1300000.00,1300000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL,NULL),
(73,80,142,1,'2025-11-30 00:00:00','2025-12-01 00:00:00',NULL,NULL,1340000.00,1340000.00,0.00,0.00,'Booked',NULL,NULL,NULL,NULL,NULL,NULL),
(74,84,150,1,'2025-11-29 00:00:00','2025-11-30 00:00:00',NULL,NULL,1360000.00,1360000.00,0.00,0.00,'Booked','Đặt phòng online','Nguyễn Hải Đăng ',012345678912,03274732642,'11hdgdy@gmail.com',NULL),
(75,85,145,1,'2025-11-27 00:00:00','2025-11-29 00:00:00',NULL,NULL,1400000.00,2800000.00,0.00,0.00,'Booked','Đặt phòng online','Nguyễn Hải Đăng ',012345678912,03274732642,'11hdgdy@gmail.com',NULL),
(76,86,145,1,'2025-11-27 00:00:00','2025-11-29 00:00:00',NULL,NULL,1400000.00,2800000.00,0.00,0.00,'Booked','Đặt phòng online','Lê Thị Kim Oanh',012345678912,03274732642,'11hdgdy@gmail.com',NULL),
(77,87,145,1,'2025-11-28 00:00:00','2025-11-29 00:00:00',NULL,NULL,1400000.00,1400000.00,0.00,0.00,'Booked','Đặt phòng online','Nguyễn Hải Đăng ',012345678912,03274732642,'11hdgdy@gmail.com',NULL),
(78,88,132,2,'2025-12-12 14:00:00','2025-12-13 12:00:00',NULL,NULL,1350000.00,1350000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(79,89,144,2,'2025-12-12 14:00:00','2025-12-13 12:00:00',NULL,NULL,1380000.00,1380000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(80,89,181,2,'2025-12-12 14:00:00','2025-12-13 12:00:00',NULL,NULL,3200000.00,3200000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(81,90,147,2,'2025-12-12 14:00:00','2025-12-15 12:00:00',NULL,NULL,1390000.00,4170000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(82,91,148,2,'2025-12-12 14:00:00','2025-12-15 12:00:00',NULL,NULL,1450000.00,4350000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(83,91,176,2,'2025-12-12 14:00:00','2025-12-15 12:00:00',NULL,NULL,2400000.00,7200000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp','hello',9893257650,098775532,'bnhy@gmail.com',NULL),
(84,92,302,1,'2025-12-12 00:00:00','2025-12-13 00:00:00',NULL,NULL,500000.00,500000.00,0.00,0.00,'DaHuy','','vo nhat truong ne',762395671,03979685797,'vnt181666@gmail.com',NULL),
(85,93,302,1,'2025-12-12 00:00:00','2025-12-13 00:00:00',NULL,NULL,500000.00,500000.00,0.00,0.00,'DaHuy','','vo nhat truong ne',762395671,03979685797,'vnt181666@gmail.com',NULL),
(86,12,176,1,NULL,NULL,NULL,NULL,2400000.00,NULL,0.00,0.00,'Booked',NULL,'hihi',989325765,098756879,'hihihi@gmail.com',NULL),
(87,94,177,2,'2025-12-12 14:00:00','2025-12-13 12:00:00',NULL,NULL,2700000.00,2700000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(88,94,178,2,'2025-12-12 14:00:00','2025-12-13 12:00:00',NULL,NULL,2300000.00,2300000.00,0.00,0.00,'Booked','Đặt phòng trực tiếp',NULL,NULL,NULL,NULL,NULL),
(89,95,179,2,'2025-12-12 00:00:00','2025-12-16 00:00:00',NULL,NULL,3000000.00,12000000.00,0.00,0.00,'DaHuy','','umilang',0749853628,08998999999,'vnt566@gmail.com',NULL),
(90,96,302,2,'2025-12-15 00:00:00','2025-12-17 00:00:00',NULL,NULL,500000.00,1000000.00,0.00,0.00,'Booked','','phi phuong anh',0986343955,08998999999,'vnt566@gmail.com',NULL),
(91,97,179,2,'2025-12-11 00:00:00','2025-12-15 00:00:00',NULL,NULL,3000000.00,12000000.00,0.00,0.00,'Booked','','ho ngoc ha',098765876,03979685797,'vnt566@gmail.com',NULL),
(92,98,179,2,'2025-12-11 00:00:00','2025-12-15 00:00:00',NULL,NULL,3000000.00,12000000.00,0.00,0.00,'Booked','','ho ngoc ha',098765876,03979685797,'vnt566@gmail.com',NULL),
(93,99,179,2,'2025-12-11 00:00:00','2025-12-15 00:00:00',NULL,NULL,3000000.00,12000000.00,0.00,0.00,'Booked','','ho ngoc ha',098765876,03979685797,'vnt566@gmail.com',NULL),
(94,100,179,2,'2025-12-11 00:00:00','2025-12-15 00:00:00',NULL,NULL,3000000.00,12000000.00,0.00,0.00,'Booked','','ho ngoc ha',098765876,03979685797,'vnt566@gmail.com',NULL),
(95,101,179,2,'2025-12-11 00:00:00','2025-12-15 00:00:00',NULL,NULL,3000000.00,12000000.00,0.00,0.00,'Booked','','ho ngoc ha',098765876,03979685797,'vnt566@gmail.com',NULL),
(96,102,179,2,'2025-12-11 00:00:00','2025-12-15 00:00:00',NULL,NULL,3000000.00,12000000.00,0.00,0.00,'Booked','','ho ngoc ha',098765876,03979685797,'vnt566@gmail.com',NULL),
(97,103,179,2,'2025-12-11 00:00:00','2025-12-15 00:00:00',NULL,NULL,3000000.00,12000000.00,0.00,0.00,'Booked','','ho ngoc ha',098765876,03979685797,'vnt566@gmail.com',NULL),
(98,104,182,2,'2025-12-11 00:00:00','2025-12-15 00:00:00',NULL,NULL,2400000.00,9600000.00,0.00,0.00,'Booked','','my kieu',099964672,0986343955,'vnt@gmail.com',NULL),
(99,105,180,2,'2025-12-11 00:00:00','2025-12-15 00:00:00',NULL,NULL,2900000.00,11600000.00,0.00,0.00,'DaHuy','','hello mi mi',0987658763,098765876,'vnt@gmail.com',NULL),
(100,106,3,2,'2025-12-11 00:00:00','2025-12-15 00:00:00',NULL,NULL,1200000.00,4800000.00,0.00,0.00,'Booked','','lan khue',09876587682,09876587681,'vnt@gmail.com',NULL),
(101,107,105,2,'2025-12-12 00:00:00','2025-12-17 00:00:00',NULL,NULL,680000.00,3400000.00,0.00,0.00,'Booked','','vo nhat truong',765798968,09867576769,'hh@gmail.com',NULL),
(102,108,180,2,'2025-12-07 14:00:00','2025-12-17 12:00:00',NULL,NULL,2900000.00,29000000.00,0.00,0.00,'Stayed','','mi mi mu',1234567809,098768953,'vnnr@gmail.com',NULL),
(103,109,109,2,'2025-12-12 00:00:00','2025-12-17 00:00:00',NULL,NULL,830000.00,4150000.00,0.00,0.00,'Booked','','ly nha ky',787657569,09443772934,'hi@gmail.com',NULL),
(104,110,113,2,'2025-12-12 00:00:00','2025-12-17 00:00:00',NULL,NULL,820000.00,4100000.00,0.00,0.00,'Booked','','ngo kien huy',875439261,094367218,'ghgad@gmail.com',NULL),
(105,111,112,2,'2025-12-12 00:00:00','2025-12-17 00:00:00',NULL,NULL,950000.00,4750000.00,0.00,0.00,'Booked','','khá tiếng anh',9867540987,099437281,'ghn@gmail.com',NULL),
(106,112,123,2,'2025-12-12 00:00:00','2025-12-17 00:00:00',NULL,NULL,1020000.00,5100000.00,0.00,0.00,'Booked','','linda',909786671,099456111,'fhgnn@gmail.com',NULL),
(107,113,179,2,'2025-12-07 00:00:00','2025-12-09 00:00:00',NULL,NULL,3000000.00,6000000.00,0.00,0.00,'Stayed','','tran thanh',988765617,098764461,'hsgd@gmail.com',NULL),
(108,114,3,2,'2025-12-08 00:00:00','2025-12-09 00:00:00',NULL,NULL,1200000.00,1200000.00,0.00,0.00,'Booked','','su su',09876587680,09876587681,'vnt@gmail.com',NULL),
(109,115,109,2,'2025-12-09 00:00:00','2025-12-10 00:00:00',NULL,NULL,830000.00,830000.00,0.00,0.00,'Booked','','trieu van',7876575690,09443772934,'hi@gmail.com',NULL),
(110,116,117,2,'2025-12-12 00:00:00','2025-12-15 00:00:00',NULL,NULL,960000.00,2880000.00,0.00,0.00,'Booked','','gin tuan kiet',986558901,0976434771,'hihi@gmail.com',NULL),
(111,117,108,2,'2025-12-12 00:00:00','2025-12-15 00:00:00',NULL,NULL,720000.00,2160000.00,0.00,0.00,'Booked','','gin baby',998705402,099848671,'hihi@gmail.com',NULL),
(112,118,108,2,'2025-12-12 00:00:00','2025-12-15 00:00:00',NULL,NULL,720000.00,2160000.00,0.00,0.00,'Booked','','gin baby',998705402,099848671,'hihi@gmail.com',NULL),
(113,119,111,2,'2025-12-12 00:00:00','2025-12-15 00:00:00',NULL,NULL,660000.00,1980000.00,0.00,0.00,'Booked','','mi mi mu',1234567807,0987654328,'vnnr@gmail.com',NULL),
(114,120,111,2,'2025-12-12 00:00:00','2025-12-15 00:00:00',NULL,NULL,660000.00,1980000.00,0.00,0.00,'Booked','','puka ne ma',0749853621,0899899967,'vnt566@gmail.com',NULL),
(115,121,182,2,'2025-12-12 14:00:00','2025-12-15 12:00:00',NULL,NULL,1150000.00,3450000.00,0.00,0.00,'Booked','','musa',0749853622,0899899967,'vnt566@gmail.com',NULL),
(116,122,302,1,'2025-12-08 00:00:00','2025-12-09 00:00:00',NULL,NULL,500000.00,500000.00,0.00,0.00,'Booked','','khuong hoan my',080204014712,0333204860,'vnt1812004@gmail.com',NULL),
(117,123,108,1,'2025-12-08 14:00:00','2025-12-09 12:00:00',NULL,NULL,720000.00,720000.00,0.00,0.00,'Stayed','','khuong hoan my tho',08020401471,0333204860,'vnt1812004@gmail.com',NULL),
(118,124,123,2,'2025-12-08 14:00:00','2025-12-09 12:00:00',NULL,NULL,1020000.00,1020000.00,0.00,0.00,'CheckedOut','','toi la truong',0808040147,0333204860,'vnt5664@gmail.com',NULL),
(119,128,111,1,'2025-12-11 00:00:00','2025-12-12 00:00:00',NULL,NULL,660000.00,660000.00,0.00,0.00,'Booked','Đặt phòng online','Nguyễn An Nhi',012345678902,0909123458,'an113@example.com',NULL),
(120,130,108,2,'2025-12-11 00:00:00','2025-12-12 00:00:00',NULL,NULL,720000.00,576000.00,0.00,0.00,0,'Đặt phòng online','Nguyễn An Nhi',012345678902,0909123458,'an113@example.com',1),
(121,132,113,1,'2025-12-11 00:00:00','2025-12-12 00:00:00',NULL,NULL,820000.00,820000.00,0.00,0.00,0,'Đặt phòng online','Nguyễn An Nhi',012345678902,0909123458,'an113@example.com',NULL),
(122,133,113,1,'2025-12-11 00:00:00','2025-12-12 00:00:00',NULL,NULL,820000.00,820000.00,0.00,0.00,0,'Đặt phòng online','Nguyễn An Nhi',012345678902,0909123458,'an113@example.com',NULL),
(123,134,112,1,'2025-12-11 00:00:00','2025-12-12 00:00:00',NULL,NULL,950000.00,760000.00,0.00,0.00,0,'Đặt phòng online','Nguyễn An Nhi',012345678902,0909123458,'an113@example.com',1);

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
  `HinhAnh` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`MaDichVu`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `dichvu`
INSERT INTO `dichvu` (`MaDichVu`,`TenDichVu`,`GiaDichVu`,`MoTa`,`TrangThai`,`HinhAnh`) VALUES
(1,'Spa 60’',300000.00,'Massage toàn thân 60 phút','HoatDong','dv8.png'),
(2,'Giặt ủi',50000.00,'Giặt ủi quần áo','HoatDong','dv9.png'),
(3,'Đưa đón sân bay',400000.00,'Xe đưa đón sân bay','HoatDong','dv10.png'),
(18,'Dọn phòng nhanh',40000.00,'Dọn phòng cơ bản theo yêu cầu.','HoatDong','dv1.png'),
(19,'Bổ sung khăn tắm',15000.00,'Cung cấp thêm khăn tắm, khăn mặt.','HoatDong','dv2.png'),
(20,'Nước uống đóng chai',10000.00,'Cung cấp nước suối bổ sung.','HoatDong','dv3.png'),
(23,'Bảo quản hành lý',30000.00,'Giữ hành lý an toàn tại quầy lễ tân.','HoatDong','dv6.png'),
(24,'Thức ăn nhanh',20000.00,'cung cấp thức ăn nhanh','HoatDong','dv7.png'),
(26,'hello',123445678.00,'hihi','HoatDong','dv_1764431197_251.png');

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
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `doan`
INSERT INTO `doan` (`MaDoan`,`TenDoan`,`MaTruongDoan`,`SoNguoi`,`NgayDen`,`NgayDi`,`GhiChu`) VALUES
(1,'Đoàn huo huy',4,1,NULL,NULL,''),
(2,'Đoàn ngo duy thong',11,1,NULL,NULL,''),
(3,'Đoàn của vo nhat truong ne',19,1,NULL,NULL,''),
(4,'Đoàn của vo nhat truong ne',19,1,NULL,NULL,''),
(5,'Đoàn của vo nhat truong ne',19,1,NULL,NULL,''),
(6,'Đoàn nguyen thanh kiet',20,1,NULL,NULL,''),
(7,'Đoàn nguyen thanh kiet lu',19,1,NULL,NULL,''),
(8,'Đoàn nguyen thanh kiet lu ne',12,1,NULL,NULL,''),
(9,'Đoàn nguyen thanh kiet lu ne',12,1,NULL,NULL,''),
(10,'Đoàn nguyen thanh kiet lu ne',12,1,NULL,NULL,''),
(11,'Đoàn nguyen thanh kiet lu ne',12,1,NULL,NULL,''),
(12,'Đoàn luong my ky',18,2,NULL,NULL,''),
(13,'Đoàn luong my ky be',5,2,NULL,NULL,''),
(14,'Đoàn luong my ky be',5,2,NULL,NULL,''),
(15,'Đoàn luong my ky be',5,2,NULL,NULL,''),
(16,'Đoàn luong my ky be',5,1,NULL,NULL,''),
(17,'Đoàn luong my ky be',5,1,NULL,NULL,''),
(18,'Đoàn luong my ky be',5,1,NULL,NULL,''),
(19,'Đoàn luong my ky be',5,1,NULL,NULL,''),
(20,'Đoàn luong my ky be',22,2,NULL,NULL,''),
(21,'Đoàn luong my ky be',24,2,NULL,NULL,''),
(22,'Đoàn luong my ky nu',26,2,NULL,NULL,''),
(23,'Đoàn luong my ky nu',28,2,NULL,NULL,''),
(24,'Đoàn milo tran',30,2,NULL,NULL,''),
(25,'Đoàn milo tran ha',32,2,NULL,NULL,''),
(26,'Đoàn milo tran ha',34,2,NULL,NULL,NULL),
(27,'Đoàn của umilang',36,2,NULL,NULL,''),
(28,'Đoàn của phi phuong anh',37,2,NULL,NULL,''),
(29,'Đoàn của ho ngoc ha',38,2,NULL,NULL,''),
(30,'Đoàn của ho ngoc ha',38,2,NULL,NULL,''),
(31,'Đoàn của ho ngoc ha',38,2,NULL,NULL,''),
(32,'Đoàn của ho ngoc ha',38,2,NULL,NULL,''),
(33,'Đoàn của ho ngoc ha',38,2,NULL,NULL,''),
(34,'Đoàn của ho ngoc ha',38,2,NULL,NULL,''),
(35,'Đoàn của ho ngoc ha',38,2,NULL,NULL,''),
(36,'Đoàn của ho ngoc ha',38,2,NULL,NULL,''),
(37,'Đoàn của ho ngoc ha  ne',39,2,NULL,NULL,''),
(38,'Đoàn của nguyen huong giang',40,2,NULL,NULL,''),
(39,'Đoàn của umilang',28,2,NULL,NULL,''),
(40,'Đoàn của umilangt',43,2,NULL,NULL,''),
(41,'Đoàn của ngo hoang huy',44,2,NULL,NULL,''),
(42,'Đoàn của dan truong',46,2,NULL,NULL,''),
(43,'Đoàn của khoai lang thang',48,2,NULL,NULL,''),
(44,'Đoàn của lam khanh chi',50,2,NULL,NULL,'hello'),
(45,'Đoàn của phuong my chi',52,2,NULL,NULL,''),
(46,'Đoàn của ly que tran',54,2,NULL,NULL,'hello'),
(47,'Đoàn của tran tuan kiet',56,2,NULL,NULL,'hello'),
(48,'Đoàn của puka',58,2,NULL,NULL,'hello'),
(49,'Đoàn của puka ne',60,2,NULL,NULL,'hello'),
(50,'Đoàn của puka ne',60,2,NULL,NULL,'hello'),
(51,'Đoàn của puka ne ma',60,2,NULL,NULL,'hello'),
(52,'Đoàn của puka ne ma',60,2,NULL,NULL,'hello'),
(53,'Đoàn của musa',63,2,NULL,NULL,'hello'),
(54,'Đoàn của khuong hoan my',8,1,NULL,NULL,''),
(55,'Đoàn của khuong hoan my tho',65,1,NULL,NULL,''),
(56,'Đoàn của toi la truong',66,2,NULL,NULL,'ok');

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
  `GhiChu` text DEFAULT NULL,
  PRIMARY KEY (`MaGiaoDich`),
  KEY `MaKhachHang` (`MaKhachHang`),
  KEY `MaDoan` (`MaDoan`),
  KEY `MaNhanVien` (`MaNhanVien`),
  KEY `MaKhuyenMai` (`MaKhuyenMai`),
  CONSTRAINT `giaodich_ibfk_1` FOREIGN KEY (`MaKhachHang`) REFERENCES `khachhang` (`MaKhachHang`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `giaodich_ibfk_2` FOREIGN KEY (`MaDoan`) REFERENCES `doan` (`MaDoan`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `giaodich_ibfk_3` FOREIGN KEY (`MaNhanVien`) REFERENCES `nhanvien` (`MaNhanVien`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `giaodich_ibfk_4` FOREIGN KEY (`MaKhuyenMai`) REFERENCES `khuyenmai` (`MaKhuyenMai`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `giaodich`
INSERT INTO `giaodich` (`MaGiaoDich`,`MaKhachHang`,`MaDoan`,`MaNhanVien`,`MaKhuyenMai`,`NgayGiaoDich`,`LoaiGiaoDich`,`TongTien`,`TrangThai`,`PhuongThucThanhToan`,`GhiChu`) VALUES
(1,3,NULL,1,NULL,'2025-11-15 02:50:35','DatPhong',3950000.00,'Booked','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(2,3,NULL,1,NULL,'2025-11-15 02:50:45','DatPhong',3850000.00,'Booked','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(3,3,NULL,1,NULL,'2025-11-16 02:23:17','DatPhong',700000.00,'Booked','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(4,3,NULL,1,NULL,'2025-11-16 02:54:11','DatPhong',6150000.00,'DaHuy','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(5,3,NULL,1,NULL,'2025-11-17 01:39:02','DatPhong',1730000.00,'DaHuy','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(6,3,NULL,1,NULL,'2025-11-17 02:24:22','DatPhong',680000.00,'Booked','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(7,3,NULL,1,NULL,'2025-11-17 02:26:15','DatPhong',2250000.00,'Paid','TienMat','Đặt phòng trực tiếp tại quầy | Check-in 2025-11-17 11:55:48\nCheck-out lúc 2025-11-17 19:03:45; PTTT: TienMat; Tổng: 3.380.000đ; Phụ thu: 0đ; Bồi thường: 0đ\nCheck-out lúc 2025-11-17 19:04:19; PTTT: TienMat; Tổng: 3.380.000đ; Phụ thu: 0đ; Bồi thường: 0đ | Check-in 2025-11-17 20:12:24\nCheck-out lúc 2025-11-17 21:02:23; PTTT: TienMat; Tổng: 950.000đ; Phụ thu: 0đ; Bồi thường: 0đ\nCheck-out lúc 2025-11-21 13:53:10; PTTT: ChuyenKhoan; Tổng: 1.835.000đ; Phụ thu: 85.000đ; Bồi thường: 0đ\nCheckout phòng 1009 | Phòng: 850,000 | DV: 900,000 | Bồi thường: 500,000 | Tổng: 2,250,000'),
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
(34,1,NULL,NULL,NULL,'2025-11-27 02:35:04','DatPhong',2720000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
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
(55,1,NULL,NULL,NULL,'2025-11-28 00:36:46','DatPhong',1100000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
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
(80,1,NULL,NULL,NULL,'2025-11-28 02:00:14','DatPhong',1390000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(84,1,NULL,NULL,NULL,'2025-11-28 02:11:07','DatPhong',1360000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(85,1,NULL,NULL,NULL,'2025-11-28 02:53:26','DatPhong',2800000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(86,1,NULL,NULL,NULL,'2025-11-28 02:56:58','DatPhong',2800000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(87,1,NULL,NULL,NULL,'2025-11-28 03:01:44','DatPhong',1400000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(88,3,NULL,1,NULL,'2025-12-05 01:05:43','DatPhong',1650000.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(89,3,NULL,1,NULL,'2025-12-05 16:23:29','DatPhong',4930000.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(90,18,NULL,1,NULL,'2025-12-05 16:28:22','DatPhong',4170000.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(91,18,NULL,1,NULL,'2025-12-06 00:12:47','DatPhong',11850000.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(92,19,3,NULL,NULL,'2025-12-06 17:49:43','DatPhong',1249980.00,'DaHuy','ChuaThanhToan','Đặt phòng trực tiếp V2. Ngày đến: 2025-12-12, ngày đi: 2025-12-13, số ngày: 1.'),
(93,19,5,NULL,1,'2025-12-06 18:27:03','DatPhong',1249980.00,'DaHuy','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-12, Ngày đi: 2025-12-13, Số ngày: 1.'),
(94,3,NULL,1,NULL,'2025-12-07 01:22:28','DatPhong',5400000.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp tại quầy'),
(95,36,27,NULL,1,'2025-12-07 01:33:25','DatPhong',12349980.00,'DaHuy','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-12, Ngày đi: 2025-12-16, Số ngày: 4.'),
(96,37,28,NULL,NULL,'2025-12-07 11:11:24','DatPhong',1300000.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-15, Ngày đi: 2025-12-17, Số ngày: 2.'),
(97,38,29,NULL,NULL,'2025-12-07 11:24:49','DatPhong',12300000.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-11, Ngày đi: 2025-12-15, Số ngày: 4.'),
(98,38,30,NULL,NULL,'2025-12-07 14:11:04','DatPhong',0.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-11, Ngày đi: 2025-12-15, Số ngày: 4.'),
(99,38,31,NULL,NULL,'2025-12-07 14:13:37','DatPhong',0.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-11, Ngày đi: 2025-12-15, Số ngày: 4.'),
(100,38,32,NULL,NULL,'2025-12-07 14:13:57','DatPhong',0.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-11, Ngày đi: 2025-12-15, Số ngày: 4.'),
(101,38,33,NULL,NULL,'2025-12-07 14:15:55','DatPhong',0.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-11, Ngày đi: 2025-12-15, Số ngày: 4.'),
(102,38,34,NULL,NULL,'2025-12-07 14:19:45','DatPhong',0.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-11, Ngày đi: 2025-12-15, Số ngày: 4.'),
(103,38,35,NULL,NULL,'2025-12-07 14:32:50','DatPhong',0.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-11, Ngày đi: 2025-12-15, Số ngày: 4.'),
(104,38,36,NULL,1,'2025-12-07 15:01:20','DatPhong',9899980.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-11, Ngày đi: 2025-12-15, Số ngày: 4.'),
(105,39,37,NULL,1,'2025-12-07 15:02:59','DatPhong',11899980.00,'DaHuy','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-11, Ngày đi: 2025-12-15, Số ngày: 4.'),
(106,40,38,NULL,1,'2025-12-07 15:17:17','DatPhong',5099980.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-11, Ngày đi: 2025-12-15, Số ngày: 4.'),
(107,28,39,NULL,NULL,'2025-12-07 16:11:10','DatPhong',3700000.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-12, Ngày đi: 2025-12-17, Số ngày: 5.'),
(108,43,40,NULL,1,'2025-12-07 16:15:19','DatPhong',23600000.00,'Paid','TienMat','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-12, Ngày đi: 2025-12-17, Số ngày: 5. | Check-in 2025-12-07 18:28:44\nCheckout phòng 1007 | Phòng: 29,000,000 | DV: 0 | Bồi thường: 500,000 | KM: -5,900,000 | Tổng: 23,600,000'),
(109,44,41,NULL,NULL,'2025-12-07 16:39:32','DatPhong',4450000.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-12, Ngày đi: 2025-12-17, Số ngày: 5.'),
(110,46,42,NULL,NULL,'2025-12-07 16:44:38','DatPhong',4400000.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-12, Ngày đi: 2025-12-17, Số ngày: 5.'),
(111,48,43,NULL,NULL,'2025-12-07 16:53:38','DatPhong',5050000.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-12, Ngày đi: 2025-12-17, Số ngày: 5.'),
(112,50,44,NULL,2,'2025-12-07 16:58:03','DatPhong',5399980.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-12, Ngày đi: 2025-12-17, Số ngày: 5.\nhello'),
(113,52,45,NULL,1,'2025-12-07 17:42:00','DatPhong',6699980.00,'Stayed','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-07, Ngày đi: 2025-12-09, Số ngày: 2. | Check-in 2025-12-07 11:43:19'),
(114,54,46,NULL,2,'2025-12-08 02:50:41','DatPhong',1549980.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-08, Ngày đi: 2025-12-09, Số ngày: 1.\nhello'),
(115,56,47,NULL,1,'2025-12-08 03:12:25','DatPhong',1179980.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-09, Ngày đi: 2025-12-10, Số ngày: 1.\nhello'),
(116,58,48,NULL,1,'2025-12-08 03:20:23','DatPhong',3229980.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-12, Ngày đi: 2025-12-15, Số ngày: 3.\nhello'),
(117,60,49,NULL,1,'2025-12-08 03:25:01','DatPhong',2509980.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-12, Ngày đi: 2025-12-15, Số ngày: 3.\nhello'),
(118,60,50,NULL,1,'2025-12-08 09:00:14','DatPhong',2160000.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-12, Ngày đi: 2025-12-15, Số ngày: 3.\nhello'),
(119,60,51,NULL,1,'2025-12-08 09:06:29','DatPhong',2329980.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-12, Ngày đi: 2025-12-15, Số ngày: 3.\nhello'),
(120,60,52,NULL,1,'2025-12-08 09:37:57','DatPhong',2329980.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-12, Ngày đi: 2025-12-15, Số ngày: 3.\nhello'),
(121,63,53,NULL,1,'2025-12-08 09:40:40','DatPhong',3799980.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-12, Ngày đi: 2025-12-15, Số ngày: 3.\nhello'),
(122,8,54,NULL,1,'2025-12-08 14:41:48','DatPhong',849980.00,'Moi','ChuaThanhToan','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-08, Ngày đi: 2025-12-09, Số ngày: 1.'),
(123,65,55,NULL,1,'2025-12-08 14:46:50','DatPhong',856000.00,'Paid','TienMat','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-08, Ngày đi: 2025-12-09, Số ngày: 1. | Check-in 2025-12-08 08:48:19\nCheckout phòng 305 | Phòng: 720,000 | DV: 350,000 | Bồi thường: 0 | KM: -214,000 | Tổng: 856,000'),
(124,66,56,NULL,1,'2025-12-08 14:56:10','DatPhong',1616000.00,'Paid','TienMat','Đặt phòng trực tiếp V2, Ngày đến: 2025-12-08, Ngày đi: 2025-12-09, Số ngày: 1.\nok | Check-in 2025-12-08 08:58:21\nCheckout phòng 410 | Phòng: 1,020,000 | DV: 1,000,000 | Bồi thường: 0 | KM: -404,000 | Tổng: 1,616,000\nCheckout phòng 410 | Phòng: 1,020,000 | DV: 1,000,000 | Bồi thường: 0 | KM: -404,000 | Tổng: 1,616,000'),
(128,1,NULL,NULL,2,'2025-12-09 23:15:51','DatPhong',462000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(130,1,NULL,NULL,1,'2025-12-09 23:28:00','DatPhong',576000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(132,1,NULL,NULL,NULL,'2025-12-09 23:33:26','DatPhong',820000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(133,1,NULL,NULL,NULL,'2025-12-09 23:33:36','DatPhong',820000.00,'Booked','ChuaThanhToan','Đặt phòng online'),
(134,1,NULL,NULL,1,'2025-12-09 23:34:36','DatPhong',760000.00,'Booked','ChuaThanhToan','Đặt phòng online');

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
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `khachhang`
INSERT INTO `khachhang` (`MaKhachHang`,`MaTK`,`TenKH`,`SDT`,`Email`,`CCCD`,`DiaChi`,`LoaiKhach`) VALUES
(1,NULL,'Nguyễn An Nhi',0909123458,'an113@example.com',012345678902,'Hồ Chí Minh','Cá nhân'),
(2,NULL,'Trần Bình',0912345678,'binh@example.com',023456789012,'TP.HCM','Cá nhân'),
(3,NULL,'võ nhật Trường',0333204860,'vnt181@gmail.com',22653661123,'Long An','Cá nhân'),
(4,NULL,'huo huy',08998999999,'vnt566@gmail.com',345435757,'long an ','Trưởng đoàn'),
(5,NULL,'hihihi',08998999999,'vnt566@gmail.com',123456789,'long an ','Thành viên'),
(6,NULL,'vo truong',0987542357,'vnt5686@gmail.com',5676486788,NULL,'Cá nhân'),
(7,NULL,'vo truong',0987542357,'vnt5686@gmail.com',56764867889,NULL,'Cá nhân'),
(8,NULL,'Vo nhat truong',0333204860,'vnt1812004@gmail.com',080204014712,'tây ninh','Cá nhân'),
(9,NULL,'hung huỵ',098564331,'hiihi123@gmail.com',342586067,NULL,'Cá nhân'),
(10,NULL,'hi hu ha',022226790,'vn12t@gmail.com',456789345,NULL,'Cá nhân'),
(11,NULL,'ngo duy thong',022226790,'vn12t@gmail.com',098538299,NULL,'Trưởng đoàn'),
(12,NULL,'nhi',08998999999,'vnt5686@gmail.com',22653661111,'quy nhơn','Thành viên'),
(16,NULL,'hello 78',0876659875,'bhihii1@gmail.com',NULL,NULL,'NhanVien'),
(18,NULL,'võ nhật Trường',08998999999,'vnt566@gmail.com',0749853627,'long an ','Cá nhân'),
(19,NULL,'vo nhat truong ne',08998999999,'vnt5686@gmail.com',762395671,'quy nhơn','Cá nhân'),
(20,NULL,'nguyen thanh kiet',08998999999,'vnt5686@gmail.com',7887699465,'quy nhơn','Trưởng đoàn'),
(21,NULL,'le trong hieu',022226790,'vn12t@gmail.com',226511189,'','Thành viên'),
(22,NULL,'luong my ky be',08998999999,'vnt566@gmail.com',1234567895,'long an ','Trưởng đoàn'),
(23,NULL,'hi hu hy',0987542357,'vnt5686@gmail.com',567648678897,'','Thành viên'),
(24,NULL,'luong my ky be',08998999999,'vnt566@gmail.com',1234567898,'long an','Trưởng đoàn'),
(25,NULL,'hi hu hy',0987542357,'vnt5686@gmail.com',567648678896,'long an','Thành viên'),
(26,NULL,'luong my ky nu',08998999999,'vnt566@gmail.com',1234567891,'long an','Trưởng đoàn'),
(27,NULL,'hi hu hy nu',0987542357,'vnt5686@gmail.com',567648678891,'long an','Thành viên'),
(28,NULL,'luong my ky nu',08998999999,'vnt566@gmail.com',1234567801,'an giang','Trưởng đoàn'),
(29,NULL,'hi hu hy nu',0987542357,'vnt5686@gmail.com',567048678891,'','Thành viên'),
(30,NULL,'milo tran',08998999999,'vnt566@gmail.com',0234567801,'long an','Trưởng đoàn'),
(31,NULL,'nu u nu',0987542357,'vnt5686@gmail.com',567008678891,'','Thành viên'),
(32,NULL,'milo tran ha',08998999999,'vnt566@gmail.com',0234560801,'long an','Trưởng đoàn'),
(33,NULL,'nu u nuu',0987542357,'vnt5686@gmail.com',567008678801,'long an','Thành viên'),
(34,NULL,'milo tran ha',08998999999,'vnt566@gmail.com',0234560001,'long an','Trưởng đoàn'),
(35,NULL,'nu u nuu',0987542357,'vnt5686@gmail.com',567008670801,'long an','Thành viên'),
(36,NULL,'umilang',08998999999,'vnt566@gmail.com',0749853628,'an giang','Cá nhân'),
(37,NULL,'phi phuong anh',08998999999,'vnt566@gmail.com',0986343955,'an giang','Cá nhân'),
(38,NULL,'ho ngoc ha',03979685797,'vnt566@gmail.com',098765876,'long an','Cá nhân'),
(39,NULL,'ho ngoc ha  ne',03979685797,'vnt566@gmail.com',0987658768,'long an','Trưởng đoàn'),
(40,NULL,'nguyen huong giang',03979685797,'vnt566@gmail.com',09876587681,'long an','Trưởng đoàn'),
(41,NULL,'lan khue',09876587681,'vnt@gmail.com',09876587682,'','Thành viên'),
(42,NULL,'vo nhat truong',09867576769,'hh@gmail.com',765798968,'','Thành viên'),
(43,NULL,'umilangtuine',0899899996,'vnt156648@gmail.com',12345678001,'an giang','Trưởng đoàn'),
(44,NULL,'mi mi mu',0899899996,'vnt5664@gmail.com',1234567809,'an giang','Thành viên'),
(45,NULL,'ly nha ky',09443772934,'hi@gmail.com',787657569,'','Thành viên'),
(46,NULL,'dan truong',0899899996,'vnt5664@gmail.com',1234567808,'an giang','Trưởng đoàn'),
(47,NULL,'ngo kien huy',094367218,'ghgad@gmail.com',875439261,'','Thành viên'),
(48,NULL,'khoai lang thang',0899899996,'vnt5664@gmail.com',0234567808,'an giang','Trưởng đoàn'),
(49,NULL,'khá tiếng anh',099437281,'ghn@gmail.com',9867540987,'long xuyen','Thành viên'),
(50,NULL,'lam khanh chi',0899899967,'vnt566@gmail.com',09098775123,'quy nhơn','Trưởng đoàn'),
(51,NULL,'linda',099456111,'fhgnn@gmail.com',909786671,'ha noi','Thành viên'),
(52,NULL,'phuong my chi',0985246771,'gghhg@gmail.com',089079681,'long an','Trưởng đoàn'),
(53,NULL,'tran thanh',098764461,'hsgd@gmail.com',988765617,'tp Hochiminh','Thành viên'),
(54,NULL,'ly que tran',08998999999,'vnt5664@gmail.com',1749853627,'long an','Trưởng đoàn'),
(55,NULL,'su su',09876587681,'vnt@gmail.com',09876587680,'da nang','Thành viên'),
(56,NULL,'tran tuan kiet',08998999999,'vnt566@gmail.com',0749853626,'long an','Trưởng đoàn'),
(57,NULL,'trieu van',09443772934,'hi@gmail.com',7876575690,'long an','Thành viên'),
(58,NULL,'puka',0899899967,'vnt566@gmail.com',0749853629,'long an','Trưởng đoàn'),
(59,NULL,'gin tuan kiet',0976434771,'hihi@gmail.com',986558901,'kiki','Thành viên'),
(60,NULL,'puka ne',0899899967,'vnt566@gmail.com',0749853621,'long an','Trưởng đoàn'),
(61,NULL,'gin baby',099848671,'hihi@gmail.com',998705402,'bhjhjsd','Thành viên'),
(62,NULL,'mi mi mu',0987654328,'vnnr@gmail.com',1234567807,'uiuihi','Thành viên'),
(63,NULL,'musashi',0899899961,'vnt51661@gmail.com',0749853622,'long an','Trưởng đoàn'),
(64,NULL,'cẩm lan',0875346781,'gtgt@gmail.com',86560901467,'long an','Thành viên'),
(65,NULL,'khuong hoan my chi',0333204860,'vnt1812004@gmail.com',08020401471,'tây ninh','Trưởng đoàn'),
(66,NULL,'toi la truong ne',0333204860,'vnt5664@gmail.com',0808040147,'tây ninh','Trưởng đoàn'),
(67,NULL,'truong la toi',0808040147,'fhgnn@gmail.com',0808040148,'ha noi','Thành viên'),
(70,NULL,'Chien',0871253124,'Chien@gmail.com',1234567899,'hồ chí minh','Trưởng đoàn');

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
  `LoaiUuDai` enum('PERCENT','FIXED') NOT NULL DEFAULT 'PERCENT',
  PRIMARY KEY (`MaKhuyenMai`),
  UNIQUE KEY `TenChuongTrinh` (`TenChuongTrinh`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `khuyenmai`
INSERT INTO `khuyenmai` (`MaKhuyenMai`,`TenChuongTrinh`,`NgayBatDau`,`NgayKetThuc`,`MucUuDai`,`DoiTuong`,`TrangThai`,`LoaiUuDai`) VALUES
(1,'Giảm 20% Mùa Hè','2025-06-01','2025-08-31',20.00,'Khách lẻ','DangApDung','PERCENT'),
(2,'Giảm 30% Mùa Hè','2025-06-01','2025-08-31',30.00,'Khách lẻ','DangApDung','PERCENT');

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `nhanvien`
INSERT INTO `nhanvien` (`MaNhanVien`,`TenNV`,`SDT`,`Email`,`ChucVu`,`MaVaiTro`) VALUES
(1,'Lê Tấn Lễ',0988000001,'letanle@abcresort.com','Lễ tân',2),
(2,'Phạm Thanh Toán',0988000002,'ketoan@abcresort.com','Kế toán',3),
(3,'Ngô Dịch Vụ',0988000003,'dichvu@abcresort.com','Nhân viên dịch vụ',4),
(4,'Hoàng CSKH',0988000004,'cskh@abcresort.com','CSKH',5),
(5,'Trần Quản Lý',0988000005,'quanly@abcresort.com','Quản lý khách sạn',6),
(6,'vo tat thien',0876659877,'tatthienkhung123@gmail.com','QuanLy',6),
(7,'Sanggg',0342208348,'vosang328@gmail.com','LeTan',2),
(9,'Chien',0871253124,'Chien@gmail.com','KeToan',3);

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
) ENGINE=InnoDB AUTO_INCREMENT=303 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `phong`
INSERT INTO `phong` (`MaPhong`,`SoPhong`,`LoaiPhong`,`DienTich`,`LoaiGiuong`,`ViewPhong`,`Gia`,`TrangThai`,`SoKhachToiDa`,`GhiChu`,`TinhTrangPhong`,`HinhAnh`) VALUES
(1,101,'Standard',20,'Đơn','Biển',500000.00,'Booked',2,NULL,'Tot','1.png'),
(2,102,'Deluxe',30,'Đôi','Thành phố',800000.00,'Booked',3,NULL,'Tot','2.png'),
(3,201,'Suite',40,'King','Biển',1200000.00,'Trong',4,NULL,'Tot','3.png'),
(104,301,'Superior',28,'Đôi','Biển',700000.00,'Booked',3,NULL,'Tot','4.png'),
(105,302,'Superior',27,'Twin','Thành phố',680000.00,'Booked',3,NULL,'Tot','5.png'),
(106,303,'Deluxe',32,'King','Biển',850000.00,'Booked',3,NULL,'Tot','6.png'),
(107,304,'Deluxe',34,'Twin','Biển',880000.00,'Stayed',3,NULL,'HuHaiNang','7.png'),
(108,305,'Superior',29,'Đôi','Biển',720000.00,'Booked',3,NULL,'Tot','8.png'),
(109,306,'Deluxe',33,'King','Thành phố',830000.00,'Trong',3,NULL,'Tot','9.png'),
(110,307,'Deluxe',35,'King','Biển',900000.00,'Booked',4,NULL,'Tot','10.png'),
(111,308,'Superior',28,'Twin','Vườn',660000.00,'Booked',3,NULL,'Tot','11.png'),
(112,309,'Deluxe',36,'King','Biển',950000.00,'Booked',4,NULL,'Tot','12.png'),
(113,310,'Deluxe',33,'Đôi','Thành phố',820000.00,'Booked',3,NULL,'Tot','13.png'),
(114,401,'Deluxe',32,'Đôi','Biển',880000.00,'Booked',3,NULL,'Tot','14.png'),
(115,402,'Deluxe',34,'Twin','Thành phố',860000.00,'Booked',3,NULL,'Tot','15.png'),
(116,403,'Deluxe',36,'King','Biển',980000.00,'Booked',4,NULL,'Tot','16.png'),
(117,404,'Deluxe',35,'King','Biển',960000.00,'Trong',4,NULL,'Tot','17.png'),
(118,405,'Deluxe',33,'Đôi','Biển',900000.00,'Booked',3,NULL,'Tot','18.png'),
(119,406,'Deluxe',37,'King','Thành phố',870000.00,'Booked',4,NULL,'Tot','19.png'),
(120,407,'Deluxe',38,'King','Biển',995000.00,'Booked',4,NULL,'Tot','20.png'),
(121,408,'Deluxe',34,'Twin','Vườn',840000.00,'Booked',3,NULL,'Tot','21.png'),
(122,409,'Deluxe',33,'Đôi','Thành phố',865000.00,'Booked',3,NULL,'Tot','22.png'),
(123,410,'Deluxe',39,'King','Biển',1020000.00,'Trong',4,NULL,'Tot','23.png'),
(124,501,'Suite',40,'King','Biển',1200000.00,'Booked',4,NULL,'Tot','24.png'),
(125,502,'Suite',42,'King','Thành phố',1150000.00,'Trong',4,NULL,'Tot','25.png'),
(126,503,'Suite',45,'Twin','Biển',1250000.00,'Booked',4,NULL,'Tot','26.png'),
(127,504,'Suite',46,'King','Biển',1300000.00,'Booked',4,NULL,'Tot','27.png'),
(128,505,'Suite',44,'King','Thành phố',1180000.00,'Booked',4,NULL,'Tot','28.png'),
(129,506,'Suite',47,'King','Biển',1320000.00,'Stayed',4,NULL,'Tot','29.png'),
(130,507,'Suite',48,'Twin','Biển',1280000.00,'Booked',4,NULL,'Tot','30.png'),
(131,508,'Suite',45,'King','Vườn',1100000.00,'Booked',4,NULL,'Tot','31.png'),
(132,509,'Suite',49,'King','Biển',1350000.00,'Booked',4,NULL,'Tot','32.png'),
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
(144,701,'Suite',45,'King','Biển',1380000.00,'Booked',4,NULL,'Tot','44.png'),
(145,702,'Suite',47,'Twin','Biển',1400000.00,'Booked',4,NULL,'Tot','45.png'),
(146,703,'Suite',50,'King','Thành phố',1320000.00,'Booked',4,NULL,'Tot','46.png'),
(147,704,'Suite',48,'King','Biển',1390000.00,'Booked',4,NULL,'Tot','47.png'),
(148,705,'Suite',52,'King','Biển',1450000.00,'Booked',4,NULL,'Tot','48.png'),
(149,706,'Suite',51,'Twin','Vườn',1200000.00,'Booked',4,NULL,'Tot','49.png'),
(150,707,'Suite',49,'King','Biển',1360000.00,'Booked',4,NULL,'Tot','50.png'),
(176,1003,'VIP',74,'Twin','Biển',2400000.00,'Booked',5,NULL,'Tot','76.png'),
(177,1004,'VIP',76,'King','Biển',2700000.00,'Booked',5,NULL,'Tot','77.png'),
(178,1005,'VIP',80,'King','Thành phố',2300000.00,'Booked',5,NULL,'Tot','78.png'),
(179,1006,'VIP',82,'King','Biển',3000000.00,'Stayed',5,NULL,'Tot','79.png'),
(180,1007,'VIP',78,'King','Biển',2900000.00,'Trong',5,NULL,'HuHaiNhe','80.png'),
(181,1008,'VIP',85,'King','Biển',3200000.00,'Booked',5,NULL,'Tot','81.png'),
(182,1009,'VIP',83,'King','Thành phố',2400000.00,'Booked',5,NULL,'HuHaiNhe','82.png'),
(183,1010,'VIP',88,'King','Biển',3500000.00,'Booked',5,NULL,'Tot','83.png'),
(302,2000,'Suite',47,'Đơn','Thành phố',500000.00,'Trong',4,'hello','Tot','2.png');

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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `taikhoan`
INSERT INTO `taikhoan` (`MaTK`,`Username`,`Password`,`NgayTao`,`TrangThai`,`MaVaiTro`,`MoTaQuyen`,`MaKhachHang`,`MaNhanVien`) VALUES
(1,'admin','$2y$10$Wv8SmiyoS1CRNjQYWrJn1e5UaQyEdBAS.qAY9w7s5dd/wgcHOEmYm','2025-11-14 16:57:18','HoatDong',1,'Toàn quyền hệ thống: quản lý người dùng, phân quyền, quản lý phòng, dịch vụ, khuyến mãi, báo cáo, cấu hình hệ thống.',NULL,NULL),
(2,'letan1','$2y$10$NCAVZdKihPxrJLWngOAG.uVYYNxhCwdGzh4frSU5CAsWiNqf7oufa','2025-11-14 16:57:18','HoatDong',2,'Đặt phòng, sửa đặt phòng, hủy phòng, check-in, check-out, quản lý trạng thái phòng, hỗ trợ khách trực tiếp.',NULL,1),
(3,'ketoan1','$2y$10$.JWLKNs1T0C0nAeLh7RFyOxansHFjVYWtzwZFWO.0d3DEuGOuDeZe','2025-11-14 16:57:18','HoatDong',3,'Quản lý dịch vụ đi kèm, nhận yêu cầu dịch vụ, cập nhật tiến trình xử lý, báo cáo sử dụng dịch vụ.',NULL,2),
(4,'dichvu1','$2y$10$mvGI4Zd3Nz4wws36k4hEF.x3a7EStAvMSIf6MkYWVGSfBWYF6zBZm','2025-11-14 16:57:18','HoatDong',4,'Quản lý hóa đơn, thanh toán, đối soát doanh thu, xem báo cáo tài chính, xử lý các khoản phí phát sinh.',NULL,3),
(5,'cskh1','$2y$10$L1RPs0dZGWOe9R.Jo1hsZ.0p3a/xQkrBBVuTzy7prpBpOX512wA5S','2025-11-14 16:57:18','HoatDong',5,'Xử lý phản hồi khách hàng, tạo & quản lý chương trình khuyến mãi, chăm sóc khách hàng thân thiết.',NULL,4),
(6,'quanly1','$2y$10$1X9Gw9hhksu9DvsflYiE0O2SYz72rhH0iln4p9r77FpyhAWDwgElO','2025-11-14 16:57:18','HoatDong',6,'Quyền giám sát nghiệp vụ, xem báo cáo tổng hợp, phê duyệt thao tác của các bộ phận, quản lý hoạt động vận hành.',NULL,5),
(7,'khach1','$2y$10$HhnkjTuk24cCbUVatzIzAuhSjjC6YwkfalX6ZZDESg0zhtHyrEbaq','2025-11-14 16:57:18','HoatDong',7,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',1,NULL),
(8,'khach2','$2y$10$9vsTCo3d.b8kjt7KN/S6iu0LJYzWR2AshXdpQjaRAGkgsn/IgQJW2','2025-11-14 16:57:18','HoatDong',7,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',2,NULL),
(9,'D001_Leader','$2y$10$Ab2ipboVLcuZ6.9om7QvNu2rYvjlbmC93O6TlKa9Md8WNhnA0vnsC','2025-11-16 19:27:05','HoatDong',7,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',4,NULL),
(10,'D001_M1','$2y$10$BUDg2qSomZJspnmopvxDi.3ib7DxZxXT69SbO.XcCPzIU9XSJJxlW','2025-11-16 19:27:05','HoatDong',7,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',5,NULL),
(12,'vnt181','$2y$10$Hk3UKch06aZS5.tpou9oz.WJ/ybpis1IgrIzG4jk9BNbOJqsjxGD6','2025-11-24 01:20:41','HoatDong',7,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',NULL,NULL),
(13,'guest','$2y$10$DkjHlbQCBU7gECxeC5.q.OoNv.0nbfwAkgr3XwVANjdeRLjCa5jse','2025-11-24 01:21:35','HoatDong',7,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',NULL,NULL),
(14,'bento181','$2y$10$A3wN9.kREzy8wXj41IBijOKVAaf7rt658Pso28RHxqcy1opwhApy2','2025-11-24 01:36:09','HoatDong',6,'Quản lý: giám sát toàn bộ hoạt động và báo cáo.',NULL,NULL),
(15,'bento2004','$2y$10$SRODgxnojIce2mhhx4XbJeIpMCPZtXgTUFizaQ0NkQFXTrJSRH0Su','2025-11-24 02:23:20','HoatDong',7,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',NULL,NULL),
(16,'bento900','$2y$10$Zmf2w55Ty7jN619DYN3KieSirc9aYEkDPCIqn1E8VhNKQjWUoOY8i','2025-11-24 02:29:16','HoatDong',2,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',NULL,NULL),
(17,'D002_Leader','$2y$10$mrIlNOiJxEgkzx/ozgluROGb1kg2bTIZprVk11eisK1Bd4mHl0lK6','2025-11-28 12:42:01','HoatDong',7,'Khách hàng: xem thông tin cá nhân và lịch sử đặt phòng.',11,NULL),
(18,'D002_M1','$2y$10$CaXM3Y0HZTwGMkyCctNXh.TWg4yfPEhVyrkMFQvuamyrvcr0hHT2y','2025-11-28 12:42:01','HoatDong',7,'Quyền sử dụng dành cho khách hàng: xem và sửa hồ sơ cá nhân, xem đặt phòng, lịch sử lưu trú, đăng ký dịch vụ bổ sung.',12,NULL),
(21,'thienkhung','$2y$10$mpQwAzrqpmIGJDpaYghsw.IsqV7/RlqqqkOJoY7FBYL4PEhuQS8BG','2025-12-05 00:09:12','HoatDong',6,'Quản lý: giám sát tổng thể, xem báo cáo chi tiết, quản lý nhân sự.',NULL,6),
(22,'D006_Leader','$2y$10$wj67z8LOYh/qREfu.FMFN.jM9ZnpiKM2CuwFbSL89ifNBUfEfV1XS','2025-12-06 21:09:50','HoatDong',7,NULL,20,NULL),
(23,'D006_M1','$2y$10$E.7QPxxVdOPMMYEMgd52ZOrkNaydl7pVh7t.ynDqe2IuB1aOlmWiq','2025-12-06 21:09:50','HoatDong',7,NULL,21,NULL),
(24,'D007_Leader','$2y$10$1oT/LUjMfET57vfDLtuFzObhedyWJlc8yZNw3SO1QAZ28ZdNVFMZq','2025-12-06 21:14:54','HoatDong',7,NULL,19,NULL),
(25,'D007_M1','$2y$10$v2VkTGKOo7wIBBxFbmutWusExjNl2mDCl5hxsVpCbNKNMDaWXU6CW','2025-12-06 21:14:54','HoatDong',7,NULL,10,NULL),
(26,'D012_Leader','$2y$10$/qDcl/9NAGveFqIFD.tQ9OQXawbEuz0b6H/uP0n0WzVRz1ZmYERYa','2025-12-06 23:12:22','HoatDong',7,NULL,18,NULL),
(27,'D012_M1','$2y$10$.QlO0fSv1CKA/HN49bC7.u.xM0uqQzcT2s7fzDMKOyrLQWBDPgdtK','2025-12-06 23:12:22','HoatDong',7,NULL,6,NULL),
(28,'D013_M1','$2y$10$tWkJjUziIZ6iMGw3XArE4umtARreq7XZxBTFnSy/XheTvcZpw/NWO','2025-12-06 23:13:19','HoatDong',7,NULL,7,NULL),
(29,'D026_Leader','$2y$10$Asycgin9/C2bNEdps7yweePk2JSI5dSlp.fenc5/l.ED826d5CE7K','2025-12-07 00:08:23','HoatDong',7,NULL,34,NULL),
(30,'D026_M1','$2y$10$A9VORJAWApT01zvrCQ7lVuDrqF/8iBhRIXVkp/5/PMCxebdiPTfTa','2025-12-07 00:08:23','HoatDong',7,NULL,35,NULL),
(31,'sanggg','$2y$10$kNih3GkAeTt/1AfvGRWLy.ysT2set3S7Jrfq7FddKjSeCc9vO5lNe','2025-12-11 01:14:54','HoatDong',2,NULL,NULL,7),
(33,'chien','$2y$10$4O5uqtZPGtverbvjPx.d3eVPw4Jrr/4A8uC/lrqNrB0gRlCYxp3/O','2025-12-11 01:46:21','HoatDong',3,'Kế toán: xuất hóa đơn, xem báo cáo doanh thu, quản lý chi phí.',NULL,9);

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
