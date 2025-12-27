
<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) return false; // tôn trọng error_reporting
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CORE
require_once "config/config.php";
require_once "core/Database.php";
require_once "core/Controller.php";
require_once "core/Auth.php";

// LOAD TOÀN BỘ MODEL
foreach (glob(__DIR__ . "/models/*.php") as $file) {
    require_once $file;
}

// Lấy controller & action từ URL
$controller = $_GET['controller'] ?? 'home';
$action     = $_GET['action'] ?? 'index';

$controllerName = ucfirst($controller) . "Controller";
$controllerFile = "controllers/$controllerName.php";

// Kiểm tra controller tồn tại
if (!file_exists($controllerFile)) {
    die("Controller <b>$controllerName</b> không tồn tại!");
}
require_once $controllerFile;

// Tạo object controller
$controllerObj = new $controllerName();

// Kiểm tra action tồn tại
if (!method_exists($controllerObj, $action)) {
    die("Action <b>$action</b> không tồn tại trong controller $controllerName!");
}

// Gọi action
$controllerObj->$action();