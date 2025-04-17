<?php
$host = "localhost";
$user = "root";
$pass = ""; // mật khẩu MySQL mặc định thường là rỗng
$db = "do-an-1";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8"); // xử lý tiếng Việt
?>
