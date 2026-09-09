<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AuthService;
use App\Service\CsrfService;
use App\View\ViewRenderer;

class AuthController
{
    public function __construct(
        private readonly AuthService $auth,
        private readonly CsrfService $csrf,
        private readonly ViewRenderer $view
    ) {
    }

    public function showLogin(): string
    {
        if ($this->auth->check()) {
            header('Location: /dashboard');
            if (!defined('PHPUNIT_RUNNING')) {
                exit;
            }
            return '';
        }

        return $this->view->render('auth/login', [
            'title'      => 'Connexion',
            'csrf_token' => $this->csrf->getToken(),
            'old_email'  => '',
            'error'      => null,
        ]);
    }

    public function login(): string
    {
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($this->auth->login($email, $password)) {
            $user = $this->auth->user();
            $this->view->setFlash('success', "Bienvenue, {$user->nom}.");

            $redirect = $_GET['redirect'] ?? ($this->auth->isAdmin() ? '/dashboard' : '/reservations');
            header('Location: ' . $redirect);
            if (!defined('PHPUNIT_RUNNING')) {
                exit;
            }
            return '';
        }

        return $this->view->render('auth/login', [
            'title'      => 'Connexion',
            'csrf_token' => $this->csrf->getToken(),
            'old_email'  => $email,
            'error'      => 'Identifiants incorrects. Veuillez vérifier votre adresse courriel et votre mot de passe.',
        ]);
    }

    public function quickLogin(): void
    {
        $role = (string)($_POST['role'] ?? 'responsable');
        if (!in_array($role, ['admin', 'responsable'], true)) {
            $role = 'responsable';
        }

        if ($this->auth->loginAs($role)) {
            $user = $this->auth->user();
            $this->view->setFlash('success', "Connexion rapide réussie en tant que {$user->nom} ({$role}).");

            $redirect = $role === 'admin' ? '/dashboard' : '/reservations';
            header('Location: ' . $redirect);
            if (!defined('PHPUNIT_RUNNING')) {
                exit;
            }
            return;
        }

        $this->view->setFlash('error', 'Impossible de se connecter avec ce compte.');
        header('Location: /login');
        if (!defined('PHPUNIT_RUNNING')) {
            exit;
        }
    }

    public function logout(): void
    {
        $this->auth->logout();
        $this->view->setFlash('success', 'Vous avez été déconnecté avec succès.');
        header('Location: /login');
        if (!defined('PHPUNIT_RUNNING')) {
            exit;
        }
    }
}
