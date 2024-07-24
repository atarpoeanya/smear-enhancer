<?php

namespace App\Services;

use App\Jobs\ProcessImage;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class BulkDeleteFiles
{
  public $paths;
  public $colormap_paths;

  public function __construct(array $processed_paths, array $colormap_paths)
  {
    $this->paths = $processed_paths;
    $this->colormap_paths = $colormap_paths;
  }


  function deleteFiles(): int
  {

    foreach ($this->paths as $path) {
      Storage::disk('preprocessed')->delete($path);
    }

    foreach ($this->colormap_paths as $path) {
      Storage::disk('preprocessed')->delete($path);
    }
    
    return 1;
  }
}
