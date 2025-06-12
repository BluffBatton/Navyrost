<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Реєстрація | Navyrost</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/mainStyle.css" />
    <link rel="stylesheet" href="/css/signup.css" />
</head>
<body>
    <?php require_once '../blocks/header.php'; ?>

    <main class="register-container">
        <div class="register-form">
            <h1>Створити обліковий запис</h1>
            <p>Отримайте доступ до особистого кабінету та історії замовлень.</p>
            
            <?php if (isset($_SESSION['register_error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['register_error']; ?>
                    <?php unset($_SESSION['register_error']); ?>
                </div>
            <?php endif; ?>
            
            <form action="/Controller/register_handler.php" method="POST">
                <div class="form-group">
                    <label for="email">Електронна пошта</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" required>
                    <small class="password-requirements">
                        Пароль повинен містити:
                        <ul>
                            <li>Щонайменше 12 символів</li>
                            <li>Велику (A-Z) та малу (a-z) літери</li>
                            <li>Хоча б одну цифру (0-9)</li>
                            <li>Спеціальний символ (!@#$%^&* і т.д.)</li>
                        </ul>
                    </small>
                </div>

                <div class="form-group">
                    <label for="firstname">Ім'я</label>
                    <input type="text" id="firstname" name="firstname" required>
                </div>
                <div class="form-group">
                    <label for="lastname">Прізвище</label>
                    <input type="text" id="lastname" name="lastname" required>
                </div>
                <div class="form-group">
                    <label for="phonenumber">Телефон</label>
                    <input type="tel" id="phonenumber" name="phonenumber">
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="updates" name="updates">
                    <label for="updates">Отримувати інформаційні листи про новинки та акції</label>
                </div>
                
                <button type="submit" class="register-btn">Продовжити</button>
                
                <p class="login-link">Вже маєте обліковий запис? <a href="/View/login.php">Увійти</a></p>
            </form>
            
            <p class="terms">Створюючи обліковий запис, ви погоджуєтесь з нашими Умовами обслуговування. Для отримання додаткової інформації про практику конфіденційності Navyrost, перегляньте нашу Політику конфіденційності. Ми іноді надсилатимемо вам електронні листи, пов'язані з обліковим записом.</p>
        </div>
    </main>

    <?php require_once '../blocks/footer.php'; ?>
</body>
</html>