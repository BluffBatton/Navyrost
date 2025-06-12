<?php
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

$userName = htmlspecialchars($_SESSION['user_name'] ?? $t['user']);
$userEmail = htmlspecialchars($_SESSION['user_email'] ?? '');
$userId = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $t['support_chat'] ?> | Navyrost</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/mainStyle.css" />
    <link rel="stylesheet" href="/css/account.css" />
    <link rel="stylesheet" href="/css/chat.css" />
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
                        <li>
                            <a href="/View/notifications.php">
                                <i class="fas fa-bell"></i> <?= $t['notifications'] ?>
                                <?php if ($unreadCount > 0): ?>
                                    <span class="notification-badge"><?= $unreadCount ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="active"><a href="/View/chat.php"><i class="fas fa-comments"></i> <?= $t['support_chat'] ?></a></li>
                        <li><a href="/Controller/logout_handler.php"><i class="fas fa-sign-out-alt"></i> <?= $t['logout'] ?></a></li>
                    </ul>
                </nav>
            </aside>

            <div class="account-content">
                <h2 class="account-title"><?= $t['support_chat'] ?></h2>
                <div class="chat-container">
                    <div class="chat-messages" id="chat-messages"></div>
                    <div class="chat-input">
                        <input type="text" id="message-input" placeholder="<?= $t['type_message'] ?>">
                        <button id="send-button" onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once '../blocks/footer.php'; ?>

    <script>
        const userId = <?= json_encode($userId) ?>;
        const moderatorId = 5;
        const currentUser = {
            id: <?= json_encode($_SESSION['user_id'] ?? null) ?>,
            name: <?= json_encode($_SESSION['user_name'] ?? $t['user']) ?>,
            role: <?= json_encode($_SESSION['user_role'] ?? 'user') ?>,
            sessionId: <?= json_encode(session_id()) ?>
        };

        fetch(`../get_messages.php?user_id=${userId}`)
            .then(res => res.json())
            .then(data => {
                const chat = document.getElementById("chat-messages");
                data.forEach(msg => {
                    chat.innerHTML += `
                        <div class="chat-message ${msg.sender_id == moderatorId ? 'moderator' : 'user'}">
                            <strong>${msg.sender_name}:</strong>
                            ${msg.message}
                            <em>${msg.sent_at}</em>
                        </div>
                    `;
                });
                chat.scrollTop = chat.scrollHeight;
            });

        let socket = new WebSocket(`ws://localhost:8080?user_id=${userId}`);

        socket.onmessage = function(event) {
            const data = JSON.parse(event.data);
            const chat = document.getElementById("chat-messages");
            chat.innerHTML += `
                <div class="chat-message ${parseInt(data.sender_id) === moderatorId ? 'moderator' : 'user'}">
                    <strong>${data.sender_name || data.sender_id}:</strong>
                    ${data.message}
                    <em>${data.sent_at}</em>
                </div>
            `;
            chat.scrollTop = chat.scrollHeight;
        };

            console.log("WebSocket state:", socket.readyState);
            socket.onopen = () => console.log("WebSocket connected!");
            socket.onerror = (err) => console.error("WebSocket error:", err);
        
        function sendMessage() {
            const text = document.getElementById("message-input").value.trim();
            if (text) {
                socket.send(JSON.stringify({
                    receiver_id: moderatorId,
                    message: text
                }));
                document.getElementById("message-input").value = '';
            }
        }

        document.getElementById('message-input').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    </script>
</body>
</html>