<?php
require_once __DIR__ . '/../config/database.php';

class Favorite {
    private $conn;
    private $table_name = "favorites";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ✅ ADD FAVORITE
    public function add($student_id, $scholarship_id) {
        $query = "INSERT IGNORE INTO {$this->table_name} (student_id, scholarship_id, created_at) 
                  VALUES (:student_id, :scholarship_id, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':scholarship_id', $scholarship_id);
        return $stmt->execute();
    }

    // ✅ REMOVE FAVORITE - SỬA LỖI
    public function remove($student_id, $scholarship_id) {
        $query = "DELETE FROM {$this->table_name} 
                  WHERE student_id = :student_id AND scholarship_id = :scholarship_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':scholarship_id', $scholarship_id);
        return $stmt->execute();
    }

    // ✅ GET BY STUDENT
    public function getByStudent($student_id) {
        $query = "SELECT f.*, s.title FROM {$this->table_name} f 
                  JOIN scholarships s ON f.scholarship_id = s.id 
                  WHERE f.student_id = :student_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ CHECK EXISTS
    public function exists($student_id, $scholarship_id) {
        $query = "SELECT id FROM {$this->table_name} 
                  WHERE student_id = :student_id AND scholarship_id = :scholarship_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':scholarship_id', $scholarship_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
