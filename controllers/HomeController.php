<?php

class HomeController extends Controller
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $user = $_SESSION['user'] ?? null;

        // Nếu muốn: nếu đã đăng nhập và là nhân viên thì đẩy về trang lễ tân / quản lý
        if ($user && !empty($user['Role'])) {
            // Ví dụ: Role = 'LeTan' thì cho về màn lễ tân
            if ($user['Role'] === 'LeTan') {
                header("Location: index.php?controller=letan&action=index");
                exit;
            }
            // Nếu có role khác thì tuỳ bạn redirect
        }

        // Render view trang chủ
        $this->view('home/trang_chu', [
            'user' => $user
        ]);
    }
}