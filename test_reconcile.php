<?php
require_once 'core/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maNganQuy = $_POST['id'] ?? 0;
    echo "ID received: " . $maNganQuy . "\n";
    
    if ($maNganQuy > 0) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE songanquy SET DaDoiSoat = 1 WHERE MaNganQuy = ?");
        $stmt->bind_param('i', $maNganQuy);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $db->error]);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<body>
<button onclick="test()">Test POST</button>
<script>
function test() {
    fetch('test_reconcile.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=21'
    })
    .then(r => r.text())
    .then(text => {
        console.log(text);
        alert(text);
    });
}
</script>
</body>
</html>
