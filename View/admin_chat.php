<?php
$baseUrl = '';
require_once '../functions/Database.php';
$db = new Database();
$users = $db->fetchAll("
    SELECT id,
    firstname || ' ' || lastname AS full_name
    FROM users
    WHERE id != 5
");
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
        <link rel="stylesheet" href="/css/admin_chat.css">
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
          <li><a href="/View/admin_day_calculator.php"><i class="fas fa-calendar-alt"></i> День тижня</a></li>
          <li><a href="/View/admin_sql_test.php"><i class="fa-solid fa-database"></i> SQL-редактор</a></li>
          <li><a href="/View/admin_db_test.php"><i class="fas fa-box"></i> Створення БД</a></li>
          <li><a href="/View/admin_scripts.php"><i class="fas fa-code"></i> Скрипти</a></li>
          <li><a href="/View/admin_parser.php"><i class="fas fa-code"></i> HTML-парсер</a></li>
          <li><a href="/View/admin_xml.php"><i class="fas fa-file-export"></i> XML-інструменти</a></li>
          <li class="active"><a href="/View/admin_chat.php"><i class="fas fa-comments"></i> Чат з клієнтами</a></li>
          <li><a href="/View/admin_tables.php"><i class="fas fa-database"></i> Таблиці БД</a></li>
          <li><a href="/Controller/logout_handler.php"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
        </ul>
      </nav>
    </aside>

    <main class="admin-content">
      <header class="admin-header">
        <h1>Чат технічної підтримки з клієнтами</h1>
        <div class="admin-user">
          <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Адмін') ?></span>
          <i class="fas fa-user-circle"></i>
        </div>
      </header>

      <div class="adminChat-row">
        <div class="adminChat-users">
          <?php foreach ($users as $user): ?>
            <div class="adminChat-user" data-id="<?= $user['id'] ?>">
              <?= htmlspecialchars($user['full_name']) ?>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="adminChat-chat">
          <div id="chatMessages" class="adminChat-messages"></div>
          <div class="adminChat-inputArea">
            <textarea id="messageInput" placeholder="Напишіть повідомлення..."></textarea>
            <button class="adminChat-sendBtn" onclick="sendMessage()">
              <i class="fa fa-paper-plane"></i>
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>


<script>
const moderatorId = 5;
let receiverId = 1;

function loadMessages(userId) {
    document.getElementById("chatMessages").innerHTML = '';
    fetch (`../get_messages.php?user_id=${userId}`)
        .then(res => res.json())
        .then(data => {
            const chat = document.getElementById("chatMessages");
            data.forEach(msg => {
                chat.innerHTML += `
    <div class="adminChat-message ${msg.sender_id == moderatorId ? 'admin' : 'user'}">
        <div class="adminChat-message-sender">${msg.sender_name}</div>
        ${msg.message}
        <span class="adminChat-message-time">${msg.sent_at}</span>
    </div>
`;
            });
        });
}

let socket = new WebSocket(`ws://localhost:8080?user_id=${moderatorId}`);
socket.onmessage = function(event) {
    const data = JSON.parse(event.data);
    if (parseInt(data.sender_id) === receiverId || parseInt(data.receiver_id) === receiverId) {
        const chat = document.getElementById("chatMessages");
        chat.innerHTML += `<div class="adminChat-message ${parseInt(data.sender_id) === moderatorId ? 'admin' : 'user'}">
        <div class="adminChat-message-sender">${data.sender_name || data.sender_id}</div>
        ${data.message}
        <span class="adminChat-message-time">${data.sent_at}</span>
    </div>
`;
    }
};

function sendMessage() {
    const text = document.getElementById("messageInput").value;
    socket.send(JSON.stringify({
        receiver_id: receiverId,
        message: text
    }));
    document.getElementById("messageInput").value = '';
}

document.querySelectorAll(".adminChat-user").forEach(el => {
    el.addEventListener("click", () => {
        document.querySelectorAll(".adminChat-user").forEach(u => u.classList.remove("active"));
        el.classList.add("active");
        receiverId = parseInt(el.getAttribute("data-id"));
        loadMessages(receiverId);
    });
});

document.getElementById('messageInput').addEventListener('keydown', function(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    sendMessage();
  }
});

document.getElementById("receiverIdSelect").addEventListener("change", function() {
    receiverId = parseInt(this.value);
    loadMessages(receiverId);
});

loadMessages(receiverId);
</script>
</body>
</html>