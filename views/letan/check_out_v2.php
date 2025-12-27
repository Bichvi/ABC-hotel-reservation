<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user   = $_SESSION['user'] ?? null;
$errors = $errors ?? [];
$success = $success ?? null;

$giaoDich            = $giaoDich            ?? null;
$roomsStayed         = $roomsStayed         ?? [];
$selectedRoomId      = $selectedRoomId      ?? 0;
$selectedRoomDetail  = $selectedRoomDetail  ?? null;
$selectedRoomServices= $selectedRoomServices?? [];
$roomFee             = $roomFee             ?? 0;
$serviceFee          = $serviceFee          ?? 0;
$damageFee           = $damageFee           ?? 0;
// ===== FIX: tránh undefined + dùng để hiển thị đúng =====
$soThanhVien     = $soThanhVien     ?? 0;   // tổng thành viên của giao dịch (đếm bảng khachhang)
$tongNguoiOPhong = $tongNguoiOPhong ?? 0;   // tổng số người theo các phòng (sum SoNguoi trong chitietgiaodich)
$damageFee = $damageFee ?? 0;
// tổng cho 1 phòng được chọn
$totalAmount = $roomFee + $serviceFee + $damageFee;

// để giữ lại ô tìm kiếm
$searchKeyword = $_POST['search_keyword'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Check-out V2 - Trả phòng từng phòng</title>
    <style>
        /* ========== DARK PREMIUM ========== */
        body{
            margin:0;
            font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
            background:#020617;
            color:#e5e7eb;
        }
        a{color:#38bdf8;text-decoration:none;}
        a:hover{text-decoration:underline;}

        .topbar{
            background:#020617;
            color:#e5e7eb;
            padding:12px 24px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            border-bottom:1px solid rgba(148,163,184,0.3);
        }
        .brand{
            font-weight:700;
            letter-spacing:.04em;
            font-size:18px;
            color:#38bdf8;
        }
        .role{
            font-size:13px;
            opacity:.8;
            color:#94a3b8;
        }
        .topbar a{
            font-size:13px;
            color:#e5e7eb;
            text-decoration:none;
            margin-left:12px;
            opacity:.9;
        }
        .topbar a:hover{
            opacity:1;
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
        .page-title h1{
            margin:0;
            font-size:24px;
            color:#f9fafb;
        }
        .page-title span{
            font-size:13px;
            color:#94a3b8;
        }
        .back-link{
            font-size:13px;
            text-decoration:none;
            color:#38bdf8;
            display:inline-flex;
            align-items:center;
            gap:4px;
            opacity:.9;
        }
        .back-link:hover{opacity:1;}

        .card{
            background:radial-gradient(circle at top left,#1f2937,#020617);
            border-radius:14px;
            padding:16px 18px;
            box-shadow:0 18px 40px rgba(15,23,42,.75);
            border:1px solid rgba(148,163,184,0.35);
            margin-bottom:16px;
        }
        .card h2{
            margin:0 0 10px;
            font-size:16px;
            color:#e5e7eb;
        }
        .card p.caption{
            margin:0 0 10px;
            font-size:13px;
            color:#9ca3af;
        }

        .row{
            display:flex;
            gap:12px;
            flex-wrap:wrap;
        }
        .field{
            margin-bottom:10px;
            flex:1;
        }
        label{
            display:block;
            font-size:13px;
            color:#cbd5f5;
            margin-bottom:4px;
        }

        input[type="text"],
        input[type="number"],
        select{
            width:100%;
            padding:7px 10px;
            border-radius:8px;
            border:1px solid rgba(148,163,184,0.5);
            font-size:14px;
            background:#020617;
            box-sizing:border-box;
            outline:none;
            transition:all .15s ease;
            color:#e5e7eb;
        }
        input::placeholder{
            color:#64748b;
        }
        input:disabled{
            opacity:.8;
            background:#020617;
        }
        input:focus,select:focus{
            border-color:#38bdf8;
            box-shadow:0 0 0 1px rgba(56,189,248,.35);
            background:#020617;
        }

        .input-error{
            border-color:#f97373!important;
            background:#450a0a!important;
        }
        .error-msg{
            font-size:11px;
            color:#fecaca;
            margin-top:2px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            font-size:13px;
        }
        th,td{
            padding:8px 8px;
            border-bottom:1px solid rgba(148,163,184,0.25);
            text-align:left;
        }
        thead{
            background:#020617;
        }
        th{
            color:#9ca3af;
            font-weight:500;
        }
        tbody tr:nth-child(odd){
            background:rgba(15,23,42,0.7);
        }
        tbody tr:nth-child(even){
            background:rgba(15,23,42,0.9);
        }
        tbody tr:hover td{
            background:rgba(30,64,175,0.55);
        }

        .alert{
            border-radius:10px;
            padding:10px 12px;
            margin-bottom:12px;
            font-size:13px;
        }
        .alert-error{
            background:#450a0a;
            border:1px solid #fecaca;
            color:#fecaca;
        }
        .alert-success{
            background:#022c22;
            border:1px solid #34d399;
            color:#a7f3d0;
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
            box-shadow:0 10px 20px rgba(22,163,74,.45);
            transition:transform .12s ease,box-shadow .12s ease,filter .12s ease;
        }
        .btn-primary:hover{
            transform:translateY(-1px);
            box-shadow:0 16px 32px rgba(22,163,74,.55);
            filter:brightness(1.03);
        }
        .btn-secondary{
            border-radius:999px;
            border:1px solid rgba(148,163,184,0.7);
            background:#020617;
            color:#e5e7eb;
            padding:8px 14px;
            font-size:13px;
            cursor:pointer;
            display:inline-flex;
            align-items:center;
            gap:4px;
            transition:background .12s ease,border-color .12s ease,transform .12s ease;
        }
        .btn-secondary:hover{
            background:#0f172a;
            border-color:#38bdf8;
            transform:translateY(-0.5px);
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
        .badge-green{
            background:rgba(22,163,74,0.18);
            color:#bbf7d0;
            border:1px solid rgba(22,163,74,0.6);
        }
        .badge-blue{
            background:#0f172a;
            color:#93c5fd;
        }
        .badge-gray{
            background:#111827;
            color:#e5e7eb;
        }

        .summary-box{
            background:#020617;
            border-radius:10px;
            padding:10px 12px;
            border:1px dashed rgba(148,163,184,0.6);
            font-size:13px;
        }
        .summary-row{
            display:flex;
            justify-content:space-between;
            margin-bottom:4px;
        }
        .summary-row strong{
            color:#e5e7eb;
        }
        .summary-row span{
            color:#cbd5f5;
        }
        .summary-total{
            font-size:15px;
            font-weight:700;
            margin-top:6px;
            border-top:1px dashed rgba(148,163,184,0.6);
            padding-top:6px;
            display:flex;
            justify-content:space-between;
            color:#facc15;
        }

        .qr-box{
            width:96px;
            height:96px;
            border-radius:10px;
            border:1px dashed #9ca3af;
            display:flex;
            align-items:center;
            justify-content:center;
            background:#020617;
            overflow:hidden;
            transition:opacity .2s ease;
        }
        .qr-box img{
            max-width:100%;
            max-height:100%;
            border-radius:8px;
            display:block;
        }

        /* Ẩn nút "Xem chi tiết phòng" nhưng vẫn giữ để JS click */
        .hidden-load-room-btn{
            position:absolute;
            left:-9999px;
            top:-9999px;
            width:1px;
            height:1px;
            overflow:hidden;
        }
    </style>
</head>
<body>
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · LỄ TÂN</div>
        <div class="role">Check-out V2 · Trả từng phòng</div>
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
            <h1>Check-out V2</h1>
            <span>Tìm giao dịch → chọn 1 phòng đang Stayed → hệ thống tự tải chi tiết & chi phí → thanh toán & trả phòng</span>
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

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <!-- FORM TÌM KIẾM GIAO DỊCH -->
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

    <!-- FORM CHỌN PHÒNG + CHECKOUT -->
    <form method="post" id="formCheckout">
        <div class="card">
            <h2>Thông tin giao dịch & phòng Stayed</h2>

            <?php if ($giaoDich && !empty($roomsStayed)): ?>
                <input type="hidden" name="ma_gd" value="<?php echo (int)$giaoDich['MaGiaoDich']; ?>">

                <div class="row">
                    <div class="field">
                        <label>Mã giao dịch</label>
                        <input type="text" value="<?php echo htmlspecialchars($giaoDich['MaGiaoDich']); ?>" disabled>
                    </div>
                    <div class="field">
                        <label>Trạng thái giao dịch</label>
                        <input type="text" value="<?php echo htmlspecialchars($giaoDich['TrangThai']); ?>" disabled>
                    </div>
                    <?php if (!empty($giaoDich['MaKhuyenMai'])): ?>
    <div class="field">
        <label>Khuyến mãi đã áp dụng</label>
        <input type="text"
               value="<?php echo htmlspecialchars($giaoDich['MaKhuyenMai']); ?>"
               disabled>
    </div>
<?php endif; ?>
                    <div class="field">
                        <label>Khách hàng / Trưởng đoàn</label>
                        <input type="text"
                               value="<?php echo htmlspecialchars($giaoDich['TenKH'] ?? $giaoDich['TenKhachHang'] ?? ''); ?>"
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
                <div class="row">
    <div class="field">
        <label>Tổng số thành viên trong giao dịch</label>
        <input type="text"
               value="<?php echo (int)$soThanhVien; ?> người"
               disabled>
    </div>
    
</div>

                <h2 style="margin-top:6px;">Danh sách phòng đang ở (Stayed)</h2>
                <p class="caption">
                    Chọn <strong>1 phòng</strong>, hệ thống sẽ tự tải chi tiết phòng & chi phí cho bên dưới.
                </p>

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
                    <?php foreach ($roomsStayed as $ct): ?>
                    <?php
                        $maP       = (int)($ct['MaPhong'] ?? 0);
                        $tienPhong = (float)($ct['ThanhTien'] ?? 0);
                        $checked   = ($selectedRoomId === $maP);

                        // An toàn key cho ngày & số người & trạng thái
                        $ngayNhanRaw = $ct['NgayNhanDuKien'] ?? $ct['NgayNhanThucTe'] ?? '';
                        $ngayTraRaw  = $ct['NgayTraDuKien']  ?? $ct['NgayTraThucTe']  ?? '';

                        $ngayNhan = $ngayNhanRaw ? substr($ngayNhanRaw, 0, 16) : '';
                        $ngayTra  = $ngayTraRaw  ? substr($ngayTraRaw,  0, 16) : '';

                        $soNguoi   = isset($ct['SoNguoi'])   ? (int)$ct['SoNguoi']   : 0;
                        $trangThai = $ct['TrangThai']        ?? 'Stayed';
                    ?>
                    <tr>
                        <td>
                            <input type="radio"
                                   name="selected_room"
                                   value="<?php echo $maP; ?>"
                                   <?php echo $checked ? 'checked' : ''; ?>>
                        </td>
                        <td><?php echo htmlspecialchars($ct['SoPhong'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($ct['LoaiPhong'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($ngayNhan); ?></td>
                        <td><?php echo htmlspecialchars($ngayTra); ?></td>
                        <td>
    <?php echo (int)$soThanhVien; ?>
</td>
                        <td><?php echo number_format($tienPhong,0,',','.'); ?> đ</td>
                        <td>
                            <span class="badge badge-green">
                                <?php echo htmlspecialchars($trangThai); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="btn-row" style="margin-top:10px;">
                    <span style="font-size:12px;color:#9ca3af;">
                        * Khi bạn chọn phòng, hệ thống sẽ tự động tải chi tiết & chi phí bên dưới.
                    </span>
                    <!-- Nút này bị ẩn, chỉ để JS click() auto submit -->
                    <button type="submit"
                            class="btn-secondary hidden-load-room-btn"
                            id="btn_load_room"
                            name="btn_action"
                            value="load_room">
                        Xem chi tiết phòng
                    </button>
                </div>

            <?php else: ?>
                <p class="caption">
                    Chưa có giao dịch nào được chọn hoặc giao dịch không có phòng Stayed. Vui lòng tìm kiếm ở bước trên.
                </p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Chi tiết phòng & chi phí (theo phòng đã chọn)</h2>

            <?php if ($selectedRoomDetail): ?>
                <?php
                $roomId = (int)$selectedRoomDetail['MaPhong'];
                ?>
                <!-- để controller checkout sử dụng -->
                <input type="hidden" name="room_id" value="<?php echo $roomId; ?>">

                <div class="row">
                    <div class="field">
                        <label>Số phòng</label>
                        <input type="text"
                               value="<?php echo htmlspecialchars($selectedRoomDetail['SoPhong'] ?? ''); ?>"
                               disabled>
                    </div>
                    <div class="field">
                        <label>Loại phòng</label>
                        <input type="text"
                               value="<?php echo htmlspecialchars($selectedRoomDetail['LoaiPhong'] ?? ''); ?>"
                               disabled>
                    </div>
                    <div class="field">
                        <label>Số khách</label>
                        <input type="text"
                               value="<?php echo (int)$soThanhVien; ?>"
                               disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label>Ngày nhận</label>
                        <input type="text"
                               value="<?php echo htmlspecialchars(substr($selectedRoomDetail['NgayNhanDuKien'],0,16)); ?>"
                               disabled>
                    </div>
                    <div class="field">
                        <label>Ngày trả dự kiến</label>
                        <input type="text"
                               value="<?php echo htmlspecialchars(substr($selectedRoomDetail['NgayTraDuKien'],0,16)); ?>"
                               disabled>
                    </div>
                    <div class="field">
                        <label>Tình trạng phòng (sau kiểm tra)</label>
                        <?php
                        $tinhTrang = $selectedRoomDetail['TinhTrangPhong'] ?? 'tot';
                        $txtTinhTrang = [
                            'tot'        => 'Tốt (không hư hại)',
                            'huhainhe'   => 'Hư hại nhẹ',
                            'huhainang'  => 'Hư hại nặng',
                        ][$tinhTrang] ?? $tinhTrang;
                        ?>
                        <input type="text"
                               value="<?php echo htmlspecialchars($txtTinhTrang); ?>"
                               disabled>
                    </div>
                </div>

                <h2 style="margin-top:8px;">Dịch vụ của phòng này</h2>
                <?php if (!empty($selectedRoomServices)): ?>
                    <table>
                        <thead>
                        <tr>
                            <th>Tên dịch vụ</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($selectedRoomServices as $dv): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($dv['TenDichVu']); ?></td>
                                <td><?php echo (int)$dv['SoLuong']; ?></td>
                                <td><?php echo number_format($dv['GiaBan'],0,',','.'); ?> đ</td>
                                <td><?php echo number_format($dv['ThanhTien'],0,',','.'); ?> đ</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="caption">Phòng này chưa sử dụng dịch vụ nào.</p>
                <?php endif; ?>

                <div class="row" style="margin-top:14px;">
                    <div class="field" style="flex:1.4;">
                        <label>Tóm tắt chi phí (phòng đã chọn)</label>
                        <div class="summary-box">
                            <div class="summary-row">
                                <span>Tiền phòng:</span>
                                <strong><?php echo number_format($roomFee,0,',','.'); ?> đ</strong>
                            </div>
                          <?php
// ===== TÍNH TỔNG TIỀN DỊCH VỤ ĐỂ HIỂN THỊ (KHÔNG CỘNG LẠI) =====
$dvTong = 0;
foreach ($selectedRoomServices as $sv) {
    $dvTong += (float)$sv['ThanhTien'];
}
?>

<div class="summary-row">
    <span>
        Dịch vụ phòng này
        <small style="color:#9ca3af;">
            (đã cộng trong tổng tiền giao dịch)
        </small>
    </span>
    <strong style="color:#94a3b8;">
        <?php echo number_format($dvTong,0,',','.'); ?> đ
    </strong>
</div>
                            <div class="summary-row">
                                <span>Bồi thường (từ tình trạng phòng):</span>
                                <strong><?php echo number_format($damageFee,0,',','.'); ?> đ</strong>
                            </div>
                    <?php if (!empty($discountFee) && $discountFee > 0 && !empty($khuyenMaiInfo)): ?>
<div class="summary-row">
    <span>
        Khuyến mãi:
        <strong style="color:#93c5fd;">
            <?php echo htmlspecialchars($khuyenMaiInfo['MaCode'] ?? 'KM'); ?>
        </strong>
        –
        <?php
            if (($khuyenMaiInfo['LoaiUuDai'] ?? '') === 'PERCENT') {
                echo htmlspecialchars($khuyenMaiInfo['MucUuDai']) . '%';
            } else {
                echo number_format($khuyenMaiInfo['MucUuDai'],0,',','.') . ' đ';
            }
        ?>
    </span>

    <strong style="color:#fca5a5;">
        -<?php echo number_format($discountFee,0,',','.'); ?> đ
    </strong>
</div>
<?php elseif (!empty($discountFee) && $discountFee > 0): ?>
<div class="summary-row">
    <span>Giảm giá:</span>
    <strong style="color:#fca5a5;">-<?php echo number_format($discountFee,0,',','.'); ?> đ</strong>
</div>
<?php endif; ?>
                            <?php 
    $finalTotal = $totalAmount - ($discountFee ?? 0);
    if ($finalTotal < 0) $finalTotal = 0;
?>
<div class="summary-total">
    <span>Tổng phải thanh toán:</span>
    <span><?php echo number_format($finalTotal,0,',','.'); ?> đ</span>
</div>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top:10px;">
                    <div class="field" style="flex:2;">
                        <label>Phương thức thanh toán</label>
                        <select name="payment_method" id="payment_method">
                            <option value="">-- Chọn phương thức --</option>
                            <option value="cash">Tiền mặt</option>
                            <option value="transfer">Chuyển khoản</option>
                            <option value="card">Thẻ (POS)</option>
                        </select>
                    </div>
                    <div class="field" style="flex:1;">
                        <label>Trạng thái thanh toán</label>
                        <select name="payment_status" id="payment_status">
                            <option value="unpaid">Chưa thanh toán</option>
                            <option value="paid">Đã thanh toán</option>
                        </select>
                    </div>
                    <div class="field" style="flex:1;max-width:160px;">
                        <label>QR (nếu chuyển khoản)</label>
                        <div class="qr-box" id="qrBox">
                            <!-- Đổi thành QR ngân hàng thật của resort -->
                            <img src="public/images/qr_bank.png" alt="QR thanh toán">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label>Ghi chú thêm (nếu có)</label>
                        <input type="text"
                               name="damage_note"
                               placeholder="VD: khách đồng ý bồi thường hư hại nhẹ, thu thêm 500k">
                    </div>
                </div>

                <div class="btn-row">
                    <button type="button" class="btn-secondary"
                            onclick="window.location='index.php?controller=letan&action=checkOutV2'">
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
                                onclick="return confirm('Xác nhận thanh toán và hoàn tất trả phòng này?');">
                            Hoàn tất Check-out phòng này
                        </button>
                    </div>
                </div>

            <?php else: ?>
                <p class="caption">
                    Chưa có phòng nào được chọn.  
                    Vui lòng chọn 1 phòng ở bảng trên, hệ thống sẽ tự tải chi tiết.
                </p>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const formSearch  = document.getElementById('formSearch');
    const inputSearch = document.getElementById('search_keyword');
    const errSearch   = document.getElementById('err_search');

    function setError(msg) {
        if (!inputSearch || !errSearch) return;
        inputSearch.classList.add('input-error');
        errSearch.textContent = msg;
    }
    function clearError() {
        if (!inputSearch || !errSearch) return;
        inputSearch.classList.remove('input-error');
        errSearch.textContent = '';
    }

    if (formSearch && inputSearch) {
        formSearch.addEventListener('submit', function (e) {
            clearError();
            const v = inputSearch.value.trim();
            if (v === '') {
                setError('Vui lòng nhập mã giao dịch hoặc CMND/CCCD để tìm kiếm.');
                e.preventDefault();
                return;
            }
            if (!/^\d+$/.test(v)) {
                setError('Dữ liệu không hợp lệ. Chỉ được nhập số (mã giao dịch hoặc CMND/CCCD).');
                e.preventDefault();
            }
        });
    }

    const paymentMethod = document.getElementById('payment_method');
    const qrBox         = document.getElementById('qrBox');

    function updateQR() {
        if (!paymentMethod || !qrBox) return;
        if (paymentMethod.value === 'transfer') {
            qrBox.style.opacity = '1';
        } else {
            qrBox.style.opacity = '0.3';
        }
    }
    if (paymentMethod) {
        paymentMethod.addEventListener('change', updateQR);
        updateQR();
    }

    const formCheckout    = document.getElementById('formCheckout');
    const paymentStatusEl = document.getElementById('payment_status');

    if (formCheckout) {
        formCheckout.addEventListener('submit', function (e) {
            const action = (e.submitter || {}).value || '';
            if (action === 'checkout') {
                // phải có room_id (nghĩa là đã chọn phòng & load_room)
                const roomId = document.querySelector('input[name="room_id"]');
                if (!roomId || !roomId.value) {
                    alert('Vui lòng chọn phòng (nhấp vào phòng ở bảng trên) để tải chi tiết trước khi check-out.');
                    e.preventDefault();
                    return;
                }
                if (!paymentMethod || paymentMethod.value === '') {
                    alert('Vui lòng chọn phương thức thanh toán.');
                    e.preventDefault();
                    return;
                }
                if (!paymentStatusEl || paymentStatusEl.value !== 'paid') {
                    alert('Vui lòng chọn "Đã thanh toán" trước khi hoàn tất Check-out.');
                    e.preventDefault();
                    return;
                }
            }
        });
    }

    // ===== AUTO LOAD ROOM DETAIL KHI CHỌN RADIO =====
    const loadRoomBtn = document.getElementById('btn_load_room');
    const roomRadios  = document.querySelectorAll('input[name="selected_room"]');

    if (loadRoomBtn && roomRadios.length > 0) {
        roomRadios.forEach(function (r) {
            r.addEventListener('change', function () {
                // click nút load_room ẩn -> submit form với btn_action=load_room
                loadRoomBtn.click();
            });
        });
    }
});
</script>
</body>
</html>