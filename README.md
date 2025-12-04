"# 🏨 ABC Resort Management System

**Hệ thống Quản lý Khu nghỉ dưỡng Đa nền tảng**  
Giải pháp toàn diện cho quy trình đặt phòng, chăm sóc khách hàng và quản lý vận hành.

---

## 🚀 Giới thiệu

Đồ án này được xây dựng dựa trên **mô hình MVC (Model-View-Controller) thuần**, không sử dụng Framework, nhằm tối ưu hóa hiệu suất và thể hiện sự am hiểu sâu sắc về kiến trúc phần mềm. Hệ thống tích hợp công nghệ **WebSocket (Ratchet)** để hỗ trợ chat trực tuyến thời gian thực và **PHPMailer** cho các chiến dịch Marketing tự động.

---

## 🛠️ Công nghệ sử dụng

| Công nghệ | Mô tả |
|-----------|-------|
| **Backend** | PHP 8.0+ (Native MVC), MySQL |
| **Frontend** | HTML5, CSS3, JavaScript (ES6), Bootstrap 5 |
| **Real-time Communication** | Ratchet (PHP WebSocket) - Xử lý chat không độ trễ |
| **Mail Service** | PHPMailer (SMTP Gmail) |
| **Database Management** | Transaction Safe (Đảm bảo toàn vẹn dữ liệu) |

---

## ✨ Tính năng nổi bật

### 1. 💬 Hệ thống Chat Real-time (Tư vấn trực tuyến)
- ✅ Giao tiếp trực tiếp giữa **Khách hàng** và **Quản lý** thông qua WebSocket (Cổng 8081)
- ✅ Tin nhắn hiển thị ngay lập tức không cần tải lại trang
- ✅ Lưu trữ lịch sử hội thoại 2 chiều vào CSDL
- ✅ Giao diện phân luồng tin nhắn thông minh cho Quản lý

### 2. 📧 Email Marketing & Thông báo
- ✅ Gửi email thông báo đặt phòng, phản hồi khiếu nại tự động
- ✅ Chức năng gửi thông báo hàng loạt (Mass Email) cho chiến dịch Marketing
- ✅ Hỗ trợ gửi đa luồng, chọn đối tượng nhận linh hoạt

### 3. 📝 Quản lý Phản hồi Khách hàng (Feedback Loop)
- ✅ Quy trình xử lý phản hồi khép kín: **Tiếp nhận → Đổi trạng thái → Trả lời → Lưu lịch sử**
- ✅ Hiển thị lịch sử xử lý trực quan (Màu sắc phân biệt trạng thái: Đang xử lý/Đã xong)

### 4. 🎁 Quản lý Khuyến mãi Thông minh
- ✅ Tự động kiểm tra trùng lặp thời gian và đối tượng áp dụng
- ✅ Ngăn chặn xung đột dữ liệu bằng Transaction

---

## 📦 Các Module chính

| Module | Mô tả chi tiết |
|--------|----------------|
| 🛎️ **Booking** | Đặt phòng Online, lọc phòng trống, quản lý Check-in/Out |
| 💬 **Live Chat** | WebSocket Server riêng biệt. Chat realtime Khách - Admin |
| 📢 **Marketing** | Tích hợp PHPMailer. Gửi thông báo/Khuyến mãi hàng loạt |
| ⭐ **Feedback** | Hệ thống xử lý khiếu nại 2 chiều, Tracking lịch sử xử lý |
| 📊 **Dashboard** | Thống kê trực quan, giao diện Dark Mode hiện đại |

---

## 🚀 Hướng dẫn cài đặt & chạy thử

### Bước 1: Chuẩn bị môi trường
```bash
# Yêu cầu hệ thống:
- PHP >= 8.0
- MySQL >= 5.7
- Composer
- XAMPP/WAMP (Khuyến nghị)
```

### Bước 2: Cài đặt Database
```sql
-- Import file SQL vào phpMyAdmin
mysql -u root -p < abc_resort1.sql
```

### Bước 3: Cài đặt Dependencies
```bash
# Cài đặt các thư viện PHP qua Composer
composer install
```

