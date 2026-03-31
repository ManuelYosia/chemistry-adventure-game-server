<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ProgressRepository {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByUserId(int $userId) {
        $stmt = $this->db->prepare("SELECT * FROM player_progress WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch();
    }

    public function update(int $userId, array $data) {
        if (empty($data)) return false;

        $fields = [];
        $params = ['user_id' => $userId];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }

        $fieldList = implode(', ', $fields);
        $stmt = $this->db->prepare("UPDATE player_progress SET $fieldList WHERE user_id = :user_id");
        return $stmt->execute($params);
    }
}
