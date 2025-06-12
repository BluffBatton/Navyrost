<?php
// Подключение языка
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'ua';
$translations = include __DIR__ . '/../lang.php';
$t = $translations[$lang];

// Проверка слайдера
if (!isset($sliderImages) || !is_array($sliderImages)) {
    $sliderImages = [];
}
 
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $t['title'] ?? 'Головна' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/mainStyle.css" />
</head>
<body>
<main>
    <div class="slider-container">
        <div id="shoeCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner-container">
                <div class="carousel-inner">
                    <?php foreach ($sliderImages as $index => $file): ?>
                        <div class="carousel-item<?= $index === 0 ? ' active' : '' ?>">
                            <div class="image-container">
                                <img
                                    src="/pic/<?= htmlspecialchars($file) ?>"
                                    class="d-block centered-image"
                                    alt="Slider Image <?= $index + 1 ?>"
                                />
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#shoeCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden"><?= $t['prev'] ?? 'Previous' ?></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#shoeCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden"><?= $t['next'] ?? 'Next' ?></span>
                </button>
            </div>

            <div class="fixed-images">
                <img src="/pic/fixed1.png" alt="VIP Brand" class="fixed-image" />
                <img src="/pic/fixed2.png" alt="DIR Brand" class="fixed-image" />
                <img src="/pic/fixed3.png" alt="New Balance" class="fixed-image" />
            </div>
        </div>
    </div>

    <div class="container series-container">
        <div class="header"><h1><?= $t['new_arrivals'] ?? 'Наші новинки' ?></h1></div>
        <div class="row brand-section">
            <?php foreach ($this->products as $item): ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-4">
                    <div class="series-card">
                        <div class="series-img-container">
                            <img src="<?= htmlspecialchars($item->image) ?>"
                                 alt="<?= htmlspecialchars($item->name) ?>"
                                 class="series-img" />
                        </div>
                        <div class="series-info">
                            <h3 class="series-name"><?= htmlspecialchars($item->name) ?></h3>
                            <p class="series-desc"><?= htmlspecialchars($item->description) ?></p>
                            <div class="text-center">
                                <h5 class="text-price">
                                    <?= number_format($item->price, 0, ',', ' ') ?> грн.
                                </h5>
                                <a href="/View/cloth.php?id=<?= $item->id ?>" class="btn-more">
                                    <?= $t['details'] ?? 'Детальніше' ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/script/script.js"></script>
</body>
</html>
