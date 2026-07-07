<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Auth;
use Core\Db;
use Core\Security;

final class PhotoController
{
    public function index(): string
    {
        Auth::requireRole('admin');

        $pdo = Db::pdo();
        $stmt = $pdo->query("SELECT * FROM photos WHERE status = 'active' ORDER BY sort_order DESC, id DESC");
        $photos = $stmt->fetchAll();
        
        ob_start();
        $content = '<div class="space-y-6">'
            . '<div class="flex items-center justify-between">'
            . '<div><h1 class="text-4xl font-black tracking-tight text-base-content">' . htmlspecialchars(t('admin.photos.title_pre')) . ' <span class="text-rose-500">' . htmlspecialchars(t('admin.photos.title_span')) . '</span></h1>'
            . '<p class="text-base-content/60">' . htmlspecialchars(t('admin.photos.subtitle')) . '</p></div>'
            . '<a href="?r=/admin/photos/upload" class="btn bg-gradient-to-r from-rose-400 to-pink-500 text-white border-none rounded-xl shadow-lg">' . htmlspecialchars(t('admin.photos.upload_art')) . '</a>'
            . '</div>';

        $content .= '<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">';

        foreach ($photos as $photo) {
            $id = (string)$photo['id'];
            $slug = Security::e((string)$photo['slug']);
            $filename = Security::e((string)$photo['file_basename']);
            // التأكد من أن المسار يبدأ من المجلد العام
            $imageUrl = 'uploads/photos/' . $filename;
            $content .= '<div class="group relative aspect-square glass-card rounded-2xl overflow-hidden photo-hover-effect p-1">'
                . '<img src="' . $imageUrl . '" class="w-full h-full object-cover rounded-xl" alt="' . $slug . '">'
                . '<div class="absolute inset-1 bg-rose-500/80 opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col items-center justify-center gap-2 rounded-xl">'
                . '<span class="text-white text-[10px] font-bold uppercase">' . $slug . '</span>'
                . '<form method="POST" action="?r=/admin/photos/delete" onsubmit="return confirm(\'' . htmlspecialchars(t('common.confirm_delete'), ENT_QUOTES) . '\')">'
                . '<input type="hidden" name="csrf_token" value="' . Security::csrfToken() . '">'
                . '<input type="hidden" name="id" value="' . $id . '">'
                . '<button class="btn btn-xs btn-circle bg-white text-error border-none hover:bg-error hover:text-white">✕</button></form>'
                . '</div>'
                . '</div>';
        }

        $content .= '</div>'
            . '</div></div>';

        $title = 'Manage Gallery';
        require __DIR__ . '/../../views/layouts/base.php';
        return (string)ob_get_clean();
    }

    public function upload(): string
    {
        Auth::requireRole('admin');
        $pdo = Db::pdo();
        $albums = $pdo->query("SELECT id, slug FROM albums WHERE status = 'active' ORDER BY slug ASC")->fetchAll();

        $albumOptions = '<option value="">' . htmlspecialchars(t('admin.photos.standalone')) . '</option>';
        foreach ($albums as $alb) {
            $albumOptions .= '<option value="' . $alb['id'] . '">' . Security::e($alb['slug']) . '</option>';
        }

        $csrf = Security::csrfToken();
        ob_start();
        $content = '<div class="max-w-2xl mx-auto py-10"><div class="text-center mb-8"><h1 class="text-4xl font-black text-rose-500">' . htmlspecialchars(t('admin.photos.upload_title')) . '</h1></div>'
            . '<div class="glass-card rounded-[2.5rem] p-10 text-center border-dashed border-2 border-rose-200">'
            . '<form method="POST" action="?r=/admin/photos/store" enctype="multipart/form-data" class="space-y-6">'
            . "<input type=\"hidden\" name=\"csrf_token\" value=\"{$csrf}\">"
            . '<div class="form-control mb-4 text-left"><label class="label"><span class="label-text font-bold text-rose-400">' . htmlspecialchars(t('admin.photos.select_album')) . '</span></label>'
            . '<select name="album_id" class="select select-bordered border-rose-100 rounded-xl w-full">' . $albumOptions . '</select></div>'
            . '<input type="file" name="photos[]" multiple class="file-input file-input-bordered file-input-primary w-full max-w-xs" />'
            . '<div class="pt-4"><button class="btn bg-rose-500 text-white border-none rounded-xl px-10">' . htmlspecialchars(t('admin.photos.start_upload')) . '</button></div>'
            . '</form></div></div>';

        $title = 'Upload Photos';
        require __DIR__ . '/../../views/layouts/base.php';
        return (string)ob_get_clean();
    }

    /** Allowed upload types: extension => expected getimagesize() IMAGETYPE constant */
    private const ALLOWED_TYPES = [
        'jpg' => IMAGETYPE_JPEG,
        'jpeg' => IMAGETYPE_JPEG,
        'png' => IMAGETYPE_PNG,
        'gif' => IMAGETYPE_GIF,
        'webp' => IMAGETYPE_WEBP,
    ];
    private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB

    public function store(): void
    {
        Auth::requireRole('admin');
        Security::requireCsrf();

        $albumId = ($_POST['album_id'] ?? '') !== '' ? (int)$_POST['album_id'] : null;

        $files = $_FILES['photos'] ?? null;
        $rejected = 0;
        if ($files && is_array($files['name'])) {
            $pdo = Db::pdo();
            $uploadDir = __DIR__ . '/../../../public/uploads/photos/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $tmpName = $files['tmp_name'][$i];
                if (!is_uploaded_file($tmpName) || (int)$files['size'][$i] > self::MAX_FILE_SIZE) {
                    $rejected++;
                    continue;
                }

                $originalName = basename($files['name'][$i]);
                $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                // Reject anything that isn't a whitelisted extension whose
                // real content (per getimagesize) matches that type. This
                // blocks disguised .php/.phtml/.htaccess uploads.
                $imageInfo = @getimagesize($tmpName);
                if (!isset(self::ALLOWED_TYPES[$ext]) || $imageInfo === false || $imageInfo[2] !== self::ALLOWED_TYPES[$ext]) {
                    $rejected++;
                    continue;
                }

                $basename = bin2hex(random_bytes(8));
                $filename = $basename . '.' . $ext;

                if (move_uploaded_file($tmpName, $uploadDir . $filename)) {
                    $stmt = $pdo->prepare("INSERT INTO photos (slug, file_basename, status, sort_order, album_id, width, height) VALUES (?, ?, 'active', 0, ?, ?, ?)");
                    $stmt->execute([$basename, $filename, $albumId, $imageInfo[0], $imageInfo[1]]);
                }
            }
        }

        $_SESSION['flash']['success'] = $rejected > 0
            ? t('flash.photos_uploaded_partial', ['count' => $rejected])
            : t('flash.photos_uploaded');
        header('Location: ?r=/admin/photos');
        exit;
    }

    public function delete(): void
    {
        Auth::requireRole('admin');
        Security::requireCsrf();
        $id = $_POST['id'] ?? '';
        if ($id) {
            $pdo = Db::pdo();
            $pdo->prepare("UPDATE photos SET status = 'deleted' WHERE id = ?")->execute([$id]);
        }
        $_SESSION['flash']['success'] = t('flash.photo_deleted');
        header('Location: ?r=/admin/photos');
        exit;
    }
}