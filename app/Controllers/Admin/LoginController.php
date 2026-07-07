<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Auth;
use Core\Security;

final class LoginController
{
    public function showLogin(): string
    {
        $csrf = Security::csrfToken();

        ob_start();
        $content = '<div class="max-w-md mx-auto py-12">'
            . '<div class="text-center mb-8">'
            . '<span class="px-4 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase bg-rose-100 text-rose-600">' . htmlspecialchars(t('login.secure_access')) . '</span>'
            . '<h1 class="text-4xl font-black mt-4 tracking-tight text-base-content">' . htmlspecialchars(t('login.title_pre')) . ' <span class="text-rose-500">' . htmlspecialchars(t('login.title_span')) . '</span></h1>'
            . '</div>'
            . '<div class="glass-card rounded-[2.5rem] overflow-hidden animate-float">'
            . '<div class="card-body p-10">'
            . '<form method="POST" action="?r=/admin/login" class="space-y-5">'
            . "<input type=\"hidden\" name=\"csrf_token\" value=\"{$csrf}\">"
            . '<label class="form-control"><div class="label"><span class="label-text font-bold text-rose-400">' . htmlspecialchars(t('login.email')) . '</span></div>'
            . '<input class="input input-bordered border-rose-100 rounded-xl focus:border-rose-300" type="email" name="email" required></label>'
            . '<label class="form-control"><div class="label"><span class="label-text font-bold text-rose-400">' . htmlspecialchars(t('login.password')) . '</span></div>'
            . '<input class="input input-bordered border-rose-100 rounded-xl focus:border-rose-300" type="password" name="password" required></label>'
            . '<button class="btn border-none bg-gradient-to-r from-rose-400 to-pink-500 text-white w-full mt-4 rounded-2xl h-14 shadow-lg shadow-rose-200" type="submit">' . htmlspecialchars(t('login.submit')) . '</button>'
            . '</form>'
            . '</div></div></div>';

        $title = 'Admin Login';
        require __DIR__ . '/../../views/layouts/base.php';
        return (string)ob_get_clean();
    }

    public function login(): string
    {
        Security::requireCsrf();
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash']['error'] = t('flash.login_invalid_email');
            http_response_code(302);
            header('Location: ?r=/admin/login');
            exit;
        }

        if (!Auth::login($email, $password)) {
            $_SESSION['flash']['error'] = t('flash.login_invalid_credentials');
            http_response_code(302);
            header('Location: ?r=/admin/login');
            exit;
        }

        http_response_code(302);
        header('Location: ?r=/admin');
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: ?r=/admin/login');
        exit;
    }
}
