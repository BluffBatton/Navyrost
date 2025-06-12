<?php
class BasePage
{
    public $title;
    public function __construct($title)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $this->title = $title;
    }
    public function Header()
    {
        include 'blocks/header.php';
    }
    public function Body(){
        include 'View/main.php';
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