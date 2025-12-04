<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;

$errors        = $errors        ?? [];
$success       = $success       ?? null;
$searchKeyword = $searchKeyword ?? '';
$giaoDich      = $giaoDich      ?? null;
$chiTiet       = $chiTiet       ?? [];
$form          = $form          ?? [
    'ten_kh'   => '',
    'cccd'     => '',
    'sdt'      => '',
    'email'    => '',
    'ngay_den' => '',
    'ngay_di'  => '',
    'so_nguoi' => 1,
    'ma_phong' => ''
];
$dsPhong       = $dsPhong ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa thông tin đặt phòng</title>
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
        input[type="date"],
        input[type="number"],
        select{
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
        input:focus,select:focus{
            border-color:#3b82f6;
            box-shadow:0 0 0 1px rgba(59,130,246,.2);
            background:#fff;
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
        .badge-blue{background:#dbeafe;color:#1d4ed8;}
        .badge-green{background:#dcfce7;color:#166534;}
        .badge-red{background:#fee2e2;color:#b91c1c;}

        .input-error{
            border-color:#dc2626 !important;
            background:#fef2f2 !important;
        }
        .error-msg{
            font-size:11px;
            color:#b91c1c;
            margin-top:2px;
        }
    </style>
</head>
<body>
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · LỄ TÂN</div>
        <div class="role">Sửa thông tin đặt phòng</div>
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
            <h1>Sửa thông tin đặt phòng</h1>
            <span>Tìm giao dịch → chọn phòng → chỉnh sửa → lưu thông tin</span>
        </div>
        <a class="back-link" href="index.php?controller=letan&action=index">
            ← Quay lại trang chủ lễ tân
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <strong>Có lỗi xảy ra (server):</strong>
            <ul style="margin:6px 0 0 18px;padding:0;">
                <?php foreach ($errors as $e): ?>
                    <li><?php echo htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- LỖI PHÍA CLIENT -->
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
                Nhập <strong>Mã giao dịch</strong> hoặc <strong>CMND/CCCD trưởng đoàn</strong> rồi nhấn <strong>Tìm kiếm</strong>.
            </p>
            <div class="row">
                <div class="field">
                    <label>Mã giao dịch / CMND</label>
                    <input type="text"
                           id="search_keyword"
                           name="search_keyword"
                           value="<?php echo htmlspecialchars($searchKeyword); ?>"
                           placeholder="VD: 1 hoặc 0123456789">
                    <div class="error-msg" id="err_search_keyword"></div>
                </div>
                <div class="field" style="flex:0 0 auto;display:flex;align-items:flex-end;gap:8px;">
                    <button type="submit" class="btn-primary" name="btn_action" value="search">
                        Tìm kiếm
                    </button>
                    <button type="submit" class="btn-secondary" name="btn_action" value="cancel">
                        Hủy thao tác
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- FORM 2: SỬA THÔNG TIN ĐẶT PHÒNG -->
    <form method="post" id="formEditBooking">
        <div class="card">
            <h2>Thông tin đặt phòng hiện tại</h2>

            <?php if ($giaoDich && !empty($chiTiet)): ?>
                <?php $ct0 = $chiTiet[0]; ?>
                <input type="hidden" name="ma_giao_dich" value="<?php echo (int)$giaoDich['MaGiaoDich']; ?>">
                <input type="hidden" name="ma_phong_cu"  value="<?php echo (int)$ct0['MaPhong']; ?>">
                <input type="hidden" name="search_keyword" value="<?php echo htmlspecialchars($searchKeyword); ?>">

                <div class="row">
                    <div class="field">
                        <label>Mã giao dịch</label>
                        <input type="text" value="<?php echo htmlspecialchars($giaoDich['MaGiaoDich']); ?>" disabled>
                    </div>
                    <div class="field">
                        <label>Trạng thái giao dịch</label>
                        <input type="text" value="<?php echo htmlspecialchars($giaoDich['TrangThai']); ?>" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label>Họ tên trưởng đoàn <span style="color:#dc2626;">*</span></label>
                        <input type="text" id="ten_kh" name="ten_kh"
                               value="<?php echo htmlspecialchars($form['ten_kh']); ?>">
                        <div class="error-msg" id="err_ten_kh"></div>
                    </div>
                    <div class="field">
                        <label>CMND/CCCD <span style="color:#dc2626;">*</span></label>
                        <input type="text" id="cccd" name="cccd"
                               value="<?php echo htmlspecialchars($form['cccd']); ?>">
                        <div class="error-msg" id="err_cccd"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label>Số điện thoại <span style="color:#dc2626;">*</span></label>
                        <input type="text" id="sdt" name="sdt"
                               value="<?php echo htmlspecialchars($form['sdt']); ?>">
                        <div class="error-msg" id="err_sdt"></div>
                    </div>
                    <div class="field">
                        <label>Email <span style="color:#dc2626;">*</span></label>
                        <input type="text" id="email" name="email"
                               value="<?php echo htmlspecialchars($form['email']); ?>">
                        <div class="error-msg" id="err_email"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label>Ngày đến <span style="color:#dc2626;">*</span></label>
                        <input type="date" id="ngay_den" name="ngay_den"
                               value="<?php echo htmlspecialchars($form['ngay_den']); ?>">
                        <div class="error-msg" id="err_ngay_den"></div>
                    </div>
                    <div class="field">
                        <label>Ngày đi <span style="color:#dc2626;">*</span></label>
                        <input type="date" id="ngay_di" name="ngay_di"
                               value="<?php echo htmlspecialchars($form['ngay_di']); ?>">
                        <div class="error-msg" id="err_ngay_di"></div>
                    </div>
                    <div class="field">
                        <label>Số người <span style="color:#dc2626;">*</span></label>
                        <input type="number" id="so_nguoi" min="1" name="so_nguoi"
                               value="<?php echo (int)$form['so_nguoi']; ?>">
                        <div class="error-msg" id="err_so_nguoi"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label>Phòng <span style="color:#dc2626;">*</span></label>
                        <select name="ma_phong" id="ma_phong">
                            <option value="">-- Chọn phòng --</option>
                            <?php foreach ($dsPhong as $p): ?>
                                <option value="<?php echo (int)$p['MaPhong']; ?>"
                                    <?php echo ((int)$p['MaPhong'] === (int)$form['ma_phong']) ? 'selected' : ''; ?>>
                                    <?php
                                    echo htmlspecialchars(
                                        $p['SoPhong'] . " - " . ($p['LoaiPhong'] ?? '') .
                                        " (tối đa " . ($p['SoKhachToiDa'] ?? '?') . " khách)"
                                    );
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="error-msg" id="err_ma_phong"></div>
                    </div>
                </div>

                <!-- Bảng danh sách chi tiết hiện tại -->
                <h2>Danh sách chi tiết giao dịch</h2>
                <table>
                    <thead>
                    <tr>
                        <th>Mã phòng</th>
                        <th>Số phòng</th>
                        <th>Loại phòng</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Số người</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($chiTiet as $ct): ?>
                        <tr>
                            <td><?php echo (int)$ct['MaPhong']; ?></td>
                            <td><?php echo htmlspecialchars($ct['SoPhong']); ?></td>
                            <td><?php echo htmlspecialchars($ct['LoaiPhong']); ?></td>
                            <td><?php echo htmlspecialchars(substr($ct['NgayNhanDuKien'],0,10)); ?></td>
                            <td><?php echo htmlspecialchars(substr($ct['NgayTraDuKien'],0,10)); ?></td>
                            <td><?php echo isset($ct['SoNguoi']) ? (int)$ct['SoNguoi'] : 0; ?></td>
                            <td>
                                <?php
                                $st  = $ct['TrangThai'];
                                $cls = 'badge-blue';
                                if ($st === 'DaHuy' || $st === 'Cancelled') $cls = 'badge-red';
                                elseif (in_array($st, ['Stayed','CheckedIn'])) $cls = 'badge-green';
                                ?>
                                <span class="badge <?php echo $cls; ?>">
                                    <?php echo htmlspecialchars($st); ?>
                                </span>
                            </td>
                            <td>
                                <!-- Nút chọn phòng này để sửa -->
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="search_keyword"
                                           value="<?php echo htmlspecialchars($searchKeyword ?: $giaoDich['MaGiaoDich']); ?>">
                                    <input type="hidden" name="ma_giao_dich"
                                           value="<?php echo (int)$giaoDich['MaGiaoDich']; ?>">
                                    <input type="hidden" name="ma_phong_cu"
                                           value="<?php echo (int)$ct['MaPhong']; ?>">
                                    <button type="submit"
                                            class="btn-secondary"
                                            name="btn_action"
                                            value="pick_room">
                                        Sửa phòng này
                                    </button>
                                </form>
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
                        <button type="submit" class="btn-secondary"
                                name="btn_action" value="cancel"
                                onclick="return confirm('Bạn chắc chắn muốn hủy thao tác? Dữ liệu sẽ không được lưu.');">
                            Hủy thao tác
                        </button>
                        <button type="submit" class="btn-primary"
                                name="btn_action" value="save">
                            Lưu thông tin
                        </button>
                    </div>
                </div>

            <?php else: ?>
                <p class="caption">
                    Chưa có giao dịch nào được chọn. Vui lòng tìm kiếm ở phần trên.
                </p>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- VALIDATE Ô TÌM KIẾM GIAO DỊCH ---
    const formSearch   = document.getElementById('formSearch');
    const searchField  = document.getElementById('search_keyword');
    const searchErrDom = document.getElementById('err_search_keyword');

    let searchTouched = false;

    function setSearchError(msg) {
        if (!searchField) return;
        searchField.classList.add('input-error');
        if (searchErrDom) searchErrDom.textContent = msg;
    }
    function clearSearchError() {
        if (!searchField) return;
        searchField.classList.remove('input-error');
        if (searchErrDom) searchErrDom.textContent = '';
    }

    function validateSearch(fromSubmit = false) {
        if (!searchField) return [];
        const v = searchField.value.trim();

        if (!fromSubmit && !searchTouched) {
            clearSearchError();
            return [];
        }

        clearSearchError();
        const errs = [];

        if (!v) {
            if (fromSubmit) {
                errs.push('Vui lòng nhập thông tin tìm kiếm (mã giao dịch hoặc CMND/CCCD).');
            }
        } else if (!/^\d+$/.test(v)) {
            errs.push('Dữ liệu nhập không hợp lệ. Chỉ nhập số (mã giao dịch hoặc CMND/CCCD).');
        } else if (v.length > 12) {
            errs.push('Độ dài không hợp lệ (tối đa 12 chữ số).');
        }

        if (errs.length > 0) {
            setSearchError(errs[0]);
        } else {
            clearSearchError();
        }
        return errs;
    }

    if (searchField) {
        searchField.addEventListener('blur', function () {
            searchTouched = true;
            validateSearch(false);
        });
        searchField.addEventListener('input', function () {
            if (searchTouched) validateSearch(false);
        });
    }

    if (formSearch) {
        formSearch.addEventListener('submit', function (e) {
            const submitter = e.submitter || null;
            const action = submitter ? submitter.value : '';
            if (action !== 'search') return; // nút Hủy thao tác thì không chặn

            const errs = validateSearch(true);
            if (errs.length > 0) {
                e.preventDefault();
            }
        });
    }

    // --- VALIDATE FORM SỬA ĐẶT PHÒNG ---
    const formEdit   = document.getElementById('formEditBooking');
    const clientErrors     = document.getElementById('clientErrors');
    const clientErrorsList = document.getElementById('clientErrorsList');

    const fields = {
        ten_kh:   document.getElementById('ten_kh'),
        cccd:     document.getElementById('cccd'),
        sdt:      document.getElementById('sdt'),
        email:    document.getElementById('email'),
        ngay_den: document.getElementById('ngay_den'),
        ngay_di:  document.getElementById('ngay_di'),
        so_nguoi: document.getElementById('so_nguoi'),
        ma_phong: document.getElementById('ma_phong')
    };

    const errsDom = {
        ten_kh:   document.getElementById('err_ten_kh'),
        cccd:     document.getElementById('err_cccd'),
        sdt:      document.getElementById('err_sdt'),
        email:    document.getElementById('err_email'),
        ngay_den: document.getElementById('err_ngay_den'),
        ngay_di:  document.getElementById('err_ngay_di'),
        so_nguoi: document.getElementById('err_so_nguoi'),
        ma_phong: document.getElementById('err_ma_phong')
    };

    const touched = {
        ten_kh:false, cccd:false, sdt:false, email:false,
        ngay_den:false, ngay_di:false, so_nguoi:false, ma_phong:false
    };

    function setError(name, msg) {
        if (!fields[name]) return;
        fields[name].classList.add('input-error');
        if (errsDom[name]) errsDom[name].textContent = msg;
    }
    function clearError(name) {
        if (!fields[name]) return;
        fields[name].classList.remove('input-error');
        if (errsDom[name]) errsDom[name].textContent = '';
    }

    function validateField(name, fromSubmit = false) {
        if (!fields[name]) return;
        if (!fromSubmit && !touched[name]) {
            clearError(name);
            return;
        }

        const v = fields[name].value.trim();
        clearError(name);

        switch (name) {
            case 'ten_kh':
                if (!v) {
                    setError(name, 'Vui lòng nhập họ tên trưởng đoàn.');
                } else if (v.length < 2) {
                    setError(name, 'Họ tên phải có ít nhất 2 ký tự.');
                } else if (/\d/.test(v)) {
                    // chỉ cần có 1 chữ số là báo lỗi
                    setError(name, 'Họ tên không được chứa chữ số.');
                }
                break;

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

            case 'email':
                if (!v) {
                    setError(name, 'Vui lòng nhập email.');
                } else {
                    const reEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!reEmail.test(v)) {
                        setError(name, 'Email sai định dạng.');
                    }
                }
                break;

            case 'ngay_den':
            case 'ngay_di': {
                const dDenVal = fields['ngay_den'] ? fields['ngay_den'].value : '';
                const dDiVal  = fields['ngay_di'] ? fields['ngay_di'].value : '';

                clearError('ngay_den');
                clearError('ngay_di');

                const today = new Date();
                today.setHours(0,0,0,0);

                let dDen = null, dDi = null;

                if (!dDenVal) {
                    setError('ngay_den','Vui lòng chọn ngày đến.');
                } else {
                    dDen = new Date(dDenVal);
                    if (dDen < today) {
                        setError('ngay_den','Ngày đến không được nhỏ hơn ngày hiện tại.');
                    }
                }

                if (!dDiVal) {
                    setError('ngay_di','Vui lòng chọn ngày đi.');
                } else {
                    dDi = new Date(dDiVal);
                }

                if (dDen && dDi && dDi <= dDen) {
                    setError('ngay_di','Ngày đi phải lớn hơn ngày đến.');
                }
                break;
            }

            case 'so_nguoi':
                const n = parseInt(v || '0',10);
                if (!v || n <= 0) {
                    setError(name, 'Số người phải lớn hơn 0.');
                }
                break;

            case 'ma_phong':
                if (!v) {
                    setError(name, 'Vui lòng chọn phòng.');
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
            if (errsDom[name] && errsDom[name].textContent) {
                errs.push(errsDom[name].textContent);
            }
        });
        return errs;
    }

    Object.keys(fields).forEach(function (name) {
        if (!fields[name]) return;
        fields[name].addEventListener('blur', function () {
            touched[name] = true;
            validateField(name, false);
        });
        fields[name].addEventListener('input', function () {
            if (touched[name]) {
                validateField(name, false);
            }
        });
    });

    if (formEdit) {
        formEdit.addEventListener('submit', function (e) {
            const submitter = e.submitter || null;
            const action = submitter ? submitter.value : '';

            // Chỉ validate khi nhấn Lưu thông tin
            if (action !== 'save') return;

            clientErrors.style.display = 'none';
            clientErrorsList.innerHTML = '';

            const errs = validateAll(true);
            if (errs.length > 0) {
                e.preventDefault();
                errs.forEach(function (msg) {
                    const li = document.createElement('li');
                    li.textContent = msg;
                    clientErrorsList.appendChild(li);
                });
                clientErrors.style.display = 'block';
                window.scrollTo({top: 0, behavior: 'smooth'});
            }
        });
    }
});
</script>
</body>
</html>