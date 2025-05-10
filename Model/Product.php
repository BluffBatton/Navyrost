<?php
class Product
{
    public $id;
    public $name;
    public $category;
    public $gender;
    public $price;
    public $image;
    public $description;
    public $brand;
    public $size;
    public $color;

    public function __construct($id, $name, $category, $gender, $price, $image, $description, $brand, $size, $color)
    {
        $this->id = $id;
        $this->name = $name;
        $this->category = $category;
        $this->gender = $gender;
        $this->price = $price;
        $this->image = $image;
        $this->description = $description;
        $this->brand = $brand;
        $this->size = $size;
        $this->color = $color;
    }

    public static function generateDummyData()
    {
        return [
            new self(1, 'Футболка Adidas', 'Футболки', 'Чоловічій', 1200, 'pic/nv3.png', 'Класична футболка для спорту', 'Adidas', ['S', 'M', 'L'], 'Білий'),
            new self(2, 'Шорти Nike', 'Шорти', 'Чоловічій', 2000, 'pic/nv11.png', 'Легкі шорти для тренувань', 'Nike', ['L', 'XL'], 'Чорний'),
            new self(3, 'Кофта Puma Hoodie', 'Кофти', 'Жіночій', 3500, 'pic/slider4.png', 'Тепла та зручна кофта', 'Puma', ['M', 'L', 'XL'], 'Сірий'),
            new self(4, 'Кросівки Reebok Runner', 'Кросівки', 'Чоловічій', 4200, 'pic/yung1.png', 'Ідеальні для бігу', 'Reebok', ['41', '42', '43'], 'Синій'),
            new self(5, 'Штани Jordan', 'Штани', 'Чоловічій', 4500, 'pic/nv6.png', 'Спортивні штани для активного відпочинку', 'Jordan', ['M', 'L', 'XL'], 'Червоний'),
            new self(6, 'Футболка Under Armour', 'Футболки', 'Чоловічій', 1300, 'pic/slider5.png', 'Рекомендується для інтенсивних тренувань', 'Under Armour', ['S', 'M', 'L'], 'Синій'),
            new self(7, 'Шорти Asics', 'Шорти', 'Жіночій', 1800, 'pic/nv11.png', 'Шорти для залу та відпочинку', 'Asics', ['M', 'L'], 'Білий'),
            new self(8, 'Кофта The North Face', 'Кофти', 'Чоловічій', 4000, 'pic/slider1.png', 'Зручна кофта для холодної погоди', 'The North Face', ['S', 'M', 'L'], 'Чорний'),
            new self(9, 'Кросівки New Balance 574', 'Кросівки', 'Чоловічій', 5000, 'pic/New_Balance.png', 'Класичні кросівки для прогулянок', 'New Balance', ['40', '41', '42'], 'Сірий'),
            new self(10, 'Штани Adidas Joggers', 'Штани', 'Чоловічій', 3000, 'pic/slider6.png', 'Легкі та стильні штани', 'Adidas', ['L', 'XL'], 'Оливковий'),
            new self(11, 'Футболка Lacoste', 'Футболки', 'Чоловічій', 2500, 'pic/slider2.png', 'Елегантна футболка із логотипом', 'Lacoste', ['S', 'M'], 'Чорний'),
            new self(12, 'Шорти Columbia', 'Шорти', 'Чоловічій', 2200, 'pic/nv6.png', 'Шорти для подорожей і трекінгу', 'Columbia', ['L', 'XL'], 'Хакі'),
        ];
    }
}