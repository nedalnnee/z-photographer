<footer class="footer footer-center p-10 bg-white/30 backdrop-blur-sm text-base-content/70 border-t border-rose-100/50 mt-20">
  <div class="max-w-7xl mx-auto w-full">
    <div class="text-2xl font-black tracking-tighter text-rose-500 mb-2">📷 <?= htmlspecialchars($siteName) ?></div>
    <p class="font-medium"><?= htmlspecialchars($siteTagline) ?></p>
    <div class="flex gap-4 my-4">
        <a href="<?= htmlspecialchars($siteInstagram) ?>" target="_blank" class="w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all duration-300">IG</a>
        <a href="#" class="w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all duration-300">FB</a>
        <a href="#" class="w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all duration-300">TW</a>
    </div>
    <p class="text-xs opacity-50 uppercase tracking-widest">© <?= date('Y') ?> All rights reserved. Crafted with ✨</p>
  </div>
</footer>
