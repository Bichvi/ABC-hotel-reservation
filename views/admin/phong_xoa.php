<?php
$user    = Auth::user();
$rooms   = $rooms ?? [];
$errors  = $errors ?? [];
$success = $success ?? null;

// Helper function
if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xóa phòng - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body{
            margin:0;
            font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
            background: radial-gradient(circle at top, #020617, #020617 40%, #0f172a 100%);
            color:#e5e7eb;
        }
        .topbar{
            background:rgba(15,23,42,0.96);
            backdrop-filter: blur(10px);
            color:#e5e7eb;
            padding:12px 24px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            border-bottom:1px solid #1f2937;
        }
        .brand{font-weight:700;letter-spacing:.08em;font-size:14px;text-transform:uppercase;}
        .role{font-size:13px;opacity:.8;}
        .topbar a{
            font-size:13px;
            color:#93c5fd;
            text-decoration:none;
            margin-left:0;
            padding:6px 12px;
            border-radius:6px;
            transition:all 0.2s ease;
            display:inline-flex;
            align-items:center;
            gap:6px;
        }
        .topbar a:hover{
            background:rgba(147,197,253,0.1);
            text-decoration:none;
            color:#bfdbfe;
        }
        .topbar .nav-separator{
            color:#4b5563;
            margin:0 8px;
            font-size:12px;
        }
        .topbar .nav-group{
            display:flex;
            align-items:center;
            gap:4px;
        }
        .topbar span {
            font-size:16px !important;
            font-weight:500;
            margin-right:16px;
        }
        .topbar span strong {
            font-size:16px !important;
            font-weight:700;
            color:#e5e7eb !important;
        }
        .container-page{
            max-width:1200px;
            margin:24px auto 40px;
            padding:0 16px;
        }
        .page-title{
            display:flex;
            align-items:baseline;
            justify-content:space-between;
            gap:8px;
            margin-bottom:16px;
        }
        .page-title h1{margin:0;font-size:24px;color:#f9fafb;}
        .page-title span{font-size:13px;color:#9ca3af;}

        .card-shell{
            background: linear-gradient(135deg, rgba(15,23,42,0.98), rgba(15,23,42,0.95));
            border-radius:18px;
            padding:16px 18px;
            border:1px solid rgba(148,163,184,0.35);
            box-shadow:0 18px 40px rgba(0,0,0,0.65);
            margin-bottom:16px;
        }
        .card-shell h2{margin:0 0 6px;font-size:17px;color:#e5e7eb;}
        .card-shell p.caption{margin:0 0 12px;font-size:13px;color:#9ca3af;}

        label{display:block;font-size:13px;color:#cbd5f5;margin-bottom:4px;}
        input[type="number"],
        input[type="text"],
        select{
            width:100%;
            padding:7px 10px;
            border-radius:999px;
            border:1px solid #4b5563;
            font-size:14px;
            box-sizing:border-box;
            background:#020617;
            color:#e5e7eb;
            outline:none;
            transition:all .15s ease;
        }
        input:focus,select:focus{
            border-color:#3b82f6;
            box-shadow:0 0 0 1px rgba(59,130,246,.4);
            background:#020617;
        }
        .row-flex{display:flex;flex-wrap:wrap;gap:12px;}
        .field{flex:1;min-width:180px;}

        .alert{
            border-radius:12px;
            padding:10px 12px;
            margin-bottom:12px;
            font-size:13px;
        }
        .alert-error{
            background:rgba(239,68,68,0.12);
            border:1px solid rgba(248,113,113,0.6);
            color:#fecaca;
        }
        .alert-success{
            background:rgba(16,185,129,0.15);
            border:1px solid rgba(45,212,191,0.7);
            color:#bbf7d0;
        }
        .btn-danger-modern{
            border:none;
            border-radius:999px;
            padding:9px 18px;
            font-size:14px;
            font-weight:500;
            color:#fff;
            background:linear-gradient(135deg,#ef4444,#dc2626);
            cursor:pointer;
            display:inline-flex;
            align-items:center;
            gap:6px;
            box-shadow:0 12px 24px rgba(239,68,68,.4);
            transition:transform .12s ease,box-shadow .12s ease,filter .12s ease;
        }
        .btn-danger-modern:hover{
            transform:translateY(-1px);
            box-shadow:0 18px 38px rgba(239,68,68,.55);
            filter:brightness(1.03);
        }
        .btn-outline{
            border-radius:999px;
            border:1px solid #4b5563;
            background:transparent;
            color:#e5e7eb;
            padding:8px 14px;
            font-size:13px;
            cursor:pointer;
        }
        .btn-outline:hover{
            background:#020617;
        }
        .btn-row{
            margin-top:10px;
            display:flex;
            justify-content:flex-end;
            gap:10px;
            flex-wrap:wrap;
            align-items:center;
        }

        .filter-section{
            background:rgba(30,41,59,0.5);
            border-radius:12px;
            padding:14px;
            margin-bottom:16px;
            border:1px solid rgba(148,163,184,0.25);
        }
        .filter-group{
            display:inline-block;
            margin-right:16px;
            margin-bottom:8px;
        }
        .filter-group label{
            font-size:.85rem;
            color:#9ca3af;
            margin-right:6px;
            text-transform:uppercase;
            letter-spacing:.03em;
        }
        .filter-group select,
        .filter-group input{
            font-size:.9rem;
            padding:6px 10px;
            border-radius:999px;
            background:#020617;
            border:1px solid #4b5563;
            color:#e5e7eb;
        }
        .filter-group select:focus,
        .filter-group input:focus{
            border-color:#3b82f6;
            box-shadow:0 0 0 1px rgba(59,130,246,.4);
        }

        .rooms-table{
            width:100%;
            border-collapse:collapse;
            font-size:13px;
        }
        .rooms-table thead{
            background:rgba(30,41,59,0.5);
            border-bottom:2px solid rgba(148,163,184,0.45);
        }
        .rooms-table th{
            padding:10px 12px;
            text-align:left;
            color:#cbd5f5;
            font-weight:600;
            font-size:12px;
        }
        .rooms-table td{
            padding:10px 12px;
            border-bottom:1px solid rgba(148,163,184,0.2);
            color:#e5e7eb;
        }
        .rooms-table tbody tr{
            transition:.15s;
        }
        .rooms-table tbody tr:hover{
            background:rgba(30,64,175,0.15);
        }
        .room-table-container{
            border-radius:12px;
            border:1px solid rgba(148,163,184,0.3);
            overflow:hidden;
            background:rgba(15,23,42,0.5);
            margin-bottom:16px;
        }
        .room-table-scroll{
            max-height:450px;
            overflow-y:auto;
            overflow-x:auto;
        }
        .status-badge{
            display:inline-block;
            padding:4px 12px;
            border-radius:20px;
            font-size:.8rem;
            font-weight:600;
        }
        .status-hoatdong{
            background:rgba(34,197,94,0.2);
            color:#86efac;
            border:1px solid rgba(34,197,94,0.5);
        }
        .status-ngung{
            background:rgba(249,115,22,0.2);
            color:#fdba74;
            border:1px solid rgba(249,115,22,0.5);
        }
        .status-baotrisinh{
            background:rgba(59,130,246,0.2);
            color:#93c5fd;
            border:1px solid rgba(59,130,246,0.5);
        }
        .checkbox-cell{
            text-align:center;
            width:45px;
        }
        .form-check-input{
            cursor:pointer;
        }
        small.text-secondary{
            color:#9ca3af;
            font-size:12px;
        }
    </style>
</head>
<body>
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · ADMIN</div>
        <div class="role">Xóa phòng</div>
    </div>
    <div style="display:flex;align-items:center;gap:8px;">
        <?php if ($user): ?>
            <span style="font-size:13px;margin-right:8px;">
                Xin chào, <strong><?= e($user['Username'] ?? 'Admin') ?></strong>
            </span>
            <span class="nav-separator">|</span>
        <?php endif; ?>
        <div class="nav-group">
            <a href="index.php?controller=home&action=index">
                <i class="fa-solid fa-home"></i>
                <span>Trang chủ</span>
            </a>
            <span class="nav-separator">|</span>
            <a href="index.php?controller=dashboard&action=admin">
                <i class="fa-solid fa-gauge"></i>
                <span>Dashboard</span>
            </a>
            <span class="nav-separator">|</span>
            <a href="index.php?controller=admin&action=phongThem">
                <i class="fa-solid fa-plus"></i>
                <span>Thêm phòng</span>
            </a>
            <span class="nav-separator">|</span>
            <a href="index.php?controller=admin&action=phongSua">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Cập nhật phòng</span>
            </a>
            <span class="nav-separator">|</span>
            <a href="index.php?controller=admin&action=phongXoa">
                <i class="fa-solid fa-trash-can"></i>
                <span>Xóa phòng</span>
            </a>
            <span class="nav-separator">|</span>
            <a href="index.php?controller=auth&action=logout" style="color:#f87171;">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Đăng xuất</span>
            </a>
        </div>
    </div>
</div>

<div class="container-page">
    <div class="page-title">
        <div>
            <h1>Xóa phòng</h1>
            <span>Quản lý phòng → Xóa phòng</span>
        </div>
        <div>
            <a href="index.php?controller=dashboard&action=admin" class="btn-outline" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-arrow-left"></i>
                Trở về
            </a>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <strong>Có lỗi xảy ra:</strong>
            <ul class="mb-0 mt-1" style="padding-left:18px;">
                <?php foreach ($errors as $e): ?>
                    <li><?= e($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?= e($success) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="index.php?controller=admin&action=phongXoa">
        <div class="card-shell">
            <h2><i class="fa-solid fa-trash-can text-red-400 me-2"></i> Danh sách phòng</h2>
            <p class="caption">
                Chọn các phòng cần xóa. Chỉ các phòng không có giao dịch đang xử lý mới có thể bị xóa.
            </p>

            <!-- Bộ lọc -->
            <div class="filter-section">
                <div class="filter-group">
                    <label>Loại phòng:</label>
                    <select id="filterLoaiPhong">
                        <option value="">— Tất cả —</option>
                        <option value="standard">Standard</option>
                        <option value="deluxe">Deluxe</option>
                        <option value="superior">Superior</option>
                        <option value="suite">Suite</option>
                        <option value="vip">VIP</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>View phòng:</label>
                    <select id="filterViewPhong">
                        <option value="">— Tất cả —</option>
                        <option value="biển">Biển</option>
                        <option value="thành phố">Thành phố</option>
                        <option value="vườn">Vườn</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Tình trạng:</label>
                    <select id="filterTinhTrang">
                        <option value="">— Tất cả —</option>
                        <option value="tot">Tốt</option>
                        <option value="canvesinh">Cần vệ sinh</option>
                        <option value="huhahe">Hư hại nhẹ</option>
                        <option value="huhainang">Hư hại nặng</option>
                        <option value="dangbaotri">Đang bảo trì</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Trạng thái:</label>
                    <select id="filterTrangThai">
                        <option value="">— Tất cả —</option>
                        <option value="trong">Trống</option>
                        <option value="booked">Booked</option>
                        <option value="stayed">Stayed</option>
                        <option value="baotri">Bảo trì</option>
                    </select>
                </div>

                <div style="display:block;margin-top:8px;">
                    <div class="filter-group">
                        <label>Tìm kiếm:</label>
                        <input id="deleteRoomSearch" type="text" placeholder="Số phòng, loại giường, giá...">
                    </div>
                    <button type="button" class="btn-outline" id="resetFilters" style="margin-left:8px;">
                        Đặt lại bộ lọc
                    </button>
                </div>
            </div>

            <div class="room-table-container">
                <div class="room-table-scroll">
                    <table class="rooms-table">
                        <thead>
                            <tr>
                                <th class="checkbox-cell">
                                    <input type="checkbox" id="selectAllRooms" title="Chọn tất cả">
                                </th>
                                <th>Số Phòng</th>
                                <th>Loại Phòng</th>
                                <th>Loại Giường</th>
                                <th>Diện Tích</th>
                                <th>View</th>
                                <th>Giá</th>
                                <th>Khách Tối Đa</th>
                                <th>Tình Trạng</th>
                                <th>Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody id="deleteRoomList">
                            <?php if (empty($rooms)): ?>
                                <tr>
                                    <td colspan="10" class="text-center text-secondary py-4">
                                        <i class="fa-solid fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                        Chưa có dữ liệu phòng.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($rooms as $room): ?>
                                    <tr class="room-row" 
                                        data-search="<?= e(strtolower($room['SoPhong'] . ' ' . $room['LoaiPhong'] . ' ' . ($room['LoaiGiuong'] ?? '') . ' ' . ($room['ViewPhong'] ?? '') . ' ' . ($room['Gia'] ?? ''))) ?>"
                                        data-loai="<?= e(strtolower($room['LoaiPhong'] ?? '')) ?>"
                                        data-view="<?= e(strtolower($room['ViewPhong'] ?? '')) ?>"
                                        data-tinhtrang="<?= e(strtolower($room['TinhTrangPhong'] ?? 'tot')) ?>"
                                        data-trangthai="<?= e(strtolower($room['TrangThai'] ?? 'trong')) ?>">
                                        <td class="checkbox-cell">
                                            <input class="form-check-input room-checkbox" type="checkbox" name="chon[]" value="<?= e($room['MaPhong']) ?>">
                                        </td>
                                        <td>
                                            <strong><?= e($room['SoPhong']) ?></strong>
                                        </td>
                                        <td><?= e($room['LoaiPhong'] ?? '—') ?></td>
                                        <td><?= e($room['LoaiGiuong'] ?? '—') ?></td>
                                        <td><?= e($room['DienTich'] ?? '—') ?> m²</td>
                                        <td><?= e($room['ViewPhong'] ?? '—') ?></td>
                                        <td><?= number_format($room['Gia'] ?? 0, 0, ',', '.') ?> đ</td>
                                        <td><?= e($room['SoKhachToiDa'] ?? '—') ?></td>
                                        <td>
                                            <?php
                                                $tinhTrang = $room['TinhTrangPhong'] ?? 'Tot';
                                                $tinhtrangMap = [
                                                    'Tot' => 'Tốt', 
                                                    'CanVeSinh' => 'Cần vệ sinh',
                                                    'HuHaiNhe' => 'Hư hại nhẹ',
                                                    'HuHaiNang' => 'Hư hại nặng',
                                                    'DangBaoTri' => 'Đang bảo trì'
                                                ];
                                                $tinhTrangText = $tinhtrangMap[$tinhTrang] ?? $tinhTrang;
                                                
                                                $badgeClass = 'status-hoatdong';
                                                if ($tinhTrang === 'CanVeSinh') $badgeClass = 'status-ngung';
                                                elseif ($tinhTrang === 'HuHaiNhe' || $tinhTrang === 'HuHaiNang') $badgeClass = 'status-baotrisinh';
                                                elseif ($tinhTrang === 'DangBaoTri') $badgeClass = 'status-ngung';
                                            ?>
                                            <span class="status-badge <?= $badgeClass ?>">
                                                <?= $tinhTrangText ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                                $trangThai = $room['TrangThai'] ?? 'Trong';
                                                $thaiMap = ['Trong' => 'Trống', 'Booked' => 'Booked', 'Stayed' => 'Stayed'];
                                                $thaiText = $thaiMap[$trangThai] ?? $trangThai;
                                            ?>
                                            <span class="status-badge status-hoatdong">
                                                <?= e($thaiText) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row-flex" style="margin-top:16px;">
                <div class="field" style="flex:1;">
                    <div style="background:rgba(30,41,59,0.5);border-radius:12px;padding:12px;border:1px solid rgba(148,163,184,0.25);">
                        <h6 style="margin:0 0 8px;color:#e5e7eb;font-size:14px;">Lưu ý an toàn</h6>
                        <div class="small text-secondary">
                            - Phòng đang có giao dịch trạng thái <strong>Mới/Booked/Stayed</strong> sẽ không thể xóa.<br>
                            - Thao tác xóa không thể hoàn tác. Vui lòng kiểm tra kỹ trước khi xác nhận.
                        </div>
                    </div>
                </div>
                <div class="field" style="flex:0 0 200px;text-align:right;">
                    <div style="margin-bottom:8px;">
                        <span class="text-secondary small">
                            <span id="selectedCount">0</span> phòng được chọn
                        </span>
                    </div>
                    <button class="btn-danger-modern" type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa các phòng đã chọn? Thao tác này không thể hoàn tác!');">
                        <i class="fa-solid fa-trash-can"></i>
                        Xóa phòng đã chọn
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('deleteRoomSearch');
    const filterLoai = document.getElementById('filterLoaiPhong');
    const filterView = document.getElementById('filterViewPhong');
    const filterTinhTrang = document.getElementById('filterTinhTrang');
    const filterTrangThai = document.getElementById('filterTrangThai');
    const resetBtn = document.getElementById('resetFilters');
    const container = document.getElementById('deleteRoomList');
    const selectAll = document.getElementById('selectAllRooms');
    const roomCheckboxes = document.querySelectorAll('.room-checkbox');
    const selectedCount = document.getElementById('selectedCount');

    if (!container) return;

    // Apply all filters
    function applyFilters() {
        const q = (search ? search.value.trim().toLowerCase() : '');
        const loaiVal = (filterLoai ? filterLoai.value.toLowerCase() : '');
        const viewVal = (filterView ? filterView.value.toLowerCase() : '');
        const tinhTrangVal = (filterTinhTrang ? filterTinhTrang.value.toLowerCase() : '');
        const thaiVal = (filterTrangThai ? filterTrangThai.value.toLowerCase() : '');

        const rows = container.querySelectorAll('.room-row');
        rows.forEach(row => {
            const searchData = (row.getAttribute('data-search') || '').toLowerCase();
            const loai = (row.getAttribute('data-loai') || '').toLowerCase();
            const view = (row.getAttribute('data-view') || '').toLowerCase();
            const tinhTrang = (row.getAttribute('data-tinhtrang') || '').toLowerCase();
            const thai = (row.getAttribute('data-trangthai') || '').toLowerCase();

            const matchSearch = searchData.includes(q);
            const matchLoai = loaiVal === '' || loai.includes(loaiVal);
            const matchView = viewVal === '' || view.includes(viewVal);
            const matchTinhTrang = tinhTrangVal === '' || tinhTrang.includes(tinhTrangVal);
            const matchThai = thaiVal === '' || thai.includes(thaiVal);

            row.style.display = (matchSearch && matchLoai && matchView && matchTinhTrang && matchThai) ? '' : 'none';
        });

        updateSelectedCount();
    }

    // Search and filter event listeners
    if (search) search.addEventListener('input', applyFilters);
    if (filterLoai) filterLoai.addEventListener('change', applyFilters);
    if (filterView) filterView.addEventListener('change', applyFilters);
    if (filterTinhTrang) filterTinhTrang.addEventListener('change', applyFilters);
    if (filterTrangThai) filterTrangThai.addEventListener('change', applyFilters);

    // Reset filters
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            if (search) search.value = '';
            if (filterLoai) filterLoai.value = '';
            if (filterView) filterView.value = '';
            if (filterTinhTrang) filterTinhTrang.value = '';
            if (filterTrangThai) filterTrangThai.value = '';
            roomCheckboxes.forEach(cb => cb.checked = false);
            if (selectAll) selectAll.checked = false;
            applyFilters();
        });
    }

    // Update selected count
    function updateSelectedCount() {
        const checked = Array.from(roomCheckboxes).filter(cb => cb.checked).length;
        if (selectedCount) selectedCount.textContent = checked;
        
        // Update select-all indeterminate state
        if (selectAll) {
            const visibleCheckboxes = Array.from(roomCheckboxes).filter(cb => cb.closest('.room-row').style.display !== 'none');
            const visibleChecked = visibleCheckboxes.filter(cb => cb.checked).length;
            selectAll.indeterminate = visibleChecked > 0 && visibleChecked < visibleCheckboxes.length;
            selectAll.checked = visibleChecked === visibleCheckboxes.length && visibleCheckboxes.length > 0;
        }
    }

    // Select/deselect all
    if (selectAll) {
        selectAll.addEventListener('change', () => {
            const visibleCheckboxes = Array.from(roomCheckboxes).filter(cb => cb.closest('.room-row').style.display !== 'none');
            visibleCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateSelectedCount();
        });
    }

    // Update count when individual checkboxes change
    roomCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectedCount);
    });

    updateSelectedCount();
});
</script>
</body>
</html>
