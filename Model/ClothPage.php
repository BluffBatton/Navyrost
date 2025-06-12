<?php
class ClothPage extends BasePage
{
    public $product;
    public function __construct($product)
    {
        $this->product = $product;
        parent::__construct($title = $product->name);
        require_once 'Model/Product.php';
    }
    public function Body(){
        include 'View/cloth.php';
    }
}