<?php

namespace Tests\Feature;

use App\Models\Image;
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
        $imageName = 'test-image.jpg';
        Storage::disk('public')->put($imageName, 'test-content');

        // Mock the Image model and its relationship
        $original = Mockery::mock(Image::class);
        $original->shouldReceive('find')->with('1')->andReturn($original);
        $original->shouldReceive('processedImages->save')->andReturn(true);

        $this->instance(Image::class, $original);

        // Create an instance of the service
        $service = new SavePreprocessedImage();

        // Call the method
        $result = $service->saveImage('1', $imageName);

        // Assertions
        Storage::disk('public')->assertMissing($imageName);
        Storage::disk('preprocessed')->assertExists($imageName);

        $this->assertTrue($result);
    }
}
