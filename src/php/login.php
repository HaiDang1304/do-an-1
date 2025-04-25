<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Google\Client as Google_Client;
use Google\Service\Oauth2 as Google_Service_Oauth2;


if (isset($_SESSION['user']) && !isset($_GET['logout'])) {
    header("Location: index.php");
    exit();
}

$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
$client->addScope('email');
$client->addScope('profile');

// Tạo CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Xử lý đăng nhập Google
if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        if (!isset($token['error']) && $client->getAccessToken()) {
            $client->setAccessToken($token['access_token']);
            $google_service = new Google_Service_Oauth2($client);
            $user_info = $google_service->userinfo->get();

            $email = filter_var($user_info->email, FILTER_SANITIZE_EMAIL);
            $username = htmlspecialchars($user_info->name, ENT_QUOTES, 'UTF-8');

            // Kiểm tra người dùng
            $stmt = $conn->prepare("SELECT id, email, username, verified FROM users WHERE email = ?");
            if ($stmt === false) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
            }
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            // Nếu người dùng không tồn tại, tạo mới với verified = 1
            if (!$user) {
                $random_password = bin2hex(random_bytes(16));
                $hashed_password = password_hash($random_password, PASSWORD_DEFAULT);
                $verified = 1;
                $stmt = $conn->prepare("INSERT INTO users (email, username, password, login_type, verified) VALUES (?, ?, ?, 'google', ?)");
                if ($stmt === false) {
                    throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
                }
                $stmt->bind_param("sssi", $email, $username, $hashed_password, $verified);
                $stmt->execute();
            } else {
                // Kiểm tra verified
                if ($user['verified'] == 0) {
                    $error = "Tài khoản chưa được xác minh. Vui lòng kiểm tra email để xác nhận tài khoản!";
                    $stmt->close();
                    goto render_page;
                }
            }

            // Lưu thông tin người dùng vào session
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => $user ? $user['id'] : $conn->insert_id,
                'email' => $email,
                'username' => $username
            ];

            header("Location: index.php");
            exit();
        } else {
            $error = "Đăng nhập Google thất bại: " . ($token['error_description'] ?? 'Lỗi không xác định');
            error_log($error);
        }
    } catch (Exception $e) {
        $error = "Lỗi đăng nhập Google: " . $e->getMessage();
        error_log($error);
    }
}

// Xử lý đăng nhập email/mật khẩu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['password'], $_POST['csrf_token'])) {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "CSRF token không hợp lệ.";
    } else {
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'];

        if (!$email) {
            $error = "Email không hợp lệ.";
        } elseif (strlen($password) < 8) {
            $error = "Mật khẩu phải dài ít nhất 8 ký tự.";
        } else {
            try {
                $stmt = $conn->prepare("SELECT id, email, username, password, login_type, verified FROM users WHERE email = ?");
                if ($stmt === false) {
                    throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
                }
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();

                if ($user && $user['login_type'] === 'email') {
                    if ($user['verified'] == 0) {
                        $error = "Tài khoản chưa được xác minh. Vui lòng kiểm tra email để xác nhận tài khoản!";
                    } elseif (password_verify($password, $user['password'])) {
                        session_regenerate_id(true);
                        $_SESSION['user'] = [
                            'id' => $user['id'],
                            'email' => $user['email'],
                            'username' => $user['username']
                        ];
                        error_log("Đăng nhập email thành công: " . print_r($_SESSION['user'], true));
                        header("Location: index.php");
                        exit();
                    } else {
                        $error = "Sai mật khẩu.";
                    }
                } else {
                    $error = "Email không tồn tại hoặc tài khoản không phải tài khoản email.";
                }
            } catch (Exception $e) {
                $error = "Lỗi đăng nhập: " . $e->getMessage();
                error_log($error);
            }
        }
    }
}

// Xử lý đăng xuất
if (isset($_GET['logout'])) {
    unset($_SESSION['user']);
    session_regenerate_id(true);
    header("Location: login.php");
    exit();
}

// Tạo URL đăng nhập Google
$google_login_url = $client->createAuthUrl();

// Nhãn để nhảy đến phần render giao diện
render_page:
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TD Touris - Đăng Nhập</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- File CSS tùy chỉnh -->
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="background-login position-relative min-vh-100 d-flex justify-content-center align-items-center">
        <img src="../public/images/backgroudlogin.png" alt="background-login" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover">
        <div class="login-form bg-white p-4 rounded shadow w-100" style="max-width: 400px;">
            <h1 class="text-center mb-4">Đăng Nhập</h1>
            <?php if (isset($error)): ?>
                <p class="error-message text-danger text-center mb-3"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <form method="POST" action="login.php">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="txtb mb-3">
                    <input id="email" type="email" name="email" class="form-control" required placeholder="Nhập email" autocomplete="email">
                </div>
                <div class="txtb mb-3">
                    <input id="password" type="password" name="password" class="form-control" required placeholder="Nhập mật khẩu" autocomplete="current-password">
                </div>
                <input type="submit" id="logbtn" class="logbtn btn btn-primary w-100" value="Đăng Nhập">
                <div class="bottom-text text-center mt-3">
                    Bạn chưa có tài khoản? <a href="register.php" class="text-primary text-decoration-none">Đăng Ký</a>
                </div>
            </form>

            <div class="social-login mt-4">
                <div class="social-divider d-flex align-items-center mb-3">
                    <hr class="flex-grow-1 border-secondary">
                    <span class="divider-text mx-2 text-muted">Hoặc đăng nhập bằng</span>
                    <hr class="flex-grow-1 border-secondary">
                </div>
                <div class="social-buttons text-center">
                    <a href="<?php echo htmlspecialchars($google_login_url, ENT_QUOTES, 'UTF-8'); ?>" class="google-login btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2 mb-2">
                        <svg viewBox="0 0 24 24" class="w-5 h-5" style="width: 20px; height: 20px;">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.20-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.60 3.30-4.53 6.16-4.53z" fill="#EA4335"></path>
                            <path d="M1 1h22v22H1z" fill="none"></path>
                        </svg>
                        Đăng nhập với Google
                    </a>
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="login.php?logout=true" class="logout text-danger text-decoration-none d-inline-block mt-2">Đăng Xuất</a>
                    <?php endif; ?>
                </div>
                <?php if (isset($_SESSION['user'])): ?>
                    <p id="user-info" class="text-center mt-3">Xin chào, <?php echo htmlspecialchars($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8') . " (" . htmlspecialchars($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8') . ")"; ?></p>
                <?php else: ?>
                    <p id="user-info" class="text-center mt-3"></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (tùy chọn, chỉ cần nếu dùng các thành phần tương tác như modal) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>