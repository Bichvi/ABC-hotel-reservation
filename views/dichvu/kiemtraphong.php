<?php
$user     = Auth::user();
$mode     = $mode ?? "list";
$rooms    = $rooms ?? [];
$room     = $room ?? null;
$errors   = $errors ?? [];
$success  = $success ?? "";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Kiểm tra phòng trả - ABC Resort</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
    body {
        background: #0f172a;
        min-height: 100vh;
        color: #e5e7eb;
    }
    .wrapper {
        background: #1e293b;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid rgba(148,163,184,.25);
        max-width: 900px;
    }

    /* Nút kiểm tra – KHÔNG dùng .btn-check vì trùng Bootstrap */
    .btn-kiemtra {
        display: inline-block !important;
        background: #38bdf8 !important;
        color: #0f172a !important;
        padding: 6px 14px !important;
        border-radius: 10px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        border: none !important;
    }
    .btn-kiemtra:hover {
        background: #0ea5e9 !important;
        opacity: .9;
    }

    .btn-back {
        background: #64748b;
        color: white;
        padding: 7px 18px;
        border-radius: 10px;
        text-decoration: none;
    }

    .btn-save {
        background: #22c55e;
        padding: 10px 22px;
        border-radius: 10px;
        color: #0f172a;
        font-weight: 600;
        border: none;
    }

</style>
</head>

<body>

<div class="container d-flex justify-content-center mt-5">
<div class="wrapper w-100">

<!-- SUCCESS -->
<?php if (!empty($success)): ?>
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check me-2"></i>
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<!-- ERRORS -->
<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>



<!-- ============================================================
     MODE = LIST → DANH SÁCH PHÒNG STAYED
=============================================================== -->
<?php if ($mode === "list"): ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-info">
            <i class="fa-solid fa-list-check me-2"></i>
            Danh sách phòng đang ở (cần kiểm tra)
        </h3>

        <a href="index.php?controller=dashboard&action=dichvu" class="btn-back">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay về Dashboard
        </a>
    </div>

    <?php if (empty($rooms)): ?>
        <div class="text-center text-secondary">Không có phòng trạng thái Stayed.</div>
    <?php else: ?>

    <table class="table table-dark table-striped mt-3">
        <thead>
            <tr>
                <th>Số phòng</th>
                <th>Loại phòng</th>
                <th>Tình trạng hiện tại</th>
                <th class="text-end">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rooms as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['SoPhong']) ?></td>
                <td><?= htmlspecialchars($p['LoaiPhong']) ?></td>
                <td><?= htmlspecialchars($p['TinhTrangPhong']) ?></td>
                <td class="text-end">

                    <!-- Nút kiểm tra phòng (đã fix) -->
                    <a href="index.php?controller=dichvu&action=kiemTraPhong&mode=form&id=<?= $p['MaPhong'] ?>"
                       class="btn-kiemtra">
                       <i class="fa-solid fa-clipboard-check me-1"></i> Kiểm tra
                    </a>

                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php endif; ?>

<?php endif; ?>



<!-- ============================================================
     MODE = FORM → KIỂM TRA PHÒNG
=============================================================== -->
<?php if ($mode === "form" && $room): ?>

    <h3 class="text-info">
        <i class="fa-solid fa-pen-to-square me-2"></i>
        Kiểm tra phòng <?= htmlspecialchars($room['SoPhong']) ?>
    </h3>

    <form method="post" action="index.php?controller=dichvu&action=luuKiemTraPhong">

        <input type="hidden" name="MaPhong" value="<?= $room['MaPhong'] ?>">

        <div class="mb-3">
            <label class="form-label">Số phòng:</label>
            <input class="form-control" value="<?= $room['SoPhong'] ?>" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Tình trạng hiện tại:</label>
            <input class="form-control" value="<?= $room['TinhTrangPhong'] ?>" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Tình trạng mới:</label>
            <select class="form-select" name="TinhTrangPhong" required>
                <option value="">-- chọn tình trạng --</option>
                <option value="Tot">Tốt</option>
                <option value="HuHaiNhe">Hư hại nhẹ</option>
                <option value="HuHaiNang">Hư hại nặng</option>
            </select>
        </div>

        <button class="btn-save">
            <i class="fa-solid fa-save me-1"></i> Lưu kiểm tra phòng
        </button>

        <a href="index.php?controller=dichvu&action=kiemTraPhong"
           class="btn-back ms-2">
           <i class="fa-solid fa-xmark me-1"></i> Hủy
        </a>

    </form>

<?php endif; ?>


</div>
</div>

</body>
</html>