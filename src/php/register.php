<?php
session_start();

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

// Database connection and SMTP config
include('config.php');

if (isset($_GET['refresh_captcha'])) {
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $generatedCaptcha = "";
    for ($i = 0; $i < 6; $i++) {
        $generatedCaptcha .= $chars[rand(0, strlen($chars) - 1)];
    }
    $_SESSION["captcha"] = $generatedCaptcha;
    echo $generatedCaptcha;
    exit;
}

// Handle form submission
$errors = [];
$success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
    $captchaInput = $_POST["captchaInput"];
    $generatedCaptcha = isset($_SESSION["captcha"]) ? $_SESSION["captcha"] : "";

    // Validate inputs
    if (empty($username)) {
        $errors[] = "Vui lòng nhập tên người dùng!";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Định dạng email không hợp lệ!";
    }
    if (strlen($password) < 8) {
        $errors[] = "Mật khẩu phải có ít nhất 8 ký tự!";
    }
    if ($password !== $confirmPassword) {
        $errors[] = "Mật khẩu không khớp!";
    }
    if ($captchaInput !== $generatedCaptcha) {
        $errors[] = "CAPTCHA không chính xác!";
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if ($stmt === false) {
        die("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = "Email đã được đăng ký!";
    }
    $stmt->close();

    // If no errors, proceed to register
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $loginType = "email";
        $verificationToken = bin2hex(random_bytes(16));
        $isVerified = 0; // Tài khoản chưa xác minh

        // Insert user into database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, login_type, verification_token, verified, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        if ($stmt === false) {
            die("Lỗi chuẩn bị truy vấn: " . $conn->error);
        }
        $stmt->bind_param("sssssi", $username, $email, $hashedPassword, $loginType, $verificationToken, $isVerified);
        if ($stmt->execute()) {
            // Send verification email
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USERNAME;
                $mail->Password = SMTP_PASSWORD;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = SMTP_PORT;

                // Recipients
                $mail->setFrom(SMTP_USERNAME, 'TD Touris');
                $mail->addAddress($email);

                // Content
                $verificationLink = "http://localhost/doan1/src/php/verify.php?token=" . $verificationToken;
                $loginLink = "http://localhost/doan1/src/php/login.php";
                $mail->isHTML(true);
                $mail->Subject = 'Xac nhan tai khoan - TD Touris';
                $mail->Body = "Chào bạn,<br><br>Cảm ơn bạn đã đăng ký tại TD Touris. Vui lòng nhấp vào liên kết sau để xác nhận tài khoản của bạn:<br><a href='$verificationLink'>Xác nhận email</a><br><br>Sau khi xác nhận, bạn có thể <a href='$loginLink'>đăng nhập</a> để bắt đầu sử dụng dịch vụ.<br><br>Nếu bạn không đăng ký tài khoản này, vui lòng bỏ qua email này.<br><br>Trân trọng,<br>Đội ngũ TD Touris";
                $mail->AltBody = "Chào bạn,\n\nCảm ơn bạn đã đăng ký tại TD Touris. Vui lòng sao chép liên kết sau và dán vào trình duyệt để xác nhận tài khoản của bạn:\n$verificationLink\n\nSau khi xác nhận, bạn có thể truy cập liên kết sau để đăng nhập:\n$loginLink\n\nNếu bạn không đăng ký tài khoản này, vui lòng bỏ qua email này.\n\nTrân trọng,\nĐội ngũ TD Touris";

                $mail->send();
                $success = "Đăng ký thành công! Vui lòng kiểm tra email để xác nhận tài khoản. <a href='login.php'>Quay lại đăng nhập</a>";
            } catch (Exception $e) {
                $errors[] = "Lỗi gửi email xác nhận: " . $mail->ErrorInfo;
            }
        } else {
            $errors[] = "Lỗi khi đăng ký: " . $conn->error;
        }
        $stmt->close();
    }
}

// Generate CAPTCHA if not set
if (!isset($_SESSION["captcha"]) || empty($_SESSION["captcha"])) {
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $generatedCaptcha = "";
    for ($i = 0; $i < 6; $i++) {
        $generatedCaptcha .= $chars[rand(0, strlen($chars) - 1)];
    }
    $_SESSION["captcha"] = $generatedCaptcha;
} else {
    $generatedCaptcha = $_SESSION["captcha"];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TD Touris - Đăng Ký</title>
    <link rel="stylesheet" href="../css/register.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background: url('../public/images/backgroudlogin.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-form {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }

        .login-form h1 {
            font-size: 1.75rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1.5rem;
            color: #1e3a8a;
        }

        .captcha-display {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .captcha-container .row {
            align-items: center;
        }
    </style>
    <script>
        // CAPTCHA regeneration
        function refreshCaptcha() {
            fetch('register.php?refresh_captcha=1')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Lỗi mạng khi làm mới CAPTCHA');
                    }
                    return response.text();
                })
                .then(data => {
                    if (data) {
                        document.getElementById("captchaDisplay").textContent = data;
                    } else {
                        console.error('Không nhận được dữ liệu CAPTCHA');
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi làm mới CAPTCHA:', error);
                    document.getElementById("captchaDisplay").textContent = 'Lỗi tải CAPTCHA';
                });
        }

        // Generate CAPTCHA on page load
        window.onload = function() {
            const captchaText = "<?php echo htmlspecialchars($generatedCaptcha); ?>";
            if (captchaText) {
                document.getElementById("captchaDisplay").textContent = captchaText;
            } else {
                refreshCaptcha();
            }
        };
    </script>
</head>
<body>
    <div class="login-form">
        <h1 class="text-center mb-4">Đăng Ký</h1>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <?php foreach ($errors as $error): ?>
                    <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success" role="alert">
                <p class="mb-0"><?php echo $success; ?></p>
            </div>
        <?php else: ?>
            <form id="registerForm" method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Tên người dùng</label>
                    <input type="text" class="form-control" id="username" name="username" required placeholder="Nhập tên người dùng" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="Nhập email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Nhập mật khẩu">
                    <small class="form-text text-muted">Mật khẩu phải có ít nhất 8 ký tự.</small>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required placeholder="Nhập lại mật khẩu">
                </div>
                <div class="mb-3 captcha-container">
                    <label for="captchaInput" class="form-label">Nhập CAPTCHA</label>
                    <div class="captcha-display" id="captchaDisplay"><?php echo htmlspecialchars($generatedCaptcha); ?></div>
                    <div class="row">
                        <div class="col-8">
                            <input type="text" class="form-control" id="captchaInput" name="captchaInput" required placeholder="Nhập CAPTCHA">
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-secondary w-100" onclick="refreshCaptcha()">Tạo lại</button>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Đăng Ký</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>