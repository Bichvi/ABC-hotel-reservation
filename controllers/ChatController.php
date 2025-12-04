<?php
class ChatController extends Controller {
    
    // URL: index.php?controller=chat&action=getHistory
    // Hàm này được gọi bởi Javascript (fetch) để lấy tin nhắn cũ
    public function getHistory() {
        // Kết nối DB
        $db = Database::getConnection();
        
        // Lấy 50 tin nhắn gần nhất, sắp xếp theo thời gian
        // Quan trọng: Lấy cả cột receiver_name để JS phân loại
        $sql = "SELECT * FROM (
                    SELECT * FROM chat_messages ORDER BY id DESC LIMIT 50
                ) sub ORDER BY id ASC";
                
        $result = $db->query($sql);
        
        if ($result) {
            $messages = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $messages = [];
        }

        // Trả về JSON
        header('Content-Type: application/json');
        echo json_encode($messages);
        exit; // Dừng luôn, không load view
    }
}
?>