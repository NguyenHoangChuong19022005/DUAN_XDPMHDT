<?php
require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->setupSMTP();
    }

    private function setupSMTP() {
        // ✅ CẤU HÌNH GMAIL (Thay bằng email của bạn)
        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.gmail.com';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = 'your-email@gmail.com';  // ← THAY EMAIL
        $this->mail->Password   = 'your-app-password';     // ← THAY APP PASSWORD
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 587;

        $this->mail->setFrom('your-email@gmail.com', 'EduMatch');
        $this->mail->isHTML(true);
    }

    public function sendOTP($email, $otp) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($email);
            $this->mail->Subject = 'Xác thực OTP - EduMatch';
            $this->mail->Body    = "
                <h2>🎓 EduMatch - Mã OTP</h2>
                <div style='background: #f8f9fa; padding: 20px; border-radius: 10px;'>
                    <h1 style='color: #0d6efd; font-size: 48px;'>$otp</h1>
                    <p>Mã OTP có hiệu lực trong 5 phút</p>
                </div>
                <p>Nếu bạn không yêu cầu, vui lòng bỏ qua email này.</p>
            ";
            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    public function sendApplicationNotification($student_email, $scholarship_title) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($student_email);
            $this->mail->Subject = 'Đơn ứng tuyển đã được gửi - EduMatch';
            $this->mail->Body    = "
                <h2>✅ ĐƠN ỨNG TUYỂN THÀNH CÔNG!</h2>
                <p><strong>Học bổng:</strong> $scholarship_title</p>
                <p>Trạng thái: <span style='color: orange;'>⏳ Đang chờ xử lý</span></p>
                <a href='http://localhost/EduMatch/dashboard_student.php' style='background: #0d6efd; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;'>Xem đơn của bạn</a>
            ";
            return $this->mail->send();
        } catch (Exception $e) {
            return false;
        }
    }

    public function sendStatusUpdate($email, $status, $scholarship_title) {
        $status_color = $status == 'approved' ? 'green' : ($status == 'rejected' ? 'red' : 'orange');
        $status_emoji = $status == 'approved' ? '✅' : ($status == 'rejected' ? '❌' : '⏳');
        
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($email);
            $this->mail->Subject = "Cập nhật trạng thái đơn - $scholarship_title";
            $this->mail->Body    = "
                <h2>$status_emoji CẬP NHẬT TRẠNG THÁI</h2>
                <p><strong>Học bổng:</strong> $scholarship_title</p>
                <p><strong>Trạng thái:</strong> <span style='color: $status_color; font-size: 18px;'>$status</span></p>
            ";
            return $this->mail->send();
        } catch (Exception $e) {
            return false;
        }
    }
}
?>