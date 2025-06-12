<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../functions/Database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Language settings
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = $_SESSION['lang'] ?? 'ua';
$translations = include __DIR__ . '/../lang.php';
$t = $translations[$lang];

if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    header('Location: /View/admin.php');
    exit();
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: /View/login.php');
    exit();
}

$db = new Database();

$notifications = $db->execQuery("
    SELECT * FROM notifications 
    WHERE user_id = ? 
    ORDER BY created_at DESC
", [$_SESSION['user_id']]);

if (!empty($notifications)) {
    $db->execQuery("
        UPDATE notifications 
        SET is_read = 1 
        WHERE user_id = ? AND is_read = 0
    ", [$_SESSION['user_id']]);
}

$unreadCount = $db->execQuery("
    SELECT COUNT(*) as count FROM notifications 
    WHERE user_id = ? AND is_read = 0
", [$_SESSION['user_id']])[0]['count'] ?? 0;

$notifications = $db->execQuery("
    SELECT * FROM notifications 
    WHERE user_id = ? 
    ORDER BY created_at DESC
", [$_SESSION['user_id']]);

$userName = htmlspecialchars($_SESSION['user_name'] ?? $t['user']);
$userEmail = htmlspecialchars($_SESSION['user_email'] ?? '');
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $t['notifications'] ?> | Navyrost</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/mainStyle.css" />
    <link rel="stylesheet" href="/css/account.css" />
</head>
<body>
    <?php require_once '../blocks/header.php'; ?>

    <main class="account-container">
        <div class="account-wrapper">
          
            <aside class="account-sidebar">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h3 class="user-name"><?= $userName ?></h3>
                    <p class="user-email"><?= $userEmail ?></p>
                </div>
                
                <nav class="account-nav">
                    <ul>
                        <li><a href="/View/account.php"><i class="fas fa-user"></i> <?= $t['personal_data'] ?></a></li>
                        <li class="active">
                            <a href="/View/notifications.php">
                                <i class="fas fa-bell"></i> <?= $t['notifications'] ?>
                                <?php if ($unreadCount > 0): ?>
                                    <span class="notification-badge pulse"><?= $unreadCount ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li><a href="/View/chat.php"><i class="fas fa-comments"></i> <?= $t['support_chat'] ?></a></li>
                        <li><a href="/Controller/logout_handler.php"><i class="fas fa-sign-out-alt"></i> <?= $t['logout'] ?></a></li>
                    </ul>
                </nav>
            </aside>

            <div class="account-content">
                <h2 class="account-title"><?= $t['notifications'] ?></h2>
                
                <div class="account-section">
                    <?php if (empty($notifications)): ?>
                        <div class="empty-notifications">
                            <i class="fas fa-bell-slash"></i>
                            <p><?= $t['no_notifications'] ?></p>
                        </div>
                    <?php else: ?>
                        <div class="notifications-list">
                            <?php foreach ($notifications as $notification): ?>
                                <div class="notification-item <?= $notification['is_read'] ? '' : 'unread' ?>">
                                    <div class="notification-icon">
                                        <i class="fas fa-<?= $notification['is_read'] ? 'envelope-open' : 'envelope' ?>"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p><?= htmlspecialchars($notification['message']) ?></p>
                                        <span class="notification-time">
                                            <?= date($t['date_format'], strtotime($notification['created_at'])) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php require_once '../blocks/footer.php'; ?>
</body>
</html>