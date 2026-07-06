<?php
declare(strict_types=1);

namespace Core;

use PDO;

final class Db
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo) return self::$pdo;

        $cfg = require __DIR__ . '/../config/db.php';

        $host = $cfg['host'] ?? '127.0.0.1';
        $port = (int)($cfg['port'] ?? 3306);
        $name = $cfg['name'] ?? 'photograph';
        $user = $cfg['user'] ?? 'root';
        $pass = $cfg['pass'] ?? '';
        $charset = $cfg['charset'] ?? 'utf8mb4';

        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $name, $charset);

        self::$pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return self::$pdo;
    }
}

