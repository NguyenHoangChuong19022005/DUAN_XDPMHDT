<?php 
session_start();
if(isset($_SESSION['user_id'])) {
    $dashboard = "dashboard_" . $_SESSION['user_role'] . ".php";
    header("Location: " . $dashboard);
    exit();
}

require_once 'controllers/auth_controller.php';
$auth = new AuthController();
$error = $success = '';

// Debug: Bật error reporting (xóa khi production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $name = trim($_POST['name'] ?? ''); // Optional name

    $result = $auth->register($email, $password, $role, $name);
    if($result['success']) {
        $success = $result['message'];
        // Optional: Auto login sau register
        // $_SESSION['user_id'] = $result['user_id']; // Nếu controller return user_id
        // header("Location: dashboard_" . $role . ".php");
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
    <title>Đăng Ký - EduMatch</title>
    <!-- Font Awesome cho icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/all.min.css">
    <!-- Link CSS từ assets -->
    <link rel="stylesheet" href="assets/css/register.css">
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <!-- Header -->
            <div class="header-section">
                <i class="fas fa-user-plus header-icon"></i>
                <h2 class="page-title">Đăng Ký</h2>
                <p class="subtitle">Tạo tài khoản để bắt đầu</p>
            </div>
            
            <!-- Error/Success Alert -->
            <?php if($error): ?>
                <div class="error-alert">
                    <i class="fas fa-exclamation-triangle icon"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="success-alert">
                    <i class="fas fa-check-circle icon"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" id="register-form">
                <div class="form-group">
                    <label class="form-label">Tên (Tùy Chọn)</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="name" id="name" class="form-input" placeholder="Tên đầy đủ">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" id="email" class="form-input" placeholder="student@test.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Mật khẩu *</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="password" class="form-input" placeholder="Ít nhất 6 ký tự" required>
                        <button type="button" id="toggle-password" class="toggle-btn">
                            <i class="fas fa-eye" id="toggle-icon"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Vai Trò *</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user-tag input-icon"></i>
                        <select name="role" id="role" class="form-select" required>
                            <option value="">Chọn vai trò</option>
                            <option value="student">Sinh Viên</option>
                            <option value="provider">Nhà Cung Cấp</option>
                            <!-- Không thêm admin để tránh lạm dụng -->
                        </select>
                    </div>
                </div>
                <button type="submit" class="submit-btn">
                    <i class="fas fa-user-plus icon"></i>
                    Đăng Ký
                </button>
            </form>

            <!-- Link Back to Login -->
            <div class="text-center mt-6">
                <p class="signup-link">Đã có tài khoản? <a href="login.php" class="link">Đăng nhập ngay</a></p>
            </div>
        </div>
    </div>

    <!-- Link JS từ assets -->
    <script src="assets/js/register.js"></script>
</body>
</html>