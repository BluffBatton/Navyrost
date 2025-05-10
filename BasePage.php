<?php
class BasePage
{
    public $title;
    public $style = 'mainStyle.css';
    public function __construct($title)
    {
        if (session_status() === PHP_SESSION_NONE) { // Проверка статуса сессии
            session_start();
        }

        if (!isset($_SESSION['cart'])) { // Если корзина не задана, создаем её
            $_SESSION['cart'] = [];
        }

        $this->title = $title;
    }
    public function Header()
    {
        include 'blocks/header.php';
        include 'blocks/cart.php';
    }
    public function Body(){
        include 'main.php';
    }
    public function Footer(){
        include 'blocks/footer.php';
    }
    public function render(){
        $this->Header();
        $this->Body();
        $this->Footer();
    }
}