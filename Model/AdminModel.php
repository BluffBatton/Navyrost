<?php
require_once __DIR__ . '/../functions/Database.php';

class AdminModel {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->connect();
    }

    public function createAdmin($data) {
        if (!$this->validateData($data)) {
            throw new Exception('Перевірте коректність введених даних.');
        }

        // Очистка кешу перед створенням
        $this->invalidateEmailCache($data['email']);

        if ($this->emailExists($data['email'])) {
            throw new Exception('Користувач з таким email вже існує.');
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['phonenumber'] = $this->encrypt($data['phonenumber']);
        $data['role'] = 'admin';

        $stmt = $this->pdo->prepare(
            "INSERT INTO users (firstname, lastname, email, phonenumber, password, role)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        return $stmt->execute([
            $data['firstname'],
            $data['lastname'],
            $data['email'],
            $data['phonenumber'],
            $data['password'],
            $data['role']
        ]);
    }

    private function validateData($data) {
        return filter_var($data['email'], FILTER_VALIDATE_EMAIL)
            && strlen($data['password']) >= 8
            && preg_match('/[a-z]/i', $data['password'])
            && preg_match('/\d/', $data['password'])
            && !empty($data['firstname'])
            && !empty($data['lastname']);
    }

    private function emailExists($email) {
        $cacheFile = __DIR__ . '/../cache/email_' . md5($email) . '.cache';

        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 300)) {
            return (bool) unserialize(file_get_contents($cacheFile));
        }

        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $exists = (bool) $stmt->fetch();

        file_put_contents($cacheFile, serialize($exists));
        return $exists;
    }

    private function invalidateEmailCache($email) {
        $cacheFile = __DIR__ . '/../cache/email_' . md5($email) . '.cache';
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }

    // Шифрування телефону
    private function encrypt($data) {
        $key = substr(hash('sha256', 'my_secret_key'), 0, 32);
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    // Розшифрування (за потреби)
    public function decrypt($data) {
        $key = substr(hash('sha256', 'my_secret_key'), 0, 32);
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }
}
