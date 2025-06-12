<?php
require_once '../Controller/admin_handler.php';
require_once '../functions/HtmlParser.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['html_file'])) {
    try {
        $htmlContent = file_get_contents($_FILES['html_file']['tmp_name']);
        $parser = new HtmlParser();
        $results = [];
        $parser->onOpenTag(function ($tag, $level) use (&$results) {
            $results[] = [
                'type' => 'OPEN_TAG',
                'tag' => $tag,
                'level' => $level,
                'content' => ''
            ];
        });

        $parser->onCloseTag(function ($tag, $level) use (&$results) {
            $results[] = [
                'type' => 'CLOSE_TAG',
                'tag' => $tag,
                'level' => $level,
                'content' => ''
            ];
        });

        $parser->onText(function ($text, $level) use (&$results) {
            $results[] = [
                'type' => 'TEXT',
                'tag' => '',
                'level' => $level,
                'content' => $text
            ];
        });

        $parser->parse($htmlContent);

    } catch (Exception $e) {
        $_SESSION['admin_error'] = "Помилка аналізу: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аналіз HTML | Navyrost</title>
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
                <li><a href="/View/admin_scripts.php"><i class="fas fa-code"></i> Управління скриптами</a></li>
                <li class="active"><a href="/View/admin_parser.php"><i class="fas fa-code"></i> HTML-парсер</a></li>
                <li><a href="/View/admin_xml.php"><i class="fas fa-file-export"></i> XML-інструменти</a></li>
                <li><a href="/Controller/logout_handler.php"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
            </ul>
        </nav>
        </aside>
        <main class="admin-content">
            <header class="admin-header">
                <h1><i class="fas fa-code"></i> Аналіз HTML-структури</h1>
                <div class="admin-user">
                    <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Адмін') ?></span>
                    <i class="fas fa-user-circle"></i>
                </div>
            </header>

            <section class="admin-parser-section">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Завантажити HTML-файл</h5>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <input type="file" class="form-control" name="html_file" accept=".html,.php" required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Аналізувати
                            </button>
                        </form>
                    </div>
                </div>

                <?php if (!empty($results)): ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Результати аналізу</h5>
                        <div class="table-responsive">
                            <table class="table parser-table">
                                <thead>
                                    <tr>
                                        <th>Тип</th>
                                        <th>Тег</th>
                                        <th>Рівень</th>
                                        <th>Контент</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results as $item): ?>
                                    <tr>
                                        <td>
                                            <span class="badge 
                                                <?= $item['type'] === 'OPEN_TAG' ? 'bg-primary' : '' ?>
                                                <?= $item['type'] === 'CLOSE_TAG' ? 'bg-danger' : '' ?>
                                                <?= $item['type'] === 'TEXT' ? 'bg-secondary' : '' ?>">
                                                <?= $item['type'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="tag-level-<?= (int)$item['level'] ?>">
                                                <?= htmlspecialchars($item['tag']) ?>
                                            </span>
                                        </td>
                                        <td><?= $item['level'] ?></td>
                                        <td><?= htmlspecialchars(mb_substr($item['content'], 0, 50)) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>