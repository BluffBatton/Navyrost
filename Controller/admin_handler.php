<?php
// /Controller/admin_handler.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

require_once '../functions/Database.php';
require_once '../Model/Product.php';
require_once '../functions/File.php';

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    try {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $gender = $_POST['gender'];
        $brand = $_POST['brand'];
        $categories = $_POST['categories'] ?? [];
        $colors = $_POST['colors'] ?? [];
        $sizes = $_POST['sizes'] ?? [];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($fileInfo, $_FILES['image']['tmp_name']);
            finfo_close($fileInfo);
            
            if (!in_array($mimeType, $allowedTypes)) {
                throw new Exception("Дозволені лише зображення у форматах JPG, PNG, GIF або WEBP!");
            }
            
            $imagePath = File_upload('image');
        } else {
            $imagePath = '';
        }
        
        $pdo = $db->connect();
        $pdo->beginTransaction();
        
        try {
            // Добавляем товар
            $stmt = $pdo->prepare(
                "INSERT INTO products (name, price, image, description, gender, brand) 
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$name, $price, $imagePath, $description, $gender, $brand]);
            $productId = $pdo->lastInsertId();
            
            // Добавляем категории
            if (!empty($categories)) {
                $placeholders = implode(',', array_fill(0, count($categories), "(?, ?)"));
                $values = [];
                foreach ($categories as $categoryId) {
                    $values[] = $productId;
                    $values[] = $categoryId;
                }
                $pdo->prepare("INSERT INTO product_categories (product_id, category_id) VALUES $placeholders")
                   ->execute($values);
            }
            
            // Добавляем цвета
            if (!empty($colors)) {
                $placeholders = implode(',', array_fill(0, count($colors), "(?, ?)"));
                $values = [];
                foreach ($colors as $colorId) {
                    $values[] = $productId;
                    $values[] = $colorId;
                }
                $pdo->prepare("INSERT INTO product_colors (product_id, color_id) VALUES $placeholders")
                   ->execute($values);
            }
            
            // Добавляем размеры
            if (!empty($sizes)) {
                $placeholders = implode(',', array_fill(0, count($sizes), "(?, ?)"));
                $values = [];
                foreach ($sizes as $sizeId) {
                    $values[] = $productId;
                    $values[] = $sizeId;
                }
                $pdo->prepare("INSERT INTO product_sizes (product_id, size_id) VALUES $placeholders")
                   ->execute($values);
            }
            
            // Создаем уведомления для всех пользователей
            $users = $pdo->query("SELECT id FROM users WHERE role = 'user'")->fetchAll();
            $message = "Новий товар: $name ($brand) додано до асортименту!";
            
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            foreach ($users as $user) {
                $stmt->execute([$user['id'], $message]);
            }
            
            $pdo->commit();
            $_SESSION['admin_message'] = 'Товар успішно додано! Всі користувачі отримали сповіщення.';
            header('Location: admin.php');
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    } catch (Exception $e) {
        $_SESSION['admin_error'] = 'Помилка: ' . $e->getMessage();
        header('Location: admin.php');
        exit();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    try {
        $productId = $_POST['product_id'];
        $pdo = $db->connect();
        
        $pdo->beginTransaction();
        
        try {

            $pdo->prepare("DELETE FROM product_categories WHERE product_id = ?")->execute([$productId]);
            $pdo->prepare("DELETE FROM product_colors WHERE product_id = ?")->execute([$productId]);
            $pdo->prepare("DELETE FROM product_sizes WHERE product_id = ?")->execute([$productId]);
            
            $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();
            
            if ($product && $product['image'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $product['image'])) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $product['image']);
            }
            
            $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$productId]);
            
            $pdo->commit();
            
            $_SESSION['admin_message'] = 'Товар успішно видалено!';
            header('Location: admin.php');
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    } catch (Exception $e) {
        $_SESSION['admin_error'] = 'Помилка при видаленні товару: ' . $e->getMessage();
        header('Location: admin.php');
        exit();
    }
}

$products = $db->execQuery("SELECT * FROM products ORDER BY id DESC LIMIT 50");

$categories = $db->execQuery("SELECT * FROM categories");
$colors = $db->execQuery("SELECT * FROM colors");
$sizes = $db->execQuery("SELECT * FROM sizes");

$stats = $db->execQuery("
    SELECT 
        (SELECT COUNT(*) FROM products) as total_products,
        (SELECT COUNT(*) FROM users) as total_users,
        (SELECT COUNT(*) FROM users WHERE role = 'admin') as total_admins
")[0];
?>