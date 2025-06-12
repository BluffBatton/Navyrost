<?php
session_start();
// тут можете показати номер замовлення, якщо збережете його в сесії
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Дякуємо за замовлення</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/mainStyle.css" />
</head>
<body>
  <?php include '../blocks/header.php'; ?>
  <main class="container mt-5 text-center">
    <h1>Дякуємо за ваше замовлення!</h1>
    <p>Номер вашого замовлення: <strong><?= htmlspecialchars($orderUuid ?? '') ?></strong></p>
    <a href="/View/account.php" class="btn btn-primary">Переглянути історію замовлень</a>
  </main>
  <?php include '../blocks/footer.php'; ?>
</body>
</html>
