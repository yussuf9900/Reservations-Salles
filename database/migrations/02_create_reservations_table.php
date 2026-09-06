<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(): void
    {
        if (!Capsule::schema()->hasTable('reservations')) {
            Capsule::schema()->create('reservations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('salle_id')->constrained('salles')->onDelete('cascade');
                $table->string('responsable', 120);
                $table->string('email', 255);
                $table->string('motif', 255);
                $table->dateTime('date_debut');
                $table->dateTime('date_fin');
                $table->enum('statut', ['confirmée', 'annulée'])->default('confirmée');
                $table->timestamps();

                $table->index(['salle_id', 'date_debut', 'date_fin']);
            });
            echo "Table 'reservations' créée avec succès.\n";
        } else {
            echo "Table 'reservations' existe déjà.\n";
        }
    }

    public function down(): void
    {
        Capsule::schema()->dropIfExists('reservations');
        echo "Table 'reservations' supprimée.\n";
    }
};
