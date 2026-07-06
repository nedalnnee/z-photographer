<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Auth;
use Core\Db;
use Core\Security;
use PDO;

final class CategoryController
{
    public function index(): string
    {
        Auth::requireRole('admin');

        $pdo = Db::pdo();
        $stmt = $pdo->query("SELECT * FROM categories WHERE status != 'deleted' ORDER BY sort_order ASC, id DESC");
        $categories = $stmt->fetchAll();
        
        ob_start();
        $content = '<div class="space-y-6">'
            . '<div class="flex items-center justify-between">'
            . '<div><h1 class="text-4xl font-black tracking-tight text-base-content">Manage <span class="text-rose-500">Categories</span></h1>'
            . '<p class="text-base-content/60">Organize your portfolio sections.</p></div>'
            . '<a href="?r=/admin/categories/create" class="btn bg-rose-500 text-white border-none rounded-xl shadow-lg shadow-rose-200">Add New Category</a>'
            . '</div>'
            . '<div class="glass-card rounded-[2.5rem] overflow-hidden shadow-xl">'
            . '<div class="overflow-x-auto"><table class="table w-full">'
            . '<thead class="bg-rose-50/50"><tr class="text-rose-600 border-b border-rose-100 uppercase text-xs"><th>Order</th><th>Slug</th><th>Status</th><th class="text-right px-8">Actions</th></tr></thead>'
            . '<tbody>';

        if (empty($categories)) {
            $content .= '<tr><td colspan="4" class="text-center py-10 text-base-content/50">No categories found.</td></tr>';
        } else {
            foreach ($categories as $cat) {
                $id = (string)$cat['id'];
                $slug = Security::e((string)$cat['slug']);
                $sort = (int)$cat['sort_order'];
                $status = (string)$cat['status'];
                $badgeClass = $status === 'active' ? 'badge-success' : 'badge-ghost';

                $content .= '<tr class="hover:bg-rose-50/50 transition-colors border-b border-rose-50">'
                    . "<td>{$sort}</td><td class=\"font-bold\">{$slug}</td>"
                    . "<td><span class=\"badge {$badgeClass} badge-outline\">" . ucfirst($status) . "</span></td>"
                    . '<td class="text-right flex justify-end gap-2 px-8 py-4">'
                    . '<a href="?r=/admin/categories/edit&id=' . $id . '" class="btn btn-sm btn-ghost text-rose-500 hover:bg-rose-100">Edit</a>'
                    . '<form method="POST" action="?r=/admin/categories/delete" onsubmit="return confirm(\'Delete this category?\')">'
                    . '<input type="hidden" name="csrf_token" value="' . Security::csrfToken() . '">'
                    . '<input type="hidden" name="id" value="' . $id . '">'
                    . '<button class="btn btn-sm btn-ghost text-error">Delete</button></form>'
                    . '</td></tr>';
            }
        }

        $content .= '</tbody></table></div>'
            . '</div></div>';

        $title = 'Manage Categories';
        $lang = $_SESSION['lang'] ?? 'en';
        $dir = $lang === 'ar' ? 'rtl' : 'ltr';
        require __DIR__ . '/../../views/layouts/base.php';
        return (string)ob_get_clean();
    }

    public function create(): string
    {
        Auth::requireRole('admin');
        $csrf = Security::csrfToken();
        
        ob_start();
        $content = '<div class="max-w-2xl mx-auto py-10">'
            . '<div class="text-center mb-10">'
            . '<h1 class="text-4xl font-black">Create <span class="text-rose-500">Category</span></h1>'
            . '</div>'
            . '<div class="glass-card rounded-[2.5rem] p-10">'
            . '<form method="POST" action="?r=/admin/categories/store" class="space-y-6">'
            . "<input type=\"hidden\" name=\"csrf_token\" value=\"{$csrf}\">"
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">Slug</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" name="slug" placeholder="e.g. weddings" required></div>'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">Sort Order</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" type="number" name="sort_order" value="0"></div>'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">Status</span></label>'
            . '<select class="select select-bordered border-rose-100 rounded-xl" name="status"><option value="active">Active</option><option value="hidden">Hidden</option></select></div>'
            . '<div class="flex gap-4 pt-4">'
            . '<button class="btn border-none bg-gradient-to-r from-rose-400 to-pink-500 text-white flex-1 rounded-2xl h-14" type="submit">Save Category</button>'
            . '<a href="?r=/admin/categories" class="btn btn-ghost rounded-2xl h-14">Cancel</a>'
            . '</div>'
            . '</form></div></div>';

        $title = 'Create Category';
        $lang = $_SESSION['lang'] ?? 'en';
        $dir = $lang === 'ar' ? 'rtl' : 'ltr';
        require __DIR__ . '/../../views/layouts/base.php';
        return (string)ob_get_clean();
    }
    
