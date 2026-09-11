<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

if (!isset($_ENV['DB_DATABASE'])) {
    $dotenvPath = dirname(__DIR__);
    if (file_exists($dotenvPath . '/.env')) {
        $dotenv = Dotenv::createImmutable($dotenvPath);
        $dotenv->safeLoad();
    }
}

return function (): Capsule {
    static $capsule = null;

    if ($capsule !== null) {
        return $capsule;
    }

    $capsule = new Capsule();

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => (int)($_ENV['DB_TIMEOUT'] ?? (getenv('DB_TIMEOUT') ?: 5)),
    ];

    $sslMode = $_ENV['DB_SSL'] ?? getenv('DB_SSL');
    if ($sslMode === 'true' || $sslMode === '1') {
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
    }

    $connection = [
        'driver'    => $_ENV['DB_DRIVER'] ?? (getenv('DB_DRIVER') ?: 'mysql'),
        'host'      => $_ENV['DB_HOST'] ?? (getenv('DB_HOST') ?: '127.0.0.1'),
        'port'      => $_ENV['DB_PORT'] ?? (getenv('DB_PORT') ?: '3306'),
        'database'  => $_ENV['DB_DATABASE'] ?? (getenv('DB_DATABASE') ?: 'reservation_salles'),
        'username'  => $_ENV['DB_USERNAME'] ?? (getenv('DB_USERNAME') ?: 'root'),
        'password'  => $_ENV['DB_PASSWORD'] ?? (getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : ''),
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix'    => '',
        'options'   => $options,
    ];

    $socket = $_ENV['DB_SOCKET'] ?? getenv('DB_SOCKET');
    if (!empty($socket)) {
        $connection['unix_socket'] = $socket;
    }

    $capsule->addConnection($connection);

    $capsule->setAsGlobal();

    $capsule->bootEloquent();

    return $capsule;
};
