<?php
//require_once __DIR__ . '/../database/database.php';
require_once '../functions/Database.php';
// $userId = $_GET['user_id'] ?? null;
// $moderatorId = 5;

// if (!$userId) {
//     echo json_encode(['error' => 'Missing user id']);
//     exit;
// }

// $db = new Database();
// $messages = $db->fetchAll(
//     "
//     SELECT
//         m.id,
//         m.sender_id,
//         m.receiver_id,
//         m.message,
//         m.sent_at,
//         u.firstname || ' ' || u.lastname AS sender_name
//     FROM chat_messages AS m
//     JOIN users AS u
//       ON u.id = m.sender_id
//     WHERE (m.sender_id   = :user AND m.receiver_id = :mod)
//        OR (m.sender_id   = :mod  AND m.receiver_id = :user)
//     ORDER BY m.sent_at ASC
//     ",
//     [
//         'user' => $userId,
//         'mod'  => $moderatorId
//     ]
// );

// echo json_encode($messages);
require_once 'functions/Database.php';

$userId = $_GET['user_id'] ?? null;
$moderatorId = 5;

if (!$userId) {
    echo json_encode(['error' => 'Missing user_id']);
    exit;
}

$db = new Database();
$messages = $db->execQuery("
    SELECT m.*, u.firstname || ' ' || u.lastname AS sender_name
    FROM chat_messages m
    JOIN users u ON m.sender_id = u.id
    WHERE (m.sender_id = :user AND m.receiver_id = :mod)
       OR (m.sender_id = :mod AND m.receiver_id = :user)
    ORDER BY m.sent_at ASC
", ['user' => $userId, 'mod' => $moderatorId]);

echo json_encode($messages);