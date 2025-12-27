# Hướng dẫn nghiệp vụ nhân viên kế toán

## 1. Phạm vi & mục tiêu
- Bao quát luồng: Doanh thu – Chi phí – Công nợ – Kiểm toán đêm – Báo cáo – Sổ & Đối soát – Khóa sổ.
- Đảm bảo ghi nhận đúng, kiểm soát chặt, truy vết được, tuân thủ nội bộ.
- Phù hợp vận hành khách sạn/homestay và dịch vụ lưu trú.

## 2. Cấu trúc phân quyền & phân tách nhiệm vụ (RBAC)
- Kế toán thường: xem/ghi nhận, không duyệt, không xóa.
- Kế toán trưởng: phê duyệt, sửa có điều kiện, xóa mềm, mở/đóng kỳ.
- Kiểm toán nội bộ/Quản lý: chỉ xem, truy cập báo cáo và audit log.
- Nguyên tắc: không chia sẻ tài khoản; quyền theo hành động + phân hệ + trạng thái kỳ.

## 3. Kiểm soát thay đổi & phê duyệt
- Mọi sửa doanh thu/chi phí/công nợ: bắt buộc nhập Lý do; lưu giá trị cũ→mới, người sửa, thời điểm.
- Xóa chi phí: xóa mềm, 2 bước xác nhận, token hết hạn 10 phút; không xóa cứng trên giao diện.
- Giao dịch giá trị lớn hoặc sau kiểm toán đêm: bắt buộc phê duyệt (role cao hơn).

## 4. Audit Trail (Nhật ký kiểm toán)
- Ghi: người dùng, thời gian, hành động, dữ liệu trước/sau.
- Log bất biến, không cho sửa/xóa; truy cập chỉ dành cho vai trò cao cấp.

## 5. Quy trình nghiệp vụ chính
- Doanh thu: lọc, tìm kiếm, sửa có kiểm soát; tách doanh thu theo ngày lưu trú/dịch vụ.
- Chi phí: thêm/sửa/xóa mềm; phân loại; phân bổ theo phòng/bộ phận.
- Công nợ: phải thu/phải trả; cập nhật thu/chi; theo dõi quá hạn.
- Kiểm toán đêm: tổng hợp doanh thu/chi phí trong ngày; khóa tạm giao dịch ngày.
- Sổ & đối soát: theo dõi tiền mặt/chuyển khoản/thẻ/ví; đối soát sổ nội bộ ↔ sao kê ngân hàng, ghi nhận chênh lệch.
- Báo cáo: Doanh thu, Chi phí, Tổng hợp, KQKD, Lưu chuyển tiền tệ; xuất HTML/CSV; đối chiếu chéo số liệu.
- Khóa sổ: đóng kỳ hạch toán; sau khóa sổ chỉ xem; điều chỉnh bằng bút toán kỳ sau hoặc mở kỳ với quyền đặc biệt.

## 6. Checklist vận hành
- EOD (cuối ngày):
  1) Đối soát ngăn quỹ ↔ sao kê ngân hàng, ghi chênh lệch.
  2) Rà log sửa giao dịch trong ngày.
  3) Xuất CSV: doanh thu, chi phí, ngăn quỹ; lưu backup.
  4) Kiểm tra công nợ quá hạn; cập nhật thu/chi.
- EOM (cuối kỳ/trước khóa sổ):
  1) Đảm bảo không còn giao dịch “đang xử lý”.
  2) Đối soát cuối kỳ sổ ↔ bank; chốt chênh lệch.
  3) Chạy & lưu CSV: KQKD, Lưu chuyển tiền tệ.
  4) Đóng sổ kỳ, ghi nhận mã kỳ và người thực hiện.

## 7. Sao lưu & khôi phục
- Tần suất: hàng ngày.
- Vị trí: `storage/backups/`.
- Khôi phục: import SQL → kiểm tra toàn vẹn số liệu → đối soát nhanh.

## 8. Xử lý lỗi/ngoại lệ thường gặp
- Token xóa chi phí hết hạn: thực hiện lại bước xác nhận lần 1.
- Đối soát lệch: ghi chú chênh lệch, đánh dấu giao dịch cần xác minh, không sửa trực tiếp số gốc.
- Sai kỳ/ngày: tạo bút toán điều chỉnh, không sửa hồi tố khi đã khóa sổ.

## 9. KPI theo dõi
- Tỷ lệ lệch sổ ↔ bank.
- Thời gian hoàn tất đóng sổ.
- Tỷ lệ công nợ quá hạn.
- Số lần sửa giao dịch/tháng.
- Thời gian hoàn thành đối soát EOD/EOM.

## 10. Điều hướng nhanh
- Dashboard: `index.php?controller=ketoan&action=dashboard`
- Phân hệ: Tổng quan, Doanh thu, Chi phí, Kiểm toán đêm, Báo cáo (Doanh thu/Chi phí/Tổng hợp/KQKD/LC tiền tệ/Tách doanh thu), Công nợ (phải thu/phải trả), Sổ & Đối soát, Khóa sổ.

## 11. Đường dẫn hữu ích
- View: `views/ketoan/`
- Controller: `controllers/KeToanController.php`
- Model: `models/BaoCaoKeToan.php`

## 12. Ghi chú
- Các màn hình đã có nút "Quay lại Dashboard" trên navbar để điều hướng nhanh.
- Báo cáo/đối soát dùng múi giờ & ngày hệ thống; kiểm tra khoảng ngày trước khi xuất.
