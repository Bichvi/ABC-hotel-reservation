<?php
$user = Auth::user();
$mode = $mode ?? "list";
$errors  = $errors  ?? [];
$success = $success ?? "";
$customers = $customers ?? [];
$kh = $kh ?? [];
$data = $data ?? [];
$cccd = $cccd ?? "";
$duplicates = $duplicates ?? [];
$username = $username ?? "";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý hồ sơ khách hàng - ABC Resort</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
    body {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        min-height: 100vh;
        color: #e5e7eb;
    }
    .navbar {
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(12px);
    }
    .wrapper {
        background: rgba(15,23,42,0.86);
        border-radius: 20px;
        padding: 28px;
        border: 1px solid rgba(148,163,184,0.35);
        box-shadow: 0 20px 40px rgba(0,0,0,0.55);
    }
    .form-control {
        background: rgba(15,23,42,0.78);
        border: 1px solid rgba(148,163,184,.35);
        color: #ffffff !important;
    }
    .form-control:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 2px rgba(56,189,248,.4);
        background: rgba(15,23,42,1);
        color: #ffffff !important;
    }
    .form-control::placeholder {
        color: #94a3b8;
        opacity: 0.7;
    }
    .form-select {
        color: #070707ff !important;
    }
    .form-select option {
        background: #babdc1ff;
        color: #ffffff;
    }
    .page-title {
        font-size: 26px;
        font-weight: 700;
        color: #f8fafc;
        margin-bottom: 15px;
    }
    .btn-save {
        background: linear-gradient(135deg, #0ea5e9, #22c55e);
        border: none;
        color: white !important;
        font-weight: 600;
        padding: 10px 24px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
    }
    .btn-cancel {
        border: 1px solid #94a3b8;
        border-radius: 12px;
        color: #e2e8f0;
        padding: 10px 24px;
    }
    .btn-secondary {
        border-radius: 12px;
        padding: 10px 24px;
        background: #475569;
        color: white;
    }
    .error-box {
        background: rgba(220,38,38,0.25);
        border-left: 4px solid #ef4444;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 15px;
    }

.table-compact td, 
.table-compact th {
    padding: 6px 8px !important;
    font-size: 0.78rem;
    white-space: nowrap;
}

.table-compact {
    width: 100%;
    table-layout: fixed;
}

.table-compact th {
    text-transform: uppercase;
    font-weight: 600;
}

.col-ma { width: 60px; }
.col-ten { width: 140px; }
.col-loai { width: 80px; }
.col-lienhe { width: 140px; }
.col-diachi { width: 160px; }
.col-cccd { width: 120px; }
.col-tk { width: 120px; }
.col-action { width: 80px; }





    .field-error { font-size: 0.75rem; color: #fca5a5; margin-top: 4px; }
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

<style>
    .home-icon { border-radius: 6px; background: rgba(15,23,42,0.15); padding: 4px; }
    .home-icon:hover { background: rgba(56,189,248,0.08); cursor: pointer; }
</style>

<div class="container">
<div class="wrapper mx-auto" style="max-width: 900px;">


<!-- ====================================================================== -->
<!-- 🔥 MODE 1: DANH SÁCH -->
<!-- ====================================================================== -->
<?php if ($mode === "list"): ?>

    <div class="mb-3">
        <a href="index.php?controller=dashboard&action=quanly" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Quay lại dashboard
        </a>
    </div>

    <div class="page-title">
        <i class="fa-solid fa-users me-2 text-info"></i>Danh sách khách hàng
    </div>

    <!-- BỘ LỌC VÀ TÌM KIẾM -->
    <form class="mb-3 p-3" style="background: rgba(255,255,255,0.05); border: 1px solid #475569; border-radius: 12px;" method="get" action="index.php" id="filterFormKH">
        <input type="hidden" name="controller" value="quanly">
        <input type="hidden" name="action" value="danhsachKhachHang">
        
        <div class="row g-3 align-items-end">
            <!-- Tìm kiếm -->
            <div class="col-md-3">
                <label class="form-label fw-bold text-light">
                    <i class="fa-solid fa-magnifying-glass me-2"></i>Tìm kiếm
                </label>
                <input type="text" name="keyword" id="keywordInputKH" class="form-control" 
                    placeholder="Nhập Mã KH, Tên, SĐT, Email hoặc CCCD..."
                    value="<?= htmlspecialchars($keyword ?? '') ?>">
            </div>

            <!-- Tài khoản (username) -->
            <div class="col-md-3">
                <label class="form-label fw-bold text-light">
                    <i class="fa-solid fa-user-circle me-2"></i>Tài khoản (Username)
                </label>
                <input type="text" name="username" id="usernameFilterKH" class="form-control"
                    placeholder="Nhập username..."
                    value="<?= htmlspecialchars($username ?? '') ?>">
            </div>
            
            
            
            <!-- Nút tìm kiếm -->
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="fa-solid fa-filter me-2"></i>Lọc
                </button>
            </div>
        </div>
        
        <?php if (!empty($keyword) || !empty($loaiKhach) || !empty($coTaiKhoan)): ?>
        <div class="mt-3">
            <a href="index.php?controller=quanly&action=danhsachKhachHang" 
               class="btn btn-danger btn-sm">
               <i class="fa-solid fa-xmark me-1"></i>Xóa bộ lọc
            </a>
        </div>
        <?php endif; ?>
    </form>

    <script>
    // Auto submit form khi thay đổi dropdown
    document.getElementById('loaiKhachSelect').addEventListener('change', function() {
        document.getElementById('filterFormKH').submit();
    });

    document.getElementById('coTaiKhoanSelect').addEventListener('change', function() {
        document.getElementById('filterFormKH').submit();
    });

    // Real-time search - vừa gõ vừa hiện
    let searchTimeout;
    const keywordInput = document.getElementById('keywordInputKH');
    
    // Auto focus lại vào ô input sau khi trang load (nếu có keyword)
    const currentKeyword = keywordInput.value;
    if (currentKeyword) {
        keywordInput.focus();
        // Đặt con trỏ về cuối text
        keywordInput.setSelectionRange(currentKeyword.length, currentKeyword.length);
    }
    
    keywordInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            document.getElementById('filterFormKH').submit();
        }, 800); // 800ms - đủ thời gian gõ nhiều từ có dấu cách
    });

    // Submit khi nhấn Enter
    keywordInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            document.getElementById('filterFormKH').submit();
        }
    });
    </script>

    <div class="text-end mb-3">
        <a href="index.php?controller=quanly&action=themKhachHang" class="btn btn-save">
            <i class="fa-solid fa-user-plus me-1"></i> Thêm khách hàng
        </a>
    </div>

    <!-- Wrapper cho table responsive -->
