<?php

declare(strict_types=1);

namespace Tests\Http;

use App\Model\Reservation;
use DateTimeImmutable;

class ReservationHttpTest extends HttpTestCase
{
    public function testGetReservationsReturns200(): void
    {
        $res = $this->request('GET', '/reservations');

        $this->assertSame(200, $res['status']);
        $this->assertStringContainsString('Gestion des Réservations', $res['body']);
    }

    public function testGetReservationFormReturns200(): void
    {
        $res = $this->request('GET', '/reservations/create');

        $this->assertSame(200, $res['status']);
        $this->assertStringContainsString('Effectuer une réservation', $res['body']);
    }

    public function testGetReservationNotFoundReturns404(): void
    {
        $res = $this->request('GET', '/reservations/99999');

        $this->assertSame(404, $res['status']);
    }

    public function testApiGetReservationsReturnsJson(): void
    {
        $res = $this->request('GET', '/api/reservations');

        $this->assertSame(200, $res['status']);
        $json = json_decode($res['body'], true);
        $this->assertIsArray($json);
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('meta', $json);
    }

    public function testApiReservationStoreValidation(): void
    {
        $res = $this->request('POST', '/api/reservations', []);

        $this->assertSame(422, $res['status']);
        $json = json_decode($res['body'], true);
        $this->assertTrue($json['error']);
        $this->assertArrayHasKey('errors', $json);
    }
}
