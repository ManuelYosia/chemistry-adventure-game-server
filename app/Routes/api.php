<?php

/** @var \App\Core\Router $router */

use App\Controllers\TestController;
use App\Controllers\UserController;

$router->get('/', function () {
    return \App\Core\Response::success([], "Chemistry Adventure API is online.");
});

$router->get('/test', [TestController::class , 'index']);

$router->get('/user', [UserController::class , 'index']);
