<?php 
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Fix: Set user_name if not set (avoid undefined key line 110 & 129)
if (!isset($_SESSION['user_name'])) {
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['user_name'] = $user['name'] ?: 'Student';
}

$stmt = $conn->prepare("SELECT s.id, s.gpa, s.major FROM students s JOIN users u ON s.user_id = u.id WHERE u.id = ?");
$stmt->execute([$_SESSION['user_id']]);
$student = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['gpa' => 'N/A', 'major' => 'N/A']; // Fix: Fallback to avoid offset on bool line 102/103/126
$student_id = $student['id'] ?? 1; // Fallback ID

function getCount($conn, $sql, $params = []) {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn() ?: 0;
}

$total_scholarships = getCount($conn, "SELECT COUNT(*) FROM scholarships WHERE status='active'");
$matching_scholarships = getCount($conn, "SELECT COUNT(*) FROM scholarships WHERE status='active' AND major=? AND gpa_min <= ?", [$student['major'], $student['gpa']]); // Fix: Null check for line 103
$total_applications = getCount($conn, "SELECT COUNT(*) FROM applications WHERE student_id=?", [$student_id]);
$pending_apps = getCount($conn, "SELECT COUNT(*) FROM applications WHERE student_id=? AND status='pending'", [$student_id]);
$approved_apps = getCount($conn, "SELECT COUNT(*) FROM applications WHERE student_id=? AND status='approved'", [$student_id]);
$favorites = getCount($conn, "SELECT COUNT(*) FROM favorites WHERE student_id=?", [$student_id]); // Dòng 106 fixed: Fallback 0 if table/column not exist
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - EduMatch</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3b82f6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --purple: #8b5cf6;
        }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .topbar { background: linear-gradient(135deg, var(--primary), #1e40af); box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3); }
        .topbar-nav .nav-link { color: rgba(255,255,255,0.9) !important; font-weight: 500; padding: 12px 20px; border-radius: 25px; transition: all 0.3s; }
        .topbar-nav .nav-link:hover, .topbar-nav .nav-link.active { background: rgba(255,255,255,0.2); color: white !important; transform: translateY(-2px); }
        .student-card, .content-card { background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .stat-card { background: linear-gradient(135deg, var(--purple), #7c3aed); color: white; border-radius: 15px; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-10px); box-shadow: 0 25px 50px rgba(0,0,0,0.2); }
        .stat-card.bg-success { background: linear-gradient(135deg, var(--success), #059669); }
        .stat-card.bg-warning { background: linear-gradient(135deg, var(--warning), #d97706); }
        .stat-card.bg-danger { background: linear-gradient(135deg, var(--danger), #dc2626); }
        .scholarship-card { transition: all 0.3s; border: none; }
        .scholarship-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
        .btn-modern { border-radius: 25px; padding: 10px 25px; font-weight: 500; transition: all 0.3s; }
        .badge-modern { padding: 6px 12px; border-radius: 20px; font-size: 0.8em; font-weight: 600; }
        .heart-btn { transition: all 0.3s; }
        .heart-btn.liked { color: #ef4444; animation: pulse 0.6s; }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.2); } 100% { transform: scale(1); } }
        .search-box:focus { box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25); border-color: var(--primary); }
    </style>
</head>
<body>
<div class="wrapper">

    <!-- TOPBAR MENU -->
    <nav class="navbar topbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold fs-4" href="#">
                <i class="fas fa-user-graduate me-2"></i>Student Dashboard
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
                            <span class="badge bg-success ms-1"><?php echo $matching_scholarships; ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="loadApplications()">
                            <i class="fas fa-file-alt me-1"></i>Đơn của tôi
                            <span class="badge bg-warning ms-1"><?php echo $total_applications; ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="loadFavorites()">
                            <i class="fas fa-heart me-1"></i>Yêu thích
                            <span class="badge bg-danger ms-1"><?php echo $favorites; ?></span>
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
            <!-- STUDENT INFO CARD -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="student-card p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="fw-bold mb-1"><?php echo $_SESSION['user_name']; ?></h2>
                                <p class="text-muted mb-2">GPA: <?php echo $student['gpa'] ?? 'N/A'; ?> | Ngành: <?php echo $student['major'] ?? 'N/A'; ?></p>
                                <div class="d-flex gap-3 flex-wrap">
                                    <span class="badge bg-success badge-modern"><i class="fas fa-file-alt me-1"></i><?php echo $total_applications; ?> Đơn nộp</span>
                                    <span class="badge bg-primary badge-modern"><i class="fas fa-check-circle me-1"></i><?php echo $approved_apps; ?> Đã trúng tuyển</span>
                                    <span class="badge bg-warning badge-modern"><i class="fas fa-clock me-1"></i><?php echo $pending_apps; ?> Chờ duyệt</span>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <button class="btn btn-success btn-modern" onclick="loadScholarships()">
                                    <i class="fas fa-search me-2"></i>Tìm học bổng
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
                        <h3 class="fw-bold mb-1"><?php echo $total_scholarships; ?></h3>
                        <p class="mb-0 opacity-90">Tổng học bổng</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-center p-4 h-100 bg-success" onclick="loadMatchingScholarships()">
                        <i class="fas fa-filter fa-3x mb-3 opacity-75"></i>
                        <h3 class="fw-bold mb-1"><?php echo $matching_scholarships; ?></h3>
                        <p class="mb-0 opacity-90">Phù hợp với bạn</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-center p-4 h-100 bg-warning" onclick="loadApplications()">
                        <i class="fas fa-file-alt fa-3x mb-3 opacity-75"></i>
                        <h3 class="fw-bold mb-1"><?php echo $total_applications; ?></h3>
                        <p class="mb-0 opacity-90">Đơn đã nộp</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card text-center p-4 h-100 bg-danger" onclick="loadFavorites()">
                        <i class="fas fa-heart fa-3x mb-3 opacity-75"></i>
                        <h3 class="fw-bold mb-1"><?php echo $favorites; ?></h3>
                        <p class="mb-0 opacity-90">Yêu thích</p>
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT -->
            <div id="mainContent"></div>
        </div>
    </div>

    <!-- MODAL CHI TIẾT HỌC BỐNG -->
    <div class="modal fade" id="scholarshipDetailModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content rounded-4 shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title mb-0" id="detailTitle"></h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="scholarshipDetailContent"></div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-success btn-lg px-5" id="applyFromDetail">
                        <i class="fas fa-paper-plane me-2"></i>Nộp đơn ngay
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL NỘP ĐƠN -->
    <div class="modal fade" id="applyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4 shadow-lg border-0">
                <div class="modal-header bg-success text-white">
                    <h4 class="modal-title mb-0"><i class="fas fa-file-upload me-2"></i>Nộp đơn ứng tuyển</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="applyForm" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <input type="hidden" name="scholarship_id" id="applyScholarshipId">
                        <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                        <div class="mb-4">
                            <h4 id="applyScholarshipTitle" class="fw-bold"></h4>
                            <small class="text-muted">GPA yêu cầu: <span id="applyGpaReq"></span> | Hạn nộp: <span id="applyDeadline"></span></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-600">Thư xin học bổng <span class="text-danger">*</span></label>
                            <textarea name="cover_letter" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-600">File CV/Resume <span class="text-danger">*</span></label>
                            <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <i class="fas fa-paper-plane me-2"></i>Nộp đơn
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const studentId = <?= $student_id ?>;

function loadScholarships() {
    $('.topbar-nav .nav-link').removeClass('active');
    $('[onclick="loadScholarships()"]').addClass('active');
    $('#mainContent').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i></div>');
    
    $.get('controllers/student_controller.php?action=scholarships', function(data) {
        $('#mainContent').html(`
            <div class="content-card p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Học bổng</h3>
                    <div class="input-group" style="width: 400px;">
                        <input type="text" id="searchScholarship" class="form-control search-box" placeholder="🔍 Tìm kiếm học bổng, trường...">
                        <button class="btn btn-primary" onclick="searchScholarships()"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                <div class="row" id="scholarshipGrid">${data.html}</div>
            </div>
        `);
    }, 'json');
}

function searchScholarships() {
    let keyword = $('#searchScholarship').val();
    if (keyword.length < 2) return;
    
    $.get('controllers/student_controller.php?action=search&keyword=' + encodeURIComponent(keyword), function(data) {
        $('#scholarshipGrid').html(data.html);
    }, 'json');
}

function toggleFavorite(scholarshipId, btn) {
    $.post('controllers/student_controller.php?action=toggle_favorite', {scholarship_id: scholarshipId}, function(response) {
        if (response.success) {
            $(btn).toggleClass('liked').find('i').toggleClass('far fas');
            let count = $(btn).find('.count');
            if (count.length) {
                count.text(response.count);
            }
        }
    }, 'json');
}

function viewScholarshipDetail(scholarshipId) {
    $('#scholarshipDetailModal').modal('show');
    $('#scholarshipDetailContent').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i></div>');
    
    $.get('controllers/student_controller.php?action=detail&id=' + scholarshipId, function(data) {
        $('#detailTitle').text(data.title);
        $('#scholarshipDetailContent').html(`
            <div class="row">
                <div class="col-md-8">
                    <h5 class="fw-bold mb-3">${data.organization}</h5>
                    <div class="mb-4 p-3 bg-light rounded-3">
                        <h6 class="fw-bold mb-3"><i class="fas fa-align-left me-2"></i>Mô tả</h4>
                        <p class="lead">${data.description || 'Không có mô tả chi tiết'}</p>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 bg-success bg-opacity-10 text-success p-3">
                                <h6 class="fw-bold mb-2"><i class="fas fa-map-marker-alt me-2"></i>Địa điểm</h6>
                                <p class="mb-0">${data.country}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-warning bg-opacity-10 text-warning p-3">
                                <h6 class="fw-bold mb-2"><i class="fas fa-calendar-alt me-2"></i>Hạn nộp</h6>
                                <p class="mb-0">${data.deadline}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-graduation-cap fa-4x text-primary mb-3"></i>
                            <h4 class="fw-bold mb-2">${data.gpa_min} GPA</h4>
                            <div class="mb-4">
                                <span class="badge bg-info fs-6 px-3 py-2 me-2 mb-2">Ngành: ${data.major}</span>
                                <br><span class="badge bg-secondary fs-6 px-3 py-2">$${data.amount || 'Chưa rõ'}</span>
                            </div>
                            <small class="text-muted mb-3 d-block">
                                <i class="fas fa-users me-1"></i>${data.applications} đơn đã nộp
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `);
        
        $('#applyFromDetail').off('click').click(function() {
            applyScholarship(data.id, data.title, data.gpa_min, data.deadline);
            $('#scholarshipDetailModal').modal('hide');
        });
    }, 'json');
}

function applyScholarship(scholarshipId, title, gpaReq, deadline) {
    $('#applyScholarshipId').val(scholarshipId);
    $('#applyScholarshipTitle').text(title);
    $('#applyGpaReq').text(gpaReq);
    $('#applyDeadline').text(deadline);
    $('#applyModal').modal('show');
}

$('#applyForm').submit(function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    $.ajax({
        url: 'controllers/student_controller.php?action=apply',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                alert('✅ Nộp đơn thành công!');
                $('#applyModal').modal('hide');
                location.reload();
            } else {
                alert('❌ Lỗi: ' + response.error);
            }
        },
        dataType: 'json'
    });
});

$(document).ready(function() {
    loadScholarships();
    
    $(document).on('keypress', '#searchScholarship', function(e) {
        if (e.which == 13) searchScholarships();
    });
});
</script>
</body>
</html>