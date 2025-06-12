<?php
require_once __DIR__ . '/../Model/IpValidatorModel.php';

session_start();

$model = new IpValidatorModel();
$baseUrl = '/Navyrost';

// Перевірка доступу
if (!$model->checkAdminAccess()) {
    header("Location: $baseUrl/View/login.php");
    exit;
}

// Обробка POST-запиту
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_POST['ip'] ?? '';
    $result = $model->validate($ip);
    $_SESSION['ip_validation_result'] = $result;
    header("Location: $baseUrl/View/ip_validator.php");
    exit;
}

// Відображення View
require_once __DIR__ . '/../View/ip_validator.php';