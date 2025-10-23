<?php
// models/Student.php
// Bảng gợi ý: students(id INT PK AI, user_id INT FK->users.id, major VARCHAR(255), gpa DECIMAL(3,2), phone VARCHAR(20), about TEXT, created_at TIMESTAMP)
class Student {
  private mysqli $db;
  private string $table = "students";

  public function __construct(mysqli $db) {
    $this->db = $db;
  }

  public function create(int $userId, ?string $major, ?float $gpa, ?string $phone, ?string $about): ?int {
    $sql = "INSERT INTO {$this->table} (user_id, major, gpa, phone, about) VALUES (?, ?, ?, ?, ?)";
    if (!$stmt = $this->db->prepare($sql)) return null;
    $stmt->bind_param("isdss", $userId, $major, $gpa, $phone, $about);
    if (!$stmt->execute()) return null;
    return $stmt->insert_id;
  }

  public function findByUserId(int $userId): ?array {
    $sql = "SELECT * FROM {$this->table} WHERE user_id = ?";
    if (!$stmt = $this->db->prepare($sql)) return null;
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_assoc() ?: null;
  }

  public function updateProfile(int $userId, ?string $major, ?float $gpa, ?string $phone, ?string $about): bool {
    $sql = "UPDATE {$this->table} SET major = ?, gpa = ?, phone = ?, about = ? WHERE user_id = ?";
    if (!$stmt = $this->db->prepare($sql)) return false;
    $stmt->bind_param("sdssi", $major, $gpa, $phone, $about, $userId);
    return $stmt->execute();
  }

  public function deleteByUser(int $userId): bool {
    $sql = "DELETE FROM {$this->table} WHERE user_id = ?";
    if (!$stmt = $this->db->prepare($sql)) return false;
    $stmt->bind_param("i", $userId);
    return $stmt->execute();
  }
}
