<?php
require_once __DIR__ . '/../config/database.php';

class Provider {
    private $conn;
    private $table_name = "providers";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, organization=:organization";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':organization', $data['organization']);
        return $stmt->execute();
    }
}
?>