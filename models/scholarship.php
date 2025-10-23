<?php
// models/Scholarship.php
class Scholarship {
  private mysqli $db;
  private string $table = "scholarships";

  public function __construct(mysqli $db) {
    $this->db = $db;
  }

  public function create(int $providerId, string $title, string $description, ?float $amount, ?string $deadline): ?int {
    $sql = "INSERT INTO {$this->table} (provider_id, title, description, amount, deadline) VALUES (?, ?, ?, ?, ?)";
    if (!$stmt = $this->db->prepare($sql)) return null;
    $stmt->bind_param("issds", $providerId, $title, $description, $amount, $deadline);
    if (!$stmt->execute()) return null;
    return $stmt->insert_id;
  }

  public function findById(int $id): ?array {
    $sql = "SELECT s.*, p.org_name 
            FROM {$this->table} s 
            LEFT JOIN providers p ON p.id = s.provider_id
            WHERE s.id = ?";
    if (!$stmt = $this->db->prepare($sql)) return null;
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_assoc() ?: null;
  }

  public function update(int $id, string $title, string $description, ?float $amount, ?string $deadline): bool {
    $sql = "UPDATE {$this->table} SET title = ?, description = ?, amount = ?, deadline = ? WHERE id = ?";
    if (!$stmt = $this->db->prepare($sql)) return false;
    $stmt->bind_param("ssdsi", $title, $description, $amount, $deadline, $id);
    return $stmt->execute();
  }

  public function delete(int $id): bool {
    $sql = "DELETE FROM {$this->table} WHERE id = ?";
    if (!$stmt = $this->db->prepare($sql)) return false;
    $stmt->bind_param("i", $id);
    return $stmt->execute();
  }

  // ✅ Alias cho index.php đang gọi $scholarship->getAll()
  public function getAll(int $limit = 20, int $offset = 0, ?string $keyword = null): array {
    return $this->list($limit, $offset, $keyword);
  }

  public function list(int $limit = 20, int $offset = 0, ?string $keyword = null): array {
    if ($keyword) {
      $kw = "%{$keyword}%";
      $sql = "SELECT s.*, p.org_name 
              FROM {$this->table} s 
              LEFT JOIN providers p ON p.id = s.provider_id
              WHERE s.title LIKE ? OR s.description LIKE ?
              ORDER BY s.id DESC LIMIT ? OFFSET ?";
      $stmt = $this->db->prepare($sql);
      $stmt->bind_param("ssii", $kw, $kw, $limit, $offset);
    } else {
      $sql = "SELECT s.*, p.org_name 
              FROM {$this->table} s 
              LEFT JOIN providers p ON p.id = s.provider_id
              ORDER BY s.id DESC LIMIT ? OFFSET ?";
      $stmt = $this->db->prepare($sql);
      $stmt->bind_param("ii", $limit, $offset);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [];
  }
}
