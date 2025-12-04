<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;

$errors        = $errors        ?? [];
$success       = $success       ?? null;
$searchKeyword = $searchKeyword ?? '';
$giaoDich      = $giaoDich      ?? null;
$chiTietPhong  = $chiTietPhong  ?? [];
$dsDichVu      = $dsDichVu      ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt dịch vụ</title>
    <style>
        /* giữ nguyên CSS như bạn đã dán, mình chỉ thêm class error */
        body{
            margin:0;
            font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
            background:#f3f4f6;
        }
        .topbar{
            background:#0f172a;
            color:#e5e7eb;
            padding:12px 24px;
            display:flex;
            align-items:center;
            justify-content:space-between;
        }
        .topbar .brand{font-weight:600;letter-spacing:.03em;}
        .topbar .role{font-size:13px;opacity:.8;}
        .topbar a{
            font-size:13px;
            color:#e5e7eb;
            text-decoration:none;
            margin-left:12px;
        }
        .container{
            max-width:1160px;
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
        .page-title h1{margin:0;font-size:24px;color:#111827;}
        .page-title span{font-size:13px;color:#6b7280;}
        .back-link{
            font-size:13px;
            text-decoration:none;
            color:#2563eb;
            display:inline-flex;
            align-items:center;
            gap:4px;
        }
        .card{
            background:#fff;
            border-radius:12px;
            padding:14px 16px;
            box-shadow:0 10px 25px rgba(15,23,42,.05);
            border:1px solid #e5e7eb;
            margin-bottom:12px;
        }
        .card h2{margin:0 0 8px;font-size:16px;color:#111827;}
        .card p.caption{margin:0 0 10px;font-size:13px;color:#6b7280;}
        .row{display:flex;gap:12px;flex-wrap:wrap;}
        .field{margin-bottom:10px;flex:1;}
        label{display:block;font-size:13px;color:#374151;margin-bottom:4px;}
        input[type="text"],
        input[type="number"],
        input[type="date"],
        select,textarea{
            width:100%;
            padding:7px 10px;
            border-radius:8px;
            border:1px solid #d1d5db;
            font-size:14px;
            box-sizing:border-box;
            background:#f9fafb;
            outline:none;
            transition:all .15s ease;
        }
        input:focus,select:focus,textarea:focus{
            border-color:#3b82f6;
            box-shadow:0 0 0 1px rgba(59,130,246,.2);
            background:#fff;
        }
        textarea{resize:vertical;min-height:32px;font-size:13px;}
        table{width:100%;border-collapse:collapse;font-size:13px;}
        th,td{
            padding:6px 8px;
            border-bottom:1px solid #e5e7eb;
            text-align:left;
        }
        thead{background:#f9fafb;}
        .alert{
            border-radius:10px;
            padding:10px 12px;
            margin-bottom:12px;
            font-size:13px;
        }
        .alert-error{
            background:#fef2f2;
            border:1px solid #fecaca;
            color:#b91c1c;
        }
        .alert-success{
            background:#ecfdf3;
            border:1px solid #bbf7d0;
            color:#166534;
        }
        .btn-primary{
            border:none;
            border-radius:999px;
            padding:9px 18px;
            font-size:14px;
            font-weight:500;
            color:#f9fafb;
            background:linear-gradient(135deg,#22c55e,#16a34a);
            cursor:pointer;
            display:inline-flex;
            align-items:center;
            gap:6px;
            box-shadow:0 10px 20px rgba(22,163,74,.25);
            transition:transform .12s ease,box-shadow .12s ease,filter .12s ease;
        }
        .btn-primary:hover{
            transform:translateY(-1px);
            box-shadow:0 14px 28px rgba(22,163,74,.3);
            filter:brightness(1.02);
        }
        .btn-secondary{
            border-radius:999px;
            border:1px solid #d1d5db;
            background:#fff;
            color:#374151;
            padding:8px 14px;
            font-size:13px;
            cursor:pointer;
        }
        .btn-danger{
            border-radius:999px;
            border:1px solid #f97373;
            background:#ef4444;
            color:#fee2e2;
            padding:8px 14px;
            font-size:13px;
            cursor:pointer;
        }
        .btn-row{
            margin-top:10px;
            display:flex;
            justify-content:space-between;
            gap:10px;
            flex-wrap:wrap;
            align-items:center;
        }
        .badge{
            display:inline-block;
            padding:2px 8px;
            border-radius:999px;
            font-size:11px;
        }
        .badge-green{background:#dcfce7;color:#166534;}
        .badge-blue{background:#dbeafe;color:#1d4ed8;}

        .error-msg{
            font-size:11px;
            color:#b91c1c;
            margin-top:2px;
        }
        .input-error{
            border-color:#dc2626!important;
            background:#fef2f2!important;
        }
    </style>
</head>
<body>
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · LỄ TÂN</div>
        <div class="role">Đặt dịch vụ</div>
    </div>
    <div>
        <?php if ($user): ?>
            <span style="font-size:13px;margin-right:10px;">
                Xin chào, <strong><?php echo htmlspecialchars($user['Username']); ?></strong>
            </span>
        <?php endif; ?>
        <a href="index.php?controller=letan&action=index">Trang lễ tân</a>
        <a href="index.php?controller=auth&action=logout">Đăng xuất</a>
    </div>
</div>

<div class="container">
    <div class="page-title">
        <div>
            <h1>Đặt dịch vụ</h1>
            <span>Chọn giao dịch đã check-in, sau đó thêm dịch vụ cho từng phòng</span>
        </div>
        <a class="back-link" href="index.php?controller=letan&action=index">
            ← Quay lại trang chủ lễ tân
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <strong>Có lỗi xảy ra:</strong>
            <ul style="margin:6px 0 0 18px;padding:0;">
                <?php foreach ($errors as $e): ?>
                    <li><?php echo htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div id="clientErrors" class="alert alert-error" style="display:none;">
        <strong>Có lỗi xảy ra:</strong>
        <ul id="clientErrorsList" style="margin:6px 0 0 18px;padding:0;"></ul>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <!-- FORM 1: TÌM KIẾM -->
    <form method="post" id="formSearch">
        <div class="card">
            <h2>Tìm kiếm giao dịch</h2>
            <p class="caption">
                Nhập <strong>Mã giao dịch</strong> hoặc <strong>CMND/CCCD trưởng đoàn</strong> rồi nhấn <strong>Tìm kiếm</strong>.<br>
                Chỉ những giao dịch đã <span class="badge badge-green">Stayed</span> mới đặt được dịch vụ.
            </p>
            <div class="row">
                <div class="field">
                    <label>Mã giao dịch / CMND</label>
                    <input type="text"
                           id="search_keyword"
                           name="search_keyword"
                           value="<?php echo htmlspecialchars($searchKeyword); ?>"
                           placeholder="VD: 1001 hoặc 0123456789">
                    <div class="error-msg" id="err_search"></div>
                </div>
                <div class="field" style="flex:0 0 auto;display:flex;align-items:flex-end;gap:8px;">
                    <button type="submit" class="btn-primary" name="btn_action" value="search">
                        Tìm kiếm
                    </button>
                    <button type="submit" class="btn-secondary" name="btn_action" value="cancel">
                        Hủy bỏ
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- FORM 2: ĐẶT DỊCH VỤ -->
    <form method="post">
        <input type="hidden" name="search_keyword" value="<?php echo htmlspecialchars($searchKeyword); ?>">

        <div class="card">
            <h2>Thông tin giao dịch</h2>

            <?php if ($giaoDich): ?>
                <?php
                $tenTruongDoan = $giaoDich['TenKhachHang']
                    ?? $giaoDich['TenKH']
                    ?? $giaoDich['TenTruongDoan']
                    ?? $giaoDich['HoTenTruongDoan']
                    ?? '';

                $cccdTruongDoan = $giaoDich['CCCD']
                    ?? $giaoDich['CMND']
                    ?? $giaoDich['SoCMND']
                    ?? $giaoDich['CCCDTruongDoan']
                    ?? '';

                $sdtTruongDoan = $giaoDich['SDT']
                    ?? $giaoDich['Sdt']
                    ?? $giaoDich['SoDienThoai']
                    ?? '';
                ?>
                <input type="hidden" name="ma_giao_dich" value="<?php echo (int)$giaoDich['MaGiaoDich']; ?>">

                <div class="row">
                    <div class="field">
                        <label>Mã giao dịch</label>
                        <input type="text" value="<?php echo htmlspecialchars($giaoDich['MaGiaoDich']); ?>" disabled>
                    </div>
                    <div class="field">
                        <label>Trạng thái</label>
                        <input type="text" value="<?php echo htmlspecialchars($giaoDich['TrangThai']); ?>" disabled>
                    </div>
                    <div class="field">
                        <label>Tổng tiền hiện tại (đ)</label>
                        <input type="text"
                               value="<?php echo number_format($giaoDich['TongTien'] ?? 0, 0, ',', '.'); ?>"
                               disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label>Tên trưởng đoàn / khách chính</label>
                        <input type="text"
                               value="<?php echo htmlspecialchars($tenTruongDoan); ?>"
                               disabled>
                    </div>
                    <div class="field">
                        <label>CMND/CCCD trưởng đoàn</label>
                        <input type="text"
                               value="<?php echo htmlspecialchars($cccdTruongDoan); ?>"
                               disabled>
                    </div>
                    <div class="field">
                        <label>Số điện thoại trưởng đoàn</label>
                        <input type="text"
                               value="<?php echo htmlspecialchars($sdtTruongDoan); ?>"
                               disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label>Số phòng trong giao dịch</label>
                        <input type="text" value="<?php echo count($chiTietPhong); ?>" disabled>
                    </div>
                </div>

                <?php if (!empty($chiTietPhong)): ?>
                    <p class="caption" style="margin-top:4px;">
                        Danh sách phòng thuộc giao dịch:
                        <?php
                        $labels = [];
                        foreach ($chiTietPhong as $ct) {
                            $labels[] = 'P' . $ct['SoPhong'] . ' (' . $ct['TrangThai'] . ')';
                        }
                        echo htmlspecialchars(implode(', ', $labels));
                        ?>
                    </p>
                <?php endif; ?>

            <?php else: ?>
                <p class="caption">
                    Chưa có giao dịch nào được chọn. Vui lòng tìm kiếm ở phần trên.
                </p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Danh sách dịch vụ</h2>
            <p class="caption">
                Chọn dịch vụ, nhập số lượng và gán cho từng phòng. Để trống nếu không sử dụng.
            </p>

            <?php if ($giaoDich && !empty($dsDichVu) && !empty($chiTietPhong)): ?>
                <table>
                    <thead>
                    <tr>
                        <th style="width:32%;">Dịch vụ</th>
                        <th style="width:12%;">Đơn giá (đ)</th>
                        <th style="width:12%;">Số lượng</th>
                        <th style="width:18%;">Phòng</th>
                        <th>Ghi chú</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($dsDichVu as $dv): ?>
                        <?php
                        $maDV  = (int)$dv['MaDichVu'];
                        $name  = $dv['TenDichVu'] ?? ('Dịch vụ #' . $maDV);
                        $price = $dv['GiaDichVu'] ?? 0;
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($name); ?></strong>
                                <div style="font-size:11px;color:#6b7280;margin-top:2px;">
                                    Mã DV: <?php echo $maDV; ?>
                                    <?php if (!empty($dv['SoLuongToiDa'])): ?>
                                        · Tối đa <?php echo (int)$dv['SoLuongToiDa']; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?php echo number_format($price, 0, ',', '.'); ?></td>
                            <td>
                                <input type="number"
                                       name="services[<?php echo $maDV; ?>][so_luong]"
                                       min="0" value="0">
                            </td>
                            <td>
                                <select name="services[<?php echo $maDV; ?>][ma_phong]">
                                    <option value="0">-- Chọn phòng --</option>
                                    <?php foreach ($chiTietPhong as $ct): ?>
                                        <option value="<?php echo (int)$ct['MaPhong']; ?>">
                                            <?php echo 'P' . htmlspecialchars($ct['SoPhong']); ?>
                                            (<?php echo htmlspecialchars($ct['TrangThai']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <textarea name="services[<?php echo $maDV; ?>][note]"
                                          placeholder="Ghi chú thêm (nếu có)"></textarea>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="btn-row">
                    <button type="reset" class="btn-secondary">
                        Làm lại
                    </button>
                    <div style="display:flex;gap:8px;">
                        <button type="submit" class="btn-danger"
                                name="btn_action" value="cancel"
                                onclick="return confirm('Bạn chắc chắn muốn hủy? Các thông tin nhập sẽ không được lưu.');">
                            Hủy bỏ
                        </button>
                        <button type="submit" class="btn-primary"
                                name="btn_action" value="save"
                                onclick="return confirm('Xác nhận đặt dịch vụ cho giao dịch này?');">
                            Xác nhận đặt dịch vụ
                        </button>
                    </div>
                </div>
            <?php elseif ($giaoDich && empty($dsDichVu)): ?>
                <p class="caption">Hiện chưa có dịch vụ nào đang hoạt động.</p>
            <?php elseif ($giaoDich && empty($chiTietPhong)): ?>
                <p class="caption">Giao dịch không có phòng hợp lệ để đặt dịch vụ.</p>
            <?php else: ?>
                <p class="caption">Vui lòng chọn giao dịch trước khi đặt dịch vụ.</p>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const formSearch       = document.getElementById('formSearch');
    const searchInput      = document.getElementById('search_keyword');
    const clientErrors     = document.getElementById('clientErrors');
    const clientErrorsList = document.getElementById('clientErrorsList');
    const errSearch        = document.getElementById('err_search');

    function clearSearchError() {
        if (!searchInput) return;
        searchInput.classList.remove('input-error');
        if (errSearch) errSearch.textContent = '';
        if (clientErrors && clientErrorsList) {
            clientErrors.style.display = 'none';
            clientErrorsList.innerHTML = '';
        }
    }

    function showSearchError(msg) {
        if (!searchInput) return;

        searchInput.classList.add('input-error');
        if (errSearch) errSearch.textContent = msg;

        if (clientErrors && clientErrorsList) {
            clientErrorsList.innerHTML = '';
            const li = document.createElement('li');
            li.textContent = msg;
            clientErrorsList.appendChild(li);
            clientErrors.style.display = 'block';
        }
    }

    function validateSearchLive() {
        const v = searchInput.value.trim();
        clearSearchError();

        if (v !== '' && !/^\d+$/.test(v)) {
            showSearchError('Dữ liệu nhập không hợp lệ. Chỉ nhập số (mã giao dịch hoặc CMND/CCCD).');
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', validateSearchLive);
        searchInput.addEventListener('blur', validateSearchLive);
    }

    if (formSearch && searchInput) {
        formSearch.addEventListener('submit', function (e) {
            clearSearchError();

            const v = searchInput.value.trim();
            let msg = '';

            if (!v) {
                msg = 'Vui lòng nhập thông tin tìm kiếm (mã giao dịch hoặc CMND/CCCD).';
            } else if (!/^\d+$/.test(v)) {
                msg = 'Dữ liệu nhập không hợp lệ. Chỉ nhập số (mã giao dịch hoặc CMND/CCCD).';
            }

            if (msg) {
                e.preventDefault();
                showSearchError(msg);
                window.scrollTo({top: 0, behavior: 'smooth'});
            }
        });
    }
});
</script>
</body>
</html>