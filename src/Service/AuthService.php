<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\User;
use App\Repository\UserRepositoryInterface;

class AuthService
{
    private ?User $currentUser = null;

    public function __construct(
        private readonly UserRepositoryInterface $users
    ) {
        $this->ensureSessionStarted();
    }

    private function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->users->findByEmail($email);
        if ($user === null) {
            return false;
        }

        if (!password_verify($password, $user->mot_de_passe)) {
            return false;
        }

        $_SESSION['user_id'] = $user->id;
        $this->currentUser = $user;
        return true;
    }

    public function loginAs(string $role): bool
    {
        $email = $role === 'admin' ? 'admin@univ.sn' : 'prof@univ.sn';
        $user = $this->users->findByEmail($email);
        if ($user === null) {
            return false;
        }

        $_SESSION['user_id'] = $user->id;
        $this->currentUser = $user;
        return true;
    }

    public function logout(): void
    {
        unset($_SESSION['user_id']);
        $this->currentUser = null;
    }

    public function user(): ?User
    {
        if ($this->currentUser !== null) {
            return $this->currentUser;
        }

        $userId = $_SESSION['user_id'] ?? null;
        if ($userId === null) {
            return null;
        }

        $this->currentUser = $this->users->findById((int)$userId);
        return $this->currentUser;
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function isAdmin(): bool
    {
        $user = $this->user();
        return $user !== null && $user->isAdmin();
    }

    public function isResponsable(): bool
    {
        $user = $this->user();
        return $user !== null && $user->isResponsable();
    }

    public function hasRole(string $role): bool
    {
        $user = $this->user();
        return $user !== null && $user->role === $role;
    }
}
