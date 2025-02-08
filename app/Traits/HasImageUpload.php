<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasImageUpload
{
    public function uploadImage(UploadedFile $image, $path = 'products', $oldImage = null)
    {
        // Delete old image if exists
        if ($oldImage) {
            Storage::delete($oldImage);
        }

        // Store the new image
        return $image->store($path, 'public');
    }

    public function deleteImage($imagePath)
    {
        if ($imagePath) {
            Storage::delete($imagePath);
        }
    }
} 