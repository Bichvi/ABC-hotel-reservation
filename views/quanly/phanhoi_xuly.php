<?php
// Khởi tạo biến để tránh lỗi
$user = Auth::user(); 
$mode = $mode ?? "list"; 
$dsPhanHoi = $dsPhanHoi ?? [];
$info = $info ?? [];
$history = $history ?? [];
$keyword = $keyword ?? '';
$status = $status ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý phản hồi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
    /* COPY STYLE TỪ FILE CỦA BẠN BẠN ĐỂ ĐỒNG BỘ */
    body { background: linear-gradient(135deg, #0f172a, #1e293b); min-height: 100vh; color: #e5e7eb; font-family: 'Segoe UI', sans-serif; }
    .wrapper { background: rgba(15,23,42,0.86); border-radius: 20px; padding: 25px; border: 1px solid rgba(148,163,184,0.35); box-shadow: 0 20px 40px rgba(0,0,0,0.55); max-width: 1000px; margin: 40px auto; }
    .page-title { font-size: 24px; font-weight: 700; color: #f8fafc; text-transform: uppercase; border-bottom: 2px solid #38bdf8; padding-bottom: 10px; margin-bottom: 20px; text-align: center; }
    
    /* STYLE RIÊNG CHO PHẢN HỒI (GIỐNG WIREFRAME) */
    .table-custom th { background-color: rgba(56, 189, 248, 0.2); color: #38bdf8; }
    .table-custom td { vertical-align: middle; }
    
    /* Màu trạng thái (Testcase TC13) */
    .st-chua { color: #f87171; font-weight: bold; border: 1px solid #f87171; padding: 2px 8px; border-radius: 4px; } /* Đỏ */
    .st-dang { color: #facc15; font-weight: bold; border: 1px solid #facc15; padding: 2px 8px; border-radius: 4px; } /* Vàng Cam */
    .st-da   { color: #4ade80; font-weight: bold; border: 1px solid #4ade80; padding: 2px 8px; border-radius: 4px; } /* Xanh */

    /* Khung chi tiết */
    .detail-box { border: 1px solid #475569; padding: 15px; border-radius: 10px; background: rgba(30, 41, 59, 0.5); margin-bottom: 20px; }
    .chat-bubble { background: rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; margin-top: 5px; font-style: italic; }
    
    .form-control-dark { background: rgba(15,23,42,1); border: 1px solid #475569; color: white; }
    .form-control-dark:focus { background: rgba(15,23,42,1); color: white; border-color: #38bdf8; box-shadow: none; }
</style>
</head>
<body>

<div class="container">
    <!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark border-bottom border-slate-700">
    <div class="container d-flex justify-content-between">
        <a class="navbar-brand" href="index.php?controller=dashboard&action=quanly">
            <i class="fa-solid fa-hotel me-2 text-info"></i>ABC Resort - Quản lý
        </a>
        <div class="d-flex align-items-center">
            <span class="me-3 small">
                <i class="fa-regular fa-user me-1"></i>
                <?= htmlspecialchars($user['Username'] ?? 'manager') ?>
            </span>
            <a href="index.php?controller=auth&action=logout"
               class="btn btn-outline-light btn-sm">
               <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </a>
        </div>
    </div>
</nav>

    <div class="wrapper">
        
        <?php if ($mode === "list"): ?>
            <div class="page-title">Xử lý phản hồi khách hàng</div>
            <form action="index.php" method="GET" class="mb-3 p-3" style="background: rgba(255,255,255,0.05); border: 1px solid #475569; border-radius: 8px;" id="filterForm">
            <input type="hidden" name="controller" value="quanly">
            <input type="hidden" name="action" value="searchPhanHoi">
            
            <div class="row mb-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-dark text-white border-secondary">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        
                        <input type="text" name="keyword" id="keywordInput" class="form-control form-control-dark" 
                               placeholder="Nhập Mã PH, Mã KH, Tên, Email hoặc SĐT..." 
                               value="<?= htmlspecialchars($keyword ?? '') ?>">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <select name="status" id="statusSelect" class="form-select form-control-dark" onchange="document.getElementById('filterForm').submit()">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="ChuaXuLy" <?= ($status ?? '') === 'ChuaXuLy' ? 'selected' : '' ?>>Chưa xử lý</option>
                        <option value="DangXuLy" <?= ($status ?? '') === 'DangXuLy' ? 'selected' : '' ?>>Đang xử lý</option>
                        <option value="DaXuLy" <?= ($status ?? '') === 'DaXuLy' ? 'selected' : '' ?>>Đã xử lý</option>
                    </select>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary fw-bold px-4 flex-grow-1">
                    <i class="fa-solid fa-filter"></i> Lọc kết quả
                </button>
                
                <?php if(!empty($keyword) || !empty($status)): ?>
                    <a href="index.php?controller=quanly&action=phanHoi" class="btn btn-danger fw-bold px-4">
                        <i class="fa-solid fa-xmark"></i> Xóa bộ lọc
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="text-muted small mt-2 fst-italic">
                * Hỗ trợ tìm theo: Mã phản hồi (VD: 1), Mã khách (VD: 5), Họ tên, Email hoặc Số điện thoại.
            </div>
        </form>
 
<!-- 
### Kết quả sau khi cập nhật:
Bây giờ tại ô tìm kiếm, bạn có thể nhập bất cứ thông tin gì:
1.  Nhập **"1"** -> Ra Mã PH 1, Mã KH 1, hoặc SĐT có chứa số 1.
2.  Nhập **"An"** -> Ra khách tên An.
3.  Nhập **"0909"** -> Ra SĐT chứa đầu 0909.
4.  Nhập **"@gmail"** -> Ra tất cả email Gmail.

Hệ thống sẽ tự động quét qua tất cả các cột dữ liệu đó để trả về kết quả. -->

            
            <div class="d-flex justify-content-between mb-3">
                <span class="fw-bold">Danh sách phản hồi từ khách hàng</span>
                <a href="index.php?controller=dashboard&action=quanly" class="btn btn-sm btn-secondary"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
            </div>

            <?php if (empty($dsPhanHoi)): ?>
                <div class="alert alert-warning text-center py-4" style="background: rgba(251, 191, 36, 0.15); border: 1px solid #facc15; color: #fef3c7;">
                    <i class="fa-solid fa-circle-exclamation fa-2x mb-3"></i>
                    <h5 class="mb-2">Không tìm thấy phản hồi</h5>
                    <p class="mb-0 small">
                        <?php if (!empty($keyword) || !empty($status)): ?>
                            Không có phản hồi nào khớp với bộ lọc của bạn.
                        <?php else: ?>
                            Chưa có phản hồi nào từ khách hàng.
                        <?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
                <table class="table table-dark table-hover table-custom">
                    <thead>
                        <tr>
                            <th>MaPH</th><th>MaKH</th><th>Họ tên</th><th>Email</th><th>SĐT</th><th>Tình trạng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dsPhanHoi as $ph): ?>
                            <?php 
                                $cls = 'st-chua'; $txt = 'Chưa xử lý';  
                                if ($ph['TinhTrang'] == 'DangXuLy') { $cls = 'st-dang'; $txt = 'Đang xử lý'; }
                                if ($ph['TinhTrang'] == 'DaXuLy')   { $cls = 'st-da';   $txt = 'Đã xử lý'; }
                            ?>
                            <tr style="cursor:pointer" onclick="location.href='index.php?controller=quanly&action=chiTietPhanHoi&id=<?= $ph['MaPH'] ?>'">
                                <td>PH<?= str_pad($ph['MaPH'], 3, '0', STR_PAD_LEFT) ?></td>
                                <td><?= $ph['MaKhachHang'] ? 'KH'.$ph['MaKhachHang'] : '---' ?></td>
                                <td><?= htmlspecialchars(($ph['HoTenKH'] ?: $ph['TenKH_Goc']) ?? '') ?></td>
                                <td><?= htmlspecialchars($ph['Email'] ?? '') ?></td>
                                <td><?= htmlspecialchars($ph['SDT'] ?? '') ?></td>
                                <td><span class="<?= $cls ?>"><?= $txt ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>

<?php if ($mode === "detail"): ?>
            <div class="page-title">Chi tiết phản hồi khách hàng</div>

            <div class="row">
                <div class="col-md-12">
                    <div class="detail-box">
                        <div class="d-flex justify-content-between text-info fw-bold mb-3 border-bottom border-secondary pb-2">
                            <span>Mã KH: <?= $info['MaKhachHang'] ? 'KH'.$info['MaKhachHang'] : 'Vãng lai' ?></span>
                            <span class="border border-info px-2 rounded">Mã phản hồi: PH<?= str_pad($info['MaPH'], 3, '0', STR_PAD_LEFT) ?></span>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><i class="fa-solid fa-user me-2"></i><strong>Khách hàng:</strong> <?= $info['HoTenKH'] ?: $info['TenKH_Tk'] ?></p>
                                <p class="mb-1"><i class="fa-solid fa-envelope me-2"></i><strong>Email:</strong> <?= $info['Email'] ?></p>
                                <p class="mb-1"><i class="fa-solid fa-phone me-2"></i><strong>SĐT:</strong> <?= $info['SDT'] ?></p>
                            </div>
                            
                            <div class="col-md-6 border-start border-secondary">
                                <p class="mb-1">
                                    <i class="fa-solid fa-tag me-2"></i><strong>Dịch vụ:</strong> 
                                    <span class="text-warning fw-bold"><?= $info['LoaiDichVu'] ?: 'Chung' ?></span>
                                </p>
                                <p class="mb-1">
                                    <i class="fa-solid fa-star me-2"></i><strong>Đánh giá:</strong>
                                    <?php 
                                        // Logic hiển thị ngôi sao
                                        $stars = (int)$info['MucDoHaiLong'];
                                        for($i=1; $i<=5; $i++) {
                                            if($i <= $stars) echo '<i class="fa-solid fa-star text-warning"></i>';
                                            else echo '<i class="fa-regular fa-star text-secondary"></i>';
                                        }
                                    ?>
                                    <span class="small text-muted ms-2">(<?= $stars ?>/5)</span>
                                </p>
                                <p class="mb-1">
                                    <i class="fa-solid fa-clock me-2"></i><strong>Ngày gửi:</strong> 
                                    <?= date('d/m/Y H:i', strtotime($info['NgayPhanHoi'])) ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <span class="text-white fw-bold"><i class="fa-solid fa-comment-dots me-2"></i>Nội dung phản hồi:</span>
                            <div class="chat-bubble mt-2">
                                <?= nl2br(htmlspecialchars($info['NoiDung'])) ?>
                            </div>
                        </div>

                        <?php if (!empty($info['TepDinhKem'])): ?>
                            <div class="mt-3">
                                <span class="text-white fw-bold"><i class="fa-solid fa-paperclip me-2"></i>Hình ảnh đính kèm:</span>
                                <div class="mt-2 p-2 bg-dark rounded border border-secondary text-center">
                                    <img src="uploads/phanhoi/<?= $info['TepDinhKem'] ?>" alt="Ảnh đính kèm" 
                                         class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                                    
                                    <div class="mt-2">
                                        <a href="uploads/phanhoi/<?= $info['TepDinhKem'] ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="fa-solid fa-magnifying-glass-plus"></i> Xem phóng to
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>


<?php if (!empty($history)): ?>
                <div class="text-center text-muted mb-2 fw-bold" style="font-size: 14px; color: #aaa;">--- LỊCH SỬ XỬ LÝ ---</div>
                
                <div class="history-container mb-4" style="max-height: 300px; overflow-y: auto;">
                    <?php foreach ($history as $his): ?>
                        <?php 
                            // --- LOGIC MỚI: ĐỌC NHÃN TRONG NỘI DUNG ĐỂ TÔ MÀU VĨNH VIỄN ---
                            
                            // 1. Mặc định là màu Cam (Đang xử lý)
                            $borderClr = '#f59e0b'; // Cam
                            $bgClr     = 'rgba(245, 158, 11, 0.1)'; // Cam nhạt
                            $textClr   = '#d97706'; // Cam đậm
                            $icon      = 'fa-spinner';
                            $statusText = 'Đang xử lý';

                            // Lấy nội dung gốc từ DB
                            $contentShow = $his['NoiDungTraLoi'];

                            // 2. Kiểm tra xem trong nội dung có từ khóa "[ĐÃ XỬ LÝ]" không
                            if (strpos($contentShow, '[ĐÃ XỬ LÝ]') !== false) {
                                // Nếu có -> Đổi sang màu Xanh
                                $borderClr = '#22c55e'; // Xanh lá
                                $bgClr     = 'rgba(34, 197, 94, 0.1)'; // Xanh nhạt
                                $textClr   = '#16a34a'; // Xanh đậm
                                $icon      = 'fa-check-circle';
                                $statusText = 'Đã xử lý';
                                
                                // Xóa cái nhãn đi để hiển thị nội dung cho đẹp
                                $contentShow = str_replace('[ĐÃ XỬ LÝ] ', '', $contentShow);
                            } else {
                                // Xóa nhãn Đang xử lý nếu có
                                $contentShow = str_replace('[ĐANG XỬ LÝ] ', '', $contentShow);
                            }
                        ?>

                        <div class="detail-box" style="background: <?= $bgClr ?>; border: 1px solid <?= $borderClr ?>; border-left: 5px solid <?= $borderClr ?>; margin-bottom: 10px; padding: 10px;">
                            <div class="d-flex justify-content-between small mb-1" style="color: <?= $textClr ?>;">
                                <strong>
                                    <i class="fa-solid <?= $icon ?> me-1"></i> 
                                    <?= $his['TenNV'] ?? 'Quản lý' ?>
                                    <span class="ms-1 border border-current px-1 rounded" style="font-size:11px; border-color:<?= $textClr ?>!important"><?= $statusText ?></span>
                                </strong>
                                <span><?= date('d/m/Y H:i', strtotime($his['NgayTraLoi'])) ?></span>
                            </div>
                            <div style="color: #e5e7eb; font-size: 15px;">
                                <?= nl2br(htmlspecialchars($contentShow)) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="text-center text-muted mb-3 fw-bold">.........................................</div>

            <form action="index.php?controller=quanly&action=luuPhanHoi" method="POST" 
                  onsubmit="return confirm('Nhấn Xác nhận nếu bạn muốn gửi phản hồi cho khách hàng?');">
                
                <input type="hidden" name="MaPH" value="<?= $info['MaPH'] ?>">

                <div class="detail-box" style="border-color: #38bdf8;">
                    <div class="d-flex justify-content-between mb-2">
                        <strong class="text-success">Trả lời khách hàng (Mới):</strong>
                        <small>Ngày trả lời: <?= date('d/m/Y') ?></small>
                    </div>
                    
                    <textarea name="NoiDungTraLoi" class="form-control form-control-dark mb-3" rows="4" required placeholder="Nhập nội dung trả lời tại đây..."></textarea>

                    <div class="mb-3">
                        <label class="fw-bold me-3">Trạng thái xử lý:</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="TinhTrang" value="DangXuLy" id="r1" <?= $info['TinhTrang']=='DangXuLy'?'checked':'' ?> required>
                            <label class="form-check-label text-warning" for="r1">Đang xử lý</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="TinhTrang" value="DaXuLy" id="r2" <?= $info['TinhTrang']=='DaXuLy'?'checked':'' ?>>
                            <label class="form-check-label text-success" for="r2">Đã xử lý</label>
                        </div>
                    </div>

                    <div class="text-center">
                        <button class="btn btn-success px-4 py-2 fw-bold me-2">Gửi & Lưu</button>
                        <a href="index.php?controller=quanly&action=phanHoi" class="btn btn-outline-secondary px-4 py-2">Hủy</a>
                    </div>
                </div>
            </form>

        <?php endif; ?>
        </div>
</div>

<script>
// Auto-submit form khi thay đổi dropdown trạng thái (đã có onchange inline)
// Auto-submit form khi gõ từ khóa (sau 500ms không gõ nữa)
let typingTimer;
const keywordInput = document.getElementById('keywordInput');
const filterForm = document.getElementById('filterForm');

if (keywordInput && filterForm) {
    keywordInput.addEventListener('keyup', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function() {
            filterForm.submit();
        }, 500); // Đợi 500ms sau khi người dùng ngừng gõ
    });

    keywordInput.addEventListener('keydown', function() {
        clearTimeout(typingTimer);
    });
}
</script>

</body>
</html>