    public function edit(): string
    {
        Auth::requireRole('admin');
        $id = $_GET['id'] ?? '';

        $pdo = Db::pdo();
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ? AND status != 'deleted' LIMIT 1");
        $stmt->execute([$id]);
        $category = $stmt->fetch();

        if (!$category) {
            $_SESSION['flash']['error'] = 'Category not found.';
            header('Location: ?r=/admin/categories');
            exit;
        }

        $csrf = Security::csrfToken();
        $id = Security::e((string)$id);
        $slug = Security::e((string)$category['slug']);
        $sort = (int)$category['sort_order'];
        $status = (string)$category['status'];
        
        ob_start();
        $content = '<div class="max-w-2xl mx-auto py-10">'
            . '<div class="text-center mb-10"><h1 class="text-4xl font-black text-rose-500">Edit Category</h1></div>'
            . '<div class="glass-card rounded-[2.5rem] p-10">'
            . '<form method="POST" action="?r=/admin/categories/update" class="space-y-6">'
            . "<input type=\"hidden\" name=\"csrf_token\" value=\"{$csrf}\">"
            . "<input type=\"hidden\" name=\"id\" value=\"{$id}\">"
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">Slug</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" name="slug" value="' . $slug . '" required></div>'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">Sort Order</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" type="number" name="sort_order" value="' . $sort . '"></div>'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">Status</span></label>'
            . '<select class="select select-bordered border-rose-100 rounded-xl" name="status">'
            . '<option value="active"' . ($status === 'active' ? ' selected' : '') . '>Active</option>'
            . '<option value="hidden"' . ($status === 'hidden' ? ' selected' : '') . '>Hidden</option>'
            . '</select></div>'
            . '<div class="flex gap-4 pt-4">'
            . '<button class="btn border-none bg-gradient-to-r from-rose-400 to-pink-500 text-white flex-1 rounded-2xl h-14" type="submit">Update Category</button>'
            . '<a href="?r=/admin/categories" class="btn btn-ghost rounded-2xl h-14">Cancel</a>'
            . '</div>'
            . '</form></div></div>';

        $title = 'Edit Category';
        $lang = $_SESSION['lang'] ?? 'en';
        $dir = $lang === 'ar' ? 'rtl' : 'ltr';
        require __DIR__ . '/../../views/layouts/base.php';
        return (string)ob_get_clean();
    }

    public function store(): void
    {
        Auth::requireRole('admin');
        Security::requireCsrf();

        $slug = trim((string)($_POST['slug'] ?? ''));
        $sort = (int)($_POST['sort_order'] ?? 0);
        $status = $_POST['status'] ?? 'active';

        $pdo = Db::pdo();
        // نستخدم slug كاسم عرض في حال عدم وجود عمود name
        $stmt = $pdo->prepare("INSERT INTO categories (slug, sort_order, status) VALUES (?, ?, ?)");
        $stmt->execute([$slug, $sort, $status]);

        $_SESSION['flash']['success'] = 'Category created successfully.';
        header('Location: ?r=/admin/categories');
        exit;
    }

    public function update(): void
    {
        Auth::requireRole('admin');
        Security::requireCsrf();

        $id = $_POST['id'] ?? '';
        $slug = trim((string)($_POST['slug'] ?? ''));
        $sort = (int)($_POST['sort_order'] ?? 0);
        $status = $_POST['status'] ?? 'active';

        $pdo = Db::pdo();
        $stmt = $pdo->prepare("UPDATE categories SET slug = ?, sort_order = ?, status = ? WHERE id = ?");
        $stmt->execute([$slug, $sort, $status, $id]);

        $_SESSION['flash']['success'] = 'Category updated successfully.';
        header('Location: ?r=/admin/categories');
        exit;
    }

    public function delete(): void
    {
        Auth::requireRole('admin');
        Security::requireCsrf();
        $id = $_POST['id'] ?? '';
        if ($id) {
            $pdo = Db::pdo();
            $pdo->prepare("UPDATE categories SET status = 'deleted' WHERE id = ?")->execute([$id]);
            $_SESSION['flash']['success'] = 'Category deleted.';
        } else {
            $_SESSION['flash']['error'] = 'Invalid category ID.';
        }
        header('Location: ?r=/admin/categories');
        exit;
    }
}