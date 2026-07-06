<?php
declare(strict_types=1);

namespace App\Controllers;

final class AboutController
{
    public function index(): string
    {
        $view = __DIR__ . '/../views/about.php';
        $title = 'About';

        ob_start();
        require $view;
        return (string) ob_get_clean();
    }
}

