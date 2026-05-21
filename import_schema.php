<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    
    // Execute SQL queries (PDO exec does not support multiple statements if emulation is disabled,
    // but in MySQL it typically works or we can split it by semicolon)
    // We split by semicolon to make sure all queries execute successfully
    $queries = explode(';', $sql);
    foreach ($queries as $query) {
        $trimmed = trim($query);
        if ($trimmed !== '') {
            $pdo->exec($trimmed);
        }
    }
    
    echo "DATABASE AND TABLES CREATED SUCCESSFULLY\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
