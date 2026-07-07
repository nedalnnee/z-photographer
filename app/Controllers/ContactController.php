<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Security;
use Core\Db;

final class ContactController
{
    public function index(): string
    {
        $csrf = Security::csrfToken();
        
        ob_start();
        require __DIR__ . '/../views/contact.php';
        $content = ob_get_clean();

        $title = 'Contact Us';
        require __DIR__ . '/../views/layouts/base.php';
        return (string)ob_get_clean();
    }

    public function submit(): void
    {
        Security::requireCsrf();

        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $subject = trim((string)($_POST['subject'] ?? ''));
        $message = trim((string)($_POST['message'] ?? ''));

        if ($name !== '' && $message !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $pdo = Db::pdo();
            $stmt = $pdo->prepare("INSERT INTO contacts (full_name, email, subject, message, status, created_at) VALUES (?, ?, ?, ?, 'unread', NOW())");
            $stmt->execute([$name, $email, $subject, $message]);
            
            $_SESSION['flash']['success'] = t('flash.contact_success');
        } else {
            $_SESSION['flash']['error'] = t('flash.contact_error');
        }

        header('Location: ?r=/contact');
        exit;
    }
}