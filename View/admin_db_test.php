<?php
require_once '../Controller/admin_handler.php';
require_once '../functions/testDatabase.php';

try {
    $db = new NewDatabase(__DIR__ . '/../SQLite/CustomDatabase.db');
    $users = $db->getData('Users');
    $orders = $db->getData('Orders');
} catch (Exception $e) {
    $_SESSION['admin_error'] = "Помилка роботи з базою даних: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест БД | Адмін-панель Navyrost</title>
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
                <li><a href="/View/admin_sql_test.php"><i class="fa-solid fa-database"></i> Редактор запитів SQL</a></li>
                <li class="active"><a href="/View/admin_db_test.php"><i class="fas fa-box"></i> Створення бази даних</a></li>
                <li><a href="/View/admin_scripts.php"><i class="fas fa-code"></i> Управління скриптами</a></li>
                <li><a href="/View/admin_parser.php"><i class="fas fa-code"></i> HTML-парсер</a></li>
                <li><a href="/View/admin_xml.php"><i class="fas fa-file-export"></i> XML-інструменти</a></li>
                <li><a href="/Controller/logout_handler.php"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
            </ul>
        </nav>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h1>Тестування бази даних</h1>
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

            <section class="admin-data-section">
                <div class="row">
                    <div class="col-md-6">
                        <div class="data-card">
                            <div class="card-header bg-primary text-white">
                                <i class="fas fa-users"></i> Користувачі
                            </div>
                            <div class="card-body">
                                <?php if (!empty($users)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Ім'я</th>
                                                    <th>Email</th>
                                                    <th>Дата реєстрації</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($users as $user): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($user['id']) ?></td>
                                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                                    <td><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">Немає даних про користувачів</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="data-card">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-shopping-cart"></i> Замовлення
                            </div>
                            <div class="card-body">
                                <?php if (!empty($orders)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Користувач</th>
                                                    <th>Сума</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($orders as $order): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($order['id']) ?></td>
                                                    <td>
                                                        <?= $users[array_search($order['user_id'], array_column($users, 'id'))]['username'] ?? 'Невідомо' ?>
                                                    </td>
                                                    <td><?= number_format($order['amount'], 2) ?> грн</td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">Немає даних про замовлення</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/script/admin.js"></script>
</body>
</html>