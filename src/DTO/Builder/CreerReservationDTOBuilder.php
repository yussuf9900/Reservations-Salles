<?php

declare(strict_types=1);

namespace App\DTO\Builder;

use App\DTO\CreerReservationDTO;
use DateTimeImmutable;
use InvalidArgumentException;

class CreerReservationDTOBuilder
{
    private ?int $salleId = null;
    private ?string $responsable = null;
    private ?string $email = null;
    private ?string $motif = null;
    private ?DateTimeImmutable $dateDebut = null;
    private ?DateTimeImmutable $dateFin = null;

    public function salleId(int $salleId): self
    {
        $this->salleId = $salleId;
        return $this;
    }

    public function responsable(string $responsable): self
    {
        $this->responsable = trim($responsable);
        return $this;
    }

    public function email(string $email): self
    {
        $this->email = trim($email);
        return $this;
    }

    public function motif(string $motif): self
    {
        $this->motif = trim($motif);
        return $this;
    }

    public function dateDebut(DateTimeImmutable|string $dateDebut): self
    {
        if (is_string($dateDebut)) {
            try {
                $this->dateDebut = new DateTimeImmutable($dateDebut);
            } catch (\Throwable $e) {
                throw new InvalidArgumentException("Format de date de début invalide : " . $e->getMessage(), 0, $e);
            }
        } else {
            $this->dateDebut = $dateDebut;
        }
        return $this;
    }

    public function dateFin(DateTimeImmutable|string $dateFin): self
    {
        if (is_string($dateFin)) {
            try {
                $this->dateFin = new DateTimeImmutable($dateFin);
            } catch (\Throwable $e) {
                throw new InvalidArgumentException("Format de date de fin invalide : " . $e->getMessage(), 0, $e);
            }
        } else {
            $this->dateFin = $dateFin;
        }
        return $this;
    }

    public function fromArray(array $data): self
    {
        $salleId = (int)($data['salle_id'] ?? $data['salleId'] ?? 0);
        $this->salleId($salleId);

        $this->responsable((string)($data['responsable'] ?? ''));
        $this->email((string)($data['email'] ?? ''));
        $this->motif((string)($data['motif'] ?? ''));

        $debutRaw = (string)($data['date_debut'] ?? $data['dateDebut'] ?? '');
        $finRaw = (string)($data['date_fin'] ?? $data['dateFin'] ?? '');

        try {
            $this->dateDebut($debutRaw);
            $this->dateFin($finRaw);
        } catch (\Throwable $e) {
            throw new InvalidArgumentException("Format de date invalide pour la réservation : " . $e->getMessage(), 0, $e);
        }

        return $this;
    }

    public function build(): CreerReservationDTO
    {
        if ($this->salleId === null) {
            throw new InvalidArgumentException("L'identifiant de la salle est requis.");
        }
        if ($this->responsable === null || $this->responsable === '') {
            throw new InvalidArgumentException("Le nom du responsable est requis.");
        }
        if ($this->email === null || $this->email === '') {
            throw new InvalidArgumentException("L'email du responsable est requis.");
        }
        if ($this->motif === null || $this->motif === '') {
            throw new InvalidArgumentException("Le motif de la réservation est requis.");
        }
        if ($this->dateDebut === null) {
            throw new InvalidArgumentException("La date de début est requise.");
        }
        if ($this->dateFin === null) {
            throw new InvalidArgumentException("La date de fin est requise.");
        }

        return new CreerReservationDTO(
            salleId: $this->salleId,
            responsable: $this->responsable,
            email: $this->email,
            motif: $this->motif,
            dateDebut: $this->dateDebut,
            dateFin: $this->dateFin
        );
    }
}
