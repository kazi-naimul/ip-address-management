<?php
$pdo = new PDO(
    'mysql:host=common-mysql;dbname=ip_management_db',
    'root',
    'secret'
);

$sql = file_get_contents(__DIR__ . '/migrations.sql');
$statements = explode(';', $sql);

foreach ($statements as $statement) {
    $statement = trim($statement);
    if (!empty($statement)) {
        try {
            $pdo->exec($statement);
            echo "Executed: " . substr($statement, 0, 50) . "...\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'already exists') !== false || strpos($e->getMessage(), 'Duplicate') !== false) {
                echo "Skipped (already exists): " . substr($statement, 0, 50) . "...\n";
            } else {
                echo "Error: " . $e->getMessage() . "\n";
                die(1);
            }
        }
    }
}

echo "\nMigrations completed successfully!\n";
