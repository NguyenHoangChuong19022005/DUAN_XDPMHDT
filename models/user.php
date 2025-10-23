<?php
// models/User.php
// Bảng gợi ý: users(id INT PK AI, email VARCHAR(255) UNIQUE, password VARCHAR(255), name VARCHAR(255), role ENUM('student','admin','provider'), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
class User {
  private mysqli $db;
  private string $table = "users";

  public function __construct(mysqli $db) {
    $this->db = $db;
  }

  public function create(string $email, string $passwordHash, string $name, string $role = 'student'): ?int {
    $sql = "INSERT INTO {$this->table} (email, password, name, role) VALUES (?, ?, ?, ?)";
    if (!$stmt = $this->db->prepare($sql)) return null;
    $stmt->bind_param("ssss", $email, $passwordHash, $name, $role);
    if (!$stmt->execute()) return null;
    return $stmt->insert_id;
  }

  public function findById(int $id): ?array {
    $sql = "SELECT id, email, name, role, created_at FROM {$this->table} WHERE id = ?";
    if (!$stmt = $this->db->prepare($sql)) return null;
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_assoc() ?: null;
  }

  public function findByEmail(string $email): ?array {
    $sql = "SELECT * FROM {$this->table} WHERE email = ?";
    if (!$stmt = $this->db->prepare($sql)) return null;
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_assoc() ?: null;
  }

  public function updateBasic(int $id, string $name, string $role): bool {
    $sql = "UPDATE {$this->table} SET name = ?, role = ? WHERE id = ?";
    if (!$stmt = $this->db->prepare($sql)) return false;
    $stmt->bind_param("ssi", $name, $role, $id);
    return $stmt->execute();
  }

  public function updatePassword(int $id, string $newPasswordHash): bool {
    $sql = "UPDATE {$this->table} SET password = ? WHERE id = ?";
    if (!$stmt = $this->db->prepare($sql)) return false;
    $stmt->bind_param("si", $newPasswordHash, $id);
    return $stmt->execute();
  }

  public function delete(int $id): bool {
    $sql = "DELETE FROM {$this->table} WHERE id = ?";
    if (!$stmt = $this->db->prepare($sql)) return false;
    $stmt->bind_param("i", $id);
    return $stmt->execute();
  }

  public function list(int $limit = 20, int $offset = 0, ?string $role = null): array {
    if ($role) {
      $sql = "SELECT id, email, name, role, created_at FROM {$this->table} WHERE role = ? ORDER BY id DESC LIMIT ? OFFSET ?";
      $stmt = $this->db->prepare($sql);
      $stmt->bind_param("sii", $role, $limit, $offset);
    } else {
      $sql = "SELECT id, email, name, role, created_at FROM {$this->table} ORDER BY id DESC LIMIT ? OFFSET ?";
      $stmt = $this->db->prepare($sql);
      $stmt->bind_param("ii", $limit, $offset);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [];
  }
}
