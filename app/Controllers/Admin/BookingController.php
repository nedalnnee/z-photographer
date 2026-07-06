<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Auth;
use Core\Db;
use Core\Security;

final class BookingController
{
    public function index(): string
    {
        Auth::requireRole('admin');

        $pdo = Db::pdo();
        $stmt = $pdo->query("SELECT b.*, s.title AS service_title FROM bookings b LEFT JOIN services s ON s.id = b.service_id WHERE b.status <> 'archived' ORDER BY b.created_at DESC");
        $bookings = $stmt->fetchAll();

        ob_start();
        $content = '<div class="flex items-center justify-between mb-6">'
            . '<div><h1 class="text-4xl font-black tracking-tight text-base-content">Bookings</h1>'
            . '<p class="text-base-content/60">Manage your upcoming photography sessions.</p></div>'
            . '</div>'
            . '<div class="glass-card rounded-[2.5rem] overflow-hidden">'
            . '<div class="overflow-x-auto"><table class="table w-full">'
            . '<thead><tr class="text-rose-400 border-b border-rose-100"><th>Client</th><th>Service</th><th>Date</th><th>Status</th><th class="text-right">Actions</th></tr></thead>'
            . '<tbody>';

        foreach ($bookings as $b) {
            $id = (string)$b['id'];
            $fullName = Security::e((string)$b['full_name']);
            $email = Security::e((string)$b['email']);
            $serviceTitle = Security::e((string)($b['service_title'] ?? '—'));
            $date = Security::e((string)($b['created_at'] ?? '—'));
            $status = (string)$b['status'];

            $badgeText = ucfirst($status);
            $badgeClass = match ($status) {
                'confirmed' => 'badge-success',
                'rejected' => 'badge-error',
                'pending' => 'badge-warning',
                'new' => 'badge-rose',
                default => 'badge-ghost'
            };

            $confirmBtn = ($status !== 'confirmed') ? 
                '<form method="POST" action="?r=/admin/bookings/update" class="inline">'
                . '<input type="hidden" name="csrf_token" value="' . Security::csrfToken() . '">' 
                . '<input type="hidden" name="id" value="' . $id . '"><input type="hidden" name="status" value="confirmed">'
                . '<button class="btn btn-sm btn-ghost text-rose-500 font-bold" type="submit">Confirm</button></form>' : '';

            $rejectBtn = ($status !== 'rejected') ? 
                '<form method="POST" action="?r=/admin/bookings/update" class="inline">'
                . '<input type="hidden" name="csrf_token" value="' . Security::csrfToken() . '">' 
                . '<input type="hidden" name="id" value="' . $id . '"><input type="hidden" name="status" value="rejected">'
                . '<button class="btn btn-sm btn-ghost text-error" type="submit">Reject</button></form>' : '';

            $archiveBtn = '<form method="POST" action="?r=/admin/bookings/update" class="inline">'
                . '<input type="hidden" name="csrf_token" value="' . Security::csrfToken() . '">' 
                . '<input type="hidden" name="id" value="' . $id . '"><input type="hidden" name="status" value="archived">'
                . '<button class="btn btn-sm btn-ghost text-base-content/30" title="Archive">✕</button></form>';

            $content .= '<tr class="hover:bg-rose-50/50 transition-colors border-b border-rose-50">'
                . '<td><div class="font-bold">' . $fullName . '</div><div class="text-xs opacity-50">' . $email . '</div></td>'
                . '<td>' . $serviceTitle . '</td>'
                . '<td>' . $date . '</td>'
                . '<td><span class="badge ' . $badgeClass . ' badge-outline">' . $badgeText . '</span></td>'
                . '<td class="text-right flex justify-end gap-2">'
                . $confirmBtn . $rejectBtn . $archiveBtn
                . '</td></tr>';
        }

        $content .= '</tbody></table></div></div></div>';

        $title = 'Manage Bookings';
        $lang = $_SESSION['lang'] ?? 'en';
        $dir = $_SESSION['lang'] === 'ar' ? 'rtl' : 'ltr';
        require __DIR__ . '/../../views/layouts/base.php';
        return (string)ob_get_clean();
    }

    public function update(): void
    {
        Auth::requireRole('admin');
        Security::requireCsrf();

        $id = (string)($_POST['id'] ?? '');
        $status = (string)($_POST['status'] ?? 'pending');
        $allowed = ['confirmed', 'rejected', 'pending', 'new', 'archived'];

        if ($id === '' || !in_array($status, $allowed, true)) {
            $_SESSION['flash']['error'] = 'Invalid booking update.';
            header('Location: ?r=/admin/bookings');
            exit;
        }

        $pdo = Db::pdo();
        $stmt = $pdo->prepare('UPDATE bookings SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);

        $_SESSION['flash']['success'] = 'Booking updated.';
        header('Location: ?r=/admin/bookings');
        exit;
    }
}
