<?php

$conn = new mysqli("localhost", "root", "", "do-an-1");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Thiếu hoặc sai định dạng ticket_id");
}

$ticket_id = intval($_GET['id']);

// Lấy thông tin chính
$ticket_sql = "SELECT * FROM `tickets-tour` WHERE id = $ticket_id";
$ticket_result = $conn->query($ticket_sql);
$ticket = $ticket_result->fetch_assoc();

// Lấy thông tin chi tiết
$details_sql = "SELECT * FROM ticket_details WHERE ticket_id = $ticket_id";
$details_result = $conn->query($details_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h2><?= $ticket['title'] ?></h2>
    <p>Địa điểm: <?= $ticket['location'] ?></p>
    <p>Giá: <?= $ticket['price'] ?>đ</p>
    <p>Đánh giá: <?= $ticket['rating'] ?>/10 (<?= $ticket['reviews'] ?> reviews)</p>

    <h3>Chi tiết combo:</h3>
    <ul>
        <?php while ($row = $details_result->fetch_assoc()): ?>
            <li><?= $row['icon'] ?> <strong><?= $row['detail_title'] ?>:</strong> <?= $row['detail_description'] ?></li>
        <?php endwhile; ?>
    </ul>
</body>

</html>