<?php

namespace App\Services;

use App\Models\Image;
use App\Models\ProcessedImage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SavePreprocessedImage
{

    public function saveImage(string $id, string $prepro_name, string $colormap_name): array
    {
        // Check if file exist in public
        $output_folder = Storage::disk('public')->path($prepro_name);
        $temp_file = new File($output_folder);
        $pre_path = Storage::disk('preprocessed')->putFile('', $temp_file);

        $colormap_folder = Storage::disk('public')->path($colormap_name);
        $temp_c_file = new File($colormap_folder);
        $pre_c_path = Storage::disk('action')->putFile('', $temp_c_file);
        
        Storage::disk('public')->delete($prepro_name);
        Storage::disk('public')->delete($colormap_name);

        
        // Save an entry to the database
        $original = Image::find($id);
        $full_original_path = Storage::disk('original')->path($original->path);
        $processed_path = Storage::disk('preprocessed')->path($pre_path);

        $p_image = new ProcessedImage;
        $psnr = new EvaluateImages;
        $psnr_value = $psnr->getPSNR($full_original_path, $processed_path);

        Log::error($psnr_value);        
        $p_image->psnr = $psnr_value;
        $p_image->path = $pre_path;
        $p_image->colormap_path = $pre_c_path;
        $original->processedImages()->save($p_image);

        return [$p_image->id, $pre_path];

    }
}
