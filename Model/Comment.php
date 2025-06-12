<?php
require_once __DIR__ . '/../functions/Database.php';

class Comment {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function create($productId, $userId, $text, $rating = null) {
        return $this->db->execQuery(
            "INSERT INTO comments (product_id, user_id, text, rating) 
             VALUES (?, ?, ?, ?)",
            [$productId, $userId, $text, $rating],
            false
        );
    }

    public function getByProduct($productId) {
        return $this->db->execQuery(
            "SELECT c.*, u.firstname, u.lastname 
             FROM comments c
             JOIN users u ON c.user_id = u.id
             WHERE c.product_id = ?
             ORDER BY c.created_at DESC",
            [$productId]
        );
    }

    public function delete($commentId, $userId, $isAdmin = false) {
        $query = "DELETE FROM comments WHERE id = ?";
        $params = [$commentId];

        if (!$isAdmin) {
            $query .= " AND user_id = ?";
            $params[] = $userId;
        }

        return $this->db->execQuery($query, $params, false);
    }

    public function getCountForProduct($productId) {
        $result = $this->db->execQuery(
            "SELECT COUNT(*) as count FROM comments 
             WHERE product_id = ?",
            [$productId]
        );
        return $result[0]['count'] ?? 0;
    }

    public function getAverageRating($productId) {
        $result = $this->db->execQuery(
            "SELECT AVG(rating) as avg_rating FROM comments 
             WHERE product_id = ? AND rating IS NOT NULL",
            [$productId]
        );
        return round($result[0]['avg_rating'] ?? 0, 1);
    }
}
