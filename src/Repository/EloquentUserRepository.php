<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function save(User $user): bool
    {
        return $user->save();
    }
}
