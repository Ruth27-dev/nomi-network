<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
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

    public static function uploadFile($destination, $image, $fallbackName = null)
    {
        if ($image && $image->isValid()) {
            $originalName = $image->getClientOriginalName();
            $fileName = time() . rand(1111, 9999) . '-' . str_replace(' ', '_', $originalName);

            Storage::disk('public')->putFileAs($destination, $image, $fileName);
        } else {
            $fileName = $fallbackName;
        }

        return $fileName;
    }

    public static function deleteFile($destination, $filename)
    {
        if ($filename) {
            Storage::disk('public')->delete($destination . '/' . $filename);
        }
    }
}
