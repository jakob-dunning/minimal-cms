<?php declare(strict_types=1);

namespace App\Service;

use PDO;

class Factory
{
    private function __construct()
    {
    }

    public static function createRouter(): Router
    {
        return new Router(self::createDatabase());
    }

    public static function createDatabase(): Database
    {
        return new Database(self::createPDO());
    }

    private static function createPDO(): PDO
    {
        $config = self::createConfig();

        return new PDO(
            $config->getByKey('dsn'),
            null,
            null,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    }

    private static function createConfig(): Config
    {
        return new Config(__DIR__ . '/../config/general.json');
    }
}