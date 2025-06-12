<?php
// /View/admin_visits.php

// 1. Перевірка авторизації
session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: /View/login.php');
    exit;
}

require_once '../functions/Database.php';;

$db = new Database();

// Отримуємо останні 7 днів статистики
$stats = $db->execQuery(
    "SELECT date, hosts, hits, total
     FROM statistics
     ORDER BY date DESC
     LIMIT 7"
);

// Отримуємо відвідування залогінених користувачів
$userStats = $db->execQuery(
    "SELECT us.date,
            u.firstname || ' ' || u.lastname AS user_name,
            us.visits
     FROM user_statistics us
     LEFT JOIN users u ON u.id = us.user_id
     ORDER BY us.date DESC, us.visits DESC
     LIMIT 50"
);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Статистика відвідувань | Navyrost</title>
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
                <img src="/pic/logo main.png" alt="Navyrost Logo">
            </a>
            <h2>Адмін-панель</h2>
        </div>
        <nav class="admin-nav">
            <ul>
                <li><a href="/View/admin.php"><i class="fas fa-tachometer-alt"></i> Панель керування</a></li>
                <li><a href="/View/ip_validator.php"><i class="fas fa-check-circle"></i> Валідатор IP</a></li>
                <li><a href="/View/admin_day_calculator.php"><i class="fas fa-calendar-alt"></i> Визначити день тижня</a></li>
                <li><a href="/View/admin_sql_test.php"><i class="fa-solid fa-database"></i> Редактор запитів SQL</a></li>
                <li><a href="/View/admin_db_test.php"><i class="fas fa-box"></i> Створення бази даних</a></li>
                <li><a href="/View/admin_scripts.php"><i class="fas fa-code"></i> Управління скриптами</a></li>
                <li><a href="/View/admin_parser.php"><i class="fas fa-code"></i> HTML-парсер</a></li>
                <li><a href="/View/admin_xml.php"><i class="fas fa-file-export"></i> XML-інструменти</a></li>
                <li><a href="/View/admin_chat.php"><i class="fas fa-comments"></i> Чат з клієнтами</a></li>
                <li><a href="/View/admin_tables.php"><i class="fas fa-database"></i> Таблиці різних БД</a></li>
                <li class="active"><a href="/View/admin_visits.php"><i class="fas fa-chart-bar"></i> Статистика відвідувань</a></li>
                <li><a href="/Controller/logout_handler.php"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
            </ul>
        </nav>
    </aside>

    <main class="admin-content">
        <header class="admin-header">
            <h1>Статистика відвідувань</h1>
            <div class="admin-user">
                <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Адмін') ?></span>
                <i class="fas fa-user-circle"></i>
            </div>
        </header>

        <section class="admin-stats">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                <div class="stat-info">
                    <h3>Останні 7 днів</h3>
                    <p><?= count($stats) ?> днів</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h3>Користувачі</h3>
                    <p><?= count($userStats) ?> записів</p>
                </div>
            </div>
        </section>

        <section class="admin-table-section">
            <h2><i class="fas fa-list"></i> Загальна статистика</h2>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                    <tr>
                        <th>Дата</th>
                        <th>Хости (IP)</th>
                        <th>Хіти</th>
                        <th>Загалом</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($stats as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['date']) ?></td>
                            <td><?= (int)$row['hosts'] ?></td>
                            <td><?= (int)$row['hits'] ?></td>
                            <td><?= (int)$row['total'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="admin-table-section mt-5">
            <h2><i class="fas fa-user-clock"></i> Відвідування залогінених користувачів</h2>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                    <tr>
                        <th>Дата</th>
                        <th>Користувач</th>
                        <th>Відвідувань</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($userStats as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['date']) ?></td>
                            <td><?= htmlspecialchars($row['user_name'] ?? '—') ?></td>
                            <td><?= (int)$row['visits'] ?></td>
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
