<?php

require_once "./Model/Order.php";

require_once "./Model/OrderItem.php";

class OrderController {
    public function index(): void {
        session_start();
        $userId = $_SESSION['user_id'] ?? 0;
        if (!$userId) {
            header('Location: /login.php');
            exit;
        }
        $orders = Order::getByUser($userId);
        require __DIR__ . '/../View/orders/index.php';
    }

    public function show(int $id): void {
        session_start();
        $userId = $_SESSION['user_id'] ?? 0;
        if (!$userId) {
            header('Location: /login.php');
            exit;
        }
        $order = Order::getById($id, $userId);
        if (!$order) {
            header('HTTP/1.0 404 Not Found');
            echo 'Замовлення не знайдено';
            exit;
        }
        $items = OrderItem::getByOrder($id);
        require __DIR__ . '/../View/orders/show.php';
    }
}