### Bước 4: Khởi động Chat Server
```bash
# Mở Command Prompt/Terminal và chạy lệnh:
php libraries/server.php

# Bạn sẽ thấy thông báo:
# --> Server Chat & DB đã sẵn sàng!
# --> Lắng nghe tại cổng 8081
```

### Bước 5: Chạy ứng dụng
```
1. Khởi động Apache và MySQL trong XAMPP
2. Truy cập: http://localhost/xong2actor/xong2actor/code1/index.php
3. Đăng nhập với tài khoản demo (xem file SQL)
```

---

## 📂 Cấu trúc thư mục

```
ABC-hotel-reservation/
├── config/              # Cấu hình kết nối CSDL
├── controllers/         # Xử lý logic nghiệp vụ
├── models/              # Tương tác với Database
├── views/               # Giao diện người dùng
│   ├── admin/          # Dashboard Quản trị
│   ├── khachhang/      # Trang khách hàng
│   ├── letan/          # Trang lễ tân
│   └── quanly/         # Trang quản lý
├── libraries/           # Thư viện bên thứ 3
│   ├── server.php      # WebSocket Server
│   ├── ChatServer.php  # Logic xử lý Chat
│   ├── PHPMailer.php   # Gửi Email
│   └── MailService.php # Service gửi mail
├── public/              # Tài nguyên tĩnh
├── uploads/             # File upload (Hình ảnh phòng, feedback)
└── index.php            # Entry point
```

---

## 🎯 Điểm nổi bật của Đồ án

### 🏆 Kỹ thuật nổi bật
- ✨ **Pure MVC Architecture** - Không dùng Framework, code từ đầu
- ⚡ **WebSocket Real-time** - Chat không độ trễ với Ratchet
- 🔒 **Transaction Safe** - Đảm bảo tính toàn vẹn dữ liệu
- 📧 **Automated Email** - PHPMailer tích hợp sâu

### 🎨 Thiết kế UI/UX
- 🌙 **Dark Mode** - Giao diện hiện đại, dễ nhìn
- 📱 **Responsive** - Tương thích đa thiết bị
- ⚡ **Auto-submit Forms** - Tìm kiếm thông minh không reload

### 🔐 Bảo mật
- 🔑 **Session Management** - Xác thực người dùng
- 🛡️ **SQL Injection Prevention** - Prepared Statements
- 🔒 **Role-based Access Control** - Phân quyền chi tiết

---

## 📸 Screenshots

### Dashboard Quản lý
![Dashboard](docs/screenshots/dashboard.png)

### Hệ thống Chat Real-time
![Chat System](docs/screenshots/chat.png)

### Đặt phòng Online
![Booking](docs/screenshots/booking.png)

---

## 🧪 Testing

### Test Chat Server
```bash
# Terminal 1: Khởi động server
php libraries/server.php

# Terminal 2: Test kết nối
telnet localhost 8081
```

### Test Email Service
```php
// Chạy file test
php -f libraries/test_mail.php
```

---

## 🤝 Đóng góp

Đồ án được thực hiện bởi:

**Nhóm 1 - DHHTTT18ATT**  
**Đại học Công Nghiệp TP.HCM (IUH)**

### Thành viên:
- 👨‍💻 **Vũ Bích Vi** - 22691011  
  *Role*: Team Leader, Backend Developer, WebSocket Implementation

---

## 📞 Liên hệ & Hỗ trợ

- 📧 Email: 22691011@student.iuh.edu.vn
- 🏫 Trường: Đại học Công Nghiệp TP.HCM (IUH)
- 📚 Lớp: DHHTTT18ATT

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🙏 Lời cảm ơn

- Cảm ơn các thầy cô Khoa Công nghệ Thông tin - IUH đã hướng dẫn nhiệt tình
- Cảm ơn cộng đồng PHP và Ratchet đã cung cấp tài liệu hữu ích
- Cảm ơn team đã hợp tác hoàn thành dự án

---

<div align="center">
  <p>⭐ Nếu thấy dự án hữu ích, hãy cho chúng mình 1 star nhé! ⭐</p>
  <p>Made with ❤️ by Nhóm 1 - IUH</p>
  <p>© 2025 ABC Resort Management System</p>
</div>" 
