<?php
header('Content-Type: application/json'); // Luôn đặt header đầu tiên

$host = "localhost";
$user = "root";
$pass = "";
$db = "do-an-1";

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Kết nối thất bại']);
    exit;
}

$sql = "SELECT * FROM hotels";
$result = $conn->query($sql);

$hotels = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['tags'] = json_decode($row['tags'], true);
        $hotels[] = $row;
    }
}

echo json_encode($hotels);
$conn->close();