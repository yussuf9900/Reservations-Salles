<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AuthService;
use App\Service\CsrfService;
use App\View\ViewRenderer;

class AuthController extends AbstractController
{
    public function __construct(
        private readonly AuthService $auth,
        private readonly CsrfService $csrf,
        ViewRenderer $view
    ) {
        parent::__construct($view);
    }

    public function showLogin(): string
    {
        if ($this->auth->check()) {
            return $this->redirect('/dashboard', ['csrf_token' => $this->csrf->getToken()]);
        }

        $redirect = (string)($_GET['redirect'] ?? '');

        return $this->render('auth/login', [
            'title'      => 'Connexion',
            'csrf_token' => $this->csrf->getToken(),
            'old_email'  => '',
            'error'      => null,
            'redirect'   => $redirect,
        ]);
    }

    public function login(): string
    {
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $redirect = (string)($_POST['redirect'] ?? $_GET['redirect'] ?? '');

        if ($this->auth->login($email, $password)) {
            $user = $this->auth->user();
            $this->flash('success', "Bienvenue, {$user->nom}.");

            $target = $this->resolveRedirect($redirect, $this->auth->isAdmin());
            return $this->redirect($target, ['csrf_token' => $this->csrf->getToken()]);
        }

        http_response_code(401);
        return $this->render('auth/login', [
            'title'      => 'Connexion',
            'csrf_token' => $this->csrf->getToken(),
            'old_email'  => $email,
            'error'      => 'Identifiants incorrects. Veuillez vérifier votre adresse courriel et votre mot de passe.',
            'redirect'   => $redirect,
        ]);
    }

    public function quickLogin(): string
    {
        $role = (string)($_POST['role'] ?? 'responsable');
        if (!in_array($role, ['admin', 'responsable'], true)) {
            $role = 'responsable';
        }

        $redirect = (string)($_POST['redirect'] ?? $_GET['redirect'] ?? '');

        if ($this->auth->loginAs($role)) {
            $user = $this->auth->user();
            $this->flash('success', "Connexion rapide réussie en tant que {$user->nom} ({$role}).");

            $target = $this->resolveRedirect($redirect, $role === 'admin');
            return $this->redirect($target, ['csrf_token' => $this->csrf->getToken()]);
        }

        $this->flash('error', 'Impossible de se connecter avec ce compte.');
        return $this->redirect('/login');
    }

    private function resolveRedirect(?string $redirect, bool $isAdmin): string
    {
        if (
            $redirect !== null &&
            $redirect !== '' &&
            str_starts_with($redirect, '/') &&
            !str_starts_with($redirect, '//') &&
            !str_starts_with($redirect, '/login') &&
            !str_starts_with($redirect, '/logout')
        ) {
            return $redirect;
        }

        return $isAdmin ? '/dashboard' : '/salles';
    }

    public function logout(): string
    {
        $this->auth->logout();
        $this->flash('success', 'Vous avez été déconnecté avec succès.');
        return $this->redirect('/login');
    }
}
