<?php
use Core\Auth;
$currentLang = \Core\Lang::current();
$currentRoute = $_GET['r'] ?? '/';
$switchLangUrl = fn(string $code) => '?r=' . urlencode($currentRoute) . '&lang=' . $code;
?>
<header class="navbar bg-white/70 backdrop-blur-md sticky top-0 z-50 border-b border-rose-100/50">
  <div class="navbar-start">
    <a href="?r=/" class="text-xl font-black tracking-tighter text-rose-500 px-4 flex items-center gap-2">
      <span>📷</span> <?= htmlspecialchars($siteName) ?>
    </a>
  </div>
  <div class="navbar-center hidden md:flex gap-2">
    <a class="btn btn-ghost" href="?r=/"><?= t('nav.home') ?></a>
    <a class="btn btn-ghost" href="?r=/portfolio"><?= t('nav.portfolio') ?></a>
    <a class="btn btn-ghost" href="?r=/services"><?= t('nav.services') ?></a>
    <a class="btn btn-ghost" href="?r=/booking"><?= t('nav.booking') ?></a>
    <a class="btn btn-ghost" href="?r=/contact"><?= t('nav.contact') ?></a>
    <?php if (Auth::check()): ?>
      <a class="btn btn-ghost text-rose-500 font-bold" href="?r=/admin"><?= t('nav.dashboard') ?></a>
      <a class="btn btn-ghost" href="?r=/admin/albums"><?= t('nav.albums') ?></a>
      <a class="btn btn-ghost" href="?r=/admin/photos"><?= t('nav.photos') ?></a>
    <?php else: ?>
      <a class="btn btn-ghost" href="?r=/admin/login"><?= t('nav.admin_login') ?></a>
    <?php endif; ?>
  </div>
  <div class="navbar-end gap-2">
    <div class="dropdown dropdown-end">
      <div tabindex="0" class="btn btn-ghost btn-sm text-rose-400 uppercase"><?= htmlspecialchars($currentLang) ?></div>
      <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-32">
        <li><a href="<?= htmlspecialchars($switchLangUrl('en')) ?>">English</a></li>
        <li><a href="<?= htmlspecialchars($switchLangUrl('ar')) ?>">العربية</a></li>
      </ul>
    </div>
    <a class="btn btn-sm border-none bg-gradient-to-r from-rose-400 to-pink-500 text-white rounded-xl px-6" href="?r=/booking"><?= t('nav.book_now') ?></a>
  </div>
</header>
