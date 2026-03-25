<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\LeaderboardService;

class LeaderboardController {
    protected LeaderboardService $leaderboardService;

    public function __construct() {
        $this->leaderboardService = new LeaderboardService();
    }

    public function global(Request $request) {
        $limit = $request->getBody()['limit'] ?? 10;
        try {
            $rankings = $this->leaderboardService->getGlobalLeaderboard((int)$limit);
            return Response::success($rankings, "Global leaderboard fetched successfully.");
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function level(Request $request) {
        $data = $request->getBody();
        $mapId = $data['map_id'] ?? null;
        $levelId = $data['level_id'] ?? null;
        $limit = $data['limit'] ?? 10;

        if (!$mapId || !$levelId) {
            return Response::error("Map ID and Level ID are required.", 400);
        }

        try {
            $rankings = $this->leaderboardService->getLevelLeaderboard((int)$mapId, (int)$levelId, (int)$limit);
            return Response::success($rankings, "Level leaderboard fetched successfully.");
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }
}
