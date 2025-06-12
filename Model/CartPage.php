<?php
class CartPage extends BasePage{
    public $products;
    public function __construct($title)
    {
        parent::__construct('Кошик');
        require_once 'Model/Product.php';
    }
    public function Body(){
        include 'View/cart.php';
    }
}