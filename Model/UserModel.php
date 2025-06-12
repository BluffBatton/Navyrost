<?php
require_once __DIR__ . '/../functions/Database.php';

class UserModel {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->connect();
    }

    // Основні методи
    public function registerUser($userData) {
        $this->validateUserData($userData);
        
        if ($this->emailExists($userData['email'])) {
            throw new Exception('Користувач з такою поштою вже існує.');
        }

        $userId = $this->saveUser($userData);
        $this->logRegistration($userData['email']);

        return $userId;
    }

    // Валідація
    private function validateUserData($data) {
        // Перевірка російських доменів
        if (preg_match('/\.ru$/i', $data['email'])) {
            throw new Exception('Вхід підорам заборонений.');
        }

        // Валідація телефону
        if (!empty($data['phonenumber']) && 
            !preg_match('/^(\+380|380|0)(39|50|63|66|67|68|73|91|92|93|94|95|96|97|98|99)\d{7}$/', $data['phonenumber'])) {
            throw new Exception('Будь ласка, введіть коректний номер телефону українського оператора.');
        }

        // Основна валідація
        if (empty($data['firstname']) || empty($data['lastname']) ||
            !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $data['email']) || 
            !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{12,}$/', $data['password'])
        ) {
            throw new Exception('Будь ласка, перевірте правильність введених даних.');
        }
    }

    // Робота з базою даних
    private function emailExists($email) {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return (bool)$stmt->fetch();
    }

    private function saveUser($userData) {
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare(
            "INSERT INTO users (firstname, lastname, email, phonenumber, password) 
            VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->execute([
            $userData['firstname'],
            $userData['lastname'],
            $userData['email'],
            $userData['phonenumber'],
            $userData['password']
        ]);

        return $this->pdo->lastInsertId();
    }

    // Логування
    private function logRegistration($email) {
        // Тут може бути запис у файл, базу даних або іншу систему логування
        error_log("Користувач зареєстрований: " . $email);
    }

    // Інші корисні методи
    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ==========================
    // Робота з cookie
    // ==========================

    // Зберегти email користувача у cookie на 7 днів
    public function rememberUser($email) {
        setcookie("remembered_user", $email, time() + 60 * 60 * 24 * 7, "/");
    }

    // Отримати email з cookie
    public function getRememberedUser() {
        return isset($_COOKIE['remembered_user']) ? $_COOKIE['remembered_user'] : null;
    }

    // Очистити cookie
    public function forgetUser() {
        setcookie("remembered_user", "", time() - 3600, "/");
    }
}
