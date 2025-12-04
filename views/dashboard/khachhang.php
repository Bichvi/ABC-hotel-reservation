<?php
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Khách hàng - ABC Resort</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #020617, #0f172a);
            min-height: 100vh;
            color: #e5e7eb;
        }
        .navbar {
            background: rgba(15, 23, 42, 0.95) !important;
            backdrop-filter: blur(10px);
        }
        .brand-logo {
            font-weight: 700;
            letter-spacing: 1px;
        }
        .main-wrapper {
            padding: 30px 0;
        }
        .card-module {
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.3);
            background: radial-gradient(circle at top left, rgba(56,189,248,0.25), rgba(15,23,42,0.96));
            color: #e5e7eb;
            box-shadow: 0 18px 40px rgba(0,0,0,0.65);
            transition: all 0.2s ease-in-out;
            position: relative;
            overflow: hidden;
        }
        .card-module:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 24px 60px rgba(0,0,0,0.7);
            border-color: rgba(96, 165, 250, 0.9);
        }
        .card-module .icon-circle {
            width: 52px;
            height: 52px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(15,23,42,0.9);
            border: 1px solid rgba(148,163,184,0.6);
        }
        .badge-soft {
            background: rgba(8,47,73,0.8);
            color: #bae6fd;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 0.75rem;
        }
        .quick-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 9px;
            border-radius: 999px;
            background: rgba(15,23,42,0.85);
            border: 1px solid rgba(148,163,184,0.3);
            font-size: 0.75rem;
        }
        .footer-text {
            font-size: 0.8rem;
            color: #9ca3af;
        }
        .section-title {
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: .18em;
            color: #9ca3af;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark border-bottom border-slate-700">
    <div class="container">
        <a class="navbar-brand brand-logo" href="#">
            <i class="fa-solid fa-hotel me-2 text-info"></i>ABC Resort - Khách hàng
        </a>
        <div class="d-flex align-items-center">
            <span class="me-3 small text-slate-300">
                <i class="fa-regular fa-user me-1"></i>
                <?= htmlspecialchars($user['Username'] ?? 'khach') ?>
            </span>
            <a href="index.php" class="btn btn-outline-light btn-sm me-3" title="Trang chủ">
                <i class="fa-solid fa-house"></i>
            </a>
            <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </a>
        </div>
    </div>
</nav>

<div class="container main-wrapper">
    <div class="row mb-4">
        <div class="col-lg-8">
            <h2 class="mb-1">Xin chào, <?= htmlspecialchars($user['Username'] ?? 'Quý khách') ?>!</h2>
            <p class="text-secondary">
                Từ đây, bạn có thể đặt phòng online, quản lý đặt phòng, đặt dịch vụ bổ sung và gửi phản hồi cho ABC Resort.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <span class="badge-soft">
                <i class="fa-solid fa-circle-info me-1"></i>
                Hôm nay: <?= date('d/m/Y') ?>
            </span>
        </div>
    </div>

    <div class="mb-3">
        <span class="section-title">Chức năng dành cho khách hàng</span>
    </div>

    <div class="row g-3">
        <!-- 1. Đặt phòng Online -->
        <div class="col-md-4">
            <a href="index.php?controller=khachhang&action=datPhongOnline1"
               class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-bed fa-lg text-success"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-globe"></i> Đặt phòng Online
                        </span>
                    </div>
                    <h5 class="mb-1">Đặt phòng Online</h5>
                    <p class="mb-2 small text-secondary">
                        Chọn ngày lưu trú, lọc phòng trống theo hạng phòng, số giường, tầng và xác nhận đặt phòng ngay trên hệ thống.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-filter me-1"></i>Lọc theo nhu cầu</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 2. Hủy đặt phòng -->
        <div class="col-md-4">
            <a href="index.php?controller=khachhang&action=huyDatPhong"
               class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-ban fa-lg text-danger"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-receipt"></i> Mã giao dịch
                        </span>
                    </div>
                    <h5 class="mb-1">Hủy đặt phòng</h5>
                    <p class="mb-2 small text-secondary">
                        Xem các đặt phòng hiện có, kiểm tra điều kiện và thực hiện hủy đặt phòng nếu còn trong thời hạn cho phép.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-door-open me-1"></i>Giải phóng lịch lưu trú</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 3. Cập nhật thông tin cá nhân -->
        <div class="col-md-4">
            <a href="index.php?controller=khachhang&action=capNhatThongTin"
               class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-user-pen fa-lg text-warning"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-regular fa-id-card"></i> Thông tin cá nhân
                        </span>
                    </div>
                    <h5 class="mb-1">Cập nhật thông tin cá nhân</h5>
                    <p class="mb-2 small text-secondary">
                        Cập nhật số điện thoại, email, địa chỉ liên hệ để đảm bảo nhận đủ thông tin về đặt phòng và khuyến mãi.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-shield-halved me-1"></i>Bảo mật thông tin</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 4. Đặt dịch vụ bổ sung -->
        <div class="col-md-4">
            <a href="index.php?controller=khachhang&action=datDichVuBoSung"
               class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-concierge-bell fa-lg text-info"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-spa"></i> Spa / Ăn uống /...
                        </span>
                    </div>
                    <h5 class="mb-1">Đặt dịch vụ bổ sung</h5>
                    <p class="mb-2 small text-secondary">
                        Đăng ký sử dụng các dịch vụ thêm như spa, ăn uống, giặt ủi, đưa đón... gắn với phòng đang lưu trú.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-plus-circle me-1"></i>Dịch vụ linh hoạt</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 5. Hủy đặt dịch vụ bổ sung -->
        <div class="col-md-4">
            <a href="index.php?controller=khachhang&action=huyDichVuBoSung"
               class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-xmark-circle fa-lg text-danger"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-solid fa-file-invoice"></i> Lịch sử dịch vụ
                        </span>
                    </div>
                    <h5 class="mb-1">Hủy dịch vụ bổ sung</h5>
                    <p class="mb-2 small text-secondary">
                        Xem lại các dịch vụ đã đặt và thực hiện hủy nếu chưa đến thời gian sử dụng hoặc theo chính sách resort.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-clock-rotate-left me-1"></i>Chủ động thay đổi</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 6. Gửi phản hồi -->
        <div class="col-md-4">
            <a href="index.php?controller=khachhang&action=guiPhanHoi"
               class="text-decoration-none text-light">
                <div class="card card-module h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-comment-dots fa-lg text-success"></i>
                        </div>
                        <span class="quick-pill">
                            <i class="fa-regular fa-star"></i> Đánh giá / Góp ý
                        </span>
                    </div>
                    <h5 class="mb-1">Gửi phản hồi cho resort</h5>
                    <p class="mb-2 small text-secondary">
                        Gửi đánh giá, góp ý hoặc khiếu nại để ABC Resort cải thiện chất lượng dịch vụ trong tương lai.
                    </p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span><i class="fa-solid fa-heart me-1"></i>Chăm sóc khách hàng</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="mt-4 text-center footer-text">
        ABC Resort – Khách hàng | Đặt phòng & quản lý dịch vụ trực tuyến
    </div>

    <div id="chat-widget" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
    <button id="btn-toggle-chat" onclick="toggleChat()" 
            style="width: 60px; height: 60px; border-radius: 50%; background: #0ea5e9; border: none; color: white; box-shadow: 0 4px 10px rgba(0,0,0,0.3); cursor: pointer;">
        <i class="fa-solid fa-comments fa-xl"></i>
    </button>

    <div id="chat-box" style="display: none; width: 300px; height: 400px; background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); overflow: hidden; flex-direction: column;">
        
        <div style="background: #0f172a; color: white; padding: 10px; font-weight: bold; display: flex; justify-content: space-between;">
            <span>Tư vấn trực tuyến</span>
            <span onclick="toggleChat()" style="cursor: pointer;">&times;</span>
        </div>

        <div id="chat-content" style="flex: 1; padding: 10px; overflow-y: auto; background: #f1f5f9; font-size: 14px; color: #333;">
            <div style="text-align: center; color: #888; font-size: 12px; margin-bottom: 10px;">
                -- Bắt đầu cuộc trò chuyện --
            </div>
        </div>

        <div style="padding: 10px; border-top: 1px solid #ddd; display: flex;">
            <input type="text" id="msg-input" placeholder="Nhập tin nhắn..." 
                   style="flex: 1; padding: 5px; border: 1px solid #ccc; border-radius: 4px; outline: none;">
            <button onclick="sendMsg()" style="margin-left: 5px; background: #0ea5e9; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>



<!-- vi---quản lí chat -->


<script>
    // --- CẤU HÌNH ---
    // Lấy tên khách từ PHP, nếu chưa đăng nhập thì là 'Khách'
    const myName = "<?= $_SESSION['user']['Username'] ?? 'Khách' ?>";
    const wsPort = 8081; // Cổng Socket
    let conn;

    // 1. HÀM TẢI LỊCH SỬ TỪ DB (Chỉ hiện tin của mình và Quản lý)
    function loadHistory() {
        fetch('index.php?controller=chat&action=getHistory')
            .then(response => response.json())
            .then(data => {
                const box = document.getElementById('chat-content');
                box.innerHTML = '<div style="text-align:center; color:#888; font-size:12px; margin-bottom:10px;">-- Lịch sử chat --</div>';
                
                data.forEach(msg => {
                    // Logic lọc: Chỉ hiện tin của TÔI hoặc của QUẢN LÝ
                    // (Để tránh khách này nhìn thấy tin của khách kia nếu Server chưa lọc kỹ)
                    if (msg.sender_name === myName || msg.sender_name === 'Quản lý') {
                        appendMessage(msg.sender_name, msg.message, msg.sender_name === myName);
                    }
                });
            })
            .catch(err => console.error("Lỗi tải lịch sử:", err));
    }

    // 2. KẾT NỐI SOCKET
    function connectChat() {
        try {
            conn = new WebSocket('ws://localhost:' + wsPort);
            
            conn.onopen = function(e) {
                console.log("✅ Đã kết nối Chat Server!");
                loadHistory(); // Tải lịch sử ngay khi kết nối xong
            };

            conn.onmessage = function(e) {
                const data = JSON.parse(e.data);
                
                // 👇 ĐOẠN QUAN TRỌNG: Nếu tin nhắn là của CHÍNH MÌNH vừa gửi -> Bỏ qua 
                // (Vì ta đã cho hiện ngay lúc bấm gửi rồi, không cần hiện lại lần nữa)
                if (data.name === myName) return;

                // Nếu là tin người khác (Quản lý) -> Hiện lên
                appendMessage(data.name, data.msg, false);
            };

            conn.onerror = function(e) {
                console.log("Lỗi kết nối Chat.");
            };

        } catch(err) {
            console.log("Không thể kết nối Server Chat.");
        }
    }

    // 3. GỬI TIN NHẮN (ĐÃ SỬA ĐỂ HIỆN NGAY LẬP TỨC)
    function sendMsg() {
        const input = document.getElementById('msg-input');
        const msg = input.value.trim();
        
        if (msg === "") return;

        // A. Đóng gói dữ liệu gửi đi
        const data = {
            name: myName,
            msg: msg,
            to: 'Quản lý' // Gửi cho Admin
        };

        // B. Gửi lên Server (nếu đang kết nối)
        if (conn && conn.readyState === WebSocket.OPEN) {
            conn.send(JSON.stringify(data));
        } else {
            console.log("Mất kết nối, đang thử kết nối lại...");
            connectChat(); // Thử kết nối lại nếu rớt mạng
        }

        // C. 👇 QUAN TRỌNG: Hiện tin nhắn lên màn hình NGAY LẬP TỨC (Không chờ Server)
        appendMessage("Me", msg, true);

        // D. Xóa ô nhập
        input.value = '';
    }

    // 4. HÀM VẼ TIN NHẮN RA MÀN HÌNH
    function appendMessage(name, msg, isMe) {
        const box = document.getElementById('chat-content');
        const div = document.createElement('div');
        
        // CSS chỉnh style tin nhắn
        div.style.marginBottom = "8px";
        div.style.textAlign = isMe ? "right" : "left";
        
        const contentSpan = document.createElement('span');
        contentSpan.style.display = "inline-block";
        contentSpan.style.padding = "8px 12px";
        contentSpan.style.borderRadius = "15px";
        contentSpan.style.maxWidth = "80%";
        contentSpan.style.wordWrap = "break-word";
        
        if (isMe) {
            // Tin của mình: Màu xanh, chữ trắng
            contentSpan.style.background = "#0ea5e9";
            contentSpan.style.color = "white";
            contentSpan.innerHTML = msg; 
        } else {
            // Tin của Quản lý: Màu xám, chữ đen
            contentSpan.style.background = "#e2e8f0";
            contentSpan.style.color = "#333";
            // Hiện tên người gửi nếu không phải mình
            contentSpan.innerHTML = `<strong style="font-size:11px; display:block; margin-bottom:2px; color:#64748b">${name}</strong>${msg}`;
        }

        div.appendChild(contentSpan);
        box.appendChild(div);
        
        // Tự cuộn xuống dưới cùng
        box.scrollTop = box.scrollHeight;
    }

    // 5. BẬT TẮT KHUNG CHAT
    function toggleChat() {
        const box = document.getElementById('chat-box');
        const btn = document.getElementById('btn-toggle-chat');
        
        if (box.style.display === 'none') {
            box.style.display = 'flex';
            btn.style.display = 'none';
            // Nếu chưa kết nối thì kết nối luôn
            if (!conn || conn.readyState !== WebSocket.OPEN) connectChat();
        } else {
            box.style.display = 'none';
            btn.style.display = 'block';
        }
    }

    // Bắt sự kiện nhấn Enter để gửi
    document.getElementById('msg-input').addEventListener("keypress", function(event) {
        if (event.key === "Enter") sendMsg();
    });
</script>
</div>

</body>
</html>