<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;

// $form, $errors, $success, $createdAccounts được controller truyền sang
$form            = $form            ?? [];
$errors          = $errors          ?? [];
$success         = $success         ?? null;
$createdAccounts = $createdAccounts ?? [];

// Lấy số người để render số dòng thành viên ban đầu
$soNguoiForm = isset($form['so_nguoi']) ? (int)$form['so_nguoi'] : 3;
if ($soNguoiForm <= 0) {
    $soNguoiForm = 3;
}
$tvHoTen = $form['tv_ho_ten'] ?? [];
$maxRows = max($soNguoiForm, count($tvHoTen)); // ít nhất = số lượng thành viên
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản đoàn - Lễ tân</title>
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
            grid-template-columns: minmax(0, 2.2fr) minmax(0, 3fr);
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
        input[type="email"] {
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
        input:focus {
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
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }
        thead { background: #f9fafb; }
        .btn-primary {
            border: none;
            border-radius: 999px;
            padding: 9px 18px;
            font-size: 14px;
            font-weight: 500;
            color: #f9fafb;
            background: linear-gradient(135deg,#16a34a,#15803d);
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
        .btn-danger {
            border-radius: 999px;
            border: 1px solid #b91c1c;
            background: #fee2e2;
            color: #b91c1c;
            padding: 8px 14px;
            font-size: 13px;
            cursor: pointer;
        }
        .btn-row {
            margin-top: 12px;
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

        .result-card {
            margin-top: 20px;
            background: #ffffff;
            border-radius: 12px;
            padding: 16px 18px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 20px rgba(15,23,42,0.04);
        }
    </style>
</head>
<body>
<div class="topbar">
    <div>
        <div class="brand">ABC RESORT · LỄ TÂN</div>
        <div class="role">Đăng ký tài khoản đoàn / khách</div>
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
            <h1>Đăng ký tài khoản (đoàn / khách)</h1>
            <span>Nhập thông tin trưởng đoàn và thành viên → Hệ thống sinh username + mật khẩu tạm</span>
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

    <!-- Lỗi phía client -->
    <div id="clientErrors" class="alert alert-error" style="display:none;">
        <strong>Có lỗi xảy ra:</strong>
        <ul id="clientErrorsList" style="margin:6px 0 0 18px;padding:0;"></ul>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <form method="post" id="formDangKyDoan">
        <div class="grid">
            <!-- Cột trái: Trưởng đoàn -->
            <div class="card">
                <h2>Thông tin trưởng đoàn</h2>
                <p class="caption">
                    Các trường <strong>*</strong> là bắt buộc. Nếu thiếu hoặc sai định dạng, ô sẽ được tô đỏ.
                </p>

                <div class="field">
                    <label>Họ tên trưởng đoàn <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="leader_name" name="leader_name"
                           value="<?php echo htmlspecialchars($form['leader_name'] ?? ''); ?>"
                           placeholder="Nguyễn Văn A">
                    <div class="error-msg" id="err_leader_name"></div>
                </div>

                <div class="field">
                    <label>CMND / CCCD <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="leader_cccd" name="leader_cccd"
                           value="<?php echo htmlspecialchars($form['leader_cccd'] ?? ''); ?>"
                           placeholder="Chỉ gồm 9–12 chữ số">
                    <div class="error-msg" id="err_leader_cccd"></div>
                </div>

                <div class="field">
                    <label>Số điện thoại <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="leader_sdt" name="leader_sdt"
                           value="<?php echo htmlspecialchars($form['leader_sdt'] ?? ''); ?>"
                           placeholder="09xxxxxxxx">
                    <div class="error-msg" id="err_leader_sdt"></div>
                </div>

                <div class="field">
                    <label>Email <span style="color:#dc2626;">*</span></label>
                    <input type="email" id="leader_email" name="leader_email"
                           value="<?php echo htmlspecialchars($form['leader_email'] ?? ''); ?>"
                           placeholder="truongdoan@email.com">
                    <div class="error-msg" id="err_leader_email"></div>
                </div>

                <div class="field">
                    <label>Địa chỉ</label>
                    <input type="text" id="leader_diachi" name="leader_diachi"
                           value="<?php echo htmlspecialchars($form['leader_diachi'] ?? ''); ?>"
                           placeholder="Địa chỉ liên hệ">
                </div>

                <div class="field">
                    <label>Số lượng thành viên <span style="color:#dc2626;">*</span></label>
                    <input type="number" id="so_nguoi" name="so_nguoi" min="1" max="200"
                           value="<?php echo htmlspecialchars($form['so_nguoi'] ?? '3'); ?>">
                    <div class="error-msg" id="err_so_nguoi"></div>
                </div>

                <div class="btn-row">
                    <button type="button" class="btn-danger" id="btnCancel">
                        Hủy đăng ký
                    </button>
                    <div style="display:flex;gap:10px;">
                        <button type="reset" class="btn-secondary">Làm lại</button>
                        <button type="submit" name="btn_register" value="1" class="btn-primary">
                            Đăng ký tài khoản
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Danh sách thành viên -->
            <div class="card">
                <h2>Danh sách thành viên</h2>
                <p class="caption">
                    Nhập danh sách thành viên (ít nhất họ tên).<br>
                    CMND/CCCD, Email, SĐT nếu trống → hệ thống đánh dấu “Chưa đầy đủ thông tin”.
                </p>

                <div class="section-title">Thông tin từng thành viên</div>
                <table id="tblMembers">
                    <thead>
                    <tr>
                        <th style="width:22%;">Họ tên</th>
                        <th style="width:18%;">CMND/CCCD</th>
                        <th style="width:18%;">Ngày sinh</th>
                        <th style="width:18%;">SĐT</th>
                        <th style="width:24%;">Email</th>
                    </tr>
                    </thead>
                   <tbody>
<?php
// render 5 dòng mặc định; fill dữ liệu nếu có
$maxRows = max(5, count($form['tv_ho_ten'] ?? []));
for ($i = 0; $i < $maxRows; $i++):
    $name   = $form['tv_ho_ten'][$i]   ?? '';
    $cccd   = $form['tv_cccd'][$i]     ?? '';
    $dob    = $form['tv_ngaysinh'][$i] ?? '';
    $sdt    = $form['tv_sdt'][$i]      ?? '';
    $email  = $form['tv_email'][$i]    ?? '';
?>
    <tr>
        <td>
            <input type="text" name="tv_ho_ten[]" value="<?php echo htmlspecialchars($name); ?>">
            <div class="error-msg"></div>
        </td>
        <td>
            <input type="text" name="tv_cccd[]" value="<?php echo htmlspecialchars($cccd); ?>">
            <div class="error-msg"></div>
        </td>
        <td>
            <input type="date" name="tv_ngaysinh[]" value="<?php echo htmlspecialchars($dob); ?>">
        </td>
        <td>
            <input type="text" name="tv_sdt[]" value="<?php echo htmlspecialchars($sdt); ?>">
            <div class="error-msg"></div>
        </td>
        <td>
            <input type="email" name="tv_email[]" value="<?php echo htmlspecialchars($email); ?>">
            <div class="error-msg"></div>
        </td>
    </tr>
<?php endfor; ?>
</tbody>
                </table>
                <div style="margin-top:8px;font-size:12px;color:#6b7280;">
                    Gợi ý: có thể để trống các dòng dư; hệ thống chỉ tính những dòng có nhập họ tên.
                </div>
            </div>
        </div>
    </form>

    <?php if (!empty($createdAccounts)): ?>
        <div class="result-card">
            <div class="section-title">Danh sách tài khoản được tạo / liên kết</div>
            <p class="caption">
                Bàn giao username + mật khẩu tạm cho trưởng đoàn. Yêu cầu khách đổi mật khẩu sau khi đăng nhập lần đầu.
            </p>
            <table>
                <thead>
                <tr>
                    <th>Họ tên</th>
                    <th>CMND/CCCD</th>
                    <th>Vai trò</th>
                    <th>Username</th>
                    <th>Mật khẩu tạm</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($createdAccounts as $acc): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($acc['hoTen']); ?></td>
                        <td><?php echo htmlspecialchars($acc['cmnd']); ?></td>
                        <td><?php echo htmlspecialchars($acc['vaiTro']); ?></td>
                        <td><?php echo htmlspecialchars($acc['username']); ?></td>
                        <td><?php echo htmlspecialchars($acc['password']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formDangKyDoan');
    const clientErrors = document.getElementById('clientErrors');
    const clientErrorsList = document.getElementById('clientErrorsList');
    const btnCancel = document.getElementById('btnCancel');

    const fields = {
        leader_name:  document.getElementById('leader_name'),
        leader_cccd:  document.getElementById('leader_cccd'),
        leader_sdt:   document.getElementById('leader_sdt'),
        leader_email: document.getElementById('leader_email'),
        so_nguoi:     document.getElementById('so_nguoi')
    };

    const errsDom = {
        leader_name:  document.getElementById('err_leader_name'),
        leader_cccd:  document.getElementById('err_leader_cccd'),
        leader_sdt:   document.getElementById('err_leader_sdt'),
        leader_email: document.getElementById('err_leader_email'),
        so_nguoi:     document.getElementById('err_so_nguoi')
    };

    const reEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const reName  = /^[A-Za-zÀ-ỹà-ỹĂăÂâÊêÔôƠơƯưĐđ\s]+$/u;

    // ===== Trưởng đoàn =====
    function setError(name, msg) {
        fields[name].classList.add('input-error');
        errsDom[name].textContent = msg;
    }
    function clearError(name) {
        fields[name].classList.remove('input-error');
        errsDom[name].textContent = '';
    }

    function validateLeaderField(name, fromBlur) {
        const el = fields[name];
        const v  = el.value.trim();

        // luôn clear trước
        clearError(name);

        switch (name) {
            case 'leader_name':
                if (!v) {
                    if (fromBlur) setError(name, 'Vui lòng nhập họ tên trưởng đoàn.');
                    return;
                }
                if (!reName.test(v)) {
                    setError(name, 'Tên chỉ được chứa chữ cái và khoảng trắng.');
                }
                break;

            case 'leader_cccd':
                if (!v) {
                    if (fromBlur) setError(name, 'Vui lòng nhập CMND/CCCD.');
                    return;
                }
                if (!/^\d{9,12}$/.test(v)) {
                    setError(name, 'CMND/CCCD phải từ 9–12 chữ số.');
                }
                break;

            case 'leader_sdt':
                if (!v) {
                    if (fromBlur) setError(name, 'Vui lòng nhập số điện thoại.');
                    return;
                }
                if (!/^0\d{8,10}$/.test(v)) {
                    setError(name, 'Số điện thoại sai định dạng (09xxxxxxxx).');
                }
                break;

            case 'leader_email':
                if (!v) {
                    if (fromBlur) setError(name, 'Vui lòng nhập email.');
                    return;
                }
                if (!reEmail.test(v)) {
                    setError(name, 'Email sai định dạng (vd: ten@gmail.com).');
                }
                break;

            case 'so_nguoi':
                if (!v) {
                    if (fromBlur) setError(name, 'Vui lòng nhập số lượng thành viên.');
                    return;
                }
                const n = parseInt(v, 10);
                if (isNaN(n) || n <= 0) {
                    setError(name, 'Số lượng thành viên phải lớn hơn 0.');
                } else if (n > 100) {
                    setError(name, 'Số lượng thành viên tối đa là 100.');
                }
                break;
        }
    }

    // blur: luôn validate
    // input: chỉ validate lại nếu đang có lỗi (để clear khi sửa đúng)
    Object.keys(fields).forEach(function (name) {
        fields[name].addEventListener('blur', function () {
            validateLeaderField(name, true);
        });
        fields[name].addEventListener('input', function () {
            if (fields[name].classList.contains('input-error')) {
                validateLeaderField(name, false);
            }
        });
    });

    // ===== Thành viên =====
    function setInputError(input, msg) {
        input.classList.add('input-error');
        const errDiv = input.parentElement.querySelector('.error-msg');
        if (errDiv) errDiv.textContent = msg;
    }
    function clearInputError(input) {
        input.classList.remove('input-error');
        const errDiv = input.parentElement.querySelector('.error-msg');
        if (errDiv) errDiv.textContent = '';
    }

    function validateMemberInput(input, type, fromBlur) {
        const v = input.value.trim();
        clearInputError(input);

        if (type === 'name') {
            // name bắt buộc nếu đã blur
            if (!v) {
                if (fromBlur) setInputError(input, 'Vui lòng nhập họ tên thành viên.');
                return;
            }
            if (!reName.test(v)) {
                setInputError(input, 'Tên chỉ chứa chữ cái và khoảng trắng.');
            }
        } else if (type === 'cccd') {
            if (!v) return; // cho phép trống
            if (!/^\d{9,12}$/.test(v)) {
                setInputError(input, 'CCCD phải từ 9–12 chữ số.');
            }
        } else if (type === 'sdt') {
            if (!v) return;
            if (!/^0\d{8,10}$/.test(v)) {
                setInputError(input, 'SĐT không hợp lệ (09xxxxxxxx).');
            }
        } else if (type === 'email') {
            if (!v) return;
            if (!reEmail.test(v)) {
                setInputError(input, 'Email không hợp lệ.');
            }
        }
    }

    function attachMemberRealtimeValidation() {
        const rows = document.querySelectorAll('#tblMembers tbody tr');

        rows.forEach((tr) => {
            const nameInput  = tr.querySelector('input[name="tv_ho_ten[]"]');
            const cccdInput  = tr.querySelector('input[name="tv_cccd[]"]');
            const sdtInput   = tr.querySelector('input[name="tv_sdt[]"]');
            const emailInput = tr.querySelector('input[name="tv_email[]"]');

            if (nameInput) {
                nameInput.addEventListener('blur',  () => validateMemberInput(nameInput, 'name', true));
                nameInput.addEventListener('input', () => {
                    if (nameInput.classList.contains('input-error')) {
                        validateMemberInput(nameInput, 'name', false);
                    }
                });
            }
            if (cccdInput) {
                cccdInput.addEventListener('blur',  () => validateMemberInput(cccdInput, 'cccd', true));
                cccdInput.addEventListener('input', () => {
                    if (cccdInput.classList.contains('input-error')) {
                        validateMemberInput(cccdInput, 'cccd', false);
                    }
                });
            }
            if (sdtInput) {
                sdtInput.addEventListener('blur',  () => validateMemberInput(sdtInput, 'sdt', true));
                sdtInput.addEventListener('input', () => {
                    if (sdtInput.classList.contains('input-error')) {
                        validateMemberInput(sdtInput, 'sdt', false);
                    }
                });
            }
            if (emailInput) {
                emailInput.addEventListener('blur',  () => validateMemberInput(emailInput, 'email', true));
                emailInput.addEventListener('input', () => {
                    if (emailInput.classList.contains('input-error')) {
                        validateMemberInput(emailInput, 'email', false);
                    }
                });
            }
        });
    }

    attachMemberRealtimeValidation();

    // ===== Đồng bộ số dòng thành viên theo "Số lượng thành viên" =====
    function syncMemberRows() {
        const tbody = document.querySelector('#tblMembers tbody');
        let n = parseInt(fields['so_nguoi'].value || '0', 10);
        if (isNaN(n) || n <= 0) return;

        let current = tbody.children.length;

        // thêm dòng nếu thiếu
        while (current < n) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <input type="text" name="tv_ho_ten[]">
                    <div class="error-msg"></div>
                </td>
                <td>
                    <input type="text" name="tv_cccd[]">
                    <div class="error-msg"></div>
                </td>
                <td>
                    <input type="date" name="tv_ngaysinh[]">
                </td>
                <td>
                    <input type="text" name="tv_sdt[]">
                    <div class="error-msg"></div>
                </td>
                <td>
                    <input type="email" name="tv_email[]">
                    <div class="error-msg"></div>
                </td>
            `;
            tbody.appendChild(tr);
            current++;
        }

        // bớt dòng nếu thừa
        while (current > n) {
            tbody.removeChild(tbody.lastElementChild);
            current--;
        }

        // gán lại event cho các input mới
        attachMemberRealtimeValidation();
    }

    fields['so_nguoi'].addEventListener('change', function () {
        validateLeaderField('so_nguoi', true);
        syncMemberRows();
    });

    // ===== Validate toàn form khi submit =====
    form.addEventListener('submit', function (e) {
        // nếu bấm Hủy thì không validate
        if (e.submitter && e.submitter.id === 'btnCancel') {
            return;
        }

        clientErrors.style.display = 'none';
        clientErrorsList.innerHTML = '';
        let errs = [];

        // validate trưởng đoàn lần nữa
        Object.keys(fields).forEach(name => validateLeaderField(name, true));
        Object.keys(errsDom).forEach(name => {
            if (errsDom[name].textContent) errs.push(errsDom[name].textContent);
        });

        // validate thành viên lúc submit + kiểm tra số lượng khớp
        const rows = document.querySelectorAll('#tblMembers tbody tr');
        let countFilled = 0;
        const soNguoi = parseInt(fields['so_nguoi'].value || '0', 10);

        rows.forEach((tr, i) => {
            const nameInput  = tr.querySelector('input[name="tv_ho_ten[]"]');
            const cccdInput  = tr.querySelector('input[name="tv_cccd[]"]');
            const sdtInput   = tr.querySelector('input[name="tv_sdt[]"]');
            const emailInput = tr.querySelector('input[name="tv_email[]"]');

            const name  = nameInput ? nameInput.value.trim() : '';
            const cccd  = cccdInput ? cccdInput.value.trim() : '';
            const sdt   = sdtInput ? sdtInput.value.trim() : '';
            const email = emailInput ? emailInput.value.trim() : '';

            if (name !== '') {
                countFilled++;
                if (!reName.test(name)) errs.push('Thành viên ' + (i+1) + ': tên không hợp lệ.');
                if (cccd !== '' && !/^\d{9,12}$/.test(cccd)) errs.push('Thành viên ' + (i+1) + ': CCCD không hợp lệ.');
                if (sdt !== '' && !/^0\d{8,10}$/.test(sdt)) errs.push('Thành viên ' + (i+1) + ': SĐT không hợp lệ.');
                if (email !== '' && !reEmail.test(email)) errs.push('Thành viên ' + (i+1) + ': Email không hợp lệ.');
            }
        });

        if (soNguoi > 0 && countFilled !== soNguoi) {
            errs.push('Số lượng thành viên (' + soNguoi + ') không khớp với số dòng có họ tên (' + countFilled + ').');
        }

        if (errs.length > 0) {
            e.preventDefault();
            errs.forEach(msg => {
                const li = document.createElement('li');
                li.textContent = msg;
                clientErrorsList.appendChild(li);
            });
            clientErrors.style.display = 'block';
            window.scrollTo({top: 0, behavior: 'smooth'});
        }
    });

    // HỦY ĐĂNG KÝ
    btnCancel.addEventListener('click', function () {
        if (confirm('Bạn có chắc muốn hủy thao tác đăng ký không?')) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'btn_cancel';
            input.value = '1';
            form.appendChild(input);
            form.submit();
        }
    });
});
</script>
</body>
</html>