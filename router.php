<?php
class Router
{
    private array $routes = [
        'account' => 'accountController.php',
        'admin_chat' => 'adminChatController.php',
        'admin_day_calculator' => 'login_handler.php',
        'admin_db_test' => 'register_handler.php',
        'admin_parser' => 'logout_handler.php',
        'admin_scripts' => 'adminScriptsController.php',
        'admin_signup' => 'admin_register_handler.php',
        'admin_sql_test' => '',
        'admin_tables' => 'ProductList.php',
        'admin_xml' => 'CreatePlane.php',
        'admin' => 'ProductPreview.php',
        'cart' => 'ModeratorController.php',
        'catalog' => 'ManageShopProductController.php',
        'chat' => 'CreateDateBaseController.php',
        'cloth' => 'ModeratorSqlTester.php',
        'edit_product' => 'TestClassController.php',
        'ip_validator' => 'StringConversion.php',
        'login' => 'ConfirmNumberController.php',
        'main' => 'ConfirmDateController.php',
        'notifications' => 'UserChatController.php',
        'pages' => 'ModeratorChatController.php',
        'signup' => 'TagParserTestController.php',
    ];

    public function __construct() {}

    public function route(string $page): void
    {
        $controllerFile = $this->routes[$page] ?? 'HomeController.php';
        require_once __DIR__ . "/../controllers/{$controllerFile}";
    }
}