<?php
use Core\Auth;
$currentLang = $_SESSION['lang'] ?? 'en';
?>
<header class="navbar bg-white/70 backdrop-blur-md sticky top-0 z-50 border-b border-rose-100/50">
  <div class="navbar-start">
    <a href="?r=/" class="text-xl font-black tracking-tighter text-rose-500 px-4 flex items-center gap-2">
      <span>📷</span> <?= htmlspecialchars($siteName) ?>
    </a>
  </div>
  <div class="navbar-center hidden md:flex gap-2">
    <a class="btn btn-ghost" href="?r=/"><?= $currentLang === 'ar' ? 'الرئيسية' : 'Home' ?></a>
    <a class="btn btn-ghost" href="?r=/portfolio"><?= $currentLang === 'ar' ? 'المعرض' : 'Portfolio' ?></a>
    <a class="btn btn-ghost" href="?r=/services"><?= $currentLang === 'ar' ? 'الخدمات' : 'Services' ?></a>
    <a class="btn btn-ghost" href="?r=/booking"><?= $currentLang === 'ar' ? 'الحجز' : 'Booking' ?></a>
    <a class="btn btn-ghost" href="?r=/contact"><?= $currentLang === 'ar' ? 'اتصل بنا' : 'Contact' ?></a>
    <?php if (Auth::check()): ?>
      <a class="btn btn-ghost text-rose-500 font-bold" href="?r=/admin"><?= $currentLang === 'ar' ? 'لوحة التحكم' : 'Dashboard' ?></a>
      <a class="btn btn-ghost" href="?r=/admin/albums"><?= $currentLang === 'ar' ? 'الألبومات' : 'Albums' ?></a>
      <a class="btn btn-ghost" href="?r=/admin/photos"><?= $currentLang === 'ar' ? 'الصور' : 'Photos' ?></a>
    <?php else: ?>
      <a class="btn btn-ghost" href="?r=/admin/login"><?= $currentLang === 'ar' ? 'دخول المسؤول' : 'Admin' ?></a>
    <?php endif; ?>
  </div>
  <div class="navbar-end gap-2">
    <div class="dropdown dropdown-end">
      <div tabindex="0" class="btn btn-ghost btn-sm text-rose-400 uppercase"><?= $currentLang ?></div>
      <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-32">
        <li><a href="?lang=en">English</a></li>
        <li><a href="?lang=ar">العربية</a></li>
      </ul>
    </div>
    <a class="btn btn-sm border-none bg-gradient-to-r from-rose-400 to-pink-500 text-white rounded-xl px-6" href="?r=/booking"><?= $currentLang === 'ar' ? 'احجز الآن' : 'Book Now' ?></a>
  </div>
</header>
