<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$conn = $database->getConnection();

header('Content-Type: text/html');

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'users':
            $users = $conn->query("
                SELECT u.*, s.gpa, s.major, p.organization 
                FROM users u 
                LEFT JOIN students s ON u.id = s.user_id 
                LEFT JOIN providers p ON u.id = p.user_id 
                ORDER BY u.created_at DESC
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            $html = '';
            foreach ($users as $user) {
                $html .= "<tr>
                    <td>{$user['id']}</td>
                    <td>{$user['email']}</td>
                    <td>{$user['name']}</td>
                    <td><span class='badge bg-" . ($user['role']=='admin' ? 'danger' : ($user['role']=='student' ? 'success' : 'warning')) . "'>" . ucfirst($user['role']) . "</span></td>
                    <td>" . date('d/m/Y', strtotime($user['created_at'])) . "</td>
                    <td>
                        <button class='btn btn-sm btn-primary'>Sửa</button>
                        <button class='btn btn-sm btn-danger'>Xóa</button>
                    </td>
                </tr>";
            }
            echo $html;
            break;
            
        case 'scholarships':
            $scholarships = $conn->query("
                SELECT s.*, p.organization 
                FROM scholarships s 
                JOIN providers p ON s.provider_id = p.id 
                ORDER BY s.created_at DESC
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            $html = '';
            foreach ($scholarships as $sch) {
                $html .= "<tr>
                    <td>{$sch['id']}</td>
                    <td>{$sch['title']}</td>
                    <td>{$sch['organization']}</td>
                    <td>{$sch['major']}</td>
                    <td>{$sch['gpa_min']}</td>
                    <td>" . date('d/m/Y', strtotime($sch['deadline'])) . "</td>
                    <td><span class='badge bg-" . ($sch['status']=='active' ? 'success' : 'secondary') . "'>" . ucfirst($sch['status']) . "</span></td>
                    <td>
                        <button class='btn btn-sm btn-primary'>Sửa</button>
                        <button class='btn btn-sm btn-danger'>Xóa</button>
                    </td>
                </tr>";
            }
            echo $html;
            break;
            
        case 'applications':
            $apps = $conn->query("
                SELECT a.*, u.name as student_name, s.title as scholarship_title, p.organization 
                FROM applications a 
                JOIN students st ON a.student_id = st.id 
                JOIN users u ON st.user_id = u.id 
                JOIN scholarships s ON a.scholarship_id = s.id
                JOIN providers p ON s.provider_id = p.id
                ORDER BY a.created_at DESC
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            $html = '';
            foreach ($apps as $app) {
                $html .= "<tr>
                    <td>{$app['id']}</td>
                    <td>{$app['student_name']}</td>
                    <td>{$app['scholarship_title']}</td>
                    <td>" . ($app['cv'] ? '<a href="' . $app['cv'] . '" target="_blank" class="btn btn-sm btn-info">📄</a>' : 'N/A') . "</td>
                    <td><span class='badge bg-" . ($app['status']=='pending' ? 'warning' : ($app['status']=='approved' ? 'success' : 'danger')) . "'>" . ucfirst($app['status']) . "</span></td>
                    <td>" . date('d/m/Y H:i', strtotime($app['created_at'])) . "</td>
                    <td>
                        <button class='btn btn-sm btn-success'>Duyệt</button>
                        <button class='btn btn-sm btn-danger'>Từ chối</button>
                    </td>
                </tr>";
            }
            echo $html;
            break;
    }
    exit();
}
?>