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

class ReservationController extends AbstractController
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservations,
        private readonly SalleRepositoryInterface $salles,
        private readonly ReservationValidator $validator,
        private readonly CreerReservationService $creerReservationService,
        private readonly AnnulerReservationService $annulerReservationService,
        ViewRenderer $view,
        private readonly CsrfService $csrf,
        private readonly AuthService $auth
    ) {
        parent::__construct($view);
    }

    public function index(): string
    {
        $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
        $perPage = 8;

        $criteres = [
            'salle_id' => isset($_GET['salle_id']) && is_numeric($_GET['salle_id']) ? (int)$_GET['salle_id'] : '',
            'statut'   => isset($_GET['statut']) ? trim((string)$_GET['statut']) : '',
            'q'        => isset($_GET['q']) ? trim((string)$_GET['q']) : '',
            'date'     => isset($_GET['date']) ? trim((string)$_GET['date']) : '',
        ];

        $paginator = $this->reservations->search($criteres, $page, $perPage);

        $queryParams = array_filter($criteres, fn($v) => $v !== '' && $v !== null);
        $paginator->appends($queryParams);

        return $this->render('reservation/index', [
            'title'               => 'Liste des réservations',
            'reservations'        => $paginator->items(),
            'paginator'           => $paginator,
            'criteres'            => $criteres,
            'queryParams'         => $queryParams,
            'baseUrl'             => '/reservations',
            'salles'              => $this->salles->all(),
            'salleIdSelectionnee' => $criteres['salle_id'] !== '' ? (int)$criteres['salle_id'] : null,
            'csrf_token'          => $this->csrf->getToken(),
            'isAdmin'             => $this->auth->isAdmin(),
        ]);
    }

    public function show(int $id): string
    {
        $reservation = $this->reservations->findById($id);
        if ($reservation === null) {
            http_response_code(404);
            return $this->render('error/404', ['title' => 'Réservation introuvable']);
        }

        $currentUser = $this->auth->user();
        $canCancel = $this->auth->isAdmin() || ($currentUser !== null && ($reservation->email === $currentUser->email || $reservation->responsable === $currentUser->nom));

        return $this->render('reservation/show', [
            'title'       => 'Réservation #' . $reservation->id,
            'reservation' => $reservation,
            'csrf_token'  => $this->csrf->getToken(),
            'isAdmin'     => $this->auth->isAdmin(),
            'currentUser' => $currentUser,
            'canCancel'   => $canCancel,
        ]);
    }

    public function create(): string
    {
        $salleId = isset($_GET['salle_id']) && is_numeric($_GET['salle_id']) ? (int)$_GET['salle_id'] : null;
        $currentUser = $this->auth->user();

        $old = $salleId ? ['salle_id' => $salleId] : [];
        if ($currentUser !== null) {
            $old['responsable'] = $currentUser->nom;
            $old['email'] = $currentUser->email;
        }

        return $this->render('reservation/form', [
            'title'               => 'Nouvelle réservation',
            'salles'              => $this->salles->all(),
            'salleIdSelectionnee' => $salleId,
            'old'                 => $old,
            'errors'              => [],
            'csrf_token'          => $this->csrf->getToken(),
        ]);
    }

    public function store(): string
    {
        $data = $_POST;
        $currentUser = $this->auth->user();
        if ($currentUser !== null) {
            $data['responsable'] = $currentUser->nom;
            $data['email'] = $currentUser->email;
        }

        $result = $this->validator->validate($data);

        if (!$result->isValid()) {
            return $this->render('reservation/form', [
                'title'               => 'Nouvelle réservation',
                'salles'              => $this->salles->all(),
                'salleIdSelectionnee' => (int)($data['salle_id'] ?? 0),
                'old'                 => $data,
                'errors'              => $result->errors(),
                'csrf_token'          => $this->csrf->getToken(),
            ]);
        }

        try {
            $dto = CreerReservationDTO::builder()->fromArray($result->validated())->build();
        } catch (Throwable $e) {
            return $this->render('reservation/form', [
                'title'               => 'Nouvelle réservation',
                'salles'              => $this->salles->all(),
                'salleIdSelectionnee' => (int)($data['salle_id'] ?? 0),
                'old'                 => $data,
                'errors'              => ['date_debut' => $e->getMessage()],
                'csrf_token'          => $this->csrf->getToken(),
            ]);
        }

        try {
            $reservation = $this->creerReservationService->execute($dto);
        } catch (SalleIndisponibleException $e) {
            $this->flash('error', $e->getMessage());
            return $this->render('reservation/form', [
                'title'               => 'Nouvelle réservation',
                'salles'              => $this->salles->all(),
                'salleIdSelectionnee' => (int)($data['salle_id'] ?? 0),
                'old'                 => $data,
                'errors'              => ['general' => $e->getMessage()],
                'csrf_token'          => $this->csrf->getToken(),
            ]);
        }

        $this->flash('success', 'Réservation confirmée avec succès.');
        return $this->redirect('/reservations/' . $reservation->id, ['reservation' => $reservation], 201);
    }

    public function cancel(int $id): string
    {
        $currentUser = $this->auth->user();
        $reservation = $this->reservations->findById($id);

        if ($reservation === null) {
            return $this->error(404, "La réservation #{$id} est introuvable.");
        }

        if (!$this->auth->isAdmin() && $currentUser !== null && $reservation->email !== $currentUser->email && $reservation->responsable !== $currentUser->nom) {
            http_response_code(403);
            return $this->render('error/403', [
                'title'          => 'Accès Refusé',
                'message'        => 'Vous ne disposez pas des autorisations requises pour annuler la réservation d\'un autre utilisateur.',
                'allowedMethods' => ['Seul le responsable propriétaire ou un administrateur peut annuler ce dossier.'],
            ]);
        }

        try {
            $this->annulerReservationService->execute($id);
            $this->flash('success', "La réservation #{$id} a été annulée avec succès.");
        } catch (ReservationIntrouvableException $e) {
            $this->flash('error', $e->getMessage());
        }

        return $this->redirect('/reservations/' . $id, ['reservation' => $this->reservations->findById($id)]);
    }
}
