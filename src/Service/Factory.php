<?php declare(strict_types=1);

namespace App\Service;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\PageController;
use App\Controller\Admin\UserController;
use App\Controller\PublicController;
use App\Repository\PageRepository;
use App\Repository\UserRepository;
use App\Service\Database\MariaDb;
use PDO;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Factory
{
    private function __construct()
    {
    }

    public static function createRouter(): Router
    {
        return new Router(
            new DashboardController(self::createUserRepository(), self::createPageRepository(), self::createConfig(), self::createTwig()),
            new UserController(self::createUserRepository(), self::createTwig()),
            new PageController(self::createPageRepository(), self::createUserRepository(), self::createTwig()),
            new PublicController(self::createPageRepository(), self::createTwig())
        );
    }

    public static function createTwig() : Environment
    {
        $twig = new Environment(new FilesystemLoader(__DIR__ . '/../View/'));
        $twig->addGlobal('adminMenu', DashboardController::ADMIN_MENU);
        $twig->addGlobal('menu', PublicController::MENU);

        return $twig;
    }

    private static function createDatabase(): MariaDb
    {
        return new MariaDb(self::createPDO());
    }

    private static function createUserRepository(): UserRepository
    {
        return new UserRepository(self::createDatabase());
    }

    private static function createPageRepository(): PageRepository
    {
        return new PageRepository(self::createDatabase());
    }

    private static function createPDO(): PDO
    {
        $config = self::createConfig();

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
}