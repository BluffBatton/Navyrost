<?php
// cart.php — кладём в blocks/cart.php или в корень, и подключаем в BasePage::Header()

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Обработка POST-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    switch ($action) {
        case 'add':
            $details = [
                'name'  => $_POST['name'],
                'price' => (float)$_POST['price'],
                'image' => $_POST['image'],
            ];
            if (!isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id] = ['quantity'=>1, 'details'=>$details];
            } else {
                $_SESSION['cart'][$id]['quantity']++;
            }
            break;

        case 'update':
            $qty = max(1, (int)($_POST['quantity'] ?? 1));
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['quantity'] = $qty;
            }
            break;

        case 'remove':
            unset($_SESSION['cart'][$id]);
            break;
    }

    // После POST — редирект, чтобы форма не отправлялась повторно
    header('Location: '.$_SERVER['REQUEST_URI']);
    exit;
}

// Далее — отрисовка самого оверлея
$cart = $_SESSION['cart'] ?? [];
$total = 0;
$baseUrl = '/Navyrost';
?>
<div class="cart-modal" id="cart-modal">
    <div class="cart-content">
        <span class="close-modal">&times;</span>
        <h2>КОШИК</h2>
        <div class="cart-header">
            <div class="header-item product-header">ТОВАР</div>
            <div class="header-item">ЦІНА</div>
            <div class="header-item">КІЛ-КЬ</div>
            <div class="header-item">СУМА</div>
            <div class="header-item"></div>
        </div>

        <?php foreach($cart as $id => $item):
            $line = $item['details']['price'] * $item['quantity'];
            $total += $line;
            ?>
            <div class="cart-item">
                <div class="item-product">
                    <img src="<?= $baseUrl ?>/<?=htmlspecialchars($item['details']['image'])?>" alt="" class="product-image">
                    <div class="product-info">
                        <div class="product-name"><?=htmlspecialchars($item['details']['name'])?></div>
                    </div>
                </div>
                <div class="item-price"><?=number_format($item['details']['price'],0,',',' ')?> грн.</div>

                <!-- Обновление количества -->
                <div class="item-quantity">
                    <form method="POST" style="display:flex;align-items:center;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id"     value="<?=$id?>">
                        <button type="submit" name="quantity" value="<?=$item['quantity']-1?>" class="quantity-btn">-</button>
                        <span><?=$item['quantity']?></span>
                        <button type="submit" name="quantity" value="<?=$item['quantity']+1?>" class="quantity-btn">+</button>
                    </form>
                </div>

                <div class="item-total"><?=number_format($line,0,',',' ')?> грн.</div>

                <!-- Удаление -->
                <div class="item-remove">
                    <form method="POST">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="id"     value="<?=$id?>">
                        <button type="submit" class="remove-btn">ВИДАЛИТИ</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="cart-footer">
            <div class="total-sum">
                <span>Загалом:</span>
                <span class="total-price"><?=number_format($total,0,',',' ')?> грн.</span>
            </div>
            <form method="POST" action="/Navyrost/checkout.php">
                <button type="submit" class="checkout-btn">ОФОРМИТИ ЗАМОВЛЕННЯ</button>
            </form>
        </div>
    </div>
</div>
