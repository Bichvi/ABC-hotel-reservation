<?php $user = Auth::user(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Gửi thông báo - ABC Resort</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<style>
    body { background: linear-gradient(135deg, #0f172a, #1e293b); min-height: 100vh; color: #e5e7eb; font-family: 'Segoe UI', sans-serif; }
    
    /* Wrapper màu Tím đồng bộ Dashboard */
    .wrapper { 
        background: radial-gradient(circle at top left, rgba(168, 85, 247, 0.15), rgba(15,23,42,0.95)); 
        border-radius: 20px; padding: 30px; 
        border: 1px solid rgba(168, 85, 247, 0.3); /* Viền tím */
        max-width: 800px; margin: 40px auto; 
    }
    
    .form-control, .form-select { background: rgba(15,23,42,0.78); border: 1px solid #475569; color: #fff; }
    .form-control:focus, .form-select:focus { background: rgba(15,23,42,1); color: #fff; border-color: #d8b4fe; box-shadow: 0 0 0 2px rgba(168, 85, 247, 0.3); }
    
    /* Nút gửi màu Tím */
    .btn-send { 
        background: linear-gradient(135deg, #9333ea, #7c3aed); 
        border: none; color: white; font-weight: bold; 
        padding: 10px 30px; border-radius: 10px; transition: 0.2s;
    }
    .btn-send:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(147, 51, 234, 0.4); }
</style>
</head>
<body>

<div class="container">
    <div class="wrapper">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
            <h3 class="m-0" style="color: #d8b4fe;"><i class="fa-solid fa-paper-plane me-2"></i>Gửi Thông Báo</h3>
            <a href="index.php?controller=dashboard&action=quanly" class="btn btn-sm btn-outline-secondary">Quay lại</a>
        </div>

        <form action="index.php?controller=quanly&action=guiThongBao" method="POST" onsubmit="return confirm('Xác nhận gửi email?');">
            
            <div class="mb-3">
                <label class="form-label fw-bold text-light">Gửi đến:</label>
                <select name="NguoiNhan[]" class="form-select" multiple size="5" required>
                    <option value="ALL" class="fw-bold text-warning">📢 Gửi tất cả khách hàng</option>
                    <optgroup label="Khách hàng cụ thể (Giữ Ctrl để chọn nhiều)">
                        <?php foreach ($listKH as $k): ?>
                            <option value="<?= $k['MaKhachHang'] ?>">
                                <?= htmlspecialchars($k['TenKH']) ?> (<?= $k['Email'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                </select>
                <div class="form-text text-info"><i class="fa-solid fa-circle-info me-1"></i> Giữ phím <strong>Ctrl</strong> (Windows) hoặc <strong>Command</strong> (Mac) để chọn nhiều người.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-light">Tiêu đề Email:</label>
                <input type="text" name="TieuDe" class="form-control" placeholder="VD: Thông báo bảo trì hệ thống..." required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-light">Nội dung thông báo:</label>
                <textarea name="NoiDung" class="form-control" rows="6" placeholder="Nhập nội dung chi tiết..." required></textarea>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-send">
                    <i class="fa-solid fa-envelope-circle-check me-2"></i> Gửi Ngay
                </button>
            </div>

        </form>
    </div>
</div>

</body>
</html>