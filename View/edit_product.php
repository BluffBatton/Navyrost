<?php
require_once '../Controller/edit_handler.php';
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редагування товару | Navyrost</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/admin.css">
    <link rel="stylesheet" href="/css/edit.css">
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h1>Редагування товару: <?= htmlspecialchars($product['name']) ?></h1>
                <div class="admin-user">
                    <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Адмін') ?></span>
                    <i class="fas fa-user-circle"></i>
                </div>
            </header>

            <?php if (isset($_SESSION['admin_error'])): ?>
                <div class="admin-alert error">
                    <?= $_SESSION['admin_error'] ?>
                    <?php unset($_SESSION['admin_error']); ?>
                </div>
            <?php endif; ?>


            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <div class="form-group">
                    <label>ID товару</label>
                    <input type="text" value="<?= $product['id'] ?>" readonly class="readonly-field">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Назва товару *</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Ціна (грн) *</label>
                        <input type="number" id="price" name="price" min="0" step="0.01" 
                               value="<?= htmlspecialchars($product['price']) ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Опис товару</label>
                    <textarea id="description" name="description" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="gender">Стать *</label>
                        <select id="gender" name="gender" required>
                            <option value="Чоловічій" <?= $product['gender'] === 'Чоловічій' ? 'selected' : '' ?>>Чоловічій</option>
                            <option value="Жіночій" <?= $product['gender'] === 'Жіночій' ? 'selected' : '' ?>>Жіночій</option>
                            <option value="Унісекс" <?= $product['gender'] === 'Унісекс' ? 'selected' : '' ?>>Унісекс</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="brand">Бренд *</label>
                        <input type="text" id="brand" name="brand" value="<?= htmlspecialchars($product['brand']) ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Поточне зображення</label>
                    <div class="image-preview">
                        <?php if ($product['image']): ?>
                            <img src="/<?= htmlspecialchars($product['image']) ?>" alt="Поточне зображення">
                        <?php endif; ?>
                        <label class="image-upload-btn">
                            <i class="fas fa-upload"></i> Змінити зображення
                            <input type="file" id="image" name="image" accept="image/*" hidden>
                        </label>
                        <div class="hint">Залиште пустим, щоб зберегти поточне</div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Категорії *</label>
                        <div class="checkbox-group">
                            <?php foreach ($categories as $category): ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="category_<?= $category['id'] ?>" 
                                           name="categories[]" value="<?= $category['id'] ?>"
                                           <?= in_array($category['id'], $productCategories) ? 'checked' : '' ?>>
                                    <label for="category_<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Кольори</label>
                        <div class="checkbox-group">
                            <?php foreach ($colors as $color): ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="color_<?= $color['id'] ?>" 
                                           name="colors[]" value="<?= $color['id'] ?>"
                                           <?= in_array($color['id'], $productColors) ? 'checked' : '' ?>>
                                    <label for="color_<?= $color['id'] ?>"><?= htmlspecialchars($color['name']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Розміри</label>
                        <div class="checkbox-group">
                            <?php foreach ($sizes as $size): ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="size_<?= $size['id'] ?>" 
                                           name="sizes[]" value="<?= $size['id'] ?>"
                                           <?= in_array($size['id'], $productSizes) ? 'checked' : '' ?>>
                                    <label for="size_<?= $size['id'] ?>"><?= htmlspecialchars($size['name']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="update_product" class="admin-submit-btn">
                        <i class="fas fa-save"></i> Зберегти зміни
                    </button>
                    <a href="admin.php" class="admin-cancel-btn">
                        <i class="fas fa-times"></i> Скасувати
                    </a>
                </div>
            </form>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script/script.js"></script>
</body>
</html>