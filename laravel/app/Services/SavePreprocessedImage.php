<?php

namespace App\Services;

use App\Models\Image;
use App\Models\ProcessedImage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class SavePreprocessedImage
{
    public function saveImage(string $id, string $imageName): array
    {
        // Check if file exist in public
        $file = Storage::disk('public')->path($imageName);
        $tempFile = new File($file);
        $pre_path = Storage::disk('preprocessed')->putFile('', $tempFile);
        Storage::disk('public')->delete($imageName);

        // Save an entry to the database
        $original = Image::find($id);
        $p_image = new ProcessedImage;
        $p_image->path = $pre_path;
        $original->processedImages()->save($p_image);

        return [$p_image->id, $pre_path];

    }
}
