<?php
interface DiscountStrategyInterface {
    public function applyDiscount(float $total): float;
    public function getDiscountText(): string;
}