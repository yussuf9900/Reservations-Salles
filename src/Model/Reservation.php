<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $table = 'reservations';

    protected $fillable = [
        'salle_id',
        'responsable',
        'email',
        'motif',
        'date_debut',
        'date_fin',
        'statut',
    ];

    protected $casts = [
        'salle_id'   => 'integer',
        'date_debut' => 'datetime',
        'date_fin'   => 'datetime',
    ];

    public const STATUTS_AUTORISES = [
        'confirmée',
        'annulée',
    ];

    /**
     * Relation : Une réservation appartient à une salle
     */
    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class, 'salle_id');
    }
}
