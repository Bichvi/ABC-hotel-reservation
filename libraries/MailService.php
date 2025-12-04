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
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
             $mail->Username   = 'capybaraduongthe@gmail.com'; // <--- Thay Email của bạn vào đây
            $mail->Password   = 'uxqj xwcn hfvv qqwk';       // <--- Thay Mật khẩu ứng dụng vào đây (Xem hướng dẫn ở cuối bài)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // 2. Người gửi - Nhận
            $mail->setFrom('cskh@abcresort.com', 'ABC Resort Notification');
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

            $mail->send();
            return true;
        } catch (Exception $e) {
            // error_log($mail->ErrorInfo); // Bật lên nếu muốn debug lỗi
            return false;
        }
    }
}
?>