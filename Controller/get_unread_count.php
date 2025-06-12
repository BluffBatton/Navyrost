<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit();
}

require_once '../functions/Database.php';
$db = new Database();

$result = $db->execQuery("
    SELECT COUNT(*) as count FROM notifications 
    WHERE user_id = ? AND is_read = 0
", [$_SESSION['user_id']]);

echo json_encode(['count' => $result[0]['count'] ?? 0]);