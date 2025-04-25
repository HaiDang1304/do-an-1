<?php
include "../php/config.php";
session_start();
// Lấy dữ liệu GET request
$hotel_name = isset($_GET['hotel_name']) ? trim($_GET['hotel_name']) : '';  // Tên khách sạn
$ratings = isset($_GET['rating']) ? array_map('intval', $_GET['rating']) : [];  // Rating
$start = isset($_GET['start']) ? array_map('intval', $_GET['start']) : [];  // Lọc theo sao (1-5)
$areas = isset($_GET['area']) ? $_GET['area'] : [];  // Lọc theo khu vực

$query = "SELECT h.*, hd.description, l.area_name 
          FROM hotels h
          INNER JOIN hotels_detail hd ON h.id = hd.id_hotels
          LEFT JOIN location l ON h.location_id = l.id
          WHERE 1";

// Lọc theo tên khách sạn 
if ($hotel_name !== '') {
    $query .= " AND (h.name LIKE '%" . $conn->real_escape_string($hotel_name) . "%' OR hd.description LIKE '%" . $conn->real_escape_string($hotel_name) . "%')";
}

// Lọc theo rating 
if (!empty($ratings)) {
    $query .= " AND h.rating IN (" . implode(",", array_map('intval', $ratings)) . ")";
}

// Lọc theo sao (star) 
if (!empty($start)) {
    $query .= " AND h.start IN (" . implode(",", array_map('intval', $start)) . ")";
}

// Lọc theo khu vực
if (!empty($areas)) {
    $escaped_areas = array_map(function ($area) use ($conn) {
        return "'" . $conn->real_escape_string($area) . "'";
    }, $areas);
    $query .= " AND l.area_name IN (" . implode(',', $escaped_areas) . ")";
}

$result = $conn->query($query);
if (!$result) {
    die("Lỗi truy vấn SQL: " . $conn->error);
}

// Đếm số khách sạn theo khu vực
$area_counts = [];
$area_query = "SELECT l.area_name, COUNT(h.id) as count 
               FROM location l 
               LEFT JOIN hotels h ON h.location_id = l.id 
               GROUP BY l.id, l.area_name";
$area_result = $conn->query($area_query);
while ($row = $area_result->fetch_assoc()) {
    $area_counts[$row['area_name']] = $row['count'];
}
?>

