<?php
require_once __DIR__ . '/../config/database.php';

class Premium {
    private $conn;
    private $table_name = "premiums";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function subscribe($user_id, $plan, $stripe_id) {
        $end_date = date('Y-m-d', strtotime('+1 month'));
        $query = "INSERT INTO " . $this->table_name . " SET user_id=:user_id, plan=:plan, stripe_id=:stripe_id, end_date=:end_date";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':plan', $plan);
        $stmt->bindParam(':stripe_id', $stripe_id);
        $stmt->bindParam(':end_date', $end_date);
        return $stmt->execute();
    }

    public function isPremium($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id AND status = 'active' AND end_date > CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>