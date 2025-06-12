<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$validationResult = null;

if (isset($_SESSION['ip_validation_result'])) {
    $validationResult = $_SESSION['ip_validation_result']['message'];
    unset($_SESSION['ip_validation_result']);
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Валідатор IP | Navyrost</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body>
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
                    <li class="active"><a href="/View/ip_validator.php"><i class="fas fa-check-circle"></i> Валідатор IP</a></li>
                    <li><a href="/View/admin_day_calculator.php"><i class="fas fa-calendar-alt"></i> Визначити день тижня</a></li>
                    <li><a href="/View/admin_sql_test.php"><i class="fa-solid fa-database"></i> Редактор запитів SQL</a></li>
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
                <h1>Валідатор IP-адрес</h1>
                <div class="admin-user">
                    <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    <i class="fas fa-user-circle"></i>
                </div>
            </header>

            <section class="ip-validation-section">
                <form method="POST" action="/Controller/IpValidatorController.php" class="ip-validation-form">
                    <div class="form-group">
                        <label for="ip">Введіть IP-адресу:</label>
                        <input type="text" id="ip" name="ip" 
                               placeholder="Наприклад: 192.168.1.1" required>
                    </div>
                    <button type="submit" class="validate-btn">Перевірити</button>
                </form>

                <?php if ($validationResult !== null): ?>
                    <div class="validation-result <?= strpos($validationResult, '✅') !== false ? 'valid' : 'invalid' ?>">
                        <?= $validationResult ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>