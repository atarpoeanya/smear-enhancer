<?php

namespace Tests\Feature;


use App\Models\ProcessedImage;
use App\Services\SavePreprocessedImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Mockery;

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

        // Create a fake image file in the 'public' disk
        $imageName = 'image.png';
        $file = File::fake()->image($imageName);
        $pth = $file->store('', 'public');
        Storage::disk('public')->assertExists($pth);

        // // Create an instance of the service
        $service = new SavePreprocessedImage();
        // // Call the method
        $id_rs = $service->saveImage('1', $pth);

        
        $this->assertDatabaseHas('processed_image', ['id' => $id_rs]);
        $result = ProcessedImage::find($id_rs[0]);
        // // Assertions
        Storage::disk('public')->assertMissing($pth);
        Storage::disk('preprocessed')->assertExists($result->path);

        // $this->assertTrue($result);
    }
}
