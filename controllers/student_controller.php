<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$conn = $database->getConnection();

function getCount($conn, $sql, $params = []) {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn() ?: 0;
}

header('Content-Type: application/json');

if (isset($_GET['action']) || $_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_GET['action'] ?? $_POST['action'] ?? '') {
        case 'scholarships':
            $stmt = $conn->prepare("
                SELECT s.*, p.organization, 
                       COUNT(a.id) as applications,
                       CASE WHEN f.scholarship_id IS NOT NULL THEN 1 ELSE 0 END as is_favorite
                FROM scholarships s 
                JOIN providers p ON s.provider_id = p.id 
                LEFT JOIN applications a ON s.id = a.scholarship_id 
                LEFT JOIN favorites f ON s.id = f.scholarship_id AND f.student_id = ?
                WHERE s.status = 'active' 
                GROUP BY s.id 
                ORDER BY s.created_at DESC 
                LIMIT 12
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $scholarships = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $html = '';
            foreach ($scholarships as $sch) {
                $heartClass = $sch['is_favorite'] ? 'fas liked' : 'far';
                $html .= "
                <div class='col-lg-6 col-xl-4 mb-4'>
                    <div class='card scholarship-card h-100'>
                        <div class='card-body'>
                            <div class='d-flex justify-content-between align-items-start mb-3'>
                                <h5 class='card-title fw-bold'>{$sch['title']}</h5>
                                <button class='btn heart-btn p-2' onclick='toggleFavorite({$sch['id']}, this)' title='Yêu thích'>
                                    <i class='$heartClass fa-heart'></i>
                                </button>
                            </div>
                            <p class='text-muted mb-2'><i class='fas fa-building me-1'></i>{$sch['organization']}</p>
                            <p class='text-muted mb-3'><i class='fas fa-map-marker-alt me-1'></i>{$sch['country']}</p>
                            <div class='row mb-3'>
                                <div class='col-6'>
                                    <small class='text-success'><i class='fas fa-graduation-cap me-1'></i>{$sch['major']}</small>
                                </div>
                                <div class='col-6'>
                                    <small class='text-warning'><i class='fas fa-star me-1'></i>{$sch['gpa_min']} GPA</small>
                                </div>
                            </div>
                            <div class='mb-3'>
                                <small class='text-muted'>Hạn chót: " . date('d/m/Y', strtotime($sch['deadline'])) . "</small>
                            </div>
                            <div class='d-grid gap-2 d-md-block'>
                                <button class='btn btn-outline-primary btn-modern me-2 mb-2' onclick='viewScholarshipDetail({$sch['id']})'>
                                    <i class='fas fa-eye me-2'></i>Chi tiết
                                </button>
                                <button class='btn btn-success btn-modern' onclick='applyScholarship({$sch['id']}, \"{$sch['title']}\", {$sch['gpa_min']}, \"" . date('d/m/Y', strtotime($sch['deadline'])) . "\")'>
                                    <i class='fas fa-paper-plane me-2'></i>Nộp đơn
                                </button>
                            </div>
                        </div>
                    </div>
                </div>";
            }
            
            echo json_encode([
                'count' => getCount($conn, "SELECT COUNT(*) FROM scholarships WHERE status='active'"),
                'html' => $html ?: '<div class="col-12"><div class="alert alert-info text-center">Chưa có học bổng nào</div></div>'
            ]);
            break;
            
        case 'search':
            $keyword = $_GET['keyword'] ?? '';
            $stmt = $conn->prepare("
                SELECT s.*, p.organization, 
                       COUNT(a.id) as applications,
                       CASE WHEN f.scholarship_id IS NOT NULL THEN 1 ELSE 0 END as is_favorite
                FROM scholarships s 
                JOIN providers p ON s.provider_id = p.id 
                LEFT JOIN applications a ON s.id = a.scholarship_id 
                LEFT JOIN favorites f ON s.id = f.scholarship_id AND f.student_id = ?
                WHERE s.status = 'active' 
                AND (s.title LIKE ? OR p.organization LIKE ? OR s.major LIKE ? OR s.country LIKE ?)
                GROUP BY s.id 
                ORDER BY s.created_at DESC
            ");
            $searchTerm = "%$keyword%";
            $stmt->execute([$_SESSION['user_id'], $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            $scholarships = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $html = '';
            foreach ($scholarships as $sch) {
                $heartClass = $sch['is_favorite'] ? 'fas liked' : 'far';
                $html .= "
                <div class='col-lg-6 col-xl-4 mb-4'>
                    <div class='card scholarship-card h-100'>
                        <div class='card-body'>
                            <div class='d-flex justify-content-between align-items-start mb-3'>
                                <h5 class='card-title fw-bold'>{$sch['title']}</h5>
                                <button class='btn heart-btn p-2' onclick='toggleFavorite({$sch['id']}, this)' title='Yêu thích'>
                                    <i class='$heartClass fa-heart'></i>
                                </button>
                            </div>
                            <p class='text-muted mb-2'><i class='fas fa-building me-1'></i>{$sch['organization']}</p>
                            <div class='d-grid gap-2 d-md-block'>
                                <button class='btn btn-outline-primary btn-modern me-2 mb-2' onclick='viewScholarshipDetail({$sch['id']})'>
                                    <i class='fas fa-eye me-2'></i>Chi tiết
                                </button>
                                <button class='btn btn-success btn-modern' onclick='applyScholarship({$sch['id']}, \"{$sch['title']}\", {$sch['gpa_min']}, \"" . date('d/m/Y', strtotime($sch['deadline'])) . "\")'>
                                    <i class='fas fa-paper-plane me-2'></i>Nộp đơn
                                </button>
                            </div>
                        </div>
                    </div>
                </div>";
            }
            
            echo json_encode(['html' => $html ?: '<div class="col-12"><div class="alert alert-warning text-center">Không tìm thấy học bổng nào</div></div>']);
            break;
            
        case 'detail':
            $id = $_GET['id'];
            $stmt = $conn->prepare("
                SELECT s.*, p.organization, COUNT(a.id) as applications 
                FROM scholarships s 
                JOIN providers p ON s.provider_id = p.id 
                LEFT JOIN applications a ON s.id = a.scholarship_id 
                WHERE s.id = ? AND s.status = 'active'
            ");
            $stmt->execute([$id]);
            $scholarship = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($scholarship) {
                echo json_encode([
                    'id' => $scholarship['id'],
                    'title' => $scholarship['title'],
                    'organization' => $scholarship['organization'],
                    'description' => $scholarship['description'],
                    'country' => $scholarship['country'],
                    'gpa_min' => $scholarship['gpa_min'],
                    'major' => $scholarship['major'],
                    'deadline' => date('d/m/Y', strtotime($scholarship['deadline'])),
                    'amount' => $scholarship['amount'] ?: 'Chưa rõ',
                    'applications' => $scholarship['applications']
                ]);
            } else {
                echo json_encode(['error' => 'Học bổng không tồn tại']);
            }
            break;
            
        case 'toggle_favorite':
        case 'apply':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if ($_GET['action'] === 'toggle_favorite') {
                    $scholarship_id = $_POST['scholarship_id'];
                    
                    $stmt = $conn->prepare("SELECT * FROM favorites WHERE student_id = ? AND scholarship_id = ?");
                    $stmt->execute([$_SESSION['user_id'], $scholarship_id]);
                    
                    if ($stmt->rowCount()) {
                        $stmt = $conn->prepare("DELETE FROM favorites WHERE student_id = ? AND scholarship_id = ?");
                        $result = $stmt->execute([$_SESSION['user_id'], $scholarship_id]);
                    } else {
                        $stmt = $conn->prepare("INSERT INTO favorites (student_id, scholarship_id, created_at) VALUES (?, ?, NOW())");
                        $result = $stmt->execute([$_SESSION['user_id'], $scholarship_id]);
                    }
                    
                    $count = getCount($conn, "SELECT COUNT(*) FROM favorites WHERE student_id = ?", [$_SESSION['user_id']]);
                    echo json_encode(['success' => $result, 'count' => $count]);
                    
                } else { // apply
                    $scholarship_id = $_POST['scholarship_id'];
                    $student_id = $_POST['student_id'];
                    $cover_letter = $_POST['cover_letter'];
                    
                    $cv_path = '';
                    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === 0) {
                        $upload_dir = 'uploads/cvs/';
                        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                        $cv_name = 'cv_' . $student_id . '_' . time() . '_' . $_FILES['cv']['name'];
                        $cv_path = $upload_dir . $cv_name;
                        move_uploaded_file($_FILES['cv']['tmp_name'], $cv_path);
                    }
                    
                    $stmt = $conn->prepare("
                        INSERT INTO applications (student_id, scholarship_id, cover_letter, cv, status, created_at) 
                        VALUES (?, ?, ?, ?, 'pending', NOW())
                    ");
                    $result = $stmt->execute([$student_id, $scholarship_id, $cover_letter, $cv_path]);
                    
                    echo json_encode(['success' => $result, 'error' => $result ? '' : 'Lỗi nộp đơn']);
                }
            }
            break;
    }
}
?>