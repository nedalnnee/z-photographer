-- Seed data for Photograph platform

-- Languages
INSERT INTO languages (code, name, direction) VALUES
('en', 'English', 'ltr'),
('ar', 'Arabic', 'rtl')
ON DUPLICATE KEY UPDATE code=code;

-- Settings placeholders
INSERT INTO settings (name, value, autoload) VALUES
('site.name', 'Photograph', 1),
('site.tagline', 'Luxury photography portfolio & booking platform.', 1),
('site.default_language', 'en', 1),
('site.contact_email', 'admin@example.com', 1)
ON DUPLICATE KEY UPDATE value=VALUES(value);

-- Clear existing admin users to ensure a clean import
DELETE FROM users WHERE role = 'admin';

-- Admin user (Email: admin@admin.com, Password: password)
-- SECURITY: this is a well-known default. Log in and change the password
-- (or update password_hash directly) before exposing the site publicly.
INSERT INTO users (email, password_hash, full_name, role, status) VALUES
(
 'admin@admin.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Site Admin',
 'admin',
 'active'
)
ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash), full_name=VALUES(full_name);

-- Minimal translated keys
-- You can extend this for full i18n coverage.
INSERT INTO translations (language_id, key_name, value)
SELECT l.id, 'nav.home', 'Home' FROM languages l WHERE l.code='en'
ON DUPLICATE KEY UPDATE value=VALUES(value);

INSERT INTO translations (language_id, key_name, value)
SELECT l.id, 'nav.home', 'الرئيسية' FROM languages l WHERE l.code='ar'
ON DUPLICATE KEY UPDATE value=VALUES(value);

-- Placeholder categories/services (optional)
INSERT INTO categories (slug, sort_order, status) VALUES
('weddings', 0, 'active')
ON DUPLICATE KEY UPDATE slug=slug;

INSERT INTO services (slug, sort_order, status, title, description, pricing_text, booking_enabled) VALUES
('wedding', 0, 'active', 'Wedding Photography', 'Premium wedding photography coverage.', 'From $1,500', 1)
ON DUPLICATE KEY UPDATE slug=slug;
