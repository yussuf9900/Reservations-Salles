<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

// Charger les variables d'environnement si non chargées
if (!isset($_ENV['DB_DATABASE'])) {
    $dotenvPath = dirname(__DIR__);
    if (file_exists($dotenvPath . '/.env')) {
        $dotenv = Dotenv::createImmutable($dotenvPath);
        $dotenv->safeLoad();
    }
}

// Fonction de fabrique pour Capsule\Manager (configuré une seule fois)
return function (): Capsule {
    static $capsule = null;

    if ($capsule !== null) {
        return $capsule;
    }

    $capsule = new Capsule();

    $capsule->addConnection([
        'driver'    => $_ENV['DB_DRIVER'] ?? 'mysql',
        'host'      => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port'      => $_ENV['DB_PORT'] ?? '3306',
        'database'  => $_ENV['DB_DATABASE'] ?? 'reservation_salles',
        'username'  => $_ENV['DB_USERNAME'] ?? 'root',
        'password'  => $_ENV['DB_PASSWORD'] ?? '',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix'    => '',
    ]);

    // Rend l'instance accessible globalement via des méthodes statiques
    $capsule->setAsGlobal();

    // Démarre l'ORM Eloquent
    $capsule->bootEloquent();

    return $capsule;
};
