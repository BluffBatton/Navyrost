<?php

require_once "./functions/Database.php";

class Order {
    public static function getByUser(int $userId): array {
        $db = new Database();
        return $db->execQuery(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
    }

    public static function getById(int $orderId, int $userId): ?array {
        $db = new Database();
        $rows = $db->execQuery(
            "SELECT * FROM orders WHERE id = ? AND user_id = ?",
            [$orderId, $userId]
        );
        return $rows[0] ?? null;
    }
}
