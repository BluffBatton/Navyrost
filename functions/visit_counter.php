<?php
// functions/visit_counter.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/Database.php';
$db = new Database();

$date = date('Y-m-d');
$ip   = $_SERVER['REMOTE_ADDR'];

$db->execQuery(
    "INSERT OR IGNORE INTO statistics (date, hosts, hits, total) VALUES (?, 0, 0, 0)",
    [$date]
);

$db->execQuery(
    "UPDATE statistics SET hits = hits + 1, total = total + 1 WHERE date = ?",
    [$date]
);

$res = $db->execQuery(
    "SELECT 1 FROM daily_hosts WHERE ip = ? AND date = ?",
    [$ip, $date]
);
if (count($res) === 0) {
    $db->execQuery(
        "INSERT INTO daily_hosts (ip, date) VALUES (?, ?)",
        [$ip, $date]
    );
    $db->execQuery(
        "UPDATE statistics SET hosts = hosts + 1 WHERE date = ?",
        [$date]
    );
}

if (!empty($_SESSION['user_id'])) {
    $uid   = $_SESSION['user_id'];

    $stmt = $db->execQuery(
        "UPDATE user_statistics SET visits = visits + 1 WHERE user_id = ? AND date = ?",
        [$uid, $date]
    );
    if ($stmt->rowCount() === 0) {

        $db->execQuery(
            "INSERT INTO user_statistics (user_id, date, visits) VALUES (?, ?, 1)",
            [$uid, $date]
        );
    }
}
