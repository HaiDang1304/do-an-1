<?php
session_start();
include 'config.php'; // Kết nối cơ sở dữ liệu

// Cấu hình PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra người dùng đã đăng nhập chưa
    $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
    if (!$user_id) {
        // header("Location: login.php?error=Please login to book");
        $user_id = 14; // Tạm thời gán user_id để kiểm tra
    }

    // Lấy dữ liệu từ form
    $hotel_id = $_POST['hotel_id'];
    $hotel_name = $_POST['hotel_name'];
    $price = $_POST['price'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $guests = $_POST['guests'];
    $room_type = $_POST['room_type'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $notes = $_POST['notes'];
    $staff = $_POST['staff'];

    // Lưu thông tin đặt phòng vào cơ sở dữ liệu
    $sql = "INSERT INTO bookings (user_id, hotel_id, hotel_name, price, checkin, checkout, guests, room_type, name, email, phone, notes, staff, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);

    // Kiểm tra nếu prepare thất bại
    if ($stmt === false) {
        die("Lỗi prepare: " . $conn->error);
    }

    // Bind tham số
    $stmt->bind_param("iisississssss", $user_id, $hotel_id, $hotel_name, $price, $checkin, $checkout, $guests, $room_type, $name, $email, $phone, $notes, $staff);

    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id; // Lấy ID của booking vừa tạo

        // Gửi email xác nhận
        $mail = new PHPMailer(true);
        try {
            // Cấu hình server email
            // Server settings
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
        

            // Recipients
            $mail->setFrom(SMTP_USERNAME, 'TD Touris');
            $mail->addAddress($email);

            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = 'Xác nhận đặt phòng tại ' . $hotel_name;
            $mail->Body = "
                <h2>Xác nhận đặt phòng thành công</h2>
                <p>Chào $name,</p>
                <p>Cảm ơn bạn đã đặt phòng tại TD Touris. Dưới đây là thông tin đặt phòng của bạn:</p>
                <ul>
                    <li><strong>Mã đặt phòng:</strong> $booking_id</li>
                    <li><strong>Khách sạn:</strong> $hotel_name</li>
                    <li><strong>Ngày đi:</strong> " . date('d/m/Y', strtotime($checkin)) . "</li>
                    <li><strong>Ngày về:</strong> " . date('d/m/Y', strtotime($checkout)) . "</li>
                    <li><strong>Số người:</strong> $guests</li>
                    <li><strong>Loại phòng:</strong> $room_type</li>
                    <li><strong>Tổng giá:</strong> " . number_format($price, 0, ',', '.') . " VND</li>
                </ul>
                <p>Vui lòng liên hệ chúng tôi nếu bạn cần hỗ trợ thêm!</p>
                <p>Trân trọng,<br>TD Touris</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            // Nếu gửi email thất bại, ghi log lỗi (không ảnh hưởng đến quy trình đặt phòng)
            error_log("Email không gửi được: {$mail->ErrorInfo}");
        }

        // Hiển thị thông báo đặt phòng thành công
        echo "<!DOCTYPE html>
        <html lang='vi'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Đặt phòng thành công</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <style>
                body {
                    font-family: 'Roboto', sans-serif;
                    background-color: #f8f9fa;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                }
                .success-container {
                    max-width: 600px;
                    padding: 30px;
                    background-color: #fff;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    text-align: center;
                }
                .success-container h2 {
                    color: #28a745;
                    margin-bottom: 20px;
                }
                .success-container p {
                    font-size: 16px;
                    color: #555;
                    margin-bottom: 10px;
                }
                .success-container .btn-home {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #007bff;
                    color: #fff;
                    text-decoration: none;
                    border-radius: 5px;
                    margin-top: 20px;
                }
                .success-container .btn-home:hover {
                    background-color: #0056b3;
                }
            </style>
        </head>
        <body>
            <div class='success-container'>
                <h2>Đặt phòng thành công!</h2>
                <p>Mã đặt phòng của bạn: <strong>$booking_id</strong></p>
                <p>Chúng tôi đã gửi email xác nhận đến: <strong>$email</strong></p>
                <p>Vui lòng kiểm tra email để xem chi tiết đặt phòng.</p>
                <a href='index.php' class='btn-home'>Quay về trang chủ</a>
            </div>
        </body>
        </html>";

        $stmt->close();
        $conn->close();
        exit();
    } else {
        echo "<script>alert('Đặt phòng thất bại: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<p>Yêu cầu không hợp lệ.</p>";
}
?>