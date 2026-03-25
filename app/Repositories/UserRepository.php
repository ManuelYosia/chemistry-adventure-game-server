<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class UserRepository {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function findByUsername(string $username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    public function create(array $data) {
        $stmt = $this->db->prepare("
            INSERT INTO users (username, email, password_hash) 
            VALUES (:username, :email, :password_hash)
        ");
        $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => $data['password_hash']
        ]);
        return $this->db->lastInsertId();
    }

    public function updateToken(int $userId, ?string $token) {
        $stmt = $this->db->prepare("UPDATE users SET auth_token = :token WHERE user_id = :id");
        return $stmt->execute(['token' => $token, 'id' => $userId]);
    }
}
