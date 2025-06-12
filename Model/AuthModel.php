<?php
// /Model/AuthModel.php
require_once __DIR__ . '/../functions/Database.php';

class AuthModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function authenticate($email, $password) {
        if (empty($email) || empty($password)) {
            throw new Exception('Будь ласка, заповніть всі поля.');
        }

        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $users = $this->db->execQuery($sql, ['email' => $email]);

        if (!$users || count($users) === 0) {
            throw new Exception('Користувача не знайдено.');
        }

        $user = $users[0];

        if (!password_verify($password, $user['password'])) {
            throw new Exception('Невірний пароль.');
        }

        return $user;
    }

    public function checkUserSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }
}