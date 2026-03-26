<?php

$tests = [
    'Auth' => 'AuthTest.php',
    'Progress' => 'ProgressTest.php',
    'Level Results' => 'LevelResultTest.php',
    'Progression' => 'ProgressionTest.php',
    'Leaderboard' => 'LeaderboardTest.php',
    'Middleware' => 'AuthMiddlewareTest.php'
];

echo "========================================\n";
echo "   CHEMISTRY ADVENTURE TEST RUNNER      \n";
echo "========================================\n\n";

$allPassed = true;
$results = [];

foreach ($tests as $name => $file) {
    echo "[$name] Running... ";
    $path = __DIR__ . '/' . $file;
    $output = shell_exec("php $path 2>&1");
    
    if (strpos($output, 'FAILED') !== false || strpos($output, 'Fatal error') !== false) {
        echo "FAILED\n";
        $results[$name] = false;
        $allPassed = false;
        // Optionally print output on failure
        echo "----------------------------------------\n";
        echo $output;
        echo "----------------------------------------\n";
    } else if (strpos($output, 'SUCCESS') !== false) {
        echo "SUCCESS\n";
        $results[$name] = true;
    } else {
        echo "UNKNOWN (Check Output)\n";
        $results[$name] = null;
        echo "----------------------------------------\n";
        echo $output;
        echo "----------------------------------------\n";
    }
}

echo "\n========================================\n";
echo "            TEST SUMMARY                \n";
echo "========================================\n";
foreach ($results as $name => $passed) {
    $status = $passed === true ? "PASS" : ($passed === false ? "FAIL" : "???");
    printf("%-20s: %s\n", $name, $status);
}
echo "========================================\n";

if ($allPassed) {
    echo "OVERALL STATUS: ALL TESTS PASSED!\n";
} else {
    echo "OVERALL STATUS: SOME TESTS FAILED.\n";
    exit(1);
}
echo "========================================\n";
