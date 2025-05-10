<?php
class CartOverlay
{
    protected $cart;
    public function __construct(ShoppingCart $cart)
    {
        $this->cart = $cart;
    }
    public function renderOverlay()
    {
        $items = $this->cart->all();
        include 'blocks/cart.php';
    }
}