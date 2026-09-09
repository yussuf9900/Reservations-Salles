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

    public function testReservationFormHasValidCsrfTokenAndPrefillsUserWhenLoggedIn(): void
    {
        $_SESSION['user_id'] = 2; // Prof Test

        $res = $this->request('GET', '/reservations/create');

        $this->assertSame(200, $res['status']);
        $token = $this->csrf->getToken();
        $this->assertStringContainsString('value="' . $token . '"', $res['body']);
        $this->assertStringContainsString('value="Prof Test"', $res['body']);
        $this->assertStringContainsString('value="prof@univ.sn"', $res['body']);
    }

    public function testUserCanCancelReservationViaPostWithCsrf(): void
    {
        $debut = new DateTimeImmutable('+1 day 10:00:00');
        $fin = new DateTimeImmutable('+1 day 12:00:00');
        $res = new Reservation([
            'id'          => 1,
            'salle_id'    => 1,
            'responsable' => 'Prof Test',
            'email'       => 'prof@univ.sn',
            'motif'       => 'Reunion',
            'date_debut'  => $debut->format('Y-m-d H:i:s'),
            'date_fin'    => $fin->format('Y-m-d H:i:s'),
            'statut'      => 'confirmée',
        ]);
        $this->reservationRepo->save($res);

        $token = $this->csrf->getToken();
        $response = $this->request('POST', '/reservations/1/cancel', [
            '_token' => $token,
        ]);

        $this->assertSame(302, $response['status']);
        $updated = $this->reservationRepo->findById(1);
        $this->assertNotNull($updated);
        $this->assertSame('annulée', $updated->statut);
    }
}
