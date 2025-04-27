<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendVoucherEmail($email, $conn) {
    $response = ['success' => false, 'message' => ''];

    // Kiểm tra email hợp lệ
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = '<p style="color: red;">Email không hợp lệ!</p>';
        return $response;
    }

    try {
        // Kiểm tra xem email đã tồn tại trong bảng subscribers chưa
        $stmt = $conn->prepare("SELECT id FROM subscribers WHERE email = ?");
        if ($stmt === false) {
            throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $response['message'] = '<p style="color: red;">Email này đã đăng ký nhận voucher!</p>';
            return $response;
        }

        // Lưu email vào bảng subscribers
        $stmt = $conn->prepare("INSERT INTO subscribers (email, subscribed_at) VALUES (?, NOW())");
        if ($stmt === false) {
            throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $voucher_code = strtoupper(bin2hex(random_bytes(4))); 
        
        $mail = new PHPMailer(true);
        try {
            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;

            $mail->setFrom(SMTP_USERNAME, 'TD Touris');
            $mail->addAddress($email);

        
            $mail->isHTML(true);
            $mail->Subject = 'Voucher Khuyen Mai Tu TD Touris';
            $mail->Body = "
                <h2>Chào bạn,</h2>
                <p>Cảm ơn bạn đã đăng ký nhận thông báo từ TD Touris!</p>
                <p>Dưới đây là mã voucher đặc biệt dành cho bạn:</p>
                <h3 style='color: #007bff;'>$voucher_code</h3>
                <p>Sử dụng mã này để nhận ưu đãi 10% cho lần đặt tour tiếp theo của bạn (hạn sử dụng: 30 ngày).</p>
                <p>Truy cập <a href='http://localhost/doan1/src/php/index.php'>website của chúng tôi</a> để khám phá các tour du lịch hấp dẫn!</p>
                <p>Trân trọng,<br>Đội ngũ TD Touris</p>
            ";
            $mail->AltBody = "
                Chào bạn,\n\n
                Cảm ơn bạn đã đăng ký nhận thông báo từ TD Touris!\n
                Dưới đây là mã voucher đặc biệt dành cho bạn:\n
                $voucher_code\n
                Sử dụng mã này để nhận ưu đãi 10% cho lần đặt tour tiếp theo của bạn (hạn sử dụng: 30 ngày).\n
                Truy cập http://localhost/doan1/src/php/index.php để khám phá các tour du lịch hấp dẫn!\n\n
                Trân trọng,\nĐội ngũ TD Touris
            ";

            $mail->send();
            $response['success'] = true;
            $response['message'] = '<p style="color: #ffff;">Email đã được gửi! Vui lòng kiểm tra hộp thư của bạn.</p>';
        } catch (Exception $e) {
            $response['message'] = '<p style="color: red;">Lỗi gửi email: ' . htmlspecialchars($mail->ErrorInfo) . '</p>';
        }
    } catch (Exception $e) {
        $response['message'] = '<p style="color: red;">Lỗi: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }

    return $response;
}
?>