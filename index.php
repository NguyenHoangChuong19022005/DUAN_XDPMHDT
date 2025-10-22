<?php 
session_start(); 
require_once 'config/database.php';
require_once 'models/Scholarship.php';

$database = new Database();
$db = $database->getConnection();
$scholarship = new Scholarship($db);
$featured = $scholarship->getAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduMatch - Kết nối học bổng thông minh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --purple: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }
        
        * { font-family: 'Poppins', sans-serif; }
        body { 
            background: linear-gradient(135deg, #0f0f23 0%, #2d1b69 50%, #1a1a2e 100%); 
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* HERO SECTION */
        .hero-section {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.95) 0%, rgba(118, 75, 162, 0.95) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="300" cy="200" r="100" fill="url(%23a)"><animate attributeName="r" values="100;150;100" dur="8s" repeatCount="indefinite"/></circle><circle cx="700" cy="800" r="80" fill="url(%23a)"><animate attributeName="r" values="80;120;80" dur="10s" repeatCount="indefinite"/></circle></svg>');
            animation: float 20s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .hero-content h1 {
            font-size: clamp(2.5rem, 5vw, 5rem);
            font-weight: 800;
            background: linear-gradient(45deg, #fff, #f0f8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: glow 3s ease-in-out infinite alternate;
        }
        @keyframes glow {
            from { text-shadow: 0 0 20px #fff, 0 0 30px #fff, 0 0 40px rgba(255,255,255,0.5); }
            to { text-shadow: 0 0 20px #667eea, 0 0 30px #764ba2, 0 0 50px rgba(118,75,162,0.5); }
        }
        
        .btn-hero {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255,255,255,0.3);
            padding: 18px 40px;
            font-size: 1.2rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .btn-hero:hover {
            transform: translateY(-8px) scale(1.05);
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            border-color: rgba(255,255,255,0.6);
        }
        
        /* SCHOLARSHIP CARDS */
        .scholarship-section { padding: 120px 0; position: relative; }
        .scholarship-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        }
        
        .scholarship-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 25px;
            overflow: hidden;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            height: 100%;
        }
        .scholarship-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--secondary);
            transform: scaleX(0);
            transition: transform 0.5s ease;
        }
        .scholarship-card:hover {
            transform: translateY(-20px) rotateX(5deg);
            box-shadow: 0 40px 80px rgba(0,0,0,0.3);
            border-color: rgba(255,255,255,0.4);
        }
        .scholarship-card:hover::before { transform: scaleX(1); }
        
        .card-img-top {
            height: 250px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .scholarship-card:hover .card-img-top { transform: scale(1.1); }
        
        .card-title {
            color: #fff;
            font-weight: 700;
            font-size: 1.3rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .badge-scholarship {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: 600;
            color: #fff;
            transition: all 0.3s ease;
        }
        .badge-scholarship:hover { background: rgba(255,255,255,0.3); }
        
        .btn-scholarship {
            background: var(--success);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.4s ease;
            box-shadow: 0 10px 30px rgba(79, 172, 254, 0.4);
        }
        .btn-scholarship:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(79, 172, 254, 0.6);
        }
        
        /* STATS */
        .stats-section {
            background: rgba(0,0,0,0.3);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 60px 0;
            margin: 80px 0;
            position: relative;
        }
        .stats-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: 50%;
            transform: translateX(-50%);
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
        }
        .stat-item {
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .stat-number {
            font-size: clamp(2.5rem, 8vw, 5rem);
            font-weight: 800;
            background: linear-gradient(45deg, #fff, #f0f8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            .hero-content h1 { font-size: 2.5rem !important; }
            .scholarship-section { padding: 80px 0 !important; }
        }
        
        /* FLOATING ELEMENTS */
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        .floating-dot {
            position: absolute;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 15s infinite linear;
        }
        .floating-dot:nth-child(1) { width: 80px; height: 80px; top: 20%; left: 10%; animation-delay: 0s; }
        .floating-dot:nth-child(2) { width: 50px; height: 50px; top: 60%; right: 20%; animation-delay: 3s; }
        .floating-dot:nth-child(3) { width: 120px; height: 120px; bottom: 20%; left: 20%; animation-delay: 6s; }
    </style>
</head>
<body>
    <!-- HERO SECTION -->
    <section class="hero-section position-relative">
        <div class="floating-elements">
            <div class="floating-dot"></div>
            <div class="floating-dot"></div>
            <div class="floating-dot"></div>
        </div>
        <div class="container position-relative z-index-1">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center hero-content animate__animated animate__fadeInUp">
                    <h1 class="display-2 fw-bold mb-5">Chào mừng đến với <span class="text-warning fw-bold">EduMatch</span></h1>
                    <p class="lead fs-3 mb-5 opacity-90">🔥 Kết nối <strong>sinh viên xuất sắc</strong> với <strong>học bổng danh giá</strong> từ các trường top thế giới</p>
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-4">
                        <?php if(!isset($_SESSION['user_id'])): ?>
                            <a href="login.php" class="btn btn-hero btn-lg shadow-lg">
                                <i class="fas fa-rocket me-2"></i>🚀 Đăng nhập ngay
                            </a>
                            <a href="register.php" class="btn btn-outline-light btn-lg btn-hero shadow-lg">
                                <i class="fas fa-user-plus me-2"></i>✨ Đăng ký miễn phí
                            </a>
                        <?php else: ?>
                            <?php 
                            $dashboard = "dashboard_" . $_SESSION['user_role'] . ".php";
                            $role_text = $_SESSION['user_role'] == 'student' ? 'Dashboard Sinh viên' : 
                                       ($_SESSION['user_role'] == 'provider' ? 'Dashboard Nhà cung cấp' : 'Admin Panel');
                            ?>
                            <a href="<?php echo $dashboard; ?>" class="btn btn-warning btn-lg btn-hero shadow-lg animate__animated animate__pulse animate__infinite">
                                <i class="fas fa-tachometer-alt me-2"></i>⚡ <?php echo $role_text; ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STATS SECTION -->
    <section class="stats-section mx-auto">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <h5 class="text-white-50">Học bổng</h5>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">10K+</div>
                        <h5 class="text-white-50">Sinh viên</h5>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">250+</div>
                        <h5 class="text-white-50">Trường ĐH</h5>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">95%</div>
                        <h5 class="text-white-50">Tỷ lệ match</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SCHOLARSHIP SECTION -->
    <section class="scholarship-section">
        <div class="container">
            <div class="text-center mb-5 animate__animated animate__fadeInUp">
                <h2 class="display-4 fw-bold mb-4" style="background: linear-gradient(45deg, #fff, #f0f8ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    🎓 <i class="fas fa-star text-warning"></i> Học bổng HOT nhất
                </h2>
                <p class="lead text-white-50 fs-4">Cập nhật realtime từ 250+ trường đại học danh tiếng</p>
            </div>
            
            <div class="row g-4">
                <?php foreach(array_slice($featured, 0, 6) as $index => $sch): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="scholarship-card animate__animated animate__fadeInUp" style="animation-delay: <?= $index * 0.1 ?>s">
                        <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=500&h=250&fit=crop&crop=center" 
                             class="card-img-top" alt="<?= htmlspecialchars($sch['title']) ?>" 
                             onerror="this.src='https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=500&h=250&fit=crop'">
                        <div class="card-body p-4">
                            <h5 class="card-title"><?= htmlspecialchars($sch['title']) ?></h5>
                            <p class="card-text text-white-50" style="font-size: 0.95rem; line-height: 1.6;">
                                <?= substr(htmlspecialchars($sch['description'] ?? 'Học bổng danh giá dành cho sinh viên xuất sắc...'), 0, 100) ?>...
                            </p>
                            <div class="row text-center mb-4">
                                <div class="col-6">
                                    <span class="badge badge-scholarship">
                                        <i class="fas fa-star me-1"></i><?= $sch['gpa_min'] ?> GPA
                                    </span>
                                </div>
                                <div class="col-6">
                                    <span class="badge badge-scholarship">
                                        <i class="fas fa-globe me-1"></i><?= strtoupper($sch['country']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <small class="text-white-50 fw-bold">
                                    <?= date('d/m/Y', strtotime($sch['deadline'])) ?>
                                </small>
                            </div>
                            <small class="text-white d-block mb-3 fw-semibold">
                                <i class="fas fa-building text-info me-1"></i>
                                <?= htmlspecialchars($sch['organization']) ?>
                            </small>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-4">
                            <?php if(isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'student'): ?>
                                <a href="#" class="btn btn-scholarship w-100 text-white fw-bold shadow-lg" 
                                   onclick="applyScholarship(<?= $sch['id'] ?>, '<?= addslashes($sch['title']) ?>')">
                                    <i class="fas fa-paper-plane me-2"></i>🚀 Nộp đơn NGAY
                                </a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-outline-light w-100 fw-bold">
                                    <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập để nộp đơn
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-5">
                <a href="login.php" class="btn btn-hero btn-lg px-5" style="background: var(--warning); color: #000;">
                    <i class="fas fa-search me-2"></i>Xem tất cả học bổng
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="fw-bold mb-3"><i class="fas fa-graduation-cap text-warning me-2"></i>EduMatch</h4>
                    <p class="opacity-75">Kết nối tương lai - Học bổng thông minh cho mọi sinh viên Việt Nam</p>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-6">
                            <h6 class="fw-bold mb-3">Sản phẩm</h6>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-white-50">Sinh viên</a></li>
                                <li><a href="#" class="text-white-50">Trường ĐH</a></li>
                                <li><a href="#" class="text-white-50">Admin</a></li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <h6 class="fw-bold mb-3">Liên hệ</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-envelope me-2"></i>hello@edumatch.vn</li>
                                <li><i class="fas fa-phone me-2"></i>+84 123 456 789</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-4 opacity-25">
            <p class="mb-0 opacity-75">&copy; 2025 EduMatch. Tất cả quyền được bảo lưu. <i class="fas fa-heart text-danger"></i></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
    function applyScholarship(id, title) {
        if(confirm(`Bạn có muốn nộp đơn cho học bổng "${title}"?`)) {
            alert('🎉 Đã chuyển đến dashboard để nộp đơn!');
            window.location.href = 'login.php';
        }
    }
    
    // Smooth scroll & animations
    window.addEventListener('scroll', () => {
        document.querySelectorAll('.scholarship-card').forEach((card, index) => {
            const rect = card.getBoundingClientRect();
            if(rect.top < window.innerHeight && rect.bottom > 0) {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }
        });
    });
    </script>
</body>
</html>