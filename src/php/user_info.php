<?php
session_start();
require_once __DIR__ . '/config.php';

// Kiểm tra đăng nhập người dùng
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Lấy thông tin người dùng
$user_id = $_SESSION['user']['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Lấy lịch sử đặt khách sạn
$stmt = $conn->prepare("
    SELECT b.* 
    FROM bookings b 
    WHERE b.user_id = ? 
    ORDER BY b.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Người Dùng</title>
    <link rel="stylesheet" href="../css/doan.css">
    <link rel="stylesheet" href="../css/user_info.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h1 class="my-4 text-center" style="color: #007bff; font-size: 28px;">Thông Tin Người Dùng</h1>

        <!-- Thông tin người dùng -->
        <div class="user-info-card">
            <div class="user-avatar">
                <?= strtoupper(substr($user['username'], 0, 1)) ?>
            </div>
            <div>
                <h3>Xin chào, <?= htmlspecialchars($user['username']) ?>!</h3>
                <p><i class="fas fa-envelope"></i><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><i class="fas fa-sign-in-alt"></i><strong>Loại đăng nhập:</strong> <?= htmlspecialchars($user['login_type']) ?></p>
                <p><i class="fas fa-calendar-alt"></i><strong>Ngày tạo tài khoản:</strong> <?= $user['created_at'] ?></p>

            </div>
        </div>

        <!-- Lịch sử đặt khách sạn -->
        <div class="booking-history">
            <h3>Lịch Sử Đặt Khách Sạn</h3>
            <?php if ($bookings->num_rows > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên khách sạn</th>
                            <th>Giá (VND)</th>
                            <th>Ngày nhận phòng</th>
                            <th>Ngày trả phòng</th>
                            <th>Số khách</th>
                            <th>Loại phòng</th>
                            <th>Ngày đặt</th>
                            <th>Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = $bookings->fetch_assoc()): ?>
                            <tr>
                                <td><?= $booking['id'] ?></td>
                                <td><?= htmlspecialchars($booking['hotel_name']) ?></td>
                                <td><?= number_format($booking['price'], 0, ',', '.') ?></td>
                                <td><?= $booking['checkin'] ?></td>
                                <td><?= $booking['checkout'] ?></td>
                                <td><?= $booking['guests'] ?></td>
                                <td><?= htmlspecialchars($booking['room_type']) ?></td>
                                <td><?= $booking['created_at'] ?></td>
                                <td>
                                    <button class="btn-details" >Xem chi tiết</button> 
                                    <!-- <onclick="alert('Chức năng xem chi tiết đang được phát triển!')" -->
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center" style="color: #666;">Bạn chưa có lịch sử đặt khách sạn nào.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>