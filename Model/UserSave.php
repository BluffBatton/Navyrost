<?php
require_once 'UserRepositoryInterface.php';

class UserSave implements UserRepositoryInterface {
    private PDO $pdo;

    public function __construct(Database $db) {
        $this->pdo = $db->connect();
    }

    public function save(array $userData): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (firstname, lastname, email, phonenumber, password)
            VALUES (:firstname, :lastname, :email, :phonenumber, :password)
        ");
        $stmt->execute([
            ':firstname' => $userData['firstname'],
            ':lastname' => $userData['lastname'],
            ':email' => $userData['email'],
            ':phonenumber' => $userData['phonenumber'],
            ':password' => $userData['password'],
        ]);
    }
}
