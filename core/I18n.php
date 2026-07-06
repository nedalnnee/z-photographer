<?php
declare(strict_types=1);

namespace Core;

use Core\Db;

final class I18n
{
    public static function currentLanguage(): array
    {
        $appCfg = require __DIR__ . '/../config/app.php';
        $default = (string)($appCfg['default_language'] ?? 'en');

        $langCode = $_GET['lang'] ?? ($_SESSION['lang'] ?? $default);
        if (!is_string($langCode) || $langCode === '') $langCode = $default;

        $pdo = Db::pdo();
        $stmt = $pdo->prepare('SELECT id, code, direction FROM languages WHERE code = :code AND status = :status LIMIT 1');
        $stmt->execute([':code' => $langCode, ':status' => 'active']);
        $lang = $stmt->fetch();

        if (!$lang) {
            $stmt = $pdo->prepare('SELECT id, code, direction FROM languages WHERE code = :code AND status = :status LIMIT 1');
            $stmt->execute([':code' => $default, ':status' => 'active']);
            $lang = $stmt->fetch();
        }

        $_SESSION['lang'] = $lang['code'] ?? $default;

        return [
            'code' => (string)($lang['code'] ?? $default),
            'direction' => (string)($lang['direction'] ?? 'ltr'),
            'id' => (string)($lang['id'] ?? '0'),
        ];
    }

    public static function t(string $key, string $fallback = ''): string
    {
        $pdo = Db::pdo();
        $lang = self::currentLanguage();

        $stmt = $pdo->prepare('SELECT value FROM translations WHERE language_id = :lang_id AND key_name = :key_name LIMIT 1');
        $stmt->execute([':lang_id' => (int)$lang['id'], ':key_name' => $key]);
        $row = $stmt->fetch();

        return (string)($row['value'] ?? ($fallback !== '' ? $fallback : $key));
    }
}

