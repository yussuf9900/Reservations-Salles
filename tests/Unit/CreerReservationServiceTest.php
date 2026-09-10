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
        $this->service = new CreerReservationService($this->salleRepo, $this->reservationRepo, new \Tests\Double\InMemoryTransactionStrategy());

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

    public function testReservationEchoueSiSalleInexistante(): void
    {
        $demain = new DateTimeImmutable('+1 day');
        $dto = new CreerReservationDTO(
            salleId: 999,
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

    public function testReservationEchoueSiFinAnterieureAuDebut(): void
    {
        $demain = new DateTimeImmutable('+1 day');
        $dto = new CreerReservationDTO(
            salleId: 1,
            responsable: 'Awa Ndiaye',
            email: 'awa.ndiaye@universite.sn',
            motif: 'Cours d\'architecture logicielle',
            dateDebut: $demain->setTime(14, 0),
            dateFin: $demain->setTime(12, 0)
        );

        $this->expectException(SalleIndisponibleException::class);
        $this->expectExceptionMessage("La date de début doit précéder la date de fin.");

        $this->service->execute($dto);
    }

    public function testReservationEchoueSiDureeSuperieureAQuatreHeures(): void
    {
        $demain = new DateTimeImmutable('+1 day');
        $dto = new CreerReservationDTO(
            salleId: 1,
            responsable: 'Awa Ndiaye',
            email: 'awa.ndiaye@universite.sn',
            motif: 'Long atelier de programmation',
            dateDebut: $demain->setTime(8, 0),
            dateFin: $demain->setTime(14, 0)
        );

        $this->expectException(SalleIndisponibleException::class);
        $this->expectExceptionMessage("Une réservation ne peut pas dépasser quatre heures.");

        $this->service->execute($dto);
    }

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

    public function testReservationEchoueEnCasDeConflit(): void
    {
        $demain = new DateTimeImmutable('+1 day');
        
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

    public function testReservationsVoisinesSontAcceptees(): void
    {
        $demain = new DateTimeImmutable('+1 day');
        
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
