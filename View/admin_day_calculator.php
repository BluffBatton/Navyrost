<?php
require_once '../Controller/admin_handler.php';
$baseUrl = '';
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Калькулятор дня тижня | Адмін-панель</title>
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
                <li  class="active"><a href="/View/admin_day_calculator.php"><i class="fas fa-calendar-alt"></i> Визначити день тижня</a></li>
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
                <h1>Визначення дня тижня</h1>
                <div class="admin-user">
                    <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Адмін') ?></span>
                    <i class="fas fa-user-circle"></i>
                </div>
            </header>

            <form method="GET" class="date-form">
                <div class="form-group triple-input">
                    <div class="input-wrapper">
                        <label for="day">День</label>
                        <input type="number" id="day" name="day" min="1" max="31" 
                               placeholder="ДД" required>
                    </div>
                    
                    <div class="input-wrapper">
                        <label for="month">Місяць</label>
                        <input type="number" id="month" name="month" min="1" max="12" 
                               placeholder="ММ" required>
                    </div>
                    
                    <div class="input-wrapper">
                        <label for="year">Рік</label>
                        <input type="number" id="year" name="year" min="1900" max="2100" 
                               placeholder="РРРР" required>
                    </div>
                </div>
                <button type="submit" class="btn">Перевірити</button>
            </form>

            <?php if (isset($_GET['day'], $_GET['month'], $_GET['year'])) : 
                $day = (int)$_GET['day'];
                $month = (int)$_GET['month'];
                $year = (int)$_GET['year'];
                
                $isValid = checkdate($month, $day, $year);
                $ukrainianDays = [
                    'Monday'    => 'Понеділок',
                    'Tuesday'   => 'Вівторок',
                    'Wednesday' => 'Середа',
                    'Thursday'  => 'Четвер',
                    'Friday'    => 'Пʼятниця',
                    'Saturday'  => 'Субота',
                    'Sunday'    => 'Неділя'
                ];
            ?>
                <div class="result-box <?= $isValid ? 'success' : 'error' ?>">
                    <?php if ($isValid) : 
                        $date = new DateTime("$year-$month-$day");
                        $dayName = $ukrainianDays[$date->format('l')];
                    ?>
                        Дата: <?= sprintf("%02d.%02d.%d", $day, $month, $year) ?><br>
                        День тижня: <strong><?= $dayName ?></strong>
                    <?php else : ?>
                        Помилка: Невірна дата!
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>