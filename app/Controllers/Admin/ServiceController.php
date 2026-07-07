<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Auth;
use Core\Db;
use Core\Security;

final class ServiceController
{
    private function fetchActiveServices(): array
    {
        $pdo = Db::pdo();
        $stmt = $pdo->query("SELECT * FROM services WHERE status IN ('active','hidden') ORDER BY sort_order ASC, id DESC");
        return $stmt->fetchAll();
    }

    private function fetchServiceById(string|int $id): ?array
    {
        $pdo = Db::pdo();
        $stmt = $pdo->prepare('SELECT * FROM services WHERE id = ? LIMIT 1');
        $stmt->execute([(string)$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function index(): string
    {
        Auth::requireRole('admin');

        $services = $this->fetchActiveServices();

        ob_start();
        $content = '<div class="flex items-center justify-between mb-6">'
            . '<div><h1 class="text-4xl font-black tracking-tight text-base-content">' . htmlspecialchars(t('admin.services.title_pre')) . ' <span class="text-rose-500">' . htmlspecialchars(t('admin.services.title_span')) . '</span></h1>'
            . '<p class="text-base-content/60">' . htmlspecialchars(t('admin.services.subtitle')) . '</p></div>'
            . '<a href="?r=/admin/services/create" class="btn bg-rose-500 text-white border-none rounded-xl shadow-lg">' . htmlspecialchars(t('admin.services.new_service')) . '</a>'
            . '</div>';

        $content .= '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';

        if (!$services) {
            $content .= '<div class="glass-card p-8 rounded-[2rem] col-span-full">'
                . '<p class="text-base-content/60">' . htmlspecialchars(t('admin.services.none_found')) . '</p>'
                . '</div>';
        } else {
            foreach ($services as $s) {
                $id = (string)$s['id'];
                $serviceTitle = Security::e((string)$s['title']);
                $pricing = Security::e((string)($s['pricing_text'] ?? ''));
                $status = (string)$s['status'];
                $badge = htmlspecialchars($status === 'active' ? t('admin.services.live') : t('admin.services.hidden'));
                $badgeClass = $status === 'active' ? 'badge-success' : 'badge-ghost';

                $content .= '<div class="glass-card p-8 rounded-[2rem] relative photo-hover-effect">'
                    . '<div class="absolute top-6 right-6 flex gap-2">'
                    . '<a href="?r=/admin/services/edit&id=' . $id . '" class="btn btn-xs btn-ghost text-rose-500 font-bold">' . htmlspecialchars(t('common.edit')) . '</a>'
                    . '<form method="POST" action="?r=/admin/services/delete" onsubmit="return confirm(\'' . htmlspecialchars(t('common.confirm_delete'), ENT_QUOTES) . '\')">'
                    . "<input type=\"hidden\" name=\"csrf_token\" value=\"" . Security::e(Security::csrfToken()) . "\">"
                    . '<input type="hidden" name="id" value="' . $id . '">'
                    . '<button class="btn btn-xs btn-ghost text-error">✕</button></form>'
                    . '</div>'
                    . '<h3 class="text-2xl font-black text-base-content">' . $serviceTitle . '</h3>'
                    . ($pricing !== '' ? '<p class="text-sm text-base-content/60 mt-2 font-medium">' . $pricing . '</p>' : '')
                    . '<div class="mt-6 flex items-center gap-2">'
                    . '<span class="badge ' . $badgeClass . ' badge-outline font-bold text-[10px]">' . $badge . '</span>'
                    . '<span class="text-[10px] uppercase text-base-content/30 font-bold tracking-widest">' . htmlspecialchars(t('admin.services.order_label')) . ': ' . (int)$s['sort_order'] . '</span>'
                    . '</div>'
                    . '</div>';
            }
        }

        $content .= '</div>';

        $title = 'Manage Services';
        require __DIR__ . '/../../views/layouts/base.php';
        return (string)ob_get_clean();
    }

    public function create(): string
    {
        Auth::requireRole('admin');
        $csrf = Security::csrfToken();

        ob_start();
        $content = '<div class="max-w-3xl mx-auto py-10">'
            . '<div class="text-center mb-10">'
            . '<h1 class="text-4xl font-black">' . htmlspecialchars(t('admin.services.create_title_pre')) . ' <span class="text-rose-500">' . htmlspecialchars(t('admin.services.create_title_span')) . '</span></h1>'
            . '</div>'
            . '<div class="glass-card rounded-[2.5rem] p-10">'
            . '<form method="POST" action="?r=/admin/services/store" class="space-y-6">'
            . "<input type=\"hidden\" name=\"csrf_token\" value=\"{$csrf}\">"
            . '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">' . htmlspecialchars(t('admin.services.service_title_label')) . '</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" name="title" required></div>'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">' . htmlspecialchars(t('admin.services.pricing_label')) . '</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" name="pricing_text" placeholder="' . htmlspecialchars(t('admin.services.pricing_placeholder')) . '"></div>'
            . '</div>'
            . '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">' . htmlspecialchars(t('admin.services.sort_order_label')) . '</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" type="number" name="sort_order" value="0"></div>'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">' . htmlspecialchars(t('admin.services.status_label')) . '</span></label>'
            . '<select class="select select-bordered border-rose-100 rounded-xl" name="status">'
            . '<option value="active">' . htmlspecialchars(t('common.active')) . '</option>'
            . '<option value="hidden">' . htmlspecialchars(t('common.hidden')) . '</option>'
            . '</select></div>'
            . '</div>'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">' . htmlspecialchars(t('admin.services.description_label')) . '</span></label>'
            . '<textarea class="textarea textarea-bordered border-rose-100 rounded-xl h-32" name="description"></textarea></div>'
            . '<div class="flex gap-4 pt-4">'
            . '<button class="btn border-none bg-gradient-to-r from-rose-400 to-pink-500 text-white flex-1 rounded-2xl h-14" type="submit">' . htmlspecialchars(t('admin.services.save')) . '</button>'
            . '<a href="?r=/admin/services" class="btn btn-ghost rounded-2xl h-14">' . htmlspecialchars(t('common.cancel')) . '</a>'
            . '</div>'
            . '</form></div></div>';

        $title = 'Create Service';
        require __DIR__ . '/../../views/layouts/base.php';
        return (string)ob_get_clean();
    }

    public function edit(): string
    {
        Auth::requireRole('admin');
        $id = (string)($_GET['id'] ?? '');
        $service = $this->fetchServiceById($id);
        if (!$service) {
            http_response_code(404);
            exit('Service not found');
        }

        $csrf = Security::csrfToken();

        ob_start();
        $idValue = Security::e($id);
        $titleValue = Security::e((string)$service['title']);
        $pricingValue = Security::e((string)($service['pricing_text'] ?? ''));
        $descValue = Security::e((string)($service['description'] ?? ''));
        $sort = (int)$service['sort_order'];
        $status = (string)$service['status'];

        $content = '<div class="max-w-3xl mx-auto py-10">'
            . '<div class="text-center mb-10"><h1 class="text-4xl font-black text-rose-500">' . htmlspecialchars(t('admin.services.edit_title')) . '</h1></div>'
            . '<div class="glass-card rounded-[2.5rem] p-10">'
            . '<form method="POST" action="?r=/admin/services/update" class="space-y-6">'
            . "<input type=\"hidden\" name=\"csrf_token\" value=\"{$csrf}\">"
            . '<input type="hidden" name="id" value="' . $idValue . '">'
            . '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">' . htmlspecialchars(t('admin.services.service_title_label')) . '</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" name="title" value="' . $titleValue . '" required></div>'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">' . htmlspecialchars(t('admin.services.pricing_label')) . '</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" name="pricing_text" value="' . $pricingValue . '" placeholder="' . htmlspecialchars(t('admin.services.pricing_placeholder')) . '"></div>'
            . '</div>'
            . '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">' . htmlspecialchars(t('admin.services.sort_order_label')) . '</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" type="number" name="sort_order" value="' . $sort . '"></div>'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">' . htmlspecialchars(t('admin.services.status_label')) . '</span></label>'
            . '<select class="select select-bordered border-rose-100 rounded-xl" name="status">'
            . '<option value="active"' . ($status === 'active' ? ' selected' : '') . '>' . htmlspecialchars(t('common.active')) . '</option>'
            . '<option value="hidden"' . ($status === 'hidden' ? ' selected' : '') . '>' . htmlspecialchars(t('common.hidden')) . '</option>'
            . '</select></div>'
            . '</div>'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">' . htmlspecialchars(t('admin.services.description_label')) . '</span></label>'
            . '<textarea class="textarea textarea-bordered border-rose-100 rounded-xl h-32" name="description">' . $descValue . '</textarea></div>'
            . '<button class="btn border-none bg-gradient-to-r from-rose-400 to-pink-500 text-white w-full rounded-2xl h-14">' . htmlspecialchars(t('admin.services.update')) . '</button>'
            . '</form></div></div>';

        $title = 'Edit Service';
        require __DIR__ . '/../../views/layouts/base.php';
        return (string)ob_get_clean();
    }

    public function store(): void
    {
        Auth::requireRole('admin');
        Security::requireCsrf();

        $title = trim((string)($_POST['title'] ?? ''));
        $pricingText = trim((string)($_POST['pricing_text'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $status = in_array((string)($_POST['status'] ?? 'active'), ['active', 'hidden'], true) ? (string)$_POST['status'] : 'active';

        if ($title === '') {
            $_SESSION['flash']['error'] = t('flash.service_title_required');
            header('Location: ?r=/admin/services/create');
            exit;
        }

        $slug = strtolower(preg_replace('~[^a-z0-9]+~', '-', $title) ?? '');
        $slug = trim((string)$slug, '-');
        if ($slug === '') $slug = 'service-' . time();

        $pdo = Db::pdo();
        $stmt = $pdo->prepare('INSERT INTO services (slug, sort_order, status, title, description, pricing_text, booking_enabled) VALUES (?, ?, ?, ?, ?, ?, 1)');
        $stmt->execute([$slug, $sortOrder, $status, $title, $description !== '' ? $description : null, $pricingText !== '' ? $pricingText : null]);

        $_SESSION['flash']['success'] = t('flash.service_added');
        header('Location: ?r=/admin/services');
        exit;
    }

    public function update(): void
    {
        Auth::requireRole('admin');
        Security::requireCsrf();

        $id = (string)($_POST['id'] ?? '');
        if ($id === '') {
            $_SESSION['flash']['error'] = t('flash.service_invalid_id');
            header('Location: ?r=/admin/services');
            exit;
        }

        $title = trim((string)($_POST['title'] ?? ''));
        $pricingText = trim((string)($_POST['pricing_text'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $status = in_array((string)($_POST['status'] ?? 'active'), ['active', 'hidden'], true) ? (string)$_POST['status'] : 'active';

        if ($title === '') {
            $_SESSION['flash']['error'] = t('flash.service_title_required');
            header('Location: ?r=/admin/services/edit&id=' . urlencode($id));
            exit;
        }

        $slug = strtolower(preg_replace('~[^a-z0-9]+~', '-', $title) ?? '');
        $slug = trim((string)$slug, '-');
        if ($slug === '') $slug = 'service-' . $id;

        $pdo = Db::pdo();
        $stmt = $pdo->prepare('UPDATE services SET slug = ?, sort_order = ?, status = ?, title = ?, description = ?, pricing_text = ? WHERE id = ?');
        $stmt->execute([
            $slug,
            $sortOrder,
            $status,
            $title,
            $description !== '' ? $description : null,
            $pricingText !== '' ? $pricingText : null,
            $id,
        ]);

        $_SESSION['flash']['success'] = t('flash.service_updated');
        header('Location: ?r=/admin/services');
        exit;
    }

    public function delete(): void
    {
        Auth::requireRole('admin');
        Security::requireCsrf();

        $id = (string)($_POST['id'] ?? '');
        if ($id !== '') {
            $pdo = Db::pdo();
            $pdo->prepare("UPDATE services SET status = 'deleted' WHERE id = ?")->execute([$id]);
            $_SESSION['flash']['success'] = t('flash.service_deleted');
        } else {
            $_SESSION['flash']['error'] = t('flash.service_invalid_id');
        }

        header('Location: ?r=/admin/services');
        exit;
    }
}
