<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\ProcessedImage;
use App\Services\SavePreprocessedImage;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SavePreprocessedImageServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_save_processed_image_to_storage_and_db(): void
    {
        // Mock the Storage facade
        Storage::fake('public');
        Storage::fake('preprocessed');
        Storage::fake('action');

        // Create a fake image file in the 'public' disk
        $imageName = 'image.png';
        $imageName_2 = 'image_2.png';

        $file = File::fake()->image($imageName);
        $pth = $file->store('', 'public');
        Storage::disk('public')->assertExists($pth);

        $file = File::fake()->image($imageName_2);
        $pth_2 = $file->store('', 'public');
        Storage::disk('public')->assertExists($pth_2);

        $image = new Image;
        $image->path = "";
        $image->episode = 1;
        $image->save();

        // // Create an instance of the service
        $service = new SavePreprocessedImage();
        // // Call the method
        $id_rs = $service->saveImage($image->id, $pth, $pth_2);

        $this->assertDatabaseHas('processed_images', ['id' => $id_rs[0]]);
        $result = ProcessedImage::find($id_rs[0]);
        // // Assertions
        Storage::disk('public')->assertMissing($pth);
        Storage::disk('public')->assertMissing($pth_2);
        Storage::disk('preprocessed')->assertExists($result->path);
        Storage::disk('action')->assertExists($result->colormap_path);

        // $this->assertTrue($result);
    }
}
