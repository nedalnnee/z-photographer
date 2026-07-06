<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Auth;
use Core\Db;
use Core\Security;

final class ContactController
{
    public function index(): string
    {
        Auth::requireRole('admin');

        $pdo = Db::pdo();
        $stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC");
        $messages = $stmt->fetchAll();
        
        ob_start();
        $content = '<div class="space-y-6">'
            . '<div><h1 class="text-4xl font-black tracking-tight text-base-content">Inbox <span class="text-rose-500">Messages</span></h1>'
            . '<p class="text-base-content/60">Read and respond to client inquiries.</p></div>'
            . '<div class="grid grid-cols-1 gap-4">';

        if (empty($messages)) {
            $content .= '<div class="glass-card p-10 rounded-3xl text-center text-base-content/40 italic">No messages yet.</div>';
        } else {
            foreach ($messages as $m) {
                $id = (string)$m['id'];
                $isNew = $m['status'] === 'unread';
                $content .= '<div class="glass-card p-6 rounded-3xl photo-hover-effect">'
                    . '<div class="flex justify-between items-start mb-4">'
                    . '<div><h3 class="font-black text-lg text-rose-500">' . Security::e((string)$m['subject'] ?: 'No Subject') . '</h3>'
                    . '<p class="text-xs text-base-content/40">From: ' . Security::e((string)$m['full_name']) . ' (' . Security::e((string)$m['email']) . ') • ' . $m['created_at'] . '</p></div>'
                    . ($isNew ? '<span class="badge badge-rose bg-rose-100 text-rose-600 border-none text-xs">New</span>' : '')
                    . '</div>'
                    . '<p class="text-base-content/70 italic">"' . nl2br(Security::e((string)$m['message'])) . '"</p>'
                    . '<div class="mt-4 flex gap-2">'
                    . '<form method="POST" action="?r=/admin/contacts/read">'
                    . '<input type="hidden" name="csrf_token" value="' . Security::csrfToken() . '"><input type="hidden" name="id" value="' . $id . '">'
                    . '<button class="btn btn-sm btn-ghost text-rose-400">Mark as Read</button></form>'
                    . '<form method="POST" action="?r=/admin/contacts/delete" onsubmit="return confirm(\'Delete message?\')">'
                    . '<input type="hidden" name="csrf_token" value="' . Security::csrfToken() . '"><input type="hidden" name="id" value="' . $id . '">'
                    . '<button class="btn btn-sm btn-ghost text-error">Delete</button></form>'
                    . '</div>'
                    . '</div>';
            }
        }

        $content .= '</div></div>';

        $title = 'Contact Messages';
        $lang = $_SESSION['lang'] ?? 'en';
        $dir = $lang === 'ar' ? 'rtl' : 'ltr';
        require __DIR__ . '/../../views/layouts/base.php';
        return (string)ob_get_clean();
    }

    public function delete(): void
    {
        Auth::requireRole('admin');
        Security::requireCsrf();
        $id = $_POST['id'] ?? '';
        if ($id) {
            Db::pdo()->prepare("DELETE FROM contacts WHERE id = ?")->execute([$id]);
            $_SESSION['flash']['success'] = 'Message deleted.';
        }
        header('Location: ' . $_SERVER['PHP_SELF'] . '?r=/admin/contacts');
        exit;
    }

    public function markAsRead(): void
    {
        Auth::requireRole('admin');
        Security::requireCsrf();
        $id = $_POST['id'] ?? '';
        if ($id) {
            Db::pdo()->prepare("UPDATE contacts SET status = 'read' WHERE id = ?")->execute([$id]);
            $_SESSION['flash']['success'] = 'Message marked as read.';
        }
        header('Location: ' . $_SERVER['PHP_SELF'] . '?r=/admin/contacts');
        exit;
    }
}