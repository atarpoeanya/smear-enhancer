<?php

namespace Tests\Feature;

use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageTest extends TestCase
{

    /**
     * A basic feature test example.
     */
    public function test_image_upload_and_return_response(): void
    {
        // Fake the storage
        Storage::fake('original');
        // Create a fake image
        $file = UploadedFile::fake()->image('test.jpg');

        // Perform the POST request
        $response = $this->post(route('image.store'), [
            'image' => $file,
            'episode_len' => 1,
        ]);

        // Assert the file was stored
        Storage::disk('original')->assertExists($file->hashName());
        
        // Assert the database contains the correct path
        $this->assertDatabaseHas('images', [
            'path' => $file->hashName(),
        ]);
        
        
        // Assert the response is a redirect back
        $response->assertStatus(302);
    }
}
