<?php

declare(strict_types=1);

namespace Tests\Http;

class SalleHttpTest extends HttpTestCase
{
    public function testGetSallesReturns200WhenLoggedIn(): void
    {
        $this->loginAsResponsable();
        $res = $this->request('GET', '/salles');

        $this->assertSame(200, $res['status']);
        $this->assertStringContainsString('Salle 101', $res['body']);
    }

    public function testGuestAccessingSallesRedirectsToLogin(): void
    {
        $res = $this->request('GET', '/salles');

        $this->assertSame(302, $res['status']);
    }

    public function testGuestAccessingSalleDetailRedirectsToLogin(): void
    {
        $res = $this->request('GET', '/salles/1');

        $this->assertSame(302, $res['status']);
    }

    public function testGetSalleNotFoundReturns404(): void
    {
        $this->loginAsAdmin();
        $res = $this->request('GET', '/salles/99999');

        $this->assertSame(404, $res['status']);
    }

    public function testMethodNotAllowedReturns405(): void
    {
        $this->loginAsAdmin();
        $res = $this->request('DELETE', '/salles');

        $this->assertSame(405, $res['status']);
    }

    public function testApiGetSallesReturnsJson(): void
    {
        $res = $this->request('GET', '/api/salles');

        $this->assertSame(200, $res['status']);
        $json = json_decode($res['body'], true);
        $this->assertIsArray($json);
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('meta', $json);
    }

    public function testAdminCanAccessEditPageAndHasValidCsrfToken(): void
    {
        $_SESSION['user_id'] = 1;

        $res = $this->request('GET', '/salles/1/edit');

        $this->assertSame(200, $res['status']);
        $this->assertStringContainsString('Modifier la salle', $res['body']);
        $token = $this->csrf->getToken();
        $this->assertStringContainsString('value="' . $token . '"', $res['body']);
    }

    public function testAdminCanUpdateSalleSuccessfully(): void
    {
        $_SESSION['user_id'] = 1;
        $token = $this->csrf->getToken();

        $res = $this->request('POST', '/salles/1/edit', [
            '_token'   => $token,
            'nom'      => 'Salle 101 Modifiee',
            'batiment' => 'Batiment B',
            'capacite' => 55,
            'type'     => 'cours',
            'active'   => '1',
        ]);

        $this->assertSame(302, $res['status']);
        $salle = $this->salleRepo->findById(1);
        $this->assertNotNull($salle);
        $this->assertSame('Salle 101 Modifiee', $salle->nom);
        $this->assertSame('Batiment B', $salle->batiment);
        $this->assertSame(55, $salle->capacite);
        $this->assertTrue($salle->active);
    }

    public function testAdminCanDeactivateSalle(): void
    {
        $_SESSION['user_id'] = 1;
        $token = $this->csrf->getToken();

        $res = $this->request('POST', '/salles/1/edit', [
            '_token'   => $token,
            'nom'      => 'Salle 101',
            'batiment' => 'Batiment A',
            'capacite' => 40,
            'type'     => 'cours',
            'active'   => '0',
        ]);

        $this->assertSame(302, $res['status']);
        $salle = $this->salleRepo->findById(1);
        $this->assertNotNull($salle);
        $this->assertFalse($salle->active);
    }

    public function testGuestCannotAccessEditPageRedirectsToLogin(): void
    {
        $res = $this->request('GET', '/salles/1/edit');

        $this->assertSame(302, $res['status']);
    }

    public function testNonAdminCannotAccessEditPageReturns403(): void
    {
        $_SESSION['user_id'] = 2; // responsable

        $res = $this->request('GET', '/salles/1/edit');

        $this->assertSame(403, $res['status']);
        $this->assertStringContainsString('Accès Refusé', $res['body']);
    }

    public function testGuestCannotPostSallesRedirectsToLogin(): void
    {
        $token = $this->csrf->getToken();
        $res = $this->request('POST', '/salles', [
            '_token'   => $token,
            'nom'      => 'Nouvelle Salle Pirate',
            'batiment' => 'Batiment X',
            'capacite' => 30,
            'type'     => 'cours',
        ]);

        $this->assertSame(302, $res['status']);
    }

    public function testUpdateSalleWithInvalidCsrfReturns403(): void
    {
        $_SESSION['user_id'] = 1;

        $res = $this->request('POST', '/salles/1/edit', [
            '_token'   => 'token-invalide',
            'nom'      => 'Salle 101 Hacked',
            'batiment' => 'Batiment A',
            'capacite' => 40,
            'type'     => 'cours',
        ]);

        $this->assertSame(403, $res['status']);
        $this->assertStringContainsString('CSRF', $res['body']);
    }
}
