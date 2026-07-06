<?php
declare(strict_types=1);

namespace Core;

final class Response
{
    public static function json(array $data, int $status = 200): string
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public static function html(string $html, int $status = 200): string
    {
        http_response_code($status);
        header('Content-Type: text/html; charset=utf-8');
        return $html;
    }
}

