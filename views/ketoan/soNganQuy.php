<?php
// View: Sổ nganh quỹ (Cash Book)
$user = $user ?? Auth::user();
$soNganQuy = $soNganQuy ?? [];
$error = $error ?? '';
$tongTienMat = $tongTienMat ?? 0;
$tongChuyenKhoan = $tongChuyenKhoan ?? 0;
$tongThe = $tongThe ?? 0;
$tongViDienTu = $tongViDienTu ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sổ Nganh Quỹ - Kế Toán</title>
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
            --accent-purple: #a855f7;
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

        .balance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .balance-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(15, 23, 42, 0.8) 100%);
            border-radius: 8px;
            padding: 25px;
            border-top: 4px solid;
            transition: transform 0.3s ease;
            position: relative;
        }

        .balance-card:hover {
            transform: translateY(-5px);
        }

        .balance-card.tienmат {
            border-top-color: #fbbf24;
        }

        .balance-card.chuyenkhoан {
            border-top-color: var(--accent-blue);
        }

        .balance-card.the {
            border-top-color: var(--accent-purple);
        }

        .balance-card.vidiental {
            border-top-color: var(--accent-green);
        }

        .balance-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .balance-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .balance-card.tienmат .balance-value {
            color: #fbbf24;
        }

        .balance-card.chuyenkhoан .balance-value {
            color: var(--accent-blue);
        }

        .balance-card.the .balance-value {
            color: var(--accent-purple);
        }

        .balance-card.vidiental .balance-value {
            color: var(--accent-green);
        }

        .method-icon {
            font-size: 2rem;
            position: absolute;
            right: 20px;
            top: 20px;
            opacity: 0.1;
        }

        .tabs-container {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            margin-bottom: 30px;
            overflow: hidden;
        }

        .nav-tabs {
            border-bottom: 2px solid var(--border-soft);
            padding: 0;
            margin: 0;
            background: rgba(15, 23, 42, 0.8);
        }

        .nav-link {
            border: none;
            color: var(--text-muted);
            padding: 15px 25px;
            font-weight: 600;
            border-radius: 0;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--accent-blue);
            background: transparent;
        }

        .nav-link.active {
            color: var(--accent-blue);
            border: none;
            border-bottom: 3px solid var(--accent-blue);
            background: transparent;
        }

        .table-container {
            padding: 20px;
        }

        .table {
            color: var(--text-main);
            margin-bottom: 0;
        }

        .table thead {
            background: rgba(15, 23, 42, 0.8);
        }

        .table th {
            padding: 15px;
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
            padding: 14px 15px;
            vertical-align: middle;
        }

        .amount-positive {
            color: var(--accent-green);
            font-weight: 700;
        }

        .amount-negative {
            color: var(--accent-red);
            font-weight: 700;
        }

        .reconciled-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .reconciled-yes {
            background: rgba(16, 185, 129, 0.2);
            color: var(--accent-green);
            border: 1px solid rgba(16, 185, 129, 0.5);
        }

        .reconciled-no {
            background: rgba(239, 68, 68, 0.2);
            color: var(--accent-red);
            border: 1px solid rgba(239, 68, 68, 0.5);
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

        .filter-section {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .form-control {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid var(--border-soft);
            color: var(--text-main);
            padding: 10px 15px;
            border-radius: 6px;
        }

        .form-control:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: var(--accent-blue);
            color: var(--text-main);
            box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.25);
        }

        .form-label {
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-book me-2" style="color: #10b981;"></i>Sổ Nganh Quỹ
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
            <i class="fas fa-book me-2"></i>Sổ Nganh Quỹ (Cash Book)
        </h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Thống kê số dư -->
        <div class="balance-grid">
            <div class="balance-card tienmат">
                <div class="balance-label">
                    <i class="fas fa-coins me-2"></i>Tiền Mặt
                </div>
                <div class="balance-value">
                    <?= number_format($tongTienMat, 0, ',', '.') ?>
                </div>
                <small style="color: var(--text-muted);">₫</small>
                <div class="method-icon"><i class="fas fa-coins"></i></div>
            </div>

            <div class="balance-card chuyenkhoан">
                <div class="balance-label">
                    <i class="fas fa-university me-2"></i>Chuyển Khoản
                </div>
                <div class="balance-value">
                    <?= number_format($tongChuyenKhoan, 0, ',', '.') ?>
                </div>
                <small style="color: var(--text-muted);">₫</small>
                <div class="method-icon"><i class="fas fa-university"></i></div>
            </div>

            <div class="balance-card the">
                <div class="balance-label">
                    <i class="fas fa-credit-card me-2"></i>Thẻ
                </div>
                <div class="balance-value">
                    <?= number_format($tongThe, 0, ',', '.') ?>
                </div>
                <small style="color: var(--text-muted);">₫</small>
                <div class="method-icon"><i class="fas fa-credit-card"></i></div>
            </div>

            <div class="balance-card vidiental">
                <div class="balance-label">
                    <i class="fas fa-mobile-alt me-2"></i>Ví Điện Tử
                </div>
                <div class="balance-value">
                    <?= number_format($tongViDienTu, 0, ',', '.') ?>
                </div>
                <small style="color: var(--text-muted);">₫</small>
                <div class="method-icon"><i class="fas fa-mobile-alt"></i></div>
            </div>
        </div>

        <!-- Lọc -->
        <div class="filter-section">
            <form method="GET" action="" class="filter-form">
                <div>
                    <label class="form-label">Từ Ngày</label>
                    <input type="date" class="form-control">
                </div>
                <div>
                    <label class="form-label">Đến Ngày</label>
                    <input type="date" class="form-control">
                </div>
                <div>
                    <label class="form-label">Phương Thức</label>
                    <select class="form-control">
                        <option value="">Tất cả</option>
                        <option value="TienMat">Tiền Mặt</option>
                        <option value="ChuyenKhoan">Chuyển Khoản</option>
                        <option value="The">Thẻ</option>
                        <option value="ViDienTu">Ví Điện Tử</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Tìm Kiếm
                    </button>
                </div>
            </form>
        </div>

        <!-- Tab danh sách -->
        <div class="tabs-container">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tienmат-tab" data-bs-toggle="tab" data-bs-target="#tienmат-content" type="button" role="tab">
                        <i class="fas fa-coins me-2"></i>Tiền Mặt
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="chuyenkhoан-tab" data-bs-toggle="tab" data-bs-target="#chuyenkhoан-content" type="button" role="tab">
                        <i class="fas fa-university me-2"></i>Chuyển Khoản
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="the-tab" data-bs-toggle="tab" data-bs-target="#the-content" type="button" role="tab">
                        <i class="fas fa-credit-card me-2"></i>Thẻ
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="vidiental-tab" data-bs-toggle="tab" data-bs-target="#vidiental-content" type="button" role="tab">
                        <i class="fas fa-mobile-alt me-2"></i>Ví Điện Tử
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Tiền Mặt -->
                <div class="tab-pane fade show active" id="tienmат-content" role="tabpanel">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Diễn Giải</th>
                                    <th>Thu/Chi</th>
                                    <th>Số Tiền</th>
                                    <th>Số Dư</th>
                                    <th>Đã Đối Soát</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $items = array_filter($soNganQuy ?? [], function($item) {
                                    return $item['PhuongThucThanhToan'] === 'TienMat';
                                });
                                if (!empty($items)):
                                    $runningBalance = 0;
                                    foreach ($items as $item):
                                        $runningBalance += $item['SoTien'] ?? 0;
                                ?>
                                    <tr>
                                        <td><?= isset($item['NgayGiaoDich']) ? date('d/m/Y', strtotime($item['NgayGiaoDich'])) : 'N/A' ?></td>
                                        <td><?= htmlspecialchars($item['DienGiai'] ?? '') ?></td>
                                        <td>
                                            <?php if (($item['SoTien'] ?? 0) > 0): ?>
                                                <span style="color: var(--accent-green);"><i class="fas fa-arrow-up me-1"></i>Thu</span>
                                            <?php else: ?>
                                                <span style="color: var(--accent-red);"><i class="fas fa-arrow-down me-1"></i>Chi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="<?= ($item['SoTien'] ?? 0) >= 0 ? 'amount-positive' : 'amount-negative' ?>">
                                                <?= number_format(abs($item['SoTien'] ?? 0), 0, ',', '.') ?> ₫
                                            </span>
                                        </td>
                                        <td><?= number_format($runningBalance, 0, ',', '.') ?> ₫</td>
                                        <td>
                                            <span class="reconciled-badge <?= ($item['DaDoiSoat'] ?? 0) ? 'reconciled-yes' : 'reconciled-no' ?>">
                                                <?= ($item['DaDoiSoat'] ?? 0) ? 'Đã Đối Soát' : 'Chưa Đối Soát' ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php 
                                    endforeach;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="6" class="text-center" style="color: var(--text-muted); padding: 40px;">
                                            <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.3; margin-right: 10px;"></i>
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Chuyển Khoản -->
                <div class="tab-pane fade" id="chuyenkhoан-content" role="tabpanel">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Diễn Giải</th>
                                    <th>Thu/Chi</th>
                                    <th>Số Tiền</th>
                                    <th>Số Dư</th>
                                    <th>Đã Đối Soát</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $items = array_filter($soNganQuy ?? [], function($item) {
                                    return $item['PhuongThucThanhToan'] === 'ChuyenKhoan';
                                });
                                if (!empty($items)):
                                    $runningBalance = 0;
                                    foreach ($items as $item):
                                        $runningBalance += $item['SoTien'] ?? 0;
                                ?>
                                    <tr>
                                        <td><?= isset($item['NgayGiaoDich']) ? date('d/m/Y', strtotime($item['NgayGiaoDich'])) : 'N/A' ?></td>
                                        <td><?= htmlspecialchars($item['DienGiai'] ?? '') ?></td>
                                        <td>
                                            <?php if (($item['SoTien'] ?? 0) > 0): ?>
                                                <span style="color: var(--accent-green);"><i class="fas fa-arrow-up me-1"></i>Thu</span>
                                            <?php else: ?>
                                                <span style="color: var(--accent-red);"><i class="fas fa-arrow-down me-1"></i>Chi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="<?= ($item['SoTien'] ?? 0) >= 0 ? 'amount-positive' : 'amount-negative' ?>">
                                                <?= number_format(abs($item['SoTien'] ?? 0), 0, ',', '.') ?> ₫
                                            </span>
                                        </td>
                                        <td><?= number_format($runningBalance, 0, ',', '.') ?> ₫</td>
                                        <td>
                                            <span class="reconciled-badge <?= ($item['DaDoiSoat'] ?? 0) ? 'reconciled-yes' : 'reconciled-no' ?>">
                                                <?= ($item['DaDoiSoat'] ?? 0) ? 'Đã Đối Soát' : 'Chưa Đối Soát' ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php 
                                    endforeach;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="6" class="text-center" style="color: var(--text-muted); padding: 40px;">
                                            <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.3; margin-right: 10px;"></i>
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Thẻ -->
                <div class="tab-pane fade" id="the-content" role="tabpanel">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Diễn Giải</th>
                                    <th>Thu/Chi</th>
                                    <th>Số Tiền</th>
                                    <th>Số Dư</th>
                                    <th>Đã Đối Soát</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $items = array_filter($soNganQuy ?? [], function($item) {
                                    return $item['PhuongThucThanhToan'] === 'The';
                                });
                                if (!empty($items)):
                                    $runningBalance = 0;
                                    foreach ($items as $item):
                                        $runningBalance += $item['SoTien'] ?? 0;
                                ?>
                                    <tr>
                                        <td><?= isset($item['NgayGiaoDich']) ? date('d/m/Y', strtotime($item['NgayGiaoDich'])) : 'N/A' ?></td>
                                        <td><?= htmlspecialchars($item['DienGiai'] ?? '') ?></td>
                                        <td>
                                            <?php if (($item['SoTien'] ?? 0) > 0): ?>
                                                <span style="color: var(--accent-green);"><i class="fas fa-arrow-up me-1"></i>Thu</span>
                                            <?php else: ?>
                                                <span style="color: var(--accent-red);"><i class="fas fa-arrow-down me-1"></i>Chi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="<?= ($item['SoTien'] ?? 0) >= 0 ? 'amount-positive' : 'amount-negative' ?>">
                                                <?= number_format(abs($item['SoTien'] ?? 0), 0, ',', '.') ?> ₫
                                            </span>
                                        </td>
                                        <td><?= number_format($runningBalance, 0, ',', '.') ?> ₫</td>
                                        <td>
                                            <span class="reconciled-badge <?= ($item['DaDoiSoat'] ?? 0) ? 'reconciled-yes' : 'reconciled-no' ?>">
                                                <?= ($item['DaDoiSoat'] ?? 0) ? 'Đã Đối Soát' : 'Chưa Đối Soát' ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php 
                                    endforeach;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="6" class="text-center" style="color: var(--text-muted); padding: 40px;">
                                            <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.3; margin-right: 10px;"></i>
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Ví Điện Tử -->
                <div class="tab-pane fade" id="vidiental-content" role="tabpanel">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Diễn Giải</th>
                                    <th>Thu/Chi</th>
                                    <th>Số Tiền</th>
                                    <th>Số Dư</th>
                                    <th>Đã Đối Soát</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $items = array_filter($soNganQuy ?? [], function($item) {
                                    return $item['PhuongThucThanhToan'] === 'ViDienTu';
                                });
                                if (!empty($items)):
                                    $runningBalance = 0;
                                    foreach ($items as $item):
                                        $runningBalance += $item['SoTien'] ?? 0;
                                ?>
                                    <tr>
                                        <td><?= isset($item['NgayGiaoDich']) ? date('d/m/Y', strtotime($item['NgayGiaoDich'])) : 'N/A' ?></td>
                                        <td><?= htmlspecialchars($item['DienGiai'] ?? '') ?></td>
                                        <td>
                                            <?php if (($item['SoTien'] ?? 0) > 0): ?>
                                                <span style="color: var(--accent-green);"><i class="fas fa-arrow-up me-1"></i>Thu</span>
                                            <?php else: ?>
                                                <span style="color: var(--accent-red);"><i class="fas fa-arrow-down me-1"></i>Chi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="<?= ($item['SoTien'] ?? 0) >= 0 ? 'amount-positive' : 'amount-negative' ?>">
                                                <?= number_format(abs($item['SoTien'] ?? 0), 0, ',', '.') ?> ₫
                                            </span>
                                        </td>
                                        <td><?= number_format($runningBalance, 0, ',', '.') ?> ₫</td>
                                        <td>
                                            <span class="reconciled-badge <?= ($item['DaDoiSoat'] ?? 0) ? 'reconciled-yes' : 'reconciled-no' ?>">
                                                <?= ($item['DaDoiSoat'] ?? 0) ? 'Đã Đối Soát' : 'Chưa Đối Soát' ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php 
                                    endforeach;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="6" class="text-center" style="color: var(--text-muted); padding: 40px;">
                                            <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.3; margin-right: 10px;"></i>
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
