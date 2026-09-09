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
        $this->request('GET', '/logout');

        $this->assertArrayNotHasKey('user_id', $_SESSION);
        $this->assertFalse($this->auth->check());
    }

    public function testDashboardReturns200(): void
    {
        $res = $this->request('GET', '/dashboard');

        $this->assertSame(200, $res['status']);
        $this->assertStringContainsString('Tableau de Bord', $res['body']);
    }
}
