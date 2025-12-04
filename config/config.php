<?php
// BẬT HIỂN THỊ LỖI KHI DEV
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CẤU HÌNH DB
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // nếu bạn có mật khẩu thì điền vào đây
define('DB_NAME', 'abc_resort1');