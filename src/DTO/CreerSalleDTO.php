<?php

declare(strict_types=1);

namespace App\DTO;

use App\DTO\Builder\CreerSalleDTOBuilder;

class CreerSalleDTO
{
    public function __construct(
        public readonly string $nom,
        public readonly string $batiment,
        public readonly int $capacite,
        public readonly string $type,
        public readonly bool $active = true
    ) {
    }

    public static function builder(): CreerSalleDTOBuilder
    {
        return new CreerSalleDTOBuilder();
    }

    public static function fromArray(array $data): self
    {
        return self::builder()->fromArray($data)->build();
    }

    public function toBuilder(): CreerSalleDTOBuilder
    {
        return self::builder()
            ->nom($this->nom)
            ->batiment($this->batiment)
            ->capacite($this->capacite)
            ->type($this->type)
            ->active($this->active);
    }
}
