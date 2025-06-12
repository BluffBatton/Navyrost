<?php
session_start();

require_once __DIR__ . '/../Model/Product.php';
require_once __DIR__ . '/../functions/Database.php';
require_once __DIR__ . '/../fabric/CommentFactory.php';

// Створюємо об'єкт Comment через фабрику
$commentModel = CommentFactory::create();

$colorCodes = [
    'білий' => '#FFFFFF',
    'чорний' => '#000000',
    'сірий' => '#808080',
    'синій' => '#0000FF',
    'червоний' => '#FF0000',
    'оливковий' => '#808000',
    'хакі' => '#C3B091',
];

// Обробка видалення коментаря
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    $commentId = (int)($_POST['comment_id'] ?? 0);
    $isAdmin = ($_SESSION['user_role'] ?? '') === 'admin';

    if (!isset($_SESSION['user_id'])) {
        $_SESSION['comment_error'] = 'Для видалення коментаря потрібно увійти';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    try {
        $commentModel->delete($commentId, $_SESSION['user_id'], $isAdmin);
        $_SESSION['comment_message'] = 'Коментар успішно видалено';
    } catch (Exception $e) {
        $_SESSION['comment_error'] = 'Помилка при видаленні коментаря';
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

// Отримання інформації про продукт
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = Product::findById($productId);

if (!$product) {
    header("HTTP/1.0 404 Not Found");
    exit("Product not found");
}


$comments = $commentModel->getByProduct($productId);
$averageRating = $commentModel->getAverageRating($productId);
$commentsCount = $commentModel->getCountForProduct($productId);

// Обробка додавання коментаря
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $text = trim($_POST['comment_text']);
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;

    if (!isset($_SESSION['user_id'])) {
        $_SESSION['comment_error'] = 'Для залишення відгуку потрібно увійти';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    if (!empty($text)) {
        try {
            $commentModel->create($productId, $_SESSION['user_id'], $text, $rating);
            $_SESSION['comment_success'] = 'Коментар успішно додано!';
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        } catch (Exception $e) {
            $_SESSION['comment_error'] = 'Помилка при додаванні коментаря: ' . $e->getMessage();
        }
    } else {
        $_SESSION['comment_error'] = 'Текст коментаря не може бути порожнім';
    }
}
