<?php
require_once 'core/Database.php';

// Test update
$db = Database::getConnection();
$maNganQuy = 21;

$stmt = $db->prepare("
    UPDATE SoNganQuy 
    SET DaDoiSoat = 1
    WHERE MaNganQuy = ?
");
$stmt->bind_param('i', $maNganQuy);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $db->error]);
}
$stmt->close();
?>
