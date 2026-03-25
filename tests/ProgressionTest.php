<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\LevelResultService;
use App\Services\AuthService;
use App\Services\ProgressService;
use App\Core\Database;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function runProgressionTests() {
    echo "Running Progression Update Logic Tests (Client-Driven)...\n\n";
    
    $authService = new AuthService();
    $resultService = new LevelResultService();
    $progressService = new ProgressService();
    $db = Database::getInstance();

    // 1. Setup: Register a test user
    echo "Setup: Registering test user... ";
    $db->prepare("DELETE FROM users WHERE email = 'progression@example.com'")->execute();
    try {
        $userId = $authService->register([
            'username' => 'progressionuser',
            'email' => 'progression@example.com',
            'password' => 'password123'
        ]);
        echo "SUCCESS (User ID: $userId)\n";
    } catch (Exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
        return;
    }

    // 2. Test Unlock Next Level via Result Save
    echo "Test 1: Complete Level 1 and Unlock Level 2... ";
    $resultService->saveResult([
        'user_id' => $userId,
        'map_id' => 1,
        'level_id' => 1,
        'score' => 1000,
        'stars' => 3,
        'remaining_time' => 60.0,
        'bonus_score' => 200,
        'is_completed' => true,
        'next_level_id' => 2 // Client signals to unlock the next level
    ]);
    
    $progress = $progressService->getProgress($userId);
    if (in_array(1, $progress['unlocked_level_ids']) && in_array(2, $progress['unlocked_level_ids'])) {
        echo "SUCCESS (Levels 1 and 2 are unlocked)\n";
    } else {
        echo "FAILED (Unlocked Levels: " . implode(',', $progress['unlocked_level_ids']) . ")\n";
    }

    // 3. Test Unlock Next Map via Result Save
    echo "Test 2: Complete Map 1 and Unlock Map 2... ";
    $resultService->saveResult([
        'user_id' => $userId,
        'map_id' => 1,
        'level_id' => 10, // Assuming 10 is the last level
        'score' => 1500,
        'stars' => 3,
        'remaining_time' => 70.0,
        'bonus_score' => 300,
        'is_completed' => true,
        'next_map_id' => 2, // Client signals to unlock next map
        'next_level_id' => 11 // And maybe the first level of that map
    ]);
    
    $finalProgress = $progressService->getProgress($userId);
    if (in_array(2, $finalProgress['unlocked_map_ids']) && $finalProgress['last_unlocked_map_id'] == 2) {
        echo "SUCCESS (Map 2 unlocked and set as last unlocked map)\n";
    } else {
        echo "FAILED (Unlocked Maps: " . implode(',', $finalProgress['unlocked_map_ids']) . ")\n";
    }

    echo "\nTests Completed.\n";
}

runProgressionTests();