<div class="table-responsive">
<table class="table table-dark table-striped table-compact">
    
        <thead>
            <tr>
            <th class="col-ma">Mã KH</th>
            <th class="col-ten">Họ tên</th>
            <th class="col-lienhe">Liên hệ</th>
            <th class="col-cccd">CCCD</th>
            <th class="col-tk text-center">Tài khoản</th>
            <th class="col-action text-end">Thao tác</th>
        </tr>
        </thead>

        <tbody>
        <?php if (empty($customers)): ?>
            <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fa-solid fa-inbox fa-3x mb-3 opacity-50"></i>
                        <span class="fs-5 text-white" " >Không có khách hàng nào.</span>
                    </div>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($customers as $c): ?>

                <?php 
                    // Badge
                    $badgeColor = "secondary";
                    $icon = "user";
                    if ($c["LoaiKhach"] === "Trưởng đoàn") { $badgeColor="warning text-dark"; $icon="crown"; }
                    if ($c["LoaiKhach"] === "Thành viên")   { $badgeColor="info text-dark";    $icon="users"; }
                ?>

                <tr>
                    <td class="ps-3 fw-bold text-info">#<?= $c["MaKhachHang"] ?></td>

                    <td>
                        <div class="fw-semibold"><?= htmlspecialchars($c["TenKH"] ?? '') ?></div>
                        <div class="mt-1">
                            <span class="badge bg-<?= $badgeColor ?> bg-opacity-75 px-2 py-1" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-<?= $icon ?> me-1"></i><?= htmlspecialchars($c["LoaiKhach"] ?? '') ?>
                            </span>
                        </div>
                    </td>

                    <td style="font-size: 0.85rem;">
                        <div>
                            <i class="fa-solid fa-phone text-success opacity-75 me-2"></i>
                            <?= htmlspecialchars($c["SDT"] ?? '') ?>
                        </div>
                        <div>
                            <i class="fa-solid fa-envelope text-primary opacity-75 me-2"></i>
                            <?= htmlspecialchars($c["Email"] ?? '') ?>
                        </div>
                    </td>

                    <td class="font-monospace"><?= htmlspecialchars($c["CCCD"] ?? '') ?></td>

                    <td class="text-center">
                        <?php if (!empty($c["Username"])): ?>
                            <span class="badge bg-dark px-3 py-2 border border-secondary">
                                <i class="fa-solid fa-user-circle me-1"></i><?= htmlspecialchars($c["Username"] ?? '') ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted small">
                                <i class="fa-regular fa-circle-xmark me-1"></i>Chưa có
                            </span>
                        <?php endif; ?>
                    </td>

                    <td class="text-end pe-3">
                        <div class="btn-group">
                            <a href="index.php?controller=quanly&action=sua&id=<?= $c['MaKhachHang'] ?>"
                               class="btn btn-sm btn-outline-info"
                               title="Sửa thông tin">
                               <i class="fa-solid fa-pen"></i>
                            </a>

                            <button type="button"
                               class="btn btn-sm btn-outline-danger"
                               onclick="showDeleteModal(<?= $c['MaKhachHang'] ?>, '<?= htmlspecialchars($c['TenKH'], ENT_QUOTES) ?>', '<?= htmlspecialchars($c['SDT'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($c['Email'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($c['CCCD'] ?? '', ENT_QUOTES) ?>')"
                               title="Xóa khách hàng">
                               <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>


    <!-- Thống kê -->
    <div class="mt-3 text-muted small text-end">
        <i class="fa-solid fa-users me-1"></i>
        Tổng số: <strong class="text-info"><?= count($customers) ?></strong> khách hàng
    </div>

    <!-- Modal Xác Nhận Xóa Khách Hàng -->
    <div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: linear-gradient(135deg, #1e293b, #334155); border: 1px solid #475569;">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title text-danger" id="deleteModalLabel">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>Xác nhận xóa khách hàng
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning" style="background: rgba(251, 191, 36, 0.15); border-left: 4px solid #fbbf24;">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        <strong>Thông tin khách hàng sẽ bị xóa:</strong>
                    </div>
                    
                    <div class="customer-info p-3" style="background: rgba(15, 23, 42, 0.5); border-radius: 10px; border: 1px solid #475569;">
                        <div class="mb-2">
                            <i class="fa-solid fa-user text-info me-2"></i>
                            <strong>Họ tên:</strong> <span id="delete-customer-name" class="text-light"></span>
                        </div>
                        <div class="mb-2">
                            <i class="fa-solid fa-phone text-success me-2"></i>
                            <strong>Số điện thoại:</strong> <span id="delete-customer-phone" class="text-light"></span>
                        </div>
                        <div class="mb-2">
                            <i class="fa-solid fa-envelope text-primary me-2"></i>
                            <strong>Email:</strong> <span id="delete-customer-email" class="text-light"></span>
                        </div>
                        <div class="mb-0">
                            <i class="fa-solid fa-id-card text-warning me-2"></i>
                            <strong>CCCD:</strong> <span id="delete-customer-cccd" class="text-light"></span>
                        </div>
                    </div>

                    <div class="alert alert-danger mt-3 mb-0" style="background: rgba(220, 38, 38, 0.15); border-left: 4px solid #ef4444;">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        <strong>Cảnh báo:</strong> Hành động này không thể hoàn tác!
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark me-1"></i>Hủy
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fa-solid fa-trash me-1"></i>Xác nhận xóa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    let deleteCustomerId = null;

    function showDeleteModal(id, name, phone, email, cccd) {
        console.log('showDeleteModal called with:', id, name); // Debug
        
        deleteCustomerId = id;
        
        try {
            document.getElementById('delete-customer-name').textContent = name || 'Chưa có';
            document.getElementById('delete-customer-phone').textContent = phone || 'Chưa có';
            document.getElementById('delete-customer-email').textContent = email || 'Chưa có';
            document.getElementById('delete-customer-cccd').textContent = cccd || 'Chưa có';
            
            // Kiểm tra Bootstrap có sẵn không
            if (typeof bootstrap === 'undefined') {
                console.error('Bootstrap chưa được load!');
                alert('Lỗi: Bootstrap chưa sẵn sàng. Vui lòng tải lại trang.');
                return;
            }
            
            const modalElement = document.getElementById('deleteCustomerModal');
            if (!modalElement) {
                console.error('Modal element không tồn tại!');
                return;
            }
            
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } catch (error) {
            console.error('Lỗi khi hiển thị modal:', error);
            // Fallback: Sử dụng confirm đơn giản nếu modal lỗi
            if (confirm('Bạn có chắc muốn xóa khách hàng "' + name + '" không?')) {
                window.location.href = 'index.php?controller=quanly&action=xoaKhachHang&id=' + id;
            }
        }
    }

    // Đợi DOM ready trước khi gắn event listener
    document.addEventListener('DOMContentLoaded', function() {
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                console.log('Confirm button clicked, ID:', deleteCustomerId); // Debug
                if (deleteCustomerId) {
                    window.location.href = 'index.php?controller=quanly&action=xoaKhachHang&id=' + deleteCustomerId;
                }
            });
        } else {
            console.error('Confirm button không tìm thấy!');
        }
    });
    </script>

<?php endif; ?>


