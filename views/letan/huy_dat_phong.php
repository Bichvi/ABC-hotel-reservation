<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user          = $_SESSION['user'] ?? null;
$errors        = $errors        ?? [];
$success       = $success       ?? null;
$searchKeyword = $searchKeyword ?? '';
$giaoDich      = $giaoDich      ?? null;
$chiTiet       = $chiTiet       ?? [];
$allowCancel   = $allowCancel   ?? false;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hủy đặt phòng - Lễ tân</title>
    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f3f4f6;
        }
        .topbar {
            background: #0f172a;
            color: #e5e7eb;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar .brand { font-weight: 600; letter-spacing: .03em; }
        .topbar .role { font-size: 13px; opacity: .8; }
        .topbar a {
            font-size: 13px;
            color: #e5e7eb;
            text-decoration: none;
            margin-left: 12px;
        }
        .container {
            max-width: 1180px;
            margin: 24px auto 40px;
            padding: 0 16px;
        }
        .page-title {
            display: flex;
            align-items: baseline;
            gap: 8px;
            margin-bottom: 16px;
            justify-content: space-between;
        }
        .page-title h1 { margin: 0; font-size: 24px; color: #111827; }
        .page-title span { font-size: 13px; color: #6b7280; }
        .back-link {
            font-size: 13px;
            text-decoration: none;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 14px 16px;
            box-shadow: 0 10px 25px rgba(15,23,42,0.05);
            border: 1px solid #e5e7eb;
            margin-bottom: 12px;
        }
        .card h2 { margin: 0 0 10px; font-size: 16px; color: #111827; }
        .card p.caption { margin: 0 0 10px; font-size: 13px; color: #6b7280; }

        .row { display: flex; gap: 12px; flex-wrap: wrap; }
        .field { margin-bottom: 10px; flex: 1; min-width: 200px; }

        label { display: block; font-size: 13px; color: #374151; margin-bottom: 4px; }

        input[type="text"]{
            width: 100%;
            padding: 7px 10px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 14px;
            box-sizing: border-box;
            background: #f9fafb;
            outline: none;
            transition: all .15s ease;
        }
        input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px rgba(59,130,246,0.2);
            background: #ffffff;
        }

        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }
        thead { background: #f9fafb; }

        .alert {
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 12px;
            font-size: 13px;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }
        .alert-success {
            background: #ecfdf3;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .btn-primary {
            border: none;
            border-radius: 999px;
            padding: 9px 18px;
            font-size: 14px;
            font-weight: 500;
            color: #f9fafb;
            background: linear-gradient(135deg,#22c55e,#16a34a);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 10px 20px rgba(22,163,74,0.25);
            transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(22,163,74,0.30);
            filter: brightness(1.02);
        }
        .btn-secondary {
            border-radius: 999px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #374151;
            padding: 8px 14px;
            font-size: 13px;
            cursor: pointer;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
        }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-red { background: #fee2e2; color: #b91c1c; }

        .btn-row {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        /* client-side error cho ô tìm kiếm */
        .input-error {
            border-color: #dc2626 !important;
            background: #fef2f2 !important;
        }
        .error-msg {
            font-size: 11px;
            color: #b91c1c;
            margin-top: 2px;
        }
        .hint {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }
    </style>
</head>
<body>
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · LỄ TÂN</div>
        <div class="role">Hủy đặt phòng</div>
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
            <h1>Hủy đặt phòng</h1>
            <span>Tìm giao dịch → chọn phòng (hoặc toàn bộ) → nhập lý do → xác nhận hủy</span>
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

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <!-- FORM DUY NHẤT: dùng btn_action để phân nhánh search / cancel -->
    <form method="post" id="formHuyPhong">
        <!-- BOX 1: TÌM KIẾM -->
        <div class="card">
            <h2>Tìm kiếm giao dịch</h2>
            <p class="caption">
                Nhập <strong>Mã giao dịch</strong> hoặc <strong>CMND/CCCD trưởng đoàn</strong>, sau đó nhấn <strong>Tìm kiếm</strong>.
            </p>
            <div class="row">
                <div class="field">
                    <label>Mã giao dịch / CMND</label>
                    <input type="text"
                           id="search_keyword"
                           name="search_keyword"
                           value="<?php echo htmlspecialchars($searchKeyword); ?>"
                           placeholder="VD: 1 hoặc 0123456789">
                    <div class="error-msg" id="err_search"></div>
                    <div class="hint">
                        - Mã giao dịch: chuỗi số ngắn (VD: 1, 15, 102).<br>
                        - CMND/CCCD: 9–12 chữ số.
                    </div>
                </div>
                <div class="field" style="flex:0 0 auto;display:flex;align-items:flex-end;gap:8px;">
                    <button type="submit" class="btn-primary" name="btn_action" value="search">
                        Tìm kiếm
                    </button>
                    <button type="submit" class="btn-secondary" name="btn_action" value="back">
                        Quay lại lễ tân
                    </button>
                </div>
            </div>
        </div>

        <!-- BOX 2: THÔNG TIN GIAO DỊCH -->
        <div class="card">
            <h2>Thông tin giao dịch</h2>
            <?php if ($giaoDich): ?>
                <div class="row">
                    <div class="field">
                        <label>Mã giao dịch</label>
                        <input type="text" value="<?php echo htmlspecialchars($giaoDich['MaGiaoDich']); ?>" disabled>
                    </div>
                    <div class="field">
                        <label>CMND/CCCD trưởng đoàn</label>
                        <input type="text" value="<?php echo htmlspecialchars($giaoDich['CCCD'] ?? ''); ?>" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="field">
                        <label>Tên trưởng đoàn</label>
                        <input type="text" value="<?php echo htmlspecialchars($giaoDich['TenKH'] ?? ''); ?>" disabled>
                    </div>
                    <div class="field">
                        <label>Trạng thái giao dịch</label>
                        <input type="text" value="<?php echo htmlspecialchars($giaoDich['TrangThai'] ?? ''); ?>" disabled>
                    </div>
                </div>
                <!-- hidden để controller đọc lại khi hủy -->
                <input type="hidden" name="ma_giao_dich" value="<?php echo (int)$giaoDich['MaGiaoDich']; ?>">
            <?php else: ?>
                <p class="caption">Chưa có giao dịch nào được chọn. Vui lòng tìm kiếm ở trên.</p>
            <?php endif; ?>
        </div>

        <!-- BOX 3: DANH SÁCH CHI TIẾT + HỦY -->
        <div class="card">
            <h2>Danh sách chi tiết giao dịch</h2>
            <p class="caption">
                Chọn các phòng cần hủy hoặc chọn <strong>Hủy toàn bộ giao dịch</strong>.
            </p>

            <?php if ($giaoDich && !empty($chiTiet)): ?>
                <table>
                    <thead>
                    <tr>
                        <th>Phòng</th>
                        <th>Loại phòng</th>
                        <th>Ngày nhận dự kiến</th>
                        <th>Ngày trả dự kiến</th>
                        <th>Trạng thái</th>
                        <th style="width:70px;">Chọn</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($chiTiet as $ct): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ct['SoPhong']); ?></td>
                            <td><?php echo htmlspecialchars($ct['LoaiPhong']); ?></td>
                            <td><?php echo htmlspecialchars($ct['NgayNhanDuKien']); ?></td>
                            <td><?php echo htmlspecialchars($ct['NgayTraDuKien']); ?></td>
                            <td>
                                <?php
                                $st  = $ct['TrangThai'];
                                $cls = 'badge-blue';
                                if ($st === ChiTietGiaoDich::STATUS_CANCELLED || $st === 'Cancelled') {
                                    $cls = 'badge-red';
                                } elseif (in_array($st, ['Stayed','CheckedIn'])) {
                                    $cls = 'badge-green';
                                }
                                ?>
                                <span class="badge <?php echo $cls; ?>">
                                    <?php echo htmlspecialchars($st); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($st !== ChiTietGiaoDich::STATUS_CANCELLED && $st !== 'Cancelled'): ?>
                                    <input type="checkbox"
                                           name="phong_cancel[]"
                                           class="chk-room"
                                           value="<?php echo (int)$ct['MaPhong']; ?>">
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="margin-top:8px;font-size:13px;">
                    <label>
                        <input type="radio" name="cancel_scope" value="all" id="scope_all" checked>
                        Hủy <strong>toàn bộ</strong> giao dịch (tự chọn tất cả phòng còn hiệu lực)
                    </label>
                    &nbsp;&nbsp;
                    <label>
                        <input type="radio" name="cancel_scope" value="partial" id="scope_partial">
                        Chỉ hủy <strong>phòng được chọn</strong>
                    </label>
                </div>

                <div class="field" style="margin-top:12px;">
                    <label>Lý do hủy (bắt buộc)</label>
                    <input type="text" id="ly_do_huy" name="ly_do_huy"
                           placeholder="Ví dụ: Khách đổi kế hoạch, đặt nhầm ngày...">
                </div>

                <?php if (!$allowCancel): ?>
                    <div class="hint" style="color:#b91c1c;">
                        * Giao dịch này không đáp ứng điều kiện cho phép hủy (VD: đã check-in, đã quá hạn, đã thanh toán...). 
                        Bạn vẫn có thể xem chi tiết nhưng hệ thống sẽ không cho phép lưu khi xác nhận hủy.
                    </div>
                <?php endif; ?>

                <div class="btn-row">
                    <button type="reset" class="btn-secondary">
                        Làm lại
                    </button>
                    <button type="submit" class="btn-primary"
                            name="btn_action" value="cancel"
                            <?php echo $allowCancel ? '' : 'disabled'; ?>
                            onclick="return confirm('Bạn có chắc chắn muốn hủy theo lựa chọn hiện tại không?');">
                        Xác nhận hủy
                    </button>
                </div>
            <?php else: ?>
                <p class="caption">
                    Chưa có dữ liệu chi tiết. Vui lòng tìm kiếm giao dịch trước.
                </p>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formHuyPhong');
    const searchInput = document.getElementById('search_keyword');
    const errSearch = document.getElementById('err_search');

    const scopeAll = document.getElementById('scope_all');
    const scopePartial = document.getElementById('scope_partial');

    function getRoomCheckboxes() {
        return document.querySelectorAll('.chk-room');
    }

    // --- VALIDATE Ô TÌM KIẾM ---
    function clearSearchError() {
        if (!searchInput || !errSearch) return;
        searchInput.classList.remove('input-error');
        errSearch.textContent = '';
    }

    function setSearchError(msg) {
        if (!searchInput || !errSearch) return;
        searchInput.classList.add('input-error');
        errSearch.textContent = msg;
    }

    function validateSearch() {
        if (!searchInput) return true;
        const v = searchInput.value.trim();
        clearSearchError();

        if (!v) {
            setSearchError('Vui lòng nhập mã giao dịch hoặc CMND/CCCD để tìm kiếm.');
            return false;
        }
        if (!/^\d+$/.test(v)) {
            setSearchError('Mã giao dịch/CMND chỉ được phép chứa chữ số.');
            return false;
        }
        if (v.length > 12) {
            setSearchError('Dữ liệu không hợp lệ (tối đa 12 chữ số).');
            return false;
        }
        // Nếu là CMND/CCCD thì 9–12 số là hợp lệ; nếu mã GD thì 1–8 số cũng ok.
        return true;
    }

    if (searchInput) {
        searchInput.addEventListener('blur', validateSearch);
        searchInput.addEventListener('input', function () {
            // nhập lại thì kiểm tra và clear / báo lỗi luôn
            validateSearch();
        });
    }

    // --- HÀNH VI RADIO "HỦY TOÀN BỘ" / "HỦY MỘT PHẦN" ---
    function applyCancelScope() {
        const cbs = getRoomCheckboxes();
        if (!cbs.length) return;

        if (scopeAll && scopeAll.checked) {
            cbs.forEach(function (cb) {
                cb.checked = true;
            });
        } else if (scopePartial && scopePartial.checked) {
            cbs.forEach(function (cb) {
                cb.checked = false;
            });
        }
    }

    if (scopeAll) scopeAll.addEventListener('change', applyCancelScope);
    if (scopePartial) scopePartial.addEventListener('change', applyCancelScope);

    // Gọi lần đầu sau khi trang load (mặc định scope_all checked → tick hết)
    applyCancelScope();

    // --- SUBMIT FORM ---
    if (form) {
        form.addEventListener('submit', function (e) {
            const submitter = e.submitter || {};
            const action = submitter.value || '';

            // Nếu bấm TÌM KIẾM thì chỉ validate ô search
            if (action === 'search') {
                if (!validateSearch()) {
                    e.preventDefault();
                    return;
                }
            }

            // Nếu bấm XÁC NHẬN HỦY thì kiểm tra thêm:
            if (action === 'cancel') {
                // Nếu đang ở chế độ partial mà không chọn phòng nào
                if (scopePartial && scopePartial.checked) {
                    const cbs = getRoomCheckboxes();
                    let anyChecked = false;
                    cbs.forEach(function (cb) {
                        if (cb.checked) anyChecked = true;
                    });
                    if (!anyChecked) {
                        alert('Bạn đang chọn hủy một phần. Vui lòng tích ít nhất một phòng cần hủy.');
                        e.preventDefault();
                        return;
                    }
                }
                // Lý do hủy bắt buộc
                const lyDo = document.getElementById('ly_do_huy');
                if (lyDo && lyDo.value.trim() === '') {
                    alert('Vui lòng nhập lý do hủy.');
                    lyDo.focus();
                    e.preventDefault();
                    return;
                }
            }
        });
    }
});
</script>
</body>
</html>