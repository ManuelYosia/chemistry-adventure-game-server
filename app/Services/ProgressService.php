<?php

namespace App\Services;

use App\Repositories\ProgressRepository;

class ProgressService {
    protected ProgressRepository $progressRepository;

    public function __construct() {
        $this->progressRepository = new ProgressRepository();
    }

    public function getProgress(int $userId) {
        $progress = $this->progressRepository->findByUserId($userId);
        if ($progress) {
            $progress['unlocked_map_ids'] = json_decode($progress['unlocked_map_ids'], true);
            $progress['unlocked_level_ids'] = json_decode($progress['unlocked_level_ids'], true);
        }
        return $progress;
    }

    public function updateProgress(int $userId, array $newData) {
        $current = $this->getProgress($userId);
        if (!$current) {
            throw new \Exception("Progress record not found.");
        }

        $updateData = [];

        if (isset($newData['level_ids'])) {
            $levels = $current['unlocked_level_ids'];
            $changed = false;
            foreach ((array)$newData['level_ids'] as $lid) {
                if (!in_array($lid, $levels)) {
                    $levels[] = $lid;
                    $changed = true;
                }
            }
            if ($changed) $updateData['unlocked_level_ids'] = $levels;
        } elseif (isset($newData['level_id'])) {
            $levels = $current['unlocked_level_ids'];
            if (!in_array($newData['level_id'], $levels)) {
                $levels[] = $newData['level_id'];
                $updateData['unlocked_level_ids'] = $levels;
            }
        }

        if (isset($newData['map_ids'])) {
            $maps = $current['unlocked_map_ids'];
            $changed = false;
            foreach ((array)$newData['map_ids'] as $mid) {
                if (!in_array($mid, $maps)) {
                    $maps[] = $mid;
                    $changed = true;
                }
            }
            if ($changed) $updateData['unlocked_map_ids'] = $maps;
        } elseif (isset($newData['map_id'])) {
            $maps = $current['unlocked_map_ids'];
            if (!in_array($newData['map_id'], $maps)) {
                $maps[] = $newData['map_id'];
                $updateData['unlocked_map_ids'] = $maps;
            }
        }

        if (isset($newData['score'])) {
            $updateData['total_score'] = $current['total_score'] + $newData['score'];
        }

        if (isset($newData['stars'])) {
            $updateData['total_stars'] = $current['total_stars'] + $newData['stars'];
        }

        if (isset($newData['last_unlocked_map_id'])) {
            $updateData['last_unlocked_map_id'] = $newData['last_unlocked_map_id'];
        }

        if (empty($updateData)) {
            return true;
        }

        return $this->progressRepository->update($userId, $updateData);
    }
}
