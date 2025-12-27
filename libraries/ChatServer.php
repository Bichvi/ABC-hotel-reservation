<?php
namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

// 1. Import file kết nối Database
// (Chỉnh đường dẫn '../core/Database.php' cho đúng cấu trúc của bạn)
require_once dirname(__DIR__) . '/config/config.php'; 
require_once dirname(__DIR__) . '/core/Database.php'; 

class ChatServer implements MessageComponentInterface {
    protected $clients;
    protected $db;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        // Kết nối DB ngay khi Server bật
        $this->db = \Database::getConnection(); 
        echo "--> Server Chat & DB đã sẵn sàng!\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Khách ({$conn->resourceId}) đã kết nối.\n";
    }

// ... (Các đoạn trên giữ nguyên)

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        
        // Lấy thông tin người nhận (nếu không có thì mặc định là null)
        $receiver = $data['to'] ?? null; 

        echo "Nhận tin: {$data['msg']} | Từ: {$data['name']} -> Tới: $receiver\n";

        // 2. LƯU VÀO DATABASE (CÓ NGƯỜI NHẬN)
        $this->saveMessageToDB($data['name'], $receiver, $data['msg']);

        // 3. Gửi cho mọi người (Broadcast)
        foreach ($this->clients as $client) {
            $client->send($msg);
        }
    }

    // Hàm lưu tin nhắn (Đã cập nhật)
    private function saveMessageToDB($sender, $receiver, $msg) {
        try {
            if (!$this->db->ping()) {
                $this->db = \Database::getConnection();
            }

            // Thêm cột receiver_name vào câu lệnh SQL
            $stmt = $this->db->prepare("INSERT INTO chat_messages (sender_name, receiver_name, message) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $sender, $receiver, $msg); // 3 biến chuỗi (s-s-s)
            $stmt->execute();
        } catch (\Exception $e) {
            echo "Lỗi lưu DB: " . $e->getMessage() . "\n";
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }

    

}