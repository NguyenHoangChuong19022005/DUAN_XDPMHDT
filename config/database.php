<?php
class Database {
  private $host = "localhost";
  private $db_name = "edumatch_schema"; // đúng với tên DB trong phpMyAdmin
  private $username = "root";
  private $password = "";

  public function getConnection() {
    $conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
    if ($conn->connect_error) {
      die("❌ Lỗi kết nối: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
  }
}
?>
