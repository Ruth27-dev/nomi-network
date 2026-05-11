<?php

namespace App\Models;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadFile
{
    // public static function uploadFile($destination, $image, $temperature = null)
    // {
    //     if ($image && $image->isValid()) {
    //         $path = public_path() . '/' . $destination;
    //         $originalName = $image->getClientOriginalName();
    //         $fileName = time() . rand(1111, 9999) . '-' . str_replace(' ', '_', $originalName);
    //         $image->move($path, $fileName);
    //     } else {
    //         $fileName = $temperature;
    //     }

    //     return $fileName;
    // }

    // public static function deleteFile($destination, $temperature)
    // {
    //     if ($temperature) {
    //         $filename = public_path() . $destination . '/' . $temperature;
    //         File::delete($filename);
    //     }
    // }


    // public static function uploadFile($destination, $image, $fallbackName = null)
    // {
    //     if ($image && $image->isValid()) {
    //         $originalName = $image->getClientOriginalName();
    //         $fileName = time() . rand(1111, 9999) . '-' . str_replace(' ', '_', $originalName);

    //         Storage::disk('s3')->putFileAs($destination, $image, $fileName, 'public');
    //         $publicUrl = Storage::disk('s3')->url($destination . '/' . $fileName);

    //     } else {
    //         $publicUrl = $fallbackName;
    //     }

    //     return $publicUrl;
    // }

    // public static function deleteFile($destination, $filename)
    // {
    //     if ($filename) {
    //         $key = self::getS3KeyFromUrl($filename);
    //         Storage::disk('s3')->delete($key);
    //         // if (Storage::disk('s3')->exists($key)) {
    //         // }
    //     }
    // }

    // private static function getS3KeyFromUrl(string $url): string
    // {
    //     $baseUrl = rtrim(config('filesystems.disks.s3.endpoint'), '/');
    //     return ltrim(str_replace($baseUrl, '', $url), '/');
    // }

    # old code for local storage

    public const UNIFIED_UPLOAD_DIR = 'uploads';

    public static function uploadFile($destination, $image, $fallbackName = null)
    {
        if ($image && $image->isValid()) {
            $originalName = $image->getClientOriginalName();
            $fileName = time() . rand(1111, 9999) . '-' . str_replace(' ', '_', $originalName);
            $directory = self::resolveTargetDirectory($destination, $image);

            Storage::disk('uploads')->putFileAs($directory, $image, $fileName);
            if (self::shouldUseUnifiedDirectory($image)) {
                return self::UNIFIED_UPLOAD_DIR . '/' . $fileName;
            }
        } else {
            $fileName = $fallbackName;
        }

        return $fileName;
    }

    public static function deleteFile($destination, $filename)
    {
        if ($filename) {
            if (filter_var($filename, FILTER_VALIDATE_URL)) {
                return;
            }

            $filename = ltrim($filename, '/');
            if (str_contains($filename, '/')) {
                Storage::disk('uploads')->delete($filename);
                return;
            }

            $legacyDir = trim((string) $destination, '/');
            $paths = [self::UNIFIED_UPLOAD_DIR . '/' . $filename];
            if ($legacyDir !== '') {
                $paths[] = $legacyDir . '/' . $filename;
            }

            Storage::disk('uploads')->delete($paths);
        }
    }

    public static function resolvePublicUrl(?string $file, string $legacyDirectory, ?string $fallback = null): ?string
    {
        if (!$file) {
            return $fallback;
        }

        if (filter_var($file, FILTER_VALIDATE_URL)) {
            return $file;
        }

        $file = ltrim($file, '/');
        if (str_contains($file, '/')) {
            return self::resolveUploadsDiskUrl($file);
        }

        $legacyDirectory = trim($legacyDirectory, '/');
        if ($legacyDirectory !== '') {
            $legacyPath = $legacyDirectory . '/' . $file;
            $normalizedLegacyPath = self::normalizeUploadsPath($legacyPath);
            if (Storage::disk('uploads')->exists($legacyPath) || Storage::disk('uploads')->exists($normalizedLegacyPath)) {
                return self::resolveUploadsDiskUrl($legacyPath);
            }
        }

        $uploadPath = self::UNIFIED_UPLOAD_DIR . '/' . $file;
        $normalizedUploadPath = self::normalizeUploadsPath($uploadPath);
        if (Storage::disk('uploads')->exists($uploadPath) || Storage::disk('uploads')->exists($normalizedUploadPath)) {
            return self::resolveUploadsDiskUrl($uploadPath);
        }

        if ($legacyDirectory !== '') {
            return self::resolveUploadsDiskUrl($legacyDirectory . '/' . $file);
        }

        return self::resolveUploadsDiskUrl($file);
    }

    private static function resolveTargetDirectory($destination, UploadedFile $file): string
    {
        if (self::shouldUseUnifiedDirectory($file)) {
            return self::UNIFIED_UPLOAD_DIR;
        }

        $legacyDirectory = trim((string) $destination, '/');
        return $legacyDirectory !== '' ? $legacyDirectory : self::UNIFIED_UPLOAD_DIR;
    }

    private static function shouldUseUnifiedDirectory(UploadedFile $file): bool
    {
        $mime = (string) $file->getMimeType();
        return str_starts_with($mime, 'image/');
    }

    private static function normalizeUploadsPath(string $path): string
    {
        return preg_replace('#^uploads/#', '', ltrim($path, '/'));
    }

    private static function resolveUploadsDiskUrl(string $path): string
    {
        return Storage::disk('uploads')->url(self::normalizeUploadsPath($path));
    }
}
