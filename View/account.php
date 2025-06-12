<?php
// /View/account.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Языковые настройки
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



$unreadCount = 0;
if (isset($_SESSION['user_id'])) {
    require_once '../functions/Database.php';
    $db = new Database();
    $result = $db->execQuery("
        SELECT COUNT(*) as count FROM notifications 
        WHERE user_id = ? AND is_read = 0
    ", [$_SESSION['user_id']]);
    $unreadCount = $result[0]['count'] ?? 0;
}

$orders = $db->execQuery(
    "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC",
    [$_SESSION['user_id']]
);

$orderItemsByOrder = [];
if (!empty($orders)) {
    $orderIds      = array_column($orders, 'id');
    $placeholders  = implode(',', array_fill(0, count($orderIds), '?'));
    $itemsRows = $db->execQuery(
        "SELECT order_id, product_id, name, price, quantity, size
        FROM order_items
        WHERE order_id IN ($placeholders)",
        $orderIds
    );
    foreach ($itemsRows as $ir) {
        $orderItemsByOrder[$ir['order_id']][] = $ir;
    }
}

$userName = htmlspecialchars($_SESSION['user_name'] ?? $t['user']);
$userEmail = htmlspecialchars($_SESSION['user_email'] ?? '');
$userFirstName = htmlspecialchars($_SESSION['user_first_name'] ?? '');
$userLastName = htmlspecialchars($_SESSION['user_last_name'] ?? '');
$userPhone = htmlspecialchars($_SESSION['user_phone'] ?? '');
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $t['my_account'] ?> | Navyrost</title>
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
                        <li class="active"><a href="/View/account.php"><i class="fas fa-user"></i> <?= $t['personal_data'] ?></a></li>                       
                            
                        <li>
                            <a href="/View/notifications.php">
                                <i class="fas fa-bell"></i> <?= $t['notifications'] ?>
                                <?php if ($unreadCount > 0): ?>
                                    <span class="notification-badge"><?= $unreadCount ?></span>
                                <?php endif; ?>
                            </a>
                            <li><a href="/View/chat.php"><i class="fas fa-comments"></i> <?= $t['support_chat'] ?></a></li>
                        </li>
                        <li><a href="/Controller/logout_handler.php"><i class="fas fa-sign-out-alt"></i> <?= $t['logout'] ?></a></li>
                    </ul>
                </nav>
            </aside>

            <div class="account-content">
                <h2 class="account-title"><?= $t['personal_data'] ?></h2>
                
                <div class="account-section">
                    <h3 class="section-title"><?= $t['contact_info'] ?></h3>
                    <form class="account-form" action="/Controller/update_handler.php" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name"><?= $t['first_name'] ?></label>
                                <input type="text" id="first_name" name="first_name" value="<?= $userFirstName ?>">
                            </div>
                            <div class="form-group">
                                <label for="last_name"><?= $t['last_name'] ?></label>
                                <input type="text" id="last_name" name="last_name" value="<?= $userLastName ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email"><?= $t['email'] ?></label>
                            <input type="email" id="email" name="email" value="<?= $userEmail ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone"><?= $t['phone'] ?></label>
                            <input type="tel" id="phone" name="phone" value="<?= $userPhone ?>">
                        </div>
                        
                        <button type="submit" class="save-btn"><?= $t['save_changes'] ?></button>
                    </form>
                </div>
                
                <div class="account-section">
                    <h3 class="section-title"><?= $t['order_history'] ?></h3>
                    
                    <?php if (empty($orders)): ?>
                        <div class="empty-history">
                            <i class="fas fa-shopping-bag"></i>
                            <p><?= $t['no_orders'] ?></p>
                            <a href="/View/catalog.php" class="shop-btn"><?= $t['go_to_shop'] ?></a>
                        </div>
                    <?php else: ?>
                        <table class="table table-striped">
                        <thead>
                            <tr>
                            <th><?= $t['product'] ?></th>
                            <th><?= $t['size'] ?></th>
                            <th><?= $t['quantity'] ?></th>
                            <th><?= $t['amount'] ?>, <?= $t['currency'] ?></th>
                            <th><?= $t['date'] ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($orders as $o): ?>
                            <?php
                            $its = $orderItemsByOrder[$o['id']] ?? [];
                            foreach ($its as $i):
                                $productId   = (int)$i['product_id'];
                                $productName = htmlspecialchars($i['name']);
                                $sizeText    = htmlspecialchars($i['size'] ?? '');
                                $qty         = (int)$i['quantity'];
                                $lineSum     = number_format($i['price'] * $qty, 0, ',', ' ');
                                $productUrl  = "/View/cloth.php?id={$productId}";
                            ?>
                            <tr>
                                <td>
                                <a href="<?= $productUrl ?>" class="text-decoration-none">
                                    <?= $productName ?>
                                </a>
                                </td>
                                <td><?= $sizeText ?></td>
                                <td><?= $qty ?></td>
                                <td><?= $lineSum ?></td>
                                <td><?= htmlspecialchars($o['created_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        </tbody>
                        </table>
                    <?php endif;?>
                    
                </div>
            </div>
        </div>
        <?php if (!empty($_SESSION['profile_success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['profile_success']; unset($_SESSION['profile_success']); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['profile_error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['profile_error']; unset($_SESSION['profile_error']); ?>
            </div>
        <?php endif; ?>
    </main>

    <?php require_once '../blocks/footer.php'; ?>
<script src="/script/script.js"></script>
</body>
</html>