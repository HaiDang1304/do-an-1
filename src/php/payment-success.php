<?php
session_start();
include 'config.php';

// Kiểm tra booking_id từ URL
if (!isset($_GET['booking_id']) || !is_numeric($_GET['booking_id'])) {
    header("Location: index.php");
    exit();
}

$booking_id = $_GET['booking_id'];

// Lấy thông tin đặt phòng từ cơ sở dữ liệu
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    echo "<p>Đặt phòng không tồn tại.</p>";
    exit();
}

$stmt->close();
$conn->close();

// Tạo URL cho mã QR (giả lập, bạn có thể dùng API như Google Charts để tạo mã QR động)
$qr_url = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode("Mã đặt phòng: $booking_id");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán thành công</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/hotel-details.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .payment-success-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            text-align: center;
        }
        .payment-success-container h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        .payment-success-container .qr-code {
            margin: 20px 0;
        }
        .payment-success-container .qr-code img {
            width: 150px;
            height: 150px;
        }
        .payment-success-container .bank-details {
            margin: 20px 0;
        }
        .payment-success-container .bank-details p {
            font-size: 16px;
            color: #555;
            margin: 5px 0;
        }
        .payment-success-container .note {
            font-size: 14px;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <div class="payment-success-container">
        <h2>Quét mã QR để thanh toán</h2>
        <p>Mã đặt phòng của bạn: <strong><?= $booking_id ?></strong></p>
        <p>Đơn hàng sẽ bị hủy sau: <span id="timer">00:10:30</span></p>

        <div class="qr-code">
            <img src="<?= $qr_url ?>" alt="QR Code">
        </div>

        <div class="bank-details">
            <p><strong>Ngân hàng:</strong>BIDV</p>
            <p><strong>Số tài khoản:</strong>7302222351</p>
            <p><strong>Tên tài khoản:</strong>Lữ Hải Đăng</p>
            <p><strong>Số tiền:</strong> <?= number_format($booking['price'], 0, ',', '.') ?> VND</p>
            <p><strong>Nội dung:</strong>BOOKING-TDTOURIS</p>
        </div>

        <p class="note">Lưu ý: Nếu đơn hàng của bạn không được thanh toán sau khi chuyển khoản 5 phút, vui lòng liên hệ với đội ngũ hỗ trợ để được hỗ trợ.</p>
    </div>

    <script>
        // Đếm ngược thời gian (giả lập)
        let time = 10 * 60 + 30; // 10 phút 31 giây
        const timerElement = document.getElementById('timer');
        const countdown = setInterval(() => {
            const minutes = Math.floor(time / 60);
            const seconds = time % 60;
            timerElement.textContent = `00:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            time--;
            if (time < 0) {
                clearInterval(countdown);
                timerElement.textContent = 'Hết thời gian';
            }
        }, 1000);
    </script>
</body>
</html>