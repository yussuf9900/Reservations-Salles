<?php

declare(strict_types=1);

namespace App\DTO\Builder;

use App\DTO\CreerSalleDTO;
use InvalidArgumentException;

class CreerSalleDTOBuilder
{
    private ?string $nom = null;
    private ?string $batiment = null;
    private ?int $capacite = null;
    private ?string $type = null;
    private bool $active = true;

    public function nom(string $nom): self
    {
        $this->nom = trim($nom);
        return $this;
    }

    public function batiment(string $batiment): self
    {
        $this->batiment = trim($batiment);
        return $this;
    }

    public function capacite(int $capacite): self
    {
        $this->capacite = $capacite;
        return $this;
    }

    public function type(string $type): self
    {
        $this->type = trim($type);
        return $this;
    }

    public function active(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function fromArray(array $data): self
    {
        if (isset($data['nom'])) {
            $this->nom((string)$data['nom']);
        }
        if (isset($data['batiment'])) {
            $this->batiment((string)$data['batiment']);
        }
        if (isset($data['capacite'])) {
            $this->capacite((int)$data['capacite']);
        }
        if (isset($data['type'])) {
            $this->type((string)$data['type']);
        }
        if (isset($data['active'])) {
            $this->active((bool)filter_var($data['active'], FILTER_VALIDATE_BOOLEAN));
        }
        return $this;
    }

    public function build(): CreerSalleDTO
    {
        if ($this->nom === null || $this->nom === '') {
            throw new InvalidArgumentException("Le nom de la salle est requis.");
        }
        if ($this->batiment === null || $this->batiment === '') {
            throw new InvalidArgumentException("Le bâtiment de la salle est requis.");
        }
        if ($this->capacite === null) {
            throw new InvalidArgumentException("La capacité de la salle est requise.");
        }
        if ($this->type === null || $this->type === '') {
            throw new InvalidArgumentException("Le type de la salle est requis.");
        }

        return new CreerSalleDTO(
            nom: $this->nom,
            batiment: $this->batiment,
            capacite: $this->capacite,
            type: $this->type,
            active: $this->active
        );
    }
}
