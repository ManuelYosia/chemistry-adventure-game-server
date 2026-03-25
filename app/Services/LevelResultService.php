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

        // Update total player progress & Unlocks
        $progressUpdate = [
            'score' => $scoreImprovement,
            'stars' => $starsImprovement,
            'level_ids' => [$levelId],
            'map_ids' => [$mapId]
        ];

        if (isset($data['next_level_id'])) {
            $progressUpdate['level_ids'][] = $data['next_level_id'];
        }
        if (isset($data['next_map_id'])) {
            $progressUpdate['map_ids'][] = $data['next_map_id'];
            $progressUpdate['last_unlocked_map_id'] = $data['next_map_id'];
        }

        if ($scoreImprovement > 0 || $starsImprovement > 0 || isset($data['next_level_id']) || isset($data['next_map_id'])) {
            $this->progressService->updateProgress($userId, $progressUpdate);
        }

        return true;
    }
}
