<?php
class ShoppingCart
{
    protected $items;

    public function __construct()
    {
        // сесія вже стартована в BasePage::__construct
        $this->items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    }

    /**
     * Добавить товар в корзину
     * $id — ID товара
     * $qty — количество (по умолчанию 1)
     * $details — массив с информацией о товаре (например, название, цена)
     */
    public function add($id, $qty = 1, $details = [])
    {
        // Проверяем: если товар уже есть — обновляем количество
        if (isset($this->items[$id])) {
            $this->items[$id]['quantity'] += $qty;
        } else {
            // Если товара нет — добавляем с подробной информацией
            $this->items[$id] = [
                'id' => $id,
                'quantity' => $qty,
                'details' => $details, // Например: ['name' => 'Товар', 'price' => 1000]
            ];
        }
        $this->save();
    }

    /**
     * Обновить количество товара
     */
    public function update($id, $qty)
    {
        if ($qty > 0) {
            $this->items[$id]['quantity'] = $qty;
        } else {
            $this->remove($id);
        }
        $this->save();
    }

    /**
     * Удалить товар из корзины
     */
    public function remove($id)
    {
        unset($this->items[$id]);
        $this->save();
    }

    /**
     * Получить все товары в корзине
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Подсчитать общую сумму корзины
     */
    public function total()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['details']['price'] * $item['quantity'];
        }
        return $total;
    }

    /**
     * Сохранить корзину в сессии
     */
    protected function save()
    {
        $_SESSION['cart'] = $this->items;
    }

    /**
     * Очистить корзину
     */
    public function clear()
    {
        $this->items = [];
        $this->save();
    }

    /**
     * Проверить, есть ли товар в корзине
     */
    public function hasItem($id)
    {
        return isset($this->items[$id]);
    }
}