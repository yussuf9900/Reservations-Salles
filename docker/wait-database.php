<?php

declare(strict_types=1);

$lastError = '';
for ($attempt = 0; $attempt < 60; $attempt++) {
    try {
        new PDO(
            sprintf('mysql:host=%s;port=%s;dbname=%s', getenv('DB_HOST'), getenv('DB_PORT'), getenv('DB_DATABASE')),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD'),
            [PDO::ATTR_TIMEOUT => 2, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        exit(0);
    } catch (PDOException $e) {
        $lastError = $e->getCode();
        sleep(1);
    }
}
fwrite(STDERR, "MySQL indisponible après 60 tentatives (SQLSTATE {$lastError}). Vérifiez les identifiants et le volume existant.\n");
exit(1);
