<?php
class MainPage extends BasePage
{
    private $limit;
    public $style = 'mainStyle.css';
    protected $products = [];
    public function __construct($limit = 8)
    {
        parent::__construct("Головна");
        $this->limit = $limit;
    }

    public function Body(){
        $sliderImages = [
            'slider2.png',
            'slider1.png',
            'slider3.png',
            'slider4.png',
            'slider5.png',
            'slider6.png',
        ];
        $all = Product::generateDummyData();
        // Перемішуємо
        shuffle($all);
        // Відбираємо тільки ті, що вписуються у ліміт
        $this->products = array_slice($all, 0, $this->limit);
        include 'View/main.php';
    }
} 