<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\CreerReservationDTO;
use App\Exception\ReservationIntrouvableException;
use App\Exception\SalleIndisponibleException;
use App\Http\JsonResponse;
use App\Repository\ReservationRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Validation\ReservationValidator;
use Throwable;

class ApiReservationController
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservations,
        private readonly ReservationValidator $validator,
        private readonly CreerReservationService $creerReservationService,
        private readonly AnnulerReservationService $annulerReservationService
    ) {
    }

    public function index(): string
    {
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) && is_numeric($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 10;

        $criteres = [
            'salle_id' => $_GET['salle_id'] ?? null,
            'statut'   => $_GET['statut'] ?? null,
            'q'        => $_GET['q'] ?? null,
            'date'     => $_GET['date'] ?? null,
        ];

        $paginator = $this->reservations->search($criteres, $page, $perPage);
        return JsonResponse::ok($paginator->toArray());
    }

    public function show(int $id): string
    {
        $reservation = $this->reservations->findById($id);
        if ($reservation === null) {
            return JsonResponse::notFound("Réservation #{$id} introuvable.");
        }

        return JsonResponse::ok($reservation->toArray());
    }

    public function store(): string
    {
        $input = file_get_contents('php://input');
        $data = !empty($input) ? json_decode($input, true) : null;
        if (!is_array($data)) {
            $data = $_POST;
        }

        $result = $this->validator->validate($data);
        if (!$result->isValid()) {
            return JsonResponse::validationError($result->errors());
        }

        try {
            $dto = CreerReservationDTO::builder()->fromArray($result->validated())->build();
            $reservation = $this->creerReservationService->execute($dto);
            return JsonResponse::created($reservation->toArray());
        } catch (SalleIndisponibleException $e) {
            return JsonResponse::error($e->getMessage(), 422);
        } catch (Throwable $e) {
            return JsonResponse::error($e->getMessage(), 400);
        }
    }

    public function cancel(int $id): string
    {
        try {
            $success = $this->annulerReservationService->execute($id);
            if ($success) {
                return JsonResponse::ok([
                    'success' => true,
                    'message' => "La réservation #{$id} a été annulée.",
                ]);
            }
            return JsonResponse::notFound("Réservation #{$id} introuvable.");
        } catch (ReservationIntrouvableException $e) {
            return JsonResponse::notFound($e->getMessage());
        }
    }
}
