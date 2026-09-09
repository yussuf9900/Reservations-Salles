<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(): void
    {
        if (!Capsule::schema()->hasTable('users')) {
            Capsule::schema()->create('users', function (Blueprint $table) {
                $table->id();
                $table->string('nom', 100);
                $table->string('email', 150)->unique();
                $table->string('mot_de_passe', 255);
                $table->enum('role', ['admin', 'responsable'])->default('responsable');
                $table->timestamps();
            });
            echo "Table 'users' créée avec succès.\n";
        } else {
            echo "Table 'users' existe déjà.\n";
        }
    }

    public function down(): void
    {
        Capsule::schema()->dropIfExists('users');
        echo "Table 'users' supprimée.\n";
    }
};
