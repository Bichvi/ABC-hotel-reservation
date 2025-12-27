<?php
// =======================================
// hash.php - Reset mật khẩu admin về 123456
// =======================================

$dbHost = 'localhost';
$dbName = 'abc_resort1';
$dbUser = 'root';
$dbPass = '';

echo "<h2>Reset mật khẩu admin về 123456</h2>";

try {
    // Kết nối DB
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Hash mật khẩu mới
    $newHash = password_hash("123456", PASSWORD_DEFAULT);

    // Update
    $sql = "UPDATE taikhoan SET Password = :pwd WHERE Username = 'admin' LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pwd' => $newHash]);

    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green;'>✔ Cập nhật thành công!</p>";
    } else {
        echo "<p style='color:red;'>❌ Không tìm thấy tài khoản admin!</p>";
    }

    echo "<p>Hash mới: <code>$newHash</code></p>";
    echo "<p>Bây giờ bạn có thể đăng nhập bằng:<br>
           Username: <b>admin</b><br>
           Password: <b>123456</b></p>";

} catch (Exception $e) {
    echo "<p style='color:red;'>Lỗi: " . $e->getMessage() . "</p>";
    exit;
}