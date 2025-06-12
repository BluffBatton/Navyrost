<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /View/login.php');
    exit;
}

require_once __DIR__ . '/../functions/Database.php';
$db = new Database();

// Збираємо і валідовуємо дані
$userId = $_SESSION['user_id'];
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');

// Можна додати додаткові перевірки, наприклад мінімальна довжина, формат телефону тощо
if (empty($firstName) || empty($lastName)) {
    $_SESSION['profile_error'] = 'Ім’я та прізвище не можуть бути порожніми.';
    header('Location: /View/account.php');
    exit;
}

try {
    // Оновлюємо БД
    $sql = "
        UPDATE users
           SET firstname   = :fn,
               lastname    = :ln,
               phonenumber = :ph
         WHERE id = :id
    ";
    $db->execQuery($sql, [
        'fn' => $firstName,
        'ln' => $lastName,
        'ph' => $phone,
        'id' => $userId
    ], false);

    // Оновлюємо сесію, щоби відобразити свіжі дані в header тощо
    $_SESSION['user_name'] = $firstName . ' ' . $lastName;
    $_SESSION['user_first_name'] = $firstName;
    $_SESSION['user_last_name'] = $lastName;
    $_SESSION['user_phone'] = $phone;

    $_SESSION['profile_success'] = 'Дані успішно збережено.';
} catch (Exception $e) {
    $_SESSION['profile_error'] = 'Не вдалося зберегти дані: ' . $e->getMessage();
}

// Повертаємося назад на форму
header('Location: /View/account.php');
exit;