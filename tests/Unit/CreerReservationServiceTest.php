<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\CreerReservationDTO;
use App\Exception\SalleIndisponibleException;
use App\Model\Reservation;
use App\Model\Salle;
use App\Service\CreerReservationService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Tests\Double\InMemoryReservationRepository;
use Tests\Double\InMemorySalleRepository;

class CreerReservationServiceTest extends TestCase
{
    private InMemorySalleRepository $salleRepo;
    private InMemoryReservationRepository $reservationRepo;
    private CreerReservationService $service;

    protected function setUp(): void
    {
        $this->salleRepo = new InMemorySalleRepository();
        $this->reservationRepo = new InMemoryReservationRepository();
        $this->service = new CreerReservationService($this->salleRepo, $this->reservationRepo);

        // Créer une salle active par défaut
        $salle = new Salle([
            'nom'      => 'Salle B12',
            'batiment' => 'Bâtiment B',
            'capacite' => 40,
            'type'     => 'cours',
            'active'   => true,
        ]);
        $salle->id = 1;
        $this->salleRepo->save($salle);
    }

    /**
     * Cas 1 : Réservation valide
     */
    public function testReservationValideEstCreeeAvecSucces(): void
    {
        $demain = new DateTimeImmutable('+1 day');
        $debut = $demain->setTime(10, 0);
        $fin = $demain->setTime(12, 0);

        $dto = new CreerReservationDTO(
            salleId: 1,
            responsable: 'Awa Ndiaye',
            email: 'awa.ndiaye@universite.sn',
            motif: 'Cours d\'architecture logicielle',
            dateDebut: $debut,
            dateFin: $fin
        );

        $reservation = $this->service->execute($dto);

        $this->assertInstanceOf(Reservation::class, $reservation);
        $this->assertSame('confirmée', $reservation->statut);
        $this->assertSame(1, $reservation->salle_id);
        $this->assertCount(1, $this->reservationRepo->all());
    }

    /**
     * Cas 2 : Salle inexistante
     */
    public function testReservationEchoueSiSalleInexistante(): void
    {
        $demain = new DateTimeImmutable('+1 day');
        $dto = new CreerReservationDTO(
            salleId: 999, // Inexistante
            responsable: 'Awa Ndiaye',
            email: 'awa.ndiaye@universite.sn',
            motif: 'Cours d\'architecture logicielle',
            dateDebut: $demain->setTime(10, 0),
            dateFin: $demain->setTime(12, 0)
        );

        $this->expectException(SalleIndisponibleException::class);
        $this->expectExceptionMessage("La salle sélectionnée n'existe pas.");

        $this->service->execute($dto);
    }

    /**
     * Cas 3 : Salle inactive
     */
    public function testReservationEchoueSiSalleInactive(): void
    {
        $salleInactive = new Salle([
            'nom'      => 'Salle Rénovation',
            'batiment' => 'Bâtiment C',
            'capacite' => 20,
            'type'     => 'cours',
            'active'   => false,
        ]);
        $salleInactive->id = 2;
        $this->salleRepo->save($salleInactive);

        $demain = new DateTimeImmutable('+1 day');
        $dto = new CreerReservationDTO(
            salleId: 2,
            responsable: 'Awa Ndiaye',
            email: 'awa.ndiaye@universite.sn',
            motif: 'Cours d\'architecture logicielle',
            dateDebut: $demain->setTime(10, 0),
            dateFin: $demain->setTime(12, 0)
        );

        $this->expectException(SalleIndisponibleException::class);
        $this->expectExceptionMessage("Cette salle ne peut pas être réservée.");

        $this->service->execute($dto);
    }

    /**
     * Cas 4 : Date de fin antérieure ou égale au début
     */
    public function testReservationEchoueSiFinAnterieureAuDebut(): void
    {
        $demain = new DateTimeImmutable('+1 day');
        $dto = new CreerReservationDTO(
            salleId: 1,
            responsable: 'Awa Ndiaye',
            email: 'awa.ndiaye@universite.sn',
            motif: 'Cours d\'architecture logicielle',
            dateDebut: $demain->setTime(14, 0),
            dateFin: $demain->setTime(12, 0) // Fin antérieure
        );

        $this->expectException(SalleIndisponibleException::class);
        $this->expectExceptionMessage("La date de début doit précéder la date de fin.");

        $this->service->execute($dto);
    }

