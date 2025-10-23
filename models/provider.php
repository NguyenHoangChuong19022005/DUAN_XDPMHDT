<?php
// models/Provider.php
// Bảng gợi ý: providers(id INT PK AI, user_id INT FK->users.id, org_name VARCHAR(255), contact_email VARCHAR(255), website VARCHAR(255), created_at TIMESTAMP)
class Provider {
  private mysqli $db;
  private string $table = "providers";

  public function __construct(mysqli $db) {
    $this->db = $db;
  }

  public function create(int $userId, string $orgName, string $contactEmail, ?string $website): ?int {
    $sql = "INSERT INTO {$this->table} (user_id, org_name, contact_email, website) VALUES (?, ?, ?, ?)";
    if (!$stmt = $this->db->prepare($sql)) return null;
    $stmt->bind_param("isss", $userId, $orgName, $contactEmail, $website);
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

  public function updateProfile(int $userId, string $orgName, string $contactEmail, ?string $website): bool {
    $sql = "UPDATE {$this->table} SET org_name = ?, contact_email = ?, website = ? WHERE user_id = ?";
    if (!$stmt = $this->db->prepare($sql)) return false;
    $stmt->bind_param("sssi", $orgName, $contactEmail, $website, $userId);
    return $stmt->execute();
  }

  public function deleteByUser(int $userId): bool {
    $sql = "DELETE FROM {$this->table} WHERE user_id = ?";
    if (!$stmt = $this->db->prepare($sql)) return false;
    $stmt->bind_param("i", $userId);
    return $stmt->execute();
  }
}
