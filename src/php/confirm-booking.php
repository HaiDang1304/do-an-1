<?php
session_start();
include 'config.php';

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php?error=Please login to book");
    exit();
}

// Kiểm tra dữ liệu từ form
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$hotel_id = $_POST['hotel_id'];
$hotel_name = $_POST['hotel_name'];
$price = $_POST['price'];
$checkin = $_POST['checkin'];
$checkout = $_POST['checkout'];
$guests = $_POST['guests'];
$room_type = $_POST['room_type'];

// Lấy thông tin người dùng từ session
$username = $_SESSION['user']['username'];
$email = $_SESSION['user']['email'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đặt phòng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/hotel-details.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .confirmation-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .confirmation-container h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        .confirmation-container .info-box {
            background-color: #e6f7ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .confirmation-container .info-box h5 {
            font-size: 18px;
            color: #0d6efd;
            margin-bottom: 10px;
        }
        .confirmation-container .form-group {
            margin-bottom: 15px;
        }
        .confirmation-container .form-group label {
            font-weight: 500;
            color: #333;
        }
        .confirmation-container .form-group input,
        .confirmation-container .form-group select,
        .confirmation-container .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .confirmation-container .price-details {
            margin-top: 20px;
            padding: 15px;
            border-top: 1px solid #ddd;
        }
        .confirmation-container .price-details h5 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }
        .confirmation-container .price-details p {
            font-size: 16px;
            color: #555;
            margin-bottom: 5px;
        }
        .confirmation-container .price-details .total {
            font-size: 20px;
            font-weight: 600;
            color: #e74c3c;
        }
        .confirmation-container .btn-confirm {
            background-color: #28a745;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .confirmation-container .btn-confirm:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <div class="confirmation-container">
        <h2>Thông tin đặt phòng</h2>

        <div class="info-box">
            <h5><?= htmlspecialchars($hotel_name) ?> <i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i></h5>
            <p><i class="fas fa-map-marker-alt"></i> Bãi Biển, Bãi Dài, Gành Dầu, Phú Quốc</p>
            <p><strong>Check-in:</strong> CN, <?= date('d/m/Y', strtotime($checkin)) ?> - Từ 14:00</p>
            <p><strong>Check-out:</strong> T3, <?= date('d/m/Y', strtotime($checkout)) ?> - Trước 12:00</p>
            <p><strong>Phòng:</strong> 1 phòng - <?= htmlspecialchars($room_type) ?> - Gồm 3 bữa ăn</p>
            <p><strong>Số người:</strong> <?= htmlspecialchars($guests) ?> người</p>
            <p><i class="fas fa-times-circle text-success"></i> Không hủy, không hoàn tiền</p>
        </div>

        <form action="process-booking.php" method="POST">
            <input type="hidden" name="hotel_id" value="<?= $hotel_id ?>">
            <input type="hidden" name="hotel_name" value="<?= htmlspecialchars($hotel_name) ?>">
            <input type="hidden" name="price" value="<?= $price ?>">
            <input type="hidden" name="checkin" value="<?= $checkin ?>">
            <input type="hidden" name="checkout" value="<?= $checkout ?>">
            <input type="hidden" name="guests" value="<?= $guests ?>">
            <input type="hidden" name="room_type" value="<?= $room_type ?>">

            <h5>Thông tin người đặt</h5>
            <div class="form-group">
                <label for="name">Họ và tên <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($username) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                <input type="text" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="notes">Yêu cầu đặc biệt</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Nếu quý khách có yêu cầu đặc biệt, vui lòng cho chúng tôi biết tại đây"></textarea>
            </div>

            <h5>Chọn nhân viên tư vấn</h5>
            <div class="form-group">
                <label for="staff">Họ và tên</label>
                <select id="staff" name="staff">
                    <option value="cannhi">Cần Nhi</option>
                    <option value="other">Nhân viên khác</option>
                </select>
            </div>

            <div class="price-details">
                <h5>Tổng cộng</h5>
                <p>Khách sạn: <?= number_format($price, 0, ',', '.') ?> VND</p>
                <p>Thuế & phí: 0 VND</p>
                <p class="total">Tổng: <?= number_format($price, 0, ',', '.') ?> VND</p>
                <p><small>Gói bao gồm thuế & phí</small></p>
            </div>

            <button type="submit" class="btn-confirm">Xác nhận đặt phòng</button>
        </form>
    </div>
</body>
</html>