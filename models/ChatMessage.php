<?php
class ChatMessage {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->db->set_charset("utf8mb4");
    }

    /**
     * Lưu tin nhắn mới
     */
    public function sendMessage($senderName, $message) {
        $sql = "INSERT INTO chat_messages (sender_name, message, created_at) 
                VALUES (?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $senderName, $message);
        
        return $stmt->execute();
    }

    /**
     * Lấy tất cả tin nhắn (mới nhất trước)
     */
    public function getAllMessages($limit = 100) {
        $sql = "SELECT * FROM chat_messages 
                ORDER BY created_at ASC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy tin nhắn mới sau một thời điểm (cho polling)
     */
    public function getMessagesSince($lastMessageId) {
        $sql = "SELECT * FROM chat_messages 
                WHERE id > ? 
                ORDER BY created_at ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $lastMessageId);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy tin nhắn mới nhất
     */
    public function getLatestMessage() {
        $sql = "SELECT * FROM chat_messages 
                ORDER BY id DESC 
                LIMIT 1";
        
        $result = $this->db->query($sql);
        return $result ? $result->fetch_assoc() : null;
    }

    /**
     * Đếm tổng số tin nhắn
     */
    public function countMessages() {
        $sql = "SELECT COUNT(*) as total FROM chat_messages";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    /**
     * Xóa tất cả tin nhắn (admin only)
     */
    public function clearAllMessages() {
        $sql = "TRUNCATE TABLE chat_messages";
        return $this->db->query($sql);
    }

    /**
     * Lấy danh sách người gửi duy nhất
     */
    public function getUniqueSenders() {
        $sql = "SELECT DISTINCT sender_name, MAX(created_at) as last_message_time 
                FROM chat_messages 
                GROUP BY sender_name 
                ORDER BY last_message_time DESC";
        
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Lấy tin nhắn của một người cụ thể
     */
    public function getMessagesBySender($senderName) {
        $sql = "SELECT * FROM chat_messages 
                WHERE sender_name = ? 
                ORDER BY created_at ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $senderName);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
