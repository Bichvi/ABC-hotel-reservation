<?php

abstract class Controller
{
    /**
     * Thông tin user đang đăng nhập (nếu có)
     * Lấy từ Auth::user()
     * Dùng chung cho mọi actor: Lễ tân, Khách hàng, Admin, ...
     */
    protected $user;

    public function __construct()
    {
        // Đảm bảo session đã khởi tạo
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Nếu đã đăng nhập thì lưu user lại cho controller con xài
        if (Auth::check()) {
            $this->user = Auth::user();
        } else {
            $this->user = null;
        }
    }

    /**
     * Hàm load view
     * - $viewPath: ví dụ "dashboard/letan" -> sẽ load: /views/dashboard/letan.php
     * - $data: mảng dữ liệu truyền sang view
     */
    protected function view(string $viewPath, array $data = [])
    {
        extract($data);

        // Giữ nguyên kiểu path giống code cũ của bạn
        $file = __DIR__ . "/../views/" . $viewPath . ".php";

        if (!file_exists($file)) {
            die("View không tồn tại: $viewPath");
        }

        require $file;
    }

    /**
     * Bắt buộc user phải đăng nhập
     * Dùng chung cho mọi actor
     */
    protected function requireLogin(): void
    {
        if (!Auth::check()) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        // Cập nhật lại user trong controller sau khi chắc chắn đã login
        $this->user = Auth::user();
    }

    /**
     * Bắt buộc user phải đăng nhập + thuộc 1 trong các vai trò cho phép
     * Ví dụ:
     *   $this->requireRole([2]);          // chỉ role id = 2 (Lễ tân)
     *   $this->requireRole(['LeTan']);    // theo tên vai trò
     *   $this->requireRole([2,'LeTan']);  // mix
     */
    protected function requireRole(array $allowedRoles): void
    {
        // Đầu tiên chắc chắn đã login
        $this->requireLogin();

        // Nếu không có quyền phù hợp
        if (!Auth::hasRole($allowedRoles)) {
            echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Không có quyền</title></head><body>";
            echo "<p style='font-family:system-ui; padding:20px; color:#b91c1c;'>
                    Bạn không có quyền truy cập chức năng này.
                  </p>";
            echo "<p style='padding:0 20px;'>
                    <a href='index.php' style='color:#2563eb;'>Quay lại trang chủ</a>
                  </p>";
            echo "</body></html>";
            exit;
        }
    }
    
}