<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Db;
use Core\Response;

final class PortfolioController
{
    public function index(array $params = []): string
    {
        $pdo = Db::pdo();
        $categories = $pdo->query("SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC")->fetchAll();
        $albums = $pdo->query("SELECT * FROM albums WHERE status = 'active' ORDER BY sort_order ASC")->fetchAll();

        $view = __DIR__ . '/../views/portfolio.php';
        ob_start();
        require $view;
        return (string)ob_get_clean();
    }

    public function ajax(array $params = []): string
    {
        $page = max(1, (int)($params['page'] ?? ($_GET['page'] ?? 1)));
        $limit = 24;
        $offset = ($page - 1) * $limit;

        $cat = $_GET['cat'] ?? '';
        $alb = $_GET['alb'] ?? '';
        $q = $_GET['q'] ?? '';

        $sql = "SELECT p.id, p.slug, p.file_basename, p.sort_order 
                FROM photos p 
                LEFT JOIN albums a ON a.id = p.album_id 
                LEFT JOIN categories c ON c.id = a.category_id 
                WHERE p.status = 'active'";
        
        $whereParams = [];
        if ($cat !== '') {
            $sql .= " AND c.slug = :cat";
            $whereParams[':cat'] = $cat;
        }
        if ($alb !== '') {
            $sql .= " AND a.slug = :alb";
            $whereParams[':alb'] = $alb;
        }
        if ($q !== '') {
            $sql .= " AND p.slug LIKE :q";
            $whereParams[':q'] = '%' . $q . '%';
        }

        $sql .= " ORDER BY p.sort_order DESC, p.id DESC LIMIT :limit OFFSET :offset";

        $pdo = Db::pdo();
        $stmt = $pdo->prepare($sql);
        
        foreach ($whereParams as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $items = $stmt->fetchAll();

        return Response::json(['items' => $items, 'page' => $page]);
    }
}
