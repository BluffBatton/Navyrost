<?php
class ClothPage extends BasePage
{
    public $style = 'clothStyle.css';
    public $product;
    public function __construct($product)
    {
        $this->product = $product;
        $title = $product->name;
        require_once 'Model/Product.php';
    }
    public function Body(){
        include 'cloth.php';
    }
}