<?php

namespace App\Observers;

use App\Models\Image;
use App\Models\ProcessedImage;
use App\Services\BulkDeleteFiles;



class ProcessedObserver
{
    public function deleted(Image $image) : void {

        $p_paths = $image->processedImages()->pluck('path')->toArray();
        $cmap_paths = $image->processedImages()->pluck('colormap_path')->toArray();
        
            
        $service = new BulkDeleteFiles($p_paths, $cmap_paths);
        $service->deleteFiles();
    }
}
