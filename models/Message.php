<?php
require_once __DIR__ . '/../config/database.php';

class Message {
    private $conn;
    private $table_name = "messages";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function send($from, $to, $msg) {
        $query = "INSERT INTO " . $this->table_name . " SET from_user_id=:from, to_user_id=:to, message=:msg";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':from', $from);
        $stmt->bindParam(':to', $to);
        $stmt->bindParam(':msg', $msg);
        return $stmt->execute();
    }

    public function getByUsers($user1, $user2) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE (from_user_id = :u1 AND to_user_id = :u2) OR (from_user_id = :u2 AND to_user_id = :u1) ORDER BY created_at";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':u1', $user1);
        $stmt->bindParam(':u2', $user2);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>