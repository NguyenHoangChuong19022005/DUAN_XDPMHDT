<?php
// models/Application.php
// Bảng gợi ý: applications(id INT PK AI, student_id INT FK->students.id, scholarship_id INT FK->scholarships.id, status ENUM('pending','review','accepted','rejected') DEFAULT 'pending', submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
class Application {
  private mysqli $db;
  private string $table = "applications";

  public function __construct(mysqli $db) {
    $this->db = $db;
  }

  public function create(int $studentId, int $scholarshipId, string $status = 'pending'): ?int {
    $sql = "INSERT INTO {$this->table} (student_id, scholarship_id, status) VALUES (?, ?, ?)";
    if (!$stmt = $this->db->prepare($sql)) return null;
    $stmt->bind_param("iis", $studentId, $scholarshipId, $status);
    if (!$stmt->execute()) return null;
    return $stmt->insert_id;
  }

  public function findById(int $id): ?array {
    $sql = "SELECT a.*, s.title, st.user_id AS student_user_id
            FROM {$this->table} a
            LEFT JOIN scholarships s ON s.id = a.scholarship_id
            LEFT JOIN students st ON st.id = a.student_id
            WHERE a.id = ?";
    if (!$stmt = $this->db->prepare($sql)) return null;
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_assoc() ?: null;
  }

  public function updateStatus(int $id, string $status): bool {
    $sql = "UPDATE {$this->table} SET status = ? WHERE id = ?";
    if (!$stmt = $this->db->prepare($sql)) return false;
    $stmt->bind_param("si", $status, $id);
    return $stmt->execute();
  }

  public function listByStudent(int $studentId, int $limit = 20, int $offset = 0): array {
    $sql = "SELECT a.*, s.title
            FROM {$this->table} a
            LEFT JOIN scholarships s ON s.id = a.scholarship_id
            WHERE a.student_id = ?
            ORDER BY a.id DESC
            LIMIT ? OFFSET ?";
    if (!$stmt = $this->db->prepare($sql)) return [];
    $stmt->bind_param("iii", $studentId, $limit, $offset);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [];
  }

  public function listByScholarship(int $scholarshipId, int $limit = 20, int $offset = 0): array {
    $sql = "SELECT a.*, st.user_id AS student_user_id
            FROM {$this->table} a
            LEFT JOIN students st ON st.id = a.student_id
            WHERE a.scholarship_id = ?
            ORDER BY a.id DESC
            LIMIT ? OFFSET ?";
    if (!$stmt = $this->db->prepare($sql)) return [];
    $stmt->bind_param("iii", $scholarshipId, $limit, $offset);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [];
  }
}
