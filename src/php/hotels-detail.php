<?php
include "../php/config.php";

// Check if the 'id' parameter is set and is numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Use prepared statement to avoid SQL injection
    $sql = "SELECT 
                hotels.*, 
                hotels_detail.*
            FROM hotels
            INNER JOIN hotels_detail ON hotels.id = hotels_detail.id_hotels
            WHERE hotels.id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if the hotel is found
    if ($hotel = mysqli_fetch_assoc($result)) {
        // Decode images JSON safely
        $images = isset($hotel['images']) ? json_decode($hotel['images'], true) : [];
        ?>
        <?php include 'header.php'; ?>
        <div class="container py-4">
            <div class="row g-4 align-items-start">
                <div class="col-md-3">
                    <div class="map-responsive shadow-sm rounded-4 border">
                        <?= $hotel['map_embed'] ?>
                    </div>

                    <h3 class="text-primary fw-bold mt-2" style="font-size: 17px;">
                        Trải nghiệm phải thử ở <?= htmlspecialchars($hotel['name']) ?>
                    </h3>

                    <?php
                    $experiences = json_decode($hotel['experience'], true);

                    if (json_last_error() === JSON_ERROR_NONE && is_array($experiences)):
                        foreach ($experiences as $exp):
                            if (!empty($exp['title']) && !empty($exp['content'])): ?>
                                <h5 class="fw-bold mt-4 font-size"><?= htmlspecialchars($exp['title']) ?></h5>
                                <div class="font-size">
                                    <p><?= nl2br(htmlspecialchars($exp['content'])) ?></p>
                                </div>
                            <?php endif;
                        endforeach;
                    else:
                        echo "<p>Không có trải nghiệm nào để hiển thị.</p>";
                    endif;
                    ?>
                </div>

                <div class="col-md-6 flex-fill">
                    <div class="hotel-card d-flex justify-content-between align-items-start p-3 border rounded shadow-sm mb-4">
                        <div>
                            <h5 class="fw-bold text-primary mb-1">
                                <?= htmlspecialchars($hotel['name']) ?>
                                <i class="fa-solid fa-heart text-danger"></i>
                            </h5>

                            <div class="d-flex align-items-center mb-2 flex-wrap">
                                <div class="badge bg-success me-2"><?= number_format($hotel['rating'], 1) ?></div>
                                <span class="text-success fw-medium me-2">
                                    <?= $hotel['rating'] >= 9.0 ? 'Tuyệt vời' : ($hotel['rating'] >= 8.0 ? 'Rất tốt' : 'Tốt') ?>
                                </span>
                            </div>

                            <div class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?= htmlspecialchars($hotel['address']) ?>
                            </div>
                        </div>

                        <div class="text-end">
                            <small class="text-muted">Giá chỉ từ</small>
                            <h4 class="text-info fw-bold">
                                <?= number_format($hotel['price'], 0, ',', '.') ?>
                                <span class="fs-6">VND</span>
                            </h4>
                            <button class="btn btn-warning fw-bold text-white px-4 mt-1">Đặt ngay</button>
                        </div>
                    </div>


                    <div class="gallery-container">
                        <div class="swiper main-swiper mb-3">
                            <div class="swiper-wrapper">
                                <?php
                                $experiences = json_decode($hotel['gallery'], true);

                                if (json_last_error() === JSON_ERROR_NONE && is_array($experiences)):
                                    foreach ($experiences as $exp):
                                        if (!empty($exp['main-images']) && is_array($exp['main-images'])):
                                            foreach ($exp['main-images'] as $image): ?>
                                                <div class="swiper-slide">
                                                    <img src="../public/images/images-hotel/image-book-hotel/<?= htmlspecialchars($image) ?>"
                                                        alt="Hình ảnh khách sạn" />
                                                </div>
                                            <?php endforeach;
                                        endif;
                                    endforeach;
                                else:
                                    echo "<p>Không có hình ảnh nào để hiển thị.</p>";
                                endif;
                                ?>

                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                        <div class="swiper thumb-swiper">
                            <div class="swiper-wrapper">
                                <?php
                                $experiences = json_decode($hotel['gallery'], true);

                                if (json_last_error() === JSON_ERROR_NONE && is_array($experiences)):
                                    foreach ($experiences as $exp):
                                        if (!empty($exp['sub-images']) && is_array($exp['sub-images'])):
                                            foreach ($exp['sub-images'] as $image): ?>
                                                <div class="swiper-slide">
                                                    <img src="../public/images/images-hotel/image-book-hotel/<?= htmlspecialchars($image) ?>"
                                                        alt="Hình ảnh khách sạn" />
                                                </div>
                                            <?php endforeach;
                                        endif;
                                    endforeach;
                                else:
                                    echo "<p>Không có hình ảnh nào để hiển thị.</p>";
                                endif;
                                ?>
                            </div>
                        </div>

                    </div>
                    <div id="video-tag" style="display: flex; align-items: center; gap: 15px; cursor: pointer;"
                        class="video-thumbnail" onclick="showVideo()">
                        <img style="width: 100px;margin-top: 20px;"
                            src="https://img.youtube.com/vi/<?= htmlspecialchars($hotel['youtube_id']) ?>/0.jpg"
                            alt="thumbnail">
                        <div>
                            <div class="badge">Video</div>
                            <div style="color: teal; font-size: 18px; font-weight: 500;">
                                <?= htmlspecialchars($hotel['title_ytb']) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Iframe YouTube -->
                    <div id="video-embed" style="display: none;">
                        <h3 style="color:#3366CC ; margin-top: 20px"><b><?= htmlspecialchars($hotel['title_ytb']) ?></b></h3>
                        <iframe width="830" height="415"
                            src="https://www.youtube.com/embed/<?= htmlspecialchars($hotel['youtube_id']) ?>"
                            title="YouTube video player" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen></iframe>
                    </div>
                    <?php
                    // Giả sử $hotel là mảng dữ liệu đã lấy từ DB, chứa trường combo_details
                    $combo = json_decode($hotel['combo_details'], true);
                    ?>

                    <div class="combo-tour border rounded p-4 mt-4" style="background-color: rgba(198, 134, 88, 0.258);">
                        <h4 class="text-primary fw-bold">
                            <?= htmlspecialchars($combo['combo_name'] ?? 'Tên combo chưa có') ?>
                        </h4>
                        <p><?= htmlspecialchars($combo['description'] ?? 'Không có mô tả combo') ?></p>

                        <?php if (!empty($combo['included']) && is_array($combo['included'])): ?>
                            <ul>
                                <?php foreach ($combo['included'] as $item): ?>
                                    <li>
                                        <strong><?= htmlspecialchars($item['title'] ?? 'Không có tiêu đề') ?>:</strong>
                                        <?= htmlspecialchars($item['detail'] ?? 'Không có mô tả') ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Không có thông tin chi tiết cho combo này.</p>
                        <?php endif; ?>

                        <?php if (!empty($combo['special_moments'])): ?>
                            <div class="special-moments mt-3">
                                <strong>🌅 Khoảnh Khắc Đáng Nhớ:</strong>
                                <p><?= htmlspecialchars($combo['special_moments']) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($combo['facilities']) && is_array($combo['facilities'])): ?>
                            <div class="facilities mt-3">
                                <strong>Tiện ích đa dạng:</strong>
                                <ul>
                                    <?php foreach ($combo['facilities'] as $facility): ?>
                                        <li><?= htmlspecialchars($facility) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($combo['extra_notes'])): ?>
                            <div class="extra-notes mt-3">
                                <strong>Ghi chú đặc biệt:</strong>
                                <p><?= htmlspecialchars($combo['extra_notes']) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($combo['special_note'])): ?>
                            <div class="special-note mt-3">
                                <strong>Đặc biệt:</strong>
                                <p><?= htmlspecialchars($combo['special_note']) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($combo['conditions']) && is_array($combo['conditions'])): ?>
                            <div class="conditions mt-3">
                                <strong>Điều kiện áp dụng:</strong>
                                <ul>
                                    <?php foreach ($combo['conditions'] as $condition): ?>
                                        <li><?= htmlspecialchars($condition) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>







                </div>
                <script>
                    function showVideo() {
                        document.getElementById("video-embed").style.display = "block";
                        document.getElementById("video-tag").style.display = "none";
                    }
                </script>
                <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
                <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
                <script>
                    // Khởi tạo thumb swiper trước
                    const thumbSwiper = new Swiper('.thumb-swiper', {
                        spaceBetween: 2,
                        slidesPerView: 5,
                        freeMode: true,
                        watchSlidesProgress: true,
                    });

                    // Sau đó khởi tạo main swiper và gán thumbSwiper
                    const mainSwiper = new Swiper('.main-swiper', {
                        loop: true,
                        slidesPerView: 1,
                        spaceBetween: 2,
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                        },
                        thumbs: {
                            swiper: thumbSwiper,
                        },
                    });
                </script>

                <link rel="stylesheet" href="../css/hotel-details.css">

                <link href=" https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <?php
    } else {
        echo "<p>Khách sạn không tồn tại hoặc đã bị xóa.</p>";
    }
} else {
    echo "<p>Không có mã khách sạn.</p>";
}
?>