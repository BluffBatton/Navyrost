<?php
require_once '../Controller/admin_handler.php';

// Перевірка прав адміна
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управління скриптами | Navyrost</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/admin.css">
    <link rel="stylesheet" href="/css/adminScripts.css">
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
                <li><a href="/View/admin_db_test.php"><i class="fas fa-box"></i> Створення бази даних</a></li>
                <li class="active"><a href="/View/admin_scripts.php"><i class="fas fa-code"></i> Управління скриптами</a></li>
                <li><a href="/View/admin_parser.php"><i class="fas fa-code"></i> HTML-парсер</a></li>
                <li><a href="/View/admin_xml.php"><i class="fas fa-file-export"></i> XML-інструменти</a></li>
                <li><a href="/Controller/logout_handler.php"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
            </ul>
        </nav>
        </aside>

        <main class="admin-content">
            <h1><i class="fas fa-code"></i> Управління скриптами</h1>
            
            <section class="admin-scripts-section">
                <div class="row">
                    <!-- Блок інформації про простір імен -->
                    <div class="col-md-6">
                        <div class="data-card">
                            <div class="card-header bg-info text-white">
                                <i class="fas fa-cube"></i> Простір імен App
                            </div>
                            <div class="card-body">
                                <pre id="namespace-structure" class="bg-light p-3 rounded"></pre>
                                <button class="btn btn-secondary mt-3" onclick="displayNamespaceStructure()">
                                    <i class="fas fa-sync-alt"></i> Оновити структуру
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Блок тестування методів -->
                    <div class="col-md-6">
                        <div class="data-card">
                            <div class="card-header bg-warning text-dark">
                                <i class="fas fa-vial"></i> Тестування методів
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Оберіть модуль:</label>
                                    <select class="form-select" id="module-select">
                                        <option value="textConverter">Text Converter</option>
                                        <option value="navigation">Navigation</option>
                                        <option value="sizes">Sizes Manager</option>
                                        <option value="imagePreview">Image Preview</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Введіть дані:</label>
                                    <textarea class="form-control" id="input-data" rows="3" placeholder="Введіть Markdown текст для перетворення"></textarea>
                                </div>
                                <button class="btn btn-primary" onclick="runTest()">
                                    <i class="fas fa-play"></i> Виконати тест
                                </button>
                                <hr>
                                <h5>Результат:</h5>
                                <div id="test-result" class="mt-3 p-3 bg-light rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    
    <script src="/script/AppNamespace.js"></script>
</body>
</html>