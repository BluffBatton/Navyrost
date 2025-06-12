<?php
require_once '../Model/Product.php';

$clean_params = [];
foreach ($_GET as $key => $value) {
    if (is_array($value)) {
        $clean_params[$key] = array_map('htmlspecialchars', $value);
    } else {
        $clean_params[$key] = htmlspecialchars($value);
    }
}

$availableBrands = Product::getAvailableBrands();
$availableCategories = Product::getAvailableCategories();
$availableSizes = Product::getAvailableSizes();
$availableColors = Product::getAvailableColors();

$filters = [];

if (!empty($clean_params['gender'])) {
    $filters['gender'] = $clean_params['gender'];
}

if (!empty($clean_params['brand'])) {
    $brands = is_array($clean_params['brand']) ? $clean_params['brand'] : [$clean_params['brand']];
    $filters['brands'] = implode(',', $brands);
}

if (!empty($clean_params['category'])) {
    $categories = is_array($clean_params['category']) ? $clean_params['category'] : [$clean_params['category']];
    $filters['categories'] = implode(',', $categories);
}

if (!empty($clean_params['size'])) {
    $sizes = is_array($clean_params['size']) ? $clean_params['size'] : [$clean_params['size']];
    $filters['sizes'] = implode(',', $sizes);
}

if (!empty($clean_params['color'])) {
    $colors = is_array($clean_params['color']) ? $clean_params['color'] : [$clean_params['color']];
    $filters['colors'] = implode(',', $colors);
}

if (!empty($clean_params['min_price']) && is_numeric($clean_params['min_price'])) {
    $filters['min_price'] = (float)$clean_params['min_price'];
}

if (!empty($clean_params['max_price']) && is_numeric($clean_params['max_price'])) {
    $filters['max_price'] = (float)$clean_params['max_price'];
}

$filteredProducts = Product::fetchAll($filters);

$pageTitle = !empty($filters['gender'])
    ? "ТОВАРИ ДЛЯ " . ($filters['gender'] === 'Чоловічій' ? 'ЧОЛОВІКІВ' : 'ЖІНОК')
    : "ВСІ ТОВАРИ";


error_log("Applied filters: " . print_r($filters, true));
error_log("Found products: " . count($filteredProducts));

require_once '../View/catalog.php';