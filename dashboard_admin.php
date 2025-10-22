<?php 
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';
$database = new Database();
$conn = $database->getConnection();

$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_students = $conn->query("SELECT COUNT(*) FROM students")->fetchColumn();
$total_providers = $conn->query("SELECT COUNT(*) FROM providers")->fetchColumn();
$total_scholarships = $conn->query("SELECT COUNT(*) FROM scholarships WHERE status='active'")->fetchColumn();
$total_applications = $conn->query("SELECT COUNT(*) FROM applications")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduMatch - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- ✅ NAVBAR - FIX ĐĂNG XUẤT -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-bs-toggle="dropdown" href="#" role="button" id="userDropdown">
                    <i class="far fa-user"></i> <?php echo $_SESSION['user_name']; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item text-danger" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="dashboard_admin.php" class="brand-link">
            <span class="brand-text font-weight-light">EduMatch Admin</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link active" onclick="loadDashboard()">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="loadUsers()">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="loadScholarships()">
                            <i class="nav-icon fas fa-graduation-cap"></i>
                            <p>Học bổng</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="loadApplications()">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Đơn ứng tuyển</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 id="pageTitle">Dashboard</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div id="mainContent">
                    <!-- 6 INFO BOXES -->
                    <div class="row">
                        <div class="col-lg-2 col-6">
                            <div class="info-box" onclick="loadUsers()">
                                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Tổng Users</span>
                                    <span class="info-box-number"><?php echo $total_users; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-6">
                            <div class="info-box" onclick="loadUsers()">
                                <span class="info-box-icon bg-success"><i class="fas fa-user-graduate"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Students</span>
                                    <span class="info-box-number"><?php echo $total_students; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-6">
                            <div class="info-box" onclick="loadUsers()">
                                <span class="info-box-icon bg-warning"><i class="fas fa-university"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Providers</span>
                                    <span class="info-box-number"><?php echo $total_providers; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-6">
                            <div class="info-box" onclick="loadScholarships()">
                                <span class="info-box-icon bg-primary"><i class="fas fa-graduation-cap"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Học bổng</span>
                                    <span class="info-box-number"><?php echo $total_scholarships; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-6">
                            <div class="info-box" onclick="loadApplications()">
                                <span class="info-box-icon bg-danger"><i class="fas fa-file-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Đơn nộp</span>
                                    <span class="info-box-number"><?php echo $total_applications; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <footer class="main-footer">
        <strong>Copyright © 2025 <a href="#">EduMatch</a>.</strong>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function loadDashboard() {
    $('#pageTitle').text('Dashboard');
    $('#mainContent').html(`
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Thống kê realtime</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="statsChart" height="70"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Hoạt động gần đây</h3>
                    </div>
                    <div class="card-body">
                        <div class="activity">
                            <div class="activity-item">
                                <i class="fa fa-user-plus text-primary"></i>
                                <div class="activity-content">
                                    <h6>Student đăng ký mới</h6><small>5 phút trước</small>
                                </div>
                            </div>
                            <div class="activity-item">
                                <i class="fa fa-graduation-cap text-success"></i>
                                <div class="activity-content">
                                    <h6>Học bổng mới</h6><small>2 giờ trước</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `);
    loadChart();
}

function loadUsers() {
    $('#pageTitle').text('Quản lý Users');
    $.get('controllers/admin_controller.php?action=users', function(data) {
        $('#mainContent').html(`
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách Users</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr><th>ID</th><th>Email</th><th>Tên</th><th>Role</th><th>Ngày tạo</th><th>Action</th></tr>
                        </thead>
                        <tbody>${data}</tbody>
                    </table>
                </div>
            </div>
        `);
    });
}

function loadScholarships() {
    $('#pageTitle').text('Quản lý Học bổng');
    $.get('controllers/admin_controller.php?action=scholarships', function(data) {
        $('#mainContent').html(`
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách Học bổng</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr><th>ID</th><th>Tiêu đề</th><th>Provider</th><th>Ngành</th><th>GPA</th><th>Hạn nộp</th><th>Status</th><th>Action</th></tr>
                        </thead>
                        <tbody>${data}</tbody>
                    </table>
                </div>
            </div>
        `);
    });
}

function loadApplications() {
    $('#pageTitle').text('Đơn ứng tuyển');
    $.get('controllers/admin_controller.php?action=applications', function(data) {
        $('#mainContent').html(`
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách Đơn</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr><th>ID</th><th>Student</th><th>Học bổng</th><th>CV</th><th>Status</th><th>Ngày nộp</th><th>Action</th></tr>
                        </thead>
                        <tbody>${data}</tbody>
                    </table>
                </div>
            </div>
        `);
    });
}

function loadChart() {
    const ctx = document.getElementById('statsChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5'],
                datasets: [{
                    label: 'Học bổng',
                    data: [10, 12, 15, 18, 20],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    }
}

$(document).ready(function() {
    loadDashboard();
});
</script>
</body>
</html>