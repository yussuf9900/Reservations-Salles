<?php

declare(strict_types=1);

namespace Tests\Http;

use App\Model\Reservation;
use DateTimeImmutable;

class ReservationHttpTest extends HttpTestCase
{
    public function testGetReservationsReturns200WhenLoggedIn(): void
    {
        $this->loginAsResponsable();
        $res = $this->request('GET', '/reservations');

        $this->assertSame(200, $res['status']);
        $this->assertStringContainsString('Gestion des Réservations', $res['body']);
    }

    public function testGuestCannotAccessReservationsRedirectsToLogin(): void
    {
        $res = $this->request('GET', '/reservations');

        $this->assertSame(302, $res['status']);
    }

    public function testGetReservationFormReturns200WhenLoggedIn(): void
    {
        $this->loginAsResponsable();
        $res = $this->request('GET', '/reservations/create');

        $this->assertSame(200, $res['status']);
        $this->assertStringContainsString('Effectuer une réservation', $res['body']);
    }

    public function testGuestCannotAccessReservationFormRedirectsToLogin(): void
    {
        $res = $this->request('GET', '/reservations/create');

        $this->assertSame(302, $res['status']);
    }

    public function testGuestCannotPostReservationRedirectsToLogin(): void
    {
        $token = $this->csrf->getToken();
        $res = $this->request('POST', '/reservations', [
            '_token' => $token,
        ]);

        $this->assertSame(302, $res['status']);
    }

    public function testGetReservationNotFoundReturns404WhenLoggedIn(): void
    {
        $this->loginAsResponsable();
        $res = $this->request('GET', '/reservations/99999');

        $this->assertSame(404, $res['status']);
    }

    public function testApiAliasUsesHtmlAndAuthentication(): void
    {
        $this->loginAsAdmin();
        $res = $this->request('GET', '/api/reservations');
        $this->assertSame(200, $res['status']);
        $this->assertStringContainsString('<!DOCTYPE html>', $res['body']);
    }

    public function testApiReservationStoreValidation(): void
    {
        $this->format = 'json';
        $this->setUp();
        $this->loginAsAdmin();
        $res = $this->request('POST', '/api/reservations', ['_token' => $this->csrf->getToken()]);

        $this->assertSame(422, $res['status']);
        $json = json_decode($res['body'], true);
        $this->assertFalse($json['success']);
        $this->assertArrayHasKey('errors', $json['data']);
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
        $this->loginAsResponsable();
        $debut = new DateTimeImmutable('+1 day 10:00:00');
        $fin = new DateTimeImmutable('+1 day 12:00:00');
        $res = new Reservation([
            'salle_id'    => 1,
            'responsable' => 'Prof Test',
            'email'       => 'prof@univ.sn',
            'motif'       => 'Reunion',
            'date_debut'  => $debut->format('Y-m-d H:i:s'),
            'date_fin'    => $fin->format('Y-m-d H:i:s'),
            'statut'      => 'confirmée',
        ]);
        $res->id = 1;
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

    public function testGuestCannotCancelReservationRedirectsToLogin(): void
    {
        $token = $this->csrf->getToken();
        $response = $this->request('POST', '/reservations/1/cancel', [
            '_token' => $token,
        ]);

        $this->assertSame(302, $response['status']);
    }

    public function testResponsableCannotCancelOtherUserReservationReturns403(): void
    {
        $this->loginAsResponsable(); // user 2: Prof Test / prof@univ.sn

        $debut = new DateTimeImmutable('+3 days 10:00:00');
        $fin = new DateTimeImmutable('+3 days 12:00:00');
        $res = new Reservation([
            'salle_id'    => 1,
            'responsable' => 'Autre Professeur',
            'email'       => 'autre@univ.sn',
            'motif'       => 'Cours specifique',
            'date_debut'  => $debut->format('Y-m-d H:i:s'),
            'date_fin'    => $fin->format('Y-m-d H:i:s'),
            'statut'      => 'confirmée',
        ]);
        $res->id = 2;
        $this->reservationRepo->save($res);

        $token = $this->csrf->getToken();
        $response = $this->request('POST', '/reservations/2/cancel', [
            '_token' => $token,
        ]);

        $this->assertSame(403, $response['status']);
        $this->assertStringContainsString('Accès Refusé', $response['body']);
        $stillConfirmed = $this->reservationRepo->findById(2);
        $this->assertSame('confirmée', $stillConfirmed->statut);
    }

    public function testAdminCanCancelAnyReservation(): void
    {
        $this->loginAsAdmin(); // user 1: admin

        $debut = new DateTimeImmutable('+4 days 10:00:00');
        $fin = new DateTimeImmutable('+4 days 12:00:00');
        $res = new Reservation([
            'salle_id'    => 1,
            'responsable' => 'Autre Professeur',
            'email'       => 'autre@univ.sn',
            'motif'       => 'Cours a annuler par admin',
            'date_debut'  => $debut->format('Y-m-d H:i:s'),
            'date_fin'    => $fin->format('Y-m-d H:i:s'),
            'statut'      => 'confirmée',
        ]);
        $res->id = 3;
        $this->reservationRepo->save($res);

        $token = $this->csrf->getToken();
        $response = $this->request('POST', '/reservations/3/cancel', [
            '_token' => $token,
        ]);

        $this->assertSame(302, $response['status']);
        $updated = $this->reservationRepo->findById(3);
        $this->assertSame('annulée', $updated->statut);
    }
}
