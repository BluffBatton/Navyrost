<?php
session_start();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Реєстрація Адміна | Navyrost</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/css/signup.css" />
</head>
<body>
<?php require_once '../blocks/header.php'; ?>
<main class="register-container">
    <div class="register-form">
        <h1>Створити адміністратора</h1>
        <?php if (isset($_SESSION['register_error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['register_error']; unset($_SESSION['register_error']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['register_success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['register_success']; unset($_SESSION['register_success']); ?>
            </div>
        <?php endif; ?>
        <form action="/Controller/admin_register_handler.php" method="POST">
            <div class="form-group mb-2">
                <label for="email">Електронна пошта</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group mb-2">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <small class="form-text">Мінімум 8 символів, цифра, літера.</small>
            </div>
            <div class="form-group mb-2">
                <label for="firstname">Ім'я</label>
                <input type="text" id="firstname" name="firstname" class="form-control" required>
            </div>
            <div class="form-group mb-2">
                <label for="lastname">Прізвище</label>
                <input type="text" id="lastname" name="lastname" class="form-control" required>
            </div>
            <div class="form-group mb-2">
                <label for="phonenumber">Телефон</label>
                <input type="tel" id="phonenumber" name="phonenumber" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Створити Адміна</button>
        </form>
    </div>
</main>
<?php require_once '../blocks/footer.php'; ?>
</body>
</html>