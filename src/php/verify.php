<?php
session_start();
include('config.php'); // Kết nối cơ sở dữ liệu

// Bật hiển thị lỗi để debug (xóa dòng này khi triển khai chính thức)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Kiểm tra token trong cơ sở dữ liệu
    $stmt = $conn->prepare("SELECT id FROM users WHERE verification_token = ? AND verified = 0");
    if ($stmt === false) {
        die("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Cập nhật trạng thái đã xác nhận
        $stmt = $conn->prepare("UPDATE users SET verified = 1, verification_token = NULL WHERE verification_token = ?");
        if ($stmt === false) {
            die("Lỗi chuẩn bị truy vấn: " . $conn->error);
        }
        $stmt->bind_param("s", $token);
        if ($stmt->execute()) {
            echo "Tài khoản của bạn đã được xác nhận thành công! Vui lòng đăng nhập.";
        } else {
            echo "Lỗi khi xác nhận tài khoản: " . $conn->error;
        }
    } else {
        echo "Liên kết xác nhận không hợp lệ hoặc tài khoản đã được xác nhận.";
    }
    $stmt->close();
} else {
    echo "Không tìm thấy mã xác nhận.";
}

$conn->close();
?>