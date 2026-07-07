<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Auth;
use Core\Db;

final class DashboardController
{
    public function index(): string
    {
        Auth::requireRole('admin');

        $pdo = Db::pdo();
        $bookingsCount = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status != 'archived'")->fetchColumn();
        $contactsCount = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status != 'archived'")->fetchColumn();
        $photosCount = $pdo->query("SELECT COUNT(*) FROM photos WHERE status != 'deleted'")->fetchColumn();

        $content = '<div class="space-y-6">'
            . '<div class="flex items-center justify-between">'
            . '<div><h1 class="text-4xl font-black tracking-tight text-base-content">' . htmlspecialchars(t('admin.dashboard.title_pre')) . ' <span class="text-rose-500">' . htmlspecialchars(t('admin.dashboard.title_span')) . '</span></h1>'
            . '<p class="text-base-content/60">' . htmlspecialchars(t('admin.dashboard.subtitle')) . '</p></div>'
            . '<a href="?r=/admin/logout" class="btn btn-sm btn-ghost text-rose-400">' . htmlspecialchars(t('nav.logout')) . '</a>'
            . '</div>'
            . '<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">'
            . '<a href="?r=/admin/bookings" class="stat glass-card rounded-3xl p-6 hover:scale-[1.02] transition-transform"><div class="stat-title font-bold text-rose-400">' . htmlspecialchars(t('admin.dashboard.bookings')) . '</div><div class="stat-value text-rose-500">' . $bookingsCount . '</div></a>'
            . '<a href="?r=/admin/contacts" class="stat glass-card rounded-3xl p-6 hover:scale-[1.02] transition-transform"><div class="stat-title font-bold text-pink-400">' . htmlspecialchars(t('admin.dashboard.messages')) . '</div><div class="stat-value text-pink-500">' . $contactsCount . '</div></a>'
            . '<a href="?r=/admin/photos" class="stat glass-card rounded-3xl p-6 hover:scale-[1.02] transition-transform"><div class="stat-title font-bold text-amber-400">' . htmlspecialchars(t('admin.dashboard.photos')) . '</div><div class="stat-value text-amber-500">' . $photosCount . '</div></a>'
            . '</div>'
            . '<div class="mt-10 grid grid-cols-1 lg:grid-cols-2 gap-8">'
            . '<div class="glass-card p-8 rounded-[2.5rem]">'
            . '<h2 class="text-2xl font-bold mb-6 text-rose-500">' . htmlspecialchars(t('admin.dashboard.quick_actions')) . '</h2>'
            . '<div class="grid grid-cols-2 gap-4">'
            . '<a href="?r=/admin/categories" class="btn btn-outline border-rose-100 text-rose-400 hover:bg-rose-50 rounded-xl">' . htmlspecialchars(t('admin.dashboard.categories')) . '</a>'
            . '<a href="?r=/admin/albums" class="btn btn-outline border-rose-100 text-rose-400 hover:bg-rose-50 rounded-xl">' . htmlspecialchars(t('admin.dashboard.albums')) . '</a>'
            . '<a href="?r=/admin/services" class="btn btn-outline border-rose-100 text-rose-400 hover:bg-rose-50 rounded-xl">' . htmlspecialchars(t('admin.dashboard.services')) . '</a>'
            . '<a href="?r=/admin/settings" class="btn btn-outline border-rose-100 text-rose-400 hover:bg-rose-50 rounded-xl">' . htmlspecialchars(t('admin.dashboard.settings')) . '</a>'
            . '</div>'
            . '</div>'
            . '<div class="glass-card p-8 rounded-[2.5rem] border-dashed border-2 border-rose-200 text-center flex flex-col justify-center">'
            . '<p class="text-rose-400 font-medium text-lg">' . htmlspecialchars(t('admin.dashboard.quote')) . '</p>'
            . '<div class="mt-4"><a href="?r=/admin/photos/upload" class="btn bg-rose-500 text-white border-none rounded-xl">' . htmlspecialchars(t('admin.dashboard.upload_new_photo')) . '</a></div>'
            . '</div>'
            . '</div></div>';

        $title = 'Admin';

        ob_start();
        require __DIR__ . '/../../views/layouts/base.php';
        return (string)ob_get_clean();
    }
}
