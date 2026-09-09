<?php

declare(strict_types=1);

namespace Tests\Http;

class CsrfHttpTest extends HttpTestCase
{
    public function testPostWithoutCsrfTokenReturns403(): void
    {
        $this->loginAsAdmin();
        $res = $this->request('POST', '/salles', [
            'nom'      => 'Salle Illégale',
            'batiment' => 'Batiment X',
            'capacite' => 30,
            'type'     => 'cours',
        ]);

        $this->assertSame(403, $res['status']);
        $this->assertStringContainsString('CSRF', $res['body']);
    }

    public function testApiPostBypassesCsrfMiddleware(): void
    {
        $res = $this->request('POST', '/api/salles', []);

        $this->assertSame(422, $res['status']);
    }
}
