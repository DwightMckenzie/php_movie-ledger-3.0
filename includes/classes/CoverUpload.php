<?php
declare(strict_types=1);

/**
 * Validates and stores uploaded cover images.
 *
 * Only JPEG, PNG and WebP are accepted. The type is checked from the file
 * contents (not the browser's claim), and files are renamed to random names.
 */
final class CoverUpload
{
    public const MAX_BYTES = 5 * 1024 * 1024;

    private const ALLOWED = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    private const WEB_DIR = 'assets/uploads/covers';

    /**
     * Save the file sent in $field, if any.
     *
     * @return string|null web path such as "assets/uploads/covers/abc.jpg", or null when no file was sent
     * @throws InvalidArgumentException when the upload is present but not acceptable
     */
    public static function fromRequest(string $field): ?string
    {
        if (!isset($_FILES[$field]) || !is_array($_FILES[$field])) {
            return null;
        }
        $file = $_FILES[$field];

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (($file['error'] ?? null) !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('The cover image could not be uploaded. Try a smaller file.');
        }
        if (($file['size'] ?? 0) > self::MAX_BYTES) {
            throw new InvalidArgumentException('The cover image must be 5 MB or smaller.');
        }
        $tmp = $file['tmp_name'] ?? '';
        if (!is_string($tmp) || !is_uploaded_file($tmp)) {
            throw new InvalidArgumentException('The cover image upload was not valid.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp);
        if (!is_string($mime) || !isset(self::ALLOWED[$mime]) || getimagesize($tmp) === false) {
            throw new InvalidArgumentException('The cover must be a JPEG, PNG or WebP image.');
        }

        $dir = self::directory();
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuntimeException('Could not create the covers folder.');
        }

        $name = bin2hex(random_bytes(16)) . '.' . self::ALLOWED[$mime];
        if (!move_uploaded_file($tmp, $dir . '/' . $name)) {
            throw new RuntimeException('Could not save the cover image.');
        }
        chmod($dir . '/' . $name, 0644);

        return self::WEB_DIR . '/' . $name;
    }

    /** Delete a cover previously saved by this class. Ignores anything else. */
    public static function delete(?string $webPath): void
    {
        if ($webPath === null || $webPath === '') {
            return;
        }
        $name = basename($webPath);
        if (!preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/', $name)) {
            return;
        }
        $file = self::directory() . '/' . $name;
        if (is_file($file)) {
            unlink($file);
        }
    }

    private static function directory(): string
    {
        return dirname(__DIR__, 2) . '/' . self::WEB_DIR;
    }
}
