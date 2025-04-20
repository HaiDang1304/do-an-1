<?php
include "../php/config.php";
// travel-guides
$sql = "SELECT * FROM travel_guides";
$result = $conn->query($sql);
// top_hotels
$sqlhotels = " SELECT * FROM top_hotels";
$resulthotels = $conn->query($sqlhotels);
// top_activities
$sqlactivities = "SELECT * FROM top_activities";
$resultactivities = $conn->query($sqlactivities);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Trang chính</title>

    <link rel="stylesheet" href="../css/doan.css">

    <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css">
    <script src="https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/5.3.3/photoswipe.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>
    <?php include 'video_bg.php'; ?>
    <div class="content-full">
        <div class="tieude">
            <h5 class="tblack">HÃY ĐỒNG HÀNH CÙNG NHAU NHÉ</h5>
            <span class="tracking-in-contract-bck-bottom">__________________</span>
            <h2 class="t-content">KHÁM PHÁ NGAY CÁC ĐỊA ĐIỂM
                <span class="text-blue">NỔI TIẾNG</span>
            </h2>
        </div>
        <div class="carousel" id="carousel-container">

        </div>

        <div class="slide">
            <div class="slide-img-backgroud">
                <img src="../public/images/backgroud6.0.jpeg">
            </div>
            <div class="slide-content">
                <h1>ĐẶT TOUR NGAY HÔM NAY</h1>
                <p>Đừng bỏ lỡ cơ hội trải nghiệm những điều thú vị nhất tại Phú Quốc</p>
                <div class="slide-button">
                    <a href="dichvu.html">Đặt Tour Ngay</a>

                </div>

            </div>
        </div>

        <div class="ticket-container">
            <h2 class="t-content">TẬN HƯỞNG THỜI GIAN TUYỆT VỜI KHI ĐẾN VỚI PHÚ QUỐC</h2>
            <div class="tickets" id="tickets-list">
                <!-- Dữ liệu sẽ được render tại đây -->
            </div>
            <div class="see-more-container">
                <a href="#" class="see-more-link">Xem thêm<span class="arrow"></span></a>
            </div>
        </div>
        <div class="ticket-container">
            <h2 class="t-content">ĐA DẠNG LỰA CHỌN VỚI KHÁCH SẠN</h2>
            <div class="tickets" id="tickets-hotels">
                <!-- Dữ liệu sẽ được render tại đây -->
            </div>
            <div class="see-more-container">
                <a href="../php/hotels-list.php" class="see-more-link">
                    Xem thêm
                    <span class="arrow"></span>
                </a>
            </div>
        </div>

        <div class="travel-guide-container">
            <h2>Cẩm nang du lịch</h2>
            <div class="travel-guide-content">

                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="travel-guide-image">
                        <div class="travel-guide-overplay"></div>
                        <img src="<?= $row['image_path'] ?>" title="<?= $row['title'] ?> Image" />
                        <div class="travel-guide-text">
                            <h4><?= $row['title'] ?></h4>
                            <p>
                                <span>
                                    <a class="see-more-travel" href="<?= $row['detail_link'] ?>">
                                        Xem chi tiết
                                    </a>
                                </span>
                            </p>
                        </div>
                    </div>
                <?php endwhile; ?>

            </div>
        </div>

        <!-- tag-top -->
        <div class="tab-container">
            <div class="tab-header">
                <h2>Bạn muốn khám phá điều gì</h2>
            </div>
            <div class="tabs" id="tab-content">
                <div class="tab active" data-tab="hotels">Các khách sạn hàng đầu</div>
                <div class="tab" data-tab="activities">Các điểm tham quan hàng đầu</div>
            </div>

            <div class="tab-body">
                <!-- HOTELS -->
                <div class="body active" id="hotels">
                    <?php if ($resulthotels->num_rows > 0): ?>
                        <?php
                        $count = 0;
                        echo '<ul>';
                        while ($row = $resulthotels->fetch_assoc()):
                            echo '<li><a href="' . $row['link'] . '" class="footer-link">' . $row['name'] . '</a></li>';
                            $count++;
                            if ($count % 5 == 0)
                                echo '</ul><ul>';
                        endwhile;
                        echo '</ul>';
                        ?>
                    <?php endif; ?>
                </div>

                <!-- ACTIVITIES -->
                <div class="body" id="activities">
                    <?php if ($resultactivities->num_rows > 0): ?>
                        <?php
                        $count = 0;
                        echo '<ul>';
                        while ($row = $resultactivities->fetch_assoc()):
                            echo '<li><a href="' . $row['link'] . '" class="footer-link">' . $row['name'] . '</a></li>';
                            $count++;
                            if ($count % 5 == 0)
                                echo '</ul><ul>';
                        endwhile;
                        echo '</ul>';
                        ?>
                    <?php endif; ?>
                </div>
            </div>
            <!-- send-email-user -->
            <div class="send-notification-container">
                <div class="send-notification-image">
                    <img src="../public/images/imgaemail.webp" alt="Đăng ký nhận thông báo">

                </div>
                <div class="send-notification-content">
                    <h2 class="send-content">ĐỪNG BỎ LỠ CƠ HỘI NHẬN THÔNG BÁO MỚI NHẤT</h2>
                    <p>Đăng ký nhận thông báo để nhận thông tin mới nhất về các chương trình khuyến mãi và ưu đãi đặc
                        biệt
                        từ chúng tôi.</p>
                    <form action="#" method="POST" class="send-notification-form">
                        <input class="input-send" type="email" name="email" placeholder="Nhập email của bạn" required>
                        <button class="button-send">
                            <div class="svg-wrapper-1">
                                <div class="svg-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                        <path fill="none" d="M0 0h24v24H0z"></path>
                                        <path fill="currentColor"
                                            d="M1.946 9.315c-.522-.174-.527-.455.01-.634l19.087-6.362c.529-.176.832.12.684.638l-5.454 19.086c-.15.529-.455.547-.679.045L12 14l6-8-8 6-8.054-2.685z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <span>Send</span>
                        </button>
                    </form>
                </div>
            </div>
            <!--Tag lời gợi ý  -->
            <div class="suggest-container">
                <div class="suggest-container-content">
                    <h3>Lý do nên đặt chỗ với TD Touris ?</h3>
                </div>
                <div class="suggest-list">
                    <div class=" suggest-item">
                        <img class="suggest-image" src="../public/images/item-list.webp" alt="public travis" </div>
                        <div class="suggest-body">
                            <h4>Đáp ứng mọi như cầu của bạn</h4>
                            <p>Từ nơi lưu trú và tham quan, bạn có thể tin chọn sản phẩm hoàn chỉnh và Hướng dẫn cụ thể
                                của
                                chúng tôi.</p>
                        </div>
                    </div>
                    <div class=" suggest-item">
                        <img class="suggest-image" src="../public/images/suggest-2.webp" alt="public travis" </div>
                        <div class="suggest-body">
                            <h4>Tùy chọn chỗ linh hoạt </h4>
                            <p>Kế hoạch thay đổi bất ngờ ? Đừng lo !! Đổi lịch hoạt hoàn tiền dễ dàng.</p>
                        </div>
                    </div>
                    <div class=" suggest-item">
                        <img class="suggest-image" src="../public/images/suggest3.webp" alt="public travis" </div>
                        <div class="suggest-body">
                            <h4>Thanh toán an toàn và thuận tiện</h4>
                            <p>Tận hưởng nhiều cách thanh toán an toàn, bằng loại tiền thuận tiện nhất cho bạn.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-grid">
                    <div class="payment-partners">
                        <h4 class="partners-heading">Đối tác thanh toán</h4>
                        <div class="payment-grid">
                            <img src="../public/images//payment/visa-logo-png.png" alt="VISA" class="payment-logo">
                            <img src="../public/images//payment/BIDV-01.png" alt="BIDV" class="payment-logo">
                            <img src="../public/images//payment/logo-MB.png" alt="MB Bank" class="payment-logo">
                            <img src="../public/images//payment/Agribank-logo.png" alt="Agribank" class="payment-logo">
                            <img src="../public/images//payment/MoMo_Logo.png" alt="MoMo" class="payment-logo">
                        </div>
                    </div>
                    <!-- Cột Sản phẩm -->
                    <div class="footer-column">
                        <h4 class="footer-heading">Về TD Touris</h4>
                        <ul class="footer-list">
                            <li><a href="#" class="footer-link">Liên hệ với chúng tôi </a></li>
                            <li><a href="#" class="footer-link">Trợ giúp</a></li>
                            <li><a href="#" class="footer-link">Về chúng tôi</a></li>
                        </ul>
                    </div>
                    <!-- Cột Sản phẩm -->
                    <div class="footer-column">
                        <h4 class="footer-heading">Sản phẩm</h4>
                        <ul class="footer-list">
                            <li><a href="#" class="footer-link">Khách sạn</a></li>
                            <li><a href="#" class="footer-link">Vé du lịch</a></li>
                        </ul>
                    </div>
                    <!-- Cột Sản phẩm -->
                    <div class="footer-column">
                        <h4 class="footer-heading">Khác</h4>
                        <ul class="footer-list">
                            <li><a href="#" class="footer-link">Chính sách và quyền hạn</a></li>
                            <li><a href="#" class="footer-link">Điều kiện và điều khoản</a></li>
                        </ul>
                    </div>
                </div>

                <div class="footer-bottom">
                    <span class="copyright">© 2025 TD Touris. All rights reserved</span>
                </div>
            </div>
        </div>


        </footer>

    </div>


    </div>
    <!-- file scrip -->
    <script src="../js/carousel_sell.js" defer></script>
    <script src="../js/get_hotels.js" defer></script>
    <script src="../js/get_tickets.js" defer></script>
    <script src="../js/index-tab.js"></script>
</body>

</html>
<?php
$conn->close();
?>