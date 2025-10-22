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

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $auth->login($_POST['email'], $_POST['password']);
    if($result['success']) {
        $dashboard = "dashboard_" . $result['role'] . ".php";
        header("Location: " . $dashboard);
        exit();
    } else {
        $error = $result['message'];
    }
}
?>
<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-xl-4 col-lg-5 col-md-7">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <i class="fas fa-graduation-cap fa-4x text-primary mb-3"></i>
                        <h2 class="fw-bold text-primary">Đăng nhập</h2>
                        <p class="text-muted">Vào EduMatch ngay!</p>
                    </div>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control form-control-lg border-0 shadow-sm" 
                                       placeholder="student@test.com" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Mật khẩu</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" class="form-control form-control-lg border-0 shadow-sm" 
                                       placeholder="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold py-3">
                            <i class="fas fa-sign-in-alt me-2"></i> Đăng nhập
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <hr class="my-4">
                        <h6 class="text-muted mb-3">🧪 Demo Accounts</h6>
                        <div class="row text-start">
                            <div class="col-6">
                                <small class="d-block text-primary fw-bold mb-1">student@test.com</small>
                                <small class="d-block text-success fw-bold mb-1">provider@test.com</small>
                            </div>
                            <div class="col-6">
                                <small class="d-block text-danger fw-bold">admin@test.com</small>
                                <div class="text-muted small">Password: <strong>password</strong></div>
                            </div>
                        </div>
                        <hr class="my-4">
                        <p>Chưa có tài khoản? <a href="register.php" class="text-primary fw-bold">Đăng ký ngay</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>