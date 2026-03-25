<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Request;
use App\Core\Response;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    $router = new Router();

    // Load routes
    require_once __DIR__ . '/../app/Routes/api.php';

    $router->dispatch(new Request());
}
catch (\Exception $e) {
    Response::error($e->getMessage(), 500);
}
