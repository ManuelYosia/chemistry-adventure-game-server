<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\AuthService;
use App\Services\LevelResultService;
use App\Services\LeaderboardService;
use App\Core\Database;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function runLeaderboardTests() {
    echo "Running Leaderboard Module Tests...\n\n";
    
    $authService = new AuthService();
    $resultService = new LevelResultService();
    $leaderboardService = new LeaderboardService();
    $db = Database::getInstance();

    // 1. Setup: Register two test users
    echo "Setup: Registering test users... ";
    $db->prepare("DELETE FROM users WHERE email IN ('top1@example.com', 'top2@example.com')")->execute();
    
    $u1 = $authService->register(['username' => 'alice', 'email' => 'top1@example.com', 'password' => 'pass123']);
    $u2 = $authService->register(['username' => 'bob', 'email' => 'top2@example.com', 'password' => 'pass123']);
    echo "SUCCESS (IDs: $u1, $u2)\n";

    // 2. Setup: Save results
    echo "Setup: Saving level results... ";
    // Alice gets 1000 score
    $resultService->saveResult([
        'user_id' => $u1, 'map_id' => 1, 'level_id' => 1, 'score' => 1000, 'stars' => 3, 
        'remaining_time' => 50, 'bonus_score' => 100, 'is_completed' => true
    ]);
    // Bob gets 800 score
    $resultService->saveResult([
        'user_id' => $u2, 'map_id' => 1, 'level_id' => 1, 'score' => 800, 'stars' => 2, 
        'remaining_time' => 40, 'bonus_score' => 50, 'is_completed' => true
    ]);
    echo "SUCCESS\n";

    // 3. Test Global Leaderboard
    echo "Test 1: Global Leaderboard Order... ";
    $global = $leaderboardService->getGlobalLeaderboard(20);
    $aliceRank = -1;
    $bobRank = -1;
    foreach ($global as $rank => $entry) {
        if ($entry['username'] == 'alice') $aliceRank = $rank;
        if ($entry['username'] == 'bob') $bobRank = $rank;
    }
    if ($aliceRank !== -1 && $bobRank !== -1 && $aliceRank < $bobRank) {
        echo "SUCCESS (Alice > Bob)\n";
    } else {
        echo "FAILED (Alice Rank: $aliceRank, Bob Rank: $bobRank)\n";
        print_r($global);
    }

    // 4. Test Level Leaderboard
    echo "Test 2: Level Leaderboard Order... ";
    $level = $leaderboardService->getLevelLeaderboard(1, 1, 20);
    $aliceRankL = -1;
    $bobRankL = -1;
    foreach ($level as $rank => $entry) {
        if ($entry['username'] == 'alice') $aliceRankL = $rank;
        if ($entry['username'] == 'bob') $bobRankL = $rank;
    }
    if ($aliceRankL !== -1 && $bobRankL !== -1 && $aliceRankL < $bobRankL) {
        echo "SUCCESS (Alice > Bob)\n";
    } else {
        echo "FAILED (Level Alice Rank: $aliceRankL, Level Bob Rank: $bobRankL)\n";
        print_r($level);
    }

    echo "\nTests Completed.\n";
}

runLeaderboardTests();
