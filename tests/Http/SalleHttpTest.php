<?php

declare(strict_types=1);

namespace Tests\Http;

class SalleHttpTest extends HttpTestCase
{
    public function testGetSallesReturns200(): void
    {
        $res = $this->request('GET', '/salles');

        $this->assertSame(200, $res['status']);
        $this->assertStringContainsString('Salle 101', $res['body']);
    }

    public function testGetSalleNotFoundReturns404(): void
    {
        $res = $this->request('GET', '/salles/99999');

        $this->assertSame(404, $res['status']);
    }

    public function testMethodNotAllowedReturns405(): void
    {
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
}
