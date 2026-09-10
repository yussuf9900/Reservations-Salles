<?php

declare(strict_types=1);

namespace Tests\Http;

class AuthHttpTest extends HttpTestCase
{
    public function testGetLoginReturns200(): void
    {
        $res = $this->request('GET', '/login');

        $this->assertSame(200, $res['status']);
        $this->assertStringContainsString('Authentification', $res['body']);
        $this->assertStringContainsString('Connexion Rapide : Administrateur', $res['body']);
        $this->assertStringContainsString('Connexion Rapide : Responsable', $res['body']);
    }

    public function testQuickLoginAdminSetsSession(): void
    {
        $token = $this->csrf->getToken();
        $this->request('POST', '/login/quick', [
            '_token' => $token,
            'role'   => 'admin',
        ]);

        $this->assertSame(1, $_SESSION['user_id'] ?? null);
        $this->assertTrue($this->auth->isAdmin());
    }

    public function testQuickLoginResponsableSetsSession(): void
    {
        $token = $this->csrf->getToken();
        $this->request('POST', '/login/quick', [
            '_token' => $token,
            'role'   => 'responsable',
        ]);

        $this->assertSame(2, $_SESSION['user_id'] ?? null);
        $this->assertTrue($this->auth->isResponsable());
    }

    public function testLogoutClearsSession(): void
    {
        $_SESSION['user_id'] = 1;
        $this->request('POST', '/logout', ['_token' => $this->csrf->getToken()]);

        $this->assertArrayNotHasKey('user_id', $_SESSION);
        $this->assertFalse($this->auth->check());
    }

    public function testDashboardReturns200WhenLoggedIn(): void
    {
        $this->loginAsAdmin();
        $res = $this->request('GET', '/dashboard');

        $this->assertSame(200, $res['status']);
        $this->assertStringContainsString('Tableau de Bord', $res['body']);
        $this->assertStringContainsString('phpMyAdmin (BDD)', $res['body']);
    }

    public function testDashboardWithoutLoginRedirectsToLogin(): void
    {
        $res = $this->request('GET', '/dashboard');

        $this->assertSame(302, $res['status']);
    }

    public function testQuickLoginRedirectsToSpecifiedTarget(): void
    {
        $token = $this->csrf->getToken();
        $this->request('POST', '/login/quick', [
            '_token'   => $token,
            'role'     => 'responsable',
            'redirect' => '/salles',
        ]);

        $this->assertSame(2, $_SESSION['user_id'] ?? null);
    }

    public function testLoginFormHidesNavigationTabsWhenUnauthenticated(): void
    {
        $res = $this->request('GET', '/login');

        $this->assertSame(200, $res['status']);
        $this->assertStringNotContainsString('<span>Tableau de bord</span>', $res['body']);
        $this->assertStringNotContainsString('<span>Réservations</span>', $res['body']);
        $this->assertStringNotContainsString('<span>Réserver</span>', $res['body']);
    }
}
