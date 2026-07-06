<?php
ob_start();
?>
<div class="max-w-4xl mx-auto space-y-8 py-10">
  <div class="text-center">
    <span class="px-4 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase bg-rose-100 text-rose-600">The Artist</span>
    <h1 class="text-5xl font-black mt-4 tracking-tight">About the <span class="text-rose-500">Photographer</span></h1>
  </div>

  <div class="glass-card rounded-[2.5rem] overflow-hidden animate-float">
    <div class="p-8 lg:p-12">
      <div class="flex flex-col md:flex-row gap-10 items-center">
        <div class="w-48 h-48 rounded-full bg-gradient-to-tr from-rose-200 to-pink-300 shadow-xl shadow-rose-100 p-1">
            <div class="w-full h-full rounded-full bg-white flex items-center justify-center text-4xl">📸</div>
        </div>
        <div class="flex-1 text-center md:text-left">
          <p class="text-xl text-base-content/80 leading-relaxed font-medium">
            Capturing the essence of beauty through a boutique lens. Based in the heart of aesthetics, specializing in wedding narratives and fine art portraiture.
          </p>
          <div class="mt-6 h-[1px] bg-rose-100"></div>
          <p class="mt-6 text-base-content/60">
            This is a boutique digital space capturing life's most tender narratives. Tailored for high-end celebrations, romantic imagery, and premium visual branding.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
$title = $title ?? 'About';
$lang = 'en';
$dir = 'ltr';
require __DIR__ . '/layouts/base.php';
