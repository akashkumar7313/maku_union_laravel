<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HandlesUploads
{
    protected function storeUpload(?UploadedFile $file, string $directory): ?string
    {
        if (! $file) {
            return null;
        }

        return $file->store($directory, config('maku.uploads.disk'));
    }

    protected function deleteUpload(?string $path): void
    {
        if ($path) {
            Storage::disk(config('maku.uploads.disk'))->delete($path);
        }
    }
}
