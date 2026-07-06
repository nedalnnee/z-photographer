<?php
declare(strict_types=1);

namespace Core;

final class View
{
    public static function render(string $template, array $vars = []): string
    {
        $content = '';
        $title = $vars['title'] ?? 'Photograph';
        $lang = $vars['lang'] ?? 'en';
        $dir = $vars['dir'] ?? 'ltr';

        extract($vars, EXTR_SKIP);

        ob_start();
        require $template;
        return (string)ob_get_clean();
    }
}

