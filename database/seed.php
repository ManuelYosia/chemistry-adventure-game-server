<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    $pdo = Database::getInstance();
    echo "Connected to database successfully.\n";

    // Disable foreign key checks for seeding
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

    $users = [
        ['proton_warrior', 'proton@example.com'],
        ['electron_queen', 'electron@example.com'],
        ['neutron_master', 'neutron@example.com'],
        ['molecule_maker', 'molecule@example.com'],
        ['atom_smasher', 'atom@example.com'],
        ['catalyst_king', 'catalyst@example.com'],
        ['bond_breaker', 'bond@example.com'],
        ['element_hunter', 'element@example.com'],
        ['reaction_pro', 'reaction@example.com'],
        ['chem_wizard', 'wizard@example.com'],
        ['lab_rat', 'lab@example.com'],
        ['alkali_knight', 'alkali@example.com'],
        ['noble_gas', 'noble@example.com'],
        ['oxide_ranger', 'oxide@example.com'],
        ['ph_balancer', 'ph@example.com']
    ];

    $passwordHash = password_hash('password123', PASSWORD_DEFAULT);

    echo "Seeding 15 users...\n";
    $userStmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $progressStmt = $pdo->prepare("INSERT INTO player_progress (user_id, highest_unlocked_map_id, highest_unlocked_level_id) VALUES (?, ?, ?)");
    $resultStmt = $pdo->prepare("INSERT INTO level_results (user_id, map_id, level_id, score, stars, remaining_time, bonus_score, is_completed) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    foreach ($users as $userData) {
        // Insert user
        $userStmt->execute([$userData[0], $userData[1], $passwordHash]);
        $userId = $pdo->lastInsertId();

        echo "Created user: {$userData[0]} (ID: $userId)\n";

        // Insert progress
        $mapId = rand(1, 3);
        $levelId = rand(1, 10);
        $progressStmt->execute([$userId, $mapId, $levelId]);

        // Insert at least 1 result
        $score = rand(500, 2000);
        $stars = rand(1, 3);
        $time = rand(10, 60);
        $bonus = rand(0, 200);
        $resultStmt->execute([$userId, 1, 1, $score, $stars, $time, $bonus, 1]);

        // Randomly add a second result
        if (rand(0, 1)) {
            $score2 = rand(800, 2500);
            $stars2 = rand(1, 3);
            $time2 = rand(5, 45);
            $bonus2 = rand(0, 300);
            $resultStmt->execute([$userId, 1, 2, $score2, $stars2, $time2, $bonus2, 1]);
        }
    }

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    echo "\nSeeding completed successfully!\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
