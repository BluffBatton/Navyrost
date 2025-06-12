<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Model/UserModel.php';

try {
    $userData = [
        'firstname' => trim($_POST['firstname'] ?? ''),
        'lastname' => trim($_POST['lastname'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phonenumber' => trim($_POST['phonenumber'] ?? ''),
        'password' => $_POST['password'] ?? ''
    ];

    $userModel = new UserModel();
    $userId = $userModel->registerUser($userData);

    $_SESSION['user_id'] = $userId;
    $_SESSION['user_email'] = $userData['email'];
    $_SESSION['user_name'] = $userData['firstname'] . ' ' . $userData['lastname'];
    $_SESSION['user_first_name'] = $userData['firstname'];
    $_SESSION['user_last_name'] = $userData['lastname'];
    $_SESSION['user_phone'] = $userData['phonenumber'];

    header('Location: ../View/account.php');
    exit;

} catch (Exception $e) {
    $_SESSION['register_error'] = $e->getMessage();
    header('Location: ../View/signup.php');
    exit;
}