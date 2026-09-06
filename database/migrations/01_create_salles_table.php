<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(): void
    {
        if (!Capsule::schema()->hasTable('salles')) {
            Capsule::schema()->create('salles', function (Blueprint $table) {
                $table->id();
                $table->string('nom', 100);
                $table->string('batiment', 100);
                $table->unsignedInteger('capacite');
                $table->enum('type', ['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion']);
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
            echo "Table 'salles' créée avec succès.\n";
        } else {
            echo "Table 'salles' existe déjà.\n";
        }
    }

    public function down(): void
    {
        Capsule::schema()->dropIfExists('salles');
        echo "Table 'salles' supprimée.\n";
    }
};
