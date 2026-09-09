<?php

declare(strict_types=1);

namespace Tests\Http;

use App\Application;
use App\Controller\Api\ApiDashboardController;
use App\Controller\Api\ApiReservationController;
use App\Controller\Api\ApiSalleController;
use App\Controller\AuthController;
use App\Controller\DashboardController;
use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\LoggingMiddleware;
use App\Model\Salle;
use App\Model\User;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\AuthService;
use App\Service\CreerReservationService;
use App\Service\CsrfService;
use App\Service\LoggerService;
use App\Service\StatistiquesService;
use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;
use App\View\ViewRenderer;
use DI\Container;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use PHPUnit\Framework\TestCase;
use Tests\Double\InMemoryReservationRepository;
use Tests\Double\InMemorySalleRepository;

abstract class HttpTestCase extends TestCase
{
    protected Container $container;
    protected Application $app;
    protected InMemorySalleRepository $salleRepo;
    protected InMemoryReservationRepository $reservationRepo;
    protected UserRepositoryInterface $userRepo;
    protected CsrfService $csrf;
    protected AuthService $auth;

    protected function setUp(): void
    {
        $_SESSION = [];
        $_GET = [];
        $_POST = [];
        $_SERVER = [];

        $this->salleRepo = new InMemorySalleRepository();
        $this->reservationRepo = new InMemoryReservationRepository();

        $this->salleRepo->save(new Salle([
            'id'       => 1,
            'nom'      => 'Salle 101',
            'batiment' => 'Batiment A',
            'capacite' => 40,
            'type'     => 'cours',
            'active'   => true,
        ]));

        $adminUser = new User([
            'nom'          => 'Admin Test',
            'email'        => 'admin@univ.sn',
            'mot_de_passe' => password_hash('admin123', PASSWORD_BCRYPT),
            'role'         => 'admin',
        ]);
        $adminUser->id = 1;

        $profUser = new User([
            'nom'          => 'Prof Test',
            'email'        => 'prof@univ.sn',
            'mot_de_passe' => password_hash('prof123', PASSWORD_BCRYPT),
            'role'         => 'responsable',
        ]);
        $profUser->id = 2;

        $this->userRepo = new class($adminUser, $profUser) implements UserRepositoryInterface {
            private array $users;

            public function __construct(User ...$users)
            {
                foreach ($users as $u) {
                    $this->users[$u->id] = $u;
                }
            }

            public function findByEmail(string $email): ?User
            {
                foreach ($this->users as $u) {
                    if ($u->email === $email) {
                        return $u;
                    }
                }
                return null;
            }

            public function findById(int $id): ?User
            {
                return $this->users[$id] ?? null;
            }

            public function save(User $user): bool
            {
                $this->users[$user->id] = $user;
                return true;
            }
        };

        $this->csrf = new CsrfService();
        $this->auth = new AuthService($this->userRepo);
        $logger = new LoggerService(sys_get_temp_dir() . '/test_app.log');

        $view = new ViewRenderer();
        $salleValidator = new SalleValidator();
        $resValidator = new ReservationValidator();
        $creerResService = new CreerReservationService($this->salleRepo, $this->reservationRepo, $logger);
        $annulerResService = new AnnulerReservationService($this->reservationRepo, $logger);
        $statsService = new StatistiquesService($this->salleRepo, $this->reservationRepo);

        $salleCtrl = new SalleController($this->salleRepo, $salleValidator, $view, $this->csrf, $this->auth);
        $resCtrl = new ReservationController($this->reservationRepo, $this->salleRepo, $resValidator, $creerResService, $annulerResService, $view, $this->csrf, $this->auth);
        $authCtrl = new AuthController($this->auth, $this->csrf, $view);
        $dashCtrl = new DashboardController($statsService, $this->auth, $view);

        $apiSalleCtrl = new ApiSalleController($this->salleRepo, $salleValidator);
        $apiResCtrl = new ApiReservationController($this->reservationRepo, $resValidator, $creerResService, $annulerResService);
        $apiDashCtrl = new ApiDashboardController($statsService);

        $routesCallback = require dirname(__DIR__, 2) . '/routes/web.php';
        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) use ($routesCallback): void {
            $routesCallback($r);
        });

        $this->container = new Container();
        $this->container->set(SalleController::class, $salleCtrl);
        $this->container->set(ReservationController::class, $resCtrl);
        $this->container->set(AuthController::class, $authCtrl);
        $this->container->set(DashboardController::class, $dashCtrl);
        $this->container->set(ApiSalleController::class, $apiSalleCtrl);
        $this->container->set(ApiReservationController::class, $apiResCtrl);
        $this->container->set(ApiDashboardController::class, $apiDashCtrl);

        $middlewares = [
            new LoggingMiddleware($logger),
            new CsrfMiddleware($this->csrf, $view),
            new AuthMiddleware($this->auth, $view),
        ];

        $this->app = new Application($dispatcher, $this->container, $view, $middlewares);
    }

    protected function request(string $method, string $uri, array $post = [], array $server = []): array
    {
        $_SERVER = array_merge([
            'REQUEST_METHOD' => strtoupper($method),
            'REQUEST_URI'    => $uri,
            'REMOTE_ADDR'    => '127.0.0.1',
        ], $server);
        $parsed = parse_url($uri);
        parse_str($parsed['query'] ?? '', $query);
        $_GET = $query;
        $_POST = $post;
        $path = $parsed['path'] ?? '/';

        http_response_code(200);

        ob_start();
        $response = $this->app->handle(strtoupper($method), $path);
        $output = ob_get_clean();

        $body = is_string($response) ? $response : (string)$output;
        $status = http_response_code();

        return [
            'status' => $status,
            'body'   => $body,
        ];
    }
}
