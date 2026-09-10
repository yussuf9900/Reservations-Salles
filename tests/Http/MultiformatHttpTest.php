<?php

declare(strict_types=1);

namespace Tests\Http;

class MultiformatHttpTest extends HttpTestCase
{
    public function testHtmlIgnoresQueryAndAccept(): void
    {
        $this->loginAsAdmin();
        $res = $this->request('GET', '/salles?format=json', [], ['HTTP_ACCEPT' => 'application/json']);
        $this->assertSame(200, $res['status']);
        $this->assertStringContainsString('<!DOCTYPE html>', $res['body']);
        $this->assertStringNotContainsString('format=', $res['body']);
    }

    public function testJsonIgnoresQueryAndAcceptOnBothRoutes(): void
    {
        $this->format = 'json';
        $this->setUp();
        $this->loginAsAdmin();
        foreach (['/salles', '/api/salles', '/reservations', '/api/reservations'] as $url) {
            $res = $this->request('GET', $url . '?format=html', [], ['HTTP_ACCEPT' => 'text/html']);
            $this->assertSame(200, $res['status']);
            $this->assertSame('json', json_decode($res['body'], true)['format']);
        }
    }

    public function testJsonErrorsAndLoginToken(): void
    {
        $this->format = 'json';
        $this->setUp();
        foreach (['/salles', '/api/salles'] as $url) {
            $this->assertSame(401, $this->request('GET', $url)['status']);
        }
        $login = json_decode($this->request('GET', '/login')['body'], true);
        $token = $login['data']['csrf_token'];
        $this->assertTrue($this->csrf->validateToken($token));
        $this->assertSame(403, $this->request('POST', '/api/salles')['status']);
        $this->assertSame(404, $this->request('GET', '/inconnue')['status']);
        $this->assertSame(405, $this->request('DELETE', '/salles')['status']);
        $this->loginAsResponsable();
        $this->assertSame(403, $this->request('POST', '/api/salles', [], ['HTTP_X_CSRF_TOKEN' => $token])['status']);
    }

    public function testLoginRotatesSessionAndTokenAndLogoutInvalidatesThem(): void
    {
        $this->format = 'json';
        $this->setUp();
        $oldId = session_id();
        $oldToken = $this->csrf->getToken();
        $res = $this->request('POST', '/login', [
            'email' => 'admin@univ.sn', 'password' => 'admin123', '_token' => $oldToken,
        ]);
        $this->assertSame(200, $res['status']);
        $this->assertNotSame($oldId, session_id());
        $this->assertFalse($this->csrf->validateToken($oldToken));
        $newToken = json_decode($res['body'], true)['data']['csrf_token'];
        $this->assertTrue($this->csrf->validateToken($newToken));
        $this->assertSame(200, $this->request('POST', '/logout', ['_token' => $newToken])['status']);
        $this->assertFalse($this->auth->check());
        $this->assertFalse($this->csrf->validateToken($newToken));
    }
    public function testUnexpectedErrorUsesConfiguredStrategy(): void
    {
        foreach (['html', 'json'] as $format) {
            $this->format = $format;
            $this->setUp();
            $this->loginAsAdmin();
            $controller = $this->createMock(\App\Controller\SalleController::class);
            $controller->method('index')->willThrowException(new \RuntimeException('private database detail'));
            $this->container->set(\App\Controller\SalleController::class, $controller);
            $res = $this->request('GET', '/salles');
            $this->assertSame(500, $res['status']);
            $this->assertStringNotContainsString('private database detail', $res['body']);
            if ($format === 'json') {
                $this->assertFalse(json_decode($res['body'], true)['success']);
            } else {
                $this->assertStringContainsString('<!DOCTYPE html>', $res['body']);
            }
        }
    }
}
