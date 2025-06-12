<?php
// ./Controller/cart-handler.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Strategies/DiscountStrategyInterface.php';
require_once __DIR__ . '/../Strategies/QuantityDiscountStrategy.php';

$cart = $_SESSION['cart'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $key = $_POST['key'] ?? '';

    switch ($action) {
        case 'add':
            // Для додавання беремо product_id і size
            $productId = (int)($_POST['id'] ?? 0);
            $size      = trim($_POST['size'] ?? '');
            $key       = $productId . ':' . $size;

            $details = [
                'id'    => $productId,
                'name'  => $_POST['name'],
                'price' => (float)$_POST['price'],
                'image' => $_POST['image'],
                'size'  => $size
            ];

            if (!isset($cart[$key])) {
                $cart[$key] = ['quantity' => 1, 'details' => $details];
            } else {
                $cart[$key]['quantity']++;
            }
            break;

        case 'update':
            // Для update/remove беремо ключ
            $key = $_POST['key'] ?? '';
            $qty = max(1, (int)($_POST['quantity'] ?? 1));
            if (isset($cart[$key])) {
                $cart[$key]['quantity'] = $qty;
            }
            break;

        case 'remove':
            $key = $_POST['key'] ?? '';
            unset($cart[$key]);
            break;
    }

    $_SESSION['cart'] = $cart;

    $totalQuantity = 0;
    foreach ($cart as $item) {
        $totalQuantity += $item['quantity'];
    }

    if ($totalQuantity >= 3) {
        $_SESSION['discount_strategy'] = [
            'class' => 'QuantityDiscountStrategy',
            'minQuantity' => 3,
            'discountPercent' => 15
        ];
    } else {
        unset($_SESSION['discount_strategy']);
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}