<?php

declare(strict_types=1);

namespace Tests\Http;

class MultiformatHttpTest extends HttpTestCase
{
    private ?string $originalEnvFormat = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalEnvFormat = $_ENV['APP_RESPONSE_FORMAT'] ?? null;
        $_ENV['APP_RESPONSE_FORMAT'] = 'html';
    }

    protected function tearDown(): void
    {
        if ($this->originalEnvFormat !== null) {
            $_ENV['APP_RESPONSE_FORMAT'] = $this->originalEnvFormat;
        } else {
            unset($_ENV['APP_RESPONSE_FORMAT']);
        }
        parent::tearDown();
    }

    public function testDefaultFormatIsHtml(): void
    {
        $res = $this->request('GET', '/salles');

        $this->assertSame(200, $res['status']);
        $this->assertStringContainsString('<!DOCTYPE html>', $res['body']);
        $this->assertStringContainsString('Salle 101', $res['body']);
    }

    public function testQueryParamFormatJsonReturnsJson(): void
    {
        $res = $this->request('GET', '/salles?format=json');

        $this->assertSame(200, $res['status']);
        $json = json_decode($res['body'], true);
        $this->assertIsArray($json, 'La réponse doit être un JSON valide');
        $this->assertTrue($json['success']);
        $this->assertSame('json', $json['format']);
        $this->assertSame('salle/index', $json['view']);
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('salles', $json['data']);
    }

    public function testReservationsQueryParamFormatJsonReturnsJson(): void
    {
        $res = $this->request('GET', '/reservations?format=json');

        $this->assertSame(200, $res['status']);
        $json = json_decode($res['body'], true);
        $this->assertIsArray($json, 'La réponse doit être un JSON valide');
        $this->assertTrue($json['success']);
        $this->assertSame('json', $json['format']);
        $this->assertSame('reservation/index', $json['view']);
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('reservations', $json['data']);
    }

    public function testEnvAppResponseFormatJsonReturnsJsonByDefault(): void
    {
        $_ENV['APP_RESPONSE_FORMAT'] = 'json';

        $res = $this->request('GET', '/salles');

        $this->assertSame(200, $res['status']);
        $json = json_decode($res['body'], true);
        $this->assertIsArray($json);
        $this->assertTrue($json['success']);
        $this->assertSame('json', $json['format']);
        $this->assertSame('salle/index', $json['view']);
    }

    public function testQueryParamOverridesEnvFormat(): void
    {
        $_ENV['APP_RESPONSE_FORMAT'] = 'json';

        $res = $this->request('GET', '/salles?format=html');

        $this->assertSame(200, $res['status']);
        $this->assertStringContainsString('<!DOCTYPE html>', $res['body']);
    }

    public function testHttpAcceptHeaderReturnsJson(): void
    {
        $res = $this->request('GET', '/salles', [], ['HTTP_ACCEPT' => 'application/json']);

        $this->assertSame(200, $res['status']);
        $json = json_decode($res['body'], true);
        $this->assertIsArray($json);
        $this->assertSame('json', $json['format']);
    }

    public function testNotFoundReturnsJsonWhenRequested(): void
    {
        $res = $this->request('GET', '/route-inexistante-404?format=json');

        $this->assertSame(404, $res['status']);
        $json = json_decode($res['body'], true);
        $this->assertIsArray($json);
        $this->assertTrue($json['error']);
        $this->assertStringContainsString('introuvable', $json['message']);
    }
}
