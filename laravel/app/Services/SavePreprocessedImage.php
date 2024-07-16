<?php

namespace App\Services;

use App\Models\Image;
use App\Models\ProcessedImage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class SavePreprocessedImage
{
    public function saveImage(string $id, string $imageName): string
    {
      // Check if file exist in public
      $result_file = Storage::disk('public')->path($imageName);
      $pre_path = Storage::disk('preprocessed')->putFile('', new File($result_file));
      
      // Save an entry to the database
      $original = Image::find($id);
      $p_image = new ProcessedImage;
      $p_image->path = $pre_path;
      $original->processedImages()->save($p_image);

      $file_result = Storage::disk('public')->delete($imageName);

      return $file_result;
        
    }
}
