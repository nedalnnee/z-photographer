<?php
/**
 * @var array $categories الفئات القادمة من المتحكم
 * @var array $glimpsePhotos الصور المختارة للعرض المصغر
 */
?>

<section class="min-h-[85vh] rounded-3xl bg-gradient-to-br from-rose-50 via-pink-50/30 to-white p-6 lg:p-12 overflow-hidden flex items-center">
    <div class="hero-content flex-col lg:flex-row gap-12 w-full max-w-7xl mx-auto p-0">

        <!-- القسم الأيسر: النصوص والبيانات -->
        <div class="flex-1 max-w-xl text-center lg:text-left">
            <div class="flex items-center justify-center lg:justify-start gap-3 mb-6 transform transition-all duration-700 hover:translate-x-2">
                <span class="px-4 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase bg-rose-100 text-rose-600 shadow-sm shadow-rose-100/50">
                    <?= htmlspecialchars(t('home.artist')) ?>
                </span>
                <span class="text-sm font-medium text-rose-400/90"><?= htmlspecialchars(t('home.niche')) ?></span>
            </div>

            <h1 class="text-4xl lg:text-7xl font-black leading-[1.15] text-base-content tracking-tight">
                <?= htmlspecialchars(t('home.title1')) ?> <span class="bg-gradient-to-r from-rose-500 via-pink-500 to-amber-400 bg-clip-text text-transparent animate-pulse-soft"><?= htmlspecialchars(t('home.title_span')) ?></span><?= htmlspecialchars(t('home.title2')) ?>
            </h1>

            <p class="mt-6 text-lg text-base-content/70 leading-relaxed max-w-md mx-auto lg:mx-0">
                <?= htmlspecialchars(t('home.desc')) ?>
            </p>

            <!-- أزرار التفاعل المحسنة -->
            <div class="mt-8 flex flex-col sm:flex-row justify-center lg:justify-start gap-4">
                <a href="?r=/portfolio" class="btn border-none bg-gradient-to-r from-rose-400 to-pink-500 hover:from-rose-500 hover:to-pink-600 text-white shadow-lg shadow-rose-200 hover:shadow-rose-300 hover:scale-[1.03] transition-all duration-300 rounded-2xl px-8">
                    <?= htmlspecialchars(t('home.explore')) ?>
                </a>
                <a href="?r=/booking" class="btn btn-outline border-rose-300 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-400 rounded-2xl px-8 transition-all duration-300">
                    <?= htmlspecialchars(t('home.book')) ?>
                </a>
            </div>

            <!-- إحصائيات متطورة (Floating Cards Grid) -->
            <div class="mt-10 grid grid-cols-3 gap-4 max-w-md mx-auto lg:mx-0">
                <div class="glass-card p-4 rounded-2xl text-center shadow-sm hover:shadow-md transition-all duration-300 group hover:-translate-y-1">
                    <div class="text-2xl lg:text-3xl font-black text-rose-500 transition-transform duration-300 group-hover:scale-110">+150</div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-base-content/60 mt-1"><?= htmlspecialchars(t('home.events')) ?></div>
                </div>
                <div class="glass-card p-4 rounded-2xl text-center shadow-sm hover:shadow-md transition-all duration-300 group hover:-translate-y-1">
                    <div class="text-2xl lg:text-3xl font-black text-pink-500 transition-transform duration-300 group-hover:scale-110">5.0</div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-base-content/60 mt-1"><?= htmlspecialchars(t('home.rating')) ?></div>
                </div>
                <div class="glass-card p-4 rounded-2xl text-center shadow-sm hover:shadow-md transition-all duration-300 group hover:-translate-y-1">
                    <div class="text-2xl lg:text-3xl font-black text-amber-500 transition-transform duration-300 group-hover:scale-110">Fast</div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-base-content/60 mt-1"><?= htmlspecialchars(t('home.replies')) ?></div>
                </div>
            </div>
        </div>

        <!-- القسم الأيمن: الكارد المتطور والمعرض المصغر مجسد كلوحة فنية -->
        <div class="flex-1 w-full max-w-lg lg:max-w-xl animate-float">
            <div class="card bg-white/70 backdrop-blur-md shadow-2xl shadow-rose-100/70 border border-rose-100 w-full rounded-[2.5rem] overflow-hidden">
                <div class="card-body p-8">

                    <div class="flex items-center justify-between gap-4 mb-2">
                        <h2 class="text-xl font-bold text-base-content tracking-tight"><?= htmlspecialchars(t('home.curated_title')) ?></h2>
                        <span class="badge bg-pink-100 text-pink-600 border-none font-semibold text-xs px-3 py-1"><?= htmlspecialchars(t('home.vibrant_badge')) ?></span>
                    </div>
                    <p class="text-sm text-base-content/60 mb-6">
                        <?= htmlspecialchars(t('home.cat_desc')) ?>
                    </p>

                    <!-- فئات تفاعلية متطورة -->
                    <div class="flex flex-wrap gap-2.5 mb-8">
                        <?php if (empty($categories)): ?>
                            <span class="text-xs text-base-content/40 italic"><?= htmlspecialchars(t('home.no_cats')) ?></span>
                        <?php else: ?>
                            <?php foreach ($categories as $cat): ?>
                                <a class="btn btn-sm text-xs font-semibold rounded-xl transition-all duration-300 bg-rose-50 text-rose-600 border-rose-100 hover:bg-rose-500 hover:text-white hover:border-none" href="?r=/portfolio&cat=<?= htmlspecialchars($cat['slug']) ?>">✨ <?= htmlspecialchars(ucfirst($cat['slug'])) ?></a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="h-[1px] bg-gradient-to-r from-transparent via-rose-200/50 to-transparent my-4"></div>

                    <h3 class="font-bold text-sm text-base-content/80 mb-4 tracking-wide uppercase"><?= htmlspecialchars(t('home.glimpse')) ?></h3>

                    <!-- شبكة معرض مصغر تفاعلي يحتوي على تدرجات زهرية ناعمة تحاكي تحميل الصور الفاخرة -->
                    <div class="grid grid-cols-3 gap-3">
                        <?php if (empty($glimpsePhotos)): ?>
                            <?php for($i=0; $i<6; $i++): ?>
                                <div class="aspect-[4/5] rounded-2xl bg-rose-50 animate-pulse border border-rose-100/50"></div>
                            <?php endfor; ?>
                        <?php else: ?>
                            <?php foreach ($glimpsePhotos as $photo): ?>
                                <div class="aspect-[4/5] rounded-2xl overflow-hidden glass-card p-1 group relative">
                                    <a href="?r=/portfolio" class="block w-full h-full overflow-hidden rounded-xl">
                                        <img src="uploads/photos/<?= htmlspecialchars($photo['file_basename']) ?>"
                                             class="w-full h-full object-cover transition-all duration-700 group-hover:scale-125 group-hover:rotate-3"
                                             alt="<?= htmlspecialchars($photo['slug']) ?>">
                                        <div class="absolute inset-0 bg-gradient-to-t from-rose-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="mt-6">
                        <a href="?r=/portfolio" class="btn border-none bg-base-content text-base-100 hover:bg-base-content/90 btn-block rounded-xl tracking-wide transition-all duration-300">
                            <?= htmlspecialchars(t('home.view_all')) ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
