<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "do-an-1";
define('GOOGLE_CLIENT_ID', '261152376192-3533f1q0im59ab72tctvlgc775vj206g.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'http://localhost/doan1/src/php/login.php');


define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'haidanglu2004@gmail.com');
define('SMTP_PASSWORD', 'ejsohikzonetkxei');
define('SMTP_PORT', 587);
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
