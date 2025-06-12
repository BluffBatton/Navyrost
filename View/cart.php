<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Language settings
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = $_SESSION['lang'] ?? 'ua';
$translations = include __DIR__ . '/../lang.php';
$t = $translations[$lang];

require_once __DIR__ . '/../Strategies/DiscountStrategyInterface.php';
require_once __DIR__ . '/../Strategies/QuantityDiscountStrategy.php';

$cart = $_SESSION['cart'] ?? [];
$total = 0;

$discountApplied = false;
$discountAmount = 0;
$newTotal = 0;
$discountText = '';

if (!empty($_SESSION['discount_strategy'])) {
    $strategyData = $_SESSION['discount_strategy'];
    $discountStrategy = new QuantityDiscountStrategy(
        $strategyData['minQuantity'],
        $strategyData['discountPercent']
    );

    foreach ($cart as $item) {
        $total += $item['details']['price'] * $item['quantity'];
    }

    $newTotal = $discountStrategy->applyDiscount($total);
    $discountAmount = $total - $newTotal;
    $discountText = $discountStrategy->getDiscountText();
    $discountApplied = true;
} else {
    foreach ($cart as $item) {
        $total += $item['details']['price'] * $item['quantity'];
    }
    $newTotal = $total;
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['cart_title'] ?></title>
    <link rel="stylesheet" href="<?= $base ?>/css/cartStyle.css">
    <style>
        .discount-info {
            display: flex;
            justify-content: space-between;
            color: #e63946;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .old-price {
            text-decoration: line-through;
            color: #6c757d;
            margin-right: 10px;
        }
    </style>
</head>
<body>
<?php require_once '../blocks/header.php'; ?>
<main class="cart-page">
    <h1><?= $t['your_cart'] ?></h1>
    <?php if (empty($cart)): ?>
        <p><?= $t['empty_cart'] ?> <a href="../index.php"><?= $t['back_to_shop'] ?></a>.</p>
    <?php else: ?>
        <div class="cart-header">
            <div><?= $t['product'] ?></div>
            <div><?= $t['price'] ?></div>
            <div><?= $t['quantity'] ?></div>
            <div><?= $t['total'] ?></div>
            <div></div>
        </div>
        <?php foreach ($cart as $key => $item): ?>
            <div class="cart-item">
                <div class="item-product">
                    <img src="<?= $base ?>/<?= htmlspecialchars($item['details']['image']) ?>" alt="<?= htmlspecialchars($item['details']['name']) ?>" class="thumb">
                        <span>
                        <?= htmlspecialchars($item['details']['name']) ?>
                        <?php if (!empty($item['details']['size'])): ?>
                            <small>(<?= $t['size'] ?>: <?= htmlspecialchars($item['details']['size']) ?>)</small>
                        <?php endif; ?>
                        </span>
                </div>
                <div class="item-price"><?= number_format($item['details']['price'], 0, ',', ' ') ?> <?= $t['currency'] ?></div>
                <div class="item-qty">
                    <form method="POST" action="/Controller/cart-handler.php" style="display:flex;align-items:center;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="key" value="<?= htmlspecialchars($key) ?>">
                        <button type="submit" name="quantity" value="<?= $item['quantity'] - 1 ?>" class="quantity-btn">-</button>
                        <span><?= $item['quantity'] ?></span>
                        <button type="submit" name="quantity" value="<?= $item['quantity'] + 1 ?>" class="quantity-btn">+</button>
                    </form>
                </div>
                <div class="item-total"><?= number_format($item['details']['price'] * $item['quantity'], 0, ',', ' ') ?> <?= $t['currency'] ?></div>
                <div class="item-remove">
                    <form method="POST" action="/Controller/cart-handler.php">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="key" value="<?= htmlspecialchars($key)?>">
                        <button type="submit" class="remove-btn"><?= $t['remove'] ?></button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="cart-footer">
            <?php if ($discountApplied): ?>
                <div class="discount-info">
                    <span><?= $discountText ?></span>
                    <span>-<?= number_format($discountAmount, 0, ',', ' ') ?> <?= $t['currency'] ?></span>
                </div>
                <div>
                    <span class="old-price"><?= number_format($total, 0, ',', ' ') ?> <?= $t['currency'] ?></span>
                    <strong><?= number_format($newTotal, 0, ',', ' ') ?> <?= $t['currency'] ?></strong>
                </div>
            <?php else: ?>
                <div><?= $t['total'] ?>: <strong><?= number_format($total, 0, ',', ' ') ?> <?= $t['currency'] ?></strong></div>
            <?php endif; ?>
                <form action="/checkout.php" method="POST">
                <button type="submit" class="btn-checkout"><?= $t['checkout'] ?></button>
                </form>
        </div>
    <?php endif; ?>
</main>
<?php require_once '../blocks/footer.php'; ?>
</body>
</html>