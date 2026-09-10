<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\MySqlConnection;

if (!defined('PHPUNIT_RUNNING')) {
    define('PHPUNIT_RUNNING', true);
}

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

session_save_path(sys_get_temp_dir());
(new \App\Session\SessionManager())->start();
