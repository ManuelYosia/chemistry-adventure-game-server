<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class LevelResultRepository {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByUser(int $userId) {
        $stmt = $this->db->prepare("SELECT * FROM level_results WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getTotalsByUser(int $userId) {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(score), 0) as total_score, COALESCE(SUM(stars), 0) as total_stars
            FROM level_results WHERE user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTotalStarsByMap(int $userId, int $mapId) {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(stars), 0) as total_stars
            FROM level_results 
            WHERE user_id = :user_id AND map_id = :map_id
        ");
        $stmt->execute([
            'user_id' => $userId, 
            'map_id' => $mapId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total_stars'];
    }

    public function getAllMapTotalStars(int $userId) {
        $stmt = $this->db->prepare("
            SELECT map_id, COALESCE(SUM(stars), 0) as total_stars
            FROM level_results 
            WHERE user_id = :user_id
            GROUP BY map_id
            ORDER BY map_id ASC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUserAndMap(int $userId, int $mapId) {
        $stmt = $this->db->prepare("
            SELECT * FROM level_results 
            WHERE user_id = :user_id AND map_id = :map_id
            ORDER BY level_id ASC
        ");
        $stmt->execute([
            'user_id' => $userId,
            'map_id' => $mapId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUserLevel(int $userId, int $mapId, int $levelId) {
        $stmt = $this->db->prepare("
            SELECT * FROM level_results 
            WHERE user_id = :user_id AND map_id = :map_id AND level_id = :level_id
        ");
        $stmt->execute([
            'user_id' => $userId,
            'map_id' => $mapId,
            'level_id' => $levelId
        ]);
        return $stmt->fetch();
    }

    public function save(array $data) {
        $existing = $this->findByUserLevel($data['user_id'], $data['map_id'], $data['level_id']);

        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE level_results SET 
                    score = :score, 
                    stars = :stars, 
                    remaining_time = :remaining_time, 
                    bonus_score = :bonus_score,
                    is_completed = :is_completed
                WHERE result_id = :result_id
            ");
            return $stmt->execute([
                'score' => $data['score'],
                'stars' => $data['stars'],
                'remaining_time' => $data['remaining_time'] ?? 0,
                'bonus_score' => $data['bonus_score'] ?? 0,
                'is_completed' => (isset($data['is_completed']) && $data['is_completed']) ? 1 : 0,
                'result_id' => $existing['result_id']
            ]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO level_results (user_id, map_id, level_id, score, stars, remaining_time, bonus_score, is_completed)
                VALUES (:user_id, :map_id, :level_id, :score, :stars, :remaining_time, :bonus_score, :is_completed)
            ");
            return $stmt->execute([
                'user_id' => $data['user_id'],
                'map_id' => $data['map_id'],
                'level_id' => $data['level_id'],
                'score' => $data['score'],
                'stars' => $data['stars'],
                'remaining_time' => $data['remaining_time'] ?? 0,
                'bonus_score' => $data['bonus_score'] ?? 0,
                'is_completed' => (isset($data['is_completed']) && $data['is_completed']) ? 1 : 0
            ]);
        }
    }
}
