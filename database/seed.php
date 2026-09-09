<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Model\Salle;

$initDb = require dirname(__DIR__) . '/config/database.php';
$capsule = $initDb();

try {
    $capsule->getConnection()->getPdo();
} catch (\Throwable $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage() . "\n";
    exit(1);
}

$sallesInitiales = [
    [
        'nom'      => 'Amphithéâtre A',
        'batiment' => 'Bâtiment Principal',
        'capacite' => 250,
        'type'     => 'amphitheatre',
        'active'   => true,
    ],
    [
        'nom'      => 'Salle B12',
        'batiment' => 'Bâtiment B',
        'capacite' => 40,
        'type'     => 'cours',
        'active'   => true,
    ],
    [
        'nom'      => 'Laboratoire Chimie',
        'batiment' => 'Bâtiment Sciences',
        'capacite' => 24,
        'type'     => 'laboratoire',
        'active'   => true,
    ],
    [
        'nom'      => 'Salle Informatique 1',
        'batiment' => 'Bâtiment Informatique',
        'capacite' => 30,
        'type'     => 'informatique',
        'active'   => true,
    ],
    [
        'nom'      => 'Salle de réunion',
        'batiment' => 'Bâtiment Administratif',
        'capacite' => 12,
        'type'     => 'reunion',
        'active'   => true,
    ],
];

echo "Début du peuplement de la base de données...\n";

foreach ($sallesInitiales as $salleData) {
    $salle = Salle::updateOrCreate(
        ['nom' => $salleData['nom']],
        $salleData
    );
    echo "Salle synchronisée : [ID: {$salle->id}] {$salle->nom} ({$salle->capacite} places, {$salle->type})\n";
}

$utilisateursInitiaux = [
    [
        'nom'          => 'Administrateur Principal',
        'email'        => 'admin@univ.sn',
        'mot_de_passe' => password_hash('admin123', PASSWORD_BCRYPT),
        'role'         => 'admin',
    ],
    [
        'nom'          => 'Dr. Aïssatou Diallo',
        'email'        => 'prof@univ.sn',
        'mot_de_passe' => password_hash('prof123', PASSWORD_BCRYPT),
        'role'         => 'responsable',
    ],
];

foreach ($utilisateursInitiaux as $userData) {
    $user = \App\Model\User::updateOrCreate(
        ['email' => $userData['email']],
        $userData
    );
    echo "Utilisateur synchronisé : [ID: {$user->id}] {$user->nom} ({$user->email}, {$user->role})\n";
}

echo "Peuplement initial terminé avec succès (" . count($sallesInitiales) . " salles, " . count($utilisateursInitiaux) . " utilisateurs).\n";
