<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Salle extends Model
{
    protected $table = 'salles';

    protected $fillable = [
        'nom',
        'batiment',
        'capacite',
        'type',
        'active',
    ];

    protected $casts = [
        'capacite' => 'integer',
        'active'   => 'boolean',
    ];

    public const TYPES_AUTORISES = [
        'cours',
        'informatique',
        'laboratoire',
        'amphitheatre',
        'reunion',
    ];

    /**
     * Relation : Une salle possède plusieurs réservations
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'salle_id');
    }
}
