<?php

declare(strict_types=1);

$host = $_ENV['DB_HOST'] ?? (getenv('DB_HOST') ?: '127.0.0.1');
$port = $_ENV['DB_PORT'] ?? (getenv('DB_PORT') ?: '3306');
$db = $_ENV['DB_DATABASE'] ?? (getenv('DB_DATABASE') ?: 'reservation_salles');
$user = $_ENV['DB_USERNAME'] ?? (getenv('DB_USERNAME') ?: 'root');
$pass = $_ENV['DB_PASSWORD'] ?? (getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '');

$options = [
    PDO::ATTR_TIMEOUT => 3,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
];

$sslMode = $_ENV['DB_SSL'] ?? getenv('DB_SSL');
if ($sslMode === 'true' || $sslMode === '1') {
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
}

$lastError = '';
fwrite(STDOUT, "En attente de connexion à la base de données MySQL ({$host}:{$port}/{$db})...\n");

for ($attempt = 1; $attempt <= 60; $attempt++) {
    try {
        new PDO(
            sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $db),
            $user,
            $pass,
            $options
        );
        fwrite(STDOUT, "[OK] Connexion à la base de données réussie après {$attempt} tentative(s).\n");
        exit(0);
    } catch (PDOException $e) {
        $lastError = $e->getMessage();
        sleep(1);
    }
}

fwrite(STDERR, "[ERREUR] MySQL indisponible après 60 tentatives ({$host}:{$port}/{$db}).\nDétail de l'erreur : {$lastError}\n");
exit(1);
