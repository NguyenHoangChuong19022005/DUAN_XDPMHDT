<?php
require_once __DIR__ . '/config/database.php';
$db = new Database();
$conn = $db->getConnection();
echo "✅ Kết nối thành công tới DB!";
?>
