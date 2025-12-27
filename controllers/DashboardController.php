<?php

class DashboardController extends Controller
{
    public function __construct()
    {
        
        $this->requireLogin();
    }

    // (nếu bạn có dùng cho lễ tân thì giữ nguyên / tự code)
    public function letan()
    {
        $this->requireRole(['LeTan', 'Lễ tân', 2]);
        $this->view('dashboard/letan');
    }

    // ⚠️ SỬA / THAY THẾ HÀM NÀY
    public function khachhang()
    {
        // Chỉ là route chuyển tiếp, không cần view riêng
        header('Location: index.php?controller=khachhang&action=index');
        exit;
    }
         public function quanly()
    {
        $this->requireRole([6]); // Role 6 = Quản lý
        $this->view('dashboard/quanly');
    }
         public function admin()
    {
        $this->requireRole([1]); // Role 6 = Quản lý
        $this->view('dashboard/admin');
    }
          public function ketoan()
    {
        $this->requireRole([3]); // Role 6 = Quản lý
        $this->view('dashboard/ketoan');
    }
           public function dichvu()
    {
        $this->requireRole([4]); // Role 6 = Quản lý
        $this->view('dashboard/dichvu');
    }
           public function cskh()
    {
        $this->requireRole([5]); // Role 5 = CSKH
        $this->view('dashboard/cskh');
    }
}