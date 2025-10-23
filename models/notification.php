<?php
// models/Notification.php
// Bảng gợi ý: notifications(id INT PK AI, user_id INT FK->users.id, title VARCHAR(255), message TEXT, is_read TINYINT(1) DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
class Notification {
  private mysqli $db;
  private string $table = "notifications";

  public function __construct(mysqli $db) {
    $this->db = $db;
  }

  public function create(int $userId, string $title, string $message): ?int {
    $sql = "INSERT INTO {$this->table} (user_id, title, message) VALUES (?, ?, ?)";
    if (!$stmt = $this->db->prepare($sql)) return null;
    $stmt->bind_param("iss", $userId, $title, $message);
    if (!$stmt->execute()) return null;
    return $stmt->insert_id;
  }

  public function listByUser(int $userId, bool $onlyUnread = false, int $limit = 20, int $offset = 0): array {
    if ($onlyUnread) {
      $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND is_read = 0 ORDER BY id DESC LIMIT ? OFFSET ?";
      $stmt = $this->db->prepare($sql);
      $stmt->bind_param("iii", $userId, $limit, $offset);
    } else {
      $sql = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY id DESC LIMIT ? OFFSET ?";
      $stmt = $this->db->prepare($sql);
      $stmt->bind_param("iii", $userId, $limit, $offset);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [];
  }

  public function markRead(int $id): bool {
    $sql = "UPDATE {$this->table} SET is_read = 1 WHERE id = ?";
    if (!$stmt = $this->db->prepare($sql)) return false;
    $stmt->bind_param("i", $id);
    return $stmt->execute();
  }

  public function delete(int $id): bool {
    $sql = "DELETE FROM {$this->table} WHERE id = ?";
    if (!$stmt = $this->db->prepare($sql)) return false;
    $stmt->bind_param("i", $id);
    return $stmt->execute();
  }
}
