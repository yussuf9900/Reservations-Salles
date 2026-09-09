<?php

declare(strict_types=1);

namespace Tests\Http;

use App\Model\Reservation;
use DateTimeImmutable;

class ApiHttpTest extends HttpTestCase
{
    public function testGetApiStatsReturns200(): void
    {
        $res = $this->request('GET', '/api/stats');

        $this->assertSame(200, $res['status']);
        $json = json_decode($res['body'], true);
        $this->assertIsArray($json);
        $this->assertArrayHasKey('total_salles', $json);
        $this->assertArrayHasKey('top_salles', $json);
        $this->assertArrayHasKey('taux_occupation', $json);
    }

    public function testApiCancelReservation(): void
    {
        $debut = new DateTimeImmutable('+2 days 10:00:00');
        $fin = new DateTimeImmutable('+2 days 12:00:00');
        $reservation = new Reservation([
            'salle_id'    => 1,
            'responsable' => 'Responsable API',
            'email'       => 'api@univ.sn',
            'motif'       => 'Test annulation API',
            'date_debut'  => $debut->format('Y-m-d H:i:s'),
            'date_fin'    => $fin->format('Y-m-d H:i:s'),
            'statut'      => 'confirmée',
        ]);
        $this->reservationRepo->save($reservation);

        $res = $this->request('POST', "/api/reservations/{$reservation->id}/cancel");

        $this->assertSame(200, $res['status']);
        $json = json_decode($res['body'], true);
        $this->assertTrue($json['success']);

        $updated = $this->reservationRepo->findById((int)$reservation->id);
        $this->assertSame('annulée', $updated->statut);
    }

    public function testApiCancelNotFoundReturns404(): void
    {
        $res = $this->request('POST', '/api/reservations/99999/cancel');

        $this->assertSame(404, $res['status']);
    }
}
