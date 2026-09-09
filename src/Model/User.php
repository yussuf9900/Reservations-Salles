<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'nom',
        'email',
        'mot_de_passe',
        'role',
    ];

    protected $hidden = [
        'mot_de_passe',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isResponsable(): bool
    {
        return $this->role === 'responsable';
    }
}
