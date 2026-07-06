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
        $isAr = ($_SESSION['lang'] ?? 'en') === 'ar';
        
        $pdo = Db::pdo();
        $bookingsCount = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status != 'archived'")->fetchColumn();
        $contactsCount = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status != 'archived'")->fetchColumn();
        $photosCount = $pdo->query("SELECT COUNT(*) FROM photos WHERE status != 'deleted'")->fetchColumn();
        $categoriesCount = $pdo->query("SELECT COUNT(*) FROM categories WHERE status != 'deleted'")->fetchColumn();

        $content = '<div class="space-y-6">'
            . '<div class="flex items-center justify-between">'
            . '<div><h1 class="text-4xl font-black tracking-tight text-base-content">' . ($isAr ? 'لوحة <span class="text-rose-500">التحكم</span>' : 'Admin <span class="text-rose-500">Dashboard</span>') . '</h1>'
            . '<p class="text-base-content/60">' . ($isAr ? 'نظرة عامة على أعمال التصوير الخاصة بك.' : 'Overview of your photography business.') . '</p></div>'
            . '<a href="?r=/admin/logout" class="btn btn-sm btn-ghost text-rose-400">' . ($isAr ? 'تسجيل الخروج' : 'Logout') . '</a>'
            . '</div>'
            . '<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">'
            . '<a href="?r=/admin/bookings" class="stat glass-card rounded-3xl p-6 hover:scale-[1.02] transition-transform"><div class="stat-title font-bold text-rose-400">' . ($isAr ? 'الحجوزات' : 'Bookings') . '</div><div class="stat-value text-rose-500">' . $bookingsCount . '</div></a>'
            . '<a href="?r=/admin/contacts" class="stat glass-card rounded-3xl p-6 hover:scale-[1.02] transition-transform"><div class="stat-title font-bold text-pink-400">' . ($isAr ? 'الرسائل' : 'Messages') . '</div><div class="stat-value text-pink-500">' . $contactsCount . '</div></a>'
            . '<a href="?r=/admin/photos" class="stat glass-card rounded-3xl p-6 hover:scale-[1.02] transition-transform"><div class="stat-title font-bold text-amber-400">' . ($isAr ? 'الصور' : 'Photos') . '</div><div class="stat-value text-amber-500">' . $photosCount . '</div></a>'
            . '</div>'
            . '<div class="mt-10 grid grid-cols-1 lg:grid-cols-2 gap-8">'
            . '<div class="glass-card p-8 rounded-[2.5rem]">'
            . '<h2 class="text-2xl font-bold mb-6 text-rose-500">' . ($isAr ? 'إجراءات سريعة' : 'Quick Actions') . '</h2>'
            . '<div class="grid grid-cols-2 gap-4">'
            . '<a href="?r=/admin/categories" class="btn btn-outline border-rose-100 text-rose-400 hover:bg-rose-50 rounded-xl">' . ($isAr ? 'الفئات' : 'Categories') . '</a>'
            . '<a href="?r=/admin/albums" class="btn btn-outline border-rose-100 text-rose-400 hover:bg-rose-50 rounded-xl">' . ($isAr ? 'الألبومات' : 'Albums') . '</a>'
            . '<a href="?r=/admin/services" class="btn btn-outline border-rose-100 text-rose-400 hover:bg-rose-50 rounded-xl">' . ($isAr ? 'الخدمات' : 'Services') . '</a>'
            . '<a href="?r=/admin/settings" class="btn btn-outline border-rose-100 text-rose-400 hover:bg-rose-50 rounded-xl">' . ($isAr ? 'الإعدادات' : 'Settings') . '</a>'
            . '</div>'
            . '</div>'
            . '<div class="glass-card p-8 rounded-[2.5rem] border-dashed border-2 border-rose-200 text-center flex flex-col justify-center">'
            . '<p class="text-rose-400 font-medium text-lg">' . ($isAr ? 'معرض أعمالك هو مرآة روحك. ابقه محدثاً!' : 'Your portfolio is the mirror of your soul. Keep it updated!') . '</p>'
            . '<div class="mt-4"><a href="?r=/admin/photos/upload" class="btn bg-rose-500 text-white border-none rounded-xl">' . ($isAr ? 'رفع صورة جديدة' : 'Upload New Photo') . '</a></div>'
            . '</div>'
            . '</div></div>';

        $title = 'Admin';
        $lang = $_SESSION['lang'] ?? 'en';
        $dir = $lang === 'ar' ? 'rtl' : 'ltr';

        ob_start();
        require __DIR__ . '/../../views/layouts/base.php';
        return (string)ob_get_clean();
    }
}
