<?php
class QuantityDiscountStrategy implements DiscountStrategyInterface {
    private int $minQuantity;
    private float $discountPercent;

    public function __construct(int $minQuantity, float $discountPercent) {
        $this->minQuantity = $minQuantity;
        $this->discountPercent = $discountPercent;
    }

    public function applyDiscount(float $total): float {
        return $total * (1 - $this->discountPercent / 100);
    }

    public function getDiscountText(): string {
        return "Знижка {$this->discountPercent}% за {$this->minQuantity}+ товарів";
    }
}