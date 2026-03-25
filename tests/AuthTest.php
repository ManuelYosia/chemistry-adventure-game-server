<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\AuthService;
use App\Core\Database;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function runTests() {
    echo "Running Authentication Tests...\n\n";
    $authService = new AuthService();
    
    // Clean up test user if exists
    $db = Database::getInstance();
    $db->prepare("DELETE FROM users WHERE email = 'test@example.com'")->execute();

    // 1. Test Registration
    echo "Test 1: User Registration... ";
    try {
        $userId = $authService->register([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);
        echo "SUCCESS (User ID: $userId)\n";
    } catch (Exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
    }

    // 2. Test Login (Correct)
    echo "Test 2: Valid Login... ";
    try {
        $result = $authService->login('test@example.com', 'password123');
        echo "SUCCESS (Token: " . $result['token'] . ")\n";
    } catch (Exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
    }

    // 3. Test Login (Incorrect Password)
    echo "Test 3: Invalid Login (Wrong Password)... ";
    try {
        $authService->login('test@example.com', 'wrongpassword');
        echo "FAILED (Should have thrown exception)\n";
    } catch (Exception $e) {
        echo "SUCCESS (Expected error: " . $e->getMessage() . ")\n";
    }

    // 4. Test Duplicate Registration
    echo "Test 4: Duplicate Registration... ";
    try {
        $authService->register([
            'username' => 'testuser2',
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);
        echo "FAILED (Should have thrown exception)\n";
    } catch (Exception $e) {
        echo "SUCCESS (Expected error: " . $e->getMessage() . ")\n";
    }

    echo "\nTests Completed.\n";
}

runTests();
