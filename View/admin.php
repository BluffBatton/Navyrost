<?php
// /View/admin.php
require_once '../Controller/admin_handler.php';
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Адмін-панель | Navyrost</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body>
    <div class="admin-ip">
    <i class="fas fa-network-wired"></i>
    <span><?= htmlspecialchars($_SERVER['REMOTE_ADDR']) ?></span>
</div>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <a href="/index.php"> 
                <img src="/pic/logo main.png" alt="Navyrost Logo"></a>
                <h2>Адмін-панель</h2>
            </div>
        <nav class="admin-nav">
            <ul>
                <li class="active"><a href="/View/admin.php"><i class="fas fa-tachometer-alt"></i> Панель керування</a></li>
                <li><a href="/View/ip_validator.php"><i class="fas fa-check-circle"></i> Валідатор IP</a></li>
                <li><a href="/View/admin_day_calculator.php"><i class="fas fa-calendar-alt"></i> Визначити день тижня</a></li>
                <li><a href="/View/admin_sql_test.php"><i class="fa-solid fa-database"></i> Редактор запитів SQL</a></li>
                <li><a href="/View/admin_db_test.php"><i class="fas fa-box"></i> Створення бази даних</a></li>
                <li><a href="/View/admin_scripts.php"><i class="fas fa-code"></i> Управління скриптами</a></li>
                <li><a href="/View/admin_parser.php"><i class="fas fa-code"></i> HTML-парсер</a></li>
                <li><a href="/View/admin_xml.php"><i class="fas fa-file-export"></i> XML-інструменти</a></li>
                <li><a href="/View/admin_chat.php"><i class="fas fa-comments"></i> Чат з клієнтами</a></li>
                <li><a href="/View/admin_tables.php"><i class="fas fa-database"></i> Таблиці різних БД</a></li>
                <li><a href="/View/admin_visits.php"><i class="fas fa-chart-bar"></i> Статистика відвідувань</a></li>
                <li><a href="/Controller/logout_handler.php"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
            </ul>
        </nav>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h1>Панель керування</h1>
                <div class="admin-user">
                    <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Адмін') ?></span>
                    <i class="fas fa-user-circle"></i>
                </div>
            </header>

            <?php if (isset($_SESSION['admin_message'])): ?>
                <div class="admin-alert success">
                    <?= $_SESSION['admin_message'] ?>
                    <?php unset($_SESSION['admin_message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['admin_error'])): ?>
                <div class="admin-alert error">
                    <?= $_SESSION['admin_error'] ?>
                    <?php unset($_SESSION['admin_error']); ?>
                </div>
            <?php endif; ?>

            <section class="admin-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Товари</h3>
                        <p><?= $stats['total_products'] ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Користувачі</h3>
                        <p><?= $stats['total_users'] ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Адміністратори</h3>
                        <p><?= $stats['total_admins'] ?></p>
                    </div>
                </div>
            </section>

            <section class="admin-form-section">
                <h2><i class="fas fa-plus-circle"></i> Додати новий товар</h2>
                
                <form method="POST" enctype="multipart/form-data" class="admin-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Назва товару *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="price">Ціна (грн) *</label>
                            <input type="number" id="price" name="price" min="0" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Опис товару</label>
                        <textarea id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="gender">Стать *</label>
                            <select id="gender" name="gender" required>
                                <option value="Чоловічій">Чоловічій</option>
                                <option value="Жіночій">Жіночій</option>
                                <option value="Унісекс">Унісекс</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="brand">Бренд *</label>
                            <input type="text" id="brand" name="brand" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Зображення товару *</label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Категорії *</label>
                            <div class="checkbox-group">
                                <?php foreach ($categories as $category): ?>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="category_<?= $category['id'] ?>" name="categories[]" value="<?= $category['id'] ?>">
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
                                        <input type="checkbox" id="color_<?= $color['id'] ?>" name="colors[]" value="<?= $color['id'] ?>">
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
                                        <input type="checkbox" id="size_<?= $size['id'] ?>" name="sizes[]" value="<?= $size['id'] ?>">
                                        <label for="size_<?= $size['id'] ?>"><?= htmlspecialchars($size['name']) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" name="add_product" class="admin-submit-btn">Додати товар</button>
                </form>
            </section>

            <section class="admin-products-section">
                <h2><i class="fas fa-list"></i> Останні товари</h2>
                
                <div class="products-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Зображення</th>
                                <th>Назва</th>
                                <th>Ціна</th>
                                <th>Стать</th>
                                <th>Бренд</th>
                                <th>Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= $product['id'] ?></td>
                                    <td>
                                        <?php if ($product['image']): ?>
                                            <img src="/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-thumb">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td><?= number_format($product['price'], 0, ',', ' ') ?> грн</td>
                                    <td><?= htmlspecialchars($product['gender']) ?></td>
                                    <td><?= htmlspecialchars($product['brand']) ?></td>
                                    <td>
                                        <a href="edit_product.php?id=<?= $product['id'] ?>" class="action-btn edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <button type="submit" name="delete_product" class="action-btn delete" onclick="return confirm('Ви впевнені?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/script/admin.js"></script>
</body>
</html>