<?php
require_once 'config/config.php';
require_once 'core/Database.php';

$db = Database::getConnection();

if ($db->connect_error) {
    echo "Connection failed: " . $db->connect_error;
} else {
    echo "Connected!<br>";
    
    // Try simple query
    $result = $db->query("SELECT DATABASE()");
    $row = $result->fetch_row();
    echo "Database: " . $row[0] . "<br>";
    
    // Try update
    $stmt = $db->prepare("UPDATE songanquy SET DaDoiSoat = 1 WHERE MaNganQuy = 22");
    if ($stmt->execute()) {
        echo "Update successful!<br>";
        echo "Affected rows: " . $stmt->affected_rows . "<br>";
    } else {
        echo "Update failed: " . $stmt->error . "<br>";
    }
    
    // Check result
    $check = $db->query("SELECT DaDoiSoat FROM songanquy WHERE MaNganQuy = 22");
    $row = $check->fetch_assoc();
    echo "DaDoiSoat value: " . $row['DaDoiSoat'];
}
?>
