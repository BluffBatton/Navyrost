<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';  // LiqPay SDK

// use LiqPay\LiqPay;

// Ваші ключі
$public_key  = 'ВАШ_PUBLIC_KEY';
$private_key = 'ВАШ_PRIVATE_KEY';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    die('Кошик порожній');
}

// Розрахунок суми з урахуванням знижки
$total = 0;
foreach ($cart as $item) {
    $total += $item['details']['price'] * $item['quantity'];
}
if (isset($_SESSION['discount_strategy'])) {
    // відновлюємо стратегію, як у вашому коді
    $strategyData = $_SESSION['discount_strategy'];
    $strategy = new QuantityDiscountStrategy(
        $strategyData['minQuantity'],
        $strategyData['discountPercent']
    );
    $total = $strategy->applyDiscount($total);
}

// Створюємо LiqPay
$liqpay = new LiqPay($public_key, $private_key);

// Параметри платежу
$order_id    = uniqid('order_');
$description = 'Оплата замовлення ' . $order_id;
$params = [
    'action'     => 'pay',
    'amount'     => $total,
    'currency'   => 'UAH',
    'description'=> $description,
    'order_id'   => $order_id,
    'version'    => '3',
    'language'   => 'uk',
    // на вашому домені:
    'server_url' => 'https://your-domain.com/liqpay-callback.php',
    'result_url' => 'https://your-domain.com/thank-you.php'
];
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Оплата замовлення</title>
</head>
<body>
  <h1>Оплата: <?= number_format($total,0,',',' ') ?> грн</h1>
  <?= $liqpay->cnb_form($params) ?>
</body>
</html>
