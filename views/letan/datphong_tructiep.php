<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;

// Controller nên set $hasSearch = true sau khi bấm nút "Tìm phòng phù hợp"
$hasSearch = $hasSearch ?? false;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt phòng trực tiếp - Lễ tân</title>
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
        .grid {
            display: grid;
            grid-template-columns: minmax(0, 3fr) minmax(0, 2fr);
            gap: 20px;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 18px 20px;
            box-shadow: 0 10px 25px rgba(15,23,42,0.06);
            border: 1px solid #e5e7eb;
        }
        .card h2 { margin: 0 0 12px; font-size: 18px; color: #111827; }
        .card p.caption { margin: 0 0 14px; font-size: 13px; color: #6b7280; }
        .row { display: flex; gap: 12px; }
        .field { margin-bottom: 10px; flex: 1; }
        label { display: block; font-size: 13px; color: #374151; margin-bottom: 4px; }
        input[type="text"],
        input[type="date"],
        input[type="number"],
        input[type="email"],
        select {
            width: 100%;
            padding: 7px 10px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 14px;
            box-sizing: border-box;
            outline: none;
            transition: all .15s ease;
            background: #f9fafb;
        }
        input:focus, select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px rgba(59,130,246,0.2);
            background: #ffffff;
        }
        .input-error {
            border-color: #dc2626 !important;
            background: #fef2f2 !important;
        }
        .error-msg {
            font-size: 11px;
            color: #b91c1c;
            margin-top: 2px;
        }
        .section-title {
            font-size: 14px;
            font-weight: 600;
            margin: 10px 0 6px;
            color: #111827;
        }
        .room-list {
            max-height: 260px;
            overflow: auto;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead {
            background: #f9fafb;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        th, td {
            padding: 8px 10px;
            border-bottom: 1px solid #edf0f3;
            text-align: left;
        }
        tbody tr:hover { background: #f3f4ff; }
        .btn-primary {
            border: none;
            border-radius: 999px;
            padding: 9px 18px;
            font-size: 14px;
            font-weight: 500;
            color: #f9fafb;
            background: linear-gradient(135deg,#2563eb,#4f46e5);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 10px 20px rgba(37,99,235,0.25);
            transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(37,99,235,0.30);
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
        .btn-danger {
            border-radius: 999px;
            border: 1px solid #b91c1c;
            background: #fee2e2;
            color: #b91c1c;
            padding: 6px 12px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-row {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
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
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: #eef2ff;
            color: #3730a3;
            font-size: 12px;
        }
        .pill span { font-weight: 600; }
    </style>
</head>
<body>
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · LỄ TÂN</div>
        <div class="role">Đặt phòng trực tiếp tại quầy</div>
    </div>
    <div>
        <?php if ($user): ?>
            <span style="font-size:13px;margin-right:10px;">
                Xin chào, <strong><?php echo htmlspecialchars($user['Username']); ?></strong>
            </span>
        <?php endif; ?>
        <!-- ĐÃ FIX: trỏ đúng controller letan -->
        <a href="index.php?controller=letan&action=index">Trang lễ tân</a>
        <a href="index.php?controller=auth&action=logout">Đăng xuất</a>
    </div>
</div>

<div class="container">
    <div class="page-title">
        <div>
            <h1>Đặt phòng trực tiếp</h1>
            <span>Nhập thông tin khách → Tìm phòng phù hợp → Chọn phòng & dịch vụ → Đặt phòng</span>
        </div>
        <!-- ĐÃ FIX: trỏ đúng controller letan -->
        <a class="back-link" href="index.php?controller=letan&action=index">
            ← Quay lại trang chủ lễ tân
        </a>
    </div>

    <!-- Lỗi validate từ server -->
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

    <!-- Lỗi validate phía client -->
    <div id="clientErrors" class="alert alert-error" style="display:none;">
        <strong>Có lỗi xảy ra:</strong>
        <ul id="clientErrorsList" style="margin:6px 0 0 18px;padding:0;"></ul>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
            <?php if (!empty($maGiaoDich)): ?>
                <div class="pill" style="margin-top:6px;">
                    Mã giao dịch: <span>#<?php echo (int)$maGiaoDich; ?></span>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form method="post" id="formDatPhong">
        <div class="grid">
            <!-- CỘT 1: THÔNG TIN KHÁCH + YÊU CẦU -->
            <div class="card">
                <h2>Thông tin khách hàng</h2>
                <p class="caption">
                    Nhập đầy đủ thông tin khách. Nếu để trống hoặc nhập sai định dạng, ô sẽ báo lỗi màu đỏ.
                </p>

                <div class="row">
                    <div class="field">
                        <label>Họ tên khách <span style="color:#dc2626;">*</span></label>
                        <input type="text" id="ten_kh" name="ten_kh"
                               value="<?php echo htmlspecialchars($_POST['ten_kh'] ?? ''); ?>"
                               placeholder="Nguyễn Văn A">
                        <div class="error-msg" id="err_ten_kh"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label>CCCD / CMND <span style="color:#dc2626;">*</span></label>
                        <input type="text" id="cccd" name="cccd"
                               value="<?php echo htmlspecialchars($_POST['cccd'] ?? ''); ?>"
                               placeholder="Chỉ gồm 9–12 chữ số">
                        <div class="error-msg" id="err_cccd"></div>
                    </div>
                    <div class="field">
                        <label>Số điện thoại <span style="color:#dc2626;">*</span></label>
                        <input type="text" id="sdt" name="sdt"
                               value="<?php echo htmlspecialchars($_POST['sdt'] ?? ''); ?>"
                               placeholder="09xxxxxxxx">
                        <div class="error-msg" id="err_sdt"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label>Email <span style="color:#dc2626;">*</span></label>
                        <input type="email" id="email" name="email"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                               placeholder="ten@email.com">
                        <div class="error-msg" id="err_email"></div>
                    </div>
                    <div class="field">
                        <label>Địa chỉ</label>
                        <input type="text" name="diachi"
                               value="<?php echo htmlspecialchars($_POST['diachi'] ?? ''); ?>"
                               placeholder="Địa chỉ cư trú">
                    </div>
                </div>

                <div class="section-title" style="margin-top:16px;">Thông tin đặt phòng</div>

                <div class="row">
                    <div class="field">
                        <label>Ngày đến <span style="color:#dc2626;">*</span></label>
                        <input type="date" id="ngay_den" name="ngay_den"
                               value="<?php echo htmlspecialchars($ngayDen ?? ''); ?>">
                        <div class="error-msg" id="err_ngay_den"></div>
                    </div>
                    <div class="field">
                        <label>Ngày đi <span style="color:#dc2626;">*</span></label>
                        <input type="date" id="ngay_di" name="ngay_di"
                               value="<?php echo htmlspecialchars($ngayDi ?? ''); ?>">
                        <div class="error-msg" id="err_ngay_di"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label>Số khách (ước tính)</label>
                        <input type="number" id="so_nguoi" name="so_nguoi" min="1"
                               value="<?php echo htmlspecialchars($_POST['so_nguoi'] ?? '2'); ?>">
                        <div class="error-msg" id="err_so_nguoi"></div>
                    </div>
                    <div class="field">
                        <label>Ghi chú yêu cầu đặc biệt</label>
                        <input type="text" name="ghichu"
                               value="<?php echo htmlspecialchars($_POST['ghichu'] ?? ''); ?>"
                               placeholder="VD: cần giường phụ, ăn chay...">
                    </div>
                </div>

                <div class="btn-row">
                    <div id="btnDanger" class="btn-danger" style="display:none;">
                        ⚠ Thiếu hoặc sai thông tin. Vui lòng kiểm tra lại các ô màu đỏ.
                    </div>
                    <div style="display:flex; gap:10px;">
                        <!-- ĐÃ FIX: làm lại = reload trang, xoá sạch POST -->
                        <button type="button" class="btn-secondary"
                                onclick="window.location='index.php?controller=letan&action=datPhongTrucTiep'">
                            Làm lại
                        </button>

                        <!-- Nút TÌM PHÒNG: chỉ tìm phòng, chưa đặt -->
                        <button type="submit" name="btn_action" value="search" class="btn-secondary">
                            Tìm phòng phù hợp
                        </button>

                        <!-- Nút ĐẶT PHÒNG: sau khi đã có kết quả & chọn phòng -->
                        <button type="submit" name="btn_action" value="book" class="btn-primary">
                            Đặt phòng
                            <span>→</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- CỘT 2: CHỌN PHÒNG + DỊCH VỤ -->
            <div class="card">
                <h2>Chọn phòng & dịch vụ</h2>
                <p class="caption">
                    Bước 1: Nhấn “Tìm phòng phù hợp” để hệ thống gợi ý.<br>
                    Bước 2: Chọn phòng & nhập số lượng dịch vụ nếu khách yêu cầu.<br>
                    Nút “Đặt phòng” chỉ tạo giao dịch khi đã chọn ít nhất một phòng.
                </p>

                <div class="section-title">Danh sách phòng phù hợp</div>
                <div class="room-list">
                    <table>
                        <thead>
                        <tr>
                            <th></th>
                            <th>Phòng</th>
                            <th>Loại</th>
                            <th>Sức chứa</th>
                            <th>Giá / đêm</th>
                            <th>View</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($hasSearch && !empty($dsPhong) && $dsPhong instanceof mysqli_result && $dsPhong->num_rows > 0): ?>
                            <?php while ($p = $dsPhong->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox"
                                               name="rooms[]"
                                               value="<?php echo (int)$p['MaPhong']; ?>"
                                            <?php
                                            if (!empty($_POST['rooms']) &&
                                                in_array($p['MaPhong'], array_map('intval', $_POST['rooms']))) {
                                                echo 'checked';
                                            }
                                            ?>
                                        >
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($p['SoPhong']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($p['LoaiPhong']); ?></td>
                                    <td><?php echo (int)$p['SoKhachToiDa']; ?> khách</td>
                                    <td><?php echo number_format($p['Gia'], 0, ',', '.'); ?> đ</td>
                                    <td><?php echo htmlspecialchars($p['ViewPhong']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php elseif ($hasSearch): ?>
                            <tr>
                                <td colspan="6" style="text-align:center;color:#b91c1c;padding:12px;">
                                    Không còn phòng phù hợp với yêu cầu. Vui lòng điều chỉnh ngày / số khách / loại phòng.
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center;color:#6b7280;padding:12px;">
                                    Chưa tìm kiếm. Vui lòng nhập thông tin rồi bấm
                                    <strong>“Tìm phòng phù hợp”</strong>.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="section-title" style="margin-top:14px;">Đăng ký dịch vụ kèm theo</div>
                <p class="caption" style="margin-bottom:8px;">
                    Ghi nhận trước các dịch vụ khách muốn sử dụng. Có thể đặt thêm sau tại màn hình “Đặt dịch vụ”.
                </p>

                <?php if (!empty($dsDichVu)): ?>
                    <table>
                        <thead>
                        <tr>
                            <th>Dịch vụ</th>
                            <th>Giá</th>
                            <th style="width:90px;">Số lượng</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($dsDichVu as $dv): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($dv['TenDichVu']); ?></td>
                                <td><?php echo number_format($dv['GiaDichVu'], 0, ',', '.'); ?> đ</td>
                                <td>
                                    <input type="number"
                                           name="services[<?php echo (int)$dv['MaDichVu']; ?>]"
                                           min="0"
                                           value="<?php echo htmlspecialchars($_POST['services'][$dv['MaDichVu']] ?? '0'); ?>"
                                           style="width:70px;">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="font-size:13px;color:#6b7280;">Hiện chưa cấu hình dịch vụ nào.</div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('formDatPhong');
        const clientErrors = document.getElementById('clientErrors');
        const clientErrorsList = document.getElementById('clientErrorsList');
        const btnDanger = document.getElementById('btnDanger');

        const fields = {
            ten_kh:   document.getElementById('ten_kh'),
            cccd:     document.getElementById('cccd'),
            sdt:      document.getElementById('sdt'),
            email:    document.getElementById('email'),
            ngay_den: document.getElementById('ngay_den'),
            ngay_di:  document.getElementById('ngay_di'),
            so_nguoi: document.getElementById('so_nguoi')
        };

        const errsDom = {
            ten_kh:   document.getElementById('err_ten_kh'),
            cccd:     document.getElementById('err_cccd'),
            sdt:      document.getElementById('err_sdt'),
            email:    document.getElementById('err_email'),
            ngay_den: document.getElementById('err_ngay_den'),
            ngay_di:  document.getElementById('err_ngay_di'),
            so_nguoi: document.getElementById('err_so_nguoi')
        };

        // ĐÁNH DẤU field đã được người dùng “đụng” tới
        const touched = {
            ten_kh:false, cccd:false, sdt:false, email:false,
            ngay_den:false, ngay_di:false, so_nguoi:false
        };

        function setError(name, msg) {
            fields[name].classList.add('input-error');
            errsDom[name].textContent = msg;
        }
        function clearError(name) {
            fields[name].classList.remove('input-error');
            errsDom[name].textContent = '';
        }

        /**
         * fromSubmit = true  → validate bất kể đã touched hay chưa (dùng khi submit)
         * fromSubmit = false → chỉ validate nếu field đã touched (blur/input)
         */
        function validateField(name, fromSubmit = false) {
            if (!fromSubmit && !touched[name]) {
                // chưa nhập gì, không báo đỏ
                clearError(name);
                return;
            }

            const v = fields[name].value.trim();
            clearError(name);

            switch (name) {
                case 'ten_kh': {
                    if (!v) {
                        setError(name, 'Vui lòng nhập họ tên khách hàng.');
                        break;
                    }
                    // Regex họ tên: chỉ chữ + khoảng trắng, 2–50 ký tự
                    // Cho phép tiếng Việt có dấu
                    const reName = /^[A-Za-zÀ-Ỹà-ỹ\s]{2,50}$/;
                    if (!reName.test(v)) {
                        setError(
                            name,
                            'Họ tên chỉ được chứa chữ cái và khoảng trắng, độ dài 2–50 ký tự (không chứa số hoặc ký tự đặc biệt).'
                        );
                    }
                    break;
                }

                case 'cccd':
                    if (!v) {
                        setError(name, 'Vui lòng nhập CMND/CCCD.');
                    } else if (!/^\d{9,12}$/.test(v)) {
                        setError(name, 'CMND/CCCD sai định dạng (chỉ 9–12 chữ số).');
                    }
                    break;

                case 'sdt':
                    if (!v) {
                        setError(name, 'Vui lòng nhập số điện thoại.');
                    } else if (!/^0\d{8,10}$/.test(v)) {
                        setError(name, 'Số điện thoại sai định dạng.');
                    }
                    break;

                case 'email': {
                    if (!v) {
                        setError(name, 'Vui lòng nhập email.');
                    } else {
                        const reEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!reEmail.test(v)) {
                            setError(name, 'Email sai định dạng.');
                        }
                    }
                    break;
                }

                case 'ngay_den':
                case 'ngay_di': {
                    const dDenVal = fields['ngay_den'].value;
                    const dDiVal  = fields['ngay_di'].value;

                    const dDen = dDenVal ? new Date(dDenVal) : null;
                    const dDi  = dDiVal  ? new Date(dDiVal)  : null;
                    const today = new Date();
                    today.setHours(0,0,0,0);

                    clearError('ngay_den');
                    clearError('ngay_di');

                    if (!dDenVal) {
                        setError('ngay_den', 'Vui lòng chọn ngày đến.');
                    } else if (dDen < today) {
                        setError('ngay_den', 'Ngày đến không được nhỏ hơn ngày hiện tại.');
                    }

                    if (!dDiVal) {
                        setError('ngay_di', 'Vui lòng chọn ngày đi.');
                    } else if (dDen && dDi && dDi <= dDen) {
                        setError('ngay_di', 'Ngày đi phải lớn hơn ngày đến.');
                    }
                    break;
                }

                case 'so_nguoi':
                    if (!v || parseInt(v, 10) <= 0) {
                        setError(name, 'Số khách phải lớn hơn 0.');
                    }
                    break;
            }
        }

        function validateAll(fromSubmit = false) {
            Object.keys(fields).forEach(function (name) {
                validateField(name, fromSubmit);
            });

            const errs = [];
            Object.keys(errsDom).forEach(function (name) {
                if (errsDom[name].textContent) errs.push(errsDom[name].textContent);
            });
            return errs;
        }

        function updateDangerButton() {
            const hasError = validateAll(false).length > 0;
            btnDanger.style.display = hasError ? 'inline-flex' : 'none';
        }

        // Đăng ký sự kiện cho từng field
        Object.keys(fields).forEach(function (name) {
            fields[name].addEventListener('blur', function () {
                touched[name] = true;
                validateField(name, false);
                updateDangerButton();
            });
            fields[name].addEventListener('input', function () {
                if (touched[name]) {
                    validateField(name, false);
                    updateDangerButton();
                }
            });
        });

        form.addEventListener('submit', function (e) {
            const submitter = e.submitter || null;
            const action = submitter ? submitter.value : '';

            clientErrors.style.display = 'none';
            clientErrorsList.innerHTML = '';

            // Khi submit: validate tất cả field, bất kể đã touched hay chưa
            const errs = validateAll(true);

            // Nếu là ĐẶT PHÒNG, phải có ít nhất 1 phòng
            if (action === 'book') {
                const checkedRooms = document.querySelectorAll('input[name="rooms[]"]:checked');
                if (checkedRooms.length === 0) {
                    errs.push('Vui lòng chọn ít nhất một phòng trước khi nhấn Đặt phòng.');
                }
            }

            if (errs.length > 0) {
                e.preventDefault();
                clientErrorsList.innerHTML = '';
                errs.forEach(function (msg) {
                    const li = document.createElement('li');
                    li.textContent = msg;
                    clientErrorsList.appendChild(li);
                });
                clientErrors.style.display = 'block';
                btnDanger.style.display = 'inline-flex';
                window.scrollTo({top: 0, behavior: 'smooth'});
            }
        });

        // KHÔNG gọi validateAll(true) ở đây → lúc mới load không có lỗi nào cả
        btnDanger.style.display = 'none';
    });
</script>
</body>
</html>