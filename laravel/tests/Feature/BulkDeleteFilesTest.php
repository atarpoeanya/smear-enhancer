<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\ProcessedImage;
use App\Services\BulkDeleteFiles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

use function PHPSTORM_META\map;

class BulkDeleteFilesTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_get_images_and_delete(): void
    {
        Storage::fake('preprocessed');
        Storage::fake('action');

        $file_1  = File::fake()->image("test.png");
        $file_2  = File::fake()->image("test.png");
        $file_3  = File::fake()->image("test.png");
        $file_array_1 = [$file_1, $file_2, $file_3];

        $file_4  = File::fake()->image("test.png");
        $file_5  = File::fake()->image("test.png");
        $file_6  = File::fake()->image("test.png");
        $file_array_2 = [$file_4, $file_5, $file_6];

        $image = new Image();
        $image->path = "";
        $image->episode = 1;
        $image->save();

        $p_image_1 = new ProcessedImage();
        $p_image_1->path = $file_1->store('', 'preprocessed');
        $p_image_1->psnr = 0;
        $p_image_1->colormap_path = $file_4->store('', 'preprocessed');
        $image->processedImages()->save($p_image_1);

        $p_image_2 = new ProcessedImage();
        $p_image_2->path = $file_2->store('', 'preprocessed');
        $p_image_2->psnr = 0;
        $p_image_2->colormap_path = $file_5->store('', 'preprocessed');
        $image->processedImages()->save($p_image_2);

        $p_image_3 = new ProcessedImage();
        $p_image_3->path = $file_3->store('', 'preprocessed');
        $p_image_3->psnr = 0;
        $p_image_3->colormap_path = $file_6->store('', 'preprocessed');
        $image->processedImages()->save($p_image_3);

        $paths = $image->processedImages()->pluck('path')->toArray();
        $c_paths = $image->processedImages()->pluck('colormap_path')->toArray();

        $hashed = [];
        $hashed_map = [];
        for ($i=3; $i < 3; $i++) { 
            array_push($hashed, $file_array_1[$i]->hashName());
            array_push($hashed_map, $file_array_2[$i]->hashName());
        }
        
        Storage::disk('preprocessed')->assertExists($hashed);
        Storage::disk('action')->assertExists($hashed_map);
        
        
        $service = new BulkDeleteFiles($paths, $c_paths);
        $service->deleteFiles();
        
        Storage::disk('preprocessed')->assertMissing($paths);
        Storage::disk('action')->assertMissing($c_paths);
    }
}
