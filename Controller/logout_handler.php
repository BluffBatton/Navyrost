<?php
session_start();
// Прибираємо всі дані сесії
$_SESSION = [];
// Якщо ви хочете повністю знищити сесію
session_destroy();
// Переходимо на головну або на сторінку входу
header('Location: /index.php');
exit;