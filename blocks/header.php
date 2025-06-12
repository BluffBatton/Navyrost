<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = $_SESSION['lang'] ?? 'ua';
$translations = include __DIR__ . '/../lang.php';
$t = $translations[$lang];
?>

<header>
    <div class="top-header">
        <nav>
            <ul class="header-menu">
                <li><?= $t['payment'] ?></li>
                <li><?= $t['delivery'] ?></li>
                <li><?= $t['return'] ?></li>
                <li><?= $t['work_days'] ?></li>
                <li><?= $t['weekend'] ?></li>
            </ul>
        </nav>
    </div>

    <div class="middle-header">
        <div class="social-icons">
            <a href="https://www.instagram.com/navyrost.1/" target="_blank">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://x.com/Navyrost_" target="_blank">
                <i class="fab fa-twitter"></i>
            </a>
        </div>
        <div class="logo" >
            <a href="/index.php">
                <img src="/pic/logo main.png" alt="NAVYROST" />
            </a>
        </div>
<div class="action-icons" style="display: flex; align-items: center; gap: 10px;">
    <i class="fas fa-search"></i>
    <a href="/View/account.php" class="fas fa-user"></a>
    <a href="/View/cart.php" class="fas fa-shopping-bag" id="cart-icon"></a>
    
    <div class="lang-switcher" style="font-size: 14px;">
        <a href="?lang=ua" style="margin: 0 3px; text-decoration: none;">UA</a> | 
        <a href="?lang=en" style="margin: 0 3px; text-decoration: none;">EN</a>
    </div>
</div>

    </div>

    <nav class="bottom-header">
        <ul class="nav-menu">
            <li><a href="/view/catalog.php?gender=Чоловічій"><?= $t['men'] ?></a></li>
            <li><a href="/view/catalog.php?gender=Жіночій"><?= $t['women'] ?></a></li>
    </ul>
</nav>
</header>