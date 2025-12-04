<!-- views/dashboard/quanly.php -->
<?php
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Quản lý - ABC Resort</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0f172a, #1e293b);
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
            background: radial-gradient(circle at top left, rgba(59,130,246,0.25), rgba(15,23,42,0.95));
            color: #e5e7eb;
            box-shadow: 0 18px 40px rgba(0,0,0,0.65);
            transition: 0.2s ease-in-out;
            position: relative;
            overflow: hidden;
        }
        .card-module:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 24px 60px rgba(0,0,0,0.7);
            border-color: rgba(96,165,250,0.9);
        }
        .icon-circle {
            width: 52px;
            height: 52px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(15,23,42,0.85);
            border: 1px solid rgba(148,163,184,0.5);
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
        .section-title {
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: .18em;
            color: #9ca3af;
        }
        .footer-text {
            font-size: 0.8rem;
            color: #9ca3af;
        }

        /* --- CSS CHO WIDGET CHAT ADMIN --- */
    #admin-chat-widget { position: fixed; bottom: 20px; right: 20px; z-index: 9999; }
    
    #admin-chat-box {
        display: none; width: 350px; height: 500px; 
        background: #fff; border-radius: 12px; 
        box-shadow: 0 5px 25px rgba(0,0,0,0.3); 
        overflow: hidden; flex-direction: column;
        font-family: sans-serif;
    }

    /* Danh sách người chat (Bên trái hoặc list dọc) */
    .chat-list-item {
        padding: 10px; border-bottom: 1px solid #eee; cursor: pointer;
        display: flex; align-items: center; gap: 10px; color: #333;
    }
    .chat-list-item:hover { background: #f0f9ff; }
    .chat-list-item.active { background: #e0f2fe; border-left: 4px solid #0ea5e9; }
    
    /* Khu vực tin nhắn */
    .admin-msg-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
    .msg-container { flex: 1; padding: 10px; overflow-y: auto; background: #f8fafc; font-size: 14px; }
    
    .msg-bubble { padding: 8px 12px; border-radius: 15px; margin-bottom: 8px; max-width: 80%; word-wrap: break-word; }
    .msg-me { align-self: flex-end; background: #0ea5e9; color: white; border-bottom-right-radius: 2px; margin-left: auto; }
    .msg-guest { align-self: flex-start; background: #e2e8f0; color: #333; border-bottom-left-radius: 2px; }

    /* Badge thông báo tin mới */
    .new-msg-badge { 
        background: #ef4444; color: white; font-size: 10px; padding: 2px 6px; 
        border-radius: 10px; position: absolute; top: -5px; right: -5px;
    }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark border-bottom border-slate-700">
    <div class="container">
        <a class="navbar-brand brand-logo" href="#">
            <i class="fa-solid fa-hotel me-2 text-info"></i>ABC Resort - Quản lý
        </a>
        <div class="d-flex align-items-center">
            <span class="me-3 small text-slate-300">
                <i class="fa-regular fa-user me-1"></i>
                <?= htmlspecialchars($user['Username'] ?? 'manager') ?>
            </span>

            <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </a>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container main-wrapper">

    <div class="row mb-4">
        <div class="col-lg-8">
            <h2 class="mb-1">Xin chào, Quản lý!</h2>
            <p class="text-secondary">
                Theo dõi và điều hành hồ sơ khách hàng, phản hồi & chương trình khuyến mãi.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <span class="quick-pill">
                <i class="fa-regular fa-calendar"></i> <?= date('d/m/Y') ?>
            </span>
        </div>
    </div>

    <div class="mb-3">
        <span class="section-title">Chức năng nghiệp vụ chính</span>
    </div>

    <div class="row g-3">

        <!-- 1. Cập nhật hồ sơ khách hàng -->
        <div class="col-md-4">
            <a href="index.php?controller=quanly&action=danhsachKhachHang" class="text-decoration-none text-light">
                <div class="card card-module p-3 h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-user-pen fa-lg text-warning"></i>
                        </div>
                        <span class="quick-pill"><i class="fa-solid fa-pen"></i> Chỉnh sửa</span>
                    </div>

                    <h5 class="mb-1">Quản lý hồ sơ khách hàng</h5>
                    <p class="small text-secondary mb-2">
                        Thêm – sửa – xóa hồ sơ khách trong một giao diện.
                    </p>

                    <div class="d-flex justify-content-between small">
                        <span><i class="fa-solid fa-list me-1"></i>Danh sách khách</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 2. Phản hồi khách hàng -->
        <div class="col-md-4">
            <a href="index.php?controller=quanly&action=phanHoi" class="text-decoration-none text-light">
                <div class="card card-module p-3 h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-comments fa-lg text-primary"></i>
                        </div>
                        <span class="quick-pill"><i class="fa-solid fa-reply"></i> Xử lý</span>
                    </div>

                    <h5 class="mb-1">Xử lý phản hồi khách hàng</h5>
                    <p class="small text-secondary mb-2">
                        Theo dõi phản hồi & trả lời khách hàng nhanh chóng.
                    </p>

                    <div class="d-flex justify-content-between small">
                        <span><i class="fa-regular fa-message me-1"></i>Phản hồi</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 3. Tạo chương trình khuyến mãi -->
        <div class="col-md-4">
            <a href="index.php?controller=quanly&action=taoKhuyenMai" class="text-decoration-none text-light">
                <div class="card card-module p-3 h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-gift fa-lg text-success"></i>
                        </div>
                        <span class="quick-pill"><i class="fa-solid fa-tags"></i> Ưu đãi</span>
                    </div>

                    <h5 class="mb-1">Tạo chương trình khuyến mãi</h5>
                    <p class="small text-secondary mb-2">
                        Thiết lập ưu đãi theo loại phòng & dịp lễ.
                    </p>

                    <div class="d-flex justify-content-between small">
                        <span><i class="fa-solid fa-percent me-1"></i>Khuyến mãi</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 4. Danh sách & cập nhật khuyến mãi -->
        <div class="col-md-4">
            <a href="index.php?controller=quanly&action=khuyenMai" class="text-decoration-none text-light">
                <div class="card card-module p-3 h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-pen-to-square fa-lg text-warning"></i>
                        </div>
                        <span class="quick-pill"><i class="fa-solid fa-pen"></i> Cập nhật</span>
                    </div>

                    <h5 class="mb-1">Cập nhật chương trình khuyến mãi</h5>
                    <p class="small text-secondary mb-2">
                        Điều chỉnh mức ưu đãi, thời gian, trạng thái chương trình.
                    </p>

                    <div class="d-flex justify-content-between small">
                        <span><i class="fa-solid fa-list-check me-1"></i>Danh sách KM</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>
            </a>
        </div>

<!--  vi=============quản lí thong báo emai - chat 1:1 -->


        <!-- 5. Gửi thông báo qua email -->
         <div class="col-md-4">
            <a href="index.php?controller=quanly&action=soanThongBao" class="text-decoration-none text-light">
                <div class="card card-module p-3 h-100" style="background: radial-gradient(circle at top left, rgba(168, 85, 247, 0.25), rgba(15,23,42,0.95));">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle">
                            <i class="fa-solid fa-paper-plane fa-lg text-info" style="color: #d8b4fe !important;"></i>
                        </div>
                        <span class="quick-pill"><i class="fa-solid fa-envelope"></i> Gửi Email</span>
                    </div>

                    <h5 class="mb-1">Gửi thông báo khách hàng</h5>
                    <p class="small text-secondary mb-2">
                        Gửi email thông báo bảo trì, chúc mừng hoặc quảng cáo đến toàn bộ khách hàng.
                    </p>

                    <div class="d-flex justify-content-between small">
                        <span><i class="fa-solid fa-bullhorn me-1"></i>Marketing</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>
            </a>
        </div>
        
    </div>

    <div class="mt-4 text-center footer-text">
        ABC Resort – Quản lý | Điều hành hồ sơ khách & chương trình ưu đãi
    </div>
    <div id="admin-chat-widget">
    <button onclick="toggleAdminChat()" style="position: relative; width: 60px; height: 60px; border-radius: 50%; background: #0f172a; border: 2px solid #38bdf8; color: #38bdf8; cursor: pointer; font-size: 24px;">
        <i class="fa-solid fa-comments"></i>
        <span id="total-unread" class="new-msg-badge" style="display: none;">0</span>
    </button>

    <div id="admin-chat-box">
        <div style="background: #0f172a; color: white; padding: 12px; display: flex; justify-content: space-between; align-items: center;">
            <span id="chat-header-title"><i class="fa-solid fa-list me-2"></i>Danh sách hội thoại</span>
            <div>
                <i class="fa-solid fa-arrow-left me-3" id="btn-back-list" onclick="backToList()" style="cursor: pointer; display: none;"></i>
                <i class="fa-solid fa-xmark" onclick="toggleAdminChat()" style="cursor: pointer;"></i>
            </div>
        </div>

        <div id="view-list-users" style="flex: 1; overflow-y: auto; background: white;">
            <div class="text-center text-muted mt-5 small">Chưa có tin nhắn nào.</div>
        </div>

        <div id="view-chat-detail" class="admin-msg-area" style="display: none;">
            <div id="admin-msg-container" class="msg-container"></div>
            
            <div style="padding: 10px; border-top: 1px solid #ddd; display: flex; gap: 5px;">
                <input type="text" id="admin-input" class="form-control form-control-sm" placeholder="Nhập tin nhắn..." onkeypress="handleEnter(event)">
                <button onclick="sendAdminMsg()" class="btn btn-sm btn-primary"><i class="fa-solid fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
</div>

<script>
    // --- CẤU HÌNH CHUNG ---
    const adminName = "<?= $_SESSION['user']['Username'] ?? 'Quản lý' ?>";
    const wsPort = 8081; 
    let conn;
    
    let currentChatUser = null; 
    let users = {}; // { 'KhachA': [msg1, msg2], ... }

    // --- 1. TẢI LỊCH SỬ TỪ DATABASE (QUAN TRỌNG NHẤT) ---
    // --- 1. TẢI LỊCH SỬ (BẢN DEBUG) ---
    function loadAdminHistory() {
        console.log("🚀 Bắt đầu tải lịch sử chat...");
        
        fetch('index.php?controller=chat&action=getHistory')
            .then(res => res.text()) // Đổi sang text để xem lỗi PHP nếu có
            .then(text => {
                try {
                    const data = JSON.parse(text); // Thử parse JSON
                    console.log("✅ Dữ liệu tải về:", data); // Xem log này trong F12

                    users = {}; 
                    
                    data.forEach(msg => {
                        let chatPartner = null;

                        // Debug từng tin nhắn
                        // console.log(`Check tin: ${msg.sender_name} -> ${msg.receiver_name} (Admin là: ${adminName})`);

                        if (msg.sender_name !== adminName) {
                            chatPartner = msg.sender_name;
                        } 
                        else if (msg.sender_name === adminName && msg.receiver_name) {
                            chatPartner = msg.receiver_name;
                        }

                        if (chatPartner) {
                            if (!users[chatPartner]) users[chatPartner] = [];
                            users[chatPartner].push({ 
                                sender: msg.sender_name, 
                                msg: msg.message 
                            });
                        }
                    });
                    
                    // Kiểm tra danh sách user sau khi lọc
                    console.log("📋 Danh sách User:", users);
                    renderUserList(); 

                } catch (e) {
                    console.error("❌ Lỗi JSON:", e);
                    console.log("Nội dung lỗi từ Server:", text); // Sẽ hiện lỗi PHP nếu có
                }
            })
            .catch(err => console.error("❌ Lỗi mạng:", err));
    }


    // --- 2. KẾT NỐI SOCKET ---
    function connectChatServer() {
        try {
            conn = new WebSocket('ws://localhost:' + wsPort);
            
            conn.onopen = function(e) {
                console.log("✅ Socket đã kết nối!");
                document.getElementById('admin-chat-widget').style.display = 'block';
                
                // Gọi hàm tải lịch sử ngay khi kết nối xong
                loadAdminHistory(); 
            };

            conn.onmessage = function(e) {
                const data = JSON.parse(e.data);
                
                // Nếu là tin của chính mình (Admin) vừa gửi -> Bỏ qua (vì đã hiện ở hàm send rồi)
                if (data.name === adminName) return;

                // Xử lý tin khách gửi đến
                handleIncomingMessage(data.name, data.msg);
            };

            conn.onerror = function(e) {
                console.log("Socket lỗi kết nối (Server chưa chạy?)");
            };

        } catch(e) { console.log("Lỗi khởi tạo Socket"); }
    }

    // --- XỬ LÝ TIN ĐẾN ---
    function handleIncomingMessage(fromUser, msg) {
        if (!users[fromUser]) users[fromUser] = [];
        users[fromUser].push({ sender: fromUser, msg: msg });

        // Hiển thị badge đỏ
        const badge = document.getElementById('total-unread');
        badge.innerText = parseInt(badge.innerText) + 1;
        badge.style.display = 'block';

        if (currentChatUser === fromUser) {
            appendBubble(msg, false); // Đang chat với họ -> Hiện luôn
        } else {
            renderUserList(); // Cập nhật list bên trái (đẩy người mới lên đầu)
        }
    }

    // --- VẼ DANH SÁCH KHÁCH HÀNG ---
    function renderUserList() {
        const listDiv = document.getElementById('view-list-users');
        listDiv.innerHTML = "";

        const sortedUsers = Object.keys(users); // Có thể sort theo thời gian nếu muốn

        if (sortedUsers.length === 0) {
            listDiv.innerHTML = '<div class="text-center mt-4 text-muted small">Chưa có hội thoại nào</div>';
            return;
        }

        sortedUsers.forEach(username => {
            const msgs = users[username];
            const lastMsg = msgs[msgs.length - 1].msg;
            
            const item = document.createElement('div');
            item.className = "chat-list-item";
            item.onclick = () => openChat(username);
            item.innerHTML = `
                <div style="width: 40px; height: 40px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-user text-secondary"></i>
                </div>
                <div style="flex: 1; overflow:hidden;">
                    <div style="font-weight: bold; font-size: 14px;">${username}</div>
                    <div style="font-size: 12px; color: #666; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${lastMsg}</div>
                </div>
                <i class="fa-solid fa-chevron-right text-muted small"></i>
            `;
            listDiv.appendChild(item);
        });
    }

    // --- GỬI TIN ---
    function sendAdminMsg() {
        if (!currentChatUser) return;
        const input = document.getElementById('admin-input');
        const msg = input.value.trim();
        if (msg === "") return;

        // 1. Gửi Socket (Real-time) - BẮT BUỘC có trường 'to' để lưu receiver_name
        const data = { name: adminName, to: currentChatUser, msg: msg };
        conn.send(JSON.stringify(data));

        // 2. Lưu vào RAM (để hiện ngay)
        if (!users[currentChatUser]) users[currentChatUser] = [];
        users[currentChatUser].push({ sender: adminName, msg: msg });

        appendBubble(msg, true);
        input.value = "";
    }

    // --- CÁC HÀM GIAO DIỆN KHÁC (GIỮ NGUYÊN) ---
    function appendBubble(msg, isMe) {
        const box = document.getElementById('admin-msg-container');
        const div = document.createElement('div');
        div.className = `msg-bubble ${isMe ? 'msg-me' : 'msg-guest'}`;
        div.innerText = msg;
        box.appendChild(div);
        box.scrollTop = box.scrollHeight;
    }

    function openChat(username) {
        currentChatUser = username;
        document.getElementById('view-list-users').style.display = 'none';
        document.getElementById('view-chat-detail').style.display = 'flex';
        document.getElementById('btn-back-list').style.display = 'inline-block';
        document.getElementById('chat-header-title').innerText = "Chat: " + username;

        const box = document.getElementById('admin-msg-container');
        box.innerHTML = "";
        if (users[username]) {
            users[username].forEach(m => appendBubble(m.msg, m.sender === adminName));
        }
    }

    function backToList() {
        currentChatUser = null;
        document.getElementById('view-list-users').style.display = 'block';
        document.getElementById('view-chat-detail').style.display = 'none';
        document.getElementById('btn-back-list').style.display = 'none';
        document.getElementById('chat-header-title').innerText = 'Danh sách hội thoại';
        renderUserList();
    }

    function toggleAdminChat() {
        const box = document.getElementById('admin-chat-box');
        if (box.style.display === 'none') {
            box.style.display = 'flex';
            document.getElementById('total-unread').style.display = 'none';
        } else {
            box.style.display = 'none';
        }
    }

    function handleEnter(e) { if(e.key === 'Enter') sendAdminMsg(); }

    // KHỞI CHẠY
    window.onload = function() {
        connectChatServer();
    };
</script>

</div>

</body>
</html>