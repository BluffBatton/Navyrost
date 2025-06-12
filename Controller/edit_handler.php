<?php
session_start();

// Проверка авторизации и прав администратора
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

require_once '../functions/Database.php';
require_once '../functions/File.php';

$db = new Database();

// Получаем ID товара
$productId = $_GET['id'] ?? 0;

// Загружаем данные товара
$product = $db->execQuery("SELECT * FROM products WHERE id = ?", [$productId]);
if (empty($product)) {
    $_SESSION['admin_error'] = 'Товар не знайдено';
    header('Location: admin.php');
    exit();
}
$product = $product[0];

// Загружаем связанные данные
$productCategories = $db->execQuery("SELECT category_id FROM product_categories WHERE product_id = ?", [$productId]);
$productCategories = array_column($productCategories, 'category_id');

$productColors = $db->execQuery("SELECT color_id FROM product_colors WHERE product_id = ?", [$productId]);
$productColors = array_column($productColors, 'color_id');

$productSizes = $db->execQuery("SELECT size_id FROM product_sizes WHERE product_id = ?", [$productId]);
$productSizes = array_column($productSizes, 'size_id');

// Загружаем списки для формы
$categories = $db->execQuery("SELECT * FROM categories");
$colors = $db->execQuery("SELECT * FROM colors");
$sizes = $db->execQuery("SELECT * FROM sizes");

// Обработка формы обновления
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    try {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $gender = $_POST['gender'];
        $brand = $_POST['brand'];
        $categories = $_POST['categories'] ?? [];
        $colors = $_POST['colors'] ?? [];
        $sizes = $_POST['sizes'] ?? [];
        
        // Обработка изображения
        $imagePath = $product['image']; // По умолчанию оставляем старое
        if (!empty($_FILES['image']['name'])) {
            // Удаляем старое изображение, если оно не дефолтное
            if ($product['image'] && !str_contains($product['image'], 'placeholder.jpg')) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $product['image']);
            }
            $imagePath = File_upload('image') ?? $product['image'];
        }
        
        // Обновляем товар
        $db->execQuery(
            "UPDATE products SET 
                name = ?, 
                price = ?, 
                image = ?, 
                description = ?, 
                gender = ?, 
                brand = ? 
             WHERE id = ?",
            [$name, $price, $imagePath, $description, $gender, $brand, $productId],
            false
        );
        
        // Обновляем связи
        $db->execQuery("DELETE FROM product_categories WHERE product_id = ?", [$productId], false);
        foreach ($categories as $categoryId) {
            $db->execQuery(
                "INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)",
                [$productId, $categoryId],
                false
            );
        }
        
        $db->execQuery("DELETE FROM product_colors WHERE product_id = ?", [$productId], false);
        foreach ($colors as $colorId) {
            $db->execQuery(
                "INSERT INTO product_colors (product_id, color_id) VALUES (?, ?)",
                [$productId, $colorId],
                false
            );
        }
        
        $db->execQuery("DELETE FROM product_sizes WHERE product_id = ?", [$productId], false);
        foreach ($sizes as $sizeId) {
            $db->execQuery(
                "INSERT INTO product_sizes (product_id, size_id) VALUES (?, ?)",
                [$productId, $sizeId],
                false
            );
        }
        
        $_SESSION['admin_message'] = 'Товар успішно оновлено!';
        header('Location: admin.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['admin_error'] = 'Помилка при оновленні: ' . $e->getMessage();
    }
}

?>
