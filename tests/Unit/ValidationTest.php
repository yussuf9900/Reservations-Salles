<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;
use PHPUnit\Framework\TestCase;

class ValidationTest extends TestCase
{
    private SalleValidator $salleValidator;
    private ReservationValidator $reservationValidator;

    protected function setUp(): void
    {
        $this->salleValidator = new SalleValidator();
        $this->reservationValidator = new ReservationValidator();
    }

    public function testAdresseElectroniqueInvalide(): void
    {
        $data = [
            'salle_id'    => 1,
            'responsable' => 'Jean Dupont',
            'email'       => 'adresse-invalide-sans-arobase',
            'motif'       => 'Réunion de département',
            'date_debut'  => '2026-09-08 10:00:00',
            'date_fin'    => '2026-09-08 12:00:00',
        ];

        $result = $this->reservationValidator->validate($data);

        $this->assertFalse($result->isValid());
        $this->assertArrayHasKey('email', $result->errors());
    }

    public function testResponsableVide(): void
    {
        $data = [
            'salle_id'    => 1,
            'responsable' => '   ',
            'email'       => 'jean@universite.sn',
            'motif'       => 'Réunion de département',
            'date_debut'  => '2026-09-08 10:00:00',
            'date_fin'    => '2026-09-08 12:00:00',
        ];

        $result = $this->reservationValidator->validate($data);

        $this->assertFalse($result->isValid());
        $this->assertArrayHasKey('responsable', $result->errors());
    }

    public function testCapaciteNegative(): void
    {
        $data = [
            'nom'      => 'Salle Test',
            'batiment' => 'Bâtiment A',
            'capacite' => -10,
            'type'     => 'cours',
            'active'   => true,
        ];

        $result = $this->salleValidator->validate($data);

        $this->assertFalse($result->isValid());
        $this->assertArrayHasKey('capacite', $result->errors());
    }

    public function testTypeSalleInconnu(): void
    {
        $data = [
            'nom'      => 'Salle Test',
            'batiment' => 'Bâtiment A',
            'capacite' => 30,
            'type'     => 'cafeteria',
            'active'   => true,
        ];

        $result = $this->salleValidator->validate($data);

        $this->assertFalse($result->isValid());
        $this->assertArrayHasKey('type', $result->errors());
    }

    public function testDateIncorrecte(): void
    {
        $data = [
            'salle_id'    => 1,
            'responsable' => 'Jean Dupont',
            'email'       => 'jean@universite.sn',
            'motif'       => 'Réunion de département',
            'date_debut'  => 'pas-une-date-valide',
            'date_fin'    => '2026-09-08 12:00:00',
        ];

        $result = $this->reservationValidator->validate($data);

        $this->assertFalse($result->isValid());
        $this->assertArrayHasKey('date_debut', $result->errors());
    }
}
