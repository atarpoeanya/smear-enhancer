<?php

namespace Tests\Feature;

use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeleteImageTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_delete_image_in_database_and_storage_and_return_response(): void
    {
        // Arrange: Create a dummy image in the database
        Storage::fake('original');

        // $image = Image::factory()->create();

        $file = File::create('image.png', 200);
        $image = new Image;
        $image->path = $file->store('', 'original');
        $image->save();

        // Act: Call the deleteImage function
        $response = $this->delete(route('image.delete', $image->id));

        $this->assertDatabaseMissing('images', ['id' => $image->id]);
        Storage::disk('original')->assertMissing($image->path);

        // Assert: Check if the image is deleted and if the correct response is returned
        $response->assertRedirect();        
    }
}