<?php
require_once __DIR__ . '/../config/database.php';

class Student {
    private $conn;
    private $table_name = "students";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, gpa=:gpa, major=:major, university=:university";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':gpa', $data['gpa']);
        $stmt->bindParam(':major', $data['major']);
        $stmt->bindParam(':university', $data['university']);
        return $stmt->execute();
    }

    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($data, $user_id) {
        $query = "UPDATE " . $this->table_name . " SET gpa=:gpa, major=:major, university=:university, skills=:skills, achievements=:achievements, research_interest=:research_interest, thesis_topic=:thesis_topic WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':gpa', $data['gpa']);
        $stmt->bindParam(':major', $data['major']);
        $stmt->bindParam(':university', $data['university']);
        $stmt->bindParam(':skills', $data['skills']);
        $stmt->bindParam(':achievements', $data['achievements']);
        $stmt->bindParam(':research_interest', $data['research_interest']);
        $stmt->bindParam(':thesis_topic', $data['thesis_topic']);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }
}
?>