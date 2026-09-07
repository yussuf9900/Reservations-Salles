<?php

declare(strict_types=1);

namespace App\DTO;

use App\DTO\Builder\CreerReservationDTOBuilder;
use DateTimeImmutable;

class CreerReservationDTO
{
    public function __construct(
        public readonly int $salleId,
        public readonly string $responsable,
        public readonly string $email,
        public readonly string $motif,
        public readonly DateTimeImmutable $dateDebut,
        public readonly DateTimeImmutable $dateFin
    ) {
    }

    public static function builder(): CreerReservationDTOBuilder
    {
        return new CreerReservationDTOBuilder();
    }

    public static function fromArray(array $data): self
    {
        return self::builder()->fromArray($data)->build();
    }

    public function toBuilder(): CreerReservationDTOBuilder
    {
        return self::builder()
            ->salleId($this->salleId)
            ->responsable($this->responsable)
            ->email($this->email)
            ->motif($this->motif)
            ->dateDebut($this->dateDebut)
            ->dateFin($this->dateFin);
    }
}
