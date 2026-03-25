<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\ProgressService;
use App\Services\AuthService;
use App\Core\Database;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function runProgressTests() {
    echo "Running Player Progress Tests...\n\n";
    
    $authService = new AuthService();
    $progressService = new ProgressService();
    $db = Database::getInstance();

    // 1. Setup: Register a test user
    echo "Setup: Registering test user... ";
    $db->prepare("DELETE FROM users WHERE email = 'progress@example.com'")->execute();
    try {
        $userId = $authService->register([
            'username' => 'progressuser',
            'email' => 'progress@example.com',
            'password' => 'password123'
        ]);
        echo "SUCCESS (User ID: $userId)\n";
    } catch (Exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
        return;
    }

    // 2. Test Fetch Initial Progress
    echo "Test 1: Fetch Initial Progress... ";
    $progress = $progressService->getProgress($userId);
    if ($progress && $progress['last_unlocked_map_id'] == 1 && count($progress['unlocked_map_ids']) == 1) {
        echo "SUCCESS\n";
    } else {
        echo "FAILED\n";
        print_r($progress);
    }

    // 3. Test Update Progress (Unlock Level 2)
    echo "Test 2: Update Progress (Unlock Level 2)... ";
    $progressService->updateProgress($userId, ['level_id' => 2, 'score' => 100, 'stars' => 3]);
    $updated = $progressService->getProgress($userId);
    if (in_array(2, $updated['unlocked_level_ids']) && $updated['total_score'] == 100 && $updated['total_stars'] == 3) {
        echo "SUCCESS\n";
    } else {
        echo "FAILED\n";
        print_r($updated);
    }

    // 4. Test Update Progress (Unlock Map 2)
    echo "Test 3: Update Progress (Unlock Map 2)... ";
    $progressService->updateProgress($userId, ['map_id' => 2, 'last_unlocked_map_id' => 2]);
    $updatedMap = $progressService->getProgress($userId);
    if (in_array(2, $updatedMap['unlocked_map_ids']) && $updatedMap['last_unlocked_map_id'] == 2) {
        echo "SUCCESS\n";
    } else {
        echo "FAILED\n";
        print_r($updatedMap);
    }

    // 5. Test Update Progress (Duplicate Level - Should not add)
    echo "Test 4: Update Progress (Duplicate Level)... ";
    $progressService->updateProgress($userId, ['level_id' => 2]);
    $final = $progressService->getProgress($userId);
    if (count($final['unlocked_level_ids']) == 2) { // 1 and 2
        echo "SUCCESS (No duplicates)\n";
    } else {
        echo "FAILED (Duplicates found or count incorrect)\n";
        print_r($final['unlocked_level_ids']);
    }

    echo "\nTests Completed.\n";
}

runProgressTests();
