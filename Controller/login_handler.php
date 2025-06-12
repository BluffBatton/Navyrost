<?php
require_once __DIR__ . '/../Model/AuthModel.php';
require_once __DIR__ . '/../Model/UserModel.php';

// Начало сессии с дополнительными параметрами безопасности
session_start([
    'cookie_lifetime' => 86400, // 1 день
    'cookie_secure'   => true,  // Только через HTTPS
    'cookie_httponly' => true,  // Не доступен через JS
    'cookie_samesite' => 'Lax'  // Защита от CSRF
]);

// Загрузка переводов
$translations = include __DIR__ . '/../lang.php';
$language = $_SESSION['language'] ?? 'ua';
$t = $translations[$language] ?? $translations['ua'];

try {
    $authModel = new AuthModel();
    $userModel = new UserModel();

    // Если пользователь уже авторизован
    if ($authModel->checkUserSession()) {
        header('Location: /View/account.php');
        exit;
    }

    // Обработка формы входа
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['remember_me']);

    // Если отправили форму - логин
    if (!empty($email) && !empty($password)) {
        // Дополнительная валидация email перед использованием
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception($t['invalid_email']);
        }

        $user = $authModel->authenticate($email, $password);

        // Сохранение данных в сессии
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['firstname'] . ' ' . $user['lastname'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_first_name'] = $user['firstname'];
        $_SESSION['user_last_name'] = $user['lastname'];
        $_SESSION['user_phone'] = $user['phonenumber'];
        $_SESSION['user_role'] = $user['role'];

        // Сохранить email в cookie только если выбрано "Запомнить меня"
        if ($rememberMe) {
            $userModel->rememberUser($email);
        }

        // Перенаправление по роли
        $redirectUrl = match($user['role']) {
            'admin' => '/View/admin.php',
            default => $_SESSION['redirect_after_login'] ?? '/View/account.php'
        };

        if (isset($_SESSION['redirect_after_login'])) {
            unset($_SESSION['redirect_after_login']);
        }

        header("Location: $redirectUrl");
        exit;
    }

    // Если пользователь просто открыл страницу - перенаправить назад на login.php
    $rememberedEmail = $userModel->getRememberedUser();
    if ($rememberedEmail) {
        $_SESSION['remembered_email'] = $rememberedEmail;
    }
    
    header('Location: /View/login.php');
    exit;

} catch (Exception $e) {
    // Логирование ошибки для администратора
    error_log('Login error: ' . $e->getMessage());
    
    // Сообщение для пользователя
    $_SESSION['login_error'] = $t['login_failed'];
    header('Location: /View/login.php');
    exit;
}