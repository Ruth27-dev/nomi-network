<?php

namespace Tests\Unit;

use App\Models\UploadFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadFileTest extends TestCase
{
    public function test_it_stores_new_image_uploads_at_the_public_upload_root_and_resolves_their_url(): void
    {
        $image = UploadedFile::fake()->image('upload-file-test.jpg');

        $storedPath = UploadFile::uploadFile('/list-of-value', $image);
        $rootPath = basename($storedPath);

        $this->assertStringStartsWith('uploads/', $storedPath);
        $this->assertTrue(Storage::disk('uploads')->exists($rootPath));
        $this->assertFalse(Storage::disk('uploads')->exists($storedPath));
        $this->assertSame(
            Storage::disk('uploads')->url($rootPath),
            UploadFile::resolvePublicUrl($storedPath, 'list-of-value')
        );

        UploadFile::deleteFile('/list-of-value', $storedPath);

        $this->assertFalse(Storage::disk('uploads')->exists($rootPath));
        $this->assertFalse(Storage::disk('uploads')->exists($storedPath));
    }

    public function test_it_resolves_legacy_public_storage_files(): void
    {
        $filename = 'legacy-upload-file-test.png';
        Storage::disk('public')->put('list-of-value/' . $filename, 'legacy');

        $this->assertSame(
            Storage::disk('public')->url('list-of-value/' . $filename),
            UploadFile::resolvePublicUrl($filename, 'list-of-value')
        );

        UploadFile::deleteFile('/list-of-value', $filename);

        $this->assertFalse(Storage::disk('public')->exists('list-of-value/' . $filename));
    }
}
