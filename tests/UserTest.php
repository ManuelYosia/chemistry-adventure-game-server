<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\AuthService;
use App\Services\ProgressService;
use App\Core\Database;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function runUserTests() {
    echo "Running User API Tests...\n\n";
    $authService = new AuthService();
    $progressService = new ProgressService();
    $db = Database::getInstance();

    // Clean up and create a test user
    $email = 'usertest@example.com';
    $password = 'password123';
    $username = 'usertest';
    
    $db->prepare("DELETE FROM users WHERE email = :email")->execute(['email' => $email]);
    
    try {
        $userId = $authService->register([
            'username' => $username,
            'email' => $email,
            'password' => $password
        ]);
        echo "Test Setup: User Registered (ID: $userId)\n";

        
        // 1. Mock Login to get Token
        $loginResult = $authService->login($email, $password);
        $token = $loginResult['token'];
        echo "Test Setup: Logged in (Token obtained)\n";

        // 2. Test the UserController::getUser logic manually (since we can't easily mock the full HTTP request/response with exit calls)
        echo "Test 1: Verifying getUser data retrieval... ";
        
        // Find user by token (like AuthMiddleware does)
        $user = (new \App\Repositories\UserRepository())->findByToken($token);
        if (!$user) {
            die("FAILED: Token not found in database.\n");
        }

        $progress = $progressService->getProgress($user['user_id']);
        
        $responseData = [
            'username' => $user['username'],
            'email' => $user['email'],
            'unlocked_maps' => $progress['unlocked_map_ids'] ?? [],
            'unlocked_levels' => $progress['unlocked_level_ids'] ?? [],
            'last_unlocked_map' => $progress['last_unlocked_map_id'] ?? 1
        ];

        if ($responseData['username'] === $username && 
            $responseData['email'] === $email && 
            is_array($responseData['unlocked_maps']) && 
            is_array($responseData['unlocked_levels'])) {
            echo "SUCCESS\n";
            echo "Data: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "FAILED (Data mismatch or incorrect types)\n";
            print_r($responseData);
        }

    } catch (Exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
    }

    echo "\nTests Completed.\n";
}

runUserTests();
