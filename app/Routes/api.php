<?php

/** @var \App\Core\Router $router */

use App\Controllers\TestController;
use App\Controllers\UserController;
use App\Controllers\AuthController;
use App\Controllers\ProgressController;
use App\Controllers\LevelResultController;
use App\Controllers\LeaderboardController;
use App\Middleware\AuthMiddleware;

$router->get('/', function () {
    return \App\Core\Response::success([], "Chemistry Adventure API is online.");
});

// Authentication Routes
$router->post('/register', [AuthController::class , 'register']);
$router->post('/login', [AuthController::class , 'login']);
$router->post('/logout', [AuthController::class , 'logout'])->middleware(AuthMiddleware::class);
$router->get('/user', [UserController::class , 'getUser'])->middleware(AuthMiddleware::class);

// Progress Routes
$router->get('/progress', [ProgressController::class , 'index'])->middleware(AuthMiddleware::class);
$router->post('/progress/update', [ProgressController::class , 'update'])->middleware(AuthMiddleware::class);

// Level Result Routes
$router->get('/user/results', [LevelResultController::class , 'index'])->middleware(AuthMiddleware::class);
$router->get('/level-result/map-stars', [LevelResultController::class , 'mapStars'])->middleware(AuthMiddleware::class);
$router->get('/level-result/map', [LevelResultController::class , 'mapResults'])->middleware(AuthMiddleware::class);
$router->post('/level-result/save', [LevelResultController::class , 'save'])->middleware(AuthMiddleware::class);

// Leaderboard Routes
$router->get('/leaderboard/global', [LeaderboardController::class , 'global']);
$router->get('/leaderboard/level', [LeaderboardController::class , 'level']);