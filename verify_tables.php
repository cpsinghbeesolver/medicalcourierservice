<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=medical_courier', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "✅ Database 'medical_courier' Tables:\n";
    echo "=" . str_repeat("=", 50) . "\n";

    foreach ($tables as $index => $table) {
        echo sprintf("%2d. %s\n", $index + 1, $table);
    }

    echo "\n✅ Total Tables: " . count($tables) . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
