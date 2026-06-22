<?php

namespace App\Traits;

trait ResolvesUploadedPhoto
{
    /**
     * Absolute path to the publicly accessible uploads directory.
     * On Hostinger web context: public_html/
     * Locally: project/public/
     */
    public static function uploadsBase(): string
    {
        return rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
    }

    /**
     * Returns the absolute filesystem path for a relative photo path,
     * or null if the file does not exist.
     *
     * @param  string|null  $relativePath  e.g. "applications/filename.jpg"
     */
    public static function resolvedPhotoPath(?string $relativePath): ?string
    {
        if (!$relativePath) {
            return null;
        }
        $full = static::uploadsBase() . '/uploads/' . $relativePath;
        return file_exists($full) ? $full : null;
    }

    /**
     * Returns the public URL for a relative photo path,
     * or null if the file does not exist.
     *
     * @param  string|null  $relativePath  e.g. "applications/filename.jpg"
     */
    public static function resolvedPhotoUrl(?string $relativePath): ?string
    {
        if (!$relativePath) {
            return null;
        }
        if (static::resolvedPhotoPath($relativePath) !== null) {
            return url('uploads/' . $relativePath);
        }
        return null;
    }
}
