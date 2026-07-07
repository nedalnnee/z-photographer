<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Auth;
use Core\Security;

final class SettingsController
{
    public function index(): string
    {
        Auth::requireRole('admin');
        $pdo = \Core\Db::pdo();
        $settingsRaw = $pdo->query("SELECT name, value FROM settings")->fetchAll(\PDO::FETCH_KEY_PAIR);

        $siteName = $settingsRaw['site.name'] ?? 'Photograph';
        $email = $settingsRaw['site.contact_email'] ?? 'admin@example.com';
        $igUrl = $settingsRaw['site.ig_url'] ?? '';
        $tagline = $settingsRaw['site.tagline'] ?? 'Luxury photography portfolio & booking platform.';

        $csrf = Security::csrfToken();

        ob_start();
        $content = '<div class="max-w-3xl mx-auto py-6 space-y-8">'
            . '<div><h1 class="text-4xl font-black tracking-tight text-base-content">' . htmlspecialchars(t('admin.settings.title_pre')) . ' <span class="text-rose-500">' . htmlspecialchars(t('admin.settings.title_span')) . '</span></h1>'
            . '<p class="text-base-content/60">' . htmlspecialchars(t('admin.settings.subtitle')) . '</p></div>'
            . '<div class="glass-card rounded-[2.5rem] p-10">'
            . '<form method="POST" action="?r=/admin/settings/update" class="space-y-6">'
            . "<input type=\"hidden\" name=\"csrf_token\" value=\"{$csrf}\">"
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">' . htmlspecialchars(t('admin.settings.site_name')) . '</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" name="site_name" value="' . Security::e($siteName) . '"></div>'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">' . htmlspecialchars(t('admin.settings.contact_email')) . '</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" type="email" name="contact_email" value="' . Security::e($email) . '"></div>'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">' . htmlspecialchars(t('admin.settings.tagline')) . '</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" name="site_tagline" value="' . Security::e($tagline) . '"></div>'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">' . htmlspecialchars(t('admin.settings.ig_url')) . '</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" name="ig_url" value="' . Security::e($igUrl) . '" placeholder="https://instagram.com/..."></div>'
            . '<div class="pt-6">'
            . '<button class="btn border-none bg-gradient-to-r from-rose-400 to-pink-500 text-white w-full rounded-2xl h-14" type="submit">' . htmlspecialchars(t('admin.settings.save')) . '</button>'
            . '</div>'
            . '</form></div></div>';

        $title = 'Settings';
        require __DIR__ . '/../../views/layouts/base.php';
        return (string)ob_get_clean();
    }

    public function update(): void
    {
        Auth::requireRole('admin');
        Security::requireCsrf();

        $siteName = (string)($_POST['site_name'] ?? '');
        $email = (string)($_POST['contact_email'] ?? '');
        $igUrl = (string)($_POST['ig_url'] ?? '');
        $tagline = (string)($_POST['site_tagline'] ?? '');

        $pdo = \Core\Db::pdo();
        $stmt = $pdo->prepare("INSERT INTO settings (name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
        $stmt->execute(['site.name', $siteName]);
        $stmt->execute(['site.contact_email', $email]);
        $stmt->execute(['site.ig_url', $igUrl]);
        $stmt->execute(['site.tagline', $tagline]);

        $_SESSION['flash']['success'] = t('flash.settings_updated');
        header('Location: ' . $_SERVER['PHP_SELF'] . '?r=/admin/settings');
        exit;
    }
}