    /**
     * Cas 5 : Durée supérieure à 4 heures
     */
    public function testReservationEchoueSiDureeSuperieureAQuatreHeures(): void
    {
        $demain = new DateTimeImmutable('+1 day');
        $dto = new CreerReservationDTO(
            salleId: 1,
            responsable: 'Awa Ndiaye',
            email: 'awa.ndiaye@universite.sn',
            motif: 'Long atelier de programmation',
            dateDebut: $demain->setTime(8, 0),
            dateFin: $demain->setTime(14, 0) // 6 heures
        );

        $this->expectException(SalleIndisponibleException::class);
        $this->expectExceptionMessage("Une réservation ne peut pas dépasser quatre heures.");

        $this->service->execute($dto);
    }

    /**
     * Cas 6 : Date passée
     */
    public function testReservationEchoueSiDatePassee(): void
    {
        $hier = new DateTimeImmutable('-1 day');
        $dto = new CreerReservationDTO(
            salleId: 1,
            responsable: 'Awa Ndiaye',
            email: 'awa.ndiaye@universite.sn',
            motif: 'Session rattrapage',
            dateDebut: $hier->setTime(10, 0),
            dateFin: $hier->setTime(12, 0)
        );

        $this->expectException(SalleIndisponibleException::class);
        $this->expectExceptionMessage("La réservation doit commencer dans le futur.");

        $this->service->execute($dto);
    }

    /**
     * Cas 7 : Conflit avec une réservation existante (chevauchement)
     */
    public function testReservationEchoueEnCasDeConflit(): void
    {
        $demain = new DateTimeImmutable('+1 day');
        
        // Réservation existante : 10h00 -> 12h00
        $existante = new Reservation([
            'salle_id'    => 1,
            'responsable' => 'Moussa Diop',
            'email'       => 'moussa@universite.sn',
            'motif'       => 'Cours existant',
            'date_debut'  => $demain->setTime(10, 0),
            'date_fin'    => $demain->setTime(12, 0),
            'statut'      => 'confirmée',
        ]);
        $existante->id = 10;
        $this->reservationRepo->save($existante);

        // Nouvelle demande : 11h30 -> 13h00 (chevauchement)
        $dto = new CreerReservationDTO(
            salleId: 1,
            responsable: 'Awa Ndiaye',
            email: 'awa.ndiaye@universite.sn',
            motif: 'Nouvelle demande en conflit',
            dateDebut: $demain->setTime(11, 30),
            dateFin: $demain->setTime(13, 0)
        );

        $this->expectException(SalleIndisponibleException::class);
        $this->expectExceptionMessage("La salle est indisponible pendant cette période.");

        $this->service->execute($dto);
    }

    /**
     * Cas 8 : Réservations voisines sans chevauchement
     */
    public function testReservationsVoisinesSontAcceptees(): void
    {
        $demain = new DateTimeImmutable('+1 day');
        
        // Réservation existante : 10h00 -> 12h00
        $existante = new Reservation([
            'salle_id'    => 1,
            'responsable' => 'Moussa Diop',
            'email'       => 'moussa@universite.sn',
            'motif'       => 'Cours 1',
            'date_debut'  => $demain->setTime(10, 0),
            'date_fin'    => $demain->setTime(12, 0),
            'statut'      => 'confirmée',
        ]);
        $existante->id = 11;
        $this->reservationRepo->save($existante);

        // Nouvelle demande voisine : 12h00 -> 14h00
        $dto = new CreerReservationDTO(
            salleId: 1,
            responsable: 'Awa Ndiaye',
            email: 'awa.ndiaye@universite.sn',
            motif: 'Cours suivant sans chevauchement',
            dateDebut: $demain->setTime(12, 0),
            dateFin: $demain->setTime(14, 0)
        );

        $res = $this->service->execute($dto);

        $this->assertInstanceOf(Reservation::class, $res);
        $this->assertSame('confirmée', $res->statut);
    }
}
