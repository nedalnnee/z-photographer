<?php
declare(strict_types=1);

namespace Core;

final class Security
{
    public static function startSession(array $appCfg = []): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) return;

        $s = $appCfg['session'] ?? [];
        $cookieSecure = (bool)($s['cookie_secure'] ?? false);
        $cookieHttpOnly = (bool)($s['cookie_httponly'] ?? true);
        $cookieSameSite = (string)($s['cookie_samesite'] ?? 'Lax');

        // PHP expects SameSite values in capitalized form for modern versions
        if (PHP_VERSION_ID >= 70300) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $cookieSecure,
                'httponly' => $cookieHttpOnly,
                'samesite' => $cookieSameSite,
            ]);
        }

        session_start();
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return (string)$_SESSION['csrf_token'];
    }

    public static function requireCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        $expected = $_SESSION['csrf_token'] ?? '';
        if (!is_string($token) || !$expected || !hash_equals($expected, (string)$token)) {
            http_response_code(419);
            exit('CSRF token mismatch');
        }
    }

    public static function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

