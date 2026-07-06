<?php
use Core\I18n;
$langMeta = I18n::currentLanguage();
$lang = $langMeta['code'] ?? 'en';
$dir = ($langMeta['direction'] ?? 'ltr') === 'rtl' ? 'rtl' : 'ltr';

// استخراج رسائل التنبيه من الجلسة ومسحها
$flash = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);

// تحديد المسار الأساسي للمشروع لتجنب مشاكل الروابط النسبية
$baseUrl = dirname($_SERVER['SCRIPT_NAME']);
if ($baseUrl === '\\' || $baseUrl === '/') $baseUrl = '';

// جلب إعدادات الموقع العامة لتكون متاحة في الهيدر والفوتر
$pdo = \Core\Db::pdo();
$globalSettings = $pdo->query("SELECT name, value FROM settings WHERE autoload = 1")->fetchAll(\PDO::FETCH_KEY_PAIR);

$siteName = $globalSettings['site.name'] ?? 'Photograph';
$siteEmail = $globalSettings['site.contact_email'] ?? '';
$siteTagline = $globalSettings['site.tagline'] ?? 'Luxury photography portfolio & booking platform.';
$siteInstagram = $globalSettings['site.ig_url'] ?? '#';
?>
<!doctype html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= htmlspecialchars($dir) ?>" data-theme="light">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title ?? $siteName) ?></title>

  <!-- Tailwind + DaisyUI via CDN (scaffold) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.12/dist/full.min.css" rel="stylesheet" type="text/css" />
  <link href="<?= $baseUrl ?>/assets/css/app.css" rel="stylesheet" type="text/css" />
  <link href="<?= $baseUrl ?>/assets/css/theme.css" rel="stylesheet" type="text/css" />

  <style>
    :root {
        font-family: 'Tajawal', sans-serif;
    }
    [dir="rtl"] {
        font-family: 'Tajawal', sans-serif;
    }
    @keyframes float-slow {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-8px) rotate(1deg); }
    }
    @keyframes pulse-soft {
        0%, 100% { opacity: 0.9; transform: scale(1); }
        50% { opacity: 1; transform: scale(1.02); }
    }
    .animate-float {
        animation: float-slow 6s ease-in-out infinite;
    }
    .animate-pulse-soft {
        animation: pulse-soft 3s ease-in-out infinite;
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(251, 113, 133, 0.15);
    }
    .photo-hover-effect {
        transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .photo-hover-effect:hover {
        transform: scale(1.06) rotate(-1deg);
        box-shadow: 0 20px 25px -5px rgba(251, 113, 133, 0.2);
    }
  </style>

  <meta name="robots" content="index,follow" />
</head>
<body class="bg-gradient-to-br from-rose-50 via-white to-pink-50 min-h-screen text-base-content" data-dir="<?= htmlspecialchars($dir) ?>">
  <?php require __DIR__ . '/partials/header.php'; ?>

  <main class="container mx-auto px-4 py-10">
    <?php if (!empty($flash['error'])): ?>
      <div class="alert alert-error mb-4"><?= Core\Security::e($flash['error']) ?></div>
    <?php endif; ?>
    <?php if (!empty($flash['success'])): ?>
      <div class="alert alert-success mb-4"><?= Core\Security::e($flash['success']) ?></div>
    <?php endif; ?>

    <?= $content ?? '' ?>
  </main>

  <?php require __DIR__ . '/partials/footer.php'; ?>

  <script src="<?= $baseUrl ?>/assets/js/app.js"></script>
</body>
</html>
