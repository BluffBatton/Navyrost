<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

require_once '../functions/Database.php';
$db = new Database();


$user = $db->execQuery("SELECT role FROM users WHERE id = ?", [$_SESSION['user_id']]);


$error = '';
$result = [];
$sql = $_POST['sql'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($sql)) {
    try {
        $stmt = $db->execQuery($sql, [], false);
        
        if (stripos(trim($sql), 'SELECT') === 0) {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $result = ['rows_affected' => $stmt->rowCount()];
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Tester | Адмін-панель</title>
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
                <li><a href="/View/admin.php"><i class="fas fa-tachometer-alt"></i> Панель керування</a></li>
                <li><a href="/View/ip_validator.php"><i class="fas fa-check-circle"></i> Валідатор IP</a></li>
                <li><a href="/View/admin_day_calculator.php"><i class="fas fa-calendar-alt"></i> Визначити день тижня</a></li>
                <li class="active"><a href="/View/admin_sql_test.php"><i class="fa-solid fa-database"></i> Редактор запитів SQL</a></li>
                <li><a href="/View/admin_db_test.php"><i class="fas fa-box"></i> Створення бази даних</a></li>
                <li><a href="/View/admin_scripts.php"><i class="fas fa-code"></i> Управління скриптами</a></li>
                <li><a href="/View/admin_parser.php"><i class="fas fa-code"></i> HTML-парсер</a></li>
                <li><a href="/View/admin_xml.php"><i class="fas fa-file-export"></i> XML-інструменти</a></li>
                <li><a href="/Controller/logout_handler.php"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
            </ul>
        </nav>
        </aside>
        <main class="admin-content">
            <header class="admin-header">
                <h1>SQL Tester</h1>
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

            <section class="sql-tester-section">
                <form method="POST" class="sql-form">
                    <div class="form-group">
                        <textarea name="sql" placeholder="Введіть SQL-запит"><?= htmlspecialchars($sql) ?></textarea>
                    </div>
                    <button type="submit" class="admin-submit-btn">Виконати запит</button>
                </form>

                <?php if ($error): ?>
                    <div class="admin-alert error">
                        <strong>Помилка:</strong>
                        <pre><?= htmlspecialchars($error) ?></pre>
                    </div>
                <?php endif; ?>

                <?php if (!empty($result)): ?>
                    <div class="sql-results">
                        <h3>Результат (<?= count($result) ?> рядків):</h3>
                        
                        <?php if (isset($result[0])): ?>
                            <div class="results-table">
                                <table>
                                    <thead>
                                        <tr>
                                            <?php foreach (array_keys($result[0]) as $column): ?>
                                                <th><?= htmlspecialchars($column) ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($result as $row): ?>
                                            <tr>
                                                <?php foreach ($row as $value): ?>
                                                    <td><?= htmlspecialchars($value) ?></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="update-info">
                                Запит виконано. Змінено рядків: <?= $result['rows_affected'] ?? 0 ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="sql-examples">
                    <h4>Приклади запитів:</h4>
                    <pre>
-- Отримати всіх користувачів
SELECT * FROM users LIMIT 10;

-- Додати новий розмір
INSERT INTO sizes (name) VALUES ('XXL');

-- Оновити ціну товару
UPDATE products SET price = 1500 WHERE id = 1;

--ERROR TEST
INSERT INTO sizes (name) VALUES ('S')
                    </pre>
                </div>
            </section>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>