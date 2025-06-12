<?php
require_once __DIR__ . '/../functions/Database.php';

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

    

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->price = $data['price'] ?? 0;
        $this->image = $data['image'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->gender = $data['gender'] ?? '';
        $this->brand = $data['brand'] ?? '';
        $this->category = $data['category'] ?? null;
        $this->size = !empty($data['sizes']) ? explode(',', $data['sizes']) : [];
        $this->color = !empty($data['colors']) ? explode(',', $data['colors']) : [];
    }

    public static function fetchAll(array $filters = [])
    {
        $db = new Database(__DIR__ . '/../SQLite/Mydatabase/Navyrost.db');
        
        $sql = "SELECT 
                p.id, p.name, p.price, p.image, p.description,
                p.gender, p.brand,
                (SELECT GROUP_CONCAT(DISTINCT s.name) 
                 FROM product_sizes ps 
                 JOIN sizes s ON ps.size_id = s.id 
                 WHERE ps.product_id = p.id) AS sizes,
                (SELECT GROUP_CONCAT(DISTINCT c.name) 
                 FROM product_colors pc 
                 JOIN colors c ON pc.color_id = c.id 
                 WHERE pc.product_id = p.id) AS colors,
                (SELECT GROUP_CONCAT(DISTINCT cat.name)
                 FROM product_categories pc
                 JOIN categories cat ON pc.category_id = cat.id
                 WHERE pc.product_id = p.id) AS category
            FROM products p
            WHERE 1=1";
        
        $params = [];
        
        // обробка фільтрів
        if (!empty($filters['gender'])) {
            $sql .= " AND p.gender = :gender";
            $params['gender'] = $filters['gender'];
        }
        
        if (!empty($filters['brands'])) {
            $brands = is_array($filters['brands']) ? $filters['brands'] : explode(',', $filters['brands']);
            $placeholders = [];
            foreach ($brands as $i => $brand) {
                $paramName = 'brand_' . $i;
                $placeholders[] = ':' . $paramName;
                $params[$paramName] = $brand;
            }
            $sql .= " AND p.brand IN (" . implode(',', $placeholders) . ")";
        }
        
        if (!empty($filters['categories'])) {
            $categories = is_array($filters['categories']) ? $filters['categories'] : explode(',', $filters['categories']);
            $placeholders = [];
            foreach ($categories as $i => $category) {
                $paramName = 'category_' . $i;
                $placeholders[] = ':' . $paramName;
                $params[$paramName] = $category;
            }
            $sql .= " AND EXISTS (SELECT 1 FROM product_categories pc JOIN categories c ON pc.category_id = c.id 
                      WHERE pc.product_id = p.id AND c.name IN (" . implode(',', $placeholders) . "))";
        }
        
        if (!empty($filters['sizes'])) {
            $sizes = is_array($filters['sizes']) ? $filters['sizes'] : explode(',', $filters['sizes']);
            $placeholders = [];
            foreach ($sizes as $i => $size) {
                $paramName = 'size_' . $i;
                $placeholders[] = ':' . $paramName;
                $params[$paramName] = $size;
            }
            $sql .= " AND EXISTS (SELECT 1 FROM product_sizes ps JOIN sizes s ON ps.size_id = s.id 
                      WHERE ps.product_id = p.id AND s.name IN (" . implode(',', $placeholders) . "))";
        }
        
        if (!empty($filters['colors'])) {
            $colors = is_array($filters['colors']) ? $filters['colors'] : explode(',', $filters['colors']);
            $placeholders = [];
            foreach ($colors as $i => $color) {
                $paramName = 'color_' . $i;
                $placeholders[] = ':' . $paramName;
                $params[$paramName] = $color;
            }
            $sql .= " AND EXISTS (SELECT 1 FROM product_colors pc JOIN colors c ON pc.color_id = c.id 
                      WHERE pc.product_id = p.id AND c.name IN (" . implode(',', $placeholders) . "))";
        }
        
        if (!empty($filters['min_price']) && is_numeric($filters['min_price'])) {
            $sql .= " AND p.price >= :min_price";
            $params['min_price'] = (float)$filters['min_price'];
        }
        
        if (!empty($filters['max_price']) && is_numeric($filters['max_price'])) {
            $sql .= " AND p.price <= :max_price";
            $params['max_price'] = (float)$filters['max_price'];
        }
        
        $sql .= " ORDER BY p.id DESC";
        
        try {
            $rows = $db->execQuery($sql, $params);
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }

        $products = [];
        foreach ($rows as $row) {
            $products[] = new self($row);
        }

        return $products;
    }

    public static function getAvailableBrands()
    {
        $db = new Database(__DIR__ . '/../SQLite/Mydatabase/Navyrost.db');
        $sql = "SELECT DISTINCT brand FROM products ORDER BY brand";
        
        try {
            $rows = $db->execQuery($sql);
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
        
        return array_column($rows, 'brand');
    }

    public static function getAvailableCategories()
    {
        $db = new Database(__DIR__ . '/../SQLite/Mydatabase/Navyrost.db');
        $sql = "SELECT DISTINCT name FROM categories ORDER BY name";
        
        try {
            $rows = $db->execQuery($sql);
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
        
        return array_column($rows, 'name');
    }

    public static function getAvailableSizes()
    {
        $db = new Database(__DIR__ . '/../SQLite/Mydatabase/Navyrost.db');
        $sql = "SELECT DISTINCT name FROM sizes ORDER BY name";
        
        try {
            $rows = $db->execQuery($sql);
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
        
        return array_column($rows, 'name');
    }

    public static function getAvailableColors()
    {
        $db = new Database(__DIR__ . '/../SQLite/Mydatabase/Navyrost.db');
        $sql = "SELECT DISTINCT name FROM colors ORDER BY name";
        
        try {
            $rows = $db->execQuery($sql);
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
        
        return array_column($rows, 'name');
    }

    public static function findById(int $id): Product
    {
        $db = new Database(__DIR__ . '/../SQLite/Mydatabase/Navyrost.db');

        $sql = "SELECT 
                p.id, p.name, p.price, p.image, p.description,
                p.gender, p.brand,
                (SELECT GROUP_CONCAT(DISTINCT s.name) 
                 FROM product_sizes ps 
                 JOIN sizes s ON ps.size_id = s.id 
                 WHERE ps.product_id = p.id) AS sizes,
                (SELECT GROUP_CONCAT(DISTINCT c.name) 
                 FROM product_colors pc 
                 JOIN colors c ON pc.color_id = c.id 
                 WHERE pc.product_id = p.id) AS colors,
                (SELECT GROUP_CONCAT(DISTINCT cat.name)
                 FROM product_categories pc
                 JOIN categories cat ON pc.category_id = cat.id
                 WHERE pc.product_id = p.id) AS category
            FROM products p
            WHERE p.id = :id";

        try {
            $rows = $db->execQuery($sql, ['id' => $id]);
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return new self([]);
        }

        return !empty($rows) ? new self($rows[0]) : new self([]);
    }
}