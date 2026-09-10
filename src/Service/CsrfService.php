<?php

declare(strict_types=1);

namespace App\Service;

class CsrfService
{
    public function __construct(private readonly \App\Session\SessionManagerInterface $session)
    {
    }

    public function getToken(): string
    {
        if (!$this->session->get('_csrf_token')) {
            $this->session->set('_csrf_token', bin2hex(random_bytes(32)));
        }

        return $this->session->get('_csrf_token');
    }

    public function validateToken(?string $token): bool
    {
        if ($token === null || !$this->session->get('_csrf_token')) {
            return false;
        }

        return hash_equals($this->session->get('_csrf_token'), $token);
    }

    public function regenerateToken(): string
    {
        $this->session->set('_csrf_token', bin2hex(random_bytes(32)));
        return $this->session->get('_csrf_token');
    }
}
