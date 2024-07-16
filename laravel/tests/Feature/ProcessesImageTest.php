<?php

namespace Tests\Feature;

use App\Jobs\ProcessImage;
use App\Models\Image;
use App\Services\CreatePreprocessingJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProcessesImageTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    private $temp_model = 'python-script/blood-enhancer/model/17_2500_model.npz';
    public function test_process_image_and_save_preprocessed_result(): void
    { 
      // Queue::fake([
      //   ProcessImage::class
      // ]);
      Storage::fake('original');

      $file = File::create('image.png', 200);
      $image = new Image;
      $image->path = $file->store('', 'original');
      $image->save();

      
      Bus::fake();
 
      // Perform order shipping...

      // Assert that a job was dispatched...
      
      // Queue::assertNothingPushed();      
      Bus::assertNothingBatched();
      ProcessImage::dispatch($image->id, $image->path, $this->temp_model, 1);
      Bus::assertDispatched(ProcessImage::class);
      // Queue::assertPushedOn('default', ProcessImage::class);

      // Queue::assertClosurePushed();

    }
}
;