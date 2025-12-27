<?php
// Strict JSON API - no HTML output
header('Content-Type: application/json; charset=utf-8');

try {
    // Config
    require_once 'config/config.php';
    require_once 'core/Database.php';
    require_once 'core/Auth.php';
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $maNganQuy = (int)($_POST['id'] ?? 0);
    
    if ($maNganQuy <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $db = Database::getConnection();
    
    // Use simple query without prepared statement
    $sql = "UPDATE songanquy SET DaDoiSoat = 1, NgayDoiSoat = NOW() WHERE MaNganQuy = " . $maNganQuy;
    
    if ($db->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Updated successfully'], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $db->error], JSON_UNESCAPED_UNICODE);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
