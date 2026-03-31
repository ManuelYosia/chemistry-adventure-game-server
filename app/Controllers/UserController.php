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
            'unlocked_maps' => $progress['unlocked_map_ids'] ?? [],
            'unlocked_levels' => $progress['unlocked_level_ids'] ?? [],
            'last_unlocked_map' => $progress['last_unlocked_map_id'] ?? 1
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