<?php
// index.php
require_once __DIR__ . '/functions/visit_counter.php';

if (!class_exists('BasePage')) {
    require_once __DIR__ . '/View/pages.php';
}
$main = new MainPage(4);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userVisits = null;
if (!empty($_SESSION['user_id'])) {
    require_once __DIR__ . '/functions/Database.php';
    $db = new Database();
    $row = $db->execQuery(
        "SELECT visits FROM user_statistics WHERE user_id = ? AND date = ?",
        [$_SESSION['user_id'], date('Y-m-d')]
    );
    $userVisits = $row[0]['visits'] ?? 0;
}

$main->render();
