<?php
// public/index.php

spl_autoload_register(function (string $class) {
    $prefix  = 'App\\';
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
use App\Controller\CarpoolController;
use App\Controller\AuthController;
use App\Controller\StaticController;

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'home':
        (new HomeController())->index();
        break;

    case 'carpools':
        (new CarpoolController())->index();
        break;

    case 'login':
        (new AuthController())->login();
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
