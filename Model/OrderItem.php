<?php
require_once "./functions/Database.php";

class OrderItem {
    public static function getByOrder(int $orderId): array {
        $db = new Database();
        return $db->execQuery(
            "SELECT * FROM order_items WHERE order_id = ?",
            [$orderId]
        );
    }
}
