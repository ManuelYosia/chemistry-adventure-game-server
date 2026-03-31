<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Request;
use App\Services\ProgressService;

class UserController
{
    protected ProgressService $progressService;

    public function __construct()
    {
        $this->progressService = new ProgressService();
    }

    public function getUser(Request $request)
    {
        $user = $request->getBody()['user_auth'] ?? null;
        if (!$user) {
            return Response::error("Unauthorized.", 401);
        }

        $progress = $this->progressService->getProgress($user['user_id']);

        return Response::success([
            'username' => $user['username'],
            'email' => $user['email'],
            'highest_unlocked_map' => $progress['highest_unlocked_map_id'],
            'highest_unlocked_level' => $progress['highest_unlocked_level_id'],
            'total_score' => $progress['total_score'],
            'total_stars' => $progress['total_stars']
        ], "User profile retrieved.");
    }

    public function index(Request $request)
    {
        return Response::success([
            'method' => $request->getMethod(),
            'path' => $request->getPath(),
            'query' => $_GET
        ], "User successful.");
    }
}