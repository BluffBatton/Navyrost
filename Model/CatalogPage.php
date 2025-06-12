<?php
class CatalogPage extends BasePage{
    public $products;
    public function __construct($title)
    {
        parent::__construct('Каталог');
        require_once 'Model/Product.php';
    }
    public function Body(){
        include 'View/catalog.php';
    }
}