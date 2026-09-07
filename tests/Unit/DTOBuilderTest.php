<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\Builder\CreerReservationDTOBuilder;
use App\DTO\Builder\CreerSalleDTOBuilder;
use App\DTO\CreerReservationDTO;
use App\DTO\CreerSalleDTO;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DTOBuilderTest extends TestCase
{
    public function testReservationBuilderFluent(): void
    {
        $debut = new DateTimeImmutable('2026-09-10 10:00:00');
        $fin = new DateTimeImmutable('2026-09-10 12:00:00');

        $dto = CreerReservationDTO::builder()
            ->salleId(5)
            ->responsable('Fatou Sow')
            ->email('fatou@universite.sn')
            ->motif('Soutenance de Master')
            ->dateDebut($debut)
            ->dateFin($fin)
            ->build();

        $this->assertInstanceOf(CreerReservationDTO::class, $dto);
        $this->assertSame(5, $dto->salleId);
        $this->assertSame('Fatou Sow', $dto->responsable);
        $this->assertSame('fatou@universite.sn', $dto->email);
        $this->assertSame('Soutenance de Master', $dto->motif);
        $this->assertSame($debut, $dto->dateDebut);
        $this->assertSame($fin, $dto->dateFin);
    }

    public function testReservationBuilderWithDateStrings(): void
    {
        $dto = CreerReservationDTO::builder()
            ->salleId(2)
            ->responsable('Amadou Diallo')
            ->email('amadou@universite.sn')
            ->motif('TD Algorithmique')
            ->dateDebut('2026-09-11 08:00:00')
            ->dateFin('2026-09-11 10:00:00')
            ->build();

        $this->assertSame(2, $dto->salleId);
        $this->assertSame('2026-09-11 08:00:00', $dto->dateDebut->format('Y-m-d H:i:s'));
        $this->assertSame('2026-09-11 10:00:00', $dto->dateFin->format('Y-m-d H:i:s'));
    }

    public function testReservationBuilderFromArray(): void
    {
        $data = [
            'salle_id'    => '3',
            'responsable' => 'Ousmane Ba',
            'email'       => 'ousmane@universite.sn',
            'motif'       => 'Examen final',
            'date_debut'  => '2026-09-12 14:00:00',
            'date_fin'    => '2026-09-12 16:00:00',
        ];

        $dto = CreerReservationDTO::fromArray($data);

        $this->assertSame(3, $dto->salleId);
        $this->assertSame('Ousmane Ba', $dto->responsable);
        $this->assertSame('ousmane@universite.sn', $dto->email);
        $this->assertSame('Examen final', $dto->motif);
    }

    public function testReservationBuilderThrowsOnMissingSalleId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("L'identifiant de la salle est requis.");

        CreerReservationDTO::builder()
            ->responsable('Test')
            ->email('test@universite.sn')
            ->motif('Motif')
            ->dateDebut(new DateTimeImmutable('+1 day'))
            ->dateFin(new DateTimeImmutable('+1 day +2 hours'))
            ->build();
    }

    public function testReservationBuilderThrowsOnMissingResponsable(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Le nom du responsable est requis.");

        CreerReservationDTO::builder()
            ->salleId(1)
            ->responsable('   ')
            ->email('test@universite.sn')
            ->motif('Motif')
            ->dateDebut(new DateTimeImmutable('+1 day'))
            ->dateFin(new DateTimeImmutable('+1 day +2 hours'))
            ->build();
    }

    public function testReservationBuilderThrowsOnInvalidDates(): void
    {
        $this->expectException(InvalidArgumentException::class);

        CreerReservationDTO::builder()
            ->salleId(1)
            ->responsable('Test')
            ->email('test@universite.sn')
            ->motif('Motif')
            ->dateDebut('date-invalide')
            ->dateFin('autre-date-invalide')
            ->build();
    }

    public function testReservationToBuilder(): void
    {
        $dto = CreerReservationDTO::builder()
            ->salleId(1)
            ->responsable('Initial Name')
            ->email('initial@test.com')
            ->motif('Initial Motif')
            ->dateDebut(new DateTimeImmutable('+1 day'))
            ->dateFin(new DateTimeImmutable('+1 day +2 hours'))
            ->build();

        $nouveauDto = $dto->toBuilder()
            ->responsable('Updated Name')
            ->motif('Updated Motif')
            ->build();

        $this->assertSame('Updated Name', $nouveauDto->responsable);
        $this->assertSame('Updated Motif', $nouveauDto->motif);
        $this->assertSame(1, $nouveauDto->salleId);
        $this->assertSame('initial@test.com', $nouveauDto->email);
    }

    public function testSalleBuilderFluent(): void
    {
        $dto = CreerSalleDTO::builder()
            ->nom('Amphi B')
            ->batiment('Sciences')
            ->capacite(150)
            ->type('amphitheatre')
            ->active(true)
            ->build();

        $this->assertInstanceOf(CreerSalleDTO::class, $dto);
        $this->assertSame('Amphi B', $dto->nom);
        $this->assertSame('Sciences', $dto->batiment);
        $this->assertSame(150, $dto->capacite);
        $this->assertSame('amphitheatre', $dto->type);
        $this->assertTrue($dto->active);
    }

    public function testSalleBuilderFromArray(): void
    {
        $data = [
            'nom'      => 'Salle 102',
            'batiment' => 'Batiment C',
            'capacite' => '35',
            'type'     => 'cours',
            'active'   => '1',
        ];

        $dto = CreerSalleDTO::fromArray($data);

        $this->assertSame('Salle 102', $dto->nom);
        $this->assertSame('Batiment C', $dto->batiment);
        $this->assertSame(35, $dto->capacite);
        $this->assertSame('cours', $dto->type);
        $this->assertTrue($dto->active);
    }

    public function testSalleBuilderThrowsOnMissingNom(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Le nom de la salle est requis.");

        CreerSalleDTO::builder()
            ->batiment('Batiment A')
            ->capacite(50)
            ->type('cours')
            ->build();
    }

    public function testSalleBuilderThrowsOnMissingCapacite(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("La capacité de la salle est requise.");

        CreerSalleDTO::builder()
            ->nom('Salle A')
            ->batiment('Batiment A')
            ->type('cours')
            ->build();
    }

    public function testSalleToBuilder(): void
    {
        $dto = CreerSalleDTO::builder()
            ->nom('Salle Originale')
            ->batiment('Batiment 1')
            ->capacite(20)
            ->type('reunion')
            ->active(true)
            ->build();

        $nouveauDto = $dto->toBuilder()
            ->nom('Salle Renommee')
            ->capacite(25)
            ->build();

        $this->assertSame('Salle Renommee', $nouveauDto->nom);
        $this->assertSame('Batiment 1', $nouveauDto->batiment);
        $this->assertSame(25, $nouveauDto->capacite);
        $this->assertSame('reunion', $nouveauDto->type);
    }
}
