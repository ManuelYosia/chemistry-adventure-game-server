<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class LeaderboardRepository {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getGlobalTop(int $limit = 10) {
        $stmt = $this->db->prepare("
            SELECT 
                u.username,
                COALESCE(SUM(lr.score), 0) AS total_score,
                COALESCE(SUM(lr.stars), 0) AS total_stars,
                p.highest_unlocked_map_id,
                p.highest_unlocked_level_id
            FROM player_progress p
            JOIN users u ON p.user_id = u.user_id
            LEFT JOIN level_results lr ON lr.user_id = p.user_id
            GROUP BY p.user_id, u.username, p.highest_unlocked_map_id, p.highest_unlocked_level_id
            ORDER BY total_score DESC, total_stars DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getLevelTop(int $mapId, int $levelId, int $limit = 10) {
        $stmt = $this->db->prepare("
            SELECT u.username, lr.score, lr.stars, lr.remaining_time
            FROM level_results lr
            JOIN users u ON lr.user_id = u.user_id
            WHERE lr.map_id = :map_id AND lr.level_id = :level_id
            ORDER BY lr.score DESC, lr.stars DESC, lr.remaining_time DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':map_id', $mapId, PDO::PARAM_INT);
        $stmt->bindValue(':level_id', $levelId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
