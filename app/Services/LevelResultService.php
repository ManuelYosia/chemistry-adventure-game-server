<?php

namespace App\Services;

use App\Repositories\LevelResultRepository;
use App\Services\ProgressService;

class LevelResultService {
    protected LevelResultRepository $levelResultRepository;
    protected ProgressService $progressService;

    public function __construct() {
        $this->levelResultRepository = new LevelResultRepository();
        $this->progressService = new ProgressService();
    }

    public function getResults(int $userId) {
        return $this->levelResultRepository->findByUser($userId);
    }

    public function getMapTotalStars(int $userId, int $mapId) {
        return $this->levelResultRepository->getTotalStarsByMap($userId, $mapId);
    }

    public function getAllMapTotalStars(int $userId) {
        $results = $this->levelResultRepository->getAllMapTotalStars($userId);
        
        // Get highest unlocked map to fill in zeros for unplayed but unlocked maps
        $progress = $this->progressService->getProgress($userId);
        $maxMap = $progress['highest_unlocked_map_id'] ?? 1;

        $resultsMap = [];
        foreach ($results as $res) {
            $resultsMap[(int)$res['map_id']] = (int)$res['total_stars'];
        }

        $finalResults = [];
        for ($m = 1; $m <= $maxMap; $m++) {
            $finalResults[] = [
                'map_id' => $m,
                'total_stars' => $resultsMap[$m] ?? 0
            ];
        }

        return $finalResults;
    }

    public function getMapResults(int $userId, int $mapId) {
        return $this->levelResultRepository->findByUserAndMap($userId, $mapId);
    }

    public function saveResult(array $data) {
        $userId = (int)$data['user_id'];
        $mapId = (int)$data['map_id'];
        $levelId = (int)$data['level_id'];
        $isCompleted = !empty($data['is_completed']);

        $existing = $this->levelResultRepository->findByUserLevel($userId, $mapId, $levelId);
        
        if ($existing) {
            // Only update if current performance is better
            if ($data['score'] > $existing['score'] || $data['stars'] > $existing['stars']) {
                $this->levelResultRepository->save($data);
            }
        } else {
            $this->levelResultRepository->save($data);
        }

        // Handle Unlocks if level is completed
        if ($isCompleted) {
            $nextMapId = $mapId;
            $nextLevelId = $levelId + 1;

            if (!empty($data['is_final_level'])) {
                $nextMapId++;
                $nextLevelId = 1;
            }

            // Update highest unlocked point
            $this->progressService->updateProgress($userId, [
                'highest_unlocked_map_id' => $nextMapId,
                'highest_unlocked_level_id' => $nextLevelId
            ]);
        }

        return true;
    }
}
