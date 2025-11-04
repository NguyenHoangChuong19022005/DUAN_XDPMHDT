<?php 
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'provider') {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Fix line 102: Null check for $provider to avoid undefined key
$stmt = $conn->prepare("SELECT p.id, p.organization FROM providers p JOIN users u ON p.user_id = u.id WHERE u.id = ?");
$stmt->execute([$_SESSION['user_id']]);
$provider = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['organization' => 'N/A']; // Fallback 'N/A' if query fail
$provider_id = $provider['id'] ?? 1; // Fallback ID
$organization = $provider['organization']; // Now safe, show 'N/A' if empty

function getCount($conn, $sql, $params = []) {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn() ?: 0;
}

$total_scholarships = getCount($conn, "SELECT COUNT(*) FROM scholarships WHERE provider_id = ? AND status='active'", [$provider_id]);
$total_applications = getCount($conn, "SELECT COUNT(*) FROM applications a JOIN scholarships s ON a.scholarship_id = s.id WHERE s.provider_id = ?", [$provider_id]);
$pending_apps = getCount($conn, "SELECT COUNT(*) FROM applications a JOIN scholarships s ON a.scholarship_id = s.id WHERE s.provider_id = ? AND a.status='pending'", [$provider_id]);
$approved_apps = getCount($conn, "SELECT COUNT(*) FROM applications a JOIN scholarships s ON a.scholarship_id = s.id WHERE s.provider_id = ? AND a.status='approved'", [$provider_id]);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $organization ?> - Provider Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1f2937;
        }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .topbar { background: linear-gradient(135deg, var(--primary), #1e40af); box-shadow: 0 4px 20px rgba(37, 99, 235, 0.3); }
        .topbar-nav .nav-link { color: rgba(255,255,255,0.9) !important; font-weight: 500; padding: 12px 20px; border-radius: 25px; transition: all 0.3s; }
        .topbar-nav .nav-link:hover, .topbar-nav .nav-link.active { background: rgba(255,255,255,0.2); color: white !important; transform: translateY(-2px); }
        .provider-card { background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-10px); box-shadow: 0 25px 50px rgba(0,0,0,0.2); }
        .stat-card.bg-success { background: linear-gradient(135deg, var(--success), #059669); }
        .stat-card.bg-warning { background: linear-gradient(135deg, var(--warning), #d97706); }
        .stat-card.bg-info { background: linear-gradient(135deg, #0ea5e9, #0284c7); }
        .content-card { background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .table-hover tbody tr:hover { background-color: rgba(37, 99, 235, 0.1); transform: scale(1.01); }
        .btn-modern { border-radius: 25px; padding: 8px 20px; font-weight: 500; transition: all 0.3s; }
        .badge-modern { padding: 6px 12px; border-radius: 20px; font-size: 0.8em; font-weight: 600; }
        .list-group-item { border: none; border-radius: 10px; margin-bottom: 5px; background: rgba(255,255,255,0.8); }
        .list-group-item:hover { background: rgba(255,255,255,1); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="hold-transition layout-fixed">
<div class="wrapper">

    <!-- TOPBAR MENU -->
    <nav class="navbar topbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold fs-4" href="#">
                <i class="fas fa-graduation-cap me-2"></i><?= $organization ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="topbarNav">
                <ul class="navbar-nav me-auto topbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" onclick="loadDashboard()">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="loadScholarships()">
                            <i class="fas fa-graduation-cap me-1"></i>Học bổng
                            <span class="badge bg-danger ms-1"><?= $total_scholarships ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="loadApplications()">
                            <i class="fas fa-file-alt me-1"></i>Đơn ứng tuyển
                            <span class="badge bg-warning ms-1"><?= $pending_apps ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="loadCreateScholarship()">
                            <i class="fas fa-plus-circle me-1"></i>Tạo mới
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle fs-4 me-2"></i><?php echo $_SESSION['user_name']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="content-wrapper pt-5">
        <div class="container-fluid px-4">
            <!-- PROVIDER INFO CARD -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="provider-card p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="fw-bold mb-1"><?= $organization ?></h2>
                                <p class="text-muted mb-2">Provider ID: #<?= $provider_id ?></p>
                                <div class="d-flex gap-3">
                                    <span class="badge bg-success badge-modern"><i class="fas fa-users me-1"></i><?= $total_applications ?> Đơn nhận</span>
                                    <span class="badge bg-primary badge-modern"><i class="fas fa-check-circle me-1"></i><?= $approved_apps ?> Đã duyệt</span>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <button class="btn btn-primary btn-modern" onclick="loadCreateScholarship()">
                                    <i class="fas fa-plus me-2"></i>Tạo học bổng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATS CARDS -->
            <div class="row mb-5">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-center p-4 h-100" onclick="loadScholarships()">
                        <i class="fas fa-graduation-cap fa-3x mb-3 opacity-75"></i>
                        <h3 class="fw-bold mb-1"><?= $total_scholarships ?></h3>
                        <p class="mb-0 opacity-90">Học bổng Active</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-center p-4 h-100 bg-success" onclick="loadApplications()">
                        <i class="fas fa-file-alt fa-3x mb-3 opacity-75"></i>
                        <h3 class="fw-bold mb-1"><?php echo $total_applications; ?></h3>
                        <p class="mb-0 opacity-90">Tổng đơn</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-center p-4 h-100 bg-warning" onclick="loadPendingApps()">
                        <i class="fas fa-clock fa-3x mb-3 opacity-75"></i>
                        <h3 class="fw-bold mb-1"><?php echo $pending_apps; ?></h3>
                        <p class="mb-0 opacity-90">Chờ duyệt</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-center p-4 h-100 bg-info">
                        <i class="fas fa-chart-line fa-3x mb-3 opacity-75"></i>
                        <h3 class="fw-bold mb-1"><?php echo $approved_apps ? round(($approved_apps/$total_applications)*100, 1) : 0; ?>%</h3>
                        <p class="mb-0 opacity-90">Tỷ lệ duyệt</p>
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT -->
            <div id="mainContent">
                <div class="content-card p-5">
                    <div class="row">
                        <div class="col-md-8">
                            <canvas id="overviewChart" height="100"></canvas>
                        </div>
                        <div class="col-md-4">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Tổng học bổng</span>
                                    <span class="badge bg-primary rounded-pill"><?php echo $total_scholarships; ?></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Đơn chờ duyệt</span>
                                    <span class="badge bg-warning rounded-pill"><?php echo $pending_apps; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tạo học bổng -->
<div class="modal fade" id="createScholarshipModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title mb-0"><i class="fas fa-plus-circle me-2"></i>Tạo học bổng mới</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="createScholarshipForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="provider_id" value="<?= $provider_id ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600">Tiêu đề học bổng <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-lg" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Ngành học <span class="text-danger">*</span></label>
                            <select name="major" class="form-select form-select-lg" required>
                                <option value="">Chọn ngành...</option>
                                <option value="CNTT">Công nghệ thông tin</option>
                                <option value="Kinh tế">Kinh tế - Quản trị</option>
                                <option value="Kỹ thuật">Kỹ thuật - Xây dựng</option>
                                <option value="Y khoa">Y khoa - Dược</option>
                                <option value="Luật">Luật</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Quốc gia <span class="text-danger">*</span></label>
                            <select name="country" class="form-select form-select-lg" required>
                                <option value="">Chọn quốc gia...</option>
                                <option value="USA">🇺🇸 USA</option>
                                <option value="UK">🇬🇧 UK</option>
                                <option value="Canada">🇨🇦 Canada</option>
                                <option value="Australia">🇦🇺 Australia</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">GPA tối thiểu <span class="text-danger">*</span></label>
                            <input type="number" name="gpa_min" step="0.01" min="0" max="4" class="form-control form-control-lg" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600">Hạn nộp <span class="text-danger">*</span></label>
                            <input type="date" name="deadline" class="form-control form-control-lg" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600">Mô tả</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Mô tả chi tiết về học bổng..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-save me-2"></i>Tạo học bổng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const providerId = <?= $provider_id ?>;

function loadDashboard() {
    $('#pageTitle').text('Dashboard');
    $('#mainContent').load('controllers/provider_controller.php?action=dashboard');
}

function loadScholarships() {
    $('#pageTitle').text('Danh sách học bổng');
    $('#mainContent').load('controllers/provider_controller.php?action=scholarships');
}

function loadApplications() {
    $('#pageTitle').text('Danh sách đơn');
    $('#mainContent').load('controllers/provider_controller.php?action=applications');
}

function loadPendingApps() {
    $('#pageTitle').text('Đơn chờ duyệt');
    $('#mainContent').load('controllers/provider_controller.php?action=pending');
}

function loadCreateScholarship() {
    $('#pageTitle').text('Tạo học bổng mới');
    $('#createScholarshipModal').modal('show');
}

// Form submit
$('#createScholarshipForm').submit(function(e) {
    e.preventDefault();
    $.post('controllers/provider_controller.php?action=create', $(this).serialize(), function(response) {
        if (response.success) {
            alert('✅ Tạo học bổng thành công!');
            $('#createScholarshipModal').modal('hide');
            location.reload(); // Refresh stats
        } else {
            alert('❌ Lỗi: ' + response.error);
        }
    }, 'json');
});

$(document).ready(function() {
    // Active nav link
    $('.topbar-nav .nav-link').click(function() {
        $('.topbar-nav .nav-link').removeClass('active');
        $(this).addClass('active');
    });
});
</script>
</body>
</html>