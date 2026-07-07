<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Db;
use Core\Security;

final class ServicesController
{
    public function index(): string
    {
        $pdo = Db::pdo();

        try {
            $services = $pdo->query("SELECT * FROM services WHERE status = 'active' ORDER BY sort_order ASC")->fetchAll();
        } catch (\PDOException $e) {
            // في حال عدم وجود الجدول بعد
            $services = [];
        }

        ob_start();
        $content = '<div class="text-center mb-16">'
            . '<span class="px-4 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase bg-rose-100 text-rose-600">'. htmlspecialchars(t('services.badge')) .'</span>'
            . '<h1 class="text-5xl font-black mt-4 tracking-tight">'. htmlspecialchars(t('services.title_pre')) .' <span class="text-rose-500">'. htmlspecialchars(t('services.title_span')) .'</span> '. htmlspecialchars(t('services.title_post')) .'</h1>'
            . '<p class="mt-4 text-base-content/60 max-w-xl mx-auto">'. htmlspecialchars(t('services.desc')) .'</p>'
            . '</div>'
            . '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">';

        if (empty($services)) {
            $content .= '<div class="col-span-full text-center py-20"><p class="text-base-content/50 italic">' . htmlspecialchars(t('services.empty')) . '</p></div>';
        } else {
            foreach ($services as $s) {
                $content .= '<div class="glass-card p-10 rounded-[3rem] flex flex-col hover:scale-[1.02] transition-transform duration-500">'
                    . '<h3 class="text-3xl font-black text-rose-500 mb-2">' . Security::e($s['title']) . '</h3>'
                    . '<div class="text-sm font-bold text-base-content/40 uppercase tracking-widest mb-6">' . Security::e($s['pricing_text'] ?? '') . '</div>'
                    . '<p class="text-base-content/70 mb-8 flex-1">' . nl2br(Security::e($s['description'] ?? '')) . '</p>'
                    . '<a href="?r=/booking&service_id=' . $s['id'] . '" class="btn bg-base-content text-base-100 border-none rounded-2xl h-14">'. htmlspecialchars(t('services.book')) .'</a>'
                    . '</div>';
            }
        }

        $content .= '</div>';

        $title = 'Services';
        require __DIR__ . '/../views/layouts/base.php';
        return (string)ob_get_clean();
    }
}
