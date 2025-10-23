<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$provider_id = $_GET['provider_id'] ?? 0;

header('Content-Type: application/json');

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'scholarships':
            $stmt = $conn->prepare("
                SELECT s.*, COUNT(a.id) as application_count 
                FROM scholarships s 
                LEFT JOIN applications a ON s.id = a.scholarship_id 
                WHERE s.provider_id = ? 
                GROUP BY s.id 
                ORDER BY s.created_at DESC
            ");
            $stmt->execute([$provider_id]);
            $scholarships = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $html = '';
            foreach ($scholarships as $sch) {
                $html .= "<tr>
                    <td>{$sch['id']}</td>
                    <td>{$sch['title']}</td>
                    <td>{$sch['major']}</td>
                    <td>{$sch['gpa_min']}</td>
                    <td>" . date('d/m/Y', strtotime($sch['deadline'])) . "</td>
                    <td><span class='badge bg-info'>{$sch['application_count']}</span></td>
                    <td>
                        <button class='btn btn-sm btn-primary'>Chỉnh sửa</button>
                        <button class='btn btn-sm btn-danger'>Xóa</button>
                    </td>
                </tr>";
            }
            
            echo json_encode(['count' => count($scholarships), 'html' => $html]);
            break;
            
        case 'applications':
            $stmt = $conn->prepare("
                SELECT a.*, u.name as student_name, u.email as student_email, s.title as scholarship_title 
                FROM applications a 
                JOIN scholarships s ON a.scholarship_id = s.id 
                JOIN students st ON a.student_id = st.id 
                JOIN users u ON st.user_id = u.id 
                WHERE s.provider_id = ? 
                ORDER BY a.created_at DESC
            ");
            $stmt->execute([$provider_id]);
            $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $html = '';
            foreach ($applications as $app) {
                $html .= "<tr>
                    <td>{$app['id']}</td>
                    <td>{$app['student_name']}</td>
                    <td>{$app['scholarship_title']}</td>
                    <td>" . ($app['cv'] ? '<a href="' . $app['cv'] . '" target="_blank" class="btn btn-sm btn-info">📄 CV</a>' : 'N/A') . "</td>
                    <td><span class='badge bg-" . ($app['status']=='pending' ? 'warning' : ($app['status']=='approved' ? 'success' : 'danger')) . "'>" . ucfirst($app['status']) . "</span></td>
                    <td>" . date('d/m/Y H:i', strtotime($app['created_at'])) . "</td>
                    <td>
                        <button class='btn btn-sm btn-success'>Duyệt</button>
                        <button class='btn btn-sm btn-danger'>Từ chối</button>
                    </td>
                </tr>";
            }
            
            echo json_encode(['count' => count($applications), 'html' => $html]);
            break;
            
        case 'create':
            $data = $_POST;
            $stmt = $conn->prepare("
                INSERT INTO scholarships (provider_id, title, description, major, country, gpa_min, deadline, amount, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())
            ");
            $result = $stmt->execute([
                $data['provider_id'],
                $data['title'],
                $data['description'] ?? '',
                $data['major'],
                $data['country'],
                $data['gpa_min'],
                $data['deadline'],
                0
            ]);
            
            echo json_encode([
                'success' => $result,
                'error' => $result ? '' : 'Lỗi tạo học bổng'
            ]);
            break;
    }
    exit();
}
?>