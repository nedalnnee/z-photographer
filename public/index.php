<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';


// Simple front controller
$basePath = dirname($_SERVER['SCRIPT_NAME']);
$route = $_GET['r'] ?? '';

$appCfg = require __DIR__ . '/../config/app.php';

// Never leak stack traces/paths to visitors in production; log instead.
$debug = (bool)($appCfg['debug'] ?? false);
ini_set('display_errors', $debug ? '1' : '0');
error_reporting(E_ALL);
ini_set('log_errors', '1');

// Baseline hardening headers.
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

// session + basic security bootstrap
require __DIR__ . '/../core/Security.php';
Core\Security::startSession($appCfg);

// Language Switching Logic
if (isset($_GET['lang'])) {
    $requestedLang = $_GET['lang'] === 'ar' ? 'ar' : 'en';
    $_SESSION['lang'] = $requestedLang;
}

$router = require __DIR__ . '/../app/bootstrap/routes.php';

$response = $router->dispatch($route ?: '/');

echo $response;
