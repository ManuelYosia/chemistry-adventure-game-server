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

        $existing = $this->levelResultRepository->findByUserLevel($userId, $mapId, $levelId);
        
        // Calculate improvements
        $scoreImprovement = 0;
        $starsImprovement = 0;

        if ($existing) {
            $scoreImprovement = max(0, $data['score'] - $existing['score']);
            $starsImprovement = max(0, $data['stars'] - $existing['stars']);
            
            // Only update if current performance is better
            if ($data['score'] > $existing['score'] || $data['stars'] > $existing['stars']) {
                $this->levelResultRepository->save($data);
            }
        } else {
            $scoreImprovement = $data['score'];
            $starsImprovement = $data['stars'];
            $this->levelResultRepository->save($data);
        }

        // Update total player progress
        if ($scoreImprovement > 0 || $starsImprovement > 0) {
            $this->progressService->updateProgress($userId, [
                'score' => $scoreImprovement,
                'stars' => $starsImprovement,
                'level_id' => $levelId, // Mark level as unlocked/completed in progress too
                'map_id' => $mapId
            ]);
        }

        return true;
    }
}
