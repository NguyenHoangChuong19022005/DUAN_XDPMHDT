// script-login.js: JS cho trang login - toggle password, client validation, smooth interactions
document.addEventListener('DOMContentLoaded', function() {
    // Toggle Password Event
    const toggleBtn = document.getElementById('toggle-password');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', togglePassword);
    }

    // Form Submit Client Validation
    const form = document.getElementById('login-form');
    if (form) {
        form.addEventListener('submit', validateForm);
    }

    // Smooth Animation Khi Load (Optional)
    document.body.classList.add('loaded');
});

// Toggle Password Function (Eye Icon)
function togglePassword() {
    const password = document.getElementById('password');
    const icon = document.getElementById('toggle-icon');
    if (password && icon) {
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}

// Client-Side Form Validation (Trước Submit Đến PHP)
function validateForm(e) {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    
    if (!email || !password) {
        e.preventDefault();
        alert('Vui lòng nhập email và mật khẩu!');
        return false;
    }
    
    if (!email.includes('@') || !email.includes('.')) {
        e.preventDefault();
        alert('Email không hợp lệ!');
        document.getElementById('email').focus();
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('Mật khẩu phải ít nhất 6 ký tự!');
        document.getElementById('password').focus();
        return false;
    }
    
    // Nếu OK, cho phép submit đến PHP (AuthController/DB xử lý)
    return true;
}

// Smooth Scroll hoặc Additional Effects (Optional - Thêm Sau)
window.addEventListener('load', function() {
    // Shimmer effect cho loading nếu cần (CSS hỗ trợ)
    document.body.classList.add('loaded');
});

// Debug: Log nếu có error JS (Console Browser)
window.addEventListener('error', function(e) {
    console.error('JS Error:', e.error);
});