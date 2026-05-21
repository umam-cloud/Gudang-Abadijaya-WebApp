<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
    echo "CONNECTED\n";
    // Check if we can list databases
    $stmt = $pdo->query("SHOW DATABASES");
    $dbs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "DATABASES: " . implode(', ', $dbs) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
