<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /View/login.php');
    exit();
}

require_once '../functions/PostgresAdapter.php';
require_once '../functions/MysqlAdapter.php';

$baseUrl = '';

// Создаем объекты с правильными именами классов
$postgresClient = new PostgresAdapter();
$mysqlClient = new MysqlAdapter();

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Таблиці БД | Navyrost</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/admin.css" />
</head>
<body>

<div class="admin-container">
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <a href="/index.php">
                <img src="/pic/logo main.png" alt="Navyrost Logo" />
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
                <li class="active"><a href="/View/admin_tables.php"><i class="fas fa-table"></i> Таблиці різних БД</a></li>
                <li><a href="/View/admin_chat.php"><i class="fas fa-comments"></i> Чат з клієнтами</a></li>
                <li><a href="/Controller/logout_handler.php"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
            </ul>
        </nav>
    </aside>

    <main class="admin-content">
        <header class="admin-header">
            <h1>Таблиці з різних баз даних</h1>
            <div class="admin-user">
                <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <i class="fas fa-user-circle"></i>
            </div>
        </header>

        <section class="admin-section">

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title"><i class="fas fa-database"></i> Користувачі з PostgreSQL</h2>
                    <div class="table-responsive">
                        <?= $postgresClient->displayUsersAsHtml(); ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="card-title"><i class="fas fa-box"></i> Товари з MySQL</h2>
                    <div class="table-responsive">
                        <?= $mysqlClient->displayProductsAsHtml(); ?>
                    </div>
                </div>
            </div>

        </section>
    </main>
</div>

</body>
</html>
