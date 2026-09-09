<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Model\Reservation;
use App\Model\Salle;
use App\Service\StatistiquesService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Tests\Double\InMemoryReservationRepository;
use Tests\Double\InMemorySalleRepository;

class StatistiquesServiceTest extends TestCase
{
    public function testGetStatistiquesCalculations(): void
    {
        $salleRepo = new InMemorySalleRepository();
        $resRepo = new InMemoryReservationRepository();

        $salle1 = new Salle([
            'id'       => 1,
            'nom'      => 'Amphi 1',
            'batiment' => 'Bat A',
            'capacite' => 100,
            'type'     => 'amphitheatre',
            'active'   => true,
        ]);
        $salle2 = new Salle([
            'id'       => 2,
            'nom'      => 'Labo 1',
            'batiment' => 'Bat B',
            'capacite' => 25,
            'type'     => 'laboratoire',
            'active'   => false,
        ]);

        $salleRepo->save($salle1);
        $salleRepo->save($salle2);

        $debut1 = new DateTimeImmutable('+1 day 10:00:00');
        $fin1 = new DateTimeImmutable('+1 day 12:00:00');
        $res1 = new Reservation([
            'salle_id'    => 1,
            'responsable' => 'Professeur X',
            'email'       => 'x@univ.sn',
            'motif'       => 'Cours magistral',
            'date_debut'  => $debut1->format('Y-m-d H:i:s'),
            'date_fin'    => $fin1->format('Y-m-d H:i:s'),
            'statut'      => 'confirmée',
        ]);
        $res1->setRelation('salle', $salle1);

        $res2 = new Reservation([
            'salle_id'    => 1,
            'responsable' => 'Professeur Y',
            'email'       => 'y@univ.sn',
            'motif'       => 'Annulé',
            'date_debut'  => $debut1->format('Y-m-d H:i:s'),
            'date_fin'    => $fin1->format('Y-m-d H:i:s'),
            'statut'      => 'annulée',
        ]);
        $res2->setRelation('salle', $salle1);

        $resRepo->save($res1);
        $resRepo->save($res2);

        $service = new StatistiquesService($salleRepo, $resRepo);
        $stats = $service->getStatistiques();

        $this->assertSame(2, $stats['total_salles']);
        $this->assertSame(1, $stats['salles_actives']);
        $this->assertSame(1, $stats['salles_inactives']);
        $this->assertSame(2, $stats['total_reservations']);
        $this->assertSame(1, $stats['reservations_confirmees']);
        $this->assertSame(1, $stats['reservations_annulees']);
        $this->assertEquals(2.0, $stats['heures_totales']);
        $this->assertNotEmpty($stats['top_salles']);
        $this->assertSame(1, $stats['top_salles'][0]['id']);
        $this->assertSame(1, $stats['top_salles'][0]['reservations']);
    }
}
