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
            . '<div><h1 class="text-4xl font-black tracking-tight text-base-content">Site <span class="text-rose-500">Settings</span></h1>'
            . '<p class="text-base-content/60">Configure your platform identity and contact info.</p></div>'
            . '<div class="glass-card rounded-[2.5rem] p-10">'
            . '<form method="POST" action="?r=/admin/settings/update" class="space-y-6">'
            . "<input type=\"hidden\" name=\"csrf_token\" value=\"{$csrf}\">"
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">Site Name</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" name="site_name" value="' . Security::e($siteName) . '"></div>'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">Admin Contact Email</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" type="email" name="contact_email" value="' . Security::e($email) . '"></div>'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">Site Tagline</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" name="site_tagline" value="' . Security::e($tagline) . '"></div>'
            . '<div class="form-control"><label class="label"><span class="label-text font-bold text-rose-400">Instagram URL</span></label>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" name="ig_url" value="' . Security::e($igUrl) . '" placeholder="https://instagram.com/..."></div>'
            . '<div class="pt-6">'
            . '<button class="btn border-none bg-gradient-to-r from-rose-400 to-pink-500 text-white w-full rounded-2xl h-14" type="submit">Save Global Settings</button>'
            . '</div>'
            . '</form></div></div>';

        $title = 'Settings';
        $lang = $_SESSION['lang'] ?? 'en';
        $dir = $lang === 'ar' ? 'rtl' : 'ltr';
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

        $_SESSION['flash']['success'] = 'Settings updated successfully.';
        header('Location: ' . $_SERVER['PHP_SELF'] . '?r=/admin/settings');
        exit;
    }
}