<!--Icon Bootstrap-->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/sell-tickets.css">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <?php include 'header.php'; ?>
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
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="area[]" value="Bắc Đảo" id="bac-dao"
                                <?php if (isset($_GET['area']) && in_array('Bắc Đảo', $_GET['area']))
                                    echo 'checked'; ?>>
                            <label class="form-check-label" for="bac-dao">Bắc Đảo
                                (<?php echo isset($area_counts['Bắc Đảo']) ? $area_counts['Bắc Đảo'] : 0; ?>)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="area[]" value="Bãi Kem/Cáp Hòn Thơm"
                                id="bai-kem" <?php if (isset($_GET['area']) && in_array('Bãi Kem/Cáp Hòn Thơm', $_GET['area']))
                                    echo 'checked'; ?>>
                            <label class="form-check-label" for="bai-kem">Bãi Kem/Cáp Hòn Thơm
                                (<?php echo isset($area_counts['Bãi Kem/Cáp Hòn Thơm']) ? $area_counts['Bãi Kem/Cáp Hòn Thơm'] : 0; ?>)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="area[]" value="Chợ đêm Dinh Cậu"
                                id="cho-dem" <?php if (isset($_GET['area']) && in_array('Chợ đêm Dinh Cậu', $_GET['area']))
                                    echo 'checked'; ?>>
                            <label class="form-check-label" for="cho-dem">Chợ đêm Dinh Cậu
                                (<?php echo isset($area_counts['Chợ đêm Dinh Cậu']) ? $area_counts['Chợ đêm Dinh Cậu'] : 0; ?>)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="area[]" value="Dương Đông"
                                id="duong-dong" <?php if (isset($_GET['area']) && in_array('Dương Đông', $_GET['area']))
                                    echo 'checked'; ?>>
                            <label class="form-check-label" for="duong-dong">Dương Đông
                                (<?php echo isset($area_counts['Dương Đông']) ? $area_counts['Dương Đông'] : 0; ?>)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="area[]" value="Nam Đảo" id="nam-dao"
                                <?php if (isset($_GET['area']) && in_array('Nam Đảo', $_GET['area']))
                                    echo 'checked'; ?>>
                            <label class="form-check-label" for="nam-dao">Nam Đảo
                                (<?php echo isset($area_counts['Nam Đảo']) ? $area_counts['Nam Đảo'] : 0; ?>)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="area[]" value="Phú Quốc United Center"
                                id="united-center" <?php if (isset($_GET['area']) && in_array('Phú Quốc United Center', $_GET['area']))
                                    echo 'checked'; ?>>
                            <label class="form-check-label" for="united-center">Phú Quốc United Center
                                (<?php echo isset($area_counts['Phú Quốc United Center']) ? $area_counts['Phú Quốc United Center'] : 0; ?>)</label>
                        </div>
                    </div>
                </form>
                <!-- introduce -->
                <div class="card p-3" style="max-width: 320px; margin-top: 20px;">
                    <div class="bg-body-secondary" style="text-align: center; border-radius: 9px; margin-bottom: 10px;">
                        <h6 class="fw-bold text-secondary mb-2 " style="margin-top: 5px;">Kinh nghiệm du lịch Phú Quốc
                        </h6>
                    </div>

                    <p class="mb-2" style="font-size: 14px;">
                        Là hòn đảo lớn nhất Việt Nam, Phú Quốc sở hữu những bãi biển trong vắt, những dòng suối yên bình
                        cùng khu rừng
                        nguyên sinh rộng lớn. <br><br>
                        Nhắc tới Phú Quốc, không thể không nhắc tới bãi Sao, bãi Dài, đặc sản hải sản khô tiêu, nước
                        mắm, rượu sim hay
                        ngọc trai... Còn nữa, Phú Quốc còn nhiều ẩn số đang chờ bạn khám phá đó, đừng bỏ lỡ!
                    </p>
                    <a href="#" class="text-primary" style="font-size: 14px;">Xem thêm</a>
                </div>
            </div>

            <div class="container">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $tags = json_decode($row['tags']);
                        $tagsHTML = '';
                        if (!empty($tags)) {
                            foreach ($tags as $tag) {
                                $tagsHTML .= '<span class="badge bg-secondary">' . htmlspecialchars($tag) . '</span>';
                            }
                        }
                        ?>
                        <div class="combo-banner">
                            <p class="mb-1">
                                <?php echo isset($row['description']) && !is_null($row['description']) ? htmlspecialchars($row['description']) : 'Chưa có mô tả'; ?>
                            </p>
                            <div class="deal-box position-relative card overflow-hidden">
                                <a href="../php/hotels-detail.php?id=<?php echo htmlspecialchars($row['id']); ?>"
                                    class="stretched-link"></a>
                                <div class="row g-0">
                                    <div class="col-md-3 position-relative">
                                        <div class="ribbon">
                                            <?php
                                            $tags = json_decode($row['tags'] ?? '[]', true);
                                            if (is_array($tags)) {
                                                foreach ($tags as $tag) {
                                                    echo '<span class="badge">' . htmlspecialchars($tag) . '</span>';
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
                                        <p class="mb-1">
                                            <?php echo isset($row['description']) && !is_null($row['description']) ? htmlspecialchars($row['description']) : 'Chưa có mô tả'; ?>
                                        </p>
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
        <div class="container mt-3">
            <div class="card p-3">
                <h5 class="fw-bold text-primary mb-2">Khách sạn Phú Quốc</h5>
                <div class="d-flex align-items-center mb-2">
                    <span class="text-warning">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                        <i class="far fa-star"></i>
                    </span>
                    <span class="ms-2">2.9/5 trên 1245 đánh giá</span>
                    <span class="ms-auto">
                        <button class="btn btn-outline-primary btn-sm me-2">
                            <i class="bi bi-hand-thumbs-up"></i> Like 29K
                        </button>
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-share"></i> Share
                        </button>
                    </span>
                </div>
                <p class="mb-2">
                    Cách đây ít năm, các khách sạn Phú Quốc vẫn còn rất ít và du lịch chưa phát triển mạnh mẽ. Nhưng,
                    với vị thế là hòn đảo lớn nhất Việt Nam, Phú Quốc đang dần lột xác trở thành một thiên đường nghỉ
                    dưỡng với rất nhiều dịch vụ lưu trú từ homestay, nhà nghỉ bình dân, khách sạn, khu nghỉ dưỡng cao
                    cấp, biệt thự hướng biển sang… Do vị trí đặc biệt nằm trong vịnh Thái Lan và sự hùng vĩ của biển cả,
                    trải dài đến những bãi cát trắng mịn, Phú Quốc thích hợp cho việc du lịch khám phá lẫn nghỉ dưỡng
                    vào bất cứ thời gian nào trong năm.
                </p>
                <p class="mb-2">
                    Đa phần các khu nghỉ dưỡng nằm dọc bãi Trường, bãi Dài, bãi Ông Lang, bãi Sao, bãi Khem… nhưng tập
                    trung nhiều nhất vẫn là các khách sạn trên đường Trần Hưng Đạo hướng vào thị trấn Dương Đông với mức
                    giá vừa phải. Nếu yêu thích sự sôi động, bạn có thể chọn khách sạn Phú Quốc gần chợ đêm Dinh Cậu
                    nha.
                </p>
                <p class="mb-0">
                    TD Touris sẽ giúp bạn tìm được khách sạn Phú Quốc chất lượng tốt với mức giá thấp nhất, những combo
                    trọn gói bao gồm cả vé máy bay khởi hành từ nhiều thời gian kiểm hay so sánh để có được một chuyến
                    đi đáng tiền.
                </p>
            </div>
        </div>
        <?php include 'footer.php'; ?>
    </div>
</body>

</html>
<script src="../js/sell-tickets-location.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php
$conn->close();
?>