<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\MySqlConnection;

// Configurer une fausse connexion pour les modèles Eloquent en mémoire (sans MySQL)
$resolver = new ConnectionResolver([
    'default' => new MySqlConnection(function () {
        return new class {
            public function quote($str)
            {
                return "'$str'";
            }
        };
    })
]);
$resolver->setDefaultConnection('default');
Model::setConnectionResolver($resolver);
