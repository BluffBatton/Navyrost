<?php
// Після успішної оплати
session_start();
require_once __DIR__ . '/../functions/Database.php';
$db = new Database();

// 1) Вставити запис у orders
$userId = $_SESSION['user_id'];
$orderId = $response['order_id'];    // ваш LiqPay order_id
$total   = $response['amount'];

$db->execQuery(
  "INSERT INTO orders (user_id, order_id, total_amount) VALUES (?, ?, ?)",
  [$userId, $orderId, $total]
);
// Отримаємо згенерований PK
$orderDbId = $db->getLastInsertId();

// 2) Вставити позиції з кошика в order_items
$cart = $_SESSION['cart'] ?? [];
foreach ($cart as $prodId => $item) {
    $db->execQuery(
      "INSERT INTO order_items (order_id, product_id, name, price, quantity) VALUES (?, ?, ?, ?, ?)",
      [
        $orderDbId,
        $prodId,
        $item['details']['name'],
        $item['details']['price'],
        $item['quantity']
      ]
    );
}

// 3) Почистити кошик
unset($_SESSION['cart'], $_SESSION['discount_strategy']);
