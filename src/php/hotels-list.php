<?php
// Connect to the database
include "../php/config.php";

// Lấy dữ liệu từ GET request
$hotel_name = isset($_GET['hotel_name']) ? trim($_GET['hotel_name']) : '';  // Tên khách sạn
$ratings = isset($_GET['rating']) ? array_map('intval', $_GET['rating']) : [];  // Rating
$start = isset($_GET['start']) ? array_map('intval', $_GET['start']) : [];  // Lọc theo sao (1-5)


$query = "SELECT * FROM hotels h
          INNER JOIN hotels_detail hd ON h.id = hd.id_hotels
          WHERE 1";

// Lọc theo tên khách sạn nếu có
if ($hotel_name !== '') {
    $query .= " AND h.name LIKE '%" . $conn->real_escape_string($hotel_name) . "%'";
}

// Lọc theo rating nếu có
if (!empty($ratings)) {
    $query .= " AND h.rating IN (" . implode(",", array_map('intval', $ratings)) . ")";
}

// Lọc theo sao (star) nếu có
if (!empty($start)) {
    $query .= " AND h.start IN (" . implode(",", array_map('intval', $start)) . ")";
}

$result = $conn->query($query);
if (!$result) {
    die("Lỗi truy vấn SQL: " . $conn->error);
}
?>

<!--Icon Bootstrap-->
<link rel=" stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/sell-tickets.css">
<!-- Bootstrap CSS -->
<link href=" https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome cho icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách khách sạn</title>
</head>

