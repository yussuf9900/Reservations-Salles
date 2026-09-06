<?php

declare(strict_types=1);

namespace App\DTO;

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

    public static function fromArray(array $data): self
    {
        return new self(
            nom: trim((string)($data['nom'] ?? '')),
            batiment: trim((string)($data['batiment'] ?? '')),
            capacite: (int)($data['capacite'] ?? 0),
            type: (string)($data['type'] ?? ''),
            active: isset($data['active']) ? (bool)filter_var($data['active'], FILTER_VALIDATE_BOOLEAN) : true
        );
    }
}
