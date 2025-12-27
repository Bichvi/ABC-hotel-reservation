<?php
// View: Chi tiết công nợ phải thu
$user = $user ?? Auth::user();
$chiTiet = $chiTiet ?? [];
$error = $error ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Công Nợ - Kế Toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-main: #0f172a;
            --bg-card: rgba(15, 23, 42, 0.95);
            --border-soft: rgba(148, 163, 184, 0.2);
            --text-main: #e2e8f0;
            --text-muted: #94a3b8;
            --accent-blue: #0ea5e9;
            --accent-green: #10b981;
            --accent-red: #ef4444;
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            color: var(--text-main);
        }

        .navbar {
            background: linear-gradient(90deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%) !important;
            border-bottom: 1px solid var(--border-soft);
        }

        .main-wrapper {
            padding: 30px 20px;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: var(--accent-blue);
        }

        .info-section {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .info-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 15px;
        }

        .customer-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .debt-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.15) 0%, rgba(16, 185, 129, 0.15) 100%);
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent-green);
        }

        .stat-value.red {
            color: var(--accent-red);
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .status-chuathu {
            background: rgba(239, 68, 68, 0.2);
            color: #fecaca;
        }

        .status-thumphan {
            background: rgba(251, 146, 60, 0.2);
            color: #ffedd5;
        }

        .status-dathi {
            background: rgba(16, 185, 129, 0.2);
            color: #86efac;
        }

        .btn-back {
            background: rgba(59, 130, 246, 0.2);
            color: var(--accent-blue);
            border: 1px solid var(--accent-blue);
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: rgba(59, 130, 246, 0.3);
            color: var(--accent-blue);
        }

        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            display: flex;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(30, 41, 59, 0.8);
            border-left: 3px solid var(--accent-blue);
            border-radius: 4px;
        }

        .timeline-dot {
            width: 12px;
            height: 12px;
            background: var(--accent-blue);
            border-radius: 50%;
            margin-right: 15px;
            margin-top: 3px;
            flex-shrink: 0;
        }

        .alert {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fecaca;
            border-radius: 6px;
            padding: 15px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #86efac;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-file-invoice-dollar me-2" style="color: #10b981;"></i>Chi Tiết Công Nợ
            </a>
            <div class="ms-auto">
                <span style="color: #94a3b8; font-size: 0.9rem;">
                    <i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($user['Username'] ?? 'User') ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="container-fluid main-wrapper">
        <a href="index.php?controller=ketoan&action=congNoPhaiThu" class="btn-back">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>

        <h1 class="page-title">
            <i class="fas fa-file-invoice-dollar me-2"></i>Chi Tiết Công Nợ Phải Thu
        </h1>

        <?php if (!empty($error)): ?>
            <div class="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php elseif (!empty($chiTiet)): ?>
            <!-- Thông tin khách hàng -->
            <div class="info-section">
                <h5 style="color: var(--accent-blue); margin-bottom: 20px;">
                    <i class="fas fa-user me-2"></i>Thông Tin Khách Hàng
                </h5>
                <div class="customer-info">
                    <div>
                        <div class="info-label">Tên Khách Hàng</div>
                        <div class="info-value"><?= htmlspecialchars($chiTiet['TenKH'] ?? 'N/A') ?></div>
                    </div>
                    <div>
                        <div class="info-label">Số Điện Thoại</div>
                        <div class="info-value"><?= htmlspecialchars($chiTiet['SDT'] ?? 'N/A') ?></div>
                    </div>
                    <div>
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($chiTiet['Email'] ?? 'N/A') ?></div>
                    </div>
                    <div>
                        <div class="info-label">Địa Chỉ</div>
                        <div class="info-value" style="font-size: 0.95rem;"><?= htmlspecialchars($chiTiet['DiaChi'] ?? 'N/A') ?></div>
                    </div>
                </div>
            </div>

            <!-- Thông tin công nợ -->
            <div class="info-section">
                <h5 style="color: var(--accent-blue); margin-bottom: 20px;">
                    <i class="fas fa-calculator me-2"></i>Thông Tin Công Nợ
                </h5>
                <div class="debt-info">
                    <div class="stat-card">
                        <div class="stat-label">Số Tiền Gốc</div>
                        <div class="stat-value">
                            <?= number_format($chiTiet['SoTienGoc'] ?? 0, 0, ',', '.') ?><br>
                            <small style="font-size: 0.5em; color: var(--text-muted);">₫</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Đã Thanh Toán</div>
                        <div class="stat-value">
                            <?= number_format($chiTiet['SoTienDaThu'] ?? 0, 0, ',', '.') ?><br>
                            <small style="font-size: 0.5em; color: var(--text-muted);">₫</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Còn Nợ</div>
                        <div class="stat-value <?= ($chiTiet['SoTienGoc'] - $chiTiet['SoTienDaThu']) > 0 ? 'red' : '' ?>">
                            <?= number_format(($chiTiet['SoTienGoc'] - $chiTiet['SoTienDaThu']), 0, ',', '.') ?><br>
                            <small style="font-size: 0.5em; color: var(--text-muted);">₫</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Tỷ Lệ Hoàn Thành</div>
                        <div class="stat-value">
                            <?php
                            $tyLe = $chiTiet['SoTienGoc'] > 0 
                                ? round(($chiTiet['SoTienDaThu'] / $chiTiet['SoTienGoc']) * 100, 2)
                                : 0;
                            echo number_format($tyLe, 2, ',', '.') . '%';
                            ?><br>
                            <small style="font-size: 0.5em; color: var(--text-muted);"></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trạng thái -->
            <div class="info-section">
                <h5 style="color: var(--accent-blue); margin-bottom: 15px;">
                    <i class="fas fa-info-circle me-2"></i>Trạng Thái
                </h5>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div>
                        <div class="info-label">Trạng Thái Thanh Toán</div>
                        <?php
                        $status = $chiTiet['TrangThaiThanhToan'] ?? 'ChuaThu';
                        $statusClass = match($status) {
                            'ChuaThu' => 'status-chuathu',
                            'ThuMotPhan' => 'status-thumphan',
                            'DaThu' => 'status-dathi',
                            default => 'status-chuathu'
                        };
                        $statusLabel = match($status) {
                            'ChuaThu' => 'Chưa Thanh Toán',
                            'ThuMotPhan' => 'Thanh Toán Một Phần',
                            'DaThu' => 'Đã Thanh Toán',
                            default => 'Chưa Thanh Toán'
                        };
                        ?>
                        <span class="status-badge <?= $statusClass ?>">
                            <?= $statusLabel ?>
                        </span>
                    </div>
                    <div>
                        <div class="info-label">Ngày Phát Sinh</div>
                        <div class="info-value">
                            <?= isset($chiTiet['NgayPhatSinh']) ? date('d/m/Y H:i', strtotime($chiTiet['NgayPhatSinh'])) : 'N/A' ?>
                        </div>
                    </div>
                    <div>
                        <div class="info-label">Ngày Đến Hạn</div>
                        <div class="info-value">
                            <?php
                            if (isset($chiTiet['NgayDenHan'])) {
                                $ngayDenHan = strtotime($chiTiet['NgayDenHan']);
                                $hienTai = strtotime(date('Y-m-d'));
                                $quaHan = $hienTai > $ngayDenHan;
                                echo date('d/m/Y', $ngayDenHan);
                                if ($quaHan && $chiTiet['TrangThaiThanhToan'] !== 'DaThu') {
                                    echo ' <span style="color: var(--accent-red); font-weight: bold;">(QUÁ HẠN)</span>';
                                }
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Giao dịch liên quan -->
            <div class="info-section">
                <h5 style="color: var(--accent-blue); margin-bottom: 15px;">
                    <i class="fas fa-exchange-alt me-2"></i>Giao Dịch Liên Quan
                </h5>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div>
                        <div class="info-label">Mã Giao Dịch</div>
                        <div class="info-value">#<?= htmlspecialchars($chiTiet['MaGiaoDich'] ?? 'N/A') ?></div>
                    </div>
                    <div>
                        <div class="info-label">Tổng Tiền Giao Dịch</div>
                        <div class="info-value">
                            <?= number_format($chiTiet['TongTien'] ?? 0, 0, ',', '.') ?> ₫
                        </div>
                    </div>
                    <div>
                        <div class="info-label">Trạng Thái Giao Dịch</div>
                        <div class="info-value">
                            <?= htmlspecialchars($chiTiet['TrangThaiGiaoDich'] ?? 'N/A') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ghi chú -->
            <?php if (!empty($chiTiet['GhiChu'])): ?>
            <div class="info-section">
                <h5 style="color: var(--accent-blue); margin-bottom: 15px;">
                    <i class="fas fa-sticky-note me-2"></i>Ghi Chú
                </h5>
                <div style="padding: 15px; background: rgba(15, 23, 42, 0.5); border-radius: 6px; border-left: 3px solid var(--accent-blue);">
                    <?= htmlspecialchars($chiTiet['GhiChu']) ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Form thanh toán -->
            <?php if ($chiTiet['TrangThaiThanhToan'] !== 'DaThu'): ?>
            <div class="info-section">
                <h5 style="color: var(--accent-blue); margin-bottom: 20px;">
                    <i class="fas fa-money-bill-wave me-2"></i>Cập Nhật Thanh Toán
                </h5>
                <form id="formThanhToan" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div>
                        <label class="info-label">Số Tiền Thu Thêm</label>
                        <input type="number" id="soTienThu" class="form-control" placeholder="0" step="1000" min="0" 
                            max="<?= ($chiTiet['SoTienGoc'] - $chiTiet['SoTienDaThu']) ?>" 
                            style="background: rgba(30, 41, 59, 0.8); border: 1px solid var(--border-soft); color: var(--text-main);">
                        <small style="color: var(--text-muted); margin-top: 5px; display: block;">
                            Max: <?= number_format($chiTiet['SoTienGoc'] - $chiTiet['SoTienDaThu'], 0, ',', '.') ?> ₫
                        </small>
                    </div>
                    <div>
                        <label class="info-label">Ghi Chú</label>
                        <input type="text" id="ghiChu" class="form-control" placeholder="Ví dụ: Thanh toán qua chuyển khoản..." 
                            style="background: rgba(30, 41, 59, 0.8); border: 1px solid var(--border-soft); color: var(--text-main);">
                    </div>
                    <div style="display: flex; gap: 10px; align-items: flex-end;">
                        <button type="button" onclick="submitThanhToan()" class="btn btn-success" style="background: var(--accent-green); border: none; flex: 1;">
                            <i class="fas fa-check me-2"></i>Cập Nhật Thanh Toán
                        </button>
                    </div>
                </form>
                <div id="messageThanhToan" style="margin-top: 15px; display: none;"></div>
            </div>
            <?php else: ?>
            <div class="info-section" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(16, 185, 129, 0.1) 100%); border: 1px solid var(--accent-green);">
                <h5 style="color: var(--accent-green); margin-bottom: 0;">
                    <i class="fas fa-check-circle me-2"></i>Công Nợ Đã Thanh Toán Hoàn Toàn
                </h5>
            </div>
            <?php endif; ?>

            <!-- Hành động -->
            <div style="margin-top: 30px; display: flex; gap: 10px;">
                <a href="index.php?controller=ketoan&action=congNoPhaiThu" class="btn btn-primary" style="background: var(--accent-blue); border: none;">
                    <i class="fas fa-arrow-left me-2"></i>Quay Lại
                </a>
                <button class="btn btn-success" onclick="window.print()" style="background: var(--accent-green); border: none;">
                    <i class="fas fa-print me-2"></i>In Công Nợ
                </button>
            </div>

        <?php else: ?>
            <div class="alert">
                <i class="fas fa-inbox me-2"></i>Không có dữ liệu
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function submitThanhToan() {
            const soTienThu = parseFloat(document.getElementById('soTienThu').value) || 0;
            const ghiChu = document.getElementById('ghiChu').value;
            const maCongNo = new URLSearchParams(window.location.search).get('id');

            console.log('Submitting payment:', { maCongNo, soTienThu, ghiChu });

            if (!maCongNo) {
                showMessage('error', 'Lỗi: Không xác định được công nợ');
                return;
            }

            if (soTienThu <= 0) {
                showMessage('error', 'Vui lòng nhập số tiền lớn hơn 0');
                return;
            }

            const params = new URLSearchParams();
            params.append('id', maCongNo);
            params.append('soTienThu', soTienThu);
            params.append('ghiChu', ghiChu);

            fetch('index.php?controller=ketoan&action=thuCongNo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: params.toString()
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('HTTP error, status = ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);
                const data = JSON.parse(text);
                if (data.success) {
                    showMessage('success', data.message);
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showMessage('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('error', 'Lỗi: ' + error.message);
            });
        }

        function showMessage(type, message) {
            const div = document.getElementById('messageThanhToan');
            div.className = `alert alert-${type === 'success' ? 'success' : 'alert-danger'}`;
            div.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2></i>${message}`;
            div.style.display = 'block';
        }
    </script>
</body>
</html>

