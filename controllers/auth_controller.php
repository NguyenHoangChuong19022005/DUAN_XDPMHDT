<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $user;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->user = new User($db);
    }

    public function login($email, $password) {
        $user = $this->user->login($email, $password);
        if($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            return ['success' => true, 'role' => $user['role']];
        }
        return ['success' => false, 'message' => 'Email hoặc mật khẩu không đúng!'];
    }

    public function register($data) {
        if($this->user->create($data)) {
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Đăng ký thất bại!'];
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function logout() {
        session_destroy();
        return true;
    }
}
?>