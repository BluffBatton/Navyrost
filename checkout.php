<?php
// checkout.php
session_start();

// 1) Перевіряємо, що користувач залогінений та корзина непуста
if (empty($_SESSION['user_id'])) {
    header('Location: /View/login.php');
    exit;
}
if (empty($_SESSION['cart'])) {
    header('Location: /View/catalog.php');
    exit;
}

// 2) Підключаємо БД
require_once __DIR__ . '/functions/Database.php';
$db = new Database();

// 3) Розраховуємо суму, як у вашому кошику (тут без знижок для спрощення)
$cart = $_SESSION['cart'];
$total = 0;
foreach ($cart as $item) {
    $total += $item['details']['price'] * $item['quantity'];
}

// 4) Створюємо новий запис у orders
$userId    = $_SESSION['user_id'];
$orderUuid = uniqid('order_');
$db->execQuery(
    "INSERT INTO orders (user_id, order_id, total_amount) VALUES (?, ?, ?)",
    [$userId, $orderUuid, $total]
);

// 5) Дізнаємося внутрішній ID замовлення
$orderDbId = $db->getLastInsertId();

// 6) Зберігаємо кожну позицію
foreach ($cart as $productId => $item) {
    $db->execQuery(
        "INSERT INTO order_items (order_id, product_id, name, price, quantity, size)
        VALUES (?, ?, ?, ?, ?, ?)",
        [
        $orderDbId,
        $productId,
        $item['details']['name'],
        $item['details']['price'],
        $item['quantity'],
        $item['details']['size']  // <— тут обраний розмір
        ]
    );
}

// 7) Очищаємо корзину
unset($_SESSION['cart'], $_SESSION['discount_strategy']);

// 8) Перенаправляємо користувача на «Дякуємо» чи сторінку історії
header('Location: /View/account.php');
exit;
