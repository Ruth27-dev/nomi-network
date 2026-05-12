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

            return $fileName;
        }

        return $fallbackName;
    }

    public static function deleteFile($destination, $filename)
    {
        if ($filename) {
            if (filter_var($filename, FILTER_VALIDATE_URL)) {
                return;
            }

            $filename = ltrim($filename, '/');
            $legacyDir = trim((string) $destination, '/');
            $paths = self::buildDiskPathCandidates($filename, $legacyDir);
            Storage::disk('uploads')->delete($paths);
            Storage::disk('public')->delete($paths);
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
        $legacyDirectory = trim($legacyDirectory, '/');

        $diskFile = self::resolveExistingDiskFile($file, $legacyDirectory);
        if ($diskFile !== null) {
            return self::resolveDiskUrl($diskFile['disk'], $diskFile['path']);
        }

        if (str_contains($file, '/')) {
            return self::resolveDiskUrl('uploads', self::stripUnifiedPrefix($file));
        }

        if ($legacyDirectory !== '') {
            return self::resolveDiskUrl('public', $legacyDirectory . '/' . $file);
        }

        return self::resolveDiskUrl('uploads', $file);
    }

    private static function resolveTargetDirectory($destination, UploadedFile $file): string
    {
        if (self::shouldUseUnifiedDirectory($file)) {
            return '';
        }

        $legacyDirectory = trim((string) $destination, '/');
        return $legacyDirectory !== '' ? $legacyDirectory : self::UNIFIED_UPLOAD_DIR;
    }

    private static function shouldUseUnifiedDirectory(UploadedFile $file): bool
    {
        $mime = (string) $file->getMimeType();
        return str_starts_with($mime, 'image/');
    }

    private static function stripUnifiedPrefix(string $path): string
    {
        return preg_replace(
            '#^' . preg_quote(self::UNIFIED_UPLOAD_DIR, '#') . '/#',
            '',
            ltrim($path, '/')
        );
    }

    private static function resolveDiskUrl(string $disk, string $path): string
    {
        return Storage::disk($disk)->url(ltrim($path, '/'));
    }

    private static function resolveExistingDiskFile(string $file, string $legacyDirectory = ''): ?array
    {
        foreach (self::buildDiskPathCandidates($file, $legacyDirectory) as $candidate) {
            if (Storage::disk('uploads')->exists($candidate)) {
                return [
                    'disk' => 'uploads',
                    'path' => $candidate,
                ];
            }

            if (Storage::disk('public')->exists($candidate)) {
                return [
                    'disk' => 'public',
                    'path' => $candidate,
                ];
            }
        }

        return null;
    }

    private static function buildDiskPathCandidates(string $file, string $legacyDirectory = ''): array
    {
        $candidates = [];
        $appendCandidate = static function (?string $path) use (&$candidates): void {
            $path = trim((string) $path, '/');
            if ($path !== '' && !in_array($path, $candidates, true)) {
                $candidates[] = $path;
            }
        };

        if (str_contains($file, '/')) {
            $appendCandidate($file);
            $appendCandidate(self::stripUnifiedPrefix($file));

            return $candidates;
        }

        if ($legacyDirectory !== '') {
            $appendCandidate($legacyDirectory . '/' . $file);
        }

        $appendCandidate($file);

        return $candidates;
    }
}
