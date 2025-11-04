<?php 
session_start();
if(isset($_SESSION['user_id'])) {
    $dashboard = "dashboard_" . $_SESSION['user_role'] . ".php";
    header("Location: " . $dashboard);
    exit();
}

require_once 'controllers/auth_controller.php';
$auth = new AuthController();
$error = '';

// Debug: Bật error reporting (xóa khi production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Debug: In POST data (uncomment nếu cần test, xóa sau)
    // var_dump($_POST); exit();

    $result = $auth->login($_POST['email'], $_POST['password']);
    if($result['success']) {
        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['user_role'] = $result['role'];
        $dashboard = "dashboard_" . $result['role'] . ".php";
        header("Location: " . $dashboard);
        exit();
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - EduMatch</title>
    <!-- Font Awesome cho icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/all.min.css">
    <!-- Link CSS từ assets -->
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="header-section">
                <i class="fas fa-graduation-cap header-icon"></i>
                <h2 class="page-title">Đăng Nhập</h2>
                <p class="subtitle">Vào EduMatch ngay!</p>
            </div>
            
            <!-- Error Alert -->
            <?php if($error): ?>
                <div class="error-alert">
                    <i class="fas fa-exclamation-triangle icon"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" id="login-form">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" id="email" class="form-input" placeholder="student@test.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Mật khẩu</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="password" class="form-input" placeholder="password" required>
                        <button type="button" id="toggle-password" class="toggle-btn">
                            <i class="fas fa-eye" id="toggle-icon"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="submit-btn">
                    <i class="fas fa-sign-in-alt icon"></i>
                    Đăng Nhập
                </button>
            </form>

            <!-- Demo Accounts -->
            <div class="demo-section">
                <hr class="divider">
                <h6 class="demo-title">🧪 Demo Accounts</h6>
                <div class="demo-grid">
                    <div class="demo-card blue">
                        <small class="demo-label">Student</small>
                        <p class="demo-info">student@test.com / password</p>
                    </div>
                    <div class="demo-card green">
                        <small class="demo-label">Provider</small>
                        <p class="demo-info">provider@test.com / password</p>
                    </div>
                    <div class="demo-card red">
                        <small class="demo-label">Admin</small>
                        <p class="demo-info">admin@test.com / password</p>
                    </div>
                </div>
                <hr class="divider">
                <p class="signup-link">Chưa có tài khoản? <a href="register.php" class="link">Đăng ký ngay</a></p>
            </div>
        </div>
    </div>

    <!-- Link JS từ assets -->
    <script src="assets/js/login.js"></script>
</body>
</html>