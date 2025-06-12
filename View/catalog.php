<?php
require_once '../Model/Product.php';
require_once '../Controller/catalog_handler.php';

// Инициализация языка
session_start();
$language = $_SESSION['language'] ?? 'ua';
$translations = include __DIR__ . '/../lang.php';
$t = $translations[$language] ?? $translations['ua'];

?>

<!DOCTYPE html>
<html lang="<?= $language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | <?= $t['catalog'] ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/catalogStyle.css">
    <link rel="stylesheet" href="../css/catalog.css">
</head>
<body>
<?php require_once '../blocks/header.php'; ?>
<div class="container">
    <aside class="filters">
        <form method="GET" action="">
            <?php if (!empty($_GET['gender'])): ?>
                <input type="hidden" name="gender" value="<?= htmlspecialchars($_GET['gender']) ?>">
            <?php endif; ?>

            <div class="filter-section">
                <h3 class="filter-title"><?= $t['product_type'] ?></h3>
                <ul class="filter-options">
                    <?php foreach ($availableCategories as $category): ?>
                        <li>
                            <input type="checkbox" id="category-<?= htmlspecialchars($category) ?>"
                                   name="category[]" value="<?= htmlspecialchars($category) ?>"
                                <?= (!empty($_GET['category']) && in_array($category, $_GET['category'])) ? 'checked' : '' ?>>
                            <label for="category-<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="filter-section">
                <h3 class="filter-title"><?= $t['brand'] ?></h3>
                <ul class="filter-options">
                    <?php foreach ($availableBrands as $brand): ?>
                        <li>
                            <input type="checkbox" id="brand-<?= htmlspecialchars($brand) ?>"
                                   name="brand[]" value="<?= htmlspecialchars($brand) ?>"
                                <?= (!empty($_GET['brand']) && in_array($brand, $_GET['brand'])) ? 'checked' : '' ?>>
                            <label for="brand-<?= htmlspecialchars($brand) ?>"><?= htmlspecialchars($brand) ?></label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="filter-section">
                <h3 class="filter-title"><?= $t['size'] ?></h3>
                <div class="size-grid">
                    <?php foreach ($availableSizes as $size): ?>
                        <div class="size-option <?= (!empty($_GET['size']) && in_array($size, $_GET['size'])) ? 'selected' : '' ?>">
                            <input type="checkbox" id="size-<?= htmlspecialchars($size) ?>"
                                   name="size[]" value="<?= htmlspecialchars($size) ?>"
                                   <?= (!empty($_GET['size']) && in_array($size, $_GET['size'])) ? 'checked' : '' ?>
                                   style="display: none;">
                            <label for="size-<?= htmlspecialchars($size) ?>" style="display: block; width: 100%; height: 100%;">
                                <?= htmlspecialchars($size) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="filter-section">
                <h3 class="filter-title"><?= $t['color'] ?></h3>
                <ul class="filter-options">
                    <?php foreach ($availableColors as $color): ?>
                        <li>
                            <input type="checkbox" id="color-<?= htmlspecialchars($color) ?>"
                                   name="color[]" value="<?= htmlspecialchars($color) ?>"
                                   <?= (!empty($_GET['color']) && in_array($color, $_GET['color'])) ? 'checked' : '' ?>>
                            <label for="color-<?= htmlspecialchars($color) ?>"><?= htmlspecialchars($color) ?></label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="filter-section">
                <h3 class="filter-title"><?= $t['price'] ?></h3>
                <div style="display: flex; justify-content: space-between;">
                    <input type="number" name="min_price" placeholder="<?= $t['min_price'] ?>"
                           value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>"
                           style="width: 45%; padding: 5px;">
                    <input type="number" name="max_price" placeholder="<?= $t['max_price'] ?>"
                           value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>"
                           style="width: 45%; padding: 5px;">
                </div>
            </div>

            <button type="submit" style="width: 100%; padding: 10px; background: #b09b7b; color: white; border: none; cursor: pointer;">
                <?= $t['apply_filters'] ?>
            </button>
            <a href="?" style="display: block; text-align: center; margin-top: 10px; color: #b09b7b;">
                <?= $t['reset_filters'] ?>
            </a>
        </form>
    </aside>

    <main class="products">
        <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>

        <div class="product-grid">
            <?php if (empty($filteredProducts)): ?>
                <p style="grid-column: 1 / -1; text-align: center;"><?= $t['no_products'] ?></p>
            <?php else: ?>
                <?php foreach ($filteredProducts as $product): ?>
                    <a href="/View/cloth.php?id=<?= $product->id ?>" class="product-link">
                        <div class="product-card">
                            <div class="product-image-catalog">
                                <img src="../<?= htmlspecialchars($product->image) ?>" alt="<?= htmlspecialchars($product->name) ?>">
                                <div class="size-overlay"><?= $t['sizes'] ?>: <?= implode(', ', $product->size) ?></div>
                            </div>
                            <div class="product-info">
                                <div class="product-code"><?= $t['article'] ?>: <?= strtoupper(substr(md5($product->id), 0, 8)) ?></div>
                                <div class="product-name"><?= htmlspecialchars($product->name) ?></div>
                                <div class="product-price"><?= number_format($product->price, 0, ',', ' ') ?> <?= $t['uah'] ?></div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>
<?php require_once '../blocks/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../script/script.js"></script>
</body>
</html>