<?php

namespace App\Services;

use App\Repositories\UserRepository;

class AuthService {
    protected UserRepository $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function register(array $data) {
        if ($this->userRepository->findByEmail($data['email'])) {
            throw new \Exception("Email already exists.");
        }

        if ($this->userRepository->findByUsername($data['username'])) {
            throw new \Exception("Username already taken.");
        }

        $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $userId = $this->userRepository->create($data);
        $this->userRepository->createInitialProgress((int)$userId);

        return $userId;
    }

    public function login(string $email, string $password) {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new \Exception("Invalid credentials.");
        }

        $token = bin2hex(random_bytes(32));
        $this->userRepository->updateToken($user['user_id'], $token);

        return [
            'token' => $token,
            'user' => [
                'id' => $user['user_id'],
                'username' => $user['username'],
                'email' => $user['email']
            ]
        ];
    }

    public function logout(int $userId) {
        return $this->userRepository->updateToken($userId, null);
    }
}
