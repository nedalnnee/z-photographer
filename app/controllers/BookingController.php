<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Security;
use Core\Db;

final class BookingController
{
    public function index(): string
    {
        $csrf = Security::csrfToken();
        $isAr = ($_SESSION['lang'] ?? 'en') === 'ar';

        $t = $isAr ? [
            'badge' => 'الحجز',
            'title' => 'احجز <span class="text-rose-500">جلستك</span>',
            'name' => 'الاسم الكامل',
            'email' => 'البريد الإلكتروني',
            'service' => 'نوع الخدمة',
            'confirm' => 'تأكيد الحجز'
        ] : [
            'badge' => 'Reservation',
            'title' => 'Book Your <span class="text-rose-500">Session</span>',
            'name' => 'Full Name',
            'email' => 'Email Address',
            'service' => 'Service Type',
            'confirm' => 'Confirm Reservation'
        ];
        
        // جلب الخدمات المفعلة من قاعدة البيانات
        $pdo = Db::pdo();
        $stmt = $pdo->query("SELECT id, title FROM services WHERE status = 'active' AND booking_enabled = 1 ORDER BY sort_order ASC");
        $services = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $serviceOptions = '';
        foreach ($services as $service) {
            $serviceOptions .= '<option value="' . $service['id'] . '">' . htmlspecialchars($service['title']) . '</option>';
        }

        ob_start();
        $content = '<div class="max-w-2xl mx-auto"><div class="text-center mb-10">'
            . '<span class="px-4 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase bg-rose-100 text-rose-600">'. $t['badge'] .'</span>'
            . '<h1 class="text-4xl font-black mt-4">'. $t['title'] .'</h1>'
            . '</div>'
            . '<div class="glass-card rounded-[2.5rem]"><div class="card-body p-10">'
            . '<form method="POST" action="?r=/booking/submit" class="space-y-3">'
            . "<input type=\"hidden\" name=\"csrf_token\" value=\"{$csrf}\">"
            . '<label class="form-control"><div class="label"><span class="label-text font-bold text-rose-400">'. $t['name'] .'</span></div>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" name="full_name" required></label>'
            . '<label class="form-control"><div class="label"><span class="label-text font-bold text-rose-400">'. $t['email'] .'</span></div>'
            . '<input class="input input-bordered border-rose-100 rounded-xl" type="email" name="email" required></label>'
            . '<label class="form-control"><div class="label"><span class="label-text font-bold text-rose-400">'. $t['service'] .'</span></div>'
            . '<select class="select select-bordered border-rose-100 rounded-xl" name="service_id" required>'
            . '<option value="">' . ($isAr ? 'اختر الباقة' : 'Select a package') . '</option>' . $serviceOptions . '</select></label>'
            . '<button class="btn border-none bg-gradient-to-r from-rose-400 to-pink-500 text-white w-full mt-6 rounded-2xl h-14" type="submit">'. $t['confirm'] .'</button>'
            . '</form>'
            . '</div></div></div>';

        $title = 'Booking';
        $lang = $_SESSION['lang'] ?? 'en';
        $dir = $lang === 'ar' ? 'rtl' : 'ltr';
        require __DIR__ . '/../views/layouts/base.php';
        return (string)ob_get_clean();
    }

    public function submit(): string
    {
        Security::requireCsrf();

        $fullName = trim((string)($_POST['full_name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $serviceId = (string)($_POST['service_id'] ?? '');

        if ($fullName !== '' && $serviceId !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $pdo = Db::pdo();
            // التأكد من وجود الأعمدة المطلوبة في جدول الحجوزات
            $stmt = $pdo->prepare("INSERT INTO bookings (full_name, email, service_id, status, created_at) VALUES (?, ?, ?, 'new', NOW())");
            $stmt->execute([$fullName, $email, $serviceId]);
            
            $_SESSION['flash']['success'] = 'Thank you! Your reservation has been received and is under review.';
        } else {
            $_SESSION['flash']['error'] = 'Please fill in all required fields.';
        }

        http_response_code(302);
        header('Location: ?r=/booking');
        exit;
    }
}
