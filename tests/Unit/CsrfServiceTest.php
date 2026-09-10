<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Service\CsrfService;
use PHPUnit\Framework\TestCase;

class CsrfServiceTest extends TestCase
{
    private CsrfService $csrf;

    protected function setUp(): void
    {
        $_SESSION = [];
        $this->csrf = new CsrfService(new \App\Session\SessionManager());
    }

    public function testGetTokenGeneratesUniqueString(): void
    {
        $token1 = $this->csrf->getToken();
        $this->assertNotEmpty($token1);
        $this->assertSame(64, strlen($token1));

        $token2 = $this->csrf->getToken();
        $this->assertSame($token1, $token2);
    }

    public function testValidateToken(): void
    {
        $token = $this->csrf->getToken();

        $this->assertTrue($this->csrf->validateToken($token));
        $this->assertFalse($this->csrf->validateToken('mauvais_token'));
        $this->assertFalse($this->csrf->validateToken(null));
        $this->assertFalse($this->csrf->validateToken(''));
    }

    public function testRegenerateToken(): void
    {
        $token1 = $this->csrf->getToken();
        $token2 = $this->csrf->regenerateToken();

        $this->assertNotSame($token1, $token2);
        $this->assertTrue($this->csrf->validateToken($token2));
        $this->assertFalse($this->csrf->validateToken($token1));
    }
}
