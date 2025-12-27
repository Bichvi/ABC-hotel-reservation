<?php
// View: Đối soát nganh quỹ (Cash Reconciliation)
$user = $user ?? Auth::user();
$doiSoat = $doiSoat ?? [];
$error = $error ?? '';
$tongChieuDuGhi = $tongChieuDuGhi ?? 0;
$tongChieuDuNganHang = $tongChieuDuNganHang ?? 0;
$chenhLech = $chenhLech ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đối Soát Nganh Quỹ - Kế Toán</title>
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
            padding: 30px 20px;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: var(--accent-blue);
        }

        .reconciliation-section {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--accent-blue);
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-soft);
        }

        .balance-comparison {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .comparison-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(15, 23, 42, 0.8) 100%);
            border: 2px solid var(--border-soft);
            border-radius: 8px;
            padding: 25px;
            transition: all 0.3s ease;
        }

        .comparison-card:hover {
            border-color: var(--accent-blue);
        }

        .card-label {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin-bottom: 15px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .card-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--accent-blue);
        }

        .balance-info {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .status-panel {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(15, 23, 42, 0.8) 100%);
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .status-item {
            background: rgba(15, 23, 42, 0.8);
            border-radius: 6px;
            padding: 15px;
            border-left: 4px solid;
        }

        .status-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-value {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .status-item.matched {
            border-left-color: var(--accent-green);
        }

        .status-item.matched .status-value {
            color: var(--accent-green);
        }

        .status-item.unmatched {
            border-left-color: var(--accent-red);
        }

        .status-item.unmatched .status-value {
            color: var(--accent-red);
        }

        .status-item.variance {
            border-left-color: var(--accent-yellow);
        }

        .status-item.variance .status-value {
            color: var(--accent-yellow);
        }

        .table-container {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .table {
            margin-bottom: 0;
            color: var(--text-main);
        }

        .table thead {
            background: rgba(15, 23, 42, 0.8);
            border-bottom: 2px solid var(--border-soft);
        }

        .table th {
            padding: 15px 18px;
            font-weight: 700;
            color: var(--accent-blue);
            border: none;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            border-bottom: 1px solid var(--border-soft);
            transition: background 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(14, 165, 233, 0.05);
        }

        .table td {
            padding: 14px 18px;
            vertical-align: middle;
        }

        .matched-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .matched-yes {
            background: rgba(16, 185, 129, 0.2);
            color: var(--accent-green);
            border: 1px solid rgba(16, 185, 129, 0.5);
        }

        .matched-no {
            background: rgba(239, 68, 68, 0.2);
            color: var(--accent-red);
            border: 1px solid rgba(239, 68, 68, 0.5);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 6px;
            border: none;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-match {
            background: var(--accent-green);
            color: white;
        }

        .btn-match:hover {
            background: #059669;
            color: white;
        }

        .btn-view {
            background: var(--accent-blue);
            color: white;
        }

        .btn-view:hover {
            background: #0284c7;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .alert-variance {
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.15) 0%, rgba(251, 191, 36, 0.1) 100%);
            border: 2px solid var(--accent-yellow);
            color: var(--accent-yellow);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .alert-matched {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(16, 185, 129, 0.1) 100%);
            border: 2px solid var(--accent-green);
            color: var(--accent-green);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-sync-alt me-2" style="color: #10b981;"></i>Đối Soát Nganh Quỹ
            </a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <a href="index.php?controller=ketoan&action=dashboard" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Quay lại Dashboard
                </a>
                <span style="color: #94a3b8; font-size: 0.9rem;">
                    <i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($user['Username'] ?? 'User') ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="container-fluid main-wrapper">
        <h1 class="page-title">
            <i class="fas fa-sync-alt me-2"></i>Đối Soát Nganh Quỹ (Cash Reconciliation)
        </h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- So sánh số dư -->
        <div class="reconciliation-section">
            <h3 class="section-title">
                <i class="fas fa-balance-scale me-2"></i>So Sánh Số Dư
            </h3>

            <div class="balance-comparison">
                <div class="comparison-card">
                    <div class="card-label">
                        <i class="fas fa-book me-2"></i>Số Dư Ghi Sổ (Hệ Thống)
                    </div>
                    <div class="card-value">
                        <?= number_format($tongChieuDuGhi, 0, ',', '.') ?>
                    </div>
                    <div class="balance-info">
                        <strong>₫</strong> theo sổ nganh quỹ
                    </div>
                </div>

                <div class="comparison-card">
                    <div class="card-label">
                        <i class="fas fa-bank me-2"></i>Số Dư Ngân Hàng
                    </div>
                    <div class="card-value">
                        <?= number_format($tongChieuDuNganHang, 0, ',', '.') ?>
                    </div>
                    <div class="balance-info">
                        <strong>₫</strong> theo bảng kê ngân hàng
                    </div>
                </div>

                <div class="comparison-card" style="<?= abs($chenhLech) == 0 ? 'border-color: var(--accent-green);' : 'border-color: var(--accent-red);' ?>">
                    <div class="card-label">
                        <i class="fas fa-exchange-alt me-2"></i>Chênh Lệch
                    </div>
                    <div class="card-value" style="<?= abs($chenhLech) == 0 ? 'color: var(--accent-green);' : 'color: var(--accent-red);' ?>">
                        <?= number_format(abs($chenhLech), 0, ',', '.') ?>
                    </div>
                    <div class="balance-info">
                        <?php if ($chenhLech == 0): ?>
                            <i class="fas fa-check-circle me-2" style="color: var(--accent-green);"></i>Khớp đúng
                        <?php elseif ($chenhLech > 0): ?>
                            <i class="fas fa-arrow-up me-2" style="color: var(--accent-red);"></i>Thiếu ₫
                        <?php else: ?>
                            <i class="fas fa-arrow-down me-2" style="color: var(--accent-red);"></i>Thừa ₫
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($chenhLech == 0): ?>
                <div class="alert-matched">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Hoàn tất đối soát!</strong> Số dư ghi sổ khớp với số dư ngân hàng
                </div>
            <?php else: ?>
                <div class="alert-variance">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Cần kiểm tra!</strong> Chênh lệch giữa sổ ghi và ngân hàng: <strong><?= number_format(abs($chenhLech), 0, ',', '.') ?> ₫</strong>
                </div>
            <?php endif; ?>
        </div>

        <!-- Thống kê -->
        <div class="status-panel">
            <div class="status-grid">
                <div class="status-item matched">
                    <div class="status-label">Đã Đối Soát</div>
                    <div class="status-value">
                        <?php
                        $matched = count(array_filter($doiSoat ?? [], function($item) {
                            return $item['DaDoiSoat'] == 1;
                        }));
                        echo $matched;
                        ?>
                    </div>
                </div>

                <div class="status-item unmatched">
                    <div class="status-label">Chưa Đối Soát</div>
                    <div class="status-value">
                        <?php
                        $unmatched = count(array_filter($doiSoat ?? [], function($item) {
                            return $item['DaDoiSoat'] == 0;
                        }));
                        echo $unmatched;
                        ?>
                    </div>
                </div>

                <div class="status-item">
                    <div class="status-label">Tổng Giao Dịch</div>
                    <div class="status-value">
                        <?= count($doiSoat ?? []) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng giao dịch chưa đối soát -->
        <div class="reconciliation-section">
            <h3 class="section-title">
                <i class="fas fa-list me-2"></i>Giao Dịch Chưa Đối Soát
            </h3>

            <?php 
            $unmatchedItems = array_filter($doiSoat ?? [], function($item) {
                return $item['DaDoiSoat'] == 0;
            });
            ?>

            <?php if (!empty($unmatchedItems)): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Ngày Ghi</th>
                                <th>Diễn Giải</th>
                                <th>Số Tiền</th>
                                <th>Phương Thức</th>
                                <th>Trạng Thái</th>
                                <th>Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unmatchedItems as $index => $item): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <small><?= isset($item['NgayGiaoDich']) ? date('d/m/Y', strtotime($item['NgayGiaoDich'])) : 'N/A' ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($item['MoTa'] ?? '') ?></td>
                                    <td>
                                        <strong><?= number_format($item['SoTien'] ?? 0, 0, ',', '.') ?> ₫</strong>
                                    </td>
                                    <td>
                                        <?php
                                        $methods = [
                                            'TienMat' => 'Tiền Mặt',
                                            'ChuyenKhoan' => 'Chuyển Khoản',
                                            'The' => 'Thẻ',
                                            'Cheque' => 'Cheque',
                                            'ViDienTu' => 'Ví Điện Tử'
                                        ];
                                        $method = $item['PhuongThucThanhToan'] ?? 'TienMat';
                                        echo $methods[$method] ?? $method;
                                        ?>
                                    </td>
                                    <td>
                                        <span class="matched-badge matched-no">
                                            Chưa Đối Soát
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button type="button" class="btn btn-small btn-view" onclick="openDetailModal(<?= $item['MaNganQuy'] ?? 0 ?>, '<?= htmlspecialchars($item['MoTa'] ?? '') ?>', <?= $item['SoTien'] ?? 0 ?>)">
                                                <i class="fas fa-eye me-1"></i>Xem
                                            </button>
                                            <button type="button" class="btn btn-small btn-match" onclick="confirmReconcile(<?= $item['MaNganQuy'] ?? 0 ?>)">
                                                <i class="fas fa-check me-1"></i>Đối Soát
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <h5>Tất cả giao dịch đã được đối soát</h5>
                        <p style="color: var(--text-muted);">Không có giao dịch nào chờ đối soát</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Bảng giao dịch đã đối soát -->
        <div class="reconciliation-section">
            <h3 class="section-title">
                <i class="fas fa-list me-2"></i>Giao Dịch Đã Đối Soát
            </h3>

            <?php 
            $matchedItems = array_filter($doiSoat ?? [], function($item) {
                return $item['DaDoiSoat'] == 1;
            });
            ?>

            <?php if (!empty($matchedItems)): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Ngày Ghi</th>
                                <th>Diễn Giải</th>
                                <th>Số Tiền</th>
                                <th>Phương Thức</th>
                                <th>Trạng Thái</th>
                                <th>Ngày Đối Soát</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($matchedItems as $index => $item): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <small><?= isset($item['NgayGiaoDich']) ? date('d/m/Y', strtotime($item['NgayGiaoDich'])) : 'N/A' ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($item['MoTa'] ?? '') ?></td>
                                    <td>
                                        <strong><?= number_format($item['SoTien'] ?? 0, 0, ',', '.') ?> ₫</strong>
                                    </td>
                                    <td>
                                        <?php
                                        $methods = [
                                            'TienMat' => 'Tiền Mặt',
                                            'ChuyenKhoan' => 'Chuyển Khoản',
                                            'The' => 'Thẻ',
                                            'Cheque' => 'Cheque',
                                            'ViDienTu' => 'Ví Điện Tử'
                                        ];
                                        $method = $item['PhuongThucThanhToan'] ?? 'TienMat';
                                        echo $methods[$method] ?? $method;
                                        ?>
                                    </td>
                                    <td>
                                        <span class="matched-badge matched-yes">
                                            <i class="fas fa-check me-1"></i>Đã Đối Soát
                                        </span>
                                    </td>
                                    <td>
                                        <small><?= isset($item['NgayDoiSoat']) ? date('d/m/Y', strtotime($item['NgayDoiSoat'])) : 'N/A' ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h5>Chưa có giao dịch nào được đối soát</h5>
                        <p style="color: var(--text-muted);">Các giao dịch sẽ xuất hiện ở đây sau khi được đối soát</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="background: rgba(15, 23, 42, 0.95); border: 1px solid var(--border-soft); color: var(--text-main);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-soft);">
                    <h5 class="modal-title">
                        <i class="fas fa-eye me-2" style="color: var(--accent-blue);"></i>Chi Tiết Giao Dịch
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Diễn Giải</label>
                        <p id="detailMoTa" style="color: var(--accent-blue); font-weight: bold;"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số Tiền</label>
                        <p id="detailSoTien" style="color: var(--text-muted);"></p>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-soft);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openDetailModal(id, mota, sotien) {
            document.getElementById('detailMoTa').textContent = mota;
            document.getElementById('detailSoTien').textContent = number_format(sotien) + ' ₫';
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        }

        function confirmReconcile(id) {
            if (confirm('Xác nhận đối soát giao dịch này?')) {
                reconcileTransaction(id);
            }
        }

        async function reconcileTransaction(id) {
            try {
                const response = await fetch('api_reconcile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + id
                });

                const text = await response.text();
                console.log('Raw response:', text);
                
                const data = JSON.parse(text);
                
                if (data.success) {
                    alert('Đối soát thành công!');
                    location.reload();
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể đối soát'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Lỗi kết nối: ' + error.message);
            }
        }

        function number_format(num) {
            return new Intl.NumberFormat('vi-VN').format(num);
        }
    </script>
</body>
</html>
