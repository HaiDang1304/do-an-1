<?php
header('Content-Type: application/json'); // Luôn đặt header đầu tiên

$host = "localhost";
$user = "root";
$pass = "";
$db = "do-an-1";

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'Kết nối DB thất bại: ' . $conn->connect_error]));
}

$sql = "SELECT 
            t.title, t.location, t.rating, t.reviews, t.price, 
            i.filename, i.pswp_width, i.pswp_height, i.alt_text
        FROM `tickets-tour` t
        JOIN `images` i ON t.image_id = i.id";

$result = $conn->query($sql);
if (!$result) {
    http_response_code(500);
    die(json_encode(['error' => 'Lỗi truy vấn: ' . $conn->error]));
}

$tours = [];
while($row = $result->fetch_assoc()) {
    $tours[] = [
        'title' => $row['title'],
        'image' => '../public/images/' . $row['filename'], // Đường dẫn chuẩn
        'width' => $row['pswp_width'],
        'height' => $row['pswp_height'],
        'alt' => $row['alt_text']
    ];
}

$conn->close();
echo json_encode(['data' => $tours]);
exit;
?>