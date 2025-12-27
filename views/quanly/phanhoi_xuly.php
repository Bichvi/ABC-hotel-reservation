<?php
// Khởi tạo biến để tránh lỗi
$user = Auth::user(); 
$mode = $mode ?? "list"; 
$dsPhanHoi = $dsPhanHoi ?? [];
$info = $info ?? [];
$history = $history ?? [];
$keyword = $keyword ?? '';
$status = $status ?? '';

// Xác định controller và action dựa trên vai trò
$isCSKH = ($user['MaVaiTro'] ?? 0) == 5;
$controllerName = $isCSKH ? 'cskh' : 'quanly';
$dashboardAction = $isCSKH ? 'cskh' : 'quanly';
$roleName = $isCSKH ? 'CSKH' : 'Quản lý';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý phản hồi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
    body { 
        background: linear-gradient(135deg, #0f172a, #1e293b); 
        min-height: 100vh; 
        color: #e5e7eb; 
        font-family: 'Segoe UI', sans-serif; 
    }
    
    .home-icon { border-radius: 6px; background: rgba(15,23,42,0.15); padding: 4px; }
    .home-icon:hover { background: rgba(56,189,248,0.08); cursor: pointer; }

    .navbar {
        background: rgba(15, 23, 42, 0.95) !important;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    }
    
    .wrapper { 
        background: radial-gradient(circle at top left, rgba(168, 85, 247, 0.2), rgba(15,23,42,0.95));
        border-radius: 24px; 
        padding: 35px; 
        border: 1px solid rgba(148, 163, 184, 0.3);
        box-shadow: 0 20px 60px rgba(0,0,0,0.6);
        margin: 40px auto;
        max-width: 1200px;
        backdrop-filter: blur(10px);
    }
    
    .page-title { 
        font-size: 30px; 
        font-weight: 700; 
        color: #f8fafc; 
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        background: linear-gradient(135deg, #a78bfa, #8b5cf6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* CSS CHO NÚT BACK (NẾU CHƯA CÓ) */
    .btn-back { 
        background: rgba(148, 163, 184, 0.15); 
        border: 1px solid rgba(148, 163, 184, 0.3); 
        color: #e5e7eb; 
        padding: 8px 20px; 
        border-radius: 12px; 
        text-decoration: none; 
        transition: 0.2s; 
        display: inline-flex; 
        align-items: center; 
        font-size: 14px; 
        font-weight: 600; 
    }
    .btn-back:hover { 
        background: rgba(148, 163, 184, 0.3); 
        color: #ffffff; 
        transform: translateX(-3px); 
    } 
    
    /* STYLE TABLE */
    .table-custom { 
        background: transparent;
        border-radius: 12px;
        overflow: hidden;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-custom thead {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(99, 102, 241, 0.2));
        backdrop-filter: blur(10px);
    }
    .table-custom thead th { 
        color: #f1f5f9;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 18px 15px;
        border: none;
        border-bottom: 2px solid rgba(139, 92, 246, 0.3);
    }
    .table-custom tbody td { 
        color: #f8fafc;
        padding: 18px 15px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.15);
        font-weight: 500;
        background: rgba(15,23,42,0.4);
        vertical-align: middle; 
    }
    .table-custom tbody tr {
        transition: all 0.2s ease;
    }
    .table-custom tbody tr:hover {
        background: linear-gradient(90deg, rgba(139, 92, 246, 0.15), rgba(99, 102, 241, 0.15));
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);
    }
    
    /* Màu trạng thái */
    .st-chua { 
        color: #ffffff; 
        font-weight: 700; 
        background: linear-gradient(135deg, #ef4444, #dc2626);
        padding: 6px 14px; 
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
    }
    .st-dang { 
        color: #ffffff; 
        font-weight: 700; 
        background: linear-gradient(135deg, #f59e0b, #d97706);
        padding: 6px 14px; 
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
    }
    .st-da { 
        color: #ffffff; 
        font-weight: 700; 
        background: linear-gradient(135deg, #10b981, #059669);
        padding: 6px 14px; 
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    /* Khung chi tiết */
    .detail-box { 
        border: 1px solid rgba(139, 92, 246, 0.3);
        padding: 20px; 
        border-radius: 12px; 
        background: rgba(15,23,42,0.6);
        margin-bottom: 20px;
        backdrop-filter: blur(5px);
    }
    .chat-bubble { 
        background: rgba(168, 85, 247, 0.1);
        padding: 12px; 
        border-radius: 8px; 
        margin-top: 8px;
        border-left: 3px solid #a78bfa;
        color: #f8fafc;
    }
    
    .form-control-dark { 
        background: rgba(15,23,42,0.78);
        border: 1px solid rgba(148, 163, 184, 0.3);
        color: #f8fafc;
    }
    .form-control-dark:focus { 
        background: rgba(15,23,42,0.9);
        color: #f8fafc;
        border-color: #a78bfa;
        box-shadow: 0 0 0 0.2rem rgba(167, 139, 250, 0.25);
    }
    
    .form-control-dark::placeholder {
        color: #94a3b8;
    }
    
    .history-container::-webkit-scrollbar {
        width: 8px;
    }
    .history-container::-webkit-scrollbar-track {
        background: rgba(15,23,42,0.5);
        border-radius: 4px;
    }
    .history-container::-webkit-scrollbar-thumb {
        background: rgba(139, 92, 246, 0.5);
        border-radius: 4px;
    }
    .history-container::-webkit-scrollbar-thumb:hover {
        background: rgba(139, 92, 246, 0.7);
    }
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
            <a href="index.php" class="me-2 d-inline-flex align-items-center justify-content-center home-link" title="Trang chủ">
                <i class="fa-solid fa-house fa-lg"></i>
            </a>
            <a href="index.php?controller=auth&action=logout"
               class="btn btn-outline-light btn-sm">
               <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
            </a>
        </div>
    </div>
</nav>

    <div class="wrapper">
            <div class="action-bar d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
                <?php if ($mode === "list"): ?>
                    <a href="index.php?controller=dashboard&action=quanly" class="btn-back">
                        <i class="fa-solid fa-arrow-left me-2"></i>Quay lại Dashboard
                    </a>
                <?php else: ?>
                    <a href="index.php?controller=quanly&action=phanHoi" class="btn-back">
                        <i class="fa-solid fa-arrow-left me-2"></i>Quay lại Danh sách phản hồi
                    </a>
                <?php endif; ?>
                
            </div>
        
        <?php if ($mode === "list"): ?>
            <div class="page-title">Xử lý phản hồi khách hàng</div>
            <form action="index.php" method="GET" class="mb-3 p-3" style="background: rgba(255,255,255,0.05); border: 1px solid #475569; border-radius: 8px;" id="filterForm">
            <input type="hidden" name="controller" value="quanly">
            <input type="hidden" name="action" value="searchPhanHoi">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-dark text-white border-secondary">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        
                        <input type="text" name="keyword" id="keywordInput" class="form-control form-control-dark" 
                               placeholder="Nhập Mã PH, Mã KH, Tên, Email hoặc SĐT..." 
                               value="<?= htmlspecialchars($keyword ?? '') ?>">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <select name="status" id="statusSelect" class="form-select form-control-dark" onchange="document.getElementById('filterForm').submit()">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="ChuaXuLy" <?= ($status ?? '') === 'ChuaXuLy' ? 'selected' : '' ?>>Chưa xử lý</option>
                        <option value="DangXuLy" <?= ($status ?? '') === 'DangXuLy' ? 'selected' : '' ?>>Đang xử lý</option>
                        <option value="DaXuLy" <?= ($status ?? '') === 'DaXuLy' ? 'selected' : '' ?>>Đã xử lý</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <select name="rating" id="ratingSelect" class="form-select form-control-dark" onchange="document.getElementById('filterForm').submit()">
                        <option value="">-- Tất cả đánh giá --</option>
                        <option value="5" <?= ($rating ?? '') === '5' ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ (5 sao)</option>
                        <option value="4" <?= ($rating ?? '') === '4' ? 'selected' : '' ?>>⭐⭐⭐⭐ (4 sao)</option>
                        <option value="3" <?= ($rating ?? '') === '3' ? 'selected' : '' ?>>⭐⭐⭐ (3 sao)</option>
                        <option value="2" <?= ($rating ?? '') === '2' ? 'selected' : '' ?>>⭐⭐ (2 sao)</option>
                        <option value="1" <?= ($rating ?? '') === '1' ? 'selected' : '' ?>>⭐ (1 sao)</option>
                    </select>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4" id="fromDateDiv" style="display: <?= ($dateFilter ?? '') === 'custom' ? 'block' : 'none' ?>;">
                    <input type="date" name="fromDate" id="fromDateInput" class="form-control form-control-dark" 
                           placeholder="Từ ngày" value="<?= htmlspecialchars($fromDate ?? '') ?>">
                </div>
                
                <div class="col-md-4" id="toDateDiv" style="display: <?= ($dateFilter ?? '') === 'custom' ? 'block' : 'none' ?>;">
                    <input type="date" name="toDate" id="toDateInput" class="form-control form-control-dark" 
                           placeholder="Đến ngày" value="<?= htmlspecialchars($toDate ?? '') ?>">
                </div>
                
                <div class="col-md-4">
                    <select name="dateFilter" id="dateFilterSelect" class="form-select form-control-dark">
                        <option value="">-- Tất cả thời gian --</option>
                        <option value="today" <?= ($dateFilter ?? '') === 'today' ? 'selected' : '' ?>>📅 Hôm nay</option>
                        <option value="7days" <?= ($dateFilter ?? '') === '7days' ? 'selected' : '' ?>>📅 7 ngày qua</option>
                        <option value="30days" <?= ($dateFilter ?? '') === '30days' ? 'selected' : '' ?>>📅 30 ngày qua</option>
                        <option value="custom" <?= ($dateFilter ?? '') === 'custom' ? 'selected' : '' ?>>📅 Tùy chỉnh khoảng thời gian</option>
                    </select>
                </div>
            </div>
            
            <script>
            // Gắn sự kiện sau khi DOM load xong
            document.addEventListener('DOMContentLoaded', function() {
                const dateFilterSelect = document.getElementById('dateFilterSelect');
                const fromDiv = document.getElementById('fromDateDiv');
                const toDiv = document.getElementById('toDateDiv');
                const filterForm = document.getElementById('filterForm');
                const fromDateInput = document.getElementById('fromDateInput');
                const toDateInput = document.getElementById('toDateInput');
                
                // Xử lý thay đổi bộ lọc ngày
                dateFilterSelect.addEventListener('change', function() {
                    const value = this.value;
                    
                    if (value === 'custom') {
                        fromDiv.style.display = 'block';
                        toDiv.style.display = 'block';
                        // Không submit ngay, đợi người dùng chọn ngày
                    } else {
                        fromDiv.style.display = 'none';
                        toDiv.style.display = 'none';
                        // Submit form ngay lập tức
                        filterForm.submit();
                    }
                });
                
                // Tự động submit khi chọn xong ngày trong chế độ custom
                if (fromDateInput && toDateInput) {
                    fromDateInput.addEventListener('change', function() {
                        // Nếu đã chọn cả 2 ngày, tự động submit
                        if (fromDateInput.value && toDateInput.value) {
                            filterForm.submit();
                        }
                    });
                    
                    toDateInput.addEventListener('change', function() {
                        // Nếu đã chọn cả 2 ngày, tự động submit
                        if (fromDateInput.value && toDateInput.value) {
                            filterForm.submit();
                        }
                    });
                }
            });
            </script>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary fw-bold px-4 flex-grow-1">
                    <i class="fa-solid fa-filter"></i> Lọc kết quả
                </button>
                
                <?php if(!empty($keyword) || !empty($status) || !empty($rating) || !empty($dateFilter)): ?>
                    <a href="index.php?controller=quanly&action=phanHoi" class="btn btn-danger fw-bold px-4">
                        <i class="fa-solid fa-xmark"></i> Xóa bộ lọc
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="text small mt-2 fst-italic" style="color: white;">
                * Hỗ trợ tìm theo: Mã phản hồi (VD: 1), Mã khách (VD: 5), Họ tên, Email hoặc Số điện thoại.
            </div>
        </form>

            
            <div class="action-bar d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">

                
                <div class="fw-bold text-info" style="font-size: 16px;">
                    <i class="fa-solid fa-list-check me-2"></i>Danh sách phản hồi
                </div>
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
                            <th>MaPH</th>
                            <th>MaKH</th>
                            <th>Họ tên & Đánh giá</th>
                            <th>Email & SĐT</th>
                            <th>Thời gian</th>
                            <th style="width: 300px;">Nội dung</th>
                            <th>Tình trạng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dsPhanHoi as $ph): ?>
                            <?php 
                                $cls = 'st-chua'; $txt = 'Chưa xử lý'; $icon = 'fa-circle-xmark';
                                if ($ph['TinhTrang'] == 'DangXuLy') { $cls = 'st-dang'; $txt = 'Đang xử lý'; $icon = 'fa-spinner'; }
                                if ($ph['TinhTrang'] == 'DaXuLy')   { $cls = 'st-da';   $txt = 'Đã xử lý'; $icon = 'fa-circle-check'; }
                                
                                // Rút gọn nội dung nếu quá dài
                                $noiDung = $ph['NoiDung'] ?? '';
                                $noiDungShort = mb_strlen($noiDung) > 80 ? mb_substr($noiDung, 0, 80) . '...' : $noiDung;
                                
                                // Lấy số sao
                                $stars = (int)($ph['MucDoHaiLong'] ?? 0);
                                
                                // Format thời gian phản hồi
                                $ngayPH = $ph['NgayPhanHoi'] ?? '';
                                $timeDisplay = '';
                                if ($ngayPH) {
                                    $timestamp = strtotime($ngayPH);
                                    $timeDisplay = date('d/m/Y H:i', $timestamp);
                                }
                            ?>
                            <tr style="cursor:pointer" onclick="location.href='index.php?controller=quanly&action=chiTietPhanHoi&id=<?= $ph['MaPH'] ?>'">
                                <td>PH<?= str_pad($ph['MaPH'], 3, '0', STR_PAD_LEFT) ?></td>
                                <td><?= $ph['MaKhachHang'] ? 'KH'.$ph['MaKhachHang'] : '---' ?></td>
                                <td>
                                    <div><?= htmlspecialchars(($ph['HoTenKH'] ?: $ph['TenKH_Goc']) ?? '') ?></div>
                                    <div class="mt-1">
                                        <?php 
                                            for($i=1; $i<=5; $i++) {
                                                if($i <= $stars) echo '<i class="fa-solid fa-star text-warning" style="font-size: 12px;"></i>';
                                                else echo '<i class="fa-regular fa-star text-secondary" style="font-size: 12px;"></i>';
                                            }
                                        ?>
                                        <span class="small text-muted ms-1">(<?= $stars ?>/5)</span>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 12px;">
                                        <i class="fa-solid fa-envelope me-1 text-info"></i><?= htmlspecialchars($ph['Email'] ?? '---') ?>
                                    </div>
                                    <div style="font-size: 12px;" class="mt-1">
                                        <i class="fa-solid fa-phone me-1 text-success"></i><?= htmlspecialchars($ph['SDT'] ?? '---') ?>
                                    </div>
                                </td>
                                <td>
                                    <small style="font-size: 12px;">
                                        <i class="fa-regular fa-clock me-1"></i><?= $timeDisplay ?: '---' ?>
                                    </small>
                                </td>
                                <td style="max-width: 300px; white-space: normal; font-size: 13px;">
                                    <?= htmlspecialchars($noiDungShort) ?>
                                </td>
                                <td><span class="<?= $cls ?>"><i class="fa-solid <?= $icon ?>"></i><?= $txt ?></span></td>
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

            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] === 'success'): ?>
                    <div class="alert mb-4 text-center" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.2)); border: 2px solid #10b981; border-radius: 15px; padding: 30px;">
                        <div class="mb-3">
                            <i class="fa-solid fa-circle-check fa-3x" style="color: #10b981;"></i>
                                    <div id="charCountInfo" class="small text-white mt-1 fw-bold float-end">0/500 ký tự</div>
                        <h4 class="text-white fw-bold mb-2">
                            <i class="fa-solid fa-check-double me-2"></i>Gửi phản hồi thành công!
                        </h4>
                        <p class="text-white-50 mb-3" style="font-size: 15px;">
                            <i class="fa-solid fa-envelope me-2"></i>Email thông báo đã được gửi đến khách hàng.
                        </p>
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-outline-light px-4" style="border-radius: 8px; font-weight: 600;" onclick="window.location.href='index.php?controller=quanly&action=chiTietPhanHoi&id=<?= $info['MaPH'] ?>'">
                                <i class="fa-solid fa-rotate-left me-2"></i>Gửi phản hồi khác
                            </button>
                            <a href="index.php?controller=quanly&action=phanHoi" class="btn px-4" style="border-radius: 8px; font-weight: 600; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none;">
                                <i class="fa-solid fa-arrow-left me-2"></i>Quay về danh sách
                            </a>
                        </div>
                    </div>
                <?php elseif ($_GET['msg'] === 'error'): ?>
                    <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #fca5a5;">
                        <i class="fa-solid fa-circle-xmark me-2"></i>
                        <strong>Lỗi!</strong> Không thể lưu phản hồi. Vui lòng thử lại.
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if (isset($_GET['error']) && $_GET['error'] === 'toolong'): ?>
                <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.18); border: 1px solid #ef4444; color: #fecaca; margin-bottom:16px;">
                    <i class="fa-solid fa-circle-xmark me-2"></i>
                    <strong>Nội dung quá dài!</strong> Nội dung trả lời vượt quá 500 ký tự. Gửi không thành công.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'empty'): ?>
                <div class="alert alert-warning" style="background: rgba(251, 191, 36, 0.2); border: 1px solid #fbbf24; color: #fde68a;">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <strong>Cảnh báo!</strong> Vui lòng nhập nội dung trả lời.
                </div>
            <?php endif; ?>

            <form action="index.php?controller=quanly&action=luuPhanHoi" method="POST" id="formPhanHoi">
                
                <input type="hidden" name="MaPH" value="<?= $info['MaPH'] ?>">

                <div class="detail-box" style="border-color: #38bdf8;">
                    <div class="d-flex justify-content-between mb-2">
                        <strong class="text-success">Trả lời khách hàng (Mới):</strong>
                        <small>Ngày trả lời: <?= date('d/m/Y') ?></small>
                    </div>
                    
                    <?php $showSaveError = isset($_GET['msg']) && $_GET['msg'] === 'error'; ?>
                    <div id="errorMessage" class="alert <?= $showSaveError ? 'alert-danger' : 'alert-warning' ?> mb-3" style="background: <?= $showSaveError ? 'rgba(239, 68, 68, 0.2)' : 'rgba(251, 191, 36, 0.2)' ?>; border: 1px solid <?= $showSaveError ? '#ef4444' : '#fbbf24' ?>; color: <?= $showSaveError ? '#fca5a5' : '#fde68a' ?>; display: <?= $showSaveError ? 'block' : 'none' ?>;">
                        <i class="fa-solid <?= $showSaveError ? 'fa-circle-xmark' : 'fa-triangle-exclamation' ?> me-2"></i>
                        <strong><?= $showSaveError ? 'Lỗi!' : 'Cảnh báo!' ?></strong>
                        <span id="errorText"><?= $showSaveError ? 'Không thể lưu phản hồi. Vui lòng thử lại.' : '' ?></span>
                    </div>
                    
                    <textarea name="NoiDungTraLoi" id="noiDungTraLoi" class="form-control form-control-dark mb-3" rows="4" maxlength="500" required placeholder="Nhập nội dung trả lời tại đây..."></textarea>

                   <div id="charCountInfo" class="small text-white mt-1 fw-bold float-end" style="color: #ffffff !important;">0/500 ký tự</div>

                    <div class="mb-3" id="statusGroup">
                        <label class="fw-bold me-3">Trạng thái xử lý: <span class="text-danger">*</span></label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="TinhTrang" value="DangXuLy" id="r1" <?= $info['TinhTrang']=='DangXuLy'?'checked':'' ?>>
                            <label class="form-check-label text-warning" for="r1">Đang xử lý</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="TinhTrang" value="DaXuLy" id="r2" <?= $info['TinhTrang']=='DaXuLy'?'checked':'' ?>>
                            <label class="form-check-label text-success" for="r2">Đã xử lý</label>
                        </div>
                    </div>
                    
                    <div id="errorMessageBottom" class="alert alert-danger mb-3" style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #fca5a5; display: none;">
                        <i class="fa-solid fa-circle-xmark me-2"></i>
                        <strong>Lỗi!</strong> <span id="errorTextBottom"></span>
                    </div>

                    <div class="text-center">
                        <button type="button" class="btn btn-success px-4 py-2 fw-bold me-2" onclick="showConfirmModal()">Gửi & Lưu</button>
                        <a href="index.php?controller=quanly&action=phanHoi" class="btn btn-outline-secondary px-4 py-2">Hủy</a>
                    </div>
                </div>
            </form>

        <?php endif; ?>
        </div>
