<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "do-an-1";

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Truy vấn JOIN giữa tickets-tour và images
$sql = "
SELECT 
    t.id, 
    t.title, 
    t.location, 
    t.rating, 
    t.reviews, 
    t.price,
    i.filename AS image_url,
    i.alt_text,
    i.pswp_width,
    i.pswp_height
FROM `tickets-tour` t
LEFT JOIN `images` i ON t.image_id = i.id
";

$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Gắn thêm đường dẫn hình ảnh
        $row['image_url'] = '../public/images/' . $row['image_url'];
        $data[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($data);
?>