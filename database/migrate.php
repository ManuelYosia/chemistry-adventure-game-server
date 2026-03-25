<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    // Connect to MySQL (without specific DB first to create it)
    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $user = $_ENV['DB_USERNAME'] ?? 'root';
    $pass = $_ENV['DB_PASSWORD'] ?? '';
    
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "Connected to MySQL successfully.\n";

    // Read and execute schema
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    
    // Remove comments
    $sql = preg_replace('/--.*$/m', '', $sql);
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
    
    $statements = explode(';', $sql);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if ($statement) {
            echo "Executing: " . substr(str_replace(["\n", "\r"], ' ', $statement), 0, 100) . "...\n";
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                echo "FAILED: " . $e->getMessage() . "\n";
                // Don't exit immediately so we can see other errors, but mark as failure
                $failed = true;
            }
        }
    }

    if (isset($failed)) {
        echo "\nMigration finished with errors.\n";
        exit(1);
    }

    echo "\nDatabase schema created and seeded successfully.\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
