<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Request;
use App\Core\Response;

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $router = new Router();

    // Load routes
    require_once __DIR__ . '/../app/Routes/api.php';

    $router->dispatch(new Request());
}
catch (\Exception $e) {
    Response::error($e->getMessage(), 500);
}
