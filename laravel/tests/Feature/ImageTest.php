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
        

        $file = UploadedFile::fake()->image('test-image.jpg');
        $episode = 1;
        $model = 1;
        
        // Perform the POST request
        $response = $this->post(route('image.store'), [
            'image' => $file,
            'episode' => $episode,
            'model' => $model,
            'checkbox_value' => true,
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
