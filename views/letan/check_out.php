<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user          = $_SESSION['user'] ?? null;
$errors        = $errors        ?? [];
$success       = $success       ?? null;
$searchKeyword = $searchKeyword ?? '';
$giaoDich      = $giaoDich      ?? null;
$chiTietPhong  = $chiTietPhong  ?? [];
$chiTietDV     = $chiTietDV     ?? [];
$form          = $form          ?? [
    'selected_rooms' => [],
    'late_fee'       => 0,
    'has_damage'     => 'none',   // GIÁ TRỊ NÀY NÊN ĐƯỢC SET TỪ CSDL Ở CONTROLLER
    'damage_note'    => '',
    'damage_fee'     => 0,
    'payment_method' => '',
    'payment_status' => 'unpaid',
];
// Tóm tắt mặc định = 0, vì mình sẽ tính lại bằng JS theo phòng được chọn
$summary       = $summary ?? [
    'room_total'    => 0,
    'service_total' => 0,
    'late_fee'      => 0,
    'damage_fee'    => 0,
    'grand_total'   => 0,
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Check-out - Trả phòng</title>
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
        .brand{font-weight:600;letter-spacing:.03em;}
        .role{font-size:13px;opacity:.8;}
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
            box-shadow:0 10px 25px rgba(15,23,42,.06);
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
        select{
            width:100%;
            padding:7px 10px;
            border-radius:8px;
            border:1px solid #d1d5db;
            font-size:14px;
            background:#f9fafb;
            box-sizing:border-box;
            outline:none;
            transition:all .15s ease;
        }
        input:focus,select:focus{
            border-color:#3b82f6;
            box-shadow:0 0 0 1px rgba(59,130,246,.2);
            background:#fff;
        }
        .input-error{border-color:#dc2626!important;background:#fef2f2!important;}
        .error-msg{
            font-size:11px;
            color:#b91c1c;
            margin-top:2px;
        }
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
        .summary-box{
            background:#f9fafb;
            border-radius:10px;
            padding:10px 12px;
            border:1px dashed #d1d5db;
            font-size:13px;
        }
        .summary-row{
            display:flex;
            justify-content:space-between;
            margin-bottom:4px;
        }
        .summary-row strong{color:#111827;}
        .summary-row span{color:#374151;}
        .summary-total{
            font-size:15px;
            font-weight:700;
            margin-top:6px;
            border-top:1px dashed #d1d5db;
            padding-top:6px;
            display:flex;
            justify-content:space-between;
        }
        .qr-box{
            width:96px;
            height:96px;
            border-radius:10px;
            border:1px dashed #9ca3af;
            display:flex;
            align-items:center;
            justify-content:center;
            background:#f9fafb;
            overflow:hidden;
            transition:opacity .2s ease;
        }
        .qr-box img{
            max-width:100%;
            max-height:100%;
            border-radius:8px;
            display:block;
        }
    </style>
</head>
<body>
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · LỄ TÂN</div>
        <div class="role">Check-out · Trả phòng</div>
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
            <h1>Check-out</h1>
            <span>Tìm giao dịch → chọn phòng trả → kiểm tra chi phí → thanh toán & trả phòng</span>
        </div>
        <a class="back-link" href="index.php?controller=letan&action=index">← Quay lại trang chủ lễ tân</a>
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

    <!-- Lỗi phía client cho ô tìm kiếm -->
    <div id="clientErrors" class="alert alert-error" style="display:none;">
        <strong>Có lỗi xảy ra:</strong>
        <ul id="clientErrorsList" style="margin:6px 0 0 18px;padding:0;"></ul>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <!-- FORM TÌM KIẾM -->
    <form method="post" id="formSearch">
        <div class="card">
            <h2>Tìm kiếm giao dịch</h2>
            <p class="caption">
                Nhập <strong>Mã giao dịch</strong> hoặc <strong>CMND/CCCD trưởng đoàn</strong> (chỉ nhập số) rồi nhấn <strong>Tìm kiếm</strong>.
            </p>
            <div class="row">
                <div class="field">
                    <label>Mã giao dịch / CMND</label>
                    <input type="text"
                           id="search_keyword"
                           name="search_keyword"
                           value="<?php echo htmlspecialchars($searchKeyword); ?>"
                           placeholder="VD: 15 hoặc 22653661123">
                    <div class="error-msg" id="err_search"></div>
                </div>
                <div class="field" style="flex:0 0 auto;display:flex;align-items:flex-end;gap:8px;">
                    <button type="submit" class="btn-secondary" name="btn_action" value="search">
                        Tìm kiếm
                    </button>
                    <button type="submit" class="btn-secondary" name="btn_action" value="cancel">
                        Hủy thao tác
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- FORM CHECK-OUT -->
    <form method="post" id="formCheckout">
        <div class="card">
            <h2>Thông tin giao dịch & chi tiết phòng</h2>

            <?php if ($giaoDich && !empty($chiTietPhong)): ?>
                <input type="hidden" name="ma_giao_dich" value="<?php echo (int)$giaoDich['MaGiaoDich']; ?>">

                <div class="row">
                    <div class="field">
                        <label>Mã giao dịch</label>
                        <input type="text" value="<?php echo htmlspecialchars($giaoDich['MaGiaoDich']); ?>" disabled>
                    </div>
                    <div class="field">
                        <label>Trạng thái giao dịch</label>
                        <input type="text" value="<?php echo htmlspecialchars($giaoDich['TrangThai']); ?>" disabled>
                    </div>
                    <div class="field">
                        <label>Khách hàng / Trưởng đoàn</label>
                        <input type="text"
                               value="<?php echo htmlspecialchars($giaoDich['TenKhachHang'] ?? $giaoDich['TenKH'] ?? ''); ?>"
                               disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label>CMND/CCCD</label>
                        <input type="text"
                               value="<?php echo htmlspecialchars($giaoDich['CCCD'] ?? ''); ?>" disabled>
                    </div>
                    <div class="field">
                        <label>Số điện thoại</label>
                        <input type="text"
                               value="<?php echo htmlspecialchars($giaoDich['SDT'] ?? ''); ?>" disabled>
                    </div>
                </div>

                <h2 style="margin-top:6px;">Danh sách phòng đang ở (Stayed)</h2>
                <table>
                    <thead>
                    <tr>
                        <th>Chọn</th>
                        <th>Số phòng</th>
                        <th>Loại</th>
                        <th>Ngày nhận</th>
                        <th>Ngày trả dự kiến</th>
                        <th>Số người</th>
                        <th>Tiền phòng</th>
                        <th>Trạng thái</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($chiTietPhong as $ct): ?>
                        <?php
                        $maP       = (int)$ct['MaPhong'];
                        $checked   = in_array($maP, $form['selected_rooms'], true);
                        $tienPhong = (float)($ct['ThanhTien'] ?? 0);
                        ?>
                        <tr data-room-row="1"
                            data-room-id="<?php echo $maP; ?>"
                            data-room-amount="<?php echo $tienPhong; ?>">
                            <td>
                                <input type="checkbox" name="rooms[]"
                                       value="<?php echo $maP; ?>"
                                    <?php echo $checked ? 'checked' : ''; ?>>
                            </td>
                            <td><?php echo htmlspecialchars($ct['SoPhong']); ?></td>
                            <td><?php echo htmlspecialchars($ct['LoaiPhong']); ?></td>
                            <td><?php echo htmlspecialchars(substr($ct['NgayNhanDuKien'],0,16)); ?></td>
                            <td><?php echo htmlspecialchars(substr($ct['NgayTraDuKien'],0,16)); ?></td>
                            <td><?php echo (int)$ct['SoNguoi']; ?></td>
                            <td><?php echo number_format($tienPhong,0,',','.'); ?> đ</td>
                            <td>
                                <span class="badge badge-green">
                                    <?php echo htmlspecialchars($ct['TrangThai']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if (!empty($chiTietDV)): ?>
                    <h2 style="margin-top:12px;">Dịch vụ đã sử dụng</h2>
                    <table>
                        <thead>
                        <tr>
                            <th>Tên dịch vụ</th>
                            <th>Phòng</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($chiTietDV as $dv): ?>
                            <?php
                            $dvAmount = (float)$dv['ThanhTien'];
                            $dvRoomId = $dv['MaPhong'] ?? null; // có thể null nếu dịch vụ chung
                            ?>
                            <tr data-service-row="1"
                                data-service-room-id="<?php echo $dvRoomId ? (int)$dvRoomId : 0; ?>"
                                data-service-amount="<?php echo $dvAmount; ?>">
                                <td><?php echo htmlspecialchars($dv['TenDichVu']); ?></td>
                                <td><?php echo htmlspecialchars($dv['SoPhong'] ?? '-'); ?></td>
                                <td><?php echo (int)$dv['SoLuong']; ?></td>
                                <td><?php echo number_format($dv['GiaBan'],0,',','.'); ?> đ</td>
                                <td><?php echo number_format($dvAmount,0,',','.'); ?> đ</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <div class="row" style="margin-top:14px;">
                    <div class="field">
                        <label>Phụ thu trả phòng muộn (nếu có)</label>
                        <input type="number" name="late_fee" id="late_fee" min="0"
                               value="<?php echo htmlspecialchars($form['late_fee']); ?>">
                    </div>

                    <!-- Tình trạng phòng lấy từ CSDL, chỉ hiển thị, KHÔNG CHO CHỌN -->
                    <div class="field">
                        <label>Tình trạng phòng</label>
                        <?php
                        $damageCode = $form['has_damage'] ?? 'none';
                        $damageTextMap = [
                            'none'  => 'Không hư hại',
                            'light' => 'Hư hại nhẹ',
                            'heavy' => 'Hư hại nặng',
                        ];
                        $damageText = $damageTextMap[$damageCode] ?? $damageCode;
                        ?>
                        <input type="text"
                               value="<?php echo htmlspecialchars($damageText); ?>"
                               disabled>
                        <!-- hidden để vẫn post giá trị code lên controller -->
                        <input type="hidden" name="has_damage"
                               value="<?php echo htmlspecialchars($damageCode); ?>">
                    </div>

                    <div class="field">
                        <label>Phí bồi thường (nếu có)</label>
                        <input type="number" name="damage_fee" id="damage_fee" min="0"
                               value="<?php echo htmlspecialchars($form['damage_fee']); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label>Chi tiết hư hại / ghi chú</label>
                        <input type="text" name="damage_note"
                               value="<?php echo htmlspecialchars($form['damage_note']); ?>"
                               placeholder="VD: hư minibar 300k, khách đồng ý bồi thường">
                    </div>
                </div>

                <div class="row" style="margin-top:6px;">
                    <div class="field" style="flex:2;">
                        <label>Phương thức thanh toán</label>
                        <select name="payment_method" id="payment_method">
                            <option value="">-- Chọn phương thức --</option>
                            <option value="cash"     <?php echo $form['payment_method']==='cash'?'selected':''; ?>>Tiền mặt</option>
                            <option value="transfer" <?php echo $form['payment_method']==='transfer'?'selected':''; ?>>Chuyển khoản</option>
                            <option value="card"     <?php echo $form['payment_method']==='card'?'selected':''; ?>>Thẻ (POS)</option>
                        </select>
                    </div>
                    <div class="field" style="flex:1;">
                        <label>Trạng thái thanh toán</label>
                        <select name="payment_status" id="payment_status">
                            <option value="unpaid" <?php echo $form['payment_status']==='unpaid'?'selected':''; ?>>Chưa thanh toán</option>
                            <option value="paid"   <?php echo $form['payment_status']==='paid'?'selected':''; ?>>Đã thanh toán</option>
                        </select>
                    </div>
                    <div class="field" style="flex:1;max-width:160px;">
                        <label>QR (nếu chuyển khoản)</label>
                        <div class="qr-box" id="qrBox">
                            <!-- Thay đường dẫn này bằng ảnh QR thật của bạn -->
                            <img src="public/images/qr_bank.png" alt="QR thanh toán">
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top:10px;">
                    <div class="field" style="flex:1.4;">
                        <label>Tóm tắt chi phí (tính theo phòng được chọn)</label>
                        <div class="summary-box" id="summaryBox">
                            <div class="summary-row">
                                <span>Tiền phòng:</span>
                                <strong id="sumRoom">0 đ</strong>
                            </div>
                            <div class="summary-row">
                                <span>Dịch vụ:</span>
                                <strong id="sumService">0 đ</strong>
                            </div>
                            <div class="summary-row">
                                <span>Phụ thu:</span>
                                <strong id="sumLate">0 đ</strong>
                            </div>
                            <div class="summary-row">
                                <span>Bồi thường:</span>
                                <strong id="sumDamage">0 đ</strong>
                            </div>
                            <div class="summary-total">
                                <span>Tổng thanh toán:</span>
                                <span id="sumTotal">0 đ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="btn-row">
                    <button type="button" class="btn-secondary"
                            onclick="window.location='index.php?controller=letan&action=checkOut'">
                        Làm lại
                    </button>
                    <div style="display:flex;gap:8px;">
                        <button type="submit" class="btn-secondary"
                                name="btn_action" value="cancel"
                                onclick="return confirm('Bạn chắc chắn muốn hủy tiến trình Check-out?');">
                            Hủy thao tác
                        </button>
                        <button type="submit" class="btn-primary"
                                name="btn_action" value="checkout"
                                onclick="return confirm('Xác nhận thanh toán và hoàn tất trả phòng?');">
                            Hoàn tất Check-out
                        </button>
                    </div>
                </div>

            <?php else: ?>
                <p class="caption">
                    Chưa có giao dịch nào được chọn hoặc không có phòng Stayed. Vui lòng tìm kiếm ở bước trên.
                </p>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ====== VALIDATE Ô TÌM KIẾM (chỉ cho nhập số) ======
    const formSearch       = document.getElementById('formSearch');
    const inputSearch      = document.getElementById('search_keyword');
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
            // Cho phép trống khi đang gõ – chỉ bắt khi submit
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

    // ====== PHẦN CHECK-OUT: TÍNH TÓN TÓM TẮT & QR ======
    const form           = document.getElementById('formCheckout');
    const qrBox          = document.getElementById('qrBox');
    const paymentMethod  = document.getElementById('payment_method');
    const damageFeeInput = document.getElementById('damage_fee');
    const lateFeeInput   = document.getElementById('late_fee');
    const hasDamageHidden = document.querySelector('input[name="has_damage"]');

    const roomCheckboxes = document.querySelectorAll('input[name="rooms[]"]');
    const roomRows       = document.querySelectorAll('tr[data-room-row="1"]');
    const serviceRows    = document.querySelectorAll('tr[data-service-row="1"]');

    const sumRoomEl    = document.getElementById('sumRoom');
    const sumServiceEl = document.getElementById('sumService');
    const sumLateEl    = document.getElementById('sumLate');
    const sumDamageEl  = document.getElementById('sumDamage');
    const sumTotalEl   = document.getElementById('sumTotal');

    function fmt(n){
        return n.toLocaleString('vi-VN') + ' đ';
    }
    function parseNumber(v) {
        const n = parseFloat(v);
        return isNaN(n) ? 0 : n;
    }

    function updateQR() {
        if (!qrBox || !paymentMethod) return;
        if (paymentMethod.value === 'transfer') {
            qrBox.style.opacity = '1';
        } else {
            qrBox.style.opacity = '0.3';
        }
    }

    function recalcSummary() {
        // Lấy danh sách phòng được tick
        const checkedIds = Array.from(roomCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value.toString());

        let roomTotal    = 0;
        let serviceTotal = 0;

        if (checkedIds.length > 0) {
            // Tiền phòng: chỉ tính những phòng được chọn
            roomRows.forEach(row => {
                const id  = row.getAttribute('data-room-id');
                const amt = parseNumber(row.getAttribute('data-room-amount'));
                if (checkedIds.includes(id)) {
                    roomTotal += amt;
                }
            });

            // Dịch vụ: khi đã chọn ít nhất 1 phòng thì cộng toàn bộ chi phí dịch vụ
            serviceRows.forEach(row => {
                const amt = parseNumber(row.getAttribute('data-service-amount'));
                serviceTotal += amt;
            });
        }

        const lf = parseNumber(lateFeeInput ? lateFeeInput.value : 0);
        const df = parseNumber(damageFeeInput ? damageFeeInput.value : 0);

        if (sumRoomEl)    sumRoomEl.textContent    = fmt(roomTotal);
        if (sumServiceEl) sumServiceEl.textContent = fmt(serviceTotal);
        if (sumLateEl)    sumLateEl.textContent    = fmt(checkedIds.length > 0 ? lf : 0);
        if (sumDamageEl)  sumDamageEl.textContent  = fmt(checkedIds.length > 0 ? df : 0);

        const total = checkedIds.length > 0 ? (roomTotal + serviceTotal + lf + df) : 0;
        if (sumTotalEl)   sumTotalEl.textContent   = fmt(total);
    }

    // Sự kiện thay đổi phòng chọn
    roomCheckboxes.forEach(cb => {
        cb.addEventListener('change', recalcSummary);
    });

    // Phụ thu / bồi thường đổi -> cập nhật lại tổng
    if (lateFeeInput)   lateFeeInput.addEventListener('input', recalcSummary);
    if (damageFeeInput) damageFeeInput.addEventListener('input', recalcSummary);

    // Payment method -> QR
    if (paymentMethod) {
        paymentMethod.addEventListener('change', updateQR);
        updateQR();
    }

    // Tính lại lần đầu (mặc định chưa chọn phòng -> tất cả = 0)
    recalcSummary();

    // Validate khi submit check-out
    form && form.addEventListener('submit', function (e) {
        const action = (e.submitter || {}).value || '';

        if (action === 'checkout') {
            const roomsChecked = Array.from(roomCheckboxes).filter(cb => cb.checked);
            if (roomsChecked.length === 0) {
                alert('Vui lòng chọn ít nhất một phòng để Check-out.');
                e.preventDefault();
                return;
            }
            if (paymentMethod && paymentMethod.value === '') {
                alert('Vui lòng chọn phương thức thanh toán.');
                e.preventDefault();
                return;
            }
            // Nếu CSDL báo tình trạng là hư hại nặng -> bắt buộc phải có phí bồi thường
            if (hasDamageHidden &&
                hasDamageHidden.value === 'heavy' &&
                parseNumber(damageFeeInput.value) <= 0) {
                alert('Phòng hư hại nặng – vui lòng nhập phí bồi thường hoặc xử lý theo quy trình quản lý.');
                e.preventDefault();
                return;
            }
        }
    });
});
</script>
</body>
</html>