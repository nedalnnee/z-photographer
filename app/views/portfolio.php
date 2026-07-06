<?php
/**
 * @var array $categories
 * @var array $albums
 */
ob_start();
?>
<section class="flex items-center justify-between gap-4 flex-wrap mb-10">
  <div>
    <h1 class="text-4xl font-black text-base-content tracking-tight">Our <span class="text-rose-500">Portfolio</span></h1>
    <p class="text-base-content/60 mt-1">A curated collection of visual stories and art.</p>
  </div>
  <div class="flex gap-2">
    <div class="form-control w-56">
      <label class="label"><span class="label-text font-bold text-rose-400">Search</span></label>
      <input id="gallerySearch" type="text" placeholder="Search moments..." class="input input-bordered w-full rounded-xl border-rose-100 focus:border-rose-300" />
    </div>
  </div>
</section>

<section class="mt-6 glass-card p-8 rounded-[2.5rem]">
  <div class="flex gap-3 flex-wrap items-center">
    <div class="dropdown">
      <label tabindex="0" class="btn bg-white border-rose-100 text-rose-500 hover:bg-rose-50 rounded-xl">Category</label>
      <div tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-64 z-[20]">
        <button class="menu-item text-left px-4 py-2 hover:bg-rose-50 rounded-xl block w-full font-medium" data-cat="">All Categories</button>
        <?php foreach ($categories as $c): ?>
          <button class="menu-item text-left px-4 py-2 hover:bg-rose-50 rounded-xl block w-full text-sm" data-cat="<?= htmlspecialchars($c['slug']) ?>">✨ <?= htmlspecialchars(ucfirst($c['slug'])) ?></button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="dropdown">
      <label tabindex="0" class="btn bg-white border-rose-100 text-rose-500 hover:bg-rose-50 rounded-xl">Album</label>
      <div tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-64 z-[20]">
        <button class="menu-item text-left px-4 py-2 hover:bg-rose-50 rounded-xl block w-full font-medium" data-alb="">All Albums</button>
        <?php foreach ($albums as $a): ?>
          <button class="menu-item text-left px-4 py-2 hover:bg-rose-50 rounded-xl block w-full text-sm" data-alb="<?= htmlspecialchars($a['slug']) ?>">📂 <?= htmlspecialchars(ucfirst($a['slug'])) ?></button>
        <?php endforeach; ?>
      </div>
    </div>

    <button id="galleryReset" class="btn btn-sm btn-ghost text-base-content/40 hover:text-rose-500">Reset Filters</button>
  </div>

  <div id="galleryGrid" class="mt-6 columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-4">
    <!-- Items injected by AJAX -->
  </div>

  <div id="gallerySkeleton" class="mt-6 hidden">
    <div class="h-12 bg-base-300/60 animate-pulse rounded-lg"></div>
    <div class="h-12 bg-base-300/60 animate-pulse rounded-lg mt-3"></div>
  </div>

  <div class="mt-6 flex justify-center">
    <button id="galleryLoadMore" class="btn bg-gradient-to-r from-rose-400 to-pink-500 text-white border-none rounded-2xl px-10 shadow-lg shadow-rose-200">Load More Art</button>
  </div>

  <div id="galleryEnd" class="mt-4 text-center text-base-content/60 hidden">No more photos.</div>
</section>

<script>
  window.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('galleryGrid');
    const btn = document.getElementById('galleryLoadMore');
    const end = document.getElementById('galleryEnd');
    const search = document.getElementById('gallerySearch');
    const reset = document.getElementById('galleryReset');

    let page = 1;
    let loading = false;
    let canLoad = true;
    let currentCat = '';
    let currentAlb = '';

    const escape = (s) => String(s).replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'<','>':'>','\'':'&#39;','"':'"'}[c]));

    function renderItems(items) {
      for (const p of items) {
        const slug = escape(p.slug);
        const base = escape(p.file_basename);
        const img = `uploads/photos/${base}`;
        const wrap = document.createElement('div');
        wrap.className = 'mb-4 break-inside-avoid photo-hover-effect';
        wrap.innerHTML = `
          <a href="${img}" target="_blank" rel="noopener" class="block rounded-2xl overflow-hidden glass-card p-1">
            <img loading="lazy" class="w-full h-auto rounded-xl" src="${img}" alt="${slug}" />
          </a>
        `;
        grid.appendChild(wrap);
      }
    }

    async function loadNext() {
      if (loading || !canLoad) return;
      loading = true;
      btn.disabled = true;

      const skeleton = document.getElementById('gallerySkeleton');
      skeleton.classList.remove('hidden');

      const params = new URLSearchParams({ page });
      if (currentCat) params.set('cat', currentCat);
      if (currentAlb) params.set('alb', currentAlb);
      if (search?.value) params.set('q', search.value);

      const res = await fetch('?r=/portfolio/ajax&' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const data = await res.json();

      skeleton.classList.add('hidden');
      loading = false;
      btn.disabled = false;

      if (!data.items || data.items.length === 0) {
        canLoad = false;
        end.classList.remove('hidden');
        btn.classList.add('hidden');
        return;
      }

      renderItems(data.items);
      page += 1;
    }

    btn.addEventListener('click', loadNext);

    search?.addEventListener('input', () => {
      page = 1;
      grid.innerHTML = '';
      canLoad = true;
      end.classList.add('hidden');
      btn.classList.remove('hidden');
      loadNext();
    });

    document.querySelectorAll('.menu-item[data-cat]').forEach(el => {
      el.addEventListener('click', () => {
        currentCat = el.getAttribute('data-cat') || '';
        page = 1;
        grid.innerHTML = '';
        loadNext();
      });
    });

    document.querySelectorAll('.menu-item[data-alb]').forEach(el => {
      el.addEventListener('click', () => {
        currentAlb = el.getAttribute('data-alb') || '';
        page = 1;
        grid.innerHTML = '';
        loadNext();
      });
    });

    reset?.addEventListener('click', () => {
      currentCat = '';
      currentAlb = '';
      if (search) search.value = '';
      page = 1;
      grid.innerHTML = '';
      canLoad = true;
      end.classList.add('hidden');
      btn.classList.remove('hidden');
      loadNext();
    });

    loadNext();
  });
</script>

<?php
$content = ob_get_clean();
$title = 'Portfolio';
$lang = $_SESSION['lang'] ?? 'en';
$dir = ($lang === 'ar') ? 'rtl' : 'ltr';
require __DIR__ . '/layouts/base.php';
