<?php
// Đảm bảo người dùng đã đăng nhập và có quyền CSKH
$user = Auth::user(); 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Chăm sóc khách hàng - ABC Resort</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        /* --- GIỮ NGUYÊN CSS CŨ ĐỂ ĐỒNG BỘ GIAO DIỆN --- */
        body { background: linear-gradient(135deg, #0f172a, #1e293b); min-height: 100vh; color: #e5e7eb; }
        .navbar { background: rgba(15, 23, 42, 0.95) !important; backdrop-filter: blur(10px); }
        .brand-logo { font-weight: 700; letter-spacing: 1px; }
        .main-wrapper { padding: 30px 0; }
        .card-module {
            border-radius: 18px; border: 1px solid rgba(148, 163, 184, 0.3);
            background: radial-gradient(circle at top left, rgba(168, 85, 247, 0.25), rgba(15,23,42,0.95)); /* Đổi màu tím nhẹ cho CSKH */
            color: #e5e7eb; box-shadow: 0 18px 40px rgba(0,0,0,0.65);
            transition: 0.2s ease-in-out; position: relative; overflow: hidden;
        }
        .card-module:hover { transform: translateY(-4px) scale(1.01); border-color: rgba(192, 132, 252, 0.9); }
        .icon-circle { width: 52px; height: 52px; border-radius: 999px; display: flex; align-items: center; justify-content: center; background: rgba(15,23,42,0.85); border: 1px solid rgba(148,163,184,0.5); }
        .quick-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 9px; border-radius: 999px; background: rgba(15,23,42,0.85); border: 1px solid rgba(148,163,184,0.3); font-size: 0.75rem; }
        .section-title { font-size: 0.95rem; text-transform: uppercase; letter-spacing: .18em; color: #9ca3af; }
        
        /* --- CSS CHO CHAT WIDGET (COPY SANG ĐÂY) --- */
        #admin-chat-widget { position: fixed; bottom: 20px; right: 20px; z-index: 9999; }
        #admin-chat-box { display: none; width: 350px; height: 500px; background: #fff; border-radius: 12px; box-shadow: 0 5px 25px rgba(0,0,0,0.3); overflow: hidden; flex-direction: column; font-family: sans-serif; }
        .chat-list-item { padding: 10px; border-bottom: 1px solid #eee; cursor: pointer; display: flex; align-items: center; gap: 10px; color: #333; }
        .chat-list-item:hover { background: #f0f9ff; }
        .admin-msg-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .msg-container { flex: 1; padding: 10px; overflow-y: auto; background: #f8fafc; font-size: 14px; }
        .msg-bubble { padding: 8px 12px; border-radius: 15px; margin-bottom: 8px; max-width: 80%; word-wrap: break-word; }
        .msg-me { align-self: flex-end; background: #0ea5e9; color: white; border-bottom-right-radius: 2px; margin-left: auto; }
        .msg-guest { align-self: flex-start; background: #e2e8f0; color: #333; border-bottom-left-radius: 2px; }
        .new-msg-badge { background: #ef4444; color: white; font-size: 10px; padding: 2px 6px; border-radius: 10px; position: absolute; top: -5px; right: -5px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark border-bottom border-slate-700">
    <div class="container">
        <a class="navbar-brand brand-logo" href="#">
            <i class="fa-solid fa-headset me-2 text-warning"></i>ABC Resort - CSKH
        </a>
        <div class="d-flex align-items-center">
            <span class="me-3 small text-slate-300">
                <i class="fa-regular fa-user me-1"></i> <?= htmlspecialchars($user['Username'] ?? 'Staff') ?>
            </span>
            <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </a>
        </div>
    </div>
</nav>

<div class="container main-wrapper">
    <div class="row mb-4">
        <div class="col-lg-8">
            <h2 class="mb-1">Xin chào, Chăm sóc Khách hàng!</h2>
            <p class="text-secondary">Quản lý khuyến mãi, gửi thông báo và chăm sóc khách hàng một cách chuyên nghiệp.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <span class="quick-pill">
                <i class="fa-regular fa-calendar"></i> <?= date('d/m/Y') ?>
            </span>
        </div>
    </div>

    <div class="mb-3"><span class="section-title">Quản lý khuyến mãi & CSKH</span></div>

    <div class="row g-3">


        <div class="col-md-4">
            <a href="index.php?controller=cskh&action=phanHoi" class="text-decoration-none text-light">
                <div class="card card-module p-3 h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle"><i class="fa-solid fa-comments fa-lg text-primary"></i></div>
                        <span class="quick-pill"><i class="fa-solid fa-reply"></i> Xử lý</span>
                    </div>
                    <h5 class="mb-1">Xử lý phản hồi khách hàng</h5>
                    <p class="small text-secondary mb-2">Theo dõi phản hồi & trả lời khách hàng nhanh chóng.</p>
                    <div class="d-flex justify-content-between small">
                        <span><i class="fa-regular fa-message me-1"></i>Phản hồi</span><i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="index.php?controller=cskh&action=action_xemDS_CSKH" class="text-decoration-none text-light">
                <div class="card card-module p-3 h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle"><i class="fa-solid fa-gift fa-lg text-success"></i></div>
                        <span class="quick-pill"><i class="fa-solid fa-tags"></i> Ưu đãi</span>
                    </div>
                    <h5 class="mb-1">Quản lý chương trình khuyến mãi</h5>
                    <p class="small text-secondary mb-2">Tạo mới, cập nhật và theo dõi các chương trình ưu đãi.</p>
                    <div class="d-flex justify-content-between small">
                        <span><i class="fa-solid fa-list-check me-1"></i>Quản lý KM</span><i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>
            </a>
        </div>



    <div class="mt-4 text-center" style="font-size: 0.8rem; color: #9ca3af;">
        ABC Resort – Chăm sóc Khách hàng | Quản lý khuyến mãi & Marketing
    </div>

    <div id="admin-chat-widget">
        <button onclick="toggleAdminChat()" style="position: relative; width: 60px; height: 60px; border-radius: 50%; background: #0f172a; border: 2px solid #38bdf8; color: #38bdf8; cursor: pointer; font-size: 24px;">
            <i class="fa-solid fa-comments"></i>
            <span id="total-unread" class="new-msg-badge" style="display: none;">0</span>
        </button>

        <div id="admin-chat-box">
            <div style="background: #0f172a; color: white; padding: 12px; display: flex; justify-content: space-between; align-items: center;">
                <span id="chat-header-title"><i class="fa-solid fa-list me-2"></i>Hỗ trợ trực tuyến</span>
                <div>
                    <i class="fa-solid fa-arrow-left me-3" id="btn-back-list" onclick="backToList()" style="cursor: pointer; display: none;"></i>
                    <i class="fa-solid fa-xmark" onclick="toggleAdminChat()" style="cursor: pointer;"></i>
                </div>
            </div>
            <div id="view-list-users" style="flex: 1; overflow-y: auto; background: white; color: #333;">
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
</div>

<script>
    const adminName = "<?= $_SESSION['user']['Username'] ?? 'Staff' ?>"; // Tên nhân viên CSKH
    const wsPort = 8081; 
    let conn;
    let currentChatUser = null; 
    let users = {};

    function loadAdminHistory() {
        fetch('index.php?controller=chat&action=getHistory')
            .then(res => res.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    users = {}; 
                    data.forEach(msg => {
                        let chatPartner = null;
                        if (msg.sender_name !== adminName) chatPartner = msg.sender_name;
                        else if (msg.sender_name === adminName && msg.receiver_name) chatPartner = msg.receiver_name;

                        if (chatPartner) {
                            if (!users[chatPartner]) users[chatPartner] = [];
                            users[chatPartner].push({ sender: msg.sender_name, msg: msg.message });
                        }
                    });
                    renderUserList(); 
                } catch (e) { console.error("Lỗi JSON:", e); }
            });
    }

    function connectChatServer() {
        try {
            conn = new WebSocket('ws://localhost:' + wsPort);
            conn.onopen = function(e) {
                document.getElementById('admin-chat-widget').style.display = 'block';
                loadAdminHistory(); 
            };
            conn.onmessage = function(e) {
                const data = JSON.parse(e.data);
                if (data.name === adminName) return;
                handleIncomingMessage(data.name, data.msg);
            };
        } catch(e) {}
    }

    function handleIncomingMessage(fromUser, msg) {
        if (!users[fromUser]) users[fromUser] = [];
        users[fromUser].push({ sender: fromUser, msg: msg });
        const badge = document.getElementById('total-unread');
        badge.innerText = parseInt(badge.innerText) + 1;
        badge.style.display = 'block';
        if (currentChatUser === fromUser) appendBubble(msg, false);
        else renderUserList();
    }

    function renderUserList() {
        const listDiv = document.getElementById('view-list-users');
        listDiv.innerHTML = "";
        const sortedUsers = Object.keys(users);
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

    function sendAdminMsg() {
        if (!currentChatUser) return;
        const input = document.getElementById('admin-input');
        const msg = input.value.trim();
        if (msg === "") return;
        const data = { name: adminName, to: currentChatUser, msg: msg };
        conn.send(JSON.stringify(data));
        if (!users[currentChatUser]) users[currentChatUser] = [];
        users[currentChatUser].push({ sender: adminName, msg: msg });
        appendBubble(msg, true);
        input.value = "";
    }

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
        document.getElementById('chat-header-title').innerText = 'Hỗ trợ trực tuyến';
        renderUserList();
    }

    function toggleAdminChat() {
        const box = document.getElementById('admin-chat-box');
        if (box.style.display === 'none') {
            box.style.display = 'flex';
            document.getElementById('total-unread').style.display = 'none';
        } else { box.style.display = 'none'; }
    }
    function handleEnter(e) { if(e.key === 'Enter') sendAdminMsg(); }
    window.onload = function() { connectChatServer(); };
</script>

</body>
</html>