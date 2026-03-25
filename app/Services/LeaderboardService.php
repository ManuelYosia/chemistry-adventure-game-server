<?php

namespace App\Services;

use App\Repositories\LeaderboardRepository;

class LeaderboardService {
    protected LeaderboardRepository $leaderboardRepository;

    public function __construct() {
        $this->leaderboardRepository = new LeaderboardRepository();
    }

    public function getGlobalLeaderboard(int $limit = 10) {
        return $this->leaderboardRepository->getGlobalTop($limit);
    }

    public function getLevelLeaderboard(int $mapId, int $levelId, int $limit = 10) {
        return $this->leaderboardRepository->getLevelTop($mapId, $levelId, $limit);
    }
}
