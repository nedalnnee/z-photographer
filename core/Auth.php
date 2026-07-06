<?php
declare(strict_types=1);

namespace Core;

use Core\Db;
use PDO;

final class Auth
{
    public static function user(): ?array
    {
        if (empty($_SESSION['user'])) return null;
        return is_array($_SESSION['user']) ? $_SESSION['user'] : null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function requireRole(string $role): void
    {
        $u = self::user();
        if (!$u || ($u['role'] ?? '') !== $role) {
            http_response_code(403);
            exit('Forbidden');
        }
    }

    public static function login(string $email, string $password): bool
    {
        $pdo = Db::pdo();
        $stmt = $pdo->prepare('SELECT id, email, password_hash, full_name, role, status FROM users WHERE email = :email AND status = :status LIMIT 1');
        $stmt->execute([':email' => $email, ':status' => 'active']);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$u) return false;

        if (!password_verify($password, (string)$u['password_hash'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (string)$u['id'],
            'email' => (string)$u['email'],
            'full_name' => (string)$u['full_name'],
            'role' => (string)$u['role'],
        ];
        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}

