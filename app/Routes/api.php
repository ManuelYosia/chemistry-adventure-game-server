<?php

/** @var \App\Core\Router $router */

use App\Controllers\TestController;
use App\Controllers\UserController;
use App\Controllers\AuthController;
use App\Controllers\ProgressController;
use App\Controllers\LevelResultController;

$router->get('/', function () {
    return \App\Core\Response::success([], "Chemistry Adventure API is online.");
});

$router->get('/test', [TestController::class , 'index']);
$router->get('/user', [UserController::class , 'index']);

// Authentication Routes
$router->post('/register', [AuthController::class, 'register']);
$router->post('/login', [AuthController::class, 'login']);

// Progress Routes
$router->get('/progress', [ProgressController::class, 'index']);
$router->post('/progress/update', [ProgressController::class, 'update']);

// Level Result Routes
$router->get('/user/results', [LevelResultController::class, 'index']);
$router->post('/level-result/save', [LevelResultController::class, 'save']);
