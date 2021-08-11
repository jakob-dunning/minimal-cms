<?php declare(strict_types=1);

namespace App\Service;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\PageController;
use App\Controller\Admin\UserController;
use App\Controller\PublicController;
use App\Model\Request;
use App\Repository\PageRepository;
use App\Repository\UserRepository;
use App\Service\Database\MariaDbService;
use PDO;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Factory
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function createRouter(): Router
    {
        return new Router(
            new DashboardController(
                $this->createUserRepository(),
                $this->createPageRepository(),
                $this->createConfig(),
                $this->createTwig(),
                $this->createAuthentication(),
                $this->createSessionService()
            ),
            new UserController(
                $this->createUserRepository(),
                $this->createTwig(),
                $this->createAuthentication(),
                $this->createSessionService()
            ),
            new PageController(
                $this->createPageRepository(),
                $this->createUserRepository(),
                $this->createTwig(),
                $this->createAuthentication(),
                $this->createSessionService()
            ),
            new PublicController(
                $this->createPageRepository(),
                $this->createTwig()
            ),
            $this->createSessionService()
        );
    }

    public function createTwig(): Environment
    {
        $twig = new Environment(new FilesystemLoader(__DIR__ . '/../View/'));
        $twig->addGlobal('adminMenu', DashboardController::ADMIN_MENU);
        $twig->addGlobal('menu', PublicController::MENU);
        $twig->addGlobal('request', $this->request);
        $twig->addGlobal('sessionService', $this->createSessionService());

        return $twig;
    }

    private function createAuthentication(): AuthenticationService
    {
        return new AuthenticationService($this->createConfig(), $this->createUserRepository());
    }

    private function createDatabase(): MariaDbService
    {
        return new MariaDbService($this->createPDO());
    }

    private function createUserRepository(): UserRepository
    {
        return new UserRepository($this->createDatabase());
    }

    private function createPageRepository(): PageRepository
    {
        return new PageRepository($this->createDatabase());
    }

    private function createPDO(): PDO
    {
        $config = $this->createConfig();

        return new PDO(
            $config->getByKey('dsn'),
            null,
            null,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    private static function createConfig(): Config
    {
        return new Config(__DIR__ . '/../../config/general.json');
    }

    function createSessionService(): SessionService
    {
        return new SessionService();
    }
}