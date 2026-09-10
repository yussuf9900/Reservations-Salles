<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use DateTimeInterface;

interface ReservationRepositoryInterface
{
    public function all(): array;

    public function findBySalle(int $salleId): array;

    public function findById(int $id): ?Reservation;

    public function save(Reservation $reservation): bool;

    public function annuler(int $id): bool;

    public function trouverConflit(
        int $salleId,
        DateTimeInterface $debut,
        DateTimeInterface $fin,
        ?int $exclureId = null
    ): ?Reservation;

    public function search(array $criteres = [], int $page = 1, int $perPage = 8): LengthAwarePaginator;
}
