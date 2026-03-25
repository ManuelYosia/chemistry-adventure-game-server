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

function runLevelResultTests() {
    echo "Running Level Result Tests...\n\n";
    
    $authService = new AuthService();
    $resultService = new LevelResultService();
    $progressService = new ProgressService();
    $db = Database::getInstance();

    // 1. Setup: Register a test user
    echo "Setup: Registering test user... ";
    $db->prepare("DELETE FROM users WHERE email = 'result@example.com'")->execute();
    try {
        $userId = $authService->register([
            'username' => 'resultuser',
            'email' => 'result@example.com',
            'password' => 'password123'
        ]);
        echo "SUCCESS (User ID: $userId)\n";
    } catch (Exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
        return;
    }

    // 2. Test Save New Result
    echo "Test 1: Save New Level Result... ";
    $resultService->saveResult([
        'user_id' => $userId,
        'map_id' => 1,
        'level_id' => 1,
        'score' => 500,
        'stars' => 2,
        'remaining_time' => 30.5,
        'bonus_score' => 50,
        'is_completed' => true
    ]);
    
    $results = $resultService->getResults($userId);
    if (count($results) == 1 && $results[0]['score'] == 500) {
        echo "SUCCESS\n";
    } else {
        echo "FAILED\n";
        print_r($results);
    }

    // 3. Verify Progress Auto-Update (Initial)
    echo "Test 1b: Verify Progress Update (Initial)... ";
    $progress = $progressService->getProgress($userId);
    if ($progress['total_score'] == 500 && $progress['total_stars'] == 2) {
        echo "SUCCESS\n";
    } else {
        echo "FAILED (Score: " . $progress['total_score'] . ", Stars: " . $progress['total_stars'] . ")\n";
    }

    // 4. Test Save Improved Result (Better score and stars)
    echo "Test 2: Update with Better Result... ";
    $resultService->saveResult([
        'user_id' => $userId,
        'map_id' => 1,
        'level_id' => 1,
        'score' => 800,
        'stars' => 3,
        'remaining_time' => 45.0,
        'bonus_score' => 100,
        'is_completed' => true
    ]);
    
    $updatedResults = $resultService->getResults($userId);
    if ($updatedResults[0]['score'] == 800 && $updatedResults[0]['stars'] == 3) {
        echo "SUCCESS\n";
    } else {
        echo "FAILED\n";
        print_r($updatedResults);
    }

    // 5. Verify Progress Sync (Only adds difference)
    echo "Test 2b: Verify Progress Sync (Incremental)... ";
    $updatedProgress = $progressService->getProgress($userId);
    // Previous: 500 score, 2 stars. New: 800 score, 3 stars. 
    // Improvement: +300 score, +1 star.
    // Total should be: 800 score, 3 stars.
    if ($updatedProgress['total_score'] == 800 && $updatedProgress['total_stars'] == 3) {
        echo "SUCCESS\n";
    } else {
        echo "FAILED (Score: " . $updatedProgress['total_score'] . ", Stars: " . $updatedProgress['total_stars'] . ")\n";
    }

    // 6. Test Save Worse Result (Should not overwrite result table, should not update progress)
    echo "Test 3: Avoid Downgrading Result... ";
    $resultService->saveResult([
        'user_id' => $userId,
        'map_id' => 1,
        'level_id' => 1,
        'score' => 400,
        'stars' => 1,
        'remaining_time' => 10.0,
        'bonus_score' => 0,
        'is_completed' => true
    ]);
    
    $finalResults = $resultService->getResults($userId);
    $finalProgress = $progressService->getProgress($userId);
    
    if ($finalResults[0]['score'] == 800 && $finalProgress['total_score'] == 800) {
        echo "SUCCESS\n";
    } else {
        echo "FAILED (Result Score: " . $finalResults[0]['score'] . ", Progress Score: " . $finalProgress['total_score'] . ")\n";
    }

    echo "\nTests Completed.\n";
}

runLevelResultTests();
