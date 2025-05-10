<?php
class CatalogPage extends BasePage{
    public $style = 'catalogStyle.css';
    public $products;
    public function __construct($title)
    {
        $title = "Каталог";
        require_once 'Product.php';
    }
    public function Body(){
        include 'catalog.php';
    }
}