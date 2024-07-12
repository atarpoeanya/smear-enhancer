<?php

namespace Tests\Feature;

use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    private $original_image = 'storage/images/original/';

    private $preprocessed_image = 'storage/images/preprocessed/';

    public function test_image_upload_and_return_response(): void
    {
        // Fake the storage
        Storage::fake('public');

        // Create a fake image
        $file = UploadedFile::fake()->image('test.jpg');

        // Perform the POST request
        $response = $this->post('/image-upload', [
            '_token' => csrf_token(), #Baru ditambahin
            'image' => $file,
        ]);

        // Assert the response is a redirect back
        $response->assertStatus(302);

        // Assert the file was stored
        Storage::disk('public')->assertExists($this->original_image.$file->hashName());

        // Assert the database contains the correct path
        $this->assertDatabaseHas('images', [
            'path' => public_path($this->original_image).$file->hashName(),
        ]);

    }
}
