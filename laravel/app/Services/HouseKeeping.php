<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class HouseKeeping
{
    public function checkFolder()
    {
        // Ensure the directories exist
        error_log('Running housekeeping');

        $originalImageDir = public_path('storage/images/original');
        $preprocessedImageDir = public_path('storage/images/preprocessed');

        if (! File::exists($originalImageDir)) {
            $this->createFolder($originalImageDir);
        }

        if (! File::exists($preprocessedImageDir)) {
            $this->createFolder($preprocessedImageDir);
        }

    }

    private function createFolder(string $directory_path): null
    {
        try {
            File::makeDirectory($directory_path, 0755, true);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return null;
    }
}
