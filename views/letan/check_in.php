<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;

$errors         = $errors         ?? [];
$success        = $success        ?? null;
$giaoDich       = $giaoDich       ?? null;
$chiTiet        = $chiTiet        ?? [];
$searchMaGD     = $searchMaGD     ?? '';
$tenTruongDoan  = $tenTruongDoan  ?? '';
$cmndTruongDoan = $cmndTruongDoan ?? '';
$soThanhVien    = $soThanhVien    ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Check-in khách hàng</title>
    <style>
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
            max-width:1180px;
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
        .card h2{margin:0 0 10px;font-size:16px;color:#111827;}
        .card p.caption{margin:0 0 10px;font-size:13px;color:#6b7280;}
        .row{display:flex;gap:12px;flex-wrap:wrap;}
        .field{margin-bottom:10px;flex:1;}
        label{display:block;font-size:13px;color:#374151;margin-bottom:4px;}
        input[type="text"],
        input[type="number"],
        input[type="date"]{
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
        input:focus{
            border-color:#3b82f6;
            box-shadow:0 0 0 1px rgba(59,130,246,.2);
            background:#fff;
        }
        table{width:100%;border-collapse:collapse;font-size:13px;margin-top:8px;}
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
            border:1px solid #fecaca;
            background:#fee2e2;
            color:#b91c1c;
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
        .badge-blue{background:#dbeafe;color:#1d4ed8;}
        .badge-green{background:#dcfce7;color:#166534;}
        .badge-red{background:#fee2e2;color:#b91c1c;}
        .scope-group{
            font-size:13px;
            margin-top:6px;
            color:#374151;
        }
        .scope-group label{
            display:inline-flex;
            align-items:center;
            margin-right:12px;
            cursor:pointer;
        }

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
        <div class="role">Check-in khách hàng</div>
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
            <h1>Check-in</h1>
            <span>Tìm giao dịch → chọn phòng → kiểm tra giấy tờ → hoàn tất check-in</span>
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

    <!-- Lỗi phía client cho phần tìm kiếm -->
    <div id="clientErrors" class="alert alert-error" style="display:none;">
        <strong>Có lỗi xảy ra:</strong>
        <ul id="clientErrorsList" style="margin:6px 0 0 18px;padding:0;"></ul>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <!-- FORM 1: TÌM KIẾM GIAO DỊCH -->
    <form method="post" id="formSearch">
        <div class="card">
            <h2>Tìm kiếm giao dịch</h2>
            <p class="caption">
                Nhập <strong>Mã giao dịch</strong> hoặc <strong>CMND/CCCD trưởng đoàn</strong> (chỉ nhập số) để tra cứu.
            </p>
            <div class="row">
                <div class="field">
                    <label>Mã giao dịch / CMND/CCCD</label>
                    <input type="text"
                           id="search_ma_gd"
                           name="search_ma_gd"
                           value="<?php echo htmlspecialchars($searchMaGD); ?>"
                           placeholder="VD: 7 hoặc 22653661123">
                    <div class="error-msg" id="err_search"></div>
                </div>
                <div class="field" style="flex:0 0 auto;display:flex;align-items:flex-end;gap:8px;">
                    <button type="submit" class="btn-primary" name="btn_action" value="search">
                        Tìm kiếm
                    </button>
                    <button type="submit" class="btn-secondary" name="btn_action" value="back">
                        Quay lại
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- FORM 2: CHECK-IN -->
    <form method="post" id="formCheckin">
        <div class="card">
            <h2>Thông tin giao dịch đoàn</h2>

            <?php if ($giaoDich && !empty($chiTiet)): ?>
                <input type="hidden" name="ma_giao_dich" value="<?php echo (int)$giaoDich['MaGiaoDich']; ?>">
                <input type="hidden" name="search_ma_gd" value="<?php echo htmlspecialchars($searchMaGD); ?>">

                <div class="row">
                    <div class="field">
                        <label>Mã giao dịch</label>
                        <input type="text"
                               value="<?php echo htmlspecialchars($giaoDich['MaGiaoDich']); ?>"
                               disabled>
                    </div>
                    <div class="field">
                        <label>Trạng thái giao dịch</label>
                        <input type="text"
                               value="<?php echo htmlspecialchars($giaoDich['TrangThai'] ?? ''); ?>"
                               disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label>Tên trưởng đoàn</label>
                        <input type="text" value="<?php echo htmlspecialchars($tenTruongDoan); ?>" disabled>
                    </div>
                    <div class="field">
                        <label>CMND/CCCD trưởng đoàn</label>
                        <input type="text" value="<?php echo htmlspecialchars($cmndTruongDoan); ?>" disabled>
                    </div>
                    <div class="field">
                        <label>Số lượng thành viên</label>
                        <input type="number" value="<?php echo (int)$soThanhVien; ?>" disabled>
                    </div>
                </div>

                <h2>Danh sách chi tiết giao dịch</h2>
                <table>
                    <thead>
                    <tr>
                        <th>Họ tên khách</th>
                        <th>CMND/CCCD</th>
                        <th>Mã phòng</th>
                        <th>Số phòng</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Trạng thái</th>
                        <th>Chọn check-in</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($chiTiet as $ct): ?>
                        <?php
                        $st  = $ct['TrangThai'];
                        $cls = 'badge-blue';
                        if ($st === 'Booked') $cls = 'badge-blue';
                        if (in_array($st, ['Stayed','DangO'])) $cls = 'badge-green';
                        if (in_array($st, ['DaHuy','Cancelled'])) $cls = 'badge-red';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ct['TenKhach'] ?? $tenTruongDoan); ?></td>
                            <td><?php echo htmlspecialchars($ct['CCCD'] ?? $cmndTruongDoan); ?></td>
                            <td><?php echo (int)$ct['MaPhong']; ?></td>
                            <td><?php echo htmlspecialchars($ct['SoPhong']); ?></td>
                            <td><?php echo htmlspecialchars(substr($ct['NgayNhanDuKien'],0,10)); ?></td>
                            <td><?php echo htmlspecialchars(substr($ct['NgayTraDuKien'],0,10)); ?></td>
                            <td>
                                <span class="badge <?php echo $cls; ?>">
                                    <?php echo htmlspecialchars($st); ?>
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <?php if ($st === 'Booked'): ?>
                                    <input type="checkbox"
                                           name="phong_checkin[]"
                                           value="<?php echo (int)$ct['MaPhong']; ?>">
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="scope-group">
                    <label>
                        <input type="radio" id="check_scope_all" name="check_scope" value="all" checked>
                        Check-in toàn bộ phòng (đang Booked)
                    </label>
                    <label>
                        <input type="radio" id="check_scope_partial" name="check_scope" value="partial">
                        Chỉ check-in các phòng được chọn ở bảng trên
                    </label>
                </div>

                <div style="margin-top:8px;font-size:13px;">
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                        <input type="checkbox" name="xac_nhan_giay_to" value="1">
                        Tôi xác nhận đã kiểm tra giấy tờ tùy thân hợp lệ của khách / trưởng đoàn.
                    </label>
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn-danger"
                            name="btn_action" value="abort"
                            onclick="return confirm('Bạn chắc chắn muốn hủy tiến trình check-in?');">
                        Hủy check-in
                    </button>
                    <div style="display:flex;gap:8px;">
                        <button type="reset" class="btn-secondary">
                            Làm lại
                        </button>
                        <button type="submit" class="btn-primary"
                                name="btn_action" value="confirm"
                                onclick="return confirm('Xác nhận hoàn tất check-in cho các phòng đã chọn?');">
                            ✔ Hoàn tất Check-in
                        </button>
                    </div>
                </div>

            <?php else: ?>
                <p class="caption">
                    Chưa có giao dịch nào được chọn. Vui lòng tra cứu giao dịch ở phần trên để tiếp tục check-in.
                </p>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ====== VALIDATE Ô TÌM KIẾM (chỉ cho nhập số) ======
    const formSearch       = document.getElementById('formSearch');
    const inputSearch      = document.getElementById('search_ma_gd');
    const errSearch        = document.getElementById('err_search');
    const clientErrors     = document.getElementById('clientErrors');
    const clientErrorsList = document.getElementById('clientErrorsList');

    function clearFieldError() {
        if (!inputSearch || !errSearch) return;
        inputSearch.classList.remove('input-error');
        errSearch.textContent = '';
    }

    function setFieldError(msg) {
        if (!inputSearch || !errSearch) return;
        inputSearch.classList.add('input-error');
        errSearch.textContent = msg;
    }

    function clearClientErrorsBox() {
        if (!clientErrors || !clientErrorsList) return;
        clientErrors.style.display = 'none';
        clientErrorsList.innerHTML = '';
    }

    function pushClientError(msg) {
        if (!clientErrors || !clientErrorsList) return;
        const li = document.createElement('li');
        li.textContent = msg;
        clientErrorsList.appendChild(li);
        clientErrors.style.display = 'block';
    }

    // Validate live: nhập chữ / khoảng trắng / ký tự đặc biệt là báo ngay
    function validateSearchLive() {
        const v = (inputSearch?.value || '').trim();
        clearFieldError();
        clearClientErrorsBox();

        if (v === '') {
            // cho phép trống khi đang gõ, chỉ bắt khi submit
            return;
        }

        if (!/^\d+$/.test(v)) {
            const msg = 'Dữ liệu không hợp lệ. Chỉ được nhập số (mã giao dịch hoặc CMND/CCCD).';
            setFieldError(msg);
            pushClientError(msg);
        }
    }

    if (inputSearch) {
        inputSearch.addEventListener('input', validateSearchLive);
        inputSearch.addEventListener('blur', validateSearchLive);
    }

    if (formSearch && inputSearch) {
        formSearch.addEventListener('submit', function (e) {
            clearFieldError();
            clearClientErrorsBox();

            const v = inputSearch.value.trim();
            let hasError = false;

            if (!v) {
                const msg = 'Vui lòng nhập mã giao dịch hoặc CMND/CCCD để tìm kiếm.';
                hasError = true;
                setFieldError(msg);
                pushClientError(msg);
            } else if (!/^\d+$/.test(v)) {
                const msg = 'Dữ liệu không hợp lệ. Chỉ được nhập số (mã giao dịch hoặc CMND/CCCD).';
                hasError = true;
                setFieldError(msg);
                pushClientError(msg);
            }

            if (hasError) {
                e.preventDefault();
                window.scrollTo({top: 0, behavior: 'smooth'});
            }
        });
    }

    // ====== LOGIC CHECK TOÀN BỘ / MỘT PHẦN CHO PHÒNG (giống các chức năng khác) ======
    const radioAll     = document.getElementById('check_scope_all');
    const radioPartial = document.getElementById('check_scope_partial');

    function getRoomCheckboxes() {
        return Array.from(document.querySelectorAll('input[name="phong_checkin[]"]'));
    }

    function checkAllRooms() {
        getRoomCheckboxes().forEach(cb => {
            // chỉ tích những ô đang hiển thị (phòng Booked)
            cb.checked = true;
        });
    }

    function uncheckAllRooms() {
        getRoomCheckboxes().forEach(cb => {
            cb.checked = false;
        });
    }

    function applyScopeFromRadio() {
        const selected = document.querySelector('input[name="check_scope"]:checked');
        if (!selected) return;
        if (selected.value === 'all') {
            // giống logic "Hủy toàn bộ" bên màn hủy: tích hết
            checkAllRooms();
        } else if (selected.value === 'partial') {
            // giống logic "Hủy một phần": bỏ tích hết, để user chọn lại
            uncheckAllRooms();
        }
    }

    if (radioAll) {
        radioAll.addEventListener('change', function () {
            if (radioAll.checked) {
                applyScopeFromRadio();
            }
        });
    }
    if (radioPartial) {
        radioPartial.addEventListener('change', function () {
            if (radioPartial.checked) {
                applyScopeFromRadio();
            }
        });
    }

    // Khi trang load lần đầu, nếu radio "toàn bộ" đang được chọn thì tích hết luôn
    applyScopeFromRadio();
});
</script>
</body>
</html>