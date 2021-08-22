<?php declare(strict_types=1);

namespace App\Service;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\PageController;
use App\Controller\Admin\UserController;
use App\Controller\PublicController;
use App\Exception\AuthenticationExceptionInterface;
use App\Repository\PageRepository;
use App\Repository\UserRepository;
use App\Service\Database\MariaDbService;
use App\Service\FileLoader\JsonFileLoader;
use App\ValueObject\RouteList;
use PDO;
use Twig\Environment;
use Twig\Extra\String\StringExtension;
use Twig\Loader\FilesystemLoader;
use function var_dump;

/**
 * @codeCoverageIgnore
 */
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
                $this->createAuthenticationService(),
                $this->createSessionService(),
                $this->createLoginService()
            ),
            new UserController(
                $this->createUserRepository(),
                $this->createTwig(),
                $this->createAuthenticationService(),
                $this->createSessionService(),
                $this->createLoginService()
            ),
            new PageController(
                $this->createPageRepository(),
                $this->createTwig(),
                $this->createSessionService(),
                $this->createLoginService()
            ),
            new PublicController(
                $this->createPageRepository(),
                $this->createTwig()
            ),
            $this->createSessionService(),
            $this->createConfig(),
            $this->createTwig(),
            RouteList::createFromArray((new JsonFileLoader(__DIR__ . '/../../config/routes.json'))->getData())
        );
    }

    public function createTwig(): Environment
    {
        try {
            $user = $this->createAuthenticationService()->findAuthenticatedUser($this->request);
        } catch (AuthenticationExceptionInterface $e) {
            $user = null;
        }

        $twig = new Environment(new FilesystemLoader(__DIR__ . '/../View/'));
        $twig->addGlobal('adminMenu', DashboardController::ADMIN_MENU);
        $twig->addGlobal('userMenu', PublicController::MENU);
        $twig->addGlobal('request', $this->request);
        $twig->addGlobal('flashes', $this->createSessionService()->getFlashes());
        $twig->addGlobal('user', $user);
        $twig->addExtension(new StringExtension());

        return $twig;
    }

    private function createAuthenticationService(): AuthenticationService
    {
        return new AuthenticationService($this->createConfig(), $this->createUserRepository(), $this->createDateTimeService());
    }

    private function createDatabase(): MariaDbService
    {
        return new MariaDbService($this->createPDO());
    }

    private function createDateTimeService(): DateTimeService
    {
        return new DateTimeService();
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
                PDO::ATTR_STRINGIFY_FETCHES  => false,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }

    private static function createConfig(): Config
    {
        return Config::createFromArray((new JsonFileLoader(__DIR__ . '/../../config/general.json'))->getData());
    }

    function createSessionService(): Session
    {
        return new Session($_SESSION);
    }

    private function createLoginService(): LoginService
    {
        return new LoginService($this->createAuthenticationService(), $this->createUserRepository());
    }
}