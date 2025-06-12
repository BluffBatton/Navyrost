<?php
session_start();
require_once __DIR__ . '/../Model/AdminModel.php';

try {
    $data = [
        'email' => trim($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'firstname' => trim($_POST['firstname'] ?? ''),
        'lastname' => trim($_POST['lastname'] ?? ''),
        'phonenumber' => trim($_POST['phonenumber'] ?? '')
    ];

    $adminModel = new AdminModel();
    $adminModel->createAdmin($data);

    $_SESSION['register_success'] = 'Адмін успішно створений.';
} catch (Exception $e) {
    $_SESSION['register_error'] = $e->getMessage();
}

header('Location: /View/admin_signup.php');
exit;
