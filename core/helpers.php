<?php
declare(strict_types=1);

use Core\Lang;

if (!function_exists('t')) {
    /** Shorthand translation helper for use inside views. */
    function t(string $key, array $replace = []): string
    {
        return Lang::get($key, $replace);
    }
}
