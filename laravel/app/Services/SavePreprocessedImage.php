<?php

namespace App\Services;

use App\Models\Image;
use App\Models\ProcessedImage;

class SavePreprocessedImage
{
    public function processImage(Image $original_image, $path): void
    {

        $p_image = new ProcessedImage([
            'path' => $path,
        ]);
        $original_image->processedImages()->save($p_image);

    }
}
