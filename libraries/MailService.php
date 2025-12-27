<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/Exception.php';
require_once __DIR__ . '/PHPMailer.php';
require_once __DIR__ . '/SMTP.php';

class MailService {
    
    public static function sendPhanHoi($emailKhach, $tenKhach, $noiDungTraLoi) {
        $mail = new PHPMailer(true);

        try {
            // 1. Cấu hình Server (Dùng Gmail)
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'capybaraduongthe@gmail.com'; // <--- Thay Email của bạn vào đây
            $mail->Password   = 'uxqj xwcn hfvv qqwk';       // <--- Thay Mật khẩu ứng dụng vào đây (Xem hướng dẫn ở cuối bài)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // 2. Người gửi và Người nhận
            $mail->setFrom('capybaraduongthe@gmail.com', 'CSKH Resort ABC');
            $mail->addAddress($emailKhach, $tenKhach);

            // 3. Nội dung Email
            $mail->isHTML(true);
            $mail->Subject = '[ABC Resort] Phản hồi ý kiến khách hàng';
            
            // Tạo giao diện Email đẹp một chút
            $bodyContent = "
                <h3>Chào khách hàng $tenKhach,</h3>
                <p>Cảm ơn quý khách đã gửi phản hồi về dịch vụ của ABC Resort.</p>
                <p>Chúng tôi xin trả lời nội dung phản hồi của quý khách như sau:</p>
                <blockquote style='background: #f9f9f9; border-left: 5px solid #ccc; margin: 1.5em 10px; padding: 0.5em 10px;'>
                    $noiDungTraLoi
                </blockquote>
                <p>Nếu có thắc mắc, vui lòng liên hệ hotline 1900xxxx.</p>
                <p>Trân trọng,<br>Bộ phận Chăm sóc Khách hàng.</p>
            ";
            
            $mail->Body = $bodyContent;
            $mail->AltBody = strip_tags($bodyContent); // Nội dung rút gọn nếu không hỗ trợ HTML

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Ghi log lỗi nếu cần: echo "Lỗi gửi mail: {$mail->ErrorInfo}";
            return false;
        }
    }


