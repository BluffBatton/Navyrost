<?php
require_once '../Model/Product.php';

$genderFilter = isset($_GET['gender']) ? $_GET['gender'] : null;

$allProducts = Product::generateDummyData();

$filteredProducts = $allProducts;
if ($genderFilter) {
    $filteredProducts = array_filter($allProducts, function($product) use ($genderFilter) {
        return $product->gender === $genderFilter;
    });
}

$pageTitle = $genderFilter ? "ТОВАРИ ДЛЯ " . ($genderFilter === 'Чоловічій' ? 'ЧОЛОВІКІВ' : 'ЖІНОК') : "ВСІ ТОВАРИ";
$baseUrl = '/Navyrost';
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/catalogStyle.css">
    <style>
        .product-link {
            display: block;
            text-decoration: none;
            color: inherit;
        }
        .product-card {
            cursor: pointer;
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php require_once '../blocks/header.php'; ?>
    <div class="container">
        <aside class="filters">
            <div class="filter-section">
                <h3 class="filter-title">Тип товару</h3>
                <ul class="filter-options">
                    <li>
                        <input type="checkbox" id="type1" checked>
                        <label for="type1">Футболки Поло</label>
                    </li>
                </ul>
            </div>

            <div class="filter-section">
                <h3 class="filter-title">Бренд</h3>
                <ul class="filter-options">
                    <li>
                        <input type="checkbox" id="brand1" checked>
                        <label for="brand1">Bape</label>                        
                    </li>
                    <li>
                        <input type="checkbox" id="brand2" checked>
                        <label for="brand2">Comme des Garcons</label>                        
                    </li>
                    <li>
                        <input type="checkbox" id="brand3" checked>
                        <label for="brand3">Hugo Boss</label>                        
                    </li>
                    <li>
                        <input type="checkbox" id="brand4" checked>
                        <label for="brand4">Polo Ralph Lauren</label>                        
                    </li>
                    <li>
                        <input type="checkbox" id="brand5" checked>
                        <label for="brand5">Fred Perry</label>                        
                    </li>
                    <li>
                        <input type="checkbox" id="brand6" checked>
                        <label for="brand6">Lacoste</label>                        
                    </li>
                    <li>
                        <input type="checkbox" id="brand7" checked>
                        <label for="brand7">Kempa</label>                        
                    </li>
                    <li>
                        <input type="checkbox" id="brand8" checked>
                        <label for="brand8">Columbia</label>                        
                    </li>
                    <li>
                        <input type="checkbox" id="brand9" checked>
                        <label for="brand9">Nike</label>                        
                    </li>
                </ul>
            </div>

            <div class="filter-section">
                <h3 class="filter-title">Розмір</h3>
                <div class="size-grid">
                    <div class="size-option">S</div>
                    <div class="size-option">M</div>
                    <div class="size-option">L</div>
                    <div class="size-option">XL</div>
                    <div class="size-option">XXL</div>
                </div>
            </div>

            <div class="filter-section">
                <h3 class="filter-title">Колір</h3>
                <ul class="filter-options">
                    <li>
                        <input type="checkbox" id="color1">
                        <label for="color1">Чорний</label>
                    </li>
                    <li>
                        <input type="checkbox" id="color2">
                        <label for="color2">Білий</label>
                    </li>
                    <li>
                        <input type="checkbox" id="color3">
                        <label for="color3">Зелений</label>
                    </li>
                    <li>
                        <input type="checkbox" id="color4">
                        <label for="color4">Рожевий</label>
                    </li>
                    <li>
                        <input type="checkbox" id="color5">
                        <label for="color5">Синій</label>
                    </li>
                    <li>
                        <input type="checkbox" id="color6">
                        <label for="color6">Жовтий</label>
                    </li>
                    <li>
                        <input type="checkbox" id="color7">
                        <label for="color7">Червоний</label>
                    </li>
                    <li>
                        <input type="checkbox" id="color8">
                        <label for="color8">Сірий</label>
                    </li>
                </ul>
            </div>

            <div class="filter-section">
                <h3 class="filter-title">Ціна</h3>
                <div style="display: flex; justify-content: space-between;">
                    <input type="number" placeholder="від" style="width: 45%; padding: 5px;">
                    <input type="number" placeholder="до" style="width: 45%; padding: 5px;">
                </div>
            </div>
        </aside>

        <main class="products">
            <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>

            <div class="product-grid">
                <?php foreach ($filteredProducts as $product): ?>
                    <a href="<?= $baseUrl ?>/View/cloth.php?id=<?= $product->id ?>" class="product-link">
                        <div class="product-card">
                            <div class="product-image-catalog">
                                <img src="../<?= htmlspecialchars($product->image) ?>" alt="<?= htmlspecialchars($product->name) ?>">
                                <div class="size-overlay">Розміри: <?= implode(', ', $product->size) ?></div>
                            </div>
                            <div class="product-info">
                                <div class="product-code">Артикул: <?= strtoupper(substr(md5($product->id), 0, 8)) ?></div>
                                <div class="product-name"><?= htmlspecialchars($product->name) ?></div>
                                <div class="product-price"><?= number_format($product->price, 0, ',', ' ') ?> грн.</div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
    <?php require_once '../blocks/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script/script.js"></script>
</body>
</html>