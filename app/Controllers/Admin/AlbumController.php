<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Auth;
use Core\Db;
use Core\Security;

final class AlbumController
{
    public function index(): string
    {
        Auth::requireRole('admin');
        $pdo = Db::pdo();
        // جلب الألبومات مع اسم التصنيف المرتبط بها لعرضه في القائمة
        $albums = $pdo->query("SELECT a.id, a.slug, a.status, c.slug as category_slug 
                               FROM albums a 
                               LEFT JOIN categories c ON c.id = a.category_id 
                               WHERE a.status != 'deleted' ORDER BY a.id DESC")->fetchAll();

        ob_start();
        $content = '<div class="space-y-6">'
            . '<div class="flex items-center justify-between">'
            . '<div><h1 class="text-4xl font-black text-base-content">Manage <span class="text-rose-500">Albums</span></h1>'
            . '<p class="text-base-content/60">Group your photos into storytelling collections.</p></div>'
            . '<a href="?r=/admin/albums/create" class="btn bg-rose-500 text-white border-none rounded-xl shadow-lg">Create New Album</a>'
            . '</div>'
            . '<div class="grid grid-cols-1 md:grid-cols-3 gap-6">';

        if (empty($albums)) {
            $content .= '<div class="glass-card p-10 rounded-3xl col-span-full text-center text-base-content/40 italic">No albums created yet.</div>';
        } else {
            foreach ($albums as $album) {
                $catInfo = $album['category_slug'] ? '<span class="badge badge-sm badge-outline opacity-50 ml-2">' . Security::e($album['category_slug']) . '</span>' : '';
                $content .= '<div class="glass-card p-6 rounded-3xl photo-hover-effect">'
                    . '<h3 class="font-bold text-xl text-rose-500">' . Security::e($album['slug']) . $catInfo . '</h3>'
                    . '<div class="mt-4 flex gap-2">'
                    . '<a href="?r=/admin/albums/edit&id=' . $album['id'] . '" class="btn btn-sm btn-ghost text-rose-400">Edit</a>'
                    . '<form method="POST" action="?r=/admin/albums/delete" onsubmit="return confirm(\'Delete this album?\')">'
                    . '<input type="hidden" name="csrf_token" value="' . Security::csrfToken() . '">'
                    . '<input type="hidden" name="id" value="' . $album['id'] . '">'
                    . '<button class="btn btn-sm btn-ghost text-error">Delete</button></form>'
                    . '</div>'
                    . '</div>';
            }
        }

        $content .= '</div></div>';

        $title = 'Manage Albums';
        $lang = $_SESSION['lang'] ?? 'en';
        $dir = $lang === 'ar' ? 'rtl' : 'ltr';
        require __DIR__ . '/../../views/layouts/base.php';
        return (string)ob_get_clean();
    }

    public function create(): string
    {
        Auth::requireRole('admin');
        $pdo = Db::pdo();
        // جلب التصنيفات النشطة لعرضها في قائمة منسدلة
        $categories = $pdo->query("SELECT id, slug FROM categories WHERE status = 'active' ORDER BY slug ASC")->fetchAll();
        
        $categoryOptions = '<option value="">Standalone Album (No Category)</option>';
        foreach ($categories as $cat) {
            $categoryOptions .= '<option value="' . $cat['id'] . '">' . Security::e($cat['slug']) . '</option>';
        }

        $csrf = Security::csrfToken();
        ob_start();
        $content = '<div class="max-w-2xl mx-auto py-10">'
            . '<div class="text-center mb-10"><h1 class="text-4xl font-black text-rose-500">New Album</h1></div>'
            . '<div class="glass-card rounded-[2.5rem] p-10">'
            . '<form method="POST" action="?r=/admin/albums/store" class="space-y-6">'
            . "<input type=\"hidden\" name=\"csrf_token\" value=\"{$csrf}\">"
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">Parent Category</span></label>'
            . '<select name="category_id" class="select select-bordered border-rose-100 rounded-xl">' . $categoryOptions . '</select></div>'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">Slug</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" name="slug" placeholder="e.g. summer-2026" required></div>'
            . '<button class="btn bg-rose-500 text-white border-none rounded-xl w-full h-14">Save Album</button>'
            . '</form></div></div>';

        $title = 'Create Album';
        require __DIR__ . '/../../views/layouts/base.php';
        return (string)ob_get_clean();
    }

    public function store(): void
    {
        Auth::requireRole('admin');
        Security::requireCsrf();
        $slug = trim($_POST['slug'] ?? '');
        $categoryId = $_POST['category_id'] !== '' ? (int)$_POST['category_id'] : null;

        if ($slug) {
            $pdo = Db::pdo();
            $stmt = $pdo->prepare("INSERT INTO albums (slug, category_id, status) VALUES (?, ?, 'active')");
            $stmt->execute([$slug, $categoryId]);
            $_SESSION['flash']['success'] = 'Album created.';
        }
        header('Location: ?r=/admin/albums');
        exit;
    }

    public function delete(): void
    {
        Auth::requireRole('admin');
        Security::requireCsrf();
        $id = $_POST['id'] ?? '';
        if ($id) {
            $pdo = Db::pdo();
            $pdo->prepare("UPDATE albums SET status = 'deleted' WHERE id = ?")->execute([$id]);
            $_SESSION['flash']['success'] = 'Album deleted.';
        }
        header('Location: ?r=/admin/albums');
        exit;
    }
}