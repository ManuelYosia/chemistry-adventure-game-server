<?php

namespace App\Services;

use App\Repositories\ProgressRepository;
use App\Repositories\LevelResultRepository;

class ProgressService {
    protected ProgressRepository $progressRepository;
    protected LevelResultRepository $levelResultRepository;

    public function __construct() {
        $this->progressRepository = new ProgressRepository();
        $this->levelResultRepository = new LevelResultRepository();
    }

    public function getProgress(int $userId) {
        $progress = $this->progressRepository->findByUserId($userId);
        if ($progress) {
            // Calculate totals from level_results
            $totals = $this->levelResultRepository->getTotalsByUser($userId);
            $progress['total_score'] = (int)$totals['total_score'];
            $progress['total_stars'] = (int)$totals['total_stars'];
        }
        return $progress;
    }

    public function updateProgress(int $userId, array $newData) {
        $current = $this->progressRepository->findByUserId($userId);
        if (!$current) {
            throw new \Exception("Progress record not found.");
        }

        $updateData = [];

        // In linear progression, we only update if the new map/level is higher than the current one
        if (isset($newData['highest_unlocked_map_id'])) {
            if ($newData['highest_unlocked_map_id'] > $current['highest_unlocked_map_id']) {
                $updateData['highest_unlocked_map_id'] = $newData['highest_unlocked_map_id'];
                // Reset level to 1 for the new map
                $updateData['highest_unlocked_level_id'] = 1;
            }
        }

        if (isset($newData['highest_unlocked_level_id'])) {
            // Only update level if we are on the same highest map or a new one was already set in $updateData
            $currentMap = $updateData['highest_unlocked_map_id'] ?? $current['highest_unlocked_map_id'];
            
            // Check if level needs update within the same map context
            if (!isset($updateData['highest_unlocked_map_id']) || $newData['highest_unlocked_map_id'] == $current['highest_unlocked_map_id']) {
                if ($newData['highest_unlocked_level_id'] > $current['highest_unlocked_level_id']) {
                    $updateData['highest_unlocked_level_id'] = $newData['highest_unlocked_level_id'];
                }
            }
        }

        if (empty($updateData)) {
            return true;
        }

        return $this->progressRepository->update($userId, $updateData);
    }
}
