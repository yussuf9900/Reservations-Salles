<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

$initDb = require dirname(__DIR__) . '/config/database.php';
$capsule = $initDb();

try {
    $capsule->getConnection()->getPdo();
    echo "Connexion à la base de données établie avec succès.\n";
} catch (\Throwable $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage() . "\n";
    exit(1);
}

$migrations = [
    __DIR__ . '/migrations/01_create_salles_table.php',
    __DIR__ . '/migrations/02_create_reservations_table.php',
];

foreach ($migrations as $migrationFile) {
    if (file_exists($migrationFile)) {
        $migration = require $migrationFile;
        $migration->up();
    }
}

echo "Migrations terminées.\n";
