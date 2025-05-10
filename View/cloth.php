<?php
require_once '../Model/Product.php'; 

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$products = Product::generateDummyData();

$product = null;
foreach ($products as $item) {
    if ($item->id == $productId) {
        $product = $item;
        break;
    }
}

if (!$product) {
    header("HTTP/1.0 404 Not Found");
    exit("Problem");
}

$baseUrl = '/Navyrost';
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($product->name) ?> | ALL STARS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/clothStyle.css" />
</head>
<body>
    <?php require_once '../blocks/header.php'; ?>
    <main class="product-page">
        <div class="product-container">
            <div class="product-gallery">
                <div class="main-image">
                    <img src="<?= $baseUrl ?>/<?= htmlspecialchars($product->image) ?>" alt="<?= htmlspecialchars($product->name) ?>" id="main-image" />
                </div>
                <div class="thumbnail-container">
    
                    <div class="thumbnail active" onclick="changeImage('<?= $baseUrl ?>/<?= htmlspecialchars($product->image) ?>')">
                        <img src="<?= $baseUrl ?>/<?= htmlspecialchars($product->image) ?>" alt="Мініатюра 1" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                  
                </div>
            </div>
            
            <div class="product-info">
                <h1 class="product-title"><?= htmlspecialchars($product->name) ?></h1>
                
                <div class="product-price"><?= number_format($product->price, 0, ',', ' ') ?> грн.</div>
                
                <div class="delivery-info">
                    <div><i class="fas fa-check"></i> Безкоштовна доставка Новою поштою</div>
                    <div><i class="far fa-heart"></i> Обране</div>
                </div>
                
                <div class="size-selection">
                    <h3>Оберіть розмір</h3>
                    <table class="size-table">
                        <tr>
                            <th>U.S.</th>
                            <th>UA</th>
                            <th>EU</th>
                            <th>UK</th>
                            <th>CM</th>
                        </tr>
                        <?php foreach ($product->size as $index => $size): ?>
                        <tr>
                            <td class="<?= $index === 0 ? 'selected' : '' ?>"><?= htmlspecialchars($size) ?></td>
                           
                            <td><?= htmlspecialchars($size) ?></td>
                            <td><?= htmlspecialchars($size) ?></td>
                            <td><?= htmlspecialchars($size) ?></td>
                            <td><?= htmlspecialchars($size) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                
                <div class="quantity-selector">
                    <span>КІЛЬКІСТЬ:</span>
                    <button class="quantity-btn">-</button>
                    <span>1</span>
                    <button class="quantity-btn">+</button>
                </div>

                <form method="POST" action="/Navyrost/blocks/cart.php">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id"     value="<?=$product->id?>">
                    <input type="hidden" name="name"   value="<?=htmlspecialchars($product->name)?>">
                    <input type="hidden" name="price"  value="<?=$product->price?>">
                    <input type="hidden" name="image"  value="<?=$product->image?>">
                    <button type="submit" class="add-to-cart">ДОДАТИ В КОШИК</button>
                </form>
                
                <div class="additional-info">
                    <div><strong>Наявність у магазинах</strong></div>
                    <div><strong>Доставка</strong></div>
                    <div><strong>Повернення</strong></div>
                    
                    <div class="info-links">
                        <a href="#">Таблиця розмірів</a>
                        <a href="#">Оплата</a>
                        <a href="#">Написати відгук</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="product-details">
            <h3><?= htmlspecialchars($product->name) ?></h3>
            <p><?= htmlspecialchars($product->description) ?></p>
            
            <h4>Характеристики:</h4>
            <ul>
                <li><strong>Бренд:</strong> <?= htmlspecialchars($product->brand) ?></li>
                <li><strong>Категорія:</strong> <?= htmlspecialchars($product->category) ?></li>
                <li><strong>Стать:</strong> <?= htmlspecialchars($product->gender) ?></li>
                <li><strong>Колір:</strong> <?= htmlspecialchars($product->color) ?></li>
                <li><strong>Розміри:</strong> <?= htmlspecialchars(implode(', ', $product->size)) ?></li>
            </ul>
        </div>
    </main>
    <?php require_once '../blocks/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $baseUrl ?>/script/script.js"></script>
</body>
</html>