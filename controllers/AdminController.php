<?php
class AdminController extends Controller 

{
    public function __construct() {
        $this->requireLogin();
        $this->requireRole([1, 'Admin']);
    }

    // === LIST + CHỌN NGƯỜI DÙNG ===
    public function phanQuyen()
    {
        $modelUser = new User();
        $modelRole = new Role();

        // Nếu có id => chuyển chế độ edit
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $user = $modelUser->getById($id);

            return $this->view('admin/phanquyen', [
                'mode'  => 'edit',
                'user'  => $user,
                'roles' => $modelRole->getAll()
            ]);
        }

        // Mặc định mode list
        $search = trim($_GET['search'] ?? '');
        $users = $search !== '' ? $modelUser->search($search) : $modelUser->getAll();

        $this->view('admin/phanquyen', [
            'mode'  => 'list',
            'users' => $users
        ]);
    }

    // === LƯU PHÂN QUYỀN ===
   public function luuPhanQuyen() {
    $id = (int)$_POST['MaTK'];
    $role = (int)$_POST['MaVaiTro'];
    $mota = trim($_POST['MoTa']);

    $model = new User();

    if ($model->updateRole($id, $role, $mota)) {
        header("Location: index.php?controller=admin&action=phanQuyen&success=1");
    } else {
        header("Location: index.php?controller=admin&action=phanQuyen&id=$id&error=1");
    }
}
}