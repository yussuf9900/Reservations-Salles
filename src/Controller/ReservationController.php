<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerReservationDTO;
use App\Exception\ReservationIntrouvableException;
use App\Exception\SalleIndisponibleException;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Validation\ReservationValidator;
use App\View\ViewRenderer;

class ReservationController
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservations,
        private readonly SalleRepositoryInterface $salles,
        private readonly ReservationValidator $validator,
        private readonly CreerReservationService $creerReservationService,
        private readonly AnnulerReservationService $annulerReservationService,
        private readonly ViewRenderer $view
    ) {
    }

    public function index(): string
    {
        $salleId = isset($_GET['salle_id']) && is_numeric($_GET['salle_id']) ? (int)$_GET['salle_id'] : null;

        if ($salleId !== null && $salleId > 0) {
            $liste = $this->reservations->findBySalle($salleId);
        } else {
            $liste = $this->reservations->all();
        }

        return $this->view->render('reservation/index', [
            'title'                => 'Liste des réservations',
            'reservations'         => $liste,
            'salles'               => $this->salles->all(),
            'salleIdSelectionnee'  => $salleId,
        ]);
    }

    public function show(int $id): string
    {
        $reservation = $this->reservations->findById($id);
        if ($reservation === null) {
            http_response_code(404);
            return $this->view->render('error/404', ['title' => 'Réservation introuvable']);
        }

        return $this->view->render('reservation/show', [
            'title'       => 'Réservation #' . $reservation->id,
            'reservation' => $reservation,
        ]);
    }

    public function create(): string
    {
        $salleId = isset($_GET['salle_id']) && is_numeric($_GET['salle_id']) ? (int)$_GET['salle_id'] : null;

        return $this->view->render('reservation/form', [
            'title'               => 'Nouvelle réservation',
            'salles'              => $this->salles->all(),
            'salleIdSelectionnee' => $salleId,
            'old'                 => $salleId ? ['salle_id' => $salleId] : [],
            'errors'              => [],
        ]);
    }

    public function store(): string
    {
        // 1. Lire les données HTTP
        $data = $_POST;

        // 2. Appeler le validateur syntaxique
        $result = $this->validator->validate($data);

        // 3. Réafficher le formulaire en cas d'erreur de validation
        if (!$result->isValid()) {
            return $this->view->render('reservation/form', [
                'title'               => 'Nouvelle réservation',
                'salles'              => $this->salles->all(),
                'salleIdSelectionnee' => (int)($data['salle_id'] ?? 0),
                'old'                 => $data,
                'errors'              => $result->errors(),
            ]);
        }

        // 4. Construire le DTO
        try {
            $dto = CreerReservationDTO::fromArray($result->validated());
        } catch (\Throwable $e) {
            return $this->view->render('reservation/form', [
                'title'               => 'Nouvelle réservation',
                'salles'              => $this->salles->all(),
                'salleIdSelectionnee' => (int)($data['salle_id'] ?? 0),
                'old'                 => $data,
                'errors'              => ['date_debut' => $e->getMessage()],
            ]);
        }

        // 5. Appeler le service métier
        try {
            $reservation = $this->creerReservationService->execute($dto);
        } catch (SalleIndisponibleException $e) {
            $this->view->setFlash('error', $e->getMessage());
            return $this->view->render('reservation/form', [
                'title'               => 'Nouvelle réservation',
                'salles'              => $this->salles->all(),
                'salleIdSelectionnee' => (int)($data['salle_id'] ?? 0),
                'old'                 => $data,
                'errors'              => ['general' => $e->getMessage()],
            ]);
        }

        // 6. Rediriger après succès
        $this->view->setFlash('success', 'Réservation confirmée avec succès.');
        header('Location: /reservations/' . $reservation->id);
        exit;
    }

    public function cancel(int $id): void
    {
        try {
            $this->annulerReservationService->execute($id);
            $this->view->setFlash('success', "La réservation #{$id} a été annulée avec succès.");
        } catch (ReservationIntrouvableException $e) {
            $this->view->setFlash('error', $e->getMessage());
        }

        header('Location: /reservations/' . $id);
        exit;
    }
}
