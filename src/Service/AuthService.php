<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\User;
use App\Repository\UserRepositoryInterface;

class AuthService
{
    private ?User $currentUser = null;

    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly \App\Session\SessionManagerInterface $session,
        private readonly CsrfService $csrf
    ) {
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

        $this->session->regenerate();
        $this->session->set('user_id', $user->id);
        $this->csrf->regenerateToken();
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

        $this->session->regenerate();
        $this->session->set('user_id', $user->id);
        $this->csrf->regenerateToken();
        $this->currentUser = $user;
        return true;
    }

    public function logout(): void
    {
        $this->session->destroy();
        $this->currentUser = null;
    }

    public function user(): ?User
    {
        if ($this->currentUser !== null && $this->currentUser->id === $this->session->get('user_id')) {
            return $this->currentUser;
        }

        $userId = $this->session->get('user_id');
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