    //------ gửi mail Gửi thông báo qua Emai ---------------------------------
    /**
     * Hàm gửi email chung cho mọi mục đích
     * @param string $emailKhach Email người nhận
     * @param string $tenKhach Tên người nhận
     * @param string $tieuDe Tiêu đề email
     * @param string $noiDung Nội dung (HTML)
     */
    public static function sendEmailChung($emailKhach, $tenKhach, $tieuDe, $noiDung) {
        $mail = new PHPMailer(true);
        try {
            // 1. Cấu hình Server (Copy y hệt hàm cũ)
            $mail->SMTPDebug = 0; // Tắt debug (set = 2 nếu cần xem log)
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'capybaraduongthe@gmail.com'; // <--- Thay Email của bạn vào đây
            $mail->Password   = 'uxqj xwcn hfvv qqwk';       // <--- Thay Mật khẩu ứng dụng vào đây (Xem hướng dẫn ở cuối bài)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // 2. Người gửi - Nhận
            $mail->setFrom('capybaraduongthe@gmail.com', 'ABC Resort Notification');
            $mail->addAddress($emailKhach, $tenKhach);

            // 3. Nội dung
            $mail->isHTML(true);
            $mail->Subject = $tieuDe;
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                    <h3 style='color: #0ea5e9;'>Xin chào $tenKhach,</h3>
                    <p>Ban quản lý ABC Resort xin gửi đến bạn thông báo sau:</p>
                    <div style='background: #f4f4f5; padding: 15px; border-left: 4px solid #0ea5e9; margin: 20px 0;'>
                        $noiDung
                    </div>
                    <p>Trân trọng,<br><strong>ABC Resort Team</strong></p>
                    <hr style='border:none; border-top:1px solid #eee;'>
                    <small style='color:#999;'>Đây là email tự động, vui lòng không trả lời.</small>
                </div>
            ";

            error_log("Đang gửi email đến: $emailKhach với tiêu đề: $tieuDe");
            $mail->send();
            error_log("Gửi email thành công đến: $emailKhach");
            return true;
        } catch (Exception $e) {
            error_log("LỖI GỬI EMAIL đến $emailKhach: " . $mail->ErrorInfo);
            error_log("Chi tiết exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Hàm gửi email đơn giản
     */
    public static function send($emailNhan, $tieuDe, $noiDung) {
        $tenNhan = explode('@', $emailNhan)[0];
        return self::sendEmailChung($emailNhan, $tenNhan, $tieuDe, $noiDung);
    }

    /**
     * Hàm gửi email khuyến mãi với nội dung chi tiết và cá nhân hóa
     * @param string $emailKhach Email khách hàng
     * @param string $tenKhach Tên khách hàng
     * @param string $tenCTKM Tên chương trình khuyến mãi
     * @param string $maCode Mã khuyến mãi
     * @param string $mucUuDai Mức ưu đãi (VD: "20%", "500,000 VND")
     * @param string $ngayBatDau Ngày bắt đầu
     * @param string $ngayKetThuc Ngày kết thúc
     * @param string $doiTuongApDung Đối tượng áp dụng (VD: "Khách VIP", "Khách mới")
     */
    public static function sendKhuyenMai($emailKhach, $tenKhach, $tenCTKM, $maCode, $mucUuDai, $ngayBatDau, $ngayKetThuc, $doiTuongApDung) {
        $mail = new PHPMailer(true);
        
        try {
            // 1. Cấu hình Server
            $mail->SMTPDebug = 0; // Tắt debug
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'capybaraduongthe@gmail.com';
            $mail->Password   = 'uxqj xwcn hfvv qqwk';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // 2. Người gửi - Nhận
            $mail->setFrom('capybaraduongthe@gmail.com', 'ABC Resort - Chương trình Khuyến mãi');
            $mail->addAddress($emailKhach, $tenKhach);

            // 3. Tạo thông điệp cá nhân hóa
            $loiChao = "";
            $thongDiepDacBiet = "";
            
            if (stripos($doiTuongApDung, 'VIP') !== false) {
                $loiChao = "Kính gửi Quý khách hàng VIP $tenKhach,";
                $thongDiepDacBiet = "<div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: center;'>
                    <strong>🌟 ƯU ĐÃI ĐẶC BIỆT CHỈ DÀNH RIÊNG CHO BẠN 🌟</strong><br>
                    <small>Chương trình này chỉ áp dụng cho khách hàng VIP thân thiết</small>
                </div>";
            } elseif (stripos($doiTuongApDung, 'mới') !== false) {
                $loiChao = "Chào mừng khách hàng mới $tenKhach,";
                $thongDiepDacBiet = "<div style='background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: center;'>
                    <strong>🎉 ƯU ĐÃI ĐẶC BIỆT CHÀO MỪNG BẠN MỚI 🎉</strong><br>
                    <small>Đây là món quà chào mừng dành riêng cho bạn</small>
                </div>";
            } else {
                $loiChao = "Kính gửi Quý khách hàng $tenKhach,";
                $thongDiepDacBiet = "<div style='background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: center;'>
                    <strong>🎁 CHƯƠNG TRÌNH KHUYẾN MÃI ĐẶC BIỆT 🎁</strong>
                </div>";
            }

            // 4. Nội dung Email
            $mail->isHTML(true);
            $mail->Subject = "🎊 [$tenCTKM] - Ưu đãi $mucUuDai tại ABC Resort";
            
            $mail->Body = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; }
                    .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                    .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
                    .content { padding: 30px; }
                    .promo-box { background: #fff3cd; border: 3px dashed #ffc107; padding: 20px; margin: 20px 0; border-radius: 8px; text-align: center; }
                    .promo-code { font-size: 32px; font-weight: bold; color: #e91e63; letter-spacing: 3px; margin: 10px 0; }
                    .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                    .info-table td { padding: 12px; border-bottom: 1px solid #eee; }
                    .info-table td:first-child { font-weight: bold; color: #667eea; width: 40%; }
                    .cta-button { display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 30px; font-weight: bold; margin: 20px 0; }
                    .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1 style='margin:0; font-size: 28px;'>🏖️ ABC RESORT</h1>
                        <p style='margin:5px 0 0 0; opacity: 0.9;'>Nơi nghỉ dưỡng đẳng cấp</p>
                    </div>
                    
                    <div class='content'>
                        <p style='font-size: 16px;'>$loiChao</p>
                        
                        $thongDiepDacBiet
                        
                        <p>Chúng tôi vô cùng vui mừng được giới thiệu đến bạn chương trình khuyến mãi đặc biệt:</p>
                        
                        <h2 style='color: #667eea; text-align: center; font-size: 24px; margin: 25px 0;'>$tenCTKM</h2>
                        
                        <div class='promo-box'>
                            <p style='margin: 0; font-size: 14px; color: #856404;'>MÃ KHUYẾN MÃI CỦA BẠN</p>
                            <div class='promo-code'>$maCode</div>
                            <p style='margin: 0; font-size: 16px; color: #721c24;'>🎁 Ưu đãi: <strong style='font-size: 20px;'>$mucUuDai</strong></p>
                        </div>
                        
                        <table class='info-table'>
                            <tr>
                                <td>📅 Thời gian áp dụng:</td>
                                <td>Từ <strong>$ngayBatDau</strong> đến <strong>$ngayKetThuc</strong></td>
                            </tr>
                            <tr>
                                <td>👥 Đối tượng:</td>
                                <td><strong>$doiTuongApDung</strong></td>
                            </tr>
                            <tr>
                                <td>📝 Cách sử dụng:</td>
                                <td>Nhập mã <strong>$maCode</strong> khi đặt phòng trực tuyến hoặc xuất trình tại quầy lễ tân</td>
                            </tr>
                        </table>
                        
                        <div style='background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                            <strong style='color: #1976d2;'>💡 Lưu ý quan trọng:</strong>
                            <ul style='margin: 10px 0; padding-left: 20px;'>
                                <li>Mã khuyến mãi có giá trị trong thời gian quy định</li>
                                <li>Không áp dụng đồng thời với các chương trình khác</li>
                                <li>Số lượng phòng áp dụng có hạn, đặt sớm để không bỏ lỡ</li>
                            </ul>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='http://localhost/xong2actor/xong2actor/code1/index.php?controller=khachhang&action=action_datPhongOnline' class='cta-button'>
                                ĐẶT PHÒNG NGAY
                            </a>
                        </div>
                        
                        <p style='margin-top: 30px;'>Nếu bạn cần hỗ trợ hoặc có bất kỳ thắc mắc nào, vui lòng liên hệ:</p>
                        <p style='margin: 5px 0;'>📞 Hotline: <strong>1900-xxxx</strong></p>
                        <p style='margin: 5px 0;'>📧 Email: <strong>cskh@abcresort.com</strong></p>
                        
                        <p style='margin-top: 30px;'>Trân trọng,<br><strong>Ban Quản lý ABC Resort</strong></p>
                    </div>
                    
                    <div class='footer'>
                        <p style='margin: 5px 0;'>ABC Resort - Địa chỉ: 123 Đường Biển, Thành phố Nha Trang</p>
                        <p style='margin: 5px 0;'>Email này được gửi tự động, vui lòng không trả lời trực tiếp.</p>
                        <p style='margin: 15px 0 5px 0;'><small>Bạn nhận được email này vì bạn là khách hàng của ABC Resort.</small></p>
                    </div>
                </div>
            </body>
            </html>
            ";

            error_log("Đang gửi email khuyến mãi đến: $emailKhach - Chương trình: $tenCTKM");
            $mail->send();
            error_log("✅ Gửi email khuyến mãi thành công đến: $emailKhach");
            return true;
            
        } catch (Exception $e) {
            error_log("❌ LỖI GỬI EMAIL KHUYẾN MÃI đến $emailKhach: " . $mail->ErrorInfo);
            error_log("Chi tiết exception: " . $e->getMessage());
            return false;
        }
    }
}
?>