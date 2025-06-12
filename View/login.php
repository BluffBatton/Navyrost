<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Загрузка переводов
$translations = include __DIR__ . '/../lang.php';
$language = $_SESSION['language'] ?? 'ua';
$t = $translations[$language] ?? $translations['ua'];

// Проверка авторизации
require_once __DIR__ . '/../Model/AuthModel.php';
$authModel = new AuthModel();
if ($authModel->checkUserSession()) {
    header("Location: /View/account.php");
    exit;
}

// Получить email из cookie (если есть)
$rememberedEmail = $_COOKIE['remembered_user'] ?? '';
?>
<!DOCTYPE html>
<html lang="<?= $language ?>">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $t['login'] ?> | Navyrost</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/mainStyle.css" />
    <link rel="stylesheet" href="/css/signup.css" />
</head>
<body>
    <?php require_once '../blocks/header.php'; ?>

    <main class="register-container">
        <div class="register-form">
            <h1><?= $t['login_title'] ?></h1>
            <p><?= $t['login_description'] ?></p>
            
            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="alert alert-danger">
                    <?= $t['login_error'] ?>
                    <?php unset($_SESSION['login_error']); ?>
                </div>
            <?php endif; ?>
            
            <form action="/Controller/login_handler.php" method="POST">
                <div class="form-group">
                    <label for="email"><?= $t['email'] ?></label>
                    <input type="text" id="email" name="email" value="<?= htmlspecialchars($rememberedEmail) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password"><?= $t['password'] ?></label>
                    <input type="password" id="password" name="password" required>
                    <div class="text-right">
                        <a href="/forgot_password.php" class="forgot-password"><?= $t['forgot_password'] ?></a>
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember_me" id="remember_me" checked>
                    <label class="form-check-label" for="remember_me"><?= $t['remember_me'] ?></label>
                </div>
                
                <button type="submit" class="register-btn"><?= $t['login_button'] ?></button>
                
                <p class="login-link">
                    <?= $t['no_account'] ?> 
                    <a href="/View/signup.php"><?= $t['signup_link'] ?></a>
                </p>
            </form>
            
            <div class="social-login">
                <p class="divider"><span><?= $t['or_login_with'] ?></span></p>
                
                <div class="social-buttons">
                    <a href="/Controller/google_auth.php" class="social-btn google-btn">
                        <i class="fab fa-google"></i> Google
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php require_once '../blocks/footer.php'; ?>
</body>
</html>