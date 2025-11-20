<?php
// public/index.php
session_set_cookie_params([
    'secure'   => false,   // ⚠️ false en local (HTTP), true en production (HTTPS)
    'httponly' => true,
    'samesite' => 'Lax',
]);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../src/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Controller\HomeController;
use App\Controller\StaticController;
use App\Controller\CarpoolController;
use App\Controller\AuthController;

$page = $_REQUEST['page'] ?? 'home';

switch ($page) {
    case 'home':
        (new HomeController())->index();
        break;

    case 'carpools':
        (new CarpoolController())->index();
        break;
    
    case 'carpool_show':            
        (new CarpoolController())->show();
        break;
    
    case 'carpool_participate':
        (new CarpoolController())->participate();
        break;

    case 'login':
        (new AuthController())->login();
        break;
    
    case 'logout': 
        (new AuthController())->logout();
        break;

    case 'register':
        (new AuthController())->register();
        break;

    case 'account': 
        (new AuthController())->account();
        break;

    case 'contact':
        (new StaticController())->contact();
        break;

    case 'legal':
        (new StaticController())->legal();
        break;

    default:
        http_response_code(404);
        echo 'Page not found';
}