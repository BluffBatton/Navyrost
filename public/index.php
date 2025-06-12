<?php
require_once '../router.php';

$page = $_GET['page'] ?? 'main';

$router = new Router();
$router->route($page);