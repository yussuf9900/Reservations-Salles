<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Model\Reservation;
use App\Model\Salle;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class EloquentIntegrationTest extends TestCase
{
    private EloquentSalleRepository $salleRepo;
    private EloquentReservationRepository $reservationRepo;

    protected function setUp(): void
    {
        // Initialiser la base de données
        $initDb = require dirname(__DIR__, 2) . '/config/database.php';
        $initDb();

        $this->salleRepo = new EloquentSalleRepository();
        $this->reservationRepo = new EloquentReservationRepository();
    }

    /**
     * Test 1 : Création d'une salle avec Eloquent et le Repository
     */
    public function testCreationSalleAvecEloquent(): Salle
    {
        $nomUnique = 'Salle Test Eloquent ' . uniqid();
        $salle = new Salle([
            'nom'      => $nomUnique,
            'batiment' => 'Bâtiment Test',
            'capacite' => 50,
            'type'     => 'cours',
            'active'   => true,
        ]);

        $saved = $this->salleRepo->save($salle);

        $this->assertTrue($saved);
        $this->assertNotNull($salle->id);

        $retrouvee = $this->salleRepo->findById((int)$salle->id);
        $this->assertNotNull($retrouvee);
        $this->assertSame($nomUnique, $retrouvee->nom);

        return $retrouvee;
    }

    /**
     * Test 2 : Relation salle / réservations
     * @depends testCreationSalleAvecEloquent
     */
    public function testRelationSalleReservations(Salle $salle): Reservation
    {
        $debut = new DateTimeImmutable('+5 days 10:00:00');
        $fin = new DateTimeImmutable('+5 days 12:00:00');

        $reservation = new Reservation([
            'salle_id'    => $salle->id,
            'responsable' => 'Moussa Sall',
            'email'       => 'moussa.sall@universite.sn',
            'motif'       => 'Examen semestriel',
            'date_debut'  => $debut->format('Y-m-d H:i:s'),
            'date_fin'    => $fin->format('Y-m-d H:i:s'),
            'statut'      => 'confirmée',
        ]);

        $this->reservationRepo->save($reservation);

        $this->assertNotNull($reservation->id);
        // Test relation Reservation -> Salle
        $this->assertSame((int)$salle->id, (int)$reservation->salle->id);

        // Test relation Salle -> Reservations
        $reservationsSalle = $salle->reservations;
        $this->assertTrue($reservationsSalle->contains('id', $reservation->id));

        return $reservation;
    }

    /**
     * Test 3 : Recherche de chevauchement en base
     * @depends testCreationSalleAvecEloquent
     */
    public function testRechercheChevauchementEnBase(Salle $salle): void
    {
        // Créneau en conflit : +5 days 11h00 -> 13h00 (la résa existante est 10h00 -> 12h00)
        $debutConflit = new DateTimeImmutable('+5 days 11:00:00');
        $finConflit = new DateTimeImmutable('+5 days 13:00:00');

        $conflit = $this->reservationRepo->trouverConflit(
            (int)$salle->id,
            $debutConflit,
            $finConflit
        );

        $this->assertNotNull($conflit, 'Un conflit aurait dû être détecté pour le créneau 11h-13h.');
        $this->assertSame('confirmée', $conflit->statut);

        // Créneau voisin sans conflit : +5 days 12h00 -> 14h00
        $debutVoisin = new DateTimeImmutable('+5 days 12:00:00');
        $finVoisin = new DateTimeImmutable('+5 days 14:00:00');

        $sansConflit = $this->reservationRepo->trouverConflit(
            (int)$salle->id,
            $debutVoisin,
            $finVoisin
        );

        $this->assertNull($sansConflit, 'Aucun conflit ne devrait être détecté pour un créneau voisin (12h-14h).');
    }

    /**
     * Test 4 : Annulation d'une réservation
     * @depends testRelationSalleReservations
     */
    public function testAnnulationReservation(Reservation $reservation): void
    {
        $annule = $this->reservationRepo->annuler((int)$reservation->id);
        $this->assertTrue($annule);

        $miseAJour = $this->reservationRepo->findById((int)$reservation->id);
        $this->assertNotNull($miseAJour);
        $this->assertSame('annulée', $miseAJour->statut);

        // Une réservation annulée ne bloque plus le créneau
        $conflitApresAnnulation = $this->reservationRepo->trouverConflit(
            (int)$reservation->salle_id,
            new DateTimeImmutable('+5 days 10:30:00'),
            new DateTimeImmutable('+5 days 11:30:00')
        );

        $this->assertNull($conflitApresAnnulation, 'Une réservation annulée ne doit plus bloquer la salle.');
    }
}