</div>

<!-- Modal Xác nhận Gửi Phản hồi -->
<div class="modal fade" id="confirmModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.95), rgba(5, 150, 105, 0.95)); border: 2px solid rgba(52, 211, 153, 0.6); border-radius: 15px; box-shadow: 0 20px 60px rgba(16, 185, 129, 0.4);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fa-solid fa-paper-plane fa-bounce me-2" style="color: #d1fae5;"></i>
                    Xác nhận gửi phản hồi
                </h5>
            </div>
            <div class="modal-body text-white pt-2">
                <p class="mb-2" style="font-size: 15px; line-height: 1.6;">
                    <i class="fa-solid fa-check-circle me-2"></i>
                    Bạn có chắc chắn muốn gửi phản hồi này cho khách hàng?
                </p>
                <p class="mb-0 fst-italic" style="font-size: 14px; opacity: 0.9;">
                    <i class="fa-solid fa-envelope me-2"></i>
                    Email thông báo sẽ được tự động gửi đến khách hàng.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600;">
                    <i class="fa-solid fa-xmark me-2"></i>Hủy bỏ
                </button>
                <button type="button" class="btn px-4" onclick="submitForm()" style="border-radius: 8px; font-weight: 600; background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; border: none;">
                    <i class="fa-solid fa-check me-2"></i>Xác nhận gửi
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const MAX_CHARS = 500;

    // Count chars where Enter/newline counts as 2 characters
    function countChars(text) {
        if (!text) return 0;
        const base = text.length;
        const newlines = (text.match(/\n/g) || []).length;
        return base + newlines; // each newline counts as an extra char -> Enter = 2
    }

    // Truncate text to logical max where newline counts as 2 chars
    function truncateToChars(text, max) {
        if (!text) return '';
        let out = '';
        let len = 0;
        for (let i = 0; i < text.length; i++) {
            const ch = text[i];
            const add = ch === '\n' ? 2 : 1;
            if (len + add > max) break;
            out += ch;
            len += add;
        }
        return out;
    }

    window.showConfirmModal = function() {
        const noiDung = document.getElementById('noiDungTraLoi');
        const errorDiv = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');
        const errorDivBottom = document.getElementById('errorMessageBottom');
        const errorTextBottom = document.getElementById('errorTextBottom');
        const tinhTrangRadios = document.getElementsByName('TinhTrang');
        const charCountInfo = document.getElementById('charCountInfo');

        if (!noiDung) return;

        // Ẩn tất cả thông báo lỗi trước
        errorDiv.style.display = 'none';
        errorDivBottom.style.display = 'none';

        let hasError = false;
        let errors = [];

        // Kiểm tra nội dung rỗng hoặc toàn khoảng trắng
        if (noiDung.value.trim() === '') {
            errors.push('Vui lòng nhập nội dung trả lời.');
            hasError = true;
        }

        // Kiểm tra số ký tự
        const len = countChars(noiDung.value);
        if (len > MAX_CHARS) {
            errors.push('Nội dung không được quá ' + MAX_CHARS + ' ký tự. Hiện có ' + len + ' ký tự.');
            hasError = true;
        }

        // Kiểm tra xem đã chọn trạng thái chưa
        let tinhTrangChecked = false;
        for (let i = 0; i < tinhTrangRadios.length; i++) {
            if (tinhTrangRadios[i].checked) {
                tinhTrangChecked = true;
                break;
            }
        }

        if (!tinhTrangChecked) {
            errors.push('Vui lòng chọn trạng thái xử lý.');
            hasError = true;
        }

        // Nếu có lỗi, hiển thị cảnh báo ở trên và lỗi chi tiết ở dưới
        if (hasError) {
            // Hiển thị cảnh báo tổng quát ở trên
            errorText.textContent = 'Vui lòng kiểm tra và điền đầy đủ thông tin bắt buộc.';
            errorDiv.style.display = 'block';
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Nếu lỗi do quá ký tự thì chỉ hiện thông báo nhỏ dưới textarea (charLimitError),
            // không hiện khung lỗi đỏ lớn ở dưới (errorMessageBottom).
            const isCharLimitError = errors.some(e => e.indexOf('không được quá ' + MAX_CHARS) !== -1);
            if (!isCharLimitError) {
                // Hiển thị lỗi chi tiết ở dưới cho các lỗi khác
                errorTextBottom.innerHTML = errors.map(err => '• ' + err).join('<br>');
                errorDivBottom.style.display = 'block';
            }

            // Update counter if present
            if (charCountInfo) charCountInfo.textContent = len + '/' + MAX_CHARS + ' ký tự';

            return;
        }

        // Hiển thị modal xác nhận
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        confirmModal.show();
    };

    window.submitForm = function() {
        document.getElementById('formPhanHoi').submit();
    };

    // Word-count UI and enforcement for the reply textarea
    const noiDungInput = document.getElementById('noiDungTraLoi');
    const charCountInfo = document.getElementById('charCountInfo');
    const charLimitError = document.getElementById('charLimitError');
    if (noiDungInput && charCountInfo) {
        function updateCharCountAndEnforce() {
            const len = countChars(noiDungInput.value);
            if (len >= MAX_CHARS) {
                const truncated = truncateToChars(noiDungInput.value, MAX_CHARS);
                noiDungInput.value = truncated;
                charCountInfo.textContent = MAX_CHARS + '/' + MAX_CHARS + ' ký tự';
                if (charLimitError) charLimitError.style.display = 'block';

                // style counter red
                charCountInfo.classList.remove('text-white');
                charCountInfo.classList.add('text-danger');

                // show existing error blocks too
                const errorDiv = document.getElementById('errorMessage');
                const errorText = document.getElementById('errorText');
                const errorDivBottom = document.getElementById('errorMessageBottom');
                const errorTextBottom = document.getElementById('errorTextBottom');
                if (errorText) errorText.textContent = 'Nội dung không được quá ' + MAX_CHARS + ' ký tự.';
                if (errorDiv) errorDiv.style.display = 'block';
                // Do not show the big red bottom error box for char-limit; use the small inline message instead
                if (errorTextBottom) errorTextBottom.innerHTML = '';
                if (errorDivBottom) errorDivBottom.style.display = 'none';
            } else {
                charCountInfo.textContent = len + '/' + MAX_CHARS + ' ký tự';
                if (charLimitError) charLimitError.style.display = 'none';
                const err1 = document.getElementById('errorMessage'); if (err1) err1.style.display = 'none';
                const err2 = document.getElementById('errorMessageBottom'); if (err2) err2.style.display = 'none';

                // restore muted style
                charCountInfo.classList.remove('text-danger');
                charCountInfo.classList.add('text-muted');
            }
        }

        // On input, update counter and enforce limit
        noiDungInput.addEventListener('input', function(e) {
            // maxlength attribute already prevents typing extra, but ensure trimming if needed
            updateCharCountAndEnforce();
        });

        // On paste, insert truncated text so logical total <= MAX_CHARS (Enter counts as 2)
        noiDungInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const current = noiDungInput.value || '';
            const start = noiDungInput.selectionStart || current.length;
            const end = noiDungInput.selectionEnd || current.length;
            const newVal = current.substring(0, start) + paste + current.substring(end);
            noiDungInput.value = truncateToChars(newVal, MAX_CHARS);
            updateCharCountAndEnforce();
        });

        // Initialize counter on load
        updateCharCountAndEnforce();
    }

    // Ẩn thông báo lỗi khi chọn trạng thái
    const tinhTrangRadios = document.getElementsByName('TinhTrang');
    for (let i = 0; i < tinhTrangRadios.length; i++) {
        tinhTrangRadios[i].addEventListener('change', function() {
            document.getElementById('errorMessage').style.display = 'none';
            document.getElementById('errorMessageBottom').style.display = 'none';
        });
    }
});
</script>


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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
