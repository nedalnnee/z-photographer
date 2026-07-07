<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Db;

final class HomeController
{
    public function index(): string
    {
        $pdo = Db::pdo();
        $categories = $pdo->query("SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC")->fetchAll();
        $glimpsePhotos = $pdo->query("SELECT file_basename, slug FROM photos WHERE status = 'active' ORDER BY id DESC LIMIT 6")->fetchAll();

        ob_start();
        require __DIR__ . '/../views/home.php';
        $content = ob_get_clean();

        $title = 'Home - Photography Portfolio';

        ob_start();
        require __DIR__ . '/../views/layouts/base.php';
        return (string)ob_get_clean();
    }
}