<!-- ====================================================================== -->
<!-- 🔥 MODE 2: THÊM KHÁCH -->
<!-- ====================================================================== -->
<?php if ($mode === "add"): ?>

    <div class="page-title">
        <i class="fa-solid fa-user-plus text-info me-2"></i>Thêm hồ sơ khách hàng
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: linear-gradient(135deg, rgba(220,38,38,0.15), rgba(239,68,68,0.1)); border-left: 4px solid #ef4444; border-radius: 12px;">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation fa-2x text-danger me-3"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-2">
                        <i class="fa-solid fa-circle-xmark me-2"></i>Có lỗi xảy ra
                    </h5>
                    <ul class="mb-0" style="list-style: none; padding-left: 0;">
                        <?php foreach ($errors as $e): ?>
                            <li class="mb-1"><i class="fa-solid fa-chevron-right me-2 text-danger" style="font-size: 0.75rem;"></i><?= $e ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: linear-gradient(135deg, rgba(34,197,94,0.15), rgba(74,222,128,0.1)); border-left: 4px solid #22c55e; border-radius: 12px; animation: slideInDown 0.5s ease-out;">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="position: absolute; top: 15px; right: 15px;"></button>
            <div class="d-flex align-items-start mb-3">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-circle-check fa-3x text-success me-3" style="animation: bounceIn 0.6s;"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-0">
                        <i class="fa-solid fa-check-double me-2"></i><?= $success ?>
                    </h5>
                </div>
            </div>
            <hr style="border-color: rgba(34,197,94,0.3);">
            <div class="row mt-3">
                <div class="col-md-2">
                    <h6><i class="fa-solid fa-user me-2 text-success"></i>Thông tin khách hàng:</h6>
                    <ul class="list-unstyled ms-3">
                        <li class="mb-1"><i class="fa-solid fa-check-circle me-2 text-success" style="font-size: 0.8rem;"></i><strong>Họ tên:</strong> <?= htmlspecialchars($data['TenKH'] ?? '') ?></li>
                        <li class="mb-1"><i class="fa-solid fa-check-circle me-2 text-success" style="font-size: 0.8rem;"></i><strong>CCCD:</strong> <?= htmlspecialchars($data['CCCD'] ?? '') ?></li>
                        <li class="mb-1"><i class="fa-solid fa-check-circle me-2 text-success" style="font-size: 0.8rem;"></i><strong>Số điện thoại:</strong> <?= htmlspecialchars($data['SDT'] ?? '') ?></li>
                        <li class="mb-1"><i class="fa-solid fa-check-circle me-2 text-success" style="font-size: 0.8rem;"></i><strong>Email:</strong> <?= htmlspecialchars($data['Email'] ?? '') ?></li>
                        <li class="mb-1"><i class="fa-solid fa-check-circle me-2 text-success" style="font-size: 0.8rem;"></i><strong>Loại khách:</strong> <span class="badge bg-success"><?= htmlspecialchars($data['LoaiKhach'] ?? '') ?></span></li>
                    </ul>
                </div>
                <?php if (!empty($data['Username'])): ?>
                <div class="col-md-2">
                    <h6><i class="fa-solid fa-key me-2 text-primary"></i>Thông tin tài khoản:</h6>
                    <ul class="list-unstyled ms-3">
                        <li class="mb-2"><strong>Tên đăng nhập:</strong> <code class="text-white bg-dark px-2 py-1 rounded"><?= htmlspecialchars($data['Username']) ?></code></li>
                        <li class="mb-2"><strong>Mật khẩu:</strong> <code class="text-white bg-dark px-2 py-1 rounded"><?= htmlspecialchars($data['PlainPassword'] ?? '(đã mã hóa)') ?></code></li>
                    </ul>
                    <div class="alert alert-warning mt-2 mb-0" style="background: rgba(251,191,36,0.2); border: 1px solid rgba(251,191,36,0.4); border-radius: 8px;">
                        <small><i class="fa-solid fa-triangle-exclamation me-1"></i><strong>Lưu ý:</strong> Vui lòng lưu lại mật khẩu này, hệ thống không lưu trữ mật khẩu dạng văn bản!</small>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="mt-3 d-flex gap-2 flex-wrap">
                <a href="index.php?controller=quanly&action=danhsachKhachHang" class="btn btn-primary">
                    <i class="fa-solid fa-list me-2"></i>Về danh sách
                </a>
                <a href="index.php?controller=quanly&action=sua&mode=add" class="btn btn-success">
                    <i class="fa-solid fa-plus me-2"></i>Thêm khách hàng khác
                </a>
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="fa-solid fa-print me-2"></i>In thông tin
                </button>
            </div>
        </div>

        <style>
            @keyframes slideInDown {
                from { opacity: 0; transform: translateY(-20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            @keyframes bounceIn {
                0% { transform: scale(0); opacity: 0; }
                50% { transform: scale(1.1); }
                100% { transform: scale(1); opacity: 1; }
            }
        </style>
    <?php endif; ?>

    <form method="post" action="index.php?controller=quanly&action=luuThem">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Họ tên: <span class="text-danger">*</span></label>
                <input class="form-control" name="TenKH"
                    value="<?= htmlspecialchars($data['TenKH'] ?? "") ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>CCCD/CMND: <span class="text-danger">*</span></label>
                <input class="form-control" id="cccdInput" name="CCCD"
                    value="<?= htmlspecialchars($data['CCCD'] ?? "") ?>" required>
                <small class="text-danger d-none" id="cccdError"></small>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Số điện thoại: <span class="text-danger">*</span></label>
                <input class="form-control" id="sdtInput" name="SDT"
                    value="<?= htmlspecialchars($data['SDT'] ?? "") ?>" required>
                <small class="text-danger d-none" id="sdtError"></small>
            </div>

            <div class="col-md-6 mb-3">
                <label>Email: <span class="text-danger">*</span></label>
                <input class="form-control" type="email" id="emailInput" name="Email"
                    value="<?= htmlspecialchars($data['Email'] ?? "") ?>" required>
                <small class="text-danger d-none" id="emailError"></small>
            </div>
        </div>

        <div class="mb-3">
            <label>Địa chỉ:</label>
            <textarea id="diachiInput" class="form-control" name="DiaChi" rows="2" maxlength="150" placeholder="Nhập địa chỉ (nếu có)"><?= htmlspecialchars($data['DiaChi'] ?? "") ?></textarea>
            <div class="form-text">Tối đa <strong>150</strong> ký tự. <span id="diachiCount">0</span>/150</div>
            <small class="text-danger d-none mt-1" id="diachiError">Đã đạt giới hạn 150 ký tự — không thể nhập thêm.</small>
        </div>

        <div class="mb-3">
            <label>Loại khách: <span class="text-danger">*</span></label>
            <select class="form-select" name="LoaiKhach" required>
                <option value="">-- Chọn loại khách --</option>
                <option value="Cá nhân" <?= ($data['LoaiKhach'] ?? '') === 'Cá nhân' ? 'selected' : '' ?>>Cá nhân</option>
                <option value="Trưởng đoàn" <?= ($data['LoaiKhach'] ?? '') === 'Trưởng đoàn' ? 'selected' : '' ?>>Trưởng đoàn</option>
                <option value="Thành viên" <?= ($data['LoaiKhach'] ?? '') === 'Thành viên' ? 'selected' : '' ?>>Thành viên</option>
            </select>
        </div>

        <hr class="my-4" style="border-color: rgba(148,163,184,0.35);">

        <div class="mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="createAccount" name="CreateAccount" value="1">
                <label class="form-check-label fw-bold" for="createAccount">
                    <i class="fa-solid fa-user-plus me-2 text-success"></i>Tạo tài khoản đăng nhập cho khách hàng
                </label>
            </div>
            <small class="text-muted fst-italic">
                <i class="fa-solid fa-circle-info me-1"></i>Khách hàng sẽ nhận email chứa thông tin đăng nhập
            </small>
        </div>

        <div id="accountFields" style="display: none; background: rgba(56,189,248,0.1); padding: 20px; border-radius: 12px; border: 1px solid rgba(56,189,248,0.3);">
            <h6 class="text-info mb-3">
                <i class="fa-solid fa-key me-2"></i>Thông tin tài khoản
            </h6>
            
            <div class="mb-3">
                <label>Tên đăng nhập: <span class="text-danger">*</span></label>
                <input class="form-control" name="Username" id="usernameInput"
                    value="<?= htmlspecialchars($data['Username'] ?? "") ?>" 
                    placeholder="Ví dụ: nguyenvana, khach123...">
                <small class="text-muted d-block" id="usernameHint">
                    <i class="fa-solid fa-lightbulb me-1"></i>Phải từ 3-30 ký tự, chỉ chứa chữ, số, gạch dưới
                </small>
                <small class="text-danger d-none" id="usernameError">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i><span id="usernameErrorText"></span>
                </small>
            </div>

            <div class="mb-3">
                <label>Mật khẩu: <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input class="form-control" type="text" name="Password" id="passwordInput"
                        placeholder="Nhập mật khẩu hoặc nhấn Generate">
                    <button type="button" class="btn btn-warning" id="generatePasswordBtn">
                        <i class="fa-solid fa-wand-magic-sparkles me-1"></i>Generate
                    </button>
                </div>
                <small class="text-muted">
                    <i class="fa-solid fa-lightbulb me-1"></i>Có thể tự nhập hoặc dùng nút Generate để tạo mật khẩu ngẫu nhiên
                </small>
            </div>

            <div class="mb-3">
                <label>Xác nhận mật khẩu: <span class="text-danger">*</span></label>
                <input class="form-control" type="password" name="ConfirmPassword" id="confirmPasswordInput"
                    placeholder="Nhập lại mật khẩu">
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="index.php?controller=quanly&action=danhsachKhachHang"
               class="btn btn-cancel">
               <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-warning" id="resetFormBtn">
                    <i class="fa-solid fa-rotate-right me-1"></i>Reset Form
                </button>
                <button class="btn btn-save" type="submit">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Lưu hồ sơ
                </button>
            </div>
        </div>
    </form>

    <script>
    // Toggle account fields
    document.getElementById('createAccount').addEventListener('change', function() {
        const accountFields = document.getElementById('accountFields');
        const usernameInput = document.getElementById('usernameInput');
        const passwordInput = document.getElementById('passwordInput');
        const confirmPasswordInput = document.getElementById('confirmPasswordInput');
        
        if (this.checked) {
            accountFields.style.display = 'block';
            usernameInput.required = true;
            passwordInput.required = true;
            confirmPasswordInput.required = true;
        } else {
            accountFields.style.display = 'none';
            usernameInput.required = false;
            passwordInput.required = false;
            confirmPasswordInput.required = false;
        }
    });

    // Reset Form Button
    const resetFormBtn = document.getElementById('resetFormBtn');
    if (resetFormBtn) {
        resetFormBtn.addEventListener('click', function() {
            if (confirm('Bạn có chắc muốn xóa tất cả dữ liệu đã nhập?')) {
                // Reset tất cả input trong form
                document.querySelector('form').reset();
                
                // Ẩn thông báo thành công/lỗi nếu có
                const successAlert = document.querySelector('.alert-success');
                const errorBox = document.querySelector('.error-box');
                if (successAlert) successAlert.remove();
                if (errorBox) errorBox.remove();
                
                // Ẩn phần tài khoản
                const accountFields = document.getElementById('accountFields');
                const createAccountCheckbox = document.getElementById('createAccount');
                if (accountFields && createAccountCheckbox) {
                    accountFields.style.display = 'none';
                    createAccountCheckbox.checked = false;
                }
                
                // Reset validation states
                const allInputs = document.querySelectorAll('.form-control');
                allInputs.forEach(input => {
                    input.classList.remove('is-invalid', 'is-valid');
                });
                
                // Reset username validation messages
                const usernameInput = document.getElementById('usernameInput');
                const usernameHint = document.getElementById('usernameHint');
                const usernameError = document.getElementById('usernameError');
                if (usernameInput && usernameHint && usernameError) {
                    usernameInput.classList.remove('is-invalid', 'is-valid');
                    usernameHint.classList.remove('d-none');
                    usernameError.classList.add('d-none');
                }
            }
        });
    }

    // Realtime duplicate checks for CCCD / SDT / Email (EDIT mode)
    const cccdInputEdit = document.getElementById('cccdInputEdit');
    const cccdErrorEdit = document.getElementById('cccdErrorEdit');
    const sdtInputEdit = document.getElementById('sdtInputEdit');
    const sdtErrorEdit = document.getElementById('sdtErrorEdit');
    const emailInputEdit = document.getElementById('emailInputEdit');
    const emailErrorEdit = document.getElementById('emailErrorEdit');

    let cccdEditTimer = null, sdtEditTimer = null, emailEditTimer = null;

    if (cccdInputEdit) {
        cccdInputEdit.addEventListener('input', function() {
            clearTimeout(cccdEditTimer);
            const val = this.value.trim();
            if (val.length < 9) {
                cccdInputEdit.classList.remove('is-invalid');
                cccdErrorEdit.classList.add('d-none');
                return;
            }
            cccdEditTimer = setTimeout(function() {
                const exclude = <?= isset($kh['MaKhachHang']) ? (int)$kh['MaKhachHang'] : 0 ?>;
                fetch('index.php?controller=quanly&action=checkCustomerFieldAjax&field=cccd&value=' + encodeURIComponent(val) + '&excludeMaKhachHang=' + exclude)
                    .then(r => r.json())
                        .then(data => {
                            if (data.exists) {
                                cccdInputEdit.classList.add('is-invalid');
                                cccdErrorEdit.classList.remove('d-none');
                                const name = data.name ? escapeHtml(data.name) : '';
                                const link = data.ma ? (' <a href="index.php?controller=quanly&action=sua&id=' + encodeURIComponent(data.ma) + '" class="text-info">#' + encodeURIComponent(data.ma) + (name ? (' ' + name) : '') + '</a>') : '';
                                cccdErrorEdit.innerHTML = 'CCCD đã tồn tại' + link;
                            } else {
                                cccdInputEdit.classList.remove('is-invalid');
                                cccdErrorEdit.classList.add('d-none');
                            }
                        }).catch(err => console.error('CCCD edit check failed', err));
            }, 450);
        });
    }

    if (sdtInputEdit) {
        sdtInputEdit.addEventListener('input', function() {
            clearTimeout(sdtEditTimer);
            const val = this.value.trim();
            if (val.length < 10) {
                sdtInputEdit.classList.remove('is-invalid');
                sdtErrorEdit.classList.add('d-none');
                return;
            }
            sdtEditTimer = setTimeout(function() {
                const exclude = <?= isset($kh['MaKhachHang']) ? (int)$kh['MaKhachHang'] : 0 ?>;
                fetch('index.php?controller=quanly&action=checkCustomerFieldAjax&field=sdt&value=' + encodeURIComponent(val) + '&excludeMaKhachHang=' + exclude)
                    .then(r => r.json())
                    .then(data => {
                        if (data.exists) {
                            sdtInputEdit.classList.add('is-invalid');
                            sdtErrorEdit.classList.remove('d-none');
                            const name = data.name ? escapeHtml(data.name) : '';
                            const link = data.ma ? (' <a href="index.php?controller=quanly&action=sua&id=' + encodeURIComponent(data.ma) + '" class="text-info">#' + encodeURIComponent(data.ma) + (name ? (' ' + name) : '') + '</a>') : '';
                            sdtErrorEdit.innerHTML = 'Số điện thoại đã tồn tại' + link;
                        } else {
                            sdtInputEdit.classList.remove('is-invalid');
                            sdtErrorEdit.classList.add('d-none');
                        }
                    }).catch(err => console.error('SDT edit check failed', err));
            }, 450);
        });
    }

    if (emailInputEdit) {
        emailInputEdit.addEventListener('input', function() {
            clearTimeout(emailEditTimer);
            const val = this.value.trim();
            if (val.length < 5 || val.indexOf('@') === -1) {
                emailInputEdit.classList.remove('is-invalid');
                emailErrorEdit.classList.add('d-none');
                return;
            }
            emailEditTimer = setTimeout(function() {
                const exclude = <?= isset($kh['MaKhachHang']) ? (int)$kh['MaKhachHang'] : 0 ?>;
                fetch('index.php?controller=quanly&action=checkCustomerFieldAjax&field=email&value=' + encodeURIComponent(val) + '&excludeMaKhachHang=' + exclude)
                    .then(r => r.json())
                    .then(data => {
                        if (data.exists) {
                            emailInputEdit.classList.add('is-invalid');
                            emailErrorEdit.classList.remove('d-none');
                            const name = data.name ? escapeHtml(data.name) : '';
                            const link = data.ma ? (' <a href="index.php?controller=quanly&action=sua&id=' + encodeURIComponent(data.ma) + '" class="text-info">#' + encodeURIComponent(data.ma) + (name ? (' ' + name) : '') + '</a>') : '';
                            emailErrorEdit.innerHTML = 'Email đã tồn tại' + link;
                        } else {
                            emailInputEdit.classList.remove('is-invalid');
                            emailErrorEdit.classList.add('d-none');
                        }
                    }).catch(err => console.error('Email edit check failed', err));
            }, 450);
        });
    }

    // Validate Username realtime
    const usernameInput = document.getElementById('usernameInput');
    const usernameHint = document.getElementById('usernameHint');
    const usernameError = document.getElementById('usernameError');
    const usernameErrorText = document.getElementById('usernameErrorText');
    
    if (usernameInput) {
        usernameInput.addEventListener('input', function() {
            const value = this.value.trim();
            const usernamePattern = /^[a-zA-Z0-9_]{3,30}$/;
            
            if (value === '') {
                usernameInput.classList.remove('is-invalid');
                usernameHint.classList.remove('d-none');
                usernameError.classList.add('d-none');
            } else if (value.length < 3) {
                usernameInput.classList.add('is-invalid');
                usernameHint.classList.add('d-none');
                usernameError.classList.remove('d-none');
                usernameErrorText.textContent = 'Tên đăng nhập phải có ít nhất 3 ký tự';
            } else if (!usernamePattern.test(value)) {
                usernameInput.classList.add('is-invalid');
                usernameHint.classList.add('d-none');
                usernameError.classList.remove('d-none');
                usernameErrorText.textContent = 'Chỉ được chứa chữ cái, số và gạch dưới (_)';
            } else {
                usernameInput.classList.remove('is-invalid');
                usernameInput.classList.add('is-valid');
                usernameHint.classList.remove('d-none');
                usernameError.classList.add('d-none');
            }
        });

        // Debounced AJAX check for duplicate username (add mode)
        let usernameAddTimer = null;
        usernameInput.addEventListener('input', function() {
            clearTimeout(usernameAddTimer);
            const val = this.value.trim();
            if (val.length < 3) return;
            usernameAddTimer = setTimeout(function() {
                fetch('index.php?controller=quanly&action=checkUsernameAjax&username=' + encodeURIComponent(val))
                    .then(r => r.json())
                    .then(data => {
                        if (data.exists) {
                            usernameInput.classList.add('is-invalid');
                            usernameInput.classList.remove('is-valid');
                            usernameHint.classList.add('d-none');
                            usernameError.classList.remove('d-none');
                            usernameErrorText.textContent = 'Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.';
                        } else {
                            if (usernamePattern.test(val)) {
                                usernameInput.classList.remove('is-invalid');
                                usernameInput.classList.add('is-valid');
                                usernameHint.classList.remove('d-none');
                                usernameError.classList.add('d-none');
                            }
                        }
                        toggleSubmitButton();
                    }).catch(err => console.error('Username check failed', err));
            }, 450);
        });
    }

    // Generate Password cho form ADD
    const generatePasswordBtn = document.getElementById('generatePasswordBtn');
    const passwordInput = document.getElementById('passwordInput');
    const confirmPasswordInput = document.getElementById('confirmPasswordInput');
    
    if (generatePasswordBtn && passwordInput && confirmPasswordInput) {
        generatePasswordBtn.addEventListener('click', function() {
            const length = 12;
            const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
            let password = '';
            for (let i = 0; i < length; i++) {
                password += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            passwordInput.value = password;
            confirmPasswordInput.value = password;
        });
    }
    </script>

<?php endif; ?>


<!-- ====================================================================== -->
<!-- 🔥 MODE 3: CHỈNH SỬA -->
<!-- ====================================================================== -->
<?php if ($mode === "edit"): ?>

    <div class="page-title">
        <i class="fa-solid fa-user-pen text-info me-2"></i>Cập nhật hồ sơ khách hàng
    </div>

    <?php if (!empty($errors)): ?>
        <div class="error-box"><ul>
            <?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?>
        </ul></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <h5 class="alert-heading">
                <i class="fa-solid fa-circle-check me-2"></i><?= $success ?>
            </h5>
            <hr>
            <div class="row mt-3">
                <div class="col-md-6">
                    <h6><i class="fa-solid fa-user me-2"></i>Thông tin khách hàng:</h6>
                    <ul class="list-unstyled ms-3">
                        <li><strong>Mã KH:</strong> <?= htmlspecialchars($kh['MaKhachHang'] ?? '') ?></li>
                        <li><strong>Họ tên:</strong> <?= htmlspecialchars($kh['TenKH'] ?? '') ?></li>
                        <li><strong>CCCD:</strong> <?= htmlspecialchars($kh['CCCD'] ?? '') ?></li>
                        <li><strong>Số điện thoại:</strong> <?= htmlspecialchars($kh['SDT'] ?? '') ?></li>
                        <li><strong>Email:</strong> <?= htmlspecialchars($kh['Email'] ?? '') ?></li>
                        <li><strong>Loại khách:</strong> <span class="badge bg-info"><?= htmlspecialchars($kh['LoaiKhach'] ?? '') ?></span></li>
                    </ul>
                </div>
                <?php if (!empty($kh['Username'])): ?>
                <div class="col-md-6">
                    <h6><i class="fa-solid fa-key me-2"></i>Thông tin tài khoản:</h6>
                    <ul class="list-unstyled ms-3">
                        <li><strong>Tên đăng nhập:</strong> <code class="text-white bg-dark px-2 py-1 rounded"><?= htmlspecialchars($kh['Username']) ?></code></li>
                        <?php if (!empty($kh['PlainPassword'])): ?>
                        <li><strong>Mật khẩu mới:</strong> <code class="text-white bg-dark px-2 py-1 rounded"><?= htmlspecialchars($kh['PlainPassword']) ?></code></li>
                        <div class="alert alert-warning mt-2" style="background: rgba(251,191,36,0.2); border-color: rgba(251,191,36,0.4);">
                            <small><i class="fa-solid fa-triangle-exclamation me-1"></i>Vui lòng lưu lại mật khẩu mới này!</small>
                        </div>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
            <div class="mt-3">
                <a href="index.php?controller=quanly&action=danhsachKhachHang" class="btn btn-primary">
                    <i class="fa-solid fa-list me-2"></i>Về danh sách
                </a>
                <a href="index.php?controller=quanly&action=sua&mode=add" class="btn btn-success">
                    <i class="fa-solid fa-plus me-2"></i>Thêm khách hàng khác
                </a>
            </div>
        </div>
    <?php endif; ?>

    <form method="post" action="index.php?controller=quanly&action=luuCapNhat">

        <div class="mb-3">
            <label>Mã KH:</label>
            <input class="form-control" readonly name="MaKhachHang"
                   value="<?= $kh['MaKhachHang'] ?>" style="background: rgba(100,116,139,0.3);">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Họ tên: <span class="text-danger">*</span></label>
                <input class="form-control" name="TenKH" value="<?= htmlspecialchars($kh['TenKH']) ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>CCCD/CMND: <span class="text-danger">*</span></label>
                <input class="form-control" id="cccdInputEdit" name="CCCD" value="<?= htmlspecialchars($kh['CCCD']) ?>" required>
                <small class="text-danger d-none" id="cccdErrorEdit"></small>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Số điện thoại: <span class="text-danger">*</span></label>
                <input class="form-control" id="sdtInputEdit" name="SDT" value="<?= htmlspecialchars($kh['SDT']) ?>" required>
                <small class="text-danger d-none" id="sdtErrorEdit"></small>
            </div>

            <div class="col-md-6 mb-3">
                <label>Email: <span class="text-danger">*</span></label>
                <input class="form-control" type="email" id="emailInputEdit" name="Email" value="<?= htmlspecialchars($kh['Email']) ?>" required>
                <small class="text-danger d-none" id="emailErrorEdit"></small>
            </div>
        </div>

        <div class="mb-3">
            <label>Địa chỉ:</label>
            <textarea id="diachiInput" class="form-control" name="DiaChi" rows="2" maxlength="150" placeholder="Nhập địa chỉ (nếu có)"><?= htmlspecialchars($kh['DiaChi'] ?? '') ?></textarea>
            <div class="form-text">Tối đa <strong>150</strong> ký tự. <span id="diachiCount">0</span>/150</div>
        </div>

        <div class="mb-3">
            <label>Loại khách: <span class="text-danger">*</span></label>
            <select class="form-select" name="LoaiKhach" required>
                <option value="">-- Chọn loại khách --</option>
                <option value="Cá nhân" <?= ($kh['LoaiKhach'] ?? '') === 'Cá nhân' ? 'selected' : '' ?>>Cá nhân</option>
                <option value="Trưởng đoàn" <?= ($kh['LoaiKhach'] ?? '') === 'Trưởng đoàn' ? 'selected' : '' ?>>Trưởng đoàn</option>
                <option value="Thành viên" <?= ($kh['LoaiKhach'] ?? '') === 'Thành viên' ? 'selected' : '' ?>>Thành viên</option>
            </select>
        </div>

        <hr class="my-4" style="border-color: rgba(148,163,184,0.35);">

        <?php if (!empty($kh['MaTK'])): ?>
            <!-- Đã có tài khoản -->
            <div class="alert alert-info" style="background: rgba(56,189,248,0.2); border: 1px solid #38bdf8; color: #93c5fd;">
                <i class="fa-solid fa-circle-check me-2"></i>
                <strong>Khách hàng đã có tài khoản:</strong> <?= htmlspecialchars($kh['Username'] ?? 'Không rõ') ?>
            </div>
            
            <div class="mb-3">
                <label>Đổi mật khẩu mới (để trống nếu không đổi):</label>
                <div class="input-group">
                    <input class="form-control" type="text" name="Password" id="passwordInputEditExisting"
                        placeholder="Nhập mật khẩu mới hoặc nhấn Generate">
                    <button type="button" class="btn btn-warning" id="generatePasswordBtnExisting">
                        <i class="fa-solid fa-wand-magic-sparkles me-1"></i>Generate
                    </button>
                </div>
            </div>
        <?php else: ?>
            <!-- Chưa có tài khoản -->
            <div class="alert alert-warning" style="background: rgba(251,191,36,0.2); border: 1px solid #fbbf24; color: #fde68a;">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                Khách hàng chưa có tài khoản đăng nhập
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="createAccountEdit" name="CreateAccount" value="1">
                    <label class="form-check-label fw-bold" for="createAccountEdit">
                        <i class="fa-solid fa-user-plus me-2 text-success"></i>Tạo tài khoản đăng nhập cho khách hàng
                    </label>
                </div>
            </div>

            <div id="accountFieldsEdit" style="display: none; background: rgba(56,189,248,0.1); padding: 20px; border-radius: 12px; border: 1px solid rgba(56,189,248,0.3);">
                <h6 class="text-info mb-3">
                    <i class="fa-solid fa-key me-2"></i>Thông tin tài khoản
                </h6>
                
                <div class="mb-3">
                    <label>Tên đăng nhập: <span class="text-danger">*</span></label>
                    <input class="form-control" name="Username" id="usernameInputEdit"
                        placeholder="Ví dụ: nguyenvana, khach123...">
                    <small class="text-muted d-block" id="usernameHintEdit">
                        <i class="fa-solid fa-lightbulb me-1"></i>Phải từ 3-30 ký tự, chỉ chứa chữ, số, gạch dưới
                    </small>
                    <small class="text-danger d-none" id="usernameErrorEdit">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i><span id="usernameErrorTextEdit"></span>
                    </small>
                </div>

                <div class="mb-3">
                    <label>Mật khẩu: <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input class="form-control" type="text" name="Password" id="passwordInputEdit"
                            placeholder="Nhập mật khẩu hoặc nhấn Generate">
                        <button type="button" class="btn btn-warning" id="generatePasswordBtnEdit">
                            <i class="fa-solid fa-wand-magic-sparkles me-1"></i>Generate
                        </button>
                    </div>
                    <small class="text-muted">
                        <i class="fa-solid fa-lightbulb me-1"></i>Có thể tự nhập hoặc dùng nút Generate để tầo mật khẩu ngẫu nhiên
                    </small>
                </div>

                <div class="mb-3">
                    <label>Xác nhận mật khẩu: <span class="text-danger">*</span></label>
                    <input class="form-control" type="password" name="ConfirmPassword" id="confirmPasswordInputEdit"
                        placeholder="Nhập lại mật khẩu">
                </div>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between mt-4">
            <a href="index.php?controller=quanly&action=danhsachKhachHang"
               class="btn btn-cancel">
               <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>

            <button class="btn btn-save" type="submit">
                <i class="fa-solid fa-floppy-disk me-2"></i>Cập nhật
            </button>
        </div>
    </form>

    <script>
    // Validate Username realtime for edit mode
    const usernameInputEdit = document.getElementById('usernameInputEdit');
    const usernameHintEdit = document.getElementById('usernameHintEdit');
    const usernameErrorEdit = document.getElementById('usernameErrorEdit');
    const usernameErrorTextEdit = document.getElementById('usernameErrorTextEdit');
    
    if (usernameInputEdit) {
        usernameInputEdit.addEventListener('input', function() {
            const value = this.value.trim();
            const usernamePattern = /^[a-zA-Z0-9_]{3,30}$/;
            
            if (value === '') {
                usernameInputEdit.classList.remove('is-invalid');
                usernameHintEdit.classList.remove('d-none');
                usernameErrorEdit.classList.add('d-none');
            } else if (value.length < 3) {
                usernameInputEdit.classList.add('is-invalid');
                usernameHintEdit.classList.add('d-none');
                usernameErrorEdit.classList.remove('d-none');
                usernameErrorTextEdit.textContent = 'Tên đăng nhập phải có ít nhất 3 ký tự';
            } else if (!usernamePattern.test(value)) {
                usernameInputEdit.classList.add('is-invalid');
                usernameHintEdit.classList.add('d-none');
                usernameErrorEdit.classList.remove('d-none');
                usernameErrorTextEdit.textContent = 'Chỉ được chứa chữ cái, số và gạch dưới (_)';
            } else {
                usernameInputEdit.classList.remove('is-invalid');
                usernameInputEdit.classList.add('is-valid');
                usernameHintEdit.classList.remove('d-none');
                usernameErrorEdit.classList.add('d-none');
            }
        });

        // Debounced AJAX check for duplicate username
        let usernameEditTimer = null;
        usernameInputEdit.addEventListener('input', function() {
            clearTimeout(usernameEditTimer);
            const val = this.value.trim();
            if (val.length < 3) return; // skip too short
            usernameEditTimer = setTimeout(function() {
                // pass excludeMaTK if editing an existing account (not creating)
                const excludeMaTK = <?= isset($kh['MaTK']) ? (int)$kh['MaTK'] : 0 ?>;
                fetch('index.php?controller=quanly&action=checkUsernameAjax&username=' + encodeURIComponent(val) + '&excludeMaTK=' + excludeMaTK)
                    .then(r => r.json())
                    .then(data => {
                        if (data.exists) {
                            usernameInputEdit.classList.add('is-invalid');
                            usernameInputEdit.classList.remove('is-valid');
                            usernameHintEdit.classList.add('d-none');
                            usernameErrorEdit.classList.remove('d-none');
                            usernameErrorTextEdit.textContent = 'Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.';
                        } else {
                            // leave other validations intact
                            if (usernamePattern.test(val)) {
                                usernameInputEdit.classList.remove('is-invalid');
                                usernameInputEdit.classList.add('is-valid');
                                usernameHintEdit.classList.remove('d-none');
                                usernameErrorEdit.classList.add('d-none');
                            }
                        }
                        toggleSubmitButton();
                    }).catch(err => {
                        console.error('Username check failed', err);
                    });
            }, 450);
        });
    }

    // Toggle account fields for edit mode
    const createAccountEditCheckbox = document.getElementById('createAccountEdit');
    if (createAccountEditCheckbox) {
        createAccountEditCheckbox.addEventListener('change', function() {
            const accountFields = document.getElementById('accountFieldsEdit');
            const usernameInput = document.getElementById('usernameInputEdit');
            const passwordInput = document.getElementById('passwordInputEdit');
            const confirmPasswordInput = document.getElementById('confirmPasswordInputEdit');
            
            if (this.checked) {
                accountFields.style.display = 'block';
                usernameInput.required = true;
                passwordInput.required = true;
                confirmPasswordInput.required = true;
            } else {
                accountFields.style.display = 'none';
                usernameInput.required = false;
                passwordInput.required = false;
                confirmPasswordInput.required = false;
            }
        });

        // Generate Password cho tạo tài khoản mới trong EDIT mode
        const generatePasswordBtnEdit = document.getElementById('generatePasswordBtnEdit');
        const passwordInputEdit = document.getElementById('passwordInputEdit');
        const confirmPasswordInputEdit = document.getElementById('confirmPasswordInputEdit');
        
        if (generatePasswordBtnEdit && passwordInputEdit && confirmPasswordInputEdit) {
            generatePasswordBtnEdit.addEventListener('click', function() {
                const length = 12;
                const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
                let password = '';
                for (let i = 0; i < length; i++) {
                    password += charset.charAt(Math.floor(Math.random() * charset.length));
                }
                passwordInputEdit.value = password;
                confirmPasswordInputEdit.value = password;
            });
        }
    }
    
    // Small helper to escape HTML in names returned from server
    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, function (s) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"})[s];
        });
    }

    // Realtime duplicate checks for CCCD / SDT / Email (ADD mode)
    const cccdInput = document.getElementById('cccdInput');
    const cccdError = document.getElementById('cccdError');
    const sdtInput = document.getElementById('sdtInput');
    const sdtError = document.getElementById('sdtError');
    const emailInput = document.getElementById('emailInput');
    const emailError = document.getElementById('emailError');

    let cccdTimer = null, sdtTimer = null, emailTimer = null;

    if (cccdInput) {
        cccdInput.addEventListener('input', function() {
            clearTimeout(cccdTimer);
            const val = this.value.trim();
            // only check when plausible length
            if (val.length < 9) {
                cccdInput.classList.remove('is-invalid');
                cccdError.classList.add('d-none');
                return;
            }
            cccdTimer = setTimeout(function() {
                fetch('index.php?controller=quanly&action=checkCustomerFieldAjax&field=cccd&value=' + encodeURIComponent(val))
                    .then(r => r.json())
                    .then(data => {
                        if (data.exists) {
                            cccdInput.classList.add('is-invalid');
                            cccdError.classList.remove('d-none');
                            const name = data.name ? escapeHtml(data.name) : '';
                            const link = data.ma ? (' <a href="index.php?controller=quanly&action=sua&id=' + encodeURIComponent(data.ma) + '" class="text-info">#' + encodeURIComponent(data.ma) + (name ? (' ' + name) : '') + '</a>') : '';
                            cccdError.innerHTML = 'CCCD đã tồn tại trong hệ thống' + link;
                        } else {
                            cccdInput.classList.remove('is-invalid');
                            cccdError.classList.add('d-none');
                        }
                    }).catch(err => console.error('CCCD check failed', err));
            }, 450);
        });
    }

    if (sdtInput) {
        sdtInput.addEventListener('input', function() {
            clearTimeout(sdtTimer);
            const val = this.value.trim();
            if (val.length < 10) {
                sdtInput.classList.remove('is-invalid');
                sdtError.classList.add('d-none');
                return;
            }
            sdtTimer = setTimeout(function() {
                fetch('index.php?controller=quanly&action=checkCustomerFieldAjax&field=sdt&value=' + encodeURIComponent(val))
                    .then(r => r.json())
                    .then(data => {
                        if (data.exists) {
                            sdtInput.classList.add('is-invalid');
                            sdtError.classList.remove('d-none');
                            const name = data.name ? escapeHtml(data.name) : '';
                            const link = data.ma ? (' <a href="index.php?controller=quanly&action=sua&id=' + encodeURIComponent(data.ma) + '" class="text-info">#' + encodeURIComponent(data.ma) + (name ? (' ' + name) : '') + '</a>') : '';
                            sdtError.innerHTML = 'Số điện thoại đã tồn tại' + link;
                        } else {
                            sdtInput.classList.remove('is-invalid');
                            sdtError.classList.add('d-none');
                        }
                    }).catch(err => console.error('SDT check failed', err));
            }, 450);
        });
    }

    if (emailInput) {
        emailInput.addEventListener('input', function() {
            clearTimeout(emailTimer);
            const val = this.value.trim();
            if (val.length < 5 || val.indexOf('@') === -1) {
                emailInput.classList.remove('is-invalid');
                emailError.classList.add('d-none');
                return;
            }
            emailTimer = setTimeout(function() {
                fetch('index.php?controller=quanly&action=checkCustomerFieldAjax&field=email&value=' + encodeURIComponent(val))
                    .then(r => r.json())
                    .then(data => {
                        if (data.exists) {
                            emailInput.classList.add('is-invalid');
                            emailError.classList.remove('d-none');
                            const name = data.name ? escapeHtml(data.name) : '';
                            const link = data.ma ? (' <a href="index.php?controller=quanly&action=sua&id=' + encodeURIComponent(data.ma) + '" class="text-info">#' + encodeURIComponent(data.ma) + (name ? (' ' + name) : '') + '</a>') : '';
                            emailError.innerHTML = 'Email đã tồn tại' + link;
                        } else {
                            emailInput.classList.remove('is-invalid');
                            emailError.classList.add('d-none');
                        }
                    }).catch(err => console.error('Email check failed', err));
            }, 450);
        });
    }

    // Generate Password cho đổi mật khẩu (khi đã có tài khoản)
    const generatePasswordBtnExisting = document.getElementById('generatePasswordBtnExisting');
    const passwordInputEditExisting = document.getElementById('passwordInputEditExisting');
    
    if (generatePasswordBtnExisting && passwordInputEditExisting) {
        generatePasswordBtnExisting.addEventListener('click', function() {
            const length = 12;
            const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
            let password = '';
            for (let i = 0; i < length; i++) {
                password += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            passwordInputEditExisting.value = password;
        });
    }
    </script>

<?php endif; ?>


<!-- ====================================================================== -->
<!-- 🔥 MODE 4: PHÁT HIỆN TRÙNG -->
<!-- ====================================================================== -->
<?php if ($mode === "duplicate"): ?>

    <div class="page-title">
        <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>
        Phát hiện thông tin trùng
    </div>

    <div class="alert alert-warning alert-dismissible fade show" role="alert" style="background: linear-gradient(135deg, rgba(251,191,36,0.2), rgba(245,158,11,0.1)); border-left: 4px solid #f59e0b; border-radius: 12px;">
        <div class="d-flex align-items-start">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-triangle-exclamation fa-2x text-warning me-3"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="alert-heading mb-2">
                    <i class="fa-solid fa-exclamation-circle me-2"></i>Dữ liệu bị trùng với khách hàng đã tồn tại
                </h5>
                <p class="mb-2">Thông tin bạn nhập trùng với các khách hàng sau:</p>
                <?php 
                // So sánh để tìm trường nào trùng
                $inputEmail = $data['Email'] ?? '';
                $inputSDT = $data['SDT'] ?? '';
                $inputCCCD = $data['CCCD'] ?? '';
                
                $trungEmail = false;
                $trungSDT = false;
                $trungCCCD = false;
                
                foreach ($duplicates as $dup) {
                    if ($dup['Email'] === $inputEmail) $trungEmail = true;
                    if ($dup['SDT'] === $inputSDT) $trungSDT = true;
                    if ($dup['CCCD'] === $inputCCCD) $trungCCCD = true;
                }
                ?>
                <div class="mb-2">
                    <strong>Trường bị trùng:</strong>
                    <?php if ($trungEmail): ?>
                        <span class="badge bg-danger me-1"><i class="fa-solid fa-envelope me-1"></i>Email</span>
                    <?php endif; ?>
                    <?php if ($trungSDT): ?>
                        <span class="badge bg-danger me-1"><i class="fa-solid fa-phone me-1"></i>Số điện thoại</span>
                    <?php endif; ?>
                    <?php if ($trungCCCD): ?>
                        <span class="badge bg-danger me-1"><i class="fa-solid fa-id-card me-1"></i>CCCD/CMND</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-dark table-striped table-hover">
        <thead>
            <tr>
                <th>Họ tên</th>
                <th>Email <?php if ($trungEmail): ?><i class="fa-solid fa-circle-exclamation text-danger"></i><?php endif; ?></th>
                <th>SĐT <?php if ($trungSDT): ?><i class="fa-solid fa-circle-exclamation text-danger"></i><?php endif; ?></th>
                <th>CCCD <?php if ($trungCCCD): ?><i class="fa-solid fa-circle-exclamation text-danger"></i><?php endif; ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($duplicates as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['TenKH']) ?></td>
                <td <?php if ($trungEmail && $c['Email'] === $inputEmail): ?>class="bg-danger bg-opacity-25"<?php endif; ?>>
                    <?= htmlspecialchars($c['Email']) ?>
                    <?php if ($trungEmail && $c['Email'] === $inputEmail): ?>
                        <i class="fa-solid fa-circle-exclamation text-danger ms-1"></i>
                    <?php endif; ?>
                </td>
                <td <?php if ($trungSDT && $c['SDT'] === $inputSDT): ?>class="bg-danger bg-opacity-25"<?php endif; ?>>
                    <?= htmlspecialchars($c['SDT']) ?>
                    <?php if ($trungSDT && $c['SDT'] === $inputSDT): ?>
                        <i class="fa-solid fa-circle-exclamation text-danger ms-1"></i>
                    <?php endif; ?>
                </td>
                <td <?php if ($trungCCCD && $c['CCCD'] === $inputCCCD): ?>class="bg-danger bg-opacity-25"<?php endif; ?>>
                    <?= htmlspecialchars($c['CCCD']) ?>
                    <?php if ($trungCCCD && $c['CCCD'] === $inputCCCD): ?>
                        <i class="fa-solid fa-circle-exclamation text-danger ms-1"></i>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-between mt-3">
        <a href="index.php?controller=quanly&action=sua&id=<?= $duplicates[0]['MaKhachHang'] ?>"
           class="btn btn-primary">
            <i class="fa-solid fa-pen-to-square me-1"></i> Cập nhật hồ sơ cũ
        </a>

        <a href="index.php?controller=quanly&action=themKhachHang"
           class="btn btn-save">
           <i class="fa-solid fa-plus me-1"></i> Nhập lại với thông tin khác
        </a>
    </div>

<?php endif; ?>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-submit form khi gõ từ khóa (sau 500ms không gõ nữa)
let typingTimerKH;
const keywordInputKH = document.getElementById('keywordInputKH');
const searchFormKH = document.getElementById('filterFormKH');

const usernameFilterKH = document.getElementById('usernameFilterKH');

if (keywordInputKH && searchFormKH) {
    keywordInputKH.addEventListener('keyup', function() {
        clearTimeout(typingTimerKH);
        typingTimerKH = setTimeout(function() {
            searchFormKH.submit();
        }, 500); // Đợi 500ms sau khi người dùng ngừng gõ
    });

    keywordInputKH.addEventListener('keydown', function() {
        clearTimeout(typingTimerKH);
    });
}

// Username filter: same behaviour as keyword
if (usernameFilterKH && searchFormKH) {
    usernameFilterKH.addEventListener('keyup', function() {
        clearTimeout(typingTimerKH);
        typingTimerKH = setTimeout(function() {
            searchFormKH.submit();
        }, 500);
    });

    usernameFilterKH.addEventListener('keydown', function() {
        clearTimeout(typingTimerKH);
    });
}
</script>

<script>
// Ensure duplicate-check listeners attach after DOM ready and provide immediate checks on blur
document.addEventListener('DOMContentLoaded', function() {
    function doCheck(field, value, exclude) {
        return fetch('index.php?controller=quanly&action=checkCustomerFieldAjax&field=' + encodeURIComponent(field) + '&value=' + encodeURIComponent(value) + (exclude ? ('&excludeMaKhachHang=' + encodeURIComponent(exclude)) : ''))
            .then(r => r.json())
            .catch(e => ({ exists: false }));
    }

    const fieldLabelMap = { cccd: 'CCCD', sdt: 'Số điện thoại', email: 'Email' };

    // Disable submit when any field is invalid (only for EDIT flow)
    function toggleSubmitButton() {
        const btn = document.querySelector('.btn-save');
        if (!btn) return;
        // Detect whether this is the Add form (luuThem) or Edit form (luuCapNhat)
        const form = document.querySelector('form');
        const action = form ? (form.getAttribute('action') || '') : '';
        const isAddMode = action.indexOf('luuThem') !== -1;

        const hasError = document.querySelectorAll('.form-control.is-invalid').length > 0;
        // If add mode, do not disable submit — only show visual error
        if (isAddMode) {
            btn.disabled = false;
            btn.classList.toggle('opacity-50', false);
            return;
        }

        btn.disabled = hasError;
        btn.classList.toggle('opacity-50', hasError);
    }

    function attachAdd(fieldId, errorId, minLen, fieldName) {
        const el = document.getElementById(fieldId);
        const err = document.getElementById(errorId);
        if (!el || !err) return;

        let timer = null;
        const runCheck = (val) => {
            if (!val || val.length < minLen) return Promise.resolve({ exists: false });
            return doCheck(fieldName, val, 0).then(data => {
                if (data.exists) {
                    el.classList.add('is-invalid');
                    err.classList.remove('d-none');
                    const name = data.name ? escapeHtml(data.name) : '';
                    const link = data.ma ? (' <a href="index.php?controller=quanly&action=sua&id=' + encodeURIComponent(data.ma) + '" class="text-info">#' + encodeURIComponent(data.ma) + (name ? (' ' + name) : '') + '</a>') : '';
                    const label = fieldLabelMap[fieldName] || fieldName;
                    err.innerHTML = '<strong>' + label + '</strong> đã tồn tại: ' + link;
                } else {
                    el.classList.remove('is-invalid');
                    err.classList.add('d-none');
                }
                toggleSubmitButton();
                return data;
            });
        };

        el.addEventListener('input', function() {
            clearTimeout(timer);
            const v = this.value.trim();
            timer = setTimeout(() => runCheck(v), 300);
        });

        el.addEventListener('blur', function() {
            clearTimeout(timer);
            runCheck(this.value.trim());
        });
    }

    function attachEdit(fieldId, errorId, minLen, fieldName, excludeIdOrValue) {
        const el = document.getElementById(fieldId);
        const err = document.getElementById(errorId);
        if (!el || !err) return;

        let timer = null;
        const getExclude = () => {
            if (typeof excludeIdOrValue === 'number') return excludeIdOrValue;
            if (typeof excludeIdOrValue === 'string') {
                const v = document.querySelector("input[name='MaKhachHang']") || document.getElementById(excludeIdOrValue);
                return v ? v.value : 0;
            }
            return 0;
        };

        const runCheck = (val) => {
            const exclude = getExclude() || 0;
            if (!val || val.length < minLen) return Promise.resolve({ exists: false });
            return doCheck(fieldName, val, exclude).then(data => {
                if (data.exists) {
                    el.classList.add('is-invalid');
                    err.classList.remove('d-none');
                    const name = data.name ? escapeHtml(data.name) : '';
                    const link = data.ma ? (' <a href="index.php?controller=quanly&action=sua&id=' + encodeURIComponent(data.ma) + '" class="text-info">#' + encodeURIComponent(data.ma) + (name ? (' ' + name) : '') + '</a>') : '';
                    const label = fieldLabelMap[fieldName] || fieldName;
                    err.innerHTML = '<strong>' + label + '</strong> đã tồn tại: ' + link;
                } else {
                    el.classList.remove('is-invalid');
                    err.classList.add('d-none');
                }
                toggleSubmitButton();
                return data;
            });
        };

        el.addEventListener('input', function() {
            clearTimeout(timer);
            const v = this.value.trim();
            timer = setTimeout(() => runCheck(v), 300);
        });
        el.addEventListener('blur', function() {
            clearTimeout(timer);
            runCheck(this.value.trim());
        });
    }

    // Attach ADD-mode fields
    attachAdd('cccdInput', 'cccdError', 9, 'cccd');
    attachAdd('sdtInput', 'sdtError', 10, 'sdt');
    attachAdd('emailInput', 'emailError', 5, 'email');

    // DiaChi character counter and client-side enforcement (max 150)
    (function() {
        const MAX = 150;

        // Find textarea by id or fallback to name
        let diachi = document.getElementById('diachiInput');
        if (!diachi) diachi = document.querySelector("textarea[name='DiaChi']");

        // Ensure counter exists, otherwise create and insert after textarea
        let counter = document.getElementById('diachiCount');
        if (!counter && diachi) {
            counter = document.createElement('span');
            counter.id = 'diachiCount';
            // find or create wrapper .form-text
            let wrapper = diachi.parentElement.querySelector('.form-text');
            if (!wrapper) {
                wrapper = document.createElement('div');
                wrapper.className = 'form-text';
                diachi.parentElement.appendChild(wrapper);
            }
            wrapper.innerHTML = 'Tối đa <strong>' + MAX + '</strong> ký tự. ';
            wrapper.appendChild(counter);
            wrapper.innerHTML += '/' + MAX;
        }

        // Ensure error element exists
        let errEl = document.getElementById('diachiError');
        if (!errEl && diachi) {
            errEl = document.createElement('small');
            errEl.id = 'diachiError';
            errEl.className = 'text-danger d-none mt-1';
            errEl.textContent = 'Đã đạt giới hạn ' + MAX + ' ký tự — không thể nhập thêm.';
            diachi.parentElement.appendChild(errEl);
        }

        if (!diachi || !counter) return;

        let errTimer = null;
        const showErr = () => {
            if (!errEl) return;
            errEl.classList.remove('d-none');
            if (errTimer) clearTimeout(errTimer);
            errTimer = setTimeout(() => errEl.classList.add('d-none'), 3000);
        };

        const update = () => {
            let v = diachi.value || '';
            if (v.length > MAX) {
                diachi.value = v.slice(0, MAX);
                v = diachi.value;
                showErr();
            }
            counter.textContent = v.length;
            if (v.length === MAX) showErr();
        };

        diachi.addEventListener('input', update);
        diachi.addEventListener('paste', function() { setTimeout(update, 0); });

        // initialize
        update();
    })();

    // Attach EDIT-mode fields (exclude current MaKhachHang if present)
    const excludeId = <?= isset($kh['MaKhachHang']) ? (int)$kh['MaKhachHang'] : 0 ?>;
    attachEdit('cccdInputEdit', 'cccdErrorEdit', 9, 'cccd', excludeId);
    attachEdit('sdtInputEdit', 'sdtErrorEdit', 10, 'sdt', excludeId);
    attachEdit('emailInputEdit', 'emailErrorEdit', 5, 'email', excludeId);
    // ensure button state on load
    toggleSubmitButton();
});
</script>

</body>
</html>