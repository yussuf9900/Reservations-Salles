<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerReservationDTO;
use App\Exception\ReservationIntrouvableException;
use App\Exception\SalleIndisponibleException;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\AuthService;
use App\Service\CreerReservationService;
use App\Service\CsrfService;
use App\Validation\ReservationValidator;
use App\View\ViewRenderer;
use Throwable;

class ReservationController
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservations,
        private readonly SalleRepositoryInterface $salles,
        private readonly ReservationValidator $validator,
        private readonly CreerReservationService $creerReservationService,
        private readonly AnnulerReservationService $annulerReservationService,
        private readonly ViewRenderer $view,
        private readonly ?CsrfService $csrf = null,
        private readonly ?AuthService $auth = null
    ) {
    }

    public function index(): string
    {
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 8;

        $criteres = [
            'salle_id' => isset($_GET['salle_id']) && is_numeric($_GET['salle_id']) ? (int)$_GET['salle_id'] : '',
            'statut'   => isset($_GET['statut']) ? trim((string)$_GET['statut']) : '',
            'q'        => isset($_GET['q']) ? trim((string)$_GET['q']) : '',
            'date'     => isset($_GET['date']) ? trim((string)$_GET['date']) : '',
        ];

        $paginator = $this->reservations->search($criteres, $page, $perPage);

        $queryParams = array_filter($criteres, fn($v) => $v !== '' && $v !== null);

        return $this->view->render('reservation/index', [
            'title'               => 'Liste des réservations',
            'reservations'        => $paginator->items(),
            'paginator'           => $paginator,
            'criteres'            => $criteres,
            'queryParams'         => $queryParams,
            'baseUrl'             => '/reservations',
            'salles'              => $this->salles->all(),
            'salleIdSelectionnee' => $criteres['salle_id'] !== '' ? (int)$criteres['salle_id'] : null,
            'isAdmin'             => $this->auth?->isAdmin() ?? false,
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
            'csrf_token'  => $this->csrf?->getToken() ?? '',
            'isAdmin'     => $this->auth?->isAdmin() ?? false,
        ]);
    }

    public function create(): string
    {
        $salleId = isset($_GET['salle_id']) && is_numeric($_GET['salle_id']) ? (int)$_GET['salle_id'] : null;
        $currentUser = $this->auth?->user();

        $old = $salleId ? ['salle_id' => $salleId] : [];
        if ($currentUser !== null) {
            $old['responsable'] = $currentUser->nom;
            $old['email'] = $currentUser->email;
        }

        return $this->view->render('reservation/form', [
            'title'               => 'Nouvelle réservation',
            'salles'              => $this->salles->all(),
            'salleIdSelectionnee' => $salleId,
            'old'                 => $old,
            'errors'              => [],
            'csrf_token'          => $this->csrf?->getToken() ?? '',
        ]);
    }

    public function store(): string
    {
        $data = $_POST;

        $result = $this->validator->validate($data);

        if (!$result->isValid()) {
            return $this->view->render('reservation/form', [
                'title'               => 'Nouvelle réservation',
                'salles'              => $this->salles->all(),
                'salleIdSelectionnee' => (int)($data['salle_id'] ?? 0),
                'old'                 => $data,
                'errors'              => $result->errors(),
                'csrf_token'          => $this->csrf?->getToken() ?? '',
            ]);
        }

        try {
            $dto = CreerReservationDTO::builder()->fromArray($result->validated())->build();
        } catch (Throwable $e) {
            return $this->view->render('reservation/form', [
                'title'               => 'Nouvelle réservation',
                'salles'              => $this->salles->all(),
                'salleIdSelectionnee' => (int)($data['salle_id'] ?? 0),
                'old'                 => $data,
                'errors'              => ['date_debut' => $e->getMessage()],
                'csrf_token'          => $this->csrf?->getToken() ?? '',
            ]);
        }

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
                'csrf_token'          => $this->csrf?->getToken() ?? '',
            ]);
        }

        $this->view->setFlash('success', 'Réservation confirmée avec succès.');
        header('Location: /reservations/' . $reservation->id);
        if (!defined('PHPUNIT_RUNNING')) {
            exit;
        }
        return '';
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
        if (!defined('PHPUNIT_RUNNING')) {
            exit;
        }
    }
}
