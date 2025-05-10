<?php
// cart-handler.php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

// получаем корзину
$cart = $_SESSION['cart'] ?? [];

// читаем JSON-запрос
$request = json_decode(file_get_contents('php://input'), true);
if (!$request) {
    echo json_encode(['success'=>false,'message'=>'Invalid request']);
    exit;
}

$id      = (int)$request['id'];
$action  = $request['action'];
$details = $request['details'] ?? [];

switch ($action) {
    case 'add':
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = ['id'=>$id,'quantity'=>1,'details'=>$details];
        }
        break;

    case 'increase':
        if (isset($cart[$id])) $cart[$id]['quantity']++;
        break;

    case 'decrease':
        if (isset($cart[$id])) {
            $cart[$id]['quantity']--;
            if ($cart[$id]['quantity'] < 1) {
                unset($cart[$id]);
            }
        }
        break;

    case 'remove':
        unset($cart[$id]);
        break;
}

$_SESSION['cart'] = $cart;
echo json_encode(['success'=>true,'cart'=>$cart]);
