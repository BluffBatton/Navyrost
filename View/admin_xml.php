<?php
require_once __DIR__ . '/../Controller/admin_handler.php';
require_once __DIR__ . '/../Model/XmlUsersManager.php';
require_once __DIR__ . '/../functions/Database.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$db = new Database();
$xmlManager = new XmlUsersManager();

// Обработка экспорта
if (isset($_POST['export_xml'])) {
    if ($xmlManager->exportFromSqlite($db)) {
        $_SESSION['admin_message'] = "Експорт успішний!";
    } else {
        $_SESSION['admin_error'] = "Помилка експорту!";
    }
    header("Location: admin_xml.php");
    exit();
}

// Обработка импорта
if (isset($_POST['import_xml'])) {
    $file = $_FILES['xml_file']['tmp_name'];
    if ($xmlManager->importFromFile($file)) {
        $_SESSION['admin_message'] = "Імпорт успішний!";
    } else {
        $_SESSION['admin_error'] = "Помилка імпорту!";
    }
    header("Location: admin_xml.php");
    exit();
}

$baseUrl = '';
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
                    <li class="active"><a href="/View/admin_xml.php"><i class="fas fa-file-export"></i> XML-інструменти</a></li>
                    <li><a href="/Controller/logout_handler.php"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h1><i class="fas fa-file-export"></i> XML-інструменти</h1>
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

            <section class="admin-tools-section">
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="card-title"><i class="fas fa-file-export"></i> Експорт даних</h2>
                        <form method="POST" class="admin-form">
                            <button type="submit" name="export_xml" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i>Експортувати користувачів
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="card-title"><i class="fas fa-file-import"></i> Імпорт даних</h2>
                        <form method="POST" enctype="multipart/form-data" class="admin-form">
                            <div class="mb-3">
                                <label for="xml_file" class="form-label">Виберіть XML файл</label>
                                <input type="file" class="form-control" id="xml_file" name="xml_file" accept=".xml" required>
                            </div>
                            <button type="submit" name="import_xml" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Імпортувати користувачів
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title"><i class="fas fa-code"></i> Поточний вміст XML</h2>
                        <div class="xml-preview">
                            <?= $xmlManager->displayXmlAsHtml() ?>
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