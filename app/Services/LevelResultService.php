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

            if ($nextLevelId > 5) {
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