<body>
    <div class="content-full">
        <div class="container mt-3 bg-body-secondary p-3 rounded-3 shadow-sm"
            style="min-height: 40px; position: relative;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="mb-0 fw-bold text-primary">
                        Khách sạn Phú Quốc
                        <a href="https://www.google.com/maps/place/Phú+Quốc" class="map-link ms-2" target="_blank"
                            rel="noopener noreferrer">
                            <i class="bi bi-geo-alt-fill"></i> XEM BẢN ĐỒ
                        </a>
                    </h5>
                </div>
                <div class="small-note text-end">
                    *Giá trung bình phòng 1 đêm cho 2 khách
                </div>
            </div>
        </div>
        <div class="d-flex gap-4 p-3">
            <div>
                <div class="card p-3 d-flex flex-row " style=" max-height: 110px; min-width: 300px; margin-top: 20px;">
                    <img src="../public/images/images-hotel/bg-tickets/avata-support.jpg" alt="Hỗ trợ viên"
                        class="rounded-circle me-3" style="width: 80px; height: 80px; object-fit: cover;">
                    <div>
                        <h6 class="fw-bold mb-2">Cần hỗ trợ?</h6>
                        <div class="d-flex justify-content-between ">
                            <span>HD</span>
                            <a class="text-orange ms-2 text-decoration-none" href="tel:0948773012">0948773012</a>
                        </div>
                    </div>
                </div>
                <form method="GET" class="card p-3" style="max-height: auto; min-width: 300px; margin-top: 20px;">

                    <div class="input-group mb-3">
                        <input type="text" name="hotel_name" class="form-control" placeholder="Nhập tên khách sạn"
                            value="<?php echo isset($_GET['hotel_name']) ? $_GET['hotel_name'] : ''; ?>">
                        <button class="btn btn-warning text-white" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

                    <div class="mb-2 fw-bold">Hạng sao</div>

                    <?php
                    for ($i = 5; $i >= 1; $i--) {
                        echo '<div class="form-check mb-1">';
                        echo '<input class="form-check-input" type="checkbox" name="start[]" value="' . $i . '" id="start' . $i . '" ' . (in_array($i, $start) ? 'checked' : '') . '>';
                        echo '<label class="form-check-label ms-2" for="start' . $i . '">';
                        for ($j = 1; $j <= 5; $j++) {
                            if ($j <= $i) {
                                echo '<i class="fas fa-star text-warning"></i>';
                            } else {
                                echo '<i class="far fa-star text-secondary"></i>';
                            }
                        }
                        echo '</label>';
                        echo '</div>';
                    }
                    ?>


                    <hr class="my-3" style="margin-top: 10px;">
                    <div id="location-list" class="mb-4" style="margin-top: 10px;">
                        <h6 class="fw-bold">Khu vực</h6>
                    </div>
                </form>
            </div>

            <div class="container">
                <?php
                if ($result->num_rows > 0) {
                    // Loop through the hotel data
                    while ($row = $result->fetch_assoc()) {
                        // Decode the tags if they are stored as a JSON array
                        $tags = json_decode($row['tags']);
                        $tagsHTML = '';
                        if (!empty($tags)) {
                            foreach ($tags as $tag) {
                                $tagsHTML .= '<span class="badge bg-secondary">' . htmlspecialchars($tag) . '</span>';
                            }
                        }
                        ?>
                        <div class="combo-banner">
                            <p class="mb-1"><?php echo htmlspecialchars($row['description']); ?></p>
                            <div class="deal-box position-relative card overflow-hidden">
                                <a href="chi-tiet-<?php echo strtolower(str_replace(' ', '-', $row['name'])); ?>.html"
                                    class="stretched-link"></a>

                                <div class="row g-0">
                                    <div class="col-md-3 position-relative">
                                        <div class="ribbon">
                                            <?php
                                            // Giải mã JSON thành mảng PHP
                                            $tags = json_decode($row['tags'] ?? '[]', true);
                                            if (is_array($tags)) {
                                                foreach ($tags as $tag) {
                                                    echo '<span class="badge">'
                                                        . htmlspecialchars($tag)
                                                        . '</span>';
                                                }
                                            }
                                            ?>
                                        </div>

                                        <img src="../public/images/images-hotel/bg-tickets/<?php echo htmlspecialchars($row['image']); ?>"
                                            class="img-fluid w-100 h-100 object-fit-cover" alt="Khách sạn">

                                    </div>

                                    <div class="col-md-6 p-3 position-relative">
                                        <h5 class="fw-bold text-primary mb-2"><?php echo htmlspecialchars($row['name']); ?></h5>
                                        <div class="mb-2">
                                            <span class="text-warning">
                                                <?php
                                                $numStars = (int) $row['start'];
                                                for ($i = 1; $i <= 5; $i++) {
                                                    echo $i <= $numStars ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                                }
                                                ?>
                                            </span>

                                            <span class="badge bg-success ms-2">
                                                <?= number_format($row['rating'], 1) ?>
                                                <?= $row['rating'] >= 9.0 ? 'Tuyệt vời' : ($row['rating'] >= 8.0 ? 'Rất tốt' : 'Tốt') ?>
                                            </span>
                                            <small class="text-muted">| <?php echo $row['reviews']; ?> đánh giá</small>
                                        </div>
                                        <div class="mb-2 font-text">
                                            <i class="bi bi-geo-alt-fill text-danger"></i>
                                            <?php echo htmlspecialchars($row['location']); ?> -
                                            <a href="https://www.google.com/maps?q=<?php echo urlencode($row['name']); ?>"
                                                class="text-decoration-none text-primary map-link" target="_blank">Xem bản
                                                đồ</a>
                                        </div>
                                        <div class="hotel-tags">
                                            <?php echo $tagsHTML; ?>
                                        </div>
                                    </div>

                                    <div
                                        class="col-md-3 d-flex flex-column justify-content-center align-items-start p-3 bg-light">
                                        <h6 class="text-info fw-bold mb-2">🎁 Ưu đãi bí mật</h6>
                                        <p class="mb-1"><?php echo htmlspecialchars($row['description']); ?></p>
                                        <small class="text-muted">📍 Gồm ăn sáng</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>No hotels found.</p>";
                }
                ?>
            </div>
        </div>
    </div>


    </div>
</body>

</html>


<script src="../js/sell-tickets-location.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php
// Close the database connection
$conn->close();
?>