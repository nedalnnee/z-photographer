# Photograph Portfolio & Booking Platform (Vanilla JS + PHP 8 + PDO + MySQL)

## Requirements
- PHP 8+
- MySQL 8+
- XAMPP (Apache + MySQL)
- mod_rewrite enabled

## Setup
1. Create database in MySQL, e.g. `photograph`.
2. Run `composer install` in the project root to generate the autoloader.
3. Import SQL:
   - `sql/schema.sql`
   - `sql/seed.sql` (creates admin + sample content placeholders)
4. Configure DB credentials:
   - copy `config/db.php.example` -> `config/db.php`
5. Configure app settings:
   - copy `config/app.php.example` -> `config/app.php`
6. Enable rewrite (for pretty URLs):
   - Ensure Apache `AllowOverride All` for this folder.
7. Visit:
   - Public: `/public/`
   - Admin: `/admin/login`

## Notes
- This project is generated from scratch because the target directory was empty.
- Secure auth uses password_hash + prepared statements + CSRF tokens.
- Gallery uses masonry + lazy loading + AJAX pagination/filter endpoints.

## Production checklist
- [ ] Log in as `admin@admin.com` / `password` immediately and change the password (this hash is public in `sql/seed.sql`).
- [ ] Set `'debug' => false` in `config/app.php` (already the default in `config/app.php.example`) so PHP errors are logged, not shown to visitors.
- [ ] Once served over HTTPS, set `session.cookie_secure` to `true` in `config/app.php`.
- [ ] `config/db.php` and `config/app.php` are gitignored on purpose — each environment should have its own copy, never committed.
- [ ] Run `composer install` on the server to generate `vendor/autoload.php` (no packages to download, it's just the PSR-4 autoloader).
- [ ] Photo uploads are restricted to jpg/jpeg/png/gif/webp (validated by real image content, not just extension) and capped at 10MB; `public/uploads/` disables PHP execution as defense in depth.
