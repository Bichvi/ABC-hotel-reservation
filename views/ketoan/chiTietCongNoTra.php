<?php
// View: Chi tiết công nợ phải trả
$user = $user ?? Auth::user();
$chiTiet = $chiTiet ?? [];
$error = $error ?? '';
$soTienGoc = (float)($chiTiet['SoTienGoc'] ?? 0);
$soTienDaTra = (float)($chiTiet['SoTienDaTra'] ?? 0);
$conNo = max(0, $soTienGoc - $soTienDaTra);
$tyLeHoanThanh = $soTienGoc > 0 ? round(($soTienDaTra / $soTienGoc) * 100, 2) : 0;
$status = $chiTiet['TrangThaiThanhToan'] ?? 'ChuaTra';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Công Nợ Phải Trả - Kế Toán</title>
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
            --accent-yellow: #fbbf24;
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
            padding: 30px 20px 50px;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: var(--accent-blue);
        }

        .info-section {
            background: rgba(30, 41, 59, 0.85);
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            padding: 24px;
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
            margin-bottom: 12px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.12) 0%, rgba(16, 185, 129, 0.12) 100%);
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
            font-weight: 700;
            font-size: 0.85rem;
        }

        .status-chuatra {
            background: rgba(239, 68, 68, 0.2);
            color: #fecaca;
        }

        .status-tramophan {
            background: rgba(251, 191, 36, 0.2);
            color: #fef08a;
        }

        .status-datra {
            background: rgba(16, 185, 129, 0.2);
            color: #86efac;
        }

        .status-quahan {
            background: rgba(239, 68, 68, 0.3);
            color: #fecaca;
            border: 1px solid rgba(239, 68, 68, 0.4);
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
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-file-invoice-dollar me-2" style="color: #10b981;"></i>Chi Tiết Công Nợ Phải Trả
            </a>
            <div class="ms-auto">
                <span style="color: #94a3b8; font-size: 0.9rem;">
                    <i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($user['Username'] ?? 'User') ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="container-fluid main-wrapper">
        <a href="index.php?controller=ketoan&action=congNoPhaiTra" class="btn-back">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>

        <h1 class="page-title">
            <i class="fas fa-coins me-2"></i>Chi Tiết Công Nợ Phải Trả
        </h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php elseif (!empty($chiTiet)): ?>
            <!-- Thông tin nhà cung cấp -->
            <div class="info-section">
                <h5 style="color: var(--accent-blue); margin-bottom: 18px;">
                    <i class="fas fa-store me-2"></i>Thông Tin Nhà Cung Cấp
                </h5>
                <div class="grid">
                    <div>
                        <div class="info-label">Tên Nhà Cung Cấp</div>
                        <div class="info-value"><?= htmlspecialchars($chiTiet['TenNhaCungCap'] ?? 'N/A') ?></div>
                    </div>
                    <div>
                        <div class="info-label">Điện Thoại</div>
                        <div class="info-value"><?= htmlspecialchars($chiTiet['DienThoai'] ?? 'N/A') ?></div>
                    </div>
                    <div>
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($chiTiet['Email'] ?? 'N/A') ?></div>
                    </div>
                    <div>
                        <div class="info-label">Địa Chỉ</div>
                        <div class="info-value" style="font-size: 0.95rem;">
                            <?= htmlspecialchars($chiTiet['DiaChi'] ?? 'N/A') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin công nợ -->
            <div class="info-section">
                <h5 style="color: var(--accent-blue); margin-bottom: 18px;">
                    <i class="fas fa-calculator me-2"></i>Thông Tin Công Nợ
                </h5>
                <div class="grid">
                    <div class="stat-card">
                        <div class="stat-label">Số Tiền Gốc</div>
                        <div class="stat-value">
                            <?= number_format($soTienGoc, 0, ',', '.') ?><br>
                            <small style="font-size: 0.65rem; color: var(--text-muted);">₫</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Đã Thanh Toán</div>
                        <div class="stat-value">
                            <?= number_format($soTienDaTra, 0, ',', '.') ?><br>
                            <small style="font-size: 0.65rem; color: var(--text-muted);">₫</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Còn Nợ</div>
                        <div class="stat-value <?= $conNo > 0 ? 'red' : '' ?>">
                            <?= number_format($conNo, 0, ',', '.') ?><br>
                            <small style="font-size: 0.65rem; color: var(--text-muted);">₫</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Tỷ Lệ Hoàn Thành</div>
                        <div class="stat-value">
                            <?= number_format($tyLeHoanThanh, 2, ',', '.') ?>%<br>
                            <small style="font-size: 0.65rem; color: var(--text-muted);">Đã thanh toán</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trạng thái -->
            <div class="info-section">
                <h5 style="color: var(--accent-blue); margin-bottom: 15px;">
                    <i class="fas fa-info-circle me-2"></i>Trạng Thái
                </h5>
                <div class="grid">
                    <div>
                        <div class="info-label">Trạng Thái Thanh Toán</div>
                        <?php
                        $statusClass = match($status) {
                            'ChuaTra' => 'status-chuatra',
                            'TraMotPhan' => 'status-tramophan',
                            'DaTra' => 'status-datra',
                            'QuaHan' => 'status-quahan',
                            default => 'status-chuatra'
                        };
                        $statusLabel = match($status) {
                            'ChuaTra' => 'Chưa Thanh Toán',
                            'TraMotPhan' => 'Thanh Toán Một Phần',
                            'DaTra' => 'Đã Thanh Toán',
                            'QuaHan' => 'Quá Hạn Thanh Toán',
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
                            <?= isset($chiTiet['NgayPhatSinh']) ? date('d/m/Y H:i', strtotime($chiTiet['NgayPhatSinh'])) : (isset($chiTiet['NgayTao']) ? date('d/m/Y H:i', strtotime($chiTiet['NgayTao'])) : 'N/A') ?>
                        </div>
                    </div>
                    <div>
                        <div class="info-label">Ngày Đến Hạn</div>
                        <div class="info-value">
                            <?php
                            if (!empty($chiTiet['NgayDenHan'])) {
                                $ngayDenHan = strtotime($chiTiet['NgayDenHan']);
                                $hienTai = strtotime(date('Y-m-d'));
                                $quaHan = $hienTai > $ngayDenHan && $status !== 'DaTra';
                                echo date('d/m/Y', $ngayDenHan);
                                if ($quaHan) {
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

            <!-- Thông tin khác -->
            <div class="info-section">
                <h5 style="color: var(--accent-blue); margin-bottom: 15px;">
                    <i class="fas fa-link me-2"></i>Thông Tin Liên Quan
                </h5>
                <div class="grid">
                    <div>
                        <div class="info-label">Mã Công Nợ</div>
                        <div class="info-value">#<?= htmlspecialchars($chiTiet['MaCongNo'] ?? 'N/A') ?></div>
                    </div>
                    <div>
                        <div class="info-label">Ghi Chú</div>
                        <div class="info-value" style="font-size: 0.95rem; white-space: pre-wrap;">
                            <?= !empty($chiTiet['GhiChu']) ? htmlspecialchars($chiTiet['GhiChu']) : 'Không có' ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form thanh toán -->
            <?php if ($status !== 'DaTra'): ?>
            <div class="info-section">
                <h5 style="color: var(--accent-blue); margin-bottom: 18px;">
                    <i class="fas fa-money-bill-wave me-2"></i>Cập Nhật Thanh Toán
                </h5>
                <form id="formThanhToan" class="grid">
                    <div>
                        <label class="info-label">Số Tiền Trả Thêm</label>
                        <input type="number" id="soTienTra" class="form-control" placeholder="0" step="1000" min="0" max="<?= $conNo ?>" data-remaining="<?= $conNo ?>"
                            style="background: rgba(30, 41, 59, 0.85); border: 1px solid var(--border-soft); color: var(--text-main);">
                        <small style="color: var(--text-muted); margin-top: 5px; display: block;">
                            Còn lại: <?= number_format($conNo, 0, ',', '.') ?> ₫
                        </small>
                    </div>
                    <div>
                        <label class="info-label">Ghi Chú</label>
                        <input type="text" id="ghiChu" class="form-control" placeholder="Ví dụ: Chuyển khoản, tiền mặt..."
                            style="background: rgba(30, 41, 59, 0.85); border: 1px solid var(--border-soft); color: var(--text-main);">
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
                    <i class="fas fa-check-circle me-2"></i>Công nợ đã thanh toán hoàn toàn
                </h5>
            </div>
            <?php endif; ?>

            <!-- Hành động -->
            <div style="margin-top: 30px; display: flex; gap: 10px;">
                <a href="index.php?controller=ketoan&action=congNoPhaiTra" class="btn btn-primary" style="background: var(--accent-blue); border: none;">
                    <i class="fas fa-arrow-left me-2"></i>Quay Lại
                </a>
                <button class="btn btn-success" onclick="window.print()" style="background: var(--accent-green); border: none;">
                    <i class="fas fa-print me-2"></i>In Công Nợ
                </button>
            </div>

        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-inbox me-2"></i>Không có dữ liệu
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function submitThanhToan() {
            const input = document.getElementById('soTienTra');
            const soTienTra = parseFloat(input.value) || 0;
            const ghiChu = document.getElementById('ghiChu').value;
            const maCongNo = new URLSearchParams(window.location.search).get('id');
            const remaining = parseFloat(input.dataset.remaining || '0');

            if (!maCongNo) {
                showMessage('error', 'Lỗi: Không xác định được công nợ');
                return;
            }

            if (soTienTra <= 0) {
                showMessage('error', 'Vui lòng nhập số tiền lớn hơn 0');
                return;
            }

            if (soTienTra > remaining) {
                showMessage('error', 'Số tiền thanh toán vượt quá phần còn nợ');
                return;
            }

            const params = new URLSearchParams();
            params.append('id', maCongNo);
            params.append('soTienTra', soTienTra);
            params.append('ghiChu', ghiChu);

            fetch('index.php?controller=ketoan&action=traCongNo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: params.toString()
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP error, status = ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    throw new Error('Không parse được phản hồi từ server');
                }

                if (data.success) {
                    showMessage('success', data.message || 'Cập nhật thành công');
                    setTimeout(() => {
                        location.reload();
                    }, 1800);
                } else {
                    showMessage('error', data.message || 'Có lỗi xảy ra');
                }
            })
            .catch(error => {
                showMessage('error', 'Lỗi: ' + error.message);
            });
        }

        function showMessage(type, message) {
            const div = document.getElementById('messageThanhToan');
            div.className = 'alert ' + (type === 'success' ? 'alert-success' : 'alert-danger');
            div.innerHTML = '<i class="fas ' + (type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle') + ' me-2"></i>' + message;
            div.style.display = 'block';
        }
    </script>
</body>
</html>
