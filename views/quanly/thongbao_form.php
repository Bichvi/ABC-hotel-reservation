<?php $user = Auth::user(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Gửi thông báo - ABC Resort</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    body { background: linear-gradient(135deg, #0f172a, #1e293b); min-height: 100vh; color: #e5e7eb; font-family: 'Segoe UI', sans-serif; }
    
    .home-icon { border-radius: 6px; background: rgba(15,23,42,0.15); padding: 4px; }
    .home-icon:hover { background: rgba(56,189,248,0.08); cursor: pointer; }

    .wrapper { 
        background: radial-gradient(circle at top left, rgba(168, 85, 247, 0.15), rgba(15,23,42,0.95)); 
        border-radius: 20px; padding: 30px; 
        border: 1px solid rgba(168, 85, 247, 0.3); 
        max-width: 800px; margin: 40px auto; 
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    }
    
    .form-control { background: rgba(15,23,42,0.78); border: 1px solid #475569; color: #fff; }
    .form-control:focus { background: rgba(15,23,42,1); color: #fff; border-color: #d8b4fe; box-shadow: 0 0 0 2px rgba(168, 85, 247, 0.3); }
    
    .btn-send { 
        background: linear-gradient(135deg, #9333ea, #7c3aed); 
        border: none; color: white; font-weight: bold; 
        padding: 10px 30px; border-radius: 10px; transition: 0.2s;
    }
    .btn-send:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(147, 51, 234, 0.4); }

    /* --- CUSTOM CSS CHO SELECT2 (Giao diện tối) --- */
    .select2-container--default .select2-selection--multiple {
        background-color: rgba(15,23,42,0.78);
        border: 1px solid #475569;
        border-radius: 6px;
        min-height: 45px;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #d8b4fe;
        box-shadow: 0 0 0 2px rgba(168, 85, 247, 0.3);
    }
    /* Màu chữ khi gõ */
    .select2-container--default .select2-search--inline .select2-search__field {
        color: #fff !important;
        font-family: 'Segoe UI', sans-serif;
    }
    /* Style cho các tag KHÁCH HÀNG CŨ (Màu Tím) */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #7c3aed; 
        border: 1px solid #6d28d9;
        color: #fff;
        border-radius: 4px;
    }
    /* Style cho các tag EMAIL NHẬP TAY (Màu Xanh Dương để phân biệt) */
    .select2-container--default .select2-selection--multiple .select2-selection__choice.tag-external {
        background-color: #0ea5e9;
        border-color: #0284c7;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        border-right: 1px solid rgba(255,255,255,0.3);
        margin-right: 5px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        background-color: rgba(0,0,0,0.2);
        color: #fff;
    }
    /* Dropdown menu */
    .select2-dropdown {
        background-color: #1e293b;
        border: 1px solid #475569;
        color: #e5e7eb;
    }
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: #7c3aed;
        color: white;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #334155;
    }
</style>
</head>
<body>
<!-- NAVBAR -->
<nav class="navbar navbar-dark mb-4 border-bottom border-slate-700">
    <div class="container d-flex justify-content-between">
        <span class="navbar-brand">
            <i class="fa-solid fa-hotel me-2 text-info"></i>ABC Resort - Quản lý
        </span>
        <div class="d-flex align-items-center">
            <span class="me-3 small">
                <i class="fa-regular fa-user me-1"></i><?= htmlspecialchars($user['Username']) ?>
            </span>
            <a href="index.php" class="me-2 d-inline-flex align-items-center justify-content-center home-link" title="Trang chủ">
                <i class="fa-solid fa-house fa-lg"></i>
            </a>
            <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </a>
        </div>
    </div>
</nav>


<div class="container">
    <div class="wrapper">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
            <h3 class="m-0" style="color: #d8b4fe;"><i class="fa-solid fa-paper-plane me-2"></i>Gửi Thông Báo</h3>
            <a href="index.php?controller=dashboard&action=quanly" class="btn btn-sm btn-outline-secondary">Quay lại</a>
        </div>

        <form action="index.php?controller=quanly&action=guiThongBao" method="POST" onsubmit="return confirm('Xác nhận gửi email?');">
            
            <div class="mb-3">
                <label class="form-label fw-bold text-light">Gửi đến:</label>
                
                <select id="select-customers" name="NguoiNhan[]" class="form-control" multiple="multiple" required style="width: 100%">
                    
                    <option value="ALL">📢 Gửi tất cả khách hàng trong hệ thống</option>
                    
                    <?php foreach ($listKH as $k): ?>
                        <option value="<?= $k['MaKhachHang'] ?>">
                            <?= htmlspecialchars($k['TenKH']) ?> (<?= $k['Email'] ?>)
                        </option>
                    <?php endforeach; ?>

                </select>

                <div class="form-text text-info mt-2">
                    <i class="fa-solid fa-circle-check me-1"></i> 
                    Bạn có thể <strong>chọn khách hàng có sẵn</strong> HOẶC <strong>gõ trực tiếp email mới</strong> rồi nhấn Enter.
                </div>
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#select-customers').select2({
            placeholder: "Chọn khách hàng hoặc nhập email...",
            allowClear: true,
            tags: true, // Cho phép nhập giá trị mới (Email ngoài)
            tokenSeparators: [',', ' '], // Cho phép paste nhiều email cách nhau bằng phẩy hoặc dấu cách

            // Tùy chỉnh logic khi người dùng gõ Enter để tạo tag mới
            createTag: function (params) {
                var term = $.trim(params.term);

                if (term === '') {
                    return null;
                }

                // Logic: Nếu không chứa '@' thì không cho tạo (để tránh gõ tên linh tinh)
                if (term.indexOf('@') === -1) {
                    return null; 
                }

                return {
                    id: term, // ID của tag mới chính là cái email họ vừa gõ
                    text: term + ' (Email ngoài)', // Hiển thị thêm chữ này cho dễ nhìn
                    newTag: true // Đánh dấu để CSS biết
                }
            },
            
            // Tùy chỉnh hiển thị kết quả tìm kiếm
            language: {
                noResults: function() {
                    return "Không tìm thấy khách hàng. Hãy nhập đầy đủ Email để thêm mới.";
                }
            }
        });
        
        // Sự kiện để đổi màu tag Email ngoài (cho đẹp)
        $('#select-customers').on('select2:select', function (e) {
            var data = e.params.data;
            // Nếu là tag mới (nhập tay), thêm class CSS đặc biệt
            if(data.newTag){
               // Lưu ý: Select2 render lại DOM khá phức tạp, 
               // cách đơn giản nhất là dùng CSS :has hoặc dựa vào text hiển thị
            }
        });
    });
</script>

</body>
</html>