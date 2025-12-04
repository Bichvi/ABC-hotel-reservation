<?php
class Database {
    private static $conn;

    public static function getConnection() {
        if (!self::$conn) {
            self::$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            self::$conn->set_charset("utf8mb4");
        }
        return self::$conn;
    }
}